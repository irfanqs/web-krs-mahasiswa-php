# CLAUDE.md — Sistem Informasi KRS Mahasiswa (SIAKAD Gallery)

> Dokumen ini berperan ganda sebagai **Product Requirements Document (PRD)** dan **context file untuk Claude Code** agar dapat membangun aplikasi web KRS Mahasiswa secara konsisten.

---

## 1. Project Overview

**Nama Aplikasi:** SIAKAD Gallery — The Gallery Academic Portal
**Jenis:** Sistem Informasi Akademik / Kartu Rencana Studi (KRS) berbasis web
**Target Pengguna:** Mahasiswa, Dosen, dan Admin Akademik pada sebuah perguruan tinggi
**Bahasa UI Utama:** Bahasa Indonesia (dengan beberapa istilah teknis berbahasa Inggris)

### 1.1 Latar Belakang

Pengisian KRS manual maupun sistem lama seringkali menyulitkan mahasiswa (cek jadwal bentrok, batas SKS, prasyarat), menyita waktu dosen (input nilai), serta memberatkan admin (manajemen data). Aplikasi ini dirancang sebagai portal terpadu untuk ketiga peran tersebut, dengan UI modern ala "The Gallery" (navy + white, kartu-kartu informasi, progress bar, tabel rapi).

### 1.2 Tujuan Proyek

1. Menyediakan portal tunggal untuk pengisian KRS, input nilai, dan manajemen data akademik.
2. Memvalidasi otomatis batas SKS berdasarkan IPK semester sebelumnya.
3. Menampilkan Kartu Hasil Studi (KHS), jadwal mingguan, dan profil akademik secara informatif.
4. Membangun dengan **Laravel 11** agar terstruktur, mudah dimaintain, dan scalable.

---

## 2. Tech Stack

| Layer | Teknologi | Catatan |
|-------|-----------|---------|
| Bahasa Backend | **Laravel 11 (PHP 8.2+)** | Framework MVC. Gunakan Eloquent ORM untuk DB. |
| Bahasa Frontend | **Blade Templating + CSS3 + JavaScript (vanilla)** | Bawaan Laravel. Boleh pakai sedikit jQuery bila perlu. |
| Database | **MySQL 8.x / MariaDB 10.x** | Engine `InnoDB`, charset `utf8mb4`. Migration + Seeder Laravel. |
| Styling | **CSS Custom** + opsional **Bootstrap 5** via CDN | Ikuti design system di bagian 9. |
| Icon | **Lucide / Bootstrap Icons** via CDN | Konsisten 1 set saja. |
| Font | **Inter** (Google Fonts) | Heading tegas, body nyaman dibaca. |
| Web Server | Apache / Nginx (Laragon/Herd lokal) | Deploy akhir: shared hosting cPanel atau VPS. |
| Auth | **Laravel Auth** (Session-based) + `bcrypt` | Gunakan `Auth::attempt()`, `Auth::user()`, middleware role. |
| Package | **Composer** | Dependency management Laravel. |

> **Stack:** Laravel 11 + Eloquent ORM + Blade + MySQL. Gunakan Migration untuk DDL, Seeder untuk data dummy, Resource Controller untuk CRUD, Middleware untuk proteksi role.

---

## 3. Struktur Folder

```
web-krs-mahasiswa/
├── CLAUDE.md                       ← file ini
├── README.md                       ← cara menjalankan proyek
├── database/
│   ├── schema.sql                  ← DDL seluruh tabel
│   └── seed.sql                    ← data dummy (mahasiswa, dosen, matkul, jadwal)
├── public/                         ← document root web
│   ├── index.php                   ← redirect ke /auth/login.php
│   ├── assets/
│   │   ├── css/
│   │   │   ├── style.css           ← global style
│   │   │   ├── auth.css
│   │   │   └── dashboard.css
│   │   ├── js/
│   │   │   ├── app.js
│   │   │   └── krs.js              ← validasi pengisian KRS
│   │   └── img/
│   │       └── logo.svg
│   ├── auth/
│   │   ├── login.php
│   │   └── logout.php
│   ├── mahasiswa/
│   │   ├── dashboard.php
│   │   ├── profil.php              ← Academic Profile
│   │   ├── krs.php                 ← Pengisian KRS
│   │   ├── khs.php                 ← Kartu Hasil Studi
│   │   └── jadwal.php              ← Weekly Schedule
│   ├── dosen/
│   │   ├── dashboard.php
│   │   ├── daftar_mahasiswa.php
│   │   ├── input_nilai.php
│   │   └── jadwal.php
│   └── admin/
│       ├── dashboard.php
│       ├── mahasiswa/              ← CRUD mahasiswa
│       ├── dosen/                  ← CRUD dosen
│       ├── matkul/                 ← CRUD mata kuliah
│       ├── semester/               ← CRUD semester
│       └── jadwal/                 ← CRUD jadwal kuliah
├── includes/
│   ├── config.php                  ← konstanta APP_URL, DB_*
│   ├── db.php                      ← koneksi PDO (singleton)
│   ├── auth.php                    ← helper: login, logout, require_role()
│   ├── helpers.php                 ← util: format tanggal, huruf nilai, flash message
│   ├── header.php                  ← top bar + sidebar (sesuai role)
│   ├── sidebar.php
│   └── footer.php
└── api/                            ← endpoint internal (opsional, AJAX)
    ├── krs_save.php
    └── nilai_save.php
```

**Aturan pemanggilan:**

- Setiap halaman (kecuali `auth/login.php`) wajib memanggil `require_role(['mahasiswa'])` / `['dosen']` / `['admin']` di baris paling atas.
- Seluruh query database menggunakan **PDO prepared statement** (`$pdo->prepare` → `execute`), **dilarang** string interpolation ke SQL.
- XSS dicegah dengan `htmlspecialchars()` pada setiap echo dari variabel user/DB.

---

## 4. Database Schema (ERD)

Sesuai ERD referensi. Semua tabel memakai engine `InnoDB`, charset `utf8mb4_unicode_ci`.

### 4.1 `mahasiswa`
| Kolom | Tipe | Catatan |
|-------|------|---------|
| `nim` | VARCHAR(15) | **PK** |
| `nama` | VARCHAR(100) | NOT NULL |
| `email` | VARCHAR(100) | UNIQUE |
| `password` | VARCHAR(255) | hash |
| `angkatan` | YEAR | untuk admin list |
| `program_studi` | VARCHAR(60) | untuk admin list |
| `status` | ENUM('aktif','cuti','lulus') | default 'aktif' |
| `foto` | VARCHAR(255) | path opsional |
| `created_at` | TIMESTAMP | default CURRENT_TIMESTAMP |

### 4.2 `dosen`
| Kolom | Tipe | Catatan |
|-------|------|---------|
| `nidn` | VARCHAR(15) | **PK** |
| `nama` | VARCHAR(100) | NOT NULL |
| `email` | VARCHAR(100) | UNIQUE |
| `password` | VARCHAR(255) | hash |
| `jurusan` | VARCHAR(60) | |
| `created_at` | TIMESTAMP | |

### 4.3 `mata_kuliah`
| Kolom | Tipe | Catatan |
|-------|------|---------|
| `id_matkul` | INT AUTO_INCREMENT | **PK** |
| `kode_matkul` | VARCHAR(10) | UNIQUE (mis. `TIK-302`) |
| `nama_matkul` | VARCHAR(100) | |
| `sks` | TINYINT | 1–6 |
| `semester` | TINYINT | semester ke- |
| `jenis` | ENUM('wajib','pilihan') | untuk chart distribusi |

### 4.4 `semester`
| Kolom | Tipe | Catatan |
|-------|------|---------|
| `id_semester` | INT AUTO_INCREMENT | **PK** |
| `tahun_ajaran` | VARCHAR(9) | mis. `2023/2024` |
| `tingkatan_semester` | ENUM('ganjil','genap') | |
| `status` | ENUM('aktif','nonaktif') | hanya 1 yang aktif |

### 4.5 `jadwal_kuliah`
| Kolom | Tipe | Catatan |
|-------|------|---------|
| `id_jadwal` | INT AUTO_INCREMENT | **PK** |
| `id_matkul` | INT | FK → mata_kuliah |
| `id_dosen` | VARCHAR(15) | FK → dosen.nidn |
| `id_semester` | INT | FK → semester |
| `hari` | ENUM('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') | |
| `jam_mulai` | TIME | |
| `jam_selesai` | TIME | |
| `ruang` | VARCHAR(30) | mis. `R.402`, `Lab A` |
| `kuota` | SMALLINT | jumlah slot |

### 4.6 `krs`
| Kolom | Tipe | Catatan |
|-------|------|---------|
| `id_krs` | INT AUTO_INCREMENT | **PK** |
| `id_mahasiswa` | VARCHAR(15) | FK → mahasiswa.nim |
| `id_jadwal` | INT | FK → jadwal_kuliah |
| `tanggal_ambil` | DATETIME | default CURRENT_TIMESTAMP |
| UNIQUE(`id_mahasiswa`,`id_jadwal`) | | cegah double-ambil |

### 4.7 `nilai`
| Kolom | Tipe | Catatan |
|-------|------|---------|
| `id_nilai` | INT AUTO_INCREMENT | **PK** |
| `id_krs` | INT | FK → krs (UNIQUE, 1:1) |
| `tugas` | DECIMAL(5,2) | 0–100 |
| `uts` | DECIMAL(5,2) | |
| `uas` | DECIMAL(5,2) | |
| `nilai_angka` | DECIMAL(5,2) | auto dari bobot 20/30/50 |
| `nilai_huruf` | ENUM('A','B+','B','C+','C','D','E') | |
| `status_kunci` | TINYINT(1) | 1 = final, tidak bisa edit |

### 4.8 Relasi (RINGKAS)

- `dosen` **1 : N** `jadwal_kuliah` (mengajar)
- `mata_kuliah` **1 : N** `jadwal_kuliah` (memiliki)
- `semester` **1 : N** `jadwal_kuliah` (memiliki)
- `mahasiswa` **1 : N** `krs` (mengambil)
- `jadwal_kuliah` **1 : N** `krs` (diambil)
- `krs` **1 : 1** `nilai` (menghasilkan)

### 4.9 Formula Konversi Nilai

```
nilai_angka = 0.2 × tugas + 0.3 × uts + 0.5 × uas

Huruf:
≥ 85 → A   (bobot 4.0)
70–84 → B+ (3.5)  |  (range lebih detail boleh)
60–69 → B  (3.0)
55–59 → C+ (2.5)
50–54 → C  (2.0)
40–49 → D  (1.0)
< 40  → E  (0.0)

IPS = Σ(bobot × sks) / Σ(sks)  per semester
IPK = Σ(bobot × sks) / Σ(sks)  lintas semester (hanya yang sudah lulus/berkontribusi)
```

### 4.10 Aturan Batas SKS Berdasarkan IPK

| IPK Semester Lalu | Max SKS |
|-------------------|---------|
| ≥ 3.50 | 24 |
| 3.00 – 3.49 | 22 |
| 2.50 – 2.99 | 20 |
| 2.00 – 2.49 | 18 |
| < 2.00 | 15 |

Validasi ini dihitung di backend saat submit KRS dan ditampilkan real-time di UI.

---

## 5. User Roles & Permission

| Role | Akses |
|------|-------|
| **Mahasiswa** | Dashboard, Profil, Pengisian KRS, KHS, Jadwal |
| **Dosen** | Dashboard, Mata Kuliah Diampu, Daftar Mahasiswa, Input Nilai, Jadwal Mengajar |
| **Admin** | Dashboard, CRUD Mahasiswa, CRUD Dosen, CRUD Mata Kuliah, CRUD Semester, CRUD Jadwal, Monitoring KRS |

Role disimpan di session: `$_SESSION['role']` = `mahasiswa` / `dosen` / `admin`. Halaman diproteksi dengan `require_role(['...'])`.

---

## 6. Fitur per Role (Detail)

### 6.1 🔐 Halaman Login (semua role)

Referensi wireframe: panel kiri navy bertuliskan **"Elevating your academic journey"**, panel kanan form login.

Komponen:
- Toggle **role**: `MAHASISWA` | `DOSEN` | `ADMIN` (tombol pil).
- Input **Username / NIM** (placeholder: `e.g. 21010023`).
- Input **Password** dengan toggle ikon mata (show/hide).
- Checkbox **"Remember this session"**.
- Tombol **"Masuk →"** (full width, navy).
- Link **"Lupa Password?"**.
- Footer: `Panduan Sistem` · `SIAKAD GALLERY © 2024 · ACADEMIC EXCELLENCE`.

Behavior:
- Submit → cek tabel sesuai role terpilih.
- Login sukses → redirect ke dashboard sesuai role.
- Login gagal → flash message merah di atas form.

### 6.2 🎓 Mahasiswa — Dashboard

Referensi: kartu biru sambutan "Welcome back, Budi" + 3 metric (GPA, Credits, Semester). Di kanan ada "Quick Access". Di bawah ada "Pengumuman Kampus" dan "Today's Sessions".

Komponen:
1. Hero card navy: nama mahasiswa, deskripsi singkat, 3 stat kecil: `GPA (IPK)`, `CREDITS EARNED`, `CURRENT SEMESTER`.
2. Card **Degree Path**: progress bar persentase kelulusan (SKS_tempuh / SKS_total_minimal).
3. Sidebar kanan **Quick Access**: link ke KRS Enrollment, KHS Results, Attendance List.
4. Card notification **Scholarship / Pengumuman** (ambil dari tabel `pengumuman` jika ada; opsional fase 2).
5. **Pengumuman Kampus**: list 3 terbaru (tgl, judul, tipe badge: `ACADEMIC` / `SYSTEM` / `EVENT`).
6. **Today's Sessions**: list jadwal hari ini (ambil dari `jadwal_kuliah` ∩ hari aktif).

### 6.3 👤 Mahasiswa — Academic Profile (`profil.php`)

Referensi wireframe "Academic Profile" dengan card foto + NIM, kartu navy GPA/SKS/Predikat, enrollment status, Personal Details, Semester Performance.

Komponen:
- Header tombol **"Print Transcript"** (export PDF sederhana via `window.print()` dengan CSS print).
- Card kiri: foto, nama, NIM, Major, Degree Program, Entry Year, Current Semester.
- Card kanan navy: `Cumulative GPA (IPK)`, `SKS Earned`, `Predicates` (A- / B+ / dst).
- Card **Enrollment Status**: badge hijau "Active (2023/2024 Even)" + progress bar approval study plan.
- **Personal Details**: 3 sub-kartu — Contact Info, Address, Biographical.
- **Semester Performance** tabel: Semester | Period | SKS Taken | IPS | Status.

### 6.4 📝 Mahasiswa — Pengisian KRS (`krs.php`)

Referensi: header "Pengisian KRS · Semester Ganjil 2023/2024", banner kuning "System Validation", kartu navy GPA + max SKS, distribution chart, tabel daftar matkul dengan checkbox.

Komponen utama:
1. **Header**: "Pengisian KRS" + sub "Semester Ganjil 2023/2024". Badge filtering "Semester 5".
2. **Alert banner kuning**: "Anda telah memilih **{n} SKS**. Batas maksimal pengambilan Anda berdasarkan IPK semester lalu adalah **{max} SKS**. Harap teliti kembali jadwal yang bentrok."
3. **Card navy STUDENT GPA**: `{IPK}` + caption "Eligible for maximum credit load ({max} SKS)".
4. **Card DISTRIBUTION**: bar horizontal `Mandatory` vs `Elective` (SKS wajib/pilihan terpilih).
5. **Tabel Available Course Gallery**:
   - Kolom: `NO`, `KODE`, `MATA KULIAH` (+ sublabel jurusan/mandatory/elective), `SKS`, `JADWAL & DOSEN` (hari, jam, ruang, nama dosen), `AKSI` (checkbox).
   - Baris ter-check ditandai border kiri tebal navy.
6. **Footer bar sticky**: `SELECTION PROGRESS {n}/{max} SKS SELECTED` · `TOTAL COURSES {x} Units` · tombol **Reset Selection** · tombol **Simpan KRS** (navy).

Validasi (backend `api/krs_save.php`):
- Total SKS ≤ max berdasarkan IPK.
- Tidak ada bentrok hari+jam dengan jadwal lain yang dipilih.
- Jadwal masih ada kuota (`jumlah_krs_terdaftar < kuota`).
- Belum pernah lulus matkul yang sama (cek `nilai` semester lalu ≠ E/D, opsional).
- Jika gagal → return JSON `{ok:false, errors:[...]}` dan tampilkan di UI.
- Jika sukses → insert ke `krs` dalam transaksi.

### 6.5 📊 Mahasiswa — Kartu Hasil Studi / KHS (`khs.php`)

Referensi: header "Kartu Hasil Studi · Semester Genap 2022/2023", 3 stat (IP Semester, IPK, SKS Tempuh), tabel nilai, footer status akademik.

Komponen:
- Dropdown **pilih semester** (default: semester terakhir yang sudah dinilai).
- Tombol **"Cetak KHS (PDF)"** (window.print + CSS print).
- 3 stat: **IP Semester** (card navy aktif), **IP Kumulatif (IPK)**, **SKS Tempuh** (mis. `84 / 144`).
- Tabel: KODE | MATA KULIAH | SKS | NILAI (badge warna sesuai huruf) | BOBOT | TOTAL.
- Footer: TOTAL SKS DIAMBIL, TOTAL BOBOT NILAI, **Status Akademik** badge hijau `AKTIF / MEMUASKAN`.
- 2 sub-card: **Academic Notice** (text verifikasi) + **Timeline & Reminders** (Pengisian KRS, Kuliah Perdana).

### 6.6 📅 Mahasiswa — Weekly Schedule (`jadwal.php`)

Referensi: grid Senin–Jumat jam 08:00 / 10:00 / 13:00 / 15:00, kartu berwarna per kategori (Major, Elective, Minor, Research/Lab), sidebar Total Credits, Today's Lecturers, Next Assignment.

Komponen:
- Dropdown filter semester.
- Tombol **Download Schedule** (PDF).
- Grid kalender 5 hari × 5 slot jam. Kartu matkul diletakkan sesuai hari/jam (colspan jika lebih dari 1 jam).
- Warna kartu berdasarkan `mata_kuliah.jenis` + tipe (research/lab).
- Sidebar kanan:
  - **TOTAL CREDITS** (card navy) + badge "Full Load".
  - **Today's Lecturers** (2–3 dosen dengan mata kuliah hari ini).
  - **Next Assignment** (opsional fase 2).
  - **Campus Map** thumbnail (statis).

### 6.7 👨‍🏫 Dosen — Dashboard

Referensi: 3 metric card (Courses, Students, Grading), card navy "Grading Period Active" dengan CTA, "Jadwal Mengajar Hari Ini", "Mata Kuliah Aktif" progress enrollment, footer Campus Info.

Komponen:
- 3 metric: jumlah mata kuliah diampu, total mahasiswa di kelas-kelasnya, jumlah grading berjalan.
- Card **Grading Period Active**: countdown + tombol "Input Nilai Sekarang".
- **Jadwal Mengajar Hari Ini**: list matkul dengan jam, ruang, SKS, tombol QR absensi.
- **Mata Kuliah Aktif**: list matkul + progress `{n}/{kuota} students` + 2 tombol (Daftar Mahasiswa, Input Nilai).

### 6.8 ✏️ Dosen — Input Nilai (`input_nilai.php`)

Referensi: header "Input Nilai: Pemrograman Web (INF301)", dropdown Class Selection, 3 stat, card navy "Deadline Submission", search + tombol Export XLS / Import Bulk, tabel nilai.

Komponen:
- Dropdown **Class Selection**: pilih kelas (berdasarkan jadwal yang diampu).
- 3 stat: `TOTAL ENROLLED`, `GRADED`, `AVG. SCORE`. Avatar stack.
- Card navy **Deadline Submission**: "14 Days Remaining · Submit by July 20, 2024".
- Input search **Filter by Name or NIM**.
- Tombol **Export XLS**, **Import Bulk** (upload csv, opsional fase 2).
- Tabel kolom: NIM, NAMA MAHASISWA (+ kelas), `TUGAS (20%)`, `UTS (30%)`, `UAS (50%)`, `NILAI AKHIR` (auto-hitung), `HURUF` (badge warna), `STATUS` (Saved / Unsaved).
- Footer info **Pedoman Penilaian**.
- Tombol floating **"Simpan Perubahan"** (navy) di kanan bawah.
- Behavior: edit angka → nilai akhir & huruf dihitung JavaScript realtime. Simpan → AJAX ke `api/nilai_save.php`.
- Jika `status_kunci = 1` → input readonly.

### 6.9 🛠 Admin — Dashboard

Referensi: 4 card stat (Mahasiswa, Dosen, Matkul, KRS Validation), 3 Quick Access, panel Academic Semester, Recent Activity Log, Faculty Distribution, System Status.

Komponen:
- 4 stat dengan mini trend: Total Mahasiswa (+12% vs LY), Total Dosen (Stable), Total Matkul (+4 New), **KRS Validation** card navy dengan progress % semester aktif.
- **Quick Access Management**: Manajemen Mahasiswa, Manajemen Dosen, Pengumuman.
- Card **Academic Semester**: nama semester aktif, countdown UAS, Judicium Deadline.
- **Recent Activity Log**: list 3 log terakhir (login admin, update matkul, batch import).
- **Faculty Distribution**: bar Engineering / Economics / Humanities.
- **System Status**: Server Load, Database.

### 6.10 🗂 Admin — Manajemen Data Mahasiswa

Komponen:
- Breadcrumb `Master Data > Mahasiswa`.
- Tombol **"+ Tambah Mahasiswa"** (navy, kanan atas).
- 4 stat: Total, Aktif Akademik, Cuti/Non-Aktif, Lulus Tahun Ini.
- Tabel: FOTO, NIM, NAMA, PROGRAM STUDI, ANGKATAN, STATUS (badge), AKSI (edit/hapus).
- Pagination klasik `1 · 2 · 3 ... 482`.
- Search bar di header.

Sub-halaman CRUD `tambah.php`, `edit.php`, `hapus.php` (konfirmasi modal/JS confirm).
Pola serupa untuk **Dosen**, **Mata Kuliah**, **Semester**, **Jadwal**.

### 6.11 📚 Admin — Course Enrollment Monitoring

Referensi wireframe "Course Enrollment" dengan kartu-kartu matkul (grid) + filter departemen + schedule filter + enrollment status. Untuk admin ini mode read-only untuk monitoring seluruh matkul yang ditawarkan + jumlah terdaftar.

---

## 7. Flow Utama

### 7.1 Flow Login → Dashboard
```
user buka /  →  redirect ke /auth/login.php
user pilih role + isi form  →  POST /auth/login.php
valid → $_SESSION terisi → redirect ke /{role}/dashboard.php
invalid → render ulang login dengan flash error
```

### 7.2 Flow Pengisian KRS
```
Mahasiswa buka /mahasiswa/krs.php
→ sistem load max_sks dari IPK semester lalu
→ tampilkan seluruh jadwal_kuliah pada semester aktif
→ user centang matkul → JS update counter SKS + distribusi realtime + cek bentrok client
→ klik "Simpan KRS" → AJAX POST /api/krs_save.php
→ server validasi (SKS, bentrok, kuota, prasyarat) dalam TRANSAKSI
→ sukses → flash success + redirect; gagal → alert + highlight baris error
```

### 7.3 Flow Input Nilai
```
Dosen pilih kelas → load list mahasiswa + nilai existing
→ edit kolom tugas/uts/uas → JS hitung nilai_angka & nilai_huruf
→ klik "Simpan Perubahan" → AJAX batch ke /api/nilai_save.php
→ setelah final, admin/dosen set status_kunci=1 → input jadi readonly
→ data muncul di KHS mahasiswa setelah dikunci
```

---

## 8. Halaman & Routing (tanpa mod_rewrite)

| URL | File | Role |
|-----|------|------|
| `/` | `index.php` → redirect `/auth/login.php` | public |
| `/auth/login.php` | Login | public |
| `/auth/logout.php` | Destroy session | any logged in |
| `/mahasiswa/dashboard.php` | Dashboard mahasiswa | mahasiswa |
| `/mahasiswa/profil.php` | Academic Profile | mahasiswa |
| `/mahasiswa/krs.php` | Pengisian KRS | mahasiswa |
| `/mahasiswa/khs.php` | Kartu Hasil Studi | mahasiswa |
| `/mahasiswa/jadwal.php` | Weekly Schedule | mahasiswa |
| `/dosen/dashboard.php` | Dashboard dosen | dosen |
| `/dosen/daftar_mahasiswa.php?id_jadwal=` | Daftar Mahasiswa kelas | dosen |
| `/dosen/input_nilai.php?id_jadwal=` | Input Nilai | dosen |
| `/dosen/jadwal.php` | Jadwal mengajar | dosen |
| `/admin/dashboard.php` | Dashboard admin | admin |
| `/admin/mahasiswa/index.php` | List CRUD | admin |
| `/admin/mahasiswa/tambah.php` | Form tambah | admin |
| `/admin/mahasiswa/edit.php?nim=` | Form edit | admin |
| `/admin/mahasiswa/hapus.php?nim=` | Soft delete / hapus | admin |
| (idem utk dosen, matkul, semester, jadwal) | | admin |

---

## 9. Design System (The Gallery)

### 9.1 Palette
```
--navy-900:   #0B1E4F   /* primary, hero card */
--navy-700:   #1C3578
--navy-500:   #2A4A9E
--white:      #FFFFFF
--bg:         #F5F6FA   /* page background */
--card:       #FFFFFF
--border:     #E4E7EE
--text-900:   #0B1E4F
--text-700:   #2C3A59
--text-500:   #6B7489
--success:    #2BB673
--warning:    #F4B43C
--danger:     #E04F5F
--info-bg:    #FFF4DC   /* banner kuning system validation */
--badge-A:    #2BB673
--badge-B:    #F4B43C
--badge-C:    #F39E45
--badge-D:    #E08B5F
--badge-E:    #E04F5F
```

### 9.2 Typography
- Font: **Inter**, fallback `system-ui, sans-serif`.
- H1 page title: 28–32px, weight 700, color navy-900.
- H2 section: 18–20px, 600.
- Body: 14–15px, 400–500.
- Uppercase kecil (label di card, mis. `STUDENT GPA`): 11px, letter-spacing 0.08em, color text-500.

### 9.3 Komponen UI
- **Card**: radius 16px, shadow halus `0 2px 10px rgba(11,30,79,0.06)`, padding 20–24px.
- **Hero card navy**: background `--navy-900`, teks putih, radius 16px.
- **Metric card**: angka besar (28–32px, bold, navy), label kecil di atas.
- **Badge status**: radius pil, padding 2–8px, warna sesuai palette.
- **Button primary**: navy fill, teks putih, radius 12px, padding 10–16px. Ikon kanan (arrow / save).
- **Button ghost**: border 1px navy, teks navy, transparent.
- **Sidebar**: lebar 240px, background putih, item aktif background soft-navy + teks navy-900.
- **Progress bar**: height 6px, radius 6px, fill navy-500 on track `#E4E7EE`.

### 9.4 Layout
- Container max-width 1280px.
- Sidebar kiri fixed (240px) + topbar (search + notif + profile) + main area.
- Responsive breakpoints: `md: 768px`, `lg: 1024px`. Mobile → sidebar drawer.

---

## 10. Authentication & Security

1. **Password** disimpan sebagai `password_hash($pw, PASSWORD_DEFAULT)`.
2. **Login** verifikasi dengan `password_verify()`.
3. **Session** dimulai di `includes/auth.php` dengan `session_start()` + regenerasi id setelah login sukses: `session_regenerate_id(true)`.
4. **CSRF**: setiap form POST menyertakan token `<input type="hidden" name="_csrf" value="<?= csrf_token() ?>">`. Server verifikasi.
5. **XSS**: semua output dinamis via `htmlspecialchars($x, ENT_QUOTES, 'UTF-8')`.
6. **SQLi**: wajib PDO prepared statements. Dilarang konkatenasi string ke SQL.
7. **Authorization**: `require_role(['mahasiswa'])` di tiap halaman. Jika role tidak cocok → redirect ke login dengan flash error.
8. **File upload** (foto mahasiswa, import CSV nilai): validasi mime type + extension + ukuran maks 2 MB, simpan di `/public/assets/img/uploads/` dengan nama di-hash.

---

## 11. Development Phases

| Fase | Deliverable | Estimasi |
|------|-------------|----------|
| 0. Setup | Repo, struktur folder, `config.php`, `db.php`, `schema.sql`, seed data | 0.5 hari |
| 1. Auth | Login multi-role, logout, proteksi halaman | 0.5 hari |
| 2. Layout & Design | Header, sidebar, footer, CSS global mengikuti palette | 1 hari |
| 3. Mahasiswa Core | Dashboard, Profil, KHS, Jadwal | 1.5 hari |
| 4. Pengisian KRS | Halaman KRS + validasi SKS, bentrok, kuota | 1.5 hari |
| 5. Dosen | Dashboard, Input Nilai (dengan perhitungan otomatis) | 1.5 hari |
| 6. Admin | Dashboard + CRUD Mahasiswa/Dosen/Matkul/Semester/Jadwal | 2 hari |
| 7. Polish | Print KHS PDF, export XLS, pengumuman, notifikasi, dokumentasi README | 1 hari |
| 8. Testing | Manual test per role + fix bug | 0.5 hari |
| **Total** | | **~10 hari kerja** |

---

## 12. Coding Conventions

- **Nama file**: `snake_case.php`.
- **Nama fungsi PHP**: `snake_case()`; **nama class** (jika ada): `PascalCase`.
- **Nama tabel**: singular snake_case. **Nama kolom**: snake_case.
- **Indentasi**: 4 spasi.
- **PHP echo pendek**: `<?= ... ?>` di template. Logika tetap di atas (PHP heredoc / pemisahan jelas).
- **Query**:
  ```php
  $stmt = $pdo->prepare("SELECT * FROM krs WHERE id_mahasiswa = ?");
  $stmt->execute([$nim]);
  $rows = $stmt->fetchAll();
  ```
- **Komentar** bahasa Indonesia untuk logic bisnis, bahasa Inggris untuk util teknis.
- **Commit message** (jika pakai git): `feat(mahasiswa): pengisian krs + validasi sks`.

---

## 13. Acceptance Criteria

### Global
- [ ] Login 3 role berhasil, session terpisah, logout menghancurkan session.
- [ ] Semua halaman diproteksi sesuai role; akses tanpa login → redirect login.
- [ ] Tidak ada query rentan SQLi/XSS (dibuktikan dengan kode menggunakan PDO prepared + `htmlspecialchars`).
- [ ] Tampilan konsisten dengan design system (navy + white, Inter, card radius 16).

### Mahasiswa
- [ ] Dashboard menampilkan IPK, SKS, semester aktif dari DB nyata.
- [ ] KRS: counter SKS realtime, cegah bentrok hari+jam, cegah lebih dari max_sks, cegah full kuota.
- [ ] KRS tersimpan ke tabel `krs` hanya jika semua validasi lulus.
- [ ] KHS menampilkan nilai hanya untuk KRS yang sudah di-input dosen dan `status_kunci=1`.
- [ ] Jadwal mingguan menampilkan matkul terpilih di semester aktif dengan posisi sel yang benar.

### Dosen
- [ ] Dashboard menampilkan matkul yang diampu & jumlah mahasiswa.
- [ ] Input Nilai menghitung otomatis nilai_angka & huruf dengan bobot 20/30/50.
- [ ] Tombol "Simpan" menyimpan ke tabel `nilai`; data terkunci tidak bisa diedit.

### Admin
- [ ] CRUD Mahasiswa, Dosen, Matkul, Semester, Jadwal berjalan penuh.
- [ ] Dashboard menampilkan total entitas sesuai DB.
- [ ] Hanya 1 semester bisa berstatus `aktif`.

---

## 14. README (Cara Menjalankan — ringkas)

```bash
# 1) Buat database
mysql -u root -p < database/schema.sql
mysql -u root -p web_krs < database/seed.sql

# 2) Atur kredensial
cp includes/config.sample.php includes/config.php
# edit DB_HOST, DB_NAME, DB_USER, DB_PASS, APP_URL

# 3) Jalankan
# Opsi A: XAMPP — copy folder ke htdocs/ lalu buka http://localhost/web-krs-mahasiswa/public/
# Opsi B: PHP built-in server
php -S localhost:8000 -t public

# 4) Akun demo (dari seed)
Mahasiswa: 21010023 / password123
Dosen    : 198504122010 / password123
Admin    : admin / admin123
```

---

## 15. Panduan untuk Claude (saat coding)

Ketika diminta membuat halaman atau fitur, Claude **wajib**:

1. Membaca kembali bagian terkait (ERD, wireframe, acceptance) di file ini sebelum menulis kode.
2. Menulis kode mengikuti **Struktur Folder (bagian 3)** dan **Coding Conventions (bagian 12)**.
3. Menempatkan seluruh query di PDO prepared statement.
4. Mengikuti **Design System (bagian 9)** untuk setiap elemen UI.
5. Menyertakan `require_role([...])` pada tiap halaman yang butuh autentikasi.
6. Setelah selesai, memberi ringkasan: file apa saja yang dibuat/diubah dan bagaimana cara mengujinya.

Jika ada ambiguitas (mis. data dummy belum tersedia), tanyakan ke user sebelum menebak.

---

_Disusun pada 2026-04-24 untuk proyek **web-krs-mahasiswa**._
