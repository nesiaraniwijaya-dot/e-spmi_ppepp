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

---

### Cara Rekan Kolaborator Menerapkan Perubahan Database di Komputer Lokal / Server:
Cukup impor satu berkas master terpusat:
```bash
mysql -u root spmi_ppepp < database/spmi_ppepp.sql
```
atau melalui phpMyAdmin:
1. Buat/pilih database `spmi_ppepp`.
2. Klik tab **Import**, pilih berkas `database/spmi_ppepp.sql`, lalu klik **Import**.
