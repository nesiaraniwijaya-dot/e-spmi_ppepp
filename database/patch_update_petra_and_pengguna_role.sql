-- ==============================================================================
-- PATCH MIGRATION: UPDATE SISTEM PETRA & ROLE PENGGUNA TERDAFTAR
-- Proyek: PETRA (PEmantauan Tahapan PPEPP & Rencana Aksi) - UNIKA Soegijapranata
-- Tanggal: 06 Oktober 2026
-- ==============================================================================

-- 1. Perbarui ENUM role pada tabel `users` untuk menyertakan role 'pengguna'
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('super_admin','admin_lpm','kepala_pusat_mutu','kepala_lpm','dekan','wadek','gpm','kaprodi','sekprodi','pengguna') NOT NULL;

-- 2. Sinkronisasi 10 Fakultas
INSERT INTO `fakultas` (`id`, `kode_fakultas`, `nama_fakultas`, `warna_badge`, `nama_dekan`, `nidn_dekan`, `nama_wadek`, `nidn_wadek`) VALUES
(1, 'FAD', 'Fakultas Arsitektur dan Desain', '#1E3E62', 'Dr. Ir. Budi Santoso, M.T.', '0601017001', 'Dr. Maria Ulfah, S.T., M.Sc.', '0602027501'),
(2, 'FIKOM', 'Fakultas Ilmu Komputer', '#6B21A8', 'Dr. Bernardinus Harnadi, M.T.', '0612057301', 'Erdhi Widyarto Nugroho, S.T., M.T.', '0624087801'),
(3, 'FEB', 'Fakultas Ekonomi dan Bisnis', '#0D9488', 'Dr. Hendra Wijaya, S.E., M.Si., Ak.', '0615037101', 'Dra. Endang Supriyati, M.M.', '0618077401'),
(4, 'FBS', 'Fakultas Bahasa dan Seni', '#B45309', 'Dr. Fransiska Dewi, S.S., M.Hum.', '0610107601', 'Antonius Budi, S.Pd., M.A.', '0614058001'),
(5, 'FT', 'Fakultas Teknik', '#C0392B', 'Dr. Ir. Robertus Wahyu, M.T.', '0605056801', 'Ir. Yohanes Dwi, M.Eng.', '0609097301'),
(6, 'FPSI', 'Fakultas Psikologi', '#7C3AED', 'Dr. Elizabeth Kristina, M.Si., Psikolog', '0620047201', 'Agustinus Tri, S.Psi., M.Psi.', '0625117901'),
(7, 'FHK', 'Fakultas Hukum dan Komunikasi', '#D97706', 'Dr. Yohanes Suhardi, S.H., M.Hum.', '0603036901', 'Theresia Anita, S.H., M.H.', '0607077701'),
(8, 'FTP', 'Fakultas Teknologi Pertanian', '#059669', 'Dr. Ir. Vincentius Surya, M.P.', '0611116701', 'Dr. Maria Goretti, S.T.P., M.Sc.', '0616087501'),
(9, 'FK', 'Fakultas Kedokteran', '#2563EB', 'dr. Andreas Budi, Sp.PD., K-GEH', '0604046501', 'dr. Christina Retno, M.Biomed.', '0608088101'),
(10, 'FITL', 'Fakultas Ilmu dan Teknologi Lingkungan', '#0F766E', 'Dr. Ir. Ign. Slamet Rahardjo, M.Si.', '0612086901', 'Dr. Maria Angela, S.T., M.Env.', '0617047701')
ON DUPLICATE KEY UPDATE 
    `kode_fakultas` = VALUES(`kode_fakultas`),
    `nama_fakultas` = VALUES(`nama_fakultas`),
    `warna_badge` = VALUES(`warna_badge`);

-- 3. Hapus data prodi dummy yang tidak terakreditasi resmi & tidak memiliki relasi dokumen
DELETE FROM `prodis` WHERE `id` IN (3, 6, 15, 21, 22) AND (SELECT COUNT(*) FROM `ppepp_documents` WHERE `prodi_id` = `prodis`.`id`) = 0;

-- 4. Sinkronisasi nama dan jenjang pada 27 Program Studi
INSERT INTO `prodis` (`id`, `fakultas_id`, `kode_prodi`, `nama_prodi`, `jenjang`) VALUES
(1, 2, 'TI', 'Teknik Informatika', 'S1'),
(2, 2, 'SI', 'Sistem Informasi', 'S1'),
(4, 3, 'AKT', 'Akuntansi', 'S1'),
(5, 3, 'MNJ', 'Manajemen', 'S1'),
(7, 1, 'ARS', 'Arsitektur', 'S1'),
(8, 1, 'DKV', 'Desain Komunikasi Visual', 'S1'),
(9, 5, 'TS', 'Teknik Sipil', 'S1'),
(10, 5, 'TE', 'Teknik Elektro', 'S1'),
(11, 6, 'PSI', 'Psikologi', 'S1'),
(12, 7, 'HKM', 'Ilmu Hukum', 'S1'),
(13, 7, 'IKOM', 'Ilmu Komunikasi', 'S1'),
(14, 8, 'TP', 'Teknologi Pangan', 'S1'),
(16, 9, 'KED', 'Kedokteran', 'S1'),
(17, 9, 'PROF-KED', 'Pendidikan Profesi Dokter', 'Profesi'),
(18, 4, 'ING', 'Sastra Inggris', 'S1'),
(19, 3, 'MAG-MNJ', 'Manajemen', 'S2'),
(20, 6, 'MAG-PSI', 'Psikologi', 'S2'),
(23, 1, 'MAG-ARS', 'Arsitektur', 'S2'),
(24, 1, 'DOK-ARS', 'Arsitektur', 'S3'),
(25, 3, 'MAG-AKT', 'Akuntansi', 'S2'),
(26, 7, 'MAG-HKM', 'Ilmu Hukum', 'S2'),
(27, 10, 'RIL', 'Rekayasa Infrastruktur dan Lingkungan', 'S1'),
(28, 10, 'MAG-LP', 'Lingkungan dan Perkotaan', 'S2'),
(29, 10, 'DOK-IL', 'Ilmu Lingkungan', 'S3'),
(30, 6, 'PROF-PSI', 'Pendidikan Profesi Psikolog', 'Profesi'),
(31, 5, 'PROF-INS', 'Profesi Insinyur', 'Profesi'),
(32, 8, 'MAG-TP', 'Teknologi Pangan', 'S2')
ON DUPLICATE KEY UPDATE 
    `fakultas_id` = VALUES(`fakultas_id`),
    `nama_prodi` = VALUES(`nama_prodi`),
    `jenjang` = VALUES(`jenjang`);

-- 5. Tambah akun demo pengguna terdaftar (Akses Dokumen Lengkap di Web Publik)
INSERT INTO `users` (`id`, `fakultas_id`, `prodi_id`, `name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`)
VALUES (51, NULL, NULL, 'Civitas Akademika / Dosen', 'pengguna@unika.ac.id', '$2y$12$eXvA8Wf2f7q82kOqN.d34.UeQ4Y9Jz6yvI43jG3.uG.qL8V6bE0qG', 'pengguna', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `role` = 'pengguna', `is_active` = 1;

-- 6. Perbarui Branding PETRA pada landing_settings
UPDATE `landing_settings` SET `setting_value` = 'PETRA' WHERE `setting_key` = 'footer_brand_title';
UPDATE `landing_settings` SET `setting_value` = 'PEmantauan Tahapan PPEPP & Rencana Aksi' WHERE `setting_key` = 'footer_brand_sub';
UPDATE `landing_settings` SET `setting_value` = 'PETRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.' WHERE `setting_key` = 'footer_desc';
