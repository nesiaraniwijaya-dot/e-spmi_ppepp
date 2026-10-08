<?php
/**
 * Gugus Penjaminan Mutu (GPM) Dashboard View
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge text-white px-3 py-1 rounded-pill" style="background: #8b5cf6;">
                    <i class="fas fa-shield-halved me-1"></i> GPM Fakultas <?= htmlspecialchars($fakultas['kode_fakultas'] ?? '') ?>
                </span>
                <span class="badge bg-light text-secondary border">Koordinator Mutu Fakultas &amp; Program Studi</span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-0">Panel GPM: <?= htmlspecialchars($fakultas['nama_fakultas']) ?></h3>
            <p class="text-muted small mb-0 mt-1">Mengelola dokumen SPMI PPEPP tingkat Fakultas serta seluruh Program Studi binaan di bawah <?= htmlspecialchars($fakultas['nama_fakultas']) ?>.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('gpm/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0 d-flex align-items-center gap-2">
                <i class="fas fa-cloud-arrow-up"></i>
                <span>Unggah Dokumen Mutu</span>
            </a>
            <a href="<?= base_url('fakultas/' . $fakultas['id'] . '/ppepp') ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1.5">
                <i class="fas fa-globe"></i>
                <span>Web Publik Fakultas</span>
            </a>
        </div>
    </div>

    <!-- Alert Banner Perlu Perbaikan (Jika Ada Dokumen Direvisi LPM) -->
    <?php 
    $perluTindakanList = array_values(array_filter($revisiDocs ?? [], fn($d) => ($d['status_review'] ?? '') === 'perlu_perbaikan'));
    $perluCount = count($perluTindakanList);
    if ($perluCount > 0): 
    ?>
        <div class="alert alert-danger border-danger border-2 rounded-4 p-3.5 mb-4 shadow-sm d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3" 
             style="background: linear-gradient(135deg, #FEF2F2 0%, #FFF5F5 100%);">
            <div class="d-flex align-items-center gap-3.5">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    <i class="fas fa-triangle-exclamation fa-beat"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-danger rounded-pill px-2.5 py-0.5 text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Tindakan Diperlukan</span>
                        <span class="text-danger fw-bold small">&bull; <?= $perluCount ?> Dokumen Butuh Revisi</span>
                    </div>
                    <h6 class="fw-bold text-dark-blue mb-1" style="font-size: 1.02rem;">
                        Pemberitahuan: Terdapat <?= $perluCount ?> dokumen mutu binaan yang memerlukan perbaikan dari LPM!
                    </h6>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem;">
                        Lembaga Penjaminan Mutu telah memberikan catatan evaluasi. Harap koordinasikan dan perbarui berkas dokumen agar dapat disahkan.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0 align-self-end align-self-md-center">
                <a href="<?= base_url('gpm/perbaikan') ?>" class="btn btn-danger btn-sm rounded-pill px-4 py-2.5 fw-bold shadow-xs d-inline-flex align-items-center gap-2 text-nowrap" style="font-size: 0.84rem;">
                    <i class="fas fa-wrench"></i>
                    <span>Tindak Lanjuti Perbaikan</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Overview Scope Summary Card -->
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-lg-7 border-end-lg pe-lg-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" 
                         style="width: 56px; height: 56px; background: linear-gradient(135deg, #7c3aed, #8b5cf6); font-size: 1.6rem;">
                        <i class="fas fa-diagram-project"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-semibold" style="font-size: 0.75rem;">RUANG LINGKUP PENJAMINAN MUTU:</div>
                        <h5 class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($fakultas['nama_fakultas']) ?></h5>
                        <div class="small text-secondary">
                            Menaungi <strong>1 Tingkat Fakultas (Dekanat)</strong> &bull; <strong><?= count($prodisList) ?> Program Studi</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 ps-lg-4">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="p-2 bg-light rounded-3 border">
                            <div class="fs-5 fw-bold text-primary"><?= $fakultasDocCount ?></div>
                            <div class="text-muted" style="font-size: 0.7rem;">Dok. Fakultas</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded-3 border">
                            <div class="fs-5 fw-bold text-success"><?= $cycleStats['total'] - $fakultasDocCount ?></div>
                            <div class="text-muted" style="font-size: 0.7rem;">Dok. Prodi</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded-3 border <?= $reviewStats['perlu_perbaikan'] > 0 ? 'border-danger bg-danger-subtle' : '' ?>">
                            <div class="fs-5 fw-bold <?= $reviewStats['perlu_perbaikan'] > 0 ? 'text-danger' : 'text-secondary' ?>"><?= $reviewStats['perlu_perbaikan'] ?></div>
                            <div class="text-muted" style="font-size: 0.7rem;">Perlu Revisi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5 Cycles Metric Cards -->
    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-penetapan) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(30, 64, 175, 0.1); color: var(--ppepp-penetapan); font-weight: 700; font-size: 0.72rem;">P1</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(30, 64, 175, 0.08); color: var(--ppepp-penetapan);">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['penetapan'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Penetapan</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Fakultas &amp; Prodi</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-pelaksanaan) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(5, 150, 105, 0.1); color: var(--ppepp-pelaksanaan); font-weight: 700; font-size: 0.72rem;">P2</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(5, 150, 105, 0.08); color: var(--ppepp-pelaksanaan);">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['pelaksanaan'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Pelaksanaan</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Fakultas &amp; Prodi</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-evaluasi) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(217, 119, 6, 0.1); color: var(--ppepp-evaluasi); font-weight: 700; font-size: 0.72rem;">E</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(217, 119, 6, 0.08); color: var(--ppepp-evaluasi);">
                        <i class="fas fa-magnifying-glass-chart"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['evaluasi'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Evaluasi</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Fakultas &amp; Prodi</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-pengendalian) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(124, 58, 237, 0.1); color: var(--ppepp-pengendalian); font-weight: 700; font-size: 0.72rem;">P3</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(124, 58, 237, 0.08); color: var(--ppepp-pengendalian);">
                        <i class="fas fa-sliders"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['pengendalian'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Pengendalian</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Fakultas &amp; Prodi</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-peningkatan) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(13, 148, 136, 0.1); color: var(--ppepp-peningkatan); font-weight: 700; font-size: 0.72rem;">P4</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(13, 148, 136, 0.08); color: var(--ppepp-peningkatan);">
                        <i class="fas fa-arrow-trend-up"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['peningkatan'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Peningkatan</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Fakultas &amp; Prodi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Rekapitulasi Status per Unit (Dekanat & Program Studi) -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card bg-white border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-dark-blue mb-0.5 d-flex align-items-center gap-2">
                            <i class="fas fa-chart-pie text-primary"></i> Rekapitulasi Status per Unit (Dekanat &amp; Program Studi)
                        </h5>
                        <p class="text-muted small mb-0">Tabel komparasi kepatuhan dan status review dokumen PPEPP khusus di bawah naungan <?= htmlspecialchars($fakultas['nama_fakultas']) ?>.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('gpm/rekapitulasi') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                            <i class="fas fa-up-right-from-square me-1"></i> Buka Halaman Lengkap
                        </a>
                        <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0">
                            Lihat Semua Dokumen <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Section A: Tingkat Fakultas (Dekanat) -->
                <div class="mb-4">
                    <div class="small fw-bold text-secondary text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="fas fa-landmark text-primary me-1"></i> Rekapitulasi Tingkat Fakultas (Dekanat)
                    </div>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light text-secondary" style="font-size: 0.74rem; text-transform: uppercase;">
                                <tr>
                                    <th class="ps-3" style="width: 80px;">Kode</th>
                                    <th>Fakultas</th>
                                    <th>Pimpinan Dekan</th>
                                    <th class="text-center">Total Dokumen</th>
                                    <th class="text-center">Perlu Review</th>
                                    <th class="text-center">Perlu Revisi</th>
                                    <th class="text-center">Sesuai Mutu</th>
                                    <th style="min-width: 140px;">Progres Kepatuhan</th>
                                    <th class="text-center pe-3" style="width: 130px;">Aksi</th>
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
                                    <td class="ps-3"><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($fakultasSummary['kode_fakultas']) ?></span></td>
                                    <td>
                                        <div class="fw-bold text-dark-blue"><?= htmlspecialchars($fakultasSummary['nama_fakultas']) ?></div>
                                        <span class="badge text-white rounded-pill px-2 py-0.5" style="background:#1e3a8a; font-size:0.65rem;">DEKANAT</span>
                                    </td>
                                    <td class="text-secondary small"><?= htmlspecialchars($fakultasSummary['nama_dekan'] ?: '-') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-bold">
                                            <?= $totFak ?> Dokumen
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($revFak > 0): ?>
                                            <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold"><?= $revFak ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($rejFak > 0): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold"><?= $rejFak ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($appFak > 0): ?>
                                            <span class="badge bg-success rounded-pill px-2.5 py-1 fw-bold"><?= $appFak ?></span>
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
                                        <a href="<?= base_url('gpm/dokumen?unit=fakultas') ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1">
                                            <i class="fas fa-folder-open me-1"></i> Buka Dokumen
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section B: Program Studi Binaan -->
                <div>
                    <div class="small fw-bold text-secondary text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="fas fa-graduation-cap text-primary me-1"></i> Rekapitulasi Program Studi Binaan (<?= count($prodiSummaries) ?> Prodi)
                    </div>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light text-secondary" style="font-size: 0.74rem; text-transform: uppercase;">
                                <tr>
                                    <th class="ps-3" style="width: 70px;">Kode</th>
                                    <th>Program Studi</th>
                                    <th>Ketua Program Studi</th>
                                    <th class="text-center">Total Dokumen</th>
                                    <th class="text-center">Perlu Review</th>
                                    <th class="text-center">Perlu Revisi</th>
                                    <th class="text-center">Sesuai</th>
                                    <th style="min-width: 140px;">Progres Kepatuhan</th>
                                    <th class="text-center pe-3" style="width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($prodiSummaries)): ?>
                                    <tr><td colspan="9" class="text-center py-3 text-muted">Belum ada program studi di fakultas ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($prodiSummaries as $ps): 
                                        $totPrd = (int)($ps['total_dokumen'] ?? 0);
                                        $revPrd = (int)($ps['perlu_review_count'] ?? 0);
                                        $rejPrd = (int)($ps['revisi_count'] ?? 0);
                                        $appPrd = (int)($ps['sesuai_count'] ?? 0);
                                        $pctPrd = $totPrd > 0 ? round(($appPrd / $totPrd) * 100) : 0;
                                    ?>
                                        <tr>
                                            <td class="ps-3"><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($ps['kode_prodi']) ?></span></td>
                                            <td>
                                                <div class="fw-bold text-dark-blue"><?= htmlspecialchars($ps['nama_prodi']) ?></div>
                                                <span class="badge bg-dark-blue text-warning" style="font-size:0.65rem;"><?= htmlspecialchars($ps['jenjang']) ?></span>
                                            </td>
                                            <td class="small text-secondary"><?= htmlspecialchars($ps['nama_kaprodi'] ?: '-') ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-bold">
                                                    <?= $totPrd ?> Dokumen
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($revPrd > 0): ?>
                                                    <a href="<?= base_url('gpm/dokumen?unit=' . $ps['id'] . '&status_review=belum_direview') ?>" class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold text-decoration-none">
                                                        <?= $revPrd ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($rejPrd > 0): ?>
                                                    <a href="<?= base_url('gpm/dokumen?unit=' . $ps['id'] . '&status_review=perlu_perbaikan') ?>" class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold text-decoration-none">
                                                        <?= $rejPrd ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($appPrd > 0): ?>
                                                    <a href="<?= base_url('gpm/dokumen?unit=' . $ps['id'] . '&status_review=sesuai') ?>" class="badge bg-success rounded-pill px-2.5 py-1 fw-bold text-decoration-none">
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
                                                <a href="<?= base_url('gpm/dokumen?unit=' . $ps['id']) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1">
                                                    <i class="fas fa-folder-open me-1"></i> Buka Dokumen
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
        </div>
    </div>

    <!-- Section: Dokumen Memerlukan Revisi Evaluasi LPM -->
    <?php 
    $perluTindakanDocs = array_values(array_filter($revisiDocs ?? [], fn($d) => ($d['status_review'] ?? '') === 'perlu_perbaikan'));
    $sudahDiperbaikiDocs = array_values(array_filter($revisiDocs ?? [], fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));
    $totalRevisiCount = count($revisiDocs ?? []);
    ?>
    <?php if ($totalRevisiCount > 0): ?>
        <div class="dashboard-revision-box mb-4 shadow-sm" style="border-color: <?= !empty($perluTindakanDocs) ? '#FECACA' : '#BFDBFE' ?> !important;">
            <!-- Header Ringkasan Status Revisi -->
            <div class="dashboard-revision-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3"
                 style="background: <?= !empty($perluTindakanDocs) ? 'linear-gradient(135deg, #FEF2F2 0%, #FFF5F5 100%)' : 'linear-gradient(135deg, #EFF6FF 0%, #F0F7FF 100%)' ?>;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" 
                         style="width: 46px; height: 46px; font-size: 1.25rem; background: <?= !empty($perluTindakanDocs) ? '#DC2626; color: #ffffff;' : '#2563EB; color: #ffffff;' ?>">
                        <i class="fas <?= !empty($perluTindakanDocs) ? 'fa-wrench fa-beat-fade' : 'fa-clock-rotate-left' ?>"></i>
                    </div>
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <h5 class="fw-bold mb-0" style="color: <?= !empty($perluTindakanDocs) ? '#991B1B' : '#1E40AF' ?>; font-size: 1.12rem; letter-spacing: -0.2px;">
                                Dokumen Memerlukan Revisi Evaluasi LPM
                            </h5>
                            <?php if (count($perluTindakanDocs) > 0): ?>
                                <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold shadow-2xs" style="font-size: 0.72rem;">
                                    <?= count($perluTindakanDocs) ?> Perlu Tindakan Segera
                                </span>
                            <?php endif; ?>
                            <?php if (count($sudahDiperbaikiDocs) > 0): ?>
                                <span class="badge bg-primary rounded-pill px-2.5 py-1 fw-semibold shadow-2xs" style="font-size: 0.72rem;">
                                    <?= count($sudahDiperbaikiDocs) ?> Sedang Ditinjau LPM
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-secondary small mb-0" style="font-size: 0.84rem; line-height: 1.45;">
                            Terdapat catatan evaluasi dari Pusat Penjaminan Mutu LPM yang harus diperbaiki agar berkas dapat disahkan.
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="<?= base_url('gpm/perbaikan') ?>" class="btn <?= !empty($perluTindakanDocs) ? 'btn-danger' : 'btn-primary' ?> rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                        <span>Buka Ruang Kerja Perbaikan</span>
                    </a>
                </div>
            </div>

            <!-- Daftar Dokumen Perbaikan -->
            <div class="dashboard-revision-body">
                <?php 
                $displayDocs = array_slice($revisiDocs, 0, 5);
                foreach ($displayDocs as $item): 
                    $isPerlu = ($item['status_review'] ?? '') === 'perlu_perbaikan';
                    $noteExcerpt = trim($item['catatan_review'] ?? '');
                    if (empty($noteExcerpt)) {
                        $noteExcerpt = $isPerlu ? 'Silakan periksa dan sesuaikan dokumen mutu sesuai arahan Pusat Penjaminan Mutu LPM.' : 'Dokumen telah diperbaiki dan sedang dalam antrean verifikasi ulang LPM.';
                    }
                    $isFakultas = ($item['level'] ?? '') === 'fakultas';
                    $unitBadge = $isFakultas ? 'Fakultas' : (($item['jenjang'] ?? 'S1') . ' • ' . ($item['nama_prodi'] ?? 'Prodi'));
                ?>
                    <div class="dashboard-revision-item d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 gap-lg-4">
                        <!-- Left Info -->
                        <div class="d-flex align-items-start gap-3.5 flex-grow-1" style="min-width: 0;">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 mt-1 shadow-2xs" 
                                 style="width: 44px; height: 44px; font-size: 1.15rem; background: <?= $isPerlu ? '#FEE2E2; color: #DC2626; border: 1px solid #FECACA;' : '#EFF6FF; color: #2563EB; border: 1px solid #BFDBFE;' ?>">
                                <i class="fas <?= $isPerlu ? 'fa-triangle-exclamation' : 'fa-clock-rotate-left' ?>"></i>
                            </div>
                            <div style="min-width: 0;" class="flex-grow-1">
                                <!-- Badges -->
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <?php if ($isPerlu): ?>
                                        <span class="badge rounded-pill bg-danger text-white px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">
                                            <i class="fas fa-circle-exclamation me-1"></i> Perlu Tindakan
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-primary text-white px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">
                                            <i class="fas fa-clock-rotate-left me-1"></i> Menunggu Review
                                        </span>
                                    <?php endif; ?>
                                    
                                    <!-- Unit Badge -->
                                    <?php if ($isFakultas): ?>
                                        <span class="badge text-white px-2 py-0.5 rounded" style="background:#1e3a8a; font-size: 0.7rem;">
                                            <i class="fas fa-landmark me-1"></i> Fakultas
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-dark-blue text-warning px-2 py-0.5 rounded" style="font-size: 0.7rem;">
                                            <?= htmlspecialchars($unitBadge) ?>
                                        </span>
                                    <?php endif; ?>

                                    <?= siklus_badge($item['siklus']) ?>
                                    <?php if (!empty($item['nomor_dokumen'])): ?>
                                        <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 0.7rem;">
                                            <i class="fas fa-hashtag me-0.5 opacity-75"></i><?= htmlspecialchars($item['nomor_dokumen']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Document Title -->
                                <h6 class="mb-2 fw-bold" style="font-size: 1rem; line-height: 1.55;">
                                    <a href="<?= base_url('gpm/dokumen/edit/' . $item['id']) ?>" class="text-dark-blue text-decoration-none hover-primary">
                                        <?= htmlspecialchars($item['nama_dokumen']) ?>
                                    </a>
                                </h6>

                                <!-- Reviewer Meta -->
                                <div class="d-inline-flex flex-wrap align-items-center gap-2 text-muted px-2.5 py-1 rounded-pill bg-light border mt-1" style="font-size: 0.76rem;">
                                    <span class="d-inline-flex align-items-center gap-1.5">
                                        <i class="fas fa-building-columns text-primary"></i>
                                        <span><strong class="text-dark">Pusat Penjaminan Mutu LPM</strong></span>
                                    </span>
                                    <?php if (!empty($item['reviewed_at'])): ?>
                                        <span class="text-secondary opacity-50">&bull;</span>
                                        <span class="d-inline-flex align-items-center gap-1">
                                             <i class="far fa-calendar-check text-secondary"></i>
                                            <span><?= date('d M Y, H:i', strtotime($item['reviewed_at'])) ?> WIB</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Action Buttons Symmetrically Aligned -->
                        <div class="dashboard-revision-actions align-self-start align-self-lg-center pt-2 pt-lg-0">
                            <!-- Tombol Lihat Catatan Evaluator (Modal Popup) -->
                            <button type="button" class="btn btn-outline-secondary btn-revision-note shadow-2xs fw-semibold"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalCatatanEvaluator"
                                    data-nama="<?= htmlspecialchars($item['nama_dokumen']) ?>"
                                    data-reviewer="Pusat Penjaminan Mutu LPM"
                                    data-tanggal="<?= !empty($item['reviewed_at']) ? date('d M Y, H:i', strtotime($item['reviewed_at'])) . ' WIB' : '-' ?>"
                                    data-catatan="<?= htmlspecialchars($noteExcerpt) ?>"
                                    data-siklus="<?= htmlspecialchars($item['siklus'] ?? '') ?>"
                                    data-status="<?= $isPerlu ? 'perlu_perbaikan' : 'sudah_diperbaiki' ?>"
                                    data-edit-url="<?= base_url('gpm/dokumen/edit/' . $item['id']) ?>">
                                <i class="fas fa-comment-dots <?= $isPerlu ? 'text-danger' : 'text-primary' ?> me-2"></i>
                                <span>Lihat Catatan</span>
                            </button>

                            <?php if ($isPerlu): ?>
                                <a href="<?= base_url('gpm/dokumen/edit/' . $item['id']) ?>" class="btn btn-danger btn-revision-action fw-bold shadow-xs">
                                    <i class="fas fa-wrench me-2"></i>
                                    <span>Perbaiki</span>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-light text-primary border border-primary-subtle btn-revision-action fw-semibold">
                                    <i class="fas fa-hourglass-half text-primary me-2"></i>
                                    <span>Dalam Antrean</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Footer Tautan ke Ruang Kerja Perbaikan -->
            <div class="dashboard-revision-footer d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2.5">
                <span class="text-muted d-flex align-items-center gap-1.5">
                    <i class="fas fa-list-check text-secondary"></i>
                    <span>Menampilkan <strong><?= count($displayDocs) ?></strong> dari total <strong><?= $totalRevisiCount ?></strong> dokumen perbaikan.</span>
                </span>
                <a href="<?= base_url('gpm/perbaikan') ?>" class="text-danger fw-bold text-decoration-none hover-underline d-inline-flex align-items-center gap-1.5">
                    <span>Buka Menu Perbaikan Dokumen Lengkap</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Section: Dokumen Diunggah Hari Ini -->
    <div class="card bg-white border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold text-dark-blue mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-day text-primary"></i> Dokumen Diunggah Hari Ini
                </h5>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                    <?= date('d M Y') ?>
                </span>
            </div>
            <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                Lihat Semua Dokumen <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <?php if (empty($recentDocs)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-calendar-xmark fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                <h6 class="fw-bold text-dark">Tidak ada dokumen yang diunggah hari ini</h6>
                <p class="small text-muted mb-3">Belum ada dokumen mutu yang diunggah pada hari ini (<?= date('d M Y') ?>). Daftar ini menampilkan dokumen yang diunggah pada hari yang sama dan otomatis berganti saat berganti hari.</p>
                <a href="<?= base_url('gpm/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5">
                    <i class="fas fa-cloud-arrow-up me-1"></i> Unggah Dokumen Baru
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted" style="font-size: 0.74rem;">
                        <tr>
                            <th style="width: 45px;" class="text-center">No</th>
                            <th>Nama &amp; Nomor Dokumen</th>
                            <th>Unit Sasaran</th>
                            <th style="width: 130px;">Siklus PPEPP</th>
                            <th>Bidang &amp; Standar</th>
                            <th>Status Review</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($recentDocs as $rd): ?>
                            <tr>
                                <td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($rd['nama_dokumen']) ?></div>
                                    <div class="d-flex align-items-center gap-2 small text-muted">
                                        <?php if ($rd['nomor_dokumen']): ?>
                                            <span><i class="fas fa-hashtag me-0.5"></i> <?= htmlspecialchars($rd['nomor_dokumen']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($rd['tahun_akademik'])): ?>
                                            <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.68rem;">TA <?= htmlspecialchars($rd['tahun_akademik']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($rd['level'] === 'fakultas'): ?>
                                        <span class="badge rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.74rem;">
                                            <i class="fas fa-landmark"></i> Fakultas
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.74rem;">
                                            <i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($rd['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($rd['nama_prodi'] ?? 'Prodi') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= siklus_badge($rd['siklus']) ?></td>
                                <td>
                                    <div class="small fw-semibold text-dark"><?= htmlspecialchars($rd['nama_bidang'] ?: 'Umum / Lainnya') ?></div>
                                    <?php if (!empty($rd['nama_sub_bidang'])): ?>
                                        <div class="text-muted small d-flex align-items-center gap-1 mt-0.5" style="font-size: 0.7rem;">
                                            <i class="fas fa-turn-up fa-rotate-90 text-primary opacity-60"></i>
                                            <span class="badge bg-light text-secondary border text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($rd['nama_sub_bidang']) ?>">
                                                <?= htmlspecialchars($rd['nama_sub_bidang']) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $stClass = match($rd['status_review']) {
                                        'sesuai' => 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;',
                                        'perlu_perbaikan' => 'background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;',
                                        'sudah_diperbaiki' => 'background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D;',
                                        'draft' => 'background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;',
                                        default => 'background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A;'
                                    };
                                    $stText = match($rd['status_review']) {
                                        'sesuai' => 'Sesuai Standar',
                                        'perlu_perbaikan' => 'Perlu Perbaikan',
                                        'sudah_diperbaiki' => 'Sudah Diperbaiki',
                                        'draft' => 'Draf Dokumen',
                                        default => 'Belum Direview'
                                    };
                                    ?>
                                    <span class="badge rounded-pill px-2.5 py-1" style="<?= $stClass ?> font-size: 0.72rem; font-weight: 700;"><?= $stText ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('gpm/dokumen/edit/' . $rd['id']) ?>" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;" title="Edit Dokumen">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail Catatan Evaluator LPM -->
<div class="modal fade" id="modalCatatanEvaluator" tabindex="-1" aria-labelledby="modalCatatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3.5 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" id="evalModalIcon" style="width: 38px; height: 38px; font-size: 1rem; background: #DC2626;">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0.5" id="modalCatatanLabel" style="font-size: 1.1rem; letter-spacing: -0.2px;">
                            Catatan Evaluasi / Arahan LPM
                        </h5>
                        <p class="text-white text-opacity-75 small mb-0" style="font-size: 0.78rem;">Lembaga Penjaminan Mutu UNIKA Soegijapranata</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <div class="small text-muted mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                        Nama Dokumen
                    </div>
                    <div class="fw-bold text-dark-blue" id="evalModalDocTitle" style="font-size: 0.98rem; line-height: 1.45;">
                        -
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-2 pt-2 border-top text-muted small" style="font-size: 0.76rem;">
                        <div class="d-flex align-items-center gap-1.5">
                            <i class="fas fa-building-columns text-primary"></i>
                            <span class="text-dark fw-bold">Pusat Penjaminan Mutu LPM</span>
                        </div>
                        <div class="d-flex align-items-center gap-1.5">
                            <i class="far fa-calendar-check text-secondary"></i>
                            <span>Waktu Review: <strong class="text-dark" id="evalModalTanggal">-</strong></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label fw-bold text-dark-blue d-flex align-items-center gap-1.5 mb-2" style="font-size: 0.86rem;">
                        <i class="fas fa-file-lines text-warning"></i>
                        <span>Uraian Catatan Evaluasi &amp; Poin Perbaikan:</span>
                    </label>
                    <div class="p-3.5 rounded-3 bg-white border border-danger-subtle shadow-2xs" 
                         id="evalModalCatatan"
                         style="background-color: #FFFDFD !important; font-size: 0.88rem; line-height: 1.6; color: #1E293B; max-height: 260px; overflow-y: auto; white-space: pre-wrap;">
                        -
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5 px-4 bg-light bg-opacity-50 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-1.5 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="evalModalEditBtn" class="btn btn-danger btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2">
                    <i class="fas fa-wrench me-1"></i>
                    <span>Perbaiki Dokumen Sekarang</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('modalCatatanEvaluator');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const nama = btn.getAttribute('data-nama') || '-';
            const reviewer = btn.getAttribute('data-reviewer') || '-';
            const tanggal = btn.getAttribute('data-tanggal') || '-';
            const catatan = btn.getAttribute('data-catatan') || 'Tidak ada catatan khusus.';
            const editUrl = btn.getAttribute('data-edit-url') || '#';
            const status = btn.getAttribute('data-status') || 'perlu_perbaikan';

            const titleEl = document.getElementById('evalModalDocTitle');
            const revEl = document.getElementById('evalModalReviewer');
            const tglEl = document.getElementById('evalModalTanggal');
            const catEl = document.getElementById('evalModalCatatan');
            const editBtn = document.getElementById('evalModalEditBtn');
            const iconEl = document.getElementById('evalModalIcon');

            if (titleEl) titleEl.textContent = nama;
            if (revEl) revEl.textContent = reviewer;
            if (tglEl) tglEl.textContent = tanggal;
            if (catEl) catEl.textContent = catatan;
            
            if (editBtn) {
                editBtn.href = editUrl;
                if (status === 'sudah_diperbaiki') {
                    editBtn.className = 'btn btn-outline-primary btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2';
                    editBtn.innerHTML = '<i class="fas fa-pen-to-square me-1"></i> <span>Ubah / Lengkapi Berkas</span>';
                    if (iconEl) iconEl.style.background = '#2563EB';
                } else {
                    editBtn.className = 'btn btn-danger btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2';
                    editBtn.innerHTML = '<i class="fas fa-wrench me-1"></i> <span>Perbaiki Dokumen Sekarang</span>';
                    if (iconEl) iconEl.style.background = '#DC2626';
                }
            }
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
