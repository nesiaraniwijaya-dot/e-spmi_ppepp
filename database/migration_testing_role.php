<?php
/**
 * Migration: Add 'testing' role to users table and update testing accounts
 */
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getInstance()->getConnection();

    // 1. Alter users.role enum to add 'testing'
    $pdo->exec("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('super_admin','admin_lpm','kepala_pusat_mutu','kepala_lpm','dekan','wadek','gpm','kaprodi','sekprodi','pengguna','testing') NOT NULL DEFAULT 'kaprodi'");
    echo "[OK] Column `users.role` ENUM updated with 'testing'.\n";

    // 2. Update existing test accounts
    $defaultPass = password_hash('password123', PASSWORD_BCRYPT);

    $testUsers = [
        [
            'name' => 'Nesia Rani Wijaya (Akun Testing Multi-Role)',
            'email' => 'nesiaraniwijaya@gmail.com',
            'role' => 'testing'
        ],
        [
            'name' => 'Ravy Whienelda (Akun Testing Multi-Role)',
            'email' => 'ravywhienelda@gmail.com',
            'role' => 'testing'
        ],
        [
            'name' => 'LPM UNIKA Soegijapranata (Akun Testing)',
            'email' => 'lpm@unika.ac.id',
            'role' => 'testing'
        ],
        [
            'name' => 'Tata Usaha LPM (Akun Testing)',
            'email' => 'tu.lpm@unika.ac.id',
            'role' => 'testing'
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, password_plain, role, is_active) 
        VALUES (?, ?, ?, 'password123', ?, 1)
        ON DUPLICATE KEY UPDATE name = VALUES(name), role = VALUES(role), is_active = 1");

    foreach ($testUsers as $tu) {
        $stmt->execute([$tu['name'], $tu['email'], $defaultPass, $tu['role']]);
        echo "[OK] User {$tu['email']} set to role 'testing'.\n";
    }

    echo "=== MIGRATION TESTING ROLE COMPLETED SUCCESSFULLY ===\n";
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}
