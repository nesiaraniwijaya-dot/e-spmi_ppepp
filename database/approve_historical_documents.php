<?php
/**
 * Pengesahan Otomatis Dokumen Lampau & Penyiapan Dokumen 2026/2027 untuk Reviu GPM & LPM
 * - Dokumen tahun lampau (< 2026/2027): Disetujui (status_review = 'sesuai', tahap_review = 'selesai')
 * - Dokumen tahun berjalan (2026/2027): Menunggu Reviu GPM (status_review = 'menunggu_review_gpm', tahap_review = 'gpm')
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "========================================================\n";
    echo "  PENGESAHAN DOKUMEN LAMPAU & ALUR REVIU 2026/2027\n";
    echo "========================================================\n\n";

    // 1. Ambil ID Kepala LPM
    $kepalaLpmId = $db->query("SELECT id FROM users WHERE email = 'lpm@unika.ac.id'")->fetchColumn();
    if (!$kepalaLpmId) {
        $kepalaLpmId = $db->query("SELECT id FROM users WHERE role = 'kepala_lpm' LIMIT 1")->fetchColumn();
    }
    echo "Kepala LPM Reviewer ID: " . ($kepalaLpmId ?: 'NULL') . "\n";

    // 2. Ambil ID GPM per Fakultas
    $gpmEmails = [
        1  => 'ardiyanto@unika.ac.id',       // FAD
        2  => 'erdhi@unika.ac.id',           // FIKOM
        3  => 'hardjanti@unika.ac.id',       // FEB
        4  => 'ekawati@unika.ac.id',         // FBS
        5  => 'budi@unika.ac.id',            // FT
        6  => 'cicih@unika.ac.id',           // FPSI
        7  => 'negorotheo@unika.ac.id',      // FHK
        8  => 'pratiwi@unika.ac.id',         // FTP
        9  => 'lidwina@unika.ac.id',         // FK
        10 => 'dhiyan.krishna@unika.ac.id',  // FITL
    ];

    $gpmMap = [];
    foreach ($gpmEmails as $fakId => $email) {
        $uid = $db->query("SELECT id FROM users WHERE email = '$email'")->fetchColumn();
        if ($uid) {
            $gpmMap[$fakId] = (int)$uid;
        }
    }
    echo "Total Koordinator GPM terpetakan: " . count($gpmMap) . " fakultas.\n\n";

    // 3. Update dokumen lampau (!= '2026/2027') menjadi 'sesuai' (Approved)
    echo "3. Menyetujui seluruh dokumen tahun akademik lampau (< 2026/2027)...\n";

    // Update basis umum
    $sqlPast = "UPDATE ppepp_documents 
                SET status_review = 'sesuai',
                    tahap_review = 'selesai',
                    catatan_review = 'Disetujui dan sesuai standar mutu institusi.',
                    gpm_catatan = 'Disetujui oleh GPM Fakultas.',
                    lpm_catatan = 'Disetujui dan disahkan oleh LPM.',
                    gpm_reviewed_at = COALESCE(gpm_reviewed_at, created_at),
                    lpm_reviewed_at = COALESCE(lpm_reviewed_at, created_at),
                    reviewed_at = COALESCE(reviewed_at, created_at),
                    lpm_reviewed_by = " . ($kepalaLpmId ? $kepalaLpmId : "NULL") . ",
                    reviewed_by = " . ($kepalaLpmId ? $kepalaLpmId : "NULL") . "
                WHERE tahun_akademik != '2026/2027' 
                  AND deleted_at IS NULL";
    $affectedPast = $db->exec($sqlPast);
    echo "   -> Berhasil menyetujui $affectedPast dokumen tahun akademik lampau.\n";

    // Set gpm_reviewed_by berdasarkan fakultas_id dokumen
    foreach ($gpmMap as $fakId => $gpmUid) {
        $db->exec("UPDATE ppepp_documents 
                   SET gpm_reviewed_by = $gpmUid 
                   WHERE fakultas_id = $fakId 
                     AND tahun_akademik != '2026/2027' 
                     AND deleted_at IS NULL");
    }
    echo "   -> gpm_reviewed_by disinkronkan ke Koordinator GPM masing-masing fakultas.\n\n";

    // 4. Pastikan dokumen 2026/2027 dalam status menunggu reviu GPM
    echo "4. Menyiapkan dokumen tahun akademik 2026/2027 untuk Reviu GPM & LPM...\n";
    $sqlCurrent = "UPDATE ppepp_documents 
                   SET status_review = 'menunggu_review_gpm',
                       tahap_review = 'gpm',
                       gpm_reviewed_by = NULL,
                       gpm_reviewed_at = NULL,
                       gpm_catatan = NULL,
                       lpm_reviewed_by = NULL,
                       lpm_reviewed_at = NULL,
                       lpm_catatan = NULL,
                       reviewed_by = NULL,
                       reviewed_at = NULL,
                       catatan_review = NULL
                   WHERE tahun_akademik = '2026/2027' 
                     AND deleted_at IS NULL";
    $affectedCurrent = $db->exec($sqlCurrent);
    echo "   -> Berhasil menyetel $affectedCurrent dokumen 2026/2027 ke antrean 'menunggu_review_gpm'.\n\n";

    // 5. Tampilkan ringkasan status akhir per tahun akademik
    echo "5. Ringkasan Status Dokumen Saat Ini:\n";
    $stmt = $db->query("SELECT tahun_akademik, status_review, tahap_review, count(*) as c 
                        FROM ppepp_documents 
                        WHERE deleted_at IS NULL 
                        GROUP BY tahun_akademik, status_review, tahap_review 
                        ORDER BY tahun_akademik, status_review");
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   [{$r['tahun_akademik']}] Status: {$r['status_review']} | Tahap: {$r['tahap_review']} -> {$r['c']} dokumen\n";
    }

    echo "\n========================================================\n";
    echo "[DONE] Pengesahan dokumen lampau dan alur 2026/2027 berhasil disiapkan!\n";
    echo "========================================================\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

