<?php
/**
 * views/super_admin/review_prodi_rows.php
 * Partial rendering rows dokumen per prodi untuk LPM (per 50 dokumen).
 */

if (!isset($no)) {
    $no = isset($startNum) && $startNum > 0 ? $startNum : 1;
}

if (!empty($documents)):
    foreach ($documents as $doc): 
        $status = $doc['status_review'] ?? 'belum_direview';
        $hasNotes = !empty($doc['catatan_review']);
    ?>
        <tr class="review-row"
            data-title="<?= htmlspecialchars(strtolower($doc['nama_dokumen'])) ?>"
            data-nomor="<?= htmlspecialchars(strtolower($doc['nomor_dokumen'] ?? '')) ?>"
            data-uploader="<?= htmlspecialchars(strtolower($doc['uploader_name'] ?? '')) ?>"
            data-siklus="<?= htmlspecialchars(strtolower(trim($doc['siklus'] ?? ''))) ?>"
            data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
            data-status="<?= $status ?>">
            <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
            <td>
                <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($doc['nama_dokumen']) ?></div>
                <div class="d-flex flex-wrap align-items-center gap-1.5">
                    <?php if ($doc['nomor_dokumen']): ?>
                        <span class="text-muted small" style="font-size: 0.72rem;"><i class="fas fa-hashtag me-0.5"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                    <?php endif; ?>
                    <?php if ($doc['tahun_akademik']): ?>
                        <span class="badge bg-light text-secondary border" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;"><i class="far fa-calendar-alt text-primary me-1"></i>TA <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
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
                $hasFiles = ($filesCount > 0 || (!empty($doc['file_path']) && $doc['jenis_upload'] !== 'link'));
                $hasLinks = ($linkCount > 0);
                $isKombinasi = ($doc['jenis_upload'] === 'kombinasi') || ($hasFiles && $hasLinks);
                ?>
                <div class="d-flex flex-column gap-1.5">
                    <?php if ($hasFiles): ?>
                        <?php if ($filesCount > 1): ?>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-outline-danger dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                        style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                    <i class="fas fa-folder-open text-danger"></i> <?= $isKombinasi ? 'File (' . $filesCount . ')' : $filesCount . ' Berkas' ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 320px; max-width: 440px; width: max-content; font-size: 0.78rem;">
                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">PILIH BERKAS DOKUMEN:</li>
                                    <?php foreach ($files as $fItem): ?>
                                        <li class="mb-1.5">
                                            <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                    data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                    data-pdf-url="<?= base_url($fItem['file_path']) ?>"
                                                    data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                    data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                    data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                                    data-public-limit="0"
                                                    data-can-download="1">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                    <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate" title="<?= htmlspecialchars($fItem['file_name']) ?>">
                                                        <i class="<?= ($fItem['file_category'] ?? '') === 'photo' ? 'fas fa-image text-info' : 'fas fa-file-pdf text-danger' ?> flex-shrink-0"></i>
                                                        <span class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                    </span>
                                                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                        <?php if (!empty($fItem['folder_name'])): ?>
                                                            <span class="badge rounded-pill shadow-2xs" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;" title="Folder: <?= htmlspecialchars($fItem['folder_name']) ?>">
                                                                <i class="fas fa-folder me-0.5"></i><?= htmlspecialchars($fItem['folder_name']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <span class="badge bg-light text-muted border" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;"><?= htmlspecialchars($fItem['file_size'] ?: 'PDF') ?></span>
                                                    </div>
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
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold"
                                            data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                            data-pdf-url="<?= base_url($singleFile['file_path']) ?>"
                                            data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                            data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                            data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                            data-public-limit="0"
                                            data-can-download="1"
                                            style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <?php if (!empty($singleFile['folder_name'])): ?>
                                        <span class="badge rounded-pill shadow-2xs" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">
                                            <i class="fas fa-folder me-0.5"></i><?= htmlspecialchars($singleFile['folder_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?= render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null) ?>
                                <?php if (!empty($singleFile['narasi'])): ?>
                                    <div class="text-muted fst-italic text-truncate" style="max-width: 180px; font-size: 0.68rem;" title="<?= htmlspecialchars($singleFile['narasi']) ?>">
                                        <i class="fas fa-comment-dots text-danger me-0.5"></i> <?= htmlspecialchars($singleFile['narasi']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($hasLinks): ?>
                        <?php if ($linkCount > 1): ?>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                        style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                    <i class="fab fa-google-drive text-primary"></i> <?= $isKombinasi ? 'Drive (' . $linkCount . ')' : $linkCount . ' Link GDrive' ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 320px; max-width: 440px; width: max-content; font-size: 0.78rem;">
                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">PILIH TAUTAN GDRIVE:</li>
                                    <?php foreach ($gdriveLinks as $lIdx => $linkObj): 
                                        $gUrl = $linkObj['url'];
                                        $gNarasi = $linkObj['narasi'];
                                    ?>
                                        <li class="mb-1.5">
                                            <a href="<?= htmlspecialchars($gUrl) ?>" target="_blank" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                    <span class="d-flex align-items-center gap-1.5 fw-semibold text-primary text-truncate">
                                                        <i class="fab fa-google-drive"></i>
                                                        <span><?= htmlspecialchars($linkObj['label'] ?: 'Tautan GDrive #' . ($lIdx + 1)) ?></span>
                                                    </span>
                                                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                        <?php if (!empty($linkObj['folder_name'])): ?>
                                                            <span class="badge rounded-pill shadow-2xs" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">
                                                                <i class="fas fa-folder me-0.5"></i><?= htmlspecialchars($linkObj['folder_name']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <i class="fas fa-arrow-up-right-from-square text-muted" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;"></i>
                                                    </div>
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
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <a href="<?= htmlspecialchars($singleLink['url']) ?>" target="_blank" 
                                       class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 text-decoration-none shadow-2xs" 
                                       style="font-size: 0.72rem; height: 26px; line-height: 1;">
                                        <i class="fab fa-google-drive"></i> GDrive
                                    </a>
                                    <?php if (!empty($singleLink['folder_name'])): ?>
                                        <span class="badge rounded-pill shadow-2xs" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">
                                            <i class="fas fa-folder me-0.5"></i><?= htmlspecialchars($singleLink['folder_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?= render_sub_standar_badges($singleLink['sub_bidang_ids'] ?? null) ?>
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
            <td>
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-1.5">
                        <?= render_review_status_badge($status, $doc['tahap_review'] ?? null) ?>
                    </div>
                    <?php if ($hasNotes): ?>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5 text-start d-inline-flex align-items-center gap-1 text-truncate w-auto" 
                                style="max-width: 250px; font-size: 0.72rem;"
                                data-bs-toggle="modal" 
                                data-bs-target="#viewLpmNoteModal"
                                data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                data-reviewer="<?= htmlspecialchars($doc['reviewer_name'] ?? 'Admin LPM', ENT_QUOTES) ?>"
                                data-date="<?= !empty($doc['reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['reviewed_at'])) : '' ?>"
                                data-status="<?= htmlspecialchars($status) ?>">
                            <i class="fas fa-comment-dots text-primary"></i>
                            <span class="text-truncate"><?= htmlspecialchars($doc['catatan_review']) ?></span>
                        </button>
                    <?php endif; ?>
                </div>
            </td>
            <td class="text-center">
                <div class="d-flex flex-column gap-1.5 align-items-center justify-content-center">
                    <div class="btn-group w-100 shadow-2xs rounded-pill overflow-hidden" role="group">
                        <form action="<?= base_url('admin/review-dokumen/submit') ?>" method="POST" class="d-inline flex-fill m-0">
                            <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                            <input type="hidden" name="status_review" value="sesuai">
                            <button type="submit" class="btn btn-sm btn-success w-100 py-1 px-2 d-flex align-items-center justify-content-center gap-1" style="font-size: 0.75rem; border-top-right-radius: 0; border-bottom-right-radius: 0;" title="Setujui Dokumen (Sesuai)">
                                <i class="fas fa-check"></i> Sesuai
                            </button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 d-flex align-items-center justify-content-center gap-1 flex-fill" style="font-size: 0.75rem; border-top-left-radius: 0; border-bottom-left-radius: 0;"
                                data-bs-toggle="modal" 
                                data-bs-target="#revisionModal" 
                                data-id="<?= $doc['id'] ?>" 
                                data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                data-notes="<?= htmlspecialchars($doc['catatan_review'] ?? '', ENT_QUOTES) ?>"
                                title="Minta Revisi Dokumen">
                            <i class="fas fa-pen-to-square"></i> Revisi
                        </button>
                    </div>

                    <!-- Tombol Lihat Rincian -->
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 w-100 fw-semibold shadow-2xs d-flex align-items-center justify-content-center gap-1 text-primary" 
                            style="font-size: 0.75rem;" 
                            data-bs-toggle="modal" 
                            data-bs-target="#docModal<?= $doc['id'] ?>" 
                            title="Lihat Rincian Dokumen">
                        <i class="fas fa-eye"></i> Rincian
                    </button>

                    <!-- Tombol Riwayat Review -->
                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 w-100 btn-review-history d-inline-flex align-items-center justify-content-center gap-1 shadow-2xs" 
                            data-doc-id="<?= $doc['id'] ?>" 
                            data-doc-name="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                            title="Lihat Jejak Audit & Riwayat Review" 
                            style="font-size: 0.72rem;">
                        <i class="fas fa-history text-secondary"></i> Riwayat
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
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;">
                                        <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($doc['nama_prodi'] ?? ($prodi['nama_prodi'] ?? 'Program Studi')) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <span class="small text-muted d-block mb-1">Siklus PPEPP & Status:</span>
                                <div class="d-flex align-items-center gap-2">
                                    <?= siklus_badge($doc['siklus']) ?>
                                    <?= render_review_status_badge($status, $doc['tahap_review'] ?? null) ?>
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

                        <!-- Berkas Lampiran -->
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
                                                        <div class="text-muted d-flex align-items-center gap-2 flex-wrap small" style="font-size: 0.76rem;">
                                                            <span><i class="fas fa-database me-1 text-secondary opacity-75"></i><?= htmlspecialchars($f['file_size'] ?: 'PDF') ?></span>
                                                            &bull;
                                                            <span><i class="far fa-clock me-1 text-secondary opacity-75"></i><?= date('d M Y, H:i', strtotime($f['created_at'] ?? $doc['created_at'])) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 flex-shrink-0 align-self-sm-center align-self-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#pdfPreviewModal" 
                                                            data-pdf-url="<?= base_url($f['file_path']) ?>" 
                                                            data-doc-title="<?= htmlspecialchars($f['file_name']) ?>" 
                                                            data-doc-narasi="<?= htmlspecialchars($f['narasi'] ?? '') ?>" 
                                                            data-doc-standar="<?= htmlspecialchars($subBadgesHtml) ?>" 
                                                            data-public-limit="0" 
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
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Google Drive -->
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
                                                    <a href="<?= htmlspecialchars($l['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.78rem;">
                                                        <i class="fas fa-arrow-up-right-from-square"></i> Buka Link
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

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

