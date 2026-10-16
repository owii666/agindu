<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <h1 style="font-size: 22px; font-weight: 700; color: var(--ink); margin: 0;"><?= e($batch['batch_code']) ?></h1>
            <?php
            $badgeClass = 'badge-neutral';
            $statusLabel = $batch['status'];
            if ($batch['status'] === 'in_transit' || $batch['status'] === 'shipped') {
                $badgeClass = 'badge-lime';
                $statusLabel = 'Dalam Perjalanan';
            } elseif ($batch['status'] === 'processing') {
                $badgeClass = 'badge-amber';
                $statusLabel = 'Proses & Sortir';
            } elseif ($batch['status'] === 'packed') {
                $badgeClass = 'badge-green';
                $statusLabel = 'Dikemas';
            } elseif ($batch['status'] === 'completed') {
                $badgeClass = 'badge-green';
                $statusLabel = 'Tiba di Tujuan';
            }
            ?>
            <span class="badge <?= $badgeClass ?>">
                <span class="badge-dot"></span>
                <?= e($statusLabel) ?>
            </span>
        </div>
        <div style="color: var(--muted); font-size: 13px; margin-top: 4px;">
            Didaftarkan pada <?= format_date($batch['created_at']) ?>
        </div>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="<?= url('/batches/' . $batch['id'] . '/qr') ?>" class="btn btn-secondary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Label Cetak QR</span>
        </a>
        <a href="<?= url('/batches/' . $batch['id'] . '/edit') ?>" class="btn btn-outline">Edit Batch</a>
        <a href="<?= url('/batches') ?>" class="btn btn-outline">&larr; Daftar Batch</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Kolom Kiri: Rincian Produk, Linimasa Perjalanan, Dokumen -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Kartu Rincian Komoditas & Ekspor -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h2 class="card-title">Informasi Komoditas & Ekspor</h2>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div>
                        <div style="font-size: 11.5px; color: var(--muted); margin-bottom: 2px;">Komoditas</div>
                        <div style="font-weight: 600; font-size: 14px;"><?= e($batch['commodity_name']) ?></div>
                        <div style="font-size: 11px; color: var(--muted);"><?= e($batch['commodity_category']) ?></div>
                    </div>

                    <div>
                        <div style="font-size: 11.5px; color: var(--muted); margin-bottom: 2px;">Produsen / Eksportir</div>
                        <div style="font-weight: 600; font-size: 14px;"><?= e($batch['producer_name']) ?></div>
                    </div>

                    <div>
                        <div style="font-size: 11.5px; color: var(--muted); margin-bottom: 2px;">Wilayah Asal</div>
                        <div style="font-weight: 600; font-size: 14px;"><?= e($batch['origin_regency']) ?>, <?= e($batch['origin_province']) ?></div>
                    </div>

                    <div>
                        <div style="font-size: 11.5px; color: var(--muted); margin-bottom: 2px;">Tanggal Panen</div>
                        <div style="font-weight: 600; font-size: 14px;"><?= format_date($batch['harvest_date'], 'd M Y') ?></div>
                    </div>

                    <div>
                        <div style="font-size: 11.5px; color: var(--muted); margin-bottom: 2px;">Volume Pengiriman</div>
                        <div style="font-weight: 600; font-size: 14px; color: var(--green);"><?= format_kg($batch['volume_kg']) ?></div>
                    </div>

                    <div>
                        <div style="font-size: 11.5px; color: var(--muted); margin-bottom: 2px;">Negara Tujuan</div>
                        <div style="font-weight: 600; font-size: 14px;"><?= e($batch['destination_country_name']) ?> (<?= e($batch['country_code']) ?>)</div>
                    </div>
                </div>

                <?php if (!empty($batch['notes'])): ?>
                <div style="margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--line); font-size: 12.5px; color: var(--muted);">
                    <strong style="color: var(--ink);">Catatan Khusus:</strong> <?= nl2br(e($batch['notes'])) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Linimasa Perjalanan Logistik -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Linimasa Titik Perjalanan (Logistics Journey)</h2>
                    <div class="card-subtitle">Peristiwa ini akan ditampilkan pada paspor publik yang dipindai buyer</div>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($timelineEvents)): ?>
                <div style="text-align: center; color: var(--muted); padding: 24px; font-size: 13px;">
                    Belum ada peristiwa logistik yang dicatat untuk batch ini.
                </div>
                <?php else: ?>
                <div class="timeline">
                    <?php foreach ($timelineEvents as $ev): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <div class="timeline-marker-inner"></div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title"><?= e($ev['title']) ?></div>
                            <div class="timeline-meta">
                                <strong><?= e($ev['location']) ?></strong> &bull; <?= format_date($ev['recorded_at']) ?> &bull; Dicatat: <?= e($ev['created_by']) ?>
                            </div>
                            <?php if (!empty($ev['description'])): ?>
                            <div class="timeline-desc"><?= nl2br(e($ev['description'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Form Tambah Titik Peristiwa -->
                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line);">
                    <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--ink);">
                        Tambah Catatan Perjalanan / Checkpoint Baru
                    </h3>
                    <form action="<?= url('/batches/' . $batch['id'] . '/timeline') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="stage" class="form-label required">Tahapan Logistik</label>
                                <select name="stage" id="stage" class="form-select" required>
                                    <option value="harvest">Panen & Pengumpulan</option>
                                    <option value="sorting">Grading & Sortir Kualitas</option>
                                    <option value="packaging">Pengemasan & Labeling</option>
                                    <option value="warehouse">Penyimpanan Gudang / Cold Storage</option>
                                    <option value="port">Pelabuhan Muat & Bea Cukai</option>
                                    <option value="shipping">Pelayaran / Kargo Udara</option>
                                    <option value="arrival">Pelabuhan Bongkar / Tiba di Tujuan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="title" class="form-label required">Judul Peristiwa</label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Pemuatan Kontainer Reefer" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="location" class="form-label required">Lokasi / Fasilitas</label>
                                <input type="text" name="location" id="location" class="form-control" placeholder="Contoh: Terminal Peti Kemas Belawan" required>
                            </div>

                            <div class="form-group">
                                <label for="recorded_at" class="form-label required">Waktu Kejadian</label>
                                <input type="datetime-local" name="recorded_at" id="recorded_at" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Keterangan Detail (Opsional)</label>
                            <textarea name="description" id="description" class="form-control" rows="2" placeholder="Nomor segel, hasil uji kualitas, nama kapal kargo, dll."></textarea>
                        </div>

                        <button type="submit" class="btn btn-secondary btn-sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Simpan Peristiwa Linimasa</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Dokumen Ekspor Batch -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Kesiapan Berkas Ekspor</h2>
                    <div class="card-subtitle">Dokumen wajib tujuan <?= e($batch['destination_country_name']) ?></div>
                </div>
                <a href="<?= url('/export-guide?commodity_id=' . $batch['commodity_id'] . '&country_id=' . $batch['destination_country_id'] . '&batch_id=' . $batch['id']) ?>" class="btn btn-outline btn-sm">
                    Buka Panduan Ekspor
                </a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Dokumen</th>
                            <th>Status Kelengkapan</th>
                            <th>Keterangan / Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($checklists)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--muted); padding: 18px;">
                                Belum ada berkas checklist yang dikaitkan.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($checklists as $chk): ?>
                        <tr>
                            <td>
                                <strong><?= e($chk['doc_name']) ?></strong>
                                <?php if ($chk['is_mandatory']): ?>
                                <span style="font-size: 10px; color: var(--danger); font-weight: 600;">(Wajib)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($chk['is_checked']): ?>
                                <span class="badge badge-lime">
                                    <span class="badge-dot"></span> Lengkap
                                </span>
                                <?php else: ?>
                                <span class="badge badge-amber">
                                    <span class="badge-dot"></span> Belum Siap
                                </span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 12px; color: var(--muted);">
                                <?= e($chk['notes'] ?? ($chk['checked_at'] ? 'Diverifikasi ' . format_date($chk['checked_at']) : 'Menunggu berkas resmi')) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Modul QR Code Publik & Status Suhu IoT -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Modul QR Code Publik -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">QR Code Ketertelusuran Publik</h3>
            </div>
            <div class="card-body" style="text-align: center;">
                <!-- Kontainer QR Render -->
                <div id="batchQrCanvasContainer" style="display: inline-block; padding: 12px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius-md); margin-bottom: 14px;">
                    <!-- QRCode.js merender di sini -->
                </div>

                <div style="margin-bottom: 12px;">
                    <div style="font-size: 11px; color: var(--muted); margin-bottom: 4px;">URL Lengkap yang Dikodekan di QR:</div>
                    <input type="text" class="form-control" readonly value="<?= e($publicUrl) ?>" style="font-family: var(--font-mono); font-size: 11px; text-align: center; background: #fafcf9;" onclick="this.select();">
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <button type="button" class="btn btn-primary btn-sm" onclick="downloadQrPng('batchQrCanvasContainer', '<?= e($batch['batch_code']) ?>-qr.png')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Unduh Gambar PNG</span>
                    </button>

                    <a href="<?= url('/batches/' . $batch['id'] . '/qr') ?>" target="_blank" class="btn btn-secondary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Cetak Label QR Siap Tempel</span>
                    </a>

                    <button type="button" class="btn btn-outline btn-sm" onclick="copyTextToClipboard('<?= e($publicUrl) ?>', this)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span>Salin Tautan Publik</span>
                    </button>

                    <a href="<?= e($publicUrl) ?>" target="_blank" class="btn btn-outline btn-sm" style="color: var(--green);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                        <span>Pratinjau Halaman Publik</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Pemantauan Suhu Terkini -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">Rantai Dingin & Suhu Sensor</h3>
            </div>
            <div class="card-body">
                <div style="font-size: 12px; color: var(--muted); margin-bottom: 8px;">Rentang Suhu Aman Komoditas:</div>
                <div style="font-weight: 700; font-size: 15px; color: var(--ink); margin-bottom: 12px;">
                    <?= e($batch['temp_min']) ?> °C s/d <?= e($batch['temp_max']) ?> °C
                </div>

                <div style="background: #fafcf9; border: 1px solid var(--line); border-radius: var(--radius-md); padding: 14px; text-align: center; margin-bottom: 14px;">
                    <div style="font-size: 11px; color: var(--muted);">Pembacaan Terakhir Sensor</div>
                    <div style="font-size: 26px; font-weight: 700; color: var(--green); margin: 4px 0;">
                        <?= $latestTemp ? e($latestTemp['temperature']) . ' °C' : 'Belum ada data' ?>
                    </div>
                    <?php if ($latestTemp): ?>
                    <div style="font-size: 11px; color: var(--muted);">Waktu: <?= format_date($latestTemp['recorded_at']) ?></div>
                    <?php endif; ?>
                </div>

                <a href="<?= url('/temperature?batch_id=' . $batch['id']) ?>" class="btn btn-outline btn-sm" style="width: 100%;">
                    Buka Grafik & Simulasi IoT
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Load Local QRCode.js Library -->
<script src="<?= asset('vendor/qrcode.min.js') ?>"></script>
<script src="<?= asset('js/qr-helper.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    renderQrCode('batchQrCanvasContainer', '<?= $publicUrl ?>', 160);
});
</script>

<style>
@media (max-width: 960px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
