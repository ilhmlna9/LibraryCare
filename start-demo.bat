@echo off
title LibraryCare Localhost
color 0B

echo ============================================================
echo               LIBRARYCARE - LOCALHOST RUNNER
echo ============================================================
echo.
echo Server berjalan di: http://127.0.0.1:8000
echo Membuka website di browser...
echo.

cd /d "c:\xampp\htdocs\LibraryCare"

start "" http://127.0.0.1:8000

php artisan serve --host=127.0.0.1 --port=8000
pause
