<?php
/**
 * Dosen - Input Nilai Mahasiswa
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

require_role(['dosen']);

$pdo = get_pdo();
$nidn = $_SESSION['user_id'];
$page_title = 'Input Nilai';
$current_page = 'input_nilai';

// Get id_jadwal from GET
$id_jadwal = isset($_GET['id_jadwal']) ? (int)$_GET['id_jadwal'] : 0;

if (!$id_jadwal) {
    flash('error', 'Kelas tidak ditemukan');
    redirect(APP_URL . '/dosen/dashboard.php');
    exit;
}

// Get jadwal info and verify ownership
$stmt = $pdo->prepare("
    SELECT jk.id_jadwal, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.ruang, jk.kuota,
           mk.id_matkul, mk.nama_matkul, mk.kode_matkul, mk.sks, jk.id_dosen
    FROM jadwal_kuliah jk
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    WHERE jk.id_jadwal = ?
");
$stmt->execute([$id_jadwal]);
$jadwal = $stmt->fetch();

if (!$jadwal || $jadwal['id_dosen'] !== $nidn) {
    flash('error', 'Anda tidak memiliki akses ke kelas ini');
    redirect(APP_URL . '/dosen/dashboard.php');
    exit;
}

// Get all students with nilai
$stmt = $pdo->prepare("
    SELECT k.id_krs, m.nim, m.nama, m.program_studi,
           n.id_nilai, n.tugas, n.uts, n.uas, n.nilai_angka, n.nilai_huruf, n.status_kunci
    FROM krs k
    JOIN mahasiswa m ON k.id_mahasiswa = m.nim
    LEFT JOIN nilai n ON k.id_krs = n.id_krs
    WHERE k.id_jadwal = ?
    ORDER BY m.nama
");
$stmt->execute([$id_jadwal]);
$students = $stmt->fetchAll();

$total_students = count($students);
$graded = 0;
$avg_score = 0;

foreach ($students as $student) {
    if (!empty($student['nilai_angka'])) {
        $graded++;
        $avg_score += $student['nilai_angka'];
    }
}

if ($graded > 0) {
    $avg_score = $avg_score / $graded;
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
            margin: 0 0 8px 0;
            line-height: 1.2;
        }

        .page-subtitle {
            color: #6B7280;
            font-size: 14px;
            margin: 0;
            font-weight: 500;
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
            align-items: center;
            gap: 20px;
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
        .stat-card.indigo .stat-icon { background: #E0E7FF; color: #3730A3; }

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

        /* Banner */
        .deadline-card {
            background: linear-gradient(135deg, #0F766E 0%, #115E59 100%);
            color: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: 0 10px 25px rgba(15, 118, 110, 0.15);
        }

        .deadline-label {
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .deadline-value {
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 4px 0;
        }

        .deadline-text {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
            color: rgba(255,255,255,0.9);
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
        }

        .search-input-wrapper {
            position: relative;
            max-width: 400px;
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
        }

        .search-input:focus {
            border-color: #1B3679;
            box-shadow: 0 0 0 3px rgba(27, 54, 121, 0.1);
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
            padding: 16px 20px;
            border-bottom: 2px solid #F3F4F6;
            background: #FBFCFD;
            text-align: center;
        }
        th:nth-child(2), th:nth-child(3) { text-align: left; }

        td {
            padding: 16px 20px;
            font-size: 14px;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: middle;
            text-align: center;
        }
        td:nth-child(2), td:nth-child(3) { text-align: left; }

        tr:last-child td { border-bottom: none; }
        tbody tr.nilai-row:hover { background-color: #F8FAFC; }

        .mhs-name {
            font-weight: 700;
            color: #111827;
            display: block;
        }
        .mhs-nim {
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
        }

        .nilai-input {
            width: 60px;
            padding: 8px 10px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            color: #111827;
            transition: all 0.2s;
        }

        .nilai-input:focus {
            outline: none;
            border-color: #1B3679;
            background: #EFF6FF;
        }

        .nilai-input:disabled {
            background: #F3F4F6;
            color: #9CA3AF;
            cursor: not-allowed;
            border-color: #F3F4F6;
        }

        .nilai-angka, .nilai-huruf {
            background: #F8FAFC;
            border-color: transparent;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-saved { background: #DCFCE7; color: #166534; }
        .status-unsaved { background: #FFEDD5; color: #C2410C; }
        .status-locked { background: #F3F4F6; color: #4B5563; }

        .floating-save {
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 1000;
        }

        .btn-save-large {
            padding: 16px 32px;
            border-radius: 16px;
            font-weight: 800;
            background: #1B3679;
            color: white;
            border: none;
            box-shadow: 0 10px 25px rgba(27, 54, 121, 0.25);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .btn-save-large:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(27, 54, 121, 0.35);
            background: #25408E;
        }

        .grading-guide {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            margin-bottom: 40px;
        }
        .grading-guide h6 { margin: 0 0 20px 0; font-size: 16px; font-weight: 800; color: #1B3679; }
        
        .guide-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        
        .guide-item {
            padding: 16px;
            background: #F8FAFC;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #4B5563;
        }

        .guide-item strong { color: #111827; }
        .guide-item span.grade { font-size: 18px; font-weight: 800; display: inline-block; width: 30px; }
        .guide-item.a span.grade { color: #10B981; }
        .guide-item.b span.grade { color: #3B82F6; }
        .guide-item.c span.grade { color: #F59E0B; }
        .guide-item.d span.grade { color: #EF4444; }

    </style>
</head>
<body>
    <div class="page-layout">
        <?php include '../../includes/sidebar.php'; ?>

        <div class="page-main">
            <?php include '../../includes/header.php'; ?>

            <main class="page-content">
                <div class="page-header">
                    <div class="page-header-left">
                        <div class="breadcrumb">DOSEN <span>&gt; DOSEN INPUT NILAI</span></div>
                        <h1 class="page-title">Input Penilaian Mahasiswa</h1>
                        <p class="page-subtitle"><?= h($jadwal['nama_matkul']) ?> (<?= h($jadwal['kode_matkul']) ?>)</p>
                    </div>
                </div>

                <?php if ($msg = flash('success')): ?>
                    <div style="background: #DCFCE7; color: #166534; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-check-circle" style="font-size: 20px;"></i> <?= h($msg) ?>
                    </div>
                <?php endif; ?>
                <?php if ($msg = flash('error')): ?>
                    <div style="background: #FEE2E2; color: #991B1B; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-exclamation-circle" style="font-size: 20px;"></i> <?= h($msg) ?>
                    </div>
                <?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-people"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $total_students ?></div>
                            <div class="stat-label">Total Mahasiswa</div>
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="bi bi-check2-square"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= $graded ?></div>
                            <div class="stat-label">Sudah Dinilai</div>
                        </div>
                    </div>
                    <div class="stat-card indigo">
                        <div class="stat-icon"><i class="bi bi-bar-chart"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?= number_format($avg_score, 2) ?></div>
                            <div class="stat-label">Rata-rata Kelas</div>
                        </div>
                    </div>
                </div>

                <div class="deadline-card">
                    <div class="deadline-label">Batas Waktu Pengumpulan</div>
                    <h2 class="deadline-value">H-14 Hari</h2>
                    <p class="deadline-text">Pastikan semua nilai mahasiswa tersimpan dengan benar sebelum sistem dikunci secara otomatis oleh Admin.</p>
                </div>

                <div class="table-card">
                    <div class="table-actions">
                        <div class="search-input-wrapper">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchInput" class="search-input" placeholder="Cari berdasarkan nama atau NIM mahasiswa..." onkeyup="filterTable()">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <form id="nilaiForm" method="POST" action="<?= APP_URL ?>/api/nilai_save.php">
                            <input type="hidden" name="id_jadwal" value="<?= $id_jadwal ?>">

                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 48px;">NO</th>
                                        <th style="width: 120px;">NIM</th>
                                        <th>MAHASISWA</th>
                                        <th>TUGAS<br><small style="font-weight: 600; color: #1B3679;">(20%)</small></th>
                                        <th>UTS<br><small style="font-weight: 600; color: #1B3679;">(30%)</small></th>
                                        <th>UAS<br><small style="font-weight: 600; color: #1B3679;">(50%)</small></th>
                                        <th>NILAI AKHIR</th>
                                        <th>HURUF</th>
                                        <th style="width: 120px;">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($students) > 0): ?>
                                        <?php foreach ($students as $idx => $student):
                                            $is_locked = $student['status_kunci'] == 1;
                                            $is_graded = !empty($student['nilai_huruf']);
                                        ?>
                                            <tr class="nilai-row" data-nim="<?= h($student['nim']) ?>" data-nama="<?= h($student['nama']) ?>">
                                                <td style="font-weight: 700; color: #6B7280;"><?= $idx + 1 ?></td>
                                                <td><span class="mhs-nim"><?= h($student['nim']) ?></span></td>
                                                <td><span class="mhs-name"><?= h($student['nama']) ?></span></td>
                                                <td>
                                                    <input type="hidden" class="id-krs" value="<?= $student['id_krs'] ?>">
                                                    <input type="number" class="nilai-input nilai-tugas" value="<?= !empty($student['tugas']) ? $student['tugas'] : '' ?>"
                                                           min="0" max="100" step="0.01" <?= $is_locked ? 'disabled' : '' ?> onchange="hitungNilai(this)">
                                                </td>
                                                <td>
                                                    <input type="number" class="nilai-input nilai-uts" value="<?= !empty($student['uts']) ? $student['uts'] : '' ?>"
                                                           min="0" max="100" step="0.01" <?= $is_locked ? 'disabled' : '' ?> onchange="hitungNilai(this)">
                                                </td>
                                                <td>
                                                    <input type="number" class="nilai-input nilai-uas" value="<?= !empty($student['uas']) ? $student['uas'] : '' ?>"
                                                           min="0" max="100" step="0.01" <?= $is_locked ? 'disabled' : '' ?> onchange="hitungNilai(this)">
                                                </td>
                                                <td>
                                                    <input type="text" class="nilai-input nilai-angka" value="<?= !empty($student['nilai_angka']) ? number_format($student['nilai_angka'], 2) : '' ?>" readonly tabindex="-1">
                                                </td>
                                                <td>
                                                    <input type="text" class="nilai-input nilai-huruf" value="<?= !empty($student['nilai_huruf']) ? $student['nilai_huruf'] : '' ?>" readonly tabindex="-1">
                                                </td>
                                                <td>
                                                    <?php if ($is_locked): ?>
                                                        <span class="status-badge status-locked">
                                                            <i class="bi bi-lock-fill"></i> Terkunci
                                                        </span>
                                                    <?php elseif ($is_graded): ?>
                                                        <span class="status-badge status-saved">
                                                            <i class="bi bi-check-circle-fill"></i> Tersimpan
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="status-badge status-unsaved">
                                                            <i class="bi bi-clock-history"></i> Belum
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" style="text-align: center; padding: 48px; color: #9CA3AF;">
                                                <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                                <p style="font-weight: 500; margin: 0;">Belum ada mahasiswa di kelas ini.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <div class="floating-save">
                                <button type="button" class="btn-save-large" onclick="submitNilai()">
                                    <i class="bi bi-cloud-arrow-up"></i> Simpan Penilaian
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="grading-guide">
                    <h6>Panduan Standar Penilaian Akademi</h6>
                    <div class="guide-grid">
                        <div class="guide-item a">
                            <span class="grade">A</span> <strong>≥ 85</strong> (Excellent, 4.0)
                        </div>
                        <div class="guide-item b">
                            <span class="grade">B</span> <strong>70 - 84</strong> (Good, 3.0 / 3.5)
                        </div>
                        <div class="guide-item c">
                            <span class="grade">C</span> <strong>60 - 69</strong> (Fair, 2.0 / 2.5)
                        </div>
                        <div class="guide-item d">
                            <span class="grade">E</span> <strong>< 60</strong> (Poor/Fail, 0.0)
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script>
        function hitungNilai(input) {
            const row = input.closest('tr');
            const tugas = parseFloat(row.querySelector('.nilai-tugas').value) || 0;
            const uts = parseFloat(row.querySelector('.nilai-uts').value) || 0;
            const uas = parseFloat(row.querySelector('.nilai-uas').value) || 0;

            // Formula: 0.2 * tugas + 0.3 * uts + 0.5 * uas
            const nilaiAngka = (0.2 * tugas) + (0.3 * uts) + (0.5 * uas);

            // Determine huruf
            let nilaiHuruf = 'E';
            if (nilaiAngka >= 85) {
                nilaiHuruf = 'A';
            } else if (nilaiAngka >= 70) {
                nilaiHuruf = 'B+';
            } else if (nilaiAngka >= 60) {
                nilaiHuruf = 'B';
            } else if (nilaiAngka >= 55) {
                nilaiHuruf = 'C+';
            } else if (nilaiAngka >= 50) {
                nilaiHuruf = 'C';
            } else if (nilaiAngka >= 40) {
                nilaiHuruf = 'D';
            }

            // Update display
            row.querySelector('.nilai-angka').value = nilaiAngka.toFixed(2);
            row.querySelector('.nilai-huruf').value = (tugas===0 && uts===0 && uas===0) ? '' : nilaiHuruf;

            // Update status
            updateStatus(row);
        }

        function updateStatus(row) {
            const nilaiAngka = row.querySelector('.nilai-angka').value;
            const statusCell = row.querySelector('td:last-child');

            if (nilaiAngka > 0) {
                statusCell.innerHTML = '<span class="status-badge status-saved"><i class="bi bi-check-circle-fill"></i> Tersimpan (Draft)</span>';
            } else {
                statusCell.innerHTML = '<span class="status-badge status-unsaved"><i class="bi bi-clock-history"></i> Belum</span>';
            }
        }

        function filterTable() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.nilai-row');

            rows.forEach(row => {
                const nim = row.getAttribute('data-nim').toLowerCase();
                const nama = row.getAttribute('data-nama').toLowerCase();

                if (nim.includes(input) || nama.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function submitNilai() {
            const form = document.getElementById('nilaiForm');
            const nilai_data = [];

            document.querySelectorAll('.nilai-row').forEach(row => {
                const idKrs = row.querySelector('.id-krs').value;
                const tugas = row.querySelector('.nilai-tugas').value;
                const uts = row.querySelector('.nilai-uts').value;
                const uas = row.querySelector('.nilai-uas').value;
                const nilaiAngka = row.querySelector('.nilai-angka').value;
                const nilaiHuruf = row.querySelector('.nilai-huruf').value;

                if (tugas !== '' || uts !== '' || uas !== '') {
                    nilai_data.push({
                        id_krs: idKrs,
                        tugas: tugas,
                        uts: uts,
                        uas: uas,
                        nilai_angka: nilaiAngka,
                        nilai_huruf: nilaiHuruf
                    });
                }
            });

            if (nilai_data.length === 0) {
                alert('Tidak ada data nilai yang diisi');
                return;
            }

            document.querySelector('.btn-save-large').innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            document.querySelector('.btn-save-large').disabled = true;

            // Send via AJAX
            fetch('<?= APP_URL ?>/api/nilai_save.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_jadwal: document.querySelector('input[name="id_jadwal"]').value,
                    nilai_data: nilai_data
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    alert('Nilai berhasil disimpan!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.errors ? data.errors[0] : 'Gagal menyimpan nilai'));
                    document.querySelector('.btn-save-large').innerHTML = '<i class="bi bi-cloud-arrow-up"></i> Simpan Penilaian';
                    document.querySelector('.btn-save-large').disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan nilai');
                document.querySelector('.btn-save-large').innerHTML = '<i class="bi bi-cloud-arrow-up"></i> Simpan Penilaian';
                document.querySelector('.btn-save-large').disabled = false;
            });
        }
    </script>
</body>
</html>
