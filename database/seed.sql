-- SIAKAD Gallery - Seed Data
-- Realistic dummy data for testing and development
-- Created: 2026-04-24

SET NAMES utf8mb4;

-- =====================================================
-- Admin Data
-- =====================================================
INSERT INTO `admin` (`username`, `password`, `nama`) VALUES
('admin', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 'Administrator');

-- =====================================================
-- Dosen Data (5 dosen)
-- Password hash: $2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri (password123)
-- =====================================================
INSERT INTO `dosen` (`nidn`, `nama`, `email`, `password`, `jurusan`) VALUES
('198504122010', 'Dr. Ahmad Fauzi', 'ahmad@university.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 'Teknik Informatika'),
('197803052008', 'Dr. Sari Dewi', 'sari@university.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 'Sistem Informasi'),
('198210302011', 'Prof. Budi Santoso', 'budi.s@university.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 'Teknik Informatika'),
('199001152015', 'Dr. Rina Wahyuni', 'rina@university.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 'Sistem Informasi'),
('196912052000', 'Prof. Hendra Gunawan', 'hendra@university.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 'Teknik Informatika');

-- =====================================================
-- Mahasiswa Data (10 mahasiswa)
-- Password hash: $2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri (password123)
-- =====================================================
INSERT INTO `mahasiswa` (`nim`, `nama`, `email`, `password`, `angkatan`, `program_studi`, `status`) VALUES
('21010023', 'Budi Prasetyo', 'budi@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2021, 'Teknik Informatika', 'aktif'),
('21010045', 'Siti Rahayu', 'siti@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2021, 'Teknik Informatika', 'aktif'),
('22010012', 'Agus Salim', 'agus@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2022, 'Sistem Informasi', 'aktif'),
('22010034', 'Dewi Kusuma', 'dewi@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2022, 'Sistem Informasi', 'aktif'),
('20010078', 'Fajar Nugraha', 'fajar@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2020, 'Teknik Informatika', 'aktif'),
('20010056', 'Hani Putri', 'hani@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2020, 'Teknik Informatika', 'lulus'),
('21020067', 'Rizky Ramadan', 'rizky@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2021, 'Sistem Informasi', 'aktif'),
('22020089', 'Nurul Hidayah', 'nurul@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2022, 'Sistem Informasi', 'aktif'),
('23010001', 'Andi Firmansyah', 'andi@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2023, 'Teknik Informatika', 'aktif'),
('23010002', 'Citra Lestari', 'citra@student.ac.id', '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri', 2023, 'Teknik Informatika', 'cuti');

-- =====================================================
-- Mata Kuliah Data (15 matkul)
-- =====================================================
INSERT INTO `mata_kuliah` (`kode_matkul`, `nama_matkul`, `sks`, `semester`, `jenis`) VALUES
('TIK-301', 'Pemrograman Web', 3, 5, 'wajib'),
('TIK-302', 'Basis Data Lanjut', 3, 5, 'wajib'),
('TIK-303', 'Kecerdasan Buatan', 3, 5, 'wajib'),
('TIK-304', 'Jaringan Komputer', 3, 5, 'wajib'),
('TIK-305', 'Rekayasa Perangkat Lunak', 3, 5, 'wajib'),
('TIK-401', 'Keamanan Sistem', 3, 7, 'wajib'),
('TIK-402', 'Machine Learning', 3, 7, 'pilihan'),
('TIK-403', 'Cloud Computing', 3, 7, 'pilihan'),
('TIK-404', 'Pemrograman Mobile', 3, 7, 'pilihan'),
('TIK-405', 'Sistem Terdistribusi', 3, 7, 'wajib'),
('SIF-301', 'Analisis Sistem Informasi', 3, 5, 'wajib'),
('SIF-302', 'Enterprise Architecture', 3, 5, 'pilihan'),
('TIK-201', 'Struktur Data', 3, 3, 'wajib'),
('TIK-202', 'Algoritma Pemrograman', 3, 3, 'wajib'),
('TIK-106', 'Kalkulus', 2, 1, 'wajib');

-- =====================================================
-- Semester Data
-- =====================================================
INSERT INTO `semester` (`tahun_ajaran`, `tingkatan_semester`, `status`) VALUES
('2022/2023', 'ganjil', 'nonaktif'),
('2022/2023', 'genap', 'nonaktif'),
('2023/2024', 'ganjil', 'nonaktif'),
('2023/2024', 'genap', 'aktif');

-- =====================================================
-- Jadwal Kuliah - Semester 3 (for nilai calculation)
-- id_semester will be 1 (2022/2023 ganjil) - update after checking ID
-- For now we'll use semester ID 1 as reference
-- =====================================================
INSERT INTO `jadwal_kuliah` (`id_matkul`, `id_dosen`, `id_semester`, `hari`, `jam_mulai`, `jam_selesai`, `ruang`, `kuota`) VALUES
-- Semester 3 (2022/2023 Ganjil)
(13, '198504122010', 1, 'Senin', '08:00:00', '10:40:00', 'R.301', 35),
(14, '197803052008', 1, 'Selasa', '08:00:00', '10:40:00', 'R.302', 35);

-- =====================================================
-- Jadwal Kuliah - Semester Aktif (2023/2024 Genap - id_semester=4)
-- =====================================================
INSERT INTO `jadwal_kuliah` (`id_matkul`, `id_dosen`, `id_semester`, `hari`, `jam_mulai`, `jam_selesai`, `ruang`, `kuota`) VALUES
-- Current semester (2023/2024 Genap)
(1, '198504122010', 4, 'Senin', '08:00:00', '10:40:00', 'R.401', 35),
(2, '197803052008', 4, 'Selasa', '10:00:00', '12:40:00', 'Lab A', 30),
(3, '198210302011', 4, 'Rabu', '08:00:00', '10:40:00', 'R.402', 35),
(4, '199001152015', 4, 'Kamis', '13:00:00', '15:40:00', 'Lab B', 30),
(5, '196912052000', 4, 'Jumat', '08:00:00', '10:40:00', 'R.403', 35),
(6, '198504122010', 4, 'Senin', '13:00:00', '15:40:00', 'R.404', 30),
(7, '197803052008', 4, 'Rabu', '13:00:00', '15:40:00', 'R.401', 25),
(8, '198210302011', 4, 'Selasa', '13:00:00', '15:40:00', 'Lab C', 25),
(9, '199001152015', 4, 'Kamis', '08:00:00', '10:40:00', 'Lab A', 25),
(10, '196912052000', 4, 'Jumat', '13:00:00', '15:40:00', 'R.402', 30);

-- =====================================================
-- KRS Data - Previous Semester (Semester 3, for nilai calculation)
-- Mahasiswa 21010023 (Budi Prasetyo) - id_krs 1-2
-- =====================================================
INSERT INTO `krs` (`id_mahasiswa`, `id_jadwal`, `tanggal_ambil`) VALUES
('21010023', 11, '2022-09-05 10:30:00'),
('21010023', 12, '2022-09-05 10:35:00');

-- =====================================================
-- KRS Data - Current Semester (Semester 4 - aktif)
-- Mahasiswa 21010023 (Budi Prasetyo) - id_krs 3-6 (jadwal 15-18 which are ids 3-6 in current semester)
-- =====================================================
INSERT INTO `krs` (`id_mahasiswa`, `id_jadwal`, `tanggal_ambil`) VALUES
('21010023', 3, '2024-02-15 09:00:00'),
('21010023', 4, '2024-02-15 09:05:00'),
('21010023', 5, '2024-02-15 09:10:00'),
('21010023', 7, '2024-02-15 09:15:00');

-- =====================================================
-- Nilai Data - Previous Semester (status_kunci=1)
-- For id_krs 1-2 (Budi's semester 3 courses)
-- =====================================================
INSERT INTO `nilai` (`id_krs`, `tugas`, `uts`, `uas`, `nilai_angka`, `nilai_huruf`, `status_kunci`) VALUES
(1, 85.00, 80.00, 88.00, 86.50, 'A', 1),
(2, 78.00, 75.00, 80.00, 78.50, 'B+', 1);

-- =====================================================
-- Pengumuman (Announcements)
-- =====================================================
INSERT INTO `pengumuman` (`judul`, `isi`, `tipe`) VALUES
('Pengisian KRS Semester Genap 2023/2024 Dibuka', 'Sistem pengisian KRS sudah dibuka. Mahasiswa dapat melakukan pengisian KRS sampai dengan tanggal 28 Februari 2024.', 'ACADEMIC'),
('Jadwal UTS Semester Genap 2023/2024', 'Ujian Tengah Semester akan dilaksanakan pada bulan Maret 2024. Detail jadwal akan diumumkan lebih lanjut.', 'ACADEMIC'),
('Beasiswa Prestasi Akademik 2024 Dibuka', 'Pendaftaran beasiswa prestasi akademik tahun 2024 sudah dibuka. Silakan kunjungi portal beasiswa untuk informasi lebih lanjut.', 'EVENT');
