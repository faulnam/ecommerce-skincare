#!/bin/bash

# Berhenti jika ada perintah yang error
set -e

# Mengambil environment dari argumen (default ke staging jika tidak diisi)
ENV=${1:-staging}

echo "Update Source code sesuai branch"
sudo git pull

echo "Memulai proses deployment untuk environment: $ENV ..."

echo "Install dependencies..."
sudo composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
sudo npm install
sudo npm run build

echo "Clear dan cache ulang config, route, view..."
sudo php artisan optimize:clear
sudo php artisan optimize

echo "Deployment untuk $ENV berhasil!"
