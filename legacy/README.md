# SIAKAD Gallery — Sistem Informasi KRS Mahasiswa

Portal Akademik Terpadu untuk Mahasiswa, Dosen, dan Admin berbasis **PHP Native + MySQL**.

---

## Cara Menjalankan

### Prasyarat
- PHP 8.x
- MySQL 8.x / MariaDB 10.x
- Web server (Apache/Nginx) atau XAMPP/Laragon

### 1. Setup Database
```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE web_krs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p web_krs < database/schema.sql

# Import data dummy
mysql -u root -p web_krs < database/seed.sql
```

### 2. Konfigurasi Aplikasi
Edit file `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'web_krs');
define('DB_USER', 'root');
define('DB_PASS', '');   // sesuaikan password MySQL Anda
define('APP_URL', 'http://localhost/web-krs-mahasiswa/public');
```

### 3. Jalankan Aplikasi

**Opsi A: XAMPP**
1. Copy folder `web-krs-mahasiswa/` ke `C:/xampp/htdocs/`
2. Buka: `http://localhost/web-krs-mahasiswa/public/`

**Opsi B: PHP Built-in Server**
```bash
php -S localhost:8000 -t public
# Buka: http://localhost:8000
```
> Catatan: Update `APP_URL` di config.php menjadi `http://localhost:8000`

**Opsi C: Laragon**
1. Copy folder ke `C:/laragon/www/`
2. Update `APP_URL` di config.php ke URL yang sesuai.

---

## Akun Demo

| Role       | Username / NIM     | Password      |
|------------|--------------------|---------------|
| Mahasiswa  | `21010023`         | `password123` |
| Mahasiswa  | `21010045`         | `password123` |
| Dosen      | `198504122010`     | `password123` |
| Dosen      | `197803052008`     | `password123` |
| Admin      | `admin`            | `password123` |

---

## Struktur Folder

```
web-krs-mahasiswa/
├── database/
│   ├── schema.sql          ← DDL semua tabel
│   └── seed.sql            ← Data dummy
├── public/                 ← Document root
│   ├── index.php           ← Redirect ke login
│   ├── assets/
│   │   ├── css/            ← style.css, auth.css, dashboard.css
│   │   ├── js/             ← app.js, krs.js
│   │   └── img/            ← logo.svg, uploads/
│   ├── auth/
│   │   ├── login.php
│   │   └── logout.php
│   ├── mahasiswa/
│   │   ├── dashboard.php
│   │   ├── profil.php
│   │   ├── krs.php
│   │   ├── khs.php
│   │   └── jadwal.php
│   ├── dosen/
│   │   ├── dashboard.php
│   │   ├── input_nilai.php
│   │   ├── daftar_mahasiswa.php
│   │   └── jadwal.php
│   └── admin/
│       ├── dashboard.php
│       ├── mahasiswa/      ← CRUD mahasiswa
│       ├── dosen/          ← CRUD dosen
│       ├── matkul/         ← CRUD mata kuliah
│       ├── semester/       ← CRUD semester
│       └── jadwal/         ← CRUD jadwal kuliah
├── includes/
│   ├── config.php          ← Konfigurasi DB & APP
│   ├── db.php              ← Koneksi PDO singleton
│   ├── auth.php            ← Helper autentikasi & session
│   ├── helpers.php         ← Fungsi utility
│   ├── header.php          ← Komponen topbar
│   ├── sidebar.php         ← Komponen sidebar navigasi
│   └── footer.php          ← Komponen footer
└── api/
    ├── krs_save.php        ← AJAX endpoint simpan KRS
    └── nilai_save.php      ← AJAX endpoint simpan nilai
```

---

## Fitur

### Mahasiswa
- Dashboard (IPK, SKS, semester aktif, jadwal hari ini, pengumuman)
- Profil Akademik (history semester, predicate, enrollment status)
- Pengisian KRS (validasi SKS, cek bentrok jadwal, cek kuota, real-time counter)
- Kartu Hasil Studi / KHS (nilai per semester, IPS, IPK kumulatif)
- Jadwal Kuliah Mingguan (grid 5 hari, berwarna per jenis matkul)

### Dosen
- Dashboard (matkul diampu, total mahasiswa, jadwal hari ini)
- Jadwal Mengajar (grid mingguan)
- Daftar Mahasiswa per Kelas
- Input Nilai (kalkulasi otomatis bobot 20/30/50, AJAX save, lock nilai)

### Admin
- Dashboard (statistik sistem, semester aktif)
- CRUD Mahasiswa
- CRUD Dosen
- CRUD Mata Kuliah
- CRUD Semester (hanya 1 aktif sekaligus)
- CRUD Jadwal Kuliah

---

## Formula Penilaian

```
Nilai Akhir = 0.2 × Tugas + 0.3 × UTS + 0.5 × UAS

Konversi Huruf:
≥ 85  → A   (bobot 4.0)
70–84 → B+  (bobot 3.5)
60–69 → B   (bobot 3.0)
55–59 → C+  (bobot 2.5)
50–54 → C   (bobot 2.0)
40–49 → D   (bobot 1.0)
< 40  → E   (bobot 0.0)

IPS / IPK = Σ(bobot × SKS) / Σ(SKS)
```

## Batas SKS Berdasarkan IPK

| IPK Semester Lalu | Maks SKS |
|-------------------|----------|
| ≥ 3.50            | 24 SKS   |
| 3.00 – 3.49       | 22 SKS   |
| 2.50 – 2.99       | 20 SKS   |
| 2.00 – 2.49       | 18 SKS   |
| < 2.00            | 15 SKS   |

---

## Tech Stack

- **Backend**: PHP 8.x Native (tanpa framework), PDO prepared statements
- **Frontend**: HTML5 + CSS3 + Vanilla JavaScript (tanpa framework JS)
- **Database**: MySQL 8.x / MariaDB 10.x (InnoDB, utf8mb4)
- **Styling**: Custom CSS — Design System "The Gallery" (Navy `#0B1E4F` + White)
- **Icons**: Bootstrap Icons via CDN
- **Font**: Inter (Google Fonts)

---

## Keamanan

- Password disimpan dengan `password_hash()` (bcrypt)
- Seluruh query menggunakan **PDO prepared statements** (anti SQLi)
- Output dinamis di-escape dengan `htmlspecialchars()` (anti XSS)
- CSRF token pada setiap form POST
- Role-based access control via PHP Session
- Session regenerasi ID setelah login sukses
