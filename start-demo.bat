@echo off
title LibraryCare Localhost Runner
color 0B

echo ============================================================
echo               LIBRARYCARE - LOCALHOST RUNNER
echo ============================================================
echo.
echo [1/3] Membuka website di browser...
cd /d "c:\xampp\htdocs\LibraryCare"
start "" http://127.0.0.1:8000

echo [2/3] Menyalakan Bot Telegram Polling di background...
start /b php artisan telegram:poll > nul 2>&1

echo [3/3] Menjalankan Server Laravel (Port 8000)...
echo.
echo ------------------------------------------------------------
echo  WEBSITE & BOT TELEGRAM SUDAH AKTIF BERSAMAAN!
echo  Tutup jendela ini jika ingin mematikan demo.
echo ------------------------------------------------------------
echo.

php artisan serve --host=127.0.0.1 --port=8000
pause
