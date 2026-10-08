<?php
/**
 * Public PPEPP Dashboard for a Faculty (Tingkat Dekanat)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/header.php';
?>

<!-- Header Banner: Fakultas & Dekanat Profile (Refined & Modern) -->
<section class="page-banner text-white py-5 position-relative overflow-hidden">
    <div class="container position-relative z-index-2">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-15 text-white-50 small">
                <a href="<?= base_url() ?>" class="text-white text-decoration-none hover-gold d-inline-flex align-items-center gap-1">
                    <i class="fas fa-home"></i> Beranda
                </a>
                <span class="opacity-50">/</span>
                <a href="<?= base_url('fakultas/' . $fakultas['id']) ?>" class="text-white text-decoration-none hover-gold">
                    <?= htmlspecialchars($fakultas['nama_fakultas']) ?>
                </a>
                <span class="text-white-50">/</span>
                <span class="text-white fw-semibold">Dokumen PPEPP Dekanat</span>
            </div>
        </nav>

        <div class="row align-items-center g-4">
            <!-- Left Info -->
            <div class="col-lg-7 col-xl-8">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-20 px-3 py-1.5 rounded-pill" style="font-size: 0.78rem;">
                        <i class="fas fa-users-rectangle me-1 opacity-75"></i> Tingkat Dekanat
                    </span>
                    <span class="badge bg-primary bg-opacity-30 text-white border border-primary border-opacity-40 px-3 py-1.5 rounded-pill" style="font-size: 0.78rem;">
                        <i class="fas fa-shield-check me-1 text-info"></i> <?= $cycleStats['total'] ?> Dokumen Mutu Aktif
                    </span>
                </div>

                <h1 class="fw-bold mb-2 text-white" style="font-size: clamp(1.8rem, 3.5vw, 2.6rem); line-height: 1.25; letter-spacing: -0.5px;">
                    Dokumen Mutu Dekanat <?= htmlspecialchars($fakultas['nama_fakultas']) ?>
                </h1>
                
                <p class="text-white-50 mb-0" style="max-width: 620px; font-size: 0.95rem; line-height: 1.6;">
                    Repositori dan pemantauan penjaminan mutu SPMI tingkat Dekanat Fakultas berbasis 5 tahapan <strong>Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan (PPEPP)</strong>.
                </p>
            </div>

            <!-- Right Profile: Dekanat Executive Glass Card -->
            <div class="col-lg-5 col-xl-4">
                <div class="executive-glass-card">
                    <!-- Top Ribbon / Category Indicator -->
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white border-opacity-10">
                        <span class="badge d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill" 
                              style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.35); font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">
                            <i class="fas fa-crown text-info"></i> PIMPINAN DEKANAT FAKULTAS
                        </span>
                        <?php if (!empty($fakultas['periode_jabatan'])): ?>
                            <span class="text-white-50 small d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                <i class="far fa-calendar-check text-info opacity-75"></i>
                                <span><?= htmlspecialchars($fakultas['periode_jabatan']) ?></span>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Dekan Profile Row -->
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="executive-avatar-box executive-avatar-blue">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="text-white-50 text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                Dekan Fakultas
                            </div>
                            <div class="fw-bold text-white text-truncate mt-0.5" style="font-size: 1rem; letter-spacing: -0.2px;" title="<?= htmlspecialchars($fakultas['nama_dekan'] ?: 'Belum ditentukan') ?>">
                                <?= htmlspecialchars($fakultas['nama_dekan'] ?: 'Belum ditentukan') ?>
                            </div>
                            <?php if (!empty($fakultas['nidn_dekan'])): ?>
                                <div class="d-inline-flex align-items-center gap-1 text-primary-subtle small mt-1 px-2 py-0.5 rounded-pill" 
                                     style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.1); font-size: 0.7rem;">
                                    <i class="fas fa-id-badge opacity-75"></i> NIDN: <?= htmlspecialchars($fakultas['nidn_dekan']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Wadek Sub-Panel if exists -->
                    <?php if (!empty($fakultas['nama_wadek'])): ?>
                        <div class="executive-meta-subpanel">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <span class="text-white-50 d-flex align-items-center gap-1.5" style="font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase; font-weight: 600;">
                                    <i class="fas fa-user-group text-info"></i> Wakil Dekan
                                </span>
                            </div>
                            <div class="text-white fw-bold" style="font-size: 0.88rem; line-height: 1.35; word-break: break-word;">
                                <?= htmlspecialchars($fakultas['nama_wadek']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Dekanat Actions if Logged In -->
                    <?php if (Auth::check() && (Auth::isDekanat() || Auth::isGpm()) && (int)Auth::fakultasId() === (int)$fakultas['id']): ?>
                        <div class="d-flex gap-2 mt-3 pt-2.5 border-top border-white border-opacity-10">
                            <a href="<?= base_url('fakultas/dokumen/create') ?>" class="btn btn-warning btn-sm rounded-pill fw-bold px-3 py-1.5 text-dark flex-fill shadow-sm" style="font-size: 0.78rem;">
                                <i class="fas fa-plus me-1"></i> Unggah Dokumen
                            </a>
                            <a href="<?= base_url('fakultas/dashboard') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5 flex-fill" style="font-size: 0.78rem;">
                                <i class="fas fa-gauge me-1"></i> Dashboard Dekanat
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
$activeUnit = 'fakultas';
$activeProdiId = null;
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
                        <h4 class="fw-bold text-dark-blue mb-1">Repositori Dokumen Mutu Tingkat Fakultas</h4>
                        <p class="text-muted small mb-0">Pilih tab tahapan siklus PPEPP atau telusuri seluruh dokumen mutu tingkat Dekanat yang tersedia.</p>
                    </div>
                </div>

                <ul class="nav ppepp-nav-tabs" id="ppeppTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-all-btn" data-bs-toggle="tab" data-bs-target="#tab-all" type="button" role="tab">
                            <i class="fas fa-layer-group"></i> Semua (<?= $cycleStats['total'] ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-penetapan" id="tab-p1-btn" data-bs-toggle="tab" data-bs-target="#tab-p1" type="button" role="tab">
                            <i class="fas fa-file-signature"></i> Penetapan (<?= $cycleStats['penetapan'] ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-pelaksanaan" id="tab-p2-btn" data-bs-toggle="tab" data-bs-target="#tab-p2" type="button" role="tab">
                            <i class="fas fa-person-chalkboard"></i> Pelaksanaan (<?= $cycleStats['pelaksanaan'] ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-evaluasi" id="tab-e-btn" data-bs-toggle="tab" data-bs-target="#tab-e" type="button" role="tab">
                            <i class="fas fa-magnifying-glass-chart"></i> Evaluasi (<?= $cycleStats['evaluasi'] ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-pengendalian" id="tab-p3-btn" data-bs-toggle="tab" data-bs-target="#tab-p3" type="button" role="tab">
                            <i class="fas fa-sliders"></i> Pengendalian (<?= $cycleStats['pengendalian'] ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-peningkatan" id="tab-p4-btn" data-bs-toggle="tab" data-bs-target="#tab-p4" type="button" role="tab">
                            <i class="fas fa-arrow-trend-up"></i> Peningkatan (<?= $cycleStats['peningkatan'] ?>)
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Table Filter & Controls Toolbar -->
            <div class="card-body p-4">


                <div class="row g-3 align-items-center mb-4">
                    <!-- Search Input -->
                    <div class="col-lg-5 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" id="tableSearchInput" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama dokumen, nomor SK, kata kunci...">
                        </div>
                    </div>

                    <!-- Bidang Filter -->
                    <div class="col-lg-4 col-md-4">
                        <select id="tableBidangFilter" class="form-select bg-light">
                            <option value="">-- Semua Bidang --</option>
                            <?php foreach ($bidangList as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Per Page Pagination Selector -->
                    <div class="col-lg-3 col-md-2 d-flex justify-content-md-end align-items-center gap-2">
                        <label for="tablePerPageSelect" class="form-label small text-muted text-nowrap mb-0">Tampilkan:</label>
                        <select id="tablePerPageSelect" class="form-select form-select-sm form-select-perpage bg-light" style="min-width: 108px; width: auto;">
                            <option value="10" selected>10 data</option>
                            <option value="20">20 data</option>
                            <option value="50">50 data</option>
                            <option value="100">100 data</option>
                        </select>
                    </div>
                </div>

                <!-- Tab Panes -->
                <div class="tab-content" id="ppeppTabsContent">
                    
                    <!-- 1. All Documents Tab -->
                    <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
                        <?php renderDocTable($allDocuments, 'ppeppDocumentTable'); ?>
                    </div>

                    <!-- 2. Penetapan Tab -->
                    <div class="tab-pane fade" id="tab-p1" role="tabpanel">
                        <?php renderDocTable($docsByCycle['penetapan']); ?>
                    </div>

                    <!-- 3. Pelaksanaan Tab -->
                    <div class="tab-pane fade" id="tab-p2" role="tabpanel">
                        <?php renderDocTable($docsByCycle['pelaksanaan']); ?>
                    </div>

                    <!-- 4. Evaluasi Tab -->
                    <div class="tab-pane fade" id="tab-e" role="tabpanel">
                        <?php renderDocTable($docsByCycle['evaluasi']); ?>
                    </div>

                    <!-- 5. Pengendalian Tab -->
                    <div class="tab-pane fade" id="tab-p3" role="tabpanel">
                        <?php renderDocTable($docsByCycle['pengendalian']); ?>
                    </div>

                    <!-- 6. Peningkatan Tab -->
                    <div class="tab-pane fade" id="tab-p4" role="tabpanel">
                        <?php renderDocTable($docsByCycle['peningkatan']); ?>
                    </div>

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

<?php
/**
 * Helper function to render table markup
 */
function renderDocTable(array $docs, string $tableId = ''): void {
    ?>
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0" <?= $tableId ? 'id="'.$tableId.'"' : '' ?>>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama & Nomor Dokumen</th>
                    <th>Bidang</th>
                    <th>Jenis</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($docs)): ?>
                    <tr class="no-matching-docs">
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 text-secondary d-block"></i>
                            <h6 class="fw-bold">Belum ada dokumen dalam kategori ini</h6>
                            <p class="small mb-0">Dokumen mutu tingkat fakultas akan ditampilkan setelah diunggah oleh Admin Fakultas.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($docs as $doc): 
                        $adaptive = get_doc_adaptive_standards_and_bidang($doc);
                    ?>
                        <tr class="doc-row" 
                            data-title="<?= htmlspecialchars($doc['nama_dokumen'] . ' ' . $adaptive['standards_text']) ?>" 
                            data-nomor="<?= htmlspecialchars($doc['nomor_dokumen'] ?? '') ?>" 
                            data-bidang-id="<?= htmlspecialchars(implode(',', $adaptive['bidang_ids'])) ?>">
                            <td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($doc['nama_dokumen']) ?></div>
                                <?php if ($doc['nomor_dokumen']): ?>
                                    <div class="small text-muted mb-1"><i class="fas fa-hashtag me-1"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></div>
                                <?php endif; ?>
                                <?php if ($doc['tahun_akademik']): ?>
                                    <span class="badge bg-light text-secondary border me-1" style="font-size: 0.7rem;">TA <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($adaptive['standards_html'])): ?>
                                    <div class="mt-1">
                                        <?= $adaptive['standards_html'] ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $adaptive['bidang_html'] ?>
                            </td>
                            <td>
                                <?php 
                                $files = $doc['files'] ?? [];
                                $filesCount = count($files);
                                $gdriveLinks = parse_external_links($doc['external_link'] ?? '');
                                $linkCount = count($gdriveLinks);
                                $hasFiles = ($filesCount > 0 || (!empty($doc['file_path']) && $doc['jenis_upload'] !== 'link'));
                                $hasLinks = ($linkCount > 0);
                                $isKombinasi = ($doc['jenis_upload'] === 'kombinasi') || ($hasFiles && $hasLinks);

                                if ($isKombinasi): 
                                ?>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge" style="background: #EDE9FE; color: #7C3AED; border: 1px solid #DDD6FE; font-size: 0.72rem;">
                                            <i class="fas fa-folder-open me-1"></i><?= $filesCount ?: 1 ?> File
                                        </span>
                                        <span class="badge badge-link" style="font-size: 0.72rem;">
                                            <i class="fab fa-google-drive me-1"></i><?= $linkCount ?> Drive
                                        </span>
                                    </div>
                                <?php elseif ($hasFiles): ?>
                                    <?php if ($filesCount > 1): ?>
                                        <span class="badge badge-file"><i class="fas fa-folder-open me-1"></i> <?= $filesCount ?> File PDF</span>
                                    <?php else: ?>
                                        <span class="badge badge-file"><i class="fas fa-file-pdf me-1"></i> File PDF</span>
                                    <?php endif; ?>
                                <?php elseif ($hasLinks): ?>
                                    <?php if ($linkCount > 1): ?>
                                        <span class="badge badge-link"><i class="fab fa-google-drive me-1"></i> <?= $linkCount ?> Link GDrive</span>
                                    <?php else: ?>
                                        <span class="badge badge-link"><i class="fab fa-google-drive me-1"></i> Link GDrive</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1.5 flex-wrap">
                                    <?php if ($hasFiles): ?>
                                        <?php if ($filesCount > 1): ?>
                                            <div class="dropdown d-inline-block">
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-eye me-1"></i> <?= $isKombinasi ? 'File (' . $filesCount . ')' : 'Lihat (' . $filesCount . ')' ?>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 300px; max-width: min(92vw, 440px); width: max-content; font-size: 0.78rem;">
                                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS DOKUMEN:</li>
                                                    <?php foreach ($files as $fItem): 
                                                        $fLimit = isset($fItem['is_page_limited']) && (int)$fItem['is_page_limited'] === 0 ? 0 : (int)($fItem['public_page_limit'] ?? 1);
                                                        $fCanDl = (int)($fItem['can_download_public'] ?? 0);
                                                    ?>
                                                        <li class="mb-1.5">
                                                            <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#pdfPreviewModal" 
                                                                    data-pdf-url="<?= base_url($fItem['file_path']) ?>" 
                                                                    data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                                    data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                                    data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null)) ?>"
                                                                    data-public-limit="<?= $fLimit ?>"
                                                                    data-can-download="<?= $fCanDl ?>">
                                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                                    <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate" title="<?= htmlspecialchars($fItem['file_name']) ?>">
                                                                        <i class="fas fa-file-pdf text-danger flex-shrink-0"></i>
                                                                        <span class="text-truncate" style="max-width: 220px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                                    </span>
                                                                    <span class="badge bg-light text-muted border flex-shrink-0" style="font-size: 0.65rem;"><?= htmlspecialchars($fItem['file_size'] ?: 'PDF') ?></span>
                                                                </div>
                                                                <?= render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null) ?>
                                                                <?php if (!empty($fItem['narasi'])): ?>
                                                                    <div class="small text-muted bg-light p-1.5 rounded border border-light-subtle" style="font-size: 0.72rem; line-height: 1.45; white-space: pre-line; word-break: break-word;">
                                                                        <i class="fas fa-quote-left text-primary opacity-50 me-1"></i><?= htmlspecialchars($fItem['narasi']) ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </button>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php else: ?>
                                            <?php 
                                            $singleFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'narasi' => '']; 
                                            $sLimit = isset($singleFile['is_page_limited']) && (int)$singleFile['is_page_limited'] === 0 ? 0 : (int)($singleFile['public_page_limit'] ?? $doc['public_page_limit'] ?? 1);
                                            $sCanDl = (int)($singleFile['can_download_public'] ?? $doc['can_download_public'] ?? 0);
                                            ?>
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#pdfPreviewModal" 
                                                    data-pdf-url="<?= base_url($singleFile['file_path']) ?>" 
                                                    data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                                    data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                                    data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null)) ?>"
                                                    data-public-limit="<?= $sLimit ?>"
                                                    data-can-download="<?= $sCanDl ?>">
                                                <i class="fas fa-eye me-1"></i> <?= $isKombinasi ? 'File PDF' : 'Lihat' ?>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($hasLinks): ?>
                                        <?php 
                                        $isLoggedIn = is_user_logged_in();
                                        ?>
                                        <?php if ($linkCount > 1): ?>
                                            <div class="dropdown d-inline-block">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 dropdown-toggle shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fab fa-google-drive me-1"></i> <?= $isKombinasi ? 'Drive (' . $linkCount . ')' : 'Tautan (' . $linkCount . ')' ?>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 300px; max-width: min(92vw, 440px); width: max-content; font-size: 0.78rem;">
                                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH TAUTAN DOKUMEN:</li>
                                                    <?php foreach ($gdriveLinks as $lIdx => $linkObj): 
                                                        $gUrl = $linkObj['url'];
                                                        $gNarasi = $linkObj['narasi'];
                                                        $gCanDl = (int)($linkObj['can_download_public'] ?? 0);
                                                        $canAccessLink = $isLoggedIn || $gCanDl === 1;
                                                    ?>
                                                        <li class="mb-1.5">
                                                            <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#pdfPreviewModal" 
                                                                    data-is-link="1"
                                                                    data-link-url="<?= htmlspecialchars($gUrl) ?>" 
                                                                    data-doc-title="<?= htmlspecialchars($doc['nama_dokumen']) . ' (Tautan #' . ($lIdx + 1) . ')' ?>"
                                                                    data-doc-narasi="<?= htmlspecialchars($gNarasi) ?>"
                                                                    data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($linkObj['sub_bidang_ids'] ?? null)) ?>"
                                                                    data-can-access="<?= $canAccessLink ? 1 : 0 ?>"
                                                                    data-can-download="<?= $gCanDl ?>">
                                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                                    <span class="d-flex align-items-center gap-1.5 fw-semibold text-primary text-truncate">
                                                                        <i class="fab fa-google-drive flex-shrink-0"></i>
                                                                        <span>Tautan GDrive #<?= $lIdx + 1 ?></span>
                                                                    </span>
                                                                    <?php if ($canAccessLink): ?>
                                                                        <span class="badge bg-light text-primary border flex-shrink-0" style="font-size: 0.65rem;">Lihat</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary flex-shrink-0" style="font-size: 0.65rem;"><i class="fas fa-lock me-0.5"></i> Terbatas</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <?= render_sub_standar_badges($linkObj['sub_bidang_ids'] ?? null) ?>
                                                                <?php if (!empty($gNarasi)): ?>
                                                                    <div class="small text-muted bg-light p-1.5 rounded border border-light-subtle" style="font-size: 0.72rem; line-height: 1.45; white-space: pre-line; word-break: break-word;">
                                                                        <i class="fas fa-quote-left text-primary opacity-50 me-1"></i><?= htmlspecialchars($gNarasi) ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </button>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php else: 
                                            $singleLink = $gdriveLinks[0];
                                            $sLinkCanDl = (int)($singleLink['can_download_public'] ?? 0);
                                            $canAccessSingle = $isLoggedIn || $sLinkCanDl === 1;
                                        ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#pdfPreviewModal" 
                                                    data-is-link="1"
                                                    data-link-url="<?= htmlspecialchars($singleLink['url']) ?>" 
                                                    data-doc-title="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-doc-narasi="<?= htmlspecialchars($singleLink['narasi'] ?? '') ?>"
                                                    data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleLink['sub_bidang_ids'] ?? null)) ?>"
                                                    data-can-access="<?= $canAccessSingle ? 1 : 0 ?>"
                                                    data-can-download="<?= $sLinkCanDl ?>"
                                                    title="<?= htmlspecialchars($singleLink['narasi'] ?? '') ?>">
                                                <i class="fab fa-google-drive me-1"></i> <?= $isKombinasi ? 'GDrive' : 'Lihat' ?>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!$hasFiles && !$hasLinks): ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
