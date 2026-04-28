<?php
/**
 * Admin - Manajemen Mahasiswa
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/helpers.php';

require_role(['admin']);

$pdo = get_pdo();
$page_title = 'Manajemen Mahasiswa';
$current_page = 'mahasiswa';

// Pagination
$per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Count total
if ($search) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM mahasiswa WHERE nama LIKE ? OR nim LIKE ?");
    $search_term = "%{$search}%";
    $stmt->execute([$search_term, $search_term]);
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM mahasiswa");
    $stmt->execute();
}
$total = $stmt->fetchColumn();
$total_pages = ceil($total / $per_page);
$offset = ($page - 1) * $per_page;

// Get data
if ($search) {
    $stmt = $pdo->prepare("
        SELECT nim, nama, email, program_studi, angkatan, status
        FROM mahasiswa
        WHERE nama LIKE ? OR nim LIKE ?
        ORDER BY nama
        LIMIT ? OFFSET ?
    ");
    $search_term = "%{$search}%";
    $stmt->execute([$search_term, $search_term, $per_page, $offset]);
} else {
    $stmt = $pdo->prepare("
        SELECT nim, nama, email, program_studi, angkatan, status
        FROM mahasiswa
        ORDER BY nama
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$per_page, $offset]);
}
$mahasiswa = $stmt->fetchAll();

// Get stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE status = 'aktif'");
$stmt->execute();
$count_aktif = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE status = 'cuti' OR status = 'nonaktif'");
$stmt->execute();
$count_nonaktif = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE status = 'lulus'");
$stmt->execute();
$count_lulus = $stmt->fetchColumn();

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
            margin: 0 0 8px 0;
            line-height: 1.2;
        }

        .page-subtitle {
            color: #6B7280;
            font-size: 14px;
            margin: 0;
            font-weight: 500;
        }

        .btn-primary {
            padding: 12px 24px;
            background: #1B3679;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #25408E;
            transform: translateY(-2px);
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .stat-icon {
            position: absolute;
            top: 24px;
            right: 24px;
            width: 40px;
            height: 40px;
            background: #F3F4F6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #1B3679;
        }

        .stat-label {
            font-size: 11px;
            font-weight: 800;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 12px;
        }

        .stat-badge {
            align-self: flex-start;
            background: #EFF6FF;
            color: #1E40AF;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
        }

        .stat-badge.success { background: #DCFCE7; color: #166534; }
        .stat-badge.warning { background: #FFEDD5; color: #C2410C; }
        .stat-badge.info { background: #F3E8FF; color: #6B21A8; }

        /* Table Card & Actions */
        .table-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .table-actions {
            padding: 24px;
            border-bottom: 1px solid #F3F4F6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-form {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 14px;
        }

        .search-input {
            padding: 10px 16px 10px 40px;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            width: 250px;
            transition: all 0.2s;
            font-family: inherit;
        }

        .search-input:focus {
            border-color: #1B3679;
            box-shadow: 0 0 0 3px rgba(27, 54, 121, 0.1);
        }

        .btn-search {
            padding: 10px 20px;
            background: #F3F4F6;
            color: #111827;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-search:hover {
            background: #E5E7EB;
        }

        .btn-clear {
            color: #EF4444;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        /* Table Styles */
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
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: #F8FAFC;
        }

        .mhs-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .mhs-name {
            font-weight: 700;
            color: #111827;
        }

        .mhs-nim {
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
        }

        .dept-badge {
            background: #F3F4F6;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #4B5563;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .status-aktif { background: #DCFCE7; color: #166534; }
        .status-cuti, .status-nonaktif { background: #FEF08A; color: #854D0E; }
        .status-lulus { background: #DBEAFE; color: #1E40AF; }

        .action-btns {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6B7280;
            background: #F3F4F6;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-icon:hover {
            background: #E5E7EB;
            color: #111827;
        }

        .btn-icon.danger {
            color: #EF4444;
            background: #FEF2F2;
        }

        .btn-icon.danger:hover {
            background: #FEE2E2;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 24px;
            background: #FBFCFD;
            border-top: 1px solid #F3F4F6;
        }

        .page-link {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #4B5563;
            background: white;
            border: 1px solid #E5E7EB;
            text-decoration: none;
            transition: all 0.2s;
        }

        .page-link:hover {
            border-color: #1B3679;
            color: #1B3679;
        }

        .page-link.active {
            background: #1B3679;
            color: white;
            border-color: #1B3679;
        }

        @media (max-width: 1024px) {
            .table-actions {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            .search-form {
                width: 100%;
            }
            .search-input {
                flex: 1;
                width: auto;
            }
        }
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
                        <div class="breadcrumb">AKADEMIK <span>&gt; MAHASISWA</span></div>
                        <h1 class="page-title">Manajemen Data Mahasiswa</h1>
                        <p class="page-subtitle">Kelola data, status, dan informasi profil mahasiswa.</p>
                    </div>
                    
                    <div class="header-actions">
                        <a href="<?= APP_URL ?>/admin/mahasiswa/tambah.php" class="btn-primary">
                            <i class="bi bi-plus-lg"></i> Tambah Mahasiswa
                        </a>
                    </div>
                </div>

                <?php if ($msg = flash('success')): ?>
                    <div style="background: #DCFCE7; color: #166534; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-check-circle" style="font-size: 20px;"></i> <?= h($msg) ?>
                    </div>
                <?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-people"></i></div>
                        <div class="stat-label">TOTAL MAHASISWA</div>
                        <div class="stat-value"><?= number_format($total) ?></div>
                        <div class="stat-badge">Total Enrolled</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                        <div class="stat-label">AKTIF</div>
                        <div class="stat-value"><?= number_format($count_aktif) ?></div>
                        <div class="stat-badge success">Current</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-pause-circle"></i></div>
                        <div class="stat-label">CUTI / NON-AKTIF</div>
                        <div class="stat-value"><?= number_format($count_nonaktif) ?></div>
                        <div class="stat-badge warning">Inactive</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-mortarboard"></i></div>
                        <div class="stat-label">LULUS</div>
                        <div class="stat-value"><?= number_format($count_lulus) ?></div>
                        <div class="stat-badge info">Graduated</div>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-actions">
                        <form method="GET" class="search-form">
                            <div class="search-input-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search" class="search-input" placeholder="Cari nim atau nama..." value="<?= h($search) ?>">
                            </div>
                            <button type="submit" class="btn-search">Search</button>
                            <?php if ($search): ?>
                                <a href="<?= APP_URL ?>/admin/mahasiswa/index.php" class="btn-clear"><i class="bi bi-x-circle"></i> Clear</a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 48px;">NO</th>
                                    <th>MAHASISWA</th>
                                    <th>PROGRAM STUDI</th>
                                    <th style="text-align: center;">ANGKATAN</th>
                                    <th>STATUS</th>
                                    <th style="width: 100px; text-align: center;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($mahasiswa) > 0): ?>
                                    <?php foreach ($mahasiswa as $idx => $m): 
                                        $status_class = 'status-aktif';
                                        if ($m['status'] == 'cuti' || $m['status'] == 'nonaktif') $status_class = 'status-cuti';
                                        if ($m['status'] == 'lulus') $status_class = 'status-lulus';
                                    ?>
                                        <tr>
                                            <td style="font-weight: 700; color: #6B7280;"><?= ($offset + $idx + 1) ?></td>
                                            <td>
                                                <div class="mhs-info">
                                                    <span class="mhs-name"><?= h($m['nama']) ?></span>
                                                    <span class="mhs-nim">NIM: <?= h($m['nim']) ?></span>
                                                </div>
                                            </td>
                                            <td><span class="dept-badge"><?= h($m['program_studi']) ?></span></td>
                                            <td style="text-align: center; font-weight: 600; color: #4B5563;"><?= h($m['angkatan']) ?></td>
                                            <td><span class="status-badge <?= $status_class ?>"><?= ucfirst(h($m['status'])) ?></span></td>
                                            <td>
                                                <div class="action-btns" style="justify-content: center;">
                                                    <a href="<?= APP_URL ?>/admin/mahasiswa/edit.php?nim=<?= urlencode($m['nim']) ?>" class="btn-icon" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="<?= APP_URL ?>/admin/mahasiswa/hapus.php?nim=<?= urlencode($m['nim']) ?>" class="btn-icon danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 48px; color: #9CA3AF;">
                                            <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                            <p style="font-weight: 500; margin: 0;">Tidak ada data mahasiswa ditemukan.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link"><i class="bi bi-chevron-left"></i> Prev</a>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <a href="?page=<?= $i ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link <?= $i === $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?= $page + 1 ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link">Next <i class="bi bi-chevron-right"></i></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

    <?php include '../../../includes/footer.php'; ?>
</body>
</html>
