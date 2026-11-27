<?php
/**
 * Migrasi Skema Database: Penambahan Scope pada Bidang Standar & Sub Bidang Standar
 * Menjadikan standar mutu dapat dikelola oleh GPM tingkat Fakultas dan Prodi
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "=== MEMULAI MIGRASI SCOPED BIDANG & SUB-BIDANG STANDAR ===\n\n";

    // 1. Cek & Tambah kolom pada tabel bidang_standar
    $colsBidang = $db->query("SHOW COLUMNS FROM bidang_standar")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('scope', $colsBidang)) {
        $db->exec("ALTER TABLE bidang_standar ADD COLUMN `scope` ENUM('universitas', 'fakultas', 'prodi') NOT NULL DEFAULT 'universitas' AFTER `deskripsi`");
        echo "[OK] Kolom 'scope' berhasil ditambahkan ke bidang_standar.\n";
    } else {
        echo "[SKIP] Kolom 'scope' sudah ada di bidang_standar.\n";
    }

    if (!in_array('fakultas_id', $colsBidang)) {
        $db->exec("ALTER TABLE bidang_standar ADD COLUMN `fakultas_id` INT NULL AFTER `scope`, ADD INDEX `idx_bidang_fakultas` (`fakultas_id`)");
        echo "[OK] Kolom 'fakultas_id' berhasil ditambahkan ke bidang_standar.\n";
    } else {
        echo "[SKIP] Kolom 'fakultas_id' sudah ada di bidang_standar.\n";
    }

    if (!in_array('prodi_id', $colsBidang)) {
        $db->exec("ALTER TABLE bidang_standar ADD COLUMN `prodi_id` INT NULL AFTER `fakultas_id`, ADD INDEX `idx_bidang_prodi` (`prodi_id`)");
        echo "[OK] Kolom 'prodi_id' berhasil ditambahkan ke bidang_standar.\n";
    } else {
        echo "[SKIP] Kolom 'prodi_id' sudah ada di bidang_standar.\n";
    }

    if (!in_array('created_by_role', $colsBidang)) {
        $db->exec("ALTER TABLE bidang_standar ADD COLUMN `created_by_role` VARCHAR(50) NOT NULL DEFAULT 'lpm' AFTER `prodi_id`");
        echo "[OK] Kolom 'created_by_role' berhasil ditambahkan ke bidang_standar.\n";
    } else {
        echo "[SKIP] Kolom 'created_by_role' sudah ada di bidang_standar.\n";
    }

    // 2. Cek & Tambah kolom pada tabel sub_bidang_standar
    $colsSub = $db->query("SHOW COLUMNS FROM sub_bidang_standar")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('scope', $colsSub)) {
        $db->exec("ALTER TABLE sub_bidang_standar ADD COLUMN `scope` ENUM('universitas', 'fakultas', 'prodi') NOT NULL DEFAULT 'universitas' AFTER `deskripsi`");
        echo "[OK] Kolom 'scope' berhasil ditambahkan ke sub_bidang_standar.\n";
    } else {
        echo "[SKIP] Kolom 'scope' sudah ada di sub_bidang_standar.\n";
    }

    if (!in_array('fakultas_id', $colsSub)) {
        $db->exec("ALTER TABLE sub_bidang_standar ADD COLUMN `fakultas_id` INT NULL AFTER `scope`, ADD INDEX `idx_sub_fakultas` (`fakultas_id`)");
        echo "[OK] Kolom 'fakultas_id' berhasil ditambahkan ke sub_bidang_standar.\n";
    } else {
        echo "[SKIP] Kolom 'fakultas_id' sudah ada di sub_bidang_standar.\n";
    }

    if (!in_array('prodi_id', $colsSub)) {
        $db->exec("ALTER TABLE sub_bidang_standar ADD COLUMN `prodi_id` INT NULL AFTER `fakultas_id`, ADD INDEX `idx_sub_prodi` (`prodi_id`)");
        echo "[OK] Kolom 'prodi_id' berhasil ditambahkan ke sub_bidang_standar.\n";
    } else {
        echo "[SKIP] Kolom 'prodi_id' sudah ada di sub_bidang_standar.\n";
    }

    if (!in_array('created_by_role', $colsSub)) {
        $db->exec("ALTER TABLE sub_bidang_standar ADD COLUMN `created_by_role` VARCHAR(50) NOT NULL DEFAULT 'lpm' AFTER `prodi_id`");
        echo "[OK] Kolom 'created_by_role' berhasil ditambahkan ke sub_bidang_standar.\n";
    } else {
        echo "[SKIP] Kolom 'created_by_role' sudah ada di sub_bidang_standar.\n";
    }

    // Pastikan seluruh data lama diset ke 'universitas' jika masih null
    $db->exec("UPDATE bidang_standar SET scope = 'universitas', created_by_role = 'lpm' WHERE scope IS NULL OR scope = ''");
    $db->exec("UPDATE sub_bidang_standar SET scope = 'universitas', created_by_role = 'lpm' WHERE scope IS NULL OR scope = ''");

    echo "\n=== MIGRASI SELESAI DENGAN SUKSES ===\n";
} catch (Exception $e) {
    echo "[ERROR] Migrasi gagal: " . $e->getMessage() . "\n";
    exit(1);
}
