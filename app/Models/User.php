<?php
/**
 * AGINDU - User Model
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Database.php';

class User
{
    public static function findById(int $id): ?array
    {
        return Database::fetch("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetch("SELECT * FROM users WHERE email = :email LIMIT 1", ['email' => strtolower(trim($email))]);
    }

    public static function findByUsername(string $username): ?array
    {
        return Database::fetch("SELECT * FROM users WHERE username = :username LIMIT 1", ['username' => strtolower(trim($username))]);
    }
}
