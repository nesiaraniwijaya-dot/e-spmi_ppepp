<?php
require_once __DIR__ . '/../config/database.php';
$pdo = Database::getInstance()->getConnection();

echo "Running migration: update jenis_upload enum in ppepp_documents...\n";
try {
    $pdo->exec("ALTER TABLE `ppepp_documents` MODIFY COLUMN `jenis_upload` ENUM('file', 'link', 'kombinasi') NOT NULL DEFAULT 'file'");
    echo "[OK] Modified jenis_upload column to ENUM('file', 'link', 'kombinasi').\n";
} catch (\Throwable $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}
