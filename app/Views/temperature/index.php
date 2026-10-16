<div class="card">
    <div class="card-header">
        <div>
            <h1 class="card-title">Pemantauan Suhu IoT & Rantai Dingin (Cold Chain)</h1>
            <div class="card-subtitle">Telemetri sensor waktu-nyata selama penyimpanan dan pengangkutan ekspor</div>
        </div>

        <!-- Form Pilih Batch -->
        <form action="<?= url('/temperature') ?>" method="GET" style="display: flex; gap: 8px; align-items: center;">
            <select name="batch_id" class="form-select" onchange="this.form.submit();" style="width: 260px;">
                <?php foreach ($batches as $b): ?>
                <option value="<?= $b['id'] ?>" <?= ($selectedBatch && (int)$selectedBatch['id'] === (int)$b['id']) ? 'selected' : '' ?>>
                    <?= e($b['batch_code']) ?> - <?= e($b['commodity_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (!$selectedBatch): ?>
    <div class="card-body" style="text-align: center; color: var(--muted); padding: 48px;">
        Belum ada batch yang dipilih atau terdaftar dalam sistem.
    </div>
    <?php else: ?>
    <div class="card-body">
        <!-- Kartu Metrik Suhu Terkini -->
        <div class="stats-grid" style="margin-bottom: 20px;">
            <div class="stat-card">
                <div class="stat-label">Suhu Sensor Saat Ini</div>
                <div class="stat-value" id="metricCurrentTemp" style="color: var(--green);">Memuat...</div>
                <div class="stat-help" id="metricLastTime">Menghubungkan ke telemetri...</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Rentang Suhu Aman</div>
                <div class="stat-value" style="font-size: 22px;">
                    <?= e($selectedBatch['temp_min']) ?> s/d <?= e($selectedBatch['temp_max']) ?> °C
                </div>
                <div class="stat-help">Toleransi deviasi: &plusmn;<?= e($selectedBatch['temp_tolerance'] ?? '1.5') ?> °C</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Status Termal</div>
                <div style="margin-top: 6px;" id="metricStatusBadge">
                    <span class="badge badge-neutral"><span class="badge-dot"></span> Memeriksa...</span>
                </div>
                <div class="stat-help" style="margin-top: 6px;">Evaluasi kepatuhan cold chain</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Kelembapan Udara (RH)</div>
                <div class="stat-value" id="metricHumidity">-- %</div>
                <div class="stat-help">Sensor kelembapan kargo reefer</div>
            </div>
        </div>

        <!-- Toolbar Kontrol Simulasi & Polling -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding: 12px 16px; background-color: #fafcf9; border: 1px solid var(--line); border-radius: var(--radius-md); flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 13px; font-weight: 600; color: var(--ink);">
                    Batch: <span style="font-family: var(--font-mono); color: var(--green);"><?= e($selectedBatch['batch_code']) ?></span>
                </span>
                <span style="font-size: 12px; color: var(--muted);">&bull; <?= e($selectedBatch['commodity_name']) ?></span>
                <span id="simIndicator" style="display: none; font-size: 11px; font-weight: 600; background: #fff4d9; color: #8c5b00; padding: 2px 8px; border-radius: 4px;">
                    Simulasi Aktif (5s)
                </span>
            </div>

            <div style="display: flex; gap: 8px;">
                <!-- Tombol Pemicu Simulasi IoT -->
                <button type="button" class="btn btn-secondary btn-sm" id="btnToggleSimulate">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                    <span>Mulai Simulasi IoT</span>
                </button>
            </div>
        </div>

        <!-- Area Grafik Chart.js -->
        <div style="position: relative; height: 340px; width: 100%; margin-bottom: 24px;">
            <canvas id="temperatureChart"></canvas>
        </div>

        <!-- Riwayat Peringatan Anomali Suhu -->
        <div>
            <h3 style="font-size: 15px; font-weight: 600; color: var(--ink); margin-bottom: 12px;">
                Catatan Peringatan Termal & Anomali Sensor
            </h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu Peringatan</th>
                            <th>Kode Batch</th>
                            <th>Komoditas</th>
                            <th>Suhu Terbaca</th>
                            <th>Rentang Aman</th>
                            <th>Status Risiko</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($alerts)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--muted); padding: 20px;">
                                Tidak ada catatan anomali suhu. Rantai dingin seluruh batch berjalan normal.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($alerts as $a): ?>
                        <tr>
                            <td><?= format_date($a['recorded_at']) ?></td>
                            <td>
                                <a href="<?= url('/batches/' . $a['batch_id']) ?>" style="font-family: var(--font-mono); font-weight: 600;">
                                    <?= e($a['batch_code']) ?>
                                </a>
                            </td>
                            <td><?= e($a['commodity_name']) ?></td>
                            <td>
                                <strong style="color: <?= $a['status'] === 'danger' ? 'var(--danger)' : 'var(--amber)' ?>;">
                                    <?= e($a['temperature']) ?> °C
                                </strong>
                            </td>
                            <td><?= e($a['temp_min']) ?> s/d <?= e($a['temp_max']) ?> °C</td>
                            <td>
                                <?php if ($a['status'] === 'danger' || $a['is_anomaly'] == 1): ?>
                                <span class="badge badge-danger"><span class="badge-dot"></span> Di Luar Batas</span>
                                <?php else: ?>
                                <span class="badge badge-amber"><span class="badge-dot"></span> Waspada</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script Vendor Chart.js & Temperature Controller -->
    <script src="<?= asset('vendor/chart.js') ?>"></script>
    <script src="<?= asset('js/temperature.js') ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        initTemperatureMonitoring({
            batchId: <?= (int)$selectedBatch['id'] ?>,
            tempMin: <?= (float)$selectedBatch['temp_min'] ?>,
            tempMax: <?= (float)$selectedBatch['temp_max'] ?>,
            getUrl: '<?= url('/api/temperature/' . $selectedBatch['id']) ?>',
            simUrl: '<?= url('/api/simulate/' . $selectedBatch['id']) ?>',
            csrfToken: '<?= csrf_token() ?>'
        });
    });
    </script>
    <?php endif; ?>
</div>
