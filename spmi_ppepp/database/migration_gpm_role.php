<?php
/**
 * Migration & Seeder: Role Gugus Penjaminan Mutu (GPM)
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

echo "=== MEMULAI MIGRASI ROLE GUGUS PENJAMINAN MUTU (GPM) ===\n";

$db = Database::getInstance();
$pdo = $db->getConnection();

try {
    // 1. Update tabel `users`: tambah role 'gpm'
    echo "1. Mengupdate tabel `users` (tambah role 'gpm')...\n";
    $pdo->exec("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('super_admin', 'kepala_lpm', 'admin_prodi', 'admin_fakultas', 'gpm') NOT NULL");
    echo "   [OK] Role 'gpm' berhasil ditambahkan ke enum tabel `users`.\n";

    // 2. Cek fakultas FIKOM (Fakultas Ilmu Komputer)
    $stmtFak = $pdo->query("SELECT id, nama_fakultas FROM fakultas WHERE nama_fakultas LIKE '%Ilmu Komputer%' OR kode_fakultas = 'FIK' LIMIT 1");
    $fikom = $stmtFak->fetch(PDO::FETCH_ASSOC);

    if (!$fikom) {
        $stmtFakFirst = $pdo->query("SELECT id, nama_fakultas FROM fakultas ORDER BY id ASC LIMIT 1");
        $fikom = $stmtFakFirst->fetch(PDO::FETCH_ASSOC);
    }

    if ($fikom) {
        $fikomId = (int)$fikom['id'];
        $fikomName = $fikom['nama_fakultas'];
        echo "2. Fakultas terdeteksi untuk GPM: {$fikomName} (ID: {$fikomId})...\n";

        // 3. Buat atau perbarui akun contoh GPM FIKOM
        $gpmEmail = 'gpm.fikom@unika.ac.id';
        $stmtUser = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmtUser->execute([$gpmEmail]);
        $existingGpm = $stmtUser->fetch(PDO::FETCH_ASSOC);

        $passwordHash = password_hash('password123', PASSWORD_BCRYPT);

        if (!$existingGpm) {
            $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password, role, fakultas_id, prodi_id, is_active, created_at) VALUES (?, ?, ?, 'gpm', ?, NULL, 1, NOW())");
            $stmtInsert->execute([
                'GPM Fakultas Ilmu Komputer',
                $gpmEmail,
                $passwordHash,
                $fikomId
            ]);
            echo "   [OK] Akun GPM baru berhasil dibuat: {$gpmEmail} (Password: password123)\n";
        } else {
            $stmtUpdate = $pdo->prepare("UPDATE users SET role = 'gpm', fakultas_id = ?, prodi_id = NULL, is_active = 1 WHERE email = ?");
            $stmtUpdate->execute([$fikomId, $gpmEmail]);
            echo "   [OK] Akun GPM {$gpmEmail} diperbarui dengan role 'gpm' dan fakultas ID: {$fikomId}.\n";
        }
    } else {
        echo "   [WARN] Tidak ada data fakultas ditemukan.\n";
    }

    echo "\n=== MIGRASI SELESAI DENGAN SUKSES ===\n";
} catch (\Throwable $e) {
    echo "ERR: " . $e->getMessage() . "\n";
    exit(1);
}
