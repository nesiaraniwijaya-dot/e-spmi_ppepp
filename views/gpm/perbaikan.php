<?php
/**
 * GPM (Gugus Penjaminan Mutu): Ruang Kerja Perbaikan Dokumen Mutu
 * Mengelola revisi dokumen dari LPM untuk tingkat Fakultas dan seluruh Program Studi
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$revisiDocs    = $revisiDocs ?? [];
$fakultas      = $fakultas ?? [];
$prodisList    = $prodisList ?? [];
$unitFilter    = $unitFilter ?? 'all';
$totalPerlu    = $totalPerlu ?? 0;
$totalSudah    = $totalSudah ?? 0;
$fakultasPerlu = $fakultasPerlu ?? 0;
$prodiPerlu    = $prodiPerlu ?? 0;

$perluDocs = array_values(array_filter($revisiDocs, fn($d) => ($d['status_review'] ?? '') === 'perlu_perbaikan'));
$sudahDocs = array_values(array_filter($revisiDocs, fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1.5">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #F3E8FF; color: #7E22CE; border: 1px solid #E9D5FF; font-size: 0.76rem;">
                    <i class="fas fa-shield-halved"></i> GPM <?= htmlspecialchars($fakultas['singkatan'] ?? $fakultas['nama_fakultas'] ?? '') ?>
                </span>
                <span class="text-muted small">&bull;</span>
                <span class="badge rounded-pill px-2.5 py-1 fw-semibold text-danger d-inline-flex align-items-center gap-1" style="background: #FEF2F2; border: 1px solid #FECACA; font-size: 0.75rem;">
                    <i class="fas fa-wrench"></i> Ruang Kerja Perbaikan
                </span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">Perbaikan Dokumen Mutu</h3>
            <p class="text-muted small mb-0" style="line-height: 1.5;">
                Tindak lanjuti dokumen mutu tingkat <strong>Fakultas</strong> dan seluruh <strong>Program Studi</strong> di bawah naungan <strong><?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?></strong> yang memperoleh catatan evaluasi dari Lembaga Penjaminan Mutu (LPM).
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('gpm/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-plus"></i> Unggah Dokumen Baru
            </a>
            <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-folder-open"></i> Seluruh Dokumen PPEPP
            </a>
            <a href="<?= base_url('gpm/draft') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-file-pen"></i> Draf Dokumen
            </a>
        </div>
    </div>

    <!-- Metric KPI Cards (4 Kolom: Total Perlu, Menunggu LPM, Scope Fakultas, Scope Prodi) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #DC2626 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-danger">Perlu Tindakan</span>
                    <div class="saas-stat-icon-wrap" style="background: #FEE2E2; color: #DC2626;">
                        <i class="fas fa-triangle-exclamation <?= $totalPerlu > 0 ? 'fa-beat-fade' : '' ?>"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-danger"><?= $totalPerlu ?></span>
                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">Wajib Revisi</span>
                    </div>
                    <div class="saas-stat-help">Dokumen yang memerlukan perbaikan dari LPM</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #2563EB !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-primary">Sudah Diperbaiki</span>
                    <div class="saas-stat-icon-wrap" style="background: #EFF6FF; color: #2563EB;">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-primary"><?= $totalSudah ?></span>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">Antrean LPM</span>
                    </div>
                    <div class="saas-stat-help">Menunggu peninjauan ulang oleh tim LPM</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #7C3AED !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label" style="color: #7C3AED;">Revisi Tk. Fakultas</span>
                    <div class="saas-stat-icon-wrap" style="background: #FAF5FF; color: #7C3AED;">
                        <i class="fas fa-building-columns"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number" style="color: #7C3AED;"><?= $fakultasPerlu ?></span>
                        <span class="badge rounded-pill bg-light text-secondary border px-2 py-0.5" style="font-size: 0.7rem;">Fakultas</span>
                    </div>
                    <div class="saas-stat-help">Dokumen PPEPP milik tingkat Fakultas</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #059669 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-success">Revisi Tk. Prodi</span>
                    <div class="saas-stat-icon-wrap" style="background: #ECFDF5; color: #059669;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-success"><?= $prodiPerlu ?></span>
                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">Prodi-prodi</span>
                    </div>
                    <div class="saas-stat-help">Total dari <?= count($prodisList) ?> program studi di bawah fakultas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Unit Kerja GPM: Dropdown Rapi, User-Friendly & Ringkas -->
    <div class="card shadow-2xs rounded-4 p-3 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 flex-grow-1" style="max-width: 580px;">
                <div class="rounded-3 bg-light text-primary d-flex align-items-center justify-content-center flex-shrink-0 shadow-2xs" style="width: 42px; height: 42px; border: 1px solid #E2E8F0; font-size: 1.15rem;">
                    <i class="fas fa-building-columns"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label for="filterUnitKerjaPerbaikan" class="form-label mb-0 fw-bold text-dark small d-flex align-items-center gap-1.5">
                            <span>Filter Unit Kerja:</span>
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem;">GPM Scope</span>
                        </label>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #CBD5E1;"><i class="fas fa-filter text-scu-gold"></i></span>
                        <select id="filterUnitKerjaPerbaikan" class="form-select border-start-0 fw-semibold" style="border-color: #CBD5E1; font-size: 0.84rem;" onchange="window.location.href = this.value">
                            <option value="<?= base_url('gpm/perbaikan?unit=all') ?>" <?= $unitFilter === 'all' ? 'selected' : '' ?>>
                                📁 Semua Unit Kerja (Fakultas & Seluruh Program Studi)
                            </option>
                            <option value="<?= base_url('gpm/perbaikan?unit=fakultas') ?>" <?= $unitFilter === 'fakultas' ? 'selected' : '' ?>>
                                🏢 Tingkat Fakultas (<?= htmlspecialchars($fakultas['nama_fakultas'] ?? 'Dekanat') ?>)
                            </option>
                            <optgroup label="── Program Studi di Bawah Fakultas ──">
                                <?php foreach ($prodisList as $p): ?>
                                    <option value="<?= base_url('gpm/perbaikan?unit=' . $p['id']) ?>" <?= ((string)$unitFilter === (string)$p['id']) ? 'selected' : '' ?>>
                                        🎓 <?= htmlspecialchars($p['jenjang'] . ' ' . $p['nama_prodi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Current Scope Status indicator -->
            <div class="small text-muted d-flex align-items-center gap-2.5 ps-lg-3 border-start-lg">
                <span class="badge rounded-circle p-2 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fas fa-info fa-sm"></i>
                </span>
                <div style="line-height: 1.35;">
                    <div class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Cakupan Dokumen:</div>
                    <div class="fw-bold" style="font-size: 0.85rem;">
                        <?php if ($unitFilter === 'all'): ?>
                            <span class="text-dark">Semua Unit (Fakultas & Seluruh Prodi)</span>
                        <?php elseif ($unitFilter === 'fakultas'): ?>
                            <span class="text-primary"><i class="fas fa-building-columns me-1"></i>Tingkat Fakultas</span>
                        <?php else: ?>
                            <?php 
                            $curProdi = array_values(array_filter($prodisList, fn($x) => (string)$x['id'] === (string)$unitFilter));
                            $curProdiName = !empty($curProdi) ? ($curProdi[0]['jenjang'] . ' ' . $curProdi[0]['nama_prodi']) : 'Prodi';
                            ?>
                            <span class="text-success"><i class="fas fa-graduation-cap me-1"></i><?= htmlspecialchars($curProdiName) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Tabs, Jumlah Tampilan & Live Search Box -->
    <div class="card shadow-2xs rounded-4 p-3 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="d-flex flex-column flex-xxl-row justify-content-between align-items-start align-items-xxl-center gap-3">
            <!-- Tabs Status Filter (Horizontal single-row pill group) -->
            <div class="nav nav-pills d-flex flex-nowrap align-items-center gap-2 overflow-x-auto w-100 w-xxl-auto pb-1 pb-xxl-0" id="perbaikanTabs" role="tablist">
                <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 text-nowrap" id="tab-all-btn" data-bs-toggle="pill" data-bs-target="#tab-all" type="button" role="tab" style="font-size: 0.84rem;">
                    <i class="fas fa-layer-group text-secondary"></i>
                    <span>Semua Status (<?= count($revisiDocs) ?>)</span>
                </button>
                <button class="nav-link rounded-pill active fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 text-nowrap" id="tab-perlu-btn" data-bs-toggle="pill" data-bs-target="#tab-perlu" type="button" role="tab" style="font-size: 0.84rem;">
                    <i class="fas fa-circle-exclamation text-danger"></i>
                    <span>Perlu Tindakan</span>
                    <span class="badge rounded-pill bg-danger text-white px-2 py-0.5" style="font-size: 0.7rem;"><?= count($perluDocs) ?></span>
                </button>
                <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 text-nowrap" id="tab-sudah-btn" data-bs-toggle="pill" data-bs-target="#tab-sudah" type="button" role="tab" style="font-size: 0.84rem;">
                    <i class="fas fa-clock-rotate-left text-primary"></i>
                    <span>Menunggu Review LPM</span>
                    <span class="badge rounded-pill bg-primary text-white px-2 py-0.5" style="font-size: 0.7rem;"><?= count($sudahDocs) ?></span>
                </button>
            </div>

            <!-- Controls: Per Page + Live Search -->
            <div class="d-flex align-items-center gap-2.5 w-100 w-xxl-auto justify-content-between justify-content-xxl-end flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                    <label for="perPagePerbaikan" class="small text-muted text-nowrap mb-0" style="font-size: 0.78rem;">Tampilkan:</label>
                    <select id="perPagePerbaikan" class="form-select form-select-sm form-select-perpage shadow-none" style="min-width: 105px; font-size: 0.8rem; border-color: #CBD5E1;">
                        <option value="10" selected>10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                        <option value="100">100 data</option>
                        <option value="all">Semua</option>
                    </select>
                </div>

                <!-- Instant Search Input -->
                <div class="input-group" style="min-width: 240px; max-width: 320px;">
                    <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                        <i class="fas fa-magnifying-glass"></i>
                    </span>
                    <input type="text" id="liveSearchPerbaikan" class="form-control bg-light border-start-0 rounded-end-pill py-2 small" placeholder="Cari nama/nomor dokumen..." style="font-size: 0.82rem;">
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Contents Container -->
    <div class="tab-content" id="perbaikanTabsContent">
        
        <!-- ========================================== -->
        <!-- TAB 1: PERLU PERBAIKAN (WAJIB TINDAKAN)   -->
        <!-- ========================================== -->
        <div class="tab-pane fade show active" id="tab-perlu" role="tabpanel">
            <?php if (empty($perluDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Luar Biasa! Tidak Ada Dokumen yang Perlu Revisi</h5>
                        <p class="text-muted small mx-auto mb-3" style="max-width: 480px; line-height: 1.5;">
                            Saat ini seluruh dokumen pada unit kerja yang dipilih telah sesuai dengan standar atau sedang dalam antrean verifikasi tim LPM.
                        </p>
                        <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 bg-scu-blue border-0 shadow-xs fw-semibold">
                            <i class="fas fa-folder-open me-1.5"></i> Buka Daftar Dokumen PPEPP
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #DC2626; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-wrench"></i> <strong><?= count($perluDocs) ?> Dokumen Wajib Direvisi</strong>
                            </span>
                            <span class="text-muted small">Terdapat catatan evaluasi dari LPM yang harus disesuaikan agar berkas disahkan</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 50px;">No</th>
                                    <th style="width: 140px;">Unit Sasaran</th>
                                    <th style="min-width: 250px; width: 28%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 120px;">Siklus PPEPP</th>
                                    <th style="min-width: 170px; width: 17%;">Bidang</th>
                                    <th style="min-width: 180px; width: 18%;">Evaluasi LPM</th>
                                    <th class="text-center" style="width: 130px;">Catatan LPM</th>
                                    <th class="text-center" style="width: 110px;">Lampiran</th>
                                    <th class="pe-4 text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($perluDocs as $idx => $doc): 
                                    $filesCount = count($doc['files'] ?? []);
                                    $hasLink = !empty($doc['external_link']);
                                    $noteExcerpt = trim($doc['catatan_review'] ?? 'Silakan sesuaikan isi dan lampiran dokumen mutu sesuai standar penjaminan mutu.');
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                ?>
                                    <tr class="perbaikan-row" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . ($doc['nama_prodi'] ?? 'fakultas') . ' ' . ($doc['reviewer_name'] ?? ''))) ?>">
                                        <td class="ps-4 text-center text-muted small fw-semibold" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <!-- Unit Sasaran -->
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($doc['level'] === 'fakultas'): ?>
                                                <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.74rem;">
                                                    <i class="fas fa-building-columns"></i> Fakultas
                                                </span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.74rem;">
                                                    <i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($doc['nama_prodi'] ?? '') ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Nama Dokumen Mutu -->
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.92rem; line-height: 1.4;">
                                                <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.74rem;">
                                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                            </div>
                                        </td>

                                        <!-- Siklus PPEPP -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

                                        <!-- Bidang Standar (Nama Bidang + Sub Standar) -->
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                                <?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?>
                                            </div>
                                            <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                                <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                    <i class="fas fa-angle-right text-secondary opacity-75"></i>
                                                    <span><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Evaluasi (Pusat Penjaminan Mutu LPM) -->
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-bold text-dark-blue d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                                                <i class="fas fa-building-columns text-primary"></i>
                                                <span>Pusat Penjaminan Mutu LPM</span>
                                            </div>
                                            <div class="text-muted small mt-1" style="font-size: 0.71rem;">
                                                <i class="far fa-clock text-secondary me-1"></i><?= !empty($doc['reviewed_at']) ? date('d M Y, H:i', strtotime($doc['reviewed_at'])) . ' WIB' : '-' ?>
                                            </div>
                                        </td>

                                        <!-- Catatan LPM -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                    style="font-size: 0.78rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCatatanEvaluator"
                                                    data-nama="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-unit="<?= htmlspecialchars($unitLabel) ?>"
                                                    data-reviewer="Pusat Penjaminan Mutu LPM"
                                                    data-tanggal="<?= !empty($doc['reviewed_at']) ? date('d M Y, H:i', strtotime($doc['reviewed_at'])) . ' WIB' : '-' ?>"
                                                    data-catatan="<?= htmlspecialchars($noteExcerpt) ?>"
                                                    data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '') ?>"
                                                    data-status="perlu_perbaikan"
                                                    data-edit-url="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>">
                                                <i class="fas fa-comment-dots text-danger"></i>
                                                <span>Lihat Catatan</span>
                                            </button>
                                        </td>

                                        <!-- Lampiran -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($filesCount > 0): ?>
                                                <a href="<?= base_url($doc['files'][0]['file_path']) ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.72rem;">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    <span><?= $filesCount ?> Berkas</span>
                                                </a>
                                            <?php elseif ($hasLink): ?>
                                                <a href="<?= htmlspecialchars(explode("\n", $doc['external_link'])[0]) ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs text-primary" style="font-size: 0.72rem;">
                                                    <i class="fab fa-google-drive text-success"></i>
                                                    <span>GDrive</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="pe-4 py-3 text-center">
                                            <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5 text-nowrap" style="font-size: 0.8rem;">
                                                <i class="fas fa-wrench"></i>
                                                <span>Perbaiki</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab 1 -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabPerlu" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabPerlu">Menghitung...</div>
                        <nav id="pagTabPerlu" aria-label="Navigasi Halaman"></nav>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: SUDAH DIPERBAIKI (ANTREAN LPM)      -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-sudah" role="tabpanel">
            <?php if (empty($sudahDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-inbox text-secondary"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Sedang Menunggu Verifikasi Ulang</h5>
                        <p class="text-muted small mx-auto mb-0" style="max-width: 480px; line-height: 1.5;">
                            Dokumen yang telah diperbaiki akan tampil di sini hingga Tim Lembaga Penjaminan Mutu (LPM) menyatakan statusnya "Sesuai Standar".
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #2563EB; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-clock-rotate-left"></i> <strong><?= count($sudahDocs) ?> Dokumen Dalam Antrean Review</strong>
                            </span>
                            <span class="text-muted small">Perbaikan telah dikirim dan sedang menunggu verifikasi ulang dari evaluator LPM</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 50px;">No</th>
                                    <th style="width: 140px;">Unit Sasaran</th>
                                    <th style="min-width: 250px; width: 28%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 120px;">Siklus PPEPP</th>
                                    <th style="min-width: 170px; width: 17%;">Bidang</th>
                                    <th style="min-width: 180px; width: 18%;">Status Review LPM</th>
                                    <th class="text-center" style="width: 130px;">Catatan LPM</th>
                                    <th class="text-center" style="width: 110px;">Lampiran</th>
                                    <th class="pe-4 text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sudahDocs as $idx => $doc): 
                                    $filesCount = count($doc['files'] ?? []);
                                    $hasLink = !empty($doc['external_link']);
                                    $noteExcerpt = trim($doc['catatan_review'] ?? 'Menunggu review ulang LPM.');
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                ?>
                                    <tr class="perbaikan-row" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . ($doc['nama_prodi'] ?? 'fakultas') . ' ' . ($doc['reviewer_name'] ?? ''))) ?>">
                                        <td class="ps-4 text-center text-muted small fw-semibold" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <!-- Unit Sasaran -->
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($doc['level'] === 'fakultas'): ?>
                                                <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.74rem;">
                                                    <i class="fas fa-building-columns"></i> Fakultas
                                                </span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.74rem;">
                                                    <i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($doc['nama_prodi'] ?? '') ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Nama Dokumen Mutu -->
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.92rem; line-height: 1.4;">
                                                <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.74rem;">
                                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                            </div>
                                        </td>

                                        <!-- Siklus PPEPP -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

                                        <!-- Bidang Standar (Nama Bidang + Sub Standar) -->
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                                <?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?>
                                            </div>
                                            <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                                <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                    <i class="fas fa-angle-right text-secondary opacity-75"></i>
                                                    <span><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Status Review LPM -->
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                <i class="fas fa-hourglass-half"></i> Dalam Antrean LPM
                                            </span>
                                            <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                                <i class="far fa-clock text-secondary me-1"></i><?= date('d M Y, H:i', strtotime($doc['updated_at'])) ?> WIB
                                            </div>
                                        </td>

                                        <!-- Catatan LPM -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                    style="font-size: 0.78rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCatatanEvaluator"
                                                    data-nama="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-unit="<?= htmlspecialchars($unitLabel) ?>"
                                                    data-reviewer="Pusat Penjaminan Mutu LPM"
                                                    data-tanggal="<?= !empty($doc['reviewed_at']) ? date('d M Y, H:i', strtotime($doc['reviewed_at'])) . ' WIB' : '-' ?>"
                                                    data-catatan="<?= htmlspecialchars($noteExcerpt) ?>"
                                                    data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '') ?>"
                                                    data-status="sudah_diperbaiki"
                                                    data-edit-url="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>">
                                                <i class="fas fa-comment-dots text-primary"></i>
                                                <span>Lihat Catatan</span>
                                            </button>
                                        </td>

                                        <!-- Lampiran -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($filesCount > 0): ?>
                                                <a href="<?= base_url($doc['files'][0]['file_path']) ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.72rem;">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    <span><?= $filesCount ?> Berkas</span>
                                                </a>
                                            <?php elseif ($hasLink): ?>
                                                <a href="<?= htmlspecialchars(explode("\n", $doc['external_link'])[0]) ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs text-primary" style="font-size: 0.72rem;">
                                                    <i class="fab fa-google-drive text-success"></i>
                                                    <span>GDrive</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="pe-4 py-3 text-center">
                                            <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 text-nowrap shadow-2xs" style="font-size: 0.8rem;">
                                                <i class="fas fa-pen-to-square"></i>
                                                <span>Ubah Berkas</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab 2 -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabSudah" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabSudah">Menghitung...</div>
                        <nav id="pagTabSudah" aria-label="Navigasi Halaman"></nav>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: SEMUA REKOR REVISI                  -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-all" role="tabpanel">
            <?php if (empty($revisiDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark mb-1">Belum Ada Rekam Revisi</h5>
                        <p class="text-muted small mb-0">Belum ada riwayat dokumen yang perlu atau telah diperbaiki pada filter ini.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #002B49; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-layer-group"></i> <strong>Total: <?= count($revisiDocs) ?> Dokumen</strong>
                            </span>
                            <span class="text-muted small">Seluruh dokumen yang pernah atau sedang dalam siklus tindak lanjut revisi</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 50px;">No</th>
                                    <th style="width: 140px;">Unit Sasaran</th>
                                    <th style="min-width: 250px; width: 28%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 150px;">Status Review LPM</th>
                                    <th class="text-center" style="width: 120px;">Siklus PPEPP</th>
                                    <th style="min-width: 170px; width: 17%;">Bidang</th>
                                    <th class="text-center" style="width: 130px;">Catatan LPM</th>
                                    <th class="pe-4 text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revisiDocs as $idx => $doc): 
                                    $isPerlu = ($doc['status_review'] === 'perlu_perbaikan');
                                    $noteExcerpt = trim($doc['catatan_review'] ?? ($isPerlu ? 'Silakan sesuaikan isi dokumen sesuai arahan.' : 'Menunggu review ulang LPM.'));
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                ?>
                                    <tr class="perbaikan-row" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . ($doc['nama_prodi'] ?? 'fakultas') . ' ' . ($doc['reviewer_name'] ?? ''))) ?>">
                                        <td class="ps-4 text-center text-muted small fw-semibold" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <!-- Unit Sasaran -->
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($doc['level'] === 'fakultas'): ?>
                                                <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.74rem;">
                                                    <i class="fas fa-building-columns"></i> Fakultas
                                                </span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.74rem;">
                                                    <i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($doc['nama_prodi'] ?? '') ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Nama Dokumen Mutu -->
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.92rem; line-height: 1.4;">
                                                <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.74rem;">
                                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                            </div>
                                        </td>

                                        <!-- Status Review LPM -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($isPerlu): ?>
                                                <span class="badge rounded-pill bg-danger text-white px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">
                                                    <i class="fas fa-wrench me-1"></i> Perlu Perbaikan
                                                </span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-primary text-white px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">
                                                    <i class="fas fa-clock-rotate-left me-1"></i> Menunggu Review LPM
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Siklus PPEPP -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

                                        <!-- Bidang Standar (Nama Bidang + Sub Standar) -->
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                                <?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?>
                                            </div>
                                            <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                                <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                    <i class="fas fa-angle-right text-secondary opacity-75"></i>
                                                    <span><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Catatan LPM -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                    style="font-size: 0.78rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCatatanEvaluator"
                                                    data-nama="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-unit="<?= htmlspecialchars($unitLabel) ?>"
                                                    data-reviewer="Pusat Penjaminan Mutu LPM"
                                                    data-tanggal="<?= !empty($doc['reviewed_at']) ? date('d M Y, H:i', strtotime($doc['reviewed_at'])) . ' WIB' : '-' ?>"
                                                    data-catatan="<?= htmlspecialchars($noteExcerpt) ?>"
                                                    data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '') ?>"
                                                    data-status="<?= $isPerlu ? 'perlu_perbaikan' : 'sudah_diperbaiki' ?>"
                                                    data-edit-url="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>">
                                                <i class="fas fa-comment-dots <?= $isPerlu ? 'text-danger' : 'text-primary' ?>"></i>
                                                <span>Lihat Catatan</span>
                                            </button>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="pe-4 py-3 text-center">
                                            <?php if ($isPerlu): ?>
                                                <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5 text-nowrap" style="font-size: 0.8rem;">
                                                    <i class="fas fa-wrench"></i>
                                                    <span>Perbaiki</span>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 text-nowrap shadow-2xs" style="font-size: 0.8rem;">
                                                    <i class="fas fa-pen-to-square"></i>
                                                    <span>Ubah Berkas</span>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab 3 -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabAll" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabAll">Menghitung...</div>
                        <nav id="pagTabAll" aria-label="Navigasi Halaman"></nav>
                    </div>
                </div>
            <?php endif; ?>
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
                <!-- Info Sasaran Dokumen -->
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <div class="small text-muted mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                        Sasaran Unit & Dokumen Mutu:
                    </div>
                    <div class="badge bg-light text-secondary border mb-1 px-2.5 py-1" id="evalModalUnit" style="font-size: 0.74rem;">-</div>
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

<script>
let currentPerbaikanPages = {
    'tab-perlu': 1,
    'tab-sudah': 1,
    'tab-all': 1
};

function paginateTab(tabId, page) {
    const tabEl = document.getElementById(tabId);
    if (!tabEl) return;

    if (page !== undefined) {
        currentPerbaikanPages[tabId] = page;
    }
    const curPage = currentPerbaikanPages[tabId] || 1;

    const perPageSelect = document.getElementById('perPagePerbaikan');
    const perPageVal = perPageSelect ? perPageSelect.value : '10';
    const perPage = (perPageVal === 'all') ? 999999 : parseInt(perPageVal, 10);

    const searchInput = document.getElementById('liveSearchPerbaikan');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

    const allRows = Array.from(tabEl.querySelectorAll('tbody tr.perbaikan-row'));
    if (allRows.length === 0) return;

    // Filter by search query
    const filteredRows = allRows.filter(row => {
        const text = (row.getAttribute('data-search') || '').toLowerCase();
        return !query || text.includes(query);
    });

    const totalVisible = filteredRows.length;
    const totalPages = Math.ceil(totalVisible / perPage) || 1;
    if (currentPerbaikanPages[tabId] > totalPages) {
        currentPerbaikanPages[tabId] = totalPages;
    }
    const finalPage = currentPerbaikanPages[tabId] || 1;

    const startIdx = (finalPage - 1) * perPage;
    const endIdx = Math.min(startIdx + perPage, totalVisible);

    // Hide all rows first
    allRows.forEach(r => r.style.display = 'none');

    // Show paginated slice and update numbering
    filteredRows.forEach((row, idx) => {
        if (idx >= startIdx && idx < endIdx) {
            row.style.display = '';
            const noCol = row.querySelector('td:first-child');
            if (noCol) noCol.textContent = idx + 1;
        } else {
            row.style.display = 'none';
        }
    });

    // Determine target pagination element IDs
    let infoId = 'infoTabPerlu';
    let pagId = 'pagTabPerlu';
    if (tabId === 'tab-sudah') {
        infoId = 'infoTabSudah';
        pagId = 'pagTabSudah';
    } else if (tabId === 'tab-all') {
        infoId = 'infoTabAll';
        pagId = 'pagTabAll';
    }

    const infoEl = document.getElementById(infoId);
    const pagEl = document.getElementById(pagId);

    if (infoEl) {
        if (totalVisible === 0) {
            infoEl.textContent = 'Menampilkan 0 dari 0 dokumen';
        } else {
            const startNum = startIdx + 1;
            const endNum = endIdx;
            infoEl.innerHTML = `Menampilkan <strong>${startNum} - ${endNum}</strong> dari total <strong>${totalVisible}</strong> dokumen`;
        }
    }

    if (!pagEl) return;
    if (totalPages <= 1) {
        pagEl.innerHTML = '';
        return;
    }

    let html = '<ul class="pagination pagination-sm mb-0">';
    html += `<li class="page-item ${finalPage === 1 ? 'disabled' : ''}">
        <button type="button" class="page-link rounded-start-pill" onclick="paginateTab('${tabId}', ${finalPage - 1})" aria-label="Sebelumnya">&laquo;</button>
    </li>`;

    for (let p = 1; p <= totalPages; p++) {
        if (totalPages > 7) {
            if (p !== 1 && p !== totalPages && Math.abs(p - finalPage) > 1) {
                if (p === 2 || p === totalPages - 1) {
                    html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                }
                continue;
            }
        }
        html += `<li class="page-item ${p === finalPage ? 'active' : ''}">
            <button type="button" class="page-link" onclick="paginateTab('${tabId}', ${p})">${p}</button>
        </li>`;
    }

    html += `<li class="page-item ${finalPage === totalPages ? 'disabled' : ''}">
        <button type="button" class="page-link rounded-end-pill" onclick="paginateTab('${tabId}', ${finalPage + 1})" aria-label="Selanjutnya">&raquo;</button>
    </li>`;
    html += '</ul>';
    pagEl.innerHTML = html;
}

function refreshAllPerbaikanTabs() {
    ['tab-perlu', 'tab-sudah', 'tab-all'].forEach(tabId => {
        paginateTab(tabId);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initial pagination setup
    refreshAllPerbaikanTabs();

    // Per Page Select Handler
    const perPageSelect = document.getElementById('perPagePerbaikan');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            currentPerbaikanPages = { 'tab-perlu': 1, 'tab-sudah': 1, 'tab-all': 1 };
            refreshAllPerbaikanTabs();
        });
    }

    // Live Search Handler
    const searchInput = document.getElementById('liveSearchPerbaikan');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentPerbaikanPages = { 'tab-perlu': 1, 'tab-sudah': 1, 'tab-all': 1 };
            refreshAllPerbaikanTabs();
        });
    }

    // Tab Change Handler
    const tabButtons = document.querySelectorAll('#perbaikanTabs button[data-bs-toggle="pill"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            const targetTab = (e.target.getAttribute('data-bs-target') || '').replace('#', '');
            if (targetTab) {
                paginateTab(targetTab);
            }
        });
    });

    // Modal Catatan Evaluator Handler
    const modalEl = document.getElementById('modalCatatanEvaluator');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const nama = btn.getAttribute('data-nama') || '-';
            const unit = btn.getAttribute('data-unit') || '-';
            const reviewer = btn.getAttribute('data-reviewer') || 'Pusat Penjaminan Mutu LPM';
            const tanggal = btn.getAttribute('data-tanggal') || '-';
            const catatan = btn.getAttribute('data-catatan') || 'Tidak ada catatan tertulis.';
            const editUrl = btn.getAttribute('data-edit-url') || '#';
            const status = btn.getAttribute('data-status') || 'perlu_perbaikan';

            const titleEl = document.getElementById('evalModalDocTitle');
            const unitEl = document.getElementById('evalModalUnit');
            const revEl = document.getElementById('evalModalReviewer');
            const tglEl = document.getElementById('evalModalTanggal');
            const catEl = document.getElementById('evalModalCatatan');
            const editBtn = document.getElementById('evalModalEditBtn');
            const iconEl = document.getElementById('evalModalIcon');
            const quoteHeading = document.getElementById('evalModalQuoteHeading');
            const shieldIcon = document.getElementById('evalModalRevShield');

            if (titleEl) titleEl.textContent = nama;
            if (unitEl) unitEl.textContent = unit;
            if (revEl) revEl.textContent = reviewer;
            if (tglEl) tglEl.textContent = tanggal;
            if (catEl) catEl.textContent = catatan;
            
            if (editBtn) {
                editBtn.href = editUrl;
                if (status === 'sudah_diperbaiki') {
                    editBtn.className = 'btn btn-outline-primary btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2';
                    editBtn.innerHTML = '<i class="fas fa-pen-to-square me-1"></i> <span>Ubah / Lengkapi Berkas</span>';
                    if (iconEl) iconEl.style.background = '#2563EB';
                    if (catEl) {
                        catEl.style.background = '#EFF6FF';
                        catEl.style.borderColor = '#BFDBFE';
                        catEl.style.borderLeftColor = '#2563EB';
                    }
                    if (quoteHeading) {
                        quoteHeading.className = 'text-primary fw-bold small text-uppercase mb-1.5 d-flex align-items-center gap-1.5';
                    }
                    if (shieldIcon) {
                        shieldIcon.className = 'fas fa-user-shield text-primary';
                    }
                } else {
                    editBtn.className = 'btn btn-danger btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2';
                    editBtn.innerHTML = '<i class="fas fa-wrench me-1"></i> <span>Perbaiki Dokumen Sekarang</span>';
                    if (iconEl) iconEl.style.background = '#DC2626';
                    if (catEl) {
                        catEl.style.background = '#FFF1F2';
                        catEl.style.borderColor = '#FECDD3';
                        catEl.style.borderLeftColor = '#DC2626';
                    }
                    if (quoteHeading) {
                        quoteHeading.className = 'text-danger fw-bold small text-uppercase mb-1.5 d-flex align-items-center gap-1.5';
                    }
                    if (shieldIcon) {
                        shieldIcon.className = 'fas fa-user-shield text-danger';
                    }
                }
            }
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
