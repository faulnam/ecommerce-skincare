import re

with open('database/seeders/ShopeeProductsSeeder.php', 'r', encoding='utf-8') as f:
    content = f.read()

# I want to inject brand logic inside:
# foreach ($products as &$product) {
#     $product['id'] = (string) Str::uuid();

injection = """
        $brands = ['Babolat', 'Bullpadel', 'Nox', 'Odea', 'Head', 'Adidas', 'Wilson', 'Kuikma', 'Starvie', 'Asics', 'Mizuno', 'Joma', 'Drop Shot', 'RS Padel', 'Siux', 'Varlion', 'Black Crown', 'Dunlop', 'Puma', 'Nike', 'Decathlon'];
        foreach ($products as &$product) {
            $found_brand = null;
            $name = $product['name'] ?? '';
            foreach ($brands as $b) {
                if (stripos($name, $b) !== false) {
                    $found_brand = $b;
                    break;
                }
            }
            if (!$found_brand && !empty($name)) {
                $first_word = explode(' ', $name)[0];
                if (!in_array(strtolower($first_word), ['racket', 'knee', 'grip', 'bag', 'tas', 'sepatu', 'baju', 'kaos', 'overgrip', 'socks', 'wristband', 'headband', 'protector', 't-shirt', 'bola', 'sepeda', 'tourna', 'bisa', 'paket', 'set'])) {
                    $found_brand = ucfirst(strtolower($first_word));
                } else {
                    $found_brand = 'NoraPadel';
                }
            }
            $product['brand'] = $found_brand;
"""

if "$brands = ['Babolat'" not in content:
    content = content.replace("foreach ($products as &$product) {", injection, 1)
    
with open('database/seeders/ShopeeProductsSeeder.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Brand logic injected into seeder loop!")
