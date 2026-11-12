<?php
/**
 * Migrasi Role & Data Akun Tim LPM (Sesuai Struktur Terbaru LPM)
 * Menyelaraskan 6 jabatan LPM resmi ke tabel `users`:
 *   1. kepala_lpm                -> lpm@unika.ac.id
 *   2. sekretaris_lpm            -> lpm.sekretaris@unika.ac.id
 *   3. ketua_pusat_spm           -> lpm.spm@unika.ac.id
 *   4. ketua_pusat_ami           -> lpm.ami@unika.ac.id
 *   5. ketua_pusat_pemeringkatan -> lpm.pemeringkatan@unika.ac.id
 *   6. tu_lpm                    -> tu.lpm@unika.ac.id
 * Mempertahankan `super_admin` untuk akun multi-role/testing.
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getInstance()->getConnection();

    // 1. Perbarui ENUM kolom `role` pada tabel `users`
    $pdo->exec("
        ALTER TABLE `users`
        MODIFY COLUMN `role` ENUM(
            'super_admin',
            'kepala_lpm',
            'sekretaris_lpm',
            'ketua_pusat_spm',
            'ketua_pusat_ami',
            'ketua_pusat_pemeringkatan',
            'tu_lpm',
            'admin_lpm',
            'kepala_pusat_mutu',
            'dekan',
            'wadek',
            'gpm',
            'kaprodi',
            'sekprodi',
            'pengguna',
            'testing'
        ) NOT NULL DEFAULT 'kaprodi'
    ");
    echo "[OK] ENUM users.role berhasil diperbarui.\n";

    $defaultHash = password_hash('password123', PASSWORD_BCRYPT);

    // 2. Migrasikan 3 akun LPM lama agar ID histori tetap terjaga & hindari duplikat email
    $legacyMigrations = [
        'kepala.lpm@unika.ac.id' => [
            'email' => 'lpm@unika.ac.id',
            'name'  => 'Kepala Lembaga Penjaminan Mutu',
            'role'  => 'kepala_lpm',
        ],
        'admin.lpm@unika.ac.id' => [
            'email' => 'tu.lpm@unika.ac.id',
            'name'  => 'TU Lembaga Penjaminan Mutu',
            'role'  => 'tu_lpm',
        ],
        'pusat.mutu@unika.ac.id' => [
            'email' => 'lpm.spm@unika.ac.id',
            'name'  => 'Ketua Pusat Pengembangan Sistem Penjaminan Mutu',
            'role'  => 'ketua_pusat_spm',
        ],
    ];

    $stmtFindEmail = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = ? LIMIT 1");
    $stmtMigrate = $pdo->prepare("
        UPDATE users
        SET name = ?, email = ?, role = ?, prodi_id = NULL, fakultas_id = NULL, is_active = 1
        WHERE id = ?
    ");

    foreach ($legacyMigrations as $oldEmail => $target) {
        $stmtFindEmail->execute([strtolower($target['email'])]);
        $targetExistsId = $stmtFindEmail->fetchColumn();

        $stmtFindEmail->execute([strtolower($oldEmail)]);
        $oldId = $stmtFindEmail->fetchColumn();

        if ($oldId && !$targetExistsId) {
            $stmtMigrate->execute([$target['name'], $target['email'], $target['role'], $oldId]);
            echo "[MIGRATE] ID #{$oldId}: {$oldEmail} -> {$target['email']} ({$target['role']})\n";
        } elseif ($oldId && $targetExistsId && (int)$oldId !== (int)$targetExistsId) {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$oldId]);
            echo "[CLEANUP] Hapus duplikat akun lama #{$oldId} ({$oldEmail})\n";
        }
    }

    // 3. Upsert seluruh 6 akun LPM resmi
    $lpmAccounts = [
        [
            'name'  => 'Kepala Lembaga Penjaminan Mutu',
            'email' => 'lpm@unika.ac.id',
            'role'  => 'kepala_lpm',
        ],
        [
            'name'  => 'Sekretaris Lembaga Penjaminan Mutu',
            'email' => 'lpm.sekretaris@unika.ac.id',
            'role'  => 'sekretaris_lpm',
        ],
        [
            'name'  => 'Ketua Pusat Pengembangan Sistem Penjaminan Mutu',
            'email' => 'lpm.spm@unika.ac.id',
            'role'  => 'ketua_pusat_spm',
        ],
        [
            'name'  => 'Ketua Pusat Audit Mutu Internal',
            'email' => 'lpm.ami@unika.ac.id',
            'role'  => 'ketua_pusat_ami',
        ],
        [
            'name'  => 'Ketua Pusat Pemeringkatan',
            'email' => 'lpm.pemeringkatan@unika.ac.id',
            'role'  => 'ketua_pusat_pemeringkatan',
        ],
        [
            'name'  => 'TU Lembaga Penjaminan Mutu',
            'email' => 'tu.lpm@unika.ac.id',
            'role'  => 'tu_lpm',
        ],
    ];

    $stmtUpdateExisting = $pdo->prepare("
        UPDATE users
        SET name = ?, role = ?, prodi_id = NULL, fakultas_id = NULL, is_active = 1
        WHERE id = ?
    ");
    $stmtInsert = $pdo->prepare("
        INSERT INTO users (name, email, password, password_plain, role, prodi_id, fakultas_id, is_active)
        VALUES (?, ?, ?, 'password123', ?, NULL, NULL, 1)
    ");

    foreach ($lpmAccounts as $acc) {
        $stmtFindEmail->execute([strtolower($acc['email'])]);
        $existingId = $stmtFindEmail->fetchColumn();
        if ($existingId) {
            $stmtUpdateExisting->execute([$acc['name'], $acc['role'], $existingId]);
            echo "[UPDATE] #{$existingId} {$acc['email']} => {$acc['role']} ({$acc['name']})\n";
        } else {
            $stmtInsert->execute([$acc['name'], $acc['email'], $defaultHash, $acc['role']]);
            $newId = $pdo->lastInsertId();
            echo "[INSERT] #{$newId} {$acc['email']} => {$acc['role']} ({$acc['name']})\n";
        }
    }

    // 4. Normalisasi role lama bila masih tersisa
    $pdo->exec("UPDATE users SET role = 'kepala_lpm' WHERE role IN ('kepala_pusat_mutu', 'admin_lpm')");

    echo "\n=== DAFTAR AKUN LPM SAAT INI ===\n";
    $rows = $pdo->query("
        SELECT id, name, email, role, is_active
        FROM users
        WHERE role IN ('super_admin','kepala_lpm','sekretaris_lpm','ketua_pusat_spm','ketua_pusat_ami','ketua_pusat_pemeringkatan','tu_lpm','admin_lpm','kepala_pusat_mutu')
        ORDER BY id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "  #{$r['id']} | {$r['role']} | {$r['email']} | {$r['name']}\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, "Migration error: " . $e->getMessage() . "\n");
    exit(1);
}

