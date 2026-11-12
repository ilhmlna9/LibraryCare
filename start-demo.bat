@echo off
title LibraryCare Cloudflare Live Runner
color 0A

echo ============================================================
echo           LIBRARYCARE - CLOUDFLARE LIVE RUNNER
echo ============================================================
echo.

cd /d "c:\xampp\htdocs\LibraryCare"

echo [1/2] Menjalankan Laravel Server (Port 8000)...
start /b php artisan serve --host=127.0.0.1 --port=8000 > nul 2>&1

timeout /t 2 > nul

echo [2/2] Menjalankan Cloudflare Tunnel...
echo.
echo ============================================================
echo  TUNGGU BEBERAPA DETIK SAMPAI MUNCUL LINK https://...trycloudflare.com
echo  Salin link tersebut untuk Webhook WhatsApp dan Telegram!
echo ============================================================
echo.

"C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://127.0.0.1:8000
pause

