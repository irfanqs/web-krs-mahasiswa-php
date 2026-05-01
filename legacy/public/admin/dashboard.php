<?php
/**
 * Admin Dashboard
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_role(['admin']);

$pdo = get_pdo();
$page_title = 'Dashboard Admin';
$current_page = 'dashboard';

// Get counts
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM mahasiswa WHERE status = 'aktif'");
$stmt->execute();
$total_mahasiswa = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM dosen");
$stmt->execute();
$total_dosen = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM mata_kuliah");
$stmt->execute();
$total_matkul = $stmt->fetchColumn();

// Get active semester
$stmt = $pdo->prepare("SELECT id_semester, tahun_ajaran, tingkatan_semester FROM semester WHERE status = 'aktif' LIMIT 1");
$stmt->execute();
$active_semester = $stmt->fetch();

if ($active_semester) {
    // Count KRS validation (how many students have submitted KRS)
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT id_mahasiswa) as count FROM krs k
        JOIN jadwal_kuliah jk ON k.id_jadwal = jk.id_jadwal
        WHERE jk.id_semester = ?
    ");
    $stmt->execute([$active_semester['id_semester']]);
    $krs_submitted = $stmt->fetchColumn();

    // Percentage
    $krs_percentage = ($total_mahasiswa > 0) ? round(($krs_submitted / $total_mahasiswa) * 100) : 0;
} else {
    $active_semester = ['tahun_ajaran' => 'N/A', 'tingkatan_semester' => 'N/A'];
    $krs_percentage = 0;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?> - SIAKAD Gallery</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            margin: 0;
            background-color: #F8F9FA;
            font-family: 'Inter', sans-serif;
            color: #111827;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #1B3679;
            margin: 0 0 8px 0;
            line-height: 1.2;
        }

        .page-subtitle {
            color: #6B7280;
            font-size: 14px;
            margin: 0;
            font-weight: 500;
        }

        /* Section Titles */
        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 800;
            color: #1B3679;
            margin: 0 0 24px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-line {
            width: 32px;
            height: 4px;
            background: #1B3679;
            border-radius: 2px;
        }

        /* Metrics Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .metric-card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }

        .metric-card:hover {
            transform: translateY(-4px);
        }

        .metric-icon {
            position: absolute;
            top: 24px;
            right: 24px;
            width: 48px;
            height: 48px;
            background: #F3F4F6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #1B3679;
        }

        .metric-label {
            font-size: 11px;
            font-weight: 800;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .metric-value {
            font-size: 40px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
            margin-bottom: 16px;
        }

        .metric-badge {
            align-self: flex-start;
            background: #EFF6FF;
            color: #1E40AF;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
        }

        .krs-progress {
            margin-top: auto;
        }

        .progress-bar-container {
            height: 8px;
            background: #F3F4F6;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-bar {
            height: 100%;
            background: #1B3679;
            border-radius: 4px;
        }

        .progress-text {
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
        }

        /* Quick Access */
        .quick-access-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .quick-card {
            border-radius: 24px;
            padding: 32px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .quick-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .quick-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background-size: cover;
            background-position: right center;
            opacity: 0.1;
            z-index: 0;
        }

        .quick-card-1 {
            background: linear-gradient(135deg, #1B3679 0%, #25408E 100%);
            color: white;
        }
        
        .quick-card-2 {
            background: linear-gradient(135deg, #0F766E 0%, #115E59 100%);
            color: white;
        }

        .quick-card-3 {
            background: linear-gradient(135deg, #5B21B6 0%, #4C1D95 100%);
            color: white;
        }

        .quick-icon {
            width: 56px;
            height: 56px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            z-index: 1;
        }

        .quick-card h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            z-index: 1;
        }

        .quick-card p {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
            z-index: 1;
        }

        .quick-card i.bi-arrow-right {
            align-self: flex-start;
            margin-top: 16px;
            font-size: 20px;
            z-index: 1;
            background: rgba(255,255,255,1);
            color: #111827;
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        /* Status Grid */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .status-card {
            background: white;
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
        }

        .status-header {
            font-size: 11px;
            font-weight: 800;
            color: #1B3679;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-header i {
            font-size: 18px;
        }

        .status-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            border-bottom: 1px solid #F3F4F6;
        }

        .status-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .status-item-label {
            font-size: 13px;
            font-weight: 600;
            color: #6B7280;
        }

        .status-item-value {
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .status-item-value.positive {
            color: #10B981;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        @media (max-width: 768px) {
            .metrics-grid, .quick-access-grid, .status-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page-layout">
        <?php include '../../includes/sidebar.php'; ?>

        <div class="page-main">
            <?php include '../../includes/header.php'; ?>

            <main class="page-content">
                <!-- Header -->
                <div class="page-header">
                    <h1 class="page-title"><?= h($page_title) ?></h1>
                    <p class="page-subtitle">Selamat datang di panel administrasi SIAKAD Gallery</p>
                </div>

                <!-- Overview Metrics -->
                <h2 class="section-title"><div class="section-line"></div> Overview Metrics</h2>
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="bi bi-people"></i></div>
                        <div class="metric-label">Total Mahasiswa</div>
                        <div class="metric-value"><?= number_format($total_mahasiswa) ?></div>
                        <div class="metric-badge">Active Enrollment</div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon"><i class="bi bi-person-video3"></i></div>
                        <div class="metric-label">Total Dosen</div>
                        <div class="metric-value"><?= number_format($total_dosen) ?></div>
                        <div class="metric-badge">Teaching Staff</div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon"><i class="bi bi-journal-bookmark"></i></div>
                        <div class="metric-label">Total Mata Kuliah</div>
                        <div class="metric-value"><?= number_format($total_matkul) ?></div>
                        <div class="metric-badge">Curriculum</div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="metric-label">KRS Validation</div>
                        <div class="metric-value"><?= $krs_percentage ?>%</div>
                        <div class="krs-progress">
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: <?= $krs_percentage ?>%"></div>
                            </div>
                            <div class="progress-text">Submission Completion</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Management -->
                <h2 class="section-title"><div class="section-line"></div> Quick Management</h2>
                <div class="quick-access-grid">
                    <a href="<?= APP_URL ?>/admin/mahasiswa/index.php" class="quick-card quick-card-1">
                        <div class="quick-icon"><i class="bi bi-person-lines-fill"></i></div>
                        <div>
                            <h3>Manajemen Mahasiswa</h3>
                            <p>Update profiles, status, and academic records.</p>
                        </div>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    
                    <a href="<?= APP_URL ?>/admin/dosen/index.php" class="quick-card quick-card-2">
                        <div class="quick-icon"><i class="bi bi-person-badge"></i></div>
                        <div>
                            <h3>Manajemen Dosen</h3>
                            <p>Manage teaching staff, profiles, and assignments.</p>
                        </div>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    
                    <a href="<?= APP_URL ?>/admin/matkul/index.php" class="quick-card quick-card-3">
                        <div class="quick-icon"><i class="bi bi-bookshelf"></i></div>
                        <div>
                            <h3>Manajemen Mata Kuliah</h3>
                            <p>Configure curriculum, credits, and courses.</p>
                        </div>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <!-- System Status -->
                <h2 class="section-title"><div class="section-line"></div> System Status</h2>
                <div class="status-grid">
                    <div class="status-card">
                        <div class="status-header"><i class="bi bi-calendar3"></i> Semester Akademik Aktif</div>
                        <div class="status-list">
                            <div class="status-item">
                                <span class="status-item-label">Tahun Ajaran</span>
                                <span class="status-item-value"><?= h($active_semester['tahun_ajaran']) ?></span>
                            </div>
                            <div class="status-item">
                                <span class="status-item-label">Tingkatan</span>
                                <span class="status-item-value"><?= h(get_semester_label($active_semester['tingkatan_semester'])) ?></span>
                            </div>
                            <div class="status-item">
                                <span class="status-item-label">Status</span>
                                <span class="status-item-value positive"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Aktif</span>
                            </div>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-header"><i class="bi bi-hdd-network"></i> Status Sistem</div>
                        <div class="status-list">
                            <div class="status-item">
                                <span class="status-item-label">Database</span>
                                <span class="status-item-value positive"><i class="bi bi-check-circle-fill"></i> Terhubung</span>
                            </div>
                            <div class="status-item">
                                <span class="status-item-label">Server</span>
                                <span class="status-item-value positive"><i class="bi bi-check-circle-fill"></i> Normal</span>
                            </div>
                            <div class="status-item">
                                <span class="status-item-label">Session</span>
                                <span class="status-item-value positive"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                            </div>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-header"><i class="bi bi-lightning"></i> Statistik Cepat</div>
                        <div class="status-list">
                            <div class="status-item">
                                <span class="status-item-label">Mahasiswa Aktif</span>
                                <span class="status-item-value"><?= number_format($total_mahasiswa) ?></span>
                            </div>
                            <div class="status-item">
                                <span class="status-item-label">Dosen Aktif</span>
                                <span class="status-item-value"><?= number_format($total_dosen) ?></span>
                            </div>
                            <div class="status-item">
                                <span class="status-item-label">Kelas Aktif</span>
                                <span class="status-item-value"><?= number_format($total_matkul) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
