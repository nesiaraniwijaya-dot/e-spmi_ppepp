<?php
/**
 * views/super_admin/review_fakultas_rows.php
 * Partial rendering rows dokumen per fakultas untuk LPM (per 50 dokumen).
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
                    Diunggah oleh: <strong><?= htmlspecialchars($doc['uploader_name'] ?: 'Admin Fakultas') ?></strong> pada <?= date('d M Y H:i', strtotime($doc['created_at'])) ?>
                </div>
            </td>
            <td style="min-width: 0; max-width: 100%; overflow: hidden;">
                <div class="fw-semibold text-dark mb-1" style="line-height: 1.35; word-break: break-word;">
                    <?= htmlspecialchars($doc['nama_bidang'] ?: 'Standar Umum') ?>
                </div>
                <?php if (!empty($doc['nama_sub_bidang'])): ?>
                    <div class="d-flex align-items-start gap-1 w-100 mt-1" style="min-width: 0; max-width: 100%;">
                        <i class="fas fa-turn-up fa-rotate-90 text-primary opacity-60 flex-shrink-0 mt-1" style="font-size: 0.65rem;"></i>
                        <span class="badge border px-2 py-1 text-start shadow-2xs d-inline-block w-100" 
                              style="font-size: 0.7rem; font-weight: 500; line-height: 1.35; white-space: normal !important; word-break: break-word; background: #F8FAFC !important; color: #475569 !important; border-color: #E2E8F0 !important; border-radius: 6px;" 
                              title="<?= htmlspecialchars($doc['nama_sub_bidang']) ?>">
                            <?= htmlspecialchars($doc['nama_sub_bidang']) ?>
                        </span>
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
            <td class="text-center align-middle">
                <?php
                $firstFile = !empty($files) ? $files[0] : (!empty($doc['file_path']) ? ['file_name' => $doc['nama_dokumen'], 'file_path' => $doc['file_path'], 'file_size' => $doc['file_size'] ?? '', 'narasi' => '', 'sub_bidang_ids' => null] : null);
                $firstLink = !empty($gdriveLinks) ? $gdriveLinks[0] : null;

                $allFilesData = [];
                if (!empty($files)) {
                    foreach ($files as $fIdx => $f) {
                        $allFilesData[] = [
                            'type' => 'file',
                            'name' => $f['file_name'] ?? 'Berkas ' . ($fIdx + 1),
                            'url' => base_url($f['file_path']),
                            'narasi' => $f['narasi'] ?? '',
                            'standar' => render_sub_standar_badges($f['sub_bidang_ids'] ?? null),
                            'size' => $f['file_size'] ?? ''
                        ];
                    }
                } elseif (!empty($doc['file_path'])) {
                    $allFilesData[] = [
                        'type' => 'file',
                        'name' => $doc['nama_dokumen'],
                        'url' => base_url($doc['file_path']),
                        'narasi' => '',
                        'standar' => '',
                        'size' => $doc['file_size'] ?? ''
                    ];
                }
                if (!empty($gdriveLinks)) {
                    foreach ($gdriveLinks as $lIdx => $l) {
                        $allFilesData[] = [
                            'type' => 'link',
                            'name' => !empty($l['narasi']) ? $l['narasi'] : ('Tautan GDrive ' . ($lIdx + 1)),
                            'url' => $l['url'],
                            'narasi' => $l['narasi'] ?? '',
                            'standar' => render_sub_standar_badges($l['sub_bidang_ids'] ?? null),
                            'size' => ''
                        ];
                    }
                }

                $isFirstLink = !$firstFile && $firstLink;
                $previewPdfUrl = $firstFile ? base_url($firstFile['file_path']) : '';
                $previewLinkUrl = $firstLink ? htmlspecialchars($firstLink['url'], ENT_QUOTES) : '';
                $previewDocTitle = $firstFile ? $firstFile['file_name'] : ($firstLink ? $doc['nama_dokumen'] : $doc['nama_dokumen']);
                $previewNarasi = $firstFile ? ($firstFile['narasi'] ?? '') : ($firstLink ? ($firstLink['narasi'] ?? '') : '');
                $previewStandar = render_sub_standar_badges($firstFile ? ($firstFile['sub_bidang_ids'] ?? null) : ($firstLink ? ($firstLink['sub_bidang_ids'] ?? null) : null));
                ?>
                <div class="d-flex flex-column gap-1.5 align-items-center justify-content-center">
                    <?php if (Auth::canActionLpmMenu('review_dokumen') && $status !== 'sesuai'): ?>
                        <button type="button" 
                                class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3 py-1.5 w-100 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-2xs text-white" 
                                title="Review Dokumen Ini"
                                data-bs-toggle="modal" 
                                data-bs-target="#pdfPreviewModal"
                                data-is-review="1"
                                data-review-role="lpm"
                                data-review-source="#lpmReviewTemplate_<?= $doc['id'] ?>"
                                data-pdf-url="<?= $previewPdfUrl ?>"
                                data-gdrive-url="<?= $previewLinkUrl ?>"
                                data-is-link="<?= $isFirstLink ? '1' : '0' ?>"
                                data-doc-title="<?= htmlspecialchars($previewDocTitle, ENT_QUOTES) ?>"
                                data-doc-narasi="<?= htmlspecialchars($previewNarasi, ENT_QUOTES) ?>"
                                data-doc-standar="<?= htmlspecialchars($previewStandar, ENT_QUOTES) ?>"
                                data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '', ENT_QUOTES) ?>"
                                data-public-limit="0"
                                data-can-download="1"
                                data-all-files="<?= htmlspecialchars(json_encode($allFilesData), ENT_QUOTES, 'UTF-8') ?>"
                                style="font-size: 0.78rem; font-weight: 600;">
                            <i class="fas fa-clipboard-check"></i> Review
                        </button>
                    <?php else: ?>
                        <button type="button" 
                                class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 w-100 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-2xs text-primary" 
                                title="Tinjau Berkas & Hasil Review"
                                data-bs-toggle="modal" 
                                data-bs-target="#pdfPreviewModal"
                                data-is-review="1"
                                data-review-role="lpm"
                                data-review-source="#lpmReviewTemplate_<?= $doc['id'] ?>"
                                data-pdf-url="<?= $previewPdfUrl ?>"
                                data-gdrive-url="<?= $previewLinkUrl ?>"
                                data-is-link="<?= $isFirstLink ? '1' : '0' ?>"
                                data-doc-title="<?= htmlspecialchars($previewDocTitle, ENT_QUOTES) ?>"
                                data-doc-narasi="<?= htmlspecialchars($previewNarasi, ENT_QUOTES) ?>"
                                data-doc-standar="<?= htmlspecialchars($previewStandar, ENT_QUOTES) ?>"
                                data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '', ENT_QUOTES) ?>"
                                data-public-limit="0"
                                data-can-download="1"
                                data-all-files="<?= htmlspecialchars(json_encode($allFilesData), ENT_QUOTES, 'UTF-8') ?>"
                                style="font-size: 0.78rem; font-weight: 600;">
                            <i class="fas fa-clipboard-list"></i> Tinjau
                        </button>
                    <?php endif; ?>

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

        <tr class="d-none"><td colspan="6">
        <!-- TEMPLATE REVIEW LPM UNTUK PDF VIEWER MODAL -->
        <div id="lpmReviewTemplate_<?= $doc['id'] ?>" class="d-none">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-2 text-start">
                <div class="card-header bg-dark text-white px-3.5 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 100%) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold" style="font-size:0.72rem;">LEMBAGA PENJAMINAN MUTU (LPM)</span>
                        <span class="fw-bold small text-white text-truncate" style="max-width: 320px;"><?= htmlspecialchars($doc['nama_dokumen']) ?></span>
                    </div>
                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2.5 py-1 small"><?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?></span>
                </div>
                <div class="card-body p-3.5">
                    <?php
                    $reviewFiles = $doc['files'] ?? [];
                    $reviewLinks = parse_external_links($doc['external_link'] ?? '');
                    $hasReviewNarasi = !empty(trim($doc['deskripsi'] ?? ''));
                    if (!$hasReviewNarasi) {
                        foreach ($reviewFiles as $rf) {
                            if (!empty(trim($rf['narasi'] ?? ''))) { $hasReviewNarasi = true; break; }
                        }
                    }
                    if (!$hasReviewNarasi) {
                        foreach ($reviewLinks as $rl) {
                            if (!empty(trim($rl['narasi'] ?? ''))) { $hasReviewNarasi = true; break; }
                        }
                    }
                    ?>
                    <?php if ($hasReviewNarasi): ?>
                        <div class="card p-3 rounded-3 border-0 mb-3" style="background: #F8FAFC; border: 1px solid #E2E8F0 !important;">
                            <div class="d-flex align-items-center gap-1.5 fw-bold text-dark mb-2" style="font-size: 0.8rem;">
                                <i class="fas fa-align-left text-primary"></i>
                                <span>Narasi &amp; Keterangan Pengunggah:</span>
                            </div>
                            <?php if (!empty(trim($doc['deskripsi'] ?? ''))): ?>
                                <div class="mb-2 p-2.5 rounded-3 bg-white border border-light-subtle text-secondary small" style="line-height: 1.5; font-size: 0.8rem;">
                                    <span class="fw-semibold text-dark d-block mb-0.5"><i class="fas fa-file-lines text-muted me-1"></i>Narasi Dokumen:</span>
                                    <span class="text-dark-blue" style="white-space: pre-wrap; word-break: break-word;"><?= nl2br(htmlspecialchars($doc['deskripsi'])) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php foreach ($reviewFiles as $rf): ?>
                                <?php if (!empty(trim($rf['narasi'] ?? '')) && trim($rf['narasi']) !== trim($doc['deskripsi'] ?? '')): ?>
                                    <div class="mb-1.5 p-2.5 rounded-3 bg-white border border-light-subtle text-secondary small" style="line-height: 1.5; font-size: 0.8rem;">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-0.5 flex-wrap">
                                            <span class="fw-semibold text-dark d-flex align-items-center gap-1.5 text-truncate" style="max-width: 450px;">
                                                <i class="<?= ($rf['file_category'] ?? '') === 'photo' ? 'fas fa-image text-info' : 'fas fa-file-pdf text-danger' ?> flex-shrink-0"></i>
                                                <span class="text-truncate"><?= htmlspecialchars($rf['file_name']) ?></span>
                                            </span>
                                            <?php if (!empty($rf['folder_name'])): ?>
                                                <span class="badge bg-light text-muted border" style="font-size: 0.65rem;"><?= htmlspecialchars($rf['folder_name']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-dark-blue mt-1 ps-2.5 border-start border-2 border-primary" style="white-space: pre-wrap; word-break: break-word;">
                                            <?= nl2br(htmlspecialchars($rf['narasi'])) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php foreach ($reviewLinks as $rlIdx => $rl): ?>
                                <?php if (!empty(trim($rl['narasi'] ?? '')) && trim($rl['narasi']) !== trim($doc['deskripsi'] ?? '')): ?>
                                    <div class="mb-1.5 p-2.5 rounded-3 bg-white border border-light-subtle text-secondary small" style="line-height: 1.5; font-size: 0.8rem;">
                                        <span class="fw-semibold text-dark d-flex align-items-center gap-1.5 mb-0.5">
                                            <i class="fab fa-google-drive text-success flex-shrink-0"></i>
                                            <span><?= htmlspecialchars(!empty($rl['label']) ? $rl['label'] : ('Tautan Google Drive #' . ($rlIdx + 1))) ?></span>
                                        </span>
                                        <div class="text-dark-blue mt-1 ps-2.5 border-start border-2 border-success" style="white-space: pre-wrap; word-break: break-word;">
                                            <?= nl2br(htmlspecialchars($rl['narasi'])) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (Auth::canActionLpmMenu('review_dokumen') && $status !== 'sesuai'): ?>
                        <form action="<?= base_url('admin/review-dokumen/submit') ?>" method="POST" class="m-0 review-action-form" novalidate>
                            <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">

                            <?php if (!empty($doc['catatan_perbaikan_unit'])): ?>
                                <div class="p-2.5 rounded-3 border border-primary-subtle mb-3 small" style="background: #F0F9FF;">
                                    <div class="fw-bold text-primary mb-1 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-circle-check text-primary"></i> Catatan Tindak Lanjut Revisi oleh Unit:
                                    </div>
                                    <div class="text-dark bg-white p-2 rounded-2 border border-primary border-opacity-25" style="line-height: 1.45; white-space: pre-wrap; font-size: 0.82rem;"><?= nl2br(htmlspecialchars($doc['catatan_perbaikan_unit'])) ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($doc['gpm_catatan'])): ?>
                                <div class="p-2.5 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 mb-3 small">
                                    <div class="fw-bold text-warning-emphasis mb-1 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-user-check"></i> Catatan Rekomendasi GPM Fakultas:
                                    </div>
                                    <div class="text-secondary" style="line-height: 1.45;"><?= nl2br(htmlspecialchars($doc['gpm_catatan'])) ?></div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    Keputusan Evaluasi LPM: <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="card h-100 p-2.5 border rounded-3 cursor-pointer position-relative hover-shadow-sm review-choice-label" style="border-color: #E2E8F0; background: #FFFFFF;">
                                            <div class="d-flex align-items-start gap-2">
                                                <input type="radio" name="status_review" value="sesuai" class="form-check-input mt-1" required>
                                                <div>
                                                    <div class="fw-bold text-success small mb-0.5"><i class="fas fa-circle-check me-1"></i> Disetujui (Sesuai)</div>
                                                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1.35;">Dokumen telah memenuhi standar mutu dan siklus dinyatakan selesai/sah.</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="card h-100 p-2.5 border rounded-3 cursor-pointer position-relative hover-shadow-sm review-choice-label" style="border-color: #E2E8F0; background: #FFFFFF;">
                                            <div class="d-flex align-items-start gap-2">
                                                <input type="radio" name="status_review" value="perlu_perbaikan" class="form-check-input mt-1" required>
                                                <div>
                                                    <div class="fw-bold text-danger small mb-0.5"><i class="fas fa-circle-exclamation me-1"></i> Minta Revisi / Dikembalikan</div>
                                                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1.35;">Dokumen belum sesuai. Kembalikan ke unit / GPM untuk diperbaiki.</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark text-uppercase d-flex align-items-center justify-content-between" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <span>Catatan / Uraian Evaluasi LPM:</span>
                                    <span class="text-muted fw-normal text-none" style="font-size: 0.68rem;">(Wajib diisi jika minta revisi)</span>
                                </label>
                                <textarea name="catatan_review" class="form-control form-control-sm rounded-3" rows="3" placeholder="Tuliskan apresiasi atau poin-poin yang wajib diperbaiki oleh unit..."></textarea>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm btn-review-history d-inline-flex align-items-center gap-1.5" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>">
                                    <i class="fas fa-history"></i> Buka Riwayat Lengkap
                                </button>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                                    <button type="button" class="btn btn-primary bg-scu-blue border-0 rounded-pill px-4 py-1.5 btn-sm fw-semibold shadow-xs btn-submit-review" data-action="submit-review" data-doc-id="<?= $doc['id'] ?>">
                                        <i class="fas fa-paper-plane me-1.5"></i> Simpan Keputusan LPM
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <!-- Status jika sudah Sesuai atau user hanya bisa lihat -->
                        <div class="alert alert-success border-success border-opacity-25 rounded-3 mb-3">
                            <div class="fw-bold text-success small mb-1"><i class="fas fa-circle-check me-1"></i> Status: Dokumen Telah Disetujui (Sesuai Standar Mutu)</div>
                            <p class="small text-muted mb-0" style="line-height: 1.5; font-size: 0.78rem;">
                                Dokumen ini telah divalidasi dan disetujui oleh Lembaga Penjaminan Mutu (LPM).
                            </p>
                            <?php if (!empty($doc['catatan_review'])): ?>
                                <div class="mt-2 p-2 bg-white rounded border border-success border-opacity-25 small text-secondary">
                                    <strong>Catatan LPM:</strong> <?= nl2br(htmlspecialchars($doc['catatan_review'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm btn-review-history d-inline-flex align-items-center gap-1.5" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>">
                                <i class="fas fa-history"></i> Buka Riwayat Lengkap
                            </button>
                            <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

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
                            <?php if (!empty($doc['deskripsi'])): ?>
                                <div class="col-12">
                                    <div class="p-3 rounded-3 border" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                                        <div class="small fw-bold text-secondary d-flex align-items-center gap-1.5 mb-1.5" style="font-size: 0.74rem;">
                                            <i class="fas fa-align-left text-primary"></i> <span>Deskripsi / Narasi Dokumen:</span>
                                        </div>
                                        <div class="small text-dark" style="line-height: 1.55; white-space: pre-line; word-break: break-word; font-size: 0.84rem;">
                                            <?= htmlspecialchars($doc['deskripsi']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
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
                                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                                <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0 me-sm-3">
                                                    <div class="p-2.5 rounded-3 bg-danger bg-opacity-10 text-danger flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                        <i class="fas fa-file-pdf fa-xl"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="fw-bold text-dark text-break mb-1" style="font-size: 0.92rem; line-height: 1.45; word-break: break-word;">
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

                                            <?php if (!empty($f['narasi'])): ?>
                                                <div class="mt-2.5 p-2.5 rounded-2 border" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                                                    <div class="fw-semibold text-danger d-flex align-items-center gap-1 mb-1" style="font-size: 0.72rem;">
                                                        <i class="fas fa-quote-left text-danger opacity-75"></i> <span>Catatan / Narasi Berkas:</span>
                                                    </div>
                                                    <div class="text-secondary small" style="line-height: 1.45; white-space: pre-line; word-break: break-word; font-size: 0.78rem;">
                                                        <?= htmlspecialchars($f['narasi']) ?>
                                                    </div>
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
                                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                                <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0 me-sm-3">
                                                    <div class="p-2.5 rounded-3 bg-success bg-opacity-10 text-success flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                        <i class="fab fa-google-drive fa-xl"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="fw-bold text-dark text-break mb-1" style="font-size: 0.92rem; line-height: 1.45;">
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
        </td></tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr id="emptyRow">
        <td colspan="6" class="text-center py-5 text-muted">
            <i class="fas fa-folder-open fa-3x text-secondary opacity-50 mb-3 d-block"></i>
            <h6 class="fw-bold">Tidak Ada Dokumen yang Sesuai</h6>
            <p class="small mb-0">Coba ubah filter atau kata kunci pencarian.</p>
        </td>
    </tr>
<?php endif; ?>

