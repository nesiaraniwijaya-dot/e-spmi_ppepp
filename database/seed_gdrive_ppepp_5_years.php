<?php
/**
 * Seeder Dokumen SPMI PPEPP (5 Tahun Akademik) dari Google Drive Matrix
 * SPMI PPEPP (MITRA) - UNIKA Soegijapranata
 *
 * Mengimpor 234 butir dokumen mutu PPEPP untuk 5 tahun akademik:
 * - 2021/2022
 * - 2022/2023
 * - 2023/2024
 * - 2024/2025
 * - 2025/2026
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "========================================================\n";
    echo "   MEMULAI SEEDER DOKUMEN MUTU PPEPP 5 TAHUN AKADEMIK   \n";
    echo "========================================================\n\n";

    // 1. Baca master items dari JSON
    $jsonPath = __DIR__ . '/../scratch/ppepp_master_items.json';
    if (!file_exists($jsonPath)) {
        throw new Exception("File master items tidak ditemukan di: {$jsonPath}");
    }
    $masterItems = json_decode(file_get_contents($jsonPath), true);
    echo "Ditemukan " . count($masterItems) . " butir template dokumen PPEPP dari Google Drive.\n";

    // 2. Ambil seluruh prodi dari database
    $prodis = $db->query("SELECT p.id, p.kode_prodi, p.nama_prodi, p.fakultas_id, f.nama_fakultas 
                          FROM prodis p 
                          JOIN fakultas f ON p.fakultas_id = f.id 
                          ORDER BY p.id ASC")->fetchAll();
    echo "Ditemukan " . count($prodis) . " Program Studi di database.\n";

    // 3. Petakan user pengunggah per prodi
    $users = $db->query("SELECT id, name, role, prodi_id, fakultas_id FROM users WHERE is_active = 1")->fetchAll();
    $prodiUserMap = [];
    foreach ($prodis as $p) {
        $userId = 7; // Fallback: Admin LPM
        foreach ($users as $u) {
            if ($u['prodi_id'] == $p['id'] && in_array($u['role'], ['kaprodi', 'sekprodi', 'admin_prodi'])) {
                $userId = $u['id'];
                break;
            }
        }
        $prodiUserMap[$p['id']] = $userId;
    }

    // 4. Daftar 5 Tahun Akademik dan estimasi tanggal dokumen
    $academicYears = [
        '2021/2022' => ['start_year' => 2021, 'date' => '2021-10-15'],
        '2022/2023' => ['start_year' => 2022, 'date' => '2022-10-15'],
        '2023/2024' => ['start_year' => 2023, 'date' => '2023-10-15'],
        '2024/2025' => ['start_year' => 2024, 'date' => '2024-10-15'],
        '2025/2026' => ['start_year' => 2025, 'date' => '2025-10-15'],
    ];

    $defaultGdriveRoot = 'https://drive.google.com/drive/folders/1AtO6x4sYiPuggdl2zcqLnZbSiWplrm1S?usp=sharing';

    // 5. Query pengecekan eksistensi dokumen untuk menghindari duplikasi
    $checkStmt = $db->prepare("SELECT id FROM ppepp_documents 
                               WHERE prodi_id = ? AND tahun_akademik = ? AND siklus = ? AND nama_dokumen = ? 
                               LIMIT 1");

    // 6. Statement INSERT dokumen
    $insertSql = "INSERT INTO ppepp_documents (
        prodi_id, fakultas_id, level, user_id, bidang_id,
        nama_dokumen, nomor_dokumen, siklus, tahun_akademik,
        tanggal_dokumen, tanggal_berlaku_mulai, tanggal_berlaku_selesai,
        jenis_upload, external_link, public_page_limit, can_download_public,
        status_review, tahap_review, deskripsi, created_at, updated_at
    ) VALUES (
        :prodi_id, :fakultas_id, 'prodi', :user_id, :bidang_id,
        :nama_dokumen, :nomor_dokumen, :siklus, :tahun_akademik,
        :tanggal_dokumen, :tanggal_berlaku_mulai, :tanggal_berlaku_selesai,
        'link', :external_link, 1, 1,
        'menunggu_review_gpm', 'gpm', :deskripsi, :created_at, :updated_at
    )";
    $insertStmt = $db->prepare($insertSql);

    $totalInserted = 0;
    $totalSkipped = 0;

    $db->beginTransaction();

    foreach ($prodis as $p) {
        $prodiId = $p['id'];
        $fakultasId = $p['fakultas_id'];
        $userId = $prodiUserMap[$prodiId] ?? 7;

        $prodiInserted = 0;

        foreach ($academicYears as $thn => $thnMeta) {
            $tglDok = $thnMeta['date'];
            $tglMulai = $thnMeta['start_year'] . '-09-01';
            $tglSelesai = ($thnMeta['start_year'] + 1) . '-08-31';

            foreach ($masterItems as $item) {
                $namaDok = mb_substr(trim($item['nama_dokumen']), 0, 250);
                $siklus = $item['siklus'];
                $bidangId = $item['bidang_id'];
                
                $rawNomor = !empty($item['nomor_dokumen']) ? trim($item['nomor_dokumen']) : "";
                if (!empty($rawNomor) && mb_strlen($rawNomor) <= 100) {
                    $nomorDok = $rawNomor;
                } else if (!empty($rawNomor)) {
                    $nomorDok = mb_substr($rawNomor, 0, 95);
                } else {
                    $nomorDok = "SPMI/{$p['kode_prodi']}/" . str_replace('/', '-', $thn) . "/" . strtoupper(substr($siklus, 0, 3));
                }
                
                $extLink = !empty($item['link']) ? $item['link'] : $defaultGdriveRoot;
                
                $deskripsiParts = [];
                if (!empty($item['komponen'])) $deskripsiParts[] = "Komponen: " . $item['komponen'];
                if (!empty($item['kegiatan'])) $deskripsiParts[] = "Kegiatan SPMI: " . $item['kegiatan'];
                if (!empty($rawNomor) && mb_strlen($rawNomor) > 100) $deskripsiParts[] = "Ref Dokumen: " . $rawNomor;
                $deskripsiParts[] = "Dokumen bukti tahapan " . ucfirst($siklus) . " Bidang " . $item['bidang_name'] . " untuk " . $p['nama_prodi'] . " Tahun Akademik " . $thn . ".";
                $deskripsi = implode("\n", $deskripsiParts);

                // Cek duplikasi
                $checkStmt->execute([$prodiId, $thn, $siklus, $namaDok]);
                if ($checkStmt->fetch()) {
                    $totalSkipped++;
                    continue;
                }

                $createdAt = date('Y-m-d H:i:s');

                $insertStmt->execute([
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
                    ':external_link' => $extLink,
                    ':deskripsi' => $deskripsi,
                    ':created_at' => $createdAt,
                    ':updated_at' => $createdAt
                ]);

                $totalInserted++;
                $prodiInserted++;
            }
        }

        echo "-> [Prodi {$prodiId}] {$p['nama_prodi']} ({$p['nama_fakultas']}): +{$prodiInserted} dokumen baru dimasukkan.\n";
    }

    $db->commit();

    echo "\n========================================================\n";
    echo "                 PROSES SEEDER SELESAI                  \n";
    echo "========================================================\n";
    echo "Total Dokumen Berhasil Dimasukkan : {$totalInserted}\n";
    echo "Total Dokumen Dilewati (Duplikat) : {$totalSkipped}\n";

    $finalCount = $db->query("SELECT COUNT(*) FROM ppepp_documents")->fetchColumn();
    echo "Total Keseluruhan Dokumen di Database Saat Ini: {$finalCount}\n";

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
