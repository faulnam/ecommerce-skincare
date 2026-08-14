import os
import boto3
import urllib.request
from dotenv import load_dotenv

load_dotenv()

s3 = boto3.client(
    's3',
    endpoint_url=os.getenv('CLOUDFLARE_R2_ENDPOINT'),
    aws_access_key_id=os.getenv('CLOUDFLARE_R2_ACCESS_KEY_ID'),
    aws_secret_access_key=os.getenv('CLOUDFLARE_R2_SECRET_ACCESS_KEY'),
    region_name='auto'
)

bucket = os.getenv('CLOUDFLARE_R2_BUCKET', 'norapadel-development')

# 1. Check size directly on R2 S3 Object
res = s3.head_object(Bucket=bucket, Key='home/hero-player.jpg')
r2_size = res['ContentLength']

print(f"R2 Object Size for 'home/hero-player.jpg': {r2_size} bytes ({r2_size / 1024:.2f} KB)")

# 2. Check HTTP CDN response header
cdn_url = 'https://cdn3.norapadel.com/home/hero-player.jpg'
req = urllib.request.Request(cdn_url, method='HEAD')
with urllib.request.urlopen(req) as response:
    content_len = response.headers.get('Content-Length')
    print(f"CDN Header Content-Length for '{cdn_url}': {content_len} bytes ({int(content_len) / 1024:.2f} KB)")
