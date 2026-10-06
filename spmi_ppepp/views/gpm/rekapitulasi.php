<?php
/**
 * Views: Rekapitulasi Status Dokumen per Unit (Dekanat & Program Studi) - GPM
 * Path: views/gpm/rekapitulasi.php
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

// Calculate aggregated stats across Dekanat + Prodis
$grandTotal = (int)($fakultasSummary['total_dokumen_fakultas'] ?? 0);
$grandPerluReview = (int)($fakultasSummary['perlu_review_count'] ?? 0);
$grandRevisi = (int)($fakultasSummary['revisi_count'] ?? 0);
$grandSesuai = (int)($fakultasSummary['sesuai_count'] ?? 0);

foreach ($prodiSummaries as $ps) {
    $grandTotal += (int)($ps['total_dokumen'] ?? 0);
    $grandPerluReview += (int)($ps['perlu_review_count'] ?? 0);
    $grandRevisi += (int)($ps['revisi_count'] ?? 0);
    $grandSesuai += (int)($ps['sesuai_count'] ?? 0);
}

$grandPct = $grandTotal > 0 ? round(($grandSesuai / $grandTotal) * 100) : 0;
?>

<div class="container-fluid p-0 admin-container">

    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge text-white px-3 py-1 rounded-pill" style="background: #8b5cf6;">
                    <i class="fas fa-shield-halved me-1"></i> GPM Fakultas <?= htmlspecialchars($fakultas['kode_fakultas'] ?? '') ?>
                </span>
                <span class="badge bg-light text-secondary border">Rekapitulasi Khusus Fakultas Binaan</span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-0">Rekapitulasi Status per Unit: <?= htmlspecialchars($fakultas['nama_fakultas']) ?></h3>
            <p class="text-muted small mb-0 mt-1">Pemantauan kepatuhan dokumen PPEPP non-draf untuk tingkat Dekanat dan <?= count($prodiSummaries) ?> Program Studi di bawah <?= htmlspecialchars($fakultas['nama_fakultas']) ?>.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1.5">
                <i class="fas fa-folder-open"></i>
                <span>Semua Dokumen GPM</span>
            </a>
            <a href="<?= base_url('gpm/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0 d-flex align-items-center gap-1.5">
                <i class="fas fa-cloud-arrow-up"></i>
                <span>Unggah Dokumen Mutu</span>
            </a>
        </div>
    </div>

    <!-- Top KPI Metrics Banner -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-muted small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase;">Total Dokumen</div>
                <div class="fs-4 fw-extrabold text-primary mb-0"><?= $grandTotal ?></div>
                <div class="text-muted" style="font-size: 0.7rem;">Dekanat &amp; <?= count($prodiSummaries) ?> Prodi</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-danger">
                <div class="text-danger small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase;">Perlu Review LPM</div>
                <div class="fs-4 fw-extrabold text-danger mb-0"><?= $grandPerluReview ?></div>
                <div class="text-muted" style="font-size: 0.7rem;">Menunggu verifikasi</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="text-warning-emphasis small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase;">Perlu Revisi</div>
                <div class="fs-4 fw-extrabold text-warning-emphasis mb-0"><?= $grandRevisi ?></div>
                <div class="text-muted" style="font-size: 0.7rem;">Catatan perbaikan LPM</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="text-success small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase;">Sesuai Standar</div>
                <div class="fs-4 fw-extrabold text-success mb-0"><?= $grandSesuai ?></div>
                <div class="text-muted" style="font-size: 0.7rem;">Telah disahkan</div>
            </div>
        </div>
        <div class="col-12 col-md-8 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-center mb-1.5">
                    <span class="small fw-bold text-dark-blue">Rata-rata Kepatuhan Mutu Fakultas</span>
                    <span class="small fw-extrabold text-success fs-6"><?= $grandPct ?>%</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $grandPct ?>%" aria-valuenow="<?= $grandPct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-muted small mt-1.5" style="font-size: 0.72rem;">
                    Berdasarkan rasio dokumen berstatus "Sesuai" terhadap seluruh dokumen yang diajukan.
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================
         SECTION 1: REKAPITULASI TINGKAT FAKULTAS (DEKANAT)
    ======================================================== -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
            <div>
                <h5 class="fw-bold text-dark-blue mb-0.5 d-flex align-items-center gap-2">
                    <i class="fas fa-landmark text-primary"></i>
                    <span>Rekapitulasi Dokumen Tingkat Fakultas (Dekanat)</span>
                </h5>
                <p class="text-muted small mb-0">Status kepatuhan dokumen PPEPP level fakultas yang diunggah oleh pimpinan dekanat.</p>
            </div>
            <a href="<?= base_url('gpm/dokumen?unit=fakultas') ?>" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1 fw-semibold text-nowrap">
                <i class="fas fa-arrow-right me-1"></i> Buka Dokumen Dekanat
            </a>
        </div>

        <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-secondary" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3" style="width: 80px;">Kode</th>
                        <th>Fakultas</th>
                        <th>Pimpinan Dekan</th>
                        <th class="text-center">Total Dokumen</th>
                        <th class="text-center">Perlu Review</th>
                        <th class="text-center">Perlu Revisi</th>
                        <th class="text-center">Sesuai Mutu</th>
                        <th style="min-width: 150px;">Progres Kepatuhan</th>
                        <th class="text-center pe-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totFak = (int)($fakultasSummary['total_dokumen_fakultas'] ?? 0);
                    $revFak = (int)($fakultasSummary['perlu_review_count'] ?? 0);
                    $rejFak = (int)($fakultasSummary['revisi_count'] ?? 0);
                    $appFak = (int)($fakultasSummary['sesuai_count'] ?? 0);
                    $pctFak = $totFak > 0 ? round(($appFak / $totFak) * 100) : 0;
                    ?>
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-light text-dark border font-monospace fw-bold"><?= htmlspecialchars($fakultasSummary['kode_fakultas']) ?></span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark-blue"><?= htmlspecialchars($fakultasSummary['nama_fakultas']) ?></div>
                            <span class="badge text-white rounded-pill px-2 py-0.5" style="background:#1e3a8a; font-size:0.65rem;">TINGKAT FAKULTAS</span>
                        </td>
                        <td class="text-secondary small">
                            <?= htmlspecialchars($fakultasSummary['nama_dekan'] ?: '-') ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-bold">
                                <?= $totFak ?> Dokumen
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($revFak > 0): ?>
                                <a href="<?= base_url('gpm/dokumen?unit=fakultas&status_review=belum_direview') ?>" class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold text-decoration-none" title="Klik untuk memfilter dokumen perlu review">
                                    <?= $revFak ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($rejFak > 0): ?>
                                <a href="<?= base_url('gpm/dokumen?unit=fakultas&status_review=perlu_perbaikan') ?>" class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold text-decoration-none" title="Klik untuk memfilter dokumen perlu perbaikan">
                                    <?= $rejFak ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($appFak > 0): ?>
                                <a href="<?= base_url('gpm/dokumen?unit=fakultas&status_review=sesuai') ?>" class="badge bg-success rounded-pill px-2.5 py-1 fw-bold text-decoration-none" title="Klik untuk memfilter dokumen sesuai">
                                    <?= $appFak ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">0</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 7px; border-radius: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctFak ?>%" aria-valuenow="<?= $pctFak ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small fw-bold text-dark" style="font-size: 0.72rem; min-width: 32px;"><?= $pctFak ?>%</span>
                            </div>
                        </td>
                        <td class="text-center pe-3">
                            <a href="<?= base_url('gpm/dokumen?unit=fakultas') ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold">
                                <i class="fas fa-folder-open me-1"></i> Lihat Dokumen
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================
         SECTION 2: REKAPITULASI SELURUH PROGRAM STUDI BINAAN
    ======================================================== -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
            <div>
                <h5 class="fw-bold text-dark-blue mb-0.5 d-flex align-items-center gap-2">
                    <i class="fas fa-graduation-cap text-primary"></i>
                    <span>Rekapitulasi Dokumen Program Studi (Binaan Fakultas)</span>
                </h5>
                <p class="text-muted small mb-0">Tabel komparasi status kepatuhan dan review dokumen PPEPP program studi di bawah <?= htmlspecialchars($fakultas['nama_fakultas']) ?>.</p>
            </div>
            <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill small">
                Total <?= count($prodiSummaries) ?> Program Studi
            </span>
        </div>

        <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-secondary" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3" style="width: 70px;">Kode</th>
                        <th>Program Studi</th>
                        <th>Fakultas</th>
                        <th>Ketua Program Studi</th>
                        <th class="text-center">Total Dokumen</th>
                        <th class="text-center">Perlu Review</th>
                        <th class="text-center">Perlu Revisi</th>
                        <th class="text-center">Sesuai</th>
                        <th style="min-width: 140px;">Progres Kepatuhan</th>
                        <th class="text-center pe-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($prodiSummaries)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                Tidak ada program studi terdaftar di bawah fakultas ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($prodiSummaries as $psum): 
                            $totPrd = (int)($psum['total_dokumen'] ?? 0);
                            $revPrd = (int)($psum['perlu_review_count'] ?? 0);
                            $rejPrd = (int)($psum['revisi_count'] ?? 0);
                            $appPrd = (int)($psum['sesuai_count'] ?? 0);
                            $pctPrd = $totPrd > 0 ? round(($appPrd / $totPrd) * 100) : 0;
                        ?>
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-light text-dark border font-monospace fw-bold"><?= htmlspecialchars($psum['kode_prodi']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark-blue"><?= htmlspecialchars($psum['nama_prodi']) ?></div>
                                    <span class="badge bg-dark-blue text-warning" style="font-size:0.65rem;"><?= htmlspecialchars($psum['jenjang']) ?></span>
                                </td>
                                <td class="small text-secondary"><?= htmlspecialchars($psum['nama_fakultas']) ?></td>
                                <td class="small text-secondary"><?= htmlspecialchars($psum['nama_kaprodi'] ?: '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-bold">
                                        <?= $totPrd ?> Dokumen
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($revPrd > 0): ?>
                                        <a href="<?= base_url('gpm/dokumen?unit=' . $psum['id'] . '&status_review=belum_direview') ?>" class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold text-decoration-none" title="Klik untuk memfilter dokumen perlu review">
                                            <?= $revPrd ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($rejPrd > 0): ?>
                                        <a href="<?= base_url('gpm/dokumen?unit=' . $psum['id'] . '&status_review=perlu_perbaikan') ?>" class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold text-decoration-none" title="Klik untuk memfilter dokumen perlu perbaikan">
                                            <?= $rejPrd ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($appPrd > 0): ?>
                                        <a href="<?= base_url('gpm/dokumen?unit=' . $psum['id'] . '&status_review=sesuai') ?>" class="badge bg-success rounded-pill px-2.5 py-1 fw-bold text-decoration-none" title="Klik untuk memfilter dokumen sesuai">
                                            <?= $appPrd ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 7px; border-radius: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctPrd ?>%" aria-valuenow="<?= $pctPrd ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="small fw-bold text-dark" style="font-size: 0.72rem; min-width: 32px;"><?= $pctPrd ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center pe-3">
                                    <a href="<?= base_url('gpm/dokumen?unit=' . $psum['id']) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold">
                                        <i class="fas fa-folder-open me-1"></i> Lihat Dokumen
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
