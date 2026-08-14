import os
import io
import argparse
import logging
from concurrent.futures import ThreadPoolExecutor, as_completed
from PIL import Image
import boto3
from botocore.config import Config
from botocore.exceptions import ClientError
from dotenv import load_dotenv, find_dotenv
from tqdm import tqdm

# Load environment variables
load_dotenv(find_dotenv())

# Configure Logging
log_file = "log.txt"
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler(log_file),
    ]
)

logger = logging.getLogger("R2Resizer")

R2_ENDPOINT_URL = os.getenv('CLOUDFLARE_R2_ENDPOINT')
R2_ACCESS_KEY_ID = os.getenv('CLOUDFLARE_R2_ACCESS_KEY_ID')
R2_SECRET_ACCESS_KEY = os.getenv('CLOUDFLARE_R2_SECRET_ACCESS_KEY')
R2_BUCKET_NAME = os.getenv('CLOUDFLARE_R2_BUCKET')

SUPPORTED_EXTENSIONS = {'.jpg', '.jpeg', '.png', '.webp'}
TARGET_WIDTHS = [300, 600, 900, 1200]
DEFAULT_QUALITY = 80
CACHE_CONTROL = "public, max-age=31536000, immutable"

class Stats:
    def __init__(self):
        self.processed = 0
        self.skipped = 0
        self.failed = 0
        self.original_size = 0
        self.new_size = 0

def get_s3_client():
    return boto3.client(
        's3',
        endpoint_url=R2_ENDPOINT_URL,
        aws_access_key_id=R2_ACCESS_KEY_ID,
        aws_secret_access_key=R2_SECRET_ACCESS_KEY,
        config=Config(signature_version='s3v4')
    )

def object_exists(client, bucket, key):
    try:
        client.head_object(Bucket=bucket, Key=key)
        return True
    except ClientError as e:
        if e.response['Error']['Code'] == '404':
            return False
        raise e

def generate_resized_key(original_key, width):
    name, ext = os.path.splitext(original_key)
    # As requested, output format should be .webp
    return f"{name}-{width}w.webp"

def process_image(args, client, obj, dry_run=False):
    key = obj['Key']
    original_size = obj['Size']
    
    # Check extension
    ext = os.path.splitext(key)[1].lower()
    if ext not in SUPPORTED_EXTENSIONS:
        return {'status': 'skipped', 'reason': 'Unsupported extension', 'key': key}

    # Skip files that are already responsive variants (ending with -300w.webp etc.)
    if any(key.endswith(f"-{w}w.webp") for w in TARGET_WIDTHS):
        return {'status': 'skipped', 'reason': 'Already a responsive variant', 'key': key}

    try:
        # Check if we have all variants
        missing_variants = []
        for w in TARGET_WIDTHS:
            variant_key = generate_resized_key(key, w)
            if not object_exists(client, R2_BUCKET_NAME, variant_key):
                missing_variants.append((w, variant_key))
        
        if not missing_variants:
            return {'status': 'skipped', 'reason': 'All variants exist', 'key': key}
            
        if dry_run:
            msg = f'[DRY RUN] Would process {key} (Size: {original_size} bytes). Generating {len(missing_variants)} variants: {[w for w, _ in missing_variants]}w'
            logger.info(msg)
            return {
                'status': 'success',
                'key': key,
                'original_size': original_size,
                'new_size': 0,
                'msg': msg
            }

        # Download original
        response = client.get_object(Bucket=R2_BUCKET_NAME, Key=key)
        img_data = response['Body'].read()
        
        total_new_size = 0
        variants_created = 0
        
        with Image.open(io.BytesIO(img_data)) as img:
            # Convert to RGB/RGBA based on mode
            if img.mode in ('RGBA', 'LA') or (img.mode == 'P' and 'transparency' in img.info):
                img = img.convert('RGBA')
            else:
                img = img.convert('RGB')
            
            orig_width, orig_height = img.size
            
            for target_w, variant_key in missing_variants:
                # To prevent 404s on the frontend which expects all variants to exist,
                # if the original image is smaller than the target width, we don't upscale,
                # but we still save it (at its original size) under the target variant name.
                if orig_width < target_w:
                    logger.info(f"[{key}] Original width ({orig_width}) < target ({target_w}). Saving at original size to prevent 404s.")
                    resize_w = orig_width
                    resize_h = orig_height
                else:
                    ratio = target_w / orig_width
                    resize_h = int(orig_height * ratio)
                    resize_w = target_w
                
                resized_img = img.resize((resize_w, resize_h), Image.LANCZOS)
                
                out_io = io.BytesIO()
                resized_img.save(out_io, format='WEBP', quality=args.quality)
                out_io.seek(0)
                out_data = out_io.read()
                
                out_size = len(out_data)
                total_new_size += out_size
                variants_created += 1
                
                # Upload variant
                client.put_object(
                    Bucket=R2_BUCKET_NAME,
                    Key=variant_key,
                    Body=out_data,
                    ContentType='image/webp',
                    CacheControl=CACHE_CONTROL
                )
                logger.info(f"[{key}] Uploaded {variant_key} ({out_size / 1024:.2f} KB) - Original: {original_size / 1024:.2f} KB")
        
        if variants_created == 0:
            return {'status': 'skipped', 'reason': 'Image smaller than all target sizes', 'key': key}

        return {
            'status': 'success',
            'key': key,
            'original_size': original_size,
            'new_size': total_new_size,
            'msg': f'Processed variants successfully.'
        }
    except Exception as e:
        logger.error(f"[{key}] Error processing: {e}")
        return {'status': 'failed', 'reason': str(e), 'key': key}

def main():
    parser = argparse.ArgumentParser(description="Bulk resize R2 images to responsive WebP")
    parser.add_argument('--dry-run', action='store_true', help="Preview files to be processed without uploading")
    parser.add_argument('--workers', type=int, default=4, help="Number of concurrent workers")
    parser.add_argument('--prefix', type=str, default="", help="Prefix (folder) to scan in bucket")
    parser.add_argument('--quality', type=int, default=DEFAULT_QUALITY, help="WebP quality (0-100)")
    args = parser.parse_args()

    if not all([R2_ENDPOINT_URL, R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, R2_BUCKET_NAME]):
        print("Missing required environment variables (CLOUDFLARE_R2_ENDPOINT, CLOUDFLARE_R2_ACCESS_KEY_ID, CLOUDFLARE_R2_SECRET_ACCESS_KEY, CLOUDFLARE_R2_BUCKET)")
        return

    client = get_s3_client()
    stats = Stats()
    
    print(f"Scanning bucket: {R2_BUCKET_NAME}, prefix: '{args.prefix}'")
    
    # Use pagination to list all objects
    paginator = client.get_paginator('list_objects_v2')
    pages = paginator.paginate(Bucket=R2_BUCKET_NAME, Prefix=args.prefix)
    
    objects_to_process = []
    try:
        for page in pages:
            if 'Contents' in page:
                objects_to_process.extend(page['Contents'])
    except Exception as e:
        print(f"Failed to list objects in bucket. Please check credentials. Error: {e}")
        return

    print(f"Found {len(objects_to_process)} objects. Starting processing...")
    
    with ThreadPoolExecutor(max_workers=args.workers) as executor:
        futures = {executor.submit(process_image, args, client, obj, args.dry_run): obj for obj in objects_to_process}
        
        for future in tqdm(as_completed(futures), total=len(futures), desc="Processing Images"):
            res = future.result()
            if res['status'] == 'success':
                stats.processed += 1
                stats.original_size += res['original_size']
                stats.new_size += res['new_size']
            elif res['status'] == 'skipped':
                stats.skipped += 1
            else:
                stats.failed += 1

    print("\n--- SUMMARY ---")
    print(f"Dry Run Mode: {'ON' if args.dry_run else 'OFF'}")
    print(f"Total Files Scanned: {len(objects_to_process)}")
    print(f"Processed (generated variants): {stats.processed}")
    print(f"Skipped: {stats.skipped}")
    print(f"Failed: {stats.failed}")
    
    if stats.processed > 0 and not args.dry_run:
        orig_mb = stats.original_size / (1024 * 1024)
        new_mb = stats.new_size / (1024 * 1024)
        print(f"Original Size (of processed files): {orig_mb:.2f} MB")
        print(f"New Responsive Variants Total Size: {new_mb:.2f} MB")
        
        # Calculate % difference based on the total original size vs the total generated sizes
        if orig_mb > 0:
            diff = orig_mb - new_mb
            pct = (diff / orig_mb) * 100
            if diff > 0:
                print(f"Size difference: {diff:.2f} MB saved compared to original files ({pct:.1f}% space saving)")
            else:
                print(f"Size difference: {abs(diff):.2f} MB increased (Multiple variants generated)")
    
    print(f"Detailed logs written to {log_file}")

if __name__ == '__main__':
    main()
