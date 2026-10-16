<?php
/**
 * AGINDU - Lightweight Fast Router
 */

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

class Router
{
    private array $routes = [];
    private $notFoundHandler = null;

    public function get(string $pattern, array $handler): void
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, array $handler): void
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    private function addRoute(string $method, string $pattern, array $handler): void
    {
        // Normalisasi format pattern: {param} menjadi regex capture group (?P<param>[^/]+)
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'regex'   => $regex,
            'handler' => $handler
        ];
    }

    public function setNotFound(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    /**
     * Jalankan request saat ini
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($requestMethod === 'HEAD') {
            $requestMethod = 'GET';
        }
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);

        // Hapus basePath jika aplikasi berjalan di subdirektori (misal: /agindu)
        $basePath = detect_base_path();
        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        // Hapus file index.php jika ada di URL path
        if ($path === '/index.php') {
            $path = '/';
        } elseif (str_starts_with($path, '/index.php/')) {
            $path = substr($path, 10);
        }

        $path = '/' . trim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['regex'], $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $val) {
                    if (is_string($key)) {
                        $params[] = ctype_digit($val) ? (int)$val : $val;
                    }
                }

                [$controllerClass, $method] = $route['handler'];

                // Autoload Controller
                $controllerFile = APP_ROOT . '/app/Controllers/' . $controllerClass . '.php';
                if (!file_exists($controllerFile)) {
                    throw new RuntimeException("File controller tidak ditemukan: {$controllerFile}");
                }
                require_once $controllerFile;

                $instance = new $controllerClass();
                call_user_func_array([$instance, $method], array_values($params));
                return;
            }
        }

        // Tidak ada route yang cocok: 404 Not Found
        if ($this->notFoundHandler) {
            call_user_func($this->notFoundHandler);
        } else {
            http_response_code(404);
            $errView = APP_ROOT . '/app/Views/errors/404.php';
            if (file_exists($errView)) {
                require $errView;
            } else {
                echo '404 - Halaman Tidak Ditemukan';
            }
        }
    }
}
