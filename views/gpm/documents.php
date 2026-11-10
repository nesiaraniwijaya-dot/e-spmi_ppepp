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

$pendingGpmCount = $gpmPendingReviewCount ?? count(array_filter($documents, fn($d) => in_array($d['status_review'] ?? '', ['menunggu_review_gpm', 'belum_direview'])));
$waitingLpmCount = $gpmWaitingLpmCount ?? count(array_filter($documents, fn($d) => ($d['status_review'] ?? '') === 'menunggu_review_lpm'));
$revisiCount     = $gpmPerluCount ?? count(array_filter($documents, fn($d) => in_array($d['status_review'] ?? '', ['revisi_gpm', 'revisi_lpm', 'perlu_perbaikan'])));
$sesuaiCount     = $gpmSesuaiCount ?? count(array_filter($documents, fn($d) => ($d['status_review'] ?? '') === 'sesuai'));

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
                    <i class="fas fa-clipboard-check text-warning"></i> Pusat Review Dokumen Mutu
                </span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">Review Dokumen Mutu PPEPP</h3>
            <p class="text-muted small mb-0" style="line-height: 1.5;">
                Telaah dan verifikasi dokumen PPEPP tingkat <strong>Fakultas</strong> dan seluruh <strong>Program Studi</strong> di bawah naungan <strong><?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?></strong> sebelum diteruskan ke antrean LPM.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('gpm/perbaikan') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.84rem;">
                <i class="fas fa-wrench"></i> Monitoring Revisi
                <?php if ($revisiCount > 0): ?>
                    <span class="badge bg-danger rounded-pill px-2 py-0.5 text-white" style="font-size:0.65rem;"><?= $revisiCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Quick Action Notification Grid: GPM Review Workflow -->
    <div class="row g-3 g-xl-4 mb-4 pb-1">
        <!-- 1. Antrean Review GPM -->
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 p-xl-4 h-100 d-flex flex-row align-items-center justify-content-between gap-3.5 transition-all" 
                 style="background: linear-gradient(135deg, #FEF3C7 0%, #FFFBEB 100%); border: 1px solid #FCD34D !important;">
                <div class="d-flex align-items-center gap-3.5" style="min-width: 0;">
                    <div class="rounded-4 bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 48px; height: 48px; min-width: 48px; font-size: 1.25rem;">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-center" style="min-width: 0;">
                        <div class="mb-1.5">
                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold shadow-2xs" style="font-size: 0.68rem; letter-spacing: 0.3px;">Menunggu GPM</span>
                        </div>
                        <div class="fw-bold text-dark-blue mb-0.5 text-truncate" style="font-size: 0.98rem; line-height: 1.3;">
                            <?= $pendingGpmCount ?> Dokumen Antrean
                        </div>
                        <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.35;">
                            Siap ditelaah oleh GPM
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('gpm/dokumen?status_review=menunggu_review_gpm') ?>" class="btn btn-warning text-dark btn-sm rounded-pill px-3.5 py-2 fw-bold text-nowrap shadow-xs d-inline-flex align-items-center gap-1.5 flex-shrink-0" style="font-size: 0.82rem;">
                    <span>Filter</span>
                    <i class="fas fa-filter"></i>
                </a>
            </div>
        </div>

        <!-- 2. Diteruskan ke LPM -->
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 p-xl-4 h-100 d-flex flex-row align-items-center justify-content-between gap-3.5 transition-all" 
                 style="background: linear-gradient(135deg, #F0F7FF 0%, #EFF6FF 100%); border: 1px solid #BFDBFE !important;">
                <div class="d-flex align-items-center gap-3.5" style="min-width: 0;">
                    <div class="rounded-4 bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 48px; height: 48px; min-width: 48px; font-size: 1.25rem;">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-center" style="min-width: 0;">
                        <div class="mb-1.5">
                            <span class="badge bg-primary rounded-pill px-2.5 py-1 fw-bold shadow-2xs" style="font-size: 0.68rem; letter-spacing: 0.3px;">Menunggu LPM</span>
                        </div>
                        <div class="fw-bold text-dark-blue mb-0.5 text-truncate" style="font-size: 0.98rem; line-height: 1.3;">
                            <?= $waitingLpmCount ?> Dokumen Diteruskan
                        </div>
                        <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.35;">
                            Antre review akhir LPM
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('gpm/dokumen?status_review=menunggu_review_lpm') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-2xs d-inline-flex align-items-center gap-1.5 flex-shrink-0" style="font-size: 0.82rem;">
                    <span>Filter</span>
                    <i class="fas fa-filter"></i>
                </a>
            </div>
        </div>

        <!-- 3. Monitoring Revisi -->
        <div class="col-lg-4 col-md-12 col-12">
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 p-xl-4 h-100 d-flex flex-row align-items-center justify-content-between gap-3.5 transition-all" 
                 style="background: linear-gradient(135deg, #FFF5F5 0%, #FEF2F2 100%); border: 1px solid #FECACA !important;">
                <div class="d-flex align-items-center gap-3.5" style="min-width: 0;">
                    <div class="rounded-4 bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 48px; height: 48px; min-width: 48px; font-size: 1.25rem;">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-center" style="min-width: 0;">
                        <div class="mb-1.5">
                            <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold shadow-2xs" style="font-size: 0.68rem; letter-spacing: 0.3px;">Perlu Revisi</span>
                        </div>
                        <div class="fw-bold text-dark-blue mb-0.5 text-truncate" style="font-size: 0.98rem; line-height: 1.3;">
                            <?= $revisiCount ?> Dokumen Revisi
                        </div>
                        <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.35;">
                            Perbaikan GPM / LPM
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('gpm/perbaikan') ?>" class="btn btn-danger btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-xs d-inline-flex align-items-center gap-1.5 flex-shrink-0" style="font-size: 0.82rem;">
                    <span>Pantau</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
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
                <button type="button" onclick="selectGpmSiklusTab('', this)" 
                   class="nav-link <?= empty($siklusFilter) ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> Semua (<?= $siklusCounts['all'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button type="button" onclick="selectGpmSiklusTab('penetapan', this)" 
                   class="nav-link tab-penetapan <?= $siklusFilter === 'penetapan' ? 'active' : '' ?>">
                    <i class="fas fa-file-signature"></i> Penetapan (<?= $siklusCounts['penetapan'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button type="button" onclick="selectGpmSiklusTab('pelaksanaan', this)" 
                   class="nav-link tab-pelaksanaan <?= $siklusFilter === 'pelaksanaan' ? 'active' : '' ?>">
                    <i class="fas fa-tasks"></i> Pelaksanaan (<?= $siklusCounts['pelaksanaan'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button type="button" onclick="selectGpmSiklusTab('evaluasi', this)" 
                   class="nav-link tab-evaluasi <?= $siklusFilter === 'evaluasi' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Evaluasi (<?= $siklusCounts['evaluasi'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button type="button" onclick="selectGpmSiklusTab('pengendalian', this)" 
                   class="nav-link tab-pengendalian <?= $siklusFilter === 'pengendalian' ? 'active' : '' ?>">
                    <i class="fas fa-sliders"></i> Pengendalian (<?= $siklusCounts['pengendalian'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button type="button" onclick="selectGpmSiklusTab('peningkatan', this)" 
                   class="nav-link tab-peningkatan <?= $siklusFilter === 'peningkatan' ? 'active' : '' ?>">
                    <i class="fas fa-arrow-trend-up"></i> Peningkatan (<?= $siklusCounts['peningkatan'] ?>)
                </button>
            </li>
        </ul>

        <!-- Filter Form Toolbar: Pencarian (Baris 1) & Filter Dropdown (Baris 2) -->
        <form id="gpmFilterForm" method="GET" action="<?= base_url('gpm/dokumen') ?>" onsubmit="event.preventDefault(); onGpmFilterChange(true);" class="mb-4">
            <input type="hidden" name="unit" id="gpmUnitFilterInput" value="<?= htmlspecialchars($unitFilter) ?>">
            <input type="hidden" name="siklus" id="gpmSiklusFilterInput" value="<?= htmlspecialchars($siklusFilter) ?>">

            <!-- Baris 1: Keyword Search (Full Width) -->
            <div class="mb-2.5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="fas fa-magnifying-glass"></i></span>
                    <input type="text" id="gpmSearchInput" name="search" class="form-control border-start-0 ps-1" placeholder="Cari nama dokumen, kode, prodi..." value="<?= htmlspecialchars($search) ?>" oninput="onGpmFilterChange()">
                    <button type="submit" class="btn btn-primary bg-scu-blue border-0 px-4">Cari</button>
                </div>
            </div>

            <!-- Baris 2: Dropdown Filter & Reset -->
            <div class="row g-2.5 align-items-center">
                <!-- Filter Bidang -->
                <div class="col-lg-3 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-bookmark"></i></span>
                        <select name="bidang_id" id="gpmBidangSelect" class="form-select border-start-0" onchange="onGpmFilterChange(true)">
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
                <div class="col-lg-4 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-shield"></i></span>
                        <select name="status_review" id="gpmStatusSelect" class="form-select border-start-0" onchange="onGpmFilterChange(true)">
                            <option value="">Semua Status Review</option>
                            <option value="menunggu_review_gpm" <?= in_array($statusFilter, ['menunggu_review_gpm', 'belum_direview']) ? 'selected' : '' ?>>Menunggu Telaah GPM</option>
                            <option value="revisi_gpm" <?= $statusFilter === 'revisi_gpm' ? 'selected' : '' ?>>Perlu Revisi (GPM)</option>
                            <option value="menunggu_review_lpm" <?= $statusFilter === 'menunggu_review_lpm' ? 'selected' : '' ?>>Diteruskan ke LPM</option>
                            <option value="revisi_lpm" <?= in_array($statusFilter, ['revisi_lpm', 'perlu_perbaikan']) ? 'selected' : '' ?>>Perlu Revisi (LPM)</option>
                            <option value="sesuai" <?= $statusFilter === 'sesuai' ? 'selected' : '' ?>>Sesuai Standar Mutu</option>
                            <option value="sudah_diperbaiki" <?= $statusFilter === 'sudah_diperbaiki' ? 'selected' : '' ?>>Sudah Diperbaiki</option>
                            <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>Draf Dokumen</option>
                        </select>
                    </div>
                </div>

                <!-- Filter Tahun Akademik -->
                <div class="col-lg-3 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-calendar-alt"></i></span>
                        <select name="tahun_akademik" id="gpmTahunSelect" class="form-select border-start-0" onchange="onGpmFilterChange(true)">
                            <option value="">Semua Tahun Akademik</option>
                            <?php foreach (get_distinct_tahun_akademik() as $ta): ?>
                                <option value="<?= htmlspecialchars($ta) ?>" <?= ($tahunFilter ?? '') === $ta ? 'selected' : '' ?>>
                                    Tahun Akademik <?= htmlspecialchars($ta) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Reset Button -->
                <div class="col-lg-2 col-md-6 ms-auto text-end">
                    <button type="button" onclick="resetGpmFilters()" class="btn btn-outline-secondary btn-sm rounded-pill w-100 fw-semibold" style="font-size: 0.78rem;" title="Reset Filter">
                        <i class="fas fa-rotate-left me-1"></i> Reset Filter
                    </button>
                </div>
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
                        <th class="py-3" style="min-width: 180px;">Status &amp; Tahap Review</th>
                        <th class="text-end pe-3 py-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" id="gpmDocTableBody">
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                                        <i class="fas fa-folder-open text-secondary"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Ditemukan</h6>
                                    <p class="small text-muted mb-0">Belum ada dokumen mutu yang diunggah atau cocok dengan filter.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = $startNum; 
                        require ROOT_PATH . '/views/gpm/documents_rows.php'; 
                        ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination Controls -->
        <div class="pt-3.5 mt-2 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3" id="gpmPaginationWrapper" style="border-color: #E2E8F0 !important; display: <?= empty($documents) ? 'none !important' : 'flex' ?>;">
            <div class="small text-muted" id="gpmTableInfo">
                Menampilkan <span class="fw-bold text-dark" id="gpmStartNum"><?= $startNum ?></span> - <span class="fw-bold text-dark" id="gpmEndNum"><?= $endNum ?></span> dari <span class="fw-bold text-dark" id="gpmTotalFiltered"><?= number_format($totalFiltered, 0, ',', '.') ?></span> Dokumen
            </div>
            <div class="table-pagination-control">
                <div class="table-perpage-wrap">
                    <label for="gpmPageSizeSelect" class="table-perpage-label">Ukuran Halaman:</label>
                    <select id="gpmPageSizeSelect" class="form-select table-perpage-select shadow-none" onchange="changeGpmPageSize(this.value)">
                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10 data</option>
                        <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20 data</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 data</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 data</option>
                    </select>
                </div>
                <nav aria-label="Navigasi Halaman Dokumen" id="gpmTablePagination">
                    <!-- Tombol pagination digenerate oleh JavaScript -->
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
let gpmCurrentPage = <?= (int)$page ?>;
let gpmPerPage = <?= (int)$perPage ?>;
let gpmTotalPages = <?= (int)$totalPages ?>;
let gpmSearchTimer = null;

function selectGpmSiklusTab(siklus, btn) {
    const siklusInput = document.getElementById('gpmSiklusFilterInput');
    if (siklusInput) siklusInput.value = siklus;
    const tabs = document.querySelectorAll('#gpmPpeppTabs .nav-link');
    tabs.forEach(t => t.classList.remove('active'));
    if (btn) btn.classList.add('active');
    fetchGpmData(1);
}

function onGpmFilterChange(immediate = false) {
    clearTimeout(gpmSearchTimer);
    if (immediate) {
        fetchGpmData(1);
    } else {
        gpmSearchTimer = setTimeout(() => {
            fetchGpmData(1);
        }, 250);
    }
}

function resetGpmFilters() {
    const sInput = document.getElementById('gpmSearchInput');
    if (sInput) sInput.value = '';
    const bSel = document.getElementById('gpmBidangSelect');
    if (bSel) bSel.value = '';
    const stSel = document.getElementById('gpmStatusSelect');
    if (stSel) stSel.value = '';
    const thSel = document.getElementById('gpmTahunSelect');
    if (thSel) thSel.value = '';
    const siklusInput = document.getElementById('gpmSiklusFilterInput');
    if (siklusInput) siklusInput.value = '';

    const tabs = document.querySelectorAll('#gpmPpeppTabs .nav-link');
    tabs.forEach(t => t.classList.remove('active'));
    if (tabs[0]) tabs[0].classList.add('active');

    fetchGpmData(1);
}

function changeGpmPageSize(size) {
    gpmPerPage = parseInt(size, 10) || 50;
    fetchGpmData(1);
}

function fetchGpmData(page) {
    gpmCurrentPage = page || 1;
    const form = document.getElementById('gpmFilterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.set('ajax', '1');
    params.set('page', gpmCurrentPage);
    params.set('per_page', gpmPerPage);

    const searchVal = document.getElementById('gpmSearchInput') ? document.getElementById('gpmSearchInput').value.trim() : '';
    params.set('search', searchVal);
    params.set('q', searchVal);

    const tbody = document.getElementById('gpmDocTableBody');
    if (tbody) tbody.style.opacity = '0.5';

    fetch('<?= base_url("gpm/dokumen") ?>?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (tbody) {
            tbody.style.opacity = '1';
            tbody.innerHTML = data.html || '';
        }

        gpmTotalPages = data.total_pages || 1;
        gpmCurrentPage = data.page || 1;

        const startEl = document.getElementById('gpmStartNum');
        const endEl = document.getElementById('gpmEndNum');
        const totalEl = document.getElementById('gpmTotalFiltered');
        const topInfo = document.getElementById('gpmTopTableInfo');
        const pagWrapper = document.getElementById('gpmPaginationWrapper');

        if (startEl) startEl.textContent = data.start_num || 0;
        if (endEl) endEl.textContent = data.end_num || 0;
        if (totalEl) totalEl.textContent = Number(data.total || 0).toLocaleString('id-ID');
        if (topInfo) topInfo.innerHTML = '<i class="fas fa-layer-group text-primary me-1"></i> Menampilkan total <strong>' + Number(data.total || 0).toLocaleString('id-ID') + ' Dokumen Mutu</strong>';

        if (pagWrapper) {
            pagWrapper.style.setProperty('display', (data.total > 0) ? 'flex' : 'none', 'important');
        }

        renderGpmPagination(gpmTotalPages, gpmCurrentPage);

        // Smooth scroll ke tabel saat ganti slide halaman
        const tableEl = document.getElementById('gpmDocumentsTable');
        if (tableEl && page > 1) {
            tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    })
    .catch(err => {
        console.error("Error fetching gpm data:", err);
        if (tbody) tbody.style.opacity = '1';
    });
}

function renderGpmPagination(totalPages, currentPage) {
    const nav = document.getElementById('gpmTablePagination');
    if (!nav) return;
    nav.innerHTML = '';

    if (totalPages <= 1) return;

    let html = '<ul class="pagination-segmented mb-0">';
    html += '<li class="page-item ' + (currentPage <= 1 ? 'disabled' : '') + '">';
    html += '<button type="button" class="page-link" ' + (currentPage > 1 ? 'onclick="fetchGpmData(' + (currentPage - 1) + ')\"' : 'disabled') + ' aria-label="Sebelumnya"><i class="fas fa-chevron-left" style="font-size: 0.72rem;"></i></button>';
    html += '</li>';

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        html += '<li class="page-item"><button type="button" class="page-link" onclick="fetchGpmData(1)">1</button></li>';
        if (startPage > 2) {
            html += '<li class="page-item ellipsis disabled"><span class="page-link dots">&hellip;</span></li>';
        }
    }

    for (let p = startPage; p <= endPage; p++) {
        html += '<li class="page-item ' + (p === currentPage ? 'active' : '') + '">';
        html += '<button type="button" class="page-link" onclick="fetchGpmData(' + p + ')">' + p + '</button>';
        html += '</li>';
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += '<li class="page-item ellipsis disabled"><span class="page-link dots">&hellip;</span></li>';
        }
        html += '<li class="page-item"><button type="button" class="page-link" onclick="fetchGpmData(' + totalPages + ')">' + totalPages + '</button></li>';
    }

    html += '<li class="page-item ' + (currentPage >= totalPages ? 'disabled' : '') + '">';
    html += '<button type="button" class="page-link" ' + (currentPage < totalPages ? 'onclick="fetchGpmData(' + (currentPage + 1) + ')\"' : 'disabled') + ' aria-label="Selanjutnya"><i class="fas fa-chevron-right" style="font-size: 0.72rem;"></i></button>';
    html += '</li>';
    html += '</ul>';

    nav.innerHTML = html;
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
