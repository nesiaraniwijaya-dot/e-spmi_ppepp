# Catatan Perubahan Database (Database Changelog)
**Proyek:** MITRA (Monitoring dan Implementasi Tahapan PPEPP & Rencana Aksi)  
**Slogan:** *MITRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.*  
**Versi Pembaruan:** 08 Oktober 2026  
**Kolaborator:** Tim Pengembang SPMI PPEPP UNIKA Soegijapranata

---

### Master SQL Terpusat (Single Centralized Database Dump):
Seluruh berkas SQL parsial/terpisah telah dikonsolidasikan ke dalam satu berkas master utama:
- **`database/spmi_ppepp.sql`** (Dump database MySQL lengkap: struktur tabel, relasi, indeks, data master fakultas/prodi, bidang, sub-bidang, dokumen, landing settings, dan akun pengguna).

Berkas SQL parsial lama yang telah dihapus dan disatukan:
- `akreditasi_prodi.sql` (dihapus & dilebur)
- `patch_update_jenis_upload_kombinasi.sql` (dihapus & dilebur)
- `patch_update_petra_and_pengguna_role.sql` (dihapus & dilebur)
- `ppepp.sql` (dihapus & digantikan oleh `spmi_ppepp.sql`)
- `spmi_ppepp_hosting_ready.sql` (dihapus & digantikan oleh `spmi_ppepp.sql`)

---

### Ringkasan Pembaruan Database:
1. **Rebranding MITRA & Pengaturan Footer (`landing_settings`)**:
   - `footer_brand_title`: `MITRA`
   - `footer_brand_sub`: `Monitoring dan Implementasi Tahapan PPEPP & Rencana Aksi`
   - `footer_desc`: `MITRA = Monitoring dan Implementasi Tahapan PPEPP & Rencana Aksi. MITRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.`
   - `footer_website_lpm_url`: `https://lpm.unika.ac.id` (Tautan resmi Website Lembaga Penjaminan Mutu)
   - `footer_website_lpm_text`: `Website Resmi LPM` (Label tombol/tautan footer)
   - `footer_website_custom_url` & `footer_website_custom_text`: Opsi tautan kustom tambahan pada footer.

2. **Perubahan Role Pengguna (`users.role`)**:
   - Ditambahkan opsi enum `'pengguna'`.
   - Role `'pengguna'` dikhususkan bagi civitas akademika/dosen/pengguna terdaftar non-admin.
   - Pengguna dengan role ini hanya mengakses portal publik tetapi diberikan hak akses penuh untuk membaca seluruh lembar dokumen yang sebelumnya dibatasi, serta mengunduh berkas aslinya.
   - Akun pengujian demo: `pengguna@unika.ac.id` / `password`.

3. **Sinkronisasi Data Fakultas (`fakultas`)**:
   - Total fakultas diselaraskan menjadi 10 Fakultas resmi UNIKA Soegijapranata.
   - Fakultas ID 10 ditetapkan sebagai **FITL** (*Fakultas Ilmu dan Teknologi Lingkungan*).

4. **Sinkronisasi Data Program Studi (`prodi`)**:
   - Total program studi disesuaikan menjadi 27 Program Studi terakreditasi resmi.
   - Seluruh program studi S1, S2, S3, dan Profesi dipetakan secara akurat di bawah fakultas masing-masing.

5. **Dukungan Multi-Upload Kombinasi (`ppepp_documents.jenis_upload`)**:
   - Ditambahkan opsi enum `'kombinasi'` pada kolom `jenis_upload`.
   - Mengizinkan satu dokumen mutu mengunggah berkas fisik (PDF/Word/Excel) sekaligus tautan Google Drive secara bersamaan dengan standar mutu masing-masing.

6. **Relasi Multi-Berkas & Multi-Standar (`ppepp_document_files`)**:
   - Tabel `ppepp_document_files` menampung banyak file dalam 1 dokumen.
   - Kolom `sub_bidang_ids` (TEXT, menyimpan referensi multi standar/sub-bidang per berkas).
   - Kolom `narasi` (TEXT, menyimpan catatan naratif penjelasan berkas).
   - Kolom proteksi berkas: `is_page_limited`, `public_page_limit`, `can_download_public`.

7. **Kolom Penyimpanan Kata Sandi Terkelola (`users.password_plain`)**:
   - Ditambahkan kolom `password_plain` (VARCHAR(255) NULL) pada tabel `users`.
   - Mengizinkan Administrator melihat/mengelola kata sandi akun pengguna pada modal edit tanpa perlu reset acak.
   - Telah disinkronkan dan diisi nilai kata sandi default untuk seluruh akun pengguna yang ada.

8. **Alur Review Bertingkat (Prodi/Fakultas -> GPM -> LPM) & Riwayat Revisi (`ppepp_document_reviews`)**:
   - Dibuat tabel baru **`ppepp_document_reviews`** untuk mencatat jejak kronologis setiap keputusan dan catatan perbaikan (kapan direview, oleh siapa, catatan apa, status hasil).
   - Memperluas enum `ppepp_documents.status_review`:
     - `'draft'`: Draf belum diajukan.
     - `'menunggu_review_gpm'`: Dokumen baru diunggah / perbaikan diajukan, menunggu telaah GPM Fakultas.
     - `'revisi_gpm'`: GPM meminta perbaikan kepada Prodi/Fakultas.
     - `'menunggu_review_lpm'`: GPM menyetujui & merekomendasikan ke antrean review LPM.
     - `'revisi_lpm'`: LPM meminta perbaikan kepada Prodi/Fakultas (GPM mendapat tembusan).
     - `'sudah_diperbaiki'`: Prodi/Fakultas telah mengirimkan berkas revisi.
     - `'sesuai'`: Dokumen disahkan final oleh LPM (Sesuai Standar Mutu SPMI).
   - Menambahkan kolom pendukung di `ppepp_documents`:
     - `tahap_review` ENUM('draft', 'gpm', 'lpm', 'selesai').
     - `gpm_reviewed_by`, `gpm_reviewed_at`, `gpm_catatan`.
     - `lpm_reviewed_by`, `lpm_reviewed_at`, `lpm_catatan`.
   - Skrip migrasi terpasang: `database/migration_gpm_review_workflow.php`.

9. **Penyesuaian Siklus PPEPP (`ppepp_cycles`), Pembersihan Data Review Lama LPM, dan Reset Dokumen ke Telaah GPM (15 Oktober 2026)**:
   - **Tabel `ppepp_cycles`**: Menampung siklus berkelanjutan per judul dokumen untuk rekapitulasi capaian mutu pada Review Hub (`SuperAdminController`).
   - **Tabel `ppepp_document_revisions`**: Menampung riwayat versi berkas revisi.
   - **Kolom `ppepp_documents.cycle_id`**: Menghubungkan setiap dokumen ke `ppepp_cycles`.
   - **Reset Status Dokumen ke Antrean Telaah GPM**:
     - Seluruh dokumen mutu yang telah diunggah diatur ulang ke `status_review = 'menunggu_review_gpm'` dan `tahap_review = 'gpm'`.
     - Seluruh catatan review dan evaluator sebelumnya di-reset menjadi `NULL`.
   - **Pembersihan Riwayat Review Lama**:
     - Data review lampau LPM dan catatan uji coba dibersihkan dari `ppepp_document_reviews` & `ppepp_document_revisions` agar proses telaah dokumen oleh GPM dapat dimulai dari awal secara bersih.
   - **Defensive Error Handling**: Ditambahkan blok `try-catch` pada `SuperAdminController`, `AdminProdiController`, dan `AdminFakultasController` agar akses modul tetap aman tanpa unhandled PDOException.
   - Skrip maintenance: `database/reset_reviews_to_gpm_and_cleanup.php`.

10. **Standardisasi Format Tahun Akademik & Kesiapan Dual-Environment (20 Oktober 2026)**:
    - **Format Otomatis Input Tahun Akademik (`YYYY/YYYY`)**:
      - Ditambahkan helper `autoPairTahunAkademik(input, event)` di `views/layouts/admin_header.php` dan `footer.php`.
      - Saat pengguna mengetik 4 digit tahun awal (misal `2026`), sistem secara otomatis menambahkan garis miring dan tahun berikutnya (`2026/2027`).
    - **Perapian Dropdown & Toolbar Filter Tahun Akademik**:
      - Memisahkan baris status tab dan baris toolbar kontrol pada halaman perbaikan dokumen (`admin_fakultas/perbaikan.php`, `admin_prodi/perbaikan.php`, `gpm/perbaikan.php`) agar label dan kontrol tidak bertubrukan/menumpuk.
      - Memperlebar alokasi kolom grid dropdown Tahun Akademik menjadi `col-xl-3 col-lg-3 col-md-6` (dan `min-width: 200px` pada draf) sehingga teks `Semua Tahun Akademik` dan opsi tahun tidak terpotong.
    - **Dukungan Konfigurasi Dual-Environment (Localhost & Hosting)**:
      - Diperkenalkan fungsi `is_localhost()` di `config/app.php` yang secara otomatis mengenali apakah aplikasi berjalan di Localhost (Laragon) atau Server Hosting Online (ProFreeHost / cPanel).
      - File konfigurasi `config/env.php` memisahkan konfigurasi database lokal dan hosting secara otomatis tanpa perlu mengubah kode saat deployment.
    - **Pembaruan Master Database SQL**:
      - Master dump `database/spmi_ppepp.sql` telah disinkronkan dan memuat seluruh data terbaru (10 fakultas, 27 prodi, akun demo `pengguna@unika.ac.id`, kolom `password_plain`, dan riwayat dokumen).

11. **Fitur Tanggal Dokumen, Tenggat Waktu (Deadline) Unggah, & Izin Akses Tahun Lampau (22 Oktober 2026)**:
    - **Kolom `ppepp_documents.tanggal_dokumen` (DATE NULL)**:
      - Menampung tanggal resmi pengesahan/pembuatan dokumen mutu.
      - Menjadi dasar perhitungan otomatis Tahun Akademik (1 September - 31 Agustus).
      - Telah disinkronkan otomatis dari `DATE(created_at)` untuk seluruh dokumen eksisting.
    - **Tabel `academic_year_deadlines`**:
      - Menampung daftar Tahun Akademik, penanda tahun aktif (`is_active`), dan batas akhir waktu unggah (`deadline_upload`).
      - Dikelola langsung oleh LPM pada menu *Akses & Tenggat Upload*.
    - **Tabel `upload_access_requests`**:
      - Menampung pengajuan izin buka akses upload dokumen tahun lampau / susulan dari Program Studi dan Fakultas kepada LPM.
      - Menyimpan status permohonan (`pending`, `approved`, `rejected`), masa berlaku dispensasi (`akses_berlaku_sampai`), dan catatan evaluasi LPM.
    - Skrip migrasi terpasang: `database/migration_fitur_akademik_dan_gpm.php`.

12. **Dukungan Multi-Folder & Pemantauan Lalu Lintas Dokumen LPM (22 Oktober 2026)**:
    - **Tabel `tahun_akademik_settings`**:
      - Menampung master pengaturan periode tahun akademik (`tanggal_mulai`, `tanggal_selesai`, `is_open`, `catatan`).
    - **Kolom `folder_name` pada `ppepp_document_files` (VARCHAR(255) DEFAULT 'Utama')**:
      - Mengizinkan pengelompokan berkas dokumen ke dalam folder-folder terstruktur saat diunggah.
    - **Kolom `file_category` pada `ppepp_document_files` (ENUM('file', 'link', 'photo') DEFAULT 'file')**:
      - Menandai tipe berkas (file fisik dokumen, tautan Google Drive, atau foto pendukung).
13. **Pembersihan Seluruh Dokumen Dummy untuk Pengisian Data Asli (29 Oktober 2026)**:
    - **Backup Otomatis Sebelum Pembersihan**:
      - Seluruh data tabel dummy tersimpan di: `database/backups/backup_dummy_20261029_015709/`.
      - Berkas dump SQL sebelumnya tersimpan di: `database/backups/spmi_ppepp_with_dummy_backup.sql`.
    - **Pembersihan Data Terkait Dokumen**:
      - Menghapus seluruh data dari tabel: `ppepp_documents`, `ppepp_document_files`, `ppepp_document_sub_bidang`, `ppepp_document_file_sub_bidang`, `ppepp_document_reviews`, `ppepp_document_revisions`, `ppepp_cycles`, dan `upload_access_requests`.
      - Menghapus seluruh berkas fisik unggahan dummy dari direktori `uploads/documents/`.
      - Menghapus log audit trail uji coba yang terkait dengan dokumen dummy.
      - Mereset counter `AUTO_INCREMENT = 1` pada seluruh tabel dokumen agar penginputan data asli dimulai dari ID 1.
    - **Master Database Terupdate**:
      - Berkas master `database/spmi_ppepp.sql` telah diperbarui dengan data bersih siap pakai (master fakultas, prodi, standar akreditasi, user, dan setelan tetap utuh).
    - Skrip pembersih mandiri: `database/cleanup_dummy_documents.php`.

14. **Optimasi Indeks Performa Database, Reset Antrean ke GPM, & Pemisahan Privasi Catatan Alur Berjenjang (02 - 10 November 2026)**:
    - **Penambahan 11 Indeks Komposit Performa Query (`database/migration_performance_indexes.sql`)**:
      - Tabel `ppepp_documents`: `idx_docs_filter_status`, `idx_docs_prodi_filter`, `idx_docs_fakultas_filter`, `idx_docs_siklus_filter`, `idx_docs_tahun`, `idx_docs_created_at`.
      - Tabel `ppepp_document_files`: `idx_docfiles_doc_sort`, `idx_docfiles_cat`.
      - Tabel `ppepp_cycles`: `idx_cycles_prodi`, `idx_cycles_fakultas`, `idx_cycles_bidang`.
      - Aman dijalankan (hanya menambah indeks pencarian tanpa mengubah/menghapus data). Tersedia pula skrip PHP idempoten: `database/apply_performance_indexes.php` (melewati indeks yang sudah terpasang).
    - **Semantik Privasi Alur Berjenjang (*Masked Review Flow*: LPM -> GPM -> Prodi/Dekanat)**:
      - Tidak ada perubahan skema kolom baru (memanfaatkan kolom eksisting `status_review = 'revisi_lpm'`, `lpm_catatan`, `gpm_catatan`, `catatan_review`, `ppepp_document_reviews`, dan `ppepp_document_revisions`).
      - Saat LPM mengembalikan dokumen (`status_review = 'revisi_lpm'`, `tahap_review = 'gpm'`), catatan perbaikan internal LPM hanya disimpan ke `lpm_catatan` & `ppepp_document_reviews` (tahap `lpm`) tanpa menimpa `catatan_review` dan tanpa membuat entri di `ppepp_document_revisions`.
      - Setelah GPM menelaah & meneruskan arahan ke unit pengunggah (`gpm/review/forward-lpm`), status berubah ke `'revisi_gpm'` dengan catatan resmi di `gpm_catatan`, `catatan_review`, dan `ppepp_document_revisions` atas nama `GPM Fakultas`.
    - **Skrip Seeder Dummy Uji Coba Alur Berjenjang**:
      - `database/seed_dummy_review_flow.php` menyediakan 7 dokumen dummy (`DUMMY-FLOW/2026/A1..A3, B1..B4`) untuk uji coba skenario disetujui LPM maupun ditolak LPM -> diteruskan GPM -> Prodi/Dekanat.
      - Jalankan `php database/seed_dummy_review_flow.php --clean` untuk menghapus seluruh data dummy uji coba.

15. **Tabel Penilaian Kinerja GPM oleh LPM (`gpm_evaluations`) & Atribusi Multi-Role Per Fakultas (10 November 2026)**:
    - **Tabel Baru `gpm_evaluations` (`database/migration_gpm_evaluations.php`)**:
      - Kolom: `id`, `gpm_user_id`, `fakultas_id`, `evaluator_id`, `evaluator_name`, `evaluator_role`, `periode` (`varchar(50)`, default `'2025/2026'`), `skor_ketelitian` (`tinyint unsigned`, 0-100), `skor_kecepatan` (`tinyint unsigned`, 0-100), `skor_komunikasi` (`tinyint unsigned`, 0-100), `skor_manual_total` (`decimal(5,2)`), `catatan_evaluasi` (`text`), `created_at`, `updated_at`.
      - Indeks: `idx_gpm_eval_user` (`gpm_user_id`, `created_at`), `idx_gpm_eval_fak` (`fakultas_id`, `periode`).
      - **Visibilitas Eksklusif LPM**: Skor otomatis (Akurasi Lolos LPM, Ketuntasan Antrean & Relay LPM, Kecepatan Respon), skor gabungan (`60% Otomatis + 40% Manual LPM`), serta form/catatan evaluasi manual **hanya ditampilkan** pada halaman role LPM (`super_admin`, `admin_lpm`, `kepala_lpm`, `kepala_pusat_mutu` di `/admin/monitoring-gpm`), tidak ditampilkan di dashboard GPM, Prodi, maupun Dekanat.
      - **Atribusi Akun Multi-Role**: Aktivitas telaah & relay dokumen menggunakan akun multi-role (`reviewer_role = 'gpm'`) otomatis dikaitkan ke fakultas sasaran dan digabung ke rekap kartu anggota GPM utama fakultas tersebut tanpa penghitungan ganda.
    - Migrasi bersifat idempoten (`CREATE TABLE IF NOT EXISTS`) dan turut dibuat otomatis oleh `SuperAdminController::ensureGpmEvaluationsTable()`.

---

### Cara Rekan Kolaborator Menerapkan Perubahan Database di Komputer Lokal / Server:
Cukup jalankan skrip migrasi PHP:
```bash
php database/migration_fitur_akademik_dan_gpm.php
php database/migration_lpm_traffic_and_academic_year.php
php database/apply_performance_indexes.php
php database/migration_gpm_evaluations.php
```
atau jalankan kueri SQL `database/migration_performance_indexes.sql` di phpMyAdmin / MySQL CLI, serta impor berkas master bila mulai dari awal:
```bash
mysql -u root spmi_ppepp < database/spmi_ppepp.sql
```
atau melalui phpMyAdmin:
1. Buat/pilih database `spmi_ppepp`.
2. Klik tab **Import**, pilih berkas `database/spmi_ppepp.sql`, lalu klik **Import**.




