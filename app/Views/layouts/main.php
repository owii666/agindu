<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' . APP_NAME : APP_NAME . ' - ' . APP_TAGLINE ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigation -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                    <div>
                        <?= APP_NAME ?>
                        <span><?= APP_TAGLINE ?></span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Menu Utama</div>
                
                <a href="<?= url('/dashboard') ?>" class="nav-item <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="<?= url('/batches') ?>" class="nav-item <?= ($activeNav ?? '') === 'batches' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span>Manajemen Batch</span>
                </a>

                <a href="<?= url('/temperature') ?>" class="nav-item <?= ($activeNav ?? '') === 'temperature' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                    </svg>
                    <span>Pemantauan Suhu IoT</span>
                </a>

                <a href="<?= url('/export-guide') ?>" class="nav-item <?= ($activeNav ?? '') === 'export_guide' ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    <span>Panduan Ekspor</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <?php $user = auth_user(); ?>
                <div class="user-profile-summary">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= e($user['name'] ?? 'Admin Produsen') ?></div>
                        <div class="user-org"><?= e($user['company_name'] ?? 'AGINDU Network') ?></div>
                    </div>
                </div>

                <form action="<?= url('/logout') ?>" method="POST" style="margin: 0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline btn-sm" style="width: 100%; justify-content: flex-start;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="app-main">
            <!-- Header -->
            <header class="app-header">
                <div class="header-left">
                    <button type="button" class="header-toggle-btn" id="sidebarToggleBtn" aria-label="Buka Menu">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="header-title"><?= isset($pageTitle) ? e($pageTitle) : 'Sistem Ketertelusuran Ekspor' ?></div>
                </div>
                <div class="header-right">
                    <span class="badge badge-lime">
                        <span class="badge-dot"></span>
                        Sistem Aktif
                    </span>
                </div>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                <!-- Flash Messages -->
                <?php if (has_flash('success')): ?>
                <div class="alert-box alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span><?= e(get_flash('success')) ?></span>
                </div>
                <?php endif; ?>

                <?php if (has_flash('error')): ?>
                <div class="alert-box alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <span><?= e(get_flash('error')) ?></span>
                </div>
                <?php endif; ?>

                <!-- Konten Halaman Spesifik -->
                <?= $content ?>
            </div>
        </main>
    </div>

    <!-- Script Global -->
    <script src="<?= asset('js/app.js') ?>"></script>
    <?php if (isset($extraScripts)): ?>
        <?= $extraScripts ?>
    <?php endif; ?>
</body>
</html>
