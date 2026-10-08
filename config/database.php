<?php
/**
 * Database Connection & Singleton
 * SPMI PPEPP UNIKA Soegijapranata
 */

class Database {
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    private string $host = '127.0.0.1';
    private string $port = '3306';
    private string $dbname = 'spmi_ppepp';
    private string $username = 'root';
    private string $password = '';
    private string $charset = 'utf8mb4';

    private function __construct() {
        // Load custom config from environment or overrides if needed
        if (file_exists(__DIR__ . '/env.php')) {
            $env = require __DIR__ . '/env.php';
            $this->host = $env['DB_HOST'] ?? $this->host;
            $this->port = $env['DB_PORT'] ?? $this->port;
            $this->dbname = $env['DB_DATABASE'] ?? $this->dbname;
            $this->username = $env['DB_USERNAME'] ?? $this->username;
            $this->password = $env['DB_PASSWORD'] ?? $this->password;
        }

        try {
            // Direct connection to database
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            // If database does not exist on local dev (error 1049), attempt auto-creation
            if (($e->getCode() == 1049 || str_contains($e->getMessage(), 'Unknown database')) && in_array($this->host, ['127.0.0.1', 'localhost'])) {
                try {
                    $dsnInit = "mysql:host={$this->host};port={$this->port};charset={$this->charset}";
                    $pdoInit = new PDO($dsnInit, $this->username, $this->password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                    $pdoInit->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                    $this->pdo = new PDO($dsn, $this->username, $this->password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]);
                    return;
                } catch (Exception $ex) {
                    // Fallback to error display
                }
            }
            die("Database Connection Error: " . htmlspecialchars($e->getMessage()));
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }

    public function query(...$args) {
        return $this->pdo->query(...$args);
    }

    public function prepare(...$args) {
        return $this->pdo->prepare(...$args);
    }

    public function exec(...$args) {
        return $this->pdo->exec(...$args);
    }

    public function lastInsertId(?string $name = null): string {
        return $this->pdo->lastInsertId($name);
    }

    public function __call(string $name, array $arguments) {
        return $this->pdo->$name(...$arguments);
    }
}
