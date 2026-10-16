<?php
/**
 * AGINDU (Agribisnis Indonesia Mendunia)
 * File Konfigurasi Aplikasi & Database
 * 
 * Standar lingkungan lokal Laragon dengan dukungan override via config.local.php.
 */

declare(strict_types=1);

// Set timezone aplikasi ke WIB (Waktu Indonesia Barat)
date_default_timezone_set('Asia/Jakarta');

// ============================================================================
// 1. PENGATURAN URL DASAR (BASE_URL)
// ============================================================================

/**
 * Deteksi skema protokol (http atau https)
 */
function detect_protocol(): string
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return 'https';
    }
    if (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) {
        return 'https';
    }
    return 'http';
}

/**
 * Deteksi host HTTP
 */
function detect_host(): string
{
    return $_SERVER['HTTP_HOST'] ?? 'localhost';
}

/**
 * Hitung base path dari lokasi script (misal: /agindu atau kosong jika di root domain)
 */
function detect_base_path(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($scriptName));
    if ($dir === '/' || $dir === '.') {
        return '';
    }
    return rtrim($dir, '/');
}

// ============================================================================
// 2. KONFIGURASI DATABASE & APLIKASI (DEFAULT LARAGON)
// ============================================================================

$config = [
    'DB_HOST'           => 'localhost',
    'DB_PORT'           => '3306',
    'DB_NAME'           => 'agindu',
    'DB_USER'           => 'root',
    'DB_PASS'           => '',
    'BASE_URL_OVERRIDE' => '',
    'APP_DEBUG'         => true,
];

// Di bagian akhir, jika berkas config/config.local.php ada, muat untuk menimpa nilai default
$localConfigFile = __DIR__ . '/config.local.php';
if (file_exists($localConfigFile)) {
    $localConfig = require $localConfigFile;
    if (is_array($localConfig)) {
        $config = array_merge($config, $localConfig);
    }
}

// Definisikan konstanta jika belum didefinisikan sebelumnya
foreach ($config as $key => $val) {
    if (!defined($key)) {
        define($key, $val);
    }
}

// Tentukan BASE_URL akhir
if (defined('BASE_URL_OVERRIDE') && BASE_URL_OVERRIDE !== '') {
    define('BASE_URL', rtrim(BASE_URL_OVERRIDE, '/'));
} else {
    $calculatedUrl = detect_protocol() . '://' . detect_host() . detect_base_path();
    define('BASE_URL', rtrim($calculatedUrl, '/'));
}

// ============================================================================
// 3. KONSTANTA STATIS APLIKASI
// ============================================================================

define('APP_NAME', 'AGINDU');
define('APP_TAGLINE', 'Agribisnis Indonesia Mendunia');
define('APP_ROOT', dirname(__DIR__));
define('STORAGE_PATH', APP_ROOT . DIRECTORY_SEPARATOR . 'storage');
define('LOG_FILE', STORAGE_PATH . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'app.log');

// Batas upload file (2 MB)
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);
