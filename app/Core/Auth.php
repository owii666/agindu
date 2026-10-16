<?php
/**
 * AGINDU - Authentication Service
 */

declare(strict_types=1);

require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Database.php';

class Auth
{
    private const SESSION_USER_KEY = 'auth_user_id';

    public static function attempt(string $login, string $password): array
    {
        // 1. Cek Rate Limiter
        $rateLimit = Session::checkLoginRateLimit();
        if (!$rateLimit['allowed']) {
            return [
                'success' => false,
                'message' => $rateLimit['message']
            ];
        }

        // 2. Cari pengguna berdasarkan username atau email
        $cleanLogin = strtolower(trim($login));
        $user = Database::fetch(
            "SELECT * FROM users WHERE email = :login_email OR username = :login_user LIMIT 1",
            ['login_email' => $cleanLogin, 'login_user' => $cleanLogin]
        );

        if (!$user) {
            Session::recordFailedLogin();
            return [
                'success' => false,
                'message' => 'Username/email atau kata sandi tidak cocok dengan data kami.'
            ];
        }

        // 3. Verifikasi kata sandi
        if (!password_verify($password, $user['password_hash'])) {
            Session::recordFailedLogin();
            return [
                'success' => false,
                'message' => 'Username/email atau kata sandi tidak cocok dengan data kami.'
            ];
        }

        // 4. Sukses: Regenerasi session ID untuk mencegah session fixation
        Session::regenerate();
        Session::resetLoginRateLimit();
        Session::set(self::SESSION_USER_KEY, (int)$user['id']);

        return [
            'success' => true,
            'user' => $user
        ];
    }

    public static function check(): bool
    {
        return Session::has(self::SESSION_USER_KEY);
    }

    public static function id(): ?int
    {
        return Session::get(self::SESSION_USER_KEY);
    }

    public static function user(): ?array
    {
        $id = self::id();
        if (!$id) {
            return null;
        }

        static $cachedUser = null;
        if ($cachedUser && (int)$cachedUser['id'] === $id) {
            return $cachedUser;
        }

        $user = Database::fetch(
            "SELECT id, name, username, email, role, company_name, created_at FROM users WHERE id = :id LIMIT 1",
            ['id' => $id]
        );

        if (!$user) {
            self::logout();
            return null;
        }

        $cachedUser = $user;
        return $user;
    }

    public static function logout(): void
    {
        Session::remove(self::SESSION_USER_KEY);
        Session::destroy();
    }
}
