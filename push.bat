@echo off
title Git Push SPMI PPEPP
color 0B
echo ========================================================
echo   MENGIRIM (PUSH) PERUBAHAN KE REPOSITORI GITHUB
echo ========================================================
echo.
cd /d "%~dp0"

:: Cek apakah ada perubahan
git status -s
echo.

set /p commit_msg="Masukkan pesan commit (kosongkan untuk pesan otomatis): "

if "%commit_msg%"=="" (
    set commit_msg=update: sinkronisasi pembaruan berkas spmi ppepp
)

echo.
echo [1/3] Menambahkan berkas ke staging (git add .)...
git add .

echo [2/3] Menyimpan commit (git commit)...
git commit -m "%commit_msg%"

echo [3/3] Mendorong ke GitHub (git push origin main)...
git push origin main

echo.
if %ERRORLEVEL% EQU 0 (
    echo [BERHASIL] Seluruh perubahan berhasil di-push ke GitHub!
) else (
    echo [PERHATIAN] Terjadi kendala saat push. Pastikan sudah melakukan pull terlebih dahulu.
)
echo.
echo Tekan tombol apa saja untuk menutup jendela ini...
pause >nul

