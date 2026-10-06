<?php
/**
 * Migration & Seeder: Struktur Pengelola SPMI Tingkat LPM, Fakultas, dan Prodi
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

echo "=== MEMULAI MIGRASI STRUKTUR PENGELOLA SPMI PPEPP ===\n";

try {
    $pdo = Database::getInstance()->getConnection();

    // 1. Update kolom `role` pada tabel `users`
    echo "1. Memperbarui enum role pada tabel users...\n";
    $pdo->exec("
        ALTER TABLE `users` 
        MODIFY COLUMN `role` ENUM(
            'super_admin', 
            'admin_lpm', 
            'kepala_pusat_mutu', 
            'kepala_lpm', 
            'dekan', 
            'wadek', 
            'admin_fakultas', 
            'gpm', 
            'kaprodi', 
            'sekprodi', 
            'admin_prodi'
        ) NOT NULL
    ");
    echo "   [OK] Enum role tabel users berhasil diperbarui.\n";

    // 2. Tambah kolom nama_sekprodi & nidn_sekprodi pada tabel `prodis` jika belum ada
    echo "2. Memeriksa kolom sekprodi pada tabel prodis...\n";
    $colsProdi = $pdo->query("SHOW COLUMNS FROM prodis LIKE 'nama_sekprodi'")->fetchAll();
    if (empty($colsProdi)) {
        $pdo->exec("ALTER TABLE prodis ADD COLUMN nama_sekprodi VARCHAR(150) NULL AFTER nidn_kaprodi, ADD COLUMN nidn_sekprodi VARCHAR(50) NULL AFTER nama_sekprodi");
        echo "   [OK] Kolom nama_sekprodi & nidn_sekprodi berhasil ditambahkan pada tabel prodis.\n";
    } else {
        echo "   [SKIP] Kolom nama_sekprodi sudah ada.\n";
    }

    // Update data sekprodi awal untuk prodi sampel
    $pdo->exec("UPDATE prodis SET nama_sekprodi = 'Rosalia Hadi, S.Kom., M.T.', nidn_sekprodi = '0615088201' WHERE kode_prodi = '55201'");
    $pdo->exec("UPDATE prodis SET nama_sekprodi = 'Kristophorus Hadiono, Ph.D.', nidn_sekprodi = '0602037901' WHERE kode_prodi = '57201'");
    $pdo->exec("UPDATE prodis SET nama_sekprodi = 'Y. Denny Krisnamurti, S.E., M.Si.', nidn_sekprodi = '0611097601' WHERE kode_prodi = '62201'");

    // 3. Mapping ID Fakultas & Prodi
    $fakMap = [];
    $resFak = $pdo->query("SELECT id, kode_fakultas FROM fakultas")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($resFak as $f) {
        $fakMap[$f['kode_fakultas']] = (int)$f['id'];
    }

    $prodiMap = [];
    $resProdi = $pdo->query("SELECT id, kode_prodi FROM prodis")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($resProdi as $p) {
        $prodiMap[$p['kode_prodi']] = (int)$p['id'];
    }

    $fikId = $fakMap['FIKOM'] ?? ($fakMap['FIK'] ?? 2);
    $febId = $fakMap['FEB'] ?? 3;
    $tiId  = $prodiMap['55201'] ?? 1;
    $siId  = $prodiMap['57201'] ?? 2;
    $aknId = $prodiMap['62201'] ?? 4;

    // 4. Daftar Akun Baru & Standarisasi Password
    $defaultPass = password_hash('password123', PASSWORD_BCRYPT);

    $accounts = [
        // --- TINGKAT LPM (UNIVERSITAS) ---
        [
            'email' => 'pusat.mutu@unika.ac.id',
            'name' => 'Dr. B. Danang Setianto, S.H., LL.M. (Ketua Pusat Penjaminan Mutu)',
            'role' => 'kepala_pusat_mutu',
            'fakultas_id' => null,
            'prodi_id' => null
        ],
        [
            'email' => 'kepala.lpm@unika.ac.id',
            'name' => 'Prof. Dr. Ridwan Sanjaya, S.E., S.Kom., MS.IEC. (Kepala LPM)',
            'role' => 'kepala_lpm',
            'fakultas_id' => null,
            'prodi_id' => null
        ],
        [
            'email' => 'admin.lpm@unika.ac.id',
            'name' => 'Admin Lembaga Penjaminan Mutu (LPM)',
            'role' => 'super_admin',
            'fakultas_id' => null,
            'prodi_id' => null
        ],
        [
            'email' => 'lpm@unika.ac.id',
            'name' => 'Ir. I.M. Tri Hesti Mulyani, MT. (Pusat Penjamin Mutu)',
            'role' => 'kepala_pusat_mutu',
            'fakultas_id' => null,
            'prodi_id' => null
        ],

        // --- TINGKAT FAKULTAS (DEKANAT & GPM) ---
        [
            'email' => 'dekan.fikom@unika.ac.id',
            'name' => 'Dr. Bernardinus Harnadi, M.T. (Dekan FIKOM)',
            'role' => 'dekan',
            'fakultas_id' => $fikId,
            'prodi_id' => null
        ],
        [
            'email' => 'wadek.fikom@unika.ac.id',
            'name' => 'Erdhi Widyarto Nugroho, S.T., M.T. (Wakil Dekan FIKOM)',
            'role' => 'wadek',
            'fakultas_id' => $fikId,
            'prodi_id' => null
        ],
        [
            'email' => 'admin.fik@unika.ac.id',
            'name' => 'Admin Fakultas Ilmu Komputer (FIK)',
            'role' => 'admin_fakultas',
            'fakultas_id' => $fikId,
            'prodi_id' => null
        ],
        [
            'email' => 'gpm.fikom@unika.ac.id',
            'name' => 'GPM Fakultas Ilmu Komputer',
            'role' => 'gpm',
            'fakultas_id' => $fikId,
            'prodi_id' => null
        ],
        [
            'email' => 'dekan.feb@unika.ac.id',
            'name' => 'Dr. St. Vena Purnamasari, S.E., M.Si. (Dekan FEB)',
            'role' => 'dekan',
            'fakultas_id' => $febId,
            'prodi_id' => null
        ],
        [
            'email' => 'wadek.feb@unika.ac.id',
            'name' => 'Dr. Agnes Advensia C., S.E., M.Si., Akt. (Wakil Dekan FEB)',
            'role' => 'wadek',
            'fakultas_id' => $febId,
            'prodi_id' => null
        ],
        [
            'email' => 'admin.feb@unika.ac.id',
            'name' => 'Admin Fakultas Ekonomi & Bisnis (FEB)',
            'role' => 'admin_fakultas',
            'fakultas_id' => $febId,
            'prodi_id' => null
        ],

        // --- TINGKAT PROGRAM STUDI (KAPRODI, SEKPRODI, ADMIN PRODI) ---
        // Teknik Informatika
        [
            'email' => 'kaprodi.ti@unika.ac.id',
            'name' => 'Prof. Dr. Ir. Abdi, M.T., IPU (Kaprodi Teknik Informatika)',
            'role' => 'kaprodi',
            'fakultas_id' => $fikId,
            'prodi_id' => $tiId
        ],
        [
            'email' => 'sekprodi.ti@unika.ac.id',
            'name' => 'Rosalia Hadi, S.Kom., M.T. (Sekprodi Teknik Informatika)',
            'role' => 'sekprodi',
            'fakultas_id' => $fikId,
            'prodi_id' => $tiId
        ],
        [
            'email' => 'admin.ti@unika.ac.id',
            'name' => 'Admin Prodi Teknik Informatika',
            'role' => 'admin_prodi',
            'fakultas_id' => $fikId,
            'prodi_id' => $tiId
        ],

        // Sistem Informasi
        [
            'email' => 'kaprodi.si@unika.ac.id',
            'name' => 'Agustinus Eko Nugroho, S.Kom., M.Cs. (Kaprodi Sistem Informasi)',
            'role' => 'kaprodi',
            'fakultas_id' => $fikId,
            'prodi_id' => $siId
        ],
        [
            'email' => 'sekprodi.si@unika.ac.id',
            'name' => 'Kristophorus Hadiono, Ph.D. (Sekprodi Sistem Informasi)',
            'role' => 'sekprodi',
            'fakultas_id' => $fikId,
            'prodi_id' => $siId
        ],
        [
            'email' => 'admin.si@unika.ac.id',
            'name' => 'Admin Prodi Sistem Informasi',
            'role' => 'admin_prodi',
            'fakultas_id' => $fikId,
            'prodi_id' => $siId
        ],

        // Akuntansi
        [
            'email' => 'kaprodi.akuntansi@unika.ac.id',
            'name' => 'Dr. Christian Herdinata, S.E., M.M., CFP (Kaprodi Akuntansi)',
            'role' => 'kaprodi',
            'fakultas_id' => $febId,
            'prodi_id' => $aknId
        ],
        [
            'email' => 'sekprodi.akuntansi@unika.ac.id',
            'name' => 'Y. Denny Krisnamurti, S.E., M.Si. (Sekprodi Akuntansi)',
            'role' => 'sekprodi',
            'fakultas_id' => $febId,
            'prodi_id' => $aknId
        ],
        [
            'email' => 'admin.akuntansi@unika.ac.id',
            'name' => 'Admin Prodi Akuntansi',
            'role' => 'admin_prodi',
            'fakultas_id' => $febId,
            'prodi_id' => $aknId
        ]
    ];

    echo "3. Menyinkronkan akun-akun pengelola sistem...\n";
    $stmtUser = $pdo->prepare("
        INSERT INTO users (name, email, password, role, fakultas_id, prodi_id, is_active, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            password = VALUES(password),
            role = VALUES(role),
            fakultas_id = VALUES(fakultas_id),
            prodi_id = VALUES(prodi_id),
            is_active = 1
    ");

    foreach ($accounts as $acc) {
        $stmtUser->execute([
            $acc['name'],
            $acc['email'],
            $defaultPass,
            $acc['role'],
            $acc['fakultas_id'],
            $acc['prodi_id']
        ]);
        echo "   [OK] Akun {$acc['email']} ({$acc['role']}) tersimpan.\n";
    }

    // 5. Pastikan semua dokumen yang sudah pernah direview disahkan atas nama Ketua Pusat Mutu
    $kId = $pdo->query("SELECT id FROM users WHERE role = 'kepala_pusat_mutu' ORDER BY id ASC LIMIT 1")->fetchColumn();
    if ($kId) {
        $pdo->exec("UPDATE ppepp_documents SET reviewed_by = " . (int)$kId . " WHERE reviewed_by IS NOT NULL");
        echo "   [OK] Sinkronisasi pengesahan review lama ke Ketua Pusat Penjaminan Mutu (ID: {$kId}).\n";
    }

    echo "\n=== MIGRASI DAN SEEDING SELESAI DENGAN SUKSES ===\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
