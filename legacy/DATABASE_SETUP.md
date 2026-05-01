# Database Setup Guide - SIAKAD Gallery

## Overview
Complete database schema and seed data for SIAKAD Gallery - Sistem Informasi KRS Mahasiswa (Student Course Plan System).

## Files Created

### 1. Database Files

#### `/database/schema.sql`
Complete DDL (Data Definition Language) for all tables:
- **admin**: Administrative user accounts
- **mahasiswa**: Student records with authentication
- **dosen**: Lecturer/professor records
- **mata_kuliah**: Course/subject master data
- **semester**: Academic semester definitions
- **jadwal_kuliah**: Class schedule/timetable
- **krs**: Kartu Rencana Studi (Student Course Plan registration)
- **nilai**: Course grades and assessment scores
- **pengumuman**: Campus announcements

**Key Features:**
- InnoDB engine for transaction support
- UTF-8MB4 charset for full Unicode support
- Proper foreign key relationships
- Strategic indexes for common queries
- UNIQUE constraints to prevent duplicates

#### `/database/seed.sql`
Realistic dummy data for development and testing:

**Accounts Created:**
- 1 Admin account (username: `admin`, password: `password123`)
- 5 Lecturers (NIDN format IDs)
- 10 Students (NIM format IDs)
- Test passwords: All use hash `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi` (password123)

**Academic Data:**
- 4 Semesters (2022/2023 and 2023/2024, both odd and even terms)
- Current active semester: 2023/2024 Genap (Even)
- 15 Courses across different semesters
- 12 Class schedules for current semester
- Sample KRS enrollments and grades

**Test Student:** 
- NIM: `21010023` (Budi Prasetyo)
- Has KRS enrollments in current semester
- Has previous semester grades (for IPK calculation)

### 2. PHP Configuration Files

#### `/includes/config.php`
Application configuration constants:
```php
APP_NAME = 'SIAKAD Gallery'
APP_URL = 'http://localhost/web-krs-mahasiswa/public'
DB_HOST = 'localhost'
DB_NAME = 'web_krs'
DB_USER = 'root'
DB_PASS = '' (empty)
DB_CHARSET = 'utf8mb4'
```

**Security Settings:**
- CSRF token length: 32 bytes
- Session lifetime: 8 hours
- Max login attempts: 5
- Login timeout: 5 minutes
- File upload max: 2 MB

#### `/includes/db.php`
Database connection using PDO (Singleton pattern):
- `Database::getInstance()` - Get singleton instance
- `get_pdo()` - Helper function to get PDO connection
- PDO in EXCEPTION mode for proper error handling
- Prevents cloning and unserialization for true singleton

**Features:**
- Automatic charset configuration
- Prepared statements ready
- Connection pooling support
- Error reporting

#### `/includes/auth.php`
Authentication and authorization functions:

**CSRF Protection:**
- `csrf_token()` - Generate/retrieve CSRF token
- `csrf_verify($token)` - Verify CSRF token

**Session Management:**
- `is_logged_in()` - Check if user is logged in
- `current_user()` - Get current user data
- `do_logout()` - Destroy session securely

**Login Functions:**
- `login_mahasiswa($nim, $password)` - Student login
- `login_dosen($nidn, $password)` - Lecturer login
- `login_admin($username, $password)` - Admin login

**Authorization:**
- `require_role($roles)` - Enforce role-based access control
- Redirects to login on unauthorized access

**Utilities:**
- `flash($key, $message)` - Flash message handling
- `redirect($url)` - Safe URL redirect

#### `/includes/helpers.php`
Utility and formatting functions:

**Security:**
- `h($str)` - HTML escape for XSS prevention

**Formatting:**
- `format_tanggal($date)` - Format date in Indonesian
- `format_rupiah($amount)` - Format currency
- `truncate($str, $len)` - Truncate with ellipsis
- `format_durasi($seconds)` - Format time duration

**Grade/Academic Functions:**
- `nilai_huruf($angka)` - Convert numeric to letter grade
- `nilai_bobot($huruf)` - Get GPA weight for letter grade
- `hitung_nilai_angka($tugas, $uts, $uas)` - Calculate final score
- `get_badge_class($huruf)` - Get CSS class for grade badge
- `get_max_sks($ipk)` - Calculate max SKS from IPK

**Status/Display:**
- `get_status_badge_class($status)` - Get status badge CSS
- `get_semester_label($tingkatan)` - Format semester label
- `get_hari_number($hari)` - Convert day name to number

**Validation & Utilities:**
- `is_valid_email($email)` - Email validation
- `time_overlap($start1, $end1, $start2, $end2)` - Check schedule conflict
- `generate_upload_filename($original)` - Safe filename generation

## Installation Instructions

### Step 1: Create Database
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE web_krs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p web_krs < database/schema.sql

# Import seed data
mysql -u root -p web_krs < database/seed.sql
```

### Step 2: Configure PHP
The `config.php` is pre-configured for localhost. Adjust if needed:
- DB_HOST
- DB_NAME
- DB_USER
- DB_PASS

### Step 3: Create Upload Directory
```bash
mkdir -p public/assets/uploads
chmod 755 public/assets/uploads
```

### Step 4: Run Application
Option A (XAMPP):
```bash
# Copy folder to htdocs
cp -r web-krs-mahasiswa /path/to/xampp/htdocs/

# Open in browser
http://localhost/web-krs-mahasiswa/public
```

Option B (PHP Built-in Server):
```bash
cd public
php -S localhost:8000
```

## Test Credentials

### Admin
- Username: `admin`
- Password: `password123`

### Lecturer (Dosen)
- NIDN: `198504122010`
- Name: Dr. Ahmad Fauzi
- Password: `password123`

### Student (Mahasiswa)
- NIM: `21010023`
- Name: Budi Prasetyo
- Password: `password123`

## Database Schema Summary

### Key Relationships
- `dosen` 1:N `jadwal_kuliah` (Lecturer teaches multiple classes)
- `mata_kuliah` 1:N `jadwal_kuliah` (Course has multiple class schedules)
- `semester` 1:N `jadwal_kuliah` (Semester has multiple schedules)
- `mahasiswa` 1:N `krs` (Student enrolls in multiple courses)
- `jadwal_kuliah` 1:N `krs` (Class has multiple student enrollments)
- `krs` 1:1 `nilai` (Enrollment results in one grade record)

### Grade Conversion Formula
```
Nilai Angka = 0.2 × Tugas + 0.3 × UTS + 0.5 × UAS

Grade Mapping:
≥ 85.00 → A  (4.0)
70-84.99 → B+ (3.5)
60-69.99 → B  (3.0)
55-59.99 → C+ (2.5)
50-54.99 → C  (2.0)
40-49.99 → D  (1.0)
< 40.00 → E  (0.0)
```

### IPK and SKS Calculation
```
IPS (Semester) = Σ(Grade Weight × SKS) / Σ(SKS)
IPK (Cumulative) = Σ(Grade Weight × SKS) / Σ(SKS) [all semesters]

Max SKS by IPK:
≥ 3.50 → 24 SKS
3.00-3.49 → 22 SKS
2.50-2.99 → 20 SKS
2.00-2.49 → 18 SKS
< 2.00 → 15 SKS
```

## Security Features

### Implemented
✓ PDO prepared statements (SQL injection prevention)
✓ Password hashing (password_hash/verify)
✓ CSRF tokens for forms
✓ HTML escaping (htmlspecialchars)
✓ Session management with regeneration
✓ Role-based access control
✓ Foreign key constraints
✓ Unique constraints on critical fields

### To Implement
- Rate limiting on login
- Two-factor authentication
- Audit logging
- File upload validation
- HTTPS enforcement
- Content Security Policy headers

## Next Steps

1. Create layout includes (header.php, sidebar.php, footer.php)
2. Implement authentication pages (login, logout)
3. Build mahasiswa module (dashboard, krs, khs, jadwal, profil)
4. Build dosen module (dashboard, input_nilai, daftar_mahasiswa)
5. Build admin module (CRUD operations)
6. Add CSS styling (style.css, dashboard.css, auth.css)
7. Add JavaScript functionality (krs.js, app.js)

---

Created: 2026-04-24
SIAKAD Gallery v1.0.0
