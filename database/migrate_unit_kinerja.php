<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS `unit_kinerja_evaluations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `target_type` ENUM('fakultas', 'prodi') NOT NULL,
        `fakultas_id` INT NOT NULL,
        `prodi_id` INT NULL,
        `tahun_akademik` VARCHAR(50) NOT NULL DEFAULT 'Semua',
        `skor_otomatis` DECIMAL(5,2) DEFAULT NULL,
        `predikat` VARCHAR(50) DEFAULT NULL,
        `siklus_terpenuhi` VARCHAR(255) DEFAULT NULL,
        `siklus_kosong` VARCHAR(255) DEFAULT NULL,
        `catatan_lpm` TEXT NULL,
        `lpm_user_id` INT NULL,
        `lpm_user_name` VARCHAR(255) NULL,
        `lpm_evaluated_at` DATETIME NULL,
        `catatan_gpm` TEXT NULL,
        `gpm_user_id` INT NULL,
        `gpm_user_name` VARCHAR(255) NULL,
        `gpm_evaluated_at` DATETIME NULL,
        `is_forwarded` TINYINT(1) DEFAULT 0,
        `forwarded_at` DATETIME NULL,
        `forwarded_by_user_id` INT NULL,
        `forwarded_by_name` VARCHAR(255) NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_unit_ta` (`target_type`, `fakultas_id`, `prodi_id`, `tahun_akademik`),
        INDEX `idx_fakultas` (`fakultas_id`),
        INDEX `idx_prodi` (`prodi_id`),
        INDEX `idx_ta` (`tahun_akademik`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "Tabel unit_kinerja_evaluations berhasil dibuat / diverifikasi.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
