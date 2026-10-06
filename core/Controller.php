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
}
