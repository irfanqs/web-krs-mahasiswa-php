<?php
/**
 * Database Connection — PDO Singleton
 * SIAKAD Gallery
 */

require_once __DIR__ . '/config.php';

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Tampilkan pesan error yang ramah pengguna di mode development
            if (APP_ENV === 'development') {
                $msg = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                die("
                <style>body{font-family:system-ui,sans-serif;background:#f5f6fa;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
                .err{background:#fff;border-left:4px solid #e04f5f;padding:24px 32px;border-radius:8px;max-width:600px;box-shadow:0 2px 12px rgba(0,0,0,.08)}
                h2{color:#e04f5f;margin:0 0 8px}pre{background:#f5f6fa;padding:12px;border-radius:4px;font-size:13px;overflow:auto}
                p{color:#444;margin:8px 0}code{background:#f5f6fa;padding:2px 6px;border-radius:3px;font-size:13px}</style>
                <div class='err'>
                    <h2>🔴 Koneksi Database Gagal</h2>
                    <p>Pastikan:</p>
                    <p>1. MySQL/MariaDB sudah berjalan (XAMPP/Laragon → Start Apache &amp; MySQL)</p>
                    <p>2. Database <code>" . DB_NAME . "</code> sudah dibuat:<br>
                       <code>CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</code></p>
                    <p>3. Kredensial di <code>includes/config.php</code> sudah benar (DB_USER, DB_PASS)</p>
                    <p>4. Schema &amp; seed sudah diimport:<br>
                       <code>mysql -u root -p " . DB_NAME . " &lt; database/schema.sql</code></p>
                    <pre>Error: $msg</pre>
                </div>");
            } else {
                die('Terjadi kesalahan sistem. Silakan hubungi administrator.');
            }
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }
}

/**
 * Shorthand — dapatkan PDO connection
 */
function get_pdo(): PDO
{
    return Database::getInstance()->getConnection();
}
