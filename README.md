# Setup Proyek

Dokumen ini berisi panduan untuk melakukan *setup* awal proyek di lingkungan lokal (development). Ikuti langkah-langkah di bawah ini secara berurutan.

## Prasyarat

Pastikan komputer Anda sudah menginstal aplikasi berikut:
- **PHP** (Minimal versi 8.2)
- **Composer**
- **Node.js** & **NPM**
- **MySQL / MariaDB** (melalui Laragon, XAMPP, atau sejenisnya)
- **Git**

## Langkah-langkah Setup

### 1. Clone Repository
Jika belum, clone repository proyek ke komputer Anda:
```bash
git clone <URL_REPOSITORY>
cd nama-folder-proyek
```

### 2. Install Dependensi PHP (Composer)
Jalankan perintah berikut untuk menginstal semua *library* PHP yang dibutuhkan:
```bash
composer install
```

### 3. Install Dependensi JavaScript (NPM)
Instal juga dependensi untuk *frontend* menggunakan NPM:
```bash
npm install
```

### 4. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`. Anda bisa menjalankannya via command line atau *copy-paste* secara manual:
```bash
cp .env.example .env
```
Setelah itu, sesuaikan pengaturan database di dalam file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_rekomendasi
DB_USERNAME=root
DB_PASSWORD=
```
*(Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi MySQL di komputer Anda)*

### 5. Generate Application Key
Buat *application key* unik untuk proyek ini:
```bash
php artisan key:generate
```

### 6. Migrasi dan Seeding Database
Pastikan layanan MySQL Anda sudah menyala dan database dengan nama `ecommerce_rekomendasi` (atau sesuai konfigurasi di atas) sudah dibuat.
Setelah itu, jalankan perintah ini untuk membangun tabel dan mengisi data awal (dummy/seeder):
```bash
php artisan migrate:fresh --seed
```

### 7. Buat Symbolic Link Storage (Opsional)
Jika proyek membutuhkan penyimpanan file (seperti gambar produk yang di-*upload*), jalankan perintah ini:
```bash
php artisan storage:link
```

### 8. Jalankan Server Development
Buka dua terminal terpisah dan jalankan perintah berikut.

**Terminal 1:** (Untuk menjalankan server PHP)
```bash
php artisan serve
```

**Terminal 2:** (Untuk menjalankan Vite/Frontend assets)
```bash
npm run dev
```

Sekarang Anda bisa mengakses aplikasi melalui browser di: `http://localhost:8000` atau URL lain yang diberikan oleh terminal.

---

## Akun Pengguna Asli (Default)

Berikut adalah daftar akun peran asli untuk pengujian sistem:

| Peran (Role) | Email | Password |
| :--- | :--- | :--- |
| Admin | admin@hijab.id | qwertyu123 |
| Customer | customer@hijab.id | qwertyu123 |
| Kurir | courier@hijab.id | qwertyu123 |
| Developer | developer@hijab.id | qwertyu123 |
| Blogger | blogger@hijab.id | qwertyu123 |

---

## Troubleshooting Umum
- **Error 500 / Layar Putih**: Coba cek *file* `storage/logs/laravel.log` atau pastikan Anda sudah menjalankan perintah `php artisan key:generate`.
- **Database tidak terhubung / Access denied**: Pastikan service database (MySQL) berjalan dan kredensial (`DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`) di `.env` sudah benar. 
- **Tampilan berantakan (CSS/JS tidak termuat)**: Pastikan Anda sudah menjalankan perintah `npm run dev` atau `npm run build`.

