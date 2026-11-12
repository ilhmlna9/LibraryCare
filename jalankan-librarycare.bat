@echo off
chcp 65001 >nul
title LibraryCare Launcher
cd /d "%~dp0"

echo ========================================================
echo       MENYALAKAN SISTEM LIBRARYCARE PERPUSTAKAAN
echo ========================================================
echo.

echo [1/3] Menjalankan Server Laravel (Port 8000)...
start "LibraryCare - Laravel Server" cmd /k "title Laravel Server && php artisan serve --host=127.0.0.1 --port=8000"

timeout /t 2 /nobreak >nul

echo [2/3] Menjalankan Tunnel Ngrok (bolt-numerate-eskimo)...
start "LibraryCare - Ngrok Tunnel" cmd /k "title Ngrok Tunnel && node tunnel.cjs"

timeout /t 2 /nobreak >nul

echo [3/3] Menjalankan Worker Telegram Bot...
start "LibraryCare - Telegram Bot" cmd /k "title Telegram Bot && php artisan telegram:poll"

timeout /t 2 /nobreak >nul

echo.
echo Membuka website di browser...
start http://localhost:8000

echo.
echo ========================================================
echo     SEMUA SERVICE TELAH BERJALAN DENGAN SUKSES!
echo.
echo   1. Laravel Server  : http://localhost:8000
echo   2. Ngrok Tunnel    : https://bolt-numerate-eskimo.ngrok-free.dev
echo   3. Telegram Bot    : Aktif Polling
echo.
echo   Jangan tutup jendela-jendela hitam yang terbuka tadi
echo   selama demo atau pengujian berlangsung.
echo ========================================================
echo.
pause

