<?php
/**
 * Script Idempoten Optimasi Index Performa Database
 * Menjalankan penambahan 11 indeks pada ppepp_documents, ppepp_document_files, dan ppepp_cycles
 * dengan aman (melewati indeks yang sudah terpasang sehingga bebas error Duplicate key).
 *
 * Cara menjalankan: php database/apply_performance_indexes.php
 */

require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

echo "========================================================\n";
echo "  OPTIMASI INDEX PERFORMA DATABASE SPMI PPEPP\n";
echo "========================================================\n";
echo "Server DB: " . $db->query("SELECT VERSION()")->fetchColumn() . "\n\n";

$indexesToAdd = [
    'ppepp_documents' => [
        'idx_docs_filter_status'    => '(`deleted_at`, `status_review`)',
        'idx_docs_prodi_filter'     => '(`prodi_id`, `deleted_at`, `status_review`, `siklus`)',
        'idx_docs_fakultas_filter'  => '(`fakultas_id`, `level`, `deleted_at`, `status_review`)',
        'idx_docs_siklus_filter'    => '(`siklus`, `deleted_at`, `status_review`)',
        'idx_docs_tahun'            => '(`tahun_akademik`, `deleted_at`)',
        'idx_docs_created_at'       => '(`created_at`)',
    ],
    'ppepp_document_files' => [
        'idx_docfiles_doc_sort'     => '(`document_id`, `sort_order`)',
        'idx_docfiles_cat'          => '(`file_category`)',
    ],
    'ppepp_cycles' => [
        'idx_cycles_prodi'          => '(`prodi_id`, `deleted_at`)',
        'idx_cycles_fakultas'       => '(`fakultas_id`, `level`, `deleted_at`)',
        'idx_cycles_bidang'         => '(`bidang_id`, `sub_bidang_id`)',
    ],
];

$added = 0;
$skipped = 0;
$errors = 0;

foreach ($indexesToAdd as $table => $idxList) {
    try {
        $existing = $db->query("SHOW INDEX FROM `{$table}`")->fetchAll(PDO::FETCH_COLUMN, 2);
    } catch (\Throwable $e) {
        echo "[ERR]  Tabel `{$table}` tidak dapat diakses: " . $e->getMessage() . "\n";
        $errors++;
        continue;
    }

    $existingSet = array_flip($existing);
    foreach ($idxList as $idxName => $cols) {
        if (isset($existingSet[$idxName])) {
            echo "[SKIP] {$table}.{$idxName} sudah terpasang.\n";
            $skipped++;
            continue;
        }
        $sql = "ALTER TABLE `{$table}` ADD INDEX `{$idxName}` {$cols}";
        try {
            $db->exec($sql);
            echo "[OK]   Ditambahkan: {$table}.{$idxName} {$cols}\n";
            $added++;
        } catch (\Throwable $e) {
            echo "[ERR]  Gagal pada {$table}.{$idxName}: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
}

echo "\nRingkasan: {$added} ditambahkan, {$skipped} sudah ada, {$errors} gagal.\n";
exit($errors > 0 ? 1 : 0);
