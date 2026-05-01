<?php
/**
 * Mahasiswa Weekly Schedule Page
 * SIAKAD Gallery
 */

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_role(['mahasiswa']);

$pdo = get_pdo();
$user = current_user();
$nim = $user['user_id'];

// Get mahasiswa data
$stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE nim = ?");
$stmt->execute([$nim]);
$mahasiswa = $stmt->fetch();

// Get semester list for dropdown
$stmt = $pdo->prepare("SELECT * FROM semester ORDER BY tahun_ajaran DESC, tingkatan_semester DESC");
$stmt->execute();
$semester_list = $stmt->fetchAll();

// Get selected semester (default: active)
$selected_semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;

if ($selected_semester === 0 && count($semester_list) > 0) {
    // Try to find active semester, otherwise use first
    foreach ($semester_list as $sem) {
        if ($sem['status'] === 'aktif') {
            $selected_semester = $sem['id_semester'];
            break;
        }
    }
    if ($selected_semester === 0) {
        $selected_semester = $semester_list[0]['id_semester'];
    }
}

// Get mahasiswa's schedule for selected semester
$stmt = $pdo->prepare("
    SELECT
        jk.id_jadwal,
        mk.kode_matkul,
        mk.nama_matkul,
        mk.sks,
        mk.jenis,
        jk.hari,
        jk.jam_mulai,
        jk.jam_selesai,
        jk.ruang,
        d.nama as nama_dosen,
        d.nidn
    FROM krs k
    JOIN jadwal_kuliah jk ON k.id_jadwal = jk.id_jadwal
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    JOIN dosen d ON jk.id_dosen = d.nidn
    WHERE k.id_mahasiswa = ? AND jk.id_semester = ?
    ORDER BY jk.hari ASC, jk.jam_mulai ASC
");
$stmt->execute([$nim, $selected_semester]);
$schedule = $stmt->fetchAll();

// Group schedule by hari
$schedule_by_day = [];
$hari_order = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

foreach ($schedule as $item) {
    if (!isset($schedule_by_day[$item['hari']])) {
        $schedule_by_day[$item['hari']] = [];
    }
    $schedule_by_day[$item['hari']][] = $item;
}

// Get today's classes
$hari_today = format_tanggal(time(), 'hari');
$jadwal_today = $schedule_by_day[$hari_today] ?? [];

// Calculate total credits
$total_credits = 0;
foreach ($schedule as $item) {
    $total_credits += $item['sks'];
}

// Get list of dosen teaching today
$dosen_today = [];
foreach ($jadwal_today as $item) {
    if (!isset($dosen_today[$item['nidn']])) {
        $dosen_today[$item['nidn']] = [
            'nama' => $item['nama_dosen'],
            'matkul' => []
        ];
    }
    $dosen_today[$item['nidn']]['matkul'][] = $item['nama_matkul'];
}

$page_title = 'Jadwal Mingguan';
$current_page = 'jadwal';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?> - SIAKAD Gallery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .page-layout {
            margin-left: 240px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .page-main {
            flex: 1;
            overflow-y: auto;
        }

        .page-content {
            padding: var(--spacing-2xl);
            max-width: 100%;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-2xl);
            flex-wrap: wrap;
            gap: var(--spacing-lg);
        }

        .page-header-left {
            flex: 1;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-900);
            margin-bottom: var(--spacing-sm);
        }

        .page-subtitle {
            color: var(--text-500);
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            gap: var(--spacing-lg);
            align-items: center;
        }

        .semester-selector select {
            padding: var(--spacing-md) var(--spacing-lg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 14px;
            background: white;
        }

        .btn-download {
            padding: var(--spacing-md) var(--spacing-lg);
            background: var(--navy-900);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            transition: var(--transition);
        }

        .btn-download:hover {
            background: var(--navy-700);
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }

        .schedule-day {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .schedule-day-header {
            background: var(--navy-900);
            color: white;
            padding: var(--spacing-lg);
            text-align: center;
            font-weight: 600;
            border-bottom: 2px solid var(--navy-700);
        }

        .schedule-day-content {
            flex: 1;
            padding: var(--spacing-lg);
            min-height: 300px;
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .schedule-class {
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            font-size: 12px;
            color: white;
            border-left: 4px solid;
        }

        .class-wajib {
            background: rgba(11, 30, 79, 0.9);
            border-left-color: #0B1E4F;
        }

        .class-pilihan {
            background: rgba(74, 144, 226, 0.9);
            border-left-color: #4A90E2;
        }

        .class-time {
            font-size: 11px;
            opacity: 0.8;
            margin-bottom: var(--spacing-xs);
        }

        .class-name {
            font-weight: 600;
            margin-bottom: var(--spacing-xs);
        }

        .class-room {
            font-size: 11px;
            opacity: 0.8;
        }

        .empty-day {
            text-align: center;
            color: var(--text-500);
            padding: var(--spacing-xl);
        }

        .empty-day i {
            font-size: 32px;
            margin-bottom: var(--spacing-md);
            opacity: 0.3;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }

        .card {
            background: white;
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-md);
        }

        .card-navy {
            background: linear-gradient(135deg, var(--navy-900) 0%, var(--navy-700) 100%);
            color: white;
        }

        .card-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-500);
            margin-bottom: var(--spacing-md);
        }

        .card-navy .card-title {
            color: rgba(255, 255, 255, 0.8);
        }

        .card-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
        }

        .card-navy .card-value {
            color: white;
        }

        .card-subtitle {
            font-size: 13px;
            opacity: 0.8;
        }

        .dosen-list {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .dosen-item {
            padding: var(--spacing-lg);
            background: var(--bg);
            border-radius: var(--radius-md);
            display: flex;
            gap: var(--spacing-lg);
            align-items: start;
        }

        .dosen-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--navy-900);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            flex-shrink: 0;
        }

        .dosen-info {
            flex: 1;
        }

        .dosen-name {
            font-weight: 600;
            color: var(--text-900);
            margin-bottom: var(--spacing-xs);
        }

        .dosen-matkul {
            font-size: 12px;
            color: var(--text-500);
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-2xl);
            color: var(--text-500);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: var(--spacing-lg);
            opacity: 0.3;
        }

        @media print {
            .page-header,
            .sidebar,
            .topbar,
            .page-footer,
            .grid-3 {
                display: none;
            }

            .page-layout {
                margin-left: 0;
            }

            .page-content {
                padding: 0;
            }

            .schedule-grid {
                margin-bottom: 0;
            }
        }

        @media (max-width: 1024px) {
            .schedule-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .page-layout {
                margin-left: 0;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .schedule-grid {
                grid-template-columns: 1fr;
            }

            .grid-3 {
                grid-template-columns: 1fr;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }

            .semester-selector select,
            .btn-download {
                width: 100%;
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
                <div class="page-header">
                    <div class="page-header-left">
                        <h1 class="page-title">Jadwal Mingguan</h1>
                        <p class="page-subtitle">Jadwal kuliah Anda untuk minggu ini</p>
                    </div>
                    <div class="header-actions">
                        <?php if (count($semester_list) > 0): ?>
                            <div class="semester-selector">
                                <select onchange="window.location.href='?semester=' + this.value">
                                    <?php foreach ($semester_list as $sem): ?>
                                        <option value="<?= $sem['id_semester'] ?>" <?= $sem['id_semester'] == $selected_semester ? 'selected' : '' ?>>
                                            <?= ucfirst($sem['tingkatan_semester']) ?> <?= h($sem['tahun_ajaran']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <button class="btn-download" onclick="window.print()">
                            <i class="bi bi-download"></i>
                            Download Jadwal
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid-3">
                    <div class="card card-navy">
                        <div class="card-title">Total SKS</div>
                        <div class="card-value"><?= $total_credits ?></div>
                        <div class="card-subtitle">Credits this semester</div>
                    </div>
                    <div class="card card-navy">
                        <div class="card-title">Jadwal Hari Ini</div>
                        <div class="card-value"><?= count($jadwal_today) ?></div>
                        <div class="card-subtitle"><?= h($hari_today) ?> classes</div>
                    </div>
                    <div class="card card-navy">
                        <div class="card-title">Dosen Hari Ini</div>
                        <div class="card-value"><?= count($dosen_today) ?></div>
                        <div class="card-subtitle">Lecturers today</div>
                    </div>
                </div>

                <!-- Weekly Schedule Grid -->
                <?php if ($selected_semester > 0 && count($schedule) > 0): ?>
                    <div class="schedule-grid">
                        <?php foreach ($hari_order as $hari): ?>
                            <div class="schedule-day">
                                <div class="schedule-day-header"><?= h($hari) ?></div>
                                <div class="schedule-day-content">
                                    <?php if (isset($schedule_by_day[$hari])): ?>
                                        <?php foreach ($schedule_by_day[$hari] as $class): ?>
                                            <div class="schedule-class <?= 'class-' . $class['jenis'] ?>">
                                                <div class="class-time"><?= substr($class['jam_mulai'], 0, 5) ?>-<?= substr($class['jam_selesai'], 0, 5) ?></div>
                                                <div class="class-name"><?= h($class['nama_matkul']) ?></div>
                                                <div class="class-room"><i class="bi bi-geo-alt"></i> <?= h($class['ruang']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="empty-day">
                                            <i class="bi bi-calendar-x"></i>
                                            <p>Tidak ada jadwal</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="empty-state">
                            <i class="bi bi-calendar"></i>
                            <p>Tidak ada jadwal untuk semester ini</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Today's Schedule Sidebar -->
                <?php if (count($jadwal_today) > 0): ?>
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--spacing-2xl); margin-top: var(--spacing-2xl);">
                        <div></div>
                        <div>
                            <div class="card">
                                <div style="font-size: 14px; font-weight: 600; color: var(--text-900); margin-bottom: var(--spacing-lg); display: flex; align-items: center; gap: var(--spacing-md);">
                                    <i class="bi bi-person-tie" style="color: var(--navy-900); font-size: 18px;"></i>
                                    Dosen Hari Ini
                                </div>
                                <div class="dosen-list">
                                    <?php foreach ($dosen_today as $dosen): ?>
                                        <div class="dosen-item">
                                            <div class="dosen-avatar">
                                                <?= strtoupper(substr($dosen['nama'], 0, 1)) ?>
                                            </div>
                                            <div class="dosen-info">
                                                <div class="dosen-name"><?= h($dosen['nama']) ?></div>
                                                <div class="dosen-matkul">
                                                    <?= implode(', ', array_map('h', $dosen['matkul'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
