<?php
/**
 * Add nesiaraniwijaya@gmail.com User to Database
 */
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getInstance()->getConnection();

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_active) 
        VALUES ('Nesia Rani Wijaya (Multi-Role Super User)', 'nesiaraniwijaya@gmail.com', ?, 'super_admin', 1)
        ON DUPLICATE KEY UPDATE name = VALUES(name), role = 'super_admin', is_active = 1");
    
    $defaultPass = password_hash('password123', PASSWORD_BCRYPT);
    $stmt->execute([$defaultPass]);

    echo "[OK] User nesiaraniwijaya@gmail.com successfully inserted/updated in users table.\n";
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}
