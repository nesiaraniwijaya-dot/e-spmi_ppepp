<?php
/**
 * Pembersihan Akun Dummy / Akun Lama Tingkat GPM, Dekanat, dan Prodi
 * Mentransfer kepemilikan dokumen riil ke akun resmi baru, lalu menghapus akun lama.
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "========================================================\n";
    echo "  PEMBERSIHAN AKUN LAMA & TRANSFER DOKUMEN KE AKUN RESMI\n";
    echo "========================================================\n\n";

    $oldEmailsToDelete = [
        'gpm.fikom@unika.ac.id',
        'dekan.fikom@unika.ac.id',
        'wadek.fikom@unika.ac.id',
        'dekan.feb@unika.ac.id',
        'wadekfeb@gmail.com',
        'kaprodi.ti@unika.ac.id',
        'sekprodi.ti@unika.ac.id',
        'kaprodi.si@unika.ac.id',
        'sekprodi.si@unika.ac.id',
        'kaprodi.akuntansi@unika.ac.id',
        'sekprodi.akuntansi@unika.ac.id',
        'pengguna@unika.ac.id',
    ];

    // Mapping transfer kepemilikan dokumen/siklus/log dari email lama ke email resmi baru
    $ownershipTransferMap = [
        'dekan.fikom@unika.ac.id'       => 'fikom@unika.ac.id',
        'wadek.fikom@unika.ac.id'       => 'fikom@unika.ac.id',
        'kaprodi.ti@unika.ac.id'        => 'ti@unika.ac.id',
        'sekprodi.ti@unika.ac.id'       => 'ti@unika.ac.id',
        'kaprodi.si@unika.ac.id'        => 'si@unika.ac.id',
        'sekprodi.si@unika.ac.id'       => 'si@unika.ac.id',
        'kaprodi.akuntansi@unika.ac.id' => 'akuntansi@unika.ac.id',
        'sekprodi.akuntansi@unika.ac.id' => 'akuntansi@unika.ac.id',
        'dekan.feb@unika.ac.id'         => 'feb@unika.ac.id',
        'wadekfeb@gmail.com'            => 'feb@unika.ac.id',
        'gpm.fikom@unika.ac.id'         => 'erdhi@unika.ac.id',
        'pengguna@unika.ac.id'          => 'tu.lpm@unika.ac.id',
    ];

    $db->beginTransaction();

    echo "1. Mentransfer riwayat dan kepemilikan dokumen ke akun resmi:\n";
    foreach ($ownershipTransferMap as $oldEmail => $newEmail) {
        $oldId = $db->query("SELECT id FROM users WHERE email = '$oldEmail'")->fetchColumn();
        $newId = $db->query("SELECT id FROM users WHERE email = '$newEmail'")->fetchColumn();

        if ($oldId && $newId) {
            // ppepp_documents
            $uDocs = $db->exec("UPDATE ppepp_documents SET user_id = $newId WHERE user_id = $oldId");
            $uGpmRev = $db->exec("UPDATE ppepp_documents SET gpm_reviewed_by = $newId WHERE gpm_reviewed_by = $oldId");
            $uLpmRev = $db->exec("UPDATE ppepp_documents SET lpm_reviewed_by = $newId WHERE lpm_reviewed_by = $oldId");
            $uRev = $db->exec("UPDATE ppepp_documents SET reviewed_by = $newId WHERE reviewed_by = $oldId");
            // ppepp_cycles
            $uCycles = $db->exec("UPDATE ppepp_cycles SET created_by = $newId WHERE created_by = $oldId");
            // ppepp_document_reviews
            $uReviews = $db->exec("UPDATE ppepp_document_reviews SET reviewer_id = $newId WHERE reviewer_id = $oldId");
            // audit_logs
            $uLogs = $db->exec("UPDATE audit_logs SET user_id = $newId WHERE user_id = $oldId");
            // upload_access_requests
            $uReqs1 = $db->exec("UPDATE upload_access_requests SET user_id = $newId WHERE user_id = $oldId");
            $uReqs2 = $db->exec("UPDATE upload_access_requests SET processed_by = $newId WHERE processed_by = $oldId");

            echo "   -> $oldEmail (ID $oldId) => $newEmail (ID $newId) [Docs: $uDocs, Cycles: $uCycles, Logs: $uLogs]\n";
        }
    }

    echo "\n2. Menghapus akun lama/dummy dari tabel users:\n";
    $inClause = "'" . implode("','", $oldEmailsToDelete) . "'";
    $deleted = $db->exec("DELETE FROM users WHERE email IN ($inClause)");

    $db->commit();
    echo "   [OK] Sebanyak $deleted akun lama/dummy berhasil dihapus dari sistem.\n\n";

    echo "3. Status Akun Tersisa di Tabel users:\n";
    $stmt = $db->query("SELECT id, name, email, role, fakultas_id, prodi_id FROM users ORDER BY role, id");
    $total = 0;
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $total++;
        $fak = $r['fakultas_id'] ? "Fak: {$r['fakultas_id']}" : "Fak: -";
        $prodi = $r['prodi_id'] ? "Prodi: {$r['prodi_id']}" : "Prodi: -";
        echo "   #{$r['id']} | [{$r['role']}] | {$r['email']} | $fak | $prodi | {$r['name']}\n";
    }

    echo "\nTotal akun aktif resmi saat ini: $total akun.\n";
    echo "========================================================\n";

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

