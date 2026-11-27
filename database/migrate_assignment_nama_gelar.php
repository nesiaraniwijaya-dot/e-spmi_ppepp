<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "=== MEMULAI MIGRASI KOLOM nama_lengkap_gelar PADA TABEL user_assignments ===\n";

    $cols = $db->query("SHOW COLUMNS FROM user_assignments")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('nama_lengkap_gelar', $cols)) {
        $db->exec("ALTER TABLE user_assignments ADD COLUMN `nama_lengkap_gelar` VARCHAR(255) NULL AFTER `prodi_id`");
        echo "[OK] Kolom 'nama_lengkap_gelar' berhasil ditambahkan ke tabel user_assignments.\n";
    } else {
        echo "[SKIP] Kolom 'nama_lengkap_gelar' sudah ada di tabel user_assignments.\n";
    }

    echo "=== MIGRASI SELESAI DENGAN SUKSES ===\n";
} catch (Exception $e) {
    echo "[ERROR] Migrasi gagal: " . $e->getMessage() . "\n";
    exit(1);
}
