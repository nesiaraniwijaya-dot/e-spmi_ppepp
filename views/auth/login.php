<?php
/**
 * Login View
 * SPMI PPEPP UNIKA Soegijapranata
 */
$pageTitle = 'Masuk Portal PETRA';
require_once ROOT_PATH . '/views/layouts/header.php';
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card border-0 shadow-2-strong overflow-hidden" style="border-radius: 20px; box-shadow: 0 20px 40px rgba(11, 25, 44, 0.15);">
                <div class="row g-0">
                    
                    <!-- Left Column: SCU Brand & PPEPP Identity (Dark Blue Theme) -->
                    <div class="col-lg-6 bg-scu-gradient text-white p-4 p-md-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <img src="<?= base_url('assets/images/logo-unika.png') ?>" alt="Logo UNIKA Soegijapranata" style="width: 60px; height: 60px; object-fit: contain; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.35)); flex-shrink: 0;">
                                <div>
                                    <h3 class="fw-bold mb-0 text-white" style="letter-spacing: 0.5px; line-height: 1.2;">PETRA</h3>
                                    <div class="small text-light opacity-90" style="font-size: 0.82rem; font-weight: 500;">PEmantauan Tahapan PPEPP &amp; Rencana Aksi</div>
                                </div>
                            </div>

                            <h3 class="fw-extrabold mb-3 text-white" style="font-weight: 800; line-height: 1.3;">
                                PETRA &bull; Pengawal Mutu
                            </h3>
                            <p class="text-light opacity-75 small mb-4" style="line-height: 1.7;">
                                PETRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan di <?= INSTITUTION_NAME ?>.
                            </p>

                            <div class="d-flex flex-column gap-2 mb-4">
                                <div class="d-flex align-items-center gap-2 small text-light">
                                    <i class="fas fa-check-circle text-warning"></i> Kontrol Hak Akses Berbasis Peran (RBAC)
                                </div>
                                <div class="d-flex align-items-center gap-2 small text-light">
                                    <i class="fas fa-check-circle text-warning"></i> Rekam Jejak Audit Trail Terperinci
                                </div>
                                <div class="d-flex align-items-center gap-2 small text-light">
                                    <i class="fas fa-check-circle text-warning"></i> Repositori &amp; Verifikasi Dokumen 5 Siklus
                                </div>
                            </div>
                        </div>

                        <!-- Institutional Quality Badge Card -->
                        <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-15 d-flex align-items-center gap-3 mt-4">
                            <div class="rounded-circle bg-warning bg-opacity-20 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                <i class="fas fa-award fa-lg"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white small" style="letter-spacing: 0.3px;">Terakreditasi UNGGUL &bull; BAN-PT</div>
                                <div class="text-white text-opacity-75" style="font-size: 0.75rem;">Siklus PPEPP Berkelanjutan Tridharma Perguruan Tinggi</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Login Form -->
                    <div class="col-lg-6 bg-white p-4 p-md-5 d-flex flex-column justify-content-center">
                        <div class="text-center mb-4">
                            <img src="<?= base_url('assets/images/logo-unika.png') ?>" alt="Logo UNIKA Soegijapranata" style="width: 64px; height: 64px; object-fit: contain; margin-bottom: 0.65rem; filter: drop-shadow(0 3px 6px rgba(0,0,0,0.12));">
                            <h4 class="fw-bold text-dark-blue mb-0" style="letter-spacing: 0.5px;">PETRA</h4>
                            <div class="text-muted small fw-medium mb-3" style="font-size: 0.82rem;">PEmantauan Tahapan PPEPP &amp; Rencana Aksi</div>
                            <div class="border-top pt-2" style="border-color: #E2E8F0 !important;">
                                <span class="text-secondary small">Masuk ke Akun Anda untuk Mengakses Portal</span>
                            </div>
                        </div>

                        <form action="<?= base_url('login') ?>" method="POST">
                            <?php if (!empty($returnUrl)): ?>
                                <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl) ?>">
                                <div class="alert alert-info border-info border-opacity-25 py-2 px-3 rounded-3 small mb-3 d-flex align-items-center gap-2">
                                    <i class="fas fa-unlock-keyhole text-info fa-lg"></i>
                                    <div><strong>Akses Dokumen Penuh:</strong> Masuk untuk membuka seluruh halaman dokumen tanpa batasan pratinjau.</div>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold small text-secondary">Alamat Email Institusi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0 ps-0" id="email" name="email" placeholder="nama@unika.ac.id" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label fw-semibold small text-secondary mb-0">Kata Sandi</label>
                                    <a href="#" class="text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Lupa kata sandi?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 border-end-0 ps-0" id="password" name="password" placeholder="••••••••" required>
                                    <button class="btn btn-light border border-start-0 text-muted" type="button" id="togglePasswordBtn">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mb-4 pt-2">
                                <button type="submit" class="btn btn-primary py-2.5 fw-bold rounded-3 shadow-sm bg-scu-blue border-0">
                                    <i class="fas fa-sign-in-alt me-2"></i> Masuk Sekarang
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="<?= base_url() ?>" class="text-decoration-none small text-muted">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda Publik
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Lupa Password (Hubungi Admin LPM) -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark-blue text-white">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-key text-warning fa-lg"></i>
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="forgotPasswordModalLabel">Bantuan Reset Kata Sandi</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="scu-brand-icon mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h5 class="fw-bold text-dark-blue">Reset Sandi Melalui Admin LPM</h5>
                    <p class="text-muted small">
                        Untuk menjaga integritas dan keamanan sistem penjaminan mutu internal, perubahan dan reset kata sandi akun dilakukan secara terpusat oleh <strong>Super Admin / Admin LPM UNIKA Soegijapranata</strong>.
                    </p>
                </div>
                <div class="card bg-light border-0 p-3 text-start small">
                    <div class="fw-bold text-dark mb-2">Silakan hubungi kontak resmi LPM:</div>
                    <div class="mb-1"><i class="fas fa-envelope text-primary me-2"></i> Email: <strong>lpm@unika.ac.id</strong></div>
                    <div class="mb-1"><i class="fas fa-phone text-success me-2"></i> Telepon: <strong>024-8441555 Ext 1473</strong></div>
                    <div><i class="fas fa-building text-warning me-2"></i> Ruang Lembaga Penjaminan Mutu, Gedung Thomas Aquinas Lantai 5, Kampus Bendan Duwur</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePasswordBtn').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
