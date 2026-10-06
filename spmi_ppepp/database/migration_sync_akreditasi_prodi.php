<?php
/**
 * Migration & Synchronization Script:
 * 1. Create table akreditasi_prodi if not exists and populate from akreditasi_prodi.sql
 * 2. Update fakultas table (including Fakultas Ilmu dan Teknologi Lingkungan)
 * 3. Add accreditation columns to prodis table
 * 4. Sync prodis records to match the 27 accredited programs in akreditasi_prodi.sql
 * 5. Add 'pengguna' to users role enum
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "[START] Memulai sinkronisasi data fakultas, prodi, dan akreditasi...\n";

    // 1. Create akreditasi_prodi table
    $db->exec("
        CREATE TABLE IF NOT EXISTS `akreditasi_prodi` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `fakultas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `program_studi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `strata` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `peringkat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `lembaga` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
            `no_sk` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
            `file_sk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `masa_berlaku` date NOT NULL,
            `file_sertifikat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[OK] Tabel akreditasi_prodi siap.\n";

    // Import data from akreditasi_prodi.sql into akreditasi_prodi table
    $sqlContent = file_get_contents(__DIR__ . '/akreditasi_prodi.sql');
    if ($sqlContent) {
        // Extract INSERT statement
        if (preg_match('/INSERT INTO `akreditasi_prodi`.*?;/s', $sqlContent, $matches)) {
            $db->exec("TRUNCATE TABLE `akreditasi_prodi`");
            $db->exec($matches[0]);
            echo "[OK] 27 Data akreditasi berhasil diimpor ke tabel akreditasi_prodi.\n";
        }
    }

    // 2. Synchronize Fakultas
    // Update or insert 10 fakultas
    $fakultasList = [
        1  => ['FAD', 'Fakultas Arsitektur dan Desain', 'Dr. Ir. B. Tyas Susanti, M.T.', '0629086701', 'Peter Ardiansyah, S.Sn., M.Ds.', '0621098401', 'Mengembangkan pendidikan arsitektur dan desain visual berbasis humaniora dan teknologi.'],
        2  => ['FIKOM', 'Fakultas Ilmu Komputer', 'Dr. Bernardinus Harnadi, M.T.', '0625077401', 'Erdhi Widyarto Nugroho, S.T., M.T.', '0618037901', 'Pusat keunggulan ilmu komputer, rekayasa perangkat lunak, AI, dan sistem informasi terdepan.'],
        3  => ['FEB', 'Fakultas Ekonomi dan Bisnis', 'Dr. St. Vena Purnamasari, S.E., M.Si.', '0602057501', 'Dr. Agnes Advensia C., S.E., M.Si., Akt.', '0615097402', 'Mendidik calon profesional bisnis, akuntan, dan manajer dengan etika dan daya saing global.'],
        4  => ['FBS', 'Fakultas Bahasa dan Seni', NULL, NULL, NULL, NULL, 'Fokus pada literasi bahasa, komunikasi antar budaya, dan ekspresi seni visual.'],
        5  => ['FT', 'Fakultas Teknik', 'Dr. Ir. Djoko Suwarno, M.T.', '0610046501', 'Daniel Hartanto, S.T., M.Eng.', '0614058101', 'Pengembangan keahlian teknik sipil, elektro, dan robotika berbasis keberlanjutan lingkungan.'],
        6  => ['FPSI', 'Fakultas Psikologi', 'Dr. Margaretha Sih Setija Utami, M.Kes.', '0622046101', 'Kuriake Kharismawan, S.Psi., M.Si.', '0605078501', 'Pendidikan psikologi komprehensif untuk penguatan kesehatan mental dan organisasi.'],
        7  => ['FHK', 'Fakultas Hukum dan Komunikasi', NULL, NULL, NULL, NULL, 'Menghasilkan praktisi hukum berintegritas dan komunikator strategis era digital.'],
        8  => ['FTP', 'Fakultas Teknologi Pertanian', NULL, NULL, NULL, NULL, 'Inovasi ilmu pangan, nutrisi berkelanjutan, dan teknologi hasil pertanian modern.'],
        9  => ['FK', 'Fakultas Kedokteran', NULL, NULL, NULL, NULL, 'Pendidikan dokter profesional yang menjunjung tinggi empati, etika moral, dan keilmuan medis.'],
        10 => ['FITL', 'Fakultas Ilmu dan Teknologi Lingkungan', NULL, NULL, NULL, NULL, 'Pusat keunggulan rekayasa infrastruktur, tata kelola lingkungan hidup, dan perkotaan berkelanjutan.'],
    ];

    $stmtFak = $db->prepare("
        INSERT INTO fakultas (id, kode_fakultas, nama_fakultas, nama_dekan, nidn_dekan, nama_wadek, nidn_wadek, deskripsi)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            kode_fakultas = VALUES(kode_fakultas),
            nama_fakultas = VALUES(nama_fakultas),
            nama_dekan = COALESCE(VALUES(nama_dekan), nama_dekan),
            nidn_dekan = COALESCE(VALUES(nidn_dekan), nidn_dekan),
            nama_wadek = COALESCE(VALUES(nama_wadek), nama_wadek),
            nidn_wadek = COALESCE(VALUES(nidn_wadek), nidn_wadek),
            deskripsi = VALUES(deskripsi)
    ");

    foreach ($fakultasList as $fId => $fData) {
        $stmtFak->execute([$fId, $fData[0], $fData[1], $fData[2], $fData[3], $fData[4], $fData[5], $fData[6]]);
    }
    echo "[OK] 10 Fakultas berhasil disinkronkan.\n";

    // 3. Add accreditation columns to prodis if not exists
    $cols = $db->query("SHOW COLUMNS FROM prodis")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('status_akreditasi', $cols)) {
        $db->exec("ALTER TABLE prodis ADD COLUMN status_akreditasi VARCHAR(50) NULL AFTER jenjang");
        $db->exec("ALTER TABLE prodis ADD COLUMN lembaga_akreditasi VARCHAR(100) NULL AFTER status_akreditasi");
        $db->exec("ALTER TABLE prodis ADD COLUMN no_sk_akreditasi VARCHAR(150) NULL AFTER lembaga_akreditasi");
        $db->exec("ALTER TABLE prodis ADD COLUMN masa_berlaku_akreditasi DATE NULL AFTER no_sk_akreditasi");
        $db->exec("ALTER TABLE prodis ADD COLUMN file_sk_akreditasi VARCHAR(255) NULL AFTER masa_berlaku_akreditasi");
        $db->exec("ALTER TABLE prodis ADD COLUMN file_sertifikat_akreditasi VARCHAR(255) NULL AFTER file_sk_akreditasi");
        echo "[OK] Kolom akreditasi ditambahkan ke tabel prodis.\n";
    }

    // 4. Update role ENUM on users table to include 'pengguna'
    $db->exec("
        ALTER TABLE `users` 
        MODIFY COLUMN `role` ENUM('super_admin','admin_lpm','kepala_pusat_mutu','kepala_lpm','dekan','wadek','gpm','kaprodi','sekprodi','pengguna') NOT NULL
    ");
    echo "[OK] Role 'pengguna' ditambahkan ke tabel users.\n";

    // 5. Synchronize all 27 Prodis according to akreditasi_prodi.sql
    // Read from akreditasi_prodi table
    $akrRows = $db->query("SELECT * FROM akreditasi_prodi ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Map fakultas string to fakultas_id
    $fakNameToId = [
        'Arsitektur dan Desain' => 1,
        'Ilmu Komputer' => 2,
        'Ekonomi dan Bisnis' => 3,
        'Bahasa dan Seni' => 4,
        'Teknik' => 5,
        'Psikologi' => 6,
        'Hukum dan Komunikasi' => 7,
        'Teknologi Pertanian' => 8,
        'Kedokteran' => 9,
        'Ilmu dan Teknologi Lingkungan' => 10
    ];

    // Standard PDDIKTI / default kode_prodi mapping
    $prodiCodes = [
        // FAD
        '1_Desain Komunikasi Visual_S1' => '90241',
        '1_Arsitektur_S1'               => '22201',
        '1_Arsitektur_S2'               => '22101',
        '1_Arsitektur_S3'               => '22001',
        // FBS
        '4_Sastra Inggris_S1'           => '79202',
        // FEB
        '3_Akuntansi_S1'                => '62201',
        '3_Akuntansi_S2'                => '62102',
        '3_Manajemen_S1'                => '61201',
        '3_Manajemen_S2'                => '61101',
        // FHK
        '7_Ilmu Komunikasi_S1'          => '70201',
        '7_Ilmu Hukum_S1'               => '74201',
        '7_Ilmu Hukum_S2'               => '74101',
        // FITL
        '10_Rekayasa Infrastruktur dan Lingkungan_S1' => '25201',
        '10_Lingkungan dan Perkotaan_S2'              => '25101',
        '10_Ilmu Lingkungan_S3'                       => '25001',
        // FIKOM
        '2_Teknik Informatika_S1'       => '55201',
        '2_Sistem Informasi_S1'         => '57201',
        // FK
        '9_Kedokteran_S1'               => '11201',
        '9_Pendidikan Profesi Dokter_Profesi' => '11901',
        // FPSI
        '6_Psikologi_S1'                => '73201',
        '6_Psikologi_S2'                => '73101',
        '6_Pendidikan Profesi Psikolog_Profesi' => '73901',
        // FT
        '5_Teknik Elektro_S1'           => '20202',
        '5_Teknik Sipil_S1'             => '20201',
        '5_Profesi Insinyur_Profesi'    => '20901',
        // FTP
        '8_Teknologi Pangan_S1'         => '41201',
        '8_Teknologi Pangan_S2'         => '41101'
    ];

    // Known Kaprodi from existing DB
    $knownKaprodi = [
        '55201' => ['Prof. Dr. Ir. Abdi, M.T., IPU', '0612057302', 'Rosalia Hadi, S.Kom., M.T.', '0615088201'],
        '57201' => ['Agustinus Eko Nugroho, S.Kom., M.Cs.', '0624088002', 'Kristophorus Hadiono, Ph.D.', '0602037901'],
        '62201' => ['Dr. Christian Herdinata, S.E., M.M., CFP', '0601077701', 'Y. Denny Krisnamurti, S.E., M.Si.', '0611097601'],
        '61201' => ['Dr. Agnes Advensia C., S.E., M.Si., Akt.', '0615097402', null, null],
        '22201' => ['Ir. Maria E. Priambodo, M.T.', '0608026501', null, null],
        '90241' => ['Peter Ardiansyah, S.Sn., M.Ds.', '0621098401', null, null],
        '20201' => ['Daniel Hartanto, S.T., M.Eng.', '0614058101', null, null],
        '20202' => ['Ferdian Ronny, S.T., M.Sc.', '0609118302', null, null],
        '73201' => ['Dr. M. Sih Setija Utami, M.Kes.', '0622046101', null, null],
        '74201' => ['Marcella Elwina S., S.H., C.N., M.Hum.', '0629036901', null, null],
        '70201' => ['Rika Saraswati, S.H., CN., M.Hum., Ph.D.', '0607067201', null, null],
        '41201' => ['Dr. Ir. Lindayani, M.P.', '0628106401', null, null],
        '11201' => ['dr. Jonsinar Silalahi, M.Si.Med., Sp.B.', '0611046701', null, null],
        '11901' => ['dr. Indah Saraswati, M.Biomed.', '0617058202', null, null],
        '79202' => ['Angelika Riyandari, Ph.D.', '0605017001', null, null],
        '61101' => ['Dr. Ferdinandus Hindiarto, S.Psi., M.Si.', '0618077501', null, null],
        '73101' => ['Dr. Kristiana Haryanti, M.Si.', '0604116301', null, null],
    ];

    // Update or Insert each of the 27 prodis
    $stmtFind = $db->prepare("SELECT id FROM prodis WHERE kode_prodi = ? OR (fakultas_id = ? AND nama_prodi = ? AND jenjang = ?) LIMIT 1");
    $stmtUpdate = $db->prepare("
        UPDATE prodis SET 
            fakultas_id = ?,
            kode_prodi = ?,
            nama_prodi = ?,
            jenjang = ?,
            status_akreditasi = ?,
            lembaga_akreditasi = ?,
            no_sk_akreditasi = ?,
            masa_berlaku_akreditasi = ?,
            file_sk_akreditasi = ?,
            file_sertifikat_akreditasi = ?,
            nama_kaprodi = COALESCE(?, nama_kaprodi),
            nidn_kaprodi = COALESCE(?, nidn_kaprodi),
            nama_sekprodi = COALESCE(?, nama_sekprodi),
            nidn_sekprodi = COALESCE(?, nidn_sekprodi)
        WHERE id = ?
    ");
    $stmtInsertProdi = $db->prepare("
        INSERT INTO prodis (
            fakultas_id, kode_prodi, nama_prodi, jenjang, 
            status_akreditasi, lembaga_akreditasi, no_sk_akreditasi, masa_berlaku_akreditasi, file_sk_akreditasi, file_sertifikat_akreditasi,
            nama_kaprodi, nidn_kaprodi, nama_sekprodi, nidn_sekprodi
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $syncedProdis = 0;
    foreach ($akrRows as $row) {
        $fakultasName = trim($row['fakultas']);
        $prodiName    = trim($row['program_studi']);
        $strata       = trim($row['strata']);
        $peringkat    = trim($row['peringkat']);
        $lembaga      = trim($row['lembaga']);
        $noSk         = trim($row['no_sk']);
        $fileSk       = $row['file_sk'];
        $masaBerlaku  = $row['masa_berlaku'];
        $fileSertif   = $row['file_sertifikat'];

        $fId = $fakNameToId[$fakultasName] ?? null;
        if (!$fId) continue;

        $key = "{$fId}_{$prodiName}_{$strata}";
        $kodeProdi = $prodiCodes[$key] ?? ('P' . $row['id']);

        $kapInfo = $knownKaprodi[$kodeProdi] ?? [null, null, null, null];

        // Check if existing
        $stmtFind->execute([$kodeProdi, $fId, $prodiName, $strata]);
        $existingId = $stmtFind->fetchColumn();

        if ($existingId) {
            $stmtUpdate->execute([
                $fId, $kodeProdi, $prodiName, $strata,
                $peringkat, $lembaga, $noSk, $masaBerlaku, $fileSk, $fileSertif,
                $kapInfo[0], $kapInfo[1], $kapInfo[2], $kapInfo[3],
                $existingId
            ]);
        } else {
            $stmtInsertProdi->execute([
                $fId, $kodeProdi, $prodiName, $strata,
                $peringkat, $lembaga, $noSk, $masaBerlaku, $fileSk, $fileSertif,
                $kapInfo[0], $kapInfo[1], $kapInfo[2], $kapInfo[3]
            ]);
        }
        $syncedProdis++;
    }
    echo "[OK] {$syncedProdis} Program Studi berhasil disinkronkan sesuai data akreditasi_prodi.sql!\n";

    // 6. Pastikan ada user demo role 'pengguna' untuk pengujian akses dokumen penuh
    $checkUser = $db->query("SELECT id FROM users WHERE email = 'pengguna@unika.ac.id'")->fetchColumn();
    if (!$checkUser) {
        $hash = password_hash('password', PASSWORD_BCRYPT);
        $db->exec("
            INSERT INTO users (name, email, password, role, is_active, created_at)
            VALUES ('Civitas Akademika / Dosen', 'pengguna@unika.ac.id', '{$hash}', 'pengguna', 1, NOW())
        ");
        echo "[OK] Akun pengujian role 'pengguna' berhasil dibuat (Email: pengguna@unika.ac.id / Pass: password).\n";
    }

    echo "[SELESAI] Semua proses migrasi & sinkronisasi berhasil 100%.\n";

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
