<?php
/**
 * Role Selector View: Pilih Peran Sesi Akses
 * SPMI PPEPP UNIKA Soegijapranata
 */
$pageTitle = 'Pilih Peran Akses Sistem - MITRA';
require_once ROOT_PATH . '/views/layouts/header.php';

$assignments = $assignments ?? Auth::getAssignments();
$userName = $_SESSION['user_real_name'] ?? $_SESSION['user_name'] ?? 'Civitas UNIKA';
$userEmail = $_SESSION['user_email'] ?? '';
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <!-- Header Banner -->
                <div class="bg-scu-gradient text-white p-4 p-md-5 text-center position-relative">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <img src="<?= base_url('assets/images/logo-unika.png') ?>" alt="Logo UNIKA" style="width: 46px; height: 46px; object-fit: contain;">
                    </div>
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2 rounded-pill d-inline-block">
                        <i class="fas fa-id-card-clip me-1"></i> Penugasan Multi-Peran
                    </span>
                    <h3 class="fw-bold mb-1 text-white">Selamat Datang, <?= htmlspecialchars($userName) ?>!</h3>
                    <p class="text-light opacity-90 mb-0 small mx-auto" style="max-width: 580px; line-height: 1.6;">
                        Akun Anda (<strong><?= htmlspecialchars($userEmail) ?></strong>) memiliki <strong><?= count($assignments) ?> penugasan resmi</strong> di sistem E-SPMI. Silakan pilih peran yang ingin Anda gunakan untuk sesi kerja ini:
                    </p>
                </div>

                <!-- Body Cards: Daftar Penugasan Resmi Milik Akun Ini -->
                <div class="card-body p-4 p-md-5 bg-light">
                    <div class="row g-3 justify-content-center">
                        <?php foreach ($assignments as $asg): 
                            $role = $asg['role'];
                            $isDekanat = in_array($role, ['dekan', 'wadek']);
                            $isProdi = in_array($role, ['kaprodi', 'sekprodi']);
                            $isGpm = ($role === 'gpm');
                            $isLpm = in_array($role, Auth::LPM_ROLES);

                            $cardColor = match($role) {
                                'dekan' => '#1E3E62',
                                'wadek' => '#0284C7',
                                'gpm' => '#8B5CF6',
                                'kaprodi' => '#059669',
                                'sekprodi' => '#0D9488',
                                'super_admin', 'admin_lpm' => '#DC2626',
                                'kepala_lpm' => '#D97706',
                                default => '#2563EB'
                            };

                            $iconClass = match($role) {
                                'dekan', 'wadek' => 'fas fa-landmark',
                                'gpm' => 'fas fa-shield-halved',
                                'kaprodi' => 'fas fa-graduation-cap',
                                'sekprodi' => 'fas fa-signature',
                                default => 'fas fa-user-tie'
                            };

                            $title = match($role) {
                                'dekan' => 'Dekan Fakultas',
                                'wadek' => 'Wakil Dekan Fakultas',
                                'gpm' => 'GPM (Gugus Penjaminan Mutu)',
                                'kaprodi' => 'Ketua Program Studi (Kaprodi)',
                                'sekprodi' => 'Sekretaris Program Studi (Sekprodi)',
                                default => Auth::getRoleLabel($role)
                            };

                            $unitName = !empty($asg['nama_prodi']) 
                                ? $asg['nama_prodi'] . ' (' . ($asg['jenjang'] ?? 'S1') . ')'
                                : ($asg['nama_fakultas'] ?? 'Tingkat Universitas');

                            $subUnit = !empty($asg['nama_prodi']) ? ($asg['nama_fakultas'] ?? '') : '';

                            $desc = match(true) {
                                $isDekanat => 'Pengelolaan dan pengesahan siklus PPEPP tingkat fakultas, review dokumen, serta profil Dekanat.',
                                $isProdi => 'Pengelolaan siklus PPEPP program studi, unggah dokumen mutu, revisi borang, dan arsip prodi.',
                                $isGpm => 'Penjaminan mutu fakultas, audit internal, review kepatuhan dokumen, dan koordinasi LPM.',
                                default => 'Akses pengelolaan dokumen dan sistem penjaminan mutu sesuai wewenang penugasan.'
                            };
                        ?>
                            <div class="col-12 col-md-6">
                                <form action="<?= base_url('auth/select-role') ?>" method="POST" class="h-100">
                                    <input type="hidden" name="assignment_id" value="<?= $asg['id'] ?>">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white transition-all position-relative hover-lift" 
                                         style="border-top: 5px solid <?= $cardColor ?> !important;">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-sm" 
                                                 style="width: 50px; height: 50px; background-color: <?= $cardColor ?>;">
                                                <i class="<?= $iconClass ?> fa-lg"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="badge rounded-pill text-uppercase mb-1" 
                                                      style="background: <?= $cardColor ?>15; color: <?= $cardColor ?>; font-size: 0.7rem; font-weight: 700;">
                                                    <?= $isDekanat ? 'Dekanat Fakultas' : ($isProdi ? 'Program Studi' : ($isGpm ? 'GPM Fakultas' : 'LPM')) ?>
                                                </span>
                                                <h6 class="fw-bold mb-0 text-dark-blue text-truncate" style="font-size: 0.98rem;">
                                                    <?= htmlspecialchars($title) ?>
                                                </h6>
                                            </div>
                                        </div>

                                        <div class="p-2.5 rounded-3 bg-light mb-3 border">
                                            <div class="fw-bold text-dark small text-truncate">
                                                <i class="fas fa-building text-primary me-1"></i> <?= htmlspecialchars($unitName) ?>
                                            </div>
                                            <?php if (!empty($subUnit)): ?>
                                                <div class="text-muted small text-truncate" style="font-size: 0.74rem;">
                                                    <?= htmlspecialchars($subUnit) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <p class="text-muted small mb-4" style="font-size: 0.8rem; line-height: 1.5; min-height: 48px;">
                                            <?= htmlspecialchars($desc) ?>
                                        </p>

                                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
                                                style="background-color: <?= $cardColor ?>; border-color: <?= $cardColor ?>;">
                                            <span>Masuk Sebagai <?= htmlspecialchars($title) ?></span>
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i> Anda dapat berpindah peran kapan saja melalui menu <strong>Ganti Peran</strong> di pojok kanan atas setelah masuk ke dalam sistem.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}
</style>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
