<?php
/**
 * Dosen - Daftar Mahasiswa dalam Kelas
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_role(['dosen']);

$pdo = get_pdo();
$nidn = $_SESSION['user_id'];
$page_title = 'Daftar Mahasiswa';
$current_page = 'daftar_mahasiswa';

// Get id_jadwal from GET
$id_jadwal = isset($_GET['id_jadwal']) ? (int)$_GET['id_jadwal'] : 0;

if (!$id_jadwal) {
    flash('error', 'Kelas tidak ditemukan');
    redirect(APP_URL . '/dosen/dashboard.php');
    exit;
}

// Get jadwal info and verify ownership
$stmt = $pdo->prepare("
    SELECT jk.id_jadwal, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.ruang, jk.kuota,
           mk.id_matkul, mk.nama_matkul, mk.kode_matkul, mk.sks, jk.id_dosen
    FROM jadwal_kuliah jk
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    WHERE jk.id_jadwal = ?
");
$stmt->execute([$id_jadwal]);
$jadwal = $stmt->fetch();

if (!$jadwal || $jadwal['id_dosen'] !== $nidn) {
    flash('error', 'Anda tidak memiliki akses ke kelas ini');
    redirect(APP_URL . '/dosen/dashboard.php');
    exit;
}

// Get all students in this class with nilai info
$stmt = $pdo->prepare("
    SELECT m.nim, m.nama, m.program_studi, k.id_krs,
           n.nilai_angka, n.nilai_huruf, n.status_kunci
    FROM krs k
    JOIN mahasiswa m ON k.id_mahasiswa = m.nim
    LEFT JOIN nilai n ON k.id_krs = n.id_krs
    WHERE k.id_jadwal = ?
    ORDER BY m.nama
");
$stmt->execute([$id_jadwal]);
$students = $stmt->fetchAll();

$total_students = count($students);
$graded = 0;
foreach ($students as $student) {
    if (!empty($student['nilai_huruf'])) {
        $graded++;
    }
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

        .breadcrumb a {
            color: #4B5563;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .breadcrumb a:hover {
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

        .btn-primary-custom {
            background: linear-gradient(135deg, #1B3679 0%, #25408E 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(27, 54, 121, 0.2);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(27, 54, 121, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-4px); }

        .stat-icon {
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
        
        .stat-card.success .stat-icon { background: #DCFCE7; color: #166534; }
        .stat-card.warning .stat-icon { background: #FFF7ED; color: #C2410C; }

        .stat-content { flex: 1; }
        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
        }
        .stat-label {
            font-size: 11px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        /* Table Card */
        .table-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin-bottom: 40px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            font-size: 11px;
            font-weight: 800;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 24px;
            border-bottom: 2px solid #F3F4F6;
            background: #FBFCFD;
            text-align: left;
        }

        td {
            padding: 16px 24px;
            font-size: 14px;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: middle;
            color: #4B5563;
        }

        tr:last-child td { border-bottom: none; }
        tbody tr:hover { background-color: #F8FAFC; }

        .student-nim {
            font-weight: 700;
            color: #1B3679;
        }
        .student-nama {
            font-weight: 700;
            color: #111827;
        }
        .student-prodi {
            font-size: 13px;
            color: #6B7280;
        }
        
        .nilai-angka {
            font-family: monospace;
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-status.tersimpan { background: #DCFCE7; color: #166534; }
        .badge-status.belum-dinilai { background: #F3F4F6; color: #6B7280; }

        .empty-state {
            padding: 80px 24px;
            text-align: center;
        }
        .empty-state i {
            font-size: 56px;
            color: #D1D5DB;
            margin-bottom: 24px;
            display: block;
        }
        .empty-state p {
            color: #6B7280;
            font-weight: 500;
            font-size: 15px;
            margin: 0;
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
                        <div class="breadcrumb">
                            <a href="<?= APP_URL ?>/dosen/dashboard.php">DASHBOARD</a> <span>&gt;</span> 
                            <a href="<?= APP_URL ?>/dosen/jadwal.php">JADWAL KELAS</a> <span>&gt;</span> 
                            DAFTAR MAHASISWA
                        </div>
                        <h1 class="page-title"><?= h($page_title) ?></h1>
                        <div class="page-subtitle"><?= h($jadwal['nama_matkul']) ?> (<?= h($jadwal['kode_matkul']) ?>) &bull; <?= h($jadwal['hari']) ?>, <?= substr($jadwal['jam_mulai'],0,5) ?></div>
                    </div>
                    <div class="page-header-right">
                        <a href="<?= APP_URL ?>/dosen/input_nilai.php?id_jadwal=<?= $id_jadwal ?>" class="btn-primary-custom">
                            <i class="bi bi-pencil-square"></i> Input Nilai Kelas
                        </a>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-people"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $total_students ?></div>
                            <div class="stat-label">Total Mahasiswa</div>
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="bi bi-check2-all"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $graded ?></div>
                            <div class="stat-label">Sudah Dinilai</div>
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $total_students - $graded ?></div>
                            <div class="stat-label">Belum Dinilai</div>
                        </div>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-responsive">
                        <?php if (count($students) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 48px; text-align: center;">NO</th>
                                        <th style="width: 140px;">NIM</th>
                                        <th>MAHASISWA</th>
                                        <th style="width: 140px; text-align: center;">NILAI ANGKA</th>
                                        <th style="width: 140px; text-align: center;">NILAI HURUF</th>
                                        <th style="width: 160px; text-align: center;">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $idx => $student): ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: 700; color: #9CA3AF;"><?= $idx + 1 ?></td>
                                            <td><span class="student-nim"><?= h($student['nim']) ?></span></td>
                                            <td>
                                                <div class="student-nama"><?= h($student['nama']) ?></div>
                                                <div class="student-prodi"><?= h($student['program_studi']) ?></div>
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="nilai-angka"><?= !empty($student['nilai_angka']) ? number_format($student['nilai_angka'], 2) : '-' ?></span>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if (!empty($student['nilai_huruf'])): ?>
                                                    <span style="font-size: 16px; font-weight: 800; color: <?= get_badge_class($student['nilai_huruf']) === 'bg-success' ? '#166534' : '#111827' ?>"><?= h($student['nilai_huruf']) ?></span>
                                                <?php else: ?>
                                                    <span style="color: #9CA3AF;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if (!empty($student['nilai_huruf'])): ?>
                                                    <span class="badge-status tersimpan">
                                                        <i class="bi bi-check-circle-fill" style="margin-right: 6px;"></i> Tersimpan
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge-status belum-dinilai">
                                                        <i class="bi bi-clock-history" style="margin-right: 6px;"></i> Belum Ada
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-person-slash"></i>
                                <p>Belum ada mahasiswa terdaftar di kelas <?= h($jadwal['nama_matkul']) ?>.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
