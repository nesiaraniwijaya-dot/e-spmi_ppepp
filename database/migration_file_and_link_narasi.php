<?php
/**
 * Migration: Add 'narasi' column to ppepp_document_files
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "=== MIGRATION: ADD NARASI TO DOCUMENT FILES ===\n";

try {
    $pdo->exec("ALTER TABLE `ppepp_document_files` ADD COLUMN `narasi` TEXT NULL AFTER `file_extension`");
    echo "[OK] Added 'narasi' column to 'ppepp_document_files' table successfully.\n";
} catch (\Throwable $e) {
    echo "[INFO] Column 'narasi' already exists or error: " . $e->getMessage() . "\n";
}

echo "=== MIGRATION COMPLETED ===\n";
