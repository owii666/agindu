<?php
/**
 * AGINDU (Agribisnis Indonesia Mendunia)
 * Contoh Berkas Override Konfigurasi Lokal
 * 
 * Salin berkas ini menjadi "config.local.php" di folder yang sama (config/)
 * jika Anda perlu menyesuaikan kredensial database atau URL aplikasi lokal.
 * Berkas "config.local.php" diabaikan oleh Git (.gitignore) sehingga tidak akan ter-commit.
 */

return [
    // Host database MySQL (default Laragon: localhost)
    'DB_HOST' => 'localhost',

    // Port database MySQL (default Laragon: 3306)
    'DB_PORT' => '3306',

    // Nama database aplikasi
    'DB_NAME' => 'agindu',

    // Username database (default Laragon: root)
    'DB_USER' => 'root',

    // Password database (default Laragon: kosong)
    'DB_PASS' => '',

    // Override BASE_URL jika dibutuhkan secara spesifik (opsional)
    // Biarkan kosong untuk deteksi otomatis dari peramban
    // Contoh: 'http://localhost/agindu' atau 'http://agindu.test'
    'BASE_URL_OVERRIDE' => '',

    // Mode debug (true untuk mencatat log rinci selama pengembangan)
    'APP_DEBUG' => true,
];
