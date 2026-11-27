<?php
/**
 * Public PPEPP Dashboard for a Program Studi
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/header.php';
?>

<!-- Header Banner: Prodi & Kaprodi Profile (Refined & Modern) -->
<section class="page-banner text-white py-5 position-relative overflow-hidden">
    <div class="container position-relative z-index-2">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-15 text-white-50 small">
                <a href="<?= base_url() ?>" class="text-white text-decoration-none hover-gold d-inline-flex align-items-center gap-1">
                    <i class="fas fa-home"></i> Beranda
                </a>
                <span class="opacity-50">/</span>
                <a href="<?= base_url('fakultas/' . $prodi['fakultas_id']) ?>" class="text-white text-decoration-none hover-gold">
                    <?= htmlspecialchars($prodi['nama_fakultas']) ?>
                </a>
                <span class="text-white-50">/</span>
                <span class="text-white fw-semibold"><?= htmlspecialchars($prodi['nama_prodi']) ?></span>
            </div>
        </nav>

        <div class="row align-items-center g-4">
            <!-- Left Info -->
            <div class="col-lg-7 col-xl-8">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-20 px-3 py-1.5 rounded-pill" style="font-size: 0.78rem;">
                        <i class="fas fa-building-columns me-1 opacity-75"></i> <?= htmlspecialchars($prodi['nama_fakultas']) ?>
                    </span>
                    <span class="badge bg-primary bg-opacity-30 text-white border border-primary border-opacity-40 px-3 py-1.5 rounded-pill" style="font-size: 0.78rem;">
                        <i class="fas fa-shield-check me-1 text-info"></i> <?= $cycleStats['total'] ?> Dokumen Mutu Aktif
                    </span>
                </div>

                <h1 class="fw-bold mb-2 text-white" style="font-size: clamp(1.8rem, 3.5vw, 2.6rem); line-height: 1.25; letter-spacing: -0.5px;">
                    Program Studi <?= htmlspecialchars($prodi['nama_prodi']) ?>
                </h1>
                
                <p class="text-white-50 mb-0" style="max-width: 620px; font-size: 0.95rem; line-height: 1.6;">
                    Pusat repositori dan monitoring siklus SPMI berbasis tahapan <strong>Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan (PPEPP)</strong>.
                </p>
            </div>

            <!-- Right Profile: Kaprodi Executive Glass Card -->
            <div class="col-lg-5 col-xl-4">
                <div class="executive-glass-card">
                    <!-- Top Ribbon / Category Indicator -->
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white border-opacity-10">
                        <span class="badge d-inline-flex align-items-center px-2.5 py-1 rounded-pill" 
                              style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.35); font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">
                            KETUA PROGRAM STUDI
                        </span>
                        <span class="text-white-50 small d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                            <i class="fas fa-landmark text-primary-subtle opacity-75"></i>
                            <span><?= htmlspecialchars($prodi['nama_fakultas']) ?></span>
                        </span>
                    </div>

                    <!-- Kaprodi Profile Row -->
                    <div class="mb-3">
                        <div class="text-white-50 text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                            Kaprodi <?= htmlspecialchars($prodi['jenjang']) ?>
                        </div>
                        <div class="fw-bold text-white text-truncate mt-1" style="font-size: 1.05rem; letter-spacing: -0.2px; line-height: 1.35;" title="<?= htmlspecialchars($prodi['nama_kaprodi'] ?: 'Belum ditentukan') ?>">
                            <?= htmlspecialchars($prodi['nama_kaprodi'] ?: 'Belum ditentukan') ?>
                        </div>
                        <?php if (!empty($prodi['nidn_kaprodi'])): ?>
                            <div class="d-inline-flex align-items-center gap-1 text-primary-subtle small mt-1.5 px-2.5 py-0.5 rounded-pill" 
                                 style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.1); font-size: 0.72rem;">
                                <span>NIDN: <?= htmlspecialchars($prodi['nidn_kaprodi']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Prodi Metadata Sub-Panel (Spacious & Clean without truncation) -->
                    <div class="executive-meta-subpanel">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="text-white-50 d-flex align-items-center gap-1.5" style="font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase; font-weight: 600;">
                                <i class="fas fa-graduation-cap text-info"></i> Program Studi
                            </span>
                            <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-15 px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                <?= htmlspecialchars($prodi['jenjang']) ?>
                            </span>
                        </div>
                        <div class="text-white fw-bold" style="font-size: 0.88rem; line-height: 1.35; word-break: break-word;">
                            <?= htmlspecialchars($prodi['nama_prodi']) ?>
                        </div>
                    </div>

                    <!-- Kaprodi / Sekprodi Actions if Logged In -->
                    <?php if (Auth::check() && Auth::isProdi() && (int)Auth::prodiId() === (int)$prodi['id']): ?>
                        <div class="d-flex gap-2 mt-3 pt-2.5 border-top border-white border-opacity-10">
                            <a href="<?= base_url('prodi/dokumen/create') ?>" class="btn btn-warning btn-sm rounded-pill fw-bold px-3 py-1.5 text-dark flex-fill shadow-sm" style="font-size: 0.78rem;">
                                <i class="fas fa-plus me-1"></i> Unggah Dokumen
                            </a>
                            <a href="<?= base_url('prodi/dashboard') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5 flex-fill" style="font-size: 0.78rem;">
                                <i class="fas fa-gauge me-1"></i> Dashboard Prodi
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sub-Menu Navigasi Unit PPEPP (Seragam: Dekanat & Prodi) -->
<?php
$activeUnit = 'prodi';
$activeProdiId = $prodi['id'];
require ROOT_PATH . '/views/public/components/ppepp_unit_nav.php';
?>

<!-- Main PPEPP Document Table & Tabs Section -->
<section class="py-4 py-lg-5 bg-main">
    <div class="container">
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <!-- 5 Siklus Tabs Header -->
            <div class="card-header bg-white border-bottom pt-4 px-4 pb-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h4 class="fw-bold text-dark-blue mb-1">Repositori Dokumen Mutu</h4>
                        <p class="text-muted small mb-0">Pilih tab tahapan siklus PPEPP atau telusuri seluruh dokumen mutu yang tersedia.</p>
                    </div>
                </div>

                <ul class="nav ppepp-nav-tabs" id="ppeppTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-all-btn" type="button" role="tab" data-siklus-tab="" onclick="changeProdiPpeppCycleTab('', this)">
                            <i class="fas fa-layer-group"></i> Semua <span class="tab-cycle-count">(<?= $cycleStats['total'] ?>)</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-penetapan" id="tab-p1-btn" type="button" role="tab" data-siklus-tab="penetapan" onclick="changeProdiPpeppCycleTab('penetapan', this)">
                            <i class="fas fa-file-signature"></i> Penetapan <span class="tab-cycle-count">(<?= $cycleStats['penetapan'] ?>)</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-pelaksanaan" id="tab-p2-btn" type="button" role="tab" data-siklus-tab="pelaksanaan" onclick="changeProdiPpeppCycleTab('pelaksanaan', this)">
                            <i class="fas fa-person-chalkboard"></i> Pelaksanaan <span class="tab-cycle-count">(<?= $cycleStats['pelaksanaan'] ?>)</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-evaluasi" id="tab-e-btn" type="button" role="tab" data-siklus-tab="evaluasi" onclick="changeProdiPpeppCycleTab('evaluasi', this)">
                            <i class="fas fa-magnifying-glass-chart"></i> Evaluasi <span class="tab-cycle-count">(<?= $cycleStats['evaluasi'] ?>)</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-pengendalian" id="tab-p3-btn" type="button" role="tab" data-siklus-tab="pengendalian" onclick="changeProdiPpeppCycleTab('pengendalian', this)">
                            <i class="fas fa-sliders"></i> Pengendalian <span class="tab-cycle-count">(<?= $cycleStats['pengendalian'] ?>)</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-peningkatan" id="tab-p4-btn" type="button" role="tab" data-siklus-tab="peningkatan" onclick="changeProdiPpeppCycleTab('peningkatan', this)">
                            <i class="fas fa-arrow-trend-up"></i> Peningkatan <span class="tab-cycle-count">(<?= $cycleStats['peningkatan'] ?>)</span>
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Table Filter & Controls Toolbar -->
            <div class="card-body p-4">


                <!-- Toolbar: Pencarian (Baris 1) & Filter Dropdown (Baris 2) -->
                <div class="mb-4">
                    <!-- Baris 1: Pencarian Cepat & Pengaturan Jumlah Data -->
                    <div class="row g-2.5 mb-2.5 align-items-center">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="fas fa-search"></i></span>
                                <input type="text" id="tableSearchInput" class="form-control bg-light border-start-0 ps-1" placeholder="Cari nama dokumen, nomor SK, kata kunci...">
                            </div>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-2">
                            <label for="tablePerPageSelect" class="form-label small text-muted text-nowrap mb-0 d-none d-sm-inline">Tampilkan:</label>
                            <select id="tablePerPageSelect" class="form-select bg-light" style="min-width: 95px; width: auto;">
                                <option value="10" selected>10 data</option>
                                <option value="20">20 data</option>
                                <option value="50">50 data</option>
                                <option value="100">100 data</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 2: Filter Bidang & Tahun Akademik -->
                    <div class="row g-2.5 align-items-center">
                        <!-- Bidang Filter -->
                        <div class="col-md-6 col-sm-6">
                            <select id="tableBidangFilter" class="form-select bg-light">
                                <option value="">Semua Bidang</option>
                                <?php foreach ($bidangList as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filter Tahun Akademik -->
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

                <!-- Single Optimized Table Container -->
                <div id="ppeppTableContainer">
                    <?php 
                    $docs = $allDocuments; 
                    $tableId = 'ppeppDocumentTable'; 
                    $unitTypeLabel = 'Admin Prodi'; 
                    require ROOT_PATH . '/views/public/doc_table_partial.php'; 
                    ?>
                </div>

                <!-- Footer Table Pagination Controls -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top mt-3">
                    <div id="tableInfo" class="small text-muted">
                        Menampilkan <?= min(count($allDocuments), 10) ?> dari <?= count($allDocuments) ?> dokumen
                    </div>
                    <div id="tablePagination"></div>
                </div>

            </div>
        </div>

    </div>
</section>

<script>
function changeProdiPpeppCycleTab(cycle, btn) {
    document.querySelectorAll('#ppeppTabs .nav-link').forEach(el => el.classList.remove('active'));
    if (btn) btn.classList.add('active');
    if (typeof window.setProdiPpeppCycleFilter === 'function') {
        window.setProdiPpeppCycleFilter(cycle);
    }
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
