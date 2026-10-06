<?php
/**
 * Super Admin / LPM: Ruang Kerja Review Dokumen Mutu Program Studi
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark-blue text-warning px-3 py-1"><?= htmlspecialchars($prodi['jenjang']) ?> &bull; <?= htmlspecialchars($prodi['kode_prodi']) ?></span>
                <span class="badge bg-light text-secondary border"><?= htmlspecialchars($prodi['nama_fakultas']) ?></span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Review Dokumen Mutu: <?= htmlspecialchars($prodi['nama_prodi']) ?></h3>
            <p class="text-muted small mb-0">
                Ketua Program Studi: <strong><?= htmlspecialchars($prodi['nama_kaprodi'] ?: 'Belum diisi') ?></strong> <?= $prodi['nidn_kaprodi'] ? '(NIDN: '.htmlspecialchars($prodi['nidn_kaprodi']).')' : '' ?>
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
                <div class="small text-danger" style="font-size: 0.72rem;">Catatan revisi dikirim ke Prodi</div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left: 4px solid #EF4444 !important;">
                <div class="small text-muted fw-bold mb-1">Perlu Review Ulang</div>
                <div class="fs-4 fw-bold text-dark-blue"><?= $reviewStats['sudah_diperbaiki'] ?></div>
                <div class="small text-danger" style="font-size: 0.72rem;">Sudah diperbaiki oleh Prodi</div>
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

        <!-- Filter Toolbar -->
        <div class="row g-3 align-items-center mb-4 pb-3 border-bottom">
            <div class="col-lg-6 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="reviewSearchInput" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama dokumen, nomor SK, pengunggah..." onkeyup="filterReviewTable()">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <select id="reviewStatusFilter" class="form-select bg-light" onchange="filterReviewTable()">
                    <option value="">-- Semua Status Review --</option>
                    <option value="belum_direview">⏳ Belum Direview</option>
                    <option value="perlu_perbaikan">⚠️ Perlu Perbaikan (Revisi)</option>
                    <option value="sudah_diperbaiki">🔄 Perlu Review Ulang</option>
                    <option value="sesuai">✅ Sesuai Standar</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-12 text-lg-end">
                <span class="text-muted small" id="docCountLabel">Total <?= count($documents) ?> Dokumen</span>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="reviewDocTable" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Nama & Nomor Dokumen</th>
                        <th>Bidang</th>
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
                                <h6 class="fw-bold">Belum Ada Dokumen Mutu</h6>
                                <p class="small mb-0">Admin Program Studi <?= htmlspecialchars($prodi['nama_prodi']) ?> belum mengunggah dokumen mutu.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($documents as $doc): 
                            $status = $doc['status_review'] ?? 'belum_direview';
                            $hasNotes = !empty($doc['catatan_review']);
                        ?>
                            <tr class="review-row"
                                data-title="<?= htmlspecialchars(strtolower($doc['nama_dokumen'])) ?>"
                                data-nomor="<?= htmlspecialchars(strtolower($doc['nomor_dokumen'] ?? '')) ?>"
                                data-uploader="<?= htmlspecialchars(strtolower($doc['uploader_name'] ?? '')) ?>"
                                data-siklus="<?= $doc['siklus'] ?>"
                                data-status="<?= $status ?>">
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($doc['nama_dokumen']) ?></div>
                                    <div class="d-flex flex-wrap align-items-center gap-1.5">
                                        <?php if ($doc['nomor_dokumen']): ?>
                                            <span class="text-muted small" style="font-size: 0.72rem;"><i class="fas fa-hashtag me-0.5"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($doc['tahun_akademik']): ?>
                                            <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">TA <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        Diunggah oleh: <strong><?= htmlspecialchars($doc['uploader_name'] ?: 'Admin Prodi') ?></strong> pada <?= date('d M Y H:i', strtotime($doc['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark mb-0.5">
                                        <?= htmlspecialchars($doc['nama_bidang'] ?: 'Standar Umum') ?>
                                    </div>
                                    <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                        <div class="small text-muted d-flex align-items-center gap-1" style="font-size: 0.7rem;">
                                            <i class="fas fa-turn-up fa-rotate-90 text-secondary opacity-50"></i>
                                            <span class="badge bg-light text-secondary border px-1.5 py-0.5"><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <?php 
                                    $files = $doc['files'] ?? [];
                                    $filesCount = count($files);
                                    $gdriveLinks = parse_external_links($doc['external_link'] ?? '');
                                    $linkCount = count($gdriveLinks);

                                    if ($doc['jenis_upload'] === 'file'): 
                                    ?>
                                        <?php if ($filesCount > 1): ?>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-outline-danger dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                                        style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                                    <i class="fas fa-folder-open text-danger"></i> <?= $filesCount ?> Berkas
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 320px; max-width: 440px; width: max-content; font-size: 0.78rem;">
                                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS DOKUMEN:</li>
                                                    <?php foreach ($files as $fItem): ?>
                                                        <li class="mb-1.5">
                                                            <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                    data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                                    data-pdf-url="<?= base_url($fItem['file_path']) ?>"
                                                                    data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                                    data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                                    data-public-limit="0"
                                                                    data-can-download="1">
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
                                            <?php $singleFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'narasi' => '']; ?>
                                            <div class="d-inline-flex flex-column align-items-start gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs"
                                                        data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                        data-pdf-url="<?= base_url($singleFile['file_path']) ?>"
                                                        data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                                        data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                                        data-public-limit="0"
                                                        data-can-download="1"
                                                        style="font-size: 0.72rem; height: 26px; line-height: 1;">
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
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                                    style="font-size: 0.72rem; height: 26px; line-height: 1;">
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
                                            <a href="<?= htmlspecialchars($singleLink['url']) ?>" target="_blank" 
                                               class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 text-decoration-none shadow-2xs" 
                                               style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                                <i class="fab fa-google-drive"></i> GDrive
                                            </a>
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
                                                <i class="fas fa-triangle-exclamation text-danger"></i> Perlu Revisi Prodi
                                            </span>
                                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 btn-view-note shadow-2xs" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewLpmNoteModal"
                                                    data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                    data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                                    data-reviewer="<?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM', ENT_QUOTES) ?>"
                                                    data-date="<?= !empty($doc['reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['reviewed_at'])) : '' ?>"
                                                    data-status="perlu_perbaikan"
                                                    style="font-size: 0.7rem;">
                                                <i class="fas fa-comment-dots"></i> Lihat Catatan LPM
                                            </button>
                                        <?php elseif ($status === 'sudah_diperbaiki'): ?>
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                                <i class="fas fa-clock text-warning"></i> Sudah Diperbaiki (Review Ulang)
                                            </span>
                                            <?php if (!empty($doc['catatan_review'])): ?>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 btn-view-note shadow-2xs" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewLpmNoteModal"
                                                        data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                        data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                                        data-reviewer="<?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM', ENT_QUOTES) ?>"
                                                        data-date="<?= !empty($doc['updated_at']) ? date('d/m/Y H:i', strtotime($doc['updated_at'])) : '' ?>"
                                                        data-status="sudah_diperbaiki"
                                                        style="font-size: 0.7rem;">
                                                    <i class="fas fa-history"></i> Catatan Sebelumnya
                                                </button>
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
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-1.5 align-items-center">
                                        <!-- Tombol Setujui / Sesuai -->
                                        <form action="<?= base_url('admin/review-dokumen/submit') ?>" method="POST" class="w-100" onsubmit="return confirm('Apakah Anda yakin menyetujui dokumen ini telah sesuai standar mutu?');">
                                            <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                            <input type="hidden" name="status_review" value="sesuai">
                                            <input type="hidden" name="catatan_review" value="Dokumen telah diverifikasi dan disetujui sesuai standar mutu.">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 w-100 fw-bold shadow-sm" style="font-size: 0.75rem;">
                                                <i class="fas fa-circle-check me-1"></i> Setujui
                                            </button>
                                        </form>

                                        <!-- Tombol Minta Perbaikan (Revisi) -->
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 w-100 fw-bold btn-open-revision" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#revisionModal"
                                                data-id="<?= $doc['id'] ?>"
                                                data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                                style="font-size: 0.75rem;">
                                            <i class="fas fa-pen-to-square me-1"></i> Minta Revisi
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size: 0.68rem;">Status: Perlu Revisi</span>
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
                                  placeholder="Tuliskan catatan perbaikan secara spesifik... Contoh: Nomor SK pengesahan belum tercantum, harap lengkapi tanda tangan Dekan pada lembar akhir." required></textarea>
                        <div class="d-flex align-items-center gap-2 mt-2.5 p-2.5 rounded-2 bg-light border text-muted" style="font-size: 0.74rem;">
                            <i class="fas fa-circle-info text-primary flex-shrink-0"></i>
                            <span>Catatan ini akan otomatis muncul sebagai instruksi revisi di dashboard Admin Prodi saat memperbarui berkas.</span>
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
function filterReviewTable() {
    const searchVal = document.getElementById('reviewSearchInput').value.toLowerCase().trim();
    const statusVal = document.getElementById('reviewStatusFilter').value;
    const rows = document.querySelectorAll('.review-row');

    let visibleCount = 0;
    rows.forEach(row => {
        const title = row.getAttribute('data-title') || '';
        const nomor = row.getAttribute('data-nomor') || '';
        const uploader = row.getAttribute('data-uploader') || '';
        const siklus = row.getAttribute('data-siklus') || '';
        const status = row.getAttribute('data-status') || '';

        const matchSearch = (!searchVal || title.includes(searchVal) || nomor.includes(searchVal) || uploader.includes(searchVal));
        const matchSiklus = (!currentReviewSiklus || siklus === currentReviewSiklus);
        const matchStatus = (!statusVal || status === statusVal);

        if (matchSearch && matchSiklus && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const countLabel = document.getElementById('docCountLabel');
    if (countLabel) countLabel.textContent = `Menampilkan ${visibleCount} dari ${rows.length} Dokumen`;
}

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
            document.getElementById('revCatatan').value = docNotes || '';
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
