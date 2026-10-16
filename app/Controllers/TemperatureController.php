<?php
/**
 * AGINDU - Temperature Monitoring & IoT Simulation Controller
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Batch.php';
require_once __DIR__ . '/../Models/TemperatureLog.php';
require_once __DIR__ . '/../Models/Commodity.php';

class TemperatureController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        // Ambil daftar seluruh batch aktif atau terbaru
        $batches = Database::fetchAll(
            "SELECT b.id, b.batch_code, b.status, c.name AS commodity_name, c.temp_min, c.temp_max
             FROM batches b
             JOIN commodities c ON b.commodity_id = c.id
             ORDER BY b.id DESC"
        );

        $selectedBatchId = isset($_GET['batch_id']) ? (int)$_GET['batch_id'] : ($batches[0]['id'] ?? 0);
        $selectedBatch = null;

        if ($selectedBatchId > 0) {
            $selectedBatch = Batch::findById($selectedBatchId);
        }

        $alerts = TemperatureLog::getRecentAlerts(12);

        $this->view('temperature.index', [
            'pageTitle'     => 'Pemantauan Suhu IoT & Rantai Dingin',
            'activeNav'     => 'temperature',
            'batches'       => $batches,
            'selectedBatch' => $selectedBatch,
            'alerts'        => $alerts
        ], 'main');
    }

    /**
     * Endpoint API JSON: Ambil 30 log suhu terbaru untuk batch
     */
    public function getLogs(int $batchId): void
    {
        $this->requireAuth();

        $batch = Batch::findById($batchId);
        if (!$batch) {
            $this->json(['error' => 'Batch tidak ditemukan.'], 404);
        }

        $logs = TemperatureLog::getRecentByBatchId($batchId, 30);
        $latest = TemperatureLog::getLatestByBatchId($batchId);

        $this->json([
            'batch_code' => $batch['batch_code'],
            'commodity'  => $batch['commodity_name'],
            'temp_min'   => (float)$batch['temp_min'],
            'temp_max'   => (float)$batch['temp_max'],
            'logs'       => $logs,
            'latest'     => $latest
        ]);
    }

    /**
     * Endpoint API JSON: Tambah pembacaan acak realistis simulasi IoT
     */
    public function simulate(int $batchId): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $batch = Batch::findById($batchId);
        if (!$batch) {
            $this->json(['error' => 'Batch tidak ditemukan.'], 404);
        }

        $min = (float)$batch['temp_min'];
        $max = (float)$batch['temp_max'];
        $tolerance = (float)($batch['temp_tolerance'] ?? 1.5);

        // Simulasi pembacaan suhu realistis
        $latest = TemperatureLog::getLatestByBatchId($batchId);
        $currentBase = $latest ? (float)$latest['temperature'] : (($min + $max) / 2);

        // Peluang 12% anomali suhu (lonjakan sementara)
        $isAnomaly = (random_int(1, 100) <= 12);

        if ($isAnomaly) {
            // Lonjakan di atas batas maksimum
            $newTemp = $max + (random_int(15, 35) / 10.0);
            $status = 'danger';
        } else {
            // Fluktuasi normal kecil +/- 0.4 C di sekitar nilai sebelumnya, ditarik ke rentang aman
            $delta = (random_int(-4, 4) / 10.0);
            $newTemp = $currentBase + $delta;

            // Batasi agar tidak melenceng liar jika bukan anomali
            if ($newTemp < ($min - 0.5)) {
                $newTemp = $min + (random_int(1, 5) / 10.0);
            } elseif ($newTemp > ($max + 0.5)) {
                $newTemp = $max - (random_int(1, 5) / 10.0);
            }

            // Tentukan status
            if ($newTemp < $min || $newTemp > $max) {
                $status = (abs($newTemp - $min) > $tolerance || abs($newTemp - $max) > $tolerance) ? 'danger' : 'warning';
            } else {
                $status = 'normal';
            }
        }

        // Kelembaban acak 60 - 90%
        $humidity = random_int(65, 88) + (random_int(0, 9) / 10.0);

        $logId = TemperatureLog::create([
            'batch_id'    => $batchId,
            'temperature' => round($newTemp, 2),
            'humidity'    => round($humidity, 2),
            'status'      => $status,
            'is_anomaly'  => $isAnomaly ? 1 : 0,
            'recorded_at' => date('Y-m-d H:i:s')
        ]);

        $this->json([
            'success'     => true,
            'id'          => $logId,
            'temperature' => round($newTemp, 2),
            'humidity'    => round($humidity, 2),
            'status'      => $status,
            'is_anomaly'  => $isAnomaly,
            'recorded_at' => date('Y-m-d H:i:s')
        ]);
    }
}
