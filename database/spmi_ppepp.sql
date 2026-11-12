-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: spmi_ppepp
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.22-MariaDB

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

--
-- Table structure for table `academic_year_deadlines`
--

DROP TABLE IF EXISTS `academic_year_deadlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_year_deadlines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tahun_akademik` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `deadline_upload` datetime DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `tahun_akademik` (`tahun_akademik`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  `prodi_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aksi` enum('CREATE','UPDATE','DELETE','RESTORE','LOGIN','LOGOUT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `modul` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_values` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_values` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_prodi` (`prodi_id`),
  KEY `idx_aksi` (`aksi`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bidang_standar`
--

DROP TABLE IF EXISTS `bidang_standar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bidang_standar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_bidang` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_bidang` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fakultas`
--

DROP TABLE IF EXISTS `fakultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fakultas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_fakultas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_fakultas` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dekan` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nidn_dekan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_wadek` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nidn_wadek` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periode_jabatan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '2022 - 2026',
  `deskripsi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_fakultas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_fakultas` (`kode_fakultas`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gpm_evaluations`
--

DROP TABLE IF EXISTS `gpm_evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gpm_evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gpm_user_id` int(11) NOT NULL,
  `fakultas_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `evaluator_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `evaluator_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2025/2026',
  `skor_ketelitian` tinyint(3) unsigned NOT NULL DEFAULT 80,
  `skor_kecepatan` tinyint(3) unsigned NOT NULL DEFAULT 80,
  `skor_komunikasi` tinyint(3) unsigned NOT NULL DEFAULT 80,
  `skor_manual_total` decimal(5,2) NOT NULL DEFAULT 80.00,
  `catatan_evaluasi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_gpm_eval_user` (`gpm_user_id`,`created_at`),
  KEY `idx_gpm_eval_fak` (`fakultas_id`,`periode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `landing_settings`
--

DROP TABLE IF EXISTS `landing_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_settings` (
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setting_group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ppepp_cycles`
--

DROP TABLE IF EXISTS `ppepp_cycles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppepp_cycles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul_siklus` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` enum('prodi','fakultas') COLLATE utf8mb4_unicode_ci DEFAULT 'prodi',
  `prodi_id` int(11) DEFAULT NULL,
  `fakultas_id` int(11) DEFAULT NULL,
  `bidang_id` int(11) DEFAULT NULL,
  `sub_bidang_id` int(11) DEFAULT NULL,
  `tahun_akademik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_prodi_cycle` (`prodi_id`),
  KEY `idx_fakultas_cycle` (`fakultas_id`),
  KEY `idx_level_cycle` (`level`),
  KEY `idx_cycles_prodi` (`prodi_id`,`deleted_at`),
  KEY `idx_cycles_fakultas` (`fakultas_id`,`level`,`deleted_at`),
  KEY `idx_cycles_bidang` (`bidang_id`,`sub_bidang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ppepp_document_file_sub_bidang`
--

DROP TABLE IF EXISTS `ppepp_document_file_sub_bidang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppepp_document_file_sub_bidang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_file_id` int(11) NOT NULL,
  `sub_bidang_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_file_sub` (`document_file_id`,`sub_bidang_id`),
  KEY `fk_dfs_file` (`document_file_id`),
  KEY `fk_dfs_sub` (`sub_bidang_id`),
  CONSTRAINT `fk_dfs_file` FOREIGN KEY (`document_file_id`) REFERENCES `ppepp_document_files` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dfs_sub` FOREIGN KEY (`sub_bidang_id`) REFERENCES `sub_bidang_standar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ppepp_document_files`
--

DROP TABLE IF EXISTS `ppepp_document_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppepp_document_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_extension` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Utama',
  `file_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'file',
  `narasi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_bidang_ids` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_page_limited` tinyint(1) NOT NULL DEFAULT 1,
  `public_page_limit` int(11) NOT NULL DEFAULT 1,
  `can_download_public` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_doc_files_doc` (`document_id`),
  KEY `idx_docfiles_doc_sort` (`document_id`,`sort_order`),
  KEY `idx_docfiles_cat` (`file_category`),
  CONSTRAINT `fk_doc_files_doc` FOREIGN KEY (`document_id`) REFERENCES `ppepp_documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ppepp_document_reviews`
--

DROP TABLE IF EXISTS `ppepp_document_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppepp_document_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `reviewer_role` enum('gpm','lpm','super_admin','kepala_lpm','admin_prodi','admin_fakultas') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reviewer_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahap` enum('gpm','lpm','resubmission') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_sebelumnya` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_hasil` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pdr_doc` (`document_id`),
  KEY `idx_pdr_reviewer` (`reviewer_id`),
  CONSTRAINT `fk_pdr_doc` FOREIGN KEY (`document_id`) REFERENCES `ppepp_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pdr_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ppepp_document_revisions`
--

DROP TABLE IF EXISTS `ppepp_document_revisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppepp_document_revisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `cycle_id` int(11) DEFAULT NULL,
  `revisi_ke` int(11) DEFAULT 1,
  `reviewer_id` int(11) DEFAULT NULL,
  `reviewer_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewer_role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_review` enum('perlu_perbaikan','sesuai','belum_direview','sudah_diperbaiki') COLLATE utf8mb4_unicode_ci DEFAULT 'perlu_perbaikan',
  `catatan_review` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `uploader_id` int(11) DEFAULT NULL,
  `uploader_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploader_role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_perbaikan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revised_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_doc_revision` (`document_id`),
  KEY `idx_cycle_revision` (`cycle_id`),
  KEY `idx_reviewed_at` (`reviewed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ppepp_document_sub_bidang`
--

DROP TABLE IF EXISTS `ppepp_document_sub_bidang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppepp_document_sub_bidang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `sub_bidang_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_doc_sub` (`document_id`,`sub_bidang_id`),
  KEY `fk_ds_doc` (`document_id`),
  KEY `fk_ds_sub` (`sub_bidang_id`),
  CONSTRAINT `fk_ds_doc` FOREIGN KEY (`document_id`) REFERENCES `ppepp_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ds_sub` FOREIGN KEY (`sub_bidang_id`) REFERENCES `sub_bidang_standar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ppepp_documents`
--

DROP TABLE IF EXISTS `ppepp_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppepp_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prodi_id` int(11) DEFAULT NULL,
  `fakultas_id` int(11) DEFAULT NULL,
  `level` enum('prodi','fakultas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prodi',
  `user_id` int(11) NOT NULL,
  `bidang_id` int(11) DEFAULT NULL,
  `sub_bidang_id` int(11) DEFAULT NULL,
  `cycle_id` int(11) DEFAULT NULL,
  `nama_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_dokumen` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `siklus` enum('penetapan','pelaksanaan','evaluasi','pengendalian','peningkatan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_akademik` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_dokumen` date DEFAULT NULL,
  `tanggal_berlaku_mulai` date DEFAULT NULL,
  `tanggal_berlaku_selesai` date DEFAULT NULL,
  `jenis_upload` enum('file','link','kombinasi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'file',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_extension` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_link` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_page_limit` int(11) NOT NULL DEFAULT 1,
  `can_download_public` tinyint(1) NOT NULL DEFAULT 0,
  `status_review` enum('draft','menunggu_review_gpm','revisi_gpm','menunggu_review_lpm','revisi_lpm','sudah_diperbaiki','sesuai','belum_direview','perlu_perbaikan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_review_gpm',
  `tahap_review` enum('draft','gpm','lpm','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gpm',
  `catatan_review` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gpm_reviewed_by` int(11) DEFAULT NULL,
  `gpm_reviewed_at` datetime DEFAULT NULL,
  `gpm_catatan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lpm_reviewed_by` int(11) DEFAULT NULL,
  `lpm_reviewed_at` datetime DEFAULT NULL,
  `lpm_catatan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_docs_prodi` (`prodi_id`),
  KEY `fk_docs_user` (`user_id`),
  KEY `fk_docs_bidang` (`bidang_id`),
  KEY `fk_docs_reviewer` (`reviewed_by`),
  KEY `fk_docs_fakultas` (`fakultas_id`),
  KEY `idx_cycle_id` (`cycle_id`),
  KEY `fk_docs_gpm_reviewer` (`gpm_reviewed_by`),
  KEY `fk_docs_lpm_reviewer` (`lpm_reviewed_by`),
  KEY `idx_docs_filter_status` (`deleted_at`,`status_review`),
  KEY `idx_docs_prodi_filter` (`prodi_id`,`deleted_at`,`status_review`,`siklus`),
  KEY `idx_docs_fakultas_filter` (`fakultas_id`,`level`,`deleted_at`,`status_review`),
  KEY `idx_docs_siklus_filter` (`siklus`,`deleted_at`,`status_review`),
  KEY `idx_docs_tahun` (`tahun_akademik`,`deleted_at`),
  KEY `idx_docs_created_at` (`created_at`),
  CONSTRAINT `fk_docs_bidang` FOREIGN KEY (`bidang_id`) REFERENCES `bidang_standar` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_docs_fakultas` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_docs_gpm_reviewer` FOREIGN KEY (`gpm_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_docs_lpm_reviewer` FOREIGN KEY (`lpm_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_docs_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_docs_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_docs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prodis`
--

DROP TABLE IF EXISTS `prodis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prodis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fakultas_id` int(11) NOT NULL,
  `kode_prodi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_prodi` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenjang` enum('D3','S1','S2','S3','Profesi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S1',
  `nama_kaprodi` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nidn_kaprodi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_sekprodi` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nidn_sekprodi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_prodi` (`kode_prodi`),
  KEY `fk_prodis_fakultas` (`fakultas_id`),
  CONSTRAINT `fk_prodis_fakultas` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sub_bidang_standar`
--

DROP TABLE IF EXISTS `sub_bidang_standar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_bidang_standar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bidang_id` int(11) NOT NULL,
  `kode_sub_bidang` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_sub_bidang` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sub_bidang_bidang` (`bidang_id`),
  CONSTRAINT `fk_sub_bidang_bidang` FOREIGN KEY (`bidang_id`) REFERENCES `bidang_standar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tahun_akademik_settings`
--

DROP TABLE IF EXISTS `tahun_akademik_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tahun_akademik_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tahun_akademik` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `tahun_akademik` (`tahun_akademik`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `upload_access_requests`
--

DROP TABLE IF EXISTS `upload_access_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `upload_access_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `level` enum('prodi','fakultas') COLLATE utf8mb4_unicode_ci NOT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  `fakultas_id` int(11) DEFAULT NULL,
  `tahun_akademik` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `akses_berlaku_sampai` datetime DEFAULT NULL,
  `catatan_lpm` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_unit_status` (`status`,`level`,`prodi_id`,`fakultas_id`,`tahun_akademik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_plain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('super_admin','kepala_lpm','sekretaris_lpm','ketua_pusat_spm','ketua_pusat_ami','ketua_pusat_pemeringkatan','tu_lpm','admin_lpm','kepala_pusat_mutu','dekan','wadek','gpm','kaprodi','sekprodi','pengguna','testing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kaprodi',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  `fakultas_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_prodi` (`prodi_id`),
  KEY `fk_users_fakultas` (`fakultas_id`),
  CONSTRAINT `fk_users_fakultas` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'spmi_ppepp'
--

--
-- Master seed data
--

-- Dumping data for table `academic_year_deadlines`
--

LOCK TABLES `academic_year_deadlines` WRITE;
/*!40000 ALTER TABLE `academic_year_deadlines` DISABLE KEYS */;
INSERT INTO `academic_year_deadlines` VALUES (1,'2026/2027',1,'2027-08-31 23:59:59','Tahun Akademik Aktif (Berjalan)','2026-10-22 14:40:21','2026-10-22 14:47:32'),(2,'2025/2026',0,'2026-08-31 23:59:59','Tahun Akademik Lampau','2026-10-22 14:40:21','2026-10-22 14:47:32'),(3,'2024/2025',0,'2025-08-31 23:59:59','Tahun Akademik Lampau','2026-10-22 14:40:21','2026-10-22 14:40:21'),(4,'2023/2024',0,'2024-08-31 23:59:59','Tahun Akademik Lampau','2026-10-22 14:40:21','2026-10-22 14:40:21'),(5,'2027/2028',0,'2028-08-31 23:59:59','Sinkronisasi dari Pengaturan Periode','2026-10-22 15:27:44','2026-10-22 15:27:44'),(6,'2021/2022',0,'2022-08-31 23:59:59','Tahun Akademik Lampau','2026-10-27 02:05:04','2026-10-27 02:05:04'),(7,'2022/2023',0,'2023-08-31 23:59:59','Tahun Akademik Lampau','2026-10-27 02:05:04','2026-10-27 02:05:04');
/*!40000 ALTER TABLE `academic_year_deadlines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `bidang_standar`
--

LOCK TABLES `bidang_standar` WRITE;
/*!40000 ALTER TABLE `bidang_standar` DISABLE KEYS */;
INSERT INTO `bidang_standar` VALUES (1,'VMTS','Visi, Misi, Tujuan, dan Strategi','Standar perencanaan strategis dan arah mutu institusi/program studi.',1,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(2,'TATA_PAMONG','Tata Pamong, Tata Kelola & Kerjasama','Standar sistem kepemimpinan, penjaminan mutu, dan jejaring kemitraan.',1,'2026-09-16 13:36:03','2026-10-05 18:27:01'),(3,'MAHASISWA','Mahasiswa dan Layanan Kemahasiswaan','Standar rekrutmen, prestasi, bimbingan karir, dan kesejahteraan mahasiswa.',1,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(4,'SDM','Sumber Daya Manusia','Standar kualifikasi, kompetensi, beban kerja, dan pengembangan dosen/tendik.',1,'2026-09-16 13:36:03','2026-10-05 18:27:01'),(5,'KEUANGAN_SARPRAS','Keuangan, Sarana, dan Prasarana','Standar kecukupan dana, laboratorium, perpustakaan, dan fasilitas penunjang.',1,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(6,'PENDIDIKAN','Pendidikan dan Kurikulum','Standar kurikulum OBE, proses pembelajaran, asesmen, dan suasana akademik.',1,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(7,'PENELITIAN','Penelitian dan Publikasi','Standar roadmap riset dosen, keterlibatan mahasiswa, dan publikasi ilmiah.',1,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(8,'PKM','Pengabdian kepada Masyarakat','Standar kegiatan pengabdian masyarakat berorientasi solutif dan berkelanjutan.',1,'2026-09-16 13:36:03','2026-10-05 18:27:01'),(9,'LUARAN_TRIDHARMA','Luaran dan Capaian Tridarma','Standar IPK lulusan, waktu tunggu kerja, kepuasan pengguna, dan HKI/paten.',1,'2026-09-16 13:36:03','2026-10-05 18:27:01');
/*!40000 ALTER TABLE `bidang_standar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `fakultas`
--

LOCK TABLES `fakultas` WRITE;
/*!40000 ALTER TABLE `fakultas` DISABLE KEYS */;
INSERT INTO `fakultas` VALUES (1,'FAD','Fakultas Arsitektur dan Desain','Dr. Ir. B. Tyas Susanti, M.T.','0629086701','Peter Ardiansyah, S.Sn., M.Ds.','0621098401','2022 - 2026','Mengembangkan pendidikan arsitektur dan desain visual berbasis humaniora dan teknologi.',NULL,'2026-09-16 13:36:03','2026-09-22 19:00:20'),(2,'FIKOM','Fakultas Ilmu Komputer','Dr. Ridwan Sanjaya, S.E., S.Kom., MS.IEC.','0624087701','Erdhi Widyarto Nugroho, S.T., M.T.','0618037901','2022 - 2026','Pusat keunggulan ilmu komputer, rekayasa perangkat lunak, AI, dan sistem informasi terdepan.',NULL,'2026-09-16 13:36:03','2026-09-22 20:34:14'),(3,'FEB','Fakultas Ekonomi dan Bisnis','Dr. St. Vena Purnamasari, S.E., M.Si.','0602057501','Dr. Agnes Advensia C., S.E., M.Si., Akt.','0615097402','2022 - 2026','Mendidik calon profesional bisnis, akuntan, dan manajer dengan etika dan daya saing global.',NULL,'2026-09-16 13:36:03','2026-09-22 19:00:20'),(4,'FBS','Fakultas Bahasa dan Seni',NULL,NULL,NULL,NULL,'2022 - 2026','Fokus pada literasi bahasa, komunikasi antar budaya, dan ekspresi seni visual.',NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(5,'FT','Fakultas Teknik','Dr. Ir. Djoko Suwarno, M.T.','0610046501','Daniel Hartanto, S.T., M.Eng.','0614058101','2022 - 2026','Pengembangan keahlian teknik sipil, elektro, dan robotika berbasis keberlanjutan lingkungan.',NULL,'2026-09-16 13:36:03','2026-09-22 19:00:20'),(6,'FPSI','Fakultas Psikologi','Dr. Margaretha Sih Setija Utami, M.Kes.','0622046101','Kuriake Kharismawan, S.Psi., M.Si.','0605078501','2022 - 2026','Pendidikan psikologi komprehensif untuk penguatan kesehatan mental dan organisasi.',NULL,'2026-09-16 13:36:03','2026-09-22 19:00:20'),(7,'FHK','Fakultas Hukum dan Komunikasi',NULL,NULL,NULL,NULL,'2022 - 2026','Menghasilkan praktisi hukum berintegritas dan komunikator strategis era digital.',NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(8,'FTP','Fakultas Teknologi Pertanian',NULL,NULL,NULL,NULL,'2022 - 2026','Inovasi ilmu pangan, nutrisi berkelanjutan, dan teknologi hasil pertanian modern.',NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(9,'FK','Fakultas Kedokteran',NULL,NULL,NULL,NULL,'2022 - 2026','Pendidikan dokter profesional yang menjunjung tinggi empati, etika moral, dan keilmuan medis.',NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(10,'FITL','Fakultas Ilmu dan Teknologi Lingkungan',NULL,NULL,NULL,NULL,'2022 - 2026','Program magister dan doktoral interdisipliner untuk penguatan kepemimpinan dan riset terapan.',NULL,'2026-09-16 13:36:03','2026-10-08 08:53:54');
/*!40000 ALTER TABLE `fakultas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `landing_settings`
--

LOCK TABLES `landing_settings` WRITE;
/*!40000 ALTER TABLE `landing_settings` DISABLE KEYS */;
INSERT INTO `landing_settings` VALUES ('direktori_section_desc','Pilih fakultas di bawah untuk meninjau profil kepemimpinan, sebaran dokumen mutu 5 siklus PPEPP, dan status kepatuhan standar mutu masing-masing prodi.','direktori','2026-09-28 22:31:13','2026-10-08 12:07:49'),('direktori_section_tag','DIREKTORI AKADEMIK','direktori','2026-09-28 22:31:13','2026-10-08 12:07:49'),('direktori_section_title','Fakultas & Program Studi','direktori','2026-09-28 22:31:13','2026-10-08 12:07:49'),('footer_address','Ruangg Lembaga Penjaminan Mutu, Gedung Thomas Aquinas Lantai 5, Kampus Universitas Katolik Soegijapranata, Jalan Pawiyatan Luhur IV/1 Bendan Duwur, Semarang 50234','footer','2026-09-28 22:31:13','2026-10-08 12:07:49'),('footer_akreditasi','Terakreditasi UNGGUL • BAN-PT','footer','2026-09-28 22:31:13','2026-10-08 12:07:49'),('footer_brand_sub','PEmantauan Tahapan PPEPP & Rencana Aksi','footer','2026-09-28 22:31:13','2026-10-08 12:07:49'),('footer_brand_title','PETRA','footer','2026-09-28 22:31:13','2026-10-08 12:07:49'),('footer_desc','PETRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.','footer','2026-09-28 22:31:13','2026-10-08 12:07:49'),('footer_email','lpm@unika.ac.id','footer','2026-09-28 22:31:13','2026-10-08 12:07:49'),('footer_phone','024-8441555 Ext 1473','footer','2026-09-28 22:31:13','2026-10-08 12:07:49'),('footer_website_text','Website Utama SCU','footer','2026-09-28 22:31:13','2026-10-08 12:07:49'),('footer_website_url','https://www.unika.ac.id','footer','2026-09-28 22:31:13','2026-10-08 12:07:49'),('hero_badge','Lembaga Penjaminan Mutu • UNIKA Soegijapranata','hero','2026-09-28 22:31:13','2026-10-08 12:07:49'),('hero_btn1_text','Jelajahi Direktori Fakultas','hero','2026-09-28 22:31:13','2026-10-08 12:07:49'),('hero_btn1_url','#direktoriFakultas','hero','2026-09-28 22:31:13','2026-10-08 12:07:49'),('hero_btn2_text','Repositori Dokumen Mutu','hero','2026-09-28 22:31:13','2026-10-08 12:07:49'),('hero_btn2_url','dokumen','hero','2026-09-28 22:31:13','2026-10-08 12:07:49'),('hero_desc','Portal terpadu pengawasan dan repositori dokumen Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan mutu tridharma di seluruh program studi Universitas Katolik Soegijapranata.','hero','2026-09-28 22:31:13','2026-10-08 12:07:49'),('hero_highlight','Siklus PPEPP Berkelanjutan','hero','2026-09-28 22:31:13','2026-10-08 12:07:49'),('hero_title','Sistem Penjaminan Mutu Internal','hero','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_e_desc','Audit Mutu Internal (AMI), monitoring berkala, monev pembelajaran, dan pengukuran kepuasan pengguna.','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_e_title','Evaluasi','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_p1_desc','Perumusan standar, manual mutu, kebijakan SPMI, dan Capaian Pembelajaran Lulusan (CPL).','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_p1_title','Penetapan','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_p2_desc','Implementasi kurikulum, RPS, SOP pembelajaran, penelitian, dan pengabdian masyarakat prodi.','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_p2_title','Pelaksanaan','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_p3_desc','Tindakan koreksi terhadap deviasi standar, evaluasi akar masalah, dan Rapat Tinjauan Manajemen (RTM).','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_p3_title','Pengendalian','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_p4_desc','Pembaruan dan peningkatan standar mutu secara berkelanjutan (Kaizen) melampaui SN-Dikti.','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_p4_title','Peningkatan','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_section_desc','Implementasi siklus berkelanjutan (Continuous Quality Improvement) sesuai pedoman Permendikbudristek No. 53 Tahun 2023 untuk mencapai akreditasi unggul institusi.','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_section_tag','ALUR PENJAMINAN MUTU','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('siklus_section_title','5 Siklus Penjaminan Mutu Internal (PPEPP)','siklus','2026-09-28 22:31:13','2026-10-08 12:07:49'),('stats_dokumen_label','Dokumen Mutu Terverifikasi','stats','2026-09-28 22:31:13','2026-10-08 12:07:49'),('stats_fakultas_label','Fakultas & Pascasarjana','stats','2026-09-28 22:31:13','2026-10-08 12:07:49'),('stats_prodi_label','Program Studi Aktif','stats','2026-09-28 22:31:13','2026-10-08 12:07:49'),('stats_siklus_label','Siklus PPEPP Terintegrasi','stats','2026-09-28 22:31:13','2026-10-08 12:07:49');
/*!40000 ALTER TABLE `landing_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `prodis`
--

LOCK TABLES `prodis` WRITE;
/*!40000 ALTER TABLE `prodis` DISABLE KEYS */;
INSERT INTO `prodis` VALUES (1,2,'55201','Teknik Informatika','S1','Prof. Dr. Ir. Abdi, M.T., IPU','0612057302','Rosalia Hadi, S.Kom., M.T.','0615088201','2026-09-16 13:36:03','2026-09-28 14:13:08'),(2,2,'57201','Sistem Informasi','S1','Agustinus Eko Nugroho, S.Kom., M.Cs.','0624088002','Kristophorus Hadiono, Ph.D.','0602037901','2026-09-16 13:36:03','2026-09-28 14:13:08'),(4,3,'62201','Akuntansi','S1','Dr. Christian Herdinata, S.E., M.M., CFP','0601077701','Y. Denny Krisnamurti, S.E., M.Si.','0611097601','2026-09-16 13:36:03','2026-09-28 14:13:08'),(5,3,'61201','Manajemen','S1','Dr. Agnes Advensia C., S.E., M.Si., Akt.','0615097402',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(7,1,'22201','Arsitektur','S1','Ir. Maria E. Priambodo, M.T.','0608026501',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(8,1,'90241','Desain Komunikasi Visual','S1','Peter Ardiansyah, S.Sn., M.Ds.','0621098401',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(9,5,'20201','Teknik Sipil','S1','Daniel Hartanto, S.T., M.Eng.','0614058101',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(10,5,'20202','Teknik Elektro','S1','Ferdian Ronny, S.T., M.Sc.','0609118302',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(11,6,'73201','Psikologi','S1','Dr. M. Sih Setija Utami, M.Kes.','0622046101',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(12,7,'74201','Ilmu Hukum','S1','Marcella Elwina S., S.H., C.N., M.Hum.','0629036901',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(13,7,'70201','Ilmu Komunikasi','S1','Rika Saraswati, S.H., CN., M.Hum., Ph.D.','0607067201',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(14,8,'41201','Teknologi Pangan','S1','Dr. Ir. Lindayani, M.P.','0628106401',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(16,9,'11201','Kedokteran','S1','dr. Jonsinar Silalahi, M.Si.Med., Sp.B.','0611046701',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(17,9,'11901','Pendidikan Profesi Dokter','Profesi','dr. Indah Saraswati, M.Biomed.','0617058202',NULL,NULL,'2026-09-16 13:36:03','2026-10-08 08:53:54'),(18,4,'79202','Sastra Inggris','S1','Angelika Riyandari, Ph.D.','0605017001',NULL,NULL,'2026-09-16 13:36:03','2026-09-16 13:36:03'),(19,3,'61101','Manajemen','S2','Dr. Ferdinandus Hindiarto, S.Psi., M.Si.','0618077501',NULL,NULL,'2026-09-16 13:36:03','2026-10-08 08:53:54'),(20,6,'73101','Psikologi','S2','Dr. Kristiana Haryanti, M.Si.','0604116301',NULL,NULL,'2026-09-16 13:36:03','2026-10-08 08:53:54'),(23,1,'MAG-ARS','Arsitektur','S2',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54'),(24,1,'DOK-ARS','Arsitektur','S3',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54'),(25,3,'MAG-AKT','Akuntansi','S2',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54'),(26,7,'MAG-HKM','Ilmu Hukum','S2',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54'),(27,10,'RIL','Rekayasa Infrastruktur dan Lingkungan','S1',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54'),(28,10,'MAG-LP','Lingkungan dan Perkotaan','S2',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54'),(29,10,'DOK-IL','Ilmu Lingkungan','S3',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54'),(30,6,'PROF-PSI','Pendidikan Profesi Psikolog','Profesi',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54'),(31,5,'PROF-INS','Profesi Insinyur','Profesi',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54'),(32,8,'MAG-TP','Teknologi Pangan','S2',NULL,NULL,NULL,NULL,'2026-10-08 08:53:54','2026-10-08 08:53:54');
/*!40000 ALTER TABLE `prodis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `sub_bidang_standar`
--

LOCK TABLES `sub_bidang_standar` WRITE;
/*!40000 ALTER TABLE `sub_bidang_standar` DISABLE KEYS */;
INSERT INTO `sub_bidang_standar` VALUES (1,1,'VMTS-01','Standar Diferensiasi Misi','Standar SPMI untuk Standar Diferensiasi Misi dalam lingkup Visi, Misi, Tujuan, dan Strategi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(2,1,'VMTS-02','Standar Benchmarking dan Peningkatan Daya Saing','Standar SPMI untuk Standar Benchmarking dan Peningkatan Daya Saing dalam lingkup Visi, Misi, Tujuan, dan Strategi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(3,1,'VMTS-03','Standar Internasional dan Daya Saing Global','Standar SPMI untuk Standar Internasional dan Daya Saing Global dalam lingkup Visi, Misi, Tujuan, dan Strategi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(4,1,'VMTS-04','Standar Peningkatan Kinerja dan Pemeringkatan','Standar SPMI untuk Standar Peningkatan Kinerja dan Pemeringkatan dalam lingkup Visi, Misi, Tujuan, dan Strategi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(5,1,'VMTS-05','Standar Keberlanjutan dan ESG','Standar SPMI untuk Standar Keberlanjutan dan ESG dalam lingkup Visi, Misi, Tujuan, dan Strategi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(6,1,'VMTS-06','Standar Kepemimpinan Mutu dan Manajemen Risiko','Standar SPMI untuk Standar Kepemimpinan Mutu dan Manajemen Risiko dalam lingkup Visi, Misi, Tujuan, dan Strategi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(7,2,'TP-01','Standar Organisasi','Standar SPMI untuk Standar Organisasi dalam lingkup Tata Pamong, Tata Kelola & Kerjasama.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(8,2,'TP-02','Standar Tata Kelola','Standar SPMI untuk Standar Tata Kelola dalam lingkup Tata Pamong, Tata Kelola & Kerjasama.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(9,2,'TP-03','Standar Kolaborasi dan Kemitraan Strategis','Standar SPMI untuk Standar Kolaborasi dan Kemitraan Strategis dalam lingkup Tata Pamong, Tata Kelola & Kerjasama.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(10,2,'TP-04','Standar Akreditasi dan Penjaminan Mutu','Standar SPMI untuk Standar Akreditasi dan Penjaminan Mutu dalam lingkup Tata Pamong, Tata Kelola & Kerjasama.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(11,2,'TP-05','Standar Kepemimpinan Mutu dan Manajemen Risiko','Standar SPMI untuk Standar Kepemimpinan Mutu dan Manajemen Risiko dalam lingkup Tata Pamong, Tata Kelola & Kerjasama.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(12,2,'TP-06','Standar Keamanan Data dan Privasi','Standar SPMI untuk Standar Keamanan Data dan Privasi dalam lingkup Tata Pamong, Tata Kelola & Kerjasama.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(13,2,'TP-07','Standar Integritas Akademik','Standar SPMI untuk Standar Integritas Akademik dalam lingkup Tata Pamong, Tata Kelola & Kerjasama.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(14,3,'MHS-01','Standar Kemahasiswaan','Standar SPMI untuk Standar Kemahasiswaan dalam lingkup Mahasiswa dan Layanan Kemahasiswaan.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(15,3,'MHS-02','Standar Pelibatan Mahasiswa dalam Tridarma','Standar SPMI untuk Standar Pelibatan Mahasiswa dalam Tridarma dalam lingkup Mahasiswa dan Layanan Kemahasiswaan.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(16,3,'MHS-03','Standar Keterlibatan dan Kontribusi Alumni','Standar SPMI untuk Standar Keterlibatan dan Kontribusi Alumni dalam lingkup Mahasiswa dan Layanan Kemahasiswaan.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(17,4,'SDM-01','Standar Dosen dan Tenaga Kependidikan','Standar SPMI untuk Standar Dosen dan Tenaga Kependidikan dalam lingkup Sumber Daya Manusia.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(18,4,'SDM-02','Standar Ketenagaan','Standar SPMI untuk Standar Ketenagaan dalam lingkup Sumber Daya Manusia.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(19,4,'SDM-03','Standar Penugasan dan Peningkatan Kompetensi Dosen dalam Penelitian','Standar SPMI untuk Standar Penugasan dan Peningkatan Kompetensi Dosen dalam Penelitian dalam lingkup Sumber Daya Manusia.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(20,4,'SDM-04','Standar Penugasan dan Peningkatan Kompetensi Dosen dalam Pengabdian','Standar SPMI untuk Standar Penugasan dan Peningkatan Kompetensi Dosen dalam Pengabdian dalam lingkup Sumber Daya Manusia.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(21,5,'KSP-01','Standar Keuangan','Standar SPMI untuk Standar Keuangan dalam lingkup Keuangan, Sarana, dan Prasarana.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(22,5,'KSP-02','Standar Sarana dan Prasarana','Standar SPMI untuk Standar Sarana dan Prasarana dalam lingkup Keuangan, Sarana, dan Prasarana.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(23,5,'KSP-03','Standar Pembiayaan','Standar SPMI untuk Standar Pembiayaan dalam lingkup Keuangan, Sarana, dan Prasarana.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(24,5,'KSP-04','Standar Sarana Prasarana Penelitian','Standar SPMI untuk Standar Sarana Prasarana Penelitian dalam lingkup Keuangan, Sarana, dan Prasarana.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(25,5,'KSP-05','Standar Pembiayaan Penelitian','Standar SPMI untuk Standar Pembiayaan Penelitian dalam lingkup Keuangan, Sarana, dan Prasarana.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(26,5,'KSP-06','Standar Sarana Prasarana Pengabdian','Standar SPMI untuk Standar Sarana Prasarana Pengabdian dalam lingkup Keuangan, Sarana, dan Prasarana.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(27,5,'KSP-07','Standar Pembiayaan Pengabdian','Standar SPMI untuk Standar Pembiayaan Pengabdian dalam lingkup Keuangan, Sarana, dan Prasarana.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(28,6,'DIK-01','Standar Isi Pendidikan','Standar SPMI untuk Standar Isi Pendidikan dalam lingkup Pendidikan dan Kurikulum.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(29,6,'DIK-02','Standar Proses Pembelajaran','Standar SPMI untuk Standar Proses Pembelajaran dalam lingkup Pendidikan dan Kurikulum.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(30,6,'DIK-03','Standar Penilaian','Standar SPMI untuk Standar Penilaian dalam lingkup Pendidikan dan Kurikulum.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(31,6,'DIK-04','Standar Integritas Akademik','Standar SPMI untuk Standar Integritas Akademik dalam lingkup Pendidikan dan Kurikulum.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(32,6,'DIK-05','Standar Pelibatan Mahasiswa dalam Tridarma','Standar SPMI untuk Standar Pelibatan Mahasiswa dalam Tridarma dalam lingkup Pendidikan dan Kurikulum.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(33,6,'DIK-06','Standar Internasional dan Daya Saing Global','Standar SPMI untuk Standar Internasional dan Daya Saing Global dalam lingkup Pendidikan dan Kurikulum.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(34,7,'LIT-01','Standar Mutu Penelitian','Standar SPMI untuk Standar Mutu Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(35,7,'LIT-02','Standar Relevansi Penelitian','Standar SPMI untuk Standar Relevansi Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(36,7,'LIT-03','Standar Kemanfaatan Penelitian','Standar SPMI untuk Standar Kemanfaatan Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(37,7,'LIT-04','Standar Perencanaan Penelitian','Standar SPMI untuk Standar Perencanaan Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(38,7,'LIT-05','Standar Pelaksanaan Penelitian','Standar SPMI untuk Standar Pelaksanaan Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(39,7,'LIT-06','Standar Penilaian Penelitian','Standar SPMI untuk Standar Penilaian Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(40,7,'LIT-07','Standar Pengawasan dan Pengendalian Penelitian','Standar SPMI untuk Standar Pengawasan dan Pengendalian Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(41,7,'LIT-08','Standar Penerapan Teknologi Informasi dalam Penelitian','Standar SPMI untuk Standar Penerapan Teknologi Informasi dalam Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(42,7,'LIT-09','Standar Sarana Prasarana Penelitian','Standar SPMI untuk Standar Sarana Prasarana Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(43,7,'LIT-10','Standar Pembiayaan Penelitian','Standar SPMI untuk Standar Pembiayaan Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(44,7,'LIT-11','Standar Penugasan dan Peningkatan Kompetensi Dosen dalam Penelitian','Standar SPMI untuk Standar Penugasan dan Peningkatan Kompetensi Dosen dalam Penelitian dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(45,7,'LIT-12','Standar Integritas Akademik','Standar SPMI untuk Standar Integritas Akademik dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(46,7,'LIT-13','Standar Internasional dan Daya Saing Global','Standar SPMI untuk Standar Internasional dan Daya Saing Global dalam lingkup Penelitian dan Publikasi.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(47,8,'PKM-01','Standar Mutu Pengabdian','Standar SPMI untuk Standar Mutu Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(48,8,'PKM-02','Standar Relevansi Pengabdian','Standar SPMI untuk Standar Relevansi Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(49,8,'PKM-03','Standar Kemanfaatan Pengabdian','Standar SPMI untuk Standar Kemanfaatan Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(50,8,'PKM-04','Standar Perencanaan Pengabdian','Standar SPMI untuk Standar Perencanaan Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(51,8,'PKM-05','Standar Pelaksanaan Pengabdian','Standar SPMI untuk Standar Pelaksanaan Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(52,8,'PKM-06','Standar Penilaian Pengabdian','Standar SPMI untuk Standar Penilaian Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(53,8,'PKM-07','Standar Pengawasan dan Pengendalian Pengabdian','Standar SPMI untuk Standar Pengawasan dan Pengendalian Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(54,8,'PKM-08','Standar Penerapan Teknologi Informasi dalam Pengabdian','Standar SPMI untuk Standar Penerapan Teknologi Informasi dalam Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(55,8,'PKM-09','Standar Sarana Prasarana Pengabdian','Standar SPMI untuk Standar Sarana Prasarana Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(56,8,'PKM-10','Standar Pembiayaan Pengabdian','Standar SPMI untuk Standar Pembiayaan Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(57,8,'PKM-11','Standar Penugasan dan Peningkatan Kompetensi Dosen dalam Pengabdian','Standar SPMI untuk Standar Penugasan dan Peningkatan Kompetensi Dosen dalam Pengabdian dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(58,8,'PKM-12','Standar Internasional dan Daya Saing Global','Standar SPMI untuk Standar Internasional dan Daya Saing Global dalam lingkup Pengabdian kepada Masyarakat.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(59,9,'OUT-01','Standar Luaran/Standar Kompetensi','Standar SPMI untuk Standar Luaran/Standar Kompetensi dalam lingkup Luaran dan Capaian Tridarma.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(60,9,'OUT-02','Standar Mutu Penelitian','Standar SPMI untuk Standar Mutu Penelitian dalam lingkup Luaran dan Capaian Tridarma.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(61,9,'OUT-03','Standar Mutu Pengabdian','Standar SPMI untuk Standar Mutu Pengabdian dalam lingkup Luaran dan Capaian Tridarma.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(62,9,'OUT-04','Standar Relevansi dan Kemanfaatan Penelitian','Standar SPMI untuk Standar Relevansi dan Kemanfaatan Penelitian dalam lingkup Luaran dan Capaian Tridarma.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(63,9,'OUT-05','Standar Relevansi dan Kemanfaatan Pengabdian','Standar SPMI untuk Standar Relevansi dan Kemanfaatan Pengabdian dalam lingkup Luaran dan Capaian Tridarma.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(64,9,'OUT-06','Standar Keterlibatan dan Kontribusi Alumni','Standar SPMI untuk Standar Keterlibatan dan Kontribusi Alumni dalam lingkup Luaran dan Capaian Tridarma.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(65,9,'OUT-07','Standar Peningkatan Kinerja dan Pemeringkatan','Standar SPMI untuk Standar Peningkatan Kinerja dan Pemeringkatan dalam lingkup Luaran dan Capaian Tridarma.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02'),(66,9,'OUT-08','Standar Keberlanjutan dan ESG','Standar SPMI untuk Standar Keberlanjutan dan ESG dalam lingkup Luaran dan Capaian Tridarma.',1,'2026-10-05 18:27:02','2026-10-05 18:27:02');
/*!40000 ALTER TABLE `sub_bidang_standar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `tahun_akademik_settings`
--

LOCK TABLES `tahun_akademik_settings` WRITE;
/*!40000 ALTER TABLE `tahun_akademik_settings` DISABLE KEYS */;
INSERT INTO `tahun_akademik_settings` VALUES (1,'2023/2024','2023-09-01','2024-08-31',0,'Tahun Akademik Lalu (Ditutup)','2026-10-22 13:19:08','2026-10-22 13:19:08'),(2,'2024/2025','2024-09-01','2025-08-31',0,'Tahun Akademik Lalu (Ditutup)','2026-10-22 13:19:08','2026-10-22 13:19:08'),(3,'2025/2026','2025-09-01','2026-08-31',1,'Tahun Akademik Aktif (Terbuka)','2026-10-22 13:19:08','2026-10-22 13:19:08'),(4,'2026/2027','2026-09-01','2027-08-31',1,'Tahun Akademik Berjalan (Terbuka)','2026-10-22 13:19:08','2026-10-22 13:19:08'),(5,'2027/2028','2027-09-01','2028-08-31',0,'Tahun Akademik Mendatang (Ditutup)','2026-10-22 13:19:08','2026-10-22 13:19:08'),(11,'2021/2022','2021-09-01','2022-08-31',0,'Tahun Akademik Lampau','2026-10-27 02:05:04','2026-10-27 02:05:04'),(12,'2022/2023','2022-09-01','2023-08-31',0,'Tahun Akademik Lampau','2026-10-27 02:05:04','2026-10-27 02:05:04');
/*!40000 ALTER TABLE `tahun_akademik_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Kepala Lembaga Penjaminan Mutu','lpm@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','kepala_lpm',NULL,NULL,NULL,1,'2026-09-16 13:36:03','2026-11-12 10:34:58'),(7,'TU Lembaga Penjaminan Mutu','tu.lpm@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','tu_lpm',NULL,NULL,NULL,1,'2026-09-21 19:50:16','2026-11-12 10:34:58'),(10,'GPM Fakultas Ilmu Komputer','gpm.fikom@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','gpm',NULL,NULL,2,1,'2026-09-24 11:08:22','2026-10-08 09:09:01'),(11,'Ketua Pusat Pengembangan Sistem Penjaminan Mutu','lpm.spm@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','ketua_pusat_spm',NULL,NULL,NULL,1,'2026-09-28 14:13:08','2026-11-12 10:34:58'),(15,'Dr. Bernardinus Harnadi, M.T. (Dekan FIKOM)','dekan.fikom@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','dekan',NULL,NULL,2,1,'2026-09-28 14:13:08','2026-10-08 09:09:01'),(16,'Erdhi Widyarto Nugroho, S.T., M.T. (Wakil Dekan FIKOM)','wadek.fikom@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','wadek',NULL,NULL,2,1,'2026-09-28 14:13:08','2026-10-08 09:09:01'),(19,'Dr. St. Vena Purnamasari, S.E., M.Si. (Dekan FEB)','dekan.feb@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','dekan',NULL,NULL,3,1,'2026-09-28 14:13:08','2026-10-08 09:09:01'),(20,'Dr. Agnes Advensia C., S.E., M.Si., Akt. (Wakil Dekan FEB)','wadekfeb@gmail.com','$2y$12$ZCBRdrq3oI.OWg2UgFpEIuTHZJMU7JQoTgDSXQg.d3JDneOzgpese','123','wadek',NULL,NULL,3,1,'2026-09-28 14:13:08','2026-10-29 11:24:03'),(22,'Prof. Dr. Ir. Abdi, M.T., IPU (Kaprodi Teknik Informatika)','kaprodi.ti@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','kaprodi',NULL,1,2,1,'2026-09-28 14:13:08','2026-11-12 09:44:42'),(23,'Rosalia Hadi, S.Kom., M.T. (Sekprodi Teknik Informatika)','sekprodi.ti@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','sekprodi',NULL,1,2,1,'2026-09-28 14:13:08','2026-10-08 09:09:01'),(25,'Agustinus Eko Nugroho, S.Kom., M.Cs. (Kaprodi Sistem Informasi)','kaprodi.si@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','kaprodi',NULL,2,2,1,'2026-09-28 14:13:08','2026-10-08 09:09:01'),(26,'Kristophorus Hadiono, Ph.D. (Sekprodi Sistem Informasi)','sekprodi.si@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','sekprodi',NULL,2,2,1,'2026-09-28 14:13:08','2026-10-08 09:09:01'),(28,'Dr. Christian Herdinata, S.E., M.M., CFP (Kaprodi Akuntansi)','kaprodi.akuntansi@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','kaprodi',NULL,4,3,1,'2026-09-28 14:13:08','2026-10-08 09:09:01'),(29,'Y. Denny Krisnamurti, S.E., M.Si. (Sekprodi Akuntansi)','sekprodi.akuntansi@unika.ac.id','$2y$10$1D.ooqvh8BByGxPX.1sBDudL3xuHJJT.FVIwUtF3T.K4oVXrfFX3u','password123','sekprodi',NULL,4,3,1,'2026-09-28 14:13:08','2026-10-08 09:09:01'),(51,'Civitas Akademika / Dosen','pengguna@unika.ac.id','$2y$12$qV2x00AnZvPrTnc4XyhVVeT5tWtTrhws/8S30j2NsG3CrLPjOGY2i','password','pengguna',NULL,NULL,NULL,1,'2026-10-08 08:53:54','2026-10-08 09:09:01'),(52,'Nesia Rani Wijaya (Multi-Role Super User)','nesiaraniwijaya@gmail.com','$2y$10$Nnmj5IjX5Jf70UqbItF2DuUsuoP4KzUb0DC4Qip4Nu2DttV18uRvK',NULL,'super_admin',NULL,NULL,NULL,1,'2026-10-08 14:06:48','2026-10-08 14:06:48'),(53,'Ravy Whienelda (Super Admin)','ravywhienelda@gmail.com','$2y$10$.ndl9veoDz9RhcjPKA0pJ.O0HE8lSHX7RuIk0/qnMCkNdWBh7FAFq',NULL,'super_admin','uploads/avatars/avatar_53_1792040180.png',NULL,NULL,1,'2026-10-08 14:25:34','2026-10-15 11:56:20'),(66,'Sekretaris Lembaga Penjaminan Mutu','lpm.sekretaris@unika.ac.id','$2y$10$2WX0rJZbckjRmRwlFM1akepkFuslChn9EicD/R3Ihd2lq4fu17r.K','password123','sekretaris_lpm',NULL,NULL,NULL,1,'2026-11-12 10:34:58','2026-11-12 10:34:58'),(67,'Ketua Pusat Audit Mutu Internal','lpm.ami@unika.ac.id','$2y$10$2WX0rJZbckjRmRwlFM1akepkFuslChn9EicD/R3Ihd2lq4fu17r.K','password123','ketua_pusat_ami',NULL,NULL,NULL,1,'2026-11-12 10:34:58','2026-11-12 10:34:58'),(68,'Ketua Pusat Pemeringkatan','lpm.pemeringkatan@unika.ac.id','$2y$10$2WX0rJZbckjRmRwlFM1akepkFuslChn9EicD/R3Ihd2lq4fu17r.K','password123','ketua_pusat_pemeringkatan',NULL,NULL,NULL,1,'2026-11-12 10:34:58','2026-11-12 10:34:58');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-12 15:28:11
