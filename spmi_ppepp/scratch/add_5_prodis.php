<?php
require_once dirname(__DIR__) . '/config/database.php';
$db = Database::getInstance()->getConnection();

// Check if already exist
$check = $db->query("SELECT COUNT(*) FROM prodis WHERE kode_prodi IN ('55203', '55101')")->fetchColumn();
if ($check == 0) {
    $stmt = $db->prepare("
        INSERT INTO prodis (fakultas_id, nama_prodi, jenjang, kode_prodi, nama_kaprodi, nidn_kaprodi, created_at)
        VALUES 
        (2, 'Rekayasa Perangkat Lunak', 'S1', '55203', 'Dr. Bernardus Harnadi, S.Kom., M.Sc.', '0625077401', NOW()),
        (2, 'Magister Sistem Informasi', 'S2', '55101', 'Prof. Dr. Ridwan Sanjaya, S.E., S.Kom.', '0624087701', NOW())
    ");
    $stmt->execute();
    echo "Inserted 2 prodis for FIKOM! Total prodis in FIKOM is now 5!\n";
} else {
    echo "Already exists, total prodis in FIKOM is already 5.\n";
}
