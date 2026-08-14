import os
import io
import sys
import boto3
from dotenv import load_dotenv
from PIL import Image, ImageOps

# Load environment variables from .env
load_dotenv()

R2_ACCESS_KEY = os.getenv('CLOUDFLARE_R2_ACCESS_KEY_ID')
R2_SECRET_KEY = os.getenv('CLOUDFLARE_R2_SECRET_ACCESS_KEY')
R2_BUCKET = os.getenv('CLOUDFLARE_R2_BUCKET', 'norapadel-development')
R2_ENDPOINT = os.getenv('CLOUDFLARE_R2_ENDPOINT')

MAX_DIMENSION = 1600

if not R2_ACCESS_KEY or not R2_SECRET_KEY or not R2_ENDPOINT:
    print("Error: Cloudflare R2 credentials not found in .env file!")
    sys.exit(1)

s3 = boto3.client(
    's3',
    endpoint_url=R2_ENDPOINT,
    aws_access_key_id=R2_ACCESS_KEY,
    aws_secret_access_key=R2_SECRET_KEY,
    region_name='auto'
)

def convert_to_webp(raw_bytes: bytes, filename: str) -> tuple[bytes, str]:
    ext = os.path.splitext(filename)[1].lower()
    
    # Khusus untuk logo, jangan diapa-apakan
    if 'logo' in filename.lower():
        mime = 'image/png' if ext == '.png' else ('image/jpeg' if ext in ['.jpg', '.jpeg'] else 'image/webp')
        if ext == '.svg': mime = 'image/svg+xml'
        return raw_bytes, mime

    try:
        img = Image.open(io.BytesIO(raw_bytes))
        img = ImageOps.exif_transpose(img)
    except Exception as e:
        return raw_bytes, ""

    # Resize if max dimension > 1600px
    w, h = img.size
    if max(w, h) > MAX_DIMENSION:
        ratio = MAX_DIMENSION / float(max(w, h))
        new_size = (int(w * ratio), int(h * ratio))
        img = img.resize(new_size, Image.Resampling.LANCZOS)

    out = io.BytesIO()
    quality = 85

    # Always save as WebP
    # WebP lossy handles alpha channel perfectly.
    img.save(out, format='WEBP', quality=quality, method=5)

    return out.getvalue(), 'image/webp'

def main():
    print(f"==================================================")
    print(f"WebP Conversion on R2 Bucket: '{R2_BUCKET}'")
    print(f"All images will be converted to WebP (Content-Type: image/webp)")
    print(f"Original filenames and extensions will be KEPT.")
    print(f"==================================================\n")

    paginator = s3.get_paginator('list_objects_v2')
    pages = paginator.paginate(Bucket=R2_BUCKET)

    total_objects = 0
    converted_count = 0
    skipped_count = 0
    total_orig_size = 0
    total_new_size = 0

    valid_extensions = ('.jpg', '.jpeg', '.png', '.webp')

    for page in pages:
        if 'Contents' not in page:
            continue
        
        for obj in page['Contents']:
            key = obj['Key']
            size = obj['Size']
            total_orig_size += size
            total_objects += 1

            if not key.lower().endswith(valid_extensions):
                skipped_count += 1
                total_new_size += size
                continue

            orig_kb = size / 1024

            try:
                response = s3.get_object(Bucket=R2_BUCKET, Key=key)
                raw_bytes = response['Body'].read()
                current_ctype = response.get('ContentType', '')
                
                if 'logo' in key.lower():
                    skipped_count += 1
                    total_new_size += size
                    print(f"[SKIPPED] {key}: Logo file is protected.")
                    continue

                webp_bytes, mime_type = convert_to_webp(raw_bytes, key)
                new_size = len(webp_bytes)
                
                if mime_type == 'image/webp':
                    put_kwargs = {
                        'Bucket': R2_BUCKET,
                        'Key': key,
                        'Body': webp_bytes,
                        'ContentType': mime_type
                    }
                    
                    # Upload jika ukurannya lebih kecil atau formatnya di cloudflare belum webp
                    if new_size < size or current_ctype != 'image/webp':
                        s3.put_object(**put_kwargs)
                        converted_count += 1
                        total_new_size += new_size
                        new_kb = new_size / 1024
                        print(f"[CONVERTED] {key}: {orig_kb:.1f} KB -> {new_kb:.1f} KB (WebP)")
                    else:
                        skipped_count += 1
                        total_new_size += size
                        print(f"[OK] {key}: {orig_kb:.1f} KB (Already optimal WebP)")
                else:
                    skipped_count += 1
                    total_new_size += size
                    print(f"[SKIPPED] {key}: Could not convert.")

            except Exception as e:
                print(f"[ERROR] {key}: {e}")
                skipped_count += 1
                total_new_size += size

    total_orig_mb = total_orig_size / (1024 * 1024)
    total_new_mb = total_new_size / (1024 * 1024)
    total_saved_mb = total_orig_mb - total_new_mb

    print(f"\n==================================================")
    print(f"CONVERSION SUMMARY:")
    print(f"Total Objects Scanned: {total_objects}")
    print(f"Total Images Converted to WebP: {converted_count}")
    print(f"Original Bucket Size: {total_orig_mb:.2f} MB")
    print(f"New Bucket Size: {total_new_mb:.2f} MB")
    print(f"Total Space Saved: {total_saved_mb:.2f} MB")
    print(f"==================================================")

if __name__ == '__main__':
    main()
