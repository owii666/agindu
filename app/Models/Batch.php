<?php
/**
 * AGINDU - Batch Model
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Database.php';

class Batch
{
    /**
     * Hasilkan kode batch unik berurutan format AGD-TAHUN-NNNN
     */
    public static function generateBatchCode(): string
    {
        $year = date('Y');
        $prefix = "AGD-{$year}-";

        $latest = Database::fetch(
            "SELECT batch_code FROM batches WHERE batch_code LIKE :prefix ORDER BY id DESC LIMIT 1",
            ['prefix' => "{$prefix}%"]
        );

        if ($latest && preg_match('/AGD-\d{4}-(\d+)/', $latest['batch_code'], $matches)) {
            $num = (int)$matches[1] + 1;
        } else {
            $num = 1;
        }

        return sprintf('%s%04d', $prefix, $num);
    }

    /**
     * Hasilkan token acak publik kriptografis (32 karakter hex)
     */
    public static function generatePublicToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Ambil data batch dengan paginasi, pencarian, dan filter
     */
    public static function paginate(int $page = 1, int $perPage = 10, ?string $search = null, ?string $status = null, ?int $commodityId = null): array
    {
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(b.batch_code LIKE :search OR b.producer_name LIKE :search OR b.origin_regency LIKE :search OR c.name LIKE :search OR dc.name LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($status)) {
            $conditions[] = "b.status = :status";
            $params['status'] = $status;
        }

        if (!empty($commodityId)) {
            $conditions[] = "b.commodity_id = :commodity_id";
            $params['commodity_id'] = $commodityId;
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Hitung total baris
        $countSql = "SELECT COUNT(*) FROM batches b 
                     JOIN commodities c ON b.commodity_id = c.id 
                     JOIN destination_countries dc ON b.destination_country_id = dc.id 
                     {$whereClause}";
        $total = (int)Database::fetch($countSql, $params)['COUNT(*)'];

        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        // Ambil data halaman
        $dataSql = "SELECT b.*, 
                           c.name AS commodity_name, c.temp_min, c.temp_max, c.category AS commodity_category,
                           dc.name AS destination_country_name, dc.code AS country_code
                    FROM batches b
                    JOIN commodities c ON b.commodity_id = c.id
                    JOIN destination_countries dc ON b.destination_country_id = dc.id
                    {$whereClause}
                    ORDER BY b.id DESC
                    LIMIT {$perPage} OFFSET {$offset}";

        $items = Database::fetchAll($dataSql, $params);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages
        ];
    }

    public static function findById(int $id): ?array
    {
        $sql = "SELECT b.*, 
                       c.name AS commodity_name, c.temp_min, c.temp_max, c.temp_tolerance, c.category AS commodity_category,
                       dc.name AS destination_country_name, dc.code AS country_code,
                       dc.requirements_summary, dc.regulations_summary
                FROM batches b
                JOIN commodities c ON b.commodity_id = c.id
                JOIN destination_countries dc ON b.destination_country_id = dc.id
                WHERE b.id = :id
                LIMIT 1";
        return Database::fetch($sql, ['id' => $id]);
    }

    public static function findByToken(string $token): ?array
    {
        $sql = "SELECT b.*, 
                       c.name AS commodity_name, c.temp_min, c.temp_max, c.temp_tolerance, c.category AS commodity_category,
                       dc.name AS destination_country_name, dc.code AS country_code
                FROM batches b
                JOIN commodities c ON b.commodity_id = c.id
                JOIN destination_countries dc ON b.destination_country_id = dc.id
                WHERE b.public_token = :token
                LIMIT 1";
        return Database::fetch($sql, ['token' => trim($token)]);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO batches (
                    batch_code, public_token, commodity_id, producer_name, 
                    origin_regency, origin_province, harvest_date, volume_kg, 
                    destination_country_id, status, notes
                ) VALUES (
                    :batch_code, :public_token, :commodity_id, :producer_name,
                    :origin_regency, :origin_province, :harvest_date, :volume_kg,
                    :destination_country_id, :status, :notes
                )";

        Database::query($sql, [
            'batch_code'             => $data['batch_code'],
            'public_token'           => $data['public_token'],
            'commodity_id'           => (int)$data['commodity_id'],
            'producer_name'          => $data['producer_name'],
            'origin_regency'         => $data['origin_regency'],
            'origin_province'        => $data['origin_province'],
            'harvest_date'           => $data['harvest_date'],
            'volume_kg'              => (float)$data['volume_kg'],
            'destination_country_id' => (int)$data['destination_country_id'],
            'status'                 => $data['status'] ?? 'processing',
            'notes'                  => $data['notes'] ?? null
        ]);

        return (int)Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE batches SET 
                    commodity_id = :commodity_id,
                    producer_name = :producer_name,
                    origin_regency = :origin_regency,
                    origin_province = :origin_province,
                    harvest_date = :harvest_date,
                    volume_kg = :volume_kg,
                    destination_country_id = :destination_country_id,
                    status = :status,
                    notes = :notes
                WHERE id = :id";

        Database::query($sql, [
            'id'                     => $id,
            'commodity_id'           => (int)$data['commodity_id'],
            'producer_name'          => $data['producer_name'],
            'origin_regency'         => $data['origin_regency'],
            'origin_province'        => $data['origin_province'],
            'harvest_date'           => $data['harvest_date'],
            'volume_kg'              => (float)$data['volume_kg'],
            'destination_country_id' => (int)$data['destination_country_id'],
            'status'                 => $data['status'],
            'notes'                  => $data['notes'] ?? null
        ]);

        return true;
    }

    public static function delete(int $id): bool
    {
        Database::query("DELETE FROM batches WHERE id = :id", ['id' => $id]);
        return true;
    }

    /**
     * Ringkasan statistik untuk dashboard produsen
     */
    public static function getDashboardStats(): array
    {
        // 1. Total batch
        $totalBatches = (int)Database::fetch("SELECT COUNT(*) AS total FROM batches")['total'];

        // 2. Batch aktif (sedang proses, kemas, jalan, atau berlayar)
        $activeBatches = (int)Database::fetch(
            "SELECT COUNT(*) AS total FROM batches WHERE status IN ('processing', 'packed', 'in_transit', 'shipped')"
        )['total'];

        // 3. Jumlah peringatan suhu (anomali atau status warning/danger)
        $tempAlerts = (int)Database::fetch(
            "SELECT COUNT(*) AS total FROM temperature_logs WHERE status IN ('warning', 'danger') OR is_anomaly = 1"
        )['total'];

        // 4. Persentase kelengkapan dokumen ekspor
        $docStats = Database::fetch(
            "SELECT COUNT(*) AS total, SUM(CASE WHEN is_checked = 1 THEN 1 ELSE 0 END) AS checked FROM export_doc_checklists"
        );
        $docTotal = (int)($docStats['total'] ?? 0);
        $docChecked = (int)($docStats['checked'] ?? 0);
        $docCompletionRate = $docTotal > 0 ? (int)round(($docChecked / $docTotal) * 100) : 100;

        // 5. Batch terbaru (5 data)
        $recentBatches = Database::fetchAll(
            "SELECT b.*, c.name AS commodity_name, dc.name AS destination_country_name
             FROM batches b
             JOIN commodities c ON b.commodity_id = c.id
             JOIN destination_countries dc ON b.destination_country_id = dc.id
             ORDER BY b.id DESC LIMIT 5"
        );

        // 6. Distribusi per status untuk ringkasan grafik
        $statusCounts = Database::fetchAll(
            "SELECT status, COUNT(*) AS count FROM batches GROUP BY status"
        );

        return [
            'total_batches'        => $totalBatches,
            'active_batches'       => $activeBatches,
            'temp_alerts'          => $tempAlerts,
            'doc_completion_rate'  => $docCompletionRate,
            'recent_batches'       => $recentBatches,
            'status_counts'        => $statusCounts
        ];
    }
}
