<?php
/**
 * AGINDU - Database Connection Helper
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

class DatabaseConnection
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                DB_HOST,
                DB_PORT,
                DB_NAME
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                // Sinkronisasi timezone sesi MySQL ke WIB (+07:00)
                $pdo->exec("SET time_zone = '+07:00'");
                self::$instance = $pdo;
            } catch (PDOException $e) {
                // Pastikan direktori log tersedia
                $logDir = dirname(LOG_FILE);
                if (!is_dir($logDir)) {
                    @mkdir($logDir, 0775, true);
                }

                // Tulis ke log jika ada kegagalan koneksi
                $logMsg = sprintf(
                    "[%s] Database Connection Error: %s in %s:%d\n",
                    date('Y-m-d H:i:s'),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                );
                @error_log($logMsg, 3, LOG_FILE);

                http_response_code(500);
                echo "<!DOCTYPE html><html lang='id'><head><meta charset='utf-8'><title>Koneksi Database Gagal</title>";
                echo "<style>body{font-family:system-ui,-apple-system,sans-serif;background:#f5f8f2;color:#172820;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}";
                echo ".box{background:#fff;border:1px solid #e4ebe2;padding:32px;border-radius:8px;max-width:520px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.05);}";
                echo "h2{color:#b3402f;margin-top:0;font-size:20px;}p{color:#6b7b71;font-size:14px;line-height:1.6;margin:12px 0 0;}</style></head><body>";
                echo "<div class='box'>";
                echo "<h2>Koneksi Database Gagal</h2>";
                echo "<p>Koneksi database gagal. Pastikan MySQL di Laragon sudah berjalan dan database agindu sudah diimpor. Lihat README.md.</p>";
                echo "</div></body></html>";
                exit;
            }
        }

        return self::$instance;
    }
}
