<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getInstance()->getConnection();

    // 1. Buat tabel user_assignments jika belum ada
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_assignments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            role VARCHAR(50) NOT NULL,
            fakultas_id INT NULL,
            prodi_id INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_role (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Tabel user_assignments berhasil dibuat / sudah ada.\n";

    // 2. Migrasikan peran dari tabel users yang belum ada di user_assignments
    $stmtUsers = $pdo->query("SELECT id, role, fakultas_id, prodi_id FROM users");
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM user_assignments WHERE user_id = ? AND role = ? AND (fakultas_id <=> ?) AND (prodi_id <=> ?)");
    $stmtInsert = $pdo->prepare("INSERT INTO user_assignments (user_id, role, fakultas_id, prodi_id) VALUES (?, ?, ?, ?)");

    $migrated = 0;
    foreach ($users as $u) {
        $stmtCheck->execute([$u['id'], $u['role'], $u['fakultas_id'], $u['prodi_id']]);
        if ($stmtCheck->fetchColumn() == 0) {
            $stmtInsert->execute([$u['id'], $u['role'], $u['fakultas_id'], $u['prodi_id']]);
            $migrated++;
        }
    }

    echo "Migrasi awal: {$migrated} penugasan awal berhasil disinkronkan ke user_assignments.\n";

} catch (\Throwable $e) {
    echo "Error migrasi: " . $e->getMessage() . "\n";
    exit(1);
}
