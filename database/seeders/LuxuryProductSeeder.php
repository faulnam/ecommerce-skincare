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
                'name' => 'Centella Asiatica Calming Serum',
                'description' => 'Serum penenang kulit sensitif dengan konsentrasi 85% Centella Asiatica alami dan Madecassoside. Efektif meredakan kemerahan, menenangkan iritasi, mengontrol minyak berlebih, dan mempercepat pemulihan jerawat tanpa rasa lengket.',
                'price' => 149000,
                'discount_percent' => 0,
                'stock' => 120,
                'weight' => 150,
                'image' => 'product-1-skincare.jpg',
                'category' => 'Serum',
                'type' => 'serum',
                'brand' => 'LUMINA',
            ],
            [
                'name' => 'Ceramide Barrier Moisture Gel',
                'description' => 'Pelembap bertekstur gel ringan dengan 5X Ceramide, Hyaluronic Acid, dan Centella. Menghidrasi mendalam, memperbaiki skin barrier yang rusak, mengunci kelembapan 24 jam, serta memberikan sensasi dingin yang menyegarkan di kulit.',
                'price' => 139000,
                'discount_percent' => 50,
                'stock' => 100,
                'weight' => 200,
                'image' => 'product-2-skincare.jpg',
                'category' => 'Moisturizer',
                'type' => 'moisturizer',
                'brand' => 'LUMINA',
            ],
            [
                'name' => 'Gentle Low pH Amino Cleanser',
                'description' => 'Pembersih wajah berbusa lembut dengan pH seimbang 5.5 yang aman untuk skin barrier. Diformulasikan dengan Amino Acid Complex dan Chamomile Extract untuk membersihkan kotoran hingga ke pori-pori tanpa rasa kesat atau kering tertarik.',
                'price' => 99000,
                'discount_percent' => 0,
                'stock' => 150,
                'weight' => 180,
                'image' => 'product-1-baju.jpg',
                'category' => 'Cleanser',
                'type' => 'cleanser',
                'brand' => 'LUMINA',
            ],
            [
                'name' => 'Niacinamide 10% Glow Brightening Serum',
                'description' => 'Serum pencerah intensif dengan 10% Niacinamide murni, Alpha Arbutin, dan Zinc PCA. Membantu menyamarkan noda hitam bekas jerawat (PIE/PIH), meratakan warna kulit tidak merata, serta membuat wajah glowing bercahaya alami.',
                'price' => 159000,
                'discount_percent' => 50,
                'stock' => 110,
                'weight' => 150,
                'image' => 'product-2-baju.jpg',
                'category' => 'Serum',
                'type' => 'serum',
                'brand' => 'LUMINA',
            ],
            [
                'name' => 'Hyaluronic Hydra Firming Essence',
                'description' => 'Essence toner dengan 8 tipe multi-molecular Hyaluronic Acid yang meresap hingga lapisan kulit terdalam. Memberikan hidrasi instan, mengenyalkan kulit, dan mempersiapkan wajah menyerap tahapan skincare berikutnya secara maksimal.',
                'price' => 135000,
                'discount_percent' => 0,
                'stock' => 95,
                'weight' => 250,
                'image' => 'product-1-celana.jpg',
                'category' => 'Toner',
                'type' => 'toner',
                'brand' => 'LUMINA',
            ],
            [
                'name' => 'Mugwort Pore Clarifying Clay Mask',
                'description' => 'Masker bilas berbahan Mugwort alami dan Kaolin Clay murni untuk membersihkan komedo, mengecilkan pori-pori, dan menenangkan jerawat aktif. Tekstur creamy yang lembut mudah diaplikasikan dan tidak membuat kulit kering.',
                'price' => 115000,
                'discount_percent' => 50,
                'stock' => 80,
                'weight' => 170,
                'image' => 'product-2-celana.jpg',
                'category' => 'Treatment',
                'type' => 'treatment',
                'brand' => 'LUMINA',
            ],
            [
                'name' => 'Daily UV Shield Sunscreen SPF 50+ PA++++',
                'description' => 'Tabir surya bertekstur seringan air tanpa rasa lengket dan tanpa whitecast. Dilengkapi kombinasi 4 chemical filter modern serta Vitamin E untuk perlindungan maksimal dari sinar UVA, UVB, dan polusi lingkungan sehari-hari.',
                'price' => 119000,
                'discount_percent' => 0,
                'stock' => 140,
                'weight' => 120,
                'image' => 'product-1-sandal.jpg',
                'category' => 'Sunscreen',
                'type' => 'sunscreen',
                'brand' => 'LUMINA',
            ],
            [
                'name' => 'Retinol 1% Rejuvenating Night Fluid',
                'description' => 'Perawatan malam anti-aging dengan teknologi encapsulated retinol 1% yang lembut dan stabil. Menstimulasi regenerasi sel kulit baru, menyamarkan garis halus, memperbaiki tekstur kulit kasar, dan meningkatkan elastisitas wajah.',
                'price' => 189000,
                'discount_percent' => 50,
                'stock' => 75,
                'weight' => 130,
                'image' => 'product-2-sandal.jpg',
                'category' => 'Serum',
                'type' => 'serum',
                'brand' => 'LUMINA',
            ],
            [
                'name' => 'Deluxe Glowing Skin 4-Step Routine Set',
                'description' => 'Paket bundel perawatan lengkap 4 langkah untuk kulit sehat glowing impian: Gentle Cleanser 100ml, Hydra Essence 100ml, Niacinamide Serum 30ml, dan Barrier Moisture Gel 50ml. Hemat hingga 35% lebih terjangkau.',
                'price' => 349000,
                'discount_percent' => 0,
                'stock' => 50,
                'weight' => 700,
                'image' => 'product-1-tas.jpg',
                'category' => 'Paket Bundel',
                'type' => 'bundle',
                'brand' => 'LUMINA',
            ],
            [
                'name' => 'Ultimate Barrier Repair Travel Kit',
                'description' => 'Paket hemat travel-friendly dalam pouch eksklusif water-resistant. Berisi 4 produk mini size esensial penyelamat skin barrier rusak saat bepergian atau untuk pemula yang ingin mencoba khasiat rangkaian LUMINA Skincare.',
                'price' => 279000,
                'discount_percent' => 50,
                'stock' => 60,
                'weight' => 400,
                'image' => 'product-2-tas.jpg',
                'category' => 'Paket Bundel',
                'type' => 'bundle',
                'brand' => 'LUMINA',
            ],
        ];

        $sizes = ['10ml', '30ml', '50ml'];
        // Skincare finishes & shades
        $finishes = [
            ['name' => 'Dewy Glow', 'hex' => '#F5EBE0'],
            ['name' => 'Calming Sage', 'hex' => '#D8E2DC'],
            ['name' => 'Hydra Fresh', 'hex' => '#E0EDF4'],
            ['name' => 'Rose Petal', 'hex' => '#F2D5CE'],
        ];

        foreach ($productsData as $data) {
            $discountPercent = $data['discount_percent'] ?? 0;
            $product = Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'price' => $data['price'],
                'discount_percent' => $discountPercent,
                'discount_start' => $discountPercent > 0 ? now() : null,
                'discount_end' => $discountPercent > 0 ? now()->addYears(2) : null,
                'stock' => $data['stock'],
                'weight' => $data['weight'],
                'image' => $data['image'],
                'category' => $data['category'],
                'type' => $data['type'],
                'brand' => $data['brand'],
                'is_active' => true,
                'has_variants' => true,
            ]);

            // Add variants (Size/Volume & Skin Finish combinations)
            foreach ($sizes as $size) {
                $selectedFinishes = array_rand(array_flip(array_column($finishes, 'name')), 2);
                if (!is_array($selectedFinishes)) {
                    $selectedFinishes = [$selectedFinishes];
                }

                foreach ($selectedFinishes as $finishName) {
                    $finishInfo = collect($finishes)->firstWhere('name', $finishName);
                    
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $size . '|' . $finishName . '|' . $finishInfo['hex'],
                        'image' => null, 
                        'stock' => 15,
                        'price' => $product->price,
                        'discount_percent' => $discountPercent,
                        'discount_start' => $product->discount_start,
                        'discount_end' => $product->discount_end,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
