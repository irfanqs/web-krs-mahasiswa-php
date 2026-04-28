<?php
/**
 * API Endpoint - Save/Update Nilai
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

header('Content-Type: application/json');

require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

// Check authentication and role
if (!is_logged_in() || $_SESSION['role'] !== 'dosen') {
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
$nidn = $_SESSION['user_id'];

// Get JSON data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['nilai_data']) || !is_array($input['nilai_data'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'errors' => ['Invalid input']]);
    exit;
}

$nilai_data = $input['nilai_data'];
$id_jadwal = isset($input['id_jadwal']) ? (int)$input['id_jadwal'] : 0;

// Verify jadwal belongs to current dosen
$stmt = $pdo->prepare("SELECT id_dosen FROM jadwal_kuliah WHERE id_jadwal = ?");
$stmt->execute([$id_jadwal]);
$jadwal = $stmt->fetch();

if (!$jadwal || $jadwal['id_dosen'] !== $nidn) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'errors' => ['Jadwal not found or unauthorized']]);
    exit;
}

$errors = [];
$saved_count = 0;

try {
    $pdo->beginTransaction();

    foreach ($nilai_data as $item) {
        $id_krs = (int)$item['id_krs'];
        $tugas = !empty($item['tugas']) ? floatval($item['tugas']) : null;
        $uts = !empty($item['uts']) ? floatval($item['uts']) : null;
        $uas = !empty($item['uas']) ? floatval($item['uas']) : null;
        $nilai_angka = !empty($item['nilai_angka']) ? floatval($item['nilai_angka']) : null;
        $nilai_huruf = !empty($item['nilai_huruf']) ? $item['nilai_huruf'] : null;

        // Validate KRS exists and belongs to this jadwal
        $stmt = $pdo->prepare("SELECT id_krs, status_kunci FROM nilai WHERE id_krs = ? LIMIT 1");
        $stmt->execute([$id_krs]);
        $existing = $stmt->fetch();

        // Check if locked
        if ($existing && $existing['status_kunci'] == 1) {
            $errors[] = "Nilai untuk KRS {$id_krs} sudah terkunci dan tidak bisa diubah";
            continue;
        }

        // Check if nilai entry exists
        if ($existing) {
            // UPDATE
            $stmt = $pdo->prepare("
                UPDATE nilai
                SET tugas = ?, uts = ?, uas = ?, nilai_angka = ?, nilai_huruf = ?, updated_at = NOW()
                WHERE id_krs = ?
            ");
            $result = $stmt->execute([$tugas, $uts, $uas, $nilai_angka, $nilai_huruf, $id_krs]);
            if ($result) {
                $saved_count++;
            }
        } else {
            // INSERT
            $stmt = $pdo->prepare("
                INSERT INTO nilai (id_krs, tugas, uts, uas, nilai_angka, nilai_huruf, status_kunci, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), NOW())
            ");
            $result = $stmt->execute([$id_krs, $tugas, $uts, $uas, $nilai_angka, $nilai_huruf]);
            if ($result) {
                $saved_count++;
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        'ok' => true,
        'saved' => $saved_count,
        'errors' => $errors,
        'message' => "Nilai berhasil disimpan ({$saved_count} record)"
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'errors' => ['Database error: ' . $e->getMessage()]
    ]);
}
