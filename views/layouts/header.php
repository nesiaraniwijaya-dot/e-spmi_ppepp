<?php
/**
 * Public Layout Header / Navbar
 * SPMI PPEPP UNIKA Soegijapranata
 * Design: LPM Design System (Navy + Purple Theme)
 */
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?><?= APP_NAME ?> - <?= INSTITUTION_SHORT ?></title>
    <meta name="description" content="Sistem Informasi Manajemen Siklus PPEPP - Lembaga Penjaminan Mutu <?= INSTITUTION_NAME ?>">

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
    </script>
</head>
<body class="public-page">

    <!-- ============================================================
         PUBLIC NAVBAR
    ============================================================ -->
    <nav id="main-navbar" class="navbar navbar-expand-lg">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand" href="<?= base_url() ?>">
                <div class="brand-logo-wrap">
                    <img src="<?= base_url('assets/images/logo-unika.png') ?>" alt="Logo UNIKA Soegijapranata" class="brand-logo-img">
                </div>
                <div class="brand-text-wrap">
                    <div class="brand-title">MITRA</div>
                    <div class="brand-subtitle">Monitoring dan Implementasi Tahapan PPEPP &amp; Rencana Aksi</div>
                </div>
            </a>

            <!-- Mobile Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Links -->
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto align-items-lg-center" style="gap: 10px;">
                    <li class="nav-item">
                        <a href="<?= base_url() ?>"
                           class="nav-link <?= !isset($activeNav) || $activeNav === 'home' ? 'active' : '' ?>">
                            <i class="fas fa-home me-1"></i> Beranda Fakultas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('dokumen') ?>"
                           class="nav-link <?= isset($activeNav) && $activeNav === 'dokumen' ? 'active' : '' ?>">
                            <i class="fas fa-file-shield me-1"></i> Portal Dokumen Mutu
                        </a>
                    </li>

                    <!-- Auth Section -->
                    <?php if ($currentUser): ?>
                        <li class="nav-item ms-lg-2 d-flex align-items-center gap-2.5">
                            <?php if ($currentUser['role'] === 'pengguna'): ?>
                                <!-- User / Civitas Badge with Full Access -->
                                <span class="badge bg-success bg-opacity-20 text-warning border border-warning border-opacity-30 rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" style="font-size:0.78rem;">
                                    <i class="fas fa-unlock-keyhole text-warning"></i>
                                    <span>Akses Dokumen Penuh</span>
                                </span>
                            <?php else: ?>
                                <!-- Direct Dashboard Return Button for Admin/Dosen/Pengelola -->
                                <a href="<?= base_url(Auth::getDashboardRoute()) ?>"
                                   class="btn btn-warning btn-sm rounded-pill px-3 py-1.5 fw-bold text-dark d-inline-flex align-items-center gap-2 shadow-sm text-decoration-none">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Kembali ke Dashboard</span>
                                </a>
                            <?php endif; ?>

                            <!-- User Profile & Logout Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-light rounded-pill px-3 py-1.5 dropdown-toggle d-flex align-items-center gap-2"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="font-size:0.82rem; border-color:rgba(255,255,255,0.25); background:rgba(255,255,255,0.08);">
                                    <?php if (!empty($currentUser['avatar']) && file_exists(ROOT_PATH . '/' . $currentUser['avatar'])): ?>
                                        <img src="<?= base_url($currentUser['avatar']) ?>" alt="Avatar" class="rounded-circle" style="width: 22px; height: 22px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-circle-user fa-lg text-white-50"></i>
                                    <?php endif; ?>
                                    <span class="d-none d-lg-inline text-truncate fw-semibold" style="max-width:150px;"><?= htmlspecialchars($currentUser['name']) ?></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width:220px;">
                                    <li class="px-3 py-2 border-bottom">
                                        <div style="font-weight:700;font-size:0.9rem;color:var(--navy);"><?= htmlspecialchars($currentUser['name']) ?></div>
                                        <div style="font-size:0.75rem;color:var(--text-muted);"><?= htmlspecialchars($currentUser['email']) ?></div>
                                        <span class="badge rounded-pill mt-1" style="font-size:0.7rem; font-weight:700; <?= $currentUser['role'] === 'pengguna' ? 'background:#DCFCE7 !important; color:#166534 !important; border:1px solid #86EFAC !important;' : 'background:#EFF6FF !important; color:#1E40AF !important; border:1px solid #93C5FD !important;' ?>">
                                            <?= $currentUser['role'] === 'pengguna' ? 'PENGGUNA TERDAFTAR' : strtoupper(str_replace('_', ' ', $currentUser['role'])) ?>
                                        </span>
                                    </li>
                                    <?php if ($currentUser['role'] !== 'pengguna'): ?>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                           href="<?= base_url(Auth::getDashboardRoute()) ?>">
                                            <i class="fas fa-gauge text-primary"></i> Dashboard Admin
                                        </a>
                                    </li>
                                    <?php else: ?>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                           href="<?= base_url('dokumen') ?>">
                                            <i class="fas fa-file-shield text-success"></i> Portal Dokumen Mutu
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <li>
                                        <?php 
                                        $pubProfileLink = match(true) {
                                            Auth::isProdi()    => base_url('prodi/profile'),
                                            Auth::isFakultas() => base_url('fakultas/profile'),
                                            Auth::isGpm()      => base_url('gpm/profile'),
                                            default            => base_url('admin/profile'),
                                        };
                                        ?>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                           href="<?= $pubProfileLink ?>">
                                            <i class="fas fa-user-gear text-secondary"></i> Profil & Foto Saya
                                        </a>
                                    </li>
                                    <?php if (!empty($_SESSION['is_multi_role_testing']) || Auth::userEmail() === 'nesiaraniwijaya@gmail.com' || Auth::isSuperAdmin()): ?>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-primary fw-bold" style="background: rgba(37, 99, 235, 0.08);"
                                           href="<?= base_url('auth/select-role') ?>">
                                            <i class="fas fa-arrows-rotate text-primary"></i> Ganti Peran (Role Switcher)
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger"
                                           href="<?= base_url('logout') ?>">
                                            <i class="fas fa-sign-out-alt"></i> Keluar (Logout)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-2">
                            <a href="<?= base_url('login') ?>" class="nav-cta">
                                <i class="fas fa-lock me-1"></i> Masuk Sistem
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- /Navbar -->

    <!-- Flash Notification -->
    <?php if ($flash = Auth::getFlash()): ?>
        <div class="container mt-3">
            <div class="alert-lpm alert-<?= $flash['type'] ?>" role="alert">
                <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'info-circle' ?>"></i>
                <div class="flex-grow-1"><?= htmlspecialchars($flash['message']) ?></div>
            </div>
        </div>
    <?php endif; ?>

<?php
// Note: <body> is intentionally left open — closed by footer.php
?>
