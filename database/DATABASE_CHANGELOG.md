# Catatan Perubahan Database (Database Changelog)
**Proyek:** PETRA (PEmantauan Tahapan PPEPP & Rencana Aksi)  
**Versi Pembaruan:** 06 Oktober 2026  
**Kolaborator:** Tim Pengembang (2 Orang)

---

### Ringkasan Pembaruan Database:
1. **Perubahan Role Pengguna (`users.role`)**:
   - Ditambahkan opsi enum `'pengguna'`.
   - Role `'pengguna'` dikhususkan bagi civitas akademika/dosen/pengguna terdaftar non-admin.
   - Pengguna dengan role ini hanya mengakses portal publik tetapi diberikan hak akses penuh untuk membaca seluruh lembar dokumen yang sebelumnya dibatasi, serta mengunduh berkas aslinya.
   - Akun pengujian demo: `pengguna@unika.ac.id` / `password`.

2. **Sinkronisasi Data Fakultas (`fakultas`)**:
   - Total fakultas diselaraskan menjadi 10 Fakultas resmi UNIKA Soegijapranata.
   - Fakultas ID 10 ditetapkan sebagai **FITL** (*Fakultas Ilmu dan Teknologi Lingkungan*).

3. **Sinkronisasi Data Program Studi (`prodis`)**:
   - Total program studi disesuaikan menjadi 27 Program Studi terakreditasi resmi.
   - Kolom-kolom akreditasi spesifik (`status_akreditasi`, `lembaga_akreditasi`, `no_sk_akreditasi`, `masa_berlaku_akreditasi`, dll.) telah dihapus sesuai instruksi agar skema tetap bersih.
   - Seluruh program studi S1, S2, S3, dan Profesi dipetakan secara akurat di bawah fakultas masing-masing.
   - Data program studi lama/dummy yang tidak terakreditasi dan tidak memiliki dokumen telah dibersihkan.

4. **Pembaruan Branding PETRA (`landing_settings`)**:
   - `footer_brand_title`: `PETRA`
   - `footer_brand_sub`: `PEmantauan Tahapan PPEPP & Rencana Aksi`
   - `footer_desc`: `PETRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.`

---

### Cara Rekan Kolaborator Menerapkan Perubahan Database di Komputer Lokal:
Pilih salah satu cara di bawah ini:

* **Opsi A (Rekomendasi - Menjalankan Patch Ringan tanpa reset data)**:
  Jalankan berkas SQL patch di phpMyAdmin atau terminal MySQL:
  ```sql
  SOURCE database/patch_update_petra_and_pengguna_role.sql;
  ```

* **Opsi B (Impor Ulang Database Penuh Bersih)**:
  Impor berkas dump terbaru yang telah diperbarui:
  ```bash
  mysql -u root spmi_ppepp < database/ppepp.sql
  ```
