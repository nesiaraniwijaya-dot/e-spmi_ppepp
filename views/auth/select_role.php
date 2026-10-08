<?php
/**
 * Role Selector View for Multi-Role Testing Account (nesiaraniwijaya@gmail.com)
 * MITRA - UNIKA Soegijapranata
 */
$pageTitle = 'Pilih Peran Akses Sistem - MITRA';
require_once ROOT_PATH . '/views/layouts/header.php';

$rolesOptions = [
    [
        'role' => 'super_admin',
        'title' => 'Admin LPM (Super Admin)',
        'subtitle' => 'Lembaga Penjaminan Mutu Universitas',
        'desc' => 'Akses penuh ke seluruh master data, verifikasi dokumen, manajemen pengguna, dan audit trail.',
        'badge' => 'Pusat LPM',
        'badge_bg' => 'bg-danger',
        'icon' => 'fas fa-user-shield',
        'color' => '#DC2626'
    ],
    [
        'role' => 'kepala_lpm',
        'title' => 'Kepala LPM',
        'subtitle' => 'Pimpinan Lembaga Penjaminan Mutu',
        'desc' => 'Monitoring seluruh siklus PPEPP, laporan statistik universitas, dan log audit trail.',
        'badge' => 'Pimpinan LPM',
        'badge_bg' => 'bg-primary',
        'icon' => 'fas fa-user-tie',
        'color' => '#2563EB'
    ],
    [
        'role' => 'kepala_pusat_mutu',
        'title' => 'Kepala Pusat Penjaminan Mutu',
        'subtitle' => 'Pusat Standar & Penjaminan Mutu',
        'desc' => 'Monitoring ketercapaian standar mutu, verifikasi review, dan pengawasan siklus PPEPP.',
        'badge' => 'Pusat Mutu',
        'badge_bg' => 'bg-info',
        'icon' => 'fas fa-award',
        'color' => '#0284C7'
    ],
    [
        'role' => 'gpm',
        'title' => 'GPM Fakultas (Gugus Penjaminan Mutu)',
        'subtitle' => 'Fakultas Ilmu Komputer (FIKOM)',
        'desc' => 'Kelola & verifikasi dokumen mutu tingkat fakultas, review dokumen prodi, dan tindak lanjut perbaikan.',
        'badge' => 'Tingkat Fakultas',
        'badge_bg' => 'bg-success',
        'icon' => 'fas fa-clipboard-check',
        'color' => '#059669'
    ],
    [
        'role' => 'dekan',
        'title' => 'Dekan Fakultas',
        'subtitle' => 'Dekan Fakultas Ilmu Komputer (FIKOM)',
        'desc' => 'Review dan pengesahan dokumen PPEPP tingkat fakultas serta monitoring kinerja prodi.',
        'badge' => 'Dekanat',
        'badge_bg' => 'bg-warning text-dark',
        'icon' => 'fas fa-university',
        'color' => '#D97706'
    ],
    [
        'role' => 'wadek',
        'title' => 'Wakil Dekan',
        'subtitle' => 'Wakil Dekan Fakultas Ilmu Komputer (FIKOM)',
        'desc' => 'Peninjauan dokumen mutu akademik dan pendampingan implementasi standar fakultas.',
        'badge' => 'Dekanat',
        'badge_bg' => 'bg-warning text-dark',
        'icon' => 'fas fa-user-graduate',
        'color' => '#B45309'
    ],
    [
        'role' => 'kaprodi',
        'title' => 'Kaprodi (Ketua Program Studi)',
        'subtitle' => 'Teknik Informatika (S1)',
        'desc' => 'Unggah dokumen 5 siklus PPEPP prodi, ajukan draft perbaikan, dan kelola arsip prodi.',
        'badge' => 'Tingkat Prodi',
        'badge_bg' => 'bg-secondary',
        'icon' => 'fas fa-book-reader',
        'color' => '#4F46E5'
    ],
    [
        'role' => 'sekprodi',
        'title' => 'Sekprodi (Sekretaris Prodi)',
        'subtitle' => 'Teknik Informatika (S1)',
        'desc' => 'Membantu pengelolaan dokumen mutu prodi, perbaikan dokumen, dan persiapan audit AMI.',
        'badge' => 'Tingkat Prodi',
        'badge_bg' => 'bg-secondary',
        'icon' => 'fas fa-user-edit',
        'color' => '#7C3AED'
    ],
    [
        'role' => 'pengguna',
        'title' => 'Pengguna (Civitas Akademika)',
        'subtitle' => 'Dosen / Mahasiswa / Civitas Terdaftar',
        'desc' => 'Akses penuh ke seluruh dokumen publik institusi tanpa watermark atau pembatasan pratinjau.',
        'badge' => 'Pengguna Publik',
        'badge_bg' => 'bg-info text-dark',
        'icon' => 'fas fa-user-check',
        'color' => '#059669'
    ]
];
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <!-- Header Banner -->
                <div class="bg-scu-gradient text-white p-4 p-md-5 position-relative">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-white bg-opacity-20 p-2 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <img src="<?= base_url('assets/images/logo-unika.png') ?>" alt="Logo UNIKA" style="width: 40px; height: 40px; object-fit: contain;">
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-1 rounded-pill"><i class="fas fa-shield-halved me-1"></i> Mode Pengujian Multi-Role Google SSO</span>
                            <h3 class="fw-bold mb-0 text-white">Selamat Datang, <?= htmlspecialchars($_SESSION['user_real_name'] ?? $_SESSION['user_name'] ?? 'Pengguna Testing') ?>!</h3>
                        </div>
                    </div>
                    <p class="text-light opacity-90 mb-0 small" style="max-width: 750px; line-height: 1.6;">
                        Akun Google SSO Anda (<strong><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></strong>) memiliki wewenang khusus untuk melakukan simulasi dan pengujian di seluruh tingkatan peran sistem. Silakan pilih <strong>satu peran</strong> yang ingin Anda gunakan untuk sesi ini.
                    </p>
                </div>

                <!-- Body Role Selector Grid -->
                <div class="card-body p-4 p-md-5 bg-light">
                    <div class="row g-3">
                        <?php foreach ($rolesOptions as $r): ?>
                            <div class="col-md-6 col-xl-6">
                                <form action="<?= base_url('auth/select-role') ?>" method="POST">
                                    <input type="hidden" name="role" value="<?= $r['role'] ?>">
                                    <button type="submit" class="w-100 text-start border-0 p-0 bg-transparent" style="cursor: pointer;">
                                        <div class="card h-100 border-0 shadow-sm rounded-3 role-card hover-lift transition-all p-3 bg-white border-start border-4" style="border-left-color: <?= $r['color'] ?> !important;">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 48px; height: 48px; background-color: <?= $r['color'] ?>;">
                                                    <i class="<?= $r['icon'] ?> fa-lg"></i>
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <h6 class="fw-bold mb-0 text-dark text-truncate"><?= $r['title'] ?></h6>
                                                        <span class="badge <?= $r['badge_bg'] ?> text-capitalize px-2 py-1" style="font-size: 0.7rem;"><?= $r['badge'] ?></span>
                                                    </div>
                                                    <div class="small fw-semibold text-primary mb-1" style="font-size: 0.78rem;"><?= $r['subtitle'] ?></div>
                                                    <p class="text-muted small mb-0 opacity-75" style="font-size: 0.75rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                        <?= $r['desc'] ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between text-primary small fw-bold">
                                                <span>Masuk Sebagai <?= $r['title'] ?></span>
                                                <i class="fas fa-arrow-right"></i>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="<?= base_url('logout') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                            <i class="fas fa-sign-out-alt me-1"></i> Keluar / Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.role-card {
    transition: all 0.25s ease-in-out;
}
.role-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
}
</style>
