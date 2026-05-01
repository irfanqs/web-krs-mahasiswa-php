<?php
/**
 * Admin - Edit Mahasiswa
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/helpers.php';

require_role(['admin']);

$pdo = get_pdo();
$page_title = 'Edit Mahasiswa';
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

$errors = [];
$form_data = [
    'nama' => $mahasiswa['nama'],
    'email' => $mahasiswa['email'],
    'angkatan' => $mahasiswa['angkatan'],
    'program_studi' => $mahasiswa['program_studi'],
    'status' => $mahasiswa['status']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (empty($_POST['_csrf']) || !csrf_verify($_POST['_csrf'])) {
        $errors[] = 'CSRF token tidak valid';
    }

    // Get form data
    $form_data['nama'] = trim($_POST['nama'] ?? '');
    $form_data['email'] = trim($_POST['email'] ?? '');
    $form_data['angkatan'] = (int)($_POST['angkatan'] ?? date('Y'));
    $form_data['program_studi'] = trim($_POST['program_studi'] ?? '');
    $form_data['status'] = $_POST['status'] ?? 'aktif';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validate
    if (empty($form_data['nama'])) {
        $errors[] = 'Nama harus diisi';
    } elseif (strlen($form_data['nama']) > 100) {
        $errors[] = 'Nama maksimal 100 karakter';
    }

    if (empty($form_data['email'])) {
        $errors[] = 'Email harus diisi';
    } elseif (!is_valid_email($form_data['email'])) {
        $errors[] = 'Format email tidak valid';
    } elseif ($form_data['email'] !== $mahasiswa['email']) {
        // Check duplicate email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE email = ? AND nim != ?");
        $stmt->execute([$form_data['email'], $nim]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Email sudah terdaftar';
        }
    }

    if (empty($form_data['program_studi'])) {
        $errors[] = 'Program Studi harus diisi';
    }

    // Password validation (optional)
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }
        if ($password !== $password_confirm) {
            $errors[] = 'Konfirmasi password tidak sesuai';
        }
    }

    // If no errors, update
    if (empty($errors)) {
        try {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    UPDATE mahasiswa
                    SET nama = ?, email = ?, angkatan = ?, program_studi = ?, status = ?, password = ?
                    WHERE nim = ?
                ");
                $stmt->execute([
                    $form_data['nama'],
                    $form_data['email'],
                    $form_data['angkatan'],
                    $form_data['program_studi'],
                    $form_data['status'],
                    $hashed_password,
                    $nim
                ]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE mahasiswa
                    SET nama = ?, email = ?, angkatan = ?, program_studi = ?, status = ?
                    WHERE nim = ?
                ");
                $stmt->execute([
                    $form_data['nama'],
                    $form_data['email'],
                    $form_data['angkatan'],
                    $form_data['program_studi'],
                    $form_data['status'],
                    $nim
                ]);
            }

            flash('success', 'Mahasiswa berhasil diperbarui');
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
        .form-container {
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 2px 10px rgba(11, 30, 79, 0.06);
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #0B1E4F;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #E4E7EE;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #2A4A9E;
            background: #F0F4FF;
            box-shadow: 0 0 0 3px rgba(42, 74, 158, 0.1);
        }

        .form-control:disabled {
            background: #F5F6FA;
            color: #6B7489;
            cursor: not-allowed;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #E4E7EE;
        }

        .btn-secondary {
            padding: 10px 20px;
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

        .alert-danger {
            background: #FFEBEE;
            border: 1px solid #EF9A9A;
            color: #C62828;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-danger li {
            margin-bottom: 4px;
        }

        .form-hint {
            font-size: 12px;
            color: #6B7489;
            margin-top: 4px;
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

                <!-- Header -->
                <div style="margin-bottom: 24px;">
                    <h1><?= h($page_title) ?></h1>
                    <p class="text-muted"><?= h($mahasiswa['nama']) ?> (<?= h($mahasiswa['nim']) ?>)</p>
                </div>

                <!-- Form -->
                <div class="form-container">
                    <?php if (!empty($errors)): ?>
                        <div class="alert-danger">
                            <ul style="margin: 0; padding-left: 20px;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= h($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" novalidate>
                        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">

                        <div class="form-group">
                            <label class="form-label">NIM</label>
                            <input type="text" class="form-control" value="<?= h($mahasiswa['nim']) ?>" disabled>
                            <div class="form-hint">NIM tidak dapat diubah</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span style="color: #E04F5F;">*</span></label>
                            <input type="text" name="nama" class="form-control" value="<?= h($form_data['nama']) ?>" maxlength="100" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email <span style="color: #E04F5F;">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= h($form_data['email']) ?>" maxlength="100" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Angkatan <span style="color: #E04F5F;">*</span></label>
                                <input type="number" name="angkatan" class="form-control" value="<?= $form_data['angkatan'] ?>" min="2000" max="2099" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status <span style="color: #E04F5F;">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="aktif" <?= $form_data['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="cuti" <?= $form_data['status'] === 'cuti' ? 'selected' : '' ?>>Cuti</option>
                                    <option value="lulus" <?= $form_data['status'] === 'lulus' ? 'selected' : '' ?>>Lulus</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Program Studi <span style="color: #E04F5F;">*</span></label>
                            <input type="text" name="program_studi" class="form-control" value="<?= h($form_data['program_studi']) ?>" maxlength="60" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control" minlength="6">
                                <div class="form-hint">Biarkan kosong jika tidak ingin mengubah password</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirm" class="form-control" minlength="6">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Simpan Perubahan
                            </button>
                            <a href="<?= APP_URL ?>/admin/mahasiswa/index.php" class="btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
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
