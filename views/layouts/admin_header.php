<?php
/**
 * Admin Layout Header
 * SPMI PPEPP UNIKA Soegijapranata
 * Design: LPM Design System (Navy + Purple Theme)
 */
$currentUser = Auth::user();
$isAdminLayout = true;

// Notification items for Admin Prodi (Perlu Perbaikan & Sudah Diperbaiki / Menunggu Verifikasi)
$notificationItems = [];
$notificationCount = 0;
if ($currentUser && Auth::isProdi() && !empty($currentUser['prodi_id'])) {
    try {
        $headerDb = Database::getInstance();
        $prodiId = (int)$currentUser['prodi_id'];
        $notificationItems = $headerDb->query("
            SELECT id, nama_dokumen, nomor_dokumen, siklus, catatan_review, status_review, reviewed_at, updated_at
            FROM ppepp_documents
            WHERE prodi_id = {$prodiId} AND status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND deleted_at IS NULL
            ORDER BY FIELD(status_review, 'perlu_perbaikan', 'sudah_diperbaiki'), updated_at DESC
        ")->fetchAll() ?: [];
        $notificationCount = count($notificationItems);
    } catch (\Throwable $e) {
        $notificationItems = [];
        $notificationCount = 0;
    }
} elseif ($currentUser && Auth::isFakultas() && !empty($currentUser['fakultas_id'])) {
    try {
        $headerDb = Database::getInstance();
        $fakultasId = (int)$currentUser['fakultas_id'];
        $notificationItems = $headerDb->query("
            SELECT id, nama_dokumen, nomor_dokumen, siklus, catatan_review, status_review, reviewed_at, updated_at
            FROM ppepp_documents
            WHERE fakultas_id = {$fakultasId} AND level = 'fakultas' AND status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND deleted_at IS NULL
            ORDER BY FIELD(status_review, 'perlu_perbaikan', 'sudah_diperbaiki'), updated_at DESC
        ")->fetchAll() ?: [];
        $notificationCount = count($notificationItems);
    } catch (\Throwable $e) {
        $notificationItems = [];
        $notificationCount = 0;
    }
} elseif ($currentUser && $currentUser['role'] === 'gpm' && !empty($currentUser['fakultas_id'])) {
    try {
        $headerDb = Database::getInstance();
        $fakultasId = (int)$currentUser['fakultas_id'];
        $notificationItems = $headerDb->query("
            SELECT id, nama_dokumen, nomor_dokumen, siklus, catatan_review, status_review, reviewed_at, updated_at
            FROM ppepp_documents
            WHERE (fakultas_id = {$fakultasId} OR prodi_id IN (SELECT id FROM prodis WHERE fakultas_id = {$fakultasId}))
              AND status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND deleted_at IS NULL
            ORDER BY FIELD(status_review, 'perlu_perbaikan', 'sudah_diperbaiki'), updated_at DESC
        ")->fetchAll() ?: [];
        $notificationCount = count($notificationItems);
    } catch (\Throwable $e) {
        $notificationItems = [];
        $notificationCount = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Panel Admin - <?= APP_NAME ?></title>
    <meta name="description" content="Panel Administrasi MITRA <?= INSTITUTION_NAME ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- LPM Design System Stylesheet -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <script>
        window.IS_USER_LOGGED_IN = <?= Auth::check() ? 'true' : 'false' ?>;
        window.LOGIN_URL = '<?= base_url("login") ?>';
        if (window.innerWidth > 991.98 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('sidebar-collapsed');
            });
        }
    </script>
</head>
<body class="<?= (isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] === '1') ? 'sidebar-collapsed' : '' ?>">

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Admin Sidebar -->
<?php require_once ROOT_PATH . '/views/layouts/admin_sidebar.php'; ?>

<!-- Main Content Area -->
<div class="admin-content" id="adminContent">

    <!-- Admin Topbar -->
    <div class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <!-- Mobile Sidebar Toggle -->
            <button class="sidebar-toggle-btn" onclick="toggleSidebar()" id="sidebarToggle" title="Buka/Tutup Menu">
                <i class="fas fa-bars"></i>
            </button>

            <div>
                <div class="admin-topbar-title">
                    <?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard' ?>
                </div>
                <div style="font-size:0.72rem;color:var(--text-muted);">MITRA &bull; Monitoring dan Implementasi Tahapan PPEPP &amp; Rencana Aksi</div>
            </div>
        </div>

        <!-- Topbar Right: User Info & Actions -->
        <div class="d-flex align-items-center gap-2">
            <!-- Tombol Cepat Menuju Web Publik -->
            <a href="<?= base_url() ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-2 shadow-2xs text-decoration-none" title="Buka Portal Web Publik">
                <i class="fas fa-globe me-1"></i>
                <span class="d-none d-sm-inline fw-semibold" style="font-size: 0.78rem;">Lihat Web Publik</span>
            </a>

            <!-- Notification Bell Icon (Revisi & Perbaikan Dokumen) -->
            <?php if (Auth::isProdi() || Auth::isFakultas() || (isset($currentUser['role']) && $currentUser['role'] === 'gpm')): 
                $perluActionCount = count(array_filter($notificationItems, fn($i) => ($i['status_review'] ?? '') === 'perlu_perbaikan'));
            ?>
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle position-relative d-flex align-items-center justify-content-center shadow-2xs border" 
                            type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                            style="width: 38px; height: 38px; color: <?= $perluActionCount > 0 ? '#DC2626' : ($notificationCount > 0 ? '#2563EB' : '#64748B') ?>; background: <?= $perluActionCount > 0 ? '#FEF2F2' : '#FFFFFF' ?>;" 
                            title="<?= $perluActionCount > 0 ? $perluActionCount . ' dokumen butuh perbaikan segera' : ($notificationCount > 0 ? $notificationCount . ' dokumen dalam proses revisi' : 'Tidak ada pemberitahuan perbaikan') ?>">
                        <i class="fas fa-bell <?= $perluActionCount > 0 ? 'fa-shake' : '' ?>"></i>
                        <?php if ($notificationCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill <?= $perluActionCount > 0 ? 'bg-danger' : 'bg-primary' ?> border border-white" style="font-size: 0.65rem; padding: 0.3em 0.55em;">
                                <?= $notificationCount ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0 rounded-4 overflow-hidden" style="min-width: 340px; max-width: 380px;">
                        <li class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge <?= $perluActionCount > 0 ? 'bg-danger' : 'bg-primary' ?> rounded-pill p-1.5"><i class="fas fa-wrench text-white" style="font-size: 0.75rem;"></i></span>
                                <h6 class="fw-bold text-dark-blue mb-0" style="font-size: 0.88rem;">Pemberitahuan Perbaikan</h6>
                            </div>
                            <?php if ($perluActionCount > 0): ?>
                                <span class="badge bg-danger text-white rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.7rem;">
                                    <?= $perluActionCount ?> Butuh Revisi
                                </span>
                            <?php elseif ($notificationCount > 0): ?>
                                <span class="badge bg-primary text-white rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    <?= $notificationCount ?> Dalam Antrean
                                </span>
                            <?php endif; ?>
                        </li>
                        <div style="max-height: 320px; overflow-y: auto;">
                            <?php if (empty($notificationItems)): ?>
                                <div class="text-center py-4 px-3 text-muted">
                                    <i class="fas fa-circle-check text-success fs-3 mb-2 opacity-50"></i>
                                    <div class="small fw-semibold">Semua Dokumen Sesuai</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Tidak ada dokumen yang membutuhkan perbaikan saat ini.</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($notificationItems as $notifDoc): 
                                    $isPerlu = ($notifDoc['status_review'] ?? '') === 'perlu_perbaikan';
                                    $editUrl = match(true) {
                                        Auth::isProdi()    => base_url('prodi/dokumen/edit/' . $notifDoc['id']),
                                        Auth::isFakultas() => base_url('fakultas/dokumen/edit/' . $notifDoc['id']),
                                        ($currentUser['role'] ?? '') === 'gpm' => base_url('gpm/dokumen/edit/' . $notifDoc['id']),
                                        default            => base_url('admin/dashboard'),
                                    };
                                ?>
                                    <a href="<?= $editUrl ?>" class="dropdown-item p-3 border-bottom text-wrap d-flex align-items-start gap-2.5 hover-bg-light" style="font-size: 0.82rem;">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" 
                                             style="width: 32px; height: 32px; background: <?= $isPerlu ? '#FEE2E2; color: #DC2626;' : '#EFF6FF; color: #2563EB;' ?> font-size: 0.85rem;">
                                            <i class="fas <?= $isPerlu ? 'fa-triangle-exclamation' : 'fa-clock-rotate-left' ?>"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                <span class="badge rounded-pill <?= $isPerlu ? 'bg-danger text-white' : 'bg-primary text-white' ?>" style="font-size: 0.65rem;">
                                                    <?= $isPerlu ? 'Perlu Perbaikan' : 'Menunggu Review' ?>
                                                </span>
                                                <span class="text-muted" style="font-size: 0.68rem;">
                                                    <?= !empty($notifDoc['updated_at']) ? date('d M', strtotime($notifDoc['updated_at'])) : '' ?>
                                                </span>
                                            </div>
                                            <div class="fw-bold text-dark text-truncate mb-1" style="max-width: 250px;" title="<?= htmlspecialchars($notifDoc['nama_dokumen']) ?>">
                                                <?= htmlspecialchars($notifDoc['nama_dokumen']) ?>
                                            </div>
                                            <?php if (!empty($notifDoc['catatan_review'])): ?>
                                                <div class="text-muted text-truncate small fst-italic" style="font-size: 0.72rem; max-width: 250px;">
                                                    "<?= htmlspecialchars($notifDoc['catatan_review']) ?>"
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php 
                        $perbaikanPageUrl = match(true) {
                            Auth::isProdi()    => base_url('prodi/perbaikan'),
                            Auth::isFakultas() => base_url('fakultas/perbaikan'),
                            ($currentUser['role'] ?? '') === 'gpm' => base_url('gpm/perbaikan'),
                            default            => base_url('admin/review'),
                        };
                        ?>
                        <li class="p-2 text-center bg-light">
                            <a href="<?= $perbaikanPageUrl ?>" class="text-primary fw-bold text-decoration-none small d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                <span>Buka Menu Perbaikan Dokumen</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- User Dropdown -->
            <?php
            $headerOfficialTitle = Auth::getOfficialTitle();
            $headerRoleIcon = match($currentUser['role'] ?? '') {
                'kaprodi', 'sekprodi' => 'fas fa-graduation-cap text-primary',
                'dekan', 'wadek'      => 'fas fa-landmark text-info',
                'gpm'                 => 'fas fa-shield-halved text-purple',
                default               => 'fas fa-user-tie text-warning',
            };
            ?>
            <div class="dropdown">
                <button class="d-flex align-items-center gap-2 border-0 bg-transparent"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="cursor:pointer;padding:0.35rem 0.5rem;border-radius:var(--radius-sm);transition:background 0.2s ease;"
                        onmouseover="this.style.background='rgba(10,25,47,0.06)'"
                        onmouseout="this.style.background='transparent'">
                    <?php if (!empty($currentUser['avatar']) && file_exists(ROOT_PATH . '/' . $currentUser['avatar'])): ?>
                        <img src="<?= base_url($currentUser['avatar']) ?>" alt="Avatar" class="rounded-circle shadow-xs border" style="width:36px;height:36px;object-fit:cover;flex-shrink:0;">
                    <?php else: ?>
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,var(--purple-dark),var(--purple));border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-weight:700;font-size:0.85rem;">
                            <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="d-none d-md-block text-start">
                        <div style="font-family:var(--font-heading);font-size:0.84rem;font-weight:700;color:var(--navy);line-height:1.2;">
                            <?= htmlspecialchars($currentUser['name']) ?>
                        </div>
                        <div style="font-size:0.68rem;color:var(--text-muted);font-weight:600;">
                            <?= htmlspecialchars($headerOfficialTitle) ?>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down d-none d-md-block" style="font-size:0.65rem;color:var(--text-muted);"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width:240px;">
                    <li class="px-3 py-2 border-bottom">
                        <div style="font-family:var(--font-heading);font-weight:700;font-size:0.9rem;color:var(--navy);">
                            <?= htmlspecialchars($currentUser['name']) ?>
                        </div>
                        <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($currentUser['email']) ?></div>
                        <div class="mt-1.5">
                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill" style="font-size:0.7rem;">
                                <i class="<?= $headerRoleIcon ?> me-1"></i>
                                <?= htmlspecialchars($headerOfficialTitle) ?>
                            </span>
                        </div>
                    </li>
                    <li>
                        <?php 
                        $profileLink = match(true) {
                            Auth::isProdi()    => base_url('prodi/profile'),
                            Auth::isFakultas() => base_url('fakultas/profile'),
                            Auth::isGpm()      => base_url('gpm/profile'),
                            default            => base_url('admin/profile'),
                        };
                        ?>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= $profileLink ?>">
                            <i class="fas fa-user-gear text-primary"></i> Profil & Foto Saya
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= base_url() ?>">
                            <i class="fas fa-globe text-secondary"></i> Buka Web Publik
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="<?= base_url('logout') ?>">
                            <i class="fas fa-sign-out-alt"></i> Keluar (Logout)
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Admin Topbar -->

    <!-- Admin Main Content -->
    <div class="admin-main">

        <!-- Flash Message -->
        <?php if ($flash = Auth::getFlash()): ?>
            <?php
            $alertType = $flash['type'];
            $iconClass  = $alertType === 'success' ? 'fa-check-circle' : ($alertType === 'danger' ? 'fa-exclamation-circle' : 'fa-info-circle');
            ?>
            <div class="alert-lpm alert-<?= $alertType ?>" role="alert">
                <i class="fas <?= $iconClass ?>"></i>
                <div><?= htmlspecialchars($flash['message']) ?></div>
            </div>
        <?php endif; ?>

<?php
// Note: This file intentionally leaves admin-main and admin-content open.
// They are closed implicitly by the browser, or by the view including footer.php.
?>
