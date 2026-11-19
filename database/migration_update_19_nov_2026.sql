-- ========================================================
-- MIGRASI TERPADU PEMBARUAN DATABASE (19 NOVEMBER 2026)
-- SPMI PPEPP UNIKA SOEGIJAPRANATA
-- Dapat langsung dijalankan di phpMyAdmin / MySQL CLI
-- ========================================================

-- --------------------------------------------------------
-- 1. PENAMBAHAN KOLOM IKON & WARNA FAKULTAS (tabel fakultas)
-- --------------------------------------------------------
ALTER TABLE `fakultas` 
  ADD COLUMN IF NOT EXISTS `icon` VARCHAR(50) NOT NULL DEFAULT 'fa-landmark' AFTER `deskripsi`,
  ADD COLUMN IF NOT EXISTS `warna` VARCHAR(30) NOT NULL DEFAULT '#1E3E62' AFTER `icon`;

UPDATE `fakultas` SET `icon` = 'fa-compass-drafting', `warna` = '#7C3AED' WHERE `kode_fakultas` = 'FAD';
UPDATE `fakultas` SET `icon` = 'fa-laptop-code',      `warna` = '#2563EB' WHERE `kode_fakultas` IN ('FIKOM', 'FIK');
UPDATE `fakultas` SET `icon` = 'fa-chart-line',        `warna` = '#059669' WHERE `kode_fakultas` = 'FEB';
UPDATE `fakultas` SET `icon` = 'fa-language',          `warna` = '#C2410C' WHERE `kode_fakultas` = 'FBS';
UPDATE `fakultas` SET `icon` = 'fa-gears',             `warna` = '#D97706' WHERE `kode_fakultas` = 'FT';
UPDATE `fakultas` SET `icon` = 'fa-brain',             `warna` = '#0D9488' WHERE `kode_fakultas` = 'FPSI';
UPDATE `fakultas` SET `icon` = 'fa-scale-balanced',    `warna` = '#BE123C' WHERE `kode_fakultas` = 'FHK';
UPDATE `fakultas` SET `icon` = 'fa-seedling',          `warna` = '#15803D' WHERE `kode_fakultas` = 'FTP';
UPDATE `fakultas` SET `icon` = 'fa-stethoscope',       `warna` = '#0284C7' WHERE `kode_fakultas` = 'FK';
UPDATE `fakultas` SET `icon` = 'fa-leaf',              `warna` = '#059669' WHERE `kode_fakultas` = 'FITL';
UPDATE `fakultas` SET `icon` = 'fa-lightbulb',         `warna` = '#4F46E5' WHERE `kode_fakultas` = 'PIP';

-- --------------------------------------------------------
-- 2. TABEL MATRIKS HAK AKSES MENU LPM (lpm_menu_permissions)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lpm_menu_permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role` VARCHAR(50) NOT NULL,
    `menu_key` VARCHAR(50) NOT NULL,
    `can_view` TINYINT(1) NOT NULL DEFAULT 1,
    `can_action` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_role_menu` (`role`, `menu_key`),
    INDEX `idx_role` (`role`),
    INDEX `idx_menu_key` (`menu_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lpm_menu_permissions` (`role`, `menu_key`, `can_view`, `can_action`) VALUES
-- Kepala LPM
('kepala_lpm', 'dashboard', 1, 1),
('kepala_lpm', 'review_dokumen', 1, 1),
('kepala_lpm', 'monitoring_berkas', 1, 1),
('kepala_lpm', 'monitoring_gpm', 1, 1),
('kepala_lpm', 'upload_access', 1, 1),
('kepala_lpm', 'master_fakultas', 1, 1),
('kepala_lpm', 'master_prodi', 1, 1),
('kepala_lpm', 'master_bidang', 1, 1),
-- Sekretaris LPM (Hanya Pantau pada Review Dokumen)
('sekretaris_lpm', 'dashboard', 1, 1),
('sekretaris_lpm', 'review_dokumen', 1, 0),
('sekretaris_lpm', 'monitoring_berkas', 1, 1),
('sekretaris_lpm', 'monitoring_gpm', 1, 1),
('sekretaris_lpm', 'upload_access', 1, 1),
('sekretaris_lpm', 'master_fakultas', 1, 1),
('sekretaris_lpm', 'master_prodi', 1, 1),
('sekretaris_lpm', 'master_bidang', 1, 1),
-- Ketua Pusat SPM
('ketua_pusat_spm', 'dashboard', 1, 1),
('ketua_pusat_spm', 'review_dokumen', 1, 1),
('ketua_pusat_spm', 'monitoring_berkas', 1, 1),
('ketua_pusat_spm', 'monitoring_gpm', 1, 1),
('ketua_pusat_spm', 'upload_access', 1, 1),
('ketua_pusat_spm', 'master_fakultas', 1, 1),
('ketua_pusat_spm', 'master_prodi', 1, 1),
('ketua_pusat_spm', 'master_bidang', 1, 1),
-- Ketua Pusat AMI
('ketua_pusat_ami', 'dashboard', 1, 1),
('ketua_pusat_ami', 'review_dokumen', 1, 1),
('ketua_pusat_ami', 'monitoring_berkas', 1, 1),
('ketua_pusat_ami', 'monitoring_gpm', 1, 1),
('ketua_pusat_ami', 'upload_access', 1, 1),
('ketua_pusat_ami', 'master_fakultas', 1, 1),
('ketua_pusat_ami', 'master_prodi', 1, 1),
('ketua_pusat_ami', 'master_bidang', 1, 1),
-- Ketua Pusat Pemeringkatan
('ketua_pusat_pemeringkatan', 'dashboard', 1, 1),
('ketua_pusat_pemeringkatan', 'review_dokumen', 1, 1),
('ketua_pusat_pemeringkatan', 'monitoring_berkas', 1, 1),
('ketua_pusat_pemeringkatan', 'monitoring_gpm', 1, 1),
('ketua_pusat_pemeringkatan', 'upload_access', 1, 1),
('ketua_pusat_pemeringkatan', 'master_fakultas', 1, 1),
('ketua_pusat_pemeringkatan', 'master_prodi', 1, 1),
('ketua_pusat_pemeringkatan', 'master_bidang', 1, 1)
ON DUPLICATE KEY UPDATE can_action = VALUES(can_action), updated_at = updated_at;

-- --------------------------------------------------------
-- 3. PEMBARUAN AKUN tu.lpm@unika.ac.id MENJADI ROOT ADMIN
-- --------------------------------------------------------
UPDATE `users` 
SET `name` = 'Admin', 
    `role` = 'super_admin', 
    `is_active` = 1, 
    `prodi_id` = NULL, 
    `fakultas_id` = NULL 
WHERE LOWER(`email`) = 'tu.lpm@unika.ac.id';

