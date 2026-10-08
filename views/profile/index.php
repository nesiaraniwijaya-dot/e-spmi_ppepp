<?php
/**
 * View: Profil Pengguna (Edit Profile & Avatar)
 * SPMI PPEPP UNIKA Soegijapranata
 */
$currentUser = Auth::user();
$isAdmin = Auth::isLpm();
$isFak = Auth::isFakultas() || Auth::isGpm();
$formAction = $isAdmin ? base_url('admin/profile/update') : ($isFak ? base_url('fakultas/profile/update') : base_url('prodi/profile/update'));
$backUrl = base_url(Auth::getDashboardRoute());

require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<?php
$officialTitle = Auth::getOfficialTitle();
$unitName = Auth::isProdi()
    ? ($user['nama_fakultas'] ?? 'Fakultas')
    : (Auth::isFakultas() || Auth::isGpm() ? ($user['nama_fakultas'] ?? 'Fakultas') : 'Lembaga Penjaminan Mutu (LPM) Universitas');
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-blue mb-1">Pengaturan Profil & Akun</h3>
            <p class="text-muted small mb-0">Kelola identitas, foto profil pengelola, dan informasi penugasan akun Anda.</p>
        </div>
        <a href="<?= $backUrl ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row g-4">
        <!-- Left: Profile Summary Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <?php if (!empty($user['avatar']) && file_exists(ROOT_PATH . '/' . $user['avatar'])): ?>
                        <img src="<?= base_url($user['avatar']) ?>" id="avatarPreviewSidebar" alt="Avatar" class="rounded-circle shadow-sm border border-3 border-white" style="width: 120px; height: 120px; object-fit: cover;">
                    <?php else: ?>
                        <div id="avatarFallbackSidebar" class="rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto text-white fw-bold" style="width: 120px; height: 120px; font-size: 2.5rem; background: linear-gradient(135deg, var(--purple-dark), var(--purple));">
                            <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2" title="Akun Aktif"></span>
                </div>

                <h5 class="fw-bold text-dark-blue mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                <p class="text-muted small mb-2"><?= htmlspecialchars($user['email']) ?></p>
                
                <!-- Official Account Identity Badge -->
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 bg-light border rounded-pill text-dark fw-semibold" style="font-size: 0.78rem;">
                        <i class="<?= Auth::isProdi() ? 'fas fa-graduation-cap text-primary' : (Auth::isFakultas() ? 'fas fa-landmark text-info' : 'fas fa-award text-warning') ?> me-1"></i>
                        <span><?= htmlspecialchars($officialTitle) ?></span>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 text-start small text-muted">
                    <div class="d-flex justify-content-between mb-1.5">
                        <span>Penugasan Akun:</span>
                        <strong class="text-dark"><?= htmlspecialchars($officialTitle) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1.5">
                        <span>Unit / Fakultas:</span>
                        <strong class="text-dark"><?= htmlspecialchars($unitName) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Status:</span>
                        <strong class="text-success"><i class="fas fa-check-circle me-1"></i>Aktif</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Edit Form Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <form action="<?= $formAction ?>" method="POST" enctype="multipart/form-data">
                    
                    <h5 class="fw-bold text-dark-blue mb-3 d-flex align-items-center gap-2 pb-2 border-bottom">
                        <i class="fas fa-id-badge text-primary"></i>
                        <span>Informasi Identitas & Foto</span>
                    </h5>

                    <!-- Avatar Upload & Preview -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Foto Profil Pengelola</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                <?php if (!empty($user['avatar']) && file_exists(ROOT_PATH . '/' . $user['avatar'])): ?>
                                    <img src="<?= base_url($user['avatar']) ?>" id="avatarPreviewForm" alt="Preview" class="rounded-circle shadow-xs border" style="width: 70px; height: 70px; object-fit: cover;">
                                <?php else: ?>
                                    <div id="avatarFallbackForm" class="rounded-circle shadow-xs d-flex align-items-center justify-content-center text-white fw-bold" style="width: 70px; height: 70px; font-size: 1.5rem; background: linear-gradient(135deg, var(--purple-dark), var(--purple));">
                                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" class="form-control form-control-sm mb-1" id="avatarInput" name="avatar" accept="image/png, image/jpeg, image/webp" onchange="previewAvatar(this)">
                                <div class="text-muted small" style="font-size: 0.72rem;">Format gambar: <strong>JPG, PNG, WEBP</strong> (Maks. 5 MB). Disarankan rasio 1:1.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small text-secondary">Nama Lengkap Pengelola <span class="text-danger">*</span></label>
                        <div class="input-group mb-1">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control bg-light border-start-0 ps-1" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Nama lengkap pengelola" required>
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.72rem;">
                            <i class="fas fa-info-circle text-primary me-1"></i>
                            Nama penanggung jawab akun. Akun ini secara resmi bertugas sebagai <strong><?= htmlspecialchars($officialTitle) ?></strong> (<?= htmlspecialchars($unitName) ?>).
                        </div>
                    </div>

                    <!-- Email (Readonly) -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold small text-secondary">Alamat Email (Username Akun)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control bg-light border-start-0 ps-1 text-muted" id="email" value="<?= htmlspecialchars($user['email']) ?>" readonly disabled>
                        </div>
                        <div class="form-text small" style="font-size: 0.72rem;">Email login dikelola oleh Administrator Sistem LPM.</div>
                    </div>

                    <?php if ($isAdmin): ?>
                        <h5 class="fw-bold text-dark-blue mb-3 mt-4 d-flex align-items-center gap-2 pb-2 border-bottom">
                            <i class="fas fa-lock text-warning"></i>
                            <span>Ubah Kata Sandi Admin LPM (Opsional)</span>
                        </h5>
                        <div class="text-muted small mb-3" style="font-size: 0.75rem;">
                            Biarkan kolom kata sandi kosong jika Anda tidak ingin mengubah kata sandi saat ini.
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label fw-semibold small text-secondary">Kata Sandi Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-key"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 ps-1" id="new_password" name="new_password" placeholder="Minimal 6 karakter" autocomplete="new-password">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label fw-semibold small text-secondary">Konfirmasi Kata Sandi Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-check-double"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 ps-1" id="confirm_password" name="confirm_password" placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Informational Box for Google SSO & Password Policy -->
                        <div class="alert alert-light border rounded-3 p-3.5 mt-4 mb-4 bg-light">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle bg-primary bg-opacity-15 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="fab fa-google fa-lg"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.85rem;">Keamanan Akun &amp; Pengelolaan Kata Sandi Google SSO</div>
                                    <div class="text-muted small" style="font-size: 0.76rem; line-height: 1.6;">
                                        Untuk akun pengguna yang masuk menggunakan <strong>Google SSO</strong>, keamanan dan perubahan kata sandi dikelola secara terpusat langsung melalui akun Google Anda. Pemulihan kata sandi dapat dilakukan di portal <a href="https://myaccount.google.com" target="_blank" class="text-primary fw-semibold">Akun Google Saya <i class="fas fa-external-link-alt ms-0.5"></i></a>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="<?= $backUrl ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const formPreview = document.getElementById('avatarPreviewForm');
            const sidebarPreview = document.getElementById('avatarPreviewSidebar');
            
            if (formPreview) {
                formPreview.src = e.target.result;
                formPreview.style.display = 'block';
            } else {
                const fb = document.getElementById('avatarFallbackForm');
                if (fb) fb.outerHTML = `<img src="${e.target.result}" id="avatarPreviewForm" class="rounded-circle shadow-xs border" style="width: 70px; height: 70px; object-fit: cover;">`;
            }

            if (sidebarPreview) {
                sidebarPreview.src = e.target.result;
                sidebarPreview.style.display = 'block';
            } else {
                const fb2 = document.getElementById('avatarFallbackSidebar');
                if (fb2) fb2.outerHTML = `<img src="${e.target.result}" id="avatarPreviewSidebar" class="rounded-circle shadow-sm border border-3 border-white" style="width: 120px; height: 120px; object-fit: cover;">`;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
