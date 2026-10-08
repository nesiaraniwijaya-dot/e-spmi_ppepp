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

        <!-- Search Bar and Bidang Filter -->
        <div class="row g-3 align-items-center mb-4">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-1" id="filterSearch" placeholder="Cari nama dokumen, nomor SK, deskripsi..." oninput="handleTableFilter()">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select form-select-sm" id="filterBidang" onchange="handleTableFilter()">
                    <option value="">-- Semua Bidang --</option>
                    <?php foreach ($bidangList as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 text-end d-flex justify-content-md-end align-items-center gap-2">
                <label for="pageSizeSelect" class="small text-muted text-nowrap mb-0" style="font-size: 0.78rem;">Tampilkan:</label>
                <select class="form-select form-select-sm form-select-perpage" id="pageSizeSelect" style="min-width: 108px; width: auto;" onchange="changePageSize(this.value)">
                    <option value="10" selected>10 data</option>
                    <option value="25">25 data</option>
                    <option value="50">50 data</option>
                    <option value="100">100 data</option>
                    <option value="all">Semua</option>
                </select>
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
                                <div class="fw-semibold">Belum Ada Dokumen Mutu Tingkat Fakultas</div>
                                <div class="small">Klik tombol "Unggah Dokumen Baru" untuk menambahkan dokumen mutu fakultas pertama Anda.</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($documents as $doc): 
                            $siklusKey = strtolower($doc['siklus']);
                            $files = $doc['files'] ?? [];
                            $fileCount = count($files);
                            $gdriveLinks = parse_external_links($doc['external_link'] ?? '');
                            $linkCount = count($gdriveLinks);

                            // Status Review Data
                            $reviewStatus = $doc['status_review'] ?? 'belum_direview';
                            $reviewNotes = $doc['catatan_review'] ?? '';
                            $reviewerName = 'Pusat Penjaminan Mutu LPM';
                            $isPerluPerbaikan = ($reviewStatus === 'perlu_perbaikan');
                        ?>
                            <tr class="doc-row <?= $isPerluPerbaikan ? 'table-row-revision' : '' ?>" 
                                data-siklus="<?= $siklusKey ?>" 
                                data-bidang-id="<?= $doc['bidang_id'] ?? '' ?>"
                                data-review-status="<?= $reviewStatus ?>"
                                data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . $doc['nomor_dokumen'] . ' ' . ($doc['nama_bidang'] ?? ''))) ?>"
                                style="<?= $isPerluPerbaikan ? 'border-left: 4px solid #DC2626 !important; background: #FFFBFB;' : '' ?>">
                                
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                
                                <td>
                                    <div class="fw-bold text-dark-blue mb-1">
                                        <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-1.5 text-muted small" style="font-size: 0.72rem;">
                                        <?php if (!empty($doc['nomor_dokumen'])): ?>
                                            <span><i class="fas fa-hashtag text-secondary me-0.5"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                            <span>•</span>
                                        <?php endif; ?>
                                        <span class="badge bg-light text-secondary border px-1.5 py-0.5">TA <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
                                    </div>
                                </td>

                                <td>
                                    <?= siklus_badge($doc['siklus']) ?>
                                </td>

                                <td>
                                    <?php if (!empty($doc['nama_bidang'])): ?>
                                        <div class="fw-semibold text-secondary" style="font-size: 0.78rem;">
                                            <?= htmlspecialchars($doc['nama_bidang']) ?>
                                        </div>
                                        <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                            <div class="text-muted small d-flex align-items-center gap-1 mt-0.5" style="font-size: 0.7rem;">
                                                <i class="fas fa-turn-up fa-rotate-90 text-primary opacity-60"></i>
                                                <span class="badge bg-light text-secondary border text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($doc['nama_sub_bidang']) ?>">
                                                    <?= htmlspecialchars($doc['nama_sub_bidang']) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Berkas & Narasi Column -->
                                <td>
                                    <?php 
                                    $hasFiles = ($fileCount > 0 || (!empty($doc['file_path']) && $doc['jenis_upload'] !== 'link'));
                                    $hasLinks = ($linkCount > 0);
                                    $isKombinasi = ($doc['jenis_upload'] === 'kombinasi') || ($hasFiles && $hasLinks);
                                    ?>
                                    <div class="d-flex flex-column align-items-start gap-1.5">
                                        <?php if ($isKombinasi): ?>
                                            <span class="badge rounded-pill px-2 py-0.5" style="background: #EDE9FE; color: #7C3AED; border: 1px solid #DDD6FE; font-size: 0.67rem; font-weight: 600;">
                                                <i class="fas fa-layer-group me-1"></i>Kombinasi
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($hasFiles): ?>
                                            <?php if ($fileCount > 1): ?>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 dropdown-toggle d-inline-flex align-items-center gap-1 shadow-2xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                                        <i class="fas fa-copy text-primary"></i> <?= $fileCount ?> Berkas
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 320px; max-width: 440px; width: max-content; font-size: 0.78rem;">
                                                        <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS UNTUK DILIHAT:</li>
                                                        <?php foreach ($files as $fIdx => $fItem): 
                                                            $fExt = strtolower($fItem['file_extension']);
                                                            $isFSafe = in_array($fExt, ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
                                                        ?>
                                                            <li class="mb-1.5">
                                                                <div class="dropdown-item p-2 rounded-2 text-wrap border-bottom border-light">
                                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                                        <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate">
                                                                            <i class="fas fa-file-<?= ($fExt==='pdf')?'pdf text-danger':(($fExt==='doc'||$fExt==='docx')?'word text-primary':'excel text-success') ?>"></i>
                                                                            <span class="text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($fItem['file_name']) ?>"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                                        </span>
                                                                        <span class="badge bg-light text-muted border" style="font-size: 0.65rem;"><?= htmlspecialchars($fItem['file_size']) ?></span>
                                                                    </div>
                                                                    <?php if (!empty($fItem['narasi'])): ?>
                                                                        <div class="small text-muted bg-light p-1.5 rounded border border-light-subtle mb-1.5" style="font-size: 0.72rem; line-height: 1.45; white-space: pre-line; word-break: break-word;">
                                                                            <i class="fas fa-quote-left text-primary opacity-50 me-1"></i><?= htmlspecialchars($fItem['narasi']) ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <div class="d-flex gap-1.5 justify-content-end">
                                                                        <?php if ($fExt === 'pdf'): 
                                                                            $fLimit = isset($fItem['is_page_limited']) && (int)$fItem['is_page_limited'] === 0 ? 0 : (int)($fItem['public_page_limit'] ?? 1);
                                                                            $fCanDl = (int)($fItem['can_download_public'] ?? 0);
                                                                        ?>
                                                                            <button type="button" class="btn btn-xs btn-primary rounded-pill px-2.5 py-0.5"
                                                                                    data-bs-toggle="modal" 
                                                                                    data-bs-target="#pdfPreviewModal" 
                                                                                    data-pdf-url="<?= base_url($fItem['file_path']) ?>"
                                                                                    data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                                                    data-public-limit="<?= $fLimit ?>"
                                                                                    data-can-download="<?= $fCanDl ?>"
                                                                                    data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                                                    data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null)) ?>"
                                                                                    style="font-size: 0.7rem;">
                                                                                <i class="fas fa-eye me-1"></i> Pratinjau
                                                                            </button>
                                                                        <?php endif; ?>
                                                                        <a href="<?= base_url($fItem['file_path']) ?>" download class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 0.7rem;">
                                                                            <i class="fas fa-download me-1"></i> Unduh
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php else: 
                                                $singleFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'narasi' => '', 'file_extension' => 'pdf', 'file_size' => 'PDF'];
                                                $sfExt = strtolower($singleFile['file_extension'] ?? 'pdf');
                                                $sLimit = isset($singleFile['is_page_limited']) && (int)$singleFile['is_page_limited'] === 0 ? 0 : (int)($singleFile['public_page_limit'] ?? $doc['public_page_limit'] ?? 1);
                                                $sCanDl = (int)($singleFile['can_download_public'] ?? $doc['can_download_public'] ?? 0);
                                            ?>
                                                <div class="d-inline-flex flex-column align-items-start gap-1">
                                                    <div class="d-inline-flex align-items-center gap-1">
                                                        <?php if ($sfExt === 'pdf'): ?>
                                                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#pdfPreviewModal" 
                                                                    data-pdf-url="<?= base_url($singleFile['file_path']) ?>"
                                                                    data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                                                    data-public-limit="<?= $sLimit ?>"
                                                                    data-can-download="<?= $sCanDl ?>"
                                                                    data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                                                    data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null)) ?>"
                                                                    style="font-size: 0.72rem;">
                                                                <i class="fas fa-file-pdf"></i> PDF
                                                            </button>
                                                        <?php else: ?>
                                                            <a href="<?= base_url($singleFile['file_path']) ?>" download class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.72rem;">
                                                                <i class="fas fa-file"></i> <?= strtoupper($sfExt) ?>
                                                            </a>
                                                        <?php endif; ?>
                                                        <span class="text-muted" style="font-size: 0.68rem;"><?= htmlspecialchars($singleFile['file_size'] ?? '') ?></span>
                                                    </div>
                                                    <?php if (!empty($singleFile['narasi'])): ?>
                                                        <div class="text-muted fst-italic text-truncate" style="max-width: 180px; font-size: 0.68rem;" title="<?= htmlspecialchars($singleFile['narasi']) ?>">
                                                            <i class="fas fa-comment-dots text-primary me-0.5"></i> <?= htmlspecialchars($singleFile['narasi']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if ($hasLinks): ?>
                                            <?php if ($linkCount > 1): ?>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 dropdown-toggle d-inline-flex align-items-center gap-1 shadow-2xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                                        <i class="fab fa-google-drive text-primary"></i> <?= $linkCount ?> Link GDrive
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 320px; max-width: 440px; width: max-content; font-size: 0.78rem;">
                                                        <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH TAUTAN GDRIVE:</li>
                                                        <?php foreach ($gdriveLinks as $lIdx => $linkObj): 
                                                            $gUrl = $linkObj['url'];
                                                            $gNarasi = $linkObj['narasi'];
                                                        ?>
                                                            <li class="mb-1.5">
                                                                <a href="<?= htmlspecialchars($gUrl) ?>" target="_blank" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light">
                                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                                        <span class="d-flex align-items-center gap-1.5 fw-semibold text-primary text-truncate">
                                                                            <i class="fab fa-google-drive"></i>
                                                                            <span>Tautan GDrive #<?= $lIdx + 1 ?></span>
                                                                        </span>
                                                                        <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 0.68rem;"></i>
                                                                    </div>
                                                                    <?php if (!empty($gNarasi)): ?>
                                                                        <div class="small text-muted bg-light p-1.5 rounded border border-light-subtle" style="font-size: 0.72rem; line-height: 1.45; white-space: pre-line; word-break: break-word;">
                                                                            <i class="fas fa-quote-left text-primary opacity-50 me-1"></i><?= htmlspecialchars($gNarasi) ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php elseif ($linkCount === 1): 
                                                $singleLink = $gdriveLinks[0];
                                            ?>
                                                <div class="d-inline-flex flex-column align-items-start gap-1">
                                                    <a href="<?= htmlspecialchars($singleLink['url']) ?>" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.72rem;">
                                                        <i class="fab fa-google-drive"></i> GDrive
                                                    </a>
                                                    <?php if (!empty($singleLink['narasi'])): ?>
                                                        <div class="text-muted fst-italic text-truncate" style="max-width: 180px; font-size: 0.68rem;" title="<?= htmlspecialchars($singleLink['narasi']) ?>">
                                                            <i class="fas fa-comment-dots text-primary me-0.5"></i> <?= htmlspecialchars($singleLink['narasi']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if (!$hasFiles && !$hasLinks): ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Status Review LPM Column -->
                                <td>
                                    <?php if ($reviewStatus === 'perlu_perbaikan'): ?>
                                        <div class="d-flex flex-column align-items-start gap-1">
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-triangle-exclamation text-danger"></i> Perlu Perbaikan
                                            </span>
                                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1 btn-view-review-notes" 
                                                    data-title="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-status="<?= $reviewStatus ?>"
                                                    data-reviewer="<?= htmlspecialchars($reviewerName) ?>"
                                                    data-reviewed-at="<?= $reviewedAt ?>"
                                                    data-notes="<?= htmlspecialchars($reviewNotes) ?>"
                                                    style="font-size: 0.7rem;">
                                                <i class="fas fa-comment-dots"></i> Lihat Catatan
                                            </button>
                                        </div>
                                    <?php elseif ($reviewStatus === 'sudah_diperbaiki'): ?>
                                        <div class="d-flex flex-column align-items-start gap-1">
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #EFF6FF; color: #1E40AF; border: 1px solid #93C5FD; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-rotate text-primary"></i> Sudah Diperbaiki
                                            </span>
                                            <span class="text-muted" style="font-size: 0.68rem;">Menunggu Review Ulang</span>
                                        </div>
                                    <?php elseif ($reviewStatus === 'sesuai'): ?>
                                        <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                            <i class="fas fa-circle-check text-success"></i> Sesuai Standar
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                            <i class="fas fa-clock text-warning"></i> Belum Direview
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi Column -->
                                <td class="text-center text-nowrap" style="width: 130px;">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-1.5">
                                        <!-- Aksi 1: Lihat Rincian Dokumen -->
                                        <button type="button" 
                                                class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                                style="width: 36px; height: 36px;" 
                                                title="Lihat Rincian Dokumen" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#docModal<?= $doc['id'] ?>">
                                            <i class="fas fa-eye text-primary" style="font-size: 0.85rem;"></i>
                                        </button>

                                        <!-- Aksi 2: Ubah / Edit Dokumen -->
                                        <a href="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>" 
                                           class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                           style="width: 36px; height: 36px;" 
                                           title="Edit / Ubah Dokumen">
                                            <i class="fas fa-pen text-warning" style="font-size: 0.85rem;"></i>
                                        </a>

                                        <!-- Aksi 3: Hapus Dokumen -->
                                        <form action="<?= base_url('fakultas/dokumen/delete/' . $doc['id']) ?>" method="POST" class="d-inline m-0 p-0">
                                            <button type="button" 
                                                    class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs btn-delete-confirm" 
                                                    data-name="<?= htmlspecialchars($doc['nama_dokumen']) ?>" 
                                                    style="width: 36px; height: 36px;" 
                                                    title="Hapus Dokumen">
                                                <i class="fas fa-trash-can text-danger" style="font-size: 0.85rem;"></i>
                                            </button>
                                        </form>
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
                                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE;">
                                                            <i class="fas fa-building-columns me-1"></i> Fakultas <?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="small text-muted d-block mb-1">Siklus PPEPP & Status:</span>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <?= siklus_badge($doc['siklus']) ?>
                                                        <?= review_status_badge($reviewStatus) ?>
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
                                            <?php if (!empty($reviewNotes)): ?>
                                                <div class="p-3 rounded-3 mb-3 border <?= $reviewStatus === 'perlu_perbaikan' ? 'border-danger' : 'border-primary' ?>" style="background: <?= $reviewStatus === 'perlu_perbaikan' ? '#FFF1F2' : '#EFF6FF' ?>; border-left: 4px solid <?= $reviewStatus === 'perlu_perbaikan' ? '#DC2626' : '#2563EB' ?> !important;">
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                        <span class="small fw-bold text-uppercase <?= $reviewStatus === 'perlu_perbaikan' ? 'text-danger' : 'text-primary' ?>" style="font-size: 0.74rem;">
                                                            <i class="fas fa-comment-dots me-1"></i> Catatan Evaluasi Reviewer LPM:
                                                        </span>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                                            <i class="fas fa-award me-0.5"></i> <?= htmlspecialchars($reviewerName) ?>
                                                        </span>
                                                    </div>
                                                    <div class="small text-dark mb-2" style="line-height: 1.55; white-space: pre-wrap; font-size: 0.85rem;"><?= htmlspecialchars($reviewNotes) ?></div>
                                                    <div class="small text-muted d-flex align-items-center gap-2 pt-1 border-top" style="font-size: 0.72rem; border-color: rgba(0,0,0,0.06) !important;">
                                                        <span><i class="fas fa-building-columns text-primary me-1"></i><?= htmlspecialchars($reviewerName) ?></span>
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
                                            <?php if (empty($files) && empty($gdriveLinks)): ?>
                                                <div class="p-3 rounded-3 bg-light border text-center text-muted small mb-3">
                                                    <i class="fas fa-circle-info text-secondary me-1"></i> Belum ada berkas fisik maupun tautan yang dilampirkan pada dokumen ini.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer border-top pt-3">
                                            <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                                            <a href="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>" class="btn btn-primary bg-scu-blue border-0 rounded-pill px-4 btn-sm fw-semibold shadow-xs">
                                                <i class="fas fa-pen me-1.5"></i> Edit Dokumen
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top mt-3">
            <div id="tableInfo" class="small text-muted"></div>
            <div id="tablePagination"></div>
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
let currentPage = 1;
let pageSize = 10;
let activeSiklus = '';
let filterReviewStatusVal = '';

function selectFakultasSiklusTab(siklus, btn) {
    document.querySelectorAll('#fakultasPpeppTabs .nav-link, .ppepp-tab-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    activeSiklus = siklus ? siklus.toLowerCase() : '';
    currentPage = 1;
    handleTableFilter();
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
    currentPage = 1;
    handleTableFilter();
}

function filterByReviewStatus(status) {
    filterReviewStatusVal = status;
    currentPage = 1;
    handleTableFilter();
}

function handleTableFilter() {
    const searchVal = document.getElementById('filterSearch').value.toLowerCase().trim();
    const bidangVal = document.getElementById('filterBidang').value;
    const allRows = document.querySelectorAll('.doc-row');
    let visibleRows = [];

    allRows.forEach(row => {
        const rowSiklus = row.getAttribute('data-siklus') || '';
        const rowBidang = row.getAttribute('data-bidang-id') || '';
        const rowStatus = row.getAttribute('data-review-status') || '';
        const rowSearch = row.getAttribute('data-search') || '';

        const matchSiklus = !activeSiklus || rowSiklus === activeSiklus;
        const matchBidang = !bidangVal || rowBidang === bidangVal;
        const matchStatus = !filterReviewStatusVal || rowStatus === filterReviewStatusVal;
        const matchSearch = !searchVal || rowSearch.includes(searchVal);

        if (matchSiklus && matchBidang && matchStatus && matchSearch) {
            visibleRows.push(row);
        } else {
            row.style.display = 'none';
        }
    });

    // Render pagination
    const totalVisible = visibleRows.length;
    const totalPages = (pageSize === 'all') ? 1 : Math.ceil(totalVisible / pageSize);
    if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

    const startIdx = (pageSize === 'all') ? 0 : (currentPage - 1) * pageSize;
    const endIdx = (pageSize === 'all') ? totalVisible : startIdx + pageSize;

    visibleRows.forEach((row, idx) => {
        if (idx >= startIdx && idx < endIdx) {
            row.style.display = '';
            row.querySelector('td:first-child').textContent = idx + 1;
        } else {
            row.style.display = 'none';
        }
    });

    // Empty row message
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) {
        emptyRow.style.display = (totalVisible === 0) ? '' : 'none';
    }

    renderPaginationControls(totalVisible, totalPages);
}

function renderPaginationControls(totalVisible, totalPages) {
    const info = document.getElementById('tableInfo');
    const pagination = document.getElementById('tablePagination');
    if (!info || !pagination) return;

    if (totalVisible === 0) {
        info.textContent = 'Menampilkan 0 dari 0 dokumen';
        pagination.innerHTML = '';
        return;
    }

    const startNum = (pageSize === 'all') ? 1 : (currentPage - 1) * pageSize + 1;
    const endNum = (pageSize === 'all') ? totalVisible : Math.min(currentPage * pageSize, totalVisible);
    info.textContent = `Menampilkan ${startNum} - ${endNum} dari total ${totalVisible} dokumen`;

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '<ul class="pagination pagination-sm mb-0">';
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link rounded-start-pill" onclick="goToPage(${currentPage - 1})">&laquo;</button></li>`;
    for (let p = 1; p <= totalPages; p++) {
        html += `<li class="page-item ${p === currentPage ? 'active' : ''}"><button class="page-link" onclick="goToPage(${p})">${p}</button></li>`;
    }
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link rounded-end-pill" onclick="goToPage(${currentPage + 1})">&raquo;</button></li>`;
    html += '</ul>';
    pagination.innerHTML = html;
}

function goToPage(page) {
    currentPage = page;
    handleTableFilter();
}

function changePageSize(size) {
    pageSize = (size === 'all') ? 'all' : parseInt(size);
    currentPage = 1;
    handleTableFilter();
}

document.addEventListener('DOMContentLoaded', function() {
    handleTableFilter();

    // Review Notes Modal Trigger
    document.querySelectorAll('.btn-view-review-notes').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalNoteDocTitle').textContent = this.dataset.title;
            document.getElementById('modalNoteReviewer').textContent = 'Pusat Penjaminan Mutu LPM';
            document.getElementById('modalNoteDate').textContent = this.dataset.reviewedAt || 'Baru Saja';
            document.getElementById('modalNoteContent').textContent = this.dataset.notes || 'Tidak ada catatan tertulis.';
            const modal = new bootstrap.Modal(document.getElementById('fakultasViewNoteModal'));
            modal.show();
        });
    });

    // Delete Confirmation
    document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const docName = this.dataset.name || 'Dokumen ini';

            if (window.Swal) {
                Swal.fire({
                    title: 'Hapus Dokumen Mutu?',
                    html: `Dokumen <strong>"${docName}"</strong> akan dinonaktifkan dan dipindahkan ke daftar dokumen terhapus fakultas. Anda dapat memulihkannya kembali sewaktu-waktu.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'rounded-4' }
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            } else {
                if (confirm(`Pindahkan dokumen "${docName}" ke daftar dokumen terhapus?`)) form.submit();
            }
        });
    });
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
