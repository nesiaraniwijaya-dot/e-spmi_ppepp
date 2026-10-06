<?php
/**
 * Database Migration: Sub-Bidang Standar, Multi-File Documents, and User Avatar
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "=== RUNNING SPMI PPEPP ENHANCEMENT MIGRATIONS ===\n";

// 1. Add avatar to users table if not exists
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER role");
    echo "[OK] Added 'avatar' column to 'users' table.\n";
} catch (\Throwable $e) {
    echo "[SKIP] Column 'avatar' already exists or error: " . $e->getMessage() . "\n";
}

// 2. Add sub_bidang_id to ppepp_documents if not exists
try {
    $pdo->exec("ALTER TABLE ppepp_documents ADD COLUMN sub_bidang_id INT NULL AFTER bidang_id");
    echo "[OK] Added 'sub_bidang_id' column to 'ppepp_documents' table.\n";
} catch (\Throwable $e) {
    echo "[SKIP] Column 'sub_bidang_id' already exists or error: " . $e->getMessage() . "\n";
}

// 3. Create sub_bidang_standar table
$pdo->exec("
CREATE TABLE IF NOT EXISTS `sub_bidang_standar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bidang_id` INT NOT NULL,
    `kode_sub_bidang` VARCHAR(50) NULL,
    `nama_sub_bidang` VARCHAR(200) NOT NULL,
    `deskripsi` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sub_bidang_bidang` FOREIGN KEY (`bidang_id`) REFERENCES `bidang_standar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "[OK] Table 'sub_bidang_standar' ready.\n";

// 4. Create ppepp_document_files table for multi-file attachments
$pdo->exec("
CREATE TABLE IF NOT EXISTS `ppepp_document_files` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `document_id` INT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_size` VARCHAR(50) NULL,
    `file_extension` VARCHAR(20) NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_doc_files_doc` FOREIGN KEY (`document_id`) REFERENCES `ppepp_documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "[OK] Table 'ppepp_document_files' ready.\n";

// 5. Seed Sub-Bidang Standar Data (9 Kriteria Akreditasi LAM/BAN-PT & SN-Dikti)
$bidangRows = $pdo->query("SELECT id, kode_bidang, nama_bidang FROM bidang_standar")->fetchAll(PDO::FETCH_ASSOC);
$bMap = [];
foreach ($bidangRows as $br) {
    $bMap[$br['kode_bidang']] = $br['id'];
}

$subBidangSeed = [
    // 1. VMTS
    'VMTS' => [
        ['VMTS-01', 'Standar Rumusan Visi, Misi, Tujuan, dan Sasaran Strategis', 'Pedoman dan kriteria perumusan VMTS institusi dan prodi.'],
        ['VMTS-02', 'Standar Sosialisasi dan Pemahaman VMTS', 'Mekanisme diseminasi, sosialisasi berkala, dan evaluasi pemahaman pemangku kepentingan.'],
        ['VMTS-03', 'Standar Pencapaian dan Evaluasi Sasaran Strategis', 'Monitoring dan pengukuran ketercapaian milestone rencana strategis (Renstra).']
    ],
    // 2. TATA_PAMONG
    'TATA_PAMONG' => [
        ['TP-01', 'Standar Struktur Organisasi & Tata Kelola', 'Struktur organisasi, pembagian wewenang, dan tata kerja pimpinan unit/fakultas/prodi.'],
        ['TP-02', 'Standar Sistem Penjaminan Mutu Internal (SPMI)', 'Sistem PPEPP, audit mutu internal (AMI), dan laporan RTM universitas/prodi.'],
        ['TP-03', 'Standar Kepemimpinan & Akuntabilitas Tata Pamong', 'Prinsip kredibel, transparan, akuntabel, bertanggung jawab, dan adil.'],
        ['TP-04', 'Standar Kerjasama Nasional & Internasional', 'Perencanaan, pelaksanaan, evaluasi kemitraan Tri Dharma dengan industri dan institusi global.']
    ],
    // 3. MAHASISWA
    'MAHASISWA' => [
        ['MHS-01', 'Standar Penerimaan Mahasiswa Baru (PMB)', 'Sistem seleksi transparan, rasio pendaftar, dan kebijakan inklusif/beasiswa.'],
        ['MHS-02', 'Standar Layanan Bimbingan Akademik & Konseling', 'Pendampingan DPA, bimbingan psikologis, dan monitoring progres studi mahasiswa.'],
        ['MHS-03', 'Standar Pengembangan Minat, Bakat, & Organisasi Kemahasiswaan', 'Dukungan ormawa, kompetisi nasional/internasional, dan penalaran mahasiswa.'],
        ['MHS-04', 'Standar Layanan Karir, Kewirausahaan, & Alumni', 'Tracer study berkala, pusat karir (CDC), inkubasi bisnis, dan keterlibatan ikatan alumni.']
    ],
    // 4. SDM
    'SDM' => [
        ['SDM-01', 'Standar Kualifikasi & Kecukupan Dosen Tetap', 'Kualifikasi akademik S2/S3, jabatan fungsional Lektor/Guru Besar, dan rasio dosen:mahasiswa.'],
        ['SDM-02', 'Standar Beban Kinerja Dosen (BKD / EWMP)', 'Alokasi waktu dan ekuivalensi waktu mengajar penuh untuk pengajaran, riset, dan PkM.'],
        ['SDM-03', 'Standar Kualifikasi & Kompetensi Tenaga Kependidikan', 'Kualifikasi, sertifikasi keahlian laboran/pustakawan/staf administrasi.'],
        ['SDM-04', 'Standar Pengembangan Karir & Kesejahteraan SDM', 'Studi lanjut, pelatihan profesional, kenaikan jabatan, dan sistem remunerasi/reward.']
    ],
    // 5. KEUANGAN_SARPRAS
    'KEUANGAN_SARPRAS' => [
        ['KSP-01', 'Standar Perencanaan & Pengalokasian Anggaran', 'RKAT terstruktur, alokasi dana operasional, riset, PkM, dan investasi pengembangan prodi.'],
        ['KSP-02', 'Standar Kecukupan & Pemeliharaan Ruang Kuliah & Lab', 'Kelayakan ruang kelas, laboratorium spesifik prodi, rasio alat, dan jadwal pemeliharaan.'],
        ['KSP-03', 'Standar Sistem Informasi, Jaringan IT, & E-Learning', 'Kapasitas bandwidth, LMS terintegrasi, SIAKAD, dan keamanan data institusi.'],
        ['KSP-04', 'Standar Perpustakaan & Akses Sumber Belajar Digital', 'Koleksi buku teks, langganan e-journal internasional terakreditasi, dan ruang baca modern.']
    ],
    // 6. PENDIDIKAN
    'PENDIDIKAN' => [
        ['DIK-01', 'Standar Kompetensi Lulusan (CPL / Kurikulum OBE)', 'Rumusan CPL berstandar KKNI, SN-Dikti, dan akreditasi internasional (IABEE/ASIIN).'],
        ['DIK-02', 'Standar Isi Pembelajaran & Struktur Kurikulum', 'Kesesuaian mata kuliah, muatan MBKM, dan integrasi hasil riset dalam silabus/kurikulum.'],
        ['DIK-03', 'Standar Proses Pembelajaran & Dokumen RPS', 'Metode SCL (Case Method/Project-Based Learning), RPS lengkap, dan kehadiran perkuliahan.'],
        ['DIK-04', 'Standar Penilaian Pembelajaran & Asesmen OBE', 'Rubrik penilaian objektif, asesmen portofolio, dan pengukuran ketercapaian CPL tiap mahasiswa.'],
        ['DIK-05', 'Standar Pengelolaan Pembelajaran & Suasana Akademik', 'Kebebasan mimbar akademik, interaksi ilmiah, kuliah pakar, dan kepuasan mahasiswa.']
    ],
    // 7. PENELITIAN
    'PENELITIAN' => [
        ['LIT-01', 'Standar Rencana Induk Riset (Roadmap Penelitian)', 'Pohon riset keilmuan prodi yang selaras dengan RIRN dan keunggulan universitas.'],
        ['LIT-02', 'Standar Pelaksanaan & Pendanaan Penelitian', 'Hibah internal, hibah eksternal/Kemendikbudristek, dan kemitraan riset terapan.'],
        ['LIT-03', 'Standar Keterlibatan Mahasiswa dalam Penelitian', 'Integrasi tugas akhir/skripsi dan asisten peneliti mahasiswa dalam proyek riset dosen.'],
        ['LIT-04', 'Standar Publikasi Ilmiah & Diseminasi Riset', 'Publikasi jurnal bereputasi (SINTA/Scopus), prosiding seminar internasional, dan sitasi dosen.']
    ],
    // 8. PkM
    'PkM' => [
        ['PKM-01', 'Standar Rencana Induk & Roadmap Pengabdian (PkM)', 'Arah pengabdian masyarakat berbasis keilmuan prodi dan pemecahan masalah komunitas/mitra.'],
        ['PKM-02', 'Standar Pelaksanaan & Pendanaan PkM', 'Alokasi dana PkM, kerjasama mitra binaan, dan keberlanjutan program pengabdian.'],
        ['PKM-03', 'Standar Keterlibatan Mahasiswa dalam Kegiatan PkM', 'KKN Tematik, pemberdayaan desa, dan proyek kemanusiaan bersama mahasiswa.'],
        ['PKM-04', 'Standar Penerapan Teknologi & Hilirisasi Hasil PkM', 'Penerapan tepat guna hasil riset dosen untuk peningkatan ekonomi/kesejahteraan masyarakat.']
    ],
    // 9. LUARAN_TRIDHARMA
    'LUARAN_TRIDHARMA' => [
        ['OUT-01', 'Standar Capaian Pembelajaran & Prestasi Mahasiswa', 'Rata-rata IPK lulusan, ketepatan waktu kelulusan, dan juara kompetisi akademik/seni/olahraga.'],
        ['OUT-02', 'Standar Masa Tunggu Kerja & Relevansi (Tracer Study)', 'Persentase lulusan bekerja dalam waktu < 6 bulan dan kesesuaian bidang pekerjaan (high-income).'],
        ['OUT-03', 'Standar Kepuasan Pengguna Lulusan (Stakeholder)', 'Tingkat kepuasan atasan/industri terhadap integritas, keahlian teknis, dan komunikasi alumni.'],
        ['OUT-04', 'Standar Luaran Paten, HKI, & Karya Monumental', 'Pendaftaran Hak Cipta, Paten sederhana/biasa, buku ajar ber-ISBN, dan produk terkomersialisasi.']
    ]
];

$stmtInsertSub = $pdo->prepare("INSERT INTO sub_bidang_standar (bidang_id, kode_sub_bidang, nama_sub_bidang, deskripsi, is_active) 
    VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE nama_sub_bidang=VALUES(nama_sub_bidang), deskripsi=VALUES(deskripsi)");

$subCount = 0;
foreach ($subBidangSeed as $bidangCode => $subItems) {
    if (!isset($bMap[$bidangCode])) continue;
    $bId = $bMap[$bidangCode];
    foreach ($subItems as $sub) {
        $stmtInsertSub->execute([$bId, $sub[0], $sub[1], $sub[2]]);
        $subCount++;
    }
}
echo "[OK] Seeded {$subCount} Sub-Bidang Standar Mutu into database.\n";

// Populate existing single-file documents into ppepp_document_files table if not already populated
$stmtDocs = $pdo->query("SELECT id, nama_dokumen, file_path, file_size, file_extension FROM ppepp_documents WHERE file_path IS NOT NULL AND file_path != ''");
$allFileDocs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);
$stmtInsertFile = $pdo->prepare("INSERT IGNORE INTO ppepp_document_files (document_id, file_name, file_path, file_size, file_extension, sort_order) VALUES (?, ?, ?, ?, ?, 0)");
$populatedFiles = 0;
foreach ($allFileDocs as $d) {
    $chk = $pdo->prepare("SELECT COUNT(*) FROM ppepp_document_files WHERE document_id = ? AND file_path = ?");
    $chk->execute([$d['id'], $d['file_path']]);
    if ($chk->fetchColumn() == 0) {
        $stmtInsertFile->execute([
            $d['id'],
            $d['nama_dokumen'] . ' (Dokumen Utama)',
            $d['file_path'],
            $d['file_size'] ?: '1.2 MB',
            $d['file_extension'] ?: 'pdf'
        ]);
        $populatedFiles++;
    }
}
echo "[OK] Populated {$populatedFiles} existing files into ppepp_document_files table.\n";
echo "=== MIGRATION COMPLETED SUCCESSFULLY ===\n";
