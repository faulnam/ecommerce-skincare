# ecommerce-skincare
# LUMINA Skincare - E-Commerce Platform

Platform e-commerce skincare premium dengan formulasi dermatologis teruji klinis dan terdaftar resmi BPOM. Dokumen ini berisi panduan untuk melakukan *setup* awal proyek di lingkungan lokal (development).

## Prasyarat

Pastikan komputer Anda sudah menginstal aplikasi berikut:
- **PHP** (Minimal versi 8.2 / 8.3)
- **Composer**
- **Node.js** & **NPM**
- **MySQL / MariaDB** (melalui Laragon, XAMPP, atau sejenisnya)
- **Git**

## Langkah-langkah Setup

### 1. Clone Repository
```bash
git clone https://github.com/faulnam/ecommerce-skincare.git
cd ecommerce-skincare
```

### 2. Install Dependensi PHP (Composer)
```bash
composer install
```

### 3. Install Dependensi JavaScript (NPM)
```bash
npm install
```

### 4. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan pengaturan database di dalam file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_skincare
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Migrasi dan Seeding Database
Pastikan layanan MySQL sudah menyala dan database `ecommerce_skincare` sudah dibuat:
```sql
CREATE DATABASE IF NOT EXISTS ecommerce_skincare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Setelah itu jalankan migrasi & seeder lengkap:
```bash
php artisan migrate:fresh --seed
```

### 7. Buat Symbolic Link Storage
```bash
php artisan storage:link
```

### 8. Jalankan Server Development
Buka terminal dan jalankan:
```bash
php artisan serve
```
Dan di terminal kedua untuk assets frontend:
```bash
npm run dev
```
Akses aplikasi melalui browser di: `http://localhost:8000`.

---

## Akun Pengguna Asli (Default)

Berikut adalah daftar akun peran asli untuk pengujian sistem:

| Peran (Role) | Email | Password |
| :--- | :--- | :--- |
| Admin | admin@skincare.id | qwertyu123 |
| Customer | customer@skincare.id | qwertyu123 |
| Kurir | courier@skincare.id | qwertyu123 |
| Developer | developer@skincare.id | qwertyu123 |
| Blogger | blogger@skincare.id | qwertyu123 |

### Akun Demo Tambahan
| Peran (Role) | Email | Password |
| :--- | :--- | :--- |
| Demo Admin | demo_admin@skincare.id | qwertyu123 |
| Demo Customer | demo_customer@skincare.id | qwertyu123 |
| Demo Kurir | demo_courier@skincare.id | qwertyu123 |
| Demo Developer | demo_developer@skincare.id | qwertyu123 |
| Demo Blogger | demo_blogger@skincare.id | qwertyu123 |

---

## Troubleshooting Umum
- **Error 500 / Layar Putih**: Cek file `storage/logs/laravel.log` atau jalankan `php artisan key:generate`.
- **Database Error**: Pastikan database `ecommerce_skincare` sudah dibuat dan kredensial `.env` sesuai.
- **Tampilan CSS/JS**: Jalankan `npm run dev` atau `npm run build`.
