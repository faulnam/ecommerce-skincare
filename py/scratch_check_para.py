import re
from collections import Counter

with open('database/seeders/ShopeeProductsSeeder.php', 'r', encoding='utf-8') as f:
    content = f.read()

descs = re.findall(r"'name'\s*=>\s*'(.*?)'.*?'description'\s*=>\s*'(.*?)'", content, flags=re.DOTALL)

d_clean = [(name, re.sub(r'<h2>.*?</h2>', '', d)) for name, d in descs]
c = Counter([d for n, d in d_clean])
dups = [k for k, v in c.items() if v > 1]

for n, d in d_clean:
    if d in dups:
        print(f"Product: {n}")
        print(f"Desc: {d[:50]}...")
