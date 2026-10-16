<?php
/**
 * AGINDU - Public Traceability Passport Controller
 * Akses Publik tanpa login via pemindaian QR Code
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Batch.php';
require_once __DIR__ . '/../Models/TimelineEvent.php';
require_once __DIR__ . '/../Models/TemperatureLog.php';

class TraceController extends Controller
{
    public function show(string $token): void
    {
        // Tangani pemilihan bahasa dari query string (?lang=id atau ?lang=en)
        if (isset($_GET['lang']) && in_array($_GET['lang'], ['id', 'en'], true)) {
            Session::set('lang', $_GET['lang']);
        } elseif (!Session::has('lang')) {
            // Default bahasa tampilan publik adalah Bahasa Inggris (pasar ekspor/buyer internasional)
            Session::set('lang', 'en');
        }

        $batch = Batch::findByToken($token);

        if (!$batch) {
            http_response_code(404);
            $this->view('errors.404', [
                'pageTitle' => 'Batch Record Not Found',
                'message'   => 'The requested traceability passport token does not exist or has expired.'
            ], null);
            return;
        }

        $timelineEvents = TimelineEvent::getByBatchId((int)$batch['id']);
        $latestTemp = TemperatureLog::getLatestByBatchId((int)$batch['id']);

        // Evaluasi kondisi termal
        $tempStatus = 'nodata';
        if ($latestTemp) {
            $tVal = (float)$latestTemp['temperature'];
            $tMin = (float)$batch['temp_min'];
            $tMax = (float)$batch['temp_max'];
            $tolerance = (float)($batch['temp_tolerance'] ?? 1.5);

            if ($latestTemp['is_anomaly'] == 1 || $latestTemp['status'] === 'danger' || $tVal < ($tMin - $tolerance) || $tVal > ($tMax + $tolerance)) {
                $tempStatus = 'danger';
            } elseif ($latestTemp['status'] === 'warning' || $tVal < $tMin || $tVal > $tMax) {
                $tempStatus = 'warning';
            } else {
                $tempStatus = 'normal';
            }
        }

        $this->view('trace.index', [
            'pageTitle'      => "Certificate of Origin - {$batch['batch_code']}",
            'batch'          => $batch,
            'timelineEvents' => $timelineEvents,
            'latestTemp'     => $latestTemp,
            'tempStatus'     => $tempStatus
        ], 'public');
    }
}
