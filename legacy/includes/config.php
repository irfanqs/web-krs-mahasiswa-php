<?php
/**
 * Application Configuration
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

// Application Settings
define('APP_NAME', 'SIAKAD Gallery');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development');

// Database Configuration — EDIT sesuai setup lokal Anda
define('DB_HOST', 'localhost');
define('DB_NAME', 'web_krs');
define('DB_USER', 'root');
define('DB_PASS', '');          // Ganti jika MySQL Anda pakai password
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT', 3306);

// Auto-detect APP_URL berdasarkan server yang sedang berjalan
// Mendukung: php -S localhost:8000, XAMPP subdirectory, dsb.
if (!defined('APP_URL')) {
    if (PHP_SAPI === 'cli' || !isset($_SERVER['HTTP_HOST'])) {
        define('APP_URL', 'http://localhost:8000');
    } else {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'];                       // e.g. localhost:8000
        $script   = $_SERVER['SCRIPT_NAME'] ?? '';              // e.g. /web-krs-mahasiswa/public/auth/login.php

        // Temukan segment '/public' di path
        $pub_pos  = strpos($script, '/public/');
        if ($pub_pos !== false) {
            $base = substr($script, 0, $pub_pos + 7);           // ambil s/d '/public'
        } elseif (substr($script, -7) === '/public') {
            $base = $script;
        } else {
            $base = '';                                          // built-in server langsung di /
        }

        define('APP_URL', rtrim($protocol . '://' . $host . $base, '/'));
    }
}

// Session Configuration
define('SESSION_LIFETIME', 3600 * 8);   // 8 jam
define('SESSION_NAME', 'SIAKAD_SESSION');

// Security Settings
define('CSRF_TOKEN_LENGTH', 32);

// File Upload Settings
define('UPLOAD_PATH', __DIR__ . '/../public/assets/img/uploads/');
define('UPLOAD_MAX_SIZE', 2097152);     // 2 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Jakarta');
