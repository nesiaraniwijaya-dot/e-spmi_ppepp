-- ========================================================
-- MIGRASI PENAMBAHAN KOLOM IKON & WARNA FAKULTAS
-- SPMI PPEPP UNIKA SOEGIJAPRANATA
-- Jalankan query ini di phpMyAdmin pada database SPMI
-- ========================================================

-- 1. Tambahkan kolom icon dan warna pada tabel fakultas (jika belum ada)
ALTER TABLE `fakultas` 
  ADD COLUMN IF NOT EXISTS `icon` VARCHAR(50) NOT NULL DEFAULT 'fa-landmark' AFTER `deskripsi`,
  ADD COLUMN IF NOT EXISTS `warna` VARCHAR(30) NOT NULL DEFAULT '#1E3E62' AFTER `icon`;

-- 2. Update Ikon & Warna Default Fakultas UNIKA Soegijapranata
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
