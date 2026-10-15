<?php
/**
 * Migrasi Alur Review Dokumen 2-Tingkat (Prodi/Fakultas -> GPM -> LPM)
 * SPMI PPEPP UNIKA Soegijapranata
 *
 * Pembaruan:
 * 1. Tabel `ppepp_document_reviews` untuk mencatat riwayat/log seluruh catatan revisi per dokumen.
 * 2. Penambahan status baru pada enum `status_review` di `ppepp_documents`:
 *    - `menunggu_review_gpm`: Dokumen baru diunggah / perbaikan diajukan, menunggu telaah GPM.
 *    - `revisi_gpm`: GPM meminta perbaikan kepada Prodi/Fakultas.
 *    - `menunggu_review_lpm`: GPM menyetujui & meneruskan ke antrean review LPM.
 *    - `revisi_lpm`: LPM meminta perbaikan kepada Prodi/Fakultas (GPM mendapat tembusan).
 *    - `sudah_diperbaiki`: Prodi/Fakultas telah mengirimkan perbaikan.
 *    - `sesuai`: Disahkan oleh LPM (Final SPMI).
 *    - `draft`: Draf dokumen.
 * 3. Kolom pendukung di `ppepp_documents`:
 *    - `tahap_review` ENUM('draft', 'gpm', 'lpm', 'selesai')
 *    - `gpm_reviewed_by`, `gpm_reviewed_at`, `gpm_catatan`
 *    - `lpm_reviewed_by`, `lpm_reviewed_at`, `lpm_catatan`
 */

require_once __DIR__ . '/../config/database.php';

echo "=== MEMULAI MIGRASI ALUR REVIEW GPM & LPM ===\n";

$db = Database::getInstance();
$pdo = $db->getConnection();

try {
    // 1. Buat tabel `ppepp_document_reviews`
    echo "1. Memeriksa / Membuat tabel `ppepp_document_reviews`...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `ppepp_document_reviews` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `document_id` INT NOT NULL,
            `reviewer_id` INT NULL,
            `reviewer_role` ENUM('gpm', 'lpm', 'super_admin', 'kepala_lpm', 'admin_prodi', 'admin_fakultas') NOT NULL,
            `reviewer_name` VARCHAR(150) NULL,
            `tahap` ENUM('gpm', 'lpm', 'resubmission') NOT NULL,
            `status_sebelumnya` VARCHAR(50) NULL,
            `status_hasil` VARCHAR(50) NOT NULL,
            `catatan` TEXT NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_pdr_doc` (`document_id`),
            INDEX `idx_pdr_reviewer` (`reviewer_id`),
            CONSTRAINT `fk_pdr_doc` FOREIGN KEY (`document_id`) REFERENCES `ppepp_documents` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_pdr_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "   [OK] Tabel `ppepp_document_reviews` siap.\n";

    // 2. Modifikasi kolom enum `status_review` di `ppepp_documents`
    echo "2. Memperbarui kolom `status_review` pada `ppepp_documents`...\n";
    $pdo->exec("
        ALTER TABLE `ppepp_documents` 
        MODIFY COLUMN `status_review` ENUM(
            'draft',
            'menunggu_review_gpm',
            'revisi_gpm',
            'menunggu_review_lpm',
            'revisi_lpm',
            'sudah_diperbaiki',
            'sesuai',
            'belum_direview',
            'perlu_perbaikan'
        ) NOT NULL DEFAULT 'menunggu_review_gpm'
    ");
    echo "   [OK] Enum `status_review` berhasil diperluas.\n";

    // 3. Tambah kolom pelacak tahap & verifikator GPM & LPM
    echo "3. Memeriksa kolom pelacak tahap di `ppepp_documents`...\n";
    
    // tahap_review
    $chkCol = $pdo->query("SHOW COLUMNS FROM `ppepp_documents` LIKE 'tahap_review'")->fetch();
    if (!$chkCol) {
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `tahap_review` ENUM('draft', 'gpm', 'lpm', 'selesai') NOT NULL DEFAULT 'gpm' AFTER `status_review`");
        echo "   [OK] Kolom `tahap_review` ditambahkan.\n";
    }

    // gpm_reviewed_by
    $chkCol = $pdo->query("SHOW COLUMNS FROM `ppepp_documents` LIKE 'gpm_reviewed_by'")->fetch();
    if (!$chkCol) {
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `gpm_reviewed_by` INT NULL AFTER `catatan_review`");
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `gpm_reviewed_at` DATETIME NULL AFTER `gpm_reviewed_by`");
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `gpm_catatan` TEXT NULL AFTER `gpm_reviewed_at`");
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD CONSTRAINT `fk_docs_gpm_reviewer` FOREIGN KEY (`gpm_reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL");
        echo "   [OK] Kolom pelacak review GPM ditambahkan.\n";
    }

    // lpm_reviewed_by
    $chkCol = $pdo->query("SHOW COLUMNS FROM `ppepp_documents` LIKE 'lpm_reviewed_by'")->fetch();
    if (!$chkCol) {
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `lpm_reviewed_by` INT NULL AFTER `gpm_catatan`");
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `lpm_reviewed_at` DATETIME NULL AFTER `lpm_reviewed_by`");
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD COLUMN `lpm_catatan` TEXT NULL AFTER `lpm_reviewed_at`");
        $pdo->exec("ALTER TABLE `ppepp_documents` ADD CONSTRAINT `fk_docs_lpm_reviewer` FOREIGN KEY (`lpm_reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL");
        echo "   [OK] Kolom pelacak review LPM ditambahkan.\n";
    }

    // 4. Migrasi data lama yang berstatus 'belum_direview' agar masuk ke antrean 'menunggu_review_gpm'
    echo "4. Menyelaraskan status data lama...\n";
    $pdo->exec("UPDATE `ppepp_documents` SET `status_review` = 'menunggu_review_gpm', `tahap_review` = 'gpm' WHERE `status_review` = 'belum_direview'");
    $pdo->exec("UPDATE `ppepp_documents` SET `tahap_review` = 'draft' WHERE `status_review` = 'draft'");
    $pdo->exec("UPDATE `ppepp_documents` SET `tahap_review` = 'selesai' WHERE `status_review` = 'sesuai'");
    $pdo->exec("UPDATE `ppepp_documents` SET `tahap_review` = 'lpm' WHERE `status_review` IN ('perlu_perbaikan', 'sudah_diperbaiki')");

    // 5. Salin catatan_review lama yang ada ke tabel ppepp_document_reviews sebagai riwayat awal (jika ada)
    $stmtExistingNotes = $pdo->query("SELECT id, catatan_review, reviewed_by, reviewed_at, status_review FROM `ppepp_documents` WHERE catatan_review IS NOT NULL AND catatan_review != ''");
    $notesList = $stmtExistingNotes->fetchAll(PDO::FETCH_ASSOC);
    $insertedHist = 0;
    foreach ($notesList as $n) {
        $checkH = $pdo->prepare("SELECT id FROM ppepp_document_reviews WHERE document_id = ? AND catatan = ?");
        $checkH->execute([$n['id'], $n['catatan_review']]);
        if (!$checkH->fetch()) {
            $insH = $pdo->prepare("
                INSERT INTO ppepp_document_reviews (document_id, reviewer_id, reviewer_role, reviewer_name, tahap, status_sebelumnya, status_hasil, catatan, created_at)
                VALUES (?, ?, 'lpm', 'Pusat Penjaminan Mutu LPM', 'lpm', 'menunggu_review_lpm', ?, ?, ?)
            ");
            $insH->execute([
                $n['id'],
                $n['reviewed_by'],
                $n['status_review'],
                $n['catatan_review'],
                $n['reviewed_at'] ?: date('Y-m-d H:i:s')
            ]);
            $insertedHist++;
        }
    }
    echo "   [OK] Menyelaraskan {$insertedHist} catatan riwayat review eksisting.\n";

    echo "\n=== MIGRASI ALUR REVIEW GPM & LPM SELESAI DENGAN SUKSES ===\n";
} catch (\Throwable $e) {
    echo "ERR: " . $e->getMessage() . "\n";
    exit(1);
}
