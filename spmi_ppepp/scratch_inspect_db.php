<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();
echo "USERS COLS:\n";
print_r($db->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC));

echo "ALL USERS:\n";
print_r($db->query("SELECT id, name, email, role, prodi_id, fakultas_id FROM users")->fetchAll(PDO::FETCH_ASSOC));

echo "ALL FAKULTAS:\n";
print_r($db->query("SELECT id, kode_fakultas, nama_fakultas FROM fakultas ORDER BY id")->fetchAll(PDO::FETCH_ASSOC));

echo "ALL PRODIS:\n";
print_r($db->query("SELECT id, fakultas_id, kode_prodi, nama_prodi, jenjang FROM prodis ORDER BY id")->fetchAll(PDO::FETCH_ASSOC));
