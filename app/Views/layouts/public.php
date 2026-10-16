<!DOCTYPE html>
<html lang="<?= e($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' . APP_NAME : APP_NAME . ' Traceability Passport' ?></title>
    <link rel="stylesheet" href="<?= asset('css/trace.css') ?>">
</head>
<body>
    <div class="trace-container">
        <!-- Brand & Verified Seal -->
        <header class="trace-header">
            <div class="brand-badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                    <path d="M2 17l10 5 10-5"></path>
                    <path d="M2 12l10 5 10-5"></path>
                </svg>
                <span><?= APP_NAME ?> TRACEABILITY</span>
            </div>

            <div>
                <span class="verified-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <?= t('verified_by_agindu') ?>
                </span>
            </div>

            <h1 class="trace-title"><?= isset($batch) ? e($batch['commodity_name']) : 'Export Product Trace' ?></h1>
            <p class="trace-subtitle"><?= t('verified_subtitle') ?></p>

            <!-- Language Switcher -->
            <div class="lang-switcher">
                <a href="?lang=en" class="lang-btn <?= ($_SESSION['lang'] ?? 'en') === 'en' ? 'active' : '' ?>">English (EN)</a>
                <a href="?lang=id" class="lang-btn <?= ($_SESSION['lang'] ?? 'en') === 'id' ? 'active' : '' ?>">Bahasa Indonesia (ID)</a>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <?= $content ?>
        </main>

        <!-- Public Footer & Security Seal -->
        <footer class="trace-footer">
            <div class="security-seal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>AGINDU Cryptographic Verification Protocol</span>
            </div>
            <p><?= t('public_note') ?></p>
            <p style="margin-top: 8px;">&copy; <?= date('Y') ?> AGINDU System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
