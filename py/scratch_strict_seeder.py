import re

with open('database/seeders/ShopeeProductsSeeder.php', 'r', encoding='utf-8') as f:
    content = f.read()

# I want to inject STRICT brand and type logic inside:
# foreach ($products as &$product) {
#     $product['id'] = (string) Str::uuid();

strict_injection = """
        $brands = ['Bullpadel', 'Babolat', 'Nox', 'Alpha', 'Zephyr', 'Arronax'];
        foreach ($products as &$product) {
            $found_brand = 'NoraPadel';
            $name = $product['name'] ?? '';
            foreach ($brands as $b) {
                if (stripos($name, $b) !== false) {
                    $found_brand = $b;
                    break;
                }
            }
            $product['brand'] = $found_brand;
            
            $name_lower = strtolower($name);
            if (strpos($name_lower, 'racket') !== false || strpos($name_lower, 'raket') !== false) {
                $product['type'] = 'Racket';
            } elseif (strpos($name_lower, 'shoes') !== false || strpos($name_lower, 'sepatu') !== false) {
                $product['type'] = 'Shoes';
            } else {
                $product['type'] = 'Accecories';
            }
"""

# Replace the previous block we injected
content = re.sub(r"\$brands = \['Babolat', 'Bullpadel'.*?\$product\['brand'\] = \$found_brand;", strict_injection, content, flags=re.DOTALL)

with open('database/seeders/ShopeeProductsSeeder.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Strict brand and type logic injected into seeder loop!")
