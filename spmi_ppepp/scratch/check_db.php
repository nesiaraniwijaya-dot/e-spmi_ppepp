<?php
require_once __DIR__ . '/../config/database.php';
$pdo = Database::getInstance()->getConnection();
$cols = $pdo->query("DESCRIBE ppepp_document_files")->fetchAll(PDO::FETCH_ASSOC);
echo "=== COLUMNS IN ppepp_document_files ===\n";
foreach ($cols as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "\n=== TABLES ===\n";
print_r($tables);
