@echo off
chcp 65001 >nul
title Matikan LibraryCare
echo ========================================================
echo           MEMATIKAN SEMUA SERVICE LIBRARYCARE
echo ========================================================
echo.
echo Menghentikan Server Laravel, Ngrok Tunnel, dan Telegram Bot...
taskkill /F /FI "WINDOWTITLE eq Laravel Server*" >nul 2>&1
taskkill /F /FI "WINDOWTITLE eq Ngrok Tunnel*" >nul 2>&1
taskkill /F /FI "WINDOWTITLE eq Telegram Bot*" >nul 2>&1

echo.
echo Semua service LibraryCare telah dinonaktifkan dengan aman.
echo.
timeout /t 3

