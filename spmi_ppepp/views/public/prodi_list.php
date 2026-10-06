<?php
/**
 * Public View: Daftar Program Studi & Dokumen PPEPP Tingkat Fakultas
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/header.php';
$fakDocStats = $fakultasDocStats ?? ['total_dokumen' => 0, 'count_p1' => 0, 'count_p2' => 0, 'count_e' => 0, 'count_p3' => 0, 'count_p4' => 0];
$prodiCount = count($prodiList);
?>

<!-- Breadcrumb & Header Banner -->
<section class="page-banner text-white py-4 position-relative overflow-hidden">
    <div class="container position-relative z-index-2">
        <nav aria-label="breadcrumb">
            <div class="breadcrumb-lpm mb-2">
                <a href="<?= base_url() ?>"><i class="fas fa-home me-1"></i> Beranda</a>
                <span>/</span>
                <span class="current"><?= htmlspecialchars($fakultas['nama_fakultas']) ?></span>
            </div>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 text-warning small fw-bold mb-2">
                    <i class="fas fa-landmark"></i> Fakultas <?= htmlspecialchars($fakultas['kode_fakultas']) ?>
                </div>
                <h1 class="page-banner-title mb-1"><?= htmlspecialchars($fakultas['nama_fakultas']) ?></h1>
                <p class="text-light opacity-80 small mb-0" style="max-width: 700px;"><?= htmlspecialchars($fakultas['deskripsi']) ?></p>
            </div>
            <a href="<?= base_url() ?>" class="btn btn-outline-light btn-sm rounded-pill px-3.5 py-2 align-self-start align-self-md-center" style="font-family:var(--font-heading);font-weight:600;">
                <i class="fas fa-arrow-left me-1"></i> Ganti Fakultas
            </a>
        </div>
    </div>
</section>

<!-- Sub-Menu Navigasi Unit PPEPP (Seragam di Bawah Header) -->
<?php
$activeUnit = 'ringkasan';
$activeProdiId = null;
require ROOT_PATH . '/views/public/components/ppepp_unit_nav.php';
?>

<!-- Section: Daftar Unit PPEPP (Dekanat & Program Studi) -->
<section class="py-5 bg-main">
    <div class="container">
        
        <!-- Section Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4 pb-2 border-bottom">
            <div>
                <h4 class="fw-bold text-dark-blue mb-1">
                    <i class="fas fa-folder-tree text-primary me-2"></i>Pilih Unit Dokumen PPEPP
                </h4>
                <p class="text-muted small mb-0">
                    Buka Dashboard Dokumen PPEPP tingkat Dekanat Fakultas maupun Program Studi di bawah <?= htmlspecialchars($fakultas['nama_fakultas']) ?>.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill" style="font-size: 0.78rem;">
                    1 Tingkat Dekanat &bull; <?= $prodiCount ?> Program Studi
                </span>
            </div>
        </div>

        <!-- Grid Seragam: Kartu Dekanat + Kartu Program Studi -->
        <div class="row g-4">

            <!-- ============================================== -->
            <!-- 1. KARTU DOKUMEN PPEPP TINGKAT DEKANAT FAKULTAS -->
            <!-- ============================================== -->
            <div class="col-lg-6">
                <div class="card card-prodi h-100 d-flex flex-column shadow-2xs" style="border-top: 4px solid var(--purple) !important;">
                    <!-- Header Kartu -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: rgba(107, 70, 193, 0.12); color: var(--purple); font-size: 0.72rem;">
                                    <i class="fas fa-landmark me-1"></i> TINGKAT FAKULTAS
                                </span>
                                <span class="badge bg-light text-secondary border" style="font-size: 0.72rem;">Kode: <?= htmlspecialchars($fakultas['kode_fakultas']) ?></span>
                            </div>
                            <h5 class="fw-bold text-dark-blue mb-0">
                                Dokumen PPEPP Dekanat <?= htmlspecialchars($fakultas['nama_fakultas']) ?>
                            </h5>
                        </div>
                        <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 fw-bold" style="font-size: 0.75rem;">
                            <i class="fas fa-shield-check me-1"></i> <?= (int)$fakDocStats['total_dokumen'] ?> Dokumen Mutu
                        </div>
                    </div>

                    <!-- Leadership / Dekanat Info Box -->
                    <div class="bg-light rounded-3 p-3 mb-3 border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                                 style="width: 42px; height: 42px; background: rgba(107, 70, 193, 0.12); color: var(--purple); font-size: 1.2rem;">
                                <i class="fas fa-landmark"></i>
                            </div>
                            <div class="text-truncate">
                                <div class="small text-muted" style="font-size: 0.75rem;">Pimpinan Dekanat Fakultas:</div>
                                <div class="fw-bold text-dark-blue small text-truncate">
                                    Dekan: <?= htmlspecialchars($fakultas['nama_dekan'] ?: 'Belum diatur') ?>
                                </div>
                                <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                    <?php if (!empty($fakultas['nama_wadek'])): ?>
                                        Wadek: <?= htmlspecialchars($fakultas['nama_wadek']) ?> &bull;
                                    <?php endif; ?>
                                    Periode: <?= htmlspecialchars($fakultas['periode_jabatan'] ?: '-') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5 Cycle Mini Indicators -->
                    <div class="d-flex flex-wrap gap-1 mb-4">
                        <span class="badge badge-penetapan" title="Penetapan">P1: <?= $fakDocStats['count_p1'] ?? 0 ?></span>
                        <span class="badge badge-pelaksanaan" title="Pelaksanaan">P2: <?= $fakDocStats['count_p2'] ?? 0 ?></span>
                        <span class="badge badge-evaluasi" title="Evaluasi">E: <?= $fakDocStats['count_e'] ?? 0 ?></span>
                        <span class="badge badge-pengendalian" title="Pengendalian">P3: <?= $fakDocStats['count_p3'] ?? 0 ?></span>
                        <span class="badge badge-peningkatan" title="Peningkatan">P4: <?= $fakDocStats['count_p4'] ?? 0 ?></span>
                    </div>

                    <!-- Tombol Aksi Seragam: Buka PPEPP Dekanat -->
                    <div class="mt-auto pt-2">
                        <a href="<?= base_url('fakultas/' . $fakultas['id'] . '/ppepp') ?>" 
                           class="btn btn-primary w-100 py-2.5 fw-bold rounded-pill bg-scu-blue border-0 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i class="fas fa-chart-pie text-warning"></i> Buka Dashboard PPEPP Dekanat Fakultas
                            <i class="fas fa-chevron-right ms-auto"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 2. KARTU-KARTU DOKUMEN PPEPP PROGRAM STUDI -->
            <!-- ============================================== -->
            <?php if (!empty($prodiList)): ?>
                <?php foreach ($prodiList as $p): ?>
                    <div class="col-lg-6">
                        <div class="card card-prodi h-100 d-flex flex-column shadow-2xs" style="border-top: 4px solid var(--navy) !important;">
                            <!-- Header Kartu -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-dark-blue text-warning px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars($p['jenjang']) ?>
                                        </span>
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.72rem;">Kode: <?= htmlspecialchars($p['kode_prodi']) ?></span>
                                    </div>
                                    <h5 class="fw-bold text-dark-blue mb-0">
                                        Program Studi <?= htmlspecialchars($p['nama_prodi']) ?>
                                    </h5>
                                </div>
                                <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 fw-bold" style="font-size: 0.75rem;">
                                    <i class="fas fa-shield-check me-1"></i> <?= (int)$p['total_dokumen'] ?> Dokumen Mutu
                                </div>
                            </div>

                            <!-- Kaprodi Info Box -->
                            <div class="bg-light rounded-3 p-3 mb-3 border">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" 
                                         style="width: 42px; height: 42px; font-size: 1.2rem;">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="small text-muted" style="font-size: 0.75rem;">Ketua Program Studi (Kaprodi):</div>
                                        <div class="fw-bold text-dark-blue small text-truncate">
                                            <?= htmlspecialchars($p['nama_kaprodi'] ?: 'Belum ditentukan') ?>
                                        </div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                            <?= !empty($p['nidn_kaprodi']) ? 'NIDN: ' . htmlspecialchars($p['nidn_kaprodi']) : 'Program Studi ' . htmlspecialchars($p['jenjang']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 5 Cycle Mini Indicators -->
                            <div class="d-flex flex-wrap gap-1 mb-4">
                                <span class="badge badge-penetapan" title="Penetapan">P1: <?= $p['count_p1'] ?? 0 ?></span>
                                <span class="badge badge-pelaksanaan" title="Pelaksanaan">P2: <?= $p['count_p2'] ?? 0 ?></span>
                                <span class="badge badge-evaluasi" title="Evaluasi">E: <?= $p['count_e'] ?? 0 ?></span>
                                <span class="badge badge-pengendalian" title="Pengendalian">P3: <?= $p['count_p3'] ?? 0 ?></span>
                                <span class="badge badge-peningkatan" title="Peningkatan">P4: <?= $p['count_p4'] ?? 0 ?></span>
                            </div>

                            <!-- Tombol Aksi Seragam: Buka PPEPP Prodi -->
                            <div class="mt-auto pt-2">
                                <a href="<?= base_url('prodi/' . $p['id']) ?>" 
                                   class="btn btn-primary w-100 py-2.5 fw-bold rounded-pill bg-scu-blue border-0 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                    <i class="fas fa-chart-pie text-warning"></i> Buka Dashboard PPEPP Program Studi
                                    <i class="fas fa-chevron-right ms-auto"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card p-4 text-center border-0 shadow-sm rounded-4">
                        <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                        <h6 class="fw-bold">Belum Ada Program Studi</h6>
                        <p class="text-muted small mb-0">Program studi pada fakultas ini belum ditambahkan oleh Admin LPM.</p>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>
</section>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
