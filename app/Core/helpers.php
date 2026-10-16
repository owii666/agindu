<?php
/**
 * AGINDU - Global Helper Functions
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';

/**
 * Buat URL absolut aplikasi
 */
function url(string $path = ''): string
{
    $cleanPath = '/' . ltrim($path, '/');
    if ($path === '' || $path === '/') {
        return BASE_URL === '' ? '/' : BASE_URL;
    }
    return BASE_URL . $cleanPath;
}

/**
 * Buat URL untuk asset (CSS, JS, Vendor, Gambar)
 */
function asset(string $path = ''): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

/**
 * Escape teks untuk mencegah Cross-Site Scripting (XSS)
 */
function e(?string $value): string
{
    if ($value === null) {
        return '';
    }
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Dapatkan token CSRF saat ini
 */
function csrf_token(): string
{
    return Csrf::getToken();
}

/**
 * Render input field tersembunyi berisi token CSRF
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Redirect ke URL aplikasi
 */
function redirect(string $path): void
{
    $target = str_starts_with($path, 'http') ? $path : url($path);
    header('Location: ' . $target);
    exit;
}

/**
 * Set flash message ke session
 */
function set_flash(string $type, string $message): void
{
    Session::setFlash($type, $message);
}

/**
 * Ambil flash message dari session
 */
function get_flash(string $type): ?string
{
    return Session::getFlash($type);
}

/**
 * Periksa keberadaan flash message
 */
function has_flash(string $type): bool
{
    return Session::hasFlash($type);
}

/**
 * Ambil data user yang sedang login
 */
function auth_user(): ?array
{
    return Auth::user();
}

/**
 * Cek apakah user sudah login
 */
function is_logged_in(): bool
{
    return Auth::check();
}

/**
 * Format tanggal dalam bahasa Indonesia
 */
function format_date(?string $datetime, string $format = 'd M Y, H:i'): string
{
    if (empty($datetime)) {
        return '-';
    }
    try {
        $dt = new DateTime($datetime);
        $bulanIndo = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        $d = $dt->format('d');
        $m = (int)$dt->format('m');
        $y = $dt->format('Y');
        $time = $dt->format('H:i');

        if ($format === 'd M Y') {
            return sprintf('%s %s %s', $d, $bulanIndo[$m], $y);
        }
        return sprintf('%s %s %s, %s WIB', $d, $bulanIndo[$m], $y, $time);
    } catch (Exception $e) {
        return $datetime;
    }
}

/**
 * Format angka berat komoditas (kg)
 */
function format_kg(float|int|string $val): string
{
    $num = (float)$val;
    return number_format($num, 0, ',', '.') . ' kg';
}

/**
 * Penerjemah sederhana berbasis file bahasa (app/Lang/{lang}.php)
 */
function t(string $key, array $replace = []): string
{
    static $translations = [];
    $lang = $_SESSION['lang'] ?? 'en';
    if (!in_array($lang, ['id', 'en'], true)) {
        $lang = 'en';
    }

    if (!isset($translations[$lang])) {
        $filePath = APP_ROOT . '/app/Lang/' . $lang . '.php';
        if (file_exists($filePath)) {
            $translations[$lang] = require $filePath;
        } else {
            $translations[$lang] = [];
        }
    }

    $text = $translations[$lang][$key] ?? $key;
    foreach ($replace as $k => $v) {
        $text = str_replace(':' . $k, (string)$v, $text);
    }
    return $text;
}
