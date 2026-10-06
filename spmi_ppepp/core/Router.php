<?php
/**
 * URL Router & Dispatcher
 * SPMI PPEPP UNIKA Soegijapranata
 */

class Router {
    private array $routes = [];

    public function get(string $path, array|callable $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array|callable $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, array|callable $handler): void {
        // Support {param} or {param:regex} such as {id:\d+}
        $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)(?::([^}]+))?\}/', function($m) {
            $name = $m[1];
            $regex = !empty($m[2]) ? $m[2] : '[^/]+';
            return '(?P<' . $name . '>' . $regex . ')';
        }, trim($path, '/'));
        $pattern = '#^' . ($pattern === '' ? '' : $pattern) . '$#i';

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'raw_path' => $path
        ];
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Remove base directory path from URI if running in subdirectory (like /spmi_ppepp/)
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = trim($uri, '/');

        // Check if matching route
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));

                if (is_callable($route['handler'])) {
                    call_user_func_array($route['handler'], $params);
                    return;
                }

                if (is_array($route['handler'])) {
                    [$controllerName, $actionName] = $route['handler'];
                    $controllerFile = ROOT_PATH . '/controllers/' . $controllerName . '.php';

                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                        $controller = new $controllerName();
                        if (method_exists($controller, $actionName)) {
                            call_user_func_array([$controller, $actionName], $params);
                            return;
                        }
                    }
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        require_once ROOT_PATH . '/config/app.php';
        $currentUser = Auth::user();
        require ROOT_PATH . '/views/layouts/header.php';
        ?>
        <div class="container py-5 text-center my-5">
            <div class="card p-5 mx-auto shadow-lg" style="max-width: 600px; border-radius: 16px;">
                <div class="display-1 text-primary fw-bold mb-3">404</div>
                <h3 class="fw-bold text-dark-blue mb-3">Halaman Tidak Ditemukan</h3>
                <p class="text-muted mb-4">Mohon maaf, alamat halaman yang Anda tuju tidak tersedia atau telah dipindahkan.</p>
                <div>
                    <a href="<?= base_url() ?>" class="btn btn-primary px-4 py-2 rounded-pill">
                        <i class="fas fa-home me-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
        <?php
        require ROOT_PATH . '/views/layouts/footer.php';
        exit;
    }
}
