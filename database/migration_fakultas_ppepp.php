<?php
/**
 * Migration & Seeder: SPMI PPEPP Tingkat Fakultas & Profil Dekanat
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

echo "=== MEMULAI MIGRASI PPEPP TINGKAT FAKULTAS & DEKANAT ===\n";

$db = Database::getInstance();
$pdo = $db->getConnection();

try {
    // 1. Update tabel `users`: tambah role 'admin_fakultas' dan kolom `fakultas_id`
    echo "1. Mengupdate tabel `users`...\n";
    $pdo->exec("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('super_admin', 'kepala_lpm', 'admin_prodi', 'admin_fakultas') NOT NULL");

    $cols = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'fakultas_id'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `fakultas_id` INT NULL AFTER `prodi_id`, 
                    ADD CONSTRAINT `fk_users_fakultas` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas`(`id`) ON DELETE SET NULL");
        echo "   [OK] Kolom `fakultas_id` berhasil ditambahkan pada tabel `users`.\n";
    } else {
        echo "   [SKIP] Kolom `fakultas_id` sudah ada pada tabel `users`.\n";
    }

    // 2. Update tabel `fakultas`: tambah atribut profil dekanat
    echo "2. Mengupdate tabel `fakultas` untuk Profil Dekanat...\n";
    $dekanatColumns = [
        'nama_dekan' => "VARCHAR(150) NULL AFTER `nama_fakultas`",
        'nidn_dekan' => "VARCHAR(50) NULL AFTER `nama_dekan`",
        'nama_wadek' => "VARCHAR(150) NULL AFTER `nidn_dekan`",
        'nidn_wadek' => "VARCHAR(50) NULL AFTER `nama_wadek`",
        'periode_jabatan' => "VARCHAR(50) NULL DEFAULT '2022 - 2026' AFTER `nidn_wadek`"
    ];

    foreach ($dekanatColumns as $colName => $colDef) {
        $check = $pdo->query("SHOW COLUMNS FROM `fakultas` LIKE '{$colName}'")->fetchAll();
        if (empty($check)) {
            $pdo->exec("ALTER TABLE `fakultas` ADD COLUMN `{$colName}` {$colDef}");
            echo "   [OK] Kolom `{$colName}` berhasil ditambahkan pada tabel `fakultas`.\n";
        } else {
            echo "   [SKIP] Kolom `{$colName}` sudah ada.\n";
        }
    }

    // 3. Update tabel `ppepp_documents`: dukung tingkat fakultas
    echo "3. Mengupdate tabel `ppepp_documents`...\n";
    
    // Pastikan prodi_id boleh NULL (karena dokumen tingkat fakultas tidak memiliki prodi_id)
    $pdo->exec("ALTER TABLE `ppepp_documents` MODIFY COLUMN `prodi_id` INT NULL");

    // Kolom fakultas_id
    $checkFakId = $pdo->query("SHOW COLUMNS FROM `ppepp_documents` LIKE 'fakultas_id'")->fetchAll();
    if (empty($checkFakId)) {
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `fakultas_id` INT NULL AFTER `prodi_id`,
                    ADD CONSTRAINT `fk_docs_fakultas` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas`(`id`) ON DELETE CASCADE");
        echo "   [OK] Kolom `fakultas_id` berhasil ditambahkan pada tabel `ppepp_documents`.\n";
    } else {
        echo "   [SKIP] Kolom `fakultas_id` sudah ada pada tabel `ppepp_documents`.\n";
    }

    // Kolom level
    $checkLevel = $pdo->query("SHOW COLUMNS FROM `ppepp_documents` LIKE 'level'")->fetchAll();
    if (empty($checkLevel)) {
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `level` ENUM('prodi', 'fakultas') NOT NULL DEFAULT 'prodi' AFTER `fakultas_id`");
        echo "   [OK] Kolom `level` berhasil ditambahkan pada tabel `ppepp_documents`.\n";
    } else {
        echo "   [SKIP] Kolom `level` sudah ada pada tabel `ppepp_documents`.\n";
    }

    // Isi fakultas_id untuk dokumen prodi yang sudah ada sebelumnya
    $pdo->exec("
        UPDATE ppepp_documents d 
        JOIN prodis p ON d.prodi_id = p.id 
        SET d.fakultas_id = p.fakultas_id, d.level = 'prodi' 
        WHERE d.prodi_id IS NOT NULL AND (d.fakultas_id IS NULL OR d.level IS NULL)
    ");
    echo "   [OK] Sinkronisasi data lama: fakultas_id dokumen prodi telah diisi.\n";

    // 4. Update Profil Dekanat Bawaan untuk Fakultas
    echo "4. Mengisi data awal Profil Dekanat...\n";
    $dekanatDefaults = [
        'FIK' => ['Dr. Ridwan Sanjaya, S.E., S.Kom., MS.IEC.', '0624087701', 'Erdhi Widyarto Nugroho, S.T., M.T.', '0618037901', '2022 - 2026'],
        'FEB' => ['Dr. St. Vena Purnamasari, S.E., M.Si.', '0602057501', 'Dr. Agnes Advensia C., S.E., M.Si., Akt.', '0615097402', '2022 - 2026'],
        'FT'  => ['Dr. Ir. Djoko Suwarno, M.T.', '0610046501', 'Daniel Hartanto, S.T., M.Eng.', '0614058101', '2022 - 2026'],
        'FPSI'=> ['Dr. Margaretha Sih Setija Utami, M.Kes.', '0622046101', 'Kuriake Kharismawan, S.Psi., M.Si.', '0605078501', '2022 - 2026'],
        'FAD' => ['Dr. Ir. B. Tyas Susanti, M.T.', '0629086701', 'Peter Ardiansyah, S.Sn., M.Ds.', '0621098401', '2022 - 2026']
    ];

    $stmtUpdFak = $pdo->prepare("UPDATE `fakultas` SET `nama_dekan` = ?, `nidn_dekan` = ?, `nama_wadek` = ?, `nidn_wadek` = ?, `periode_jabatan` = ? WHERE `kode_fakultas` = ?");
    foreach ($dekanatDefaults as $kFak => $dInfo) {
        $stmtUpdFak->execute([$dInfo[0], $dInfo[1], $dInfo[2], $dInfo[3], $dInfo[4], $kFak]);
    }
    echo "   [OK] Data Dekanat untuk FIK, FEB, FT, FPSI, dan FAD berhasil diperbarui.\n";

    // 5. Seed Akun Admin Fakultas
    echo "5. Membuat Akun Admin Fakultas Bawaan...\n";
    $defaultPass = password_hash('password123', PASSWORD_BCRYPT);
    
    // Dapatkan ID FIK dan FEB
    $resFaks = $pdo->query("SELECT id, kode_fakultas, nama_fakultas FROM fakultas")->fetchAll(PDO::FETCH_ASSOC);
    $fakMap = [];
    foreach ($resFaks as $rf) {
        $fakMap[$rf['kode_fakultas']] = $rf['id'];
    }

    $adminFakultasList = [
        [
            'name' => 'Admin Fakultas Ilmu Komputer (FIK)',
            'email' => 'admin.fik@unika.ac.id',
            'password' => $defaultPass,
            'role' => 'admin_fakultas',
            'fakultas_id' => $fakMap['FIK'] ?? null
        ],
        [
            'name' => 'Admin Fakultas Ekonomi & Bisnis (FEB)',
            'email' => 'admin.feb@unika.ac.id',
            'password' => $defaultPass,
            'role' => 'admin_fakultas',
            'fakultas_id' => $fakMap['FEB'] ?? null
        ]
    ];

    $stmtUserFak = $pdo->prepare("
        INSERT INTO users (name, email, password, role, fakultas_id, is_active)
        VALUES (?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            role = VALUES(role),
            fakultas_id = VALUES(fakultas_id),
            is_active = 1
    ");

    foreach ($adminFakultasList as $uFak) {
        if (!empty($uFak['fakultas_id'])) {
            $stmtUserFak->execute([$uFak['name'], $uFak['email'], $uFak['password'], $uFak['role'], $uFak['fakultas_id']]);
            echo "   [OK] Akun {$uFak['email']} berhasil dibuat/disinkronkan.\n";
        }
    }

    // 6. Seed Contoh Dokumen PPEPP Tingkat Fakultas untuk FIK
    echo "6. Membuat contoh dokumen mutu PPEPP Tingkat Fakultas...\n";
    $fikId = $fakMap['FIK'] ?? null;
    $fikAdminId = $pdo->query("SELECT id FROM users WHERE email = 'admin.fik@unika.ac.id'")->fetchColumn();
    $bidangVmts = $pdo->query("SELECT id FROM bidang_standar WHERE kode_bidang = 'VMTS' LIMIT 1")->fetchColumn();
    $bidangTataPamong = $pdo->query("SELECT id FROM bidang_standar WHERE kode_bidang = 'TATA_PAMONG' LIMIT 1")->fetchColumn();

    if ($fikId && $fikAdminId && $bidangVmts) {
        $sampleFakDocs = [
            [
                'fakultas_id' => $fikId,
                'prodi_id' => null,
                'user_id' => $fikAdminId,
                'bidang_id' => $bidangVmts,
                'nama_dokumen' => 'Rencana Strategis (Renstra) Fakultas Ilmu Komputer 2022-2026',
                'nomor_dokumen' => 'SK-DEK-FIK/2022/012',
                'siklus' => 'penetapan',
                'tahun_akademik' => '2024/2025',
                'level' => 'fakultas',
                'jenis_upload' => 'link',
                'external_link' => json_encode([['url' => 'https://drive.google.com/file/d/1RenstraFIK2026/view', 'narasi' => 'Dokumen Rencana Strategis resmi FIK periode kepemimpinan Dekanat 2022-2026.']], JSON_UNESCAPED_SLASHES),
                'public_page_limit' => 3,
                'can_download_public' => 0,
                'status_review' => 'sesuai',
                'catatan_review' => 'Renstra fakultas telah sesuai dengan arah pengembangan strategis universitas.'
            ],
            [
                'fakultas_id' => $fikId,
                'prodi_id' => null,
                'user_id' => $fikAdminId,
                'bidang_id' => $bidangTataPamong,
                'nama_dokumen' => 'Manual Mutu & Standar Tata Kelola Dekanat FIK',
                'nomor_dokumen' => 'MM-FIK-SCU/2023/004',
                'siklus' => 'pelaksanaan',
                'tahun_akademik' => '2024/2025',
                'level' => 'fakultas',
                'jenis_upload' => 'link',
                'external_link' => json_encode([['url' => 'https://drive.google.com/file/d/1ManualMutuFIK/view', 'narasi' => 'Panduan tata kelola dan implementasi penjaminan mutu fakultas.']], JSON_UNESCAPED_SLASHES),
                'public_page_limit' => 3,
                'can_download_public' => 0,
                'status_review' => 'belum_direview',
                'catatan_review' => null
            ]
        ];

        $stmtDocFak = $pdo->prepare("
            INSERT INTO ppepp_documents (
                fakultas_id, prodi_id, user_id, bidang_id, nama_dokumen, nomor_dokumen, 
                siklus, tahun_akademik, level, jenis_upload, external_link, 
                public_page_limit, can_download_public, status_review, catatan_review, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        foreach ($sampleFakDocs as $sDoc) {
            $exist = $pdo->prepare("SELECT id FROM ppepp_documents WHERE nama_dokumen = ? AND level = 'fakultas'");
            $exist->execute([$sDoc['nama_dokumen']]);
            if (!$exist->fetch()) {
                $stmtDocFak->execute([
                    $sDoc['fakultas_id'], $sDoc['prodi_id'], $sDoc['user_id'], $sDoc['bidang_id'],
                    $sDoc['nama_dokumen'], $sDoc['nomor_dokumen'], $sDoc['siklus'], $sDoc['tahun_akademik'],
                    $sDoc['level'], $sDoc['jenis_upload'], $sDoc['external_link'],
                    $sDoc['public_page_limit'], $sDoc['can_download_public'], $sDoc['status_review'],
                    $sDoc['catatan_review']
                ]);
                echo "   [OK] Dokumen contoh '{$sDoc['nama_dokumen']}' berhasil ditambahkan.\n";
            }
        }
    }

    echo "\n=== MIGRASI SELESAI DENGAN SUKSES! ===\n";

} catch (Exception $e) {
    echo "\n[ERROR] Migrasi gagal: " . $e->getMessage() . "\n";
    exit(1);
}
