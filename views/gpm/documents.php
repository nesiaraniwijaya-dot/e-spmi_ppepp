<?php
/**
 * GPM (Gugus Penjaminan Mutu): Repositori Dokumen Mutu PPEPP
 * Menampilkan dan mengelola dokumen tingkat Fakultas dan seluruh Program Studi
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$documents    = $documents ?? [];
$fakultas     = $fakultas ?? [];
$prodisList   = $prodisList ?? [];
$bidangList   = $bidangList ?? [];
$unitFilter   = $unitFilter ?? 'all';
$siklusFilter = $siklusFilter ?? '';
$bidangFilter = $bidangFilter ?? 0;
$statusFilter = $statusFilter ?? '';
$search       = $search ?? '';

$perluDocs  = array_values(array_filter($documents, fn($d) => ($d['status_review'] ?? '') === 'perlu_perbaikan'));
$sudahDocs  = array_values(array_filter($documents, fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));
$draftDocs  = array_values(array_filter($documents, fn($d) => ($d['status_review'] ?? '') === 'draft'));

$perluCount = $gpmPerluCount ?? count($perluDocs);
$sudahCount = $gpmSudahCount ?? count($sudahDocs);
$draftCount = $gpmDraftCount ?? count($draftDocs);

// Hitung siklus
$siklusCounts = [
    'all'          => count($documents),
    'penetapan'    => 0,
    'pelaksanaan'  => 0,
    'evaluasi'     => 0,
    'pengendalian' => 0,
    'peningkatan'  => 0,
];
foreach ($documents as $d) {
    $s = $d['siklus'] ?? '';
    if (isset($siklusCounts[$s])) {
        $siklusCounts[$s]++;
    }
}
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
                <span class="badge rounded-pill px-2.5 py-1 fw-semibold text-secondary d-inline-flex align-items-center gap-1" style="background: #F1F5F9; border: 1px solid #E2E8F0; font-size: 0.75rem;">
                    <i class="fas fa-folder-open text-primary"></i> Repositori Mutu PPEPP
                </span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">Daftar Dokumen Mutu PPEPP</h3>
            <p class="text-muted small mb-0" style="line-height: 1.5;">
                Kelola dokumen PPEPP untuk tingkat <strong>Fakultas</strong> dan seluruh <strong>Program Studi</strong> di bawah naungan <strong><?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?></strong>.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('gpm/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.84rem;">
                <i class="fas fa-plus"></i> Unggah Dokumen Baru
            </a>
        </div>
    </div>

    <?php 
    $activeNotifCount = ($perluCount > 0 ? 1 : 0) + ($sudahCount > 0 ? 1 : 0) + ($draftCount > 0 ? 1 : 0);
    $colClass = $activeNotifCount === 3 ? 'col-lg-4 col-md-6 col-12' : ($activeNotifCount === 2 ? 'col-md-6 col-12' : 'col-12');
    ?>

    <?php if ($activeNotifCount > 0): ?>
        <!-- Quick Action Notification Grid: Bernafas Lega, Rapi, & Proporsional -->
        <div class="row g-3 g-xl-4 mb-4 pb-1">
            <!-- 1. Perlu Tindakan Revisi LPM -->
            <?php if ($perluCount > 0): ?>
                <div class="<?= $colClass ?>">
                    <div class="card border-0 shadow-2xs rounded-4 p-3.5 p-xl-4 h-100 d-flex flex-row align-items-center justify-content-between gap-3.5 transition-all" 
                         style="background: linear-gradient(135deg, #FFF5F5 0%, #FEF2F2 100%); border: 1px solid #FECACA !important;">
                        <div class="d-flex align-items-center gap-3.5" style="min-width: 0;">
                            <div class="rounded-4 bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 48px; height: 48px; min-width: 48px; font-size: 1.25rem;">
                                <i class="fas fa-wrench"></i>
                            </div>
                            <div class="d-flex flex-column justify-content-center" style="min-width: 0;">
                                <div class="mb-1.5">
                                    <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold shadow-2xs" style="font-size: 0.68rem; letter-spacing: 0.3px;">Perlu Tindakan</span>
                                </div>
                                <div class="fw-bold text-dark-blue mb-0.5 text-truncate" style="font-size: 0.98rem; line-height: 1.3;">
                                    <?= $perluCount ?> Dokumen Revisi
                                </div>
                                <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.35;">
                                    Catatan evaluasi mutu dari LPM
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('gpm/perbaikan') ?>" class="btn btn-danger btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-xs d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
                            <span>Perbaiki</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 2. Menunggu Antrean Review LPM -->
            <?php if ($sudahCount > 0): ?>
                <div class="<?= $colClass ?>">
                    <div class="card border-0 shadow-2xs rounded-4 p-3.5 p-xl-4 h-100 d-flex flex-row align-items-center justify-content-between gap-3.5 transition-all" 
                         style="background: linear-gradient(135deg, #F0F7FF 0%, #EFF6FF 100%); border: 1px solid #BFDBFE !important;">
                        <div class="d-flex align-items-center gap-3.5" style="min-width: 0;">
                            <div class="rounded-4 bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 48px; height: 48px; min-width: 48px; font-size: 1.25rem;">
                                <i class="fas fa-clock-rotate-left"></i>
                            </div>
                            <div class="d-flex flex-column justify-content-center" style="min-width: 0;">
                                <div class="mb-1.5">
                                    <span class="badge bg-primary rounded-pill px-2.5 py-1 fw-bold shadow-2xs" style="font-size: 0.68rem; letter-spacing: 0.3px;">Antrean Review</span>
                                </div>
                                <div class="fw-bold text-dark-blue mb-0.5 text-truncate" style="font-size: 0.98rem; line-height: 1.3;">
                                    <?= $sudahCount ?> Dokumen Antrean
                                </div>
                                <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.35;">
                                    Verifikasi ulang Tim LPM
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('gpm/perbaikan') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-2xs d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
                            <span>Pantau Revisi</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 3. Draf Dokumen Belum Diajukan -->
            <?php if ($draftCount > 0): ?>
                <div class="<?= $colClass ?>">
                    <div class="card border-0 shadow-2xs rounded-4 p-3.5 p-xl-4 h-100 d-flex flex-row align-items-center justify-content-between gap-3.5 transition-all" 
                         style="background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%); border: 1px solid #E2E8F0 !important;">
                        <div class="d-flex align-items-center gap-3.5" style="min-width: 0;">
                            <div class="rounded-4 text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 48px; height: 48px; min-width: 48px; font-size: 1.25rem; background-color: #64748B !important;">
                                <i class="fas fa-file-pen"></i>
                            </div>
                            <div class="d-flex flex-column justify-content-center" style="min-width: 0;">
                                <div class="mb-1.5">
                                    <span class="badge bg-secondary rounded-pill px-2.5 py-1 fw-bold shadow-2xs" style="font-size: 0.68rem; letter-spacing: 0.3px;">Draf Tersimpan</span>
                                </div>
                                <div class="fw-bold text-dark-blue mb-0.5 text-truncate" style="font-size: 0.98rem; line-height: 1.3;">
                                    <?= $draftCount ?> Draf Dokumen
                                </div>
                                <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.35;">
                                    Belum diajukan ke LPM
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('gpm/draft') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-2xs d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
                            <span>Buka Draf</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Filter Unit Kerja GPM: Dropdown Rapi, User-Friendly & Ringkas -->
    <div class="card shadow-2xs rounded-4 p-3 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 flex-grow-1" style="max-width: 580px;">
                <div class="rounded-3 bg-light text-primary d-flex align-items-center justify-content-center flex-shrink-0 shadow-2xs" style="width: 42px; height: 42px; border: 1px solid #E2E8F0; font-size: 1.15rem;">
                    <i class="fas fa-building-columns"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label for="filterUnitKerjaGpm" class="form-label mb-0 fw-bold text-dark small d-flex align-items-center gap-1.5">
                            <span>Filter Unit Kerja:</span>
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem;">GPM Scope</span>
                        </label>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #CBD5E1;"><i class="fas fa-filter text-scu-gold"></i></span>
                        <select id="filterUnitKerjaGpm" class="form-select border-start-0 fw-semibold" style="border-color: #CBD5E1; font-size: 0.84rem;" onchange="window.location.href = this.value">
                            <option value="<?= base_url('gpm/dokumen?' . http_build_query(array_merge($_GET, ['unit' => 'all']))) ?>" <?= $unitFilter === 'all' ? 'selected' : '' ?>>
                                📁 Semua Unit Kerja (Fakultas & Seluruh Program Studi)
                            </option>
                            <option value="<?= base_url('gpm/dokumen?' . http_build_query(array_merge($_GET, ['unit' => 'fakultas']))) ?>" <?= $unitFilter === 'fakultas' ? 'selected' : '' ?>>
                                🏢 Tingkat Fakultas (<?= htmlspecialchars($fakultas['nama_fakultas'] ?? 'Dekanat') ?>)
                            </option>
                            <optgroup label="── Program Studi di Bawah Fakultas ──">
                                <?php foreach ($prodisList as $p): ?>
                                    <option value="<?= base_url('gpm/dokumen?' . http_build_query(array_merge($_GET, ['unit' => $p['id']]))) ?>" <?= ((string)$unitFilter === (string)$p['id']) ? 'selected' : '' ?>>
                                        🎓 <?= htmlspecialchars($p['jenjang'] . ' ' . $p['nama_prodi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Current Scope Status indicator -->
            <div class="small text-muted d-flex align-items-center gap-2 ps-lg-3 border-start-lg">
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

    <!-- Table & Filters Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        
        <!-- 5 Siklus PPEPP Tabs Header -->
        <ul class="nav ppepp-nav-tabs mb-4" id="gpmPpeppTabs" role="tablist">
            <li class="nav-item">
                <a href="<?= base_url('gpm/dokumen?' . http_build_query(array_merge($_GET, ['siklus' => '']))) ?>" 
                   class="nav-link <?= empty($siklusFilter) ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> Semua (<?= $siklusCounts['all'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('gpm/dokumen?' . http_build_query(array_merge($_GET, ['siklus' => 'penetapan']))) ?>" 
                   class="nav-link tab-penetapan <?= $siklusFilter === 'penetapan' ? 'active' : '' ?>">
                    <i class="fas fa-file-signature"></i> Penetapan (<?= $siklusCounts['penetapan'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('gpm/dokumen?' . http_build_query(array_merge($_GET, ['siklus' => 'pelaksanaan']))) ?>" 
                   class="nav-link tab-pelaksanaan <?= $siklusFilter === 'pelaksanaan' ? 'active' : '' ?>">
                    <i class="fas fa-tasks"></i> Pelaksanaan (<?= $siklusCounts['pelaksanaan'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('gpm/dokumen?' . http_build_query(array_merge($_GET, ['siklus' => 'evaluasi']))) ?>" 
                   class="nav-link tab-evaluasi <?= $siklusFilter === 'evaluasi' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Evaluasi (<?= $siklusCounts['evaluasi'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('gpm/dokumen?' . http_build_query(array_merge($_GET, ['siklus' => 'pengendalian']))) ?>" 
                   class="nav-link tab-pengendalian <?= $siklusFilter === 'pengendalian' ? 'active' : '' ?>">
                    <i class="fas fa-sliders"></i> Pengendalian (<?= $siklusCounts['pengendalian'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('gpm/dokumen?' . http_build_query(array_merge($_GET, ['siklus' => 'peningkatan']))) ?>" 
                   class="nav-link tab-peningkatan <?= $siklusFilter === 'peningkatan' ? 'active' : '' ?>">
                    <i class="fas fa-arrow-trend-up"></i> Peningkatan (<?= $siklusCounts['peningkatan'] ?>)
                </a>
            </li>
        </ul>

        <!-- Filter Form Toolbar -->
        <form method="GET" action="<?= base_url('gpm/dokumen') ?>" class="row g-2.5 mb-4 align-items-center">
            <input type="hidden" name="unit" value="<?= htmlspecialchars($unitFilter) ?>">
            <input type="hidden" name="siklus" value="<?= htmlspecialchars($siklusFilter) ?>">

            <!-- Filter Bidang -->
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-bookmark"></i></span>
                    <select name="bidang_id" class="form-select border-start-0" onchange="this.form.submit()">
                        <option value="">Semua Bidang</option>
                        <?php foreach ($bidangList as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= $bidangFilter == $b['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($b['nama_bidang']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Filter Status Review -->
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-shield"></i></span>
                    <select name="status_review" class="form-select border-start-0" onchange="this.form.submit()">
                        <option value="">Semua Status Review</option>
                        <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>Draf Dokumen</option>
                        <option value="belum_direview" <?= $statusFilter === 'belum_direview' ? 'selected' : '' ?>>Belum Direview</option>
                        <option value="perlu_perbaikan" <?= $statusFilter === 'perlu_perbaikan' ? 'selected' : '' ?>>Perlu Perbaikan</option>
                        <option value="sudah_diperbaiki" <?= $statusFilter === 'sudah_diperbaiki' ? 'selected' : '' ?>>Sudah Diperbaiki</option>
                        <option value="sesuai" <?= $statusFilter === 'sesuai' ? 'selected' : '' ?>>Sesuai Standar</option>
                    </select>
                </div>
            </div>

            <!-- Keyword Search -->
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, kode, prodi..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary bg-scu-blue border-0 px-3">Cari</button>
                </div>
            </div>

            <!-- Reset Button -->
            <div class="col-md-2 text-md-end">
                <?php if (!empty($unitFilter !== 'all') || !empty($siklusFilter) || !empty($bidangFilter) || !empty($statusFilter) || !empty($search)): ?>
                    <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill w-100 fw-semibold" style="font-size: 0.78rem;">
                        <i class="fas fa-rotate-left me-1"></i> Reset Filter
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Sub-Toolbar: Jumlah Tampilan & Info Dokumen -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pt-2">
            <div class="small text-muted" id="gpmTopTableInfo">
                <i class="fas fa-layer-group text-primary me-1"></i> Menampilkan total <strong><?= count($documents) ?> Dokumen Mutu</strong>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label for="gpmPageSizeSelect" class="small text-muted text-nowrap mb-0" style="font-size: 0.78rem;">Tampilkan:</label>
                <select id="gpmPageSizeSelect" class="form-select form-select-sm form-select-perpage shadow-none" style="min-width: 108px; width: auto; font-size: 0.8rem; border-color: #CBD5E1;" onchange="changeGpmPageSize(this.value)">
                    <option value="10" selected>10 data</option>
                    <option value="25">25 data</option>
                    <option value="50">50 data</option>
                    <option value="100">100 data</option>
                    <option value="all">Semua</option>
                </select>
            </div>
        </div>

        <!-- Dokumen Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="gpmDocumentsTable">
                <thead class="table-light text-uppercase small text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3 py-3" style="width: 45px;">No</th>
                        <th class="py-3" style="width: 140px;">Unit Sasaran</th>
                        <th class="py-3" style="min-width: 250px;">Dokumen Mutu & Berkas</th>
                        <th class="py-3" style="width: 130px;">Siklus PPEPP</th>
                        <th class="py-3" style="min-width: 170px;">Bidang &amp; Standar</th>
                        <th class="py-3" style="min-width: 180px;">Status Review LPM</th>
                        <th class="text-end pe-3 py-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                                        <i class="fas fa-folder-open text-secondary"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Sesuai</h6>
                                    <p class="small text-muted mb-3">Belum ada dokumen yang sesuai dengan kriteria filter yang Anda pilih.</p>
                                    <a href="<?= base_url('gpm/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 bg-scu-blue border-0 fw-semibold">
                                        <i class="fas fa-plus me-1"></i> Unggah Dokumen Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($documents as $doc): ?>
                            <tr class="gpm-doc-row">
                                <td class="ps-3 align-top py-3 text-muted fw-semibold small text-center gpm-row-no"><?= $no++ ?></td>

                                <!-- Unit Sasaran Badge -->
                                <td class="align-top py-3">
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

                                <!-- Dokumen Mutu & Berkas -->
                                <td class="align-top py-3">
                                    <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.9rem; line-height: 1.35;">
                                        <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap align-items-center gap-2 small text-muted mb-1.5">
                                        <?php if (!empty($doc['nomor_dokumen'])): ?>
                                            <span><i class="fas fa-hashtag text-muted"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                            <span>&bull;</span>
                                        <?php endif; ?>
                                        <span><i class="far fa-calendar text-muted"></i> TA <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                    </div>

                                    <!-- Lampiran Berkas / Links -->
                                    <div class="d-flex flex-wrap align-items-center gap-1.5">
                                        <?php 
                                        $files = $doc['files'] ?? [];
                                        $links = parse_external_links($doc['external_link'] ?? '');
                                        ?>
                                        <?php if (!empty($files)): ?>
                                            <?php if (count($files) === 1): 
                                                $singleF = $files[0];
                                            ?>
                                                <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#pdfPreviewModal" 
                                                        data-pdf-url="<?= base_url($singleF['file_path']) ?>" 
                                                        data-doc-title="<?= htmlspecialchars($singleF['file_name']) ?>" 
                                                        data-doc-narasi="<?= htmlspecialchars($singleF['narasi'] ?? '') ?>" 
                                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleF['sub_bidang_ids'] ?? null)) ?>" 
                                                        data-public-limit="0" 
                                                        data-can-download="1"
                                                        data-can-access="1"
                                                        style="font-size: 0.72rem;" title="Pratinjau Berkas PDF">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </button>
                                            <?php else: ?>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-xs btn-outline-danger dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                            type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                                        <i class="fas fa-file-pdf text-danger"></i> <?= count($files) ?> PDF
                                                    </button>
                                                    <ul class="dropdown-menu shadow-lg border-0 p-2 rounded-3" style="min-width: 260px; max-width: 360px; font-size: 0.78rem;">
                                                        <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS PDF:</li>
                                                        <?php foreach ($files as $fItem): ?>
                                                            <li class="mb-1">
                                                                <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#pdfPreviewModal" 
                                                                        data-pdf-url="<?= base_url($fItem['file_path']) ?>" 
                                                                        data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                                        data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null)) ?>"
                                                                        data-public-limit="0"
                                                                        data-can-download="1"
                                                                        data-can-access="1">
                                                                    <div class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate">
                                                                        <i class="fas fa-file-pdf text-danger flex-shrink-0"></i>
                                                                        <span class="text-truncate"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                                    </div>
                                                                </button>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if (!empty($links)): ?>
                                            <?php if (count($links) === 1): 
                                                $singleL = $links[0];
                                            ?>
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#pdfPreviewModal" 
                                                        data-is-link="1" 
                                                        data-link-url="<?= htmlspecialchars($singleL['url']) ?>" 
                                                        data-doc-title="<?= htmlspecialchars($singleL['narasi'] ?: $doc['nama_dokumen']) ?>" 
                                                        data-doc-narasi="<?= htmlspecialchars($singleL['narasi'] ?? '') ?>" 
                                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleL['sub_bidang_ids'] ?? null)) ?>" 
                                                        data-can-access="1" 
                                                        data-can-download="1"
                                                        style="font-size: 0.72rem;" title="Pratinjau Link GDrive">
                                                    <i class="fab fa-google-drive"></i> GDrive
                                                </button>
                                            <?php else: ?>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-xs btn-outline-primary dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                            type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                                        <i class="fab fa-google-drive text-primary"></i> <?= count($links) ?> Drive
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 260px; max-width: 360px; font-size: 0.78rem;">
                                                        <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH TAUTAN DRIVE:</li>
                                                        <?php foreach ($links as $lIdx => $lItem): ?>
                                                            <li class="mb-1">
                                                                <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#pdfPreviewModal" 
                                                                        data-is-link="1" 
                                                                        data-link-url="<?= htmlspecialchars($lItem['url']) ?>" 
                                                                        data-doc-title="<?= htmlspecialchars($lItem['narasi'] ?: ('Tautan GDrive #' . ($lIdx + 1))) ?>" 
                                                                        data-doc-narasi="<?= htmlspecialchars($lItem['narasi'] ?? '') ?>" 
                                                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($lItem['sub_bidang_ids'] ?? null)) ?>" 
                                                                        data-can-access="1" 
                                                                        data-can-download="1">
                                                                    <div class="d-flex align-items-center gap-1.5 fw-semibold text-primary text-truncate">
                                                                        <i class="fab fa-google-drive"></i>
                                                                        <span class="text-truncate"><?= htmlspecialchars($lItem['narasi'] ?: ('Tautan GDrive #' . ($lIdx + 1))) ?></span>
                                                                    </div>
                                                                </button>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if (empty($files) && empty($links)): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">
                                                <i class="fas fa-triangle-exclamation me-1"></i> Belum ada lampiran
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Siklus PPEPP -->
                                <td class="align-top py-3">
                                    <?= siklus_badge($doc['siklus']) ?>
                                </td>

                                <!-- Bidang Standar -->
                                <td class="align-top py-3">
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
                                <td class="align-top py-3">
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <?php if ($doc['status_review'] === 'perlu_perbaikan'): ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-triangle-exclamation text-danger"></i> Perlu Perbaikan
                                            </span>
                                            <div class="d-flex flex-wrap gap-1 mt-0.5">
                                                <?php if (!empty($doc['catatan_review'])): ?>
                                                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#docModal<?= $doc['id'] ?>"
                                                            style="font-size: 0.68rem;">
                                                        <i class="fas fa-comment-dots"></i> Catatan LPM
                                                    </button>
                                                <?php endif; ?>
                                                <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="btn btn-xs btn-danger text-white rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs text-decoration-none" style="font-size: 0.68rem;">
                                                    <i class="fas fa-wrench"></i> Perbaiki
                                                </a>
                                            </div>
                                        <?php elseif ($doc['status_review'] === 'sudah_diperbaiki'): ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-clock-rotate-left text-primary"></i> Menunggu Review LPM
                                            </span>
                                            <?php if (!empty($doc['catatan_review'])): ?>
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5 mt-0.5 d-inline-flex align-items-center gap-1 shadow-2xs"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#docModal<?= $doc['id'] ?>"
                                                        style="font-size: 0.68rem;">
                                                    <i class="fas fa-history"></i> Catatan Sebelumnya
                                                </button>
                                            <?php endif; ?>
                                        <?php elseif ($doc['status_review'] === 'sesuai'): ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-circle-check text-success"></i> Sesuai Standar
                                            </span>
                                        <?php elseif ($doc['status_review'] === 'draft'): ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-pencil text-secondary"></i> Draf Dokumen
                                            </span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-clock text-warning"></i> Belum Direview
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($doc['reviewed_at']) && !in_array($doc['status_review'], ['draft', 'belum_direview'])): ?>
                                            <div class="text-muted d-flex align-items-center gap-1 mt-0.5" style="font-size: 0.68rem;">
                                                <i class="far fa-clock text-secondary opacity-75"></i>
                                                <span><?= date('d/m/Y H:i', strtotime($doc['reviewed_at'])) ?> WIB</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Aksi -->
                                <td class="pe-3 align-top text-end py-3">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <!-- Detail Modal Trigger -->
                                        <button type="button" class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" style="width: 36px; height: 36px;" title="Lihat Rincian Dokumen" data-bs-toggle="modal" data-bs-target="#docModal<?= $doc['id'] ?>">
                                            <i class="fas fa-eye text-primary" style="font-size: 0.85rem;"></i>
                                        </button>

                                        <!-- Edit -->
                                        <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" style="width: 36px; height: 36px;" title="Edit / Ubah Dokumen">
                                            <i class="fas fa-pen text-warning" style="font-size: 0.85rem;"></i>
                                        </a>

                                        <!-- Delete Soft -->
                                        <button type="button" class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" style="width: 36px; height: 36px;" title="Hapus Dokumen" data-bs-toggle="modal" data-bs-target="#delModal<?= $doc['id'] ?>">
                                            <i class="fas fa-trash-can text-danger" style="font-size: 0.85rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Detail Dokumen -->
                            <div class="modal fade" id="docModal<?= $doc['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-0 px-4 py-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 100%) !important;">
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="p-2 rounded-3 bg-white bg-opacity-10 text-info d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-file-lines fa-lg"></i>
                                                </div>
                                                <div>
                                                    <h6 class="modal-title fw-bold text-white mb-0" style="font-size: 1.05rem; letter-spacing: -0.2px;">Rincian Dokumen Mutu</h6>
                                                    <div class="text-white-50 small" style="font-size: 0.72rem;">Detail metadata dan berkas lampiran</div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <span class="small text-muted d-block mb-1">Unit Pemilik / Sasaran:</span>
                                                    <div>
                                                        <?php if ($doc['level'] === 'fakultas'): ?>
                                                            <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE;">
                                                                <i class="fas fa-building-columns me-1"></i> Fakultas <?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;">
                                                                <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($doc['nama_prodi'] ?? '') ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="small text-muted d-block mb-1">Siklus PPEPP & Status:</span>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <?= siklus_badge($doc['siklus']) ?>
                                                        <?= review_status_badge($doc['status_review']) ?>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <span class="small text-muted d-block mb-1">Nama Dokumen:</span>
                                                    <h5 class="fw-bold text-dark-blue mb-0"><?= htmlspecialchars($doc['nama_dokumen']) ?></h5>
                                                    <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                        <div class="small text-muted mt-1"><i class="fas fa-hashtag me-1"></i> Nomor: <?= htmlspecialchars($doc['nomor_dokumen']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="small text-muted d-block mb-1">Bidang & Standar:</span>
                                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?></div>
                                                    <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                                        <div class="small text-muted"><?= htmlspecialchars($doc['nama_sub_bidang']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="small text-muted d-block mb-1">Tahun Akademik:</span>
                                                    <div class="fw-semibold text-dark">TA <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></div>
                                                </div>
                                            </div>

                                            <!-- Catatan Evaluasi LPM -->
                                            <?php if (!empty($doc['catatan_review'])): ?>
                                                <div class="p-3 rounded-3 mb-3 border <?= $doc['status_review'] === 'perlu_perbaikan' ? 'border-danger' : 'border-primary' ?>" style="background: <?= $doc['status_review'] === 'perlu_perbaikan' ? '#FFF1F2' : '#EFF6FF' ?>; border-left: 4px solid <?= $doc['status_review'] === 'perlu_perbaikan' ? '#DC2626' : '#2563EB' ?> !important;">
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                        <span class="small fw-bold text-uppercase <?= $doc['status_review'] === 'perlu_perbaikan' ? 'text-danger' : 'text-primary' ?>" style="font-size: 0.74rem;">
                                                            <i class="fas fa-comment-dots me-1"></i> Catatan Evaluasi Reviewer LPM:
                                                        </span>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                                            <i class="fas fa-award me-0.5"></i> Pusat Penjaminan Mutu LPM
                                                        </span>
                                                    </div>
                                                    <div class="small text-dark mb-2" style="line-height: 1.55; white-space: pre-wrap; font-size: 0.85rem;"><?= htmlspecialchars($doc['catatan_review']) ?></div>
                                                    <div class="small text-muted d-flex align-items-center gap-2 pt-1 border-top" style="font-size: 0.72rem; border-color: rgba(0,0,0,0.06) !important;">
                                                        <span><i class="fas fa-building-columns text-primary me-1"></i>Pusat Penjaminan Mutu LPM</span>
                                                        <?php if (!empty($doc['reviewed_at'])): ?>
                                                            <span>&bull;</span>
                                                            <span><i class="far fa-clock text-secondary me-1"></i><?= date('d M Y, H:i', strtotime($doc['reviewed_at'])) ?> WIB</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Berkas Lampiran Fisik (Hanya tampil jika ada berkas) -->
                                            <?php if (!empty($files)): ?>
                                                <div class="p-3.5 p-md-4 rounded-4 bg-light border mb-4">
                                                    <div class="d-flex align-items-center justify-content-between pb-2.5 mb-3 border-bottom border-light-subtle">
                                                        <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                                                            <i class="fas fa-paperclip text-primary"></i> Berkas Lampiran Dokumen (<?= count($files) ?>):
                                                        </span>
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2.5 py-1 rounded-pill fw-medium" style="font-size: 0.7rem;">PDF Interaktif</span>
                                                    </div>
                                                    <div class="d-flex flex-column gap-3">
                                                        <?php foreach ($files as $f): 
                                                            $subBadgesHtml = render_sub_standar_badges($f['sub_bidang_ids'] ?? null);
                                                        ?>
                                                            <div class="card border rounded-3 p-3.5 bg-white shadow-2xs">
                                                                <div class="d-flex flex-column flex-sm-row sm-align-items-center justify-content-between gap-3">
                                                                    <div class="d-flex align-items-start gap-3 me-sm-2">
                                                                        <div class="p-2.5 rounded-3 bg-danger bg-opacity-10 text-danger flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                                            <i class="fas fa-file-pdf fa-xl"></i>
                                                                        </div>
                                                                        <div class="min-w-0">
                                                                            <div class="fw-bold text-dark mb-1" style="font-size: 0.92rem; line-height: 1.35; word-break: break-word;">
                                                                                <?= htmlspecialchars($f['file_name']) ?>
                                                                            </div>
                                                                            <?php if (!empty($f['file_size'])): ?>
                                                                                <span class="badge bg-light text-muted border px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                                                                    <?= htmlspecialchars($f['file_size']) ?>
                                                                                </span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2 flex-shrink-0 align-self-sm-center align-self-end">
                                                                        <button type="button" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-2xs fw-semibold"
                                                                                data-bs-toggle="modal" 
                                                                                data-bs-target="#pdfPreviewModal" 
                                                                                data-pdf-url="<?= base_url($f['file_path']) ?>" 
                                                                                data-doc-title="<?= htmlspecialchars($f['file_name']) ?>" 
                                                                                data-doc-narasi="<?= htmlspecialchars($f['narasi'] ?? '') ?>" 
                                                                                data-doc-standar="<?= htmlspecialchars($subBadgesHtml) ?>" 
                                                                                data-public-limit="0" 
                                                                                data-can-download="1"
                                                                                data-can-access="1"
                                                                                style="font-size: 0.78rem;">
                                                                            <i class="fas fa-eye"></i> Pratinjau
                                                                        </button>
                                                                        <a href="<?= base_url($f['file_path']) ?>" target="_blank" download class="btn btn-sm btn-outline-secondary rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.78rem;">
                                                                            <i class="fas fa-download"></i> Unduh
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                                <?php if (!empty($subBadgesHtml)): ?>
                                                                    <div class="mt-2.5 pt-1 d-flex flex-wrap align-items-center gap-1.5">
                                                                        <?= $subBadgesHtml ?>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if (!empty($f['narasi'])): ?>
                                                                    <div class="mt-2.5 p-2.5 px-3 rounded-3 bg-light-subtle border border-light-subtle text-secondary small d-flex align-items-start gap-2" style="font-size: 0.8rem; line-height: 1.5;">
                                                                        <i class="fas fa-comment-dots text-primary mt-0.5 opacity-75 flex-shrink-0"></i>
                                                                        <span class="text-break"><?= htmlspecialchars($f['narasi']) ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Tautan Google Drive Eksternal (Hanya tampil jika ada tautan) -->
                                            <?php if (!empty($links)): ?>
                                                <div class="p-3.5 p-md-4 rounded-4 bg-light border mb-4">
                                                    <div class="d-flex align-items-center justify-content-between pb-2.5 mb-3 border-bottom border-light-subtle">
                                                        <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                                                            <i class="fab fa-google-drive text-success"></i> Tautan Google Drive Eksternal (<?= count($links) ?>):
                                                        </span>
                                                        <span class="badge bg-success bg-opacity-10 text-success border px-2.5 py-1 rounded-pill fw-medium" style="font-size: 0.7rem;">Cloud Storage</span>
                                                    </div>
                                                    <div class="d-flex flex-column gap-3">
                                                        <?php foreach ($links as $lIdx => $l): 
                                                            $subBadgesHtml = render_sub_standar_badges($l['sub_bidang_ids'] ?? null);
                                                        ?>
                                                            <div class="card border rounded-3 p-3.5 bg-white shadow-2xs">
                                                                <div class="d-flex flex-column flex-sm-row sm-align-items-center justify-content-between gap-3">
                                                                    <div class="d-flex align-items-start gap-3 me-sm-2">
                                                                        <div class="p-2.5 rounded-3 bg-success bg-opacity-10 text-success flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                                            <i class="fab fa-google-drive fa-xl"></i>
                                                                        </div>
                                                                        <div class="min-w-0">
                                                                            <div class="fw-bold text-dark mb-1" style="font-size: 0.92rem; line-height: 1.35; word-break: break-word;">
                                                                                <?= htmlspecialchars($l['narasi'] ?: ('Tautan Google Drive #' . ($lIdx + 1))) ?>
                                                                            </div>
                                                                            <div class="text-muted text-break small" style="font-size: 0.76rem;">
                                                                                <i class="fas fa-link text-secondary me-1 opacity-75"></i><?= htmlspecialchars($l['url']) ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2 flex-shrink-0 align-self-sm-center align-self-end">
                                                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold"
                                                                                data-bs-toggle="modal" 
                                                                                data-bs-target="#pdfPreviewModal" 
                                                                                data-is-link="1" 
                                                                                data-link-url="<?= htmlspecialchars($l['url']) ?>" 
                                                                                data-doc-title="<?= htmlspecialchars($l['narasi'] ?: $doc['nama_dokumen']) ?>" 
                                                                                data-doc-narasi="<?= htmlspecialchars($l['narasi'] ?? '') ?>" 
                                                                                data-doc-standar="<?= htmlspecialchars($subBadgesHtml) ?>" 
                                                                                data-can-access="1" 
                                                                                data-can-download="1"
                                                                                style="font-size: 0.78rem;">
                                                                            <i class="fas fa-eye"></i> Pratinjau
                                                                        </button>
                                                                        <a href="<?= htmlspecialchars($l['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.78rem;">
                                                                            <i class="fas fa-arrow-up-right-from-square"></i> Buka Link
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                                <?php if (!empty($subBadgesHtml)): ?>
                                                                    <div class="mt-2.5 pt-1 d-flex flex-wrap align-items-center gap-1.5">
                                                                        <?= $subBadgesHtml ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Jika tidak ada berkas maupun tautan sama sekali -->
                                            <?php if (empty($files) && empty($links)): ?>
                                                <div class="p-3 rounded-3 bg-light border text-center text-muted small mb-3">
                                                    <i class="fas fa-circle-info text-secondary me-1"></i> Belum ada berkas fisik maupun tautan yang dilampirkan pada dokumen ini.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer border-top pt-3">
                                            <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                                            <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="btn btn-primary bg-scu-blue border-0 rounded-pill px-4 btn-sm fw-semibold shadow-xs">
                                                <i class="fas fa-pen me-1.5"></i> Edit Dokumen
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Konfirmasi Hapus Soft -->
                            <div class="modal fade" id="delModal<?= $doc['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-body p-4 text-center">
                                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                                                <i class="fas fa-trash-can"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Hapus Dokumen Mutu?</h6>
                                            <p class="small text-muted mb-4" style="line-height: 1.45;">
                                                Dokumen <strong>"<?= htmlspecialchars($doc['nama_dokumen']) ?>"</strong> akan dinonaktifkan dan masuk ke daftar Dokumen Terhapus (dapat dipulihkan sewaktu-waktu).
                                            </p>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-light rounded-pill px-3 btn-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                                                <form action="<?= base_url('gpm/dokumen/delete/' . $doc['id']) ?>" method="POST" class="d-inline">
                                                    <button type="submit" class="btn btn-danger rounded-pill px-3 btn-sm fw-semibold shadow-xs">
                                                        Ya, Hapus Dokumen
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($documents)): ?>
            <!-- Footer Pagination Controls -->
            <div class="pt-3.5 mt-2 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3" id="gpmPaginationWrapper" style="border-color: #E2E8F0 !important;">
                <div class="small text-muted" id="gpmTableInfo">
                    Menghitung data dokumen...
                </div>
                <nav aria-label="Navigasi Halaman Dokumen" id="gpmTablePagination">
                    <!-- Tombol pagination digenerate oleh JavaScript -->
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
let gpmCurrentPage = 1;
let gpmPageSize = 10;

function changeGpmPageSize(size) {
    gpmPageSize = (size === 'all') ? 'all' : parseInt(size);
    gpmCurrentPage = 1;
    renderGpmPagination();
}

function renderGpmPagination() {
    const rows = document.querySelectorAll('.gpm-doc-row');
    const total = rows.length;
    if (total === 0) {
        const topInfo = document.getElementById('gpmTopTableInfo');
        if (topInfo) topInfo.innerHTML = '<i class="fas fa-layer-group text-primary me-1"></i> Total: <strong>0 Dokumen</strong>';
        const info = document.getElementById('gpmTableInfo');
        if (info) info.textContent = 'Menampilkan 0 dari 0 dokumen';
        const pag = document.getElementById('gpmTablePagination');
        if (pag) pag.innerHTML = '';
        return;
    }

    const totalPages = (gpmPageSize === 'all') ? 1 : Math.ceil(total / gpmPageSize);
    if (gpmCurrentPage > totalPages) gpmCurrentPage = totalPages;

    const startIdx = (gpmPageSize === 'all') ? 0 : (gpmCurrentPage - 1) * gpmPageSize;
    const endIdx = (gpmPageSize === 'all') ? total : startIdx + gpmPageSize;

    rows.forEach((row, idx) => {
        if (idx >= startIdx && idx < endIdx) {
            row.style.display = '';
            const noCell = row.querySelector('.gpm-row-no');
            if (noCell) noCell.textContent = idx + 1;
        } else {
            row.style.display = 'none';
        }
    });

    const startNum = (gpmPageSize === 'all') ? 1 : (gpmCurrentPage - 1) * gpmPageSize + 1;
    const endNum = (gpmPageSize === 'all') ? total : Math.min(gpmCurrentPage * gpmPageSize, total);
    
    const info = document.getElementById('gpmTableInfo');
    if (info) {
        info.innerHTML = `Menampilkan <strong>${startNum} - ${endNum}</strong> dari total <strong>${total}</strong> dokumen`;
    }

    const pag = document.getElementById('gpmTablePagination');
    if (!pag) return;
    if (totalPages <= 1) {
        pag.innerHTML = '';
        return;
    }

    let html = '<ul class="pagination pagination-sm mb-0">';
    html += `<li class="page-item ${gpmCurrentPage === 1 ? 'disabled' : ''}">
        <button type="button" class="page-link rounded-start-pill" onclick="goToGpmPage(${gpmCurrentPage - 1})" aria-label="Sebelumnya">&laquo;</button>
    </li>`;

    for (let p = 1; p <= totalPages; p++) {
        if (totalPages > 7) {
            if (p !== 1 && p !== totalPages && Math.abs(p - gpmCurrentPage) > 1) {
                if (p === 2 || p === totalPages - 1) {
                    html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                }
                continue;
            }
        }
        html += `<li class="page-item ${p === gpmCurrentPage ? 'active' : ''}">
            <button type="button" class="page-link" onclick="goToGpmPage(${p})">${p}</button>
        </li>`;
    }

    html += `<li class="page-item ${gpmCurrentPage === totalPages ? 'disabled' : ''}">
        <button type="button" class="page-link rounded-end-pill" onclick="goToGpmPage(${gpmCurrentPage + 1})" aria-label="Selanjutnya">&raquo;</button>
    </li>`;
    html += '</ul>';
    pag.innerHTML = html;
}

function goToGpmPage(page) {
    gpmCurrentPage = page;
    renderGpmPagination();
}

document.addEventListener('DOMContentLoaded', function() {
    renderGpmPagination();
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
