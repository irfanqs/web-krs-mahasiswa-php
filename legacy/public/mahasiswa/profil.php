<?php
/**
 * Mahasiswa Academic Profile
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

// Get IPK and SKS
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
    $ipk = round($nilai_data['total_bobot'] / $nilai_data['total_sks'], 2);
    $sks_tempuh = (int)$nilai_data['total_sks'];
}

// Get predicate (A-, B+, etc for UI mockup matching)
$predicate = 'A-';
if ($ipk >= 3.8) {
    $predicate = 'A';
} elseif ($ipk < 3.5 && $ipk >= 3.0) {
    $predicate = 'B+';
} elseif ($ipk < 3.0) {
    $predicate = 'B';
}

// Get current active semester
$stmt = $pdo->prepare("SELECT * FROM semester WHERE status = 'aktif' LIMIT 1");
$stmt->execute();
$semester_aktif = $stmt->fetch();

$active_sem_name = "2023/2024 Even"; // default for UI mockup
if ($semester_aktif) {
    $active_sem_name = $semester_aktif['tahun_ajaran'] . ' ' . ucfirst($semester_aktif['tingkatan_semester']);
}

// Get semester performance (history)
$stmt = $pdo->prepare("
    SELECT
        s.tahun_ajaran,
        s.tingkatan_semester,
        COUNT(DISTINCT k.id_krs) as jumlah_matkul,
        SUM(mk.sks) as sks_diambil,
        ROUND(SUM(CASE n.nilai_huruf WHEN 'A' THEN 4.0 WHEN 'B+' THEN 3.5 WHEN 'B' THEN 3.0 WHEN 'C+' THEN 2.5 WHEN 'C' THEN 2.0 WHEN 'D' THEN 1.0 ELSE 0.0 END * mk.sks) / NULLIF(SUM(mk.sks), 0), 2) as ips
    FROM semester s
    LEFT JOIN jadwal_kuliah jk ON s.id_semester = jk.id_semester
    LEFT JOIN krs k ON jk.id_jadwal = k.id_jadwal AND k.id_mahasiswa = ?
    LEFT JOIN nilai n ON k.id_krs = n.id_krs AND n.status_kunci = 1
    LEFT JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    WHERE s.status = 'nonaktif' OR s.status = 'aktif'
    GROUP BY s.id_semester
    ORDER BY s.tahun_ajaran DESC, s.tingkatan_semester DESC
    LIMIT 8
");
$stmt->execute([$nim]);
$semester_history = $stmt->fetchAll();

$page_title = 'Academic Profile';
$current_page = 'profil';

$avatar_initial = strtoupper(substr($mahasiswa['nama'] ?? 'M', 0, 1));
$email = strtolower(str_replace(' ', '.', $mahasiswa['nama'])) . '@student.university.ac.id';
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
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 24px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #1B3679;
            margin: 0 0 8px 0;
            line-height: 1.2;
        }

        .page-subtitle {
            color: #4B5563;
            font-size: 14px;
            margin: 0;
            font-weight: 500;
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
        }

        .btn-print:hover {
            background: #D1D5DB;
        }

        .btn-print i {
            font-size: 18px;
        }

        /* Top Grid */
        .top-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 32px;
            margin-bottom: 40px;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            gap: 32px;
            position: relative;
            overflow: hidden;
        }

        .profile-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 120px;
            background: #F3F4F6;
            border-bottom-left-radius: 120px;
            opacity: 0.5;
        }

        .profile-avatar {
            width: 140px;
            height: 160px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1B3679 0%, #25408E 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: white;
            font-size: 64px;
            font-weight: 800;
            object-fit: cover;
            box-shadow: 0 10px 25px rgba(27, 54, 121, 0.2);
        }

        .profile-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            z-index: 1;
        }

        .profile-name {
            font-size: 24px;
            font-weight: 800;
            color: #1B3679;
            margin: 0 0 4px 0;
        }

        .profile-nim {
            font-size: 14px;
            font-weight: 600;
            color: #6B7280;
            margin: 0 0 24px 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 10px;
            font-weight: 800;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        /* Academic Standing Card */
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .standing-card {
            background: #1B3679;
            border-radius: 20px;
            padding: 32px;
            color: white;
            position: relative;
            box-shadow: 0 10px 30px rgba(27, 54, 121, 0.15);
        }

        .standing-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #A9BFFF;
            margin-bottom: 24px;
        }

        .standing-gpa {
            font-size: 48px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 8px;
        }

        .standing-subtitle {
            font-size: 13px;
            font-weight: 500;
            color: #D8E0FE;
        }

        .standing-stats {
            margin-top: 32px;
            display: flex;
            gap: 32px;
        }

        .stat-group {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 800;
        }

        .stat-label {
            font-size: 10px;
            font-weight: 700;
            color: #A9BFFF;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .standing-icon {
            position: absolute;
            top: 32px;
            right: 32px;
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .enrollment-card {
            background: white;
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .enrollment-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .enroll-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #DCFCE7;
            color: #10B981;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .enroll-info h4 {
            margin: 0 0 2px 0;
            font-size: 11px;
            font-weight: 800;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .enroll-info p {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }

        .enroll-progress {
            background: #F3F4F6;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
        }

        .enroll-progress-bar {
            height: 100%;
            background: #10B981;
            width: 100%;
        }

        .enroll-footer {
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
        }

        /* Section Titles */
        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 800;
            color: #1B3679;
            margin: 0 0 24px 0;
        }

        .section-line {
            width: 32px;
            height: 4px;
            background: #1B3679;
            border-radius: 2px;
        }

        /* Details Grid */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 48px;
        }

        .detail-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .detail-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            font-size: 12px;
            font-weight: 800;
            color: #1B3679;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-header i {
            font-size: 18px;
        }

        .detail-content {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .detail-item p {
            margin: 0;
        }

        .detail-item-label {
            font-size: 10px;
            font-weight: 800;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .detail-item-value {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            line-height: 1.5;
        }

        /* Table Section */
        .table-card {
            background: white;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
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

        .semester-name {
            color: #1B3679;
            font-weight: 800;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            display: inline-flex;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-pass {
            background: #DCFCE7;
            color: #166534;
        }

        .status-warn {
            background: #FEF08A;
            color: #854D0E;
        }

        @media (max-width: 1024px) {
            .top-grid {
                grid-template-columns: 1fr;
            }
            .details-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .profile-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .info-grid {
                grid-template-columns: 1fr;
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
                <div>
                    <h1 class="page-title">Academic Profile</h1>
                    <p class="page-subtitle">Manage and view your official university credentials.</p>
                </div>
                <button class="btn-print" onclick="window.print()">
                    <i class="bi bi-download"></i>
                    Print Transcript
                </button>
            </div>

            <div class="top-grid">
                <!-- Profile General Info -->
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?= h($avatar_initial) ?>
                    </div>
                    <div class="profile-details">
                        <h2 class="profile-name"><?= h($mahasiswa['nama'] ?? 'Student') ?></h2>
                        <p class="profile-nim">NIM: <?= h($mahasiswa['nim']) ?></p>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Major</span>
                                <span class="info-value"><?= h($mahasiswa['program_studi'] ?? 'Informatics Engineering') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Degree Program</span>
                                <span class="info-value">Bachelor of Science (S1)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Entry Year</span>
                                <span class="info-value"><?= h($mahasiswa['angkatan'] ?? '2021') ?> (Odd Semester)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Current Semester</span>
                                <span class="info-value">6 (Enrolled)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Stats -->
                <div class="right-column">
                    <div class="standing-card">
                        <div class="standing-label">Academic Standing</div>
                        <div class="standing-gpa"><?= number_format($ipk, 2) ?></div>
                        <div class="standing-subtitle">Cumulative GPA (IPK)</div>
                        <div class="standing-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        
                        <div class="standing-stats">
                            <div class="stat-group">
                                <div class="stat-value"><?= $sks_tempuh ?></div>
                                <div class="stat-label">SKS Earned</div>
                            </div>
                            <div class="stat-group">
                                <div class="stat-value"><?= h($predicate) ?></div>
                                <div class="stat-label">Predicates</div>
                            </div>
                        </div>
                    </div>

                    <div class="enrollment-card">
                        <div class="enrollment-header">
                            <div class="enroll-icon"><i class="bi bi-check2"></i></div>
                            <div class="enroll-info">
                                <h4>Enrollment Status</h4>
                                <p>Active (<?= h($active_sem_name) ?>)</p>
                            </div>
                        </div>
                        <div class="enroll-progress">
                            <div class="enroll-progress-bar"></div>
                        </div>
                        <div class="enroll-footer">Study Plan Approved by Supervisor</div>
                    </div>
                </div>
            </div>

            <!-- Personal Details -->
            <h3 class="section-title"><div class="section-line"></div> Personal Details</h3>
            <div class="details-grid">
                <div class="detail-card">
                    <div class="detail-header"><i class="bi bi-envelope"></i> Contact Info</div>
                    <div class="detail-content">
                        <div class="detail-item">
                            <div class="detail-item-label">University Email</div>
                            <div class="detail-item-value"><?= h($email) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item-label">Phone Number</div>
                            <div class="detail-item-value">+62 812-3456-7890</div>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-header"><i class="bi bi-geo-alt"></i> Address</div>
                    <div class="detail-content">
                        <div class="detail-item">
                            <div class="detail-item-label">Home Address</div>
                            <div class="detail-item-value">Jl. Kebon Jeruk No. 12, Jakarta Barat, DKI Jakarta, 11530</div>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-header"><i class="bi bi-person-vcard"></i> Biographical</div>
                    <div class="detail-content">
                        <div class="detail-item">
                            <div class="detail-item-label">Place & Date of Birth</div>
                            <div class="detail-item-value">Surabaya, 14 August 2003</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item-label">Religion</div>
                            <div class="detail-item-value">Islam</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semester Performance Table -->
            <h3 class="section-title"><div class="section-line"></div> Semester Performance</h3>
            <div class="table-card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>SEMESTER</th>
                                <th>PERIOD</th>
                                <th class="text-center">SKS TAKEN</th>
                                <th class="text-center">SEMESTER GPA (IPS)</th>
                                <th class="text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($semester_history) > 0): ?>
                                <?php foreach ($semester_history as $index => $sem): ?>
                                    <tr>
                                        <td class="semester-name">Semester <?= (count($semester_history) - $index) ?></td>
                                        <td><?= h($sem['tahun_ajaran'] ?? '-') ?> <?= ucfirst(h($sem['tingkatan_semester'] ?? '-')) ?></td>
                                        <td class="text-center"><?= ($sem['sks_diambil'] ?? 0) ?></td>
                                        <td class="text-center" style="font-weight: 800;"><?= number_format($sem['ips'] ?? 0, 2) ?></td>
                                        <td class="text-center">
                                            <?php if ($sem['ips'] && $sem['ips'] >= 2.0): ?>
                                                <span class="status-badge status-pass">PASS</span>
                                            <?php else: ?>
                                                <span class="status-badge status-warn">WARN</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 48px; color: #9CA3AF;">
                                        No academic history found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
