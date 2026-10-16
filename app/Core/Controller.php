<?php
/**
 * AGINDU - Base Controller
 */

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Csrf.php';
require_once __DIR__ . '/Validator.php';

abstract class Controller
{
    /**
     * Render view dengan layout tertentu
     */
    protected function view(string $viewPath, array $data = [], ?string $layout = 'main'): void
    {
        extract($data);

        $viewFile = APP_ROOT . '/app/Views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("File view tidak ditemukan: {$viewFile}");
        }

        // Tangkap isi view
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutFile = APP_ROOT . '/app/Views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new RuntimeException("File layout tidak ditemukan: {$layoutFile}");
        }

        require $layoutFile;
    }

    /**
     * Kirim response JSON
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Pastikan pengguna telah login
     */
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->json(['error' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            set_flash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
            redirect('/login');
        }
    }

    /**
     * Validasi token CSRF untuk request POST
     */
    protected function validateCsrf(): void
    {
        if (!Csrf::validateRequest()) {
            http_response_code(419);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->json(['error' => 'Sesi kedaluwarsa atau token CSRF tidak valid. Silakan muat ulang halaman.'], 419);
            }
            die('Sesi kedaluwarsa atau token CSRF tidak valid. Silakan muat ulang halaman.');
        }
    }
}
