-- MySQL dump 10.13  Distrib 9.5.0, for macos15.7 (arm64)
--
-- Host: localhost    Database: web_krs
-- ------------------------------------------------------
-- Server version	9.5.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'b5ee1988-cf52-11f0-a33a-28f553bfc59e:1-17874';

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri','Administrator',NULL,NULL);
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dosen`
--

DROP TABLE IF EXISTS `dosen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dosen` (
  `nidn` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`nidn`),
  UNIQUE KEY `dosen_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dosen`
--

LOCK TABLES `dosen` WRITE;
/*!40000 ALTER TABLE `dosen` DISABLE KEYS */;
INSERT INTO `dosen` VALUES ('196912052000','Prof. Hendra Gunawan','hendra@university.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri','Teknik Informatika',NULL,NULL),('197803052008','Dr. Sari Dewi','sari@university.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri','Sistem Informasi',NULL,NULL),('198210302011','Prof. Budi Santoso','budi.s@university.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri','Teknik Informatika',NULL,NULL),('198504122010','Dr. Ahmad Fauzi','ahmad@university.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri','Teknik Informatika',NULL,NULL),('199001152015','Dr. Rina Wahyuni','rina@university.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri','Sistem Informasi',NULL,NULL);
/*!40000 ALTER TABLE `dosen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_kuliah`
--

DROP TABLE IF EXISTS `jadwal_kuliah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_kuliah` (
  `id_jadwal` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_matkul` bigint unsigned NOT NULL,
  `id_dosen` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_semester` bigint unsigned NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruang` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kuota` smallint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jadwal`),
  KEY `jadwal_kuliah_id_matkul_foreign` (`id_matkul`),
  KEY `jadwal_kuliah_id_dosen_foreign` (`id_dosen`),
  KEY `jadwal_kuliah_id_semester_foreign` (`id_semester`),
  CONSTRAINT `jadwal_kuliah_id_dosen_foreign` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`nidn`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_kuliah_id_matkul_foreign` FOREIGN KEY (`id_matkul`) REFERENCES `mata_kuliah` (`id_matkul`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_kuliah_id_semester_foreign` FOREIGN KEY (`id_semester`) REFERENCES `semester` (`id_semester`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_kuliah`
--

LOCK TABLES `jadwal_kuliah` WRITE;
/*!40000 ALTER TABLE `jadwal_kuliah` DISABLE KEYS */;
INSERT INTO `jadwal_kuliah` VALUES (1,13,'198504122010',1,'Senin','08:00:00','10:40:00','R.301',35,NULL,NULL),(2,14,'197803052008',1,'Selasa','08:00:00','10:40:00','R.302',35,NULL,NULL),(3,1,'198504122010',4,'Senin','08:00:00','10:40:00','R.401',35,NULL,NULL),(4,2,'197803052008',4,'Selasa','10:00:00','12:40:00','Lab A',30,NULL,NULL),(5,3,'198210302011',4,'Rabu','08:00:00','10:40:00','R.402',35,NULL,NULL),(6,4,'199001152015',4,'Kamis','13:00:00','15:40:00','Lab B',30,NULL,NULL),(7,5,'196912052000',4,'Jumat','08:00:00','10:40:00','R.403',35,NULL,NULL),(8,6,'198504122010',4,'Senin','13:00:00','15:40:00','R.404',30,NULL,NULL),(9,7,'197803052008',4,'Rabu','13:00:00','15:40:00','R.401',25,NULL,NULL),(10,8,'198210302011',4,'Selasa','13:00:00','15:40:00','Lab C',25,NULL,NULL),(11,9,'199001152015',4,'Kamis','08:00:00','10:40:00','Lab A',25,NULL,NULL),(12,10,'196912052000',4,'Jumat','13:00:00','15:40:00','R.402',30,NULL,NULL),(13,1,'198504122010',2,'Senin','08:00:00','10:40:00','R.301',35,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(14,2,'197803052008',2,'Selasa','10:00:00','12:40:00','Lab A',30,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(15,3,'198210302011',2,'Rabu','08:00:00','10:40:00','R.302',35,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(16,11,'199001152015',2,'Kamis','13:00:00','15:40:00','R.303',30,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(17,4,'196912052000',2,'Jumat','08:00:00','10:40:00','R.304',30,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(18,5,'196912052000',3,'Senin','08:00:00','10:40:00','R.401',35,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(19,6,'198504122010',3,'Selasa','13:00:00','15:40:00','R.402',30,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(20,7,'197803052008',3,'Rabu','08:00:00','10:40:00','R.403',25,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(21,15,'198210302011',3,'Kamis','08:00:00','10:40:00','R.301',40,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(22,13,'199001152015',3,'Jumat','08:00:00','10:40:00','R.302',35,'2026-05-30 05:17:17','2026-05-30 05:17:17'),(23,16,'198210302011',4,'Senin','08:00:00','10:30:00','Lab A',40,NULL,NULL),(24,17,'196912052000',4,'Selasa','10:00:00','12:30:00','R.201',40,NULL,NULL),(25,18,'199001152015',4,'Rabu','08:00:00','09:40:00','R.202',40,NULL,NULL),(26,19,'197803052008',4,'Kamis','13:00:00','15:30:00','Lab B',40,NULL,NULL),(27,20,'198504122010',4,'Jumat','08:00:00','10:30:00','R.203',40,NULL,NULL),(28,21,'199001152015',4,'Jumat','13:00:00','14:40:00','R.301',40,NULL,NULL);
/*!40000 ALTER TABLE `jadwal_kuliah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `krs`
--

DROP TABLE IF EXISTS `krs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `krs` (
  `id_krs` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_mahasiswa` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_jadwal` bigint unsigned NOT NULL,
  `tanggal_ambil` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_krs`),
  UNIQUE KEY `krs_id_mahasiswa_id_jadwal_unique` (`id_mahasiswa`,`id_jadwal`),
  KEY `krs_id_jadwal_foreign` (`id_jadwal`),
  CONSTRAINT `krs_id_jadwal_foreign` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_kuliah` (`id_jadwal`) ON DELETE CASCADE,
  CONSTRAINT `krs_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `krs`
--

LOCK TABLES `krs` WRITE;
/*!40000 ALTER TABLE `krs` DISABLE KEYS */;
INSERT INTO `krs` VALUES (1,'21010023',1,'2022-09-05 10:30:00',NULL,NULL),(2,'21010023',2,'2022-09-05 10:35:00',NULL,NULL),(3,'21010023',3,'2024-02-15 09:00:00',NULL,NULL),(4,'21010023',4,'2024-02-15 09:05:00',NULL,NULL),(5,'21010023',5,'2024-02-15 09:10:00',NULL,NULL),(6,'21010023',7,'2024-02-15 09:15:00',NULL,NULL),(7,'20010056',1,'2022-09-01 08:00:00',NULL,NULL),(8,'20010056',2,'2022-09-01 08:00:00',NULL,NULL),(9,'20010078',1,'2022-09-01 08:30:00',NULL,NULL),(10,'20010078',2,'2022-09-01 08:30:00',NULL,NULL),(11,'21010045',1,'2022-09-01 09:00:00',NULL,NULL),(12,'21010045',2,'2022-09-01 09:00:00',NULL,NULL),(13,'21020067',1,'2022-09-01 09:30:00',NULL,NULL),(14,'21020067',2,'2022-09-01 09:30:00',NULL,NULL),(15,'20010056',13,'2023-02-01 08:00:00',NULL,NULL),(16,'20010056',14,'2023-02-01 08:00:00',NULL,NULL),(17,'20010056',15,'2023-02-01 08:00:00',NULL,NULL),(18,'20010056',16,'2023-02-01 08:00:00',NULL,NULL),(19,'20010056',17,'2023-02-01 08:00:00',NULL,NULL),(20,'20010078',13,'2023-02-01 08:30:00',NULL,NULL),(21,'20010078',14,'2023-02-01 08:30:00',NULL,NULL),(22,'20010078',15,'2023-02-01 08:30:00',NULL,NULL),(23,'20010078',17,'2023-02-01 08:30:00',NULL,NULL),(24,'21010023',13,'2023-02-01 09:00:00',NULL,NULL),(25,'21010023',14,'2023-02-01 09:00:00',NULL,NULL),(26,'21010023',15,'2023-02-01 09:00:00',NULL,NULL),(27,'21010023',16,'2023-02-01 09:00:00',NULL,NULL),(28,'21010045',13,'2023-02-01 09:30:00',NULL,NULL),(29,'21010045',14,'2023-02-01 09:30:00',NULL,NULL),(30,'21010045',15,'2023-02-01 09:30:00',NULL,NULL),(31,'21020067',13,'2023-02-01 10:00:00',NULL,NULL),(32,'21020067',14,'2023-02-01 10:00:00',NULL,NULL),(33,'21020067',16,'2023-02-01 10:00:00',NULL,NULL),(34,'20010056',18,'2023-09-01 08:00:00',NULL,NULL),(35,'20010056',19,'2023-09-01 08:00:00',NULL,NULL),(36,'20010056',20,'2023-09-01 08:00:00',NULL,NULL),(37,'20010056',21,'2023-09-01 08:00:00',NULL,NULL),(38,'20010056',22,'2023-09-01 08:00:00',NULL,NULL),(39,'20010078',18,'2023-09-01 08:30:00',NULL,NULL),(40,'20010078',19,'2023-09-01 08:30:00',NULL,NULL),(41,'20010078',20,'2023-09-01 08:30:00',NULL,NULL),(42,'20010078',21,'2023-09-01 08:30:00',NULL,NULL),(43,'21010023',18,'2023-09-01 09:00:00',NULL,NULL),(44,'21010023',19,'2023-09-01 09:00:00',NULL,NULL),(45,'21010023',20,'2023-09-01 09:00:00',NULL,NULL),(46,'21010023',22,'2023-09-01 09:00:00',NULL,NULL),(47,'22010012',21,'2023-09-01 09:30:00',NULL,NULL),(48,'22010012',22,'2023-09-01 09:30:00',NULL,NULL),(49,'22010034',21,'2023-09-01 10:00:00',NULL,NULL),(50,'22010034',22,'2023-09-01 10:00:00',NULL,NULL),(51,'22020089',21,'2023-09-01 10:30:00',NULL,NULL),(52,'22020089',22,'2023-09-01 10:30:00',NULL,NULL),(53,'20010056',8,'2024-02-01 08:00:00',NULL,NULL),(54,'20010056',9,'2024-02-01 08:00:00',NULL,NULL),(55,'20010056',10,'2024-02-01 08:00:00',NULL,NULL),(56,'20010056',11,'2024-02-01 08:00:00',NULL,NULL),(57,'20010056',12,'2024-02-01 08:00:00',NULL,NULL),(58,'20010078',8,'2024-02-01 08:30:00',NULL,NULL),(59,'20010078',9,'2024-02-01 08:30:00',NULL,NULL),(60,'20010078',10,'2024-02-01 08:30:00',NULL,NULL),(61,'20010078',12,'2024-02-01 08:30:00',NULL,NULL),(62,'21010045',3,'2024-02-01 09:00:00',NULL,NULL),(63,'21010045',4,'2024-02-01 09:00:00',NULL,NULL),(64,'21010045',5,'2024-02-01 09:00:00',NULL,NULL),(65,'21010045',6,'2024-02-01 09:00:00',NULL,NULL),(66,'21020067',3,'2024-02-01 09:30:00',NULL,NULL),(67,'21020067',4,'2024-02-01 09:30:00',NULL,NULL),(68,'21020067',11,'2024-02-01 09:30:00',NULL,NULL),(69,'21020067',12,'2024-02-01 09:30:00',NULL,NULL),(70,'22010012',3,'2024-02-01 10:00:00',NULL,NULL),(71,'22010012',4,'2024-02-01 10:00:00',NULL,NULL),(72,'22010012',5,'2024-02-01 10:00:00',NULL,NULL),(73,'22010034',3,'2024-02-01 10:30:00',NULL,NULL),(74,'22010034',4,'2024-02-01 10:30:00',NULL,NULL),(75,'22010034',6,'2024-02-01 10:30:00',NULL,NULL),(76,'22020089',3,'2024-02-01 11:00:00',NULL,NULL),(77,'22020089',5,'2024-02-01 11:00:00',NULL,NULL),(78,'22020089',6,'2024-02-01 11:00:00',NULL,NULL),(79,'23010001',3,'2024-02-01 11:30:00',NULL,NULL),(80,'23010001',4,'2024-02-01 11:30:00',NULL,NULL),(81,'23010002',3,'2024-02-01 12:00:00',NULL,NULL),(82,'23010002',5,'2024-02-01 12:00:00',NULL,NULL),(83,'21010023',6,'2026-05-30 08:21:19','2026-05-30 01:21:19','2026-05-30 01:21:19'),(84,'21010023',11,'2026-05-30 09:33:39','2026-05-30 02:33:39','2026-05-30 02:33:39'),(87,'21010023',8,'2026-05-30 09:53:44','2026-05-30 02:53:44','2026-05-30 02:53:44');
/*!40000 ALTER TABLE `krs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mahasiswa`
--

DROP TABLE IF EXISTS `mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahasiswa` (
  `nim` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan` year NOT NULL,
  `program_studi` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aktif','cuti','lulus') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`nim`),
  UNIQUE KEY `mahasiswa_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahasiswa`
--

LOCK TABLES `mahasiswa` WRITE;
/*!40000 ALTER TABLE `mahasiswa` DISABLE KEYS */;
INSERT INTO `mahasiswa` VALUES ('20010056','Hani Putri','hani@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2020,'Teknik Informatika','lulus',NULL,NULL,NULL),('20010078','Fajar Nugraha','fajar@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2020,'Teknik Informatika','aktif',NULL,NULL,NULL),('21010023','Budi Prasetyo','budi@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2023,'Teknik Informatika','aktif',NULL,NULL,NULL),('21010045','Siti Rahayu','siti@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2021,'Teknik Informatika','aktif',NULL,NULL,NULL),('21020067','Rizky Ramadan','rizky@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2021,'Sistem Informasi','aktif',NULL,NULL,NULL),('22010012','Agus Salim','agus@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2022,'Sistem Informasi','aktif',NULL,NULL,NULL),('22010034','Dewi Kusuma','dewi@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2022,'Sistem Informasi','aktif',NULL,NULL,NULL),('22020089','Nurul Hidayah','nurul@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2022,'Sistem Informasi','aktif',NULL,NULL,NULL),('23010001','Andi Firmansyah','andi@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2023,'Teknik Informatika','aktif',NULL,NULL,NULL),('23010002','Citra Lestari','citra@student.ac.id','$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri',2023,'Teknik Informatika','cuti',NULL,NULL,NULL);
/*!40000 ALTER TABLE `mahasiswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mata_kuliah`
--

DROP TABLE IF EXISTS `mata_kuliah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mata_kuliah` (
  `id_matkul` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_matkul` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_matkul` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sks` tinyint NOT NULL,
  `semester` tinyint NOT NULL,
  `jenis` enum('wajib','pilihan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_matkul`),
  UNIQUE KEY `mata_kuliah_kode_matkul_unique` (`kode_matkul`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mata_kuliah`
--

LOCK TABLES `mata_kuliah` WRITE;
/*!40000 ALTER TABLE `mata_kuliah` DISABLE KEYS */;
INSERT INTO `mata_kuliah` VALUES (1,'TIK-301','Pemrograman Web',3,5,'wajib',NULL,NULL),(2,'TIK-302','Basis Data Lanjut',3,5,'wajib',NULL,NULL),(3,'TIK-303','Kecerdasan Buatan',3,5,'wajib',NULL,NULL),(4,'TIK-304','Jaringan Komputer',3,5,'wajib',NULL,NULL),(5,'TIK-305','Rekayasa Perangkat Lunak',3,5,'wajib',NULL,NULL),(6,'TIK-401','Keamanan Sistem',3,7,'wajib',NULL,NULL),(7,'TIK-402','Machine Learning',3,7,'pilihan',NULL,NULL),(8,'TIK-403','Cloud Computing',3,7,'pilihan',NULL,NULL),(9,'TIK-404','Pemrograman Mobile',3,7,'pilihan',NULL,NULL),(10,'TIK-405','Sistem Terdistribusi',3,7,'wajib',NULL,NULL),(11,'SIF-301','Analisis Sistem Informasi',3,5,'wajib',NULL,NULL),(12,'SIF-302','Enterprise Architecture',3,5,'pilihan',NULL,NULL),(13,'TIK-201','Struktur Data',3,3,'wajib',NULL,NULL),(14,'TIK-202','Algoritma Pemrograman',3,3,'wajib',NULL,NULL),(15,'TIK-106','Kalkulus',2,1,'wajib',NULL,NULL),(16,'TIK-102','Pemrograman Dasar',3,2,'wajib',NULL,NULL),(17,'TIK-103','Matematika Diskrit',3,2,'wajib',NULL,NULL),(18,'TIK-104','Logika Informatika',2,2,'wajib',NULL,NULL),(19,'TIK-105','Pengantar Basis Data',3,2,'wajib',NULL,NULL),(20,'TIK-107','Organisasi & Arsitektur Komputer',3,2,'wajib',NULL,NULL),(21,'TIK-108','Bahasa Inggris Teknik',2,2,'pilihan',NULL,NULL);
/*!40000 ALTER TABLE `mata_kuliah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2024_01_01_000001_create_admin_table',1),(3,'2024_01_01_000002_create_mahasiswa_table',1),(4,'2024_01_01_000003_create_dosen_table',1),(5,'2024_01_01_000004_create_mata_kuliah_table',1),(6,'2024_01_01_000005_create_semester_table',1),(7,'2024_01_01_000006_create_jadwal_kuliah_table',1),(8,'2024_01_01_000007_create_krs_table',1),(9,'2024_01_01_000008_create_nilai_table',1),(10,'2024_01_01_000009_create_pengumuman_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nilai`
--

DROP TABLE IF EXISTS `nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai` (
  `id_nilai` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_krs` bigint unsigned NOT NULL,
  `tugas` decimal(5,2) DEFAULT NULL,
  `uts` decimal(5,2) DEFAULT NULL,
  `uas` decimal(5,2) DEFAULT NULL,
  `nilai_angka` decimal(5,2) DEFAULT NULL,
  `nilai_huruf` enum('A','B+','B','C+','C','D','E') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_kunci` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_nilai`),
  UNIQUE KEY `nilai_id_krs_unique` (`id_krs`),
  CONSTRAINT `nilai_id_krs_foreign` FOREIGN KEY (`id_krs`) REFERENCES `krs` (`id_krs`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nilai`
--

LOCK TABLES `nilai` WRITE;
/*!40000 ALTER TABLE `nilai` DISABLE KEYS */;
INSERT INTO `nilai` VALUES (1,1,85.00,80.00,88.00,86.50,'A',1,NULL,NULL),(2,2,78.00,75.00,80.00,78.50,'B+',1,NULL,NULL),(3,7,90.00,88.00,92.00,90.40,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(4,8,82.00,78.00,85.00,82.30,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(5,9,75.00,70.00,78.00,75.00,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(6,10,68.00,65.00,72.00,69.10,'B',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(7,11,88.00,85.00,90.00,88.10,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(8,12,79.00,76.00,82.00,79.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(9,13,72.00,68.00,75.00,72.30,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(10,14,65.00,60.00,68.00,65.00,'B',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(18,15,92.00,89.00,94.00,92.10,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(19,16,85.00,82.00,88.00,85.60,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(20,17,80.00,77.00,83.00,80.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(21,18,88.00,84.00,90.00,87.80,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(22,19,78.00,75.00,80.00,78.10,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(23,20,70.00,68.00,75.00,71.90,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(24,21,72.00,70.00,74.00,72.40,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(25,22,65.00,62.00,68.00,65.60,'B',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(26,23,68.00,65.00,70.00,68.10,'B',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(27,24,88.00,85.00,90.00,88.10,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(28,25,82.00,79.00,85.00,82.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(29,26,77.00,74.00,79.00,77.10,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(30,27,85.00,82.00,87.00,85.10,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(31,28,90.00,88.00,92.00,90.40,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(32,29,83.00,80.00,86.00,83.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(33,30,75.00,72.00,78.00,75.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(34,31,68.00,65.00,71.00,68.60,'B',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(35,32,72.00,69.00,74.00,72.10,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(36,33,78.00,75.00,80.00,78.10,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(49,34,91.00,88.00,93.00,91.10,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(50,35,86.00,83.00,88.00,86.10,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(51,36,83.00,80.00,86.00,83.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(52,37,79.00,77.00,81.00,79.40,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(53,38,88.00,85.00,90.00,88.10,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(54,39,74.00,71.00,76.00,74.10,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(55,40,70.00,67.00,72.00,70.10,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(56,41,65.00,62.00,68.00,65.60,'B',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(57,42,68.00,65.00,70.00,68.10,'B',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(58,43,86.00,83.00,88.00,86.10,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(59,44,80.00,77.00,82.00,80.10,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(60,45,75.00,72.00,78.00,75.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(61,46,82.00,79.00,85.00,82.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(62,47,85.00,82.00,87.00,85.10,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(63,48,78.00,75.00,80.00,78.10,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(64,49,88.00,85.00,91.00,88.60,'A',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(65,50,82.00,79.00,85.00,82.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(66,51,76.00,73.00,78.00,76.10,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(67,52,70.00,67.00,73.00,70.60,'B+',1,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(80,3,88.00,85.00,92.00,89.10,'A',0,'2026-05-30 05:18:17','2026-05-30 02:24:07'),(81,4,80.00,77.00,83.00,80.60,'B+',0,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(82,5,75.00,72.00,78.00,75.60,'B+',0,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(83,6,82.00,79.00,85.00,82.60,'B+',0,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(84,62,85.00,32.00,88.00,70.60,'B+',0,'2026-05-30 05:18:17','2026-05-30 02:24:07'),(85,63,78.00,75.00,80.00,78.10,'B+',0,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(86,70,72.00,69.00,75.00,72.60,'B+',0,'2026-05-30 05:18:17','2026-05-30 02:24:07'),(87,71,70.00,67.00,73.00,70.60,'B+',0,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(88,73,80.00,77.00,83.00,80.60,'B+',0,'2026-05-30 05:18:17','2026-05-30 02:24:07'),(89,53,88.00,85.00,90.00,88.10,'A',0,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(90,54,83.00,80.00,86.00,83.60,'B+',0,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(91,58,74.00,71.00,76.00,74.10,'B+',0,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(92,59,70.00,67.00,73.00,70.60,'B+',0,'2026-05-30 05:18:17','2026-05-30 05:18:17'),(97,66,0.00,0.00,0.00,0.00,'E',0,'2026-05-30 02:14:33','2026-05-30 02:24:07'),(98,76,0.00,0.00,0.00,0.00,'E',0,'2026-05-30 02:14:33','2026-05-30 02:24:07'),(99,79,12.00,10.00,20.00,15.40,'E',0,'2026-05-30 02:14:33','2026-05-30 02:24:07'),(100,81,0.00,0.00,0.00,0.00,'E',0,'2026-05-30 02:14:33','2026-05-30 02:24:07');
/*!40000 ALTER TABLE `nilai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengumuman`
--

DROP TABLE IF EXISTS `pengumuman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengumuman` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci,
  `tipe` enum('ACADEMIC','SYSTEM','EVENT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACADEMIC',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengumuman`
--

LOCK TABLES `pengumuman` WRITE;
/*!40000 ALTER TABLE `pengumuman` DISABLE KEYS */;
INSERT INTO `pengumuman` VALUES (1,'Pengisian KRS Semester Genap 2023/2024 Dibuka','Sistem pengisian KRS sudah dibuka. Mahasiswa dapat melakukan pengisian KRS sampai dengan tanggal 28 Februari 2024.','ACADEMIC','2026-04-30 18:19:05','2026-04-30 18:19:05'),(2,'Jadwal UTS Semester Genap 2023/2024','Ujian Tengah Semester akan dilaksanakan pada bulan Maret 2024. Detail jadwal akan diumumkan lebih lanjut.','ACADEMIC','2026-04-30 18:19:05','2026-04-30 18:19:05'),(3,'Beasiswa Prestasi Akademik 2024 Dibuka','Pendaftaran beasiswa prestasi akademik tahun 2024 sudah dibuka. Silakan kunjungi portal beasiswa untuk informasi lebih lanjut.','EVENT','2026-04-30 18:19:05','2026-04-30 18:19:05');
/*!40000 ALTER TABLE `pengumuman` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semester`
--

DROP TABLE IF EXISTS `semester`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `semester` (
  `id_semester` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tahun_ajaran` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkatan_semester` enum('ganjil','genap') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nonaktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_semester`),
  UNIQUE KEY `semester_tahun_ajaran_tingkatan_semester_unique` (`tahun_ajaran`,`tingkatan_semester`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semester`
--

LOCK TABLES `semester` WRITE;
/*!40000 ALTER TABLE `semester` DISABLE KEYS */;
INSERT INTO `semester` VALUES (1,'2024/2025','ganjil','nonaktif',NULL,NULL),(2,'2024/2025','genap','nonaktif',NULL,NULL),(3,'2025/2026','ganjil','nonaktif',NULL,NULL),(4,'2025/2026','genap','aktif',NULL,NULL);
/*!40000 ALTER TABLE `semester` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-30 17:13:47
