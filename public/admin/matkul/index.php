<?php
require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/helpers.php';
require_role(['admin']);

$pdo = get_pdo();
$page_title = 'Manajemen Mata Kuliah';
$current_page = 'matkul';

$per_page = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM mata_kuliah WHERE nama_matkul LIKE ? OR kode_matkul LIKE ?");
    $search_term = "%{$search}%";
    $stmt->execute([$search_term, $search_term]);
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM mata_kuliah");
    $stmt->execute();
}
$total = $stmt->fetchColumn();
$total_pages = ceil($total / $per_page);
$offset = ($page - 1) * $per_page;

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM mata_kuliah WHERE nama_matkul LIKE ? OR kode_matkul LIKE ? ORDER BY nama_matkul LIMIT ? OFFSET ?");
    $search_term = "%{$search}%";
    $stmt->execute([$search_term, $search_term, $per_page, $offset]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM mata_kuliah ORDER BY nama_matkul LIMIT ? OFFSET ?");
    $stmt->execute([$per_page, $offset]);
}
$matkul = $stmt->fetchAll();

// Count types just for metric
$stmt_jenis = $pdo->query("SELECT jenis, COUNT(*) as count FROM mata_kuliah GROUP BY jenis");
$jenis_counts = $stmt_jenis->fetchAll(PDO::FETCH_KEY_PAIR);
$total_wajib = $jenis_counts['wajib'] ?? 0;
$total_pilihan = $jenis_counts['pilihan'] ?? 0;

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
        
        .stat-card.indigo .stat-icon { background: #E0E7FF; color: #3730A3; }
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

        .table-actions {
            padding: 24px;
            border-bottom: 1px solid #F3F4F6;
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-form {
            display: flex;
            flex: 1;
            gap: 12px;
            max-width: 500px;
        }

        .search-input-wrapper {
            position: relative;
            flex: 1;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 16px;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
            box-sizing: border-box;
            background: #F8FAFC;
        }

        .search-input:focus {
            border-color: #1B3679;
            background: white;
            box-shadow: 0 0 0 3px rgba(27, 54, 121, 0.1);
        }

        .btn-search {
            background: #1B3679;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-search:hover { background: #25408E; }

        .btn-ghost {
            background: transparent;
            color: #6B7280;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 0 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }
        .btn-ghost:hover { background: #F3F4F6; color: #111827; }

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
        }
        .matkul-nama {
            font-weight: 700;
            color: #111827;
        }

        .badge-jenis {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-jenis.wajib { background: #DCFCE7; color: #166534; }
        .badge-jenis.pilihan { background: #F3F4F6; color: #4B5563; }

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

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 24px;
            border-top: 1px solid #F3F4F6;
        }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            background: white;
            border: 1px solid #E5E7EB;
            color: #4B5563;
            text-decoration: none;
            transition: all 0.2s;
        }

        .page-link:hover {
            border-color: #1B3679;
            color: #1B3679;
        }

        .page-link.active {
            background: #1B3679;
            border-color: #1B3679;
            color: white;
        }

        .empty-state {
            padding: 64px 24px;
            text-align: center;
        }
        .empty-state i {
            font-size: 48px;
            color: #D1D5DB;
            margin-bottom: 16px;
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
        <?php include '../../../includes/sidebar.php'; ?>

        <div class="page-main">
            <?php include '../../../includes/header.php'; ?>

            <main class="page-content">
                <div class="page-header">
                    <div class="page-header-left">
                        <div class="breadcrumb">ADMINISTRATOR <span>&gt; MATA KULIAH</span></div>
                        <h1 class="page-title"><?= h($page_title) ?></h1>
                    </div>
                    <div class="page-header-right">
                        <a href="<?= APP_URL ?>/admin/matkul/tambah.php" class="btn-primary-custom">
                            <i class="bi bi-plus-lg"></i> Tambah Mata Kuliah
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
                        <div class="stat-icon"><i class="bi bi-book"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $total ?></div>
                            <div class="stat-label">Total Mata Kuliah</div>
                        </div>
                    </div>
                    <div class="stat-card indigo">
                        <div class="stat-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $total_wajib ?></div>
                            <div class="stat-label">Mata Kuliah Wajib</div>
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-icon"><i class="bi bi-journal-plus"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $total_pilihan ?></div>
                            <div class="stat-label">Mata Kuliah Pilihan</div>
                        </div>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-actions">
                        <form method="GET" class="search-form">
                            <div class="search-input-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search" class="search-input" placeholder="Cari berdasarkan nama mata kuliah atau kode..." value="<?= h($search) ?>">
                            </div>
                            <button type="submit" class="btn-search">Cari</button>
                            <?php if ($search): ?>
                                <a href="<?= APP_URL ?>/admin/matkul/index.php" class="btn-ghost">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <?php if (count($matkul) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 48px; text-align: center;">NO</th>
                                        <th style="width: 120px;">KODE</th>
                                        <th>NAMA MATA KULIAH</th>
                                        <th style="width: 100px; text-align: center;">SKS</th>
                                        <th style="width: 100px; text-align: center;">SEMESTER</th>
                                        <th style="width: 140px;">JENIS</th>
                                        <th style="width: 120px; text-align: center;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($matkul as $idx => $m): ?>
                                        <tr>
                                            <td style="text-align: center; font-weight: 700; color: #9CA3AF;"><?= ($offset + $idx + 1) ?></td>
                                            <td><span class="matkul-kode"><?= h($m['kode_matkul']) ?></span></td>
                                            <td><span class="matkul-nama"><?= h($m['nama_matkul']) ?></span></td>
                                            <td style="text-align: center; font-weight: 700; color: #111827;"><?= $m['sks'] ?></td>
                                            <td style="text-align: center; font-weight: 600; color: #4B5563;"><?= $m['semester'] ?></td>
                                            <td><span class="badge-jenis <?= $m['jenis'] == 'wajib' ? 'wajib' : 'pilihan' ?>"><?= h($m['jenis']) ?></span></td>
                                            <td>
                                                <div class="action-flex" style="justify-content: center;">
                                                    <a href="<?= APP_URL ?>/admin/matkul/edit.php?id=<?= $m['id_matkul'] ?>" class="btn-icon" title="Edit Mata Kuliah">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="<?= APP_URL ?>/admin/matkul/hapus.php?id=<?= $m['id_matkul'] ?>" class="btn-icon danger" title="Hapus Mata Kuliah" onclick="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?');">
                                                        <i class="bi bi-trash3"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-book-half"></i>
                                <p>Tidak ada data Mata Kuliah yang ditemukan.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=1<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link"><i class="bi bi-chevron-double-left"></i></a>
                                <a href="?page=<?= $page - 1 ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link"><i class="bi bi-chevron-left"></i></a>
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
                                <a href="?page=<?= $page + 1 ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link"><i class="bi bi-chevron-right"></i></a>
                                <a href="?page=<?= $total_pages ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link"><i class="bi bi-chevron-double-right"></i></a>
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
