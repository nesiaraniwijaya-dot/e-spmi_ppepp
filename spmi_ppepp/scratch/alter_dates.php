<?php
require_once __DIR__ . '/../config/database.php';

$pdo = Database::getInstance()->getConnection();
$pdo->exec("ALTER TABLE ppepp_documents MODIFY tanggal_berlaku_mulai DATE NULL");
$pdo->exec("ALTER TABLE ppepp_documents MODIFY tanggal_berlaku_selesai DATE NULL");
$pdo->exec("ALTER TABLE ppepp_documents MODIFY tahun_akademik VARCHAR(20) NOT NULL");
echo "SUCCESS: Database columns modified.\n";
