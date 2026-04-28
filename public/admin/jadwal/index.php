<?php
require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/helpers.php';
require_role(['admin']);

$pdo = get_pdo();
$page_title = 'Manajemen Jadwal Kuliah';

$stmt = $pdo->prepare("
    SELECT jk.*, mk.nama_matkul, mk.kode_matkul, mk.sks, d.nama, s.tahun_ajaran
    FROM jadwal_kuliah jk
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    JOIN dosen d ON jk.id_dosen = d.nidn
    JOIN semester s ON jk.id_semester = s.id_semester
    ORDER BY s.tahun_ajaran DESC, jk.hari, jk.jam_mulai
");
$stmt->execute();
$jadwal_list = $stmt->fetchAll();

$total_jadwal = count($jadwal_list);

// Calculate total active classes for today (just for metrics)
$today_hari = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'][date('l')];
$today_classes = 0;
foreach($jadwal_list as $j) {
    if ($j['hari'] === $today_hari) $today_classes++;
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

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #1B3679;
            margin: 0;
            line-height: 1.2;
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
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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

        .table-actions {
            padding: 24px;
            border-bottom: 1px solid #F3F4F6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
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

        .matkul-kode {
            font-size: 12px;
            font-weight: 700;
            color: #1B3679;
            background: #EFF6FF;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 4px;
        }
        .matkul-nama {
            font-weight: 700;
            color: #111827;
            display: block;
        }
        
        .dosen-name {
            font-weight: 600;
            color: #4B5563;
        }

        .jadwal-time {
            font-family: monospace;
            font-weight: 600;
            color: #111827;
            background: #F3F4F6;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 13px;
        }

        .action-flex {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: white;
            border: 1px solid #E5E7EB;
            color: #4B5563;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-icon:hover { border-color: #1B3679; color: #1B3679; background: #EFF6FF; }
        .btn-icon.danger { color: #DC2626; }
        .btn-icon.danger:hover { border-color: #DC2626; background: #FEF2F2; }

    </style>
</head>
<body>
    <div class="page-layout">
        <?php include '../../../includes/sidebar.php'; ?>

        <div class="page-main">
            <?php include '../../../includes/header.php'; ?>

            <main class="page-content">
                <div class="page-header">
                    <div class="page-header-left">
                        <div class="breadcrumb">ADMINISTRATOR <span>&gt; MANAJEMEN JADWAL</span></div>
                        <h1 class="page-title"><?= h($page_title) ?></h1>
                    </div>
                    <div class="page-header-right">
                        <a href="<?= APP_URL ?>/admin/jadwal/tambah.php" class="btn-primary-custom">
                            <i class="bi bi-calendar-plus"></i> Tambah Jadwal Kuliah
                        </a>
                    </div>
                </div>

                <?php if ($msg = flash('success')): ?>
                    <div style="background: #DCFCE7; color: #166534; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-check-circle-fill"></i> <?= h($msg) ?>
                    </div>
                <?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-calendar-week"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $total_jadwal ?></div>
                            <div class="stat-label">Total Jadwal Aktif</div>
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="bi bi-calendar2-day"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $today_classes ?></div>
                            <div class="stat-label">Jadwal Kelas Hari Ini</div>
                        </div>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-responsive">
                        <?php if (count($jadwal_list) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 48px; text-align: center;">NO</th>
                                        <th>MATA KULIAH</th>
                                        <th>DOSEN PENGAJAR</th>
                                        <th>HARI & WAKTU</th>
                                        <th>RUANGAN</th>
                                        <th style="width: 80px; text-align: center;">KUOTA</th>
                                        <th style="width: 120px; text-align: center;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jadwal_list as $idx => $j): ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: 700; color: #9CA3AF;"><?= ($idx + 1) ?></td>
                                            <td>
                                                <span class="matkul-kode"><?= h($j['kode_matkul']) ?></span>
                                                <span class="matkul-nama"><?= h($j['nama_matkul']) ?> (<?= $j['sks'] ?> SKS)</span>
                                            </td>
                                            <td><span class="dosen-name"><?= h($j['nama']) ?></span></td>
                                            <td>
                                                <div style="font-weight: 700; color: #4B5563; margin-bottom: 4px;"><?= h($j['hari']) ?></div>
                                                <span class="jadwal-time"><?= substr($j['jam_mulai'], 0, 5) ?> - <?= substr($j['jam_selesai'], 0, 5) ?></span>
                                            </td>
                                            <td><div style="display: flex; align-items: center; gap: 6px;"><i class="bi bi-geo-alt" style="color: #9CA3AF;"></i> <?= h($j['ruang']) ?></div></td>
                                            <td style="text-align: center; font-weight: 600; color: #1B3679;"><?= $j['kuota'] ?></td>
                                            <td>
                                                <div class="action-flex" style="justify-content: center;">
                                                    <a href="<?= APP_URL ?>/admin/jadwal/edit.php?id=<?= $j['id_jadwal'] ?>" class="btn-icon" title="Edit Jadwal">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="<?= APP_URL ?>/admin/jadwal/hapus.php?id=<?= $j['id_jadwal'] ?>" class="btn-icon danger" title="Hapus Jadwal" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                                        <i class="bi bi-trash3"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div style="padding: 64px 24px; text-align: center;">
                                <i class="bi bi-calendar-x" style="font-size: 48px; color: #D1D5DB; margin-bottom: 16px; display: block;"></i>
                                <p style="color: #6B7280; font-weight: 500; margin: 0;">Tidak ada data jadwal kuliah ditemukan.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../../../includes/footer.php'; ?>
</body>
</html>
