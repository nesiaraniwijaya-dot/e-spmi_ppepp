<?php
/**
 * Migration: Add 'draft' to status_review ENUM on ppepp_documents
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

echo "=== MEMULAI MIGRASI STATUS DRAFT PPEPP ===\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Update ENUM on ppepp_documents
    $pdo->exec("
        ALTER TABLE `ppepp_documents` 
        MODIFY COLUMN `status_review` ENUM('draft', 'belum_direview', 'perlu_perbaikan', 'sudah_diperbaiki', 'sesuai') 
        NOT NULL DEFAULT 'belum_direview'
    ");
    echo "SUCCESS: Column status_review altered to include 'draft'.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
