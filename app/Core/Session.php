<?php
/**
 * AGINDU - Secure Session Management
 */

declare(strict_types=1);

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        if (!headers_sent()) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

            // Batasi masa aktif cookie session (2 jam)
            $lifetime = 7200;

            session_name('AGINDU_SESS');

            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
        } elseif (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        self::$started = true;

        // Inisialisasi container flash jika belum ada
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        self::$started = false;
    }

    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['_flash'][$type] = $message;
    }

    public static function hasFlash(string $type): bool
    {
        self::start();
        return isset($_SESSION['_flash'][$type]);
    }

    public static function getFlash(string $type): ?string
    {
        self::start();
        if (isset($_SESSION['_flash'][$type])) {
            $msg = $_SESSION['_flash'][$type];
            unset($_SESSION['_flash'][$type]);
            return $msg;
        }
        return null;
    }

    /**
     * Rate limiter sederhana berbasis session dan IP untuk proteksi brute force login
     * Maksimal 5 percobaan gagal per 15 menit.
     */
    public static function checkLoginRateLimit(): array
    {
        self::start();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = 'rate_limit_login_' . md5($ip);
        $now = time();

        $data = self::get($key, [
            'attempts' => 0,
            'first_attempt' => $now,
            'locked_until' => 0
        ]);

        if ($data['locked_until'] > $now) {
            $waitMinutes = (int)ceil(($data['locked_until'] - $now) / 60);
            return [
                'allowed' => false,
                'wait_minutes' => $waitMinutes,
                'message' => "Terlalu banyak percobaan login gagal. Silakan coba kembali dalam {$waitMinutes} menit."
            ];
        }

        // Reset jika interval 15 menit sudah lewat
        if ($now - $data['first_attempt'] > 900) {
            $data = ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];
            self::set($key, $data);
        }

        return ['allowed' => true];
    }

    public static function recordFailedLogin(): void
    {
        self::start();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = 'rate_limit_login_' . md5($ip);
        $now = time();

        $data = self::get($key, [
            'attempts' => 0,
            'first_attempt' => $now,
            'locked_until' => 0
        ]);

        $data['attempts']++;
        if ($data['attempts'] >= 5) {
            // Kunci selama 15 menit (900 detik)
            $data['locked_until'] = $now + 900;
        }

        self::set($key, $data);
    }

    public static function resetLoginRateLimit(): void
    {
        self::start();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = 'rate_limit_login_' . md5($ip);
        self::remove($key);
    }
}
