<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LuxuryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Cart::truncate();
        OrderItem::truncate();
        ProductVariant::truncate();
        Product::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $productsData = [
            [
                'name' => 'Premium Silk Pashmina',
                'description' => 'Pashmina eksklusif berbahan silk premium yang memberikan kilau mewah dan tekstur sangat lembut. Mudah dibentuk, tegak di dahi, dan tidak menerawang. Sempurna untuk acara formal maupun gaya elegan sehari-hari.',
                'price' => 150000,
                'stock' => 100,
                'weight' => 200,
                'image' => 'product-1-hijab.jpg',
                'category' => 'Hijab',
                'type' => 'hijab',
                'brand' => 'Nora',
            ],
            [
                'name' => 'Signature Instan Bergo',
                'description' => 'Hijab instan bergo dengan pet antem (anti tembem) yang membingkai wajah dengan sempurna. Terbuat dari bahan jersey premium yang adem, flowy, dan anti-kusut. Pilihan tepat untuk tampil cantik tanpa ribet.',
                'price' => 125000,
                'stock' => 100,
                'weight' => 250,
                'image' => 'product-2-hijab.jpg',
                'category' => 'Hijab',
                'type' => 'hijab',
                'brand' => 'Nora',
            ],
            [
                'name' => 'Kala Ruffle Blouse',
                'description' => 'Blouse cantik dengan detail ruffle di bagian lengan dan dada yang memberikan kesan feminin nan elegan. Terbuat dari katun linen yang breathable, cocok dipadukan dengan celana kulot atau rok untuk ke kantor maupun hangout.',
                'price' => 250000,
                'stock' => 100,
                'weight' => 300,
                'image' => 'product-1-baju.jpg',
                'category' => 'Baju',
                'type' => 'apparel',
                'brand' => 'Nora',
            ],
            [
                'name' => 'Nayla Midi Dress',
                'description' => 'Midi dress berpotongan A-line yang anggun dengan aksen tali pinggang yang bisa dilepas pasang. Material maxmara lux yang jatuh dan tidak mudah kusut. Tampil effortless elegan untuk acara spesial Anda.',
                'price' => 350000,
                'stock' => 100,
                'weight' => 350,
                'image' => 'product-2-baju.jpg',
                'category' => 'Baju',
                'type' => 'apparel',
                'brand' => 'Nora',
            ],
            [
                'name' => 'Luna Highwaist Kulot',
                'description' => 'Celana kulot berpotongan high-waist yang membuat ilusi kaki lebih jenjang dan pinggang lebih ramping. Berbahan scuba premium yang stretch dan rapi, nyaman dipakai seharian untuk gaya kasual hingga profesional.',
                'price' => 195000,
                'stock' => 100,
                'weight' => 400,
                'image' => 'product-1-celana.jpg',
                'category' => 'Celana',
                'type' => 'apparel',
                'brand' => 'Nora',
            ],
            [
                'name' => 'Amira Pleated Skirt',
                'description' => 'Rok plisket premium dengan lipatan konsisten yang tidak mudah hilang meski dicuci berulang kali. Karet pinggang yang elastis dan material hyget super yang flowy menjadikannya andalan untuk gaya OOTD hijab Anda.',
                'price' => 180000,
                'stock' => 100,
                'weight' => 350,
                'image' => 'product-2-celana.jpg',
                'category' => 'Celana',
                'type' => 'apparel',
                'brand' => 'Nora',
            ],
            [
                'name' => 'Chloe Classic Mules',
                'description' => 'Sepatu mules bergaya klasik dengan detail pointed toe yang memberikan kesan kaki lebih ramping. Sol empuk dan material vegan leather berkualitas tinggi menjamin kenyamanan maksimal saat melangkah.',
                'price' => 280000,
                'stock' => 100,
                'weight' => 500,
                'image' => 'product-1-sandal.jpg',
                'category' => 'Sepatu',
                'type' => 'shoes',
                'brand' => 'Nora',
            ],
            [
                'name' => 'Bella Block Heels',
                'description' => 'Heels berhak tahu (block heels) 5 cm yang super nyaman dan stabil digunakan berjalan seharian. Aksen strap yang minimalis membuatnya mudah dipadupadankan dengan dress maupun celana untuk tampilan chic.',
                'price' => 320000,
                'stock' => 100,
                'weight' => 550,
                'image' => 'product-2-sandal.jpg',
                'category' => 'Sepatu',
                'type' => 'shoes',
                'brand' => 'Nora',
            ],
            [
                'name' => 'Anya Quilted Tote',
                'description' => 'Tote bag bertekstur quilted yang mewah dan elegan. Kompartemen sangat luas, muat laptop hingga 14 inch, pouch makeup, dan mukena. Material kulit sintetis yang tebal memastikan tas ini awet dan kokoh.',
                'price' => 350000,
                'stock' => 50,
                'weight' => 800,
                'image' => 'product-1-tas.jpg',
                'category' => 'Tas',
                'type' => 'bag',
                'brand' => 'Nora',
            ],
            [
                'name' => 'Zara Chain Sling Bag',
                'description' => 'Tas selempang berdesain timeless dengan kombinasi rantai emas elegan. Ukuran yang pas untuk esensial harian seperti ponsel, lipstik, dan dompet. Sempurna untuk melengkapi gaya hangout atau menghadiri pesta santai.',
                'price' => 290000,
                'stock' => 50,
                'weight' => 400,
                'image' => 'product-2-tas.jpg',
                'category' => 'Tas',
                'type' => 'bag',
                'brand' => 'Nora',
            ],
        ];

        $sizes = ['XS', 'S', 'M', 'L'];
        // Hex colors for basic variants
        $colors = [
            ['name' => 'Black', 'hex' => '#1a1a1a'],
            ['name' => 'Navy', 'hex' => '#1e3a8a'],
            ['name' => 'Sage', 'hex' => '#8fbc8f'],
            ['name' => 'Dusty Pink', 'hex' => '#d8b4e2'],
        ];

        foreach ($productsData as $data) {
            $product = Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'weight' => $data['weight'],
                'image' => $data['image'],
                'category' => $data['category'],
                'type' => $data['type'],
                'brand' => $data['brand'],
                'is_active' => true,
                'has_variants' => true,
            ]);

            // Add variants (Size & Color combinations)
            foreach ($sizes as $size) {
                // We'll pick 2 random colors for each size to make it realistic
                $selectedColors = array_rand(array_flip(array_column($colors, 'name')), 2);
                if (!is_array($selectedColors)) {
                    $selectedColors = [$selectedColors];
                }

                foreach ($selectedColors as $colorName) {
                    $colorInfo = collect($colors)->firstWhere('name', $colorName);
                    
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $size . ' - ' . $colorName,
                        // Storing hex in image field temporarily since we don't have a color_hex field in variants.
                        // Or we could encode it in the name. Let's encode it in a structured way if needed, 
                        // but since the UI needs hex, maybe we can append it to the name.
                        // Actually, looking at the UI, we need to extract colors. 
                        // I will append [hex] to the variant name so it can be parsed in the view.
                        'image' => null, 
                        'stock' => 10,
                        'price' => $product->price,
                        'is_active' => true,
                        // We use a convention for the name: Size | ColorName | #hexcode
                        'name' => $size . '|' . $colorName . '|' . $colorInfo['hex']
                    ]);
                }
            }
        }
    }
}
