<?php
/**
 * AGINDU - Export Document Checklist Model
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Database.php';

class ExportDocChecklist
{
    public static function getByBatchId(int $batchId): array
    {
        return Database::fetchAll(
            "SELECT * FROM export_doc_checklists WHERE batch_id = :batch_id ORDER BY id ASC",
            ['batch_id' => $batchId]
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::fetch(
            "SELECT * FROM export_doc_checklists WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public static function updateStatus(int $id, bool $isChecked, ?string $notes = null): bool
    {
        $checkedAt = $isChecked ? date('Y-m-d H:i:s') : null;
        $sql = "UPDATE export_doc_checklists SET 
                    is_checked = :is_checked,
                    checked_at = :checked_at,
                    notes = :notes
                WHERE id = :id";

        Database::query($sql, [
            'id'         => $id,
            'is_checked' => $isChecked ? 1 : 0,
            'checked_at' => $checkedAt,
            'notes'      => $notes
        ]);

        return true;
    }

    /**
     * Inisialisasi daftar dokumen standar otomatis jika batch baru dibuat
     */
    public static function initializeForBatch(int $batchId, int $countryId, int $commodityId): void
    {
        $existing = self::getByBatchId($batchId);
        if (!empty($existing)) {
            return;
        }

        // Ambil info negara dan komoditas
        $country = Database::fetch("SELECT * FROM destination_countries WHERE id = :id", ['id' => $countryId]);
        $commodity = Database::fetch("SELECT * FROM commodities WHERE id = :id", ['id' => $commodityId]);

        $defaultDocs = [
            ['key' => 'inv', 'name' => 'Commercial Invoice (Faktur Komersial)', 'mandatory' => 1],
            ['key' => 'pack_list', 'name' => 'Packing List (Daftar Kemasan & Berat Bersih)', 'mandatory' => 1],
            ['key' => 'coo', 'name' => 'Certificate of Origin (Surat Keterangan Asal / SKA)', 'mandatory' => 1],
            ['key' => 'phyto_hc', 'name' => ($commodity['category'] === 'Perikanan' ? 'Health Certificate BKIPM KKP' : 'Phytosanitary Certificate Karantina Tumbuhan'), 'mandatory' => 1],
            ['key' => 'bl_awb', 'name' => 'Bill of Lading (B/L) atau Air Waybill (AWB)', 'mandatory' => 1],
        ];

        // Dokumen spesifik negara
        if ($country) {
            if ($country['code'] === 'JPN') {
                $defaultDocs[] = ['key' => 'coa_mrl', 'name' => 'Certificate of Analysis Residu Pestisida (Positive List MAFF)', 'mandatory' => 1];
            } elseif ($country['code'] === 'NLD') {
                $defaultDocs[] = ['key' => 'eudr', 'name' => 'EUDR Due Diligence Statement (Geolokasi Bebas Deforestasi)', 'mandatory' => 1];
            } elseif ($country['code'] === 'USA') {
                $defaultDocs[] = ['key' => 'fda_pn', 'name' => 'FDA Prior Notice Confirmation Number', 'mandatory' => 1];
            } elseif ($country['code'] === 'CHN') {
                $defaultDocs[] = ['key' => 'gacc_ciq', 'name' => 'Registrasi Fasilitas GACC & Sertifikat CIQ', 'mandatory' => 1];
            } elseif ($country['code'] === 'SAU') {
                $defaultDocs[] = ['key' => 'halal_sfda', 'name' => 'Sertifikat Halal Diakui SFDA Arab Saudi', 'mandatory' => 1];
            }
        }

        foreach ($defaultDocs as $doc) {
            Database::query(
                "INSERT INTO export_doc_checklists (batch_id, doc_key, doc_name, is_mandatory, is_checked) 
                 VALUES (:batch_id, :doc_key, :doc_name, :is_mandatory, 0)",
                [
                    'batch_id'     => $batchId,
                    'doc_key'      => $doc['key'],
                    'doc_name'     => $doc['name'],
                    'is_mandatory' => $doc['mandatory']
                ]
            );
        }
    }
}
