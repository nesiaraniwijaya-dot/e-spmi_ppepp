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

        <!-- 2. Belum Direview -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 kpi-card cursor-pointer border-start border-4 border-warning" onclick="applyStatusFilter('belum_direview')" title="Klik untuk filter: Dokumen belum pernah direview">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Belum Direview</span>
                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning p-2"><i class="fas fa-clock"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-warning mb-0"><?= $reviewStats['belum_direview'] ?></div>
                <div class="small text-muted" style="font-size: 0.7rem;">Menunggu verifikasi awal</div>
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

        <!-- 6. Total Seluruh Dokumen -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 kpi-card cursor-pointer border-start border-4 border-primary" onclick="applyStatusFilter('all')" title="Klik untuk menampilkan semua dokumen">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Dokumen</span>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary p-2"><i class="fas fa-folder-closed"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-primary mb-0"><?= $reviewStats['total'] ?></div>
                <div class="small text-muted" style="font-size: 0.7rem;">Seluruh unit universitas</div>
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
                    <button type="button" class="btn btn-sm rounded-pill px-3 filter-pill" data-filter="belum_direview" onclick="applyStatusFilter('belum_direview')">
                        <i class="fas fa-clock text-warning me-1"></i> Belum Direview
                        <span class="badge bg-warning text-dark rounded-pill ms-1"><?= $reviewStats['belum_direview'] ?></span>
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

                <!-- 2. Horizontal Advanced Filter Bar -->
                <div class="row g-2 mb-4 align-items-center bg-light p-3 rounded-3 border">
                    <!-- Live Search -->
                    <div class="col-lg-3 col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" id="liveSearchInput" class="form-control bg-white border-start-0" placeholder="Cari dokumen, SK, uploader..." onkeyup="filterHubTable()">
                        </div>
                    </div>

                    <!-- Filter Fakultas -->
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select form-select-sm" id="fakultasFilter" onchange="onFakultasChange()">
                            <option value="">-- Semua Fakultas --</option>
                            <?php foreach ($fakultasList as $fak): ?>
                                <option value="<?= $fak['id'] ?>"><?= htmlspecialchars($fak['nama_fakultas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Program Studi (Dynamic) -->
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select form-select-sm" id="prodiFilter" onchange="filterHubTable()">
                            <option value="">-- Semua Program Studi --</option>
                            <?php foreach ($prodiList as $prd): ?>
                                <option value="<?= $prd['id'] ?>" data-fakultas-id="<?= $prd['fakultas_id'] ?>">
                                    <?= htmlspecialchars($prd['nama_prodi']) ?> (<?= $prd['jenjang'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Siklus PPEPP -->
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select form-select-sm" id="siklusFilter" onchange="filterHubTable()">
                            <option value="">-- Semua Siklus PPEPP --</option>
                            <option value="penetapan">P - Penetapan</option>
                            <option value="pelaksanaan">P - Pelaksanaan</option>
                            <option value="evaluasi">E - Evaluasi</option>
                            <option value="pengendalian">P - Pengendalian</option>
                            <option value="peningkatan">P - Peningkatan</option>
                        </select>
                    </div>

                    <!-- Filter Bidang -->
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select form-select-sm" id="bidangFilter" onchange="filterHubTable()">
                            <option value="">-- Semua Bidang --</option>
                            <?php foreach ($bidangList as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Reset Filter Button -->
                    <div class="col-lg-1 col-md-12 text-lg-end text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-pill" onclick="resetHubFilters()" title="Reset semua filter ke default">
                            <i class="fas fa-arrow-rotate-left me-1"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- Table Header Metadata & Row Counter -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2.5">
                    <div class="small text-muted fw-semibold" id="tableCountDisplay">
                        Menampilkan <span class="fw-bold text-dark" id="visibleDocCount">0</span> dari <span class="fw-bold text-dark"><?= count($documents) ?></span> Dokumen
                    </div>
                    <div class="small text-muted fst-italic" style="font-size: 0.72rem;">
                        <i class="fas fa-circle-info text-primary me-1"></i> Tip: Klik tombol <strong>Setujui</strong> atau <strong>Minta Revisi</strong> untuk verifikasi instan tanpa reload halaman.
                    </div>
                </div>

                <!-- Master Documents Table -->
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0" id="hubMasterTable" style="font-size: 0.85rem;">
                        <thead class="table-light text-secondary" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <tr>
                                <th class="text-center ps-3" style="width: 45px;">No</th>
                                <th style="min-width: 220px;">Dokumen Mutu</th>
                                <th style="min-width: 170px;">Unit &amp; Level</th>
                                <th class="text-center" style="width: 110px;">Siklus</th>
                                <th style="min-width: 140px;">Bidang &amp; Standar</th>
                                <th class="text-center" style="width: 110px;">Berkas</th>
                                <th class="text-center" style="width: 160px;">Status Review</th>
                                <th class="text-center pe-3" style="width: 170px;">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documents)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fs-1 text-secondary opacity-50 mb-2"></i>
                                        <p class="mb-0 fw-semibold">Belum ada dokumen PPEPP yang diunggah.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $rowNo = 1;
                                foreach ($documents as $doc): 
                                    $st = $doc['status_review'] ?? 'belum_direview';
                                    $isFakDoc = ($doc['level'] === 'fakultas' || empty($doc['prodi_id']));
                                    $unitFakId = $doc['calculated_fakultas_id'] ?? $doc['fakultas_id'] ?? 0;
                                    $unitPrdId = $doc['prodi_id'] ?? 0;
                                    $siklus = $doc['siklus'] ?? '';
                                ?>
                                    <tr class="hub-doc-row" 
                                        data-doc-id="<?= $doc['id'] ?>"
                                        data-status="<?= $st ?>"
                                        data-fakultas-id="<?= $unitFakId ?>"
                                        data-prodi-id="<?= $unitPrdId ?>"
                                        data-siklus="<?= $siklus ?>"
                                        data-bidang-id="<?= $doc['bidang_id'] ?? '' ?>"
                                        data-title="<?= htmlspecialchars(strtolower($doc['nama_dokumen'])) ?>"
                                        data-nomor="<?= htmlspecialchars(strtolower($doc['nomor_dokumen'] ?? '')) ?>"
                                        data-uploader="<?= htmlspecialchars(strtolower($doc['uploader_name'] ?? '')) ?>"
                                        data-unit="<?= htmlspecialchars(strtolower(($doc['nama_prodi'] ?? '') . ' ' . ($doc['nama_fakultas'] ?? ''))) ?>">
                                        
                                        <!-- No -->
                                        <td class="text-center text-muted fw-semibold ps-3 row-number"><?= $rowNo++ ?></td>

                                        <!-- Dokumen Mutu -->
                                        <td>
                                            <div class="fw-bold text-dark-blue mb-0.5 line-clamp-2" title="<?= htmlspecialchars($doc['nama_dokumen']) ?>">
                                                <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-1.5 mb-1">
                                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                    <span class="text-muted small" style="font-size: 0.72rem;">
                                                        <i class="fas fa-hashtag me-0.5"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($doc['tahun_akademik'])): ?>
                                                    <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">TA <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($doc['uploader_name'] ?: 'Unit Kerja') ?>
                                                &bull; <?= date('d M Y H:i', strtotime($doc['created_at'])) ?>
                                            </div>
                                        </td>

                                        <!-- Unit & Level -->
                                        <td>
                                            <?php if ($isFakDoc): ?>
                                                <div class="d-flex flex-column align-items-start gap-1">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.68rem; font-weight: 700;">
                                                        <i class="fas fa-landmark me-1"></i> DEKANAT FAKULTAS
                                                    </span>
                                                    <span class="fw-semibold text-dark" style="font-size: 0.8rem;"><?= htmlspecialchars($doc['nama_fakultas'] ?? 'Fakultas') ?></span>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex flex-column align-items-start gap-1">
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 0.68rem; font-weight: 700;">
                                                        <i class="fas fa-graduation-cap me-1"></i> PRODI <?= htmlspecialchars($doc['jenjang'] ?? '') ?>
                                                    </span>
                                                    <span class="fw-bold text-dark-blue" style="font-size: 0.82rem;"><?= htmlspecialchars($doc['nama_prodi'] ?? 'Prodi') ?></span>
                                                    <span class="text-muted small" style="font-size: 0.7rem;"><?= htmlspecialchars($doc['nama_fakultas'] ?? '') ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Siklus PPEPP -->
                                        <td class="text-center">
                                            <?php
                                            $siklusColors = [
                                                'penetapan'    => 'primary',
                                                'pelaksanaan'  => 'success',
                                                'evaluasi'     => 'warning',
                                                'pengendalian' => 'danger',
                                                'peningkatan'  => 'info'
                                            ];
                                            $cColor = $siklusColors[$siklus] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $cColor ?> bg-opacity-10 text-<?= $cColor ?> border border-<?= $cColor ?> border-opacity-25 rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem; text-transform: capitalize;">
                                                <?= htmlspecialchars($siklus) ?>
                                            </span>
                                        </td>

                                        <!-- Standar & Bidang -->
                                        <td>
                                            <div class="fw-semibold text-dark mb-0.5 line-clamp-1" title="<?= htmlspecialchars($doc['nama_bidang'] ?: 'Standar Umum') ?>" style="font-size: 0.8rem;">
                                                <?= htmlspecialchars($doc['nama_bidang'] ?: 'Standar Umum') ?>
                                            </div>
                                            <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                                <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.7rem;">
                                                    <i class="fas fa-turn-up fa-rotate-90 text-secondary opacity-50"></i>
                                                    <span class="badge bg-light text-secondary border px-1.5 py-0.5"><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Berkas File / GDrive -->
                                        <td class="text-center text-nowrap">
                                            <?php 
                                            $files = $doc['files'] ?? [];
                                            $filesCount = count($files);
                                            $gdriveLinks = parse_external_links($doc['external_link'] ?? '');
                                            $linkCount = count($gdriveLinks);
                                            $hasFiles = ($filesCount > 0 || (!empty($doc['file_path']) && $doc['jenis_upload'] !== 'link'));
                                            $hasLinks = ($linkCount > 0);
                                            $isKombinasi = ($doc['jenis_upload'] === 'kombinasi') || ($hasFiles && $hasLinks);
                                            ?>
                                            <div class="d-inline-flex align-items-center justify-content-center gap-1.5 flex-wrap">
                                                <?php if ($hasFiles): ?>
                                                    <?php if ($filesCount > 1): ?>
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-sm btn-outline-danger dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                                                    style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                                                <i class="fas fa-folder-open text-danger"></i> <?= $isKombinasi ? 'File (' . $filesCount . ')' : $filesCount . ' Berkas' ?>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 300px; max-width: 400px; font-size: 0.78rem;">
                                                                <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS DOKUMEN:</li>
                                                                <?php foreach ($files as $fItem): ?>
                                                                    <li class="mb-1">
                                                                        <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                                data-bs-toggle="modal" data-bs-target="#hubPdfPreviewModal"
                                                                                data-pdf-url="<?= base_url($fItem['file_path']) ?>"
                                                                                data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                                                data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>">
                                                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                                                <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate">
                                                                                    <i class="fas fa-file-pdf text-danger flex-shrink-0"></i>
                                                                                    <span class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                                                </span>
                                                                                <span class="badge bg-light text-muted border flex-shrink-0" style="font-size: 0.65rem;"><?= htmlspecialchars($fItem['file_size'] ?: 'PDF') ?></span>
                                                                            </div>
                                                                            <?= render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null) ?>
                                                                        </button>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php else: ?>
                                                        <?php $singleFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'narasi' => '']; ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold"
                                                                data-bs-toggle="modal" data-bs-target="#hubPdfPreviewModal"
                                                                data-pdf-url="<?= base_url($singleFile['file_path']) ?>"
                                                                data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                                                data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                                                style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                                            <i class="fas fa-file-pdf text-danger"></i> <?= $isKombinasi ? 'File PDF' : 'Berkas PDF' ?>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if ($hasLinks): ?>
                                                    <?php if ($linkCount > 1): ?>
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                                                    style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                                                <i class="fab fa-google-drive text-primary"></i> <?= $isKombinasi ? 'Drive (' . $linkCount . ')' : $linkCount . ' Tautan' ?>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 250px; font-size: 0.78rem;">
                                                                <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">TAUTAN DOKUMEN:</li>
                                                                <?php foreach ($gdriveLinks as $idx => $gLink): ?>
                                                                    <li>
                                                                        <a class="dropdown-item p-2 rounded-2 text-primary d-flex flex-column gap-1" href="<?= htmlspecialchars($gLink['url']) ?>" target="_blank" rel="noopener noreferrer">
                                                                            <div class="d-flex align-items-center justify-content-between w-100">
                                                                                <span><i class="fab fa-google-drive me-1.5 text-primary"></i> Tautan <?= $idx + 1 ?> <?= !empty($gLink['narasi']) ? ' - ' . htmlspecialchars($gLink['narasi']) : '' ?></span>
                                                                                <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 0.7rem;"></i>
                                                                            </div>
                                                                            <?= render_sub_standar_badges($gLink['sub_bidang_ids'] ?? null) ?>
                                                                        </a>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php elseif ($linkCount === 1): ?>
                                                        <?php $singleLink = $gdriveLinks[0]; ?>
                                                        <a href="<?= htmlspecialchars($singleLink['url']) ?>" target="_blank" rel="noopener noreferrer" 
                                                           class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                           style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                                            <i class="fab fa-google-drive text-primary"></i> <?= $isKombinasi ? 'GDrive' : 'Buka Tautan' ?>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if (!$hasFiles && !$hasLinks): ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Status Review -->
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <?php if ($st === 'perlu_perbaikan'): ?>
                                                    <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                        <i class="fas fa-triangle-exclamation text-danger"></i> Perlu Revisi Unit
                                                    </span>
                                                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#viewLpmNoteModal"
                                                            data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                            data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                                            data-reviewer="<?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM', ENT_QUOTES) ?>"
                                                            data-date="<?= !empty($doc['reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['reviewed_at'])) : '' ?>"
                                                            data-status="perlu_perbaikan"
                                                            style="font-size: 0.7rem;">
                                                        <i class="fas fa-comment-dots"></i> Lihat Catatan
                                                    </button>
                                                <?php elseif ($st === 'sudah_diperbaiki'): ?>
                                                    <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                        <i class="fas fa-clock text-warning"></i> Sudah Diperbaiki
                                                    </span>
                                                    <?php if (!empty($doc['catatan_review'])): ?>
                                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#viewLpmNoteModal"
                                                                data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                                data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                                                data-reviewer="<?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM', ENT_QUOTES) ?>"
                                                                data-date="<?= !empty($doc['updated_at']) ? date('d/m/Y H:i', strtotime($doc['updated_at'])) : '' ?>"
                                                                data-status="sudah_diperbaiki"
                                                                style="font-size: 0.7rem;">
                                                            <i class="fas fa-history"></i> Catatan Lalu
                                                        </button>
                                                    <?php endif; ?>
                                                <?php elseif ($st === 'sesuai'): ?>
                                                    <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                        <i class="fas fa-circle-check text-success"></i> Sesuai Standar
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                        <i class="fas fa-clock text-warning"></i> Belum Direview
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Aksi Cepat (Quick Review) -->
                                        <td class="text-center pe-3">
                                            <div class="d-flex flex-column align-items-center" style="gap: 8px;">
                                                <!-- Tombol Setujui Langsung -->
                                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1" 
                                                        style="font-size: 0.75rem;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#quickApproveModal"
                                                        data-id="<?= $doc['id'] ?>"
                                                        data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                        data-unit="<?= htmlspecialchars($isFakDoc ? ($doc['nama_fakultas'] ?? '') : ($doc['nama_prodi'] ?? ''), ENT_QUOTES) ?>">
                                                    <i class="fas fa-circle-check"></i> Setujui
                                                </button>

                                                <!-- Tombol Minta Revisi -->
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 w-100 fw-bold shadow-2xs d-flex align-items-center justify-content-center gap-1" 
                                                        style="font-size: 0.75rem;"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#quickRevisionModal"
                                                        data-id="<?= $doc['id'] ?>"
                                                        data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                        data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                                        data-unit="<?= htmlspecialchars($isFakDoc ? ($doc['nama_fakultas'] ?? '') : ($doc['nama_prodi'] ?? ''), ENT_QUOTES) ?>">
                                                    <i class="fas fa-pen-to-square"></i> Minta Revisi
                                                </button>

                                                <!-- Tombol Lihat Rincian -->
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 w-100 fw-semibold shadow-2xs d-flex align-items-center justify-content-center gap-1 text-primary" 
                                                        style="font-size: 0.75rem;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#docModal<?= $doc['id'] ?>"
                                                        title="Lihat Rincian Dokumen">
                                                    <i class="fas fa-eye"></i> Rincian
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Detail Dokumen (Rincian Dokumen Mutu) -->
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
                                                <div class="modal-body p-4 text-start">
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <span class="small text-muted d-block mb-1">Unit Pemilik / Sasaran:</span>
                                                            <div>
                                                                <?php if ($isFakDoc): ?>
                                                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE;">
                                                                        <i class="fas fa-building-columns me-1"></i> Fakultas <?= htmlspecialchars($doc['nama_fakultas'] ?? '') ?>
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
                                                                <?= review_status_badge($st) ?>
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
                                                        <div class="p-3 rounded-3 mb-3 border <?= $st === 'perlu_perbaikan' ? 'border-danger' : 'border-primary' ?>" style="background: <?= $st === 'perlu_perbaikan' ? '#FFF1F2' : '#EFF6FF' ?>; border-left: 4px solid <?= $st === 'perlu_perbaikan' ? '#DC2626' : '#2563EB' ?> !important;">
                                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                                <span class="small fw-bold text-uppercase <?= $st === 'perlu_perbaikan' ? 'text-danger' : 'text-primary' ?>" style="font-size: 0.74rem;">
                                                                    <i class="fas fa-comment-dots me-1"></i> Catatan Evaluasi Reviewer LPM:
                                                                </span>
                                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                                                    <i class="fas fa-award me-0.5"></i> <?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM') ?>
                                                                </span>
                                                            </div>
                                                            <div class="small text-dark mb-2" style="line-height: 1.55; white-space: pre-wrap; font-size: 0.85rem;"><?= htmlspecialchars($doc['catatan_review']) ?></div>
                                                            <div class="small text-muted d-flex align-items-center gap-2 pt-1 border-top" style="font-size: 0.72rem; border-color: rgba(0,0,0,0.06) !important;">
                                                                <span><i class="fas fa-building-columns text-primary me-1"></i><?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM') ?></span>
                                                                <?php if (!empty($doc['reviewed_at'])): ?>
                                                                    <span>&bull;</span>
                                                                    <span><i class="far fa-clock text-secondary me-1"></i><?= date('d M Y, H:i', strtotime($doc['reviewed_at'])) ?> WIB</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Berkas Lampiran Fisik -->
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
                                                                                        data-bs-target="#hubPdfPreviewModal" 
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

                                                    <!-- Tautan Google Drive Eksternal -->
                                                    <?php if (!empty($gdriveLinks)): ?>
                                                        <div class="p-3.5 p-md-4 rounded-4 bg-light border mb-4">
                                                            <div class="d-flex align-items-center justify-content-between pb-2.5 mb-3 border-bottom border-light-subtle">
                                                                <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                                                                    <i class="fab fa-google-drive text-success"></i> Tautan Google Drive Eksternal (<?= count($gdriveLinks) ?>):
                                                                </span>
                                                                <span class="badge bg-success bg-opacity-10 text-success border px-2.5 py-1 rounded-pill fw-medium" style="font-size: 0.7rem;">Cloud Storage</span>
                                                            </div>
                                                            <div class="d-flex flex-column gap-3">
                                                                <?php foreach ($gdriveLinks as $lIdx => $l): 
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
                                                                                        data-bs-target="#hubPdfPreviewModal" 
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
                                                    <?php if (empty($files) && empty($gdriveLinks)): ?>
                                                        <div class="p-3 rounded-3 bg-light border text-center text-muted small mb-3">
                                                            <i class="fas fa-circle-info text-secondary me-1"></i> Belum ada berkas fisik maupun tautan yang dilampirkan pada dokumen ini.
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer border-top pt-3">
                                                    <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                                    <th class="ps-3" style="width: 80px;">Kode</th>
                                    <th>Fakultas</th>
                                    <th>Pimpinan Dekan</th>
                                    <th class="text-center">Total Dokumen</th>
                                    <th class="text-center">Perlu Review</th>
                                    <th class="text-center">Perlu Revisi</th>
                                    <th class="text-center">Sesuai Mutu</th>
                                    <th style="min-width: 150px;">Progres Kepatuhan</th>
                                    <th class="text-center pe-3" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fakultasSummaries as $fsum): 
                                    $totFak = (int)$fsum['total_dokumen_fakultas'];
                                    $revFak = (int)$fsum['perlu_review_count'];
                                    $rejFak = (int)$fsum['revisi_count'];
                                    $appFak = (int)$fsum['sesuai_count'];
                                    $pctFak = $totFak > 0 ? round(($appFak / $totFak) * 100) : 0;
                                ?>
                                    <tr>
                                        <td class="ps-3"><span class="badge bg-light text-dark border"><?= htmlspecialchars($fsum['kode_fakultas']) ?></span></td>
                                        <td>
                                            <span class="fw-bold text-dark-blue"><?= htmlspecialchars($fsum['nama_fakultas']) ?></span>
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
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctFak ?>%" aria-valuenow="<?= $pctFak ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="small fw-bold text-dark" style="font-size: 0.72rem; min-width: 32px;"><?= $pctFak ?>%</span>
                                            </div>
                                        </td>
                                        <td class="text-center pe-3">
                                            <a href="<?= base_url('admin/review-fakultas/' . $fsum['id']) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1">
                                                <i class="fas fa-arrow-right me-1"></i> Detail
                                            </a>
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

                    <div class="mb-2">
                        <label for="approveCatatan" class="form-label fw-bold text-dark-blue small mb-1.5" style="font-size: 0.82rem;">
                            Catatan Verifikator LPM <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <input type="text" class="form-control rounded-3 p-2.5 text-dark" id="approveCatatan" name="catatan_review" 
                               value="Dokumen telah diverifikasi dan disetujui sesuai standar mutu."
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

    // Show all prodi options
    document.querySelectorAll('#prodiFilter option').forEach(opt => opt.style.display = '');

    // Reset status pill to default: perlu_tindakan
    applyStatusFilter('perlu_tindakan');
}

function filterHubTable() {
    const searchVal = document.getElementById('liveSearchInput').value.toLowerCase().trim();
    const fakultasVal = document.getElementById('fakultasFilter').value;
    const prodiVal = document.getElementById('prodiFilter').value;
    const siklusVal = document.getElementById('siklusFilter').value;
    const bidangVal = document.getElementById('bidangFilter').value;

    const rows = document.querySelectorAll('.hub-doc-row');
    let visibleCount = 0;
    let runningNo = 1;

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status') || '';
        const rowFakultas = row.getAttribute('data-fakultas-id') || '';
        const rowProdi = row.getAttribute('data-prodi-id') || '';
        const rowSiklus = row.getAttribute('data-siklus') || '';
        const rowBidang = row.getAttribute('data-bidang-id') || '';

        const title = row.getAttribute('data-title') || '';
        const nomor = row.getAttribute('data-nomor') || '';
        const uploader = row.getAttribute('data-uploader') || '';
        const unit = row.getAttribute('data-unit') || '';

        // Status match logic
        let matchStatus = false;
        if (currentStatusFilter === 'all') {
            matchStatus = true;
        } else if (currentStatusFilter === 'perlu_tindakan') {
            matchStatus = (rowStatus === 'belum_direview' || rowStatus === 'sudah_diperbaiki');
        } else {
            matchStatus = (rowStatus === currentStatusFilter);
        }

        // Dropdown matches
        const matchFakultas = (!fakultasVal || rowFakultas === fakultasVal);
        const matchProdi = (!prodiVal || rowProdi === prodiVal);
        const matchSiklus = (!siklusVal || rowSiklus === siklusVal);
        const matchBidang = (!bidangVal || rowBidang === bidangVal);

        // Search match
        const matchSearch = (!searchVal || 
            title.includes(searchVal) || 
            nomor.includes(searchVal) || 
            uploader.includes(searchVal) || 
            unit.includes(searchVal)
        );

        if (matchStatus && matchFakultas && matchProdi && matchSiklus && matchBidang && matchSearch) {
            row.style.display = '';
            const noEl = row.querySelector('.row-number');
            if (noEl) noEl.textContent = runningNo++;
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const visibleCountEl = document.getElementById('visibleDocCount');
    if (visibleCountEl) visibleCountEl.textContent = visibleCount;

    const noResultMsg = document.getElementById('noMatchingDocsMsg');
    const tableEl = document.getElementById('hubMasterTable');
    if (visibleCount === 0 && rows.length > 0) {
        if (noResultMsg) noResultMsg.classList.remove('d-none');
        if (tableEl) tableEl.classList.add('d-none');
    } else {
        if (noResultMsg) noResultMsg.classList.add('d-none');
        if (tableEl) tableEl.classList.remove('d-none');
    }
}

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

            document.getElementById('approveDocId').value = docId;
            document.getElementById('approveDocTitle').textContent = docTitle;
            document.getElementById('approveDocUnit').textContent = docUnit ? 'Unit: ' + docUnit : '';
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

            document.getElementById('revDocId').value = docId;
            document.getElementById('revDocTitle').textContent = docTitle;
            document.getElementById('revDocUnit').textContent = docUnit ? 'Unit: ' + docUnit : '';
            document.getElementById('revCatatan').value = docNotes;
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
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
