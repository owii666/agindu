<!-- Metrik Kartu Statistik -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">
            <span>Total Batch Terdaftar</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
        </div>
        <div class="stat-value"><?= number_format($stats['total_batches']) ?></div>
        <div class="stat-help">Semua komoditas tercatat di sistem</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            <span>Batch Aktif Berjalan</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="stat-value" style="color: var(--green);"><?= number_format($stats['active_batches']) ?></div>
        <div class="stat-help">Tahap proses, kemas & pengapalan</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            <span>Peringatan Suhu</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
            </svg>
        </div>
        <div class="stat-value" style="color: <?= $stats['temp_alerts'] > 0 ? 'var(--danger)' : 'var(--ink)' ?>;">
            <?= number_format($stats['temp_alerts']) ?>
        </div>
        <div class="stat-help">Anomali atau suhu di luar batas aman</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            <span>Kesiapan Dokumen</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <div class="stat-value"><?= $stats['doc_completion_rate'] ?>%</div>
        <div class="stat-help">Kelengkapan checklist regulasi ekspor</div>
    </div>
</div>

<!-- Grid 2 Kolom: Batch Terbaru & Ringkasan Status -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Batch Terbaru -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <div>
                <h2 class="card-title">Batch Ekspor Terbaru</h2>
                <div class="card-subtitle">Pengiriman komoditas agribisnis terkini</div>
            </div>
            <a href="<?= url('/batches/create') ?>" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Tambah Batch</span>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Batch</th>
                        <th>Komoditas</th>
                        <th>Tujuan</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stats['recent_batches'])): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--muted); padding: 24px;">
                            Belum ada batch yang didaftarkan.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($stats['recent_batches'] as $b): ?>
                    <tr>
                        <td>
                            <strong style="font-family: var(--font-mono); color: var(--green);">
                                <?= e($b['batch_code']) ?>
                            </strong>
                            <div style="font-size: 11px; color: var(--muted);"><?= format_date($b['created_at'], 'd M Y') ?></div>
                        </td>
                        <td>
                            <div><?= e($b['commodity_name']) ?></div>
                            <div style="font-size: 11px; color: var(--muted);"><?= format_kg($b['volume_kg']) ?></div>
                        </td>
                        <td><?= e($b['destination_country_name']) ?></td>
                        <td>
                            <?php
                            $badgeClass = 'badge-neutral';
                            $statusLabel = $b['status'];
                            if ($b['status'] === 'in_transit' || $b['status'] === 'shipped') {
                                $badgeClass = 'badge-lime';
                                $statusLabel = 'Dalam Perjalanan';
                            } elseif ($b['status'] === 'processing') {
                                $badgeClass = 'badge-amber';
                                $statusLabel = 'Proses & Sortir';
                            } elseif ($b['status'] === 'packed') {
                                $badgeClass = 'badge-green';
                                $statusLabel = 'Dikemas';
                            } elseif ($b['status'] === 'completed') {
                                $badgeClass = 'badge-green';
                                $statusLabel = 'Tiba di Tujuan';
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>">
                                <span class="badge-dot"></span>
                                <?= e($statusLabel) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="<?= url('/batches/' . $b['id']) ?>" class="btn btn-outline btn-sm">Lihat Detail</a>
                            <a href="<?= url('/batches/' . $b['id'] . '/qr') ?>" class="btn btn-secondary btn-sm" title="Buka Label QR">QR</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer" style="text-align: right;">
            <a href="<?= url('/batches') ?>" style="font-size: 12.5px; font-weight: 600;">Lihat Seluruh Batch &rarr;</a>
        </div>
    </div>

    <!-- Kolom Kanan: Peringatan Anomali & Breakdown Status -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Peringatan Suhu Terkini -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Peringatan Suhu Sensor</h3>
                    <div class="card-subtitle">Insiden termal pada rantai pasok</div>
                </div>
            </div>
            <div class="card-body" style="padding: 12px 16px;">
                <?php if (empty($recentAlerts)): ?>
                <div style="text-align: center; color: var(--muted); padding: 18px; font-size: 12.5px;">
                    Tidak ada anomali suhu. Rantai dingin terpantau aman dan stabil.
                </div>
                <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach ($recentAlerts as $alert): ?>
                    <div style="background: #fff8f7; border: 1px solid #f7cfc8; border-radius: var(--radius-md); padding: 10px 12px; font-size: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                            <strong style="color: var(--danger); font-family: var(--font-mono);"><?= e($alert['batch_code']) ?></strong>
                            <span style="font-weight: 700; color: var(--danger);"><?= e($alert['temperature']) ?> °C</span>
                        </div>
                        <div style="color: var(--muted);"><?= e($alert['commodity_name']) ?> (Aman: <?= e($alert['temp_min']) ?> - <?= e($alert['temp_max']) ?> °C)</div>
                        <div style="font-size: 11px; color: #999; margin-top: 4px;"><?= format_date($alert['recorded_at']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer" style="text-align: center;">
                <a href="<?= url('/temperature') ?>" style="font-size: 12px; font-weight: 600;">Buka Grafik Pemantauan IoT &rarr;</a>
            </div>
        </div>

        <!-- Panduan Singkat Otoritas Ekspor -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">Kepatuhan Otoritas Ekspor</h3>
            </div>
            <div class="card-body" style="font-size: 12.5px; color: var(--muted); line-height: 1.5;">
                <p style="margin-bottom: 10px;">
                    Pastikan seluruh dokumen wajib (Phytosanitary, SKA, Invoice, dan regulasi spesifik negara mitra) tervalidasi sebelum kapal kargo berlayar.
                </p>
                <a href="<?= url('/export-guide') ?>" class="btn btn-outline btn-sm" style="width: 100%;">
                    Buka Checklist Regulasi Ekspor
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 960px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
