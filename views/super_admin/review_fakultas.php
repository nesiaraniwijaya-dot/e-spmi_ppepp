<?php
/**
 * Super Admin / LPM: Ruang Kerja Review Dokumen Mutu Tingkat Fakultas
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark-blue text-warning px-3 py-1">FAKULTAS &bull; <?= htmlspecialchars($fakultas['kode_fakultas']) ?></span>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Tingkat Dekanat</span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Review Dokumen Mutu: <?= htmlspecialchars($fakultas['nama_fakultas']) ?></h3>
            <p class="text-muted small mb-0">
                Dekan: <strong><?= htmlspecialchars($fakultas['nama_dekan'] ?: 'Belum diisi') ?></strong> <?= !empty($fakultas['nidn_dekan']) ? '(NIDN: '.htmlspecialchars($fakultas['nidn_dekan']).')' : '' ?>
                <?php if (!empty($fakultas['nama_wadek'])): ?>
                    &bull; Wakil Dekan: <strong><?= htmlspecialchars($fakultas['nama_wadek']) ?></strong>
                <?php endif; ?>
                <?php if (!empty($fakultas['periode_jabatan'])): ?>
                    &bull; Periode: <strong><?= htmlspecialchars($fakultas['periode_jabatan']) ?></strong>
                <?php endif; ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Review Status Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #F59E0B !important;">
                <div class="small text-muted fw-bold mb-1">Belum Direview</div>
                <div class="fs-4 fw-bold text-dark-blue"><?= $reviewStats['belum_direview'] ?></div>
                <div class="small text-muted" style="font-size: 0.72rem;">Menunggu verifikasi awal LPM</div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #EF4444 !important;">
                <div class="small text-muted fw-bold mb-1">Perlu Perbaikan</div>
                <div class="fs-4 fw-bold text-dark-blue"><?= $reviewStats['perlu_perbaikan'] ?></div>
                <div class="small text-danger" style="font-size: 0.72rem;">Catatan revisi dikirim ke Fakultas</div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #EF4444 !important;">
                <div class="small text-muted fw-bold mb-1">Perlu Review Ulang</div>
                <div class="fs-4 fw-bold text-dark-blue"><?= $reviewStats['sudah_diperbaiki'] ?></div>
                <div class="small text-danger" style="font-size: 0.72rem;">Sudah diperbaiki oleh Fakultas</div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #10B981 !important;">
                <div class="small text-muted fw-bold mb-1">Sesuai Standar</div>
                <div class="fs-4 fw-bold text-dark-blue"><?= $reviewStats['sesuai'] ?></div>
                <div class="small text-success" style="font-size: 0.72rem;">Telah diverifikasi & disetujui</div>
            </div>
        </div>
    </div>

    <?php
    $ppeppCounts = [
        'all' => count($documents),
        'penetapan' => 0,
        'pelaksanaan' => 0,
        'evaluasi' => 0,
        'pengendalian' => 0,
        'peningkatan' => 0,
    ];
    foreach ($documents as $doc) {
        $s = $doc['siklus'] ?? '';
        if (isset($ppeppCounts[$s])) {
            $ppeppCounts[$s]++;
        }
    }
    ?>

    <!-- Main Documents Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        
        <!-- 5 Siklus PPEPP Tabs Header -->
        <ul class="nav ppepp-nav-tabs mb-4" id="reviewPpeppTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" type="button" onclick="selectReviewSiklusTab(this, '')">
                    <i class="fas fa-layer-group"></i> Semua (<?= $ppeppCounts['all'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-penetapan" type="button" onclick="selectReviewSiklusTab(this, 'penetapan')">
                    <i class="fas fa-file-signature"></i> Penetapan (<?= $ppeppCounts['penetapan'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-pelaksanaan" type="button" onclick="selectReviewSiklusTab(this, 'pelaksanaan')">
                    <i class="fas fa-person-chalkboard"></i> Pelaksanaan (<?= $ppeppCounts['pelaksanaan'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-evaluasi" type="button" onclick="selectReviewSiklusTab(this, 'evaluasi')">
                    <i class="fas fa-magnifying-glass-chart"></i> Evaluasi (<?= $ppeppCounts['evaluasi'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-pengendalian" type="button" onclick="selectReviewSiklusTab(this, 'pengendalian')">
                    <i class="fas fa-sliders"></i> Pengendalian (<?= $ppeppCounts['pengendalian'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-peningkatan" type="button" onclick="selectReviewSiklusTab(this, 'peningkatan')">
                    <i class="fas fa-arrow-trend-up"></i> Peningkatan (<?= $ppeppCounts['peningkatan'] ?>)
                </button>
            </li>
        </ul>

        <!-- Filter Toolbar: Pencarian (Baris 1) & Filter Dropdown (Baris 2) -->
        <div class="mb-4 pb-3 border-bottom">
            <!-- Baris 1: Pencarian Cepat (Full Width) -->
            <div class="mb-2.5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="fas fa-search"></i></span>
                    <input type="text" id="reviewSearchInput" class="form-control bg-light border-start-0 ps-1" placeholder="Cari nama dokumen, nomor SK, pengunggah..." oninput="filterReviewTable()">
                </div>
            </div>

            <!-- Baris 2: Filter Status Review, Tahun Akademik, dan Info Total -->
            <div class="row g-2.5 align-items-center">
                <div class="col-md-5 col-sm-6">
                    <select id="reviewStatusFilter" class="form-select bg-light" onchange="filterReviewTable()">
                        <option value="">-- Semua Status Review --</option>
                        <option value="belum_direview">⏳ Belum Direview</option>
                        <option value="perlu_perbaikan">⚠️ Perlu Perbaikan (Revisi)</option>
                        <option value="sudah_diperbaiki">🔄 Perlu Review Ulang</option>
                        <option value="sesuai">✅ Sesuai Standar</option>
                    </select>
                </div>
                <div class="col-md-4 col-sm-6">
                    <select id="reviewTahunFilter" class="form-select bg-light" onchange="filterReviewTable()">
                        <option value="">Semua Tahun Akademik</option>
                        <?php foreach (get_distinct_tahun_akademik() as $ta): ?>
                            <option value="<?= htmlspecialchars($ta) ?>">Tahun Akademik <?= htmlspecialchars($ta) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-12 text-md-end ms-auto">
                    <span class="text-muted small" id="docCountLabel">Total <?= count($documents) ?> Dokumen</span>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="reviewDocTable" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Nama & Nomor Dokumen</th>
                        <th>Bidang &amp; Standar</th>
                        <th style="width: 110px;">Berkas</th>
                        <th style="min-width: 250px;">Status Review LPM</th>
                        <th class="text-center" style="width: 160px;">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x text-secondary opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold">Tidak Ada Dokumen yang Sesuai</h6>
                                <p class="small mb-0">Coba ubah filter atau kata kunci pencarian.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = $startNum; 
                        require ROOT_PATH . '/views/super_admin/review_fakultas_rows.php'; 
                        ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination Footer Bar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top mt-3" id="reviewPaginationWrapper">
            <div class="small text-muted" id="reviewTableInfo">
                Menampilkan <span class="fw-bold text-dark" id="reviewStartNum"><?= $startNum ?></span> - <span class="fw-bold text-dark" id="reviewEndNum"><?= $endNum ?></span> dari <span class="fw-bold text-dark" id="reviewTotalFiltered"><?= number_format($totalFiltered, 0, ',', '.') ?></span> Dokumen
            </div>
            <div class="table-pagination-control">
                <div class="table-perpage-wrap">
                    <label for="reviewPerPageSelect" class="table-perpage-label">Ukuran Halaman:</label>
                    <select class="form-select table-perpage-select shadow-none" id="reviewPerPageSelect" onchange="changeReviewPerPage(this.value)">
                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10 data</option>
                        <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20 data</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 data</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 data</option>
                    </select>
                </div>
                <nav aria-label="Review Pagination">
                    <ul class="pagination-segmented mb-0" id="reviewPaginationNav">
                        <!-- Populated dynamically by JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Minta Revisi Dokumen -->
<div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('admin/review-dokumen/submit') ?>" method="POST" id="revisionForm">
                <input type="hidden" name="document_id" id="revDocId" value="">
                <input type="hidden" name="status_review" value="perlu_perbaikan">
                
                <div class="modal-header py-3.5 px-4 bg-white border-bottom" style="border-color: #F1F5F9 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark-blue fs-6 mb-0" id="revisionModalLabel">
                                Kembalikan ke GPM untuk Perbaikan Fakultas
                            </h5>
                            <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.78rem;">
                                Berikan instruksi revisi yang jelas untuk unit dekanat fakultas.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <!-- Dokumen Yang Direvisi Info Card -->
                    <div class="p-3.5 rounded-3 mb-4 border" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 bg-white border shadow-2xs text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                                <i class="fas fa-file-lines"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Dokumen Yang Direvisi</span>
                                    <span class="badge rounded-pill" style="background: #FEE2E2 !important; color: #991B1B !important; border: 1px solid #FCA5A5 !important; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">Status: Perlu Revisi</span>
                                </div>
                                <div class="fw-bold text-dark-blue fs-6 mb-0" id="revDocTitle" style="line-height: 1.45;">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Banner Pengesahan a.n. Pusat Penjaminan Mutu LPM -->
                    <div class="p-3 rounded-3 mb-3.5 border d-flex align-items-center gap-2.5" style="background: #EFF6FF; border-color: #BFDBFE !important;">
                        <i class="fas fa-shield-halved text-primary fs-4 flex-shrink-0"></i>
                        <div style="font-size: 0.77rem; color: #1E40AF; line-height: 1.45;">
                            <div><strong>Pengesahan Resmi LPM:</strong> Hasil review dokumen ini akan diterbitkan secara sah <strong>a.n. Pusat Penjaminan Mutu LPM</strong>.</div>
                            <div class="text-secondary opacity-75 mt-0.5" style="font-size: 0.7rem;">Petugas Verifikator: <?= htmlspecialchars(Auth::user()['name']) ?> (<?= Auth::getOfficialTitle() ?>)</div>
                        </div>
                    </div>

                    <!-- Catatan / Arahan Input Form -->
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="revCatatan" class="form-label fw-bold text-dark-blue small mb-0" style="font-size: 0.82rem;">
                                Catatan &amp; Poin-Poin Perbaikan <span class="text-danger">*</span>
                            </label>
                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.68rem;">Wajib Diisi</span>
                        </div>
                        <textarea class="form-control rounded-3 p-3 text-dark" id="revCatatan" name="catatan_review" rows="5" 
                                  style="font-size: 0.86rem; line-height: 1.6; border-color: #CBD5E1; resize: vertical;"
                                  placeholder="Tuliskan catatan perbaikan secara spesifik... Contoh: Nomor SK Dekanat belum tercantum, harap lengkapi tanda tangan pengesahan dari Dekan pada lembar akhir." required></textarea>
                        <div class="d-flex align-items-center gap-2 mt-2.5 p-2.5 rounded-2 bg-light border text-muted" style="font-size: 0.74rem;">
                            <i class="fas fa-circle-info text-primary flex-shrink-0"></i>
                            <span>Catatan ini akan otomatis muncul sebagai instruksi revisi di dashboard Admin Fakultas saat memperbarui berkas.</span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer px-4 py-3 bg-light bg-opacity-60 border-top d-flex justify-content-between align-items-center" style="border-color: #F1F5F9 !important;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.82rem;">
                        <i class="fas fa-undo"></i> Kembalikan ke GPM untuk Perbaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Catatan LPM -->
<div class="modal fade" id="viewLpmNoteModal" tabindex="-1" aria-labelledby="viewLpmNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header py-3.5 px-4 bg-white border-bottom" style="border-color: #F1F5F9 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark-blue fs-6 mb-0" id="viewLpmNoteModalLabel">
                            Detail Catatan Review LPM
                        </h5>
                        <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.78rem;">
                            Riwayat evaluasi dan catatan penjaminan mutu.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-3 mb-3.5 border" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                    <div class="text-muted fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">Dokumen:</div>
                    <div class="fw-bold text-dark-blue fs-6 mb-2" id="viewNoteDocTitle">-</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small" style="font-size: 0.72rem;">Status:</span>
                        <div id="viewNoteStatusBadge">-</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark-blue small mb-1.5" style="font-size: 0.82rem;">Isi Catatan / Arahan LPM:</label>
                    <div class="p-3.5 rounded-3 bg-danger bg-opacity-10 border border-danger-subtle text-danger-emphasis fw-medium" style="font-size: 0.86rem; line-height: 1.6; white-space: pre-wrap;" id="viewNoteContent">-</div>
                </div>
                <div class="p-2.5 rounded-2 bg-light border d-flex justify-content-between align-items-center text-muted" style="font-size: 0.74rem;" id="viewNoteMeta">
                    <span><i class="fas fa-user-shield text-primary me-1"></i> Peninjau: <strong class="text-dark" id="viewNoteReviewer">-</strong></span>
                    <span><i class="fas fa-clock text-secondary me-1"></i> <span id="viewNoteDate">-</span></span>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-light bg-opacity-60 border-top d-flex justify-content-end" style="border-color: #F1F5F9 !important;">
                <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentReviewSiklus = '';

function selectReviewSiklusTab(btn, siklus) {
    currentReviewSiklus = siklus;
    const tabButtons = document.querySelectorAll('#reviewPpeppTabs .nav-link');
    tabButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filterReviewTable();
}

// Filter Table Script
let currentReviewPage = <?= (int)$page ?>;
let currentReviewPerPage = <?= (int)$perPage ?>;
let reviewTotalPages = <?= (int)$totalPages ?>;
let reviewSearchTimer = null;

function filterReviewTable() {
    clearTimeout(reviewSearchTimer);
    reviewSearchTimer = setTimeout(() => {
        fetchReviewFakultasData(1);
    }, 280);
}

function changeReviewPerPage(val) {
    currentReviewPerPage = parseInt(val, 10) || 50;
    fetchReviewFakultasData(1);
}

function fetchReviewFakultasData(page) {
    currentReviewPage = page || 1;
    const searchVal = document.getElementById('reviewSearchInput').value.trim();
    const statusVal = document.getElementById('reviewStatusFilter').value;
    const tahunVal = document.getElementById('reviewTahunFilter') ? document.getElementById('reviewTahunFilter').value.trim() : '';

    const tbody = document.querySelector('#reviewDocTable tbody');
    if (tbody) {
        tbody.style.opacity = '0.5';
    }

    const params = new URLSearchParams({
        ajax: '1',
        page: currentReviewPage,
        per_page: currentReviewPerPage,
        siklus: currentReviewSiklus,
        status_review: statusVal,
        tahun_akademik: tahunVal,
        search: searchVal,
        q: searchVal
    });

    fetch('<?= base_url("admin/review-fakultas/" . $fakultas["id"]) ?>?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (tbody) {
            tbody.style.opacity = '1';
            tbody.innerHTML = data.html || '';
        }

        reviewTotalPages = data.total_pages || 1;
        currentReviewPage = data.page || 1;

        // Update info text
        const startEl = document.getElementById('reviewStartNum');
        const endEl = document.getElementById('reviewEndNum');
        const totalEl = document.getElementById('reviewTotalFiltered');
        const countLabel = document.getElementById('docCountLabel');

        if (startEl) startEl.textContent = data.start_num || 0;
        if (endEl) endEl.textContent = data.end_num || 0;
        if (totalEl) totalEl.textContent = Number(data.total || 0).toLocaleString('id-ID');
        if (countLabel) countLabel.textContent = 'Menampilkan ' + (data.end_num ? (data.end_num - data.start_num + 1) : 0) + ' dari ' + Number(data.total || 0).toLocaleString('id-ID') + ' Dokumen';

        renderReviewPagination(reviewTotalPages, currentReviewPage);
    })
    .catch(err => {
        console.error("Error fetching review data:", err);
        if (tbody) tbody.style.opacity = '1';
    });
}

function renderReviewPagination(totalPages, currentPage) {
    const nav = document.getElementById('reviewPaginationNav');
    if (!nav) return;
    nav.innerHTML = '';

    if (totalPages <= 1) return;

    // Previous Button
    const prevLi = document.createElement('li');
    prevLi.className = 'page-item ' + (currentPage <= 1 ? 'disabled' : '');
    prevLi.innerHTML = '<a class="page-link" href="javascript:void(0)" aria-label="Previous"><i class="fas fa-chevron-left" style="font-size: 0.72rem;"></i></a>';
    if (currentPage > 1) {
        prevLi.querySelector('a').onclick = () => fetchReviewFakultasData(currentPage - 1);
    }
    nav.appendChild(prevLi);

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        const firstLi = document.createElement('li');
        firstLi.className = 'page-item';
        firstLi.innerHTML = '<a class="page-link" href="javascript:void(0)">1</a>';
        firstLi.querySelector('a').onclick = () => fetchReviewFakultasData(1);
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
            li.querySelector('a').onclick = ((pageNum) => () => fetchReviewFakultasData(pageNum))(p);
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
        lastLi.querySelector('a').onclick = () => fetchReviewFakultasData(totalPages);
        nav.appendChild(lastLi);
    }

    // Next Button
    const nextLi = document.createElement('li');
    nextLi.className = 'page-item ' + (currentPage >= totalPages ? 'disabled' : '');
    nextLi.innerHTML = '<a class="page-link" href="javascript:void(0)" aria-label="Next"><i class="fas fa-chevron-right" style="font-size: 0.72rem;"></i></a>';
    if (currentPage < totalPages) {
        nextLi.querySelector('a').onclick = () => fetchReviewFakultasData(currentPage + 1);
    }
    nav.appendChild(nextLi);
}

// Initial pagination render
document.addEventListener('DOMContentLoaded', function() {
    renderReviewPagination(reviewTotalPages, currentReviewPage);
});


// Bootstrap modal event binding
document.addEventListener('DOMContentLoaded', function () {
    const revModal = document.getElementById('revisionModal');
    if (revModal) {
        revModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const docId = button.getAttribute('data-id');
            const docTitle = button.getAttribute('data-title');
            const docNotes = button.getAttribute('data-notes');

            document.getElementById('revDocId').value = docId || '';
            document.getElementById('revDocTitle').textContent = docTitle || '-';
            document.getElementById('revCatatan').value = '';
        });
    }

    const noteModal = document.getElementById('viewLpmNoteModal');
    if (noteModal) {
        noteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const title = button.getAttribute('data-title') || '-';
            const notes = button.getAttribute('data-notes') || 'Tidak ada catatan khusus.';
            const reviewer = button.getAttribute('data-reviewer') || 'Admin LPM';
            const date = button.getAttribute('data-date') || '-';
            const status = button.getAttribute('data-status') || '';

            document.getElementById('viewNoteDocTitle').textContent = title;
            document.getElementById('viewNoteContent').textContent = notes;
            document.getElementById('viewNoteReviewer').textContent = reviewer;
            document.getElementById('viewNoteDate').textContent = date;

            const badgeEl = document.getElementById('viewNoteStatusBadge');
            if (status === 'perlu_perbaikan') {
                badgeEl.innerHTML = '<span class="badge rounded-pill" style="background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; font-size:0.75rem;"><i class="fas fa-triangle-exclamation text-danger me-1"></i> Perlu Revisi / Perbaikan</span>';
            } else if (status === 'sudah_diperbaiki') {
                badgeEl.innerHTML = '<span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E; border:1px solid #FCD34D; font-size:0.75rem;"><i class="fas fa-clock text-warning me-1"></i> Sudah Diperbaiki (Perlu Review Ulang)</span>';
            } else {
                badgeEl.innerHTML = '<span class="badge bg-secondary">' + status + '</span>';
            }
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
