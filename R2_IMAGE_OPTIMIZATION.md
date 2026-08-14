# Panduan Optimasi Gambar Cloudflare R2

Panduan ini berisi langkah-langkah lengkap untuk melakukan manajemen dan optimasi gambar yang disimpan di Cloudflare R2 Bucket, termasuk mengonversi ke format WebP, memampatkan ukuran file, dan membuat variasi resolusi (responsive images).

## 1. Persiapan Environment

Sangat disarankan untuk menggunakan *Virtual Environment* (venv) agar library Python yang diinstal tidak bentrok dengan proyek lain di sistem Anda.

Buka terminal dan pastikan Anda berada di root direktori project (`d:\laragonzo\www\hijab.com`):

```bash
# 1. Buat virtual environment bernama 'venv' (hanya perlu dilakukan sekali)
python -m venv venv

# 2. Aktifkan virtual environment
# Untuk Windows (Command Prompt / PowerShell):
.\venv\Scripts\activate

# Untuk Linux / Mac / Git Bash (di Windows):
source venv/Scripts/activate

# 3. Masuk ke folder script
cd py/r2_resizer

# 4. Install semua library yang dibutuhkan
pip install -r requirements.txt

# 5. Kembali ke root direktori setelah instalasi selesai
cd ../..
```

*(Catatan: Pastikan file `.env` di root project Anda sudah memiliki variabel kredensial Cloudflare R2 yang benar sebelum menjalankan script).*

---

## 2. Urutan Eksekusi Script

Untuk mendapatkan hasil yang paling optimal dan rapi, disarankan menjalankan script-script ini dengan urutan sebagai berikut:

### Langkah 1: Konversi Master Gambar ke WebP
Script ini akan memastikan semua gambar Anda diubah menjadi format WebP agar lebih ringan, tanpa mengubah nama atau ekstensi aslinya (Content-Type diubah di server R2).

```bash
# Pastikan venv masih aktif
python py/convert_to_webp.py
```

### Langkah 2: Kompresi Gambar Berukuran Besar
Script ini akan melakukan scanning pada seluruh gambar. Jika ada gambar master yang ukurannya masih di atas 500 KB (bahkan setelah jadi WebP), script ini akan melakukan kompresi agresif (menurunkan kualitas atau dimensi) agar ukurannya berada di bawah 500 KB.

```bash
# Pastikan venv masih aktif
python py/compress_r2_images.py
```

### Langkah 3: Membuat Variasi Gambar Responsif (Resize)
Langkah terakhir ini bertugas memecah dan membuat versi responsif (300w, 600w, 900w, 1200w) dari gambar-gambar master yang sudah dioptimasi sebelumnya. Gambar variasi ini akan diunggah dengan format WebP.

```bash
# Masuk ke direktori r2_resizer
cd py/r2_resizer

# OPSI A: Jalankan dry-run terlebih dahulu (untuk melihat apa yang akan diubah tanpa memodifikasi data)
python resize_images.py --dry-run

# OPSI B: Eksekusi untuk seluruh bucket
python resize_images.py

# OPSI C: Eksekusi dengan mengatur jumlah worker (lebih cepat, misal 8 worker) dan direktori tertentu
python resize_images.py --workers 8 --prefix "products/"

# Kembali ke root direktori setelah selesai
cd ../..
```

---

## 3. Keluar dari Virtual Environment
Jika seluruh pekerjaan optimasi telah selesai, Anda dapat menonaktifkan virtual environment dengan command berikut:

```bash
deactivate
```
