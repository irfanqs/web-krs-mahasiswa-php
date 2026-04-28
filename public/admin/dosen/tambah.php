<?php
require_once '../../../includes/config.php';
require_once '../../../includes/db.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/helpers.php';
require_role(['admin']);

$pdo = get_pdo();
$page_title = 'Tambah Dosen';
$current_page = 'dosen';

$errors = [];
$form_data = ['nidn' => '', 'nama' => '', 'email' => '', 'jurusan' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['_csrf']) || !csrf_verify($_POST['_csrf'])) {
        $errors[] = 'CSRF token tidak valid';
    }

    $form_data['nidn'] = trim($_POST['nidn'] ?? '');
    $form_data['nama'] = trim($_POST['nama'] ?? '');
    $form_data['email'] = trim($_POST['email'] ?? '');
    $form_data['jurusan'] = trim($_POST['jurusan'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($form_data['nidn'])) { $errors[] = 'NIDN harus diisi'; }
    if (empty($form_data['nama'])) { $errors[] = 'Nama harus diisi'; }
    if (empty($form_data['email']) || !is_valid_email($form_data['email'])) { $errors[] = 'Email tidak valid'; }
    if (empty($form_data['jurusan'])) { $errors[] = 'Jurusan harus diisi'; }
    if (empty($password) || strlen($password) < 6) { $errors[] = 'Password minimal 6 karakter'; }
    if ($password !== $password_confirm) { $errors[] = 'Konfirmasi password tidak sesuai'; }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM dosen WHERE nidn = ?");
            $stmt->execute([$form_data['nidn']]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'NIDN sudah terdaftar';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO dosen (nidn, nama, email, password, jurusan) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $form_data['nidn'],
                    $form_data['nama'],
                    $form_data['email'],
                    $hashed_password,
                    $form_data['jurusan']
                ]);
                flash('success', 'Dosen berhasil ditambahkan');
                redirect(APP_URL . '/admin/dosen/index.php');
                exit;
            }
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
            margin-bottom: 32px;
        }

        .breadcrumb {
            font-size: 11px;
            font-weight: 800;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        
        .breadcrumb a {
            color: #6B7280;
            text-decoration: none;
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

        .form-card {
            background: white;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            max-width: 600px;
        }

        .form-group { margin-bottom: 24px; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .form-label .required { color: #DC2626; }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            color: #111827;
            background: #F8FAFC;
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #1B3679;
            background: white;
            box-shadow: 0 0 0 3px rgba(27, 54, 121, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #F3F4F6;
        }

        .btn-submit {
            background: linear-gradient(135deg, #1B3679 0%, #25408E 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(27, 54, 121, 0.2); }

        .btn-cancel {
            background: white;
            color: #4B5563;
            padding: 12px 28px;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-cancel:hover { background: #F3F4F6; color: #111827; }

        .error-box {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .error-box ul {
            margin: 0;
            padding-left: 20px;
            color: #991B1B;
            font-size: 14px;
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
                    <div class="breadcrumb">
                        <a href="<?= APP_URL ?>/admin/dashboard.php">DASHBOARD</a> <span>&gt;</span>
                        <a href="<?= APP_URL ?>/admin/dosen/index.php">MANAJEMEN DOSEN</a> <span>&gt;</span>
                        TAMBAH DATA
                    </div>
                    <h1 class="page-title"><?= h($page_title) ?></h1>
                </div>

                <div class="form-card">
                    <?php if (!empty($errors)): ?>
                        <div class="error-box">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= h($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                        
                        <div class="form-group">
                            <label class="form-label">NIDN <span class="required">*</span></label>
                            <input type="text" name="nidn" class="form-input" value="<?= h($form_data['nidn']) ?>" maxlength="15" placeholder="Masukkan NIDN" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama" class="form-input" value="<?= h($form_data['nama']) ?>" maxlength="100" placeholder="Contoh: Dr. Budi Santoso, M.Kom" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-input" value="<?= h($form_data['email']) ?>" maxlength="100" placeholder="budi@kampus.ac.id" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Program Studi/Jurusan <span class="required">*</span></label>
                            <input type="text" name="jurusan" class="form-input" value="<?= h($form_data['jurusan']) ?>" maxlength="60" placeholder="Teknik Informatika" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Password <span class="required">*</span></label>
                                <input type="password" name="password" class="form-input" minlength="6" placeholder="Minimal 6 karakter" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Konfirmasi Password <span class="required">*</span></label>
                                <input type="password" name="password_confirm" class="form-input" minlength="6" placeholder="Ulangi password" required>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-person-plus-fill"></i> Simpan Data Dosen
                            </button>
                            <a href="<?= APP_URL ?>/admin/dosen/index.php" class="btn-cancel">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <?php include '../../../includes/footer.php'; ?>
</body>
</html>
