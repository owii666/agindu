<?php
/**
 * AGINDU - Timeline Event Model
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Database.php';

class TimelineEvent
{
    public static function getByBatchId(int $batchId): array
    {
        return Database::fetchAll(
            "SELECT * FROM timeline_events WHERE batch_id = :batch_id ORDER BY recorded_at ASC, id ASC",
            ['batch_id' => $batchId]
        );
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO timeline_events (
                    batch_id, stage, title, location, description, recorded_at, created_by
                ) VALUES (
                    :batch_id, :stage, :title, :location, :description, :recorded_at, :created_by
                )";

        Database::query($sql, [
            'batch_id'    => (int)$data['batch_id'],
            'stage'       => $data['stage'],
            'title'       => $data['title'],
            'location'    => $data['location'],
            'description' => $data['description'] ?? null,
            'recorded_at' => $data['recorded_at'],
            'created_by'  => $data['created_by'] ?? 'Petugas Lapangan'
        ]);

        return (int)Database::lastInsertId();
    }
}
