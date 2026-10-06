<?php
/**
 * Migration: Multi Sub-Standar Per File & Link Dokumen
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

echo "=== MEMULAI MIGRASI MULTI SUB-STANDAR PER BERKAS / LINK ===\n";

try {
    $pdo = Database::getInstance()->getConnection();

    // 1. Tambah kolom `sub_bidang_ids` pada tabel `ppepp_document_files` jika belum ada
    echo "1. Memeriksa kolom sub_bidang_ids pada tabel ppepp_document_files...\n";
    $colsFiles = $pdo->query("SHOW COLUMNS FROM ppepp_document_files LIKE 'sub_bidang_ids'")->fetchAll();
    if (empty($colsFiles)) {
        $pdo->exec("ALTER TABLE `ppepp_document_files` ADD COLUMN `sub_bidang_ids` TEXT NULL AFTER `narasi`");
        echo "   [OK] Kolom sub_bidang_ids berhasil ditambahkan ke ppepp_document_files.\n";
    } else {
        echo "   [SKIP] Kolom sub_bidang_ids sudah ada di ppepp_document_files.\n";
    }

    // 2. Buat tabel pivot `ppepp_document_file_sub_bidang`
    echo "2. Membuat tabel pivot ppepp_document_file_sub_bidang...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `ppepp_document_file_sub_bidang` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `document_file_id` INT NOT NULL,
            `sub_bidang_id` INT NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_file_sub` (`document_file_id`, `sub_bidang_id`),
            KEY `fk_dfs_file` (`document_file_id`),
            KEY `fk_dfs_sub` (`sub_bidang_id`),
            CONSTRAINT `fk_dfs_file` FOREIGN KEY (`document_file_id`) REFERENCES `ppepp_document_files`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_dfs_sub` FOREIGN KEY (`sub_bidang_id`) REFERENCES `sub_bidang_standar`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "   [OK] Tabel ppepp_document_file_sub_bidang siap.\n";

    // 3. Buat tabel pivot `ppepp_document_sub_bidang` (untuk dokumen induk)
    echo "3. Membuat tabel pivot ppepp_document_sub_bidang...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `ppepp_document_sub_bidang` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `document_id` INT NOT NULL,
            `sub_bidang_id` INT NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_doc_sub` (`document_id`, `sub_bidang_id`),
            KEY `fk_ds_doc` (`document_id`),
            KEY `fk_ds_sub` (`sub_bidang_id`),
            CONSTRAINT `fk_ds_doc` FOREIGN KEY (`document_id`) REFERENCES `ppepp_documents`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_ds_sub` FOREIGN KEY (`sub_bidang_id`) REFERENCES `sub_bidang_standar`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "   [OK] Tabel ppepp_document_sub_bidang siap.\n";

    // 4. Backfill data eksisting: ppepp_documents -> ppepp_document_sub_bidang
    echo "4. Migrasi data sub standar eksisting ke ppepp_document_sub_bidang...\n";
    $stmtDocs = $pdo->query("SELECT id, sub_bidang_id FROM ppepp_documents WHERE sub_bidang_id IS NOT NULL AND sub_bidang_id > 0");
    $docs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);
    $stmtInsDocSub = $pdo->prepare("INSERT IGNORE INTO ppepp_document_sub_bidang (document_id, sub_bidang_id) VALUES (?, ?)");
    $migratedDocSubs = 0;
    foreach ($docs as $d) {
        $stmtInsDocSub->execute([$d['id'], $d['sub_bidang_id']]);
        $migratedDocSubs++;
    }
    echo "   [OK] Migrasi {$migratedDocSubs} data sub standar dokumen ke tabel pivot selesai.\n";

    // 5. Backfill data eksisting ke ppepp_document_files & ppepp_document_file_sub_bidang
    echo "5. Migrasi data sub standar ke berkas fisik ppepp_document_files...\n";
    $stmtFiles = $pdo->query("
        SELECT f.id, f.document_id, f.sub_bidang_ids, d.sub_bidang_id as parent_sub_id 
        FROM ppepp_document_files f
        LEFT JOIN ppepp_documents d ON f.document_id = d.id
    ");
    $allFiles = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);
    $stmtUpdFile = $pdo->prepare("UPDATE ppepp_document_files SET sub_bidang_ids = ? WHERE id = ?");
    $stmtInsFileSub = $pdo->prepare("INSERT IGNORE INTO ppepp_document_file_sub_bidang (document_file_id, sub_bidang_id) VALUES (?, ?)");
    $migratedFileSubs = 0;

    foreach ($allFiles as $f) {
        if (!empty($f['parent_sub_id']) && (empty($f['sub_bidang_ids']) || $f['sub_bidang_ids'] === 'null' || $f['sub_bidang_ids'] === '[]')) {
            $subArr = [(int)$f['parent_sub_id']];
            $stmtUpdFile->execute([json_encode($subArr), $f['id']]);
            $stmtInsFileSub->execute([$f['id'], (int)$f['parent_sub_id']]);
            $migratedFileSubs++;
        }
    }
    echo "   [OK] Migrasi {$migratedFileSubs} berkas lampiran dengan sub standar bawaan selesai.\n";

    // 6. Backfill sub standar pada tautan Google Drive eksisting
    echo "6. Memeriksa tautan Google Drive eksisting...\n";
    $stmtLinks = $pdo->query("SELECT id, external_link, sub_bidang_id FROM ppepp_documents WHERE external_link IS NOT NULL AND external_link != ''");
    $linkDocs = $stmtLinks->fetchAll(PDO::FETCH_ASSOC);
    $stmtUpdLink = $pdo->prepare("UPDATE ppepp_documents SET external_link = ? WHERE id = ?");
    $updatedLinks = 0;

    foreach ($linkDocs as $ld) {
        $raw = trim($ld['external_link']);
        if (str_starts_with($raw, '[') || str_starts_with($raw, '{')) {
            $arr = json_decode($raw, true);
            if (is_array($arr)) {
                $changed = false;
                foreach ($arr as &$lItem) {
                    if (is_array($lItem) && !isset($lItem['sub_bidang_ids'])) {
                        $lItem['sub_bidang_ids'] = !empty($ld['sub_bidang_id']) ? [(int)$ld['sub_bidang_id']] : [];
                        $changed = true;
                    }
                }
                if ($changed) {
                    $newJson = json_encode($arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $stmtUpdLink->execute([$newJson, $ld['id']]);
                    $updatedLinks++;
                }
            }
        }
    }
    echo "   [OK] Diperbarui {$updatedLinks} dokumen dengan tautan Google Drive.\n";

    echo "=== MIGRASI MULTI SUB-STANDAR SELESAI DENGAN SUKSES ===\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
