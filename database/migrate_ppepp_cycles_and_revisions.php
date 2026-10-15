<?php
/**
 * Migration Script: PPEPP Cycles & Revision History
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "=== MEMULAI MIGRASI SIKLUS PPEPP & RIWAYAT REVISI ===\n\n";

    // 1. Buat Tabel ppepp_cycles
    echo "1. Memeriksa / Membuat tabel `ppepp_cycles`...\n";
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
    echo "   -> Tabel `ppepp_cycles` siap.\n";

    // 2. Tambah kolom `cycle_id` ke `ppepp_documents` jika belum ada
    echo "2. Memeriksa kolom `cycle_id` pada `ppepp_documents`...\n";
    $cols = $db->query("SHOW COLUMNS FROM `ppepp_documents` LIKE 'cycle_id'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `cycle_id` INT NULL AFTER `sub_bidang_id`, ADD INDEX `idx_cycle_id` (`cycle_id`);");
        echo "   -> Kolom `cycle_id` berhasil ditambahkan ke `ppepp_documents`.\n";
    } else {
        echo "   -> Kolom `cycle_id` sudah ada.\n";
    }

    // 3. Buat Tabel ppepp_document_revisions
    echo "3. Memeriksa / Membuat tabel `ppepp_document_revisions`...\n";
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
    echo "   -> Tabel `ppepp_document_revisions` siap.\n";

    // 4. Sinkronisasi dokumen yang sudah ada ke `ppepp_cycles`
    echo "4. Menghubungkan dokumen existing ke `ppepp_cycles`...\n";
    $docsWithoutCycle = $db->query("SELECT * FROM `ppepp_documents` WHERE `cycle_id` IS NULL AND `deleted_at` IS NULL ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $cycleCount = 0;
    foreach ($docsWithoutCycle as $doc) {
        // Ambil judul siklus dari nama dokumen
        $judul = trim($doc['nama_dokumen']);
        // Cek apakah cycle dengan judul, prodi, fakultas serupa sudah dibuat
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
            $cycleCount++;
        }

        $db->prepare("UPDATE `ppepp_documents` SET `cycle_id` = ? WHERE `id` = ?")->execute([$cycleId, $doc['id']]);
    }
    echo "   -> Selesai! Sebanyak {$cycleCount} siklus dibuat dan dokumen berhasil dihubungkan.\n";

    // 5. Migrasi data riwayat review dari audit_logs ke ppepp_document_revisions
    echo "5. Migrasi log review lampau dari `audit_logs`...\n";
    $auditReviews = $db->query("
        SELECT * FROM `audit_logs` 
        WHERE `modul` = 'Review Dokumen PPEPP' AND `target_id` IS NOT NULL 
        ORDER BY id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $revLogCount = 0;
    foreach ($auditReviews as $log) {
        $docId = (int)$log['target_id'];
        $newVals = json_decode($log['new_values'] ?? '{}', true) ?: [];
        $statusRev = $newVals['status_review'] ?? 'perlu_perbaikan';
        $catatanRev = $newVals['catatan_review'] ?? '';
        $reviewerName = !empty($newVals['verifikator']) ? $newVals['verifikator'] : (!empty($newVals['reviewer']) ? $newVals['reviewer'] : $log['user_name']);
        $reviewerRole = $log['user_role'] ?? 'super_admin';
        $reviewedAt = $log['created_at'];

        // Cek apakah sudah pernah dimasukkan
        $cek = $db->prepare("SELECT id FROM `ppepp_document_revisions` WHERE `document_id` = ? AND `reviewed_at` = ? LIMIT 1");
        $cek->execute([$docId, $reviewedAt]);
        if (!$cek->fetchColumn()) {
            // Ambil info cycle_id dokumen
            $docInfo = $db->prepare("SELECT cycle_id, user_id FROM `ppepp_documents` WHERE id = ?");
            $docInfo->execute([$docId]);
            $docRow = $docInfo->fetch(PDO::FETCH_ASSOC);
            $cycleId = $docRow['cycle_id'] ?? null;

            // Cari apakah ada perbaikan setelahnya oleh prodi
            $nextFix = $db->prepare("
                SELECT * FROM `audit_logs` 
                WHERE `modul` = 'Dokumen PPEPP' AND `target_id` = ? AND `created_at` > ? 
                ORDER BY id ASC LIMIT 1
            ");
            $nextFix->execute([(string)$docId, $reviewedAt]);
            $fixRow = $nextFix->fetch(PDO::FETCH_ASSOC);

            $uploaderName = $fixRow ? $fixRow['user_name'] : null;
            $uploaderRole = $fixRow ? $fixRow['user_role'] : null;
            $revisedAt = $fixRow ? $fixRow['created_at'] : null;

            $revNumber = (int)$db->query("SELECT COUNT(*) FROM `ppepp_document_revisions` WHERE `document_id` = {$docId}")->fetchColumn() + 1;

            $insRev = $db->prepare("
                INSERT INTO `ppepp_document_revisions`
                (document_id, cycle_id, revisi_ke, reviewer_id, reviewer_name, reviewer_role, status_review, catatan_review, reviewed_at, uploader_name, uploader_role, revised_at, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insRev->execute([
                $docId,
                $cycleId,
                $revNumber,
                $log['user_id'] ?? null,
                $reviewerName,
                $reviewerRole,
                $statusRev,
                $catatanRev,
                $reviewedAt,
                $uploaderName,
                $uploaderRole,
                $revisedAt,
                $reviewedAt
            ]);
            $revLogCount++;
        }
    }
    echo "   -> Sebanyak {$revLogCount} riwayat review berhasil disinkronkan.\n\n";

    echo "=== MIGRASI SELESAI DENGAN SUKSES! ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
