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
        // Hapus semua review lama
        Review::truncate();
        $this->command->info('Review lama dihapus.');

        // ================================================
        // BANK REVIEW SPESIFIK PER PRODUK
        // ================================================
        $bankReview = [
            'premium silk pashmina' => [
                ['name' => 'athena_shigen', 'rating' => 5, 'comment' => 'Bahan silknya beneran premium, jatuh dan nggak kaku. Warnanya mewah banget dipakai ke kondangan.', 'is_verified' => true],
                ['name' => 'mudhanovia84', 'rating' => 5, 'comment' => 'Suka banget! Nggak gampang lecek dan gampang diatur di bagian dahi. Recommended!', 'is_verified' => true],
                ['name' => 'yudhadara18', 'rating' => 4, 'comment' => 'Cakep sih, tapi ukurannya kurang panjang sedikit buat model syari.', 'is_verified' => true],
            ],
            'signature instan bergo' => [
                ['name' => 'trizkiawawaw', 'rating' => 5, 'comment' => 'Instan bergonya enak banget buat daily. Pet-nya antem beneran bikin pipi tirus.', 'is_verified' => true],
                ['name' => 'slava8899', 'rating' => 5, 'comment' => 'Bahannya adem banget dipake seharian nggak bikin gerah. Udah punya 3 warna.', 'is_verified' => true],
            ],
            'kala ruffle blouse' => [
                ['name' => 'arvinsetiawan79', 'rating' => 5, 'comment' => 'Cuttingannya pas banget di badan, rufflenya manis nggak lebay. Cocok buat ngantor.', 'is_verified' => true],
                ['name' => 'widi_kendhi', 'rating' => 5, 'comment' => 'Bahan katun linen-nya juara, nggak gerah dan nyerap keringat. Must have item!', 'is_verified' => true],
            ],
            'nayla midi dress' => [
                ['name' => 'derichteha', 'rating' => 5, 'comment' => 'Jatuhnya bagus banget dress-nya, material maxmaranya mengkilap mewah. Jahitannya rapi.', 'is_verified' => true],
                ['name' => 'alamnh', 'rating' => 5, 'comment' => 'Elegan banget dipake ke acara formal. Talinya bisa dilepas jadi bisa diganti belt lain.', 'is_verified' => true],
            ],
            'luna highwaist kulot' => [
                ['name' => 'carlosardo', 'rating' => 5, 'comment' => 'Pinggangnya beneran high waist, bikin kaki kelihatan panjang. Bahannya stretch jadi nyaman buat duduk lama.', 'is_verified' => true],
                ['name' => 'filgi', 'rating' => 5, 'comment' => 'Bagus banget! Nggak nerawang dan tebalnya pas. Gampang dipadupadanin sama kemeja.', 'is_verified' => true],
            ],
            'amira pleated skirt' => [
                ['name' => 'gandhigo', 'rating' => 5, 'comment' => 'Plisketnya rapi banget dan konsisten. Pinggang karetnya nggak bikin engap.', 'is_verified' => true],
                ['name' => 'srikandi_32', 'rating' => 5, 'comment' => 'Suka sama flowy-nya pas dipakai jalan. Udah dicuci berkali-kali plisketnya tetep awet.', 'is_verified' => true],
            ],
            'chloe classic mules' => [
                ['name' => 'ryo_siregar', 'rating' => 5, 'comment' => 'Mulesnya empuk banget di bagian sol. Pointed toe-nya bikin kaki terlihat ramping dan elegan.', 'is_verified' => true],
                ['name' => 'pleat_pleats', 'rating' => 4, 'comment' => 'Bagus dan elegan, tapi buat yang kakinya lebar mungkin harus up 1 size biar nggak sempit di depan.', 'is_verified' => true],
            ],
            'bella block heels' => [
                ['name' => 'karimnazri', 'rating' => 5, 'comment' => 'Heels 5cm paling nyaman yang pernah aku coba. Dipakai jalan lama keliling mall nggak pegal.', 'is_verified' => true],
                ['name' => 'raksepatu.idn', 'rating' => 5, 'comment' => 'Desainnya cantik dan kokoh. Strapnya pas, nggak bikin lecet di kulit.', 'is_verified' => true],
            ],
            'anya quilted tote' => [
                ['name' => 'carlosardo', 'rating' => 5, 'comment' => 'Tas andalan ke kantor sekarang. Laptop, mukena, makeup semua muat. Keliatan mahal banget tasnya.', 'is_verified' => true],
                ['name' => 'meryfu89', 'rating' => 5, 'comment' => 'Jahitannya super rapi, kulit sintetisnya lentur tapi kokoh. Desain quiltednya mewah.', 'is_verified' => true],
            ],
            'zara chain sling bag' => [
                ['name' => 'rangganisa2431', 'rating' => 5, 'comment' => 'Ukurannya imut tapi pas buat naruh hp sama dompet lipat. Rantainya bikin tasnya makin elegan.', 'is_verified' => true],
                ['name' => 'luzasby', 'rating' => 5, 'comment' => 'Tasnya cantik banget aslinya. Cocok buat dibawa jalan-jalan santai atau nge-mall.', 'is_verified' => true],
            ],
        ];

        // ================================================
        // POOL REVIEW FALLBACK (untuk produk yg tidak ada di bankReview)
        // Dibuat bervariasi per produk menggunakan product_id sebagai seed
        // ================================================
        $fallbackPool = [
            ['name' => 'andi_wijaya88', 'rating' => 5, 'comment' => 'Produknya sangat bagus dan sesuai deskripsi. Kualitas premium, recommended banget!', 'is_verified' => true],
            ['name' => 'rina_susanti21', 'rating' => 4, 'comment' => 'Build quality oke, finishing rapi. Cukup puas dengan pembelian ini, worth it.', 'is_verified' => true],
            ['name' => 'budi_santoso', 'rating' => 5, 'comment' => 'Pelayanan cepat dan aman. Barang sampai dalam kondisi sempurna. Langsung dipakai dan performa maksimal.', 'is_verified' => true],
            ['name' => 'dian_kusuma77', 'rating' => 5, 'comment' => 'Suka banget sama produk ini. Nyaman dipakai dan hasilnya sesuai ekspektasi. Worth the price!', 'is_verified' => false],
            ['name' => 'eko_prasetyo', 'rating' => 4, 'comment' => 'Desainnya elegan dan enak dipakai. Pengiriman cepat. Overall puas.', 'is_verified' => true],
            ['name' => 'fani_mulyani', 'rating' => 5, 'comment' => 'Ini salah satu produk terbaik yang pernah saya beli. Teman-teman pada nanya beli di mana.', 'is_verified' => false],
            ['name' => 'gilang_rama', 'rating' => 4, 'comment' => 'Kualitas solid, bahan terasa premium. Stabil saat dipakai, meski perlu sedikit penyesuaian.', 'is_verified' => true],
            ['name' => 'hani_putri03', 'rating' => 5, 'comment' => 'Sangat nyaman dipakai, tidak licin. Sizing pas sesuai dengan ukuran biasa saya.', 'is_verified' => true],
            ['name' => 'indra_lesmana', 'rating' => 4, 'comment' => 'Material bagus, muat banyak perlengkapan. Packaging rapi dan aman sampai rumah.', 'is_verified' => true],
            ['name' => 'joko_tanto99', 'rating' => 5, 'comment' => 'Enak dipakai, tidak licin meski berkeringat. Durabilitas bagus, sudah 2 bulan masih oke.', 'is_verified' => false],
            ['name' => 'kartika_sari', 'rating' => 5, 'comment' => 'Langganan beli di sini. Produk original, packing aman, dan admin responsif. Top!', 'is_verified' => true],
            ['name' => 'lukman_hakim', 'rating' => 4, 'comment' => 'Ringan dan enak dipakai. Anak saya jadi lebih semangat latihan. Harga terjangkau.', 'is_verified' => true],
            ['name' => 'maya_anggraini', 'rating' => 5, 'comment' => 'Warna dan desainnya keren. Performa konsisten, jadi andalan saat main.', 'is_verified' => false],
            ['name' => 'nanda_perkasa', 'rating' => 4, 'comment' => 'Mudah dipasang dan melindungi dengan baik. Tidak mengganggu keseimbangan saat pakai.', 'is_verified' => true],
            ['name' => 'olivia_hart', 'rating' => 5, 'comment' => 'Comfort level tinggi dan support oke banget. Pertama kali coba dan langsung puas.', 'is_verified' => true],
            ['name' => 'ryan_practice', 'rating' => 5, 'comment' => 'Persis seperti foto, kualitas mantap. Seller fast response dan packing aman.', 'is_verified' => true],
            ['name' => 'sinta_dewi', 'rating' => 4, 'comment' => 'Sudah order 3x di sini, selalu puas. Barang ori dan pengiriman cepat.', 'is_verified' => true],
            ['name' => 'tommy_hijab', 'rating' => 5, 'comment' => 'Kualitasnya jauh di atas ekspektasi. Grip enak, balance oke. Highly recommended!', 'is_verified' => true],
            ['name' => 'umar_faruq', 'rating' => 5, 'comment' => 'Mantap, sesuai deskripsi. Pengiriman same day, packing rapih. Bonus grip juga ada!', 'is_verified' => true],
            ['name' => 'vina_sport', 'rating' => 4, 'comment' => 'Overall bagus, tapi sedikit lama pengirimannya. Kualitas produk tidak mengecewakan.', 'is_verified' => true],
        ];

        $users = User::whereIn('role', ['customer', 'admin'])->pluck('id');
        if ($users->isEmpty()) {
            $users = collect([1]);
        }
        $userIds = $users->values()->all();

        $products = Product::all();
        $totalCreated = 0;

        foreach ($products as $product) {
            $productNameLower = strtolower($product->name);
            $matchedKey = null;

            // Cari match di bankReview
            foreach ($bankReview as $key => $reviews) {
                if (str_contains($productNameLower, $key)) {
                    $matchedKey = $key;
                    break;
                }
            }

            if ($matchedKey !== null) {
                // Produk ada di bankReview → pakai review spesifik
                foreach ($bankReview[$matchedKey] as $r) {
                    Review::create([
                        'product_id'     => $product->id,
                        'user_id'        => $userIds[$totalCreated % count($userIds)],
                        'reviewer_name'  => $r['name'],
                        'order_id'       => null,
                        'rating'         => $r['rating'],
                        'comment'        => $r['comment'],
                        'quality_rating' => 95,
                        'sizing_rating'  => null,
                        'usual_size'     => null,
                        'is_verified'    => $r['is_verified'],
                        'is_approved'    => true,
                        'created_at'     => now()->subDays(rand(1, 90)),
                    ]);
                    $totalCreated++;
                }
            } else {
                // Produk tidak ada di bankReview → pakai 3 review fallback unik per produk
                // Gunakan product->id sebagai offset agar tiap produk dapat review berbeda
                $offset = ($product->id * 3) % count($fallbackPool);
                for ($i = 0; $i < 3; $i++) {
                    $r = $fallbackPool[($offset + $i) % count($fallbackPool)];
                    Review::create([
                        'product_id'     => $product->id,
                        'user_id'        => $userIds[$totalCreated % count($userIds)],
                        'reviewer_name'  => $r['name'],
                        'order_id'       => null,
                        'rating'         => $r['rating'],
                        'comment'        => $r['comment'],
                        'quality_rating' => rand(80, 96),
                        'sizing_rating'  => null,
                        'usual_size'     => null,
                        'is_verified'    => $r['is_verified'],
                        'is_approved'    => true,
                        'created_at'     => now()->subDays(rand(1, 90)),
                    ]);
                    $totalCreated++;
                }
            }
        }

        $this->command->info("Berhasil seed {$totalCreated} reviews untuk {$products->count()} produk.");
    }
}
