<div class="no-print" style="max-width: 600px; margin: 0 auto 20px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <a href="<?= url('/batches/' . $batch['id']) ?>" class="btn btn-outline btn-sm">&larr; Kembali ke Detail Batch</a>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="button" class="btn btn-primary btn-sm" onclick="printQrLabel()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>Cetak Label QR</span>
        </button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="downloadQrPng('labelQrContainer', '<?= e($batch['batch_code']) ?>-label.png')">
            Unduh PNG
        </button>
        <button type="button" class="btn btn-outline btn-sm" onclick="copyTextToClipboard('<?= e($publicUrl) ?>', this)">
            Salin Tautan Publik
        </button>
    </div>
</div>

<!-- Kotak Label Siap Cetak & Tempel pada Kemasan Goni / Karton -->
<div class="qr-label-container" style="max-width: 500px; margin: 0 auto; background: #fff; border: 2px dashed #b5c7ba; border-radius: 8px; padding: 28px; text-align: center; box-shadow: var(--shadow-card);">
    <!-- Header Sertifikat Label -->
    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid var(--ink); padding-bottom: 12px; margin-bottom: 20px;">
        <div style="text-align: left;">
            <div style="font-size: 16px; font-weight: 800; color: var(--green); letter-spacing: 0.5px;">AGINDU</div>
            <div style="font-size: 10px; color: var(--muted); text-transform: uppercase;">Export Traceability Passport</div>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 10px; font-weight: 700; background: var(--lime); color: #17422b; padding: 3px 8px; border-radius: 4px;">
                VERIFIED BATCH
            </span>
        </div>
    </div>

    <!-- Render QR Code -->
    <div id="labelQrContainer" style="display: inline-block; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 16px;">
        <!-- QRCode Canvas Disuntikkan di Sini -->
    </div>

    <!-- Nomor Batch Besar -->
    <div style="font-family: var(--font-mono); font-size: 20px; font-weight: 800; color: var(--ink); letter-spacing: 1px; margin-bottom: 4px;">
        <?= e($batch['batch_code']) ?>
    </div>
    <div style="font-size: 15px; font-weight: 700; color: var(--green); margin-bottom: 16px;">
        <?= e($batch['commodity_name']) ?>
    </div>

    <!-- Tabel Data Ringkas Label -->
    <table style="width: 100%; border-collapse: collapse; font-size: 12px; text-align: left; margin-bottom: 16px; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line);">
        <tbody>
            <tr>
                <td style="padding: 6px 0; color: var(--muted); width: 40%;">Eksportir / Produsen</td>
                <td style="padding: 6px 0; font-weight: 600; text-align: right;"><?= e($batch['producer_name']) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--muted);">Asal Daerah</td>
                <td style="padding: 6px 0; font-weight: 600; text-align: right;"><?= e($batch['origin_regency']) ?>, <?= e($batch['origin_province']) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--muted);">Tanggal Panen</td>
                <td style="padding: 6px 0; font-weight: 600; text-align: right;"><?= format_date($batch['harvest_date'], 'd M Y') ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--muted);">Volume Bersih</td>
                <td style="padding: 6px 0; font-weight: 600; text-align: right;"><?= format_kg($batch['volume_kg']) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--muted);">Negara Tujuan</td>
                <td style="padding: 6px 0; font-weight: 600; text-align: right;"><?= e($batch['destination_country_name']) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Teks URL Lengkap yang Dikodekan -->
    <div style="font-size: 10px; color: var(--muted); word-break: break-all; margin-bottom: 6px;">
        Tautan Verifikasi: <span style="font-family: var(--font-mono); color: var(--ink);"><?= e($publicUrl) ?></span>
    </div>
    <div style="font-size: 9.5px; color: #88998e; text-transform: uppercase; letter-spacing: 0.5px;">
        Pindai menggunakan kamera ponsel untuk melihat sertifikat rantai pasok
    </div>
</div>

<script src="<?= asset('vendor/qrcode.min.js') ?>"></script>
<script src="<?= asset('js/qr-helper.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    renderQrCode('labelQrContainer', '<?= $publicUrl ?>', 190);
});
</script>
