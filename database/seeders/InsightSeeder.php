<?php

namespace Database\Seeders;

use App\Models\Insight;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InsightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Insight::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $insightsData = [
            [
                'title' => 'Tren Skincare 2026: Fokus pada Penguatan Skin Barrier & Clean Beauty',
                'category' => 'Tren Kecantikan',
                'excerpt' => 'Tahun 2026 menandai era baru perawatan kulit yang berfokus pada kesehatan skin barrier jangka panjang, bahan aktif biocompatible, dan konsep less is more.',
                'image' => '/media/products/model-1.jpg',
                'content' => "<p>Industri perawatan kulit mengalami transformasi besar di tahun 2026. Pendekatan agresif yang mengandalkan eksfoliasi berlebih dan layering belasan tahapan produk mulai ditinggalkan. Konsumen modern kini beralih ke filosofi <em>skin minimalism</em> dan perlindungan pelindung alami kulit (skin barrier).</p>
                <h2>Mengapa Skin Barrier Menjadi Fokus Utama?</h2>
                <p>Skin barrier adalah lapisan lipid terluar kulit yang berfungsi mencegah hilangnya kadar air (transepidermal water loss) sekaligus membentengi kulit dari polutan lingkungan, bakteri, dan sinar ultraviolet. Ketika barrier ini rusak akibat over-cleansing atau bahan iritatif, kulit menjadi mudah kemerahan, beruntusan, dan dehidrasi parah.</p>
                <h2>Kombinasi Bahan Aktif Unggulan</h2>
                <ul>
                    <li><strong>Ceramide Complex:</strong> Menggantikan asam lemak esensial yang hilang dan memperkuat dinding sel kulit.</li>
                    <li><strong>Centella Asiatica (Cica):</strong> Memberikan efek menenangkan instan pada kulit reaktif dan mempercepat pemulihan mikrolesi.</li>
                    <li><strong>Ectoin & Peptides:</strong> Bahan bio-fermentasi mutakhir yang melindungi sel kulit dari stresor lingkungan dan penuaan dini.</li>
                </ul>
                <p>Kunci dari kulit glowing yang sehat di tahun 2026 bukanlah kilap buatan, melainkan kulit terhidrasi optimal dengan pertahanan alami yang kokoh.</p>",
            ],
            [
                'title' => 'Urutan Skincare Pagi dan Malam yang Benar untuk Pemula',
                'category' => 'Panduan Rutinitas',
                'excerpt' => 'Merasa bingung kapan harus memakai serum, pelembap, atau toner? Simak urutan langkah demi langkah pemakaian skincare pagi dan malam yang paling efektif.',
                'image' => '/media/products/model-2.jpg',
                'content' => "<p>Banyak orang membeli produk skincare berkualitas namun tidak merasakan hasil optimal. Salah satu penyebab paling umum adalah urutan pengaplikasian produk yang keliru. Aturan mendasar dalam dunia dermatologi adalah mengaplikasikan produk dari konsistensi paling cair (encer) hingga paling kental (pekat).</p>
                <h2>Rutinitas Pagi: Perlindungan & Hidrasi</h2>
                <ol>
                    <li><strong>Gentle Cleanser:</strong> Gunakan pembersih wajah berbusa lembut dengan pH seimbang untuk mengangkat sisa minyak alami semalaman.</li>
                    <li><strong>Hydrating Toner / Essence:</strong> Mengembalikan kelembapan alami dan membuka jalan bagi bahan aktif berikutnya.</li>
                    <li><strong>Antioxidant Serum (Vitamin C / Niacinamide):</strong> Menangkal radikal bebas dari polusi dan paparan sinar matahari.</li>
                    <li><strong>Moisturizer Gel:</strong> Mengunci hidrasi tanpa memberikan rasa berat atau lengket di siang hari.</li>
                    <li><strong>Sunscreen SPF 50+:</strong> Langkah paling krusial! Oleskan sebanyak 2 jari ke seluruh wajah dan leher setiap pagi.</li>
                </ol>
                <h2>Rutinitas Malam: Pemulihan & Nutrisi</h2>
                <p>Di malam hari, fokus bergeser ke perbaikan sel kulit. Setelah membersihkan wajah secara menyeluruh (double cleansing jika menggunakan makeup), gunakan toner, serum pemulih seperti Hyaluronic Acid atau Retinol, dan tutup dengan pelembap yang kaya Ceramide untuk mengunci kelembapan saat tidur.</p>",
            ],
            [
                'title' => 'Memahami Perbedaan Niacinamide, Vitamin C, dan Alpha Arbutin untuk Mencerahkan Kulit',
                'category' => 'Edukasi Bahan Aktif',
                'excerpt' => 'Bingung memilih pencerah wajah? Pelajari bagaimana ketiga bahan ini bekerja dalam memudarkan noda hitam dan meratakan warna kulit Anda.',
                'image' => '/media/products/model-3.jpg',
                'content' => "<p>Hiperpigmentasi seperti bekas jerawat kehitaman (PIH), flek matahari, atau warna kulit kusam membutuhkan penanganan dengan bahan aktif yang sesuai dengan mekanisme pigmentasi kulit.</p>
                <h2>1. Niacinamide (Vitamin B3)</h2>
                <p>Niacinamide bekerja dengan menghambat transfer melanosom (butiran pigmen melanin) dari melanosit ke sel-sel keratinosit di permukaan kulit. Selain mencerahkan, bahan serbaguna ini juga menenangkan peradangan dan mengatur produksi sebum berlebih.</p>
                <h2>2. Vitamin C (L-Ascorbic Acid & Derivatnya)</h2>
                <p>Antioksidan kuat yang menekan aktivitas enzim tirosinase penyebab pembentukan pigmen gelap. Sangat efektif digunakan di pagi hari bersamaan dengan sunscreen untuk menangkal bahaya fotooksidatif.</p>
                <h2>3. Alpha Arbutin</h2>
                <p>Berasal dari tanaman bearberry, Alpha Arbutin adalah turunan alami dari hydroquinone yang aman digunakan jangka panjang untuk memudarkan flek membandel tanpa memicu iritasi.</p>
                <p>Pilihlah bahan aktif sesuai kebutuhan spesifik dan toleransi jenis kulit Anda untuk hasil yang aman dan bertahan lama.</p>",
            ],
            [
                'title' => 'Chemical vs Physical Sunscreen: Mana yang Paling Tepat untuk Kulit Anda?',
                'category' => 'Perlindungan UV',
                'excerpt' => 'Kenali perbedaan mendasar antara tabir surya kimiawi dan fisikal, mulai dari cara kerja, tekstur, hingga kecocokannya dengan jenis kulit tertentu.',
                'image' => '/media/products/model-4.jpg',
                'content' => "<p>Sunscreen adalah investasi perawatan kulit paling fundamental. Tidak ada serum anti-aging atau pelembap mewah yang akan bekerja efektif jika kulit Anda tidak terlindungi dari degradasi sinar ultraviolet harian.</p>
                <h2>Physical (Mineral) Sunscreen</h2>
                <p>Menggunakan filter mineral seperti <em>Zinc Oxide</em> dan <em>Titanium Dioxide</em> yang bekerja di atas permukaan kulit layaknya cermin pemantul sinar UV. Kelebihannya: sangat ramah untuk kulit yang sangat sensitif, mudah alergi, atau sedang berjerawat meradang parah.</p>
                <h2>Chemical (Organic) Sunscreen</h2>
                <p>Menggunakan molekul karbon penyerap UV (seperti Tinosorb S, Uvinul A Plus) yang menyerap sinar matahari dan mengubahnya menjadi panas yang tidak berbahaya. Keunggulannya: formula seringan air, menyerap seketika, dan sama sekali tidak meninggalkan lapisan residu putih (no whitecast).</p>
                <p>Apapun pilihannya, yang paling penting adalah konsistensi penggunaan minimal SPF 30 hingga SPF 50 setiap hari!</p>",
            ],
            [
                'title' => 'Panduan Memakai Retinol Bagi Pemula Agar Bebas dari Purging dan Iritasi',
                'category' => 'Anti-Aging Care',
                'excerpt' => 'Ingin mencoba retinol tetapi takut iritasi atau breakout? Ikuti strategi sandwich method dan panduan bertahap ini untuk hasil optimal.',
                'image' => '/media/products/banner-2.jpg',
                'content' => "<p>Retinol telah terbukti secara ilmiah sebagai <em>gold standard</em> dalam mempercepat pergantian sel kulit, merangsang produksi kolagen alami, dan menyamarkan garis halus. Namun, kekuatannya yang tinggi memerlukan adaptasi kulit yang cermat.</p>
                <h2>Aturan Emas Penggunaan Retinol</h2>
                <ul>
                    <li><strong>Mulai dari Konsentrasi Rendah:</strong> Pilih formulasi encapsulated retinol 0.5% - 1% yang melepas bahan aktif secara bertahap sehingga lebih minim iritasi.</li>
                    <li><strong>Gunakan Frekuensi Bertahap (Skin Cycling):</strong> Cukup gunakan 1 kali seminggu pada minggu pertama, lalu naikkan ke 2 kali seminggu di minggu berikutnya.</li>
                    <li><strong>Terapkan Sandwich Method:</strong> Oleskan pelembap tipis, tunggu 5 menit, aplikasikan retinol, lalu lapisi kembali dengan pelembap. Teknik ini terbukti ampuh mencegah kekeringan dan rasa perih.</li>
                    <li><strong>Hanya Dipakai di Malam Hari:</strong> Retinol sensitif terhadap cahaya matahari dan meningkatkan fotosensitivitas kulit. Selalu kenakan sunscreen di keesokan paginya.</li>
                </ul>",
            ],
            [
                'title' => 'Eksfoliasi Lembut dengan BHA: Rahasia Pori-Pori Bersih Bebas Komedo',
                'category' => 'Perawatan Pori',
                'excerpt' => 'Salicylic Acid (BHA) adalah senjata utama melawan komedo hitam dan pori-pori tersumbat. Pelajari cara penggunaannya tanpa merusak kelembapan kulit.',
                'image' => '/media/products/banner-3.jpg',
                'content' => "<p>Berbeda dengan AHA yang hanya larut dalam air dan bekerja di permukaan atas kulit, <strong>Beta Hydroxy Acid (BHA)</strong> seperti asam salisilat bersifat larut dalam minyak (lipid-soluble). Sifat unik ini memungkinkannya menembus masuk ke dalam kelenjar minyak dan melarutkan sumbatan sebum serta sel kulit mati yang menjadi biang komedo.</p>
                <h2>Tips Aman Melakukan Eksfoliasi BHA</h2>
                <p>Cukup gunakan toner atau serum BHA 2% sebanyak 2 hingga 3 kali seminggu di malam hari. Hindari menggabungkan BHA bersamaan dengan Retinol atau Vitamin C dosis tinggi dalam satu waktu untuk mencegah iritasi lapisan pelindung kulit.</p>",
            ],
        ];

        foreach ($insightsData as $data) {
            Insight::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'category' => $data['category'],
                'excerpt' => $data['excerpt'],
                'image' => $data['image'],
                'content' => $data['content'],
                'status' => 'published',
                'views' => rand(150, 2400),
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
