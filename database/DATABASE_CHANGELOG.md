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

---

### Cara Rekan Kolaborator Menerapkan Perubahan Database di Komputer Lokal / Server:
Cukup impor satu berkas master terpusat:
```bash
mysql -u root spmi_ppepp < database/spmi_ppepp.sql
```
atau melalui phpMyAdmin:
1. Buat/pilih database `spmi_ppepp`.
2. Klik tab **Import**, pilih berkas `database/spmi_ppepp.sql`, lalu klik **Import**.


