<?php
require_once dirname(__DIR__) . '/config/database.php';
$db = Database::getInstance()->getConnection();
$rows = $db->query('
    SELECT p.id, p.nama_prodi, p.jenjang, p.kode_prodi, f.nama_fakultas, f.kode_fakultas 
    FROM prodis p 
    JOIN fakultas f ON p.fakultas_id = f.id 
    ORDER BY f.id, p.id
')->fetchAll();
foreach ($rows as $r) {
    echo "[{$r['kode_fakultas']}] {$r['jenjang']} {$r['nama_prodi']} (ID: {$r['id']})\n";
}
