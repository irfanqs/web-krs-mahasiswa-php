<?php
/**
 * Admin - Hapus Mahasiswa (Confirmation)
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/helpers.php';

require_role(['admin']);

$pdo = get_pdo();
$page_title = 'Hapus Mahasiswa';
$current_page = 'mahasiswa';

$nim = isset($_GET['nim']) ? trim($_GET['nim']) : '';

if (!$nim) {
    flash('error', 'NIM tidak ditemukan');
    redirect(APP_URL . '/admin/mahasiswa/index.php');
    exit;
}

// Get mahasiswa data
$stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE nim = ?");
$stmt->execute([$nim]);
$mahasiswa = $stmt->fetch();

if (!$mahasiswa) {
    flash('error', 'Mahasiswa tidak ditemukan');
    redirect(APP_URL . '/admin/mahasiswa/index.php');
    exit;
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (empty($_POST['_csrf']) || !csrf_verify($_POST['_csrf'])) {
        $errors[] = 'CSRF token tidak valid';
    } else {
        try {
            // Delete mahasiswa (cascade will delete related KRS and Nilai)
            $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE nim = ?");
            $stmt->execute([$nim]);

            flash('success', 'Mahasiswa berhasil dihapus');
            redirect(APP_URL . '/admin/mahasiswa/index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Error: ' . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?> - SIAKAD Gallery</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .delete-container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(11, 30, 79, 0.06);
            max-width: 500px;
            text-align: center;
        }

        .delete-icon {
            font-size: 64px;
            color: #E04F5F;
            margin-bottom: 20px;
            display: block;
        }

        .delete-title {
            font-size: 24px;
            font-weight: 700;
            color: #0B1E4F;
            margin-bottom: 12px;
        }

        .delete-message {
            color: #6B7489;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .delete-details {
            background: #FFEBEE;
            border: 1px solid #EF9A9A;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: left;
        }

        .delete-detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .delete-detail-item .label {
            color: #6B7489;
            font-weight: 500;
        }

        .delete-detail-item .value {
            color: #0B1E4F;
            font-weight: 600;
        }

        .delete-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-danger {
            padding: 10px 24px;
            background: #E04F5F;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-danger:hover {
            background: #C62828;
            box-shadow: 0 4px 12px rgba(224, 79, 95, 0.3);
        }

        .btn-secondary {
            padding: 10px 24px;
            border-radius: 8px;
            border: 1px solid #E4E7EE;
            background: white;
            color: #2C3A59;
            text-decoration: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #F5F6FA;
            border-color: #2A4A9E;
            color: #2A4A9E;
        }

        .breadcrumb-nav {
            margin-bottom: 24px;
        }

        .breadcrumb-nav a {
            color: #2A4A9E;
            text-decoration: none;
            font-size: 13px;
        }

        .breadcrumb-nav span {
            color: #6B7489;
            margin: 0 6px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include '../../../includes/header.php'; ?>

        <div class="main-content">
            <?php include '../../../includes/sidebar.php'; ?>

            <div class="content-area">
                <!-- Breadcrumb -->
                <div class="breadcrumb-nav">
                    <a href="<?= APP_URL ?>/admin/dashboard.php">Dashboard</a>
                    <span>›</span>
                    <a href="<?= APP_URL ?>/admin/mahasiswa/index.php">Mahasiswa</a>
                    <span>›</span>
                    <span><?= h($page_title) ?></span>
                </div>

                <!-- Delete Confirmation -->
                <div class="delete-container">
                    <i class="bi bi-exclamation-triangle delete-icon"></i>
                    <div class="delete-title">Hapus Mahasiswa?</div>
                    <p class="delete-message">
                        Anda akan menghapus data mahasiswa ini secara permanen. Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="delete-details">
                        <div class="delete-detail-item">
                            <span class="label">NIM</span>
                            <span class="value"><?= h($mahasiswa['nim']) ?></span>
                        </div>
                        <div class="delete-detail-item">
                            <span class="label">Nama</span>
                            <span class="value"><?= h($mahasiswa['nama']) ?></span>
                        </div>
                        <div class="delete-detail-item">
                            <span class="label">Program Studi</span>
                            <span class="value"><?= h($mahasiswa['program_studi']) ?></span>
                        </div>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                        <div class="delete-actions">
                            <button type="submit" class="btn-danger">
                                <i class="bi bi-trash"></i> Ya, Hapus Permanen
                            </button>
                            <a href="<?= APP_URL ?>/admin/mahasiswa/index.php" class="btn-secondary">
                                <i class="bi bi-x-circle"></i> Batalkan
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../../includes/footer.php'; ?>
</body>
</html>
