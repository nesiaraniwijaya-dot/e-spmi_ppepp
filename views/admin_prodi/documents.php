<?php
/**
 * Admin Prodi: Daftar Dokumen PPEPP
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-blue mb-1">Daftar Dokumen PPEPP</h3>
            <p class="text-muted small mb-0">Kelola dokumen Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan mutu program studi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('prodi/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.84rem;">
                <i class="fas fa-plus"></i> Unggah Dokumen Baru
            </a>
        </div>
    </div>

    <?php 
    $perluPerbaikanDocs = $perluPerbaikanDocs ?? array_values(array_filter($documents ?? [], fn($d) => in_array($d['status_review'] ?? '', ['revisi_gpm', 'perlu_perbaikan'])));
    $sudahDiperbaikiDocs = $sudahDiperbaikiDocs ?? array_values(array_filter($documents ?? [], fn($d) => in_array($d['status_review'] ?? '', ['sudah_diperbaiki', 'menunggu_review_lpm', 'menunggu_review_gpm', 'revisi_lpm'])));
    $perluCount = isset($perluCount) ? (int)$perluCount : count($perluPerbaikanDocs);
    $sudahCount = isset($sudahCount) ? (int)$sudahCount : count($sudahDiperbaikiDocs);
    $draftCount = $draftCount ?? 0;
    
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
                                    Catatan evaluasi dari GPM Fakultas
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('prodi/perbaikan') ?>" class="btn btn-danger btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-xs d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
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
                                    Verifikasi ulang GPM / LPM
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('prodi/perbaikan') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-2xs d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
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
                                    Belum diajukan ke GPM
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('prodi/draft') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-2xs d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
                            <span>Buka Draf</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
    if (!isset($ppeppCounts) || !is_array($ppeppCounts)) {
        $ppeppCounts = [
            'all' => 0,
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0,
        ];
    }
    ?>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        
        <!-- 5 Siklus PPEPP Tabs Header -->
        <ul class="nav ppepp-nav-tabs mb-4" id="prodiPpeppTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" type="button" data-siklus-tab="" onclick="selectProdiSiklusTab(this, '')">
                    <i class="fas fa-layer-group"></i> Semua <span class="tab-cycle-count">(<?= number_format($ppeppCounts['all'] ?? 0, 0, ',', '.') ?>)</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-penetapan" type="button" data-siklus-tab="penetapan" onclick="selectProdiSiklusTab(this, 'penetapan')">
                    <i class="fas fa-file-signature"></i> Penetapan <span class="tab-cycle-count">(<?= number_format($ppeppCounts['penetapan'] ?? 0, 0, ',', '.') ?>)</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-pelaksanaan" type="button" data-siklus-tab="pelaksanaan" onclick="selectProdiSiklusTab(this, 'pelaksanaan')">
                    <i class="fas fa-person-chalkboard"></i> Pelaksanaan <span class="tab-cycle-count">(<?= number_format($ppeppCounts['pelaksanaan'] ?? 0, 0, ',', '.') ?>)</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-evaluasi" type="button" data-siklus-tab="evaluasi" onclick="selectProdiSiklusTab(this, 'evaluasi')">
                    <i class="fas fa-magnifying-glass-chart"></i> Evaluasi <span class="tab-cycle-count">(<?= number_format($ppeppCounts['evaluasi'] ?? 0, 0, ',', '.') ?>)</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-pengendalian" type="button" data-siklus-tab="pengendalian" onclick="selectProdiSiklusTab(this, 'pengendalian')">
                    <i class="fas fa-sliders"></i> Pengendalian <span class="tab-cycle-count">(<?= number_format($ppeppCounts['pengendalian'] ?? 0, 0, ',', '.') ?>)</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-peningkatan" type="button" data-siklus-tab="peningkatan" onclick="selectProdiSiklusTab(this, 'peningkatan')">
                    <i class="fas fa-arrow-trend-up"></i> Peningkatan <span class="tab-cycle-count">(<?= number_format($ppeppCounts['peningkatan'] ?? 0, 0, ',', '.') ?>)</span>
                </button>
            </li>
        </ul>

        <!-- Filter Toolbar: Pencarian (Baris 1) & Filter Dropdown (Baris 2) -->
        <div class="mb-4">
            <!-- Baris 1: Pencarian Cepat & Pengaturan Jumlah Data -->
            <div class="row g-2.5 mb-2.5 align-items-center">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="fas fa-search"></i></span>
                        <input type="text" id="tableSearchInput" class="form-control bg-light border-start-0 ps-1" placeholder="Cari nama dokumen, nomor SK..." oninput="onProdiFilterChange()">
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    <label for="tablePerPageSelect" class="small text-muted text-nowrap mb-0 d-none d-sm-inline" style="font-size: 0.78rem;">Tampilkan:</label>
                    <select id="tablePerPageSelect" class="form-select bg-light" style="min-width: 95px; width: auto; font-size: 0.82rem;">
                        <option value="10" selected>10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                        <option value="100">100 data</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
            </div>

            <!-- Baris 2: Filter Bidang & Tahun Akademik -->
            <div class="row g-2.5 align-items-center">
                <div class="col-md-6 col-sm-6">
                    <select id="tableBidangFilter" class="form-select bg-light">
                        <option value="">Semua Bidang</option>
                        <?php foreach ($bidangList as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-sm-6">
                    <?php $distinctTahunList = get_distinct_tahun_akademik(); ?>
                    <select id="tableTahunFilter" class="form-select bg-light">
                        <option value="">Semua Tahun Akademik</option>
                        <?php foreach ($distinctTahunList as $ta): ?>
                            <option value="<?= htmlspecialchars($ta) ?>">Tahun Akademik <?= htmlspecialchars($ta) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="prodiDocumentsTable" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Nama & Nomor Dokumen</th>
                        <th style="width: 140px;">Siklus PPEPP</th>
                        <th>Bidang &amp; Standar</th>
                        <th>Berkas</th>
                        <th style="min-width: 220px;">Status Review LPM</th>
                        <th class="text-center text-nowrap" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="prodiDocTableBody">
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold">Tidak Ada Dokumen yang Sesuai</h6>
                                <p class="small mb-0">Coba ubah kata kunci pencarian atau filter yang dipilih.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = $startNum; 
                        require ROOT_PATH . '/views/admin_prodi/documents_rows.php'; 
                        ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top mt-3" id="prodiPaginationWrapper">
            <div id="tableInfo" class="small text-muted">
                Menampilkan <span class="fw-bold text-dark" id="prodiStartNum"><?= $startNum ?></span> - <span class="fw-bold text-dark" id="prodiEndNum"><?= $endNum ?></span> dari <span class="fw-bold text-dark" id="prodiTotalFiltered"><?= number_format($totalFiltered, 0, ',', '.') ?></span> Dokumen
            </div>
            <div class="table-pagination-control">
                <div class="table-perpage-wrap">
                    <label for="prodiPerPageSelect" class="table-perpage-label">Ukuran Halaman:</label>
                    <select class="form-select table-perpage-select shadow-none" id="prodiPerPageSelect" onchange="changeProdiPerPage(this.value)">
                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10 data</option>
                        <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20 data</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 data</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 data</option>
                    </select>
                </div>
                <nav aria-label="Prodi Pagination">
                    <ul class="pagination-segmented mb-0" id="prodiPaginationNav">
                        <!-- Populated dynamically -->
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</div>

<!-- Modal Detail Catatan LPM untuk Admin Prodi -->
<div class="modal fade" id="prodiViewNoteModal" tabindex="-1" aria-labelledby="prodiViewNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white py-3 px-4" id="prodiViewNoteModalHeader">
                <h5 class="modal-title fw-bold fs-6" id="prodiViewNoteModalLabel">
                    <i class="fas fa-triangle-exclamation me-2"></i> Catatan Perbaikan Dokumen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small text-muted mb-1">Nama Dokumen Mutu:</label>
                    <div class="fw-bold text-dark-blue p-2.5 rounded-3 bg-light border" id="prodiNoteDocTitle">-</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted mb-1">Status Review:</label>
                    <div id="prodiNoteStatusBadge">-</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-danger mb-1" id="prodiNotePoinLabel">Poin-Poin Perbaikan yang Diminta Evaluator:</label>
                    <div class="p-3 rounded-3 bg-white border border-danger border-opacity-25 text-dark" style="font-size: 0.85rem; line-height: 1.5; white-space: pre-wrap;" id="prodiNoteContent">-</div>
                </div>
                <div class="small text-muted d-flex align-items-center gap-2" id="prodiNoteMeta">
                    <i class="fas fa-shield-halved text-purple"></i> <span id="prodiNoteReviewer">Gugus Penjaminan Mutu (GPM) Fakultas</span> &bull; <span id="prodiNoteDate">-</span>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="prodiNoteEditBtn" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fas fa-wrench me-1"></i> Perbaiki Dokumen Ini
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let isReviewStatusFilterActive = false;
let currentProdiPage = <?= (int)$page ?>;
let currentProdiPerPage = <?= (int)$perPage ?>;
let prodiTotalPages = <?= (int)$totalPages ?>;
let currentProdiSiklus = '';
let currentReviewStatus = '';
let prodiSearchTimer = null;

function toggleFilterReviewStatus(status) {
    isReviewStatusFilterActive = !isReviewStatusFilterActive;
    const btn = document.getElementById('btnFilterRevisiTable');
    const txt = document.getElementById('btnFilterRevisiText');

    if (isReviewStatusFilterActive) {
        if (btn) {
            btn.classList.remove('btn-outline-danger');
            btn.classList.add('btn-danger', 'text-white');
        }
        if (txt) txt.textContent = 'Menampilkan Dokumen Revisi Saja (Reset Filter)';
        currentReviewStatus = 'revisi';
    } else {
        if (btn) {
            btn.classList.remove('btn-danger', 'text-white');
            btn.classList.add('btn-outline-danger');
        }
        if (txt) txt.textContent = 'Filter Tabel: Dokumen Revisi Saja';
        currentReviewStatus = '';
    }
    fetchProdiData(1);
}

function selectProdiSiklusTab(btn, siklus) {
    const tabButtons = document.querySelectorAll('#prodiPpeppTabs .nav-link');
    tabButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentProdiSiklus = siklus || '';
    fetchProdiData(1);
}

function changeProdiPerPage(val) {
    currentProdiPerPage = parseInt(val, 10) || 50;
    const topSel = document.getElementById('tablePerPageSelect');
    if (topSel && topSel.value != val) topSel.value = val;
    const btmSel = document.getElementById('prodiPerPageSelect');
    if (btmSel && btmSel.value != val) btmSel.value = val;
    fetchProdiData(1);
}

function onProdiFilterChange() {
    clearTimeout(prodiSearchTimer);
    prodiSearchTimer = setTimeout(() => {
        fetchProdiData(1);
    }, 250);
}

function fetchProdiData(page) {
    currentProdiPage = page || 1;
    const searchVal = document.getElementById('tableSearchInput') ? document.getElementById('tableSearchInput').value.trim() : '';
    const bidangVal = document.getElementById('tableBidangFilter') ? document.getElementById('tableBidangFilter').value : '';
    const tahunVal = document.getElementById('tableTahunFilter') ? document.getElementById('tableTahunFilter').value.trim() : '';

    const tbody = document.getElementById('prodiDocTableBody');
    if (tbody) tbody.style.opacity = '0.5';

    const params = new URLSearchParams({
        ajax: '1',
        page: currentProdiPage,
        per_page: currentProdiPerPage,
        siklus: currentProdiSiklus,
        bidang_id: bidangVal,
        tahun_akademik: tahunVal,
        status_review: currentReviewStatus,
        search: searchVal,
        q: searchVal
    });

    fetch('<?= base_url("prodi/dokumen") ?>?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (tbody) {
            tbody.style.opacity = '1';
            tbody.innerHTML = data.html || '';
        }

        prodiTotalPages = data.total_pages || 1;
        currentProdiPage = data.page || 1;

        const startEl = document.getElementById('prodiStartNum');
        const endEl = document.getElementById('prodiEndNum');
        const totalEl = document.getElementById('prodiTotalFiltered');
        const pagWrapper = document.getElementById('prodiPaginationWrapper');

        if (startEl) startEl.textContent = data.start_num || 0;
        if (endEl) endEl.textContent = data.end_num || 0;
        if (totalEl) totalEl.textContent = Number(data.total || 0).toLocaleString('id-ID');

        if (pagWrapper) {
            pagWrapper.style.setProperty('display', (data.total > 0) ? 'flex' : 'none', 'important');
        }

        if (data.cycle_counts && typeof window.updatePpeppCycleTabCounts === 'function') {
            window.updatePpeppCycleTabCounts('#prodiPpeppTabs', data.cycle_counts, data.total_all_cycles);
        }

        renderProdiPagination(prodiTotalPages, currentProdiPage);

        // Smooth scroll ke atas tabel saat navigasi halaman
        const tableEl = document.getElementById('prodiDocumentsTable');
        if (tableEl && page > 1) {
            tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    })
    .catch(err => {
        console.error("Error fetching prodi data:", err);
        if (tbody) tbody.style.opacity = '1';
    });
}

function renderProdiPagination(totalPages, currentPage) {
    const nav = document.getElementById('prodiPaginationNav');
    if (!nav) return;
    nav.innerHTML = '';

    if (totalPages <= 1) return;

    // Prev Button
    const prevLi = document.createElement('li');
    prevLi.className = 'page-item ' + (currentPage <= 1 ? 'disabled' : '');
    prevLi.innerHTML = '<a class="page-link" href="javascript:void(0)" aria-label="Previous"><i class="fas fa-chevron-left" style="font-size: 0.72rem;"></i></a>';
    if (currentPage > 1) {
        prevLi.querySelector('a').onclick = () => fetchProdiData(currentPage - 1);
    }
    nav.appendChild(prevLi);

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        const firstLi = document.createElement('li');
        firstLi.className = 'page-item';
        firstLi.innerHTML = '<a class="page-link" href="javascript:void(0)">1</a>';
        firstLi.querySelector('a').onclick = () => fetchProdiData(1);
        nav.appendChild(firstLi);

        if (startPage > 2) {
            const dotsLi = document.createElement('li');
            dotsLi.className = 'page-item ellipsis disabled';
            dotsLi.innerHTML = '<span class="page-link dots">&hellip;</span>';
            nav.appendChild(dotsLi);
        }
    }

    for (let p = startPage; p <= endPage; p++) {
        const li = document.createElement('li');
        li.className = 'page-item ' + (p === currentPage ? 'active' : '');
        li.innerHTML = '<a class="page-link" href="javascript:void(0)">' + p + '</a>';
        if (p !== currentPage) {
            li.querySelector('a').onclick = ((pageNum) => () => fetchProdiData(pageNum))(p);
        }
        nav.appendChild(li);
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const dotsLi = document.createElement('li');
            dotsLi.className = 'page-item ellipsis disabled';
            dotsLi.innerHTML = '<span class="page-link dots">&hellip;</span>';
            nav.appendChild(dotsLi);
        }
        const lastLi = document.createElement('li');
        lastLi.className = 'page-item';
        lastLi.innerHTML = '<a class="page-link" href="javascript:void(0)">' + totalPages + '</a>';
        lastLi.querySelector('a').onclick = () => fetchProdiData(totalPages);
        nav.appendChild(lastLi);
    }

    // Next Button
    const nextLi = document.createElement('li');
    nextLi.className = 'page-item ' + (currentPage >= totalPages ? 'disabled' : '');
    nextLi.innerHTML = '<a class="page-link" href="javascript:void(0)" aria-label="Next"><i class="fas fa-chevron-right" style="font-size: 0.72rem;"></i></a>';
    if (currentPage < totalPages) {
        nextLi.querySelector('a').onclick = () => fetchProdiData(currentPage + 1);
    }
    nav.appendChild(nextLi);
}

// Attach event listeners to input search & dropdowns
document.addEventListener('DOMContentLoaded', function() {
    renderProdiPagination(prodiTotalPages, currentProdiPage);

    const sIn = document.getElementById('tableSearchInput');
    if (sIn) sIn.addEventListener('keyup', onProdiFilterChange);

    const bFilter = document.getElementById('tableBidangFilter');
    if (bFilter) bFilter.addEventListener('change', onProdiFilterChange);

    const tFilter = document.getElementById('tableTahunFilter');
    if (tFilter) tFilter.addEventListener('change', onProdiFilterChange);

    // Modal Catatan Perbaikan
    const prodiNoteModal = document.getElementById('prodiViewNoteModal');
    if (prodiNoteModal) {
        prodiNoteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const title = button.getAttribute('data-title') || '-';
            const notes = button.getAttribute('data-notes') || 'Tidak ada catatan perbaikan khusus.';
            const reviewer = button.getAttribute('data-reviewer') || 'Gugus Penjaminan Mutu (GPM) Fakultas';
            const date = button.getAttribute('data-date') || '-';
            const status = button.getAttribute('data-status') || '';
            const editUrl = button.getAttribute('data-edit-url') || '#';

            const isGpm = (status === 'revisi_gpm' || (reviewer && reviewer.includes('GPM')));
            const titleEl = document.getElementById('prodiViewNoteModalLabel');
            if (titleEl) {
                titleEl.innerHTML = '<i class="fas fa-comment-dots me-2"></i> ' + (isGpm ? 'Catatan Perbaikan Dokumen dari GPM Fakultas' : 'Catatan Perbaikan Dokumen dari LPM Universitas');
            }
            const labelPoin = document.getElementById('prodiNotePoinLabel');
            if (labelPoin) {
                labelPoin.textContent = isGpm ? 'Catatan & Rekomendasi Perbaikan oleh GPM Fakultas:' : 'Poin-Poin Perbaikan yang Diminta Admin LPM:';
            }

            document.getElementById('prodiNoteDocTitle').textContent = title;
            document.getElementById('prodiNoteContent').textContent = notes;
            document.getElementById('prodiNoteReviewer').textContent = (reviewer && reviewer !== 'Pusat Penjaminan Mutu LPM') ? reviewer : 'Gugus Penjaminan Mutu (GPM) Fakultas';
            document.getElementById('prodiNoteDate').textContent = date || '-';
            document.getElementById('prodiNoteEditBtn').setAttribute('href', editUrl);

            const badgeEl = document.getElementById('prodiNoteStatusBadge');
            const editBtn = document.getElementById('prodiNoteEditBtn');
            if (status === 'revisi_gpm') {
                badgeEl.innerHTML = '<span class="badge rounded-pill" style="background:#F3E8FF; color:#7E22CE; border:1px solid #E9D5FF; font-size:0.75rem;"><i class="fas fa-shield-halved text-purple me-1"></i> Perlu Revisi (GPM Fakultas)</span>';
                if (editBtn) editBtn.style.display = 'inline-flex';
            } else if (status === 'perlu_perbaikan' || status === 'revisi_lpm') {
                badgeEl.innerHTML = '<span class="badge rounded-pill" style="background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; font-size:0.75rem;"><i class="fas fa-triangle-exclamation text-danger me-1"></i> Perlu Perbaikan (LPM)</span>';
                if (editBtn) editBtn.style.display = 'inline-flex';
            } else if (status === 'sudah_diperbaiki') {
                badgeEl.innerHTML = '<span class="badge rounded-pill" style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD; font-size:0.75rem;"><i class="fas fa-clock text-info me-1"></i> Telah Diperbaiki (Menunggu Review)</span>';
                if (editBtn) editBtn.style.display = 'none';
            } else {
                badgeEl.innerHTML = '<span class="badge bg-secondary">' + status + '</span>';
                if (editBtn) editBtn.style.display = 'none';
            }
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
