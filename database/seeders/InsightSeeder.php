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
        Insight::truncate();

        $insightsData = [
            // === Tips Mix & Match ===
            [
                'title' => 'Tren Warna Hijab 2026: Dari Earth Tone Hingga Pastel, Mana Pilihanmu?',
                'category' => 'Tren Gaya Terkini',
                'excerpt' => 'Warna hijab sangat menentukan aura wajah. Mari kenali tren warna hijab di tahun 2026 dan bagaimana menyesuaikannya dengan undertone kulitmu.',
                'image' => '/media/products/model-1.jpg',
                'content' => "<p>Tahun 2026 membawa angin segar bagi dunia fashion muslimah, khususnya dalam pemilihan palet warna hijab. Jika beberapa tahun terakhir kita dibombardir dengan warna-warna netral yang aman seperti hitam, putih, dan abu-abu, kini saatnya beralih ke warna yang lebih berkarakter namun tetap elegan.</p>
                <h2>Kembalinya Earth Tone yang Menenangkan</h2>
                <p>Warna bernuansa bumi atau <em>earth tones</em> seperti terracotta, olive green, dan mustard diprediksi akan merajai pasar. Warna-warna ini memberikan kesan hangat dan sangat cocok untuk kulit perempuan Indonesia yang mayoritas memiliki warm undertone. Padukan hijab berwarna terracotta dengan tunik putih atau kemeja linen berwarna beige untuk tampilan kasual yang chic.</p>
                <h2>Soft Pastel yang Feminim</h2>
                <p>Selain earth tone, warna pastel yang lembut seperti sage green, dusty pink, dan baby blue juga kembali digemari. Warna ini memberikan ilusi wajah yang lebih cerah dan segar. Sangat direkomendasikan untuk digunakan pada acara-acara semi-formal atau undangan pernikahan di siang hari.</p>
                <h2>Cara Menentukan Warna Sesuai Undertone</h2>
                <ul>
                    <li><strong>Warm Undertone:</strong> Pilih warna-warna dengan dasar kuning atau oranye seperti mocca, coral, atau mustard.</li>
                    <li><strong>Cool Undertone:</strong> Cocok dengan warna berbasis biru seperti navy, icy blue, atau dusty purple.</li>
                    <li><strong>Neutral Undertone:</strong> Beruntunglah Anda! Hampir semua warna akan terlihat cantik, terutama warna-warna jewel seperti emerald green atau ruby red.</li>
                </ul>
                <p>Jangan takut untuk bereksperimen. Warna hijab yang tepat tidak hanya menyempurnakan outfit, tapi juga bisa menaikkan mood Anda seharian!</p>",
            ],
            [
                'title' => 'Mix and Match Kulot Highwaist Untuk Wanita Bertubuh Mungil',
                'category' => 'Tips Mix & Match',
                'excerpt' => 'Siapa bilang wanita mungil tidak cocok pakai kulot? Ini rahasia padu padan kulot highwaist agar kaki terlihat lebih jenjang.',
                'image' => '/media/products/model-2.jpg',
                'content' => "<p>Celana kulot seringkali dihindari oleh wanita bertubuh petite atau mungil karena dianggap bisa membuat tubuh terlihat \"tenggelam\". Padahal, jika memilih potongan dan gaya yang tepat, kulot justru bisa menjadi senjata rahasia untuk tampil lebih tinggi dan semampai.</p>
                <h2>Kunci Utama: Potongan Highwaist</h2>
                <p>Selalu pilih celana kulot dengan potongan <strong>highwaist</strong> (berpinggang tinggi). Celana model ini akan menggeser garis pinggang alami Anda menjadi lebih tinggi, sehingga ilusi kaki yang lebih panjang akan langsung tercipta. Pastikan bagian pinggang pas dan tidak kedodoran.</p>
                <h2>Tuck In (Masukkan) Baju Anda</h2>
                <p>Untuk memaksimalkan efek highwaist, selipkan (tuck in) atasan Anda ke dalam celana. Jika Anda mengenakan kemeja, blouse, atau kaos, trik ini sangat efektif. Jika Anda kurang percaya diri dengan bagian pinggul, Anda bisa menggunakan trik <em>french tuck</em> (hanya memasukkan bagian depan atasan saja).</p>
                <h2>Pilih Atasan yang Pas di Badan</h2>
                <p>Aturan dasar proporsi fashion adalah: jika bawahan sudah longgar (kulot), maka seimbangkan dengan atasan yang lebih pas di badan (fit body) atau tidak terlalu kebesaran. Hindari memadukan kulot lebar dengan tunik oversized karena akan membuat siluet tubuh terlihat penuh dan lebih pendek.</p>
                <h2>Sepatu Memainkan Peran Penting</h2>
                <p>Untuk wanita mungil, padukan kulot dengan sepatu berhak. Tidak harus heels tinggi; mules dengan hak 3-5 cm atau wedges sneakers sudah cukup untuk menambah tinggi badan. Jika memilih flat shoes, pastikan celana kulot Anda tidak menyapu lantai, idealnya panjangnya jatuh tepat di atas mata kaki (ankle length).</p>
                <p>Jangan biarkan postur tubuh membatasi gaya Anda. Dengan sedikit trik proporsi, Anda bisa tampil stylish dengan kulot kapan saja!</p>",
            ],
            [
                'title' => 'Inspirasi Outfit Hangout Weekend: Kasual namun Tetap Elegan',
                'category' => 'Tips Mix & Match',
                'excerpt' => 'Bosan dengan gaya hangout yang itu-itu saja? Simak ide padu padan outfit yang nyaman dipakai seharian tanpa mengorbankan sisi elegan Anda.',
                'image' => '/media/products/model-3.jpg',
                'content' => "<p>Weekend adalah waktu yang tepat untuk bersantai dan berekspresi melalui pakaian. Namun, seringkali kita terjebak pada kombo andalan \"kaos dan celana jeans\" yang membosankan. Tampil kasual bukan berarti Anda tidak bisa terlihat elegan.</p>
                <h2>1. Sentuhan Ruffle yang Manis</h2>
                <p>Coba gantikan kaos polos Anda dengan blouse beraksen ruffle di bagian dada atau lengan. Ruffle memberikan dimensi dan tekstur pada penampilan tanpa membuatnya terlihat berlebihan. Padukan dengan celana kulot atau celana bahan berpotongan lurus (straight cut) berwarna netral. Ini akan memberikan kesan rapi namun tetap santai.</p>
                <h2>2. Bermain dengan Layering Ringan</h2>
                <p>Di cuaca tropis, layering tebal tentu bukan pilihan. Namun, Anda bisa memadukan inner tanpa lengan dengan outer kemeja katun linen yang ringan dan dibiarkan tak terkancing. Gaya ini memberikan kesan effortless dan dinamis. Padukan dengan rok plisket untuk sentuhan feminin.</p>
                <h2>3. Monochrome is Never Wrong</h2>
                <p>Gaya monokrom (satu tone warna) dari atas hingga bawah adalah cara paling mudah untuk terlihat mahal dan elegan seketika. Misalnya, gunakan atasan dan bawahan berwarna coklat susu (mocca), lalu beri statement pada hijab yang bermotif atau warna tas yang kontras.</p>
                <h2>4. Perhatikan Aksesoris</h2>
                <p>Outfit yang simpel bisa naik kelas hanya dengan aksesoris yang tepat. Tambahkan kalung minimalis, jam tangan elegan, dan kacamata hitam andalan Anda. Pilih tas model sling bag yang praktis namun berdesain kokoh (structured bag) untuk menjaga kesan rapi.</p>
                <p>Kunci dari tampilan elegan adalah pakaian yang disetrika rapi, tidak kusut, dan pemilihan bahan yang tidak terlihat murahan. Selamat bereksperimen untuk weekend Anda selanjutnya!</p>",
            ],
            [
                'title' => '5 Cara Stylish Memakai Pashmina Silk Tanpa Jarum Pentul',
                'category' => 'Tips Mix & Match',
                'excerpt' => 'Jarum pentul sering merusak bahan silk yang halus. Ini dia 5 tutorial gaya pashmina silk yang chic dan aman tanpa jarum.',
                'image' => '/media/products/model-4.jpg',
                'content' => "<p>Pashmina berbahan silk memberikan kilau mewah yang seketika membuat penampilan menjadi elegan. Namun, bahannya yang licin dan halus membuatnya rentan rusak atau berlubang jika sering ditusuk jarum pentul. Tenang saja, Anda bisa tampil stylish tanpa menggunakan sebatang jarum pun!</p>
                <h2>Kunci Utama: Inner Ciput yang Pas</h2>
                <p>Sebelum memulai, pastikan Anda menggunakan ciput ninja atau ciput rajut yang menyerap keringat dan tidak licin. Ciput ini berfungsi sebagai fondasi agar pashmina tidak mudah bergeser.</p>
                <h2>1. Gaya Lempar Bahu Klasik</h2>
                <p>Pasang pashmina dengan panjang sebelah (misal kanan lebih panjang). Jepit bagian bawah dagu dengan peniti bohlam (yang tidak merusak kain) atau bros magnet. Ambil sisi yang panjang, tarik memutar ke belakang leher dan jatuhkan kembali ke depan, atau biarkan terjuntai di salah satu bahu.</p>
                <h2>2. Gaya Tie-Back (Ikat Belakang)</h2>
                <p>Posisikan pashmina sama panjang di kedua sisi. Tarik kedua ujungnya ke arah belakang leher, di bawah ciput. Ikat kedua ujung tersebut membentuk simpul mati yang rapi. Gaya ini sangat clean dan cocok untuk menonjolkan detail krah baju atau kalung.</p>
                <h2>3. The Effortless Wrap</h2>
                <p>Letakkan pashmina di kepala dengan satu sisi sangat pendek dan satu sisi sangat panjang. Jepit di bawah dagu dengan bros magnet. Ambil sisi yang panjang, lilitkan dengan longgar di sekitar leher hingga ujungnya kembali ke depan. Gaya ini sangat cantik untuk look kasual dan flowy.</p>
                <h2>Gunakan Bros Magnet</h2>
                <p>Investasikan uang Anda pada bros magnet berkualitas tinggi. Bros ini memiliki daya rekat yang kuat tanpa melubangi serat kain sedikit pun. Ini adalah solusi terbaik bagi para pencinta pashmina silk dan satin.</p>
                <p>Dengan sedikit latihan, Anda akan terbiasa mengatur pashmina silk tanpa jarum pentul, menjaga kain tetap awet dan penampilan tetap memukau.</p>",
            ],

            // === Edukasi Fashion ===
            [
                'title' => 'Mengenal Karakteristik Bahan Maxmara Lux Pada Dress',
                'category' => 'Edukasi Fashion',
                'excerpt' => 'Sering dengar bahan Maxmara Lux tapi belum tahu bedanya dengan satin biasa? Mari bahas tuntas keistimewaan bahan ini.',
                'image' => '/media/products/product-1-baju.jpg',
                'content' => "<p>Dalam dunia busana muslim, terutama untuk pembuatan gamis atau dress pesta, nama kain \"Maxmara Lux\" mungkin sudah tidak asing lagi. Namun, tahukah Anda apa yang membedakannya dari jenis kain satin atau katun lainnya?</p>
                <h2>Tekstur dan Kilau yang Elegan</h2>
                <p>Bahan Maxmara Lux sering disebut sebagai satinnya orang kelas atas. Berbeda dengan satin silk biasa yang kilaunya seringkali terlalu mencolok (bahkan terkesan murahan), Maxmara Lux memiliki kilau <em>doff</em> (matte shine) yang sangat halus dan elegan. Saat terkena cahaya, pantulannya lembut dan tidak menyilaukan.</p>
                <h2>Karakteristik 'Jatuh' (Flowy)</h2>
                <p>Salah satu alasan mengapa bahan ini sangat dicintai untuk midi dress atau gamis adalah karena karakteristik jatuhnya yang sempurna. Kain ini memiliki bobot yang pas—tidak terlalu ringan hingga mudah tertiup angin, namun tidak terlalu berat hingga membuat gerah. Hal ini membuat siluet tubuh terlihat lebih anggun dan ramping.</p>
                <h2>Kenyamanan dan Sirkulasi Udara</h2>
                <p>Maxmara Lux terbuat dari serat sintetis berkualitas tinggi yang dirancang untuk memiliki sirkulasi udara yang baik. Tidak seperti bahan satin murahan yang membuat tubuh cepat berkeringat, Maxmara Lux terasa adem saat bersentuhan dengan kulit, sehingga nyaman dipakai seharian di acara indoor maupun outdoor.</p>
                <h2>Anti Kusut (Wrinkle-Free)</h2>
                <p>Ini adalah nilai plus tertinggi bagi wanita aktif. Maxmara Lux sangat tidak mudah kusut. Meskipun Anda duduk berjam-jam di acara pernikahan atau di dalam mobil, saat Anda berdiri, gaun Anda akan kembali lurus dan rapi tanpa bekas lipatan yang mengganggu.</p>
                <p>Jadi, ketika Anda mencari dress untuk acara spesial, pastikan untuk mempertimbangkan bahan Maxmara Lux. Investasi pada bahan yang berkualitas akan membuat Anda tampil maksimal dan percaya diri.</p>",
            ],
            [
                'title' => 'Panduan Memilih Bahan Hijab Sesuai Bentuk Wajah',
                'category' => 'Edukasi Fashion',
                'excerpt' => 'Tidak semua bahan hijab cocok untuk setiap bentuk wajah. Kenali bahan yang tepat agar wajah Anda terbingkai dengan proporsional.',
                'image' => '/media/products/product-1-hijab.jpg',
                'content' => "<p>Sering merasa pipi terlihat lebih tembem saat memakai hijab tertentu? Atau wajah terasa terlalu panjang? Masalahnya mungkin bukan pada bentuk wajah Anda, melainkan pada pemilihan tekstur dan bahan hijab yang kurang tepat.</p>
                <h2>Untuk Wajah Bulat (Chubby)</h2>
                <p>Tujuan utama untuk wajah bulat adalah memberikan ilusi wajah yang lebih panjang dan menyamarkan pipi. <strong>Pilih bahan yang kokoh dan mudah tegak di dahi</strong> seperti Katun Voal, Ceruty Babydoll, atau bahan kaku lainnya. Buat lipatan hijab menutupi sebagian tulang pipi dan pastikan bagian atas (awning) melengkung tajam, bukan menempel di dahi. Hindari bahan spandex atau jersey tipis yang menjiplak bentuk wajah.</p>
                <h2>Untuk Wajah Panjang (Lonjong)</h2>
                <p>Anda perlu memberikan ilusi volume pada sisi wajah agar terlihat lebih proporsional. <strong>Pilih bahan yang bervolume dan jatuh (flowy)</strong> seperti Pashmina Silk, Satin, atau sifon tekstur. Gaya hijab pashmina yang dililit di leher (turkish style) atau ciput ninja yang terlihat menutupi sebagian dahi sangat cocok untuk menyeimbangkan panjang wajah.</p>
                <h2>Untuk Wajah Persegi (Rahang Tegas)</h2>
                <p>Bagi pemilik rahang tegas, Anda harus menyamarkan sudut-sudut wajah. <strong>Pilih bahan yang ringan dan lembut (soft drape)</strong> seperti sifon, ceruty, atau voal ultrafine. Gunakan gaya hijab yang melengkung membulat di bagian rahang dan hindari ciput yang ditarik terlalu kencang ke belakang. Biarkan kain jatuh secara alami membingkai sisi wajah Anda.</p>
                <h2>Untuk Wajah Oval</h2>
                <p>Selamat! Wajah oval adalah bentuk yang paling proporsional, sehingga cocok menggunakan hampir semua jenis bahan dan gaya hijab, mulai dari pashmina silk, voal, hingga instan bergo. Anda bebas bereksperimen dengan berbagai look baru.</p>
                <p>Kenali bentuk wajah Anda dan jadikan hijab sebagai alat untuk memancarkan aura kecantikan terbaik Anda!</p>",
            ],
            [
                'title' => 'Kesalahan Umum Menggunakan Hijab Instan yang Bikin Wajah Terlihat Tembem',
                'category' => 'Edukasi Fashion',
                'excerpt' => 'Hijab instan memang praktis, tapi salah pilih model pet justru membuat wajah terlihat penuh. Hindari kesalahan-kesalahan ini.',
                'image' => '/media/products/product-2-hijab.jpg',
                'content' => "<p>Hijab instan bergo adalah penyelamat saat Anda sedang buru-buru atau hanya sekadar bersantai di rumah. Kepraktisannya tak tertandingi. Namun, banyak wanita yang mengeluh wajahnya terlihat lebih bulat atau \"tembem\" saat mengenakannya. Mengapa hal itu terjadi?</p>
                <h2>1. Memilih Pet (Busa) yang Terlalu Lebar dan Kaku</h2>
                <p>Desain pet atau bantalan busa pada bagian dahi sangat menentukan bentuk wajah. Jika pet terlalu lebar (melewati ujung alis) dan sangat kaku, wajah Anda akan terlihat tenggelam dan bulat. Solusinya: Pilih hijab instan dengan <strong>Pet Antem (Anti Tembem)</strong>. Pet antem dirancang memanjang ke bawah hingga tulang pipi, memberikan ilusi pipi yang lebih tirus dan rahang yang lebih V-shape.</p>
                <h2>2. Memakai Ciput yang Menutupi Dahi Terlalu Jauh</h2>
                <p>Jika Anda memakai ciput (inner) sebelum bergo, pastikan posisinya tepat di garis rambut. Menarik ciput terlalu jauh ke bawah hingga menutupi setengah dahi akan membuat wajah terlihat pendek dan memampat, sehingga pipi terlihat lebih mendominasi.</p>
                <h2>3. Pemilihan Bahan yang Terlalu Mengkilap</h2>
                <p>Bahan spandex licin yang terlalu mengkilap memantulkan cahaya di sekitar wajah, membuat ilusi volume yang berlebih. Sebaiknya pilih bahan jersey premium, diamond crepe, atau kaos rayon yang memiliki hasil akhir matte (tidak mengkilap) dan jatuh sempurna mengikuti bahu.</p>
                <h2>4. Model yang Terlalu Ketat di Leher</h2>
                <p>Bergo yang jahitannya terlalu sempit di bagian bawah dagu/leher akan mencekik leher dan membuat pipi serta dagu ganda (double chin) terlihat lebih jelas. Pastikan ukuran lingkar muka pas, tidak kelonggaran dan tidak terlalu ketat.</p>
                <p>Dengan mengenali fitur wajah dan memilih inovasi pet antem yang tepat, hijab instan tak lagi menjadi musuh bagi Anda yang ingin tampil tirus dan menawan.</p>",
            ],
            [
                'title' => 'Tampil Ramping dengan Pilihan Baju Bermotif Garis Vertikal',
                'category' => 'Edukasi Fashion',
                'excerpt' => 'Garis vertikal adalah sahabat terbaik untuk menciptakan ilusi tubuh ramping. Pahami cara kerja dan aturan padu padannya di sini.',
                'image' => '/media/products/product-2-baju.jpg',
                'content' => "<p>Ilusi optik adalah salah satu rahasia terbesar dalam dunia fashion. Anda tidak perlu diet ketat hanya untuk terlihat lebih ramping dalam sebuah acara; Anda hanya perlu memahami cara kerja motif pakaian, khususnya motif garis (stripes).</p>
                <h2>Mengapa Harus Garis Vertikal?</h2>
                <p>Motif garis vertikal (garis dari atas ke bawah) secara otomatis memaksa mata pengamat untuk melihat tubuh Anda dari atas ke bawah. Gerakan mata ini menciptakan ilusi visual bahwa tubuh Anda lebih panjang dan lebih langsing dari yang sebenarnya. Sebaliknya, garis horizontal (kiri ke kanan) memaksa mata bergerak menyamping, membuat tubuh terlihat lebih lebar.</p>
                <h2>Aturan Lebar Garis (Ketebalan Motif)</h2>
                <p>Tidak semua garis vertikal diciptakan sama. Jika Anda memiliki tubuh berisi, hindari garis vertikal yang sangat tebal dan berjarak lebar (bold block stripes). Sebaiknya, pilih <strong>pinstripes</strong> (garis tipis seperti pensil) atau garis vertikal medium dengan jarak yang rapat. Semakin tipis dan rapat garisnya, efek merampingkannya akan semakin kuat.</p>
                <h2>Fokuskan Pada Satu Bagian Tubuh</h2>
                <p>Jangan memakai motif garis dari ujung kepala hingga ujung kaki (kecuali dalam bentuk jumpsuit yang dirancang khusus). Jika Anda ingin menyamarkan paha dan pinggul, gunakan celana kulot bergaris vertikal, padukan dengan atasan polos. Jika Anda ingin menyamarkan perut atau dada, gunakan kemeja bergaris vertikal, padukan dengan celana bahan polos.</p>
                <h2>Perhatikan Kontras Warna</h2>
                <p>Motif garis dengan kontras warna yang tidak terlalu mencolok (low contrast), seperti garis abu-abu muda di atas dasar putih, atau biru navy tua di atas hitam, memberikan efek ramping yang jauh lebih elegan dan natural dibandingkan kombinasi mencolok seperti hitam pekat dan putih terang.</p>
                <p>Manfaatkan kekuatan visual ini dan lihat bagaimana pakaian bisa mengubah postur dan kepercayaan diri Anda secara instan!</p>",
            ],

            // === Perawatan Pakaian / Fashion Care ===
            [
                'title' => 'Cara Merawat Rok Plisket Agar Lipatannya Tetap Awet dan Tidak Pudar',
                'category' => 'Perawatan Pakaian',
                'excerpt' => 'Mencuci rok plisket butuh perlakuan khusus. Jangan sampai lipatan cantiknya hilang hanya karena salah teknik menjemur.',
                'image' => '/media/products/product-1-celana.jpg',
                'content' => "<p>Rok plisket (pleated skirt) telah menjadi must-have item bagi para wanita karena sifatnya yang serbaguna dan tidak mudah kusut. Namun, di balik kepraktisannya saat dipakai, perawatannya membutuhkan trik khusus agar tekstur lipatan permanennya tidak memudar seiring berjalannya waktu.</p>
                <h2>1. Jangan Gunakan Mesin Cuci!</h2>
                <p>Musuh utama lipatan plisket adalah gesekan kuat dan putaran mesin cuci. Selalu cuci rok plisket secara manual menggunakan tangan. Cukup rendam dalam air yang telah dicampur deterjen cair ringan (jangan deterjen bubuk yang kasar) selama 10-15 menit. Kucek lembut di bagian yang kotor saja. Jangan pernah memeras rok dengan cara dipuntir keras; cukup remas perlahan untuk membuang air.</p>
                <h2>2. Teknik Menjemur yang Benar</h2>
                <p>Ini adalah tahap paling krusial. Setelah dicuci, jangan menjemur rok plisket dengan cara disampirkan atau dilipat di tali jemuran, karena akan meninggalkan bekas lipatan horizontal yang merusak bentuk. <strong>Gunakan hanger (gantungan baju) jepit</strong> dan jepit bagian ban pinggangnya, biarkan rok menjuntai lurus ke bawah. Gaya gravitasi akan membantu menarik lipatan plisket agar tetap tajam dan konsisten.</p>
                <h2>3. Katakan Tidak Pada Setrika Bersuhu Tinggi</h2>
                <p>Secara umum, rok plisket berbahan hyget atau sifon tidak perlu disetrika sama sekali. Namun jika dirasa perlu, JANGAN pernah menggosokkan setrika panas secara mendatar melintasi lipatan! Jika ada, gunakan <em>garment steamer</em> (setrika uap berdiri) dari jarak 10 cm. Jika harus memakai setrika biasa, gunakan suhu paling rendah, alasi dengan kain tipis, dan setrika mengikuti arah vertikal lipatan satu per satu.</p>
                <h2>4. Penyimpanan di Lemari</h2>
                <p>Cara menyimpannya pun tak boleh sembarangan. Jangan pernah melipat dan menumpuk rok plisket di dalam lemari. Selalu gantung rok plisket Anda. Menggulungnya (seperti menggulung baju untuk traveling) adalah opsi kedua terbaik jika Anda tidak memiliki ruang gantung, tapi menggantungnya tetap yang paling disarankan.</p>",
            ],
            [
                'title' => 'Menghindari Bau Tak Sedap Pada Sepatu Tertutup',
                'category' => 'Perawatan Pakaian',
                'excerpt' => 'Sepatu mules atau flat shoes kesayangan mulai berbau? Terapkan langkah pencegahan dan perawatan ini agar sepatu selalu segar.',
                'image' => '/media/products/product-1-sandal.jpg',
                'content' => "<p>Sepatu model tertutup di bagian depan, seperti mules, loafers, atau flat shoes, sangat rentan terhadap penumpukan keringat dan bakteri. Akibatnya, bau tak sedap pun muncul dan membuat kita tidak percaya diri saat harus melepas alas kaki. Jangan khawatir, masalah ini sangat mudah dicegah dan diatasi.</p>
                <h2>Penyebab Utama Bau</h2>
                <p>Bau sepatu bukan berasal dari keringat itu sendiri, melainkan dari bakteri yang memecah asam di keringat Anda. Bakteri ini sangat menyukai tempat yang gelap, lembap, dan hangat—persis seperti kondisi di dalam sepatu tertutup yang baru selesai dipakai.</p>
                <h2>1. Jangan Pakai Sepatu Berturut-turut</h2>
                <p>Aturan emas dalam perawatan sepatu adalah: beri sepatu waktu istirahat (minimal 24 jam) sebelum dipakai kembali. Jika Anda memakai sepatu yang sama setiap hari, kelembapan di dalamnya tidak pernah benar-benar mengering, menciptakan sarang kembang biak sempurna bagi bakteri.</p>
                <h2>2. Kaos Kaki Terselubung (No-Show Socks)</h2>
                <p>Memakai sepatu tertutup tanpa kaos kaki adalah kesalahan besar. Kaki kita memproduksi keringat dalam jumlah banyak setiap hari. Gunakan <em>no-show socks</em> atau kaos kaki pendek tak kasat mata berbahan katun yang menyerap keringat. Jika kotor, Anda tinggal mencuci kaos kakinya, bukan mencuci bagian dalam sepatu.</p>
                <h2>3. Menggunakan Silica Gel dan Koran Bekas</h2>
                <p>Setelah dipakai seharian, masukkan gumpalan kertas koran bekas atau beberapa sachet <em>silica gel</em> ke dalam ujung sepatu (toe box). Koran sangat efektif menyerap kelembapan dan bau ekstra di dalam sepatu semalaman.</p>
                <h2>4. Taburan Baking Soda (Soda Kue)</h2>
                <p>Jika sepatu sudah terlanjur berbau, jangan disemprot parfum ruangan! Taburkan 1-2 sendok makan baking soda murni ke dalam sepatu pada malam hari. Baking soda adalah pembunuh bakteri dan penyerap bau alami. Esok paginya, tepuk-tepuk sepatu untuk membuang bubuk tersebut sebelum digunakan.</p>
                <p>Dengan rotasi pemakaian yang benar dan perlindungan dari kelembapan, sepatu kesayangan Anda akan awet bertahun-tahun tanpa masalah bau.</p>",
            ],
            [
                'title' => 'Menjaga Bentuk Tas Kulit Tetap Sempurna dengan Cara Penyimpanan yang Benar',
                'category' => 'Perawatan Pakaian',
                'excerpt' => 'Tas mahal bisa terlihat murah jika bentuknya peyot dan mengelupas akibat penyimpanan yang salah. Pelajari cara merawat tas Anda di sini.',
                'image' => '/media/products/product-1-tas.jpg',
                'content' => "<p>Banyak wanita mengeluhkan tas kulit sintetis atau kulit asli mereka cepat mengelupas (peeling), berjamur, atau bentuknya peyot dan kehilangan struktur aslinya (structured bag). Sebagian besar kerusakan tas sebenarnya terjadi saat tas sedang disimpan, bukan saat dipakai!</p>
                <h2>1. Jangan Biarkan Tas Kosong Melompong</h2>
                <p>Saat tas struktur (seperti tote bag kaku, satchel, atau top-handle bag) disimpan dalam keadaan kosong, gravitasi akan membuatnya lemas dan muncul kerutan permanen pada kulitnya. Selalu isi (stuffing) tas Anda dengan bantalan udara plastik, gumpalan kertas tisu (acid-free), atau syal yang tidak terpakai. <strong>Jangan gunakan kertas koran</strong> karena tintanya bisa luntur ke bagian dalam tas (lining).</p>
                <h2>2. Selalu Gunakan Dust Bag</h2>
                <p>Jangan pernah membuang kantong kain pembungkus (dust bag) bawaan tas Anda. Saat tas tidak dipakai, masukkan ke dalam dust bag untuk melindunginya dari debu tebal, paparan sinar matahari dari jendela, dan gesekan dengan benda lain di lemari. Jika tidak punya, gunakan sarung bantal berbahan katun yang bersih.</p>
                <h2>3. Atur Suhu dan Kelembapan Lemari</h2>
                <p>Musuh utama kulit (terutama PU leather/sintetis) adalah kelembapan udara tropis yang memicu jamur, dan suhu panas yang membuat kulit mengering dan pecah-pecah. Jangan menyimpan tas di lemari kayu yang menempel di dinding kamar mandi. Letakkan produk penyerap kelembapan (seperti serap air) di dalam lemari dan ganti secara rutin.</p>
                <h2>4. Jangan Digantung Terlalu Lama</h2>
                <p>Menyimpan tas dengan cara digantung pada handel atau tali selempangnya (strap) dalam jangka waktu lama akan merusak bentuk handel tersebut. Beban tas (walaupun kosong) akan menarik material pengaitnya hingga melar atau bahkan putus. Simpanlah tas dengan cara diletakkan berdiri berjejer di atas rak.</p>
                <p>Merawat tas dengan benar adalah bentuk apresiasi pada nilai barang tersebut, memastikannya tetap terlihat baru kapan pun Anda membutuhkannya.</p>",
            ],
            
            // === Review & Komparasi ===
            [
                'title' => 'Sepatu Mules vs Sneakers: Mana yang Lebih Cocok untuk Outfit Kantor?',
                'category' => 'Tips Mix & Match',
                'excerpt' => 'Zaman sekarang, sneakers sudah menjadi budaya kantor. Namun, apakah mules lebih profesional? Simak komparasinya di sini.',
                'image' => '/media/products/product-2-sandal.jpg',
                'content' => "<p>Aturan berbusana di lingkungan kerja masa kini semakin santai (smart casual). High heels yang menyiksa kini mulai ditinggalkan, digantikan oleh sepatu mules dan sneakers. Namun, seringkali kita bingung memilih mana yang paling pas untuk situasi tertentu di kantor.</p>
                <h2>Sepatu Mules: Keanggunan Tanpa Rasa Sakit</h2>
                <p>Mules adalah sepatu tertutup di depan namun terbuka di bagian tumit (seperti sandal selop namun dengan desain formal). Model sepatu ini memberikan kesan profesional yang instan, terutama mules dengan pointed toe (ujung lancip).<br>
                <strong>Kapan memakainya?</strong> Sangat cocok untuk hari-hari dimana Anda ada jadwal meeting dengan klien, presentasi, atau bekerja di industri yang sedikit konvensional seperti bank dan firma hukum. Padukan mules dengan celana kulot atau ankle pants untuk mengekspos detail tumit belakang.</p>
                <h2>Sneakers Wanita: Dinamis dan Kasual</h2>
                <p>White sneakers kini telah diterima di banyak perkantoran modern, startup, maupun agensi kreatif. Memakai sneakers memberikan kesan Anda adalah pribadi yang dinamis, aktif, dan up-to-date.<br>
                <strong>Kapan memakainya?</strong> Sangat pas untuk rutinitas harian di depan komputer, hari Jumat kasual, atau saat Anda harus mobilitas tinggi keluar-masuk kantor. Kuncinya adalah: pastikan sneakers Anda SELALU dalam keadaan bersih dan berwarna netral polos (putih atau krem), hindari sneakers lari (running shoes) yang berwarna-warni mencolok.</p>
                <h2>Cara Memadukannya</h2>
                <p>Jika Anda memilih sneakers, padukan dengan atasan yang sedikit formal (seperti blazer) agar penampilan tidak terkesan seolah Anda mau pergi ke kampus. Sebaliknya, jika Anda memakai mules, Anda bisa mengenakan kemeja katun santai namun mules akan menaikkan level keseluruhan penampilan Anda.</p>
                <p>Saran terbaik? Sediakan keduanya di rak sepatu kantor Anda, dan sesuaikan dengan agenda harian Anda.</p>",
            ],
            [
                'title' => 'Tas Selempang atau Tote Bag? Temukan Jodoh Tas Harianmu',
                'category' => 'Tips Mix & Match',
                'excerpt' => 'Tas mencerminkan kepribadian dan mobilitas penggunanya. Pahami pro dan kontra antara sling bag dan tote bag untuk aktivitas padatmu.',
                'image' => '/media/products/product-2-tas.jpg',
                'content' => "<p>Bagi wanita, tas bukan hanya sekadar wadah penyimpan barang, melainkan juga bagian integral dari identitas gaya sehari-hari. Dua model tas paling populer untuk aktivitas siang hari adalah Tas Selempang (Sling bag/Crossbody) dan Tote Bag. Mana yang sebenarnya paling cocok untuk Anda?</p>
                <h2>Tote Bag: Si \"Kantong Ajaib\" yang Elegan</h2>
                <p>Tote bag berukuran besar sangat dicintai oleh wanita yang menganut prinsip \"sedia payung sebelum hujan\". Semua masuk; dari laptop, dompet besar, makeup pouch, botol minum, payung, hingga charger.<br>
                <strong>Kelebihan:</strong> Kapasitas luar biasa, memberikan kesan profesional dan sibuk (sangat cocok untuk outfit kantor atau kuliah).<br>
                <strong>Kekurangan:</strong> Beban berat hanya bertumpu pada satu bahu, yang bisa memicu pegal linu jika digunakan berjalan jauh. Selain itu, Anda harus mengubek-ubek tas untuk mencari barang kecil seperti kunci.</p>
                <h2>Sling Bag (Crossbody): Mobilitas Bebas Hambatan</h2>
                <p>Tas selempang berukuran medium memaksamu untuk menjadi minimalis. Hanya barang esensial (handphone, dompet kecil, lipstik, dan kunci) yang bisa dibawa.<br>
                <strong>Kelebihan:</strong> Kebebasan kedua tangan (hands-free)! Sangat ideal untuk hangout di mall, naik transportasi umum, atau liburan. Beban terdistribusi menyilang di tubuh sehingga lebih ergonomis.<br>
                <strong>Kekurangan:</strong> Kapasitas terbatas. Tidak bisa memuat map kerja atau tablet besar.</p>
                <h2>Solusi Hybrid</h2>
                <p>Jika Anda kesulitan memilih, tren terkini menawarkan <em>structured mini tote</em> yang dilengkapi dengan tali selempang (detachable strap). Anda bisa menjinjingnya saat butuh tampil formal, dan menyelempangkannya saat lelah. Apa pun pilihannya, sesuaikan ukuran tas dengan postur tubuh Anda agar tidak menenggelamkan penampilan secara keseluruhan.</p>",
            ],
            [
                'title' => 'Rekomendasi Sepatu Heels yang Nyaman Dipakai Seharian Penuh',
                'category' => 'Tips Mix & Match',
                'excerpt' => 'Ingin tampil tinggi dan anggun tanpa harus menderita lecet dan pegal? Ini rahasia memilih jenis hak (heels) yang tepat.',
                'image' => '/media/products/product-2-celana.jpg',
                'content' => "<p>Menggunakan sepatu hak tinggi (heels) memberikan dampak psikologis yang luar biasa; memperbaiki postur tubuh dan meningkatkan rasa percaya diri. Namun, pepatah <em>\"beauty is pain\"</em> tidak selamanya harus berlaku. Anda tetap bisa tampil anggun tanpa mengorbankan kenyamanan otot kaki Anda.</p>
                <h2>Tinggalkan Stiletto Ekstrem</h2>
                <p>Hak stiletto yang sangat runcing dan tinggi (di atas 7 cm) memberikan tekanan luar biasa pada ujung jari kaki (ball of the foot). Kecuali Anda hanya memakainya untuk duduk di acara gala dinner, hindari pemakaian stiletto untuk mobilitas harian.</p>
                <h2>Pilihlah Block Heels (Hak Tahu)</h2>
                <p>Ini adalah penyelamat wanita modern. <em>Block heels</em> memiliki penampang hak yang lebar dan kokoh. Hak ini menyebarkan berat badan Anda secara merata ke permukaan lantai, sehingga otot betis tidak perlu tegang menyeimbangkan tubuh. Block heels dengan tinggi 3-5 cm adalah sweet spot kenyamanan mutlak.</p>
                <h2>Kitten Heels untuk Tampil Chic</h2>
                <p>Jika block heels terasa terlalu berat/clunky, pilihlah kitten heels. Ini adalah sepatu berhak lancip namun sangat pendek (hanya sekitar 2-4 cm). Gaya ini sangat populer di era 90-an dan kini kembali tren karena memberikan sentuhan feminin pada celana ankle pants atau midi dress tanpa membuat kaki lecet.</p>
                <h2>Perhatikan Material dan Bentuk Depan Sepatu</h2>
                <p>Selain tinggi hak, pilihlah sepatu dengan material kulit sapi asli atau vegan leather premium yang empuk dan bisa memuai menyesuaikan bentuk kaki. Jika kaki Anda lebar, hindari ujung <em>pointed toe</em> (runcing ekstrim) yang akan menjepit jari manis dan kelingking Anda, pilih model almond toe atau square toe yang lebih lega.</p>
                <p>Investasikan dana pada sepatu berkualitas, karena kesehatan tulang belakang dan postur tubuh Anda di masa depan sangat bergantung pada alas kaki yang Anda gunakan hari ini!</p>",
            ],

        ];

        foreach ($insightsData as $data) {
            $data['slug'] = Str::slug($data['title']);
            $data['author'] = 'Nora Team';
            $data['status'] = 'published';
            $data['views'] = rand(1500, 25000);
            $data['published_at'] = now()->subDays(rand(1, 90));

            Insight::create($data);
        }
    }
}
