# Catatan Perubahan Database (Database Changelog)
**Proyek:** MITRA (Monitoring dan Implementasi Tahapan PPEPP & Rencana Aksi)  
**Slogan:** *MITRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.*  
**Versi Pembaruan:** 08 Oktober 2026  
**Kolaborator:** Tim Pengembang SPMI PPEPP UNIKA Soegijapranata

---

### Master SQL Terpusat (Single Centralized Database Dump):
Seluruh berkas SQL parsial/terpisah telah dikonsolidasikan ke dalam satu berkas master utama:
- **`database/spmi_ppepp.sql`** (Dump database MySQL lengkap: struktur tabel, relasi, indeks, data master fakultas/prodi, bidang, sub-bidang, dokumen, landing settings, dan akun pengguna).

Berkas SQL parsial/patch yang telah dihapus dan disatukan ke dalam `spmi_ppepp.sql`:
- `migration_fakultas_icon_warna.sql` (dihapus & dilebur ke `spmi_ppepp.sql`)
- `migration_performance_indexes.sql` (dihapus & dilebur ke `spmi_ppepp.sql`)
- `migration_update_19_nov_2026.sql` (dihapus & dilebur ke `spmi_ppepp.sql`)
- `seed_official_users_19_nov_2026.sql` (dihapus & dilebur ke `spmi_ppepp.sql`)
- `akreditasi_prodi.sql` (dihapus & dilebur ke `spmi_ppepp.sql`)
- `patch_update_jenis_upload_kombinasi.sql` (dihapus & dilebur ke `spmi_ppepp.sql`)
- `patch_update_petra_and_pengguna_role.sql` (dihapus & dilebur ke `spmi_ppepp.sql`)
- `ppepp.sql` (dihapus & digantikan oleh `spmi_ppepp.sql`)
- `spmi_ppepp_hosting.sql` (dihapus & digantikan oleh `spmi_ppepp.sql`)
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

16. **Penyelarasan 6 Role Resmi LPM, Perbaikan `jenis_upload`, & Sinkronisasi Master SQL (12 November 2026)**:
    - **Pembaruan `users.role` ENUM & 6 Akun Resmi LPM (`database/migration_lpm_roles.php`)**:
      - Menambahkan enum role resmi LPM pada tabel `users`: `'sekretaris_lpm'`, `'ketua_pusat_spm'`, `'ketua_pusat_ami'`, `'ketua_pusat_pemeringkatan'`, `'tu_lpm'`.
      - Memigrasikan/menyinkronkan 6 akun resmi LPM (`lpm@unika.ac.id`, `lpm.sekretaris@unika.ac.id`, `lpm.spm@unika.ac.id`, `lpm.ami@unika.ac.id`, `lpm.pemeringkatan@unika.ac.id`, `tu.lpm@unika.ac.id`) dan menormalkan role lama `kepala_pusat_mutu`/`admin_lpm` menjadi `'kepala_lpm'`.
    - **Sinkronisasi Kolom `ppepp_documents.jenis_upload` (`database/migration_multi_upload_kombinasi.php`)**:
      - Memastikan kolom `ppepp_documents.jenis_upload` bertipe `ENUM('file', 'link', 'kombinasi') NOT NULL DEFAULT 'file'` di database aktif (sebelumnya sempat tertinggal `ENUM('file', 'link')` tanpa `'kombinasi'`).
    - **Pembaruan Master Schema & Seed Dump (`database/spmi_ppepp.sql`)**:
      - Disinkronkan ulang terhadap seluruh 18 tabel MySQL aktif: menyertakan definisi tabel `gpm_evaluations`, seluruh 11 indeks performa (`idx_docs_*`, `idx_docfiles_*`, `idx_cycles_*`), `users.role` ENUM terbaru, `jenis_upload` `'kombinasi'`, serta data master & akun pengguna terbaru (tabel transaksional tetap kosong siap pakai).
    - **Skrip Migrasi Gabungan Idempoten (`database/migrate_all.php`)**:
      - Menyatukan langkah sinkronisasi skema terbaru (`jenis_upload`, `gpm_evaluations`, 11 indeks performa, dan role LPM) dalam satu perintah aman tanpa menghapus data dokumen yang sudah ada.

17. **Fitur Ganti Nama (Rename) Berkas Dokumen & Smart Mode Upload Tanpa Folder (17 November 2026)**:
    - **Pemanfaatan Kolom Eksisting**:
      - Kolom `ppepp_document_files.file_name` kini menampung nama berkas kustom/rename yang diinput pengguna saat pengunggahan (batch/single) maupun saat pengeditan dokumen mutu.
      - Kolom `ppepp_document_files.folder_name` secara default diisi `'Utama'` ketika pengguna menggunakan *Smart Mode* (1 berkas / unggah langsung tanpa membuat struktur folder tambahan).
    - **Status Skema Database**:
      - **TIDAK ADA PERUBAHAN SKEMA BARU**: Perubahan ini sepenuhnya menggunakan skema kolom eksisting yang telah ada di database (`file_name` dan `folder_name` pada tabel `ppepp_document_files`). Rekan kolaborator tidak perlu menjalankan migrasi `ALTER TABLE` tambahan dan struktur data tetap 100% kompatibel antar-pengembang.

18. **Tabel Hak Akses Menu LPM (`lpm_menu_permissions`) & Akun Admin `tu.lpm@unika.ac.id` (19 November 2026)**:
    - **Pembaruan Akun Admin LPM**:
      - Akun `tu.lpm@unika.ac.id` yang sebelumnya berlabel TU LPM diperbarui menjadi role `super_admin` dengan nama/label `Admin`.
      - Memiliki hak akses penuh sebagai Root Administrator, termasuk kontrol eksklusif atas menu *Kelola Beranda & Footer* (`admin/landing-settings`), *Manajemen Pengguna* (`admin/users`), dan *Hak Akses Menu LPM* (`admin/menu-permissions`).
    - **Tabel Baru `lpm_menu_permissions`**:
      - Kolom: `id` (int PK auto_increment), `role` (`varchar(50)`), `menu_key` (`varchar(50)`), `can_view` (`tinyint(1)` default 1), `can_action` (`tinyint(1)` default 1), `created_at`, `updated_at`.
      - Unique Key: `uk_role_menu` (`role`, `menu_key`).
      - Menyimpan konfigurasi hak akses granular untuk 5 role struktural LPM (`kepala_lpm`, `sekretaris_lpm`, `ketua_pusat_spm`, `ketua_pusat_ami`, `ketua_pusat_pemeringkatan`) atas 8 menu institusional.
      - Tingkat akses membedakan: Visibilitas (`can_view`) dan Otoritas Aksi (`can_action` - jika 0 menjadi *Read-Only*).
    - **Skrip Migrasi**:
      - `database/migration_lpm_menu_permissions.php` (idempoten dan otomatis diintegrasikan ke `database/migrate_all.php`).

19. **Penambahan Kolom Ikon & Warna Fakultas (`fakultas.icon`, `fakultas.warna`) (19 November 2026)**:
    - **Penambahan Kolom pada Tabel `fakultas`**:
      - Kolom `icon` (`varchar(50)`, default `'fa-landmark'`) dan kolom `warna` (`varchar(30)`, default `'#1E3E62'`).
      - Digunakan untuk kustomisasi visual ikon dan aksen warna tiap fakultas pada landing page publik dan panel admin.
    - **Skrip Migrasi**:
      - `database/migration_fakultas_icon_warna.sql` dan `database/migration_fakultas_icon_warna.php` (idempoten dan terdaftar di `database/migrate_all.php`).

20. **Hak Akses Khusus Pantau Review (TU & Sekretaris LPM) & Notifikasi Email Pengajuan Akses Upload (`core/MailHelper.php`) (19 November 2026)**:
    - **Pembatasan Aksi Review Dokumen**:
      - Akun Admin/TU LPM (`tu.lpm@unika.ac.id`) dan role `sekretaris_lpm` dikunci secara sistem menjadi mode **Hanya Pantau (Monitoring)** pada menu Review Dokumen.
      - Tombol aksi evaluasi mutu ("Setujui" & "Minta Revisi") disembunyikan dan digantikan badge *"Hanya Pantau"*, serta controller memblokir approval/revisi langsung dari kedua akun tersebut demi menjaga tata kelola penjaminan mutu.
    - **Notifikasi Email Permohonan Buka Akses Upload**:
      - Saat Program Studi / Fakultas mengajukan permohonan izin buka akses upload dokumen periode tertutup, sistem otomatis mengirimkan email notifikasi ke `tu.lpm@unika.ac.id`.
      - Dikelola melalui helper baru: `core/MailHelper.php` dengan fail-safe logging di `storage/logs/mail.log`.
    - **File SQL Terpadu 19 November 2026**:
      - `database/migration_update_19_nov_2026.sql` (menggabungkan seluruh perubahan kolom fakultas, tabel hak akses menu, izin role sekretaris, dan update akun admin).

21. **Sinkronisasi 53 Akun Pengguna Resmi Institusi SPMI PPEPP UNIKA (19 November 2026)**:
    - **Cakupan Akun Resmi**:
      - **6 Akun Tim LPM**: Kepala LPM (`lpm@unika.ac.id`), Sekretaris LPM (`lpm.sekretaris@unika.ac.id`), Ketua Pusat SPM (`lpm.spm@unika.ac.id`), Ketua Pusat AMI (`lpm.ami@unika.ac.id`), Ketua Pusat Pemeringkatan (`lpm.pemeringkatan@unika.ac.id`), Admin TU LPM (`tu.lpm@unika.ac.id`).
      - **10 Akun Koordinator GPM Fakultas**: FTP (`pratiwi@unika.ac.id`), FT (`budi@unika.ac.id`), FIKOM (`erdhi@unika.ac.id`), FEB (`hardjanti@unika.ac.id`), FHK (`negorotheo@unika.ac.id`), FPSI (`cicih@unika.ac.id`), FAD (`ardiyanto@unika.ac.id`), FK (`lidwina@unika.ac.id`), FBS (`ekawati@unika.ac.id`), FITL (`dhiyan.krishna@unika.ac.id`).
      - **37 Akun Official Auditee**: 10 Dekan Fakultas (`dekan`) dan 27 Ketua Program Studi (`kaprodi`) untuk jenjang S1, S2, S3, dan Profesi se-lingkungan UNIKA Soegijapranata.
    - **Password Default Awal**:
      - Ditetapkan standar `password123` (tersimpan baik dalam format hash Bcrypt maupun `password_plain` untuk kemudahan administrasi root/admin).
    - **Penyesuaian & Koreksi Data**:
      - Koreksi domain email GPM FEB dari `hardjanti@unika.ac` menjadi `hardjanti@unika.ac.id`.
      - Pemilihan email prodi tunggal S2 Magister Teknologi Pangan `pmtp@unika.ac.id` (dari data ganda `pmtp@unika.ac.id / wd3.ftp@unika.ac.id`).
      - Penataan prodi FIKOM: `ti@unika.ac.id` (Dr. Albertus Dwiyoga Widiantoro) -> S1 Teknik Informatika (prodi_id 1), dan `si@unika.ac.id` (Yonathan Purbo Santosa) -> S1 Sistem Informasi (prodi_id 2).
    - **Pembersihan Akun Dummy Lama & Transfer Kepemilikan Dokumen**:
      - 12 akun lama/dummy tingkat GPM (`gpm.fikom@unika.ac.id`), Dekanat (`dekan.fikom`, `wadek.fikom`, `dekan.feb`, `wadekfeb`), Prodi (`kaprodi.ti`, `sekprodi.ti`, `kaprodi.si`, `sekprodi.si`, `kaprodi.akuntansi`, `sekprodi.akuntansi`), dan civitas (`pengguna@unika.ac.id`) telah dibersihkan/dihapus secara aman.
      - Sebanyak 1.676 berkas dokumen dan 1.282 siklus yang sebelumnya terkait dengan akun dummy telah ditransfer kepemilikannya ke akun resmi baru (`ti@unika.ac.id`, `si@unika.ac.id`, `akuntansi@unika.ac.id`, `fikom@unika.ac.id`) sehingga data institusi tetap 100% utuh tanpa kehilangan riwayat.
      - Skrip: `database/cleanup_old_dummy_users.php` (otomatis terintegrasi di `database/migrate_all.php`).
    - **Skrip Seeder & Migrasi**:
      - PHP Seeder: `database/sync_official_users.php` & `database/cleanup_old_dummy_users.php` (otomatis terintegrasi di `database/migrate_all.php`).

22. **Pengesahan Otomatis Dokumen Lampau & Penyiapan Reviu Periode 2026/2027 (19 November 2026)**:
    - **Pengesahan Dokumen Lampau (< 2026/2027)**:
      - Seluruh 9.083 dokumen yang diunggah untuk tahun akademik lampau (2021/2022 s/d 2025/2026) langsung disahkan/disetujui (`status_review = 'sesuai'`, `tahap_review = 'selesai'`).
      - Riwayat reviu dilengkapi catatan pengesahan institusional serta ditautkan ke Kepala LPM (`lpm@unika.ac.id`) dan Koordinator GPM fakultas terkait.
    - **Penyiapan Antrean Reviu Tahun Akademik Berjalan (2026/2027)**:
      - Dokumen pada tahun akademik aktif/berjalan 2026/2027 disiapkan dalam antrean reviu berjenjang:
        - Tahap 1: Reviu oleh Koordinator GPM Fakultas masing-masing (`status_review = 'menunggu_review_gpm'`, `tahap_review = 'gpm'`).
        - Tahap 2: Reviu oleh Tim LPM institusi setelah diteruskan oleh GPM.
    - **Skrip Pembaruan**:
      - `database/approve_historical_documents.php` (terdaftar dan otomatis dieksekusi melalui `database/migrate_all.php`).

23. **Pemusatan Berkas SQL ke Satu Master Tunggal (`database/spmi_ppepp.sql`) (19 November 2026)**:
    - Seluruh berkas SQL pecahan/patch (`migration_...sql`, `seed_...sql`, `ppepp.sql`, `spmi_ppepp_hosting.sql`) telah dihapus dan dilebur menjadi satu file SQL master tunggal: `database/spmi_ppepp.sql`.
    - `database/spmi_ppepp.sql` memuat 100% struktur tabel lengkap, 55 akun resmi UNIKA, 9.086 dokumen riil, serta seluruh konfigurasi institusi.

---

### Cara Rekan Kolaborator Menerapkan Perubahan Database di Komputer Lokal / Server:
Setelah melakukan `git pull`, cukup jalankan satu perintah terpadu (aman dijalankan ulang, tidak menghapus dokumen lokal):
```bash
php database/migrate_all.php
```
Bila ingin memasang database baru secara bersih di server / phpMyAdmin / MySQL CLI, cukup impor satu berkas master terpusat:
```bash
mysql -u root spmi_ppepp < database/spmi_ppepp.sql
```
atau melalui phpMyAdmin:
1. Buat/pilih database `spmi_ppepp`.
2. Klik tab **Import**, pilih berkas tunggal `database/spmi_ppepp.sql`, lalu klik **Import**.




