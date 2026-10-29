<?php
/**
 * Pusat Review Dokumen Mutu PPEPP Terpusat (Admin LPM / Super Admin)
 * SPMI PPEPP UNIKA Soegijapranata
 * Desain: LPM Design System (Navy + Purple Theme)
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-decoration-none text-muted"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark-blue fw-semibold" aria-current="page">Pusat Review Dokumen</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark-blue mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-check text-primary"></i> Pusat Review Dokumen Mutu
                <?php if ($reviewStats['perlu_tindakan'] > 0): ?>
                    <span class="badge bg-danger rounded-pill fs-6 px-2.5 py-1 shadow-2xs">
                        <?= $reviewStats['perlu_tindakan'] ?> Menunggu Tindakan
                    </span>
                <?php endif; ?>
            </h3>
            <p class="text-muted small mb-0">Verifikasi, validasi, dan pantau status dokumen PPEPP seluruh universitas secara terpadu tanpa berpindah halaman.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-2xs" onclick="window.location.reload();">
                <i class="fas fa-rotate me-1"></i> Muat Ulang
            </button>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-2xs">
                <i class="fas fa-gauge me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Alert Flash Messages -->
    <?php if ($flash = Auth::getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-<?= $flash['type'] === 'success' ? 'circle-check text-success' : 'triangle-exclamation text-danger' ?> fs-5"></i>
                <div><?= $flash['message'] ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- KPI Metric Summary Cards (Filterable Shortcuts) -->
    <div class="row g-3 mb-4">
        <!-- 1. Perlu Tindakan Segera -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 kpi-card cursor-pointer border-start border-4 border-danger" onclick="applyStatusFilter('perlu_tindakan')" title="Klik untuk filter: Dokumen yang perlu diverifikasi">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Perlu Tindakan</span>
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger p-2"><i class="fas fa-bell"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-danger mb-0"><?= $reviewStats['perlu_tindakan'] ?></div>
                <div class="small text-muted" style="font-size: 0.7rem;">Belum review &amp; perbaikan</div>
            </div>
        </div>

        <!-- 2. Menunggu Telaah LPM -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 kpi-card cursor-pointer border-start border-4 border-warning" onclick="applyStatusFilter('menunggu_review_lpm')" title="Klik untuk filter: Dokumen disetujui GPM &amp; menunggu telaah LPM">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Menunggu Telaah LPM</span>
                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning p-2"><i class="fas fa-clock"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-warning mb-0"><?= $reviewStats['menunggu_review_lpm'] ?? $reviewStats['belum_direview'] ?></div>
                <div class="small text-muted" style="font-size: 0.7rem;">Diteruskan dari GPM</div>
            </div>
        </div>

        <!-- 3. Sudah Diperbaiki (Revisi Masuk) -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 kpi-card cursor-pointer border-start border-4 border-warning" onclick="applyStatusFilter('sudah_diperbaiki')" title="Klik untuk filter: Dokumen perbaikan masuk">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Revisi Masuk</span>
                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning p-2"><i class="fas fa-arrows-rotate"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-warning mb-0"><?= $reviewStats['sudah_diperbaiki'] ?></div>
                <div class="small text-muted" style="font-size: 0.7rem;">Prodi selesai perbaiki</div>
            </div>
        </div>

        <!-- 4. Sedang Direvisi Prodi -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 kpi-card cursor-pointer border-start border-4 border-danger border-opacity-50" onclick="applyStatusFilter('perlu_perbaikan')" title="Klik untuk filter: Menunggu respon revisi prodi">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Sedang Direvisi</span>
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger p-2"><i class="fas fa-triangle-exclamation"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-danger text-opacity-75 mb-0"><?= $reviewStats['perlu_perbaikan'] ?></div>
                <div class="small text-muted" style="font-size: 0.7rem;">Menunggu respon unit</div>
            </div>
        </div>

        <!-- 5. Sesuai Standar Mutu -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 kpi-card cursor-pointer border-start border-4 border-success" onclick="applyStatusFilter('sesuai')" title="Klik untuk filter: Dokumen telah disetujui">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Sesuai Standar</span>
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success p-2"><i class="fas fa-circle-check"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-success mb-0"><?= $reviewStats['sesuai'] ?></div>
                <div class="small text-muted" style="font-size: 0.7rem;">Valid &amp; disetujui LPM</div>
            </div>
        </div>

        <!-- 6. Total Dokumen Masuk LPM -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 kpi-card cursor-pointer border-start border-4 border-primary" onclick="applyStatusFilter('all')" title="Klik untuk menampilkan semua dokumen yang masuk ke LPM">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Dokumen LPM</span>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary p-2"><i class="fas fa-folder-closed"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-primary mb-0"><?= $reviewStats['total'] ?></div>
                <div class="small text-muted" style="font-size: 0.7rem;">Antrean masuk ke LPM</div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-tabs-modern px-3 pt-2" id="reviewHubTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold d-flex align-items-center gap-2 py-3 px-3" id="queue-tab" data-bs-toggle="tab" data-bs-target="#queue-pane" type="button" role="tab" aria-controls="queue-pane" aria-selected="true">
                        <i class="fas fa-list-check text-primary"></i>
                        <span>Antrean Dokumen Terpusat</span>
                        <span class="badge bg-danger rounded-pill px-2 py-0.5 ms-1" id="tabQueueBadge"><?= $reviewStats['perlu_tindakan'] ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold d-flex align-items-center gap-2 py-3 px-3" id="rekap-tab" data-bs-toggle="tab" data-bs-target="#rekap-pane" type="button" role="tab" aria-controls="rekap-pane" aria-selected="false">
                        <i class="fas fa-chart-pie text-secondary"></i>
                        <span>Rekapitulasi Status per Unit (Prodi &amp; Dekanat)</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="reviewHubTabContent">
            <!-- ========================================== -->
            <!-- TAB 1: ANTREAN DOKUMEN TERPUSAT (ALL DOCS) -->
            <!-- ========================================== -->
            <div class="tab-pane fade show active p-4" id="queue-pane" role="tabpanel" aria-labelledby="queue-tab">

                <!-- 1. Status Filter Pills Bar -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
                    <span class="small fw-bold text-muted me-1" style="font-size: 0.75rem; text-transform: uppercase;"><i class="fas fa-filter me-1"></i>Filter Cepat Status:</span>
                    <button type="button" class="btn btn-sm rounded-pill px-3 filter-pill active" data-filter="perlu_tindakan" onclick="applyStatusFilter('perlu_tindakan')">
                        <i class="fas fa-bell text-danger me-1"></i> Perlu Tindakan Segera
                        <span class="badge bg-danger rounded-pill ms-1"><?= $reviewStats['perlu_tindakan'] ?></span>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 filter-pill" data-filter="menunggu_review_lpm" onclick="applyStatusFilter('menunggu_review_lpm')">
                        <i class="fas fa-clock text-warning me-1"></i> Menunggu Review LPM
                        <span class="badge bg-warning text-dark rounded-pill ms-1"><?= $reviewStats['menunggu_review_lpm'] ?? $reviewStats['belum_direview'] ?></span>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 filter-pill" data-filter="sudah_diperbaiki" onclick="applyStatusFilter('sudah_diperbaiki')">
                        <i class="fas fa-arrows-rotate text-warning me-1"></i> Revisi Masuk
                        <span class="badge bg-warning text-dark rounded-pill ms-1"><?= $reviewStats['sudah_diperbaiki'] ?></span>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 filter-pill" data-filter="perlu_perbaikan" onclick="applyStatusFilter('perlu_perbaikan')">
                        <i class="fas fa-triangle-exclamation text-danger me-1"></i> Sedang Direvisi
                        <span class="badge bg-light text-danger border rounded-pill ms-1"><?= $reviewStats['perlu_perbaikan'] ?></span>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 filter-pill" data-filter="sesuai" onclick="applyStatusFilter('sesuai')">
                        <i class="fas fa-circle-check text-success me-1"></i> Sesuai Standar
                        <span class="badge bg-success rounded-pill ms-1"><?= $reviewStats['sesuai'] ?></span>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 filter-pill" data-filter="all" onclick="applyStatusFilter('all')">
                        <i class="fas fa-folder-open text-primary me-1"></i> Semua Dokumen (<?= $reviewStats['total'] ?>)
                    </button>
                </div>

                <!-- 2. Advanced Filter Bar: Pencarian (Baris 1) & Filter Dropdown (Baris 2) -->
                <div class="bg-light p-3 rounded-3 border mb-4">
                    <!-- Baris 1: Live Search & Reset Button -->
                    <div class="row g-2.5 mb-2.5 align-items-center">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted px-3"><i class="fas fa-search"></i></span>
                                <input type="text" id="liveSearchInput" class="form-control bg-white border-start-0 ps-1" placeholder="Cari dokumen, SK, uploader..." onkeyup="filterHubTable()">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="resetHubFilters()" title="Reset semua filter ke default">
                                <i class="fas fa-arrow-rotate-left me-1"></i> Reset Filter
                            </button>
                        </div>
                    </div>

                    <!-- Baris 2: Dropdown Filter -->
                    <div class="row g-2 align-items-center">
                        <!-- Filter Fakultas -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <select class="form-select form-select-sm filter-hub-select" id="fakultasFilter" onchange="onFakultasChange()">
                                <option value="">Semua Fakultas</option>
                                <?php foreach ($fakultasList as $fak): ?>
                                    <option value="<?= $fak['id'] ?>"><?= htmlspecialchars($fak['nama_fakultas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filter Program Studi (Dynamic) -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <select class="form-select form-select-sm filter-hub-select" id="prodiFilter" onchange="filterHubTable()">
                                <option value="">Semua Program Studi</option>
                                <?php foreach ($prodiList as $prd): ?>
                                    <option value="<?= $prd['id'] ?>" data-fakultas-id="<?= $prd['fakultas_id'] ?>">
                                        <?= htmlspecialchars($prd['nama_prodi']) ?> (<?= $prd['jenjang'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filter Siklus PPEPP -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <select class="form-select form-select-sm filter-hub-select" id="siklusFilter" onchange="filterHubTable()">
                                <option value="">Semua Siklus PPEPP</option>
                                <option value="penetapan">Penetapan (P1)</option>
                                <option value="pelaksanaan">Pelaksanaan (P2)</option>
                                <option value="evaluasi">Evaluasi (E)</option>
                                <option value="pengendalian">Pengendalian (P3)</option>
                                <option value="peningkatan">Peningkatan (P4)</option>
                            </select>
                        </div>

                        <!-- Filter Bidang -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <select class="form-select form-select-sm filter-hub-select" id="bidangFilter" onchange="filterHubTable()">
                                <option value="">Semua Bidang</option>
                                <?php foreach ($bidangList as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filter Tahun Akademik -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <select class="form-select form-select-sm filter-hub-select" id="tahunFilter" onchange="filterHubTable()">
                                <option value="">Semua Tahun Akademik</option>
                                <?php foreach (get_distinct_tahun_akademik() as $ta): ?>
                                    <option value="<?= htmlspecialchars($ta) ?>">Tahun Akademik <?= htmlspecialchars($ta) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table Header Metadata & Row Counter -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2.5">
                    <div class="small text-muted fw-semibold" id="tableCountDisplay">
                        Menampilkan <span class="fw-bold text-dark" id="visibleDocCount"><?= count($documents) ?></span> dari <span class="fw-bold text-dark"><?= number_format($totalFiltered, 0, ",", ".") ?></span> Dokumen
                    </div>
                    <div class="small text-muted fst-italic" style="font-size: 0.72rem;">
                        <i class="fas fa-circle-info text-primary me-1"></i> Tip: Klik tombol <strong>Setujui</strong> atau <strong>Minta Revisi</strong> untuk verifikasi instan tanpa reload halaman.
                    </div>
                </div>

                <!-- Master Documents Table -->
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0 w-100" id="hubMasterTable" style="font-size: 0.83rem;">
                        <thead class="table-light text-secondary" style="font-size: 0.73rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <tr>
                                <th class="text-center ps-3" style="width: 38px;">No</th>
                                <th style="min-width: 220px;">Dokumen Mutu &amp; Unit</th>
                                <th class="text-center" style="width: 90px;">Siklus</th>
                                <th style="min-width: 130px; max-width: 170px;">Bidang &amp; Standar</th>
                                <th class="text-center" style="width: 85px;">Berkas</th>
                                <th class="text-center" style="width: 120px;">Status Review</th>
                                <th class="text-center pe-3" style="width: 125px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="hubTableBody">
                            <?php if (empty($documents)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <div class="mb-2">
                                            <i class="fas fa-folder-open text-secondary opacity-50" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <div class="fw-semibold text-dark">Tidak ada dokumen yang sesuai dengan kriteria filter</div>
                                        <div class="small text-muted">Silakan sesuaikan filter fakultas, siklus, atau kata kunci pencarian.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $rowNo = $startNum;
                                require ROOT_PATH . '/views/super_admin/review_hub_rows.php'; 
                                ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Footer Bar (Review Hub) -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top mt-3" id="hubPaginationWrapper">
                    <div class="small text-muted" id="hubTableInfo">
                        Menampilkan <span class="fw-bold text-dark" id="hubStartNum"><?= $startNum ?></span> - <span class="fw-bold text-dark" id="hubEndNum"><?= $endNum ?></span> dari <span class="fw-bold text-dark" id="hubTotalFiltered"><?= number_format($totalFiltered, 0, ',', '.') ?></span> Dokumen
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2.5">
                        <div class="d-flex align-items-center gap-1.5 small text-muted">
                            <span style="font-size: 0.78rem;">Ukuran Halaman:</span>
                            <select class="form-select form-select-sm" id="hubPerPageSelect" style="width: 95px; font-size: 0.78rem;" onchange="changeHubPerPage(this.value)">
                                <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10 data</option>
                                <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20 data</option>
                                <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 data</option>
                                <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 data</option>
                            </select>
                        </div>
                        <nav aria-label="Review Hub Pagination">
                            <ul class="pagination pagination-sm mb-0" id="hubPaginationNav">
                                <!-- Populated dynamically by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Empty State When Filter Yields 0 Results -->
                <div id="noMatchingDocsMsg" class="text-center py-5 d-none">
                    <i class="fas fa-filter-circle-xmark text-secondary opacity-50 mb-3" style="font-size: 3rem;"></i>
                    <h6 class="fw-bold text-dark">Tidak Ada Dokumen yang Cocok dengan Filter</h6>
                    <p class="text-muted small mb-3">Cobalah mengubah kata kunci pencarian atau mengatur ulang filter status/fakultas.</p>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4" onclick="resetHubFilters()">
                        <i class="fas fa-rotate-left me-1"></i> Reset Semua Filter
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: REKAPITULASI STATUS PER UNIT        -->
            <!-- ========================================== -->
            <div class="tab-pane fade p-4" id="rekap-pane" role="tabpanel" aria-labelledby="rekap-tab">
                <!-- Section A: Kepatuhan Tingkat Fakultas (Dekanat) -->
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark-blue mb-1">
                                <i class="fas fa-landmark text-primary me-2"></i> Rekapitulasi Dokumen Tingkat Fakultas (Dekanat)
                            </h5>
                            <p class="text-muted small mb-0">Status kepatuhan dokumen PPEPP level fakultas yang diunggah oleh pimpinan dekanat.</p>
                        </div>
                    </div>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light text-secondary" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-3" style="width: 100px;">Kode</th>
                                    <th>Fakultas</th>
                                    <th>Pimpinan Dekan</th>
                                    <th class="text-center">Total Dokumen</th>
                                    <th class="text-center">Perlu Review</th>
                                    <th class="text-center">Perlu Revisi</th>
                                    <th class="text-center">Sesuai Mutu</th>
                                    <th style="min-width: 160px;">Progres Kepatuhan</th>
                                    <th class="text-center pe-3" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fakultasSummaries as $fsum): 
                                    $totFak = (int)$fsum['total_dokumen_fakultas'];
                                    $revFak = (int)$fsum['perlu_review_count'];
                                    $rejFak = (int)$fsum['revisi_count'];
                                    $appFak = (int)$fsum['sesuai_count'];
                                    $pctFak = (int)($fsum['progres_kepatuhan'] ?? 0);
                                    $cycles = $fsum['cycles'] ?? [];
                                ?>
                                    <tr class="align-middle">
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-1.5">
                                                <button class="btn btn-xs btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center toggle-cycles-btn" 
                                                        style="width: 24px; height: 24px;" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapse-fak-<?= $fsum['id'] ?>" 
                                                        aria-expanded="false" 
                                                        title="Klik untuk melihat dropdown judul siklus PPEPP">
                                                    <i class="fas fa-chevron-down transition-transform"></i>
                                                </button>
                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($fsum['kode_fakultas']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="text-decoration-none fw-bold text-dark-blue d-inline-flex align-items-center gap-1.5" data-bs-toggle="collapse" data-bs-target="#collapse-fak-<?= $fsum['id'] ?>" title="Klik untuk membuka/tutup rincian siklus">
                                                <span><?= htmlspecialchars($fsum['nama_fakultas']) ?></span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                                    <?= count($cycles) ?> Siklus PPEPP
                                                </span>
                                            </a>
                                        </td>
                                        <td class="text-secondary small"><?= htmlspecialchars($fsum['nama_dekan'] ?: '-') ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-bold">
                                                <?= $totFak ?> Dokumen
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($revFak > 0): ?>
                                                <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold"><?= $revFak ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($rejFak > 0): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold"><?= $rejFak ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($appFak > 0): ?>
                                                <span class="badge bg-success rounded-pill px-2.5 py-1 fw-bold"><?= $appFak ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 7px;">
                                                    <div class="progress-bar <?= $pctFak >= 80 ? 'bg-success' : ($pctFak >= 40 ? 'bg-info' : ($pctFak > 0 ? 'bg-warning' : 'bg-secondary')) ?>" 
                                                         role="progressbar" 
                                                         style="width: <?= $pctFak ?>%" 
                                                         aria-valuenow="<?= $pctFak ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100"></div>
                                                </div>
                                                <span class="small fw-bold text-dark" style="font-size: 0.72rem; min-width: 32px;" title="Rata-rata kepatuhan PPEPP (20% per tahap Disetujui)"><?= $pctFak ?>%</span>
                                            </div>
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1" data-bs-toggle="collapse" data-bs-target="#collapse-fak-<?= $fsum['id'] ?>" title="Buka dropdown daftar judul PPEPP">
                                                    <i class="fas fa-list-ul me-1"></i> Aspek
                                                </button>
                                                <a href="<?= base_url('admin/review-fakultas/' . $fsum['id']) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1">
                                                    <i class="fas fa-arrow-right me-1"></i> Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- DROPDOWN ACCORDION DAFTAR JUDUL ASPEK SIKLUS PPEPP -->
                                    <tr class="collapse bg-light border-bottom p-0" id="collapse-fak-<?= $fsum['id'] ?>">
                                        <td colspan="9" class="p-3 bg-light">
                                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
                                                <div class="card-header bg-scu-navy text-white py-2.5 px-3 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="fas fa-diagram-project text-warning"></i>
                                                        <span class="fw-bold small text-white">Daftar Siklus PPEPP Berkelanjutan &mdash; <?= htmlspecialchars($fsum['nama_fakultas']) ?></span>
                                                    </div>
                                                    <span class="badge bg-white bg-opacity-20 text-white fw-semibold small">
                                                        Bobot: 20% per Tahapan Disetujui (Total 100%)
                                                    </span>
                                                </div>
                                                <div class="card-body p-0">
                                                    <?php if (empty($cycles)): ?>
                                                        <div class="p-4 text-center text-muted small">
                                                            <i class="fas fa-folder-open fa-2x opacity-50 mb-2"></i>
                                                            <p class="mb-0">Belum ada judul siklus dokumen PPEPP yang diunggah untuk fakultas ini.</p>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                                                <thead class="table-light text-secondary" style="font-size: 0.72rem; text-transform: uppercase;">
                                                                    <tr>
                                                                        <th class="ps-3" style="width: 30px;">#</th>
                                                                        <th style="min-width: 220px;">Judul Aspek / Topik Mutu PPEPP</th>
                                                                        <th style="min-width: 140px;">Unit Pengunggah</th>
                                                                        <th class="text-center" style="min-width: 130px;">Progres Kepatuhan</th>
                                                                        <th class="text-center" style="min-width: 250px;">Tahapan PPEPP (P-P-E-P-P)</th>
                                                                        <th class="text-center" style="width: 100px;">Total Berkas</th>
                                                                        <th class="text-center pe-3" style="width: 160px;">Aksi & Riwayat</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                                                    $cIdx = 1;
                                                                    foreach ($cycles as $cyc): 
                                                                        $cScore = (int)($cyc['total_score'] ?? 0);
                                                                        $stages = $cyc['stages'] ?? [];
                                                                        $unitLabel = !empty($cyc['nama_prodi']) ? 'Prodi ' . htmlspecialchars($cyc['nama_prodi']) : 'Dekanat (Fakultas)';
                                                                        $unitBadge = !empty($cyc['nama_prodi']) ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-20 text-dark';
                                                                    ?>
                                                                        <tr>
                                                                            <td class="ps-3 text-muted"><?= $cIdx++ ?></td>
                                                                            <td>
                                                                                <div class="fw-bold text-dark mb-0.5"><?= htmlspecialchars($cyc['judul_siklus']) ?></div>
                                                                                <?php if (!empty($cyc['tahun_akademik'])): ?>
                                                                                    <span class="badge bg-light text-muted border" style="font-size: 0.65rem;"><?= htmlspecialchars($cyc['tahun_akademik']) ?></span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge <?= $unitBadge ?> border px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                                                                    <?= $unitLabel ?>
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="d-inline-flex flex-column align-items-center justify-content-center py-1" style="min-width: 110px;">
                                                                                    <div class="d-flex align-items-center justify-content-center gap-2 w-100 mb-1" style="max-width: 115px;">
                                                                                        <div class="progress flex-grow-1 rounded-pill" style="height: 8px; background-color: #E2E8F0;">
                                                                                            <div class="progress-bar rounded-pill <?= $cScore >= 100 ? 'bg-success' : ($cScore >= 60 ? 'bg-info' : ($cScore >= 20 ? 'bg-warning' : 'bg-secondary')) ?>" 
                                                                                                 role="progressbar" 
                                                                                                 style="width: <?= $cScore ?>%"></div>
                                                                                        </div>
                                                                                        <span class="badge <?= $cScore >= 100 ? 'bg-success text-white' : ($cScore >= 60 ? 'bg-info text-dark' : ($cScore >= 20 ? 'bg-warning text-dark' : 'bg-light text-secondary border')) ?> fw-bold rounded-pill px-2 py-0.5" style="font-size: 0.72rem; min-width: 36px; text-align: center;">
                                                                                            <?= $cScore ?>%
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class="text-muted fw-semibold" style="font-size: 0.68rem; letter-spacing: -0.01em;">
                                                                                        (<?= $cScore / 20 ?>/5 Tahap Disetujui)
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="d-inline-flex align-items-center gap-1">
                                                                                    <?php 
                                                                                    $stageMap = [
                                                                                        'penetapan'    => ['P', 'Penetapan'],
                                                                                        'pelaksanaan'  => ['P', 'Pelaksanaan'],
                                                                                        'evaluasi'     => ['E', 'Evaluasi'],
                                                                                        'pengendalian' => ['P', 'Pengendalian'],
                                                                                        'peningkatan'  => ['P', 'Peningkatan'],
                                                                                    ];
                                                                                    foreach ($stageMap as $stKey => $stMeta):
                                                                                        $stInfo = $stages[$stKey] ?? ['exists' => false, 'status' => 'belum_diisi', 'score' => 0];
                                                                                        $isAcc = ($stInfo['status'] === 'sesuai');
                                                                                        $isRevisi = in_array($stInfo['status'], ['revisi_gpm', 'revisi_lpm', 'perlu_perbaikan']);
                                                                                        $isReviewLpm = in_array($stInfo['status'], ['menunggu_review_lpm', 'sudah_diperbaiki']);
                                                                                        $isReviewGpm = in_array($stInfo['status'], ['menunggu_review_gpm', 'belum_direview']);
                                                                                        $isExists = !empty($stInfo['exists']);
                                                                                        $docItem = $stInfo['doc'] ?? null;

                                                                                        $badgeClass = 'bg-light text-muted border';
                                                                                        $badgeTitle = $stMeta[1] . ': Belum Diisi';
                                                                                        $iconHtml = '';

                                                                                        if ($isAcc) {
                                                                                            $badgeClass = 'bg-success text-white shadow-xs';
                                                                                            $badgeTitle = $stMeta[1] . ': Disetujui (Sesuai Mutu)';
                                                                                            $iconHtml = '<i class="fas fa-check-circle me-0.5"></i>';
                                                                                        } elseif ($isRevisi) {
                                                                                            $badgeClass = 'bg-warning text-dark border border-warning';
                                                                                            $revNote = ($stInfo['status'] === 'revisi_gpm') ? ' (GPM)' : (($stInfo['status'] === 'revisi_lpm') ? ' (LPM)' : '');
                                                                                            $badgeTitle = $stMeta[1] . ': Perlu Revisi' . $revNote;
                                                                                            $iconHtml = '<i class="fas fa-exclamation-triangle me-0.5"></i>';
                                                                                        } elseif ($isReviewLpm) {
                                                                                            $badgeClass = 'bg-primary text-white shadow-xs';
                                                                                            $badgeTitle = $stMeta[1] . ': Menunggu Review LPM';
                                                                                            $iconHtml = '<i class="fas fa-clock me-0.5"></i>';
                                                                                        } elseif ($isReviewGpm) {
                                                                                            $badgeClass = 'bg-info bg-opacity-25 text-info-emphasis border border-info border-opacity-50';
                                                                                            $badgeTitle = $stMeta[1] . ': Menunggu Telaah GPM';
                                                                                            $iconHtml = '<i class="fas fa-hourglass-half me-0.5"></i>';
                                                                                        }
                                                                                    ?>
                                                                                        <span class="badge rounded-pill px-2 py-1 <?= $badgeClass ?>" 
                                                                                              style="font-size: 0.68rem; cursor: <?= $docItem ? 'pointer' : 'default' ?>;"
                                                                                              title="<?= $badgeTitle ?>"
                                                                                              <?php if ($docItem): ?>
                                                                                                  onclick="openDocReviewHistory(<?= $docItem['id'] ?>)"
                                                                                              <?php endif; ?>>
                                                                                            <?= $iconHtml ?>
                                                                                            <?= $stMeta[0] ?>
                                                                                        </span>
                                                                                    <?php endforeach; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge bg-light text-dark border px-2 py-1">
                                                                                    <?= $cyc['docs_count'] ?> Berkas
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center pe-3">
                                                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                                                    <?php 
                                                                                    $firstDoc = !empty($cyc['all_docs']) ? $cyc['all_docs'][0] : null;
                                                                                    if ($firstDoc): 
                                                                                    ?>
                                                                                        <button type="button" 
                                                                                                class="btn btn-xs btn-outline-info rounded-pill px-2 py-0.5 btn-open-revisions" 
                                                                                                data-doc-id="<?= $firstDoc['id'] ?>"
                                                                                                title="Lihat Riwayat Catatan Revisi Sebelumnya">
                                                                                            <i class="fas fa-history me-1"></i> Revisi
                                                                                        </button>
                                                                                    <?php endif; ?>
                                                                                    <?php if (!empty($cyc['prodi_id'])): ?>
                                                                                        <a href="<?= base_url('admin/review-prodi/' . $cyc['prodi_id']) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5" title="Review Dokumen Prodi">
                                                                                            <i class="fas fa-arrow-right"></i>
                                                                                        </a>
                                                                                    <?php else: ?>
                                                                                        <a href="<?= base_url('admin/review-fakultas/' . $fsum['id']) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5" title="Review Dokumen Dekanat">
                                                                                            <i class="fas fa-arrow-right"></i>
                                                                                        </a>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section B: Kepatuhan Seluruh Program Studi -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark-blue mb-1">
                                <i class="fas fa-graduation-cap text-primary me-2"></i> Rekapitulasi Dokumen Seluruh Program Studi
                            </h5>
                            <p class="text-muted small mb-0">Tabel komparasi status kepatuhan dan review dokumen PPEPP program studi.</p>
                        </div>
                    </div>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light text-secondary" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-3" style="width: 70px;">Kode</th>
                                    <th>Program Studi</th>
                                    <th>Fakultas</th>
                                    <th>Ketua Program Studi</th>
                                    <th class="text-center">Total Dokumen</th>
                                    <th class="text-center">Perlu Review</th>
                                    <th class="text-center">Perlu Revisi</th>
                                    <th class="text-center">Sesuai</th>
                                    <th style="min-width: 140px;">Progres Kepatuhan</th>
                                    <th class="text-center pe-3" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prodiSummaries as $psum): 
                                    $totPrd = (int)$psum['total_dokumen'];
                                    $revPrd = (int)$psum['perlu_review_count'];
                                    $rejPrd = (int)$psum['revisi_count'];
                                    $appPrd = (int)$psum['sesuai_count'];
                                    $pctPrd = $totPrd > 0 ? round(($appPrd / $totPrd) * 100) : 0;
                                ?>
                                    <tr>
                                        <td class="ps-3"><span class="badge bg-light text-dark border"><?= htmlspecialchars($psum['kode_prodi']) ?></span></td>
                                        <td>
                                            <div class="fw-bold text-dark-blue"><?= htmlspecialchars($psum['nama_prodi']) ?></div>
                                            <span class="badge bg-dark-blue text-warning" style="font-size:0.65rem;"><?= htmlspecialchars($psum['jenjang']) ?></span>
                                        </td>
                                        <td class="small text-secondary"><?= htmlspecialchars($psum['nama_fakultas']) ?></td>
                                        <td class="small text-secondary"><?= htmlspecialchars($psum['nama_kaprodi'] ?: '-') ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-bold">
                                                <?= $totPrd ?> Dokumen
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($revPrd > 0): ?>
                                                <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold"><?= $revPrd ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($rejPrd > 0): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold"><?= $rejPrd ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($appPrd > 0): ?>
                                                <span class="badge bg-success rounded-pill px-2.5 py-1 fw-bold"><?= $appPrd ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 7px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctPrd ?>%" aria-valuenow="<?= $pctPrd ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="small fw-bold text-dark" style="font-size: 0.72rem; min-width: 32px;"><?= $pctPrd ?>%</span>
                                            </div>
                                        </td>
                                        <td class="text-center pe-3">
                                            <a href="<?= base_url('admin/review-prodi/' . $psum['id']) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1">
                                                <i class="fas fa-arrow-right me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 1: QUICK APPROVE (SETUJUI INSTAN)                 -->
<!-- ======================================================= -->
<!-- ======================================================= -->
<!-- MODAL 1: QUICK APPROVE (SETUJUI INSTAN)                 -->
<!-- ======================================================= -->
<div class="modal fade" id="quickApproveModal" tabindex="-1" aria-labelledby="quickApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('admin/review-dokumen/submit') ?>" method="POST" id="quickApproveForm">
                <input type="hidden" name="document_id" id="approveDocId" value="">
                <input type="hidden" name="status_review" value="sesuai">
                <input type="hidden" name="redirect_to" value="admin/review">

                <div class="modal-header py-3.5 px-4 bg-white border-bottom" style="border-color: #F1F5F9 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark-blue fs-6 mb-0" id="quickApproveModalLabel">
                                Setujui Dokumen Mutu
                            </h5>
                            <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.78rem;">
                                Dokumen ini akan diverifikasi memenuhi standar mutu PPEPP.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Dokumen Info Card -->
                    <div class="p-3.5 rounded-3 mb-4 border" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 bg-white border shadow-2xs text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                                <i class="fas fa-file-circle-check"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Dokumen Yang Diverifikasi</span>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.68rem;">Status: Disetujui</span>
                                </div>
                                <div class="fw-bold text-dark-blue fs-6 mb-1" id="approveDocTitle" style="line-height: 1.45;">-</div>
                                <div class="text-muted small" id="approveDocUnit" style="font-size: 0.76rem;">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Telaah GPM Fakultas (Tier-1) -->
                    <div id="approveGpmBox" class="p-3 rounded-3 mb-3 border border-warning d-none" style="background: #FFFBEB; border-left: 4px solid #D97706 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="small fw-bold text-uppercase text-warning-emphasis" style="font-size: 0.72rem;">
                                <i class="fas fa-shield-halved text-warning me-1"></i> Rekomendasi Telaah GPM Fakultas:
                            </span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                Disetujui GPM
                            </span>
                        </div>
                        <div class="small text-dark mb-1" id="approveGpmNotes" style="font-size: 0.82rem; line-height: 1.5; white-space: pre-wrap;"></div>
                        <div class="small text-muted" id="approveGpmMeta" style="font-size: 0.7rem;"></div>
                    </div>

                    <div class="mb-2">
                        <label for="approveCatatan" class="form-label fw-bold text-dark-blue small mb-1.5" style="font-size: 0.82rem;">
                            Catatan Verifikator LPM <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <input type="text" class="form-control rounded-3 p-2.5 text-dark" id="approveCatatan" name="catatan_review" 
                                value="" placeholder="Tuliskan catatan verifikasi jika diperlukan (opsional)..."
                                style="font-size: 0.85rem; border-color: #CBD5E1;">
                        <div class="form-text mt-1 text-muted" style="font-size: 0.72rem;">
                            Catatan akan tersimpan di riwayat review dan dapat dibaca oleh unit pengunggah.
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 bg-light bg-opacity-60 border-top d-flex justify-content-between align-items-center" style="border-color: #F1F5F9 !important;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.82rem;">
                        <i class="fas fa-circle-check"></i> Ya, Setujui Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 2: QUICK REVISION (MINTA PERBAIKAN)               -->
<!-- ======================================================= -->
<div class="modal fade" id="quickRevisionModal" tabindex="-1" aria-labelledby="quickRevisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('admin/review-dokumen/submit') ?>" method="POST" id="quickRevisionForm">
                <input type="hidden" name="document_id" id="revDocId" value="">
                <input type="hidden" name="status_review" value="perlu_perbaikan">
                <input type="hidden" name="redirect_to" value="admin/review">

                <div class="modal-header py-3.5 px-4 bg-white border-bottom" style="border-color: #F1F5F9 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark-blue fs-6 mb-0" id="quickRevisionModalLabel">
                                Minta Perbaikan / Revisi Dokumen
                            </h5>
                            <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.78rem;">
                                Berikan instruksi revisi yang jelas untuk unit pengunggah.
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
                                <div class="fw-bold text-dark-blue fs-6 mb-1" id="revDocTitle" style="line-height: 1.45;">-</div>
                                <div class="text-muted small" id="revDocUnit" style="font-size: 0.76rem;">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Telaah GPM Fakultas (Tier-1) -->
                    <div id="revGpmBox" class="p-3 rounded-3 mb-3 border border-warning d-none" style="background: #FFFBEB; border-left: 4px solid #D97706 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="small fw-bold text-uppercase text-warning-emphasis" style="font-size: 0.72rem;">
                                <i class="fas fa-shield-halved text-warning me-1"></i> Catatan Telaah GPM Fakultas Sebelumnya:
                            </span>
                            <span class="badge bg-warning bg-opacity-25 text-warning-emphasis border border-warning border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                Catatan GPM
                            </span>
                        </div>
                        <div class="small text-dark mb-1" id="revGpmNotes" style="font-size: 0.82rem; line-height: 1.5; white-space: pre-wrap;"></div>
                        <div class="small text-muted" id="revGpmMeta" style="font-size: 0.7rem;"></div>
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
                                  placeholder="Tuliskan catatan perbaikan secara spesifik... Contoh: Nomor SK belum tercantum, harap lengkapi tanda tangan pengesahan dari Dekan pada lembar akhir." required></textarea>
                        <div class="d-flex align-items-center gap-2 mt-2.5 p-2.5 rounded-2 bg-light border text-muted" style="font-size: 0.74rem;">
                            <i class="fas fa-circle-info text-primary flex-shrink-0"></i>
                            <span>Catatan ini akan otomatis muncul sebagai instruksi revisi di dashboard unit pengunggah saat memperbarui dokumen.</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 bg-light bg-opacity-60 border-top d-flex justify-content-between align-items-center" style="border-color: #F1F5F9 !important;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.82rem;">
                        <i class="fas fa-paper-plane"></i> Kirim Catatan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 3: VIEW LPM REVIEW NOTE                           -->
<!-- ======================================================= -->
<div class="modal fade" id="viewLpmNoteModal" tabindex="-1" aria-labelledby="viewLpmNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header py-3.5 px-4 bg-white border-bottom" style="border-color: #F1F5F9 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-15 text-warning-emphasis d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                        <i class="fas fa-comment-dots text-warning"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark-blue fs-6 mb-0" id="viewLpmNoteModalLabel">
                            Detail Catatan Review Dokumen
                        </h5>
                        <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.78rem;">
                            Riwayat catatan hasil verifikasi oleh tim LPM.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Info Dokumen Card -->
                <div class="p-3.5 rounded-3 mb-4 border" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 bg-white border shadow-2xs text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                            <i class="fas fa-file-lines"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                <span class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Nama Dokumen</span>
                                <div id="viewNoteStatusBadge">-</div>
                            </div>
                            <div class="fw-bold text-dark-blue fs-6 mb-0" id="viewNoteDocTitle" style="line-height: 1.45;">-</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark-blue small mb-2" style="font-size: 0.82rem;">
                        Catatan &amp; Arahan Verifikator LPM:
                    </label>
                    <div class="p-3.5 rounded-3 border" id="viewNoteContent" 
                         style="background: #FFF1F2; border-color: #FECDD3 !important; color: #9F1239; white-space: pre-line; line-height: 1.6; font-size: 0.86rem;">
                        -
                    </div>
                </div>

                <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                    <div>
                        <span class="text-muted">Ditinjau oleh:</span>
                        <span class="fw-bold text-dark ms-1" id="viewNoteReviewer">-</span>
                    </div>
                    <div>
                        <span class="text-muted">Waktu Review:</span>
                        <span class="fw-bold text-dark ms-1" id="viewNoteDate">-</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-light bg-opacity-60 border-top d-flex justify-content-end" style="border-color: #F1F5F9 !important;">
                <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="font-size: 0.82rem;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 4: PDF PREVIEW MODAL                              -->
<!-- ======================================================= -->
<div class="modal fade" id="hubPdfPreviewModal" tabindex="-1" aria-labelledby="hubPdfPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90vw; height: 90vh;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden h-100 d-flex flex-column">
            <div class="modal-header bg-dark-blue text-white py-3 px-4 flex-shrink-0">
                <div class="d-flex align-items-center gap-2 text-truncate me-3">
                    <i class="fas fa-file-pdf text-danger fs-5"></i>
                    <h6 class="modal-title fw-bold text-truncate mb-0" id="hubPdfPreviewModalLabel">Pratinjau Dokumen</h6>
                    <span id="hubPdfTaBadge" class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 rounded-pill fw-medium px-2 py-0.5" style="font-size:0.68rem; display:none;"></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="hubPdfDownloadBtn" class="btn btn-sm btn-outline-light rounded-pill px-3" target="_blank" download>
                        <i class="fas fa-download me-1"></i> Unduh
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 flex-grow-1 bg-light position-relative">
                <iframe id="hubPdfViewerIframe" src="about:blank" class="w-100 h-100 border-0" style="min-height: 500px;"></iframe>
            </div>
            <div class="modal-footer bg-light px-4 py-2 flex-shrink-0 justify-content-between" id="hubPdfNarasiFooter" style="display: none;">
                <div class="small text-muted text-truncate me-3" id="hubPdfNarasiText"></div>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
.kpi-card {
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
}
.cursor-pointer {
    cursor: pointer;
}
.filter-pill {
    background-color: #F8FAFC;
    color: #475569;
    border: 1px solid #E2E8F0;
    font-size: 0.78rem;
    font-weight: 600;
    transition: all 0.15s ease;
}
.filter-pill:hover {
    background-color: #EEF2FF;
    color: var(--scu-blue, #1E3A8A);
    border-color: #C7D2FE;
}
.filter-pill.active {
    background-color: var(--scu-blue, #1E3A8A);
    color: #FFFFFF !important;
    border-color: var(--scu-blue, #1E3A8A);
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.25);
}
.filter-pill.active .badge {
    background-color: rgba(255, 255, 255, 0.25) !important;
    color: #FFFFFF !important;
}
.nav-tabs-modern .nav-link {
    color: #64748B;
    border: none;
    border-bottom: 3px solid transparent;
    transition: all 0.2s ease;
}
.nav-tabs-modern .nav-link:hover {
    color: var(--scu-blue, #1E3A8A);
}
.nav-tabs-modern .nav-link.active {
    color: var(--scu-blue, #1E3A8A);
    border-bottom: 3px solid var(--scu-blue, #1E3A8A);
    background: transparent;
}
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
// Global filter state
let currentStatusFilter = 'perlu_tindakan'; // Default view: urgent review items

function applyStatusFilter(status) {
    currentStatusFilter = status;

    // Update active pill styling
    document.querySelectorAll('.filter-pill').forEach(btn => {
        if (btn.getAttribute('data-filter') === status) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    filterHubTable();
}

function onFakultasChange() {
    const selectedFakId = document.getElementById('fakultasFilter').value;
    const prodiSelect = document.getElementById('prodiFilter');
    const prodiOptions = prodiSelect.querySelectorAll('option');

    prodiOptions.forEach(opt => {
        if (!opt.value) return; // Skip default option
        const fakId = opt.getAttribute('data-fakultas-id');
        if (!selectedFakId || fakId === selectedFakId) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });

    // Reset prodi selection if current choice doesn't match new fakultas
    const currentPrdOpt = prodiSelect.options[prodiSelect.selectedIndex];
    if (selectedFakId && currentPrdOpt && currentPrdOpt.value && currentPrdOpt.getAttribute('data-fakultas-id') !== selectedFakId) {
        prodiSelect.value = '';
    }

    filterHubTable();
}

function resetHubFilters() {
    document.getElementById('liveSearchInput').value = '';
    document.getElementById('fakultasFilter').value = '';
    document.getElementById('prodiFilter').value = '';
    document.getElementById('siklusFilter').value = '';
    document.getElementById('bidangFilter').value = '';
    if (document.getElementById('tahunFilter')) document.getElementById('tahunFilter').value = '';

    // Show all prodi options
    document.querySelectorAll('#prodiFilter option').forEach(opt => opt.style.display = '');

    // Reset status pill to default: perlu_tindakan
    applyStatusFilter('perlu_tindakan');
}

let currentHubPage = <?= (int)$page ?>;
let currentHubPerPage = <?= (int)$perPage ?>;
let hubTotalPages = <?= (int)$totalPages ?>;
let hubSearchTimer = null;

function filterHubTable() {
    clearTimeout(hubSearchTimer);
    hubSearchTimer = setTimeout(() => {
        fetchHubData(1);
    }, 280);
}

function changeHubPerPage(val) {
    currentHubPerPage = parseInt(val, 10) || 50;
    fetchHubData(1);
}

function fetchHubData(page) {
    currentHubPage = page || 1;
    const searchVal = document.getElementById('liveSearchInput').value.trim();
    const fakultasVal = document.getElementById('fakultasFilter').value;
    const prodiVal = document.getElementById('prodiFilter').value;
    const siklusVal = document.getElementById('siklusFilter').value;
    const bidangVal = document.getElementById('bidangFilter').value;
    const tahunVal = document.getElementById('tahunFilter') ? document.getElementById('tahunFilter').value.trim() : '';

    const tbody = document.getElementById('hubTableBody');
    if (tbody) {
        tbody.style.opacity = '0.5';
    }

    const params = new URLSearchParams({
        ajax: '1',
        page: currentHubPage,
        per_page: currentHubPerPage,
        status: currentStatusFilter,
        fakultas_id: fakultasVal,
        prodi_id: prodiVal,
        siklus: siklusVal,
        bidang_id: bidangVal,
        tahun_akademik: tahunVal,
        search: searchVal
    });

    fetch('<?= base_url("admin/review-hub") ?>?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (tbody) {
            tbody.style.opacity = '1';
            tbody.innerHTML = data.html || '';
        }

        hubTotalPages = data.total_pages || 1;
        currentHubPage = data.page || 1;

        // Update info text
        const startEl = document.getElementById('hubStartNum');
        const endEl = document.getElementById('hubEndNum');
        const totalEl = document.getElementById('hubTotalFiltered');
        const visibleDocEl = document.getElementById('visibleDocCount');

        if (startEl) startEl.textContent = data.start_num || 0;
        if (endEl) endEl.textContent = data.end_num || 0;
        if (totalEl) totalEl.textContent = Number(data.total || 0).toLocaleString('id-ID');
        if (visibleDocEl) visibleDocEl.textContent = data.end_num ? (data.end_num - data.start_num + 1) : 0;

        const noResultMsg = document.getElementById('noMatchingDocsMsg');
        const tableEl = document.getElementById('hubMasterTable');
        const paginationWrap = document.getElementById('hubPaginationWrapper');

        if (data.total === 0) {
            if (noResultMsg) noResultMsg.classList.remove('d-none');
            if (tableEl) tableEl.classList.add('d-none');
            if (paginationWrap) paginationWrap.classList.add('d-none');
        } else {
            if (noResultMsg) noResultMsg.classList.add('d-none');
            if (tableEl) tableEl.classList.remove('d-none');
            if (paginationWrap) paginationWrap.classList.remove('d-none');
        }

        renderHubPagination(hubTotalPages, currentHubPage);
    })
    .catch(err => {
        console.error("Error fetching review hub data:", err);
        if (tbody) tbody.style.opacity = '1';
    });
}

function renderHubPagination(totalPages, currentPage) {
    const nav = document.getElementById('hubPaginationNav');
    if (!nav) return;
    nav.innerHTML = '';

    if (totalPages <= 1) return;

    // Tombol Previous
    const prevLi = document.createElement('li');
    prevLi.className = 'page-item ' + (currentPage <= 1 ? 'disabled' : '');
    prevLi.innerHTML = '<a class="page-link" href="javascript:void(0)" aria-label="Previous"><i class="fas fa-chevron-left" style="font-size: 0.72rem;"></i></a>';
    if (currentPage > 1) {
        prevLi.querySelector('a').onclick = () => fetchHubData(currentPage - 1);
    }
    nav.appendChild(prevLi);

    // Range halaman
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        const firstLi = document.createElement('li');
        firstLi.className = 'page-item';
        firstLi.innerHTML = '<a class="page-link" href="javascript:void(0)">1</a>';
        firstLi.querySelector('a').onclick = () => fetchHubData(1);
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
            li.querySelector('a').onclick = ((pageNum) => () => fetchHubData(pageNum))(p);
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
        lastLi.querySelector('a').onclick = () => fetchHubData(totalPages);
        nav.appendChild(lastLi);
    }

    // Tombol Next
    const nextLi = document.createElement('li');
    nextLi.className = 'page-item ' + (currentPage >= totalPages ? 'disabled' : '');
    nextLi.innerHTML = '<a class="page-link" href="javascript:void(0)" aria-label="Next"><i class="fas fa-chevron-right" style="font-size: 0.72rem;"></i></a>';
    if (currentPage < totalPages) {
        nextLi.querySelector('a').onclick = () => fetchHubData(currentPage + 1);
    }
    nav.appendChild(nextLi);
}

// Initial pagination render on load
document.addEventListener('DOMContentLoaded', function() {
    renderHubPagination(hubTotalPages, currentHubPage);
});


// Modal bindings on page load
document.addEventListener('DOMContentLoaded', function () {
    // Initial filter execution to apply 'perlu_tindakan' default
    filterHubTable();

    // 1. Quick Approve Modal
    const approveModal = document.getElementById('quickApproveModal');
    if (approveModal) {
        approveModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const docId = btn.getAttribute('data-id') || '';
            const docTitle = btn.getAttribute('data-title') || '-';
            const docUnit = btn.getAttribute('data-unit') || '';
            const gpmNotes = btn.getAttribute('data-gpm-notes') || '';
            const gpmReviewer = btn.getAttribute('data-gpm-reviewer') || 'GPM Fakultas';
            const gpmDate = btn.getAttribute('data-gpm-date') || '';

            document.getElementById('approveDocId').value = docId;
            document.getElementById('approveDocTitle').textContent = docTitle;
            document.getElementById('approveDocUnit').textContent = docUnit ? 'Unit: ' + docUnit : '';
            const approveNotesEl = document.getElementById('approveCatatan');
            if (approveNotesEl) {
                approveNotesEl.value = '';
            }

            const gpmBox = document.getElementById('approveGpmBox');
            if (gpmBox) {
                if (gpmNotes) {
                    gpmBox.classList.remove('d-none');
                    document.getElementById('approveGpmNotes').textContent = gpmNotes;
                    document.getElementById('approveGpmMeta').innerHTML = '<i class="far fa-user me-1"></i> ' + gpmReviewer + (gpmDate ? ' &bull; ' + gpmDate : '');
                } else {
                    gpmBox.classList.add('d-none');
                }
            }
        });
    }

    // 2. Quick Revision Modal
    const revModal = document.getElementById('quickRevisionModal');
    if (revModal) {
        revModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const docId = btn.getAttribute('data-id') || '';
            const docTitle = btn.getAttribute('data-title') || '-';
            const docNotes = btn.getAttribute('data-notes') || '';
            const docUnit = btn.getAttribute('data-unit') || '';
            const gpmNotes = btn.getAttribute('data-gpm-notes') || '';
            const gpmReviewer = btn.getAttribute('data-gpm-reviewer') || 'GPM Fakultas';
            const gpmDate = btn.getAttribute('data-gpm-date') || '';

            document.getElementById('revDocId').value = docId;
            document.getElementById('revDocTitle').textContent = docTitle;
            document.getElementById('revDocUnit').textContent = docUnit ? 'Unit: ' + docUnit : '';
            // Kosongkan catatan revisi default agar LPM dapat mengisikan catatan baru
            document.getElementById('revCatatan').value = '';

            const gpmBox = document.getElementById('revGpmBox');
            if (gpmBox) {
                if (gpmNotes) {
                    gpmBox.classList.remove('d-none');
                    document.getElementById('revGpmNotes').textContent = gpmNotes;
                    document.getElementById('revGpmMeta').innerHTML = '<i class="far fa-user me-1"></i> ' + gpmReviewer + (gpmDate ? ' &bull; ' + gpmDate : '');
                } else {
                    gpmBox.classList.add('d-none');
                }
            }
        });
    }

    // 3. View Note Modal
    const noteModal = document.getElementById('viewLpmNoteModal');
    if (noteModal) {
        noteModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const title = btn.getAttribute('data-title') || '-';
            const notes = btn.getAttribute('data-notes') || 'Tidak ada catatan khusus.';
            const reviewer = btn.getAttribute('data-reviewer') || 'Admin LPM';
            const date = btn.getAttribute('data-date') || '-';
            const status = btn.getAttribute('data-status') || '';

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

    // 4. PDF Preview Modal
    const pdfModal = document.getElementById('hubPdfPreviewModal');
    if (pdfModal) {
        pdfModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const pdfUrl = btn.getAttribute('data-pdf-url') || '';
            const docTitle = btn.getAttribute('data-doc-title') || 'Pratinjau Dokumen';
            const narasi = btn.getAttribute('data-doc-narasi') || '';

            document.getElementById('hubPdfPreviewModalLabel').textContent = docTitle;
            document.getElementById('hubPdfViewerIframe').src = pdfUrl;
            document.getElementById('hubPdfDownloadBtn').href = pdfUrl;

            const docTa = btn.getAttribute('data-doc-ta') || '';
            const taBadge = document.getElementById('hubPdfTaBadge');
            if (taBadge) {
                if (docTa) {
                    taBadge.innerHTML = `<i class="far fa-calendar-alt me-1"></i>TA ${docTa}`;
                    taBadge.style.display = 'inline-flex';
                } else {
                    taBadge.style.display = 'none';
                }
            }

            const footer = document.getElementById('hubPdfNarasiFooter');
            const narasiText = document.getElementById('hubPdfNarasiText');
            if (narasi) {
                narasiText.textContent = narasi;
                footer.style.display = 'flex';
            } else {
                footer.style.display = 'none';
            }
        });

        pdfModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('hubPdfViewerIframe').src = 'about:blank';
        });
    }

    // 5. Modal Riwayat Revisi Dokumen Mutu
    window.openDocReviewHistory = function(docId) {
        if (!docId) return;
        const modalEl = document.getElementById('modalRiwayatRevisiDokumen');
        if (!modalEl) return;
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();

        const loadingEl = document.getElementById('modalRevTimelineLoading');
        const emptyEl = document.getElementById('modalRevTimelineEmpty');
        const contentEl = document.getElementById('modalRevTimelineContent');
        const listEl = document.getElementById('modalRevTimelineList');
        const titleEl = document.getElementById('modalRevDocTitle');
        const metaEl = document.getElementById('modalRevDocMeta');
        const statusBadgeEl = document.getElementById('modalRevCurrentStatusBadge');

        loadingEl.classList.remove('d-none');
        emptyEl.classList.add('d-none');
        contentEl.classList.add('d-none');
        listEl.innerHTML = '';

        fetch('<?= base_url('api/dokumen-revisi/') ?>' + docId)
            .then(res => res.json())
            .then(data => {
                loadingEl.classList.add('d-none');
                if (data.status !== 'success' || !data.document) {
                    emptyEl.classList.remove('d-none');
                    emptyEl.querySelector('p').textContent = data.message || 'Gagal memuat data dokumen.';
                    return;
                }

                const doc = data.document;
                const revs = data.revisions || [];

                titleEl.textContent = doc.nama_dokumen || '-';
                const unit = doc.nama_prodi ? 'Prodi ' + doc.nama_prodi : (doc.nama_fakultas ? 'Fakultas ' + doc.nama_fakultas : 'Unit LPM');
                metaEl.innerHTML = '<strong>No:</strong> ' + (doc.nomor_dokumen || '-') + ' &bull; <strong>Tahap:</strong> ' + (doc.siklus ? doc.siklus.toUpperCase() : '-') + ' &bull; <strong>Unit:</strong> ' + unit;

                let stBadge = '';
                if (doc.status_review === 'sesuai') {
                    stBadge = '<span class="badge bg-success rounded-pill px-3 py-1.5"><i class="fas fa-check-circle me-1"></i> Sesuai Standar Mutu (Disetujui)</span>';
                } else if (doc.status_review === 'perlu_perbaikan') {
                    stBadge = '<span class="badge bg-warning text-dark rounded-pill px-3 py-1.5"><i class="fas fa-exclamation-triangle me-1"></i> Perlu Revisi</span>';
                } else if (doc.status_review === 'sudah_diperbaiki') {
                    stBadge = '<span class="badge bg-info text-dark rounded-pill px-3 py-1.5"><i class="fas fa-clock me-1"></i> Menunggu Review Ulang</span>';
                } else {
                    stBadge = '<span class="badge bg-secondary rounded-pill px-3 py-1.5">' + doc.status_review + '</span>';
                }
                statusBadgeEl.innerHTML = stBadge;

                if (revs.length === 0) {
                    emptyEl.classList.remove('d-none');
                    return;
                }

                contentEl.classList.remove('d-none');
                let html = '<div class="timeline position-relative ps-0 ms-3" style="border-left: 2px solid #93C5FD;">';

                revs.forEach((r, idx) => {
                    const isApproved = r.status_review === 'sesuai';
                    const isRevision = r.status_review === 'perlu_perbaikan';
                    
                    const revBadgeClass = isApproved ? 'bg-success text-white' : (isRevision ? 'bg-warning text-dark' : 'bg-info text-dark');
                    const revIcon = isApproved ? 'fa-check text-success' : (isRevision ? 'fa-triangle-exclamation text-warning' : 'fa-clock text-info');
                    const revPointBorder = isApproved ? 'border-success' : (isRevision ? 'border-warning' : 'border-info');

                    html += `
                        <div class="timeline-item mb-4 position-relative">
                            <div class="timeline-point position-absolute rounded-circle bg-white shadow-xs d-flex align-items-center justify-content-center border border-2 ${revPointBorder}" 
                                 style="width: 28px; height: 28px; left: -15px; top: 0; z-index: 2;">
                                <i class="fas ${revIcon}" style="font-size: 0.75rem;"></i>
                            </div>
                            <div class="card border-0 shadow-xs rounded-3 p-3 ms-4 bg-white">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge ${revBadgeClass} rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                            Revisi Ke-${r.revisi_ke || (idx + 1)}
                                        </span>
                                        <span class="small fw-bold text-dark-blue">${r.reviewer_name || 'Reviewer LPM'}</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        <i class="far fa-calendar-alt me-1"></i> ${r.reviewed_at_formatted || r.reviewed_at || '-'}
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <div class="small fw-semibold text-secondary mb-1">
                                        <i class="fas fa-comment-dots text-warning me-1"></i> Catatan & Arahan Revisi Reviewer:
                                    </div>
                                    <div class="p-2.5 rounded-2 bg-light border small text-dark fst-normal" style="line-height: 1.5;">
                                        ${r.catatan_review ? r.catatan_review.replace(/\n/g, '<br>') : '<span class="text-muted fst-italic">Tidak ada rincian catatan.</span>'}
                                    </div>
                                </div>

                                ${r.revised_at ? `
                                    <div class="mt-2 pt-2 border-top border-dashed">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                                <i class="fas fa-file-arrow-up me-1"></i> Perbaikan Telah Diunggah
                                            </span>
                                            <span class="text-muted small" style="font-size: 0.72rem;">
                                                <i class="far fa-clock me-1"></i> ${r.revised_at_formatted || r.revised_at}
                                            </span>
                                        </div>
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            Diunggah oleh: <strong class="text-dark">${r.uploader_name || 'Pengunggah Unit'}</strong>
                                        </div>
                                        ${r.catatan_perbaikan ? `
                                            <div class="small text-secondary mt-1 p-2 bg-light rounded border">
                                                <strong>Tanggapan:</strong> ${r.catatan_perbaikan}
                                            </div>
                                        ` : ''}
                                    </div>
                                ` : `
                                    <div class="mt-2 pt-1 text-muted small fst-italic" style="font-size: 0.72rem;">
                                        ${r.status_review === 'sesuai' ? '<i class="fas fa-check-double text-success me-1"></i> Dokumen telah disetujui.' : '<i class="fas fa-hourglass-half text-warning me-1"></i> Menunggu unit pengunggah mengunggah berkas revisi perbaikan.'}
                                    </div>
                                `}
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                listEl.innerHTML = html;
            })
            .catch(err => {
                loadingEl.classList.add('d-none');
                emptyEl.classList.remove('d-none');
                emptyEl.querySelector('p').textContent = 'Terjadi kesalahan saat memuat riwayat revisi.';
            });
    };

    // Event listener delegation untuk tombol riwayat revisi
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-open-revisions');
        if (btn) {
            const docId = btn.getAttribute('data-doc-id');
            if (docId) {
                openDocReviewHistory(docId);
            }
        }
    });
});
</script>

<!-- MODAL RIWAYAT REVISI DOKUMEN MUTU -->
<div class="modal fade" id="modalRiwayatRevisiDokumen" tabindex="-1" aria-labelledby="modalRiwayatRevisiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark-blue text-white rounded-top-4 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-history text-white"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="modalRiwayatRevisiLabel">Riwayat Catatan Revisi Dokumen Mutu</h6>
                        <small class="text-white-50" id="modalRevSubtitle">Log kronologis permintaan revisi, nama reviewer, dan perbaikan</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Info Dokumen Header -->
                <div class="card border-0 shadow-xs rounded-3 p-3 mb-3 bg-white">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                        <div class="flex-grow-1" style="min-width: 250px;">
                            <h6 class="fw-bold text-dark mb-1" id="modalRevDocTitle">-</h6>
                            <div class="text-muted small" id="modalRevDocMeta">-</div>
                        </div>
                        <div id="modalRevCurrentStatusBadge" class="flex-shrink-0"></div>
                    </div>
                </div>

                <!-- Timeline Riwayat Revisi -->
                <div id="modalRevTimelineLoading" class="text-center py-4">
                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                    <span class="text-muted small">Memuat catatan riwayat revisi...</span>
                </div>
                <div id="modalRevTimelineEmpty" class="text-center py-4 d-none">
                    <i class="fas fa-clipboard-check fa-2x text-muted opacity-50 mb-2"></i>
                    <p class="text-muted small mb-0">Belum ada catatan revisi pada dokumen ini. Dokumen ini disetujui langsung atau belum pernah diminta revisi.</p>
                </div>
                <div id="modalRevTimelineContent" class="d-none">
                    <div class="timeline-container" id="modalRevTimelineList"></div>
                </div>
            </div>
            <div class="modal-footer bg-white py-2.5 px-4 border-top">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
