<?php 
require_once '../../../includes/config.php'; 
require_once '../../../includes/db.php'; 
require_once '../../../includes/auth.php'; 
require_once '../../../includes/helpers.php'; 

require_role(['admin']); 
$pdo = get_pdo(); 
$page_title = 'Tambah Semester'; 
$current_page = 'semester'; // for active sidebar

$errors = []; 
$form_data = [
    'tahun_ajaran' => date('Y') . '/' . (date('Y') + 1), 
    'tingkatan_semester' => 'ganjil', 
    'status' => 'nonaktif'
]; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    if (empty($_POST['_csrf']) || !csrf_verify($_POST['_csrf'])) { 
        $errors[] = 'CSRF token tidak valid'; 
    } 
    $form_data['tahun_ajaran'] = trim($_POST['tahun_ajaran'] ?? ''); 
    $form_data['tingkatan_semester'] = $_POST['tingkatan_semester'] ?? 'ganjil'; 
    $form_data['status'] = $_POST['status'] ?? 'nonaktif'; 
    
    if (empty($form_data['tahun_ajaran'])) { 
        $errors[] = 'Tahun ajaran harus diisi'; 
    } 
    if (!preg_match('/^\d{4}\/\d{4}$/', $form_data['tahun_ajaran'])) { 
        $errors[] = 'Format tahun ajaran harus YYYY/YYYY (contoh: 2024/2025)'; 
    } 
    
    if (empty($errors)) { 
        try { 
            if ($form_data['status'] === 'aktif') { 
                $stmt = $pdo->prepare("UPDATE semester SET status = 'nonaktif' WHERE status = 'aktif'"); 
                $stmt->execute(); 
            } 
            $stmt = $pdo->prepare("INSERT INTO semester (tahun_ajaran, tingkatan_semester, status) VALUES (?, ?, ?)"); 
            $stmt->execute([$form_data['tahun_ajaran'], $form_data['tingkatan_semester'], $form_data['status']]); 
            flash('success', 'Semester berhasil ditambahkan'); 
            redirect(APP_URL . '/admin/semester/index.php'); 
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
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            color: #111827;
            background: #F9FAFB;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #1B3679;
            box-shadow: 0 0 0 3px rgba(27, 54, 121, 0.1);
            background: white;
        }

        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 40px;
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

        .btn-secondary {
            padding: 12px 24px;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            background: white;
            color: #4B5563;
            text-decoration: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-secondary:hover {
            background: #F3F4F6;
            color: #111827;
        }

        .alert-danger {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
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
                    <div class="breadcrumb">ADMINISTRATOR &gt; MANAJEMEN SEMESTER <span>&gt; TAMBAH</span></div>
                    <h1 class="page-title"><?= h($page_title) ?></h1>
                </div>

                <div class="form-card">
                    <?php if (!empty($errors)): ?>
                        <div class="alert-danger">
                            <ul style="margin: 0; padding-left: 20px;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= h($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                        
                        <div class="form-group">
                            <label class="form-label">Tahun Ajaran <span style="color: #DC2626;">*</span></label>
                            <input type="text" name="tahun_ajaran" class="form-control" value="<?= h($form_data['tahun_ajaran']) ?>" placeholder="2024/2025" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tingkatan <span style="color: #DC2626;">*</span></label>
                            <select name="tingkatan_semester" class="form-select" required>
                                <option value="ganjil" <?= $form_data['tingkatan_semester'] === 'ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                <option value="genap" <?= $form_data['tingkatan_semester'] === 'genap' ? 'selected' : '' ?>>Genap</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Status <span style="color: #DC2626;">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="nonaktif" <?= $form_data['status'] === 'nonaktif' ? 'selected' : '' ?>>Non-Aktif</option>
                                <option value="aktif" <?= $form_data['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary-custom"><i class="bi bi-save"></i> Simpan Data</button>
                            <a href="<?= APP_URL ?>/admin/semester/index.php" class="btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <?php include '../../../includes/footer.php'; ?>
</body>
</html>
