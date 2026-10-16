<?php
/**
 * AGINDU - Batch Management Controller
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Batch.php';
require_once __DIR__ . '/../Models/Commodity.php';
require_once __DIR__ . '/../Models/DestinationCountry.php';
require_once __DIR__ . '/../Models/TimelineEvent.php';
require_once __DIR__ . '/../Models/TemperatureLog.php';
require_once __DIR__ . '/../Models/ExportDocChecklist.php';

class BatchController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $page = (int)($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $commodityId = !empty($_GET['commodity_id']) ? (int)$_GET['commodity_id'] : null;

        $pagination = Batch::paginate($page, 10, $search ?: null, $status ?: null, $commodityId);
        $commodities = Commodity::all();

        $this->view('batches.index', [
            'pageTitle'    => 'Daftar Batch Ekspor',
            'activeNav'    => 'batches',
            'pagination'   => $pagination,
            'commodities'  => $commodities,
            'search'       => $search,
            'status'       => $status,
            'commodityId'  => $commodityId
        ], 'main');
    }

    public function create(): void
    {
        $this->requireAuth();

        $commodities = Commodity::all();
        $countries = DestinationCountry::all();
        $generatedCode = Batch::generateBatchCode();

        $this->view('batches.create', [
            'pageTitle'     => 'Tambah Batch Baru',
            'activeNav'     => 'batches',
            'commodities'   => $commodities,
            'countries'     => $countries,
            'generatedCode' => $generatedCode
        ], 'main');
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $commodityId = (int)($_POST['commodity_id'] ?? 0);
        $countryId = (int)($_POST['destination_country_id'] ?? 0);
        $producerName = trim($_POST['producer_name'] ?? '');
        $originRegency = trim($_POST['origin_regency'] ?? '');
        $originProvince = trim($_POST['origin_province'] ?? '');
        $harvestDate = trim($_POST['harvest_date'] ?? '');
        $volumeKg = (float)($_POST['volume_kg'] ?? 0);
        $status = trim($_POST['status'] ?? 'processing');
        $notes = trim($_POST['notes'] ?? '');

        $validator = Validator::make([
            'commodity_id'           => $commodityId,
            'destination_country_id' => $countryId,
            'producer_name'          => $producerName,
            'origin_regency'         => $originRegency,
            'origin_province'        => $originProvince,
            'harvest_date'           => $harvestDate,
            'volume_kg'              => $volumeKg
        ]);

        if (!$validator->validate([
            'commodity_id'           => 'required|numeric|min:1',
            'destination_country_id' => 'required|numeric|min:1',
            'producer_name'          => 'required|min:3',
            'origin_regency'         => 'required|min:2',
            'origin_province'        => 'required|min:2',
            'harvest_date'           => 'required|date',
            'volume_kg'              => 'required|numeric|min:1'
        ])) {
            set_flash('error', 'Silakan periksa kembali formulir Anda. Pastikan semua data wajib diisi dengan benar.');
            redirect('/batches/create');
        }

        $batchCode = Batch::generateBatchCode();
        $publicToken = Batch::generatePublicToken();

        $batchId = Batch::create([
            'batch_code'             => $batchCode,
            'public_token'           => $publicToken,
            'commodity_id'           => $commodityId,
            'producer_name'          => $producerName,
            'origin_regency'         => $originRegency,
            'origin_province'        => $originProvince,
            'harvest_date'           => $harvestDate,
            'volume_kg'              => $volumeKg,
            'destination_country_id' => $countryId,
            'status'                 => $status,
            'notes'                  => $notes
        ]);

        // Inisialisasi peristiwa panen perdana
        TimelineEvent::create([
            'batch_id'    => $batchId,
            'stage'       => 'harvest',
            'title'       => 'Pencatatan Panen Perdana',
            'location'    => "{$originRegency}, {$originProvince}",
            'description' => "Batch berhasil didaftarkan di sistem AGINDU dengan volume {$volumeKg} kg.",
            'recorded_at' => date('Y-m-d H:i:s'),
            'created_by'  => auth_user()['name'] ?? 'Petugas Ekspor'
        ]);

        // Inisialisasi daftar checklist dokumen ekspor
        ExportDocChecklist::initializeForBatch($batchId, $countryId, $commodityId);

        set_flash('success', "Batch {$batchCode} berhasil dibuat bersama kode QR pelacakan publik.");
        redirect('/batches/' . $batchId);
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $batch = Batch::findById($id);
        if (!$batch) {
            set_flash('error', 'Batch tidak ditemukan.');
            redirect('/batches');
        }

        $timelineEvents = TimelineEvent::getByBatchId($id);
        $checklists = ExportDocChecklist::getByBatchId($id);
        $latestTemp = TemperatureLog::getLatestByBatchId($id);
        $publicUrl = url('/trace/' . $batch['public_token']);

        $this->view('batches.show', [
            'pageTitle'      => "Detail Batch {$batch['batch_code']}",
            'activeNav'      => 'batches',
            'batch'          => $batch,
            'timelineEvents' => $timelineEvents,
            'checklists'     => $checklists,
            'latestTemp'     => $latestTemp,
            'publicUrl'      => $publicUrl
        ], 'main');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();

        $batch = Batch::findById($id);
        if (!$batch) {
            set_flash('error', 'Batch tidak ditemukan.');
            redirect('/batches');
        }

        $commodities = Commodity::all();
        $countries = DestinationCountry::all();

        $this->view('batches.edit', [
            'pageTitle'   => "Edit Batch {$batch['batch_code']}",
            'activeNav'   => 'batches',
            'batch'       => $batch,
            'commodities' => $commodities,
            'countries'   => $countries
        ], 'main');
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $batch = Batch::findById($id);
        if (!$batch) {
            set_flash('error', 'Batch tidak ditemukan.');
            redirect('/batches');
        }

        $commodityId = (int)($_POST['commodity_id'] ?? 0);
        $countryId = (int)($_POST['destination_country_id'] ?? 0);
        $producerName = trim($_POST['producer_name'] ?? '');
        $originRegency = trim($_POST['origin_regency'] ?? '');
        $originProvince = trim($_POST['origin_province'] ?? '');
        $harvestDate = trim($_POST['harvest_date'] ?? '');
        $volumeKg = (float)($_POST['volume_kg'] ?? 0);
        $status = trim($_POST['status'] ?? 'processing');
        $notes = trim($_POST['notes'] ?? '');

        $validator = Validator::make([
            'commodity_id'           => $commodityId,
            'destination_country_id' => $countryId,
            'producer_name'          => $producerName,
            'origin_regency'         => $originRegency,
            'origin_province'        => $originProvince,
            'harvest_date'           => $harvestDate,
            'volume_kg'              => $volumeKg
        ]);

        if (!$validator->validate([
            'commodity_id'           => 'required|numeric|min:1',
            'destination_country_id' => 'required|numeric|min:1',
            'producer_name'          => 'required|min:3',
            'origin_regency'         => 'required|min:2',
            'origin_province'        => 'required|min:2',
            'harvest_date'           => 'required|date',
            'volume_kg'              => 'required|numeric|min:1'
        ])) {
            set_flash('error', 'Gagal memperbarui batch. Pastikan seluruh isian valid.');
            redirect("/batches/{$id}/edit");
        }

        Batch::update($id, [
            'commodity_id'           => $commodityId,
            'producer_name'          => $producerName,
            'origin_regency'         => $originRegency,
            'origin_province'        => $originProvince,
            'harvest_date'           => $harvestDate,
            'volume_kg'              => $volumeKg,
            'destination_country_id' => $countryId,
            'status'                 => $status,
            'notes'                  => $notes
        ]);

        set_flash('success', "Informasi batch {$batch['batch_code']} berhasil diperbarui.");
        redirect("/batches/{$id}");
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $batch = Batch::findById($id);
        if (!$batch) {
            set_flash('error', 'Batch tidak ditemukan.');
            redirect('/batches');
        }

        Batch::delete($id);
        set_flash('success', "Batch {$batch['batch_code']} berhasil dihapus dari sistem.");
        redirect('/batches');
    }

    public function addTimeline(int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $batch = Batch::findById($id);
        if (!$batch) {
            set_flash('error', 'Batch tidak ditemukan.');
            redirect('/batches');
        }

        $stage = trim($_POST['stage'] ?? 'warehouse');
        $title = trim($_POST['title'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $recordedAt = trim($_POST['recorded_at'] ?? date('Y-m-d H:i:s'));

        if (empty($title) || empty($location)) {
            set_flash('error', 'Judul tahapan dan lokasi wajib diisi.');
            redirect("/batches/{$id}");
        }

        TimelineEvent::create([
            'batch_id'    => $id,
            'stage'       => $stage,
            'title'       => $title,
            'location'    => $location,
            'description' => $description,
            'recorded_at' => $recordedAt,
            'created_by'  => auth_user()['name'] ?? 'Petugas Ekspor'
        ]);

        set_flash('success', "Peristiwa linimasa baru berhasil ditambahkan pada batch {$batch['batch_code']}.");
        redirect("/batches/{$id}");
    }

    public function qr(int $id): void
    {
        $this->requireAuth();

        $batch = Batch::findById($id);
        if (!$batch) {
            set_flash('error', 'Batch tidak ditemukan.');
            redirect('/batches');
        }

        $publicUrl = url('/trace/' . $batch['public_token']);

        $this->view('batches.qr', [
            'pageTitle' => "Label QR Batch {$batch['batch_code']}",
            'activeNav' => 'batches',
            'batch'     => $batch,
            'publicUrl' => $publicUrl
        ], 'main');
    }
}
