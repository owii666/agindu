<div class="card">
    <div class="card-header">
        <div>
            <h1 class="card-title">Daftar Batch Ekspor Agribisnis</h1>
            <div class="card-subtitle">Kelola kode ketertelusuran, komoditas, dan status rantai pasok</div>
        </div>
        <a href="<?= url('/batches/create') ?>" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Tambah Batch Baru</span>
        </a>
    </div>

    <!-- Filter & Pencarian Form -->
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--line); background-color: #fafcf9;">
        <form action="<?= url('/batches') ?>" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control" placeholder="Cari kode batch, produsen, kota..." value="<?= e($search) ?>">
            </div>

            <div style="width: 200px;">
                <select name="commodity_id" class="form-select">
                    <option value="">Semua Komoditas</option>
                    <?php foreach ($commodities as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($commodityId === (int)$c['id']) ? 'selected' : '' ?>>
                        <?= e($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="width: 170px;">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Proses & Sortir</option>
                    <option value="packed" <?= $status === 'packed' ? 'selected' : '' ?>>Dikemas</option>
                    <option value="in_transit" <?= $status === 'in_transit' ? 'selected' : '' ?>>Dalam Perjalanan</option>
                    <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Dikapalkan</option>
                    <option value="arrived" <?= $status === 'arrived' ? 'selected' : '' ?>>Tiba di Tujuan</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span>Filter</span>
            </button>

            <?php if (!empty($search) || !empty($status) || !empty($commodityId)): ?>
            <a href="<?= url('/batches') ?>" class="btn btn-outline" title="Reset Filter">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabel Data Batch -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Batch</th>
                    <th>Komoditas</th>
                    <th>Produsen & Asal</th>
                    <th>Panen</th>
                    <th>Volume</th>
                    <th>Tujuan</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pagination['items'])): ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--muted); padding: 36px;">
                        Tidak ada data batch yang sesuai dengan kriteria filter.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($pagination['items'] as $b): ?>
                <tr>
                    <td>
                        <a href="<?= url('/batches/' . $b['id']) ?>" style="font-family: var(--font-mono); font-weight: 700;">
                            <?= e($b['batch_code']) ?>
                        </a>
                    </td>
                    <td>
                        <strong><?= e($b['commodity_name']) ?></strong>
                        <div style="font-size: 11px; color: var(--muted);"><?= e($b['commodity_category']) ?></div>
                    </td>
                    <td>
                        <div><?= e($b['producer_name']) ?></div>
                        <div style="font-size: 11px; color: var(--muted);"><?= e($b['origin_regency']) ?>, <?= e($b['origin_province']) ?></div>
                    </td>
                    <td><?= format_date($b['harvest_date'], 'd M Y') ?></td>
                    <td><?= format_kg($b['volume_kg']) ?></td>
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
                        <a href="<?= url('/batches/' . $b['id']) ?>" class="btn btn-outline btn-sm" title="Rincian Batch">Detail</a>
                        <a href="<?= url('/batches/' . $b['id'] . '/qr') ?>" class="btn btn-secondary btn-sm" title="Label Cetak QR">QR</a>
                        <a href="<?= url('/batches/' . $b['id'] . '/edit') ?>" class="btn btn-outline btn-sm" title="Ubah Data">Edit</a>
                        
                        <form action="<?= url('/batches/' . $b['id'] . '/delete') ?>" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus batch <?= e($b['batch_code']) ?>? Data tidak dapat dipulihkan.');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Batch">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan halaman <?= $pagination['page'] ?> dari <?= $pagination['total_pages'] ?> (Total <?= $pagination['total'] ?> batch)
        </div>
        <div class="pagination-links">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php
                $queryParams = $_GET;
                $queryParams['page'] = $i;
                $pageUrl = url('/batches?' . http_build_query($queryParams));
                ?>
                <a href="<?= $pageUrl ?>" class="page-btn <?= ($i === $pagination['page']) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
