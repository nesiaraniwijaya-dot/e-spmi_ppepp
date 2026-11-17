@echo off
title Git Pull SPMI PPEPP
color 0A
echo ========================================================
echo   MENARIK PEMBARUAN TERBARU DARI GITHUB (ORIGIN MAIN)
echo ========================================================
echo.
cd /d "%~dp0"
git pull origin main
echo.
if %ERRORLEVEL% EQU 0 (
    echo [BERHASIL] Repositori telah diperbarui ke versi paling baru!
) else (
    echo [PERHATIAN] Terjadi kendala saat melakukan pull. Periksa koneksi atau konflik.
)
echo.
echo Tekan tombol apa saja untuk menutup jendela ini...
pause >nul

