<?php
/**
 * Admin Fakultas: Pengaturan Profil Dekanat
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header Section with generous breathing room -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill small fw-bold mb-2" style="background:#FEF3C7; color:#92400E; border:1px solid #FCD34D;">
                <i class="fas fa-landmark" style="color:#D97706;"></i> Struktur Pimpinan Fakultas
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Pengaturan Profil Dekanat</h3>
            <p class="text-muted small mb-0">Informasi pimpinan Dekanat akan ditampilkan pada portal publik dan lembar pengesahan SPMI fakultas.</p>
        </div>
        <a href="<?= base_url('fakultas/dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-2xs">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <div class="dekanat-form-card">
                <!-- Faculty Identity Banner -->
                <div class="p-3.5 p-md-4 rounded-4 mb-4.5 border d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3" 
                     style="background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%); border-color: #E2E8F0 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" 
                             style="width: 58px; height: 58px; background: #FEF3C7; color: #D97706; border: 2px solid #FDE68A; font-size: 1.5rem;">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark-blue mb-1" style="font-size: 1.15rem;"><?= htmlspecialchars($fakultas['nama_fakultas']) ?></h5>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge rounded-pill bg-white text-secondary border px-2.5 py-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-fingerprint me-1 text-primary"></i> Kode Fakultas: <strong><?= htmlspecialchars($fakultas['kode_fakultas']) ?></strong>
                                </span>
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-circle-check me-1"></i> Data Terintegrasi
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('fakultas/dekanat/save') ?>" method="POST">
                    <!-- 1. DATA DEKAN FAKULTAS -->
                    <div class="dekanat-section-box">
                        <div class="d-flex align-items-start gap-3 mb-4 pb-3 border-bottom" style="border-color: #E2E8F0 !important;">
                            <span class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.05rem;">
                                <i class="fas fa-user-tie"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold text-dark-blue mb-1" style="font-size: 1.02rem;">Data Dekan Fakultas</h6>
                                <p class="text-muted small mb-0" style="font-size: 0.82rem;">Pimpinan utama fakultas dan penanggung jawab SPMI tingkat fakultas.</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nama_dekan" class="form-label-dekanat">
                                <span>Nama Lengkap Dekan (Beserta Gelar)</span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-dekanat" id="nama_dekan" name="nama_dekan" 
                                   value="<?= htmlspecialchars($fakultas['nama_dekan'] ?? '') ?>" 
                                   placeholder="Contoh: Dr. Bernardinus Harnadi, M.T." required>
                            <div class="form-text mt-2 small text-muted" style="font-size: 0.78rem;">
                                Tuliskan nama lengkap beserta seluruh gelar akademik/profesi resmi.
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="nidn_dekan" class="form-label-dekanat">
                                <span>Nomor Induk Dosen Nasional (NIDN)</span>
                            </label>
                            <div class="input-group" style="max-width: 360px;">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #CBD5E1;">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" class="form-control form-control-dekanat border-start-0 ps-1" id="nidn_dekan" name="nidn_dekan" 
                                       value="<?= htmlspecialchars($fakultas['nidn_dekan'] ?? '') ?>" 
                                       placeholder="Contoh: 0612057301">
                            </div>
                            <div class="form-text mt-2 small text-muted d-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="fas fa-circle-info text-primary opacity-75"></i>
                                <span>NIDN resmi yang terdaftar di PDDIKTI Kemendikbudristek.</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. DATA WAKIL DEKAN FAKULTAS -->
                    <div class="dekanat-section-box">
                        <div class="d-flex align-items-start gap-3 mb-4 pb-3 border-bottom" style="border-color: #E2E8F0 !important;">
                            <span class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.05rem;">
                                <i class="fas fa-user-gear"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold text-dark-blue mb-1" style="font-size: 1.02rem;">Data Wakil Dekan Fakultas</h6>
                                <p class="text-muted small mb-0" style="font-size: 0.82rem;">Membantu Dekan dalam koordinasi operasional akademik dan penjaminan mutu.</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nama_wadek" class="form-label-dekanat">
                                <span>Nama Lengkap Wakil Dekan (Beserta Gelar)</span>
                            </label>
                            <input type="text" class="form-control form-control-dekanat" id="nama_wadek" name="nama_wadek" 
                                   value="<?= htmlspecialchars($fakultas['nama_wadek'] ?? '') ?>" 
                                   placeholder="Contoh: Erdhi Widyarto Nugroho, S.T., M.T.">
                            <div class="form-text mt-2 small text-muted" style="font-size: 0.78rem;">
                                Tuliskan nama lengkap wakil dekan beserta seluruh gelar.
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="nidn_wadek" class="form-label-dekanat">
                                <span>Nomor Induk Dosen Nasional (NIDN) Wakil Dekan</span>
                            </label>
                            <div class="input-group" style="max-width: 360px;">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #CBD5E1;">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" class="form-control form-control-dekanat border-start-0 ps-1" id="nidn_wadek" name="nidn_wadek" 
                                       value="<?= htmlspecialchars($fakultas['nidn_wadek'] ?? '') ?>" 
                                       placeholder="Contoh: 0618037901">
                            </div>
                            <div class="form-text mt-2 small text-muted d-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="fas fa-circle-info text-info opacity-75"></i>
                                <span>Nomor induk dosen nasional resmi wakil dekan.</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. MASA JABATAN & PROFIL FAKULTAS -->
                    <div class="dekanat-section-box">
                        <div class="d-flex align-items-start gap-3 mb-4 pb-3 border-bottom" style="border-color: #E2E8F0 !important;">
                            <span class="rounded-circle bg-purple bg-opacity-10 text-purple d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.05rem;">
                                <i class="fas fa-calendar-check"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold text-dark-blue mb-1" style="font-size: 1.02rem;">Masa Kepengurusan & Profil Singkat</h6>
                                <p class="text-muted small mb-0" style="font-size: 0.82rem;">Periode dinas kepengurusan dekanat dan deskripsi ringkas visi misi.</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="periode_jabatan" class="form-label-dekanat">
                                <span>Periode Jabatan Kepengurusan Dekanat</span>
                            </label>
                            <div class="input-group" style="max-width: 360px;">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #CBD5E1;">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <input type="text" class="form-control form-control-dekanat border-start-0 ps-1" id="periode_jabatan" name="periode_jabatan" 
                                       value="<?= htmlspecialchars($fakultas['periode_jabatan'] ?? '2022 - 2026') ?>" 
                                       placeholder="Contoh: 2022 - 2026">
                            </div>
                            <div class="form-text mt-2 small text-muted" style="font-size: 0.78rem;">
                                Contoh penulisan format tahun: <code>2022 - 2026</code>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="deskripsi" class="form-label-dekanat">
                                <span>Deskripsi / Visi Misi Singkat Fakultas</span>
                            </label>
                            <textarea class="form-control form-control-dekanat" id="deskripsi" name="deskripsi" rows="4" 
                                      placeholder="Tuliskan gambaran profil atau fokus keunggulan penjaminan mutu fakultas..."><?= htmlspecialchars($fakultas['deskripsi'] ?? '') ?></textarea>
                            <div class="form-text mt-2 small text-muted" style="font-size: 0.78rem;">
                                Informasi ini akan ditampilkan pada ringkasan portal penjaminan mutu tingkat fakultas.
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS WITH CLEAR SEPARATION -->
                    <div class="d-flex flex-column flex-sm-row justify-content-end align-items-center gap-3 pt-4 mt-4 border-top" style="border-color: #E2E8F0 !important;">
                        <a href="<?= base_url('fakultas/dashboard') ?>" class="btn btn-light border px-4 py-2.5 rounded-pill text-secondary fw-semibold shadow-2xs w-100 w-sm-auto text-center">
                            <i class="fas fa-xmark me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4.5 py-2.5 rounded-pill bg-scu-blue border-0 fw-bold shadow-xs d-inline-flex align-items-center justify-content-center gap-2 w-100 w-sm-auto">
                            <i class="fas fa-floppy-disk"></i> Simpan Profil Dekanat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
