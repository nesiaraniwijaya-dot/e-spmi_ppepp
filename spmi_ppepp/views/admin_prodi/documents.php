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
    $perluPerbaikanDocs = $perluPerbaikanDocs ?? array_values(array_filter($documents ?? [], fn($d) => ($d['status_review'] ?? '') === 'perlu_perbaikan'));
    $sudahDiperbaikiDocs = $sudahDiperbaikiDocs ?? array_values(array_filter($documents ?? [], fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));
    $perluCount = count($perluPerbaikanDocs);
    $sudahCount = count($sudahDiperbaikiDocs);
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
                                    Catatan evaluasi mutu dari LPM
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
                                    Verifikasi ulang Tim LPM
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
                                    Belum diajukan ke LPM
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

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        
        <!-- 5 Siklus PPEPP Tabs Header -->
        <ul class="nav ppepp-nav-tabs mb-4" id="prodiPpeppTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" type="button" onclick="selectProdiSiklusTab(this, '')">
                    <i class="fas fa-layer-group"></i> Semua (<?= $ppeppCounts['all'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-penetapan" type="button" onclick="selectProdiSiklusTab(this, 'penetapan')">
                    <i class="fas fa-file-signature"></i> Penetapan (<?= $ppeppCounts['penetapan'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-pelaksanaan" type="button" onclick="selectProdiSiklusTab(this, 'pelaksanaan')">
                    <i class="fas fa-person-chalkboard"></i> Pelaksanaan (<?= $ppeppCounts['pelaksanaan'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-evaluasi" type="button" onclick="selectProdiSiklusTab(this, 'evaluasi')">
                    <i class="fas fa-magnifying-glass-chart"></i> Evaluasi (<?= $ppeppCounts['evaluasi'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-pengendalian" type="button" onclick="selectProdiSiklusTab(this, 'pengendalian')">
                    <i class="fas fa-sliders"></i> Pengendalian (<?= $ppeppCounts['pengendalian'] ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-peningkatan" type="button" onclick="selectProdiSiklusTab(this, 'peningkatan')">
                    <i class="fas fa-arrow-trend-up"></i> Peningkatan (<?= $ppeppCounts['peningkatan'] ?>)
                </button>
            </li>
        </ul>

        <!-- Filter Toolbar -->
        <div class="row g-3 align-items-center mb-4">
            <div class="col-lg-5 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="tableSearchInput" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama dokumen, nomor SK...">
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <select id="tableBidangFilter" class="form-select bg-light">
                    <option value="">-- Semua Bidang --</option>
                    <?php foreach ($bidangList as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3 col-md-2 d-flex justify-content-md-end align-items-center gap-2">
                <label for="tablePerPageSelect" class="small text-muted text-nowrap mb-0" style="font-size: 0.78rem;">Tampilkan:</label>
                <select id="tablePerPageSelect" class="form-select form-select-sm bg-light" style="width: auto; min-width: 80px; font-size: 0.8rem;">
                    <option value="10" selected>10 data</option>
                    <option value="25">25 data</option>
                    <option value="50">50 data</option>
                    <option value="100">100 data</option>
                    <option value="all">Semua</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="ppeppDocumentTable" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Nama & Nomor Dokumen</th>
                        <th style="width: 140px;">Siklus PPEPP</th>
                        <th>Bidang</th>
                        <th>Berkas</th>
                        <th style="min-width: 220px;">Status Review LPM</th>
                        <th class="text-center text-nowrap" style="width: 135px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr class="no-matching-docs">
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary d-block"></i>
                                <h6 class="fw-bold">Belum Ada Dokumen Mutu</h6>
                                <p class="small mb-3">Program studi Anda belum memiliki dokumen PPEPP yang diunggah.</p>
                                <a href="<?= base_url('prodi/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3 bg-scu-blue border-0">
                                    <i class="fas fa-plus me-1"></i> Unggah Dokumen Sekarang
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($documents as $doc): 
                            $status = $doc['status_review'] ?? 'belum_direview';
                            $hasNotes = !empty($doc['catatan_review']);
                            $isPerluPerbaikan = ($status === 'perlu_perbaikan');
                        ?>
                            <tr class="doc-row <?= $isPerluPerbaikan ? 'table-row-revision' : '' ?>" 
                                data-title="<?= htmlspecialchars($doc['nama_dokumen']) ?>" 
                                data-nomor="<?= htmlspecialchars($doc['nomor_dokumen'] ?? '') ?>" 
                                data-bidang-id="<?= $doc['bidang_id'] ?? '' ?>"
                                data-siklus="<?= $doc['siklus'] ?>"
                                data-status-review="<?= $status ?>"
                                style="<?= $isPerluPerbaikan ? 'border-left: 4px solid #DC2626 !important; background: #FFFBFB;' : '' ?>">
                                <td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($doc['nama_dokumen']) ?></div>
                                    <?php if ($doc['nomor_dokumen']): ?>
                                        <div class="small text-muted" style="font-size: 0.72rem;"><i class="fas fa-hashtag me-1"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($doc['tahun_akademik']): ?>
                                        <span class="badge bg-light text-secondary border me-1" style="font-size: 0.68rem;">TA <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= siklus_badge($doc['siklus']) ?>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark mb-0.5">
                                        <?= htmlspecialchars($doc['nama_bidang'] ?: 'Umum / Lainnya') ?>
                                    </div>
                                    <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                        <div class="small text-muted d-flex align-items-center gap-1" style="font-size: 0.7rem;">
                                            <i class="fas fa-turn-up fa-rotate-90 text-secondary opacity-50"></i>
                                            <span class="badge bg-light text-secondary border px-1.5 py-0.5"><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $files = $doc['files'] ?? [];
                                    $filesCount = count($files);
                                    $gdriveLinks = parse_external_links($doc['external_link'] ?? '');
                                    $linkCount = count($gdriveLinks);

                                    if ($doc['jenis_upload'] === 'file'): 
                                    ?>
                                        <?php if ($filesCount > 1): ?>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-xs btn-outline-danger dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                                    <i class="fas fa-folder-open text-danger"></i> <?= $filesCount ?> Berkas
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 320px; max-width: 440px; width: max-content; font-size: 0.78rem;">
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
                                            <div class="d-inline-flex flex-column align-items-start gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#pdfPreviewModal" 
                                                        data-pdf-url="<?= base_url($singleFile['file_path']) ?>" 
                                                        data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>" 
                                                        data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>" 
                                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null)) ?>" 
                                                        data-public-limit="<?= $sLimit ?>" 
                                                        data-can-download="<?= $sCanDl ?>" 
                                                        style="font-size: 0.72rem;">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </button>
                                                <?= render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null) ?>
                                                <?php if (!empty($singleFile['narasi'])): ?>
                                                    <div class="text-muted fst-italic text-truncate" style="max-width: 180px; font-size: 0.68rem;" title="<?= htmlspecialchars($singleFile['narasi']) ?>">
                                                        <i class="fas fa-comment-dots text-primary me-0.5"></i> <?= htmlspecialchars($singleFile['narasi']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif ($linkCount > 1): ?>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-xs btn-outline-primary dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
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
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <?php if ($status === 'perlu_perbaikan'): ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-triangle-exclamation text-danger"></i> Perlu Perbaikan
                                            </span>
                                            <div class="d-flex flex-wrap gap-1 mt-0.5">
                                                <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs btn-prodi-note" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#prodiViewNoteModal"
                                                        data-id="<?= $doc['id'] ?>"
                                                        data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                        data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                                        data-reviewer="<?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM', ENT_QUOTES) ?>"
                                                        data-date="<?= !empty($doc['reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['reviewed_at'])) : '' ?>"
                                                        data-status="perlu_perbaikan"
                                                        data-edit-url="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>"
                                                        style="font-size: 0.7rem;">
                                                    <i class="fas fa-comment-dots"></i> Catatan LPM
                                                </button>
                                                <a href="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>" class="btn btn-xs btn-danger text-white rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs text-decoration-none" style="font-size: 0.7rem;">
                                                    <i class="fas fa-wrench"></i> Perbaiki
                                                </a>
                                            </div>
                                        <?php elseif ($status === 'sudah_diperbaiki'): ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-clock text-warning"></i> Menunggu Review LPM
                                            </span>
                                            <?php if (!empty($doc['catatan_review'])): ?>
                                                <div class="mt-0.5">
                                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs btn-prodi-note" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#prodiViewNoteModal"
                                                            data-id="<?= $doc['id'] ?>"
                                                            data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                            data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                                            data-reviewer="<?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM', ENT_QUOTES) ?>"
                                                            data-date="<?= !empty($doc['reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['reviewed_at'])) : '' ?>"
                                                            data-status="sudah_diperbaiki"
                                                            data-edit-url="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>"
                                                            style="font-size: 0.7rem;">
                                                        <i class="fas fa-history"></i> Catatan LPM
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif ($status === 'sesuai'): ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-circle-check text-success"></i> Sesuai Standar
                                            </span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #F3F4F6; color: #4B5563; border: 1px solid #D1D5DB; font-size: 0.72rem; font-weight: 600; padding: 0.35em 0.75em;">
                                                <i class="fas fa-clock text-muted"></i> Belum Direview
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center text-nowrap" style="width: 135px;">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-1.5">
                                        <!-- Slot 1: Berkas / Pratinjau / Unduh / Tautan -->
                                        <?php if ($doc['jenis_upload'] === 'file' && $filesCount > 0): ?>
                                            <?php if ($filesCount > 1): ?>
                                                <div class="dropdown d-inline-block">
                                                    <button type="button" 
                                                            class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                                            style="width: 32px; height: 32px;" 
                                                            data-bs-toggle="dropdown" 
                                                            aria-expanded="false" 
                                                            title="Pilih Berkas (<?= $filesCount ?> Berkas)">
                                                        <i class="fas fa-folder-open text-primary" style="font-size: 0.82rem;"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1 rounded-3" style="min-width: 230px; font-size: 0.78rem;">
                                                        <?php foreach ($files as $fItem): 
                                                            $fExt = strtolower($fItem['file_extension'] ?? pathinfo($fItem['file_path'] ?? '', PATHINFO_EXTENSION));
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
                                                $singleFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'narasi' => ''];
                                                $sfExt = strtolower($singleFile['file_extension'] ?? pathinfo($singleFile['file_path'] ?? '', PATHINFO_EXTENSION));
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
                                                       title="Unduh Berkas <?= strtoupper($sfExt ?: 'FILE') ?>">
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
                                                $singleLink = $gdriveLinks[0];
                                            ?>
                                                <a href="<?= htmlspecialchars($singleLink['url']) ?>" 
                                                   target="_blank" 
                                                   class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                                   style="width: 32px; height: 32px;" 
                                                   title="<?= htmlspecialchars($singleLink['narasi'] ?: 'Buka Tautan Google Drive') ?>">
                                                    <i class="fab fa-google-drive text-success" style="font-size: 0.85rem;"></i>
                                                </a>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <button type="button" 
                                                    class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs disabled opacity-25" 
                                                    style="width: 32px; height: 32px;" 
                                                    disabled 
                                                    title="Tidak ada berkas">
                                                <i class="fas fa-minus text-muted" style="font-size: 0.75rem;"></i>
                                            </button>
                                        <?php endif; ?>

                                        <!-- Slot 2: Ubah / Perbaiki Dokumen -->
                                        <a href="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>" 
                                           class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                                           style="width: 32px; height: 32px;" 
                                           title="Ubah / Perbaiki Dokumen">
                                            <i class="fas fa-pen text-warning" style="font-size: 0.8rem;"></i>
                                        </a>

                                        <!-- Slot 3: Hapus Dokumen -->
                                        <form action="<?= base_url('prodi/dokumen/delete/' . $doc['id']) ?>" method="POST" class="d-inline m-0 p-0">
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

<!-- Modal Detail Catatan LPM untuk Admin Prodi -->
<div class="modal fade" id="prodiViewNoteModal" tabindex="-1" aria-labelledby="prodiViewNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white py-3 px-4">
                <h5 class="modal-title fw-bold fs-6" id="prodiViewNoteModalLabel">
                    <i class="fas fa-triangle-exclamation me-2"></i> Catatan Perbaikan Dokumen dari LPM
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
                    <label class="form-label fw-semibold small text-danger mb-1">Poin-Poin Perbaikan yang Diminta Admin LPM:</label>
                    <div class="p-3 rounded-3 bg-white border border-danger border-opacity-25 text-dark" style="font-size: 0.85rem; line-height: 1.5; white-space: pre-wrap;" id="prodiNoteContent">-</div>
                </div>
                <div class="small text-muted d-flex align-items-center gap-2" id="prodiNoteMeta">
                    <i class="fas fa-user-shield text-primary"></i> <span id="prodiNoteReviewer">-</span> &bull; <span id="prodiNoteDate">-</span>
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
        if (typeof window.setProdiPpeppReviewStatusFilter === 'function') {
            window.setProdiPpeppReviewStatusFilter(status);
        }
    } else {
        if (btn) {
            btn.classList.remove('btn-danger', 'text-white');
            btn.classList.add('btn-outline-danger');
        }
        if (txt) txt.textContent = 'Filter Tabel: Dokumen Revisi Saja';
        if (typeof window.setProdiPpeppReviewStatusFilter === 'function') {
            window.setProdiPpeppReviewStatusFilter('');
        }
    }
}

// Collapse icon toggle
document.addEventListener('DOMContentLoaded', function() {
    const hubCollapse = document.getElementById('collapseProdiRevisiHub');
    const iconCollapse = document.getElementById('iconCollapseHub');
    if (hubCollapse && iconCollapse) {
        hubCollapse.addEventListener('hidden.bs.collapse', function() {
            iconCollapse.className = 'fas fa-chevron-down';
        });
        hubCollapse.addEventListener('shown.bs.collapse', function() {
            iconCollapse.className = 'fas fa-chevron-up';
        });
    }
});

function selectProdiSiklusTab(btn, siklus) {
    const tabButtons = document.querySelectorAll('#prodiPpeppTabs .nav-link');
    tabButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (typeof window.setProdiPpeppCycleFilter === 'function') {
        window.setProdiPpeppCycleFilter(siklus);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const prodiNoteModal = document.getElementById('prodiViewNoteModal');
    if (prodiNoteModal) {
        prodiNoteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const title = button.getAttribute('data-title') || '-';
            const notes = button.getAttribute('data-notes') || 'Tidak ada catatan perbaikan khusus.';
            const reviewer = button.getAttribute('data-reviewer') || 'Pusat Penjaminan Mutu LPM';
            const date = button.getAttribute('data-date') || '-';
            const status = button.getAttribute('data-status') || '';
            const editUrl = button.getAttribute('data-edit-url') || '#';

            document.getElementById('prodiNoteDocTitle').textContent = title;
            document.getElementById('prodiNoteContent').textContent = notes;
            document.getElementById('prodiNoteReviewer').textContent = reviewer;
            document.getElementById('prodiNoteDate').textContent = date;
            document.getElementById('prodiNoteEditBtn').setAttribute('href', editUrl);

            const badgeEl = document.getElementById('prodiNoteStatusBadge');
            const editBtn = document.getElementById('prodiNoteEditBtn');
            if (status === 'perlu_perbaikan') {
                badgeEl.innerHTML = '<span class="badge rounded-pill" style="background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; font-size:0.75rem;"><i class="fas fa-triangle-exclamation text-danger me-1"></i> Perlu Perbaikan / Revisi</span>';
                if (editBtn) editBtn.style.display = 'inline-flex';
            } else if (status === 'sudah_diperbaiki') {
                badgeEl.innerHTML = '<span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E; border:1px solid #FCD34D; font-size:0.75rem;"><i class="fas fa-clock text-warning me-1"></i> Sudah Diperbaiki (Menunggu Verifikasi LPM)</span>';
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
