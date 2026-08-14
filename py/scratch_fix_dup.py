import re
with open('database/seeders/ShopeeProductsSeeder.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = re.sub(r"('name'\s*=>\s*'Knee Pad Maximum Protector'.*?'description'\s*=>\s*'<h2>.*?</h2><p>)Lightweight", r"\g<1>Premium", content, flags=re.DOTALL)

with open('database/seeders/ShopeeProductsSeeder.php', 'w', encoding='utf-8') as f:
    f.write(content)
