<?php
/**
 * Dosen Dashboard
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_role(['dosen']);

$pdo = get_pdo();
$nidn = $_SESSION['user_id'];
$page_title = 'Dashboard Dosen';
$current_page = 'dashboard';

// Get active semester
$stmt = $pdo->prepare("SELECT id_semester, tahun_ajaran, tingkatan_semester FROM semester WHERE status = 'aktif' LIMIT 1");
$stmt->execute();
$active_semester = $stmt->fetch();

if (!$active_semester) {
    $active_semester = ['id_semester' => 0, 'tahun_ajaran' => 'N/A', 'tingkatan_semester' => 'N/A'];
}

// Get courses taught this semester
$stmt = $pdo->prepare("
    SELECT jk.id_jadwal, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.ruang,
           mk.id_matkul, mk.nama_matkul, mk.sks, mk.kode_matkul, jk.kuota
    FROM jadwal_kuliah jk
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    WHERE jk.id_dosen = ? AND jk.id_semester = ?
    ORDER BY jk.hari, jk.jam_mulai
");
$stmt->execute([$nidn, $active_semester['id_semester']]);
$courses = $stmt->fetchAll();

$total_courses = count($courses);

// Get total students enrolled
$total_students = 0;
$courses_with_students = [];
foreach ($courses as $course) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM krs
        WHERE id_jadwal = ?
    ");
    $stmt->execute([$course['id_jadwal']]);
    $count = $stmt->fetchColumn();
    $total_students += $count;
    $courses_with_students[$course['id_jadwal']] = $count;
}

// Count records needing grading (KRS without nilai or not locked)
$total_to_grade = 0;
foreach ($courses as $course) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM krs k
        LEFT JOIN nilai n ON k.id_krs = n.id_krs
        WHERE k.id_jadwal = ? AND (n.id_nilai IS NULL OR n.status_kunci = 0)
    ");
    $stmt->execute([$course['id_jadwal']]);
    $count = $stmt->fetchColumn();
    $total_to_grade += $count;
}

// Get today's schedule
$hari_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
$today = date('l');
$today_hari = [
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu'
];
$today_hari_name = $today_hari[$today] ?? 'Minggu';

$today_schedule = [];
foreach ($courses as $course) {
    if ($course['hari'] === $today_hari_name) {
        $today_schedule[] = $course;
    }
}

usort($today_schedule, function ($a, $b) {
    return strtotime($a['jam_mulai']) - strtotime($b['jam_mulai']);
});

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

        /* Metrics Row */
        .metrics-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .metric-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }

        .metric-card:hover {
            transform: translateY(-4px);
        }

        .metric-icon {
            width: 56px;
            height: 56px;
            background: #EFF6FF;
            color: #1E40AF;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .metric-card.warning .metric-icon {
            background: #FEF2F2;
            color: #DC2626;
        }

        .metric-content {
            flex: 1;
        }

        .metric-value {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
        }

        .metric-label {
            font-size: 11px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        /* Banner */
        .grading-banner {
            background: linear-gradient(135deg, #1B3679 0%, #25408E 100%);
            border-radius: 20px;
            padding: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            margin-bottom: 40px;
            box-shadow: 0 10px 25px rgba(27, 54, 121, 0.15);
            flex-wrap: wrap;
            gap: 24px;
        }

        .grading-banner-text h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 800;
        }

        .grading-banner-text p {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
            color: rgba(255,255,255,0.8);
        }

        .btn-white {
            background: white;
            color: #1B3679;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-white:hover {
            background: #F8F9FA;
            transform: translateY(-2px);
        }

        /* Section Title */
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

        /* Schedules & Courses */
        .schedule-list, .courses-grid {
            display: grid;
            gap: 20px;
            margin-bottom: 40px;
        }

        .schedule-list {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .courses-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }

        .course-card, .schedule-item {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }

        .course-card:hover, .schedule-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            gap: 12px;
        }

        .course-header h6 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            line-height: 1.4;
        }

        .course-code {
            background: #F3F4F6;
            color: #4B5563;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .course-meta {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .course-meta p {
            margin: 0;
            font-size: 13px;
            color: #6B7280;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .course-meta i {
            color: #9CA3AF;
        }

        .enrollment-progress {
            margin-top: auto;
            margin-bottom: 24px;
        }

        .progress-bar-container {
            height: 6px;
            background: #F3F4F6;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-bar {
            height: 100%;
            background: #1B3679;
            border-radius: 3px;
        }

        .enrollment-text {
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
        }

        .course-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .btn-outline {
            flex: 1;
            padding: 10px;
            background: white;
            color: #1B3679;
            border: 1px solid #1B3679;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline:hover {
            background: #F8F9FA;
        }

        .btn-fill {
            flex: 1;
            padding: 10px;
            background: #1B3679;
            color: white;
            border: 1px solid #1B3679;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-fill:hover {
            background: #25408E;
        }

        /* Schedule specific */
        .time-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #EFF6FF;
            color: #1E40AF;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .empty-state {
            background: white;
            border-radius: 20px;
            padding: 48px;
            text-align: center;
            color: #9CA3AF;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            margin-bottom: 40px;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
        }

        .empty-state p {
            margin: 0;
            font-weight: 500;
            font-size: 15px;
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
                    <p class="page-subtitle">Semester <?= h(get_semester_label($active_semester['tingkatan_semester'])) ?> <?= h($active_semester['tahun_ajaran']) ?></p>
                </div>

                <?php if ($msg = flash('success')): ?>
                    <div style="background: #DCFCE7; color: #166534; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">
                        <i class="bi bi-check-circle"></i> <?= h($msg) ?>
                    </div>
                <?php endif; ?>

                <!-- Metrics -->
                <div class="metrics-row">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="bi bi-book"></i></div>
                        <div class="metric-content">
                            <div class="metric-value"><?= $total_courses ?></div>
                            <div class="metric-label">Mata Kuliah Diampu</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon"><i class="bi bi-people"></i></div>
                        <div class="metric-content">
                            <div class="metric-value"><?= $total_students ?></div>
                            <div class="metric-label">Total Mahasiswa</div>
                        </div>
                    </div>
                    <div class="metric-card warning">
                        <div class="metric-icon"><i class="bi bi-pencil-square"></i></div>
                        <div class="metric-content">
                            <div class="metric-value"><?= $total_to_grade ?></div>
                            <div class="metric-label">Tugas Perlu Dinilai</div>
                        </div>
                    </div>
                </div>

                <?php if ($total_to_grade > 0): ?>
                <!-- Grading Assessment Banner -->
                <div class="grading-banner">
                    <div class="grading-banner-text">
                        <h3>Terdapat Penilaian Menunggu!</h3>
                        <p>Ada beberapa data KRS mahasiswa yang belum dinilai. Segera proses agar tidak terlambat.</p>
                    </div>
                    <a href="<?= APP_URL ?>/dosen/input_nilai.php" class="btn-white">
                        <i class="bi bi-pencil"></i> Input Penilaian
                    </a>
                </div>
                <?php endif; ?>

                <!-- Today's Schedule -->
                <h2 class="section-title"><div class="section-line"></div> Jadwal Mengajar Hari Ini</h2>
                <?php if (count($today_schedule) > 0): ?>
                    <div class="schedule-list">
                        <?php foreach ($today_schedule as $schedule): ?>
                            <div class="schedule-item">
                                <div class="time-badge">
                                    <i class="bi bi-clock"></i> <?= substr($schedule['jam_mulai'], 0, 5) ?> - <?= substr($schedule['jam_selesai'], 0, 5) ?>
                                </div>
                                <div class="course-header" style="margin-bottom: 8px;">
                                    <h6><?= h($schedule['nama_matkul']) ?></h6>
                                </div>
                                <div class="course-meta">
                                    <p><i class="bi bi-geo-alt"></i> Ruang: <?= h($schedule['ruang']) ?></p>
                                    <p><i class="bi bi-mortarboard"></i> SKS: <?= h($schedule['sks']) ?></p>
                                </div>
                                <div class="course-actions" style="margin-top: 16px;">
                                    <a href="<?= APP_URL ?>/dosen/input_nilai.php?id_jadwal=<?= $schedule['id_jadwal'] ?>" class="btn-fill">
                                        <i class="bi bi-pencil"></i> Input Nilai
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-calendar-check text-muted"></i>
                        <p>Selamat, tidak ada jadwal mengajar untuk hari ini.</p>
                    </div>
                <?php endif; ?>

                <!-- Active Courses Section -->
                <h2 class="section-title"><div class="section-line"></div> Mata Kuliah Aktif</h2>
                <?php if (count($courses) > 0): ?>
                    <div class="courses-grid">
                        <?php foreach ($courses as $course):
                            $enrolled = $courses_with_students[$course['id_jadwal']] ?? 0;
                            $percentage = ($course['kuota'] > 0) ? ($enrolled / $course['kuota']) * 100 : 0;
                        ?>
                            <div class="course-card">
                                <div class="course-header">
                                    <h6><?= h($course['nama_matkul']) ?></h6>
                                    <span class="course-code"><?= h($course['kode_matkul']) ?></span>
                                </div>
                                <div class="course-meta">
                                    <p><i class="bi bi-calendar-event"></i> <?= h($course['hari']) ?>, <?= substr($course['jam_mulai'], 0, 5) ?> - <?= substr($course['jam_selesai'], 0, 5) ?></p>
                                    <p><i class="bi bi-geo-alt"></i> <?= h($course['ruang']) ?></p>
                                </div>
                                <div class="enrollment-progress">
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="width: <?= min($percentage, 100) ?>%"></div>
                                    </div>
                                    <p class="enrollment-text"><?= $enrolled ?>/<?= $course['kuota'] ?> Mahasiswa Terdaftar</p>
                                </div>
                                <div class="course-actions">
                                    <a href="<?= APP_URL ?>/dosen/daftar_mahasiswa.php?id_jadwal=<?= $course['id_jadwal'] ?>" class="btn-outline">
                                        Daftar Mahasiswa
                                    </a>
                                    <a href="<?= APP_URL ?>/dosen/input_nilai.php?id_jadwal=<?= $course['id_jadwal'] ?>" class="btn-fill">
                                        Input Nilai
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox text-muted"></i>
                        <p>Anda tidak memiliki mata kuliah yang diampu semester ini.</p>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
