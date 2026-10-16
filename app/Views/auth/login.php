<form action="<?= url('/login') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="login" class="form-label required">Username atau Email</label>
        <input type="text" id="login" name="email" class="form-control" placeholder="admin atau admin@agindu.test" required autofocus value="admin">
    </div>

    <div class="form-group">
        <label for="password" class="form-label required">Kata Sandi</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi" required value="admin123">
    </div>

    <div style="margin-bottom: 20px;">
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                <polyline points="10 17 15 12 10 7"></polyline>
                <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            <span>Masuk ke Dashboard</span>
        </button>
    </div>

    <!-- Informasi Akun Akses -->
    <div style="background:#fafcf9; border:1px solid var(--line); border-radius:var(--radius-md); padding:14px; font-size:12px; color:var(--muted); line-height:1.5;">
        <strong style="color:var(--ink); display:block; margin-bottom:4px;">Akun Akses:</strong>
        <div>Username: <code style="color:var(--green); font-weight:600;">admin</code> (atau email <code style="color:var(--green); font-weight:600;">admin@agindu.test</code>)</div>
        <div>Kata Sandi: <code style="color:var(--green); font-weight:600;">admin123</code></div>
    </div>
</form>
