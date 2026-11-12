<?php
/**
 * Base Controller Class
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Auth.php';

abstract class Controller {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function render(string $viewPath, array $data = [], string $layout = 'layouts/header'): void {
        extract($data);

        // Flash message
        $flash = Auth::getFlash();
        $currentUser = Auth::user();

        // Render view file
        $targetFile = ROOT_PATH . '/views/' . ltrim($viewPath, '/') . '.php';
        if (!file_exists($targetFile)) {
            die("View not found: " . htmlspecialchars($targetFile));
        }

        require $targetFile;
    }

    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Hitung jumlah dokumen per siklus PPEPP berdasarkan kondisi WHERE dasar (tanpa filter siklus).
     */
    protected function getSiklusCountsByWhere(string $fromClause, string $whereClause, array $params = []): array {
        $counts = [
            'penetapan'    => 0,
            'pelaksanaan'  => 0,
            'evaluasi'     => 0,
            'pengendalian' => 0,
            'peningkatan'  => 0,
        ];
        $stmt = $this->db->prepare("
            SELECT LOWER(TRIM(d.siklus)) AS siklus_key, COUNT(*) AS cnt
            {$fromClause}
            WHERE {$whereClause}
            GROUP BY LOWER(TRIM(d.siklus))
        ");
        $stmt->execute($params);
        foreach ($stmt->fetchAll() as $row) {
            $key = strtolower(trim((string)($row['siklus_key'] ?? '')));
            if (isset($counts[$key])) {
                $counts[$key] = (int)$row['cnt'];
            }
        }
        return $counts;
    }
}
