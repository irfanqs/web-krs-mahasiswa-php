<?php
/**
 * Mahasiswa KHS (Kartu Hasil Studi) Page
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

// Get list of semesters with grades
$stmt = $pdo->prepare("
    SELECT DISTINCT
        s.id_semester,
        s.tahun_ajaran,
        s.tingkatan_semester
    FROM semester s
    LEFT JOIN jadwal_kuliah jk ON s.id_semester = jk.id_semester
    LEFT JOIN krs k ON jk.id_jadwal = k.id_jadwal AND k.id_mahasiswa = ?
    LEFT JOIN nilai n ON k.id_krs = n.id_krs AND n.status_kunci = 1
    WHERE n.id_nilai IS NOT NULL
    ORDER BY s.tahun_ajaran DESC, s.tingkatan_semester DESC
");
$stmt->execute([$nim]);
$semester_list = $stmt->fetchAll();

// Get selected semester (default: latest)
$selected_semester = isset($_GET['semester']) ? (int)$_GET['semester'] : ($semester_list[0]['id_semester'] ?? 0);

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
$nilai_ipk = $stmt->fetch();

$ipk = 0;
$sks_tempuh = 0;
if ($nilai_ipk && $nilai_ipk['total_sks'] > 0) {
    $ipk = round($nilai_ipk['total_bobot'] / $nilai_ipk['total_sks'], 2);
    $sks_tempuh = (int)$nilai_ipk['total_sks'];
}

// Get nilai for selected semester
$stmt = $pdo->prepare("
    SELECT
        n.id_nilai,
        mk.kode_matkul,
        mk.nama_matkul,
        mk.sks,
        n.nilai_angka,
        n.nilai_huruf,
        (CASE n.nilai_huruf WHEN 'A' THEN 4.0 WHEN 'B+' THEN 3.5 WHEN 'B' THEN 3.0 WHEN 'C+' THEN 2.5 WHEN 'C' THEN 2.0 WHEN 'D' THEN 1.0 ELSE 0.0 END * mk.sks) as total_bobot
    FROM krs k
    JOIN jadwal_kuliah jk ON k.id_jadwal = jk.id_jadwal
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    JOIN nilai n ON k.id_krs = n.id_krs
    WHERE k.id_mahasiswa = ? AND jk.id_semester = ? AND n.status_kunci = 1
    ORDER BY mk.nama_matkul ASC
");
$stmt->execute([$nim, $selected_semester]);
$nilai_list = $stmt->fetchAll();

// Calculate semester IPS
$semester_ips = 0;
$semester_sks = 0;
$semester_total_bobot = 0;

foreach ($nilai_list as $n) {
    $semester_sks += $n['sks'];
    $semester_total_bobot += $n['total_bobot'];
}

if ($semester_sks > 0) {
    $semester_ips = round($semester_total_bobot / $semester_sks, 2);
}

// Determine academic status
$status_akademik = 'AKTIF / TERDAFTAR';
if ($semester_ips >= 3.0) {
    $status_akademik = 'AKTIF / MEMUASKAN';
}

$page_title = 'Kartu Hasil Studi';
$current_page = 'khs';

// Find the selected semester string
$selected_semester_name = '';
foreach ($semester_list as $s) {
    if ($s['id_semester'] == $selected_semester) {
        $selected_semester_name = ucfirst($s['tingkatan_semester']) . ' ' . $s['tahun_ajaran'];
        break;
    }
}
if (!$selected_semester_name && !empty($semester_list)) {
     $selected_semester_name = ucfirst($semester_list[0]['tingkatan_semester']) . ' ' . $semester_list[0]['tahun_ajaran'];
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
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 24px;
        }

        .page-header-left {
            flex: 1;
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
            color: #111827;
            margin: 0 0 8px 0;
            line-height: 1.2;
        }

        .page-subtitle {
            color: #6B7280;
            font-size: 14px;
            margin: 0;
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .semester-selector-wrapper {
            position: relative;
        }

        .semester-selector {
            appearance: none;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            padding: 12px 40px 12px 20px;
            border-radius: 99px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            min-width: 200px;
        }

        .semester-selector:focus {
            outline: none;
            border-color: #1B3679;
        }

        .semester-selector-wrapper i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            pointer-events: none;
            font-size: 12px;
        }

        .btn-print {
            padding: 12px 24px;
            background: #E5E7EB;
            color: #111827;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
            text-align: center;
            line-height: 1.2;
        }

        .btn-print:hover {
            background: #D1D5DB;
        }

        .btn-print i {
            font-size: 20px;
            color: #4B5563;
        }

        /* Top 3 Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .summary-card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-navy {
            background: #1B3679;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .card-navy::after {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -20px;
            width: 150px;
            height: 150px;
            background: url('data:image/svg+xml;utf8,<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 3V21H21" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 9L14 14L10 10L3 17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>') no-repeat center;
            background-size: contain;
            opacity: 0.1;
        }

        .summary-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
            color: #6B7280;
        }

        .card-navy .summary-label {
            color: #A9BFFF;
        }

        .summary-value {
            font-size: 48px;
            font-weight: 800;
            line-height: 1;
            color: #111827;
        }

        .card-navy .summary-value {
            color: white;
        }

        .summary-value span {
            font-size: 16px;
            font-weight: 700;
            color: #6B7280;
            margin-left: 4px;
        }

        .card-navy .summary-trend {
            display: inline-flex;
            align-items: center;
            background: rgba(255,255,255,0.15);
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
            color: white;
            margin-top: 16px;
            gap: 4px;
            width: fit-content;
        }

        /* Grades Table Card */
        .grades-card {
            background: white;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 32px;
            overflow: hidden;
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
            padding: 0 16px 20px 16px;
            border-bottom: 2px solid #F3F4F6;
            text-align: left;
        }

        td {
            padding: 20px 16px;
            font-size: 14px;
            border-bottom: 1px solid #F3F4F6;
            color: #374151;
            font-weight: 600;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .col-kode {
            background: #F3F4F6;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            color: #4B5563;
        }

        .col-matkul {
            color: #1B3679;
        }

        .grade-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 13px;
        }

        .grade-a { background: #DCFCE7; color: #166534; }  /* Green */
        .grade-b { background: #FEF08A; color: #854D0E; }  /* Yellow bg, dark yellow text to match 'B' warning style */
        .grade-bplus { background: #CCFBF1; color: #0F766E; } /* Teal */
        .grade-c { background: #FFEDD5; color: #C2410C; }  /* Orange */
        .grade-d { background: #FEE2E2; color: #991B1B; }  /* Red */
        .grade-e { background: #FECACA; color: #7F1D1D; }  /* Dark Red */

        /* Table Footer Row inside Card */
        .grades-summary-footer {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 2px solid #E5E7EB;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 24px;
        }

        .footer-stat {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-stat-label {
            font-size: 11px;
            font-weight: 800;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-stat-value {
            font-size: 24px;
            font-weight: 800;
            color: #1B3679;
        }

        .status-container {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .status-container .footer-stat {
            text-align: right;
        }

        .status-container .footer-stat-value {
            color: #059669;
            font-size: 18px;
        }

        .status-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #DCFCE7;
            color: #10B981;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border: 4px solid white;
            box-shadow: 0 0 0 1px #DCFCE7;
        }

        /* Bottom Notices Grid */
        .notices-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .notice-card {
            background: white;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .notice-header {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1B3679;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .notice-header i {
            font-size: 20px;
        }

        .notice-card p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: #6B7280;
            font-weight: 500;
        }

        .timeline-item {
            background: #F8F9FA;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 8px;
        }

        .timeline-month {
            background: white;
            color: #1B3679;
            font-size: 12px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            text-transform: uppercase;
        }
        
        .timeline-month.orange {
            color: #EA580C;
        }

        .timeline-text {
            display: flex;
            flex-direction: column;
        }

        .timeline-text span {
            font-size: 11px;
            font-weight: 600;
            color: #9CA3AF;
        }

        .timeline-text strong {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        @media (max-width: 1024px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
            .notices-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .grades-summary-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            .status-container {
                align-self: flex-start;
                flex-direction: row-reverse;
                justify-content: flex-end;
            }
            .status-container .footer-stat {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="page-layout">
        <?php include '../../includes/header.php'; ?>

        <main class="page-content">
            <div class="page-header">
                <div class="page-header-left">
                    <div class="breadcrumb">AKADEMIK <span>&gt; KHS</span></div>
                    <h1 class="page-title">Kartu Hasil Studi - <?= h($selected_semester_name) ?></h1>
                    <p class="page-subtitle">Detailed performance report for your current academic cycle.</p>
                </div>
                
                <div class="header-actions">
                    <?php if (count($semester_list) > 0): ?>
                        <div class="semester-selector-wrapper">
                            <select class="semester-selector" onchange="window.location.href='?semester=' + this.value">
                                <?php foreach ($semester_list as $sem): ?>
                                    <option value="<?= $sem['id_semester'] ?>" <?= $sem['id_semester'] == $selected_semester ? 'selected' : '' ?>>
                                        <?= ucfirst($sem['tingkatan_semester']) ?> <?= h($sem['tahun_ajaran']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    <?php endif; ?>
                    
                    <button class="btn-print" onclick="window.print()">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <div>
                            Cetak<br>KHS (PDF)
                        </div>
                    </button>
                </div>
            </div>

            <?php if (count($semester_list) > 0 && $selected_semester > 0): ?>
                
                <div class="summary-grid">
                    <div class="summary-card card-navy">
                        <div>
                            <div class="summary-label">IP SEMESTER INI</div>
                            <div class="summary-value"><?= number_format($semester_ips, 2) ?></div>
                            <div class="summary-trend"><i class="bi bi-graph-up-arrow"></i> +0.07 from prev sem</div>
                        </div>
                    </div>
                    
                    <div class="summary-card" style="border-left: 4px solid #BFDBFE;">
                        <div>
                            <div class="summary-label">IP KUMULATIF (IPK)</div>
                            <div class="summary-value"><?= number_format($ipk, 2) ?></div>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div>
                            <div class="summary-label">SKS TEMPUH</div>
                            <div class="summary-value"><?= $sks_tempuh ?><span>/ 144</span></div>
                        </div>
                    </div>
                </div>

                <div class="grades-card">
                    <?php if (count($nilai_list) > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>KODE</th>
                                        <th>MATA KULIAH</th>
                                        <th style="text-align: center;">SKS</th>
                                        <th style="text-align: center;">NILAI</th>
                                        <th style="text-align: center;">BOBOT</th>
                                        <th style="text-align: center;">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($nilai_list as $nilai):
                                        $grade_class = 'grade-a';
                                        if ($nilai['nilai_huruf'] === 'B+') $grade_class = 'grade-bplus';
                                        if ($nilai['nilai_huruf'] === 'B') $grade_class = 'grade-b';
                                        if ($nilai['nilai_huruf'] === 'C+' || $nilai['nilai_huruf'] === 'C') $grade_class = 'grade-c';
                                        if ($nilai['nilai_huruf'] === 'D') $grade_class = 'grade-d';
                                        if ($nilai['nilai_huruf'] === 'E') $grade_class = 'grade-e';
                                    ?>
                                        <tr>
                                            <td><span class="col-kode"><?= h($nilai['kode_matkul']) ?></span></td>
                                            <td class="col-matkul"><?= h($nilai['nama_matkul']) ?></td>
                                            <td style="text-align: center; color: #6B7280; font-weight: 500;"><?= $nilai['sks'] ?></td>
                                            <td style="text-align: center;">
                                                <span class="grade-badge <?= $grade_class ?>">
                                                    <?= h($nilai['nilai_huruf']) ?>
                                                </span>
                                            </td>
                                            <td style="text-align: center; color: #6B7280; font-weight: 500;"><?= number_format(nilai_bobot($nilai['nilai_huruf']), 1) ?></td>
                                            <td style="text-align: center; color: #1B3679; font-weight: 800;"><?= number_format($nilai['total_bobot'], 1) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="grades-summary-footer">
                            <div style="display: flex; gap: 48px; flex-wrap: wrap;">
                                <div class="footer-stat">
                                    <div class="footer-stat-label">TOTAL SKS DIAMBIL</div>
                                    <div class="footer-stat-value"><?= $semester_sks ?> SKS</div>
                                </div>
                                <div class="footer-stat">
                                    <div class="footer-stat-label">TOTAL BOBOT NILAI</div>
                                    <div class="footer-stat-value"><?= number_format($semester_total_bobot, 2) ?></div>
                                </div>
                            </div>
                            <div class="status-container">
                                <div class="footer-stat">
                                    <div class="footer-stat-label">STATUS AKADEMIK</div>
                                    <div class="footer-stat-value"><?= h($status_akademik) ?></div>
                                </div>
                                <?php if ($semester_ips >= 3.0): ?>
                                    <div class="status-icon"><i class="bi bi-check"></i></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 48px; color: #9CA3AF;">
                            <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                            <p style="font-weight: 500; margin: 0;">Tidak ada nilai untuk semester yang dipilih.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="notices-grid">
                    <div class="notice-card">
                        <div class="notice-header">
                            <i class="bi bi-info-circle"></i> ACADEMIC NOTICE
                        </div>
                        <p>Nilai <?= h($selected_semester_name) ?> telah diverifikasi oleh Ketua Program Studi. Silakan menghubungi bagian akademik jika terdapat ketidaksesuaian data. Proses revisi nilai maksimal dilakukan 7 hari setelah pengumuman.</p>
                    </div>

                    <div class="notice-card">
                        <div class="notice-header">
                            <i class="bi bi-calendar3"></i> TIMELINE & REMINDERS
                        </div>
                        <div>
                            <div class="timeline-item">
                                <div class="timeline-month">JUL</div>
                                <div class="timeline-text">
                                    <span>Pengisian KRS Ganjil</span>
                                    <strong>Starts 24 July 2024</strong>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-month orange">AUG</div>
                                <div class="timeline-text">
                                    <span>Kuliah Perdana</span>
                                    <strong>28 August 2024</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="grades-card" style="text-align: center; padding: 64px 32px; margin-top: 32px;">
                    <i class="bi bi-inbox" style="font-size: 64px; color: #D1D5DB; margin-bottom: 24px; display: block;"></i>
                    <h2 style="margin: 0 0 8px 0; font-size: 20px; color: #111827;">Belum ada Data Nilai</h2>
                    <p style="margin: 0; color: #6B7280;">Nilai akan muncul di sini setelah dosen memasukkan dan mengunci nilai Anda untuk semester aktif.</p>
                </div>
            <?php endif; ?>
        </main>
        
        <?php include '../../includes/footer.php'; ?>
    </div>
</body>
</html>
