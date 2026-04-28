<?php
/**
 * API Endpoint - Save KRS (Kartu Rencana Studi)
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

header('Content-Type: application/json');

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

// Check authentication and role
if (!is_logged_in() || $_SESSION['role'] !== 'mahasiswa') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'errors' => ['Unauthorized']]);
    exit;
}

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'errors' => ['Method not allowed']]);
    exit;
}

$pdo = get_pdo();
$nim = $_SESSION['user_id'];

// Get JSON data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['jadwal_ids'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'errors' => ['Invalid input']]);
    exit;
}

$jadwal_ids = array_map('intval', (array)$input['jadwal_ids']);
$errors = [];

try {
    $pdo->beginTransaction();

    // Get active semester
    $stmt = $pdo->prepare("SELECT id_semester FROM semester WHERE status = 'aktif' LIMIT 1");
    $stmt->execute();
    $semester = $stmt->fetch();

    if (!$semester) {
        throw new Exception("Tidak ada semester aktif");
    }

    $id_semester = $semester['id_semester'];

    // Get student IPK from previous semesters (using grade weight scale)
    $stmt = $pdo->prepare("
        SELECT COALESCE(
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
            ) / NULLIF(SUM(mk.sks), 0), 0
        ) as ipk
        FROM nilai n
        JOIN krs k ON n.id_krs = k.id_krs
        JOIN jadwal_kuliah jk ON k.id_jadwal = jk.id_jadwal
        JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
        WHERE k.id_mahasiswa = ? AND n.status_kunci = 1
    ");
    $stmt->execute([$nim]);
    $ipk_data = $stmt->fetch();
    $ipk = floatval($ipk_data['ipk'] ?? 0);

    // Get max SKS based on IPK
    $max_sks = get_max_sks($ipk);

    // 0. Delete existing KRS for this semester before validation 
    // This ensures current enrollments don't block kuota checks or create false duplicates.
    $stmt = $pdo->prepare("
        DELETE FROM krs
        WHERE id_mahasiswa = ? AND id_jadwal IN (
            SELECT id_jadwal FROM jadwal_kuliah WHERE id_semester = ?
        )
    ");
    $stmt->execute([$nim, $id_semester]);

    // 1. Get jadwal info and calculate total SKS
    $stmt = $pdo->prepare("
        SELECT jk.id_jadwal, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.kuota, mk.sks,
               COUNT(k.id_krs) as current_enrollment, mk.id_matkul, mk.nama_matkul
        FROM jadwal_kuliah jk
        JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
        LEFT JOIN krs k ON jk.id_jadwal = k.id_jadwal
        WHERE jk.id_jadwal IN (" . implode(',', array_fill(0, count($jadwal_ids), '?')) . ")
            AND jk.id_semester = ?
        GROUP BY jk.id_jadwal
    ");
    $exec_params = array_merge($jadwal_ids, [$id_semester]);
    $stmt->execute($exec_params);
    $jadwal_list = $stmt->fetchAll();

    // Create lookup
    $jadwal_map = [];
    foreach ($jadwal_list as $j) {
        $jadwal_map[$j['id_jadwal']] = $j;
    }

    // 2. Calculate total SKS
    $total_sks = 0;
    foreach ($jadwal_ids as $id) {
        if (!isset($jadwal_map[$id])) {
            $errors[] = "Jadwal {$id} tidak ditemukan atau tidak aktif";
            continue;
        }
        $total_sks += $jadwal_map[$id]['sks'];
    }

    // 3. Validate SKS limit
    if ($total_sks > $max_sks) {
        throw new Exception("Total SKS ({$total_sks}) melebihi batas maksimal ({$max_sks}) berdasarkan IPK Anda");
    }

    // 4. Check for time conflicts
    $jadwal_selected = [];
    foreach ($jadwal_ids as $id) {
        if (isset($jadwal_map[$id])) {
            $jadwal_selected[] = $jadwal_map[$id];
        }
    }

    for ($i = 0; $i < count($jadwal_selected); $i++) {
        for ($j = $i + 1; $j < count($jadwal_selected); $j++) {
            $j1 = $jadwal_selected[$i];
            $j2 = $jadwal_selected[$j];

            // Check if same day
            if ($j1['hari'] === $j2['hari']) {
                // Check time overlap
                $start1 = strtotime($j1['jam_mulai']);
                $end1 = strtotime($j1['jam_selesai']);
                $start2 = strtotime($j2['jam_mulai']);
                $end2 = strtotime($j2['jam_selesai']);

                if (!($end1 <= $start2 || $end2 <= $start1)) {
                    throw new Exception("Terdapat bentrok jadwal pada hari {$j1['hari']}");
                }
            }
        }
    }

    // 5. Check kuota availability
    foreach ($jadwal_ids as $id) {
        $j = $jadwal_map[$id];
        if ($j['current_enrollment'] >= $j['kuota']) {
            throw new Exception("Kuota penuh untuk jadwal {$id}");
        }
    }

    // 6. Check if multiple schedules selected for the same course in this payload
    $selected_matkul = [];
    foreach ($jadwal_selected as $j) {
        $id_mk = $j['id_matkul'];
        if (isset($selected_matkul[$id_mk])) {
            throw new Exception("Anda memilih lebih dari satu kelas untuk mata kuliah " . $j['nama_matkul']);
        }
        $selected_matkul[$id_mk] = true;
    }

    // 8. Insert new KRS
    foreach ($jadwal_ids as $id) {
        $stmt = $pdo->prepare("
            INSERT INTO krs (id_mahasiswa, id_jadwal, tanggal_ambil)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$nim, $id]);
    }

    $pdo->commit();

    echo json_encode([
        'ok' => true,
        'saved' => count($jadwal_ids),
        'total_sks' => $total_sks,
        'max_sks' => $max_sks,
        'message' => "KRS berhasil disimpan ({$total_sks} SKS)"
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'errors' => [$e->getMessage()]
    ]);
}
