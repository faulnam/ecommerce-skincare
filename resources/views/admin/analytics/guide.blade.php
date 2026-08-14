@extends('layouts.admin')

@section('page-title', 'Petunjuk Teknis Analytics')

@push('styles')
<style>
    .nav-pills .nav-link {
        color: #4b5563;
        font-weight: 500;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }
    .nav-pills .nav-link:hover {
        background-color: #f3f4f6;
    }
    .nav-pills .nav-link.active {
        background-color: #e0e7ff;
        color: #4f46e5;
        font-weight: 600;
    }
    .guide-content h5 {
        color: #1f2937;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }
    .metric-box {
        border-left: 4px solid #d1d5db;
        padding-left: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .metric-title {
        font-weight: 600;
        color: #374151;
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }
    .metric-desc {
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.5;
    }
</style>
@endpush

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / 
    <a href="{{ route('admin.analytics.index') }}">Analytics</a> / 
    Juknis
@endsection

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-book-open text-primary me-2"></i> Panduan Dashboard Analytics</h4>
        <p class="text-muted mb-0 small">Pelajari cara membaca dan memanfaatkan data untuk bisnis Anda.</p>
    </div>
    <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Analytics
    </a>
</div>

<!-- Main Content Layout -->
<div class="row g-4">
    <!-- Sidebar Tabs -->
    <div class="col-lg-3 col-md-4">
        <div class="bg-white p-3 rounded shadow-sm border-0 sticky-top" style="top: 80px; z-index: 1;">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <button class="nav-link active text-start mb-2" id="v-pills-filter-tab" data-bs-toggle="pill" data-bs-target="#v-pills-filter" type="button" role="tab"><i class="fas fa-filter me-2 w-20px text-center"></i> Filter Periode</button>
                <button class="nav-link text-start mb-2" id="v-pills-traffic-tab" data-bs-toggle="pill" data-bs-target="#v-pills-traffic" type="button" role="tab"><i class="fas fa-users me-2 w-20px text-center"></i> Metrik Pengunjung</button>
                <button class="nav-link text-start mb-2" id="v-pills-charts-tab" data-bs-toggle="pill" data-bs-target="#v-pills-charts" type="button" role="tab"><i class="fas fa-chart-line me-2 w-20px text-center"></i> Grafik Analisis</button>
                <button class="nav-link text-start mb-2" id="v-pills-tables-tab" data-bs-toggle="pill" data-bs-target="#v-pills-tables" type="button" role="tab"><i class="fas fa-table me-2 w-20px text-center"></i> Tabel Detail Data</button>
                <button class="nav-link text-start" id="v-pills-export-tab" data-bs-toggle="pill" data-bs-target="#v-pills-export" type="button" role="tab"><i class="fas fa-file-excel me-2 w-20px text-center"></i> Panduan Ekspor Excel</button>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="col-lg-9 col-md-8">
        <div class="tab-content bg-white p-4 p-lg-5 rounded shadow-sm border-0 guide-content h-100" id="v-pills-tabContent">
            
            <!-- Tab: Filter -->
            <div class="tab-pane fade show active" id="v-pills-filter" role="tabpanel">
                <h5 class="border-bottom pb-2"><i class="fas fa-filter text-primary me-2"></i> Filter Periode (Waktu)</h5>
                <p class="text-muted mb-4">Fitur ini berada di pojok kanan atas Dashboard. Mengubah *dropdown* ini akan secara otomatis menghitung ulang semua data di tabel dan grafik agar sesuai dengan rentang waktu yang Anda pilih.</p>
                
                <div class="metric-box border-primary">
                    <div class="metric-title">Hari Ini (Per Jam)</div>
                    <div class="metric-desc">Menampilkan data aktivitas pengunjung dari jam 00:00 hari ini hingga detik ini. Sangat berguna untuk melihat grafik jam sibuk harian dan merencanakan waktu terbaik memposting konten atau promosi.</div>
                </div>
                <div class="metric-box border-success">
                    <div class="metric-title">7 Hari Terakhir</div>
                    <div class="metric-desc">Pilihan *default*. Menampilkan ringkasan data mingguan. Ideal untuk evaluasi operasional rutin, seperti melihat hari apa dalam seminggu yang trafiknya paling tinggi.</div>
                </div>
                <div class="metric-box border-info">
                    <div class="metric-title">30 Hari Terakhir</div>
                    <div class="metric-desc">Menampilkan ringkasan sebulan penuh. Paling sering digunakan untuk laporan bulanan (*monthly report*) dan menganalisis tren pertumbuhan jangka menengah.</div>
                </div>
                <div class="metric-box border-warning">
                    <div class="metric-title">1 Tahun Terakhir</div>
                    <div class="metric-desc">Menampilkan data rekapitulasi tahunan. Sangat tepat digunakan untuk evaluasi akhir tahun, mencari pola musim (misalnya bulan apa penjualan tertinggi), dan membuat proyeksi tahun depan.</div>
                </div>
                <div class="metric-box border-danger">
                    <div class="metric-title">Pilih Manual (Kalender)</div>
                    <div class="metric-desc">Memungkinkan Anda memilih rentang tanggal <strong>Start Date (Mulai)</strong> dan <strong>End Date (Sampai)</strong> secara kustom. Berguna jika Anda ingin melihat hasil metrik dari periode spesifik, contohnya: data trafik tepat selama masa kampanye *Black Friday* bulan lalu.</div>
                </div>
            </div>

            <!-- Tab: Traffic -->
            <div class="tab-pane fade" id="v-pills-traffic" role="tabpanel">
                <h5 class="border-bottom pb-2"><i class="fas fa-users text-primary me-2"></i> Metrik Lalu Lintas Pengunjung</h5>
                <p class="text-muted mb-4">Kartu-kartu angka di bagian atas memberikan ringkasan cepat tentang kesehatan dan volume *traffic* website Anda.</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="metric-box" style="border-color: #ef4444;">
                            <div class="metric-title">Active Users (Now)</div>
                            <div class="metric-desc">Pengunjung yang sedang <strong>membuka halaman web saat ini juga</strong>. Data real-time ini penting dipantau saat Anda sedang menjalankan *flash sale* atau membagikan link promosi mendadak.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-box" style="border-color: #3b82f6;">
                            <div class="metric-title">Total Users</div>
                            <div class="metric-desc">Jumlah orang unik yang mengunjungi website. Menggambarkan seberapa luas audiens bisnis Anda dalam periode terpilih.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-box" style="border-color: #10b981;">
                            <div class="metric-title">New Users</div>
                            <div class="metric-desc">Orang yang baru <strong>pertama kali</strong> datang ke website Anda. Menandakan keberhasilan kampanye *marketing* Anda dalam menjaring target pasar baru.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-box" style="border-color: #8b5cf6;">
                            <div class="metric-title">Pageviews</div>
                            <div class="metric-desc">Total tayangan halaman. Jika 1 pengunjung membuka 5 halaman produk, itu dihitung sebagai 1 User dan 5 Pageviews. Mengukur kedalaman interaksi audiens.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-box" style="border-color: #f59e0b;">
                            <div class="metric-title">Bounce Rate</div>
                            <div class="metric-desc">Persentase pengunjung yang masuk, melihat satu halaman, lalu <strong>pergi tanpa mengklik apa pun</strong>. Angka tinggi (>70%) menandakan landing page kurang relevan atau lambat dimuat.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-box" style="border-color: #64748b;">
                            <div class="metric-title">Avg Session</div>
                            <div class="metric-desc">Rata-rata durasi kunjungan. Semakin lama angkanya, semakin tinggi tingkat ketertarikan pengunjung terhadap konten dan produk yang Anda tawarkan.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Charts -->
            <div class="tab-pane fade" id="v-pills-charts" role="tabpanel">
                <h5 class="border-bottom pb-2"><i class="fas fa-chart-line text-primary me-2"></i> Grafik Analisis</h5>
                <p class="text-muted mb-4">Grafik memvisualisasikan data agar tren lebih mudah dibaca dibandingkan hanya melihat angka.</p>

                <div class="metric-box border-primary">
                    <div class="metric-title">Traffic Trend (Grafik Garis)</div>
                    <div class="metric-desc">
                        Memperlihatkan pergerakan jumlah <strong>Pengunjung</strong> dan <strong>Pageviews</strong> dari hari ke hari.<br><br>
                        <strong>Cara Analisis:</strong> Cari pola lonjakan. Jika grafik tiba-tiba naik tajam di hari Rabu, ingat kembali aktivitas promosi apa yang Anda lakukan di hari Selasa atau Rabu tersebut, dan ulangi strategi yang sama.
                    </div>
                </div>

                <div class="metric-box border-success mt-4">
                    <div class="metric-title">Traffic Acquisition (Grafik Donat)</div>
                    <div class="metric-desc">
                        Menjawab pertanyaan <em>"Dari mana asal pengunjung saya?"</em><br><br>
                        <ul class="mb-0 ps-3 mt-2">
                            <li><strong>Organic Search:</strong> Masuk gratis dari pencarian Google. Menandakan SEO website Anda baik.</li>
                            <li><strong>Direct:</strong> Mengetik langsung nama website (misal: hijab.com). Menandakan *brand awareness* kuat.</li>
                            <li><strong>Social:</strong> Masuk dari link di media sosial. Mengukur seberapa efektif konten Instagram/TikTok Anda.</li>
                            <li><strong>Referral:</strong> Masuk dari rekomendasi website lain (backlink/partnership).</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tab: Tables -->
            <div class="tab-pane fade" id="v-pills-tables" role="tabpanel">
                <h5 class="border-bottom pb-2"><i class="fas fa-table text-primary me-2"></i> Tabel Detail Data</h5>
                <p class="text-muted mb-4">Tabel memberikan rincian performa per baris data, sangat penting untuk analisis tingkat produk atau halaman.</p>

                <div class="metric-box border-success">
                    <div class="metric-title">Top Products (Produk Terlaris)</div>
                    <div class="metric-desc">
                        Menampilkan produk yang diurutkan berdasarkan total pendapatan.<br>
                        <strong>Tindakan Bisnis:</strong> Pastikan stok produk yang berada di posisi atas selalu tersedia, dan jadikan produk tersebut sebagai "wajah" utama di halaman depan (*Home*) atau kampanye iklan.
                    </div>
                </div>

                <div class="metric-box border-info mt-4">
                    <div class="metric-title">Top Pages (Halaman Populer)</div>
                    <div class="metric-desc">
                        Halaman (URL) yang paling sering dibuka oleh audiens.<br>
                        <strong>Tindakan Bisnis:</strong> Jika halaman blog/artikel mendapat trafik tinggi, sisipkan tombol atau link promosi produk di dalam artikel tersebut untuk memaksimalkan peluang konversi.
                    </div>
                </div>

                <div class="metric-box border-warning mt-4">
                    <div class="metric-title">Demographics (Kota) & Device Category</div>
                    <div class="metric-desc">
                        Merekam dari kota mana audiens berasal dan perangkat apa yang digunakan (Mobile/Desktop).<br>
                        <strong>Tindakan Bisnis:</strong> Jika 85% pengunjung menggunakan <em>Mobile</em> (HP), Anda sebagai pengelola web harus lebih sering menguji tampilan fitur baru lewat layar HP, bukan hanya dari laptop. Jika mayoritas pembeli dari Jakarta, Anda bisa membuat promo pengiriman *Same-Day* khusus Jakarta.
                    </div>
                </div>
            </div>

            <!-- Tab: Export -->
            <div class="tab-pane fade" id="v-pills-export" role="tabpanel">
                <h5 class="border-bottom pb-2"><i class="fas fa-file-excel text-success me-2"></i> Panduan Ekspor Laporan Excel</h5>
                <p class="text-muted mb-4">Anda dapat mengunduh seluruh data tabel yang ada di Analytics (beserta data hasil filter) ke dalam format *Spreadsheet* (.csv) yang bisa dibuka menggunakan Microsoft Excel atau Google Sheets.</p>

                <div class="p-4 bg-light rounded border">
                    <h6 class="fw-bold mb-3">Langkah-langkah:</h6>
                    <ol class="mb-0 lh-lg">
                        <li>Buka halaman utama <strong>Analytics</strong>.</li>
                        <li>Pilih <strong>Periode Filter</strong> yang ingin diunduh datanya (misalnya: 30 Hari Terakhir, atau pilih secara manual via Kalender).</li>
                        <li>Klik tombol hijau <strong><i class="fas fa-file-excel mx-1"></i> Ekspor Excel</strong> di sudut kanan atas layar.</li>
                        <li>Sistem akan memproses data sejenak, dan sebuah file berformat `.csv` (contoh: <code>analytics_report_monthly_20231012.csv</code>) akan otomatis terunduh ke komputer Anda.</li>
                        <li>Buka file tersebut menggunakan Microsoft Excel untuk melihat rincian metrik, produk terlaris, dan halaman terpopuler Anda secara rapi.</li>
                    </ol>
                </div>
                
                <div class="alert alert-success mt-4">
                    <i class="fas fa-check-circle me-2"></i> <strong>Sistem Terintegrasi:</strong> Tombol ekspor Excel otomatis membaca rentang tanggal yang sedang Anda filter. Jadi jika Anda memfilter data bulan Agustus, file Excel yang terunduh adalah khusus untuk performa bulan Agustus.
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
