<?php
/**
 * Skrip Migrasi Terpadu (Idempoten & Aman untuk Kolaborator)
 * Menjalankan pembaruan skema database terbaru secara aman tanpa menghapus data dokumen.
 *
 * Cara menjalankan setelah git pull:
 *   php database/migrate_all.php
 */

echo "========================================================\n";
echo "  SINKRONISASI DATABASE SPMI PPEPP (IDEMPOTEN)\n";
echo "========================================================\n\n";

$steps = [
    'Update ENUM ppepp_documents.jenis_upload (file/link/kombinasi)' => __DIR__ . '/migration_multi_upload_kombinasi.php',
    'Pastikan tabel gpm_evaluations tersedia'                         => __DIR__ . '/migration_gpm_evaluations.php',
    'Terapkan 11 indeks performa query'                               => __DIR__ . '/apply_performance_indexes.php',
    'Sinkronisasi 6 role resmi LPM pada users'                        => __DIR__ . '/migration_lpm_roles.php',
];

$phpBin = PHP_BINARY ?: 'php';
$failed = false;

foreach ($steps as $label => $scriptPath) {
    echo "--> {$label}\n";
    if (!file_exists($scriptPath)) {
        fwrite(STDERR, "    [ERR] File tidak ditemukan: {$scriptPath}\n");
        $failed = true;
        continue;
    }
    $cmd = escapeshellarg($phpBin) . ' ' . escapeshellarg($scriptPath);
    passthru($cmd, $exitCode);
    if ($exitCode !== 0) {
        fwrite(STDERR, "    [ERR] Langkah gagal dengan kode keluar {$exitCode}\n");
        $failed = true;
    }
    echo "\n";
}

if ($failed) {
    fwrite(STDERR, "Sinkronisasi selesai dengan peringatan/error. Periksa log di atas.\n");
    exit(1);
}

echo "[DONE] Seluruh pembaruan skema database berhasil disinkronkan.\n";
exit(0);

