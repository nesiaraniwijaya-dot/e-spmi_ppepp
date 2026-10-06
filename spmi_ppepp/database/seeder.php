<?php
/**
 * Database Seeder for SPMI PPEPP UNIKA Soegijapranata
 */
require_once __DIR__ . '/../config/database.php';

echo "=== INITIALIZING SPMI PPEPP DATABASE & SEEDER ===\n";

$db = Database::getInstance();
$pdo = $db->getConnection();

// Execute Schema
$schemaSql = file_get_contents(__DIR__ . '/ppepp.sql');
$pdo->exec($schemaSql);
echo "[OK] Schema (ppepp.sql) executed successfully.\n";

// Execute Migrations
require_once __DIR__ . '/migration_sub_bidang_and_files.php';
require_once __DIR__ . '/migration_multi_sub_standar.php';

// 1. Seed Bidang Standar (9 Kriteria Akreditasi LAM/BAN-PT)
$bidangList = [
    ['VMTS', 'Visi, Misi, Tujuan, dan Strategi', 'Standar perencanaan strategis dan arah mutu institusi/program studi.'],
    ['TATA_PAMONG', 'Tata Pamong, Tata Kelola, dan Kerjasama', 'Standar sistem kepemimpinan, penjaminan mutu, dan jejaring kemitraan.'],
    ['MAHASISWA', 'Mahasiswa dan Layanan Kemahasiswaan', 'Standar rekrutmen, prestasi, bimbingan karir, dan kesejahteraan mahasiswa.'],
    ['SDM', 'Sumber Daya Manusia (Dosen & Tendik)', 'Standar kualifikasi, kompetensi, beban kerja, dan pengembangan dosen/tendik.'],
    ['KEUANGAN_SARPRAS', 'Keuangan, Sarana, dan Prasarana', 'Standar kecukupan dana, laboratorium, perpustakaan, dan fasilitas penunjang.'],
    ['PENDIDIKAN', 'Pendidikan dan Kurikulum', 'Standar kurikulum OBE, proses pembelajaran, asesmen, dan suasana akademik.'],
    ['PENELITIAN', 'Penelitian dan Publikasi', 'Standar roadmap riset dosen, keterlibatan mahasiswa, dan publikasi ilmiah.'],
    ['PkM', 'Pengabdian kepada Masyarakat (PkM)', 'Standar kegiatan pengabdian masyarakat berorientasi solutif dan berkelanjutan.'],
    ['LUARAN_TRIDHARMA', 'Luaran dan Capaian Tridharma', 'Standar IPK lulusan, waktu tunggu kerja, kepuasan pengguna, dan HKI/paten.']
];

$stmtBidang = $pdo->prepare("INSERT INTO bidang_standar (kode_bidang, nama_bidang, deskripsi, is_active) 
    VALUES (?, ?, ?, 1) ON DUPLICATE KEY UPDATE nama_bidang=VALUES(nama_bidang), deskripsi=VALUES(deskripsi)");

foreach ($bidangList as $b) {
    $stmtBidang->execute([$b[0], $b[1], $b[2]]);
}
echo "[OK] Bidang Standar seeded (9 Kriteria).\n";

// 2. Seed Fakultas UNIKA Soegijapranata
$fakultasList = [
    ['FAD', 'Fakultas Arsitektur dan Desain', 'Mengembangkan pendidikan arsitektur dan desain visual berbasis humaniora dan teknologi.'],
    ['FIK', 'Fakultas Ilmu Komputer', 'Pusat keunggulan ilmu komputer, rekayasa perangkat lunak, AI, dan sistem informasi terdepan.'],
    ['FEB', 'Fakultas Ekonomi dan Bisnis', 'Mendidik calon profesional bisnis, akuntan, dan manajer dengan etika dan daya saing global.'],
    ['FBS', 'Fakultas Bahasa dan Seni', 'Fokus pada literasi bahasa, komunikasi antar budaya, dan ekspresi seni visual.'],
    ['FT', 'Fakultas Teknik', 'Pengembangan keahlian teknik sipil, elektro, dan robotika berbasis keberlanjutan lingkungan.'],
    ['FPSI', 'Fakultas Psikologi', 'Pendidikan psikologi komprehensif untuk penguatan kesehatan mental dan organisasi.'],
    ['FHK', 'Fakultas Hukum dan Komunikasi', 'Menghasilkan praktisi hukum berintegritas dan komunikator strategis era digital.'],
    ['FTP', 'Fakultas Teknologi Pertanian', 'Inovasi ilmu pangan, nutrisi berkelanjutan, dan teknologi hasil pertanian modern.'],
    ['FK', 'Fakultas Kedokteran', 'Pendidikan dokter profesional yang menjunjung tinggi empati, etika moral, dan keilmuan medis.'],
    ['PASCA', 'Program Pascasarjana', 'Program magister dan doktoral interdisipliner untuk penguatan kepemimpinan dan riset terapan.']
];

$stmtFak = $pdo->prepare("INSERT INTO fakultas (kode_fakultas, nama_fakultas, deskripsi) 
    VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nama_fakultas=VALUES(nama_fakultas), deskripsi=VALUES(deskripsi)");

foreach ($fakultasList as $f) {
    $stmtFak->execute([$f[0], $f[1], $f[2]]);
}
echo "[OK] Fakultas UNIKA Soegijapranata seeded (10 Fakultas).\n";

// Get Fakultas IDs
$fakMap = [];
$resFak = $pdo->query("SELECT id, kode_fakultas FROM fakultas")->fetchAll(PDO::FETCH_ASSOC);
foreach ($resFak as $rf) {
    $fakMap[$rf['kode_fakultas']] = $rf['id'];
}

// 3. Seed Program Studi
$prodiList = [
    // FIK
    [$fakMap['FIK'], '55201', 'Teknik Informatika', 'S1', 'Dr. Bernardinus Harnadi, M.T.', '0612057301'],
    [$fakMap['FIK'], '57201', 'Sistem Informasi', 'S1', 'Agustinus Eko Nugroho, S.Kom., M.Cs.', '0624088002'],
    [$fakMap['FIK'], '55202', 'Teknologi Informasi (Game Tech & AI)', 'S1', 'Erdhi Widyarto Nugroho, S.T., M.T.', '0618037901'],
    // FEB
    [$fakMap['FEB'], '62201', 'Akuntansi', 'S1', 'Dr. Christian Herdinata, S.E., M.M., CFP', '0601077701'],
    [$fakMap['FEB'], '61201', 'Manajemen', 'S1', 'Dr. Agnes Advensia C., S.E., M.Si., Akt.', '0615097402'],
    [$fakMap['FEB'], '62101', 'Perpajakan', 'D3', 'Yohanes Suhari, S.E., M.Si.', '0603046801'],
    // FAD
    [$fakMap['FAD'], '22201', 'Arsitektur', 'S1', 'Ir. Maria E. Priambodo, M.T.', '0608026501'],
    [$fakMap['FAD'], '90241', 'Desain Komunikasi Visual', 'S1', 'Peter Ardiansyah, S.Sn., M.Ds.', '0621098401'],
    // FT
    [$fakMap['FT'], '20201', 'Teknik Sipil', 'S1', 'Daniel Hartanto, S.T., M.Eng.', '0614058101'],
    [$fakMap['FT'], '20202', 'Teknik Elektro', 'S1', 'Ferdian Ronny, S.T., M.Sc.', '0609118302'],
    // FPSI
    [$fakMap['FPSI'], '73201', 'Psikologi', 'S1', 'Dr. M. Sih Setija Utami, M.Kes.', '0622046101'],
    // FHK
    [$fakMap['FHK'], '74201', 'Ilmu Hukum', 'S1', 'Marcella Elwina S., S.H., C.N., M.Hum.', '0629036901'],
    [$fakMap['FHK'], '70201', 'Ilmu Komunikasi', 'S1', 'Rika Saraswati, S.H., CN., M.Hum., Ph.D.', '0607067201'],
    // FTP
    [$fakMap['FTP'], '41201', 'Teknologi Pangan', 'S1', 'Dr. Ir. Lindayani, M.P.', '0628106401'],
    [$fakMap['FTP'], '41202', 'Nutrisi dan Teknologi Kuliner', 'S1', 'Novita Ika Putri, S.TP., M.Sc.', '0619078801'],
    // FK
    [$fakMap['FK'], '11201', 'Kedokteran', 'S1', 'dr. Jonsinar Silalahi, M.Si.Med., Sp.B.', '0611046701'],
    [$fakMap['FK'], '11901', 'Profesi Dokter', 'Profesi', 'dr. Indah Saraswati, M.Biomed.', '0617058202'],
    // FBS
    [$fakMap['FBS'], '79202', 'Sastra Inggris', 'S1', 'Angelika Riyandari, Ph.D.', '0605017001'],
    // Pascasarjana
    [$fakMap['PASCA'], '61101', 'Magister Manajemen (MM)', 'S2', 'Dr. Ferdinandus Hindiarto, S.Psi., M.Si.', '0618077501'],
    [$fakMap['PASCA'], '73101', 'Magister Psikologi', 'S2', 'Dr. Kristiana Haryanti, M.Si.', '0604116301']
];

$stmtProdi = $pdo->prepare("INSERT INTO prodis (fakultas_id, kode_prodi, nama_prodi, jenjang, nama_kaprodi, nidn_kaprodi) 
    VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_prodi=VALUES(nama_prodi), nama_kaprodi=VALUES(nama_kaprodi), nidn_kaprodi=VALUES(nidn_kaprodi)");

foreach ($prodiList as $p) {
    $stmtProdi->execute([$p[0], $p[1], $p[2], $p[3], $p[4], $p[5]]);
}
echo "[OK] Program Studi seeded (" . count($prodiList) . " Prodi).\n";

// Get Prodi Map
$prodiMap = [];
$resProdi = $pdo->query("SELECT id, kode_prodi, nama_prodi FROM prodis")->fetchAll(PDO::FETCH_ASSOC);
foreach ($resProdi as $rp) {
    $prodiMap[$rp['kode_prodi']] = $rp['id'];
}

// 4. Seed Users
$defaultPass = password_hash('password123', PASSWORD_BCRYPT);

$usersList = [
    // LPM
    [
        'name' => 'Dr. B. Danang Setianto, S.H., LL.M. (Ketua Pusat Penjaminan Mutu)',
        'email' => 'pusat.mutu@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'kepala_pusat_mutu',
        'fakultas_id' => null,
        'prodi_id' => null
    ],
    [
        'name' => 'Prof. Dr. Ridwan Sanjaya, S.E., S.Kom., MS.IEC. (Kepala LPM)',
        'email' => 'kepala.lpm@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'kepala_lpm',
        'fakultas_id' => null,
        'prodi_id' => null
    ],
    [
        'name' => 'Admin Lembaga Penjaminan Mutu (LPM)',
        'email' => 'admin.lpm@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'super_admin',
        'fakultas_id' => null,
        'prodi_id' => null
    ],

    // DEKANAT & GPM FAKULTAS (Tanpa Admin Fakultas)
    [
        'name' => 'Dr. Bernardinus Harnadi, M.T. (Dekan FIKOM)',
        'email' => 'dekan.fikom@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'dekan',
        'fakultas_id' => $fakMap['FIK'] ?? 2,
        'prodi_id' => null
    ],
    [
        'name' => 'Erdhi Widyarto Nugroho, S.T., M.T. (Wakil Dekan FIK)',
        'email' => 'wadek.fikom@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'wadek',
        'fakultas_id' => $fakMap['FIK'] ?? 2,
        'prodi_id' => null
    ],
    [
        'name' => 'GPM Fakultas Ilmu Komputer',
        'email' => 'gpm.fikom@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'gpm',
        'fakultas_id' => $fakMap['FIK'] ?? 2,
        'prodi_id' => null
    ],
    [
        'name' => 'Dr. St. Vena Purnamasari, S.E., M.Si. (Dekan FEB)',
        'email' => 'dekan.feb@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'dekan',
        'fakultas_id' => $fakMap['FEB'] ?? 3,
        'prodi_id' => null
    ],
    [
        'name' => 'Dr. Agnes Advensia C., S.E., M.Si., Akt. (Wakil Dekan FEB)',
        'email' => 'wadek.feb@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'wadek',
        'fakultas_id' => $fakMap['FEB'] ?? 3,
        'prodi_id' => null
    ],

    // PIMPINAN PRODI: KAPRODI & SEKPRODI (Tanpa Admin Prodi)
    [
        'name' => 'Prof. Dr. Ir. Abdi, M.T., IPU (Kaprodi Teknik Informatika)',
        'email' => 'kaprodi.ti@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'kaprodi',
        'fakultas_id' => $fakMap['FIK'] ?? 2,
        'prodi_id' => $prodiMap['55201'] ?? 1
    ],
    [
        'name' => 'Rosalia Hadi, S.Kom., M.T. (Sekprodi Teknik Informatika)',
        'email' => 'sekprodi.ti@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'sekprodi',
        'fakultas_id' => $fakMap['FIK'] ?? 2,
        'prodi_id' => $prodiMap['55201'] ?? 1
    ],
    [
        'name' => 'Agustinus Eko Nugroho, S.Kom., M.Cs. (Kaprodi Sistem Informasi)',
        'email' => 'kaprodi.si@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'kaprodi',
        'fakultas_id' => $fakMap['FIK'] ?? 2,
        'prodi_id' => $prodiMap['57201'] ?? 2
    ],
    [
        'name' => 'Kristophorus Hadiono, Ph.D. (Sekprodi Sistem Informasi)',
        'email' => 'sekprodi.si@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'sekprodi',
        'fakultas_id' => $fakMap['FIK'] ?? 2,
        'prodi_id' => $prodiMap['57201'] ?? 2
    ],
    [
        'name' => 'Dr. Christian Herdinata, S.E., M.M., CFP (Kaprodi Akuntansi)',
        'email' => 'kaprodi.akuntansi@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'kaprodi',
        'fakultas_id' => $fakMap['FEB'] ?? 3,
        'prodi_id' => $prodiMap['62201'] ?? 4
    ],
    [
        'name' => 'Y. Denny Krisnamurti, S.E., M.Si. (Sekprodi Akuntansi)',
        'email' => 'sekprodi.akuntansi@unika.ac.id',
        'password' => $defaultPass,
        'role' => 'sekprodi',
        'fakultas_id' => $fakMap['FEB'] ?? 3,
        'prodi_id' => $prodiMap['62201'] ?? 4
    ]
];

$stmtUser = $pdo->prepare("INSERT INTO users (name, email, password, role, fakultas_id, prodi_id, is_active) 
    VALUES (?, ?, ?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE name=VALUES(name), role=VALUES(role), fakultas_id=VALUES(fakultas_id), prodi_id=VALUES(prodi_id)");

foreach ($usersList as $u) {
    $stmtUser->execute([$u['name'], $u['email'], $u['password'], $u['role'], $u['fakultas_id'], $u['prodi_id']]);
}
echo "[OK] Default Users & RBAC seeded (LPM, Dekanat, dan Kaprodi/Sekprodi).\n";

// Get Admin Users
$userMap = [];
$resUsers = $pdo->query("SELECT id, email FROM users")->fetchAll(PDO::FETCH_ASSOC);
foreach ($resUsers as $ru) {
    $userMap[$ru['email']] = $ru['id'];
}

// Get Bidang Map
$bidangMap = [];
$resB = $pdo->query("SELECT id, kode_bidang FROM bidang_standar")->fetchAll(PDO::FETCH_ASSOC);
foreach ($resB as $rb) {
    $bidangMap[$rb['kode_bidang']] = $rb['id'];
}

// 5. Seed Dokumen PPEPP Sampel (Realistis untuk Teknik Informatika & Akuntansi)
$tiId = $prodiMap['55201'] ?? 1;
$adminTiId = $userMap['admin.ti@unika.ac.id'] ?? 1;

$docsData = [
    // Penetapan (P)
    [
        'prodi_id' => $tiId,
        'user_id' => $adminTiId,
        'bidang_id' => $bidangMap['PENDIDIKAN'] ?? null,
        'nama_dokumen' => 'Standar Kompetensi Lulusan & Capaian Pembelajaran Lulusan (CPL) Kurikulum OBE 2024',
        'nomor_dokumen' => 'SK-LPM/SCU/TI/2024/001',
        'siklus' => 'penetapan',
        'tahun_akademik' => '2024/2025',
        'tanggal_berlaku_mulai' => '2024-01-15',
        'tanggal_berlaku_selesai' => '2028-01-15',
        'jenis_upload' => 'link',
        'file_path' => null,
        'external_link' => 'https://drive.google.com/drive/folders/unika-scu-spmi-cpl-ti-2024',
        'deskripsi' => 'Dokumen penetapan rumusan CPL prodi TI berstandar IABEE dan KKNI Level 6.'
    ],
    [
        'prodi_id' => $tiId,
        'user_id' => $adminTiId,
        'bidang_id' => $bidangMap['SDM'] ?? null,
        'nama_dokumen' => 'Standar Beban Kinerja Dosen dan Rasio Dosen-Mahasiswa Program Studi TI',
        'nomor_dokumen' => 'STD-SDM/SCU/2024/014',
        'siklus' => 'penetapan',
        'tahun_akademik' => '2024/2025',
        'tanggal_berlaku_mulai' => '2024-02-01',
        'tanggal_berlaku_selesai' => '2027-02-01',
        'jenis_upload' => 'link',
        'file_path' => null,
        'external_link' => 'https://drive.google.com/file/d/1A2bC3dE4fGhIjKlMnOpQrStUvWxYz/view',
        'deskripsi' => 'Pedoman pemetaan keahlian dosen, penelitian wajib, dan batas maksimal pembimbingan skripsi.'
    ],
    // Pelaksanaan (P)
    [
        'prodi_id' => $tiId,
        'user_id' => $adminTiId,
        'bidang_id' => $bidangMap['PENDIDIKAN'] ?? null,
        'nama_dokumen' => 'Laporan Pelaksanaan Pembelajaran & Monitoring Perkuliahan Semester Gasal 2024/2025',
        'nomor_dokumen' => 'LAP-PBM/TI-SCU/2024/GSL',
        'siklus' => 'pelaksanaan',
        'tahun_akademik' => '2024/2025',
        'tanggal_berlaku_mulai' => '2024-09-01',
        'tanggal_berlaku_selesai' => '2025-02-28',
        'jenis_upload' => 'link',
        'file_path' => null,
        'external_link' => 'https://drive.google.com/file/d/1SampleDriveLinkPBM2024/view',
        'deskripsi' => 'Rekapitulasi ketercapaian 16 pertemuan kuliah, presensi dosen/mahasiswa, dan portofolio RPS.'
    ],
    [
        'prodi_id' => $tiId,
        'user_id' => $adminTiId,
        'bidang_id' => $bidangMap['PENELITIAN'] ?? null,
        'nama_dokumen' => 'Laporan Hibah Riset Kolaborasi Dosen-Mahasiswa Bidang Artificial Intelligence',
        'nomor_dokumen' => 'RIS-LPPM/TI/2024/11',
        'siklus' => 'pelaksanaan',
        'tahun_akademik' => '2024/2025',
        'tanggal_berlaku_mulai' => '2024-04-01',
        'tanggal_berlaku_selesai' => '2024-12-31',
        'jenis_upload' => 'link',
        'file_path' => null,
        'external_link' => 'https://drive.google.com/drive/folders/riset-ai-scu-2024',
        'deskripsi' => 'Realisasi 6 proyek riset terapan Machine Learning bermitra dengan industri di Jawa Tengah.'
    ],
    // Evaluasi (E)
    [
        'prodi_id' => $tiId,
        'user_id' => $adminTiId,
        'bidang_id' => $bidangMap['TATA_PAMONG'] ?? null,
        'nama_dokumen' => 'Laporan Hasil Audit Mutu Internal (AMI) Siklus XIX Program Studi Teknik Informatika',
        'nomor_dokumen' => 'AMI-LPM/SCU/XIX/2024/TI',
        'siklus' => 'evaluasi',
        'tahun_akademik' => '2024/2025',
        'tanggal_berlaku_mulai' => '2024-10-10',
        'tanggal_berlaku_selesai' => '2025-10-10',
        'jenis_upload' => 'link',
        'file_path' => null,
        'external_link' => 'https://drive.google.com/file/d/1ReportAMI2024SCU/view',
        'deskripsi' => 'Hasil temuan auditor internal mengenai kesesuaian sarana lab komputer dan ketepatan masa studi mahasiswa.'
    ],
    [
        'prodi_id' => $tiId,
        'user_id' => $adminTiId,
        'bidang_id' => $bidangMap['LUARAN_TRIDHARMA'] ?? null,
        'nama_dokumen' => 'Hasil Survei Kepuasan Pengguna Lulusan (Tracer Study) Tahun 2024',
        'nomor_dokumen' => 'TS-TI/SCU/2024/RES',
        'siklus' => 'evaluasi',
        'tahun_akademik' => '2023/2024',
        'tanggal_berlaku_mulai' => '2024-08-01',
        'tanggal_berlaku_selesai' => '2025-08-01',
        'jenis_upload' => 'link',
        'file_path' => null,
        'external_link' => 'https://drive.google.com/file/d/1TracerStudyReport2024/view',
        'deskripsi' => 'Evaluasi masa tunggu lulusan (rata-rata 1.8 bulan) dan tingkat kepuasan user sebesar 92.4%.'
    ],
    // Pengendalian (P)
    [
        'prodi_id' => $tiId,
        'user_id' => $adminTiId,
        'bidang_id' => $bidangMap['TATA_PAMONG'] ?? null,
        'nama_dokumen' => 'Daftar Tindakan Koreksi dan Pencegahan (PTKP) Temuan Audit AMI Siklus XIX',
        'nomor_dokumen' => 'PTKP-LPM/TI/2024/09',
        'siklus' => 'pengendalian',
        'tahun_akademik' => '2024/2025',
        'tanggal_berlaku_mulai' => '2024-11-01',
        'tanggal_berlaku_selesai' => '2025-05-01',
        'jenis_upload' => 'link',
        'file_path' => null,
        'external_link' => 'https://drive.google.com/file/d/1PTKP-AMI-XIX-TI/view',
        'deskripsi' => 'Matriks tindak lanjut perbaikan lisensi software lab dan penjadwalan bimbingan proposal terpadu.'
    ],
    // Peningkatan (P)
    [
        'prodi_id' => $tiId,
        'user_id' => $adminTiId,
        'bidang_id' => $bidangMap['PENDIDIKAN'] ?? null,
        'nama_dokumen' => 'Rencana Peningkatan Mutu (Upgrading) dan Penyesuaian Standar Pembelajaran AI Terintegrasi',
        'nomor_dokumen' => 'RPM-TI/SCU/2025/01',
        'siklus' => 'peningkatan',
        'tahun_akademik' => '2025/2026',
        'tanggal_berlaku_mulai' => '2025-01-10',
        'tanggal_berlaku_selesai' => '2029-01-10',
        'jenis_upload' => 'link',
        'file_path' => null,
        'external_link' => 'https://drive.google.com/file/d/1RPM-Peningkatan-TI-2025/view',
        'deskripsi' => 'Standar baru melampaui SN-Dikti dengan mewajibkan sertifikasi kompetensi internasional bagi mahasiswa semester 7.'
    ]
];

// Check if docs exist
$existingCount = $pdo->query("SELECT COUNT(*) FROM ppepp_documents")->fetchColumn();
if ($existingCount == 0) {
    $stmtDoc = $pdo->prepare("INSERT INTO ppepp_documents 
        (prodi_id, user_id, bidang_id, nama_dokumen, nomor_dokumen, siklus, tahun_akademik, tanggal_berlaku_mulai, tanggal_berlaku_selesai, jenis_upload, external_link, deskripsi)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($docsData as $d) {
        $stmtDoc->execute([
            $d['prodi_id'],
            $d['user_id'],
            $d['bidang_id'],
            $d['nama_dokumen'],
            $d['nomor_dokumen'],
            $d['siklus'],
            $d['tahun_akademik'],
            $d['tanggal_berlaku_mulai'],
            $d['tanggal_berlaku_selesai'],
            $d['jenis_upload'],
            $d['external_link'],
            $d['deskripsi']
        ]);
    }
    echo "[OK] Sample PPEPP Documents seeded (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, Peningkatan).\n";
} else {
    echo "[INFO] PPEPP Documents already exist ($existingCount items).\n";
}

// 6. Seed Initial Audit Logs
$stmtAudit = $pdo->prepare("INSERT INTO audit_logs 
    (user_id, user_name, user_role, prodi_id, prodi_name, aksi, modul, target_id, target_name, new_values, ip_address, user_agent, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

$stmtAudit->execute([
    $adminTiId,
    'Admin Prodi Teknik Informatika',
    'admin_prodi',
    $tiId,
    'Teknik Informatika',
    'CREATE',
    'Dokumen PPEPP',
    '1',
    'Standar Kompetensi Lulusan & Capaian Pembelajaran Lulusan (CPL) Kurikulum OBE 2024',
    json_encode(['siklus' => 'penetapan', 'bidang' => 'Pendidikan dan Kurikulum', 'jenis' => 'link']),
    '127.0.0.1',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Seeder/1.0'
]);

$stmtAudit->execute([
    $adminTiId,
    'Admin Prodi Teknik Informatika',
    'admin_prodi',
    $tiId,
    'Teknik Informatika',
    'CREATE',
    'Dokumen PPEPP',
    '5',
    'Laporan Hasil Audit Mutu Internal (AMI) Siklus XIX Program Studi Teknik Informatika',
    json_encode(['siklus' => 'evaluasi', 'bidang' => 'Tata Pamong, Tata Kelola, dan Kerjasama', 'jenis' => 'link']),
    '127.0.0.1',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Seeder/1.0'
]);

echo "[OK] Initial Audit Trail logs seeded.\n";
echo "=== SEEDING COMPLETED SUCCESSFULLY ===\n";
