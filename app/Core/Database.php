<?php
/**
 * AGINDU - Database Model Base / Query Helper
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

class Database
{
    public static function pdo(): PDO
    {
        return DatabaseConnection::getConnection();
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $res = self::query($sql, $params)->fetch();
        return $res ?: null;
    }

    public static function lastInsertId(): string
    {
        return self::pdo()->lastInsertId();
    }
}
