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

        <!-- PPEPP Cycle Filter Buttons -->
        <div class="d-flex flex-wrap gap-2 mb-4 p-2 bg-light rounded-4" id="ppeppTabsContainer" style="background: #F8FAFC !important; border: 1px solid #E2E8F0;">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold ppepp-tab-btn active" data-siklus="" onclick="selectFakultasSiklusTab('', this)">
                <i class="fas fa-layer-group me-1"></i> Semua (<?= $totalSemua ?>)
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold ppepp-tab-btn" data-siklus="penetapan" onclick="selectFakultasSiklusTab('penetapan', this)">
                <i class="fas fa-file-signature me-1 text-primary"></i> Penetapan (<?= $countPenetapan ?>)
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold ppepp-tab-btn" data-siklus="pelaksanaan" onclick="selectFakultasSiklusTab('pelaksanaan', this)">
                <i class="fas fa-person-running me-1 text-success"></i> Pelaksanaan (<?= $countPelaksanaan ?>)
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold ppepp-tab-btn" data-siklus="evaluasi" onclick="selectFakultasSiklusTab('evaluasi', this)">
                <i class="fas fa-magnifying-glass-chart me-1 text-warning"></i> Evaluasi (<?= $countEvaluasi ?>)
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold ppepp-tab-btn" data-siklus="pengendalian" onclick="selectFakultasSiklusTab('pengendalian', this)">
                <i class="fas fa-sliders me-1" style="color: #7C3AED;"></i> Pengendalian (<?= $countPengendalian ?>)
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold ppepp-tab-btn" data-siklus="peningkatan" onclick="selectFakultasSiklusTab('peningkatan', this)">
                <i class="fas fa-arrow-trend-up me-1 text-info"></i> Peningkatan (<?= $countPeningkatan ?>)
            </button>
        </div>

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
                <label class="small text-muted text-nowrap mb-0" style="font-size: 0.78rem;">Tampilkan:</label>
                <select class="form-select form-select-sm" id="pageSizeSelect" style="width: 75px;" onchange="changePageSize(this.value)">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
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
                        <th style="min-width: 170px;">Bidang</th>
                        <th style="min-width: 140px;">Berkas</th>
                        <th style="min-width: 140px;">Status Review LPM</th>
                        <th style="width: 135px;" class="text-center text-nowrap">Aksi</th>
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
                            $reviewerName = $doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM';
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
                                    <?php if ($doc['jenis_upload'] === 'file'): ?>
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
                                                                <?= render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null) ?>
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
                                        <?php elseif ($fileCount === 1): 
                                            $singleFile = $files[0];
                                            $sfExt = strtolower($singleFile['file_extension']);
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
                                                    <span class="text-muted" style="font-size: 0.68rem;"><?= htmlspecialchars($singleFile['file_size']) ?></span>
                                                </div>
                                                <?= render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null) ?>
                                                <?php if (!empty($singleFile['narasi'])): ?>
                                                    <div class="text-muted fst-italic text-truncate" style="max-width: 180px; font-size: 0.68rem;" title="<?= htmlspecialchars($singleFile['narasi']) ?>">
                                                        <i class="fas fa-comment-dots text-primary me-0.5"></i> <?= htmlspecialchars($singleFile['narasi']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>

                                    <?php elseif ($doc['jenis_upload'] === 'link'): ?>
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
                                                                <?= render_sub_standar_badges($linkObj['sub_bidang_ids'] ?? null) ?>
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
                                                <?= render_sub_standar_badges($singleLink['sub_bidang_ids'] ?? null) ?>
                                                <?php if (!empty($singleLink['narasi'])): ?>
                                                    <div class="text-muted fst-italic text-truncate" style="max-width: 180px; font-size: 0.68rem;" title="<?= htmlspecialchars($singleLink['narasi']) ?>">
                                                        <i class="fas fa-comment-dots text-primary me-0.5"></i> <?= htmlspecialchars($singleLink['narasi']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
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
                                        <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #F3F4F6; color: #4B5563; border: 1px solid #D1D5DB; font-size: 0.72rem; font-weight: 600; padding: 0.35em 0.75em;">
                                            <i class="fas fa-clock text-muted"></i> Belum Direview
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi Column -->
                                <td class="text-center text-nowrap" style="width: 135px;">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-1.5">
                                        <!-- Slot 1: Berkas / Pratinjau / Unduh / Tautan -->
                                        <?php if ($doc['jenis_upload'] === 'file' && $fileCount > 0): ?>
                                            <?php if ($fileCount > 1): ?>
                                                <div class="dropdown d-inline-block">
                                                    <button type="button" 
                                                            class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                                            style="width: 32px; height: 32px;" 
                                                            data-bs-toggle="dropdown" 
                                                            aria-expanded="false" 
                                                            title="Pilih Berkas (<?= $fileCount ?> Berkas)">
                                                        <i class="fas fa-folder-open text-primary" style="font-size: 0.82rem;"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1 rounded-3" style="min-width: 230px; font-size: 0.78rem;">
                                                        <?php foreach ($files as $fItem): 
                                                            $fExt = strtolower($fItem['file_extension']);
                                                            $fLimit = isset($fItem['is_page_limited']) && (int)$fItem['is_page_limited'] === 0 ? 0 : (int)($fItem['public_page_limit'] ?? 1);
                                                            $fCanDl = (int)($fItem['can_download_public'] ?? 0);
                                                        ?>
                                                            <li>
                                                                <?php if ($fExt === 'pdf'): ?>
                                                                    <button type="button" 
                                                                            class="dropdown-item d-flex align-items-center gap-2 py-1.5 px-3"
                                                                            data-bs-toggle="modal" 
                                                                            data-bs-target="#pdfPreviewModal" 
                                                                            data-pdf-url="<?= base_url($fItem['file_path']) ?>" 
                                                                            data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                                            data-public-limit="<?= $fLimit ?>"
                                                                            data-can-download="<?= $fCanDl ?>"
                                                                            data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>">
                                                                        <i class="fas fa-file-pdf text-danger"></i>
                                                                        <span class="text-truncate" style="max-width: 165px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                                    </button>
                                                                <?php else: ?>
                                                                    <a href="<?= base_url($fItem['file_path']) ?>" download class="dropdown-item d-flex align-items-center gap-2 py-1.5 px-3">
                                                                        <i class="fas fa-download text-primary"></i>
                                                                        <span class="text-truncate" style="max-width: 165px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php else: 
                                                $singleFile = $files[0];
                                                $sfExt = strtolower($singleFile['file_extension']);
                                                $sLimit = isset($singleFile['is_page_limited']) && (int)$singleFile['is_page_limited'] === 0 ? 0 : (int)($singleFile['public_page_limit'] ?? $doc['public_page_limit'] ?? 1);
                                                $sCanDl = (int)($singleFile['can_download_public'] ?? $doc['can_download_public'] ?? 0);
                                            ?>
                                                <?php if ($sfExt === 'pdf'): ?>
                                                    <button type="button" 
                                                            class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                                            style="width: 32px; height: 32px;" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#pdfPreviewModal" 
                                                            data-pdf-url="<?= base_url($singleFile['file_path']) ?>" 
                                                            data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                                            data-public-limit="<?= $sLimit ?>"
                                                            data-can-download="<?= $sCanDl ?>"
                                                            data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                                            title="Pratinjau Dokumen">
                                                        <i class="fas fa-eye text-primary" style="font-size: 0.82rem;"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <a href="<?= base_url($singleFile['file_path']) ?>" 
                                                       download 
                                                       class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                                       style="width: 32px; height: 32px;" 
                                                       title="Unduh Berkas <?= strtoupper($sfExt) ?>">
                                                        <i class="fas fa-download text-primary" style="font-size: 0.82rem;"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                        <?php elseif ($doc['jenis_upload'] === 'link' && $linkCount > 0): ?>
                                            <?php if ($linkCount > 1): ?>
                                                <div class="dropdown d-inline-block">
                                                    <button type="button" 
                                                            class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                                            style="width: 32px; height: 32px;" 
                                                            data-bs-toggle="dropdown" 
                                                            aria-expanded="false" 
                                                            title="Buka Tautan Google Drive (<?= $linkCount ?> Tautan)">
                                                        <i class="fab fa-google-drive text-success" style="font-size: 0.85rem;"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1 rounded-3" style="min-width: 220px; font-size: 0.78rem;">
                                                        <?php foreach ($gdriveLinks as $lIdx => $lItem): ?>
                                                            <li>
                                                                <a href="<?= htmlspecialchars($lItem['url']) ?>" target="_blank" class="dropdown-item d-flex align-items-center gap-2 py-1.5 px-3">
                                                                    <i class="fab fa-google-drive text-success"></i>
                                                                    <span class="text-truncate" style="max-width: 165px;"><?= htmlspecialchars($lItem['narasi'] ?: 'Tautan #' . ($lIdx + 1)) ?></span>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php else: 
                                                $firstLink = $gdriveLinks[0];
                                            ?>
                                                <a href="<?= htmlspecialchars($firstLink['url']) ?>" 
                                                   target="_blank" 
                                                   class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                                   style="width: 32px; height: 32px;" 
                                                   title="<?= htmlspecialchars($firstLink['narasi'] ?: 'Buka Tautan Google Drive') ?>">
                                                    <i class="fab fa-google-drive text-success" style="font-size: 0.85rem;"></i>
                                                </a>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <!-- Placeholder jika tidak ada berkas/tautan agar posisi tombol tetap sejajar -->
                                            <button type="button" 
                                                    class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs disabled opacity-25" 
                                                    style="width: 32px; height: 32px;" 
                                                    disabled 
                                                    title="Tidak ada berkas">
                                                <i class="fas fa-minus text-muted" style="font-size: 0.75rem;"></i>
                                            </button>
                                        <?php endif; ?>

                                        <!-- Slot 2: Ubah / Perbaiki Dokumen -->
                                        <a href="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>" 
                                           class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                           style="width: 32px; height: 32px;" 
                                           title="Ubah / Perbaiki Dokumen">
                                            <i class="fas fa-pen text-warning" style="font-size: 0.8rem;"></i>
                                        </a>

                                        <!-- Slot 3: Hapus Dokumen -->
                                        <form action="<?= base_url('fakultas/dokumen/delete/' . $doc['id']) ?>" method="POST" class="d-inline m-0 p-0">
                                            <button type="button" 
                                                    class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs btn-delete-confirm" 
                                                    data-name="<?= htmlspecialchars($doc['nama_dokumen']) ?>" 
                                                    style="width: 32px; height: 32px;" 
                                                    title="Hapus Dokumen">
                                                <i class="fas fa-trash-can text-danger" style="font-size: 0.8rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
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
                    <span><i class="fas fa-user-check text-primary me-1"></i> Reviewer: <strong id="modalNoteReviewer">Admin LPM</strong></span>
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
    document.querySelectorAll('.ppepp-tab-btn').forEach(b => b.classList.remove('active'));
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
            document.getElementById('modalNoteReviewer').textContent = this.dataset.reviewer || 'Pusat Penjaminan Mutu LPM';
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
