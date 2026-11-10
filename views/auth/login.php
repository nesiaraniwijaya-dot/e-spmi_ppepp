<?php
/**
 * Login View: Google Single Sign-On (SSO) Exclusive
 * SPMI PPEPP UNIKA Soegijapranata
 */
$pageTitle = 'Masuk Portal MITRA';
require_once ROOT_PATH . '/views/layouts/header.php';
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card border-0 shadow-2-strong overflow-hidden" style="border-radius: 24px; box-shadow: 0 20px 45px rgba(11, 25, 44, 0.16);">
                <div class="row g-0">
                    
                    <!-- Left Column: SCU Brand & PPEPP Identity (Dark Blue Theme) -->
                    <div class="col-lg-6 bg-scu-gradient text-white p-4 p-md-5 d-flex flex-column justify-content-between position-relative" style="min-height: 480px;">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <img src="<?= base_url('assets/images/logo-unika.png') ?>" loading="lazy" decoding="async" alt="Logo UNIKA Soegijapranata" style="width: 62px; height: 62px; object-fit: contain; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.35)); flex-shrink: 0;">
                                <div>
                                    <h3 class="fw-bold mb-0 text-white" style="letter-spacing: 0.5px; line-height: 1.2;">MITRA</h3>
                                    <div class="small text-light opacity-90" style="font-size: 0.82rem; font-weight: 500;">Monitoring dan Implementasi Tahapan PPEPP &amp; Rencana Aksi</div>
                                </div>
                            </div>

                            <h3 class="fw-extrabold mb-3 text-white" style="font-weight: 800; line-height: 1.35;">
                                MITRA &bull; Pengawal Mutu
                            </h3>
                            <p class="text-light opacity-75 small mb-4" style="line-height: 1.7;">
                                MITRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan di <?= INSTITUTION_NAME ?> melalui siklus PPEPP terintegrasi.
                            </p>

                            <div class="d-flex flex-column gap-2.5 mb-4">
                                <div class="d-flex align-items-center gap-2.5 small text-light">
                                    <i class="fas fa-shield-halved text-info"></i> Otentikasi Tunggal Resmi Google Workspace Unika
                                </div>
                                <div class="d-flex align-items-center gap-2.5 small text-light">
                                    <i class="fas fa-check-circle text-info"></i> Kontrol Hak Akses Berbasis Peran Terintegrasi (RBAC)
                                </div>
                                <div class="d-flex align-items-center gap-2.5 small text-light">
                                    <i class="fas fa-award text-info"></i> Verifikasi &amp; Pratinjau Dokumen 5 Siklus Mutu
                                </div>
                            </div>
                        </div>

                        <!-- Institutional Quality Badge Card -->
                        <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-15 d-flex align-items-center gap-3 mt-4">
                            <div class="rounded-circle bg-white bg-opacity-15 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                <i class="fas fa-award fa-lg text-info"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white small" style="letter-spacing: 0.3px;">Terakreditasi UNGGUL &bull; BAN-PT</div>
                                <div class="text-white text-opacity-75" style="font-size: 0.75rem;">Siklus PPEPP Berkelanjutan Tridharma Perguruan Tinggi</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Google SSO Action -->
                    <div class="col-lg-6 bg-white p-4 p-md-5 d-flex flex-column justify-content-center">
                        <div class="text-center mb-4">
                            <img src="<?= base_url('assets/images/logo-unika.png') ?>" loading="lazy" decoding="async" alt="Logo UNIKA Soegijapranata" style="width: 68px; height: 68px; object-fit: contain; margin-bottom: 0.75rem; filter: drop-shadow(0 3px 6px rgba(0,0,0,0.12));">
                            <h4 class="fw-bold text-dark-blue mb-0" style="letter-spacing: 0.5px;">Portal Akses MITRA</h4>
                            <div class="text-muted small fw-medium mt-1 mb-2" style="font-size: 0.85rem;">Universitas Katolik Soegijapranata (SCU)</div>
                            <div class="border-top pt-2" style="border-color: #E2E8F0 !important;">
                                <span class="text-secondary small">Autentikasi Terpusat Civitas Akademika</span>
                            </div>
                        </div>

                        <?php if (!empty($returnUrl)): ?>
                            <div class="alert alert-info border-info border-opacity-25 py-2.5 px-3 rounded-3 small mb-4 d-flex align-items-center gap-2.5 shadow-2xs">
                                <i class="fas fa-unlock-keyhole text-info fa-lg flex-shrink-0"></i>
                                <div><strong>Akses Dokumen Lengkap:</strong> Masuk untuk membuka seluruh halaman dokumen tanpa batasan dan mengunduh berkas resmi.</div>
                            </div>
                        <?php endif; ?>

                        <!-- Google SSO Primary Button -->
                        <div class="d-grid mb-3">
                            <?php 
                            $googleAuthUrl = base_url('auth/google' . (!empty($returnUrl) ? '?return_url=' . urlencode($returnUrl) : ''));
                            ?>
                            <a href="<?= $googleAuthUrl ?>" 
                               class="btn btn-outline-dark py-3 px-4 fw-bold rounded-4 shadow-sm d-flex align-items-center justify-content-center gap-3 transition-all" 
                               style="border-color: #CBD5E1; background: #FFFFFF; color: #1E293B; font-size: 0.95rem; border-width: 1.5px; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
                                <svg width="22" height="22" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                                    <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.259h2.908c1.702-1.567 2.684-3.874 2.684-6.617z" fill="#4285F4"/>
                                    <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/>
                                    <path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                                    <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
                                </svg>
                                <span>Masuk dengan Google Institusi SSO</span>
                            </a>
                        </div>

                        <!-- Info Card: Single Sign-On Unika Policy -->
                        <div class="card border-0 rounded-4 p-3 mb-4" style="background: #F8FAFC; border: 1px solid #E2E8F0 !important;">
                            <div class="d-flex align-items-start gap-2.5">
                                <i class="fas fa-info-circle text-primary mt-0.5 flex-shrink-0"></i>
                                <div class="text-muted small" style="font-size: 0.8rem; line-height: 1.55;">
                                    Gunakan akun email resmi institusi Anda (<strong>@unika.ac.id</strong> atau <strong>@student.unika.ac.id</strong>) yang telah terdaftar di sistem MITRA.
                                </div>
                            </div>
                        </div>

                        <!-- Footer Navigation -->
                        <div class="d-flex flex-column align-items-center gap-2 text-center pt-2 border-top" style="border-color: #F1F5F9 !important;">
                            <a href="<?= base_url() ?>" class="text-decoration-none small text-secondary hover-primary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda Publik
                            </a>
                            <a href="#" class="text-decoration-none small text-muted" data-bs-toggle="modal" data-bs-target="#helpLoginModal" style="font-size: 0.78rem;">
                                <i class="fas fa-circle-question me-1"></i> Butuh bantuan akses akun?
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Bantuan Akses Akun LPM -->
<div class="modal fade" id="helpLoginModal" tabindex="-1" aria-labelledby="helpLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-navy text-white px-4 py-3 border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-headset text-info fa-lg"></i>
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="helpLoginModalLabel">Bantuan Akun &amp; Akses Sistem</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 60px; height: 60px;">
                        <i class="fas fa-envelope-circle-check fa-2x"></i>
                    </span>
                </div>
                <h6 class="fw-bold text-dark mb-2">Akun Belum Terdaftar atau Mengalami Kendala?</h6>
                <p class="text-muted small mb-4" style="line-height: 1.6;">
                    Pendaftaran akun dosen, tenaga kependidikan, Gugus Penjaminan Mutu (GPM), dan auditor dikelola langsung oleh Lembaga Penjaminan Mutu (LPM) SCU. Silakan hubungi tim LPM:
                </p>
                <div class="card border rounded-3 p-3 bg-light text-start mb-3">
                    <div class="d-flex align-items-center gap-2.5 mb-2 small text-dark">
                        <i class="fas fa-building text-primary"></i>
                        <span>Lembaga Penjaminan Mutu (LPM) SCU</span>
                    </div>
                    <div class="d-flex align-items-center gap-2.5 mb-2 small text-dark">
                        <i class="fas fa-envelope text-primary"></i>
                        <a href="mailto:lpm@unika.ac.id" class="text-decoration-none text-dark fw-semibold">lpm@unika.ac.id</a>
                    </div>
                    <div class="d-flex align-items-center gap-2.5 small text-dark">
                        <i class="fas fa-phone text-primary"></i>
                        <span>024-8441555 Ext 1473</span>
                    </div>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 small fw-semibold w-100" data-bs-dismiss="modal">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
