<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' . APP_NAME : 'Masuk - ' . APP_NAME ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-color: var(--paper);
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            padding: 32px;
        }
        .auth-brand {
            text-align: center;
            margin-bottom: 24px;
        }
        .auth-brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background-color: #eaf3ed;
            color: var(--green);
            margin-bottom: 12px;
        }
        .auth-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--ink);
        }
        .auth-subtitle {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-logo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <h1 class="auth-title"><?= APP_NAME ?></h1>
                <p class="auth-subtitle"><?= APP_TAGLINE ?></p>
            </div>

            <!-- Flash Messages -->
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

            <?php if (has_flash('success')): ?>
            <div class="alert-box alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span><?= e(get_flash('success')) ?></span>
            </div>
            <?php endif; ?>

            <?= $content ?>
        </div>
    </div>
</body>
</html>
