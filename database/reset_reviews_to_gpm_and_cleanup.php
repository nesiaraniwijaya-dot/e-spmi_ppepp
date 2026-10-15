<?php
/**
 * Script Pembaruan Database:
 * 1. Menyiapkan tabel ppepp_cycles & ppepp_document_revisions jika belum ada
 * 2. Memastikan kolom cycle_id ada pada ppepp_documents
 * 3. Mengatur seluruh dokumen mutu yang telah diunggah ke status 'menunggu_review_gpm' (tahap 'gpm')
 * 4. Menghapus riwayat review lama LPM (dan data review uji coba) dari ppepp_document_reviews & ppepp_document_revisions
 * 
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "=== MEMULAI PEMBARUAN STATUS REVIEW GPM & PEMBERSIHAN DATA LAMA ===\n\n";

    // 1. Pastikan tabel ppepp_cycles ada
    echo "1. Memastikan tabel `ppepp_cycles`...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS `ppepp_cycles` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `judul_siklus` VARCHAR(255) NOT NULL,
            `level` ENUM('prodi', 'fakultas') DEFAULT 'prodi',
            `prodi_id` INT NULL,
            `fakultas_id` INT NULL,
            `bidang_id` INT NULL,
            `sub_bidang_id` INT NULL,
            `tahun_akademik` VARCHAR(20) NULL,
            `deskripsi` TEXT NULL,
            `created_by` INT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` DATETIME NULL,
            INDEX `idx_prodi_cycle` (`prodi_id`),
            INDEX `idx_fakultas_cycle` (`fakultas_id`),
            INDEX `idx_level_cycle` (`level`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "   -> Tabel `ppepp_cycles` terverifikasi.\n";

    // 2. Pastikan kolom cycle_id pada ppepp_documents
    echo "2. Memeriksa kolom `cycle_id` di `ppepp_documents`...\n";
    $cols = $db->query("SHOW COLUMNS FROM `ppepp_documents` LIKE 'cycle_id'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `cycle_id` INT NULL AFTER `sub_bidang_id`, ADD INDEX `idx_cycle_id` (`cycle_id`);");
        echo "   -> Kolom `cycle_id` berhasil ditambahkan.\n";
    } else {
        echo "   -> Kolom `cycle_id` sudah ada.\n";
    }

    // 3. Pastikan tabel ppepp_document_revisions
    echo "3. Memastikan tabel `ppepp_document_revisions`...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS `ppepp_document_revisions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `document_id` INT NOT NULL,
            `cycle_id` INT NULL,
            `revisi_ke` INT DEFAULT 1,
            `reviewer_id` INT NULL,
            `reviewer_name` VARCHAR(150) NULL,
            `reviewer_role` VARCHAR(50) NULL,
            `status_review` ENUM('perlu_perbaikan', 'sesuai', 'belum_direview', 'sudah_diperbaiki') DEFAULT 'perlu_perbaikan',
            `catatan_review` TEXT NULL,
            `reviewed_at` DATETIME NULL,
            `uploader_id` INT NULL,
            `uploader_name` VARCHAR(150) NULL,
            `uploader_role` VARCHAR(50) NULL,
            `catatan_perbaikan` TEXT NULL,
            `file_snapshot` VARCHAR(255) NULL,
            `revised_at` DATETIME NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_doc_revision` (`document_id`),
            INDEX `idx_cycle_revision` (`cycle_id`),
            INDEX `idx_reviewed_at` (`reviewed_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "   -> Tabel `ppepp_document_revisions` terverifikasi.\n";

    // 4. Hapus data review lama LPM dan log riwayat review
    echo "4. Menghapus riwayat review lama LPM dari `ppepp_document_reviews` & `ppepp_document_revisions`...\n";
    $delReviews = $db->exec("DELETE FROM `ppepp_document_reviews`");
    echo "   -> Dihapus {$delReviews} entri dari `ppepp_document_reviews`.\n";
    $delRevisions = $db->exec("DELETE FROM `ppepp_document_revisions`");
    echo "   -> Dihapus {$delRevisions} entri dari `ppepp_document_revisions`.\n";

    // 5. Update seluruh dokumen yang telah diunggah menjadi 'menunggu_review_gpm' & tahap 'gpm'
    echo "5. Mengatur ulang seluruh dokumen yang telah diunggah ke antrean review GPM...\n";
    $updateStmt = $db->prepare("
        UPDATE `ppepp_documents`
        SET `status_review` = 'menunggu_review_gpm',
            `tahap_review` = 'gpm',
            `catatan_review` = NULL,
            `gpm_reviewed_by` = NULL,
            `gpm_reviewed_at` = NULL,
            `gpm_catatan` = NULL,
            `lpm_reviewed_by` = NULL,
            `lpm_reviewed_at` = NULL,
            `lpm_catatan` = NULL,
            `reviewed_by` = NULL,
            `reviewed_at` = NULL,
            `updated_at` = NOW()
        WHERE `status_review` != 'draft' AND `deleted_at` IS NULL
    ");
    $updateStmt->execute();
    $affectedDocs = $updateStmt->rowCount();
    echo "   -> Sebanyak {$affectedDocs} dokumen berhasil diatur statusnya menjadi 'menunggu_review_gpm' (Tahap GPM).\n";

    // Hubungkan dokumen ke cycle jika belum memiliki cycle_id
    $docsWithoutCycle = $db->query("SELECT * FROM `ppepp_documents` WHERE `cycle_id` IS NULL AND `deleted_at` IS NULL ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $newCycles = 0;
    foreach ($docsWithoutCycle as $doc) {
        $judul = trim($doc['nama_dokumen']);
        $checkStmt = $db->prepare("SELECT id FROM `ppepp_cycles` WHERE judul_siklus = ? AND (prodi_id = ? OR (prodi_id IS NULL AND ? IS NULL)) AND (fakultas_id = ? OR (fakultas_id IS NULL AND ? IS NULL)) LIMIT 1");
        $checkStmt->execute([$judul, $doc['prodi_id'], $doc['prodi_id'], $doc['fakultas_id'], $doc['fakultas_id']]);
        $existingCycleId = $checkStmt->fetchColumn();

        if ($existingCycleId) {
            $cycleId = (int)$existingCycleId;
        } else {
            $insStmt = $db->prepare("
                INSERT INTO `ppepp_cycles` 
                (judul_siklus, level, prodi_id, fakultas_id, bidang_id, sub_bidang_id, tahun_akademik, deskripsi, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insStmt->execute([
                $judul,
                $doc['level'] ?? 'prodi',
                $doc['prodi_id'] ?? null,
                $doc['fakultas_id'] ?? null,
                $doc['bidang_id'] ?? null,
                $doc['sub_bidang_id'] ?? null,
                $doc['tahun_akademik'] ?? null,
                $doc['deskripsi'] ?? null,
                $doc['user_id'] ?? null,
                $doc['created_at'] ?? date('Y-m-d H:i:s')
            ]);
            $cycleId = (int)$db->lastInsertId();
            $newCycles++;
        }
        $db->prepare("UPDATE `ppepp_documents` SET `cycle_id` = ? WHERE `id` = ?")->execute([$cycleId, $doc['id']]);
    }
    if ($newCycles > 0) {
        echo "   -> {$newCycles} siklus baru dibuat dan dihubungkan ke dokumen.\n";
    }

    echo "\n=== PROSES SELESAI DENGAN SUKSES! ===\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
