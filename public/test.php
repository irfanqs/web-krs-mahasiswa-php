<?php
/**
 * Halaman Diagnosa — SIAKAD Gallery
 * Akses: http://localhost:.../test.php
 * Hapus file ini setelah selesai testing!
 */
require_once '../includes/config.php';

$checks = [];

// 1. PHP Version
$checks[] = [
    'label' => 'PHP Version',
    'ok'    => version_compare(PHP_VERSION, '7.4', '>='),
    'info'  => PHP_VERSION,
    'fix'   => 'Dibutuhkan PHP 7.4+',
];

// 2. PDO Extension
$checks[] = [
    'label' => 'Ekstensi PDO',
    'ok'    => extension_loaded('pdo'),
    'info'  => extension_loaded('pdo') ? 'Aktif' : 'Tidak aktif',
    'fix'   => 'Aktifkan extension=pdo di php.ini',
];

// 3. PDO MySQL Extension
$checks[] = [
    'label' => 'Ekstensi PDO MySQL',
    'ok'    => extension_loaded('pdo_mysql'),
    'info'  => extension_loaded('pdo_mysql') ? 'Aktif' : 'Tidak aktif',
    'fix'   => 'Aktifkan extension=pdo_mysql di php.ini',
];

// 4. Session
session_name('TEST_SIAKAD');
session_start();
$_SESSION['test'] = 'ok';
$checks[] = [
    'label' => 'PHP Session',
    'ok'    => ($_SESSION['test'] ?? '') === 'ok',
    'info'  => 'Session ID: ' . session_id(),
    'fix'   => 'Pastikan direktori session PHP dapat ditulis',
];
session_destroy();

// 5. Database Connection
$db_ok = false;
$db_info = '';
try {
    require_once '../includes/db.php';
    $pdo = get_pdo();
    $db_ok = true;
    $db_info = 'Terhubung ke ' . DB_HOST . ':' . DB_PORT . '/' . DB_NAME;
} catch (\Throwable $e) {
    $db_info = $e->getMessage();
}
$checks[] = [
    'label' => 'Koneksi Database',
    'ok'    => $db_ok,
    'info'  => $db_ok ? $db_info : htmlspecialchars($db_info),
    'fix'   => 'Jalankan MySQL, buat database web_krs, import schema.sql',
];

// 6. Tabel
if ($db_ok) {
    $tables_needed = ['admin', 'mahasiswa', 'dosen', 'mata_kuliah', 'semester', 'jadwal_kuliah', 'krs', 'nilai', 'pengumuman'];
    $stmt = $pdo->query("SHOW TABLES");
    $existing = array_column($stmt->fetchAll(PDO::FETCH_NUM), 0);
    $missing  = array_diff($tables_needed, $existing);
    $checks[] = [
        'label' => 'Tabel Database',
        'ok'    => count($missing) === 0,
        'info'  => count($missing) === 0
            ? 'Semua ' . count($tables_needed) . ' tabel ditemukan'
            : 'Kurang: ' . implode(', ', $missing),
        'fix'   => 'Jalankan: mysql -u root -p web_krs < database/schema.sql',
    ];

    // 7. Data seed
    $stmt  = $pdo->query("SELECT COUNT(*) FROM mahasiswa");
    $count = (int)$stmt->fetchColumn();
    $checks[] = [
        'label' => 'Data Seed (mahasiswa)',
        'ok'    => $count > 0,
        'info'  => "$count mahasiswa ditemukan",
        'fix'   => 'Jalankan: mysql -u root -p web_krs < database/seed.sql',
    ];
}

// 7. APP_URL
$checks[] = [
    'label' => 'APP_URL (auto-detect)',
    'ok'    => !empty(APP_URL),
    'info'  => APP_URL,
    'fix'   => 'Cek includes/config.php',
];

// 8. Password verify test
require_once '../includes/helpers.php';
$test_hash = '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri';
$pw_ok = password_verify('password123', $test_hash);
$checks[] = [
    'label' => 'Hash Password Demo',
    'ok'    => $pw_ok,
    'info'  => $pw_ok ? 'password_verify() ✓ cocok untuk password123' : 'TIDAK cocok — seed.sql perlu diperbarui',
    'fix'   => 'Jalankan seed.sql ulang',
];

// Render
$all_ok = array_reduce($checks, fn($c, $i) => $c && $i['ok'], true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Diagnostik — SIAKAD Gallery</title>
<style>
  body{font-family:system-ui,sans-serif;background:#f5f6fa;margin:0;padding:24px}
  .wrap{max-width:680px;margin:0 auto}
  h1{color:#0B1E4F;margin:0 0 4px;font-size:22px}
  .subtitle{color:#6b7489;margin-bottom:24px;font-size:14px}
  .card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06);padding:20px;margin-bottom:12px;display:flex;align-items:flex-start;gap:16px}
  .icon{font-size:22px;flex-shrink:0;margin-top:2px}
  .label{font-weight:700;font-size:14px;color:#0B1E4F;margin-bottom:2px}
  .info{font-size:13px;color:#2C3A59}
  .fix{font-size:12px;color:#e04f5f;margin-top:4px;font-style:italic}
  code{background:#f5f6fa;padding:2px 6px;border-radius:4px;font-size:12px;font-family:monospace}
  .banner{border-radius:12px;padding:16px 20px;margin-bottom:20px;font-weight:600;font-size:15px}
  .ok{background:#d1fae5;color:#065f46}
  .fail{background:#fee2e2;color:#991b1b}
  .btn{display:inline-block;background:#0B1E4F;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;margin-top:16px}
</style>
</head>
<body>
<div class="wrap">
  <h1>🔍 Diagnostik SIAKAD Gallery</h1>
  <p class="subtitle">Halaman ini membantu mendiagnosa masalah koneksi. Hapus <code>test.php</code> setelah selesai.</p>

  <?php if ($all_ok): ?>
  <div class="banner ok">✅ Semua pemeriksaan lulus! Aplikasi siap digunakan.</div>
  <a href="auth/login.php" class="btn">Buka Halaman Login →</a>
  <?php else: ?>
  <div class="banner fail">⚠️ Ada masalah yang perlu diperbaiki. Lihat item merah di bawah.</div>
  <?php endif; ?>

  <?php foreach ($checks as $c): ?>
  <div class="card">
    <div class="icon"><?= $c['ok'] ? '✅' : '❌' ?></div>
    <div>
      <div class="label"><?= $c['label'] ?></div>
      <div class="info"><?= $c['info'] ?></div>
      <?php if (!$c['ok']): ?>
      <div class="fix">💡 <?= $c['fix'] ?></div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <hr style="border:none;border-top:1px solid #e4e7ee;margin:24px 0">
  <p style="font-size:12px;color:#6b7489">
    APP_URL terdeteksi: <code><?= APP_URL ?></code><br>
    Halaman login: <a href="<?= APP_URL ?>/auth/login.php"><?= APP_URL ?>/auth/login.php</a>
  </p>
</div>
</body>
</html>
