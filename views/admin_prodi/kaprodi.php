<?php
/**
 * Admin Prodi: Pengaturan Pimpinan Program Studi (Kaprodi & Sekprodi)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill small fw-bold mb-2" style="background:#FEF3C7; color:#92400E; border:1px solid #FCD34D;">
                <i class="fas fa-graduation-cap" style="color:#D97706;"></i> Struktur Pimpinan Program Studi
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Pengaturan Ketua &amp; Sekretaris Program Studi</h3>
            <p class="text-muted small mb-0">Informasi Ketua Program Studi dan Sekretaris Program Studi yang mengelola penjaminan mutu di tingkat prodi.</p>
        </div>
        <a href="<?= base_url('prodi/dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <!-- Prodi Header Banner -->
                <div class="p-3.5 rounded-3 mb-4 border d-flex align-items-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 54px; height: 54px; font-size: 1.5rem;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($prodi['nama_prodi']) ?></h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"><?= htmlspecialchars($prodi['jenjang']) ?></span>
                            <span class="text-muted small">&bull; <?= htmlspecialchars($prodi['nama_fakultas']) ?></span>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('prodi/kaprodi/save') ?>" method="POST">
                    
                    <!-- 1. DATA KETUA PROGRAM STUDI -->
                    <div class="p-3.5 rounded-3 mb-4 border" style="background: #FFFFFF; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center gap-2.5 mb-3 pb-2 border-bottom">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.95rem;">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark-blue mb-0">Ketua Program Studi (Kaprodi)</h6>
                                <div class="text-muted small" style="font-size: 0.75rem;">Penanggung jawab utama operasional dan penjaminan mutu program studi</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_kaprodi" class="form-label fw-semibold small text-secondary">Nama Lengkap Ketua Program Studi (Beserta Gelar) *</label>
                            <input type="text" class="form-control" id="nama_kaprodi" name="nama_kaprodi" 
                                   value="<?= htmlspecialchars($prodi['nama_kaprodi'] ?? '') ?>" 
                                   placeholder="Contoh: Prof. Dr. Ir. Abdi, M.T., IPU" required>
                        </div>

                        <div>
                            <label for="nidn_kaprodi" class="form-label fw-semibold small text-secondary">Nomor Induk Dosen Nasional (NIDN)</label>
                            <input type="text" class="form-control" id="nidn_kaprodi" name="nidn_kaprodi" 
                                   value="<?= htmlspecialchars($prodi['nidn_kaprodi'] ?? '') ?>" 
                                   placeholder="Contoh: 0612057301">
                            <div class="form-text small" style="font-size: 0.72rem;">NIDN resmi yang terdaftar di PDDIKTI.</div>
                        </div>
                    </div>

                    <!-- 2. DATA SEKRETARIS PROGRAM STUDI -->
                    <div class="p-3.5 rounded-3 mb-4 border" style="background: #FFFFFF; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center gap-2.5 mb-3 pb-2 border-bottom">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.95rem;">
                                <i class="fas fa-signature"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark-blue mb-0">Sekretaris Program Studi (Sekprodi)</h6>
                                <div class="text-muted small" style="font-size: 0.75rem;">Pengelola administrasi akademik dan siklus PPEPP program studi</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_sekprodi" class="form-label fw-semibold small text-secondary">Nama Lengkap Sekretaris Program Studi (Beserta Gelar)</label>
                            <input type="text" class="form-control" id="nama_sekprodi" name="nama_sekprodi" 
                                   value="<?= htmlspecialchars($prodi['nama_sekprodi'] ?? '') ?>" 
                                   placeholder="Contoh: Rosalia Hadi, S.Kom., M.T.">
                        </div>

                        <div>
                            <label for="nidn_sekprodi" class="form-label fw-semibold small text-secondary">Nomor Induk Dosen Nasional (NIDN)</label>
                            <input type="text" class="form-control" id="nidn_sekprodi" name="nidn_sekprodi" 
                                   value="<?= htmlspecialchars($prodi['nidn_sekprodi'] ?? '') ?>" 
                                   placeholder="Contoh: 0615088201">
                            <div class="form-text small" style="font-size: 0.72rem;">NIDN resmi yang terdaftar di PDDIKTI.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="<?= base_url('prodi/dashboard') ?>" class="btn btn-secondary btn-sm px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill bg-scu-blue border-0 fw-bold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Pimpinan Prodi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
