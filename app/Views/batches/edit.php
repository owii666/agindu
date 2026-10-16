<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <div>
            <h1 class="card-title">Perbarui Batch <?= e($batch['batch_code']) ?></h1>
            <div class="card-subtitle">Ubah informasi produsen, komoditas, atau status logistik</div>
        </div>
        <a href="<?= url('/batches/' . $batch['id']) ?>" class="btn btn-outline btn-sm">&larr; Kembali</a>
    </div>

    <form action="<?= url('/batches/' . $batch['id']) ?>" method="POST" class="card-body">
        <?= csrf_field() ?>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nomor / Kode Batch</label>
                <input type="text" class="form-control" value="<?= e($batch['batch_code']) ?>" readonly style="background-color: #fafcf9; font-family: var(--font-mono); font-weight: 700; color: var(--green);">
            </div>

            <div class="form-group">
                <label for="commodity_id" class="form-label required">Komoditas Agribisnis</label>
                <select name="commodity_id" id="commodity_id" class="form-select" required>
                    <?php foreach ($commodities as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ((int)$batch['commodity_id'] === (int)$c['id']) ? 'selected' : '' ?>>
                        <?= e($c['name']) ?> (Suhu aman: <?= e($c['temp_min']) ?> s/d <?= e($c['temp_max']) ?> °C)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="producer_name" class="form-label required">Nama Produsen / Eksportir</label>
                <input type="text" id="producer_name" name="producer_name" class="form-control" required value="<?= e($batch['producer_name']) ?>">
            </div>

            <div class="form-group">
                <label for="destination_country_id" class="form-label required">Negara Tujuan Ekspor</label>
                <select name="destination_country_id" id="destination_country_id" class="form-select" required>
                    <?php foreach ($countries as $ct): ?>
                    <option value="<?= $ct['id'] ?>" <?= ((int)$batch['destination_country_id'] === (int)$ct['id']) ? 'selected' : '' ?>>
                        <?= e($ct['name']) ?> (<?= e($ct['region']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="origin_regency" class="form-label required">Kabupaten / Kota Asal</label>
                <input type="text" id="origin_regency" name="origin_regency" class="form-control" required value="<?= e($batch['origin_regency']) ?>">
            </div>

            <div class="form-group">
                <label for="origin_province" class="form-label required">Provinsi Asal</label>
                <input type="text" id="origin_province" name="origin_province" class="form-control" required value="<?= e($batch['origin_province']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="harvest_date" class="form-label required">Tanggal Panen</label>
                <input type="date" id="harvest_date" name="harvest_date" class="form-control" required value="<?= e($batch['harvest_date']) ?>">
            </div>

            <div class="form-group">
                <label for="volume_kg" class="form-label required">Volume Ekspor (Kilogram)</label>
                <input type="number" step="0.01" min="1" id="volume_kg" name="volume_kg" class="form-control" required value="<?= e($batch['volume_kg']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="status" class="form-label required">Status Rantai Pasok</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="processing" <?= $batch['status'] === 'processing' ? 'selected' : '' ?>>Proses & Sortir</option>
                    <option value="packed" <?= $batch['status'] === 'packed' ? 'selected' : '' ?>>Dikemas (Export Packaging)</option>
                    <option value="in_transit" <?= $batch['status'] === 'in_transit' ? 'selected' : '' ?>>Dalam Perjalanan Logistik</option>
                    <option value="shipped" <?= $batch['status'] === 'shipped' ? 'selected' : '' ?>>Dikapalkan (Ocean / Air Freight)</option>
                    <option value="arrived" <?= $batch['status'] === 'arrived' ? 'selected' : '' ?>>Tiba di Pelabuhan Tujuan</option>
                    <option value="completed" <?= $batch['status'] === 'completed' ? 'selected' : '' ?>>Selesai & Lolos Karantina</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="notes" class="form-label">Catatan Tambahan</label>
            <textarea id="notes" name="notes" class="form-control" rows="3"><?= e($batch['notes'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--line);">
            <a href="<?= url('/batches/' . $batch['id']) ?>" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
