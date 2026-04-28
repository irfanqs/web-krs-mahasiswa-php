<?php
/**
 * Helper Functions
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

/**
 * HTML escape function
 */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date to Indonesian format
 * Input: 2024-02-15 or DateTime object
 * Output: 15 Februari 2024
 */
function format_tanggal($date, $format = 'long')
{
    if (empty($date)) {
        return '-';
    }

    if (is_string($date)) {
        $date = strtotime($date);
    }

    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $hari = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu'
    ];

    if ($format === 'long') {
        $tanggal = date('d', $date);
        $bulan_num = date('n', $date);
        $tahun = date('Y', $date);
        return "{$tanggal} {$bulan[$bulan_num]} {$tahun}";
    } elseif ($format === 'short') {
        return date('d/m/Y', $date);
    } elseif ($format === 'hari') {
        $day_name = date('l', $date);
        return $hari[$day_name] ?? $day_name;
    }

    return date('d-m-Y', $date);
}

/**
 * Convert numeric grade to letter grade
 */
function nilai_huruf($angka)
{
    if ($angka >= 85) {
        return 'A';
    } elseif ($angka >= 70 && $angka < 85) {
        return 'B+';
    } elseif ($angka >= 60 && $angka < 70) {
        return 'B';
    } elseif ($angka >= 55 && $angka < 60) {
        return 'C+';
    } elseif ($angka >= 50 && $angka < 55) {
        return 'C';
    } elseif ($angka >= 40 && $angka < 50) {
        return 'D';
    } else {
        return 'E';
    }
}

/**
 * Convert letter grade to GPA weight
 */
function nilai_bobot($huruf)
{
    $bobot = [
        'A' => 4.0,
        'B+' => 3.5,
        'B' => 3.0,
        'C+' => 2.5,
        'C' => 2.0,
        'D' => 1.0,
        'E' => 0.0
    ];

    return $bobot[$huruf] ?? 0.0;
}

/**
 * Calculate numeric grade from components
 * Formula: 0.2 * tugas + 0.3 * uts + 0.5 * uas
 */
function hitung_nilai_angka($tugas, $uts, $uas)
{
    $tugas = floatval($tugas) ?? 0;
    $uts = floatval($uts) ?? 0;
    $uas = floatval($uas) ?? 0;

    $nilai = (0.2 * $tugas) + (0.3 * $uts) + (0.5 * $uas);
    return round($nilai, 2);
}

/**
 * Get CSS class for grade badge
 */
function get_badge_class($huruf)
{
    $class_map = [
        'A' => 'badge-success',    // #2BB673 (green)
        'B+' => 'badge-warning',   // #F4B43C (yellow/amber)
        'B' => 'badge-info',       // #2A4A9E (blue)
        'C+' => 'badge-warning',   // #F39E45 (orange)
        'C' => 'badge-secondary',  // #6B7489 (gray)
        'D' => 'badge-danger',     // #E08B5F (orange-red)
        'E' => 'badge-danger'      // #E04F5F (red)
    ];

    return $class_map[$huruf] ?? 'badge-secondary';
}

/**
 * Get maximum SKS based on IPK from previous semester
 * IPK >= 3.50 = 24 SKS
 * IPK 3.00-3.49 = 22 SKS
 * IPK 2.50-2.99 = 20 SKS
 * IPK 2.00-2.49 = 18 SKS
 * IPK < 2.00 = 15 SKS
 */
function get_max_sks($ipk)
{
    $ipk = floatval($ipk) ?? 0;

    if ($ipk >= 3.50) {
        return 24;
    } elseif ($ipk >= 3.00) {
        return 22;
    } elseif ($ipk >= 2.50) {
        return 20;
    } elseif ($ipk >= 2.00) {
        return 18;
    } else {
        return 15;
    }
}

/**
 * Get semester label in Indonesian
 */
function get_semester_label($tingkatan)
{
    return $tingkatan === 'ganjil' ? 'Ganjil' : 'Genap';
}

/**
 * Truncate string with ellipsis
 */
function truncate($str, $len = 50)
{
    if (strlen($str) <= $len) {
        return $str;
    }
    return substr($str, 0, $len) . '...';
}

/**
 * Get status badge class
 */
function get_status_badge_class($status)
{
    $class_map = [
        'aktif' => 'badge-success',
        'cuti' => 'badge-warning',
        'lulus' => 'badge-info',
        'nonaktif' => 'badge-danger'
    ];

    return $class_map[$status] ?? 'badge-secondary';
}

/**
 * Format currency (Rupiah)
 */
function format_rupiah($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Get hari from day name
 */
function get_hari_number($hari)
{
    $hari_map = [
        'Senin' => 1,
        'Selasa' => 2,
        'Rabu' => 3,
        'Kamis' => 4,
        'Jumat' => 5,
        'Sabtu' => 6,
        'Minggu' => 0
    ];

    return $hari_map[$hari] ?? -1;
}

/**
 * Check if two time ranges overlap
 */
function time_overlap($start1, $end1, $start2, $end2)
{
    $start1 = strtotime($start1);
    $end1 = strtotime($end1);
    $start2 = strtotime($start2);
    $end2 = strtotime($end2);

    return !($end1 <= $start2 || $end2 <= $start1);
}

/**
 * Convert seconds to human readable time
 */
function format_durasi($seconds)
{
    if ($seconds < 60) {
        return $seconds . ' detik';
    } elseif ($seconds < 3600) {
        $menit = ceil($seconds / 60);
        return $menit . ' menit';
    } else {
        $jam = floor($seconds / 3600);
        $menit = floor(($seconds % 3600) / 60);
        if ($menit == 0) {
            return $jam . ' jam';
        }
        return $jam . ' jam ' . $menit . ' menit';
    }
}

/**
 * Generate unique filename for upload
 */
function generate_upload_filename($original_filename)
{
    $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
    $name = pathinfo($original_filename, PATHINFO_FILENAME);
    return time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
}

/**
 * Validate email
 */
function is_valid_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
