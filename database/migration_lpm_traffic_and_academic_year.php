<?php
/**
 * Migration: Create tahun_akademik_settings & add folder_name column to ppepp_document_files
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    // 1. Create table tahun_akademik_settings
    $db->exec("
        CREATE TABLE IF NOT EXISTS `tahun_akademik_settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `tahun_akademik` VARCHAR(20) NOT NULL UNIQUE,
            `tanggal_mulai` DATE NOT NULL,
            `tanggal_selesai` DATE NOT NULL,
            `is_open` TINYINT(1) NOT NULL DEFAULT 1,
            `catatan` VARCHAR(255) NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[OK] Table tahun_akademik_settings created/checked.\n";

    // Seed default tahun akademik (1 September - 31 Agustus)
    $defaultTAs = [
        ['2023/2024', '2023-09-01', '2024-08-31', 0, 'Tahun Akademik Lalu (Ditutup)'],
        ['2024/2025', '2024-09-01', '2025-08-31', 0, 'Tahun Akademik Lalu (Ditutup)'],
        ['2025/2026', '2025-09-01', '2026-08-31', 1, 'Tahun Akademik Aktif (Terbuka)'],
        ['2026/2027', '2026-09-01', '2027-08-31', 1, 'Tahun Akademik Berjalan (Terbuka)'],
        ['2027/2028', '2027-09-01', '2028-08-31', 0, 'Tahun Akademik Mendatang (Ditutup)']
    ];

    $stmtInsertTA = $db->prepare("
        INSERT IGNORE INTO `tahun_akademik_settings` (`tahun_akademik`, `tanggal_mulai`, `tanggal_selesai`, `is_open`, `catatan`)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($defaultTAs as $ta) {
        $stmtInsertTA->execute($ta);
    }
    echo "[OK] Default tahun_akademik_settings seeded.\n";

    // 2. Add folder_name column to ppepp_document_files if not exists
    $cols = $db->query("SHOW COLUMNS FROM `ppepp_document_files` LIKE 'folder_name'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `ppepp_document_files` ADD COLUMN `folder_name` VARCHAR(255) NOT NULL DEFAULT 'Utama' AFTER `file_extension`");
        echo "[OK] Column folder_name added to ppepp_document_files.\n";
    } else {
        echo "[INFO] Column folder_name already exists in ppepp_document_files.\n";
    }

    // 3. Add file_category column (file, link, photo) to ppepp_document_files if not exists
    $colsCat = $db->query("SHOW COLUMNS FROM `ppepp_document_files` LIKE 'file_category'")->fetchAll();
    if (empty($colsCat)) {
        $db->exec("ALTER TABLE `ppepp_document_files` ADD COLUMN `file_category` ENUM('file', 'link', 'photo') NOT NULL DEFAULT 'file' AFTER `folder_name`");
        echo "[OK] Column file_category added to ppepp_document_files.\n";
    } else {
        echo "[INFO] Column file_category already exists in ppepp_document_files.\n";
    }

    echo "\n[SUCCESS] Migration finished successfully!\n";

} catch (Exception $e) {
    echo "[ERROR] Migration failed: " . $e->getMessage() . "\n";
}
