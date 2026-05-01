<?php
/**
 * Admin - Tambah Mahasiswa
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/helpers.php';

require_role(['admin']);

$pdo = get_pdo();
$page_title = 'Tambah Mahasiswa Baru';
$current_page = 'mahasiswa';

$errors = [];
$form_data = [
    'nim' => '',
    'nama' => '',
    'email' => '',
    'angkatan' => date('Y'),
    'program_studi' => '',
    'status' => 'aktif'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (empty($_POST['_csrf']) || !csrf_verify($_POST['_csrf'])) {
        $errors[] = 'CSRF token tidak valid';
    }

    // Get form data
    $form_data['nim'] = trim($_POST['nim'] ?? '');
    $form_data['nama'] = trim($_POST['nama'] ?? '');
    $form_data['email'] = trim($_POST['email'] ?? '');
    $form_data['angkatan'] = (int)($_POST['angkatan'] ?? date('Y'));
    $form_data['program_studi'] = trim($_POST['program_studi'] ?? '');
    $form_data['status'] = $_POST['status'] ?? 'aktif';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validate
    if (empty($form_data['nim'])) {
        $errors[] = 'NIM harus diisi';
    } elseif (strlen($form_data['nim']) > 15) {
        $errors[] = 'NIM maksimal 15 karakter';
    }

    if (empty($form_data['nama'])) {
        $errors[] = 'Nama harus diisi';
    } elseif (strlen($form_data['nama']) > 100) {
        $errors[] = 'Nama maksimal 100 karakter';
    }

    if (empty($form_data['email'])) {
        $errors[] = 'Email harus diisi';
    } elseif (!is_valid_email($form_data['email'])) {
        $errors[] = 'Format email tidak valid';
    }

    if (empty($form_data['program_studi'])) {
        $errors[] = 'Program Studi harus diisi';
    }

    if (empty($password)) {
        $errors[] = 'Password harus diisi';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Konfirmasi password tidak sesuai';
    }

    // Check duplicate NIM
    if (!empty($form_data['nim'])) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE nim = ?");
        $stmt->execute([$form_data['nim']]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'NIM sudah terdaftar';
        }
    }

    // Check duplicate Email
    if (!empty($form_data['email'])) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE email = ?");
        $stmt->execute([$form_data['email']]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Email sudah terdaftar';
        }
    }

    // If no errors, insert
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO mahasiswa (nim, nama, email, password, angkatan, program_studi, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $form_data['nim'],
                $form_data['nama'],
                $form_data['email'],
                $hashed_password,
                $form_data['angkatan'],
                $form_data['program_studi'],
                $form_data['status']
            ]);

            flash('success', 'Mahasiswa berhasil ditambahkan');
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

        .form-control.error {
            border-color: #E04F5F;
            background: #FFEBEE;
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

        .form-error {
            color: #E04F5F;
            font-size: 12px;
            margin-top: 4px;
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
                            <label class="form-label">NIM <span style="color: #E04F5F;">*</span></label>
                            <input type="text" name="nim" class="form-control" value="<?= h($form_data['nim']) ?>" maxlength="15" required>
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
                                <label class="form-label">Password <span style="color: #E04F5F;">*</span></label>
                                <input type="password" name="password" class="form-control" minlength="6" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password <span style="color: #E04F5F;">*</span></label>
                                <input type="password" name="password_confirm" class="form-control" minlength="6" required>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Simpan
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
