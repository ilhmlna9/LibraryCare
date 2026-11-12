#!/usr/bin/env bash
set -e

echo "=== Memulai Deployment LibraryCare ==="

# Menjalankan migrasi database otomatis
echo "Menjalankan migrasi database..."
php artisan migrate --force

# Mengisi data awal (admin & kategori) jika kosong
echo "Menjalankan seed awal..."
php artisan db:seed --force || true

# Optimasi cache Laravel
echo "Mengoptimalkan cache konfigurasi..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Membuat storage link untuk upload gambar
php artisan storage:link || true

echo "=== Menjalankan Web Server LibraryCare ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
