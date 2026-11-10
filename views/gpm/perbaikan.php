<?php
/**
 * GPM (Gugus Penjaminan Mutu): Ruang Kerja Perbaikan Dokumen Mutu
 * Mengelola revisi dokumen dari LPM untuk tingkat Fakultas dan seluruh Program Studi
 * Alur Berjenjang Multi-Tier: LPM -> GPM Fakultas -> Prodi / Dekanat
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$revisiDocs     = $revisiDocs ?? [];
$lpmRevisiDocs  = $lpmRevisiDocs ?? array_values(array_filter($revisiDocs, fn($d) => in_array($d['status_review'] ?? '', ['revisi_lpm', 'perlu_perbaikan'])));
$unitRevisiDocs = $unitRevisiDocs ?? array_values(array_filter($revisiDocs, fn($d) => ($d['status_review'] ?? '') === 'revisi_gpm'));
$sudahDocs      = $sudahDocs ?? array_values(array_filter($revisiDocs, fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));
$perluDocs      = $perluDocs ?? array_values(array_filter($revisiDocs, fn($d) => in_array($d['status_review'] ?? '', ['revisi_lpm', 'revisi_gpm', 'perlu_perbaikan'])));

$fakultas      = $fakultas ?? [];
$prodisList    = $prodisList ?? [];
$unitFilter    = $unitFilter ?? 'all';
$totalPerlu    = $totalPerlu ?? count($perluDocs);
$totalRevisiGpm = count($unitRevisiDocs);
$totalRevisiLpm = count($lpmRevisiDocs);
$totalSudah    = count($sudahDocs);
$fakultasPerlu = $fakultasPerlu ?? 0;
$prodiPerlu    = $prodiPerlu ?? 0;

// Tab aktif default: Jika ada catatan baru dari LPM, prioritaskan tab LPM
$activeTab = ($totalRevisiLpm > 0) ? 'tab-lpm' : (($totalRevisiGpm > 0) ? 'tab-unit' : (($totalSudah > 0) ? 'tab-sudah' : 'tab-all'));
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
                    <i class="fas fa-clipboard-check"></i> Monitoring Revisi Dokumen Berjenjang
                </span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">Monitoring Revisi Dokumen Mutu</h3>
            <p class="text-muted small mb-0" style="line-height: 1.5;">
                Kelola tindak lanjut catatan evaluasi mutu berjenjang untuk <strong>Fakultas</strong> dan seluruh <strong>Program Studi</strong> di bawah <strong><?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?></strong>. Catatan evaluasi dari LPM ditelaah oleh GPM sebelum dirumuskan resmi ke unit sasaran.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-clipboard-check"></i> Ruang Review Dokumen
            </a>
            <a href="<?= base_url('gpm/dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Metric KPI Cards (4 Kolom: Catatan Baru LPM, Sedang Dikerjakan Unit, Siap Telaah Ulang, Total Siklus Revisi) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #EA580C !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-warning" style="color: #EA580C !important;">Catatan Baru LPM</span>
                    <div class="saas-stat-icon-wrap" style="background: #FFEDD5; color: #EA580C;">
                        <i class="fas fa-reply <?= $totalRevisiLpm > 0 ? 'fa-beat-fade' : '' ?>"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number" style="color: #EA580C;"><?= $totalRevisiLpm ?></span>
                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem; color: #C2410C !important;">Perlu Diteruskan</span>
                    </div>
                    <div class="saas-stat-help">Catatan evaluasi LPM yang perlu diteruskan GPM ke unit</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #DC2626 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-danger">Sedang Dikerjakan Unit</span>
                    <div class="saas-stat-icon-wrap" style="background: #FEE2E2; color: #DC2626;">
                        <i class="fas fa-wrench"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-danger"><?= $totalRevisiGpm ?></span>
                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">Dalam Pengerjaan</span>
                    </div>
                    <div class="saas-stat-help">Telah diteruskan ke unit &amp; menunggu upload revisi</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #2563EB !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-primary">Telah Diperbaiki Unit</span>
                    <div class="saas-stat-icon-wrap" style="background: #EFF6FF; color: #2563EB;">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-primary"><?= $totalSudah ?></span>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">Siap Telaah Ulang</span>
                    </div>
                    <div class="saas-stat-help">Telah diperbaiki unit &amp; siap diajukan kembali ke LPM</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #059669 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-success">Total Riwayat Revisi</span>
                    <div class="saas-stat-icon-wrap" style="background: #ECFDF5; color: #059669;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-success"><?= count($revisiDocs) ?></span>
                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">Total Rekam</span>
                    </div>
                    <div class="saas-stat-help">Dari <?= count($prodisList) ?> prodi &amp; 1 tingkat dekanat</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Unit Kerja GPM -->
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
                                Semua Unit Kerja (Fakultas & Seluruh Program Studi)
                            </option>
                            <option value="<?= base_url('gpm/perbaikan?unit=fakultas') ?>" <?= $unitFilter === 'fakultas' ? 'selected' : '' ?>>
                                Tingkat Fakultas (<?= htmlspecialchars($fakultas['nama_fakultas'] ?? 'Dekanat') ?>)
                            </option>
                            <optgroup label="── Program Studi di Bawah Fakultas ──">
                                <?php foreach ($prodisList as $p): ?>
                                    <option value="<?= base_url('gpm/perbaikan?unit=' . $p['id']) ?>" <?= ((string)$unitFilter === (string)$p['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['jenjang'] . ' ' . $p['nama_prodi']) ?>
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
    <div class="card shadow-2xs rounded-4 p-3.5 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="d-flex flex-column gap-3">
            <!-- Baris 1: Status Tabs Filter Berjenjang -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-2.5 border-bottom" style="border-color: #F1F5F9 !important;">
                <div class="nav nav-pills d-flex flex-wrap align-items-center gap-2" id="perbaikanTabs" role="tablist">
                    <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 <?= $activeTab === 'tab-lpm' ? 'active' : '' ?>" id="tab-lpm-btn" data-bs-toggle="pill" data-bs-target="#tab-lpm" type="button" role="tab" style="font-size: 0.84rem;">
                        <i class="fas fa-reply text-warning"></i>
                        <span>Catatan dari LPM (Perlu Diteruskan)</span>
                        <span class="badge rounded-pill bg-warning text-dark px-2 py-0.5 fw-bold" style="font-size: 0.7rem;"><?= count($lpmRevisiDocs) ?></span>
                    </button>
                    <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 <?= $activeTab === 'tab-unit' ? 'active' : '' ?>" id="tab-unit-btn" data-bs-toggle="pill" data-bs-target="#tab-unit" type="button" role="tab" style="font-size: 0.84rem;">
                        <i class="fas fa-wrench text-danger"></i>
                        <span>Sedang Diperbaiki Unit</span>
                        <span class="badge rounded-pill bg-danger text-white px-2 py-0.5" style="font-size: 0.7rem;"><?= count($unitRevisiDocs) ?></span>
                    </button>
                    <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 <?= $activeTab === 'tab-sudah' ? 'active' : '' ?>" id="tab-sudah-btn" data-bs-toggle="pill" data-bs-target="#tab-sudah" type="button" role="tab" style="font-size: 0.84rem;">
                        <i class="fas fa-clock-rotate-left text-primary"></i>
                        <span>Hasil Perbaikan (Siap Telaah GPM)</span>
                        <span class="badge rounded-pill bg-primary text-white px-2 py-0.5" style="font-size: 0.7rem;"><?= count($sudahDocs) ?></span>
                    </button>
                    <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 <?= $activeTab === 'tab-all' ? 'active' : '' ?>" id="tab-all-btn" data-bs-toggle="pill" data-bs-target="#tab-all" type="button" role="tab" style="font-size: 0.84rem;">
                        <i class="fas fa-layer-group text-secondary"></i>
                        <span>Semua Status (<?= count($revisiDocs) ?>)</span>
                    </button>
                </div>
            </div>

            <!-- Controls Toolbar: Pencarian & Pengaturan Data -->
            <div class="row g-2.5 mb-1 align-items-center">
                <div class="col">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted px-3">
                            <i class="fas fa-magnifying-glass"></i>
                        </span>
                        <input type="text" id="liveSearchPerbaikan" class="form-control bg-light border-start-0 py-2 small ps-1" placeholder="Cari nama dokumen, nomor, atau bidang standar..." style="font-size: 0.82rem;">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <select id="filterTahunPerbaikanGpm" class="form-select form-select-sm shadow-none w-100" style="font-size: 0.82rem; border-color: #CBD5E1;" onchange="filterPerbaikanList()">
                        <option value="">Semua Tahun Akademik</option>
                        <?php foreach (get_distinct_tahun_akademik() as $ta): ?>
                            <option value="<?= htmlspecialchars($ta) ?>">Tahun Akademik <?= htmlspecialchars($ta) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto d-flex align-items-center gap-1.5">
                    <label for="perPagePerbaikan" class="small text-muted text-nowrap mb-0 d-none d-sm-inline" style="font-size: 0.78rem;">Tampilkan:</label>
                    <select id="perPagePerbaikan" class="form-select form-select-sm form-select-perpage shadow-none" style="min-width: 95px; width: auto; font-size: 0.8rem; border-color: #CBD5E1;">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50" selected>50 data</option>
                        <option value="100">100 data</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Contents Container -->
    <div class="tab-content" id="perbaikanTabsContent">
        
        <!-- ======================================================= -->
        <!-- TAB 1: CATATAN EVALUASI DARI LPM (PERLU DITERUSKAN)     -->
        <!-- ======================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'tab-lpm' ? 'show active' : '' ?>" id="tab-lpm" role="tabpanel">
            <?php if (empty($lpmRevisiDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Catatan Evaluasi dari LPM yang Perlu Diteruskan</h5>
                        <p class="text-muted small mx-auto mb-3" style="max-width: 480px; line-height: 1.5;">
                            Saat ini tidak ada dokumen yang dikembalikan oleh LPM untuk dirumuskan ke unit pengunggah.
                        </p>
                        <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 bg-scu-blue border-0 shadow-xs fw-semibold">
                            <i class="fas fa-folder-open me-1.5"></i> Buka Antrean Dokumen PPEPP
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #FFFBEB; border-color: #FDE68A !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #EA580C; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-reply"></i> <strong><?= count($lpmRevisiDocs) ?> Dokumen Memerlukan Tindakan GPM</strong>
                            </span>
                            <span class="text-dark small">Tinjau catatan evaluasi internal LPM berikut, lalu teruskan arahan resmi atas nama GPM ke Prodi/Dekanat.</span>
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
                                    <th class="text-center" style="width: 150px;">Catatan LPM</th>
                                    <th class="text-center" style="width: 110px;">Lampiran</th>
                                    <th class="pe-4 text-center" style="width: 160px;">Aksi GPM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lpmRevisiDocs as $idx => $doc): 
                                    $filesCount = count($doc['files'] ?? []);
                                    $hasLink = !empty($doc['external_link']);
                                    $lpmNote = trim($doc['lpm_catatan'] ?: ($doc['catatan_review'] ?? ''));
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                    $lpmDate = !empty($doc['lpm_reviewed_at']) ? date('d M Y, H:i', strtotime($doc['lpm_reviewed_at'])) . ' WIB' : (!empty($doc['reviewed_at']) ? date('d M Y, H:i', strtotime($doc['reviewed_at'])) . ' WIB' : '-');
                                ?>
                                    <tr class="perbaikan-row" data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . $unitLabel)) ?>">
                                        <td class="ps-4 text-center text-muted fw-bold small" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <!-- Unit Sasaran -->
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold text-truncate d-inline-block shadow-2xs" 
                                                  style="max-width: 130px; font-size: 0.73rem; <?= ($doc['level'] === 'fakultas') ? 'background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' ?>">
                                                <i class="fas <?= ($doc['level'] === 'fakultas') ? 'fa-building-columns' : 'fa-graduation-cap' ?> me-1"></i>
                                                <?= htmlspecialchars($unitLabel) ?>
                                            </span>
                                        </td>

                                        <!-- Nama Dokumen Mutu -->
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="line-height: 1.4; font-size: 0.88rem;">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="text-decoration-none text-dark-blue hover-primary">
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

                                        <!-- Bidang Standar -->
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
                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                    style="font-size: 0.78rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCatatanEvaluator"
                                                    data-nama="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-unit="<?= htmlspecialchars($unitLabel) ?>"
                                                    data-reviewer="Pusat Penjaminan Mutu LPM (Internal GPM)"
                                                    data-tanggal="<?= $lpmDate ?>"
                                                    data-catatan="<?= htmlspecialchars($lpmNote) ?>"
                                                    data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '') ?>"
                                                    data-status="revisi_lpm"
                                                    data-is-gpm="0"
                                                    data-edit-url="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>">
                                                <i class="fas fa-lock text-warning"></i>
                                                <span>Catatan LPM</span>
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
                                            <div class="d-inline-flex align-items-center gap-1.5">
                                                <button type="button" class="btn btn-sm btn-warning bg-scu-gold border-0 text-white rounded-pill px-3 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5 btn-open-forward"
                                                        style="font-size: 0.78rem;"
                                                        data-doc-id="<?= $doc['id'] ?>"
                                                        data-doc-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                        data-unit-name="<?= htmlspecialchars($unitLabel, ENT_QUOTES) ?>"
                                                        data-lpm-note="<?= htmlspecialchars($lpmNote, ENT_QUOTES) ?>"
                                                        data-gpm-note="<?= htmlspecialchars($doc['gpm_catatan'] ?? '', ENT_QUOTES) ?>">
                                                    <i class="fas fa-paper-plane"></i> Teruskan ke Unit
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-review-history d-inline-flex align-items-center gap-1 shadow-2xs" data-doc-id="<?= $doc['id'] ?>" title="Riwayat Review" style="font-size: 0.72rem;">
                                                    <i class="fas fa-history text-secondary"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab LPM -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabLpm" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabLpm">Menghitung...</div>
                        <div class="table-pagination-control">
                            <div class="table-perpage-wrap">
                                <label class="table-perpage-label">Ukuran Halaman:</label>
                                <select class="form-select table-perpage-select shadow-none" onchange="changePerbaikanPageSize(this.value)">
                                    <option value="10">10 data</option>
                                    <option value="25">25 data</option>
                                    <option value="50" selected>50 data</option>
                                    <option value="100">100 data</option>
                                </select>
                            </div>
                            <nav id="pagTabLpm" aria-label="Navigasi Halaman"></nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 2: SEDANG DIPERBAIKI OLEH UNIT (REVISI GPM)         -->
        <!-- ======================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'tab-unit' ? 'show active' : '' ?>" id="tab-unit" role="tabpanel">
            <?php if (empty($unitRevisiDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Sedang Dikerjakan Unit</h5>
                        <p class="text-muted small mx-auto mb-0" style="max-width: 480px; line-height: 1.5;">
                            Saat ini seluruh dokumen yang diteruskan telah diselesaikan atau belum ada arahan perbaikan aktif ke unit.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #FEF2F2; border-color: #FECACA !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #DC2626; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-wrench"></i> <strong><?= count($unitRevisiDocs) ?> Dokumen Dalam Pengerjaan Unit</strong>
                            </span>
                            <span class="text-muted small">Unit telah menerima arahan perbaikan GPM dan sedang menyusun berkas revisi baru</span>
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
                                    <th class="text-center" style="width: 150px;">Arahan GPM</th>
                                    <th class="text-center" style="width: 110px;">Status Pengerjaan</th>
                                    <th class="pe-4 text-center" style="width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unitRevisiDocs as $idx => $doc): 
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                    $gpmDate = !empty($doc['gpm_reviewed_at']) ? date('d M Y, H:i', strtotime($doc['gpm_reviewed_at'])) . ' WIB' : (!empty($doc['reviewed_at']) ? date('d M Y, H:i', strtotime($doc['reviewed_at'])) . ' WIB' : '-');
                                    $gpmNote = trim($doc['gpm_catatan'] ?: ($doc['catatan_review'] ?? 'Silakan sesuaikan isi dan lampiran dokumen mutu sesuai standar.'));
                                ?>
                                    <tr class="perbaikan-row" data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . $unitLabel)) ?>">
                                        <td class="ps-4 text-center text-muted fw-bold small" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold text-truncate d-inline-block shadow-2xs" 
                                                  style="max-width: 130px; font-size: 0.73rem; <?= ($doc['level'] === 'fakultas') ? 'background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' ?>">
                                                <i class="fas <?= ($doc['level'] === 'fakultas') ? 'fa-building-columns' : 'fa-graduation-cap' ?> me-1"></i>
                                                <?= htmlspecialchars($unitLabel) ?>
                                            </span>
                                        </td>

                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="line-height: 1.4; font-size: 0.88rem;">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="text-decoration-none text-dark-blue hover-primary">
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

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

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

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                    style="font-size: 0.78rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCatatanEvaluator"
                                                    data-nama="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-unit="<?= htmlspecialchars($unitLabel) ?>"
                                                    data-reviewer="Gugus Penjaminan Mutu (GPM) Fakultas"
                                                    data-tanggal="<?= $gpmDate ?>"
                                                    data-catatan="<?= htmlspecialchars($gpmNote) ?>"
                                                    data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '') ?>"
                                                    data-status="revisi_gpm"
                                                    data-is-gpm="1"
                                                    data-edit-url="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>">
                                                <i class="fas fa-comment-dots text-danger"></i>
                                                <span>Arahan GPM</span>
                                            </button>
                                        </td>

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill text-dark border px-2.5 py-1" style="background: #FEE2E2; color: #991B1B; border-color: #FCA5A5 !important; font-size: 0.72rem;">
                                                <i class="fas fa-clock text-danger me-1"></i> Menunggu Unit
                                            </span>
                                        </td>

                                        <td class="pe-4 py-3 text-center">
                                            <div class="d-inline-flex align-items-center gap-1.5">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3 py-1 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                                    <i class="fas fa-clipboard-check"></i> Review
                                                </a>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-review-history d-inline-flex align-items-center gap-1 shadow-2xs" data-doc-id="<?= $doc['id'] ?>" title="Riwayat Review" style="font-size: 0.72rem;">
                                                    <i class="fas fa-history text-secondary"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab Unit -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabUnit" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabUnit">Menghitung...</div>
                        <div class="table-pagination-control">
                            <div class="table-perpage-wrap">
                                <label class="table-perpage-label">Ukuran Halaman:</label>
                                <select class="form-select table-perpage-select shadow-none" onchange="changePerbaikanPageSize(this.value)">
                                    <option value="10">10 data</option>
                                    <option value="25">25 data</option>
                                    <option value="50" selected>50 data</option>
                                    <option value="100">100 data</option>
                                </select>
                            </div>
                            <nav id="pagTabUnit" aria-label="Navigasi Halaman"></nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 3: HASIL PERBAIKAN UNIT (SIAP TELAAH ULANG GPM)     -->
        <!-- ======================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'tab-sudah' ? 'show active' : '' ?>" id="tab-sudah" role="tabpanel">
            <?php if (empty($sudahDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-inbox text-secondary"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Menunggu Telaah Ulang</h5>
                        <p class="text-muted small mx-auto mb-0" style="max-width: 480px; line-height: 1.5;">
                            Dokumen yang telah diperbaiki oleh unit kerja akan muncul di sini untuk diperiksa ulang GPM sebelum diteruskan ke LPM.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #EFF6FF; border-color: #BFDBFE !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #2563EB; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-clock-rotate-left"></i> <strong><?= count($sudahDocs) ?> Dokumen Siap Telaah Ulang GPM</strong>
                            </span>
                            <span class="text-dark small">Periksa hasil perbaikan unit sasaran. Jika telah memenuhi standar, teruskan kembali ke LPM.</span>
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
                                    <th class="text-center" style="width: 140px;">Catatan Arahan</th>
                                    <th class="text-center" style="width: 110px;">Lampiran Baru</th>
                                    <th class="pe-4 text-center" style="width: 140px;">Aksi Telaah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sudahDocs as $idx => $doc): 
                                    $filesCount = count($doc['files'] ?? []);
                                    $hasLink = !empty($doc['external_link']);
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                    $sRevDate = !empty($doc['gpm_reviewed_at']) ? date('d M Y, H:i', strtotime($doc['gpm_reviewed_at'])) . ' WIB' : (!empty($doc['reviewed_at']) ? date('d M Y, H:i', strtotime($doc['reviewed_at'])) . ' WIB' : '-');
                                    $sNote = trim($doc['gpm_catatan'] ?: ($doc['catatan_review'] ?? 'Silakan sesuaikan dokumen mutu sesuai standar penjaminan mutu.'));
                                ?>
                                    <tr class="perbaikan-row" data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . $unitLabel)) ?>">
                                        <td class="ps-4 text-center text-muted fw-bold small" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold text-truncate d-inline-block shadow-2xs" 
                                                  style="max-width: 130px; font-size: 0.73rem; <?= ($doc['level'] === 'fakultas') ? 'background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' ?>">
                                                <i class="fas <?= ($doc['level'] === 'fakultas') ? 'fa-building-columns' : 'fa-graduation-cap' ?> me-1"></i>
                                                <?= htmlspecialchars($unitLabel) ?>
                                            </span>
                                        </td>

                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="line-height: 1.4; font-size: 0.88rem;">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="text-decoration-none text-dark-blue hover-primary">
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

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

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

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                    style="font-size: 0.78rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCatatanEvaluator"
                                                    data-nama="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-unit="<?= htmlspecialchars($unitLabel) ?>"
                                                    data-reviewer="Gugus Penjaminan Mutu (GPM) Fakultas"
                                                    data-tanggal="<?= $sRevDate ?>"
                                                    data-catatan="<?= htmlspecialchars($sNote) ?>"
                                                    data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '') ?>"
                                                    data-status="sudah_diperbaiki"
                                                    data-is-gpm="1"
                                                    data-edit-url="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>">
                                                <i class="fas fa-comment-dots text-primary"></i>
                                                <span>Arahan GPM</span>
                                            </button>
                                        </td>

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

                                        <td class="pe-4 py-3 text-center">
                                            <div class="d-inline-flex align-items-center gap-1.5">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3 py-1 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1 text-white" style="font-size: 0.78rem;">
                                                    <i class="fas fa-clipboard-check"></i> Telaah &amp; Putuskan
                                                </a>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-review-history d-inline-flex align-items-center gap-1 shadow-2xs" data-doc-id="<?= $doc['id'] ?>" title="Riwayat Review" style="font-size: 0.72rem;">
                                                    <i class="fas fa-history text-secondary"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab Sudah -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabSudah" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabSudah">Menghitung...</div>
                        <div class="table-pagination-control">
                            <div class="table-perpage-wrap">
                                <label class="table-perpage-label">Ukuran Halaman:</label>
                                <select class="form-select table-perpage-select shadow-none" onchange="changePerbaikanPageSize(this.value)">
                                    <option value="10">10 data</option>
                                    <option value="25">25 data</option>
                                    <option value="50" selected>50 data</option>
                                    <option value="100">100 data</option>
                                </select>
                            </div>
                            <nav id="pagTabSudah" aria-label="Navigasi Halaman"></nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 4: SEMUA STATUS REVISI                             -->
        <!-- ======================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'tab-all' ? 'show active' : '' ?>" id="tab-all" role="tabpanel">
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
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #002B49; color: #FFFFFF;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs bg-white text-dark-blue" style="font-size: 0.8rem;">
                                <i class="fas fa-layer-group"></i> <strong>Total: <?= count($revisiDocs) ?> Dokumen</strong>
                            </span>
                            <span class="small text-white text-opacity-75">Seluruh dokumen yang berada dalam siklus tindak lanjut revisi</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 50px;">No</th>
                                    <th style="width: 140px;">Unit Sasaran</th>
                                    <th style="min-width: 250px; width: 28%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 160px;">Status Alur Revisi</th>
                                    <th class="text-center" style="width: 120px;">Siklus PPEPP</th>
                                    <th style="min-width: 170px; width: 17%;">Bidang</th>
                                    <th class="pe-4 text-center" style="width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revisiDocs as $idx => $doc): 
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                ?>
                                    <tr class="perbaikan-row" data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . $unitLabel)) ?>">
                                        <td class="ps-4 text-center text-muted fw-bold small" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold text-truncate d-inline-block shadow-2xs" 
                                                  style="max-width: 130px; font-size: 0.73rem; <?= ($doc['level'] === 'fakultas') ? 'background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' ?>">
                                                <i class="fas <?= ($doc['level'] === 'fakultas') ? 'fa-building-columns' : 'fa-graduation-cap' ?> me-1"></i>
                                                <?= htmlspecialchars($unitLabel) ?>
                                            </span>
                                        </td>

                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="line-height: 1.4; font-size: 0.88rem;">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="text-decoration-none text-dark-blue hover-primary">
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

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= render_review_status_badge($doc['status_review'], '', 'gpm') ?>
                                        </td>

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

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

                                        <td class="pe-4 py-3 text-center">
                                            <div class="d-inline-flex align-items-center gap-1.5">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3 py-1 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1 text-white" style="font-size: 0.78rem;">
                                                    <i class="fas fa-clipboard-check"></i> Review
                                                </a>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-review-history d-inline-flex align-items-center gap-1 shadow-2xs" data-doc-id="<?= $doc['id'] ?>" title="Riwayat Review" style="font-size: 0.72rem;">
                                                    <i class="fas fa-history text-secondary"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab All -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabAll" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabAll">Menghitung...</div>
                        <div class="table-pagination-control">
                            <div class="table-perpage-wrap">
                                <label class="table-perpage-label">Ukuran Halaman:</label>
                                <select class="form-select table-perpage-select shadow-none" onchange="changePerbaikanPageSize(this.value)">
                                    <option value="10">10 data</option>
                                    <option value="25">25 data</option>
                                    <option value="50" selected>50 data</option>
                                    <option value="100">100 data</option>
                                </select>
                            </div>
                            <nav id="pagTabAll" aria-label="Navigasi Halaman"></nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 1: FORWARD ARAHAN REVISI GPM KE UNIT (MASKING LPM)-->
<!-- ======================================================= -->
<div class="modal fade" id="modalForwardLpmRevision" tabindex="-1" aria-labelledby="forwardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3.5 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" style="width: 38px; height: 38px; font-size: 1rem; background: #EA580C;">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0.5" id="forwardModalLabel" style="font-size: 1.1rem; letter-spacing: -0.2px;">
                            Teruskan Catatan Revisi ke Unit Sasaran
                        </h5>
                        <p class="text-white text-opacity-75 small mb-0" style="font-size: 0.78rem;">
                            GPM <?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?> &bull; Masking &amp; Privasi Deliberasi LPM
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="<?= base_url('gpm/review/forward-lpm') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="document_id" id="forwardModalDocId" value="">
                <input type="hidden" name="redirect_to" value="gpm/perbaikan">

                <div class="modal-body px-4 py-3">
                    <!-- Dokumen Info Box -->
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="small text-muted" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Dokumen Sasaran</span>
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-0.5" id="forwardModalUnitBadge" style="font-size: 0.72rem;">-</span>
                        </div>
                        <h6 class="fw-bold text-dark-blue mb-0" id="forwardModalDocTitle" style="font-size: 0.98rem; line-height: 1.45;">-</h6>
                    </div>

                    <!-- Callout Catatan Evaluasi Internal LPM -->
                    <div class="p-3 rounded-3 mb-3 border border-warning-subtle" style="background: #FFFBEB;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <span class="small fw-bold text-warning-emphasis d-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="fas fa-lock text-warning"></i> Catatan Evaluasi Internal dari Tim LPM (Khusus Akses GPM):
                            </span>
                            <button type="button" class="btn btn-xs btn-outline-warning text-dark rounded-pill px-2.5 py-1 fw-bold shadow-2xs" onclick="copyLpmToGpm()" title="Salin catatan ini ke form arahan GPM">
                                <i class="fas fa-copy me-1"></i> Salin Catatan LPM ke Arahan GPM
                            </button>
                        </div>
                        <div class="p-2.5 rounded-3 bg-white border border-warning border-opacity-25 small text-dark" id="forwardModalLpmNote" style="line-height: 1.6; max-height: 160px; overflow-y: auto; white-space: pre-wrap; font-size: 0.84rem;">-</div>
                        <div class="text-muted small mt-1.5" style="font-size: 0.71rem;">
                            <i class="fas fa-info-circle text-primary me-1"></i> Catatan asli dari LPM ini <strong>tidak akan diperlihatkan</strong> kepada Program Studi / Dekanat.
                        </div>
                    </div>

                    <!-- Input Arahan Resmi GPM -->
                    <div class="mb-2">
                        <label for="forwardGpmNoteInput" class="form-label fw-bold text-dark small d-flex align-items-center justify-content-between mb-1.5">
                            <span>Arahan Perbaikan Resmi GPM ke Unit Sasaran <span class="text-danger">*</span></span>
                            <span class="badge rounded-pill bg-light text-secondary border px-2 py-0.5" style="font-size: 0.68rem;">Terbaca oleh Unit</span>
                        </label>
                        <textarea name="catatan_gpm" id="forwardGpmNoteInput" rows="4" class="form-control rounded-3" placeholder="Tuliskan arahan perbaikan resmi dari GPM Fakultas untuk ditindaklanjuti oleh unit kerja..." required></textarea>
                        <div class="form-text text-muted small mt-1" style="font-size: 0.72rem;">
                            <i class="fas fa-shield-halved text-success me-1"></i> Unit pengunggah akan menerima catatan ini murni atas nama <strong>GPM <?= htmlspecialchars($fakultas['nama_fakultas'] ?? 'Fakultas') ?></strong>.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2.5 px-4 bg-light bg-opacity-50 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-1.5 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary bg-scu-blue border-0 btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Arahan Revisi ke Unit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 2: DETAIL CATATAN EVALUATOR (VIEW ONLY)           -->
<!-- ======================================================= -->
<div class="modal fade" id="modalCatatanEvaluator" tabindex="-1" aria-labelledby="evalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3.5 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" id="evalModalIcon" style="width: 38px; height: 38px; font-size: 1rem; background: #DC2626;">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0.5" id="evalModalLabel" style="font-size: 1.1rem; letter-spacing: -0.2px;">
                            Catatan Evaluasi / Arahan Perbaikan
                        </h5>
                        <p class="text-white text-opacity-75 small mb-0" id="evalModalSubLabel" style="font-size: 0.78rem;">SPMI PPEPP UNIKA Soegijapranata</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <div class="small text-muted mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                        Nama Dokumen
                    </div>
                    <h6 class="fw-bold text-dark-blue mb-0" id="evalModalDocTitle" style="font-size: 0.98rem; line-height: 1.45;">-</h6>
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-2 pt-2 border-top text-muted small" style="font-size: 0.76rem;">
                        <div class="d-flex align-items-center gap-1.5">
                            <i class="fas fa-shield-halved text-primary" id="evalModalReviewerIcon"></i>
                            <span class="text-dark fw-bold" id="evalModalReviewer">-</span>
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
                <a href="#" id="evalModalEditBtn" class="btn btn-primary bg-scu-blue border-0 btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-check me-1"></i>
                    <span>Buka di Ruang Review</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let currentPerbaikanPages = {
    'tab-lpm': 1,
    'tab-unit': 1,
    'tab-sudah': 1,
    'tab-all': 1
};

function paginateTab(tabId, page) {
    const tabEl = document.getElementById(tabId);
    if (!tabEl) return;

    if (page !== undefined) {
        currentPerbaikanPages[tabId] = page;
    }

    const perPageSelect = document.getElementById('perPagePerbaikan');
    const perPageVal = perPageSelect ? perPageSelect.value : '50';
    const perPage = (perPageVal === 'all') ? 999999 : parseInt(perPageVal, 10);

    const searchInput = document.getElementById('liveSearchPerbaikan');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

    const allRows = Array.from(tabEl.querySelectorAll('tbody tr.perbaikan-row'));
    if (allRows.length === 0) return;

    const filterTahun = document.getElementById('filterTahunPerbaikanGpm') ? document.getElementById('filterTahunPerbaikanGpm').value.trim() : '';

    // Filter by search query and tahun
    const filteredRows = allRows.filter(row => {
        const text = (row.getAttribute('data-search') || '').toLowerCase();
        const rowTahun = (row.getAttribute('data-tahun') || '').trim();
        const matchSearch = !query || text.includes(query);
        const matchTahun = !filterTahun || rowTahun === filterTahun;
        return matchSearch && matchTahun;
    });

    const totalVisible = filteredRows.length;
    const totalPages = Math.ceil(totalVisible / perPage) || 1;
    if (currentPerbaikanPages[tabId] > totalPages) {
        currentPerbaikanPages[tabId] = totalPages;
    }
    const finalPage = currentPerbaikanPages[tabId] || 1;

    const startIdx = (finalPage - 1) * perPage;
    const endIdx = Math.min(startIdx + perPage, totalVisible);

    allRows.forEach(r => r.style.display = 'none');

    filteredRows.forEach((row, idx) => {
        if (idx >= startIdx && idx < endIdx) {
            row.style.display = '';
            const noCol = row.querySelector('td:first-child');
            if (noCol) noCol.textContent = idx + 1;
        } else {
            row.style.display = 'none';
        }
    });

    let infoId = 'infoTabLpm';
    let pagId = 'pagTabLpm';
    if (tabId === 'tab-unit') {
        infoId = 'infoTabUnit';
        pagId = 'pagTabUnit';
    } else if (tabId === 'tab-sudah') {
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

    let html = '<ul class="pagination-segmented mb-0">';
    html += '<li class="page-item ' + (finalPage <= 1 ? 'disabled' : '') + '">';
    html += '<button type="button" class="page-link" ' + (finalPage > 1 ? 'onclick="paginateTab(\'' + tabId + '\', ' + (finalPage - 1) + ')\"' : 'disabled') + ' aria-label="Sebelumnya"><i class="fas fa-chevron-left" style="font-size: 0.72rem;"></i></button>';
    html += '</li>';

    let startPage = Math.max(1, finalPage - 2);
    let endPage = Math.min(totalPages, finalPage + 2);

    if (startPage > 1) {
        html += '<li class="page-item"><button type="button" class="page-link" onclick="paginateTab(\'' + tabId + '\', 1)">1</button></li>';
        if (startPage > 2) {
            html += '<li class="page-item ellipsis disabled"><span class="page-link dots">&hellip;</span></li>';
        }
    }

    for (let p = startPage; p <= endPage; p++) {
        html += '<li class="page-item ' + (p === finalPage ? 'active' : '') + '">';
        html += '<button type="button" class="page-link" onclick="paginateTab(\'' + tabId + '\', ' + p + ')">' + p + '</button>';
        html += '</li>';
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += '<li class="page-item ellipsis disabled"><span class="page-link dots">&hellip;</span></li>';
        }
        html += '<li class="page-item"><button type="button" class="page-link" onclick="paginateTab(\'' + tabId + '\', ' + totalPages + ')">' + totalPages + '</button></li>';
    }

    html += '<li class="page-item ' + (finalPage >= totalPages ? 'disabled' : '') + '">';
    html += '<button type="button" class="page-link" ' + (finalPage < totalPages ? 'onclick="paginateTab(\'' + tabId + '\', ' + (finalPage + 1) + ')\"' : 'disabled') + ' aria-label="Selanjutnya"><i class="fas fa-chevron-right" style="font-size: 0.72rem;"></i></button>';
    html += '</li>';
    html += '</ul>';

    pagEl.innerHTML = html;
}

function refreshAllPerbaikanTabs() {
    ['tab-lpm', 'tab-unit', 'tab-sudah', 'tab-all'].forEach(id => paginateTab(id, 1));
}

function filterPerbaikanList() {
    refreshAllPerbaikanTabs();
}

function changePerbaikanPageSize(val) {
    const sel = document.getElementById('perPagePerbaikan');
    if (sel) sel.value = val;
    document.querySelectorAll('.table-perpage-select').forEach(s => s.value = val);
    refreshAllPerbaikanTabs();
}

function copyLpmToGpm() {
    const lpmText = document.getElementById('forwardModalLpmNote')?.textContent || '';
    const gpmInput = document.getElementById('forwardGpmNoteInput');
    if (gpmInput) {
        gpmInput.value = lpmText.trim();
        gpmInput.focus();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi awal pagination untuk seluruh tab
    refreshAllPerbaikanTabs();

    // Event listener search live
    const searchInput = document.getElementById('liveSearchPerbaikan');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            refreshAllPerbaikanTabs();
        });
    }

    // Event listener per page
    const perPageSelect = document.getElementById('perPagePerbaikan');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            changePerbaikanPageSize(this.value);
        });
    }

    // Tab change re-pagination
    const tabBtns = document.querySelectorAll('#perbaikanTabs button[data-bs-toggle="pill"]');
    tabBtns.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            const targetId = e.target.getAttribute('data-bs-target').replace('#', '');
            paginateTab(targetId);
        });
    });

    // Handler tombol Forward LPM Revision
    document.querySelectorAll('.btn-open-forward').forEach(btn => {
        btn.addEventListener('click', function() {
            const docId = this.getAttribute('data-doc-id');
            const docTitle = this.getAttribute('data-doc-title') || '-';
            const unitName = this.getAttribute('data-unit-name') || '-';
            const lpmNote = this.getAttribute('data-lpm-note') || '-';
            const gpmNote = this.getAttribute('data-gpm-note') || '';

            document.getElementById('forwardModalDocId').value = docId;
            document.getElementById('forwardModalDocTitle').textContent = docTitle;
            document.getElementById('forwardModalUnitBadge').textContent = unitName;
            document.getElementById('forwardModalLpmNote').textContent = lpmNote;
            document.getElementById('forwardGpmNoteInput').value = gpmNote || lpmNote;

            const modal = new bootstrap.Modal(document.getElementById('modalForwardLpmRevision'));
            modal.show();
        });
    });

    // Handler modal detail evaluator
    const evalModal = document.getElementById('modalCatatanEvaluator');
    if (evalModal) {
        evalModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            const nama = btn.getAttribute('data-nama') || '-';
            const unit = btn.getAttribute('data-unit') || '-';
            const reviewer = btn.getAttribute('data-reviewer') || '-';
            const tanggal = btn.getAttribute('data-tanggal') || '-';
            const catatan = btn.getAttribute('data-catatan') || 'Tidak ada catatan evaluasi.';
            const editUrl = btn.getAttribute('data-edit-url') || '#';
            const isGpm = btn.getAttribute('data-is-gpm') === '1';

            document.getElementById('evalModalDocTitle').textContent = nama;
            document.getElementById('evalModalReviewer').textContent = reviewer;
            document.getElementById('evalModalTanggal').textContent = tanggal;
            document.getElementById('evalModalCatatan').textContent = catatan;
            document.getElementById('evalModalEditBtn').href = editUrl;

            const modalLabel = document.getElementById('evalModalLabel');
            const iconEl = document.getElementById('evalModalIcon');
            const revIconEl = document.getElementById('evalModalReviewerIcon');

            if (isGpm) {
                if (modalLabel) modalLabel.textContent = 'Catatan Arahan Resmi GPM Fakultas';
                if (iconEl) iconEl.style.background = '#7C3AED';
                if (revIconEl) revIconEl.className = 'fas fa-shield-halved text-purple';
            } else {
                if (modalLabel) modalLabel.textContent = 'Catatan Evaluasi Internal Tim LPM (Akses GPM)';
                if (iconEl) iconEl.style.background = '#EA580C';
                if (revIconEl) revIconEl.className = 'fas fa-lock text-warning';
            }
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/admin_footer.php'; ?>
