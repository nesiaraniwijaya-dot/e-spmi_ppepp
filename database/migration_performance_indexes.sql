-- ========================================================
-- OPTIMASI INDEX DATABASE SPMI PPEPP UNIKA SOEGIJAPRANATA
-- Jalankan query ini di phpMyAdmin pada database spmi_ppepp
-- Aman dijalankan, tidak mengubah maupun menghapus data.
-- ========================================================

-- 1. Index Komposit pada Tabel ppepp_documents (Pusat Dokumen Mutu)
ALTER TABLE `ppepp_documents`
  ADD INDEX `idx_docs_filter_status` (`deleted_at`, `status_review`),
  ADD INDEX `idx_docs_prodi_filter` (`prodi_id`, `deleted_at`, `status_review`, `siklus`),
  ADD INDEX `idx_docs_fakultas_filter` (`fakultas_id`, `level`, `deleted_at`, `status_review`),
  ADD INDEX `idx_docs_siklus_filter` (`siklus`, `deleted_at`, `status_review`),
  ADD INDEX `idx_docs_tahun` (`tahun_akademik`, `deleted_at`),
  ADD INDEX `idx_docs_created_at` (`created_at`);

-- 2. Index pada Tabel ppepp_document_files (Lampiran Berkas & Link GDrive)
ALTER TABLE `ppepp_document_files`
  ADD INDEX `idx_docfiles_doc_sort` (`document_id`, `sort_order`),
  ADD INDEX `idx_docfiles_cat` (`file_category`);

-- 3. Index Komposit pada Tabel ppepp_cycles (Judul Aspek Siklus PPEPP)
ALTER TABLE `ppepp_cycles`
  ADD INDEX `idx_cycles_prodi` (`prodi_id`, `deleted_at`),
  ADD INDEX `idx_cycles_fakultas` (`fakultas_id`, `level`, `deleted_at`),
  ADD INDEX `idx_cycles_bidang` (`bidang_id`, `sub_bidang_id`);
