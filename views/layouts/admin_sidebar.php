<?php
/**
 * Admin Layout Sidebar
 * SPMI PPEPP UNIKA Soegijapranata
 * Design: LPM Design System (Navy + Purple)
 */
$currentUser = Auth::user();
$role        = $currentUser['role'] ?? '';
$currentUri  = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

// Notification counter for sidebar indicators
$sidebarRevisiCount = 0;
$sidebarPerluPerbaikanCount = 0;
$sidebarPendingReviewCount = 0;
$sidebarDraftCount = 0;
try {
    $sidebarDb = Database::getInstance();
    if (Auth::isProdi() && !empty($currentUser['prodi_id'])) {
        $sidebarProdiId = (int)$currentUser['prodi_id'];
        $sidebarRevisiCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE prodi_id = {$sidebarProdiId} AND status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND deleted_at IS NULL
        ")->fetchColumn();
        $sidebarPerluPerbaikanCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE prodi_id = {$sidebarProdiId} AND status_review = 'perlu_perbaikan' AND deleted_at IS NULL
        ")->fetchColumn();
        $sidebarDraftCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE prodi_id = {$sidebarProdiId} AND status_review = 'draft' AND deleted_at IS NULL
        ")->fetchColumn();
    } elseif (Auth::isFakultas() && !empty($currentUser['fakultas_id'])) {
        $sidebarFakultasId = (int)$currentUser['fakultas_id'];
        $sidebarRevisiCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE fakultas_id = {$sidebarFakultasId} AND level = 'fakultas' AND status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND deleted_at IS NULL
        ")->fetchColumn();
        $sidebarPerluPerbaikanCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE fakultas_id = {$sidebarFakultasId} AND level = 'fakultas' AND status_review = 'perlu_perbaikan' AND deleted_at IS NULL
        ")->fetchColumn();
        $sidebarDraftCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE fakultas_id = {$sidebarFakultasId} AND level = 'fakultas' AND status_review = 'draft' AND deleted_at IS NULL
        ")->fetchColumn();
    } elseif ($role === 'gpm' && !empty($currentUser['fakultas_id'])) {
        $sidebarFakultasId = (int)$currentUser['fakultas_id'];
        $sidebarRevisiCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE (fakultas_id = {$sidebarFakultasId} OR prodi_id IN (SELECT id FROM prodis WHERE fakultas_id = {$sidebarFakultasId}))
              AND status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND deleted_at IS NULL
        ")->fetchColumn();
        $sidebarPerluPerbaikanCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE (fakultas_id = {$sidebarFakultasId} OR prodi_id IN (SELECT id FROM prodis WHERE fakultas_id = {$sidebarFakultasId}))
              AND status_review = 'perlu_perbaikan' AND deleted_at IS NULL
        ")->fetchColumn();
        $sidebarDraftCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE (fakultas_id = {$sidebarFakultasId} OR prodi_id IN (SELECT id FROM prodis WHERE fakultas_id = {$sidebarFakultasId}))
              AND status_review = 'draft' AND deleted_at IS NULL
        ")->fetchColumn();
    } elseif (Auth::isLpm()) {
        $sidebarPendingReviewCount = (int)$sidebarDb->query("
            SELECT COUNT(*) FROM ppepp_documents 
            WHERE status_review IN ('belum_direview', 'sudah_diperbaiki') AND deleted_at IS NULL
        ")->fetchColumn();
    }
} catch (\Throwable $e) {
    $sidebarRevisiCount = 0;
    $sidebarPerluPerbaikanCount = 0;
    $sidebarPendingReviewCount = 0;
    $sidebarDraftCount = 0;
}

/**
 * Helper: determine if a nav link should be "active"
 */
function sidebarIsActive(string $currentUri, string $match): string {
    return str_contains($currentUri, $match) ? 'active' : '';
}
?>

<style>
@keyframes pulseRedDot {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    70% { transform: scale(1.15); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}
.sidebar-pulse-dot {
    width: 8px;
    height: 8px;
    background-color: #EF4444;
    border-radius: 50%;
    display: inline-block;
    animation: pulseRedDot 1.8s infinite;
}
</style>

<aside class="admin-sidebar" id="adminSidebar">

    <!-- Sidebar Header / Brand -->
    <div class="admin-sidebar-header d-flex align-items-center justify-content-between">
        <a href="<?= base_url(Auth::getDashboardRoute()) ?>" style="display:flex;align-items:center;gap:12px;text-decoration:none;min-width:0;" class="flex-grow-1">
            <div class="brand-logo-wrap" style="width:40px;height:40px;flex-shrink:0;">
                <img src="<?= base_url('assets/images/logo-unika.png') ?>" alt="Logo UNIKA Soegijapranata" style="width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.35));">
            </div>
            <div class="text-truncate">
                <div class="brand-title" style="font-family:var(--font-heading);font-weight:800;font-size:0.95rem;color:#fff;line-height:1.2;">MITRA</div>
                <div class="brand-subtitle text-truncate" style="font-size:0.65rem;color:rgba(255,255,255,0.72);letter-spacing:0.2px;line-height:1.25;">Monitoring &amp; Tahapan PPEPP</div>
            </div>
        </a>
        <button type="button" class="btn btn-sm text-white-50 p-1 ms-2 d-flex align-items-center justify-content-center rounded-2 border-0 bg-transparent" onclick="toggleSidebar()" title="Tutup / Sembunyikan Menu" style="width:30px;height:30px;">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>
    <!-- /Sidebar Header -->

    <!-- Sidebar Navigation -->
    <nav class="admin-sidebar-nav">

        <?php if (Auth::isLpm()): ?>

            <!-- Super Admin: Menu Utama -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Menu Utama LPM</div>
                <a href="<?= base_url('admin/dashboard') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'admin/dashboard') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-gauge"></i></span>
                    <span>Dashboard Utama</span>
                </a>
                <a href="<?= base_url('admin/review') ?>"
                   class="admin-nav-link <?= (sidebarIsActive($currentUri, 'admin/review') || sidebarIsActive($currentUri, 'admin/review-dokumen')) ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-clipboard-check"></i></span>
                    <span>Pusat Review Dokumen</span>
                    <?php if ($sidebarPendingReviewCount > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-auto px-2 py-0.5 fw-bold" style="font-size: 0.7rem;" title="<?= $sidebarPendingReviewCount ?> dokumen menunggu verifikasi"><?= $sidebarPendingReviewCount ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Super Admin: Data Master Institusi -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Data Master</div>
                <a href="<?= base_url('admin/fakultas') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'admin/fakultas') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-landmark"></i></span>
                    <span>Master Fakultas</span>
                </a>
                <a href="<?= base_url('admin/prodi') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'admin/prodi') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-graduation-cap"></i></span>
                    <span>Master Program Studi</span>
                </a>
                <a href="<?= base_url('admin/bidang') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'admin/bidang') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-layer-group"></i></span>
                    <span>Master Bidang Mutu</span>
                </a>
            </div>

            <!-- Super Admin: Manajemen Sistem -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Manajemen Sistem</div>
                <a href="<?= base_url('admin/landing-settings') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'admin/landing-settings') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-sliders"></i></span>
                    <span>Kelola Beranda &amp; Footer</span>
                </a>
                <a href="<?= base_url('admin/users') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'admin/users') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-users-gear"></i></span>
                    <span>Manajemen Pengguna</span>
                </a>
                <a href="<?= base_url('admin/profile') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'admin/profile') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-user-gear"></i></span>
                    <span>Profil & Foto Saya</span>
                </a>
            </div>

        <?php elseif (Auth::isFakultas()): ?>

            <!-- Dekanat Fakultas: Info Fakultas -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Dekanat: <?= htmlspecialchars($currentUser['fakultas_name'] ?? 'Fakultas') ?></div>
                <a href="<?= base_url('fakultas/dashboard') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'fakultas/dashboard') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-gauge"></i></span>
                    <span>Dashboard Dekanat</span>
                    <?php if ($sidebarPerluPerbaikanCount > 0): ?>
                        <span class="ms-auto sidebar-pulse-dot" title="<?= $sidebarPerluPerbaikanCount ?> dokumen perlu perbaikan"></span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Dekanat: Dokumen PPEPP -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Dokumen Mutu Fakultas</div>
                <a href="<?= base_url('fakultas/dokumen') ?>"
                   class="admin-nav-link <?= (str_contains($currentUri, 'fakultas/dokumen') && !str_contains($currentUri, 'create') && !str_contains($currentUri, 'arsip')) ? 'active' : '' ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-folder-open"></i></span>
                    <span>Daftar Dokumen</span>
                </a>
                <a href="<?= base_url('fakultas/perbaikan') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'fakultas/perbaikan') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-wrench"></i></span>
                    <span>Perbaikan Dokumen</span>
                    <?php if ($sidebarPerluPerbaikanCount > 0): ?>
                        <span class="ms-auto d-inline-flex align-items-center gap-1.5" title="<?= $sidebarPerluPerbaikanCount ?> dokumen perlu perbaikan">
                            <span class="sidebar-pulse-dot"></span>
                            <span class="badge bg-danger rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width: 20px; height: 20px; font-size: 0.68rem; padding: 0;"><?= $sidebarPerluPerbaikanCount ?></span>
                        </span>
                    <?php elseif ($sidebarRevisiCount > 0): ?>
                        <span class="ms-auto badge bg-secondary rounded-pill px-2 py-0.5" style="font-size:0.65rem;"><?= $sidebarRevisiCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= base_url('fakultas/draft') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'fakultas/draft') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-file-pen"></i></span>
                    <span>Draf Dokumen</span>
                    <?php if ($sidebarDraftCount > 0): ?>
                        <span class="ms-auto badge bg-secondary rounded-pill" style="font-size:0.62rem; padding: 0.25em 0.5em;"><?= $sidebarDraftCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= base_url('fakultas/dokumen/create') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'fakultas/dokumen/create') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-cloud-arrow-up"></i></span>
                    <span>Unggah Dokumen Baru</span>
                </a>
                <a href="<?= base_url('fakultas/dokumen/arsip') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'fakultas/dokumen/arsip') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-trash-can"></i></span>
                    <span>Dokumen Terhapus</span>
                </a>
            </div>

            <!-- Dekanat: Pengaturan & Profil -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Pengaturan & Profil</div>
                <a href="<?= base_url('fakultas/dekanat') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'fakultas/dekanat') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-users-rectangle"></i></span>
                    <span>Profil Dekanat</span>
                </a>
                <a href="<?= base_url('fakultas/profile') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'fakultas/profile') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-user-gear"></i></span>
                    <span>Profil & Foto Saya</span>
                </a>
            </div>

        <?php elseif (Auth::isProdi()): ?>

            <!-- Pimpinan Prodi: Info Prodi -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Pimpinan: <?= htmlspecialchars($currentUser['prodi_name'] ?? 'Program Studi') ?></div>
                <a href="<?= base_url('prodi/dashboard') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'prodi/dashboard') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-gauge"></i></span>
                    <span>Dashboard Program Studi</span>
                    <?php if ($sidebarPerluPerbaikanCount > 0): ?>
                        <span class="ms-auto sidebar-pulse-dot" title="<?= $sidebarPerluPerbaikanCount ?> dokumen perlu perbaikan"></span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Pimpinan Prodi: Dokumen PPEPP -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Dokumen PPEPP</div>
                <a href="<?= base_url('prodi/dokumen') ?>"
                   class="admin-nav-link <?= (str_contains($currentUri, 'prodi/dokumen') && !str_contains($currentUri, 'create') && !str_contains($currentUri, 'arsip')) ? 'active' : '' ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-folder-open"></i></span>
                    <span>Daftar Dokumen</span>
                </a>
                <a href="<?= base_url('prodi/perbaikan') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'prodi/perbaikan') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-wrench"></i></span>
                    <span>Perbaikan Dokumen</span>
                    <?php if ($sidebarPerluPerbaikanCount > 0): ?>
                        <span class="ms-auto d-inline-flex align-items-center gap-1.5" title="<?= $sidebarPerluPerbaikanCount ?> dokumen perlu perbaikan">
                            <span class="sidebar-pulse-dot"></span>
                            <span class="badge bg-danger rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width: 20px; height: 20px; font-size: 0.68rem; padding: 0;"><?= $sidebarPerluPerbaikanCount ?></span>
                        </span>
                    <?php elseif ($sidebarRevisiCount > 0): ?>
                        <span class="ms-auto badge bg-secondary rounded-pill px-2 py-0.5" style="font-size:0.65rem;"><?= $sidebarRevisiCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= base_url('prodi/draft') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'prodi/draft') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-file-pen"></i></span>
                    <span>Draf Dokumen</span>
                    <?php if ($sidebarDraftCount > 0): ?>
                        <span class="ms-auto badge bg-secondary rounded-pill" style="font-size:0.62rem; padding: 0.25em 0.5em;"><?= $sidebarDraftCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= base_url('prodi/dokumen/create') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'prodi/dokumen/create') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-cloud-arrow-up"></i></span>
                    <span>Unggah Dokumen Baru</span>
                </a>
                <a href="<?= base_url('prodi/dokumen/arsip') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'prodi/dokumen/arsip') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-trash-can"></i></span>
                    <span>Dokumen Terhapus</span>
                </a>
            </div>

            <!-- Pimpinan Prodi: Pengaturan -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Pengaturan & Profil</div>
                <a href="<?= base_url('prodi/kaprodi') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'prodi/kaprodi') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-user-tie"></i></span>
                    <span>Profil Pimpinan Prodi</span>
                </a>
                <a href="<?= base_url('prodi/profile') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'prodi/profile') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-user-gear"></i></span>
                    <span>Profil & Foto Saya</span>
                </a>
            </div>

        <?php elseif ($role === 'gpm'): ?>

            <!-- GPM: Info Fakultas & Ruang Lingkup -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">GPM: <?= htmlspecialchars($currentUser['fakultas_name'] ?? 'Fakultas') ?></div>
                <a href="<?= base_url('gpm/dashboard') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'gpm/dashboard') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-gauge"></i></span>
                    <span>Dashboard GPM</span>
                    <?php if ($sidebarPerluPerbaikanCount > 0): ?>
                        <span class="ms-auto sidebar-pulse-dot" title="<?= $sidebarPerluPerbaikanCount ?> dokumen perlu perbaikan"></span>
                    <?php endif; ?>
                </a>
                <a href="<?= base_url('gpm/rekapitulasi') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'gpm/rekapitulasi') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-chart-pie text-info"></i></span>
                    <span>Rekapitulasi Unit</span>
                </a>
            </div>

            <!-- GPM: Dokumen PPEPP (Fakultas & Prodi) -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Dokumen Mutu (Fakultas &amp; Prodi)</div>
                <a href="<?= base_url('gpm/dokumen') ?>"
                   class="admin-nav-link <?= (str_contains($currentUri, 'gpm/dokumen') && !str_contains($currentUri, 'create') && !str_contains($currentUri, 'arsip')) ? 'active' : '' ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-folder-open"></i></span>
                    <span>Daftar Dokumen Mutu</span>
                </a>
                <a href="<?= base_url('gpm/perbaikan') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'gpm/perbaikan') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-wrench"></i></span>
                    <span>Perbaikan Dokumen</span>
                    <?php if ($sidebarPerluPerbaikanCount > 0): ?>
                        <span class="ms-auto d-inline-flex align-items-center gap-1.5" title="<?= $sidebarPerluPerbaikanCount ?> dokumen perlu perbaikan">
                            <span class="sidebar-pulse-dot"></span>
                            <span class="badge bg-danger rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width: 20px; height: 20px; font-size: 0.68rem; padding: 0;"><?= $sidebarPerluPerbaikanCount ?></span>
                        </span>
                    <?php elseif ($sidebarRevisiCount > 0): ?>
                        <span class="ms-auto badge bg-secondary rounded-pill px-2 py-0.5" style="font-size:0.65rem;"><?= $sidebarRevisiCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= base_url('gpm/draft') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'gpm/draft') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-file-pen"></i></span>
                    <span>Draf Dokumen</span>
                    <?php if ($sidebarDraftCount > 0): ?>
                        <span class="ms-auto badge bg-secondary rounded-pill" style="font-size:0.62rem; padding: 0.25em 0.5em;"><?= $sidebarDraftCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= base_url('gpm/dokumen/create') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'gpm/dokumen/create') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-cloud-arrow-up"></i></span>
                    <span>Unggah Dokumen Mutu</span>
                </a>
                <a href="<?= base_url('gpm/dokumen/arsip') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'gpm/dokumen/arsip') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-trash-can"></i></span>
                    <span>Dokumen Terhapus</span>
                </a>
            </div>

            <!-- GPM: Pengaturan Akun (Tanpa Dekanat & Tanpa Kaprodi) -->
            <div class="admin-nav-group">
                <div class="admin-nav-label">Pengaturan Akun</div>
                <a href="<?= base_url('gpm/profile') ?>"
                   class="admin-nav-link <?= sidebarIsActive($currentUri, 'gpm/profile') ?>">
                    <span class="nav-icon-wrap"><i class="fas fa-user-gear"></i></span>
                    <span>Profil &amp; Foto Saya</span>
                </a>
            </div>

        <?php endif; ?>

        <!-- Sesi Akun (All Roles) -->
        <div class="admin-nav-group mt-3">
            <div class="admin-nav-label">Sesi Akun</div>
            <a href="<?= base_url('logout') ?>" class="admin-nav-link"
               style="color:rgba(252,165,165,0.85);"
               onmouseover="this.style.background='rgba(239,68,68,0.18)';this.style.color='#fca5a5';"
               onmouseout="this.style.background='';this.style.color='rgba(252,165,165,0.85)';">
                <span class="nav-icon-wrap" style="background:rgba(239,68,68,0.15);color:#ef4444;"><i class="fas fa-arrow-right-from-bracket"></i></span>
                <span>Keluar (Logout)</span>
            </a>
        </div>

    </nav>
    <!-- /Sidebar Navigation -->

    <!-- Sidebar Footer: Role Info -->
    <?php
    $sidebarOfficialTitle = Auth::getOfficialTitle();
    ?>
    <div class="admin-sidebar-footer">
        <div style="display:flex;align-items:center;gap:10px;">
            <?php if (!empty($currentUser['avatar']) && file_exists(ROOT_PATH . '/' . $currentUser['avatar'])): ?>
                <img src="<?= base_url($currentUser['avatar']) ?>" alt="Avatar" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;flex-shrink:0;border:1.5px solid rgba(255,255,255,0.3);">
            <?php else: ?>
                <div style="width:8px;height:8px;border-radius:50%;background:#4ade80;flex-shrink:0;box-shadow:0 0 6px rgba(74,222,128,0.6);"></div>
            <?php endif; ?>
            <div class="text-truncate">
                <div style="font-family:var(--font-heading);font-size:0.7rem;font-weight:600;color:rgba(255,255,255,0.5);">Identitas Penugasan</div>
                <div style="font-size:0.75rem;color:rgba(255,255,255,0.85);font-weight:600;" class="text-truncate" title="<?= htmlspecialchars($sidebarOfficialTitle) ?>">
                    <?= htmlspecialchars($sidebarOfficialTitle) ?>
                </div>
            </div>
        </div>
    </div>

</aside>

<!-- Sidebar JS Toggle with Desktop Collapse & LocalStorage State -->
<script>
(function() {
    // Restore sidebar collapsed preference on desktop
    if (window.innerWidth > 991.98) {
        if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    }
})();

function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isMobile = window.innerWidth <= 991.98;

    if (isMobile) {
        const isOpen = sidebar.classList.contains('sidebar-open');
        if (isOpen) {
            sidebar.classList.remove('sidebar-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        } else {
            sidebar.classList.add('sidebar-open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    } else {
        // Desktop collapse toggle
        document.body.classList.toggle('sidebar-collapsed');
        const isCollapsed = document.body.classList.contains('sidebar-collapsed');
        try {
            localStorage.setItem('admin_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        } catch(e) {}
    }
}

// Handle resize events smoothly
window.addEventListener('resize', function() {
    if (window.innerWidth > 991.98) {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.remove('sidebar-open');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
        if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        } else {
            document.body.classList.remove('sidebar-collapsed');
        }
    }
});
</script>
