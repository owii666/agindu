<?php
/**
 * AGINDU (Agribisnis Indonesia Mendunia)
 * Front Controller (Flat Root Architecture)
 */

declare(strict_types=1);

// Muat konfigurasi inti aplikasi
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Core/Session.php';
require_once __DIR__ . '/app/Core/Csrf.php';
require_once __DIR__ . '/app/Core/helpers.php';
require_once __DIR__ . '/app/Core/Router.php';

// Inisialisasi session dengan opsi keamanan HttpOnly dan SameSite
Session::start();

// Penanganan Error & Pengecualian Global
set_exception_handler(function (Throwable $e) {
    $logDir = dirname(LOG_FILE);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }

    $logMsg = sprintf(
        "[%s] UNCAUGHT EXCEPTION: %s in %s:%d\nStack Trace:\n%s\n----------------------------------------\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );

    @error_log($logMsg, 3, LOG_FILE);

    http_response_code(500);

    $errFile = APP_ROOT . '/app/Views/errors/500.php';
    if (file_exists($errFile)) {
        require $errFile;
    } else {
        echo "<!DOCTYPE html><html lang='id'><head><meta charset='utf-8'><title>500 - Gangguan Server</title></head><body>";
        echo "<h2>Terjadi Kendala Teknis</h2>";
        echo "<p>Sistem sedang memproses kendala internal. Seluruh aktivitas telah dicatat dalam log server.</p>";
        echo "</body></html>";
    }
    exit;
});

// ============================================================================
// REGISTRASI ROUTING APLIKASI
// ============================================================================

$router = new Router();

// Halaman Beranda (Arahkan ke Dashboard jika login, atau ke Login jika belum)
$router->get('/', ['DashboardController', 'index']);
$router->get('/dashboard', ['DashboardController', 'index']);

// Autentikasi Pengguna
$router->get('/login', ['AuthController', 'showLogin']);
$router->post('/login', ['AuthController', 'login']);
$router->post('/logout', ['AuthController', 'logout']);

// Manajemen Batch Ekspor (CRUD)
$router->get('/batches', ['BatchController', 'index']);
$router->get('/batches/create', ['BatchController', 'create']);
$router->post('/batches', ['BatchController', 'store']);
$router->get('/batches/{id}', ['BatchController', 'show']);
$router->get('/batches/{id}/edit', ['BatchController', 'edit']);
$router->post('/batches/{id}', ['BatchController', 'update']);
$router->post('/batches/{id}/delete', ['BatchController', 'delete']);
$router->post('/batches/{id}/timeline', ['BatchController', 'addTimeline']);
$router->get('/batches/{id}/qr', ['BatchController', 'qr']);

// Pemantauan Suhu IoT & Rantai Dingin
$router->get('/temperature', ['TemperatureController', 'index']);
$router->get('/api/temperature/{batch}', ['TemperatureController', 'getLogs']);
$router->post('/api/simulate/{batch}', ['TemperatureController', 'simulate']);

// Panduan Kepatuhan Regulasi & Dokumen Ekspor
$router->get('/export-guide', ['ExportGuideController', 'index']);
$router->post('/export-guide/toggle/{id}', ['ExportGuideController', 'toggle']);

// Halaman Publik Ketertelusuran Rantai Pasok (Akses Pemindaian QR Tanpa Login)
$router->get('/trace/{token}', ['TraceController', 'show']);

// Halaman 404 Custom
$router->setNotFound(function () {
    http_response_code(404);
    $errView = APP_ROOT . '/app/Views/errors/404.php';
    if (file_exists($errView)) {
        require $errView;
    } else {
        echo '404 - Halaman Tidak Ditemukan';
    }
});

// Jalankan permintaan
$router->dispatch();
