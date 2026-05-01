<?php
/**
 * Dosen Teaching Schedule
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_role(['dosen']);

$pdo = get_pdo();
$nidn = $_SESSION['user_id'];
$page_title = 'Jadwal Mengajar';
$current_page = 'jadwal';

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
           mk.id_matkul, mk.nama_matkul, mk.sks, mk.kode_matkul, mk.jenis, jk.kuota
    FROM jadwal_kuliah jk
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    WHERE jk.id_dosen = ? AND jk.id_semester = ?
    ORDER BY jk.hari, jk.jam_mulai
");
$stmt->execute([$nidn, $active_semester['id_semester']]);
$courses = $stmt->fetchAll();

// Get total students
$total_students = 0;
foreach ($courses as $course) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM krs WHERE id_jadwal = ?");
    $stmt->execute([$course['id_jadwal']]);
    $total_students += $stmt->fetchColumn();
}

// Build schedule grid
$hari_array = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$jam_slot = [];

foreach ($courses as $course) {
    $start = strtotime($course['jam_mulai']);
    $end = strtotime($course['jam_selesai']);

    if (!isset($jam_slot[$start])) {
        $jam_slot[$start] = substr($course['jam_mulai'], 0, 5);
    }
}

ksort($jam_slot);
$jam_unik = array_keys($jam_slot);

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
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 24px;
        }

        .breadcrumb {
            font-size: 11px;
            font-weight: 800;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .breadcrumb span {
            color: #1B3679;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #1B3679;
            margin: 0;
            line-height: 1.2;
        }
        
        .page-subtitle {
            margin-top: 6px;
            font-size: 15px;
            font-weight: 500;
            color: #6B7280;
        }

        .btn-ghost {
            background: white;
            color: #4B5563;
            border: 1px solid #E5E7EB;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .btn-ghost:hover {
            background: #F3F4F6;
            color: #111827;
            transform: translateY(-2px);
        }

        .layout-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 32px;
        }
        
        @media (max-width: 1024px) {
            .layout-grid { grid-template-columns: 1fr; }
        }

        /* Schedule Grid */
        .schedule-card {
            background: white;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            overflow-x: auto;
        }

        .grid-table {
            width: 100%;
            border-collapse: collapse;
        }

        .grid-table th {
            padding: 16px;
            text-align: center;
            font-weight: 800;
            color: #1B3679;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #F3F4F6;
            background: #F8FAFC;
        }

        .grid-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #F3F4F6;
            height: 90px;
            vertical-align: middle;
        }

        .grid-table td.time-slot {
            background: #F8FAFC;
            font-weight: 700;
            color: #4B5563;
            font-family: monospace;
            font-size: 14px;
            width: 80px;
        }

        .course-slot {
            background: linear-gradient(135deg, #1B3679 0%, #25408E 100%);
            color: white;
            border-radius: 12px;
            padding: 12px 8px;
            font-size: 12px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(27, 54, 121, 0.15);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .course-slot:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 8px 16px rgba(27, 54, 121, 0.25);
            background: linear-gradient(135deg, #1E40AF 0%, #1D4ED8 100%);
        }

        .course-slot strong {
            font-weight: 800;
            margin-bottom: 4px;
            text-align: center;
            line-height: 1.3;
        }

        .course-slot small {
            opacity: 0.9;
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 4px;
        }

        .empty-cell {
            background: white;
            transition: background 0.2s;
        }
        
        .empty-cell:hover {
            background: #F8FAFC;
        }

        /* Sidebar Stats */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .stat-box {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border-top: 4px solid #1B3679;
        }

        .stat-box.success {
            border-top-color: #10B981;
        }

        .stat-box h6 {
            font-size: 11px;
            font-weight: 800;
            color: #6B7280;
            margin: 0 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-box .value {
            font-size: 36px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .stat-box .label {
            font-size: 14px;
            color: #4B5563;
            font-weight: 500;
            margin-top: 8px;
        }

        .info-card {
            background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #BFDBFE;
        }

        .info-card i {
            font-size: 24px;
            color: #3B82F6;
            margin-bottom: 12px;
            display: block;
        }

        .info-card h6 {
            margin: 0 0 8px 0;
            font-size: 14px;
            font-weight: 700;
            color: #1E3A8A;
        }

        .info-card p {
            margin: 0;
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
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
                        <div class="breadcrumb">DOSEN PENGAJAR <span>&gt; JADWAL KELAS</span></div>
                        <h1 class="page-title"><?= h($page_title) ?></h1>
                        <div class="page-subtitle">Semester <?= h(get_semester_label($active_semester['tingkatan_semester'])) ?> <?= h($active_semester['tahun_ajaran']) ?></div>
                    </div>
                    <div class="page-header-right">
                        <button class="btn-ghost" onclick="window.print();">
                            <i class="bi bi-printer"></i> Cetak Jadwal
                        </button>
                    </div>
                </div>

                <div class="layout-grid">
                    <div class="schedule-card">
                        <table class="grid-table">
                            <thead>
                                <tr>
                                    <th>WAKTU</th>
                                    <?php foreach ($hari_array as $hari): ?>
                                        <th><?= h($hari) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jam_unik as $time_slot):
                                    $time_str = $jam_slot[$time_slot];
                                ?>
                                    <tr>
                                        <td class="time-slot"><?= h($time_str) ?></td>
                                        <?php foreach ($hari_array as $hari):
                                            $found = false;
                                            foreach ($courses as $course) {
                                                if ($course['hari'] === $hari && strtotime($course['jam_mulai']) === $time_slot) {
                                                    echo '<td>';
                                                    echo '<a href="' . APP_URL . '/dosen/input_nilai.php?id_jadwal=' . $course['id_jadwal'] . '" class="course-slot">';
                                                    echo '<strong>' . h(substr($course['nama_matkul'], 0, 20)) . (strlen($course['nama_matkul'])>20?'...':''). '</strong>';
                                                    echo '<small><i class="bi bi-geo-alt-fill"></i> ' . h($course['ruang']) . '</small>';
                                                    echo '</a>';
                                                    echo '</td>';
                                                    $found = true;
                                                    break;
                                                }
                                            }
                                            if (!$found) {
                                                echo '<td class="empty-cell"></td>';
                                            }
                                        ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="sidebar">
                        <div class="stat-box">
                            <h6>TOTAL KELAS AJAR</h6>
                            <div class="value"><?= count($courses) ?></div>
                            <div class="label">Mata Kuliah Aktif</div>
                        </div>

                        <div class="stat-box success">
                            <h6>TOTAL MAHASISWA</h6>
                            <div class="value"><?= $total_students ?></div>
                            <div class="label">Mahasiswa Dibimbing</div>
                        </div>

                        <div class="info-card">
                            <i class="bi bi-info-circle-fill"></i>
                            <h6>Panduan Cepat</h6>
                            <p>Klik pada jadwal kelas di dalam tabel di sebelah kiri untuk melihat daftar mahasiswa dan menginput nilai secara langsung.</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
