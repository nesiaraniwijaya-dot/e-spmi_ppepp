<?php
/**
 * Konfigurasi Database Lingkungan (Hosting / Production)
 * 
 * Salin file ini menjadi `env.php` di folder `config/` jika ingin mengubah
 * koneksi database tanpa mengutak-atik file config/database.php.
 * 
 * Khusus ProFreeHost:
 * - DB_HOST: Dapatkan di Client Area / Control Panel (contoh: sql100.byetcluster.com atau sql200.epizy.com)
 * - DB_PORT: 3306
 * - DB_DATABASE: Nama database lengkap (contoh: ezyro_12345678_spmi)
 * - DB_USERNAME: Username MySQL dari hosting (contoh: ezyro_12345678)
 * - DB_PASSWORD: Password vPanel/cPanel hosting Anda
 */

return [
    'DB_HOST'     => 'sqlxxx.byetcluster.com', // Ganti dengan MySQL Host dari ProFreeHost
    'DB_PORT'     => '3306',
    'DB_DATABASE' => 'unaux_xxxxxxxx_spmi_ppepp', // Ganti dengan nama database di cPanel
    'DB_USERNAME' => 'unaux_xxxxxxxx',            // Ganti dengan username MySQL di cPanel
    'DB_PASSWORD' => 'PasswordHostingAnda',        // Ganti dengan password akun hosting
];
