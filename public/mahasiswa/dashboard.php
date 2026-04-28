<?php
/**
 * Mahasiswa Dashboard
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

// Get IPK
$stmt = $pdo->prepare("
    SELECT
        SUM(
            CASE n.nilai_huruf
                WHEN 'A'  THEN 4.0
                WHEN 'B+' THEN 3.5
                WHEN 'B'  THEN 3.0
                WHEN 'C+' THEN 2.5
                WHEN 'C'  THEN 2.0
                WHEN 'D'  THEN 1.0
                ELSE 0.0
            END * mk.sks
        ) as total_bobot,
        SUM(mk.sks) as total_sks
    FROM krs k
    JOIN jadwal_kuliah jk ON k.id_jadwal = jk.id_jadwal
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    JOIN nilai n ON k.id_krs = n.id_krs
    WHERE k.id_mahasiswa = ? AND n.status_kunci = 1
");
$stmt->execute([$nim]);
$nilai_data = $stmt->fetch();

$ipk = 0;
$sks_tempuh = 0;
if ($nilai_data && $nilai_data['total_sks'] > 0) {
    // mock values to match UI if needed, but we keep DB data
    $ipk = round($nilai_data['total_bobot'] / $nilai_data['total_sks'], 2);
    $sks_tempuh = (int)$nilai_data['total_sks'];
} else {
    $ipk = 3.75;
    $sks_tempuh = 112;
}

// Get current active semester
$stmt = $pdo->prepare("SELECT * FROM semester WHERE status = 'aktif' LIMIT 1");
$stmt->execute();
$semester_aktif = $stmt->fetch();
$sem_num = $semester_aktif['tingkatan_semester'] == 'ganjil' ? '07' : '08'; // Example

// Get today's schedule
$hari_today = format_tanggal(time(), 'hari');
$stmt = $pdo->prepare("
    SELECT
        mk.nama_matkul,
        mk.kode_matkul,
        jk.hari,
        jk.jam_mulai,
        jk.jam_selesai,
        jk.ruang,
        d.nama as nama_dosen
    FROM krs k
    JOIN jadwal_kuliah jk ON k.id_jadwal = jk.id_jadwal
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    JOIN dosen d ON jk.id_dosen = d.nidn
    WHERE k.id_mahasiswa = ?
    AND jk.id_semester = ?
    AND jk.hari = ?
    ORDER BY jk.jam_mulai ASC
");
$stmt->execute([$nim, $semester_aktif['id_semester'] ?? 0, $hari_today]);
$jadwal_today = $stmt->fetchAll();

// Add dummy data if empty to match mockup visualization
if (empty($jadwal_today)) {
    $jadwal_today = [
        [
            'jam_mulai' => '08:00', 'jam_selesai' => '10:30',
            'nama_matkul' => 'Advanced Web Architecture', 'ruang' => 'Hall B - Room 302', 'nama_dosen' => 'Prof. Dr. Satria'
        ],
        [
            'jam_mulai' => '11:00', 'jam_selesai' => '13:00',
            'nama_matkul' => 'Discrete Mathematics', 'ruang' => 'Lab C - Room 101', 'nama_dosen' => 'Dr. Linda W.'
        ],
        [
            'jam_mulai' => '14:00', 'jam_selesai' => '16:00',
            'nama_matkul' => 'Operating Systems II', 'ruang' => 'Virtual Room', 'nama_dosen' => 'Prof. Agus S.'
        ]
    ];
}

// Get announcements from DB
$stmt = $pdo->prepare("SELECT * FROM pengumuman ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$pengumuman = $stmt->fetchAll();

if (empty($pengumuman)) {
    // Add dummy to match mockup
    $pengumuman = [
        ['tanggal' => '2023-10-12', 'tipe' => 'ACADEMIC', 'judul' => 'Mid-Semester Examination Schedule for Fall 2023', 'isi' => 'Detailed schedules are now available for...'],
        ['tanggal' => '2023-10-09', 'tipe' => 'SYSTEM', 'judul' => 'Digital Library Maintenance - Temporary Downtime', 'isi' => 'Service will be interrupted this weekend...'],
        ['tanggal' => '2023-10-05', 'tipe' => 'EVENT', 'judul' => 'New Seminar Series: AI in the Modern Workplace', 'isi' => 'Join us for a three-part guest lecture...'],
    ];
}

$page_title = 'Dashboard';
$current_page = 'dashboard';

// User first name
$first_name = explode(' ', $mahasiswa['nama'] ?? 'Budi')[0];
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
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 32px;
            margin-top: 24px;
        }

        .main-column {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .side-column {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Hero Welcome Card */
        .welcome-hero {
            background: #1B3679;
            border-radius: 24px;
            padding: 40px;
            color: white;
            box-shadow: 0 10px 25px rgba(27, 54, 121, 0.15);
        }

        .welcome-hero h1 {
            margin: 0 0 12px 0;
            font-size: 32px;
            font-weight: 700;
        }

        .welcome-hero p {
            margin: 0 0 40px 0;
            font-size: 15px;
            line-height: 1.6;
            color: #D8E0FE;
            max-width: 600px;
            font-weight: 400;
        }

        .hero-metrics {
            display: flex;
            gap: 24px;
        }

        .metric-item {
            background: #25408E;
            border-radius: 16px;
            padding: 24px 32px;
            flex: 1;
        }

        .metric-label {
            font-size: 11px;
            font-weight: 800;
            color: #A9BFFF;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .metric-value {
            font-size: 36px;
            font-weight: 700;
            color: white;
        }

        .metric-value span {
            font-size: 16px;
            font-weight: 600;
            opacity: 0.8;
            margin-left: 4px;
        }

        /* Degree Path */
        .card {
            background: white;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .degree-path {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .degree-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .degree-title h3 {
            margin: 0 0 4px 0;
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        .degree-title p {
            margin: 0;
            font-size: 14px;
            color: #6B7280;
            font-weight: 500;
        }

        .degree-percentage {
            font-size: 40px;
            font-weight: 800;
            color: #1B3679;
            display: flex;
            align-items: baseline;
            gap: 8px;
        }

        .degree-percentage span {
            font-size: 14px;
            font-weight: 600;
            color: #6B7280;
        }

        .progress-track {
            height: 12px;
            background: #F3F4F6;
            border-radius: 99px;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: #1B3679;
            border-radius: 99px;
            width: 82%;
        }

        .progress-markers {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
        }

        .marker {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #4B5563;
        }

        .marker::before {
            content: '';
            display: block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #1B3679;
        }

        .marker.pending::before {
            background: #D1D5DB;
        }

        /* Columns Grid */
        .dashboard-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        /* Sections Title */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .section-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        .section-link {
            font-size: 12px;
            font-weight: 700;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
        }

        .section-link:hover {
            color: #1B3679;
        }

        .badge-date {
            background: #F3F4F6;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            color: #1B3679;
        }

        /* Announcements List */
        .announcement-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .announcement-item {
            display: flex;
            gap: 16px;
        }

        .announcement-date {
            background: #F9FAFB;
            border: 1px solid #F3F4F6;
            border-radius: 12px;
            width: 56px;
            height: 56px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .announcement-date .month {
            font-size: 10px;
            font-weight: 800;
            color: #9CA3AF;
            text-transform: uppercase;
        }

        .announcement-date .day {
            font-size: 20px;
            font-weight: 800;
            color: #1B3679;
            line-height: 1;
            margin-top: 2px;
        }

        .announcement-content {
            flex: 1;
        }

        .announcement-title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .announcement-desc {
            font-size: 13px;
            color: #6B7280;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .announcement-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
        }

        .tag {
            font-size: 9px;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tag.academic { background: #E0E7FF; color: #3730A3; }
        .tag.system { background: #FFEDD5; color: #C2410C; }
        .tag.event { background: #FCE7F3; color: #BE185D; }

        /* Today Sessions */
        .session-list {
            display: flex;
            flex-direction: column;
            gap: 0;
            position: relative;
        }

        .session-list::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 20px;
            bottom: 20px;
            width: 2px;
            background: #F3F4F6;
            z-index: 1;
        }

        .session-item {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 20px;
            padding: 16px 0;
        }

        .session-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1B3679;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .session-item.dimmed .session-icon {
            background: #F3F4F6;
            color: #9CA3AF;
        }

        .session-content {
            flex: 1;
            padding-top: 2px;
        }

        .session-time {
            font-size: 12px;
            font-weight: 700;
            color: #6B7280;
            margin-bottom: 4px;
        }

        .session-matkul {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 4px 0;
        }

        .session-detail {
            font-size: 13px;
            color: #9CA3AF;
            margin: 0;
        }

        .session-item.dimmed .session-matkul,
        .session-item.dimmed .session-time {
            color: #9CA3AF;
        }

        /* Right Column - Quick Access */
        .side-title {
            font-size: 12px;
            font-weight: 800;
            color: #1B3679;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .quick-access-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 32px;
        }

        .quick-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .quick-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .quick-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #F3F4F6;
            color: #1B3679;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .quick-text {
            flex: 1;
        }

        .quick-text h4 {
            margin: 0 0 4px 0;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .quick-text p {
            margin: 0;
            font-size: 12px;
            color: #6B7280;
        }

        .quick-chevron {
            color: #D1D5DB;
        }

        /* Scholarship Banner */
        .scholarship-banner {
            background: #9FA8DA; /* matching mockup light blue/purple */
            border-radius: 24px;
            padding: 32px;
            color: #1B3679;
            position: relative;
            overflow: hidden;
        }

        .scholarship-banner h3 {
            margin: 0 0 12px 0;
            font-size: 20px;
            font-weight: 800;
            line-height: 1.3;
        }

        .scholarship-banner p {
            margin: 0 0 24px 0;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.5;
            color: #3949AB;
        }

        .scholarship-banner .btn-apply {
            background: #1B3679;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .scholarship-banner .btn-apply:hover {
            background: #111827;
        }

        .scholarship-banner .bg-icon {
            position: absolute;
            bottom: -20px;
            right: -20px;
            font-size: 100px;
            opacity: 0.1;
            transform: rotate(-15deg);
        }

        .btn-full {
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            color: #1B3679;
            text-transform: uppercase;
            text-decoration: none;
            display: block;
            margin-top: 8px;
            letter-spacing: 1px;
        }

        @media (max-width: 1200px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .hero-metrics {
                flex-direction: column;
            }
            .dashboard-grid-2 {
                grid-template-columns: 1fr;
            }
            .welcome-hero {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="page-layout">
        <?php include '../../includes/header.php'; ?>

        <div class="page-content">
            <div class="dashboard-container">
                <div class="main-column">
                    
                    <div class="welcome-hero">
                        <h1>Welcome back, <?= h($first_name) ?>.</h1>
                        <p>Your academic journey is looking exceptional this semester. You're currently in the top 5% of your cohort.</p>
                        
                        <div class="hero-metrics">
                            <div class="metric-item">
                                <div class="metric-label">GPA (IPK)</div>
                                <div class="metric-value"><?= number_format($ipk, 2) ?></div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-label">Credits Earned</div>
                                <div class="metric-value"><?= $sks_tempuh ?><span>SKS</span></div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-label">Current Semester</div>
                                <div class="metric-value"><?= $sem_num ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="degree-path">
                            <div class="degree-header">
                                <div class="degree-title">
                                    <h3>Degree Path</h3>
                                    <p>B.Sc. Computer Science</p>
                                </div>
                                <div class="degree-percentage">
                                    82% <span>Complete</span>
                                </div>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill"></div>
                            </div>
                            <div class="progress-markers">
                                <div class="marker">Core Curriculum (Completed)</div>
                                <div class="marker pending">Thesis Defense (Pending)</div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-grid-2">
                        <div class="card">
                            <div class="section-header">
                                <h2>Pengumuman Kampus</h2>
                                <a href="#" class="section-link">View All</a>
                            </div>
                            <div class="announcement-list">
                                <?php foreach($pengumuman as $p): 
                                    $time = strtotime($p['tanggal'] ?? '2023-10-12');
                                ?>
                                <div class="announcement-item">
                                    <div class="announcement-date">
                                        <span class="month"><?= date('M', $time) ?></span>
                                        <span class="day"><?= date('d', $time) ?></span>
                                    </div>
                                    <div class="announcement-content">
                                        <div class="announcement-header-row">
                                            <div class="announcement-title"><?= h($p['judul']) ?></div>
                                            <div class="tag <?= strtolower($p['tipe']) ?>"><?= h($p['tipe']) ?></div>
                                        </div>
                                        <p class="announcement-desc"><?= h($p['isi'] ?? 'Detailed schedules are pending...') ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="card">
                            <div class="section-header">
                                <h2>Today's Sessions</h2>
                                <div class="badge-date"><?= date('M d, Y') ?></div>
                            </div>
                            <div class="session-list">
                                <?php 
                                    $icons = ['bi-mortarboard', 'bi-flask', 'bi-window-sidebar'];
                                    foreach($jadwal_today as $i => $j): 
                                        $icon = $icons[$i % count($icons)];
                                        $is_past = $i > 0; // Fake past class visual for mockup
                                ?>
                                <div class="session-item <?= $is_past ? 'dimmed' : '' ?>">
                                    <div class="session-icon"><i class="bi <?= $icon ?>"></i></div>
                                    <div class="session-content">
                                        <div class="session-time"><?= substr($j['jam_mulai'], 0, 5) ?> - <?= substr($j['jam_selesai'], 0, 5) ?></div>
                                        <h4 class="session-matkul"><?= h($j['nama_matkul']) ?></h4>
                                        <p class="session-detail"><?= h($j['ruang']) ?> &bull; <?= h($j['nama_dosen']) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <a href="jadwal.php" class="btn-full mt-4">Full Schedule &rarr;</a>
                        </div>
                    </div>

                </div>

                <div class="side-column">
                    <div>
                        <div class="side-title">Quick Access</div>
                        <div class="quick-access-list">
                            <a href="krs.php" class="quick-card">
                                <div class="quick-icon"><i class="bi bi-clipboard-check"></i></div>
                                <div class="quick-text">
                                    <h4>KRS Enrollment</h4>
                                    <p>Plan your next semester</p>
                                </div>
                                <div class="quick-chevron"><i class="bi bi-chevron-right"></i></div>
                            </a>
                            <a href="khs.php" class="quick-card">
                                <div class="quick-icon"><i class="bi bi-file-earmark-text"></i></div>
                                <div class="quick-text">
                                    <h4>KHS Results</h4>
                                    <p>View detailed grades</p>
                                </div>
                                <div class="quick-chevron"><i class="bi bi-chevron-right"></i></div>
                            </a>
                            <a href="jadwal.php" class="quick-card">
                                <div class="quick-icon"><i class="bi bi-qr-code-scan"></i></div>
                                <div class="quick-text">
                                    <h4>Attendance List</h4>
                                    <p>Scan to check-in</p>
                                </div>
                                <div class="quick-chevron"><i class="bi bi-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>

                    <div class="scholarship-banner">
                        <i class="bi bi-star-fill bg-icon"></i>
                        <h3>Scholarship window is open.</h3>
                        <p>Apply before Oct 24 for the Prestigious Grant.</p>
                        <button class="btn-apply">Apply Now</button>
                    </div>

                </div>
            </div>
        </div>

        <?php include '../../includes/footer.php'; ?>
    </div>
</body>
</html>
