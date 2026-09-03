<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Review::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // ================================================
        // BANK REVIEW SPESIFIK PRODUK SKINCARE
        // ================================================
        $bankReview = [
            'centella asiatica calming serum' => [
                ['name' => 'nadia_beauty', 'rating' => 5, 'comment' => 'Serum penyelamat saat kulit lagi ngamuk atau breakout parah! Kemerahan langsung reda dalam 2 hari pemakaian.', 'is_verified' => true],
                ['name' => 'clarissa_m', 'rating' => 5, 'comment' => 'Teksturnya ringan banget, cepat meresap dan nggak greasy sama sekali di kulit kombinasi berminyak.', 'is_verified' => true],
                ['name' => 'dian_lestari', 'rating' => 5, 'comment' => 'Jerawat mendem jadi lebih cepet kempes tanpa bikin kulit kering mengelupas. Pasti repurchase!', 'is_verified' => true],
            ],
            'ceramide barrier moisture gel' => [
                ['name' => 'amanda_glow', 'rating' => 5, 'comment' => 'Moisturizer terbaik untuk skin barrier rusak! Ada sensasi dingin menyegarkan saat diaplikasikan ke wajah.', 'is_verified' => true],
                ['name' => 'selvi_angela', 'rating' => 5, 'comment' => 'Bangun pagi kulit kerasa kenyal dan plumpy banget. Nggak bikin pori-pori tersumbat sama sekali.', 'is_verified' => true],
                ['name' => 'rani_wardani', 'rating' => 4, 'comment' => 'Bagus banget melembapkan, ukurannya semoga ada yang jumbo size 100ml!', 'is_verified' => true],
            ],
            'gentle low ph amino cleanser' => [
                ['name' => 'fathia_zahra', 'rating' => 5, 'comment' => 'Cleanser paling gentle yang pernah aku coba. Busanya lembut dan sama sekali nggak ada sensasi kulit ketarik atau kesat.', 'is_verified' => true],
                ['name' => 'bima_putra', 'rating' => 5, 'comment' => 'Cocok buat kulit sensitif. Bersih maksimal dan nggak bikin iritasi di area mata.', 'is_verified' => true],
            ],
            'niacinamide 10% glow brightening serum' => [
                ['name' => 'putri_ayu', 'rating' => 5, 'comment' => 'Noda hitam bekas jerawat (PIH) pudar signifikan setelah pemakaian 3 minggu rutin. Kulit jadi glowing natural.', 'is_verified' => true],
                ['name' => 'kartika_dewi', 'rating' => 5, 'comment' => 'Nggak ada sensasi tingling atau perih. Formulanya stabil dan warnanya tetap bening tidak teroksidasi.', 'is_verified' => true],
            ],
            'hyaluronic hydra firming essence' => [
                ['name' => 'siti_marwah', 'rating' => 5, 'comment' => 'Hydrating essence juara! Kulit dehidrasi langsung terasa kenyal lembap seharian.', 'is_verified' => true],
                ['name' => 'indah_permatasari', 'rating' => 5, 'comment' => 'Bikin makeup jauh lebih nempel dan nggak gampang cakey. Wajib masuk rutinitas harian.', 'is_verified' => true],
            ],
            'mugwort pore clarifying clay mask' => [
                ['name' => 'farah_fauziah', 'rating' => 5, 'comment' => 'Masker bilas ternyaman. Komedo di hidung gampang terangkat dan pori-pori kelihatan lebih rapat dan bersih.', 'is_verified' => true],
                ['name' => 'alvin_ramadhan', 'rating' => 5, 'comment' => 'Menenangkan jerawat yang meradang. Pas dibilas gampang banget nggak perlu digosok keras.', 'is_verified' => true],
            ],
            'daily uv shield sunscreen spf 50+ pa++++' => [
                ['name' => 'tania_natalia', 'rating' => 5, 'comment' => 'No whitecast sama sekali! Tekstur seringan air, cepat meresap dan nggak perih saat kena keringat atau mata.', 'is_verified' => true],
                ['name' => 'yoga_pratama', 'rating' => 5, 'comment' => 'Finish-nya dewy sehat tapi nggak bikin muka tambang minyak. Sunscreen ternyaman buat pria juga.', 'is_verified' => true],
            ],
            'retinol 1% rejuvenating night fluid' => [
                ['name' => 'dr_helen', 'rating' => 5, 'comment' => 'Sebagai pemula retinol, produk ini sangat ramah di kulit. Nggak ada purging berlebih dan tekstur kulit jadi super halus.', 'is_verified' => true],
                ['name' => 'maya_kusuma', 'rating' => 5, 'comment' => 'Garis halus di dahi dan smile line tampak lebih tersamarkan. Wajah terasa kencang dan awet muda.', 'is_verified' => true],
            ],
            'deluxe glowing skin 4-step routine set' => [
                ['name' => 'anissa_tri', 'rating' => 5, 'comment' => 'Paket bundel super hemat! Semua produknya bekerja sinergis. Kulitku yang dulu kusam sekarang dapat compliment terus.', 'is_verified' => true],
                ['name' => 'melinda_s', 'rating' => 5, 'comment' => 'Packaging box-nya sangat mewah dan elegan, cocok banget buat kado atau seserahan.', 'is_verified' => true],
            ],
            'ultimate barrier repair travel kit' => [
                ['name' => 'shafira_nurul', 'rating' => 5, 'comment' => 'Penyelamat pas traveling ke daerah dingin berangin! Pouch-nya rapi dan ukurannya pas masuk ke cabin bag.', 'is_verified' => true],
                ['name' => 'vivi_claudia', 'rating' => 5, 'comment' => 'Cocok buat yang baru mau coba rangkaian LUMINA sebelum beli full size. Worth it banget!', 'is_verified' => true],
            ],
        ];

        // Fallback ulasan umum
        $ulasanUmum = [
            ['rating' => 5, 'comment' => 'Produk original dan tersegel rapi dengan barcode BPOM resmi. Pengiriman cepat dan aman.'],
            ['rating' => 5, 'comment' => 'Sangat cocok di kulitku yang sensitif. Teksturnya mewah dan cepat meresap.'],
            ['rating' => 4, 'comment' => 'Kualitas formulasi jempolan, hasilnya nyata terlihat dalam beberapa minggu.'],
            ['rating' => 5, 'comment' => 'Admin beauty advisor-nya sangat ramah dan responsif dalam merekomendasikan produk yang tepat.'],
            ['rating' => 5, 'comment' => 'Kemasan elegan dan higienis. Pasti akan langganan terus di LUMINA Skincare.'],
        ];

        $users = User::where('role', 'customer')->get();
        if ($users->isEmpty()) {
            $users = User::all();
        }

        $products = Product::all();

        foreach ($products as $product) {
            $key = strtolower(trim($product->name));
            $reviewsToAdd = $bankReview[$key] ?? $ulasanUmum;

            foreach ($reviewsToAdd as $rev) {
                $user = $users->random();
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user ? $user->id : null,
                    'reviewer_name' => $rev['name'] ?? ($user ? $user->name : 'Pelanggan Terverifikasi'),
                    'rating' => $rev['rating'],
                    'comment' => $rev['comment'],
                    'is_approved' => true,
                    'is_verified' => true,
                    'created_at' => now()->subDays(rand(1, 45)),
                ]);
            }
        }
    }
}
