<?php
/**
 * Migrasi Kolom Icon & Warna Fakultas (Idempoten)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getInstance()->getConnection();
    
    // 1. Tambahkan kolom icon dan warna pada tabel fakultas jika belum ada
    $cols = $pdo->query("SHOW COLUMNS FROM fakultas")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('icon', $cols)) {
        $pdo->exec("ALTER TABLE `fakultas` ADD COLUMN `icon` VARCHAR(50) NOT NULL DEFAULT 'fa-landmark' AFTER `deskripsi`");
        echo "[OK] Kolom `icon` berhasil ditambahkan pada tabel `fakultas`.\n";
    }
    if (!in_array('warna', $cols)) {
        $pdo->exec("ALTER TABLE `fakultas` ADD COLUMN `warna` VARCHAR(30) NOT NULL DEFAULT '#1E3E62' AFTER `icon`");
        echo "[OK] Kolom `warna` berhasil ditambahkan pada tabel `fakultas`.\n";
    }

    // 2. Update default warna & icon
    $updates = [
        'FAD'   => ['icon' => 'fa-compass-drafting', 'warna' => '#7C3AED'],
        'FIKOM' => ['icon' => 'fa-laptop-code',      'warna' => '#2563EB'],
        'FIK'   => ['icon' => 'fa-laptop-code',      'warna' => '#2563EB'],
        'FEB'   => ['icon' => 'fa-chart-line',        'warna' => '#059669'],
        'FBS'   => ['icon' => 'fa-language',          'warna' => '#C2410C'],
        'FT'    => ['icon' => 'fa-gears',             'warna' => '#D97706'],
        'FPSI'  => ['icon' => 'fa-brain',             'warna' => '#0D9488'],
        'FHK'   => ['icon' => 'fa-scale-balanced',    'warna' => '#BE123C'],
        'FTP'   => ['icon' => 'fa-seedling',          'warna' => '#15803D'],
        'FK'    => ['icon' => 'fa-stethoscope',       'warna' => '#0284C7'],
        'FITL'  => ['icon' => 'fa-leaf',              'warna' => '#059669'],
        'PIP'   => ['icon' => 'fa-lightbulb',         'warna' => '#4F46E5'],
    ];

    $stmt = $pdo->prepare("UPDATE `fakultas` SET `icon` = ?, `warna` = ? WHERE `kode_fakultas` = ?");
    foreach ($updates as $kode => $meta) {
        $stmt->execute([$meta['icon'], $meta['warna'], $kode]);
    }
    echo "[OK] Ikon dan warna default fakultas berhasil disinkronkan.\n";

} catch (\Throwable $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
