<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan - AGINDU</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        .error-page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-color: var(--paper);
        }
        .error-box {
            max-width: 480px;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 36px 32px;
            text-align: center;
            box-shadow: var(--shadow-card);
        }
        .error-code {
            font-size: 64px;
            font-weight: 800;
            color: var(--green);
            line-height: 1;
            margin-bottom: 12px;
        }
        .error-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
        }
        .error-desc {
            font-size: 13.5px;
            color: var(--muted);
            margin-bottom: 24px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="error-page-wrapper">
        <div class="error-box">
            <div class="error-code">404</div>
            <h1 class="error-title"><?= isset($pageTitle) ? e($pageTitle) : 'Halaman Tidak Ditemukan' ?></h1>
            <p class="error-desc"><?= isset($message) ? e($message) : 'Tautan yang Anda tuju mungkin salah, telah dihapus, atau kode token penelusuran tidak terdaftar dalam sistem AGINDU.' ?></p>
            <a href="<?= url('/') ?>" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
