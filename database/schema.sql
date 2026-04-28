-- SIAKAD Gallery Database Schema
-- Sistem Informasi Kartu Rencana Studi (KRS) Mahasiswa
-- Created: 2026-04-24

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET COLLATION_CONNECTION = utf8mb4_unicode_ci;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `nilai`;
DROP TABLE IF EXISTS `krs`;
DROP TABLE IF EXISTS `jadwal_kuliah`;
DROP TABLE IF EXISTS `mata_kuliah`;
DROP TABLE IF EXISTS `semester`;
DROP TABLE IF EXISTS `dosen`;
DROP TABLE IF EXISTS `mahasiswa`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `pengumuman`;

-- =====================================================
-- Table: admin
-- =====================================================
CREATE TABLE `admin` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: mahasiswa
-- =====================================================
CREATE TABLE `mahasiswa` (
  `nim` VARCHAR(15) PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `angkatan` YEAR NOT NULL,
  `program_studi` VARCHAR(60) NOT NULL,
  `status` ENUM('aktif','cuti','lulus') DEFAULT 'aktif',
  `foto` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: dosen
-- =====================================================
CREATE TABLE `dosen` (
  `nidn` VARCHAR(15) PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `jurusan` VARCHAR(60) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: mata_kuliah
-- =====================================================
CREATE TABLE `mata_kuliah` (
  `id_matkul` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_matkul` VARCHAR(10) NOT NULL UNIQUE,
  `nama_matkul` VARCHAR(100) NOT NULL,
  `sks` TINYINT NOT NULL,
  `semester` TINYINT NOT NULL,
  `jenis` ENUM('wajib','pilihan') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_kode` (`kode_matkul`),
  KEY `idx_semester` (`semester`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: semester
-- =====================================================
CREATE TABLE `semester` (
  `id_semester` INT AUTO_INCREMENT PRIMARY KEY,
  `tahun_ajaran` VARCHAR(9) NOT NULL,
  `tingkatan_semester` ENUM('ganjil','genap') NOT NULL,
  `status` ENUM('aktif','nonaktif') DEFAULT 'nonaktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_semester` (`tahun_ajaran`, `tingkatan_semester`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: jadwal_kuliah
-- =====================================================
CREATE TABLE `jadwal_kuliah` (
  `id_jadwal` INT AUTO_INCREMENT PRIMARY KEY,
  `id_matkul` INT NOT NULL,
  `id_dosen` VARCHAR(15) NOT NULL,
  `id_semester` INT NOT NULL,
  `hari` ENUM('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  `ruang` VARCHAR(30) NOT NULL,
  `kuota` SMALLINT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_matkul` (`id_matkul`),
  KEY `idx_dosen` (`id_dosen`),
  KEY `idx_semester` (`id_semester`),
  KEY `idx_jadwal` (`hari`, `jam_mulai`),
  CONSTRAINT `fk_jadwal_matkul` FOREIGN KEY (`id_matkul`) REFERENCES `mata_kuliah` (`id_matkul`) ON DELETE CASCADE,
  CONSTRAINT `fk_jadwal_dosen` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`nidn`) ON DELETE CASCADE,
  CONSTRAINT `fk_jadwal_semester` FOREIGN KEY (`id_semester`) REFERENCES `semester` (`id_semester`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: krs (Kartu Rencana Studi)
-- =====================================================
CREATE TABLE `krs` (
  `id_krs` INT AUTO_INCREMENT PRIMARY KEY,
  `id_mahasiswa` VARCHAR(15) NOT NULL,
  `id_jadwal` INT NOT NULL,
  `tanggal_ambil` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_krs` (`id_mahasiswa`, `id_jadwal`),
  KEY `idx_mahasiswa` (`id_mahasiswa`),
  KEY `idx_jadwal` (`id_jadwal`),
  CONSTRAINT `fk_krs_mahasiswa` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE,
  CONSTRAINT `fk_krs_jadwal` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_kuliah` (`id_jadwal`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: nilai (Grade/Value)
-- =====================================================
CREATE TABLE `nilai` (
  `id_nilai` INT AUTO_INCREMENT PRIMARY KEY,
  `id_krs` INT NOT NULL UNIQUE,
  `tugas` DECIMAL(5,2),
  `uts` DECIMAL(5,2),
  `uas` DECIMAL(5,2),
  `nilai_angka` DECIMAL(5,2),
  `nilai_huruf` ENUM('A','B+','B','C+','C','D','E'),
  `status_kunci` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_krs` (`id_krs`),
  CONSTRAINT `fk_nilai_krs` FOREIGN KEY (`id_krs`) REFERENCES `krs` (`id_krs`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: pengumuman (Announcements)
-- =====================================================
CREATE TABLE `pengumuman` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(200) NOT NULL,
  `isi` TEXT,
  `tipe` ENUM('ACADEMIC','SYSTEM','EVENT') DEFAULT 'ACADEMIC',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_tipe` (`tipe`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for common queries
CREATE INDEX `idx_krs_mahasiswa_semester` ON `krs` (`id_mahasiswa`);
CREATE INDEX `idx_nilai_huruf` ON `nilai` (`nilai_huruf`);
CREATE INDEX `idx_jadwal_semester_hari` ON `jadwal_kuliah` (`id_semester`, `hari`);
