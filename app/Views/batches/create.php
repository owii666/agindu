<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <div>
            <h1 class="card-title">Pendaftaran Batch Ekspor Baru</h1>
            <div class="card-subtitle">Kode batch otomatis dan token QR kriptografis publik akan diterbitkan</div>
        </div>
        <a href="<?= url('/batches') ?>" class="btn btn-outline btn-sm">&larr; Kembali</a>
    </div>

    <form action="<?= url('/batches') ?>" method="POST" class="card-body">
        <?= csrf_field() ?>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nomor / Kode Batch (Otomatis)</label>
                <input type="text" class="form-control" value="<?= e($generatedCode) ?>" readonly style="background-color: #fafcf9; font-family: var(--font-mono); font-weight: 700; color: var(--green);">
                <div class="form-help">Format standar AGINDU: AGD-[TAHUN]-[URUTAN]</div>
            </div>

            <div class="form-group">
                <label for="commodity_id" class="form-label required">Komoditas Agribisnis</label>
                <select name="commodity_id" id="commodity_id" class="form-select" required>
                    <option value="">Pilih Komoditas...</option>
                    <?php foreach ($commodities as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= e($c['name']) ?> (Suhu aman: <?= e($c['temp_min']) ?> s/d <?= e($c['temp_max']) ?> °C)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="producer_name" class="form-label required">Nama Produsen / Kelompok Tani / Eksportir</label>
                <input type="text" id="producer_name" name="producer_name" class="form-control" placeholder="Contoh: Koperasi Tani Makmur Gayo" required value="<?= e(auth_user()['company_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="destination_country_id" class="form-label required">Negara Tujuan Ekspor</label>
                <select name="destination_country_id" id="destination_country_id" class="form-select" required>
                    <option value="">Pilih Negara Tujuan...</option>
                    <?php foreach ($countries as $ct): ?>
                    <option value="<?= $ct['id'] ?>">
                        <?= e($ct['name']) ?> (<?= e($ct['region']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="origin_regency" class="form-label required">Kabupaten / Kota Asal</label>
                <input type="text" id="origin_regency" name="origin_regency" class="form-control" placeholder="Contoh: Aceh Tengah" required>
            </div>

            <div class="form-group">
                <label for="origin_province" class="form-label required">Provinsi Asal</label>
                <input type="text" id="origin_province" name="origin_province" class="form-control" placeholder="Contoh: Aceh" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="harvest_date" class="form-label required">Tanggal Panen / Pengumpulan</label>
                <input type="date" id="harvest_date" name="harvest_date" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="volume_kg" class="form-label required">Volume Ekspor (Kilogram)</label>
                <input type="number" step="0.01" min="1" id="volume_kg" name="volume_kg" class="form-control" placeholder="Contoh: 15000" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="status" class="form-label required">Status Awal Rantai Pasok</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="processing" selected>Proses & Sortir (Grading & Washing)</option>
                    <option value="packed">Dikemas (Export Packaging)</option>
                    <option value="in_transit">Dalam Perjalanan Logistik Domestik</option>
                    <option value="shipped">Dikapalkan (Ocean / Air Freight)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="notes" class="form-label">Catatan Tambahan (Spesifikasi Kontainer / Nomor Segel)</label>
            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Opsional: masukkan catatan kargo atau identitas segel kontainer..."></textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--line);">
            <a href="<?= url('/batches') ?>" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Simpan & Terbitkan Batch</span>
            </button>
        </div>
    </form>
</div>
