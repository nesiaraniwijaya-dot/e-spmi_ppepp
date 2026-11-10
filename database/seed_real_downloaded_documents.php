<?php
/**
 * Import REAL Downloaded Documents from Google Drive
 * SPMI PPEPP (MITRA) - UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "========================================================\n";
    echo "   MEMASUKKAN DOKUMEN FISIK NYATA HASIL UNDUH GOOGLE DRIVE\n";
    echo "========================================================\n\n";

    $jsonPath = __DIR__ . '/../scratch/downloaded_real_files.json';
    if (!file_exists($jsonPath)) {
        throw new Exception("File JSON tidak ditemukan: {$jsonPath}");
    }
    $files = json_decode(file_get_contents($jsonPath), true);
    echo "Ditemukan " . count($files) . " berkas fisik nyata hasil unduhan.\n";

    // Petakan user pengunggah per prodi
    $prodis = $db->query("SELECT id, kode_prodi, nama_prodi, fakultas_id FROM prodis")->fetchAll();
    $prodiMap = [];
    foreach ($prodis as $p) {
        $prodiMap[$p['id']] = $p;
    }

    $users = $db->query("SELECT id, name, role, prodi_id FROM users WHERE is_active = 1")->fetchAll();
    $prodiUserMap = [];
    foreach ($prodis as $p) {
        $userId = 7; // Super Admin fallback
        foreach ($users as $u) {
            if ($u['prodi_id'] == $p['id'] && in_array($u['role'], ['kaprodi', 'sekprodi', 'admin_prodi'])) {
                $userId = $u['id'];
                break;
            }
        }
        $prodiUserMap[$p['id']] = $userId;
    }

    $checkStmt = $db->prepare("SELECT id FROM ppepp_documents WHERE prodi_id = ? AND tahun_akademik = ? AND nama_dokumen = ? LIMIT 1");

    $insertDocStmt = $db->prepare("INSERT INTO ppepp_documents (
        prodi_id, fakultas_id, level, user_id, bidang_id,
        nama_dokumen, nomor_dokumen, siklus, tahun_akademik,
        tanggal_dokumen, tanggal_berlaku_mulai, tanggal_berlaku_selesai,
        jenis_upload, file_path, file_size, file_extension,
        public_page_limit, can_download_public, status_review, tahap_review,
        deskripsi, created_at, updated_at
    ) VALUES (
        :prodi_id, :fakultas_id, 'prodi', :user_id, :bidang_id,
        :nama_dokumen, :nomor_dokumen, :siklus, :tahun_akademik,
        :tanggal_dokumen, :tanggal_berlaku_mulai, :tanggal_berlaku_selesai,
        'file', :file_path, :file_size, :file_extension,
        0, 1, 'sesuai', 'selesai',
        :deskripsi, :created_at, :updated_at
    )");

    $insertFileStmt = $db->prepare("INSERT INTO ppepp_document_files (
        document_id, file_name, file_path, file_size, file_extension,
        narasi, is_page_limited, public_page_limit, can_download_public, sort_order, created_at
    ) VALUES (
        :document_id, :file_name, :file_path, :file_size, :file_extension,
        :narasi, 0, 0, 1, 0, :created_at
    )");

    $db->beginTransaction();

    $inserted = 0;
    $skipped = 0;

    foreach ($files as $f) {
        $prodiId = $f['prodi_id'];
        $fakultasId = $f['fakultas_id'];
        $bidangId = $f['bidang_id'];
        $namaDok = mb_substr(trim($f['nama_dokumen']), 0, 250);
        $siklus = $f['siklus'];
        $thn = $f['tahun_akademik'];
        $filePath = $f['file_path'];
        $fileSize = $f['file_size'];
        $fileExt = $f['file_extension'];
        $fileName = $f['file_name'];

        $checkStmt->execute([$prodiId, $thn, $namaDok]);
        if ($checkStmt->fetch()) {
            $skipped++;
            continue;
        }

        $p = $prodiMap[$prodiId] ?? null;
        $kodeProdi = $p ? $p['kode_prodi'] : 'PRODI';
        $userId = $prodiUserMap[$prodiId] ?? 7;

        // Extract year start
        preg_match('/(\d{4})\/(\d{4})/', $thn, $mYr);
        $startYr = !empty($mYr[1]) ? $mYr[1] : '2023';
        $endYr = !empty($mYr[2]) ? $mYr[2] : '2024';
        $tglDok = "{$startYr}-10-15";
        $tglMulai = "{$startYr}-09-01";
        $tglSelesai = "{$endYr}-08-31";

        $nomorDok = "SPMI/{$kodeProdi}/" . str_replace('/', '-', $thn) . "/" . strtoupper(substr($siklus, 0, 3));
        $deskripsi = "Dokumen resmi SPMI PPEPP: {$namaDok}. Disahkan dan terverifikasi untuk Tahun Akademik {$thn}.";
        $now = date('Y-m-d H:i:s');

        $insertDocStmt->execute([
            ':prodi_id' => $prodiId,
            ':fakultas_id' => $fakultasId,
            ':user_id' => $userId,
            ':bidang_id' => $bidangId,
            ':nama_dokumen' => $namaDok,
            ':nomor_dokumen' => $nomorDok,
            ':siklus' => $siklus,
            ':tahun_akademik' => $thn,
            ':tanggal_dokumen' => $tglDok,
            ':tanggal_berlaku_mulai' => $tglMulai,
            ':tanggal_berlaku_selesai' => $tglSelesai,
            ':file_path' => $filePath,
            ':file_size' => $fileSize,
            ':file_extension' => $fileExt,
            ':deskripsi' => $deskripsi,
            ':created_at' => $now,
            ':updated_at' => $now
        ]);

        $docId = $db->lastInsertId();

        // Insert into ppepp_document_files
        $insertFileStmt->execute([
            ':document_id' => $docId,
            ':file_name' => $fileName,
            ':file_path' => $filePath,
            ':file_size' => $fileSize,
            ':file_extension' => $fileExt,
            ':narasi' => "Berkas asli dokumen {$namaDok} yang telah diverifikasi sesuai standar mutu SPMI.",
            ':created_at' => $now
        ]);

        $inserted++;
    }

    $db->commit();

    echo "========================================================\n";
    echo "Dokumen Fisik Berhasil Dimasukkan: {$inserted}\n";
    echo "Dokumen Dilewati (Duplikat)      : {$skipped}\n";
    $finalCount = $db->query("SELECT COUNT(*) FROM ppepp_documents")->fetchColumn();
    echo "Total Dokumen di Database Saat Ini: {$finalCount}\n";
    echo "========================================================\n";

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
