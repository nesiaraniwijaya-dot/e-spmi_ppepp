<?php
/**
 * Seeder Data Dummy Uji Coba Alur Review Berjenjang & Masking Privasi
 * (Skenario A: Disetujui LPM | Skenario B: Ditolak LPM -> GPM -> Diteruskan ke Prodi/Dekanat)
 *
 * Cara Menjalankan:
 *   - Pasang / Reset Data Dummy : php database/seed_dummy_review_flow.php
 *   - Hapus Semua Data Dummy    : php database/seed_dummy_review_flow.php --clean
 */

require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();
$isCleanOnly = in_array('--clean', $argv ?? [], true);

echo "========================================================================\n";
echo "  SEEDER DUMMY UJI COBA ALUR REVIEW BERJENJANG (LPM -> GPM -> PRODI)\n";
echo "========================================================================\n\n";

// 1. Cari PDF fisik nyata di disk agar tombol preview 'Lihat' berjalan mulus
$samplePdfRel = 'uploads/documents/Prodi S1 Teknik Informatika/Pendidikan/2021 - 2022/2 - Pelaksanaan/SK Dekan Pedoman Pengukuran CPL TI.pdf';
$samplePdfAbs = __DIR__ . '/../' . $samplePdfRel;
if (!is_file($samplePdfAbs)) {
    $uploadsRoot = realpath(__DIR__ . '/../uploads/documents');
    if ($uploadsRoot && is_dir($uploadsRoot)) {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsRoot));
        foreach ($it as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'pdf') {
                $abs = str_replace('\\', '/', $file->getPathname());
                $base = str_replace('\\', '/', realpath(__DIR__ . '/..')) . '/';
                $samplePdfRel = str_replace($base, '', $abs);
                $samplePdfAbs = $abs;
                break;
            }
        }
    }
}
$samplePdfSize = is_file($samplePdfAbs) ? round(filesize($samplePdfAbs) / 1024, 1) . ' KB' : '510.9 KB';

// 2. Bersihkan data dummy sebelumnya (berbasis prefix nomor DUMMY-FLOW/)
$oldIds = $db->query("SELECT id FROM ppepp_documents WHERE nomor_dokumen LIKE 'DUMMY-FLOW/%'")->fetchAll(PDO::FETCH_COLUMN);
if (!empty($oldIds)) {
    $in = implode(',', array_map('intval', $oldIds));
    $db->exec("DELETE FROM ppepp_document_reviews WHERE document_id IN ({$in})");
    $db->exec("DELETE FROM ppepp_document_revisions WHERE document_id IN ({$in})");
    $db->exec("DELETE FROM ppepp_document_file_sub_bidang WHERE document_file_id IN (SELECT id FROM ppepp_document_files WHERE document_id IN ({$in}))");
    $db->exec("DELETE FROM ppepp_document_files WHERE document_id IN ({$in})");
    $db->exec("DELETE FROM ppepp_document_sub_bidang WHERE document_id IN ({$in})");
    $db->exec("DELETE FROM ppepp_documents WHERE id IN ({$in})");
    echo "[CLEANUP] Menghapus " . count($oldIds) . " dokumen dummy uji coba sebelumnya.\n";
}

if ($isCleanOnly) {
    echo "\n[SELESAI] Seluruh dokumen dummy uji coba berhasil dibersihkan.\n";
    exit(0);
}

// 3. Resolusi ID Fakultas FIKOM, Prodi TI, Akun Pengunggah & Reviewer
$fikom = $db->query("SELECT id, nama_fakultas FROM fakultas WHERE kode_fakultas IN ('FIK', 'FIKOM') LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$fikomId = (int)($fikom['id'] ?? 2);
$fikomName = $fikom['nama_fakultas'] ?? 'Fakultas Ilmu Komputer';

$ti = $db->query("SELECT id, nama_prodi FROM prodis WHERE kode_prodi = '55201' OR fakultas_id = {$fikomId} LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$tiId = (int)($ti['id'] ?? 1);
$tiName = $ti['nama_prodi'] ?? 'Teknik Informatika';

$kaprodiUser = $db->query("SELECT id, name FROM users WHERE prodi_id = {$tiId} AND role IN ('kaprodi', 'sekprodi', 'admin_prodi') AND is_active = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$kaprodiId = (int)($kaprodiUser['id'] ?? 22);
$kaprodiName = $kaprodiUser['name'] ?? 'Kaprodi Teknik Informatika';

$dekanUser = $db->query("SELECT id, name FROM users WHERE fakultas_id = {$fikomId} AND role IN ('dekan', 'wadek', 'admin_fakultas') AND is_active = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$dekanId = (int)($dekanUser['id'] ?? 15);
$dekanName = $dekanUser['name'] ?? 'Dekan FIKOM';

$gpmUser = $db->query("SELECT id, name FROM users WHERE fakultas_id = {$fikomId} AND role = 'gpm' AND is_active = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$gpmId = (int)($gpmUser['id'] ?? 10);
$gpmOfficialName = 'GPM ' . $fikomName;

$lpmUser = $db->query("SELECT id, name FROM users WHERE role IN ('super_admin', 'kepala_lpm', 'admin_lpm') AND is_active = 1 ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$lpmId = (int)($lpmUser['id'] ?? 7);
$lpmOfficialName = 'Pusat Penjaminan Mutu LPM';

// Ambil 1 sub_bidang aktif per bidang agar tampilan standar terisi rapi
$subByBidang = [];
foreach ($db->query("SELECT id, bidang_id FROM sub_bidang_standar WHERE is_active = 1 ORDER BY id ASC") as $row) {
    if (!isset($subByBidang[$row['bidang_id']])) {
        $subByBidang[$row['bidang_id']] = (int)$row['id'];
    }
}

$now = date('Y-m-d H:i:s');
$tMinus4h = date('Y-m-d H:i:s', strtotime('-4 hours'));
$tMinus3h = date('Y-m-d H:i:s', strtotime('-3 hours'));
$tMinus2h = date('Y-m-d H:i:s', strtotime('-2 hours'));
$tMinus1h = date('Y-m-d H:i:s', strtotime('-1 hour'));

// Daftar 7 Skenario Dokumen Dummy Uji Coba
$dummies = [
    // =========================================================================
    // SKENARIO A: ALUR DISETUJUI LPM (PRODI -> GPM -> LPM -> SESUAI)
    // =========================================================================
    [
        'code' => 'A1',
        'level' => 'prodi',
        'prodi_id' => $tiId,
        'fakultas_id' => $fikomId,
        'user_id' => $kaprodiId,
        'bidang_id' => 6,
        'nama_dokumen' => '[DUMMY UJI COBA] A1 - RPS Kecerdasan Buatan Prodi TI (Siap Uji dari GPM ke LPM)',
        'nomor_dokumen' => 'DUMMY-FLOW/2026/A1',
        'siklus' => 'penetapan',
        'tahun_akademik' => '2025/2026',
        'deskripsi' => 'Dokumen dummy skenario A1: baru diunggah oleh Prodi TI. Silakan uji klik Setujui oleh GPM FIKOM -> lalu Setujui Akhir oleh LPM.',
        'status_review' => 'menunggu_review_gpm',
        'tahap_review' => 'gpm',
        'catatan_review' => null,
        'gpm_reviewed_by' => null,
        'gpm_reviewed_at' => null,
        'gpm_catatan' => null,
        'lpm_reviewed_by' => null,
        'lpm_reviewed_at' => null,
        'lpm_catatan' => null,
        'reviewed_by' => null,
        'reviewed_at' => null,
        'created_at' => $now,
        'updated_at' => $now,
        'reviews' => [],
        'revision' => null,
    ],
    [
        'code' => 'A2',
        'level' => 'prodi',
        'prodi_id' => $tiId,
        'fakultas_id' => $fikomId,
        'user_id' => $kaprodiId,
        'bidang_id' => 6,
        'nama_dokumen' => '[DUMMY UJI COBA] A2 - Kurikulum OBE & Peta CPL Prodi TI (Siap Disetujui LPM)',
        'nomor_dokumen' => 'DUMMY-FLOW/2026/A2',
        'siklus' => 'penetapan',
        'tahun_akademik' => '2025/2026',
        'deskripsi' => 'Dokumen dummy skenario A2: sudah disetujui GPM FIKOM, kini masuk antrean LPM. Silakan uji tombol "Sesuai Standar Mutu" di akun LPM.',
        'status_review' => 'menunggu_review_lpm',
        'tahap_review' => 'lpm',
        'catatan_review' => 'Telah diperiksa GPM Fakultas Ilmu Komputer: peta CPL dan struktur kurikulum sudah sesuai pedoman fakultas, direkomendasikan disahkan LPM.',
        'gpm_reviewed_by' => $gpmId,
        'gpm_reviewed_at' => $tMinus2h,
        'gpm_catatan' => 'Telah diperiksa GPM Fakultas Ilmu Komputer: peta CPL dan struktur kurikulum sudah sesuai pedoman fakultas, direkomendasikan disahkan LPM.',
        'lpm_reviewed_by' => null,
        'lpm_reviewed_at' => null,
        'lpm_catatan' => null,
        'reviewed_by' => null,
        'reviewed_at' => null,
        'created_at' => $tMinus3h,
        'updated_at' => $now,
        'reviews' => [
            [
                'reviewer_id' => $gpmId,
                'reviewer_role' => 'gpm',
                'reviewer_name' => $gpmOfficialName,
                'tahap' => 'gpm',
                'status_sebelumnya' => 'menunggu_review_gpm',
                'status_hasil' => 'menunggu_review_lpm',
                'catatan' => 'Telah diperiksa GPM Fakultas Ilmu Komputer: peta CPL dan struktur kurikulum sudah sesuai pedoman fakultas, direkomendasikan disahkan LPM.',
                'created_at' => $tMinus2h,
            ],
        ],
        'revision' => null,
    ],
    [
        'code' => 'A3',
        'level' => 'prodi',
        'prodi_id' => $tiId,
        'fakultas_id' => $fikomId,
        'user_id' => $kaprodiId,
        'bidang_id' => 6,
        'nama_dokumen' => '[DUMMY UJI COBA] A3 - Pedoman Tugas Akhir Prodi TI (Contoh Selesai Disetujui LPM)',
        'nomor_dokumen' => 'DUMMY-FLOW/2026/A3',
        'siklus' => 'pelaksanaan',
        'tahun_akademik' => '2025/2026',
        'deskripsi' => 'Dokumen dummy skenario A3: contoh hasil akhir dokumen yang sudah disetujui GPM dan disahkan oleh LPM (status: sesuai).',
        'status_review' => 'sesuai',
        'tahap_review' => 'selesai',
        'catatan_review' => 'Disetujui dan sah memenuhi Standar Nasional Pendidikan Tinggi (SN-Dikti) oleh Pusat Penjaminan Mutu LPM.',
        'gpm_reviewed_by' => $gpmId,
        'gpm_reviewed_at' => $tMinus3h,
        'gpm_catatan' => 'Sudah sesuai pedoman akademik FIKOM, diteruskan ke LPM.',
        'lpm_reviewed_by' => $lpmId,
        'lpm_reviewed_at' => $tMinus1h,
        'lpm_catatan' => 'Disetujui dan sah memenuhi Standar Nasional Pendidikan Tinggi (SN-Dikti) oleh Pusat Penjaminan Mutu LPM.',
        'reviewed_by' => $lpmId,
        'reviewed_at' => $tMinus1h,
        'created_at' => $tMinus4h,
        'updated_at' => $now,
        'reviews' => [
            [
                'reviewer_id' => $gpmId,
                'reviewer_role' => 'gpm',
                'reviewer_name' => $gpmOfficialName,
                'tahap' => 'gpm',
                'status_sebelumnya' => 'menunggu_review_gpm',
                'status_hasil' => 'menunggu_review_lpm',
                'catatan' => 'Sudah sesuai pedoman akademik FIKOM, diteruskan ke LPM.',
                'created_at' => $tMinus3h,
            ],
            [
                'reviewer_id' => $lpmId,
                'reviewer_role' => 'lpm',
                'reviewer_name' => $lpmOfficialName,
                'tahap' => 'lpm',
                'status_sebelumnya' => 'menunggu_review_lpm',
                'status_hasil' => 'sesuai',
                'catatan' => 'Disetujui dan sah memenuhi Standar Nasional Pendidikan Tinggi (SN-Dikti) oleh Pusat Penjaminan Mutu LPM.',
                'created_at' => $tMinus1h,
            ],
        ],
        'revision' => [
            'revisi_ke' => 1,
            'reviewer_id' => $lpmId,
            'reviewer_name' => ($lpmUser['name'] ?? 'Admin LPM') . ' (Admin LPM)',
            'reviewer_role' => 'super_admin',
            'status_review' => 'sesuai',
            'catatan_review' => 'Disetujui dan sah memenuhi Standar Nasional Pendidikan Tinggi (SN-Dikti) oleh Pusat Penjaminan Mutu LPM.',
            'reviewed_at' => $tMinus1h,
        ],
    ],

    // =========================================================================
    // SKENARIO B: TIDAK DISETUJUI LPM -> GPM MENERUSKAN KE PRODI (MASKING)
    // =========================================================================
    [
        'code' => 'B1',
        'level' => 'prodi',
        'prodi_id' => $tiId,
        'fakultas_id' => $fikomId,
        'user_id' => $kaprodiId,
        'bidang_id' => 2,
        'nama_dokumen' => '[DUMMY UJI COBA] B1 - Laporan AMI Internal Prodi TI (Siap Tidak Disetujui LPM)',
        'nomor_dokumen' => 'DUMMY-FLOW/2026/B1',
        'siklus' => 'evaluasi',
        'tahun_akademik' => '2025/2026',
        'deskripsi' => 'Dokumen dummy skenario B1: siap diuji penolakan oleh LPM ("Kembalikan ke GPM untuk Perbaikan") lalu lihat di akun GPM & Prodi.',
        'status_review' => 'menunggu_review_lpm',
        'tahap_review' => 'lpm',
        'catatan_review' => 'Kelengkapan berkas awal sudah diverifikasi GPM FIKOM dan diteruskan ke LPM.',
        'gpm_reviewed_by' => $gpmId,
        'gpm_reviewed_at' => $tMinus2h,
        'gpm_catatan' => 'Kelengkapan berkas awal sudah diverifikasi GPM FIKOM dan diteruskan ke LPM.',
        'lpm_reviewed_by' => null,
        'lpm_reviewed_at' => null,
        'lpm_catatan' => null,
        'reviewed_by' => null,
        'reviewed_at' => null,
        'created_at' => $tMinus3h,
        'updated_at' => $now,
        'reviews' => [
            [
                'reviewer_id' => $gpmId,
                'reviewer_role' => 'gpm',
                'reviewer_name' => $gpmOfficialName,
                'tahap' => 'gpm',
                'status_sebelumnya' => 'menunggu_review_gpm',
                'status_hasil' => 'menunggu_review_lpm',
                'catatan' => 'Kelengkapan berkas awal sudah diverifikasi GPM FIKOM dan diteruskan ke LPM.',
                'created_at' => $tMinus2h,
            ],
        ],
        'revision' => null,
    ],
    [
        'code' => 'B2',
        'level' => 'prodi',
        'prodi_id' => $tiId,
        'fakultas_id' => $fikomId,
        'user_id' => $kaprodiId,
        'bidang_id' => 3,
        'nama_dokumen' => '[DUMMY UJI COBA] B2 - Evaluasi Kepuasan Mahasiswa TI (Ditolak LPM ke GPM, Belum Diteruskan)',
        'nomor_dokumen' => 'DUMMY-FLOW/2026/B2',
        'siklus' => 'evaluasi',
        'tahun_akademik' => '2025/2026',
        'deskripsi' => 'Dokumen dummy skenario B2: SUDAH ditolak LPM ke GPM (revisi_lpm). Catatan LPM HANYA tampil di GPM Tab 1 dan TERTUTUP bagi Prodi (status Prodi: "Sedang Ditelaah GPM").',
        'status_review' => 'revisi_lpm',
        'tahap_review' => 'gpm',
        // PRIVACY CHECK: catatan_review TIDAK berisi catatan LPM, tetap menjaga catatan awal GPM
        'catatan_review' => 'Diajukan GPM FIKOM untuk ditelaah oleh LPM.',
        'gpm_reviewed_by' => $gpmId,
        'gpm_reviewed_at' => $tMinus3h,
        'gpm_catatan' => 'Diajukan GPM FIKOM untuk ditelaah oleh LPM.',
        'lpm_reviewed_by' => $lpmId,
        'lpm_reviewed_at' => $tMinus1h,
        'lpm_catatan' => '[CATATAN INTERNAL LPM -> GPM] Hasil telaah LPM menemukan persentase responden survei kepuasan mahasiswa baru mencapai 24% (standar minimal 30%) dan belum ada lampiran berita acara rapat tinjauan manajemen. Mohon GPM meminta Prodi TI melengkapinya.',
        'reviewed_by' => $lpmId,
        'reviewed_at' => $tMinus1h,
        'created_at' => $tMinus4h,
        'updated_at' => $now,
        'reviews' => [
            [
                'reviewer_id' => $gpmId,
                'reviewer_role' => 'gpm',
                'reviewer_name' => $gpmOfficialName,
                'tahap' => 'gpm',
                'status_sebelumnya' => 'menunggu_review_gpm',
                'status_hasil' => 'menunggu_review_lpm',
                'catatan' => 'Diajukan GPM FIKOM untuk ditelaah oleh LPM.',
                'created_at' => $tMinus3h,
            ],
            [
                'reviewer_id' => $lpmId,
                'reviewer_role' => 'lpm',
                'reviewer_name' => $lpmOfficialName,
                'tahap' => 'lpm',
                'status_sebelumnya' => 'menunggu_review_lpm',
                'status_hasil' => 'revisi_lpm',
                'catatan' => '[CATATAN INTERNAL LPM -> GPM] Hasil telaah LPM menemukan persentase responden survei kepuasan mahasiswa baru mencapai 24% (standar minimal 30%) dan belum ada lampiran berita acara rapat tinjauan manajemen. Mohon GPM meminta Prodi TI melengkapinya.',
                'created_at' => $tMinus1h,
            ],
        ],
        // Belum diteruskan GPM ke Prodi, jadi belum ada record ppepp_document_revisions untuk Prodi
        'revision' => null,
    ],
    [
        'code' => 'B3',
        'level' => 'prodi',
        'prodi_id' => $tiId,
        'fakultas_id' => $fikomId,
        'user_id' => $kaprodiId,
        'bidang_id' => 4,
        'nama_dokumen' => '[DUMMY UJI COBA] B3 - Laporan BKD & Rasio Dosen TI (Sudah Diteruskan GPM ke Prodi)',
        'nomor_dokumen' => 'DUMMY-FLOW/2026/B3',
        'siklus' => 'pengendalian',
        'tahun_akademik' => '2025/2026',
        'deskripsi' => 'Dokumen dummy skenario B3: penolakan LPM SUDAH disintesis & diteruskan GPM ke Prodi (revisi_gpm). Di akun Prodi muncul sebagai arahan murni GPM Fakultas.',
        'status_review' => 'revisi_gpm',
        'tahap_review' => 'gpm',
        'catatan_review' => 'Mohon Program Studi Teknik Informatika melengkapi Lampiran 2 tabel rekap BKD dengan menambahkan kolom rasio mahasiswa bimbingan tugas akhir per dosen tetap sesuai ketentuan standar mutu Fakultas.',
        'gpm_reviewed_by' => $gpmId,
        'gpm_reviewed_at' => $tMinus1h,
        'gpm_catatan' => 'Mohon Program Studi Teknik Informatika melengkapi Lampiran 2 tabel rekap BKD dengan menambahkan kolom rasio mahasiswa bimbingan tugas akhir per dosen tetap sesuai ketentuan standar mutu Fakultas.',
        'lpm_reviewed_by' => $lpmId,
        'lpm_reviewed_at' => $tMinus2h,
        'lpm_catatan' => '[CATATAN INTERNAL LPM] Lampiran 2 rekap BKD belum mencantumkan rasio pembimbingan skripsi per dosen tetap prodi.',
        'reviewed_by' => $lpmId,
        'reviewed_at' => $tMinus2h,
        'created_at' => $tMinus4h,
        'updated_at' => $now,
        'reviews' => [
            [
                'reviewer_id' => $gpmId,
                'reviewer_role' => 'gpm',
                'reviewer_name' => $gpmOfficialName,
                'tahap' => 'gpm',
                'status_sebelumnya' => 'menunggu_review_gpm',
                'status_hasil' => 'menunggu_review_lpm',
                'catatan' => 'Berkas awal BKD Prodi TI diteruskan ke LPM.',
                'created_at' => $tMinus3h,
            ],
            [
                'reviewer_id' => $lpmId,
                'reviewer_role' => 'lpm',
                'reviewer_name' => $lpmOfficialName,
                'tahap' => 'lpm',
                'status_sebelumnya' => 'menunggu_review_lpm',
                'status_hasil' => 'revisi_lpm',
                'catatan' => '[CATATAN INTERNAL LPM] Lampiran 2 rekap BKD belum mencantumkan rasio pembimbingan skripsi per dosen tetap prodi.',
                'created_at' => $tMinus2h,
            ],
            [
                'reviewer_id' => $gpmId,
                'reviewer_role' => 'gpm',
                'reviewer_name' => $gpmOfficialName,
                'tahap' => 'gpm',
                'status_sebelumnya' => 'revisi_lpm',
                'status_hasil' => 'revisi_gpm',
                'catatan' => 'Mohon Program Studi Teknik Informatika melengkapi Lampiran 2 tabel rekap BKD dengan menambahkan kolom rasio mahasiswa bimbingan tugas akhir per dosen tetap sesuai ketentuan standar mutu Fakultas.',
                'created_at' => $tMinus1h,
            ],
        ],
        'revision' => [
            'revisi_ke' => 1,
            'reviewer_id' => $gpmId,
            'reviewer_name' => $gpmOfficialName,
            'reviewer_role' => 'gpm',
            'status_review' => 'revisi_gpm',
            'catatan_review' => 'Mohon Program Studi Teknik Informatika melengkapi Lampiran 2 tabel rekap BKD dengan menambahkan kolom rasio mahasiswa bimbingan tugas akhir per dosen tetap sesuai ketentuan standar mutu Fakultas.',
            'reviewed_at' => $tMinus1h,
        ],
    ],
    [
        'code' => 'B4',
        'level' => 'fakultas',
        'prodi_id' => null,
        'fakultas_id' => $fikomId,
        'user_id' => $dekanId,
        'bidang_id' => 1,
        'nama_dokumen' => '[DUMMY UJI COBA] B4 - Renstra Mutu FIKOM (Ditolak LPM ke GPM, Level Dekanat)',
        'nomor_dokumen' => 'DUMMY-FLOW/2026/B4',
        'siklus' => 'penetapan',
        'tahun_akademik' => '2025/2026',
        'deskripsi' => 'Dokumen dummy skenario B4 (Tingkat Dekanat/Fakultas): dikembalikan LPM ke GPM (revisi_lpm). Tersembunyi bagi Dekan FIKOM sampai diteruskan oleh GPM.',
        'status_review' => 'revisi_lpm',
        'tahap_review' => 'gpm',
        'catatan_review' => 'Telah diperiksa awal oleh GPM Fakultas Ilmu Komputer dan diteruskan ke LPM.',
        'gpm_reviewed_by' => $gpmId,
        'gpm_reviewed_at' => $tMinus3h,
        'gpm_catatan' => 'Telah diperiksa awal oleh GPM Fakultas Ilmu Komputer dan diteruskan ke LPM.',
        'lpm_reviewed_by' => $lpmId,
        'lpm_reviewed_at' => $tMinus1h,
        'lpm_catatan' => '[CATATAN INTERNAL LPM -> GPM] Target IKU publikasi internasional bereputasi pada Renstra FIKOM 2026 perlu diselaraskan kembali dengan Renstra Universitas. Mohon GPM meneruskan poin ini ke Dekanat.',
        'reviewed_by' => $lpmId,
        'reviewed_at' => $tMinus1h,
        'created_at' => $tMinus4h,
        'updated_at' => $now,
        'reviews' => [
            [
                'reviewer_id' => $gpmId,
                'reviewer_role' => 'gpm',
                'reviewer_name' => $gpmOfficialName,
                'tahap' => 'gpm',
                'status_sebelumnya' => 'menunggu_review_gpm',
                'status_hasil' => 'menunggu_review_lpm',
                'catatan' => 'Telah diperiksa awal oleh GPM Fakultas Ilmu Komputer dan diteruskan ke LPM.',
                'created_at' => $tMinus3h,
            ],
            [
                'reviewer_id' => $lpmId,
                'reviewer_role' => 'lpm',
                'reviewer_name' => $lpmOfficialName,
                'tahap' => 'lpm',
                'status_sebelumnya' => 'menunggu_review_lpm',
                'status_hasil' => 'revisi_lpm',
                'catatan' => '[CATATAN INTERNAL LPM -> GPM] Target IKU publikasi internasional bereputasi pada Renstra FIKOM 2026 perlu diselaraskan kembali dengan Renstra Universitas. Mohon GPM meneruskan poin ini ke Dekanat.',
                'created_at' => $tMinus1h,
            ],
        ],
        'revision' => null,
    ],
];

$stmtInsertDoc = $db->prepare("
    INSERT INTO ppepp_documents (
        prodi_id, fakultas_id, level, user_id, bidang_id, sub_bidang_id,
        nama_dokumen, nomor_dokumen, siklus, tahun_akademik,
        tanggal_dokumen, tanggal_berlaku_mulai, tanggal_berlaku_selesai,
        jenis_upload, file_path, file_size, file_extension,
        public_page_limit, can_download_public,
        status_review, tahap_review, catatan_review,
        gpm_reviewed_by, gpm_reviewed_at, gpm_catatan,
        lpm_reviewed_by, lpm_reviewed_at, lpm_catatan,
        reviewed_by, reviewed_at, deskripsi, created_at, updated_at
    ) VALUES (
        :prodi_id, :fakultas_id, :level, :user_id, :bidang_id, :sub_bidang_id,
        :nama_dokumen, :nomor_dokumen, :siklus, :tahun_akademik,
        '2026-11-01', '2025-09-01', '2026-08-31',
        'file', :file_path, :file_size, 'pdf',
        1, 1,
        :status_review, :tahap_review, :catatan_review,
        :gpm_reviewed_by, :gpm_reviewed_at, :gpm_catatan,
        :lpm_reviewed_by, :lpm_reviewed_at, :lpm_catatan,
        :reviewed_by, :reviewed_at, :deskripsi, :created_at, :updated_at
    )
");

$stmtInsertFile = $db->prepare("
    INSERT INTO ppepp_document_files (
        document_id, file_name, file_path, file_size, file_extension,
        folder_name, file_category, narasi, sub_bidang_ids,
        is_page_limited, public_page_limit, can_download_public, sort_order, created_at
    ) VALUES (?, ?, ?, ?, 'pdf', 'Utama', 'file', ?, ?, 1, 1, 1, 0, ?)
");

$stmtInsertSubPivot = $db->prepare("
    INSERT IGNORE INTO ppepp_document_sub_bidang (document_id, sub_bidang_id) VALUES (?, ?)
");

$stmtInsertReview = $db->prepare("
    INSERT INTO ppepp_document_reviews (
        document_id, reviewer_id, reviewer_role, reviewer_name,
        tahap, status_sebelumnya, status_hasil, catatan, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmtInsertRevision = $db->prepare("
    INSERT INTO ppepp_document_revisions (
        document_id, cycle_id, revisi_ke, reviewer_id, reviewer_name, reviewer_role,
        status_review, catatan_review, reviewed_at, created_at
    ) VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?)
");

$db->beginTransaction();
$created = [];

try {
    foreach ($dummies as $item) {
        $subId = $subByBidang[$item['bidang_id']] ?? null;

        $stmtInsertDoc->execute([
            ':prodi_id'       => $item['prodi_id'],
            ':fakultas_id'    => $item['fakultas_id'],
            ':level'          => $item['level'],
            ':user_id'        => $item['user_id'],
            ':bidang_id'      => $item['bidang_id'],
            ':sub_bidang_id'  => $subId,
            ':nama_dokumen'   => $item['nama_dokumen'],
            ':nomor_dokumen'  => $item['nomor_dokumen'],
            ':siklus'         => $item['siklus'],
            ':tahun_akademik' => $item['tahun_akademik'],
            ':file_path'      => $samplePdfRel,
            ':file_size'      => $samplePdfSize,
            ':status_review'  => $item['status_review'],
            ':tahap_review'   => $item['tahap_review'],
            ':catatan_review' => $item['catatan_review'],
            ':gpm_reviewed_by'=> $item['gpm_reviewed_by'],
            ':gpm_reviewed_at'=> $item['gpm_reviewed_at'],
            ':gpm_catatan'    => $item['gpm_catatan'],
            ':lpm_reviewed_by'=> $item['lpm_reviewed_by'],
            ':lpm_reviewed_at'=> $item['lpm_reviewed_at'],
            ':lpm_catatan'    => $item['lpm_catatan'],
            ':reviewed_by'    => $item['reviewed_by'],
            ':reviewed_at'    => $item['reviewed_at'],
            ':deskripsi'      => $item['deskripsi'],
            ':created_at'     => $item['created_at'],
            ':updated_at'     => $item['updated_at'],
        ]);

        $docId = (int)$db->lastInsertId();

        // Attach file record
        $stmtInsertFile->execute([
            $docId,
            "Berkas_{$item['code']}_Uji_Coba_SPMI.pdf",
            $samplePdfRel,
            $samplePdfSize,
            "Berkas lampiran uji coba skenario {$item['code']}: {$item['nama_dokumen']}",
            $subId ? json_encode([$subId]) : null,
            $item['created_at']
        ]);

        if ($subId) {
            $stmtInsertSubPivot->execute([$docId, $subId]);
        }

        // Insert review timeline history
        foreach ($item['reviews'] as $rev) {
            $stmtInsertReview->execute([
                $docId,
                $rev['reviewer_id'],
                $rev['reviewer_role'],
                $rev['reviewer_name'],
                $rev['tahap'],
                $rev['status_sebelumnya'],
                $rev['status_hasil'],
                $rev['catatan'],
                $rev['created_at']
            ]);
        }

        // Insert revision inbox row jika sudah diteruskan ke unit / disetujui LPM
        if (!empty($item['revision'])) {
            $rv = $item['revision'];
            $stmtInsertRevision->execute([
                $docId,
                $rv['revisi_ke'],
                $rv['reviewer_id'],
                $rv['reviewer_name'],
                $rv['reviewer_role'],
                $rv['status_review'],
                $rv['catatan_review'],
                $rv['reviewed_at'],
                $rv['reviewed_at'],
            ]);
        }

        $created[] = [
            'code'   => $item['code'],
            'id'     => $docId,
            'level'  => $item['level'],
            'status' => $item['status_review'],
            'nama'   => $item['nama_dokumen'],
        ];
    }

    $db->commit();
    echo "[BERHASIL] " . count($created) . " dokumen dummy uji coba berhasil dimasukkan:\n\n";
    foreach ($created as $c) {
        echo sprintf("  [%s] ID=%-5d | %-10s | %-20s | %s\n", $c['code'], $c['id'], strtoupper($c['level']), $c['status'], $c['nama']);
    }
    echo "\nFile PDF lampiran yang dipakai: {$samplePdfRel} ({$samplePdfSize})\n";
    echo "Scope Unit: {$fikomName} (ID {$fikomId}) & Prodi {$tiName} (ID {$tiId})\n";
} catch (\Throwable $e) {
    $db->rollBack();
    fwrite(STDERR, "[ERROR] Gagal membuat dummy data: " . $e->getMessage() . "\n");
    exit(1);
}

