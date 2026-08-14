import re
from collections import Counter

with open('database/seeders/ShopeeProductsSeeder.php', 'r', encoding='utf-8') as f:
    content = f.read()
    
descs = re.findall(r"'description'\s*=>\s*'(.*?)'", content)
print(f"Total products matched: {len(descs)}")
print(f"Unique descriptions: {len(set(descs))}")

c = Counter(descs)
for k, v in c.most_common(5):
    if v > 1:
        print(f"{v} times: {k[:50]}...")
