<?php
require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/helpers.php';
require_role(['admin']);

$pdo = get_pdo();
$page_title = 'Manajemen Semester';
$current_page = 'semester'; // for active sidebar if added

$stmt = $pdo->prepare("SELECT * FROM semester ORDER BY tahun_ajaran DESC, tingkatan_semester DESC");
$stmt->execute();
$semesters = $stmt->fetchAll();

$total_semesters = count($semesters);
$active_semesters = 0;
foreach($semesters as $s) {
    if ($s['status'] === 'aktif') $active_semesters++;
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

        .ta-badge {
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
        .badge-status.aktif { background: #DCFCE7; color: #166534; }
        .badge-status.tidak-aktif { background: #F3F4F6; color: #6B7280; }

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
                        <div class="breadcrumb">ADMINISTRATOR <span>&gt; MANAJEMEN SEMESTER</span></div>
                        <h1 class="page-title"><?= h($page_title) ?></h1>
                    </div>
                    <div class="page-header-right">
                        <a href="<?= APP_URL ?>/admin/semester/tambah.php" class="btn-primary-custom">
                            <i class="bi bi-plus-lg"></i> Tambah Semester
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
                        <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $total_semesters ?></div>
                            <div class="stat-label">Total Tahun Ajaran</div>
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="bi bi-calendar-check text-success"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $active_semesters ?></div>
                            <div class="stat-label">Semester Aktif</div>
                        </div>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-responsive">
                        <?php if (count($semesters) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 48px; text-align: center;">NO</th>
                                        <th>TAHUN AJARAN</th>
                                        <th>TINGKATAN SEMESTER</th>
                                        <th style="width: 140px; text-align: center;">STATUS</th>
                                        <th style="width: 120px; text-align: center;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($semesters as $idx => $s): ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: 700; color: #9CA3AF;"><?= ($idx + 1) ?></td>
                                            <td><span class="ta-badge"><?= h($s['tahun_ajaran']) ?></span></td>
                                            <td style="color: #4B5563; font-weight: 500; font-size: 15px;"><?= ucfirst($s['tingkatan_semester']) ?></td>
                                            <td style="text-align: center;">
                                                <span class="badge-status <?= $s['status'] === 'aktif' ? 'aktif' : 'tidak-aktif' ?>">
                                                    <?= $s['status'] === 'aktif' ? '<i class="bi bi-check-circle-fill" style="margin-right: 6px;"></i> Aktif' : 'Tidak Aktif' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-flex" style="justify-content: center;">
                                                    <a href="<?= APP_URL ?>/admin/semester/edit.php?id=<?= $s['id_semester'] ?>" class="btn-icon" title="Edit Semester">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="<?= APP_URL ?>/admin/semester/hapus.php?id=<?= $s['id_semester'] ?>" class="btn-icon danger" title="Hapus Semester" onclick="return confirm('Apakah Anda yakin ingin menghapus semester ini?');">
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
                                <p style="color: #6B7280; font-weight: 500; margin: 0;">Tidak ada data semester ditemukan.</p>
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
