<?php
/**
 * Skrip Migrasi Database:
 * 1. Menambahkan kolom `tanggal_dokumen` pada tabel `ppepp_documents`
 * 2. Membuat tabel `academic_year_deadlines` (Master Tahun Akademik Aktif & Tenggat Upload)
 * 3. Membuat tabel `upload_access_requests` (Pengajuan & Persetujuan Izin Upload Dokumen Tahun Lampau)
 * 4. Inisialisasi data default
 * 
 * SPMI PPEPP (MITRA) UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "=== MEMULAI MIGRASI FITUR AKADEMIK, DEADLINE, & AKSES UPLOAD ===\n";

    // ----------------------------------------------------
    // 1. Tambah kolom `tanggal_dokumen` pada `ppepp_documents`
    // ----------------------------------------------------
    $cols = $db->query("SHOW COLUMNS FROM `ppepp_documents` LIKE 'tanggal_dokumen'")->fetchAll();
    if (empty($cols)) {
        echo "Menambahkan kolom `tanggal_dokumen` ke tabel `ppepp_documents`...\n";
        $db->exec("
            ALTER TABLE `ppepp_documents` 
            ADD COLUMN `tanggal_dokumen` DATE NULL AFTER `tahun_akademik`
        ");
        echo "Kolom `tanggal_dokumen` berhasil ditambahkan.\n";

        // Mengisi tanggal_dokumen untuk rekaman lama berdasarkan created_at
        $db->exec("
            UPDATE `ppepp_documents` 
            SET `tanggal_dokumen` = DATE(`created_at`) 
            WHERE `tanggal_dokumen` IS NULL AND `created_at` IS NOT NULL
        ");
        echo "Sinkronisasi tanggal_dokumen dari created_at berhasil.\n";
    } else {
        echo "Kolom `tanggal_dokumen` sudah ada di `ppepp_documents`.\n";
    }

    // ----------------------------------------------------
    // 2. Buat tabel `academic_year_deadlines`
    // ----------------------------------------------------
    echo "Memeriksa tabel `academic_year_deadlines`...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS `academic_year_deadlines` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `tahun_akademik` varchar(20) NOT NULL UNIQUE,
            `is_active` tinyint(1) NOT NULL DEFAULT 0,
            `deadline_upload` datetime DEFAULT NULL,
            `keterangan` varchar(255) DEFAULT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Tabel `academic_year_deadlines` siap.\n";

    // Inisialisasi Tahun Akademik Default jika tabel masih kosong
    $countAy = (int)$db->query("SELECT COUNT(*) FROM `academic_year_deadlines`")->fetchColumn();
    if ($countAy === 0) {
        echo "Menginisialisasi data awal tahun akademik...\n";
        // Ambil daftar tahun akademik yang sudah ada di dokumen
        $existingYears = $db->query("
            SELECT DISTINCT `tahun_akademik` 
            FROM `ppepp_documents` 
            WHERE `tahun_akademik` REGEXP '^[0-9]{4}/[0-9]{4}$'
            ORDER BY `tahun_akademik` DESC
        ")->fetchAll(PDO::FETCH_COLUMN) ?: [];

        $defaultYears = ['2026/2027', '2025/2026', '2024/2025'];
        $allYears = array_values(array_unique(array_merge($defaultYears, $existingYears)));
        rsort($allYears);

        $stmtInsAy = $db->prepare("
            INSERT INTO `academic_year_deadlines` (`tahun_akademik`, `is_active`, `deadline_upload`, `keterangan`)
            VALUES (?, ?, ?, ?)
        ");

        $isFirst = true;
        foreach ($allYears as $yr) {
            $isActive = $isFirst ? 1 : 0;
            // Deadline default: 31 Agustus tahun kedua pukul 23:59:59
            $parts = explode('/', $yr);
            $secondYear = isset($parts[1]) ? (int)$parts[1] : ((int)$parts[0] + 1);
            $deadline = $secondYear . '-08-31 23:59:59';
            $desc = $isActive ? 'Tahun Akademik Aktif (Berjalan)' : 'Tahun Akademik Lampau';

            $stmtInsAy->execute([$yr, $isActive, $deadline, $desc]);
            $isFirst = false;
        }
        echo "Data awal tahun akademik berhasil dimasukkan.\n";
    }

    // ----------------------------------------------------
    // 3. Buat tabel `upload_access_requests`
    // ----------------------------------------------------
    echo "Memeriksa tabel `upload_access_requests`...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS `upload_access_requests` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `level` enum('prodi','fakultas') NOT NULL,
            `prodi_id` int(11) DEFAULT NULL,
            `fakultas_id` int(11) DEFAULT NULL,
            `tahun_akademik` varchar(20) NOT NULL,
            `alasan` text NOT NULL,
            `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
            `akses_berlaku_sampai` datetime DEFAULT NULL,
            `catatan_lpm` text DEFAULT NULL,
            `processed_by` int(11) DEFAULT NULL,
            `processed_at` datetime DEFAULT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_unit_status` (`status`, `level`, `prodi_id`, `fakultas_id`, `tahun_akademik`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Tabel `upload_access_requests` siap.\n";

    echo "\n=== MIGRASI SELESAI DENGAN SUKSES! ===\n";

} catch (\Throwable $e) {
    echo "ERROR MIGRASI: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
