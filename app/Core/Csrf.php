<?php
/**
 * AGINDU - CSRF Protection Manager
 */

declare(strict_types=1);

require_once __DIR__ . '/Session.php';

class Csrf
{
    public static function getToken(): string
    {
        Session::start();
        $token = Session::get('_csrf_token');
        if (empty($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf_token', $token);
        }
        return $token;
    }

    public static function validate(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        $stored = Session::get('_csrf_token');
        if (empty($stored)) {
            return false;
        }
        return hash_equals($stored, $token);
    }

    public static function validateRequest(): bool
    {
        // Cek dari POST body atau header HTTP_X_CSRF_TOKEN
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        return self::validate($token);
    }
}
