<?php
/**
 * Admin Prodi Dashboard View
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark-blue text-warning px-3 py-1"><?= htmlspecialchars($prodi['jenjang']) ?> - <?= htmlspecialchars($prodi['kode_prodi']) ?></span>
                <span class="badge bg-light text-secondary border"><?= htmlspecialchars($prodi['nama_fakultas']) ?></span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-0">Panel Admin: <?= htmlspecialchars($prodi['nama_prodi']) ?></h3>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('prodi/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0">
                <i class="fas fa-cloud-arrow-up me-1"></i> Unggah Dokumen Baru
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
                        Pemberitahuan: Terdapat <?= $perluCount ?> dokumen mutu yang memerlukan perbaikan dari LPM!
                    </h6>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem;">
                        Tim Penjaminan Mutu telah memberikan catatan evaluasi. Harap segera periksa dan perbarui berkas agar dapat disahkan.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0 align-self-end align-self-md-center">
                <a href="<?= base_url('prodi/perbaikan') ?>" class="btn btn-danger btn-sm rounded-pill px-4 py-2.5 fw-bold shadow-xs d-inline-flex align-items-center gap-2 text-nowrap" style="font-size: 0.84rem;">
                    <i class="fas fa-wrench"></i>
                    <span>Tindak Lanjuti Perbaikan</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pimpinan Prodi Profile Alert Card (Kaprodi & Sekprodi) -->
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-20 text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.25rem;">
                        <i class="fas fa-graduation-cap text-warning"></i>
                    </div>
                    <div>
                        <div class="small text-muted" style="font-size: 0.73rem;">Ketua Program Studi (Kaprodi):</div>
                        <div class="fw-bold text-dark-blue" style="font-size: 0.92rem;"><?= htmlspecialchars($prodi['nama_kaprodi'] ?: 'Belum diatur') ?></div>
                        <?php if (!empty($prodi['nidn_kaprodi'])): ?>
                            <div class="text-muted" style="font-size: 0.72rem;">NIDN: <?= htmlspecialchars($prodi['nidn_kaprodi']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="vr d-none d-md-block" style="height: 38px; opacity: 0.15;"></div>

                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-15 text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        <i class="fas fa-signature text-success"></i>
                    </div>
                    <div>
                        <div class="small text-muted" style="font-size: 0.73rem;">Sekretaris Program Studi (Sekprodi):</div>
                        <div class="fw-bold text-dark-blue" style="font-size: 0.92rem;"><?= htmlspecialchars($prodi['nama_sekprodi'] ?: 'Belum diatur') ?></div>
                        <?php if (!empty($prodi['nidn_sekprodi'])): ?>
                            <div class="text-muted" style="font-size: 0.72rem;">NIDN: <?= htmlspecialchars($prodi['nidn_sekprodi']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <a href="<?= base_url('prodi/kaprodi') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 align-self-start align-self-md-center text-nowrap">
                <i class="fas fa-pen-to-square me-1"></i> Kelola Pimpinan Prodi
            </a>
        </div>
    </div>

    <!-- 5 Cycles Metric Cards (Uniform 5-Column Grid on Desktop, Balanced on Tablet/Mobile) -->
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
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Mutu</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-pelaksanaan) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(5, 150, 105, 0.1); color: var(--ppepp-pelaksanaan); font-weight: 700; font-size: 0.72rem;">P2</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(5, 150, 105, 0.08); color: var(--ppepp-pelaksanaan);">
                        <i class="fas fa-person-chalkboard"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['pelaksanaan'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Pelaksanaan</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Mutu</div>
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
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Mutu</div>
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
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Mutu</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-peningkatan) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(8, 145, 178, 0.1); color: var(--ppepp-peningkatan); font-weight: 700; font-size: 0.72rem;">P4</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(8, 145, 178, 0.08); color: var(--ppepp-peningkatan);">
                        <i class="fas fa-arrow-trend-up"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['peningkatan'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Peningkatan</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Mutu</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pusat Notifikasi & Ringkasan Perbaikan Dokumen dari LPM -->
    <?php 
    $perluTindakanDocs = array_values(array_filter($revisiDocs ?? [], fn($d) => ($d['status_review'] ?? '') === 'perlu_perbaikan'));
    $menungguVerifikasiDocs = array_values(array_filter($revisiDocs ?? [], fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));
    $totalRevisiCount = count($perluTindakanDocs) + count($menungguVerifikasiDocs);
    if ($totalRevisiCount > 0): 
    ?>
        <div class="dashboard-revision-box mb-4 shadow-sm" style="border-color: <?= !empty($perluTindakanDocs) ? '#FECACA' : '#BFDBFE' ?> !important;">
            <!-- Header Ringkasan Beruang Bernapas -->
            <div class="dashboard-revision-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" 
                 style="background: <?= !empty($perluTindakanDocs) ? 'linear-gradient(135deg, #FEF2F2 0%, #FFF5F5 100%)' : 'linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 100%)' ?>; border-bottom: 1px solid <?= !empty($perluTindakanDocs) ? '#FEE2E2' : '#DBEAFE' ?>;">
                <div class="d-flex align-items-center gap-3.5">
                    <div class="rounded-3 text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-2xs" 
                         style="width: 48px; height: 48px; font-size: 1.25rem; background: <?= !empty($perluTindakanDocs) ? '#DC2626' : '#2563EB' ?>;">
                        <i class="fas <?= !empty($perluTindakanDocs) ? 'fa-wrench' : 'fa-clock-rotate-left' ?>"></i>
                    </div>
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1.5">
                            <h6 class="fw-bold <?= !empty($perluTindakanDocs) ? 'text-danger' : 'text-primary' ?> mb-0" style="font-size: 1.05rem; letter-spacing: -0.2px;">
                                Dokumen Memerlukan Revisi Evaluasi LPM
                            </h6>
                            <?php if (!empty($perluTindakanDocs)): ?>
                                <span class="badge rounded-pill bg-danger text-white px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                    <?= count($perluTindakanDocs) ?> Perlu Tindakan Segera
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($menungguVerifikasiDocs)): ?>
                                <span class="badge rounded-pill bg-primary text-white px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                    <?= count($menungguVerifikasiDocs) ?> Sedang Ditinjau LPM
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-0" style="font-size: 0.83rem; line-height: 1.55;">
                            <?= !empty($perluTindakanDocs) ? 'Terdapat catatan revisi dari Tim Penjaminan Mutu yang harus diperbaiki agar berkas dapat disahkan.' : 'Berkas perbaikan Anda telah masuk ke antrean verifikasi ulang Tim Penjaminan Mutu.' ?>
                        </p>
                    </div>
                </div>

                <!-- Right Action Button -->
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="<?= base_url('prodi/perbaikan') ?>" class="btn <?= !empty($perluTindakanDocs) ? 'btn-danger' : 'btn-primary' ?> btn-sm rounded-pill px-4 py-2.5 fw-bold shadow-xs d-inline-flex align-items-center gap-2" style="font-size: 0.84rem;">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                        <span>Buka Ruang Kerja Perbaikan</span>
                    </a>
                </div>
            </div>

            <!-- List Ringkasan Maksimal 3 Dokumen Terkini (Spacious Breathing Room) -->
            <div class="p-0 bg-white">
                <?php 
                $displayDocs = array_slice(array_merge($perluTindakanDocs, $menungguVerifikasiDocs), 0, 3);
                foreach ($displayDocs as $item): 
                    $isPerlu = ($item['status_review'] ?? '') === 'perlu_perbaikan';
                    $noteExcerpt = trim($item['catatan_review'] ?? '');
                    if (empty($noteExcerpt)) {
                        $noteExcerpt = $isPerlu ? 'Silakan sesuaikan isi dan lampiran dokumen mutu sesuai standar penjaminan mutu.' : 'Menunggu review ulang LPM.';
                    }
                ?>
                    <div class="dashboard-revision-item d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 gap-lg-4">
                        <!-- Left: Status Icon, Title, and Badges with Generous Margins -->
                        <div class="d-flex align-items-start gap-3.5 flex-grow-1" style="min-width: 0;">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 mt-1 shadow-2xs" 
                                 style="width: 44px; height: 44px; font-size: 1.15rem; background: <?= $isPerlu ? '#FEE2E2; color: #DC2626; border: 1px solid #FECACA;' : '#EFF6FF; color: #2563EB; border: 1px solid #BFDBFE;' ?>">
                                <i class="fas <?= $isPerlu ? 'fa-triangle-exclamation' : 'fa-clock-rotate-left' ?>"></i>
                            </div>
                            <div style="min-width: 0;" class="flex-grow-1">
                                <!-- Line 1: Badges with margin bottom -->
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <?php if ($isPerlu): ?>
                                        <span class="badge rounded-pill bg-danger text-white px-2.5 py-1 fw-bold" style="font-size: 0.7rem; letter-spacing: 0.02em;">
                                            <i class="fas fa-circle-exclamation me-1"></i> Perlu Tindakan
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-primary text-white px-2.5 py-1 fw-bold" style="font-size: 0.7rem; letter-spacing: 0.02em;">
                                            <i class="fas fa-clock-rotate-left me-1"></i> Menunggu Review
                                        </span>
                                    <?php endif; ?>
                                    <?= siklus_badge($item['siklus']) ?>
                                    <?php if (!empty($item['nomor_dokumen'])): ?>
                                        <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 0.7rem;">
                                            <i class="fas fa-hashtag me-0.5 opacity-75"></i><?= htmlspecialchars($item['nomor_dokumen']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Line 2: Document Title with comfortable breathing room -->
                                <h6 class="mb-2 fw-bold" style="font-size: 1rem; line-height: 1.55;">
                                    <a href="<?= base_url('prodi/dokumen/edit/' . $item['id']) ?>" class="text-dark-blue text-decoration-none hover-primary">
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
                                    data-edit-url="<?= base_url('prodi/dokumen/edit/' . $item['id']) ?>">
                                <i class="fas fa-comment-dots <?= $isPerlu ? 'text-danger' : 'text-primary' ?> me-2"></i>
                                <span>Lihat Catatan</span>
                            </button>

                            <?php if ($isPerlu): ?>
                                <a href="<?= base_url('prodi/dokumen/edit/' . $item['id']) ?>" class="btn btn-danger btn-revision-action fw-bold shadow-xs">
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

                <!-- Footer Tautan ke Ruang Kerja Perbaikan dengan Ruang Lega -->
                <div class="dashboard-revision-footer d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2.5">
                    <span class="text-muted d-flex align-items-center gap-1.5">
                        <i class="fas fa-list-check text-secondary"></i>
                        <span>Menampilkan <strong><?= count($displayDocs) ?></strong> dari total <strong><?= $totalRevisiCount ?></strong> dokumen perbaikan.</span>
                    </span>
                    <a href="<?= base_url('prodi/perbaikan') ?>" class="text-danger fw-bold text-decoration-none hover-underline d-inline-flex align-items-center gap-1.5">
                        <span>Buka Menu Perbaikan Dokumen Lengkap</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Dokumen Diunggah Hari Ini (Full Width) -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold text-dark-blue mb-0">
                            <i class="fas fa-calendar-day text-primary me-2"></i> Dokumen Diunggah Hari Ini
                        </h5>
                        <span class="badge bg-light text-primary border px-2.5 py-1 rounded-pill small fw-semibold">
                            <i class="far fa-calendar-check me-1"></i> <?= date('d M Y') ?>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?= base_url('prodi/dokumen/arsip') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" style="font-size:0.75rem;">
                            <i class="fas fa-box-archive me-1"></i> <?= $cycleStats['arsip'] ?> Terarsip
                        </a>
                        <a href="<?= base_url('prodi/dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-3 bg-scu-blue border-0" style="font-size:0.75rem;">
                            Kelola Semua Dokumen &rarr;
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45px;" class="text-center">No</th>
                                <th>Nama &amp; Nomor Dokumen</th>
                                <th style="width: 140px;">Siklus PPEPP</th>
                                <th>Bidang &amp; Standar</th>
                                <th>Berkas</th>
                                <th style="min-width: 160px;">Status Review LPM</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentDocs)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-check fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                        <h6 class="fw-bold text-dark mb-1">Belum Ada Dokumen yang Diunggah Hari Ini</h6>
                                        <p class="small text-muted mb-0">Dokumen mutu yang diunggah pada hari ini (<?= date('d M Y') ?>) akan tampil secara otomatis di tabel ringkasan ini.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $rdNo = 1; foreach ($recentDocs as $rd): 
                                    $st = $rd['status_review'] ?? 'belum_direview';
                                    $rdFiles = $rd['files'] ?? [];
                                    $rdFilesCount = count($rdFiles);
                                    $rdLinks = parse_external_links($rd['external_link'] ?? '');
                                    $rdLinkCount = count($rdLinks);
                                    $rdHasFiles = ($rdFilesCount > 0 || (!empty($rd['file_path']) && $rd['jenis_upload'] !== 'link'));
                                    $rdHasLinks = ($rdLinkCount > 0);
                                    $rdIsKombinasi = ($rd['jenis_upload'] === 'kombinasi') || ($rdHasFiles && $rdHasLinks);
                                ?>
                                    <tr>
                                        <td class="text-center text-muted fw-semibold"><?= $rdNo++ ?></td>
                                        <td>
                                            <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($rd['nama_dokumen']) ?></div>
                                            <div class="d-flex flex-wrap align-items-center gap-1.5">
                                                <?php if ($rd['nomor_dokumen']): ?>
                                                    <span class="text-muted small" style="font-size:0.72rem;"><i class="fas fa-hashtag me-0.5"></i> <?= htmlspecialchars($rd['nomor_dokumen']) ?></span>
                                                <?php endif; ?>
                                                <?php if ($rd['tahun_akademik']): ?>
                                                    <span class="badge bg-light text-secondary border" style="font-size:0.68rem;">TA <?= htmlspecialchars($rd['tahun_akademik']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?= siklus_badge($rd['siklus']) ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark mb-0.5" style="font-size: 0.82rem;">
                                                <?= htmlspecialchars($rd['nama_bidang'] ?: 'Umum / Lainnya') ?>
                                            </div>
                                            <?php if (!empty($rd['nama_sub_bidang'])): ?>
                                                <div class="small text-muted d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                    <i class="fas fa-turn-up fa-rotate-90 text-secondary opacity-50"></i>
                                                    <span class="badge bg-light text-secondary border px-1.5 py-0.5"><?= htmlspecialchars($rd['nama_sub_bidang']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($rdIsKombinasi): ?>
                                                <span class="badge" style="background: #EDE9FE; color: #7C3AED; border: 1px solid #DDD6FE; font-size:0.7rem;">
                                                    <i class="fas fa-layer-group me-1"></i> Kombinasi
                                                </span>
                                            <?php elseif ($rdHasFiles): ?>
                                                <span class="badge rounded-pill" style="background: #FEE2E2 !important; color: #991B1B !important; border: 1px solid #FCA5A5 !important; font-size:0.72rem; font-weight: 700;">
                                                    <i class="fas fa-file-pdf me-1" style="color: #DC2626 !important;"></i> <?= $rdFilesCount > 1 ? $rdFilesCount . ' Berkas' : 'PDF' ?>
                                                </span>
                                            <?php elseif ($rdHasLinks): ?>
                                                <span class="badge rounded-pill" style="background: #EFF6FF !important; color: #1E40AF !important; border: 1px solid #93C5FD !important; font-size:0.72rem; font-weight: 700;">
                                                    <i class="fab fa-google-drive me-1" style="color: #2563EB !important;"></i> GDrive
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= review_status_badge($st) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1.5">
                                                <a href="<?= base_url('prodi/dokumen/edit/' . $rd['id']) ?>"
                                                   class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1"
                                                   style="font-size: 0.75rem;"
                                                   title="Ubah / Edit Dokumen">
                                                    <i class="fas fa-pen"></i> Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
                    <!-- Info Dokumen -->
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="small text-muted mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                            Nama Dokumen
                        </div>
                        <h6 class="fw-bold text-dark-blue mb-0" id="evalModalDocTitle" style="font-size: 0.98rem; line-height: 1.45;">-</h6>
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

                    <!-- Isi Catatan Evaluator -->
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
