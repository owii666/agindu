<?php
/**
 * AGINDU - Producer Dashboard Controller
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Batch.php';
require_once __DIR__ . '/../Models/TemperatureLog.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $stats = Batch::getDashboardStats();
        $recentAlerts = TemperatureLog::getRecentAlerts(5);

        $this->view('dashboard.index', [
            'pageTitle'    => 'Dashboard Ringkasan Ekspor',
            'activeNav'    => 'dashboard',
            'stats'        => $stats,
            'recentAlerts' => $recentAlerts
        ], 'main');
    }
}
