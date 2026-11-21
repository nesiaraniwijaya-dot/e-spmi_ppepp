<?php
/**
 * Database Connection & Singleton
 * SPMI PPEPP UNIKA Soegijapranata
 */

// 1. Dukungan pembacaan file .env jika tersedia di root project
if (file_exists(__DIR__ . '/../.env')) {
    $env_lines = @file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($env_lines) {
        foreach ($env_lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($env_key, $env_val) = explode('=', $line, 2);
                $env_key = trim($env_key);
                $env_val = trim($env_val, " \t\n\r\0\x0B\"'");
                if (getenv($env_key) === false) {
                    putenv("$env_key=$env_val");
                    $_ENV[$env_key] = $env_val;
                    $_SERVER[$env_key] = $env_val;
                }
            }
        }
    }
}

// 2. Dukungan Google OAuth via environment (.env, server env, atau config/env.php)
if (!defined('GOOGLE_CLIENT_ID')) {
    $envClientId = getenv('GOOGLE_CLIENT_ID') ?: ($_ENV['GOOGLE_CLIENT_ID'] ?? ($_SERVER['GOOGLE_CLIENT_ID'] ?? ''));
    if (!$envClientId && file_exists(__DIR__ . '/env.php')) {
        $envData = require __DIR__ . '/env.php';
        $envClientId = $envData['GOOGLE_CLIENT_ID'] ?? '';
    }
    define('GOOGLE_CLIENT_ID', $envClientId ?: '');
}
if (!defined('GOOGLE_CLIENT_SECRET')) {
    $envClientSecret = getenv('GOOGLE_CLIENT_SECRET') ?: ($_ENV['GOOGLE_CLIENT_SECRET'] ?? ($_SERVER['GOOGLE_CLIENT_SECRET'] ?? ''));
    if (!$envClientSecret && file_exists(__DIR__ . '/env.php')) {
        $envData = require __DIR__ . '/env.php';
        $envClientSecret = $envData['GOOGLE_CLIENT_SECRET'] ?? '';
    }
    define('GOOGLE_CLIENT_SECRET', $envClientSecret ?: '');
}

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
        // Auto-detect hosting online vs local development
        $hostClean = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? '');
        $isLocalEnv = empty($hostClean) || in_array($hostClean, ['localhost', '127.0.0.1', '::1']) || str_ends_with($hostClean, '.test') || str_ends_with($hostClean, '.local');

        if (!$isLocalEnv) {
            $this->host = 'sql303.ezyro.com';
            $this->dbname = 'ezyro_43035390_spmi';
            $this->username = 'ezyro_43035390';
            $this->password = 'grqh7tyz';
        }

        // Load custom config from environment or overrides if needed
        if (file_exists(__DIR__ . '/env.php')) {
            $env = require __DIR__ . '/env.php';
            $this->host = $env['DB_HOST'] ?? $this->host;
            $this->port = $env['DB_PORT'] ?? $this->port;
            $this->dbname = $env['DB_DATABASE'] ?? $this->dbname;
            $this->username = $env['DB_USERNAME'] ?? $this->username;
            $this->password = $env['DB_PASSWORD'] ?? $this->password;
        }

        // Override dari environment variables (.env / server env) jika tersedia
        $envHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? ''));
        if ($envHost !== '') $this->host = $envHost;

        $envPort = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? ($_SERVER['DB_PORT'] ?? ''));
        if ($envPort !== '') $this->port = $envPort;

        $envDb = getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? ($_SERVER['DB_DATABASE'] ?? ''));
        if ($envDb !== '') $this->dbname = $envDb;

        $envUser = getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? ($_SERVER['DB_USERNAME'] ?? ''));
        if ($envUser !== '') $this->username = $envUser;

        $envPass = getenv('DB_PASSWORD');
        if ($envPass !== false) {
            $this->password = $envPass;
        } elseif (isset($_ENV['DB_PASSWORD'])) {
            $this->password = $_ENV['DB_PASSWORD'];
        } elseif (isset($_SERVER['DB_PASSWORD'])) {
            $this->password = $_SERVER['DB_PASSWORD'];
        }

        try {
            // Direct connection to database
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            // Ensure MySQL timezone is synchronized to Asia/Jakarta (WIB / UTC+7)
            if (defined('\Pdo\Mysql::ATTR_INIT_COMMAND')) {
                $options[\Pdo\Mysql::ATTR_INIT_COMMAND] = "SET time_zone = '+07:00'";
            } elseif (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
                $options[@constant('PDO::MYSQL_ATTR_INIT_COMMAND')] = "SET time_zone = '+07:00'";
            }
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            try {
                $this->pdo->exec("SET time_zone = '+07:00';");
            } catch (\Throwable $tzEx) {}
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
                    try {
                        $this->pdo->exec("SET time_zone = '+07:00';");
                    } catch (\Throwable $tzEx) {}
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
