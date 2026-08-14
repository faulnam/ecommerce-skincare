import os
import io
import boto3
from dotenv import load_dotenv
from PIL import Image, ImageOps

load_dotenv()

s3 = boto3.client(
    's3',
    endpoint_url=os.getenv('CLOUDFLARE_R2_ENDPOINT'),
    aws_access_key_id=os.getenv('CLOUDFLARE_R2_ACCESS_KEY_ID'),
    aws_secret_access_key=os.getenv('CLOUDFLARE_R2_SECRET_ACCESS_KEY'),
    region_name='auto'
)

bucket = os.getenv('CLOUDFLARE_R2_BUCKET', 'norapadel-development')

key = 'home/hero-player.jpg'

res = s3.get_object(Bucket=bucket, Key=key)
raw_bytes = res['Body'].read()
orig_size = len(raw_bytes)
print(f"Current size of '{key}' on R2: {orig_size} bytes ({orig_size / 1024:.2f} KB / {orig_size / (1024*1024):.2f} MB)")

img = Image.open(io.BytesIO(raw_bytes))
img = ImageOps.exif_transpose(img)
w, h = img.size
print(f"Original dimensions: {w}x{h}")

# Force resize to max 1600px width/height and quality=75
if max(w, h) > 1600:
    ratio = 1600 / float(max(w, h))
    new_size = (int(w * ratio), int(h * ratio))
    img = img.resize(new_size, Image.Resampling.LANCZOS)
    print(f"Resized dimensions to: {img.size}")

out = io.BytesIO()
if img.mode in ('RGBA', 'P', 'LA'):
    img = img.convert('RGB')

img.save(out, format='JPEG', quality=75, optimize=True)
compressed_bytes = out.getvalue()
new_size = len(compressed_bytes)

print(f"Compressed size: {new_size} bytes ({new_size / 1024:.2f} KB / {new_size / (1024*1024):.2f} MB)")

# Upload to R2
s3.put_object(
    Bucket=bucket,
    Key=key,
    Body=compressed_bytes,
    ContentType='image/jpeg'
)

print(f"SUCCESS! Uploaded compressed '{key}' ({new_size / 1024:.2f} KB) to R2!")
