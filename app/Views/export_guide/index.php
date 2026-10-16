<div class="card">
    <div class="card-header">
        <div>
            <h1 class="card-title">Panduan Dokumen & Regulasi Ekspor Agribisnis</h1>
            <div class="card-subtitle">Kepatuhan karantina tumbuhan, standar mutu internasional, dan persyaratan bea cukai</div>
        </div>
    </div>

    <!-- Pilihan Komoditas dan Negara Tujuan -->
    <div style="padding: 18px 20px; background-color: #fafcf9; border-bottom: 1px solid var(--line);">
        <form action="<?= url('/export-guide') ?>" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 220px;">
                <label for="commodity_id" class="form-label">Pilih Komoditas Agribisnis</label>
                <select name="commodity_id" id="commodity_id" class="form-select">
                    <?php foreach ($commodities as $cmd): ?>
                    <option value="<?= $cmd['id'] ?>" <?= ((int)$selectedCommodity['id'] === (int)$cmd['id']) ? 'selected' : '' ?>>
                        <?= e($cmd['name']) ?> (<?= e($cmd['category']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1; min-width: 220px;">
                <label for="country_id" class="form-label">Pilih Negara Tujuan Ekspor</label>
                <select name="country_id" id="country_id" class="form-select">
                    <?php foreach ($countries as $cty): ?>
                    <option value="<?= $cty['id'] ?>" <?= ((int)$selectedCountry['id'] === (int)$cty['id']) ? 'selected' : '' ?>>
                        <?= e($cty['name']) ?> (<?= e($cty['region']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="height: 38px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Tampilkan Regulasi</span>
            </button>
        </form>
    </div>

    <div class="card-body">
        <!-- Banner Catatan Disclaimer Resmi (Wajib) -->
        <div style="background-color: #f7faf6; border: 1px solid #cce0d2; border-left: 4px solid var(--green); padding: 14px 16px; border-radius: var(--radius-md); margin-bottom: 24px; font-size: 13px; color: #224230; line-height: 1.5;">
            <strong>Catatan Kepatuhan Resmi:</strong>
            Informasi bersifat panduan awal. Verifikasi ke otoritas resmi (Badan Karantina Indonesia, Kementerian Perdagangan, otoritas negara tujuan) sebelum ekspor.
        </div>

        <!-- Ringkasan Persyaratan & Regulasi Negara Tujuan -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px;">
            <div style="background: #fff; border: 1px solid var(--line); border-radius: var(--radius-md); padding: 18px;">
                <h3 style="font-size: 14px; font-weight: 700; color: var(--green); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    Persyaratan Administratif & Teknis: <?= e($selectedCountry['name']) ?>
                </h3>
                <p style="font-size: 13px; color: var(--ink); line-height: 1.5;">
                    <?= nl2br(e($selectedCountry['requirements_summary'])) ?>
                </p>
            </div>

            <div style="background: #fff; border: 1px solid var(--line); border-radius: var(--radius-md); padding: 18px;">
                <h3 style="font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Regulasi & Otoritas Kepabeanan / Karantina
                </h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.5;">
                    <?= nl2br(e($selectedCountry['regulations_summary'])) ?>
                </p>
                <div style="margin-top: 10px; font-size: 12px; color: var(--green); font-weight: 600;">
                    Rentang Suhu Aman <?= e($selectedCommodity['name']) ?>: <?= e($selectedCommodity['temp_min']) ?> s/d <?= e($selectedCommodity['temp_max']) ?> °C
                </div>
            </div>
        </div>

        <!-- Checklist Dokumen Ekspor Real-Time per Batch -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 style="font-size: 16px; font-weight: 700; color: var(--ink);">
                        Checklist Kelengkapan Dokumen Ekspor
                    </h2>
                    <div style="font-size: 12px; color: var(--muted);">
                        Centang berkas yang telah diverifikasi untuk menyimpan progres kepatuhan ekspor
                    </div>
                </div>

                <!-- Pemilih Batch Tertaut -->
                <?php if (!empty($matchingBatches)): ?>
                <form action="<?= url('/export-guide') ?>" method="GET" style="display: flex; align-items: center; gap: 8px;">
                    <input type="hidden" name="commodity_id" value="<?= $selectedCommodity['id'] ?>">
                    <input type="hidden" name="country_id" value="<?= $selectedCountry['id'] ?>">
                    <label for="batch_id" style="font-size: 12px; font-weight: 600; color: var(--muted);">Tautkan ke Batch:</label>
                    <select name="batch_id" id="batch_id" class="form-select form-select-sm" onchange="this.form.submit();" style="width: 170px;">
                        <?php foreach ($matchingBatches as $mb): ?>
                        <option value="<?= $mb['id'] ?>" <?= ($activeBatch && (int)$activeBatch['id'] === (int)$mb['id']) ? 'selected' : '' ?>>
                            <?= e($mb['batch_code']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <?php endif; ?>
            </div>

            <?php if (empty($checklists)): ?>
            <div style="background: #fafcf9; border: 1px dashed var(--line); border-radius: var(--radius-md); padding: 24px; text-align: center; color: var(--muted); font-size: 13px;">
                Belum ada batch aktif yang terdaftar untuk kombinasi komoditas dan negara ini. 
                <br>Silakan <a href="<?= url('/batches/create') ?>" style="color: var(--green); font-weight: 600;">buat batch baru</a> untuk mulai mencatat checklist dokumen pengapalan.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">Cek</th>
                            <th>Nama Dokumen Ekspor</th>
                            <th>Kategori Kepatuhan</th>
                            <th>Waktu Verifikasi</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checklists as $item): ?>
                        <tr>
                            <td style="text-align: center;">
                                <form action="<?= url('/export-guide/toggle/' . $item['id']) ?>" method="POST" style="margin: 0;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="redirect_to" value="<?= url('/export-guide?commodity_id=' . $selectedCommodity['id'] . '&country_id=' . $selectedCountry['id'] . '&batch_id=' . ($activeBatch['id'] ?? '')) ?>">
                                    <input type="checkbox" onchange="this.form.submit();" <?= $item['is_checked'] ? 'checked' : '' ?> style="width: 16px; height: 16px; cursor: pointer; accent-color: var(--green);">
                                </form>
                            </td>
                            <td>
                                <strong style="color: <?= $item['is_checked'] ? 'var(--green)' : 'var(--ink)' ?>;">
                                    <?= e($item['doc_name']) ?>
                                </strong>
                                <?php if (!empty($item['notes'])): ?>
                                <div style="font-size: 11.5px; color: var(--muted); margin-top: 2px;">
                                    <?= e($item['notes']) ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item['is_mandatory']): ?>
                                <span class="badge badge-amber"><span class="badge-dot"></span> Wajib Otoritas</span>
                                <?php else: ?>
                                <span class="badge badge-neutral">Pelengkap</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 12px; color: var(--muted);">
                                <?= $item['is_checked'] && $item['checked_at'] ? format_date($item['checked_at']) : 'Belum diverifikasi' ?>
                            </td>
                            <td style="text-align: right;">
                                <form action="<?= url('/export-guide/toggle/' . $item['id']) ?>" method="POST" style="margin: 0; display: inline-block;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="redirect_to" value="<?= url('/export-guide?commodity_id=' . $selectedCommodity['id'] . '&country_id=' . $selectedCountry['id'] . '&batch_id=' . ($activeBatch['id'] ?? '')) ?>">
                                    <button type="submit" class="btn btn-outline btn-sm">
                                        <?= $item['is_checked'] ? 'Batalkan' : 'Tandai Siap' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media (max-width: 860px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
