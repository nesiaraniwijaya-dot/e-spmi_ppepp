<?php
/**
 * Migrasi Tabel Evaluasi Kinerja GPM oleh LPM (`gpm_evaluations`)
 * Menyimpan hasil penilaian/evaluasi manual dari LPM/Kepala LPM untuk tiap anggota GPM.
 *
 * Cara menjalankan: php database/migration_gpm_evaluations.php
 */

require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

echo "========================================================\n";
echo "  MIGRASI TABEL `gpm_evaluations` (PENILAIAN LPM -> GPM)\n";
echo "========================================================\n\n";

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS `gpm_evaluations` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `gpm_user_id` INT(11) NOT NULL,
            `fakultas_id` INT(11) NOT NULL,
            `evaluator_id` INT(11) NOT NULL,
            `evaluator_name` VARCHAR(255) NOT NULL,
            `evaluator_role` VARCHAR(50) NOT NULL,
            `periode` VARCHAR(50) NOT NULL DEFAULT '2025/2026',
            `skor_ketelitian` TINYINT(3) UNSIGNED NOT NULL DEFAULT 80,
            `skor_kecepatan` TINYINT(3) UNSIGNED NOT NULL DEFAULT 80,
            `skor_komunikasi` TINYINT(3) UNSIGNED NOT NULL DEFAULT 80,
            `skor_manual_total` DECIMAL(5,2) NOT NULL DEFAULT 80.00,
            `catatan_evaluasi` TEXT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_gpm_eval_user` (`gpm_user_id`, `created_at`),
            KEY `idx_gpm_eval_fak` (`fakultas_id`, `periode`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[OK] Tabel `gpm_evaluations` siap digunakan.\n";
} catch (\Throwable $e) {
    fwrite(STDERR, "[ERR] Gagal membuat tabel `gpm_evaluations`: " . $e->getMessage() . "\n");
    exit(1);
}
