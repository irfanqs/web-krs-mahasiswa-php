<?php
/**
 * Mahasiswa KRS Enrollment Page
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

// Get previous semester IPK for SKS limit
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
if ($nilai_data && $nilai_data['total_sks'] > 0) {
    $ipk = round($nilai_data['total_bobot'] / $nilai_data['total_sks'], 2);
}

// Calculate max SKS from previous IPK, but ensure it's at least the current selected SKS
$calculated_max_sks = get_max_sks($ipk);
$max_sks = max($calculated_max_sks, $current_sks);

// Get current active semester
$stmt = $pdo->prepare("SELECT * FROM semester WHERE status = 'aktif' LIMIT 1");
$stmt->execute();
$semester_aktif = $stmt->fetch();
$id_semester = $semester_aktif['id_semester'] ?? 0;

// Get all available jadwal_kuliah for this semester
$stmt = $pdo->prepare("
    SELECT
        jk.id_jadwal,
        mk.id_matkul,
        mk.kode_matkul,
        mk.nama_matkul,
        mk.sks,
        mk.jenis,
        mk.semester,
        jk.hari,
        jk.jam_mulai,
        jk.jam_selesai,
        jk.ruang,
        d.nama as nama_dosen,
        jk.kuota,
        (SELECT COUNT(*) FROM krs WHERE id_jadwal = jk.id_jadwal) as sks_terdaftar
    FROM jadwal_kuliah jk
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    JOIN dosen d ON jk.id_dosen = d.nidn
    WHERE jk.id_semester = ?
    ORDER BY mk.semester ASC, mk.nama_matkul ASC
");
$stmt->execute([$id_semester]);
$available_courses = $stmt->fetchAll();

// Get mahasiswa's current KRS selections
$stmt = $pdo->prepare("
    SELECT DISTINCT jk.id_jadwal
    FROM krs k
    JOIN jadwal_kuliah jk ON k.id_jadwal = jk.id_jadwal
    WHERE k.id_mahasiswa = ? AND jk.id_semester = ?
");
$stmt->execute([$nim, $id_semester]);
$selected_jadwal = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
$selected_jadwal_map = array_flip($selected_jadwal);

// Calculate current SKS
$current_sks = 0;
$mandatory_sks = 0;
$elective_sks = 0;

foreach ($available_courses as $course) {
    if (isset($selected_jadwal_map[$course['id_jadwal']])) {
        $current_sks += $course['sks'];
        if ($course['jenis'] === 'wajib') {
            $mandatory_sks += $course['sks'];
        } else {
            $elective_sks += $course['sks'];
        }
    }
}

$is_krs_locked = $current_sks > 0 && !isset($_GET['edit']);

$page_title = 'Pengisian KRS';
$current_page = 'krs';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?> - SIAKAD Gallery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .page-header {
            margin-bottom: var(--spacing-2xl);
        }

        @media print {
            .sidebar, .topbar, .sticky-footer, .alert, .btn { display: none !important; }
            .page-layout { margin-left: 0 !important; }
            .page-content { padding: 0 !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #E5E7EB; }
            .card-navy { background: white !important; color: black !important; }
            .card-navy .card-title, .card-navy .card-value { color: black !important; }
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-900);
            margin-bottom: var(--spacing-sm);
        }

        .page-subtitle {
            color: var(--text-500);
            font-size: 14px;
        }

        .alert {
            padding: var(--spacing-lg);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-xl);
            background: var(--info-bg);
            border-left: 4px solid var(--warning);
            color: #856404;
            font-size: 14px;
        }

        .alert i {
            margin-right: var(--spacing-md);
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }

        .card {
            background: white;
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-md);
        }

        .card-navy {
            background: linear-gradient(135deg, var(--navy-900) 0%, var(--navy-700) 100%);
            color: white;
        }

        .card-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-500);
            margin-bottom: var(--spacing-md);
        }

        .card-navy .card-title {
            color: rgba(255, 255, 255, 0.8);
        }

        .card-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
        }

        .card-navy .card-value {
            color: white;
        }

        .card-subtitle {
            font-size: 13px;
            opacity: 0.8;
        }

        .distribution-bar {
            display: flex;
            gap: 2px;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: var(--spacing-md);
        }

        .distribution-item {
            height: 100%;
            border-radius: 4px;
        }

        .distribution-info {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--text-700);
        }

        .table-wrapper {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: var(--spacing-2xl);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table thead {
            background: var(--bg);
        }

        .table th {
            padding: var(--spacing-md) var(--spacing-lg);
            text-align: left;
            font-weight: 600;
            color: var(--text-900);
            border-bottom: 2px solid var(--border);
        }

        .table td {
            padding: var(--spacing-md) var(--spacing-lg);
            border-bottom: 1px solid var(--border);
        }

        .table tr:hover {
            background: var(--bg);
        }

        .table-checkbox {
            width: 20px;
            height: 20px;
            accent-color: var(--navy-900);
            cursor: pointer;
        }

        .course-row.selected {
            border-left: 4px solid var(--navy-900);
            background: rgba(42, 74, 158, 0.02);
        }

        .course-name {
            font-weight: 600;
            color: var(--text-900);
        }

        .course-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: var(--spacing-sm);
        }

        .badge-wajib {
            background: #E3F2FD;
            color: #1976D2;
        }

        .badge-pilihan {
            background: #F3E5F5;
            color: #7B1FA2;
        }

        .schedule-info {
            font-size: 13px;
            color: var(--text-500);
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }

        .sticky-footer {
            position: sticky;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            border-top: 1px solid var(--border);
            padding: var(--spacing-lg) 48px;
            box-shadow: 0 -2px 10px rgba(11, 30, 79, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--spacing-xl);
            z-index: 50;
        }

        .footer-progress {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
            flex: 1;
        }

        .progress-text {
            font-size: 13px;
            color: var(--text-700);
            font-weight: 600;
        }

        .footer-actions {
            display: flex;
            gap: var(--spacing-lg);
        }

        .btn {
            padding: var(--spacing-md) var(--spacing-lg);
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-size: 14px;
        }

        .btn-secondary {
            background: var(--border);
            color: var(--text-900);
        }

        .btn-secondary:hover {
            background: #D0D3DC;
        }

        .btn-primary {
            background: var(--navy-900);
            color: white;
        }

        .btn-primary:hover {
            background: var(--navy-700);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-primary:disabled {
            background: #CCCCCC;
            cursor: not-allowed;
            transform: none;
        }

        .text-center {
            text-align: center;
        }

        .text-danger {
            color: var(--danger);
        }

        @media (max-width: 1280px) {
            .grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sticky-footer {
                flex-direction: column;
                align-items: stretch;
                padding: var(--spacing-lg) 24px;
            }

            .grid-4 {
                grid-template-columns: 1fr;
            }

            .footer-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="page-layout">
        <?php include '../../includes/sidebar.php'; ?>

        <div class="page-main">
            <?php include '../../includes/header.php'; ?>

            <main class="page-content">
                <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h1 class="page-title">Pengisian KRS</h1>
                        <p class="page-subtitle">
                            Semester <?= h($semester_aktif['tingkatan_semester'] === 'ganjil' ? 'Ganjil' : 'Genap') ?>
                            <?= h($semester_aktif['tahun_ajaran'] ?? '2024/2025') ?>
                        </p>
                    </div>
                    <?php if ($is_krs_locked): ?>
                        <div style="display: flex; gap: 12px;" class="header-actions">
                            <a href="?edit=1" class="btn btn-secondary"><i class="bi bi-pencil"></i> Revisi KRS</a>
                            <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Cetak KRS</button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Validation Alert -->
                <?php if (!$is_krs_locked): ?>
                <div class="alert">
                    <i class="bi bi-info-circle"></i>
                    <strong>Sistem Validasi:</strong> Anda sedang dalam mode pengisian. Batas maksimal pengambilan Anda berdasarkan IPK semester lalu adalah <strong><?= $max_sks ?> SKS</strong>.
                </div>
                <?php else: ?>
                <div class="alert" style="background: #E8F5E9; border-left: 4px solid #4CAF50; color: #2E7D32;">
                    <i class="bi bi-check-circle-fill" style="margin-right: 12px; font-size: 18px;"></i>
                    <strong>KRS Disetujui:</strong> Anda telah mengambil <strong><?= $current_sks ?> SKS</strong> pada semester ini. Anda dapat mencetak form ini sebagai bukti rencana studi Anda.
                </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="grid-4">
                    <div class="card card-navy">
                        <div class="card-title">SKS Dipilih</div>
                        <div class="card-value"><span id="sks-counter"><?= $current_sks ?></span> <span style="font-size: 16px; font-weight: normal; opacity: 0.8;">/ <?= $max_sks ?></span></div>
                        <div class="card-subtitle">Selection progress</div>
                    </div>
                    <div class="card card-navy">
                        <div class="card-title">Total Kursus Dipilih</div>
                        <div class="card-value" id="course-counter">0</div>
                        <div class="card-subtitle">Total selected courses</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Wajib vs Pilihan</div>
                        <div class="distribution-bar">
                            <div class="distribution-item" style="background: var(--navy-900); width: <?= ($mandatory_sks / max(1, $current_sks)) * 100 ?>%;"></div>
                            <div class="distribution-item" style="background: var(--warning); width: <?= ($elective_sks / max(1, $current_sks)) * 100 ?>%;"></div>
                        </div>
                        <div class="distribution-info">
                            <span>Wajib: <?= $mandatory_sks ?> SKS</span>
                            <span>Pilihan: <?= $elective_sks ?> SKS</span>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-title">Kursus Tersedia</div>
                        <div class="card-value"><?= count($available_courses) ?></div>
                        <div class="card-subtitle">Total courses offered</div>
                    </div>
                </div>

                <!-- Course Table -->
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Mata Kuliah</th>
                                <th style="text-align: center;">SKS</th>
                                <th>Jadwal & Dosen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Daftar KRS Yang Telah Diambil (Terpilih) -->
                            <?php 
                            $no = 1; 
                            $has_selected = false;
                            foreach ($available_courses as $course):
                                $is_selected = isset($selected_jadwal_map[$course['id_jadwal']]);
                                if (!$is_selected) continue;
                                $has_selected = true;
                                $is_full = $course['sks_terdaftar'] >= $course['kuota'];
                            ?>
                                <tr class="course-row selected" data-jadwal-id="<?= $course['id_jadwal'] ?>" data-sks="<?= $course['sks'] ?>" data-jenis="<?= $course['jenis'] ?>">
                                    <td style="text-align: center;">
                                        <?php if ($is_krs_locked): ?>
                                            <i class="bi bi-check-circle-fill" style="color: #10B981; font-size: 18px;"></i>
                                        <?php else: ?>
                                            <input type="checkbox" class="table-checkbox course-checkbox"
                                                   value="<?= $course['id_jadwal'] ?>" checked>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= h($course['kode_matkul']) ?></strong></td>
                                    <td>
                                        <div class="course-name">
                                            <?= h($course['nama_matkul']) ?>
                                            <span class="course-badge badge-<?= $course['jenis'] ?>">
                                                <?= ucfirst($course['jenis']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td style="text-align: center;"><strong><?= $course['sks'] ?></strong></td>
                                    <td>
                                        <div class="schedule-info">
                                            <div class="schedule-item">
                                                <i class="bi bi-calendar3"></i>
                                                <?= h($course['hari']) ?> <?= substr($course['jam_mulai'], 0, 5) ?>-<?= substr($course['jam_selesai'], 0, 5) ?>
                                            </div>
                                            <div class="schedule-item">
                                                <i class="bi bi-geo-alt"></i>
                                                <?= h($course['ruang']) ?>
                                            </div>
                                            <div class="schedule-item">
                                                <i class="bi bi-person"></i>
                                                <?= h($course['nama_dosen']) ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (!$is_krs_locked && $has_selected): ?>
                                <tr style="background: #F3F4F6;">
                                    <td colspan="6" style="padding: 16px 24px; font-weight: 700; color: var(--navy-900); font-size: 12px; letter-spacing: 1px; text-transform: uppercase;">
                                        MATA KULIAH TERSEDIA (BELUM DIAMBIL)
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <!-- Daftar Mata Kuliah Tersedia (Belum Terpilih) -->
                            <?php if (!$is_krs_locked): ?>
                            <?php foreach ($available_courses as $course):
                                $is_selected = isset($selected_jadwal_map[$course['id_jadwal']]);
                                if ($is_selected) continue;
                                $is_full = $course['sks_terdaftar'] >= $course['kuota'];
                            ?>
                                <tr class="course-row" data-jadwal-id="<?= $course['id_jadwal'] ?>" data-sks="<?= $course['sks'] ?>" data-jenis="<?= $course['jenis'] ?>">
                                    <td>
                                        <input type="checkbox" class="table-checkbox course-checkbox"
                                               value="<?= $course['id_jadwal'] ?>"
                                               <?= $is_full ? 'disabled' : '' ?>>
                                    </td>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= h($course['kode_matkul']) ?></strong></td>
                                    <td>
                                        <div class="course-name">
                                            <?= h($course['nama_matkul']) ?>
                                            <span class="course-badge badge-<?= $course['jenis'] ?>">
                                                <?= ucfirst($course['jenis']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td style="text-align: center;"><strong><?= $course['sks'] ?></strong></td>
                                    <td>
                                        <div class="schedule-info">
                                            <div class="schedule-item">
                                                <i class="bi bi-calendar3"></i>
                                                <?= h($course['hari']) ?> <?= substr($course['jam_mulai'], 0, 5) ?>-<?= substr($course['jam_selesai'], 0, 5) ?>
                                            </div>
                                            <div class="schedule-item">
                                                <i class="bi bi-geo-alt"></i>
                                                <?= h($course['ruang']) ?>
                                            </div>
                                            <div class="schedule-item">
                                                <i class="bi bi-person"></i>
                                                <?= h($course['nama_dosen']) ?>
                                            </div>
                                            <?php if ($is_full): ?>
                                                <div class="schedule-item text-danger">
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                    Penuh
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
            <?php if (!$is_krs_locked): ?>
            <!-- Sticky Footer -->
            <div class="sticky-footer">
                <div class="footer-progress">
                    <span class="progress-text">
                        IPK SEMESTER LALU: <strong><?= number_format($ipk, 2) ?></strong>
                    </span>
                    <span class="progress-text">
                        BATAS SKS: <strong><?= $max_sks ?></strong> Maks SKS
                    </span>
                </div>
                <div class="footer-actions">
                    <button class="btn btn-secondary" id="btn-reset">
                        <i class="bi bi-arrow-clockwise"></i>
                        Reset Selection
                    </button>
                    <button class="btn btn-primary" id="btn-save">
                        <i class="bi bi-check-circle"></i>
                        Simpan KRS
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script>
        const maxSKS = <?= $max_sks ?>;
        const courseCheckboxes = document.querySelectorAll('.course-checkbox');
        const sksCounter = document.getElementById('sks-counter');
        const courseCounter = document.getElementById('course-counter');
        const btnSave = document.getElementById('btn-save');
        const btnReset = document.getElementById('btn-reset');

        function updateCounters() {
            let totalSKS = 0;
            let totalCourses = 0;

            courseCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const row = checkbox.closest('.course-row');
                    totalSKS += parseInt(row.dataset.sks);
                    totalCourses++;
                }
            });

            sksCounter.textContent = totalSKS;
            courseCounter.textContent = totalCourses;

            // Disable checkboxes if max SKS reached
            courseCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('.course-row');
                const courseSKS = parseInt(row.dataset.sks);

                if (!checkbox.checked && (totalSKS + courseSKS > maxSKS)) {
                    checkbox.disabled = true;
                } else if (checkbox.checked || totalSKS + courseSKS <= maxSKS) {
                    const courseRow = checkbox.closest('.course-row');
                    const isFull = courseRow.querySelector('.schedule-item.text-danger') !== null;
                    checkbox.disabled = isFull && !checkbox.checked;
                }
            });

            // Update row styling
            courseCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('.course-row');
                if (checkbox.checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            });
        }

        courseCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCounters);
        });

        btnReset.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin mereset seluruh pilihan?')) {
                courseCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateCounters();
            }
        });

        btnSave.addEventListener('click', function() {
            const selectedJadwal = Array.from(courseCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (selectedJadwal.length === 0) {
                alert('Silakan pilih minimal satu mata kuliah');
                return;
            }

            // Send to server
            btnSave.disabled = true;
            btnSave.textContent = 'Menyimpan...';

            fetch(window.APP_URL + '/api/krs_save.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    jadwal_ids: selectedJadwal
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    alert('KRS berhasil disimpan!');
                    location.reload();
                } else {
                    alert('Gagal menyimpan KRS: ' + (data.errors ? data.errors.join(', ') : 'Unknown error'));
                    btnSave.disabled = false;
                    btnSave.textContent = 'Simpan KRS';
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
                btnSave.disabled = false;
                btnSave.textContent = 'Simpan KRS';
            });
        });

        // Initialize counters
        updateCounters();
    </script>
</body>
</html>
