<?php
/**
 * views/super_admin/review_hub_rows.php
 * Partial rendering rows & modals dokumen untuk Review Hub (per 50 dokumen).
 * Mencegah browser membeku (freeze/crash) akibat jutaan DOM nodes.
 */

if (!isset($rowNo)) {
    $rowNo = isset($startNum) && $startNum > 0 ? $startNum : 1;
}

if (!empty($documents)):
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
            data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
            data-title="<?= htmlspecialchars(strtolower($doc['nama_dokumen'])) ?>"
            data-nomor="<?= htmlspecialchars(strtolower($doc['nomor_dokumen'] ?? '')) ?>"
            data-uploader="<?= htmlspecialchars(strtolower($doc['uploader_name'] ?? '')) ?>"
            data-unit="<?= htmlspecialchars(strtolower(($doc['nama_prodi'] ?? '') . ' ' . ($doc['nama_fakultas'] ?? ''))) ?>">
            
            <!-- No -->
            <td class="text-center text-muted fw-semibold ps-3 row-number"><?= $rowNo++ ?></td>

            <!-- Dokumen Mutu & Unit Pengunggah -->
            <td>
                <div class="fw-bold text-dark-blue mb-1 line-clamp-1" title="<?= htmlspecialchars($doc['nama_dokumen']) ?>" style="font-size: 0.84rem;">
                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-1.5 mb-1">
                    <?php if ($isFakDoc): ?>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.68rem; font-weight: 700;">
                            <i class="fas fa-landmark me-1"></i> <?= htmlspecialchars($doc['nama_fakultas'] ?? 'Fakultas') ?>
                        </span>
                    <?php else: ?>
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.68rem; font-weight: 700;">
                            <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars($doc['nama_prodi'] ?? 'Prodi') ?> (<?= htmlspecialchars($doc['jenjang'] ?? '') ?>)
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($doc['nomor_dokumen'])): ?>
                        <span class="text-muted" style="font-size: 0.7rem;">
                            <i class="fas fa-hashtag me-0.5"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($doc['tahun_akademik'])): ?>
                        <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;"><i class="far fa-calendar-alt text-primary me-1"></i>TA <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="text-muted" style="font-size: 0.68rem;">
                    <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($doc['uploader_name'] ?: 'Unit Kerja') ?>
                    &bull; <?= date('d M Y H:i', strtotime($doc['created_at'])) ?>
                </div>
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
                <div class="fw-semibold text-dark mb-0.5 line-clamp-1" title="<?= htmlspecialchars($doc['nama_bidang'] ?: 'Standar Umum') ?>" style="font-size: 0.78rem;">
                    <?= htmlspecialchars($doc['nama_bidang'] ?: 'Standar Umum') ?>
                </div>
                <?php if (!empty($doc['nama_sub_bidang'])): ?>
                    <div class="text-muted line-clamp-1" style="font-size: 0.68rem;" title="<?= htmlspecialchars($doc['nama_sub_bidang']) ?>">
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
                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                    <?php if ($hasFiles): ?>
                        <?php if ($filesCount > 1): ?>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-xs btn-outline-danger dropdown-toggle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                        style="font-size: 0.7rem; height: 24px; line-height: 1;">
                                    <i class="fas fa-folder-open text-danger"></i> <?= $isKombinasi ? 'File(' . $filesCount . ')' : $filesCount . ' File' ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 280px; max-width: 360px; font-size: 0.78rem;">
                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS DOKUMEN:</li>
                                    <?php foreach ($files as $fItem): ?>
                                        <li class="mb-1">
                                            <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                    data-bs-toggle="modal" data-bs-target="#hubPdfPreviewModal"
                                                    data-pdf-url="<?= base_url($fItem['file_path']) ?>"
                                                    data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                    data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                    data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>">
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate">
                                                        <i class="fas fa-file-pdf text-danger flex-shrink-0"></i>
                                                        <span class="text-truncate" style="max-width: 180px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
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
                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold"
                                    data-bs-toggle="modal" data-bs-target="#hubPdfPreviewModal"
                                    data-pdf-url="<?= base_url($singleFile['file_path']) ?>"
                                    data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                    data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                    data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                    style="font-size: 0.7rem; height: 24px; line-height: 1;"
                                    title="Buka Berkas PDF">
                                <i class="fas fa-file-pdf text-danger"></i> PDF
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($hasLinks): ?>
                        <?php if ($linkCount > 1): ?>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-xs btn-outline-primary dropdown-toggle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                        style="font-size: 0.7rem; height: 24px; line-height: 1;">
                                    <i class="fab fa-google-drive text-primary"></i> <?= $isKombinasi ? 'Drive(' . $linkCount . ')' : $linkCount . ' Link' ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 250px; font-size: 0.78rem;">
                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">TAUTAN DOKUMEN:</li>
                                    <?php foreach ($gdriveLinks as $idx => $gLink): ?>
                                        <li>
                                            <a class="dropdown-item p-2 rounded-2 text-primary d-flex flex-column gap-1" href="<?= htmlspecialchars($gLink['url']) ?>" target="_blank" rel="noopener noreferrer">
                                                <div class="d-flex align-items-center justify-content-between w-100">
                                                    <span><i class="fab fa-google-drive me-1.5 text-primary"></i> Link <?= $idx + 1 ?></span>
                                                    <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 0.7rem;"></i>
                                                </div>
                                                <?= render_sub_standar_badges($gLink['sub_bidang_ids'] ?? null) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <?php $singleLink = $gdriveLinks[0]; ?>
                            <a href="<?= htmlspecialchars($singleLink['url']) ?>" target="_blank" rel="noopener noreferrer" 
                               class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold"
                               style="font-size: 0.7rem; height: 24px; line-height: 1;"
                               title="Buka Tautan Google Drive">
                                <i class="fab fa-google-drive text-primary"></i> Drive
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!$hasFiles && !$hasLinks): ?>
                        <span class="text-muted small" style="font-size: 0.68rem;">-</span>
                    <?php endif; ?>
                </div>
            </td>

            <!-- Status Review LPM -->
            <td>
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-1.5">
                        <?= render_review_status_badge($st, $doc['tahap_review'] ?? null) ?>
                    </div>

                    <!-- Indikator Verifikasi GPM (Tier-1) -->
                    <?php if (!empty($doc['gpm_reviewed_at']) || !empty($doc['gpm_catatan'])): ?>
                        <div class="d-flex align-items-center gap-1 mt-0.5">
                            <span class="badge bg-warning bg-opacity-25 text-warning-emphasis border border-warning border-opacity-25 px-1.5 py-0.5" style="font-size: 0.65rem; font-weight: 600;">
                                <i class="fas fa-user-check me-0.5"></i> Lolos GPM
                            </span>
                            <?php if (!empty($doc['gpm_catatan'])): ?>
                                <button type="button" class="btn btn-xs btn-link text-warning-emphasis p-0 text-decoration-none fw-semibold" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewGpmNoteModal"
                                        data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                        data-notes="<?= htmlspecialchars($doc['gpm_catatan'], ENT_QUOTES) ?>"
                                        data-reviewer="<?= htmlspecialchars($doc['gpm_reviewer_name'] ?? 'GPM Fakultas', ENT_QUOTES) ?>"
                                        data-date="<?= !empty($doc['gpm_reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['gpm_reviewed_at'])) : '' ?>"
                                        style="font-size: 0.66rem;">
                                    <i class="fas fa-comment-dots"></i> Catatan GPM
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Catatan Evaluasi LPM jika ada -->
                    <?php if (!empty($doc['catatan_review'])): ?>
                        <div class="d-flex align-items-center gap-1 mt-0.5">
                            <button type="button" class="btn btn-xs btn-link text-primary p-0 text-decoration-none fw-semibold" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewLpmNoteModal"
                                    data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                    data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                    data-reviewer="<?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM', ENT_QUOTES) ?>"
                                    data-date="<?= !empty($doc['reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['reviewed_at'])) : '' ?>"
                                    data-status="<?= htmlspecialchars($st) ?>"
                                    style="font-size: 0.66rem;">
                                <i class="fas fa-comment-dots text-primary"></i> Catatan LPM
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </td>

            <!-- Aksi Cepat (Quick Review Icon Group) -->
            <td class="text-center pe-3">
                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                    <!-- Tombol Setujui Langsung -->
                    <button type="button" 
                            class="btn btn-sm btn-success rounded-circle shadow-2xs d-inline-flex align-items-center justify-content-center" 
                            style="width: 28px; height: 28px; padding: 0;"
                            data-bs-toggle="modal"
                            data-bs-target="#quickApproveModal"
                            data-id="<?= $doc['id'] ?>"
                            data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                            data-unit="<?= htmlspecialchars($isFakDoc ? ($doc['nama_fakultas'] ?? '') : ($doc['nama_prodi'] ?? ''), ENT_QUOTES) ?>"
                            data-gpm-notes="<?= htmlspecialchars($doc['gpm_catatan'] ?? '', ENT_QUOTES) ?>"
                            data-gpm-reviewer="<?= htmlspecialchars($doc['gpm_reviewer_name'] ?? 'GPM Fakultas', ENT_QUOTES) ?>"
                            data-gpm-date="<?= !empty($doc['gpm_reviewed_at']) ? date('d M Y, H:i', strtotime($doc['gpm_reviewed_at'])) : '' ?>"
                            title="Setujui Dokumen (Disetujui)">
                        <i class="fas fa-check" style="font-size: 0.72rem;"></i>
                    </button>

                    <!-- Tombol Minta Revisi -->
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger rounded-circle shadow-2xs d-inline-flex align-items-center justify-content-center" 
                            style="width: 28px; height: 28px; padding: 0;"
                            data-bs-toggle="modal" 
                            data-bs-target="#quickRevisionModal"
                            data-id="<?= $doc['id'] ?>"
                            data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                            data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                            data-unit="<?= htmlspecialchars($isFakDoc ? ($doc['nama_fakultas'] ?? '') : ($doc['nama_prodi'] ?? ''), ENT_QUOTES) ?>"
                            data-gpm-notes="<?= htmlspecialchars($doc['gpm_catatan'] ?? '', ENT_QUOTES) ?>"
                            data-gpm-reviewer="<?= htmlspecialchars($doc['gpm_reviewer_name'] ?? 'GPM Fakultas', ENT_QUOTES) ?>"
                            data-gpm-date="<?= !empty($doc['gpm_reviewed_at']) ? date('d M Y, H:i', strtotime($doc['gpm_reviewed_at'])) : '' ?>"
                            title="Minta Revisi Unit">
                        <i class="fas fa-pen-to-square" style="font-size: 0.72rem;"></i>
                    </button>

                    <!-- Tombol Riwayat Revisi -->
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary rounded-circle shadow-2xs d-inline-flex align-items-center justify-content-center" 
                            style="width: 28px; height: 28px; padding: 0;"
                            onclick="openDocReviewHistory(<?= $doc['id'] ?>)"
                            title="Riwayat Catatan Revisi">
                        <i class="fas fa-history" style="font-size: 0.72rem;"></i>
                    </button>

                    <!-- Tombol Rincian -->
                    <button type="button" 
                            class="btn btn-sm btn-light border rounded-circle shadow-2xs d-inline-flex align-items-center justify-content-center text-muted" 
                            style="width: 28px; height: 28px; padding: 0;"
                            data-bs-toggle="modal"
                            data-bs-target="#docModal<?= $doc['id'] ?>"
                            title="Lihat Rincian Dokumen Mutu">
                        <i class="fas fa-eye" style="font-size: 0.72rem;"></i>
                    </button>
                </div>
            </td>
        </tr>

        <!-- Modal Detail Dokumen (Rincian Dokumen Mutu untuk 50 data aktif) -->
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
                                    <?= render_review_status_badge($st, $doc['tahap_review'] ?? null) ?>
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

                        <!-- Catatan Evaluasi GPM (Tier-1) jika ada -->
                        <?php if (!empty($doc['gpm_catatan'])): ?>
                            <div class="p-3 rounded-3 mb-3 border border-warning" style="background: #FFFBEB; border-left: 4px solid #D97706 !important;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <span class="small fw-bold text-uppercase text-warning-emphasis" style="font-size: 0.74rem;">
                                        <i class="fas fa-shield-halved text-warning me-1"></i> Catatan Review GPM (Tier-1):
                                    </span>
                                    <span class="badge bg-warning bg-opacity-25 text-warning-emphasis border border-warning border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                        <i class="fas fa-user-check me-0.5"></i> <?= htmlspecialchars($doc['gpm_reviewer_name'] ?? 'Gugus Penjaminan Mutu') ?>
                                    </span>
                                </div>
                                <div class="small text-dark mb-2" style="line-height: 1.55; white-space: pre-wrap; font-size: 0.85rem;"><?= htmlspecialchars($doc['gpm_catatan']) ?></div>
                                <?php if (!empty($doc['gpm_reviewed_at'])): ?>
                                    <div class="small text-muted pt-1 border-top" style="font-size: 0.72rem; border-color: rgba(0,0,0,0.06) !important;">
                                        <i class="far fa-clock text-secondary me-1"></i>Direview GPM pada <?= date('d M Y, H:i', strtotime($doc['gpm_reviewed_at'])) ?> WIB
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Catatan Evaluasi LPM -->
                        <?php if (!empty($doc['catatan_review'])): ?>
                            <div class="p-3 rounded-3 mb-3 border" style="background: #F8FAFC;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <span class="small fw-bold text-uppercase text-muted" style="font-size: 0.74rem;">
                                        <i class="fas fa-comments text-primary me-1"></i> Catatan Evaluasi LPM:
                                    </span>
                                    <?php if (!empty($doc['reviewed_at'])): ?>
                                        <span class="small text-muted" style="font-size: 0.72rem;">
                                            <i class="far fa-clock me-1"></i><?= date('d M Y, H:i', strtotime($doc['reviewed_at'])) ?> WIB
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-dark" style="line-height: 1.55; white-space: pre-wrap;"><?= htmlspecialchars($doc['catatan_review']) ?></div>
                                <div class="small text-muted mt-2 pt-2 border-top" style="font-size: 0.72rem;">
                                    <i class="fas fa-user-tie me-1"></i>Reviewer LPM: <?= htmlspecialchars($doc['reviewer_name'] ?? 'Pusat Penjaminan Mutu LPM') ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Daftar Berkas Lampiran Dokumen -->
                        <?php if (!empty($files)): ?>
                            <div class="p-3.5 p-md-4 rounded-4 bg-light border mb-4">
                                <div class="d-flex align-items-center justify-content-between pb-2.5 mb-3 border-bottom border-light-subtle">
                                    <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-paperclip text-primary"></i> Berkas Lampiran Dokumen (<?= count($files) ?>):
                                    </span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border px-2.5 py-1 rounded-pill fw-medium" style="font-size: 0.7rem;">Multi-Dokumen Terpadu</span>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($files as $fIdx => $f): 
                                        $subBadgesHtml = render_sub_standar_badges($f['sub_bidang_ids'] ?? null);
                                    ?>
                                        <div class="p-3 rounded-3 bg-white border border-light-subtle shadow-2xs hover-shadow-sm transition-all">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div class="d-flex align-items-center gap-2.5 min-w-0" style="max-width: 70%;">
                                                    <div class="p-2 rounded-2 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                                        <i class="fas fa-file-pdf fa-lg"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="fw-semibold text-dark text-truncate" title="<?= htmlspecialchars($f['file_name']) ?>" style="font-size: 0.85rem;">
                                                            <?= htmlspecialchars($f['file_name']) ?>
                                                        </div>
                                                        <div class="text-muted small d-flex align-items-center gap-2" style="font-size: 0.72rem;">
                                                            <span><i class="fas fa-database me-1 text-secondary"></i><?= htmlspecialchars($f['file_size'] ?: 'PDF') ?></span>
                                                            &bull;
                                                            <span><i class="far fa-clock me-1 text-secondary"></i><?= date('d M Y, H:i', strtotime($f['created_at'] ?? $doc['created_at'])) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#hubPdfPreviewModal" 
                                                            data-pdf-url="<?= base_url($f['file_path']) ?>" 
                                                            data-doc-title="<?= htmlspecialchars($f['file_name']) ?>" 
                                                            data-doc-narasi="<?= htmlspecialchars($f['narasi'] ?? '') ?>" 
                                                            data-doc-standar="<?= htmlspecialchars($subBadgesHtml) ?>" 
                                                            data-can-access="1" 
                                                            data-can-download="1"
                                                            data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
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
                                        <div class="p-3 rounded-3 bg-white border border-light-subtle shadow-2xs hover-shadow-sm transition-all">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div class="d-flex align-items-center gap-2.5 min-w-0" style="max-width: 70%;">
                                                    <div class="p-2 rounded-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                                        <i class="fab fa-google-drive fa-lg"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="fw-semibold text-dark text-truncate" title="<?= htmlspecialchars($l['url']) ?>" style="font-size: 0.85rem;">
                                                            <?= htmlspecialchars($l['narasi'] ?: 'Tautan Google Drive #' . ($lIdx + 1)) ?>
                                                        </div>
                                                        <div class="text-muted small text-truncate" style="font-size: 0.72rem;">
                                                            <i class="fas fa-link me-1 text-secondary"></i><?= htmlspecialchars($l['url']) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold"
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
