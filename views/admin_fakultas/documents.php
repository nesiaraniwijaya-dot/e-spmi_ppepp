<?php
/**
 * Admin Fakultas: Daftar Dokumen PPEPP
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$totalSemua = $cycleCounts['semua'] ?? 0;
$countPenetapan = $cycleCounts['penetapan'] ?? 0;
$countPelaksanaan = $cycleCounts['pelaksanaan'] ?? 0;
$countEvaluasi = $cycleCounts['evaluasi'] ?? 0;
$countPengendalian = $cycleCounts['pengendalian'] ?? 0;
$countPeningkatan = $cycleCounts['peningkatan'] ?? 0;
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark-blue text-warning px-3 py-1">Fakultas <?= htmlspecialchars($fakultas['kode_fakultas']) ?></span>
                <span class="badge bg-light text-secondary border">SPMI PPEPP Tingkat Fakultas</span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Daftar Dokumen PPEPP Fakultas</h3>
            <p class="text-muted small mb-0">Kelola dokumen Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan mutu tingkat <?= htmlspecialchars($fakultas['nama_fakultas']) ?>.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('fakultas/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.84rem;">
                <i class="fas fa-plus"></i>
                <span>Unggah Dokumen Baru</span>
            </a>
        </div>
    </div>

    <?php 
    $fakultasPerluCount = $perluPerbaikanCount ?? count($perluPerbaikanDocs ?? []);
    $fakultasSudahCount = $sudahDiperbaikiCount ?? 0;
    $fakultasDraftCount = $draftCount ?? 0;
    
    $activeNotifCount = ($fakultasPerluCount > 0 ? 1 : 0) + ($fakultasSudahCount > 0 ? 1 : 0) + ($fakultasDraftCount > 0 ? 1 : 0);
    $colClass = $activeNotifCount === 3 ? 'col-lg-4 col-md-6 col-12' : ($activeNotifCount === 2 ? 'col-md-6 col-12' : 'col-12');
    ?>

    <?php if ($activeNotifCount > 0): ?>
        <!-- Quick Action Notification Grid: Bernafas Lega, Rapi, & Proporsional -->
        <div class="row g-3 g-xl-4 mb-4 pb-1">
            <!-- 1. Perlu Tindakan Revisi LPM -->
            <?php if ($fakultasPerluCount > 0): ?>
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
                                    <?= $fakultasPerluCount ?> Dokumen Revisi
                                </div>
                                <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.35;">
                                    Catatan evaluasi Fakultas dari LPM
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('fakultas/perbaikan') ?>" class="btn btn-danger btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-xs d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
                            <span>Perbaiki</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 2. Menunggu Antrean Review LPM -->
            <?php if ($fakultasSudahCount > 0): ?>
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
                                    <?= $fakultasSudahCount ?> Dokumen Antrean
                                </div>
                                <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.35;">
                                    Verifikasi ulang Tim LPM
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('fakultas/perbaikan') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-2xs d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
                            <span>Pantau Revisi</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 3. Draf Dokumen Belum Diajukan -->
            <?php if ($fakultasDraftCount > 0): ?>
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
                                    <?= $fakultasDraftCount ?> Draf Dokumen
                                </div>
                                <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.35;">
                                    Belum diajukan ke LPM
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('fakultas/draft') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 fw-semibold text-nowrap shadow-2xs d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
                            <span>Buka Draf</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">

        <!-- 5 Siklus PPEPP Tabs Header -->
        <ul class="nav ppepp-nav-tabs mb-4" id="fakultasPpeppTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" type="button" onclick="selectFakultasSiklusTab('', this)">
                    <i class="fas fa-layer-group"></i> Semua (<?= $totalSemua ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-penetapan" type="button" onclick="selectFakultasSiklusTab('penetapan', this)">
                    <i class="fas fa-file-signature"></i> Penetapan (<?= $countPenetapan ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-pelaksanaan" type="button" onclick="selectFakultasSiklusTab('pelaksanaan', this)">
                    <i class="fas fa-person-chalkboard"></i> Pelaksanaan (<?= $countPelaksanaan ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-evaluasi" type="button" onclick="selectFakultasSiklusTab('evaluasi', this)">
                    <i class="fas fa-magnifying-glass-chart"></i> Evaluasi (<?= $countEvaluasi ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-pengendalian" type="button" onclick="selectFakultasSiklusTab('pengendalian', this)">
                    <i class="fas fa-sliders"></i> Pengendalian (<?= $countPengendalian ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-peningkatan" type="button" onclick="selectFakultasSiklusTab('peningkatan', this)">
                    <i class="fas fa-arrow-trend-up"></i> Peningkatan (<?= $countPeningkatan ?>)
                </button>
            </li>
        </ul>

        <!-- Filter Toolbar: Pencarian (Baris 1) & Filter Dropdown (Baris 2) -->
        <div class="mb-4">
            <!-- Baris 1: Pencarian Cepat & Pengaturan Jumlah Data -->
            <div class="row g-2.5 mb-2.5 align-items-center">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted px-3"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0 ps-1" id="tableSearchInput" placeholder="Cari nama dokumen, nomor SK, deskripsi..." oninput="onFakultasFilterChange()">
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    <label for="topFakultasPerPageSelect" class="small text-muted text-nowrap mb-0 d-none d-sm-inline" style="font-size: 0.78rem;">Tampilkan:</label>
                    <select class="form-select form-select-sm" id="topFakultasPerPageSelect" style="min-width: 95px; width: auto;" onchange="changeFakultasPerPage(this.value)">
                        <option value="10">10 data</option>
                        <option value="20">20 data</option>
                        <option value="50" selected>50 data</option>
                        <option value="100">100 data</option>
                    </select>
                </div>
            </div>

            <!-- Baris 2: Filter Bidang & Tahun Akademik -->
            <div class="row g-2.5 align-items-center">
                <div class="col-md-4 col-sm-6">
                    <select class="form-select form-select-sm" id="tableUnitFilter" onchange="onFakultasFilterChange()">
                        <option value="">Semua Unit (Fakultas &amp; Seluruh Prodi)</option>
                        <option value="fakultas" <?= ($unitFilter ?? '') === 'fakultas' ? 'selected' : '' ?>>Tingkat Fakultas Saja</option>
                        <?php if (!empty($prodiUnderFakultas)): ?>
                            <optgroup label="Program Studi:">
                                <?php foreach ($prodiUnderFakultas as $pu): ?>
                                    <option value="<?= $pu['id'] ?>" <?= ($unitFilter ?? '') == $pu['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pu['nama_prodi']) ?> (<?= htmlspecialchars($pu['jenjang']) ?>)</option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4 col-sm-6">
                    <select class="form-select form-select-sm" id="tableBidangFilter" onchange="onFakultasFilterChange()">
                        <option value="">Semua Bidang</option>
                        <?php foreach ($bidangList as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 col-sm-6">
                    <select class="form-select form-select-sm" id="tableTahunFilter" onchange="onFakultasFilterChange()">
                        <option value="">Semua Tahun Akademik</option>
                        <?php foreach (get_distinct_tahun_akademik() as $ta): ?>
                            <option value="<?= htmlspecialchars($ta) ?>">Tahun Akademik <?= htmlspecialchars($ta) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="documentsTable" style="font-size: 0.82rem;">
                <thead class="table-light text-secondary">
                    <tr>
                        <th style="width: 45px;" class="text-center">No</th>
                        <th style="min-width: 250px;">Nama & Nomor Dokumen</th>
                        <th style="min-width: 130px;">Siklus PPEPP</th>
                        <th style="min-width: 170px;">Bidang &amp; Standar</th>
                        <th style="min-width: 140px;">Berkas</th>
                        <th style="min-width: 140px;">Status Review LPM</th>
                        <th style="width: 130px;" class="text-center text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($documents)): ?>
                        <tr id="emptyRow">
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-2 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Tidak Ada Dokumen yang Sesuai</div>
                                <div class="small">Silakan sesuaikan kata kunci atau filter siklus.</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = $startNum; 
                        require ROOT_PATH . '/views/admin_fakultas/documents_rows.php'; 
                        ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top mt-3" id="fakultasPaginationWrapper">
            <div id="tableInfo" class="small text-muted">
                Menampilkan <span class="fw-bold text-dark" id="fakultasStartNum"><?= $startNum ?></span> - <span class="fw-bold text-dark" id="fakultasEndNum"><?= $endNum ?></span> dari <span class="fw-bold text-dark" id="fakultasTotalFiltered"><?= number_format($totalFiltered, 0, ',', '.') ?></span> Dokumen
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2.5">
                <div class="d-flex align-items-center gap-1.5 small text-muted">
                    <span style="font-size: 0.78rem;">Ukuran Halaman:</span>
                    <select class="form-select form-select-sm" id="fakultasPerPageSelect" style="width: 95px; font-size: 0.78rem;" onchange="changeFakultasPerPage(this.value)">
                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10 data</option>
                        <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20 data</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 data</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 data</option>
                    </select>
                </div>
                <nav aria-label="Fakultas Pagination">
                    <ul class="pagination pagination-sm mb-0" id="fakultasPaginationNav">
                        <!-- Populated dynamically -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Catatan Review LPM -->
<div class="modal fade" id="fakultasViewNoteModal" tabindex="-1" aria-labelledby="fakultasViewNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white py-3 px-4">
                <h5 class="modal-title fw-bold fs-6" id="fakultasViewNoteModalLabel">
                    <i class="fas fa-triangle-exclamation me-2"></i> Catatan Perbaikan Dokumen dari LPM
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small text-muted mb-1">Nama Dokumen Mutu:</label>
                    <div class="fw-bold text-dark-blue p-2.5 rounded-3 bg-light border" id="modalNoteDocTitle">-</div>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3 text-muted small pb-2 border-bottom">
                    <span><i class="fas fa-building-columns text-primary me-1"></i> <strong id="modalNoteReviewer">Pusat Penjaminan Mutu LPM</strong></span>
                    <span><i class="fas fa-calendar-alt text-warning me-1"></i> <span id="modalNoteDate">-</span></span>
                </div>
                <div class="mb-0">
                    <label class="form-label small text-danger fw-bold mb-1">Poin-Poin / Catatan yang Perlu Diperbaiki:</label>
                    <div class="p-3 rounded-3 border border-danger-subtle text-dark" id="modalNoteContent" style="background: #FEF2F2; font-size: 0.85rem; line-height: 1.6; white-space: pre-line;">-</div>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
                <span class="small text-muted" style="font-size:0.75rem;">Gunakan tombol Ubah untuk memperbaiki dan mengunggah ulang dokumen.</span>
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentFakultasPage = <?= (int)$page ?>;
let currentFakultasPerPage = <?= (int)$perPage ?>;
let fakultasTotalPages = <?= (int)$totalPages ?>;
let activeSiklus = '<?= htmlspecialchars($activeSiklus) ?>';
let filterReviewStatusVal = '';
let fakultasSearchTimer = null;

function selectFakultasSiklusTab(siklus, btn) {
    document.querySelectorAll('#fakultasPpeppTabs .nav-link, .ppepp-tab-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    activeSiklus = siklus ? siklus.toLowerCase() : '';
    fetchFakultasData(1);
}

function toggleFilterReviewStatus(status) {
    const btn = document.getElementById('btnFilterFakultasRevisi');
    const txt = document.getElementById('btnFilterFakultasRevisiText');
    if (filterReviewStatusVal === status) {
        filterReviewStatusVal = '';
        if (btn) {
            btn.classList.remove('btn-danger', 'text-white');
            btn.classList.add('btn-outline-danger');
        }
        if (txt) txt.textContent = 'Filter Tabel: Dokumen Revisi Saja';
    } else {
        filterReviewStatusVal = status;
        if (btn) {
            btn.classList.remove('btn-outline-danger');
            btn.classList.add('btn-danger', 'text-white');
        }
        if (txt) txt.textContent = 'Menampilkan Dokumen Revisi Saja (Reset Filter)';
    }
    fetchFakultasData(1);
}

function changeFakultasPerPage(val) {
    currentFakultasPerPage = parseInt(val, 10) || 50;
    fetchFakultasData(1);
}

function onFakultasFilterChange() {
    clearTimeout(fakultasSearchTimer);
    fakultasSearchTimer = setTimeout(() => {
        fetchFakultasData(1);
    }, 280);
}

function fetchFakultasData(page) {
    currentFakultasPage = page || 1;
    const searchVal = document.getElementById('tableSearchInput') ? document.getElementById('tableSearchInput').value.trim() : '';
    const unitVal = document.getElementById('tableUnitFilter') ? document.getElementById('tableUnitFilter').value : '';
    const bidangVal = document.getElementById('tableBidangFilter') ? document.getElementById('tableBidangFilter').value : '';
    const tahunVal = document.getElementById('tableTahunFilter') ? document.getElementById('tableTahunFilter').value.trim() : '';

    const tbody = document.getElementById('tableBody');
    if (tbody) tbody.style.opacity = '0.5';

    const params = new URLSearchParams({
        ajax: '1',
        page: currentFakultasPage,
        per_page: currentFakultasPerPage,
        siklus: activeSiklus,
        unit_scope: unitVal,
        bidang_id: bidangVal,
        tahun_akademik: tahunVal,
        status_review: filterReviewStatusVal,
        q: searchVal
    });

    fetch('<?= base_url("fakultas/dokumen") ?>?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (tbody) {
            tbody.style.opacity = '1';
            tbody.innerHTML = data.html || '';
        }

        fakultasTotalPages = data.total_pages || 1;
        currentFakultasPage = data.page || 1;

        const startEl = document.getElementById('fakultasStartNum');
        const endEl = document.getElementById('fakultasEndNum');
        const totalEl = document.getElementById('fakultasTotalFiltered');

        if (startEl) startEl.textContent = data.start_num || 0;
        if (endEl) endEl.textContent = data.end_num || 0;
        if (totalEl) totalEl.textContent = Number(data.total || 0).toLocaleString('id-ID');

        renderFakultasPagination(fakultasTotalPages, currentFakultasPage);
    })
    .catch(err => {
        console.error("Error fetching fakultas data:", err);
        if (tbody) tbody.style.opacity = '1';
    });
}

function renderFakultasPagination(totalPages, currentPage) {
    const nav = document.getElementById('fakultasPaginationNav');
    if (!nav) return;
    nav.innerHTML = '';

    if (totalPages <= 1) return;

    // Prev Button
    const prevLi = document.createElement('li');
    prevLi.className = 'page-item ' + (currentPage <= 1 ? 'disabled' : '');
    prevLi.innerHTML = '<a class="page-link" href="javascript:void(0)" aria-label="Previous"><i class="fas fa-chevron-left" style="font-size: 0.72rem;"></i></a>';
    if (currentPage > 1) {
        prevLi.querySelector('a').onclick = () => fetchFakultasData(currentPage - 1);
    }
    nav.appendChild(prevLi);

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        const firstLi = document.createElement('li');
        firstLi.className = 'page-item';
        firstLi.innerHTML = '<a class="page-link" href="javascript:void(0)">1</a>';
        firstLi.querySelector('a').onclick = () => fetchFakultasData(1);
        nav.appendChild(firstLi);

        if (startPage > 2) {
            const dotsLi = document.createElement('li');
            dotsLi.className = 'page-item disabled';
            dotsLi.innerHTML = '<span class="page-link">&hellip;</span>';
            nav.appendChild(dotsLi);
        }
    }

    for (let p = startPage; p <= endPage; p++) {
        const li = document.createElement('li');
        li.className = 'page-item ' + (p === currentPage ? 'active' : '');
        li.innerHTML = '<a class="page-link" href="javascript:void(0)">' + p + '</a>';
        if (p !== currentPage) {
            li.querySelector('a').onclick = ((pageNum) => () => fetchFakultasData(pageNum))(p);
        }
        nav.appendChild(li);
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const dotsLi = document.createElement('li');
            dotsLi.className = 'page-item disabled';
            dotsLi.innerHTML = '<span class="page-link">&hellip;</span>';
            nav.appendChild(dotsLi);
        }
        const lastLi = document.createElement('li');
        lastLi.className = 'page-item';
        lastLi.innerHTML = '<a class="page-link" href="javascript:void(0)">' + totalPages + '</a>';
        lastLi.querySelector('a').onclick = () => fetchFakultasData(totalPages);
        nav.appendChild(lastLi);
    }

    // Next Button
    const nextLi = document.createElement('li');
    nextLi.className = 'page-item ' + (currentPage >= totalPages ? 'disabled' : '');
    nextLi.innerHTML = '<a class="page-link" href="javascript:void(0)" aria-label="Next"><i class="fas fa-chevron-right" style="font-size: 0.72rem;"></i></a>';
    if (currentPage < totalPages) {
        nextLi.querySelector('a').onclick = () => fetchFakultasData(currentPage + 1);
    }
    nav.appendChild(nextLi);
}

document.addEventListener('DOMContentLoaded', function() {
    renderFakultasPagination(fakultasTotalPages, currentFakultasPage);

    const sIn = document.getElementById('tableSearchInput');
    if (sIn) sIn.addEventListener('keyup', onFakultasFilterChange);

    const bFilter = document.getElementById('tableBidangFilter');
    if (bFilter) bFilter.addEventListener('change', onFakultasFilterChange);

    const tFilter = document.getElementById('tableTahunFilter');
    if (tFilter) tFilter.addEventListener('change', onFakultasFilterChange);

    // Modal Catatan Perbaikan
    const fakNoteModal = document.getElementById('fakultasViewNoteModal');
    if (fakNoteModal) {
        fakNoteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const title = button.getAttribute('data-title') || '-';
            const notes = button.getAttribute('data-notes') || 'Tidak ada catatan perbaikan khusus.';
            const reviewer = button.getAttribute('data-reviewer') || 'Pusat Penjaminan Mutu LPM';
            const date = button.getAttribute('data-date') || '-';

            document.getElementById('modalNoteDocTitle').textContent = title;
            document.getElementById('modalNoteContent').textContent = notes;
            document.getElementById('modalNoteReviewer').textContent = reviewer;
            document.getElementById('modalNoteDate').textContent = date;
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
