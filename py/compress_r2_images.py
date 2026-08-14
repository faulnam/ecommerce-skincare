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

# Strict Target Max Size: 500 KB (0.5 MB)
MAX_FILE_SIZE = 500 * 1024  # 500 KB
MAX_DIMENSION = 1600  # Max width/height in pixels

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

def compress_single_image(raw_bytes: bytes, filename: str) -> tuple[bytes, str]:
    ext = os.path.splitext(filename)[1].lower()
    
    # Khusus untuk logo, kembalikan gambar asli tanpa disentuh (transparan & ukuran asli dipertahankan)
    if 'logo' in filename.lower():
        mime = 'image/png' if ext == '.png' else ('image/jpeg' if ext in ['.jpg', '.jpeg'] else 'image/webp')
        if ext == '.svg': mime = 'image/svg+xml'
        return raw_bytes, mime

    
    try:
        img = Image.open(io.BytesIO(raw_bytes))
        img = ImageOps.exif_transpose(img)
    except Exception as e:
        return raw_bytes, ""

    # Determine format
    if ext in ['.jpg', '.jpeg']:
        fmt = 'JPEG'
        mime = 'image/jpeg'
    elif ext == '.png':
        fmt = 'PNG'
        mime = 'image/png'
    elif ext == '.webp':
        fmt = 'WEBP'
        mime = 'image/webp'
    else:
        return raw_bytes, ""

    # Resize if max dimension > 1600px
    w, h = img.size
    if max(w, h) > MAX_DIMENSION:
        ratio = MAX_DIMENSION / float(max(w, h))
        new_size = (int(w * ratio), int(h * ratio))
        img = img.resize(new_size, Image.Resampling.LANCZOS)

    # Convert transparent images to RGB JPEG if file size is very large (> 500KB)
    out = io.BytesIO()
    
    if fmt == 'JPEG':
        if img.mode in ('RGBA', 'P', 'LA'):
            img = img.convert('RGB')
        quality = 80
        img.save(out, format='JPEG', quality=quality, optimize=True)
        while out.tell() > MAX_FILE_SIZE and quality > 30:
            out = io.BytesIO()
            quality -= 10
            img.save(out, format='JPEG', quality=quality, optimize=True)
            
    elif fmt == 'WEBP':
        quality = 80
        img.save(out, format='WEBP', quality=quality, method=5)
        while out.tell() > MAX_FILE_SIZE and quality > 30:
            out = io.BytesIO()
            quality -= 10
            img.save(out, format='WEBP', quality=quality, method=5)

    elif fmt == 'PNG':
        # Optimize PNG
        img.save(out, format='PNG', optimize=True)
        # If PNG is still > 500 KB, convert to JPEG for massive size reduction
        if out.tell() > MAX_FILE_SIZE:
            out = io.BytesIO()
            if img.mode in ('RGBA', 'P', 'LA'):
                # Handle alpha background by overlaying on white
                background = Image.new('RGB', img.size, (255, 255, 255))
                if img.mode == 'RGBA':
                    background.paste(img, mask=img.split()[3])
                else:
                    background.paste(img)
                img = background
            else:
                img = img.convert('RGB')
                
            mime = 'image/jpeg'
            quality = 75
            img.save(out, format='JPEG', quality=quality, optimize=True)
            while out.tell() > MAX_FILE_SIZE and quality > 30:
                out = io.BytesIO()
                quality -= 10
                img.save(out, format='JPEG', quality=quality, optimize=True)

    res_bytes = out.getvalue()

    # Final dimension scaling if still > 500KB
    while len(res_bytes) > MAX_FILE_SIZE:
        w, h = img.size
        img = img.resize((int(w * 0.8), int(h * 0.8)), Image.Resampling.LANCZOS)
        out = io.BytesIO()
        if mime == 'image/jpeg' or fmt == 'JPEG':
            if img.mode in ('RGBA', 'P', 'LA'):
                img = img.convert('RGB')
            img.save(out, format='JPEG', quality=60, optimize=True)
        else:
            img.save(out, format=fmt, optimize=True)
        res_bytes = out.getvalue()

    return res_bytes, mime

def main():
    print(f"==================================================")
    print(f"Aggressive Image Compression on R2 Bucket: '{R2_BUCKET}'")
    print(f"Strict Target Max Size: 500 KB ({MAX_FILE_SIZE} bytes)")
    print(f"==================================================\n")

    paginator = s3.get_paginator('list_objects_v2')
    pages = paginator.paginate(Bucket=R2_BUCKET)

    total_objects = 0
    compressed_count = 0
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

                compressed_bytes, mime_type = compress_single_image(raw_bytes, key)
                new_size = len(compressed_bytes)

                if new_size < size:
                    put_kwargs = {
                        'Bucket': R2_BUCKET,
                        'Key': key,
                        'Body': compressed_bytes,
                    }
                    if mime_type:
                        put_kwargs['ContentType'] = mime_type

                    s3.put_object(**put_kwargs)
                    compressed_count += 1
                    total_new_size += new_size
                    new_kb = new_size / 1024
                    saving = ((size - new_size) / size) * 100
                    print(f"[REDUCED] {key}: {orig_kb:.1f} KB -> {new_kb:.1f} KB (-{saving:.1f}%)")
                else:
                    skipped_count += 1
                    total_new_size += size
                    print(f"[OK] {key}: {orig_kb:.1f} KB (Already optimal)")

            except Exception as e:
                print(f"[ERROR] {key}: {e}")
                skipped_count += 1
                total_new_size += size

    total_orig_mb = total_orig_size / (1024 * 1024)
    total_new_mb = total_new_size / (1024 * 1024)
    total_saved_mb = total_orig_mb - total_new_mb

    print(f"\n==================================================")
    print(f"COMPRESSION SUMMARY:")
    print(f"Total Objects Scanned: {total_objects}")
    print(f"Total Images Compressed: {compressed_count}")
    print(f"Original Bucket Size: {total_orig_mb:.2f} MB")
    print(f"New Bucket Size: {total_new_mb:.2f} MB")
    print(f"Total Space Saved: {total_saved_mb:.2f} MB")
    print(f"==================================================")

if __name__ == '__main__':
    main()
