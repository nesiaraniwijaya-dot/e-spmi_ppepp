<?php
/**
 * Audit Logger Service
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

class AuditLogger {
    public static function log(
        string $aksi,
        string $modul,
        ?string $targetId = null,
        ?string $targetName = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $prodiId = null,
        ?string $prodiName = null
    ): void {
        try {
            $pdo = Database::getInstance()->getConnection();

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $userId = $_SESSION['user_id'] ?? null;
            $userName = $_SESSION['user_name'] ?? 'Guest / Sistem';
            $userRole = $_SESSION['user_role'] ?? 'guest';

            $effectiveProdiId = $prodiId ?? ($_SESSION['user_prodi_id'] ?? null);
            $effectiveProdiName = $prodiName ?? ($_SESSION['user_prodi_name'] ?? null);

            // If prodiName is not available but prodiId is present, fetch it
            if ($effectiveProdiId && !$effectiveProdiName) {
                $stmt = $pdo->prepare("SELECT nama_prodi FROM prodis WHERE id = ?");
                $stmt->execute([$effectiveProdiId]);
                $effectiveProdiName = $stmt->fetchColumn() ?: null;
            }

            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            $stmt = $pdo->prepare("INSERT INTO audit_logs 
                (user_id, user_name, user_role, prodi_id, prodi_name, aksi, modul, target_id, target_name, old_values, new_values, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

            $stmt->execute([
                $userId,
                $userName,
                $userRole,
                $effectiveProdiId,
                $effectiveProdiName,
                $aksi,
                $modul,
                $targetId,
                $targetName,
                $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                $ipAddress,
                $userAgent
            ]);
        } catch (Exception $e) {
            // Silently fail or log to error log to avoid breaking user workflows
            error_log("Audit Logger Error: " . $e->getMessage());
        }
    }
}
