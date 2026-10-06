<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if column already exists
    $check = $db->query("SHOW COLUMNS FROM ppepp_documents LIKE 'status_review'")->fetch();
    if (!$check) {
        $db->exec("
            ALTER TABLE ppepp_documents 
            ADD COLUMN `status_review` ENUM('belum_direview', 'perlu_perbaikan', 'sudah_diperbaiki', 'sesuai') NOT NULL DEFAULT 'belum_direview' AFTER `can_download_public`,
            ADD COLUMN `catatan_review` TEXT NULL AFTER `status_review`,
            ADD COLUMN `reviewed_by` INT NULL AFTER `catatan_review`,
            ADD COLUMN `reviewed_at` DATETIME NULL AFTER `reviewed_by`,
            ADD CONSTRAINT `fk_docs_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ");
        echo "SUCCESS: Added review columns to ppepp_documents.\n";
    } else {
        echo "INFO: review columns already exist in ppepp_documents.\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
