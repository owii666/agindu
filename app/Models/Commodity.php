<?php
/**
 * AGINDU - Commodity Model
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Database.php';

class Commodity
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM commodities ORDER BY name ASC");
    }

    public static function findById(int $id): ?array
    {
        return Database::fetch("SELECT * FROM commodities WHERE id = :id LIMIT 1", ['id' => $id]);
    }
}
