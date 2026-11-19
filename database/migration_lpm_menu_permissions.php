<?php
/**
 * Migrasi Tabel Hak Akses Menu LPM & Pembaruan Akun Admin (tu.lpm@unika.ac.id)
 *
 * 1. Membuat tabel `lpm_menu_permissions` untuk kontrol RBAC dinamis:
 *    - role (kepala_lpm, sekretaris_lpm, ketua_pusat_spm, ketua_pusat_ami, ketua_pusat_pemeringkatan)
 *    - menu_key (dashboard, review_dokumen, monitoring_berkas, monitoring_gpm, upload_access, master_fakultas, master_prodi, master_bidang)
 *    - can_view (1 = tampil di sidebar & bisa dibuka, 0 = sembunyi)
 *    - can_action (1 = aksi penuh, 0 = lihat saja / read-only)
 * 2. Memperbarui akun `tu.lpm@unika.ac.id` menjadi role 'super_admin' dengan nama 'Admin'.
 * 3. Mengisi permission default (aktif penuh) untuk seluruh role LPM non-admin.
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getInstance()->getConnection();

    echo "=== 1. MEMBUAT TABEL lpm_menu_permissions ===\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `lpm_menu_permissions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `role` VARCHAR(50) NOT NULL,
            `menu_key` VARCHAR(50) NOT NULL,
            `can_view` TINYINT(1) NOT NULL DEFAULT 1,
            `can_action` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uk_role_menu` (`role`, `menu_key`),
            INDEX `idx_role` (`role`),
            INDEX `idx_menu_key` (`menu_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[OK] Tabel `lpm_menu_permissions` siap digunakan.\n";

    echo "\n=== 2. MEMPERBARUI AKUN tu.lpm@unika.ac.id MENJADI ADMIN ===\n";
    $stmtCheckTu = $pdo->prepare("SELECT id, name, email, role FROM users WHERE LOWER(email) = 'tu.lpm@unika.ac.id' LIMIT 1");
    $stmtCheckTu->execute();
    $tuUser = $stmtCheckTu->fetch();

    if ($tuUser) {
        $stmtUpdateTu = $pdo->prepare("
            UPDATE users 
            SET name = 'Admin', role = 'super_admin', is_active = 1, prodi_id = NULL, fakultas_id = NULL 
            WHERE id = ?
        ");
        $stmtUpdateTu->execute([$tuUser['id']]);
        echo "[UPDATE] User #{$tuUser['id']} ({$tuUser['email']}) diperbarui menjadi Role: super_admin, Nama: Admin.\n";
    } else {
        $defaultHash = password_hash('password123', PASSWORD_BCRYPT);
        $stmtInsertTu = $pdo->prepare("
            INSERT INTO users (name, email, password, password_plain, role, prodi_id, fakultas_id, is_active)
            VALUES ('Admin', 'tu.lpm@unika.ac.id', ?, 'password123', 'super_admin', NULL, NULL, 1)
        ");
        $stmtInsertTu->execute([$defaultHash]);
        $newId = $pdo->lastInsertId();
        echo "[INSERT] Akun baru #{$newId} (tu.lpm@unika.ac.id) dibuat sebagai Admin (super_admin).\n";
    }

    echo "\n=== 3. SEEDING DEFAULT HAK AKSES MENU LPM ===\n";
    $lpmRoles = [
        'kepala_lpm',
        'sekretaris_lpm',
        'ketua_pusat_spm',
        'ketua_pusat_ami',
        'ketua_pusat_pemeringkatan',
    ];

    $configurableMenus = [
        'dashboard',
        'review_dokumen',
        'monitoring_berkas',
        'monitoring_gpm',
        'upload_access',
        'master_fakultas',
        'master_prodi',
        'master_bidang',
    ];

    $stmtInsertPerm = $pdo->prepare("
        INSERT INTO lpm_menu_permissions (role, menu_key, can_view, can_action)
        VALUES (?, ?, 1, ?)
        ON DUPLICATE KEY UPDATE can_action = VALUES(can_action), updated_at = updated_at
    ");

    $seededCount = 0;
    foreach ($lpmRoles as $role) {
        foreach ($configurableMenus as $menu) {
            // Sekretaris LPM secara default hanya bisa memantau review dokumen (read-only)
            $canAction = ($role === 'sekretaris_lpm' && $menu === 'review_dokumen') ? 0 : 1;
            $stmtInsertPerm->execute([$role, $menu, $canAction]);
            $seededCount++;
        }
    }
    echo "[OK] {$seededCount} kombinasi hak akses role LPM berhasil disiapkan secara idempoten.\n";

    echo "\n=== RINGKASAN STATUS HAK AKSES LPM ===\n";
    $stmtSummary = $pdo->query("
        SELECT role, COUNT(*) as total_menu, SUM(can_view) as total_view, SUM(can_action) as total_action
        FROM lpm_menu_permissions
        GROUP BY role
    ");
    while ($row = $stmtSummary->fetch()) {
        echo sprintf(" - Role: %-26s | Menus: %d | View: %d | Action: %d\n", $row['role'], $row['total_menu'], $row['total_view'], $row['total_action']);
    }

    echo "\n[SUCCESS] Migrasi hak akses menu LPM selesai.\n";

} catch (\Throwable $e) {
    fwrite(STDERR, "[ERROR] " . $e->getMessage() . "\n");
    exit(1);
}

