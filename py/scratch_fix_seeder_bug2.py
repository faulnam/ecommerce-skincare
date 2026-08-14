with open('database/seeders/ShopeeProductsSeeder.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Instead of complex regex, let's just do simple text replacements!
content = content.replace("[:\\s]+([^\\n\\r.]+)", "\\s*[:\\-]\\s*([^\\n\\r.]{1,40})")
# Wait, if I replace [:\s]+([^\n\r.]+) with \s*[:\-]\s*([^\n\r.]{1,40}), it will strictly require a colon or dash, and limit match to 40 characters!

with open('database/seeders/ShopeeProductsSeeder.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Regexes replaced!")
