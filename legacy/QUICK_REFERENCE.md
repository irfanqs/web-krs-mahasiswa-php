# Quick Reference - SIAKAD Gallery Database Setup

## File Locations
```
web-krs-mahasiswa/
├── database/
│   ├── schema.sql          ← All table definitions (9 tables)
│   └── seed.sql            ← Dummy data for development
└── includes/
    ├── config.php          ← Application & database config
    ├── db.php              ← PDO singleton class
    ├── auth.php            ← Authentication & authorization
    └── helpers.php         ← Utility functions (32 functions)
```

## Database Tables Overview

| Table | Rows | Purpose |
|-------|------|---------|
| admin | 1 | Admin user (username: admin) |
| dosen | 5 | Lecturers/Professors |
| mahasiswa | 10 | Students |
| mata_kuliah | 15 | Courses |
| semester | 4 | Academic semesters |
| jadwal_kuliah | 12 | Class schedules |
| krs | 6 | Student course enrollments |
| nilai | 2 | Grades/scores |
| pengumuman | 3 | Campus announcements |

## Key PHP Functions

### Authentication
```php
login_mahasiswa($nim, $password)      // Student login
login_dosen($nidn, $password)         // Lecturer login
login_admin($username, $password)     // Admin login
is_logged_in()                        // Check session
require_role(['mahasiswa'])           // Enforce access control
current_user()                        // Get session user
do_logout()                           // Secure logout
```

### Grade Calculation
```php
hitung_nilai_angka($tugas, $uts, $uas)    // 0.2*T + 0.3*U + 0.5*A
nilai_huruf($angka)                       // Numeric to letter (A, B+, B, etc)
nilai_bobot($huruf)                       // Letter to GPA (4.0, 3.5, etc)
get_max_sks($ipk)                         // Max SKS by IPK
```

### Formatting
```php
h($str)                           // HTML escape (XSS prevention)
format_tanggal($date)             // Format date in Indonesian
format_rupiah($amount)            // Format currency
truncate($str, 50)                // Truncate with ellipsis
get_badge_class($huruf)           // CSS class for grade badge
get_status_badge_class($status)   // CSS class for status badge
```

### Validation & Utilities
```php
is_valid_email($email)            // Email validation
time_overlap($s1, $e1, $s2, $e2)  // Schedule conflict check
generate_upload_filename($orig)   // Safe filename generation
csrf_token()                      // Get/generate CSRF token
csrf_verify($token)               // Verify CSRF token
flash($key, $msg)                 // Flash message handling
```

## Database Connection Usage

```php
<?php
require_once 'includes/db.php';

// Get PDO connection
$pdo = get_pdo();

// Prepared statement example
$stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE nim = ?");
$stmt->execute([$nim]);
$mahasiswa = $stmt->fetch();

// Multiple results
$stmt = $pdo->prepare("SELECT * FROM krs WHERE id_mahasiswa = ?");
$stmt->execute([$nim]);
$krs_list = $stmt->fetchAll();
```

## Grade/Value Conversion Reference

### Letter Grade Ranges
```
A     ≥ 85.00   Weight: 4.0
B+    70-84.99  Weight: 3.5
B     60-69.99  Weight: 3.0
C+    55-59.99  Weight: 2.5
C     50-54.99  Weight: 2.0
D     40-49.99  Weight: 1.0
E     < 40.00   Weight: 0.0
```

### SKS Limits by IPK
```
IPK ≥ 3.50    →  24 SKS
IPK 3.00-3.49 →  22 SKS
IPK 2.50-2.99 →  20 SKS
IPK 2.00-2.49 →  18 SKS
IPK < 2.00    →  15 SKS
```

## Test User Credentials

### Admin
```
Username: admin
Password: password123
```

### Lecturer Example
```
NIDN: 198504122010
Nama: Dr. Ahmad Fauzi
Password: password123
```

### Student Example
```
NIM: 21010023
Nama: Budi Prasetyo
Password: password123
Program: Teknik Informatika
Status: aktif
```

## Common Queries

### Get all KRS for a student
```php
$stmt = $pdo->prepare("
    SELECT k.id_krs, jk.id_jadwal, mk.nama_matkul, mk.sks, 
           jk.hari, jk.jam_mulai, jk.jam_selesai, d.nama as dosen_nama
    FROM krs k
    JOIN jadwal_kuliah jk ON k.id_jadwal = jk.id_jadwal
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    JOIN dosen d ON jk.id_dosen = d.nidn
    WHERE k.id_mahasiswa = ? AND jk.id_semester = ?
");
$stmt->execute([$nim, $semester_id]);
```

### Get student's grades
```php
$stmt = $pdo->prepare("
    SELECT n.id_nilai, mk.nama_matkul, n.tugas, n.uts, n.uas, 
           n.nilai_angka, n.nilai_huruf
    FROM nilai n
    JOIN krs k ON n.id_krs = k.id_krs
    JOIN jadwal_kuliah jk ON k.id_jadwal = jk.id_jadwal
    JOIN mata_kuliah mk ON jk.id_matkul = mk.id_matkul
    WHERE k.id_mahasiswa = ? AND n.status_kunci = 1
");
$stmt->execute([$nim]);
```

### Get active semester
```php
$stmt = $pdo->prepare("SELECT * FROM semester WHERE status = 'aktif'");
$stmt->execute();
$semester = $stmt->fetch();
```

## Important Notes

1. **All passwords** are hashed using `password_hash()` with DEFAULT algorithm
2. **All user IDs** are unique: NIM for students, NIDN for lecturers, ID for admin
3. **CSRF tokens** required for all POST forms
4. **Session lifetime** is 8 hours (can be changed in config.php)
5. **Active semester** should have status = 'aktif' (only one at a time)
6. **Grade lock** when `status_kunci = 1` (input becomes readonly)
7. **Schedule conflicts** must be checked when enrolling in courses
8. **KRS unique constraint** on (id_mahasiswa, id_jadwal) prevents double-enrollment

## Configuration Changes (if needed)

Edit `/includes/config.php`:
```php
define('DB_HOST', 'localhost');      // Database host
define('DB_NAME', 'web_krs');        // Database name
define('DB_USER', 'root');           // Database user
define('DB_PASS', '');               // Database password
define('SESSION_LIFETIME', 3600 * 8);  // Session timeout in seconds
define('APP_URL', 'http://localhost/web-krs-mahasiswa/public');
```

---

Last Updated: 2026-04-24
For complete documentation, see DATABASE_SETUP.md
