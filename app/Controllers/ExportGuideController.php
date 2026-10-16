<?php
/**
 * AGINDU - Export Guide & Compliance Controller
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Commodity.php';
require_once __DIR__ . '/../Models/DestinationCountry.php';
require_once __DIR__ . '/../Models/Batch.php';
require_once __DIR__ . '/../Models/ExportDocChecklist.php';

class ExportGuideController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $commodities = Commodity::all();
        $countries = DestinationCountry::all();

        $selectedCommodityId = !empty($_GET['commodity_id']) ? (int)$_GET['commodity_id'] : ($commodities[0]['id'] ?? 1);
        $selectedCountryId = !empty($_GET['country_id']) ? (int)$_GET['country_id'] : ($countries[0]['id'] ?? 1);
        $selectedBatchId = !empty($_GET['batch_id']) ? (int)$_GET['batch_id'] : 0;

        $selectedCommodity = Commodity::findById($selectedCommodityId);
        $selectedCountry = DestinationCountry::findById($selectedCountryId);

        // Cari batch yang sesuai dengan kombinasi komoditas dan negara untuk demo pengisian checklist langsung
        $matchingBatches = Database::fetchAll(
            "SELECT id, batch_code FROM batches 
             WHERE commodity_id = :cid AND destination_country_id = :did 
             ORDER BY id DESC",
            ['cid' => $selectedCommodityId, 'did' => $selectedCountryId]
        );

        $checklists = [];
        $activeBatch = null;

        if ($selectedBatchId > 0) {
            $activeBatch = Batch::findById($selectedBatchId);
            if ($activeBatch) {
                $checklists = ExportDocChecklist::getByBatchId($selectedBatchId);
            }
        } elseif (!empty($matchingBatches)) {
            $activeBatch = Batch::findById((int)$matchingBatches[0]['id']);
            if ($activeBatch) {
                $checklists = ExportDocChecklist::getByBatchId((int)$activeBatch['id']);
            }
        }

        $this->view('export_guide.index', [
            'pageTitle'           => 'Panduan Dokumen & Regulasi Ekspor',
            'activeNav'           => 'export_guide',
            'commodities'         => $commodities,
            'countries'           => $countries,
            'selectedCommodity'   => $selectedCommodity,
            'selectedCountry'     => $selectedCountry,
            'matchingBatches'     => $matchingBatches,
            'activeBatch'         => $activeBatch,
            'checklists'          => $checklists
        ], 'main');
    }

    public function toggle(int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $item = ExportDocChecklist::findById($id);
        if (!$item) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                $this->json(['error' => 'Dokumen checklist tidak ditemukan.'], 404);
            }
            redirect('/export-guide');
        }

        $newStatus = !(bool)$item['is_checked'];
        ExportDocChecklist::updateStatus($id, $newStatus);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->json([
                'success'    => true,
                'is_checked' => $newStatus,
                'checked_at' => $newStatus ? date('d M Y, H:i') : null
            ]);
        }

        set_flash('success', 'Status checklist dokumen berhasil diperbarui.');
        $redirectUrl = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : '/export-guide';
        redirect($redirectUrl);
    }
}
