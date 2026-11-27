<?php
/**
 * database/seed_dummy_custom_prodi_standard.php
 * Membuat data dummy:
 * 1. Bidang Tambahan Khusus Prodi Teknik Informatika (FIKOM)
 * 2. Standar Mutu Tambahan Khusus Prodi Teknik Informatika (FIKOM)
 * 3. Dokumen 1 (Menunggu Review GPM -> Siap direview GPM lalu LPM)
 * 4. Dokumen 2 (Sudah Lolos Review GPM & LPM -> Langsung tampil di Halaman Publik Prodi TI)
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getInstance()->getConnection();
    echo "=== MEMULAI SEEDING DATA DUMMY BIDANG & STANDAR KHUSUS PRODI ===\n\n";

    $fakultasId = 2; // FIKOM
    $prodiId = 1;    // S1 Teknik Informatika
    $prodiUserId = 52; // Nesia Rani (Admin/Kaprodi TI)
    $gpmUserId = 77;   // Erdhi (GPM FIKOM)
    $lpmUserId = 7;    // TU LPM / Super Admin

    // 1. Buat / Cek Bidang Tambahan Khusus Prodi
    $kodeBidang = 'BP-TI-AI';
    $namaBidang = 'Bidang Kecerdasan Buatan & Rekayasa Perangkat Lunak Terapan';
    $deskripsiBidang = 'Standar kriteria khusus Program Studi S1 Teknik Informatika (FIKOM) terkait kurikulum, etika kecerdasan buatan, dan keahlian rekayasa piranti lunak industri.';

    $stmtCheckB = $pdo->prepare("SELECT id FROM bidang_standar WHERE kode_bidang = ? OR nama_bidang = ? LIMIT 1");
    $stmtCheckB->execute([$kodeBidang, $namaBidang]);
    $bidangId = $stmtCheckB->fetchColumn();

    if (!$bidangId) {
        $stmtInsB = $pdo->prepare("
            INSERT INTO bidang_standar (kode_bidang, nama_bidang, deskripsi, scope, fakultas_id, prodi_id, created_by_role, is_active, created_at, updated_at)
            VALUES (?, ?, ?, 'prodi', ?, ?, 'gpm', 1, NOW(), NOW())
        ");
        $stmtInsB->execute([$kodeBidang, $namaBidang, $deskripsiBidang, $fakultasId, $prodiId]);
        $bidangId = (int)$pdo->lastInsertId();
        echo "[OK] Bidang Khusus Prodi berhasil dibuat: [ID {$bidangId}] {$kodeBidang} - {$namaBidang}\n";
    } else {
        $pdo->prepare("UPDATE bidang_standar SET scope = 'prodi', fakultas_id = ?, prodi_id = ?, is_active = 1 WHERE id = ?")
            ->execute([$fakultasId, $prodiId, $bidangId]);
        echo "[OK] Bidang Khusus Prodi sudah ada: [ID {$bidangId}] {$kodeBidang} - {$namaBidang}\n";
    }

    // 2. Buat / Cek Standar Mutu (Sub-Bidang) Khusus Prodi
    $kodeStandar = 'SP-TI-01';
    $namaStandar = 'Standar Kompetensi Algoritma, Ethical AI & Sertifikasi Industri';
    $deskripsiStandar = 'Ketentuan pemenuhan capaian pembelajaran coding algoritma lanjutan, integritas implementasi AI, serta portofolio sertifikasi profesi internasional mahasiswa Teknik Informatika.';

    $stmtCheckS = $pdo->prepare("SELECT id FROM sub_bidang_standar WHERE kode_sub_bidang = ? OR nama_sub_bidang = ? LIMIT 1");
    $stmtCheckS->execute([$kodeStandar, $namaStandar]);
    $standarId = $stmtCheckS->fetchColumn();

    if (!$standarId) {
        $stmtInsS = $pdo->prepare("
            INSERT INTO sub_bidang_standar (bidang_id, kode_sub_bidang, nama_sub_bidang, deskripsi, scope, fakultas_id, prodi_id, created_by_role, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'prodi', ?, ?, 'gpm', 1, NOW(), NOW())
        ");
        $stmtInsS->execute([$bidangId, $kodeStandar, $namaStandar, $deskripsiStandar, $fakultasId, $prodiId]);
        $standarId = (int)$pdo->lastInsertId();
        echo "[OK] Standar Khusus Prodi berhasil dibuat: [ID {$standarId}] {$kodeStandar} - {$namaStandar}\n";
    } else {
        $pdo->prepare("UPDATE sub_bidang_standar SET bidang_id = ?, scope = 'prodi', fakultas_id = ?, prodi_id = ?, is_active = 1 WHERE id = ?")
            ->execute([$bidangId, $fakultasId, $prodiId, $standarId]);
        echo "[OK] Standar Khusus Prodi sudah ada: [ID {$standarId}] {$kodeStandar} - {$namaStandar}\n";
    }

    // 3. Salin sample PDF fisik agar bisa dipreview
    $sampleSource = __DIR__ . '/../uploads/documents/PANDUAN BAN PT & LAM/Budaya Mutu & Tata Pamong/2021 - 2022/1 - Penetapan/B-Kriteria-Indikator-Prosedur-Asesmen-dan-Penilaian-Akreditasi-IAPS-2.0.pdf';
    $targetDir = __DIR__ . '/../uploads/documents';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $targetPdf1 = $targetDir . '/panduan_standar_ai_ti_2026.pdf';
    $targetPdf2 = $targetDir . '/laporan_evaluasi_ai_ti_2026.pdf';

    if (file_exists($sampleSource)) {
        if (!file_exists($targetPdf1)) copy($sampleSource, $targetPdf1);
        if (!file_exists($targetPdf2)) copy($sampleSource, $targetPdf2);
    }

    // 4. Buat Dokumen 1: MENUNGGU REVIEW GPM (dan nantinya LPM)
    $nomorDok1 = 'UNIKA/FIKOM/TI/STD-AI/2026/001';
    $namaDok1 = 'Panduan Standar Kurikulum & Sertifikasi Kompetensi AI Mahasiswa TI 2026/2027';

    $stmtCheckD1 = $pdo->prepare("SELECT id FROM ppepp_documents WHERE nomor_dokumen = ? LIMIT 1");
    $stmtCheckD1->execute([$nomorDok1]);
    $doc1Id = $stmtCheckD1->fetchColumn();

    if (!$doc1Id) {
        $stmtInsD1 = $pdo->prepare("
            INSERT INTO ppepp_documents (
                prodi_id, fakultas_id, level, user_id, bidang_id, sub_bidang_id,
                nama_dokumen, nomor_dokumen, siklus, tahun_akademik, tanggal_dokumen,
                jenis_upload, file_path, file_size, file_extension,
                status_review, tahap_review, deskripsi, created_at, updated_at
            ) VALUES (
                ?, ?, 'prodi', ?, ?, ?,
                ?, ?, 'penetapan', '2026/2027', '2026-11-20',
                'file', 'uploads/documents/panduan_standar_ai_ti_2026.pdf', '2.4 MB', 'pdf',
                'menunggu_review_gpm', 'gpm', 'Dokumen penetapan standar khusus kompetensi Artificial Intelligence dan sertifikasi profesi mahasiswa Program Studi S1 Teknik Informatika.',
                NOW(), NOW()
            )
        ");
        $stmtInsD1->execute([$prodiId, $fakultasId, $prodiUserId, $bidangId, $standarId, $namaDok1, $nomorDok1]);
        $doc1Id = (int)$pdo->lastInsertId();

        // Lampirkan ke ppepp_document_files
        $stmtFile1 = $pdo->prepare("
            INSERT INTO ppepp_document_files (
                document_id, file_name, file_path, file_size, file_extension,
                narasi, sub_bidang_ids, sort_order, can_download_public, created_at
            ) VALUES (?, ?, ?, ?, 'pdf', ?, ?, 1, 1, NOW())
        ");
        $stmtFile1->execute([
            $doc1Id,
            'Panduan_Standar_Kompetensi_AI_TI_2026.pdf',
            'uploads/documents/panduan_standar_ai_ti_2026.pdf',
            '2.4 MB',
            'Berkas panduan kurikulum dan indikator capaian standar kompetensi AI prodi TI.',
            json_encode([(int)$standarId])
        ]);

        echo "[OK] Dokumen 1 (Menunggu Review GPM & LPM) berhasil dibuat: [ID {$doc1Id}] {$nomorDok1}\n";
    } else {
        echo "[OK] Dokumen 1 sudah ada: [ID {$doc1Id}] {$nomorDok1}\n";
    }

    // 5. Buat Dokumen 2: SUDAH LOLOS REVIEW GPM & LPM (LANGSUNG TAMPIL DI HALAMAN PUBLIK)
    $nomorDok2 = 'UNIKA/FIKOM/TI/EVAL-AI/2026/002';
    $namaDok2 = 'Laporan Evaluasi Implementasi Portofolio AI Terapan Mahasiswa TI 2026/2027';

    $stmtCheckD2 = $pdo->prepare("SELECT id FROM ppepp_documents WHERE nomor_dokumen = ? LIMIT 1");
    $stmtCheckD2->execute([$nomorDok2]);
    $doc2Id = $stmtCheckD2->fetchColumn();

    if (!$doc2Id) {
        $stmtInsD2 = $pdo->prepare("
            INSERT INTO ppepp_documents (
                prodi_id, fakultas_id, level, user_id, bidang_id, sub_bidang_id,
                nama_dokumen, nomor_dokumen, siklus, tahun_akademik, tanggal_dokumen,
                jenis_upload, file_path, file_size, file_extension,
                status_review, tahap_review,
                gpm_reviewed_by, gpm_reviewed_at, gpm_catatan,
                reviewed_by, reviewed_at, catatan_review,
                deskripsi, created_at, updated_at
            ) VALUES (
                ?, ?, 'prodi', ?, ?, ?,
                ?, ?, 'evaluasi', '2026/2027', '2026-11-25',
                'file', 'uploads/documents/laporan_evaluasi_ai_ti_2026.pdf', '1.8 MB', 'pdf',
                'sesuai', 'selesai',
                ?, NOW() - INTERVAL 1 DAY, 'Lolos verifikasi GPM FIKOM: Portofolio dan bukti luaran AI telah sesuai standar khusus Prodi Teknik Informatika.',
                ?, NOW(), 'Disetujui dan disahkan oleh Pusat Penjaminan Mutu LPM UNIKA Soegijapranata.',
                'Laporan hasil evaluasi pelaksanaan standar portofolio AI mahasiswa Teknik Informatika yang telah disahkan LPM dan dipublikasikan.',
                NOW(), NOW()
            )
        ");
        $stmtInsD2->execute([$prodiId, $fakultasId, $prodiUserId, $bidangId, $standarId, $namaDok2, $nomorDok2, $gpmUserId, $lpmUserId]);
        $doc2Id = (int)$pdo->lastInsertId();

        // Lampirkan ke ppepp_document_files
        $stmtFile2 = $pdo->prepare("
            INSERT INTO ppepp_document_files (
                document_id, file_name, file_path, file_size, file_extension,
                narasi, sub_bidang_ids, sort_order, can_download_public, created_at
            ) VALUES (?, ?, ?, ?, 'pdf', ?, ?, 1, 1, NOW())
        ");
        $stmtFile2->execute([
            $doc2Id,
            'Laporan_Evaluasi_Portofolio_AI_TI_2026.pdf',
            'uploads/documents/laporan_evaluasi_ai_ti_2026.pdf',
            '1.8 MB',
            'Berkas rekapan asesmen portofolio proyek industri AI mahasiswa TI.',
            json_encode([(int)$standarId])
        ]);

        echo "[OK] Dokumen 2 (Disetujui LPM & Tampil di Publik) berhasil dibuat: [ID {$doc2Id}] {$nomorDok2}\n";
    } else {
        echo "[OK] Dokumen 2 sudah ada: [ID {$doc2Id}] {$nomorDok2}\n";
    }

    echo "\n=== RINGKASAN SEEDING SELESAI ===\n";
    echo "1. Bidang Baru: {$namaBidang} (ID: {$bidangId}, Scope: prodi ID {$prodiId})\n";
    echo "2. Standar Baru: {$namaStandar} (ID: {$standarId}, Scope: prodi ID {$prodiId})\n";
    echo "3. Dokumen 1 (Menunggu Telaah GPM): ID {$doc1Id} - Status: menunggu_review_gpm\n";
    echo "4. Dokumen 2 (Tayang di Publik): ID {$doc2Id} - Status: sesuai (LPM Approved)\n";

} catch (Exception $e) {
    echo "[ERROR] Seeding gagal: " . $e->getMessage() . "\n";
    exit(1);
}
