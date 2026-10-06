-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 06, 2026 at 04:57 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lpm_scu`
--

-- --------------------------------------------------------

--
-- Table structure for table `akreditasi_prodi`
--

CREATE TABLE `akreditasi_prodi` (
  `id` int(11) NOT NULL,
  `fakultas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_studi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strata` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peringkat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lembaga` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_sk` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_sk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `masa_berlaku` date NOT NULL,
  `file_sertifikat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `akreditasi_prodi`
--

INSERT INTO `akreditasi_prodi` (`id`, `fakultas`, `program_studi`, `strata`, `peringkat`, `lembaga`, `no_sk`, `file_sk`, `masa_berlaku`, `file_sertifikat`, `created_at`) VALUES
(1, 'Arsitektur dan Desain', 'Desain Komunikasi Visual', 'S1', 'Terakreditasi', 'BAN-PT', '419/SK/BAN-PT/Ak.S/2.0/S1/VII/2026', 'sk_1789456912_2026_SKSementaraAkreditasiS1DKV1.pdf', '2027-06-18', 'prodi_1789456912_2026_SertifikatSementaraAkreditasiS1DKV1.pdf', '2026-09-08 06:40:39'),
(2, 'Arsitektur dan Desain', 'Arsitektur', 'S1', 'Unggul', 'BAN-PT', '4762/SK/BAN-PT/Ak.Ppj/S/VI/2024', '2024-SK_AKREDITASI_S1_ARSITEKTUR.pdf', '2029-10-07', '2024-SERTIFIKAT_AKREDITASI_S1_ARSITEKTUR.pdf', '2026-09-08 06:40:39'),
(3, 'Arsitektur dan Desain', 'Arsitektur', 'S2', 'Baik Sekali', 'BAN-PT', '6389/SK/BAN-PT/Ak.Ppj/M/IV/2026', '2026-SK_AKREDITASI_S2_ARSITEKTUR.pdf', '2031-04-23', '2026-SERTIFIKAT_S2_ARSITEKTUR.pdf', '2026-09-08 06:40:39'),
(4, 'Arsitektur dan Desain', 'Arsitektur', 'S3', 'Baik', 'BAN-PT', '3280/SK/BAN-PT/Ak/D/VIII/2023', '2023_SK_AKREDITASI_S3_ARSITEKTUR_BAIK.pdf', '2028-08-22', '2023_SERTIFIKAT_AKREDITASI_S3_ARSITEKTUR_BAIK.pdf', '2026-09-08 06:40:39'),
(5, 'Bahasa dan Seni', 'Sastra Inggris', 'S1', 'Terakreditasi', 'BAN-PT', '349/SK/BAN-PT/Ak.S/2.0/S1/VII/2026', 'sk_1789457192_2026-SKSementaraAkreditasiS1SastraInggris.pdf', '2027-06-03', 'prodi_1789457192_2026-SertifikatSementaraAkreditasiS1SastraInggris.pdf', '2026-09-08 06:40:39'),
(6, 'Ekonomi dan Bisnis', 'Akuntansi', 'S1', 'Unggul', 'LAMEMBA', '2340/DE/A.5/AR.10/VI/2025', '2025_SK_Peringkat_Akreditasi_S1_Akuntansi.pdf', '2030-09-13', '2025_Sertifikat_Akreditasi_S1_Akuntansi.pdf', '2026-09-08 06:40:39'),
(7, 'Ekonomi dan Bisnis', 'Akuntansi', 'S2', 'Unggul', 'LAMEMBA', '1323/DE/A.5/AR.10/VI/2024', '2024_SK_Peringkat_Akreditasi_S2_Akuntansi_UNGGUL.pdf', '2029-06-28', '2024_Sertifikat_Akreditasi_S2_Akuntansi_UNGGUL.pdf', '2026-09-08 06:40:39'),
(8, 'Ekonomi dan Bisnis', 'Manajemen', 'S1', 'Baik Sekali', 'LAMEMBA', '868/DE/A.5/AR.10/XI/2023', '2023-SK_Peringkat_Akreditasi_S1_Manajemen.pdf', '2028-12-12', '2023-Sertifikat_Akreditasi_S1_Manajemen.pdf', '2026-09-08 06:40:39'),
(9, 'Ekonomi dan Bisnis', 'Manajemen', 'S2', 'Unggul', 'LAMEMBA', '2393/DE/A.5/AR.10/VI/2025', '2025-SK_Peringkat_Akreditasi_S2_Manajemen_UNGGUL.pdf', '2030-06-13', '2025-Sertifikat_Peringkat_Akreditasi_S2_Manajemen_UNGGUL.pdf', '2026-09-08 06:40:39'),
(10, 'Hukum dan Komunikasi', 'Ilmu Komunikasi', 'S1', 'Unggul', 'BAN-PT', '728/SK/BAN-PT/Ak/S/III/2024', '2024_SK_AKREDITASI_S1_ILMU_KOMUNIKASI.pdf', '2029-03-05', '2024_SERTIFIKAT_AKREDITASI_S1_ILMU_KOMUNIKASI.pdf', '2026-09-08 06:40:39'),
(11, 'Hukum dan Komunikasi', 'Ilmu Hukum', 'S1', 'Unggul', 'BAN-PT', '6191/SK/BAN-PT/Ak.Ppj/S/X/2024', '2024_SK_Akreditasi_S1_Ilmu_Hukum_UNGGUL.pdf', '2029-10-23', '2024_Sertifikat_Akreditasi_S1_Ilmu_Hukum_UNGGUL.pdf', '2026-09-08 06:40:39'),
(12, 'Hukum dan Komunikasi', 'Ilmu Hukum', 'S2', 'Unggul', 'BAN-PT', '1501/SK/BAN-PT/Ak.Ppj/M/IV/2023', '2023-SK_Akreditasi_S2_HUKES_-_UNGGUL.pdf', '2028-04-18', '2023-Sertifikat_Akreditasi_S2_HUKES_-_UNGGUL.pdf', '2026-09-08 06:40:39'),
(13, 'Ilmu dan Teknologi Lingkungan', 'Rekayasa Infrastruktur dan Lingkungan', 'S1', 'Baik', 'BAN-PT', '4438/SK/BAN-PT/Ak/S/X/2023', '2023-SK_AKREDITASI_S1_RIL_BAIK_1.pdf', '2028-10-31', '2023-SERTIFIKAT_AKREDITASI_S1_RIL_BAIK_1.pdf', '2026-09-08 06:40:39'),
(14, 'Ilmu dan Teknologi Lingkungan', 'Lingkungan dan Perkotaan', 'S2', 'Baik Sekali', 'BAN-PT', '744/SK/BAN-PT/Ak/M/III/2024', NULL, '2029-03-13', NULL, '2026-09-08 06:40:39'),
(15, 'Ilmu dan Teknologi Lingkungan', 'Ilmu Lingkungan', 'S3', 'Terakreditasi', 'BAN-PT', '348/SK/BAN-PT/Ak.S/2.0/S3/VII/2026', 'sk_1789457326_2026_SKSementaraAkreditasiS3IlmuLingkungan.pdf', '2027-06-22', 'prodi_1789457326_2026_SertifikatSementaraAkreditasiS3IlmuLingkungan.pdf', '2026-09-08 06:40:39'),
(16, 'Ilmu Komputer', 'Teknik Informatika', 'S1', 'Unggul', 'LAM INFOKOM', '240/SK/LAM-INFOKOM/Ak/S/XII/2024', '2024_SK_Akreditasi_LAMINFOKOM_S1_Teknik_Informatika.pdf', '2029-12-17', '2024_Sertifikat_Akreditasi_LAMINFOKOM_S1_Teknik_Informatika.pdf', '2026-09-08 06:40:39'),
(17, 'Ilmu Komputer', 'Sistem Informasi', 'S1', 'Unggul', 'LAM INFOKOM', '062/SK/LAM-INFOKOM/Ak/S/VII/2026', '2026_SK_Akreditasi_S1_Sistem_Informasi.pdf', '2031-07-22', 'prodi_1790045594_2026_SertifikatPeringkatAkreditasiS1SistemInformasi.pdf', '2026-09-08 06:40:39'),
(18, 'Kedokteran', 'Kedokteran', 'S1', 'Baik', 'LAM-PTKes', '0031/LAM-PTKes/Akr/Sar/I/2022', '2022-SK_AKREDITASI_S1_KEDOKTERAN_1.pdf', '2027-01-13', '2022-SERTIFIKAT_AKREDITASI_S1_KEDOKTERAN_1.pdf', '2026-09-08 06:40:39'),
(19, 'Kedokteran', 'Pendidikan Profesi Dokter', 'Profesi', 'Baik', 'LAM-PTKes', '0032/LAM-PTKes/Akr/Pro/I/2022', '2022-SK_AKREDITASI_PROFESI_KEDOKTERAN_1.pdf', '2027-01-13', '2022-SERTIFIKAT_AKREDITASI_PRODI_PROFESI_KEDOKTERAN_1.pdf', '2026-09-08 06:40:39'),
(20, 'Psikologi', 'Psikologi', 'S1', 'Baik Sekali', 'BAN-PT', '6835/SK/BAN-PT/Ak/S/VI/2026', '2026_SK_Akreditasi_Baik_Sekali_S1_Psikologi.pdf', '2031-06-23', '2026_Sertifikat_Akreditasi_Baik_Sekali_S1_Psikologi.pdf', '2026-09-08 06:40:39'),
(21, 'Psikologi', 'Psikologi', 'S2', 'Unggul', 'BAN-PT', '4506/SK/BAN-PT/Ak/M/V/2024', '2024_SK_Peringkat_Akreditasi_S2_Psikologi.pdf', '2029-05-28', '2024_Sertifikat_Peringkat_Akreditasi_S2_Psikologi.pdf', '2026-09-08 06:40:39'),
(22, 'Psikologi', 'Pendidikan Profesi Psikolog', 'Profesi', 'Terakreditasi Sementara', 'BAN-PT', '035/SK/BAN-PT/Ak.P/2.0/Profesi/X/2025', '2024_SK_Akreditasi_Profesi_Psikologi.pdf', '2030-10-21', '2024_Sertifikat_Akreditasi_Profesi_Psikologi.pdf', '2026-09-08 06:40:39'),
(23, 'Teknik', 'Teknik Elektro', 'S1', 'Baik Sekali', 'LAM TEKNIK', '0165/SK/LAM Teknik/Smtr/IV/2026', '2021-SK_AKREDITASI_S1_ELEKTRO.pdf', '2026-12-20', '2021-SERTIFIKAT_AKREDITASI_S1_ELEKTRO.pdf', '2026-09-08 06:40:39'),
(24, 'Teknik', 'Teknik Sipil', 'S1', 'Baik Sekali', 'LAM TEKNIK', '0211/SK/LAM Teknik/A5/VIII/2023', '2023_SK_AKREDITASI_S1_TEKNIK_SIPIL_BAIK_SEKALI.pdf', '2028-08-20', '2023_SERTIFIKAT_AKREDITASI_S1_TEKNIK_SIPIL_BAIK_SEKALI.pdf', '2026-09-08 06:40:39'),
(25, 'Teknik', 'Profesi Insinyur', 'Profesi', 'Baik', 'BAN-PT', '590/SK/BAN-PT/Ak/PP/II/2023', '2023_SK_Peringkat_Akreditasi_PPI_Februari2023.pdf', '2028-02-22', 'prodi_1790045935_2023_SertifikatPeringkatAkreditasiPPI_Februari2023.pdf', '2026-09-08 06:40:39'),
(26, 'Teknologi Pertanian', 'Teknologi Pangan', 'S1', 'Unggul', 'BAN-PT', '7076/SK/BAN-PT/Ak-Ppj/S1/VII/2025', '2025_SK_Peringkat_Akreditasi_S1_Teknologi_Pangan.pdf', '2030-07-22', '2025_Sertifikat_Peringkat_Akreditasi_S1_Teknologi_Pangan.pdf', '2026-09-08 06:40:39'),
(27, 'Teknologi Pertanian', 'Teknologi Pangan', 'S2', 'Baik Sekali', 'BAN-PT', '2523/SK/BAN-PT/Ak-PPJ/M/IV/2022', '2022-SK_AKREDITASI_S2_TEKNOLOGI_PANGAN.pdf', '2027-04-19', '2022-SERTIFIKAT_AKREDITASI_S2_TEKNOLOGI_PANGAN.pdf', '2026-09-08 06:40:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akreditasi_prodi`
--
ALTER TABLE `akreditasi_prodi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akreditasi_prodi`
--
ALTER TABLE `akreditasi_prodi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
