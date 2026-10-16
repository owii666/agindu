<?php
/**
 * AGINDU - Authentication Controller
 */

declare(strict_types=1);

require_once __DIR__ . '/../Core/Controller.php';

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }

        $this->view('auth.login', [
            'pageTitle' => 'Masuk ke Sistem AGINDU'
        ], 'auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $login = trim($_POST['email'] ?? $_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $validator = Validator::make([
            'login'    => $login,
            'password' => $password
        ]);

        if (!$validator->validate([
            'login'    => 'required',
            'password' => 'required'
        ])) {
            set_flash('error', 'Silakan masukkan username/email dan kata sandi dengan benar.');
            redirect('/login');
        }

        $attempt = Auth::attempt($login, $password);

        if (!$attempt['success']) {
            set_flash('error', $attempt['message']);
            redirect('/login');
        }

        set_flash('success', 'Selamat datang kembali, ' . ($attempt['user']['name'] ?? 'Pengguna') . '.');
        redirect('/dashboard');
    }

    public function logout(): void
    {
        $this->validateCsrf();
        Auth::logout();
        set_flash('success', 'Anda telah berhasil keluar dari sistem.');
        redirect('/login');
    }
}
