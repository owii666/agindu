<?php
/**
 * AGINDU - Destination Country Model
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Database.php';

class DestinationCountry
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM destination_countries ORDER BY name ASC");
    }

    public static function findById(int $id): ?array
    {
        return Database::fetch("SELECT * FROM destination_countries WHERE id = :id LIMIT 1", ['id' => $id]);
    }
}
