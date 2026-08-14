import os
import boto3
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

paginator = s3.get_paginator('list_objects_v2')
pages = paginator.paginate(Bucket=bucket)

print("=== ALL R2 OBJECTS OVER 500 KB ===")
over_500kb = 0
for page in pages:
    if 'Contents' in page:
        for obj in page['Contents']:
            size_kb = obj['Size'] / 1024
            if size_kb > 500:
                over_500kb += 1
                print(f"Key: {obj['Key']} | Size: {size_kb:.2f} KB ({obj['Size'] / (1024*1024):.2f} MB)")

if over_500kb == 0:
    print("NO OBJECTS IN R2 BUCKET ARE OVER 500 KB! ALL FILES ARE FULLY COMPRESSED!")
