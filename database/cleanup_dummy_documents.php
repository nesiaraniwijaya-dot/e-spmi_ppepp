<?php
/**
 * Script Pembersihan Data Dummy Dokumen PPEPP
 * SPMI PPEPP UNIKA Soegijapranata
 *
 * Menghapus seluruh dokumen dummy, berkas fisik unggahan, siklus, ulasan,
 * dan riwayat terkait dokumen pada seluruh fakultas dan prodi.
 */

require_once __DIR__ . '/../config/database.php';

echo "========================================================\n";
echo "   PEMBERSIHAN DATA DUMMY DOKUMEN PPEPP (SEMUA FAKULTAS & PRODI)\n";
echo "========================================================\n\n";

$db = Database::getInstance();
$pdo = $db->getConnection();

// 1. Buat direktori backup sebelum penghapusan
$timestamp = date('Ymd_His');
$backupDir = __DIR__ . '/backups/backup_dummy_' . $timestamp;
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}
echo "[1] Membuat folder backup di:\n    {$backupDir}\n";

// Backup data tabel ke file JSON
$tablesToBackup = [
    'ppepp_documents',
    'ppepp_document_files',
    'ppepp_document_sub_bidang',
    'ppepp_document_file_sub_bidang',
    'ppepp_document_reviews',
    'ppepp_document_revisions',
    'ppepp_cycles',
    'upload_access_requests'
];

$backupSummary = [];
foreach ($tablesToBackup as $table) {
    try {
        $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
        file_put_contents($backupDir . "/{$table}.json", json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $backupSummary[$table] = count($rows);
        echo "    - Backup tabel `{$table}`: " . count($rows) . " baris tersimpan.\n";
    } catch (\Throwable $e) {
        echo "    ! Peringatan backup tabel `{$table}`: " . $e->getMessage() . "\n";
    }
}

// 2. Backup file fisik di uploads/documents
$uploadsDocDir = realpath(__DIR__ . '/../uploads/documents');
$backedUpFilesCount = 0;
if ($uploadsDocDir && is_dir($uploadsDocDir)) {
    $backupFilesDir = $backupDir . '/uploads_documents';
    if (!is_dir($backupFilesDir)) {
        mkdir($backupFilesDir, 0777, true);
    }
    $scannedFiles = scandir($uploadsDocDir);
    foreach ($scannedFiles as $file) {
        if ($file === '.' || $file === '..') continue;
        $srcFile = $uploadsDocDir . DIRECTORY_SEPARATOR . $file;
        if (is_file($srcFile)) {
            copy($srcFile, $backupFilesDir . DIRECTORY_SEPARATOR . $file);
            $backedUpFilesCount++;
        }
    }
}
echo "    - Backup berkas fisik: {$backedUpFilesCount} file tersalin ke backup.\n\n";

// 3. Eksekusi Penghapusan Data Dummy di Database dalam Transaksi
echo "[2] Menghapus data dummy dokumen dari database...\n";
$pdo->beginTransaction();

try {
    // Nonaktifkan foreign key checks sementara untuk pembersihan bersih
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Hapus junction tables sub bidang
    $delDfs = $pdo->exec("DELETE FROM `ppepp_document_file_sub_bidang`");
    $delDs  = $pdo->exec("DELETE FROM `ppepp_document_sub_bidang`");
    echo "    - Dihapus ppepp_document_file_sub_bidang: {$delDfs} baris\n";
    echo "    - Dihapus ppepp_document_sub_bidang: {$delDs} baris\n";

    // Hapus ulasan dan revisi
    $delRev = $pdo->exec("DELETE FROM `ppepp_document_reviews`");
    $delRvs = $pdo->exec("DELETE FROM `ppepp_document_revisions`");
    echo "    - Dihapus ppepp_document_reviews: {$delRev} baris\n";
    echo "    - Dihapus ppepp_document_revisions: {$delRvs} baris\n";

    // Hapus berkas dokumen
    $delFiles = $pdo->exec("DELETE FROM `ppepp_document_files`");
    echo "    - Dihapus ppepp_document_files: {$delFiles} baris\n";

    // Hapus dokumen utama
    $delDocs = $pdo->exec("DELETE FROM `ppepp_documents`");
    echo "    - Dihapus ppepp_documents: {$delDocs} baris\n";

    // Hapus siklus ppepp dummy
    $delCyc = $pdo->exec("DELETE FROM `ppepp_cycles`");
    echo "    - Dihapus ppepp_cycles: {$delCyc} baris\n";

    // Hapus upload access requests uji coba
    $delUar = $pdo->exec("DELETE FROM `upload_access_requests`");
    echo "    - Dihapus upload_access_requests: {$delUar} baris\n";

    // Hapus log audit trail terkait dokumen dummy agar monitoring statistik bersih
    $delAudit = $pdo->exec("
        DELETE FROM `audit_logs` 
        WHERE `modul` IN (
            'Dokumen PPEPP', 
            'Dokumen PPEPP GPM', 
            'Review Dokumen GPM', 
            'Review Dokumen PPEPP', 
            'Permohonan Akses Upload', 
            'Persetujuan Akses Upload'
        )
    ");
    echo "    - Dihapus audit_logs (modul dokumen dummy): {$delAudit} baris\n";

    // Reset AUTO_INCREMENT ke 1
    $tablesToReset = [
        'ppepp_documents',
        'ppepp_document_files',
        'ppepp_document_sub_bidang',
        'ppepp_document_file_sub_bidang',
        'ppepp_document_reviews',
        'ppepp_document_revisions',
        'ppepp_cycles',
        'upload_access_requests'
    ];
    foreach ($tablesToReset as $tr) {
        $pdo->exec("ALTER TABLE `{$tr}` AUTO_INCREMENT = 1;");
    }
    echo "    - Reset AUTO_INCREMENT ke 1 untuk seluruh tabel dokumen.\n";

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    echo "[OK] Penghapusan data database selesai.\n\n";
} catch (\Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "[ERROR] Terjadi kesalahan saat menghapus data: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. Hapus berkas fisik di uploads/documents
echo "[3] Menghapus berkas fisik di uploads/documents/...\n";
$deletedFilesCount = 0;
if ($uploadsDocDir && is_dir($uploadsDocDir)) {
    $scannedFiles = scandir($uploadsDocDir);
    foreach ($scannedFiles as $file) {
        if ($file === '.' || $file === '..') continue;
        $filePath = $uploadsDocDir . DIRECTORY_SEPARATOR . $file;
        if (is_file($filePath)) {
            if (unlink($filePath)) {
                $deletedFilesCount++;
            }
        }
    }
}
echo "    - Berhasil menghapus {$deletedFilesCount} file fisik dokumen dummy.\n\n";

// 5. Verifikasi Keadaan Akhir
echo "[4] Verifikasi status akhir database & direktori:\n";
foreach ($tablesToBackup as $table) {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    echo "    - Tabel `{$table}`: {$count} data\n";
}
$remainingFiles = 0;
if ($uploadsDocDir && is_dir($uploadsDocDir)) {
    foreach (scandir($uploadsDocDir) as $f) {
        if ($f !== '.' && $f !== '..') $remainingFiles++;
    }
}
echo "    - Sisa berkas di uploads/documents: {$remainingFiles} file\n\n";

echo "========================================================\n";
echo "   SEMUA DATA DUMMY DOKUMEN TELAH BERHASIL DIHAPUS!\n";
echo "   Sistem kini siap untuk penginputan dokumen asli.\n";
echo "========================================================\n";
