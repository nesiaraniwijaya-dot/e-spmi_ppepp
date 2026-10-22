-- Database Schema: SPMI PPEPP UNIKA Soegijapranata
-- Engine: InnoDB, Charset: utf8mb4

CREATE DATABASE IF NOT EXISTS `spmi_ppepp` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `spmi_ppepp`;

-- 1. Table Fakultas
CREATE TABLE IF NOT EXISTS `fakultas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kode_fakultas` VARCHAR(20) NOT NULL UNIQUE,
    `nama_fakultas` VARCHAR(150) NOT NULL,
    `deskripsi` TEXT NULL,
    `logo_fakultas` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table Program Studi (Prodi)
CREATE TABLE IF NOT EXISTS `prodis` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fakultas_id` INT NOT NULL,
    `kode_prodi` VARCHAR(20) NOT NULL UNIQUE,
    `nama_prodi` VARCHAR(150) NOT NULL,
    `jenjang` ENUM('D3', 'S1', 'S2', 'S3', 'Profesi') NOT NULL DEFAULT 'S1',
    `nama_kaprodi` VARCHAR(150) NULL,
    `nidn_kaprodi` VARCHAR(50) NULL,
    `nama_sekprodi` VARCHAR(150) NULL,
    `nidn_sekprodi` VARCHAR(50) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_prodis_fakultas` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table Master Bidang Standar (Kelolaan Admin LPM)
CREATE TABLE IF NOT EXISTS `bidang_standar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kode_bidang` VARCHAR(50) NULL,
    `nama_bidang` VARCHAR(150) NOT NULL,
    `deskripsi` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3b. Table Sub Bidang Standar
CREATE TABLE IF NOT EXISTS `sub_bidang_standar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bidang_id` INT NOT NULL,
    `kode_sub_bidang` VARCHAR(50) NULL,
    `nama_sub_bidang` VARCHAR(150) NOT NULL,
    `deskripsi` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sub_bidang` FOREIGN KEY (`bidang_id`) REFERENCES `bidang_standar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table Users & Roles
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('super_admin', 'admin_lpm', 'kepala_pusat_mutu', 'kepala_lpm', 'dekan', 'wadek', 'gpm', 'kaprodi', 'sekprodi') NOT NULL,
    `prodi_id` INT NULL,
    `fakultas_id` INT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodis`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_users_fakultas` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table Dokumen PPEPP
CREATE TABLE IF NOT EXISTS `ppepp_documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `prodi_id` INT NULL,
    `fakultas_id` INT NULL,
    `user_id` INT NOT NULL,
    `bidang_id` INT NULL,
    `sub_bidang_id` INT NULL,
    `nama_dokumen` VARCHAR(255) NOT NULL,
    `nomor_dokumen` VARCHAR(100) NULL,
    `siklus` ENUM('penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan') NOT NULL,
    `tahun_akademik` VARCHAR(20) NOT NULL,
    `jenis_upload` ENUM('file', 'link') NOT NULL DEFAULT 'file',
    `file_path` VARCHAR(255) NULL,
    `file_size` VARCHAR(50) NULL,
    `file_extension` VARCHAR(10) NULL,
    `external_link` TEXT NULL,
    `public_page_limit` INT NOT NULL DEFAULT 3,
    `can_download_public` TINYINT(1) NOT NULL DEFAULT 0,
    `level` ENUM('universitas', 'fakultas', 'prodi') NOT NULL DEFAULT 'prodi',
    `status_review` ENUM('draft', 'belum_direview', 'perlu_perbaikan', 'sudah_diperbaiki', 'sesuai') NOT NULL DEFAULT 'draft',
    `reviewed_by` INT NULL,
    `reviewed_at` DATETIME NULL,
    `catatan_review` TEXT NULL,
    `deskripsi` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    CONSTRAINT `fk_docs_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodis`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_docs_fakultas` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_docs_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_docs_bidang` FOREIGN KEY (`bidang_id`) REFERENCES `bidang_standar`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_docs_sub_bidang` FOREIGN KEY (`sub_bidang_id`) REFERENCES `sub_bidang_standar`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5b. Table Lampiran Berkas Dokumen PPEPP (Multi-Files)
CREATE TABLE IF NOT EXISTS `ppepp_document_files` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `document_id` INT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_size` INT NOT NULL DEFAULT 0,
    `file_extension` VARCHAR(10) NOT NULL DEFAULT 'pdf',
    `narasi` TEXT NULL,
    `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_doc_files_doc` FOREIGN KEY (`document_id`) REFERENCES `ppepp_documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table Audit Logs (Audit Trail)
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `user_name` VARCHAR(150) NOT NULL,
    `user_role` VARCHAR(50) NOT NULL,
    `prodi_id` INT NULL,
    `prodi_name` VARCHAR(150) NULL,
    `aksi` ENUM('CREATE', 'UPDATE', 'DELETE', 'RESTORE', 'LOGIN', 'LOGOUT') NOT NULL,
    `modul` VARCHAR(100) NOT NULL,
    `target_id` VARCHAR(100) NULL,
    `target_name` VARCHAR(255) NULL,
    `old_values` LONGTEXT NULL,
    `new_values` LONGTEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_prodi` (`prodi_id`),
    INDEX `idx_aksi` (`aksi`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
