<?php
/**
 * AGINDU - Temperature Log Model
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Database.php';

class TemperatureLog
{
    /**
     * Ambil data suhu terbaru untuk grafik Chart.js (terurut kronologis ASC)
     */
    public static function getRecentByBatchId(int $batchId, int $limit = 30): array
    {
        $sql = "SELECT * FROM (
                    SELECT * FROM temperature_logs 
                    WHERE batch_id = :batch_id 
                    ORDER BY recorded_at DESC, id DESC 
                    LIMIT {$limit}
                ) sub 
                ORDER BY recorded_at ASC, id ASC";

        return Database::fetchAll($sql, ['batch_id' => $batchId]);
    }

    /**
     * Ambil pembacaan suhu paling terakhir untuk batch
     */
    public static function getLatestByBatchId(int $batchId): ?array
    {
        $sql = "SELECT * FROM temperature_logs 
                WHERE batch_id = :batch_id 
                ORDER BY recorded_at DESC, id DESC 
                LIMIT 1";

        return Database::fetch($sql, ['batch_id' => $batchId]);
    }

    /**
     * Ambil daftar semua peringatan anomali / di luar rentang
     */
    public static function getRecentAlerts(int $limit = 15): array
    {
        $sql = "SELECT tl.*, b.batch_code, c.name AS commodity_name, c.temp_min, c.temp_max
                FROM temperature_logs tl
                JOIN batches b ON tl.batch_id = b.id
                JOIN commodities c ON b.commodity_id = c.id
                WHERE tl.status IN ('warning', 'danger') OR tl.is_anomaly = 1
                ORDER BY tl.recorded_at DESC, tl.id DESC
                LIMIT {$limit}";

        return Database::fetchAll($sql);
    }

    /**
     * Catat pembacaan sensor suhu baru
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO temperature_logs (
                    batch_id, temperature, humidity, status, is_anomaly, recorded_at
                ) VALUES (
                    :batch_id, :temperature, :humidity, :status, :is_anomaly, :recorded_at
                )";

        Database::query($sql, [
            'batch_id'    => (int)$data['batch_id'],
            'temperature' => (float)$data['temperature'],
            'humidity'    => isset($data['humidity']) ? (float)$data['humidity'] : null,
            'status'      => $data['status'],
            'is_anomaly'  => (int)($data['is_anomaly'] ?? 0),
            'recorded_at' => $data['recorded_at']
        ]);

        return (int)Database::lastInsertId();
    }
}
