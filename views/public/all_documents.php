<?php
/**
 * Public View: Repositori Dokumen Mutu Keseluruhan
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/header.php';

$initialSearch = htmlspecialchars($search ?? '');
$initialSiklus = strtolower(trim($initialSiklus ?? ''));
$currentPage = (int)($currentPage ?? 1);
$totalPages = (int)($totalPages ?? 1);
$perPage = (int)($perPage ?? 50);
$filteredTotal = (int)($filteredTotal ?? 0);
$startNum = (int)($startNum ?? 1);
$endNum = (int)($endNum ?? 0);

$siklusMetaList = [
    '' => [
        'icon' => 'fa-layer-group',
        'bg' => '#0A192F',
        'title' => 'Semua Siklus Dokumen Mutu (PPEPP)',
        'desc' => 'Menampilkan seluruh repositori dokumen mutu terverifikasi dari semua tahapan siklus PPEPP.'
    ],
    'penetapan' => [
        'icon' => 'fa-file-signature',
        'bg' => '#2563EB',
        'title' => 'Tahap Penetapan (P1) - Standar & Kebijakan SPMI',
        'desc' => 'Dokumen kebijakan mutu, standar SPMI, manual standar, dan capaian pembelajaran lulusan (CPL).'
    ],
    'pelaksanaan' => [
        'icon' => 'fa-person-chalkboard',
        'bg' => '#059669',
        'title' => 'Tahap Pelaksanaan (P2) - Pelaksanaan & Pemenuhan Standar',
        'desc' => 'Dokumen keterlaksanaan standar tridharma, instruksi kerja, SOP, dan pedoman operasional prodi.'
    ],
    'evaluasi' => [
        'icon' => 'fa-magnifying-glass-chart',
        'bg' => '#D97706',
        'title' => 'Tahap Evaluasi (E) - Evaluasi Pelaksanaan Standar & AMI',
        'desc' => 'Dokumen evaluasi berkala, laporan Audit Mutu Internal (AMI), asesmen capaian, dan evaluasi diri prodi.'
    ],
    'pengendalian' => [
        'icon' => 'fa-sliders',
        'bg' => '#7C3AED',
        'title' => 'Tahap Pengendalian (P3) - Pengendalian Standar & Tindakan Koreksi',
        'desc' => 'Dokumen Rapat Tinjauan Manajemen (RTM), perumusan rekomendasi perbaikan, dan tindakan korektif.'
    ],
    'peningkatan' => [
        'icon' => 'fa-arrow-trend-up',
        'bg' => '#0891B2',
        'title' => 'Tahap Peningkatan (P4) - Peningkatan Mutu Berkelanjutan',
        'desc' => 'Dokumen Continuous Quality Improvement (Kaizen) dan pembaharuan standar mutu melampaui SN-Dikti.'
    ]
];
$currentMeta = $siklusMetaList[$initialSiklus] ?? $siklusMetaList[''];
?>

<!-- ============================================================
     BANNER: PORTAL DOKUMEN MUTU & CAPAIAN PPEPP UNIVERSITAS
============================================================ -->
<section class="page-banner text-white">
    <div class="container position-relative z-index-2">
        <div class="row align-items-center">
            <div class="col-lg-10 col-xl-9">
                <nav aria-label="breadcrumb">
                    <div class="breadcrumb-lpm mb-2">
                        <a href="<?= base_url() ?>"><i class="fas fa-home me-1"></i> Beranda</a>
                        <span>/</span>
                        <span class="current">Portal Dokumen Mutu</span>
                    </div>
                </nav>

                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 text-info small fw-bold mb-3">
                    <i class="fas fa-file-shield"></i> Repositori Mutu Institusional &bull; MITRA
                </div>

                <h1 class="page-banner-title mb-2">Portal Dokumen Mutu Universitas</h1>
                <p class="text-light opacity-80 small mb-0" style="max-width: 720px; line-height: 1.75; font-size: 0.95rem;">
                    Pusat repositori dokumen <strong>Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan (PPEPP)</strong> seluruh program studi di lingkungan <?= INSTITUTION_NAME ?> guna mendukung transparansi dan penjaminan mutu berkelanjutan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     TABEL DOKUMEN MUTU TERKATEGORISASI BERDASARKAN PPEPP
============================================================ -->
<section class="py-4 py-lg-5">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            
            <!-- 5 Siklus PPEPP Categorized Navigation Tabs Header -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="fw-bold text-dark-blue mb-1">Daftar Dokumen Mutu Terkategorisasi</h4>
                    <p class="text-muted small mb-0">Pilih salah satu tab tahapan siklus PPEPP di bawah ini untuk melihat dokumen sesuai kategorinya.</p>
                </div>
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-1.5 rounded-pill" style="font-size: 0.8rem;">
                        <i class="fas fa-folder-open me-1"></i> <?= number_format($totalDokumen, 0, ',', '.') ?> Total Dokumen
                    </span>
                </div>
            </div>

            <ul class="nav ppepp-nav-tabs mb-4" id="publicPpeppTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link <?= empty($initialSiklus) ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, '')">
                        <i class="fas fa-layer-group"></i> Semua (<?= number_format($totalDokumen, 0, ',', '.') ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-penetapan <?= $initialSiklus === 'penetapan' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'penetapan')">
                        <i class="fas fa-file-signature"></i> Penetapan (<?= number_format($cycleCounts['penetapan'] ?? 0, 0, ',', '.') ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-pelaksanaan <?= $initialSiklus === 'pelaksanaan' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'pelaksanaan')">
                        <i class="fas fa-person-chalkboard"></i> Pelaksanaan (<?= number_format($cycleCounts['pelaksanaan'] ?? 0, 0, ',', '.') ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-evaluasi <?= $initialSiklus === 'evaluasi' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'evaluasi')">
                        <i class="fas fa-magnifying-glass-chart"></i> Evaluasi (<?= number_format($cycleCounts['evaluasi'] ?? 0, 0, ',', '.') ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-pengendalian <?= $initialSiklus === 'pengendalian' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'pengendalian')">
                        <i class="fas fa-sliders"></i> Pengendalian (<?= number_format($cycleCounts['pengendalian'] ?? 0, 0, ',', '.') ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-peningkatan <?= $initialSiklus === 'peningkatan' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'peningkatan')">
                        <i class="fas fa-arrow-trend-up"></i> Peningkatan (<?= number_format($cycleCounts['peningkatan'] ?? 0, 0, ',', '.') ?>)
                    </button>
                </li>
            </ul>

            <!-- Cycle Category Context Banner -->
            <div id="cycleCategoryHeader" class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                <div class="d-flex align-items-center gap-2.5">
                    <span id="cycleCategoryIcon" class="rounded-circle p-2 d-inline-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: <?= $currentMeta['bg'] ?>;">
                        <i class="fas <?= $currentMeta['icon'] ?>"></i>
                    </span>
                    <div>
                        <div class="fw-bold text-dark-blue small" id="cycleCategoryTitle"><?= htmlspecialchars($currentMeta['title']) ?></div>
                        <div class="text-muted" style="font-size: 0.73rem;" id="cycleCategoryDesc"><?= htmlspecialchars($currentMeta['desc']) ?></div>
                    </div>
                </div>
                <span class="badge bg-white text-dark border px-3 py-1.5 rounded-pill shadow-2xs fw-semibold" id="cycleCategoryCountBadge">
                    <?= number_format($filteredTotal, 0, ',', '.') ?> Dokumen
                </span>
            </div>

            <!-- Toolbar: Pencarian, Filter Fakultas, Filter Prodi, Filter Bidang, Filter Tahun Akademik, Tampilkan -->
            <div class="row g-2.5 align-items-center mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="tableSearchInput" class="form-control bg-light border-start-0 ps-0" 
                               placeholder="Cari nama dokumen, nomor SK, prodi..." 
                               value="<?= $initialSearch ?>">
                    </div>
                </div>

                <!-- Filter Fakultas -->
                <div class="col-lg-2 col-md-6">
                    <select id="tableFakultasFilter" class="form-select bg-light">
                        <option value="">Semua Fakultas</option>
                        <option value="tingkat_fakultas" <?= ($fakultas ?? '') === 'tingkat_fakultas' ? 'selected' : '' ?>>🏢 Tingkat Fakultas (Dekanat)</option>
                        <?php foreach (($fakultasList ?? []) as $f): ?>
                            <option value="<?= $f['id'] ?>" <?= ((string)($fakultas ?? '') === (string)$f['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['nama_fakultas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Program Studi -->
                <div class="col-lg-2 col-md-4">
                    <select id="tableProdiFilter" class="form-select bg-light">
                        <option value="">Semua Prodi</option>
                        <?php foreach (($prodiList ?? []) as $p): ?>
                            <option value="<?= $p['id'] ?>" 
                                    data-fakultas-id="<?= $p['fakultas_id'] ?? '' ?>"
                                    <?= ((int)($prodi ?? 0) === (int)$p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nama_prodi']) ?> (<?= htmlspecialchars($p['jenjang']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Bidang Standar -->
                <div class="col-lg-2 col-md-4">
                    <select id="tableBidangFilter" class="form-select bg-light">
                        <option value="">Semua Bidang</option>
                        <?php foreach (($bidangList ?? []) as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= ((int)($bidang ?? 0) === (int)$b['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($b['nama_bidang']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Tahun Akademik -->
                <div class="col-lg-2 col-md-4">
                    <?php $distinctTahunList = get_distinct_tahun_akademik(); ?>
                    <select id="tableTahunFilter" class="form-select bg-light">
                        <option value="">Semua Tahun Akademik</option>
                        <?php foreach ($distinctTahunList as $ta): ?>
                            <option value="<?= htmlspecialchars($ta) ?>" <?= (($tahun ?? '') === $ta) ? 'selected' : '' ?>>
                                Tahun Akademik <?= htmlspecialchars($ta) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Per Page Selector (Default 50 data) -->
                <div class="col-lg-1 col-md-4 d-flex justify-content-md-end align-items-center gap-1">
                    <select id="tablePerPageSelect" class="form-select form-select-sm form-select-perpage bg-light" style="min-width: 90px; width: 100%;">
                        <option value="10" <?= $perPage === 10 ? 'selected' : '' ?>>10 data</option>
                        <option value="20" <?= $perPage === 20 ? 'selected' : '' ?>>20 data</option>
                        <option value="50" <?= $perPage === 50 ? 'selected' : '' ?>>50 data</option>
                        <option value="100" <?= $perPage === 100 ? 'selected' : '' ?>>100 data</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive position-relative">
                <table class="table table-custom table-hover align-middle mb-0" id="ppeppDocumentTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama &amp; Nomor Dokumen</th>
                            <th>Program Studi</th>
                            <th style="width: 130px;">Siklus PPEPP</th>
                            <th>Bidang &amp; Standar</th>
                            <th>Jenis</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="ppeppDocumentTableBody" style="transition: opacity 0.2s ease;">
                        <?php require ROOT_PATH . '/views/public/all_documents_rows.php'; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination (Next / Prev & Numbered Pages) -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top mt-3">
                <div id="tableInfo" class="small text-muted">
                    <?php if ($filteredTotal > 0): ?>
                        Menampilkan <?= $startNum ?> - <?= $endNum ?> dari total <?= number_format($filteredTotal, 0, ',', '.') ?> dokumen
                    <?php else: ?>
                        Tidak ada dokumen yang sesuai kriteria.
                    <?php endif; ?>
                </div>
                <div id="tablePagination">
                    <!-- Pagination links injected by JavaScript -->
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Categorized PPEPP Tab Filter & Pagination Script -->
<script>
let currentPublicSiklus = <?= json_encode($initialSiklus) ?>;
let currentPage = <?= $currentPage ?>;
let totalPages = <?= $totalPages ?>;
let isTableLoading = false;
let searchDebounceTimer = null;
let currentFetchController = null;

const siklusMeta = {
    '': {
        icon: 'fa-layer-group',
        bg: '#0A192F',
        title: 'Semua Siklus Dokumen Mutu (PPEPP)',
        desc: 'Menampilkan seluruh repositori dokumen mutu terverifikasi dari semua tahapan siklus PPEPP.'
    },
    'penetapan': {
        icon: 'fa-file-signature',
        bg: '#2563EB',
        title: 'Tahap Penetapan (P1) - Standar & Kebijakan SPMI',
        desc: 'Dokumen kebijakan mutu, standar SPMI, manual standar, dan capaian pembelajaran lulusan (CPL).'
    },
    'pelaksanaan': {
        icon: 'fa-person-chalkboard',
        bg: '#059669',
        title: 'Tahap Pelaksanaan (P2) - Pelaksanaan & Pemenuhan Standar',
        desc: 'Dokumen keterlaksanaan standar tridharma, instruksi kerja, SOP, dan pedoman operasional prodi.'
    },
    'evaluasi': {
        icon: 'fa-magnifying-glass-chart',
        bg: '#D97706',
        title: 'Tahap Evaluasi (E) - Evaluasi Pelaksanaan Standar & AMI',
        desc: 'Dokumen evaluasi berkala, laporan Audit Mutu Internal (AMI), asesmen capaian, dan evaluasi diri prodi.'
    },
    'pengendalian': {
        icon: 'fa-sliders',
        bg: '#7C3AED',
        title: 'Tahap Pengendalian (P3) - Pengendalian Standar & Tindakan Koreksi',
        desc: 'Dokumen Rapat Tinjauan Manajemen (RTM), perumusan rekomendasi perbaikan, dan tindakan korektif.'
    },
    'peningkatan': {
        icon: 'fa-arrow-trend-up',
        bg: '#0891B2',
        title: 'Tahap Peningkatan (P4) - Peningkatan Mutu Berkelanjutan',
        desc: 'Dokumen Continuous Quality Improvement (Kaizen) dan pembaharuan standar mutu melampaui SN-Dikti.'
    }
};

function selectPublicSiklusTab(btn, siklus) {
    const tabButtons = document.querySelectorAll('#publicPpeppTabs .nav-link');
    tabButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentPublicSiklus = siklus;

    // Update Context Banner Header
    const meta = siklusMeta[siklus] || siklusMeta[''];
    const catIcon = document.getElementById('cycleCategoryIcon');
    const catTitle = document.getElementById('cycleCategoryTitle');
    const catDesc = document.getElementById('cycleCategoryDesc');
    
    if (catIcon) {
        catIcon.style.background = meta.bg;
        catIcon.innerHTML = `<i class="fas ${meta.icon}"></i>`;
    }
    if (catTitle) catTitle.textContent = meta.title;
    if (catDesc) catDesc.textContent = meta.desc;

    // Reset ke halaman 1 dan muat data
    fetchTableData(1);
}

function fetchTableData(targetPage = 1) {
    if (currentFetchController) {
        currentFetchController.abort();
    }
    currentFetchController = new AbortController();

    const tbody = document.getElementById('ppeppDocumentTableBody');
    const infoEl = document.getElementById('tableInfo');
    const badgeEl = document.getElementById('cycleCategoryCountBadge');

    const searchInput = document.getElementById('tableSearchInput');
    const fakFilter = document.getElementById('tableFakultasFilter');
    const prodiFilter = document.getElementById('tableProdiFilter');
    const bidangFilter = document.getElementById('tableBidangFilter');
    const tahunFilter = document.getElementById('tableTahunFilter');
    const perPageSelect = document.getElementById('tablePerPageSelect');

    const params = new URLSearchParams();
    params.set('ajax', '1');
    if (currentPublicSiklus) params.set('siklus', currentPublicSiklus);
    if (searchInput && searchInput.value.trim()) params.set('search', searchInput.value.trim());
    if (fakFilter && fakFilter.value.trim()) params.set('fakultas', fakFilter.value.trim());
    if (prodiFilter && prodiFilter.value.trim()) params.set('prodi', prodiFilter.value.trim());
    if (bidangFilter && bidangFilter.value.trim()) params.set('bidang', bidangFilter.value.trim());
    if (tahunFilter && tahunFilter.value.trim()) params.set('tahun', tahunFilter.value.trim());
    
    const perPageVal = perPageSelect ? perPageSelect.value : '50';
    params.set('per_page', perPageVal);
    params.set('page', targetPage.toString());

    if (tbody) {
        tbody.style.opacity = '0.4';
    }

    const fetchUrl = '<?= base_url('dokumen') ?>?' + params.toString();

    fetch(fetchUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        signal: currentFetchController.signal
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP error ' + res.status);
        return res.json();
    })
    .then(data => {
        if (!data.success) return;

        currentPage = data.page;
        totalPages = data.total_pages;

        if (tbody) {
            tbody.innerHTML = data.html;
            tbody.style.opacity = '1';
        }

        if (infoEl) {
            if (data.total === 0) {
                infoEl.textContent = 'Tidak ada dokumen yang sesuai kriteria.';
            } else {
                infoEl.textContent = `Menampilkan ${data.start_num} - ${data.end_num} dari total ${Number(data.total).toLocaleString('id-ID')} dokumen`;
            }
        }

        if (badgeEl) {
            badgeEl.textContent = `${Number(data.total).toLocaleString('id-ID')} Dokumen`;
        }

        renderPaginationUI(data.page, data.total_pages);

        // Update clean URL in browser bar without reload
        const urlParams = new URLSearchParams();
        if (currentPublicSiklus) urlParams.set('siklus', currentPublicSiklus);
        if (searchInput && searchInput.value.trim()) urlParams.set('search', searchInput.value.trim());
        if (fakFilter && fakFilter.value.trim()) urlParams.set('fakultas', fakFilter.value.trim());
        if (prodiFilter && prodiFilter.value.trim()) urlParams.set('prodi', prodiFilter.value.trim());
        if (bidangFilter && bidangFilter.value.trim()) urlParams.set('bidang', bidangFilter.value.trim());
        if (tahunFilter && tahunFilter.value.trim()) urlParams.set('tahun', tahunFilter.value.trim());
        if (perPageVal !== '50') urlParams.set('per_page', perPageVal);
        if (data.page > 1) urlParams.set('page', data.page.toString());

        const newUrl = '<?= base_url('dokumen') ?>' + (urlParams.toString() ? '?' + urlParams.toString() : '');
        if (window.history.pushState) {
            window.history.pushState({ siklus: currentPublicSiklus, page: data.page }, '', newUrl);
        }
    })
    .catch(err => {
        if (err.name === 'AbortError') return;
        console.error('Error fetching documents:', err);
        if (tbody) tbody.style.opacity = '1';
    });
}

function renderPaginationUI(curPage, maxPages) {
    const paginationEl = document.getElementById('tablePagination');
    if (!paginationEl) return;

    if (maxPages <= 1) {
        paginationEl.innerHTML = '';
        return;
    }

    let html = '<ul class="pagination pagination-sm mb-0 flex-wrap gap-1">';
    
    // Previous Button
    const prevDisabled = curPage <= 1 ? 'disabled' : '';
    html += `<li class="page-item ${prevDisabled}">
        <button class="page-link rounded-2 px-2.5" data-page="${curPage - 1}" ${prevDisabled ? 'disabled' : ''} title="Halaman Sebelumnya">
            <i class="fas fa-chevron-left me-1"></i> Prev
        </button>
    </li>`;

    // Numbered Pages
    for (let p = 1; p <= maxPages; p++) {
        if (p === 1 || p === maxPages || (p >= curPage - 2 && p <= curPage + 2)) {
            const isActive = (p === curPage) ? 'active' : '';
            html += `<li class="page-item ${isActive}">
                <button class="page-link rounded-2" data-page="${p}">${p}</button>
            </li>`;
        } else if (p === curPage - 3 || p === curPage + 3) {
            html += '<li class="page-item disabled"><span class="page-link border-0">...</span></li>';
        }
    }

    // Next Button
    const nextDisabled = curPage >= maxPages ? 'disabled' : '';
    html += `<li class="page-item ${nextDisabled}">
        <button class="page-link rounded-2 px-2.5" data-page="${curPage + 1}" ${nextDisabled ? 'disabled' : ''} title="Halaman Selanjutnya">
            Next <i class="fas fa-chevron-right ms-1"></i>
        </button>
    </li>`;

    html += '</ul>';
    paginationEl.innerHTML = html;

    // Attach click events
    paginationEl.querySelectorAll('button.page-link').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const target = parseInt(this.getAttribute('data-page'), 10);
            if (target >= 1 && target <= maxPages && target !== currentPage) {
                fetchTableData(target);
                // Smooth scroll to table
                const tableCard = document.getElementById('ppeppDocumentTable');
                if (tableCard) {
                    tableCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tableSearchInput');
    const fakultasFilter = document.getElementById('tableFakultasFilter');
    const prodiFilter = document.getElementById('tableProdiFilter');
    const bidangFilter = document.getElementById('tableBidangFilter');
    const tahunFilter = document.getElementById('tableTahunFilter');
    const perPageSelect = document.getElementById('tablePerPageSelect');

    // Initial render of pagination
    renderPaginationUI(currentPage, totalPages);

    // Filter Prodi dropdown when Fakultas changes
    if (fakultasFilter && prodiFilter) {
        fakultasFilter.addEventListener('change', function() {
            const selectedFak = this.value;
            const prodiOptions = prodiFilter.querySelectorAll('option');
            
            prodiOptions.forEach(opt => {
                if (!opt.value) return;
                const optFakId = opt.getAttribute('data-fakultas-id') || '';
                if (!selectedFak || selectedFak === 'tingkat_fakultas' || optFakId === selectedFak) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                    if (prodiFilter.value === opt.value) {
                        prodiFilter.value = '';
                    }
                }
            });

            fetchTableData(1);
        });
    }

    // Search Input with 300ms Debounce
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                fetchTableData(1);
            }, 300);
        });
    }

    // Select Filters
    if (prodiFilter) prodiFilter.addEventListener('change', () => fetchTableData(1));
    if (bidangFilter) bidangFilter.addEventListener('change', () => fetchTableData(1));
    if (tahunFilter) tahunFilter.addEventListener('change', () => fetchTableData(1));
    if (perPageSelect) perPageSelect.addEventListener('change', () => fetchTableData(1));

    // Handle Browser Back / Forward navigation
    window.addEventListener('popstate', function(e) {
        const url = new URL(window.location);
        const siklusParam = (url.searchParams.get('siklus') || '').toLowerCase();
        currentPublicSiklus = siklusParam;

        // Sync tab buttons
        const tabButtons = document.querySelectorAll('#publicPpeppTabs .nav-link');
        tabButtons.forEach(b => {
            b.classList.remove('active');
            if (!siklusParam && b.textContent.includes('Semua')) {
                b.classList.add('active');
            } else if (siklusParam && b.classList.contains('tab-' + siklusParam)) {
                b.classList.add('active');
            }
        });

        // Sync inputs
        if (searchInput) searchInput.value = url.searchParams.get('search') || '';
        if (fakultasFilter) fakultasFilter.value = url.searchParams.get('fakultas') || '';
        if (prodiFilter) prodiFilter.value = url.searchParams.get('prodi') || '';
        if (bidangFilter) bidangFilter.value = url.searchParams.get('bidang') || '';
        if (tahunFilter) tahunFilter.value = url.searchParams.get('tahun') || '';
        if (perPageSelect) perPageSelect.value = url.searchParams.get('per_page') || '50';

        const pageParam = parseInt(url.searchParams.get('page') || '1', 10);
        fetchTableData(pageParam);
    });
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
