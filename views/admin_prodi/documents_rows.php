<?php
/**
 * views/admin_prodi/documents_rows.php
 * Partial baris dokumen PPEPP Prodi (paginasi per 50 dokumen).
 */

if (!isset($no)) {
    $no = isset($startNum) && $startNum > 0 ? $startNum : 1;
}

if (!empty($documents)):
    foreach ($documents as $doc): 
        $status = $doc['status_review'] ?? 'belum_direview';
        $isPerluPerbaikan = in_array($status, ['revisi_gpm', 'perlu_perbaikan']);
        $isGpmRev = true;
        $noteText = $doc['gpm_catatan'] ?? $doc['catatan_review'] ?? '';
        $reviewerName = ($doc['status_review'] === 'sesuai') ? 'Pusat Penjaminan Mutu LPM' : 'Gugus Penjaminan Mutu (GPM) Fakultas';
        $revDate = !empty($doc['gpm_reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['gpm_reviewed_at'])) : (!empty($doc['reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['reviewed_at'])) : '-');
    ?>
        <tr class="doc-row" 
            data-title="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
            data-nomor="<?= htmlspecialchars($doc['nomor_dokumen'] ?? '') ?>"
            data-bidang-id="<?= $doc['bidang_id'] ?? '' ?>"
            data-siklus="<?= $doc['siklus'] ?>"
            data-tahun-akademik="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
            data-status-review="<?= $status ?>"
            style="<?= $isPerluPerbaikan ? 'border-left: 4px solid #DC2626 !important; background: #FFFBFB;' : '' ?>">
            <td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
            <td>
                <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($doc['nama_dokumen']) ?></div>
                <?php if ($doc['nomor_dokumen']): ?>
                    <div class="small text-muted" style="font-size: 0.72rem;"><i class="fas fa-hashtag me-1"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></div>
                <?php endif; ?>
                <?php if ($doc['tahun_akademik']): ?>
                    <span class="badge bg-light text-secondary border me-1" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;"><i class="far fa-calendar-alt text-primary me-1"></i>TA <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
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
                $hasFiles = ($filesCount > 0 || (!empty($doc['file_path']) && $doc['jenis_upload'] !== 'link'));
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
                        <?php if ($filesCount > 1): ?>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-xs btn-outline-danger dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                    <i class="fas fa-folder-open text-danger"></i> <?= $filesCount ?> Berkas
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 320px; max-width: 440px; width: max-content; font-size: 0.78rem;">
                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">PILIH BERKAS DOKUMEN:</li>
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
                                                    data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                                    data-public-limit="<?= $fLimit ?>"
                                                    data-can-download="<?= $fCanDl ?>"
                                                    data-file-type="<?= in_array(strtolower($fItem['file_extension'] ?? pathinfo($fItem['file_path'], PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp','gif']) || ($fItem['file_category'] ?? '') === 'photo' ? 'image' : (in_array(strtolower($fItem['file_extension'] ?? pathinfo($fItem['file_path'], PATHINFO_EXTENSION)), ['doc','docx']) ? 'word' : 'pdf') ?> ">
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
                            <?php 
                            $singleFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'narasi' => ''];
                            $fLimit = isset($singleFile['is_page_limited']) && (int)$singleFile['is_page_limited'] === 0 ? 0 : (int)($singleFile['public_page_limit'] ?? 1);
                            $fCanDl = (int)($singleFile['can_download_public'] ?? 0);
                            ?>
                            <div class="d-inline-flex flex-column align-items-start gap-1">
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <?php
                                    $sExt = strtolower($singleFile['file_extension'] ?? pathinfo($singleFile['file_path'], PATHINFO_EXTENSION));
                                    $sIsImg = in_array($sExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']) || ($singleFile['file_category'] ?? '') === 'photo';
                                    $sIsWord = in_array($sExt, ['doc', 'docx']);
                                    $sType = $sIsImg ? 'image' : ($sIsWord ? 'word' : 'pdf');
                                    $sBtnClass = $sIsImg ? 'btn-outline-warning' : ($sIsWord ? 'btn-outline-primary' : 'btn-outline-danger');
                                    $sIcon = $sIsImg ? 'fas fa-file-image' : ($sIsWord ? 'fas fa-file-word' : 'fas fa-file-pdf');
                                    $sLabel = $sIsImg ? 'Foto' : ($sIsWord ? 'Word' : 'PDF');
                                    ?>
                                    <button type="button" class="btn btn-xs <?= $sBtnClass ?> rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#pdfPreviewModal"
                                            data-pdf-url="<?= base_url($singleFile['file_path']) ?>"
                                            data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                            data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                            data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                            data-public-limit="<?= $fLimit ?>"
                                            data-can-download="<?= $fCanDl ?>"
                                            data-file-type="<?= $sType ?>"
                                            style="font-size: 0.72rem;">
                                        <i class="<?= $sIcon ?>"></i> <?= $sLabel ?>
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
                                <button class="btn btn-xs btn-outline-primary dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                    <i class="fab fa-google-drive text-primary"></i> <?= $linkCount ?> Link GDrive
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
                                       class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 text-decoration-none shadow-2xs" 
                                       style="font-size: 0.72rem;">
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
                <div class="d-flex flex-column align-items-start gap-1">
                    <?= review_status_badge($status) ?>
                    
                    <?php if ($isPerluPerbaikan): ?>
                        <div class="d-flex align-items-center gap-1 flex-wrap mt-0.5">
                            <?php if ($hasNotes): ?>
                                <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs btn-prodi-note" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#prodiViewNoteModal"
                                        data-id="<?= $doc['id'] ?>"
                                        data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                        data-notes="<?= htmlspecialchars($noteText ?? '', ENT_QUOTES) ?>"
                                        data-reviewer="<?= htmlspecialchars($reviewerName, ENT_QUOTES) ?>"
                                        data-date="<?= $revDate ?>"
                                        data-status="<?= $status ?>"
                                        data-edit-url="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>"
                                        style="font-size: 0.7rem;">
                                    <i class="fas fa-comment-dots"></i> Catatan <?= $isGpmRev ? 'GPM' : 'LPM' ?>
                                </button>
                            <?php endif; ?>
                            <a href="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>" class="btn btn-xs btn-danger text-white rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs text-decoration-none" style="font-size: 0.7rem;">
                                <i class="fas fa-wrench"></i> Perbaiki
                            </a>
                        </div>
                    <?php elseif (!empty($doc['gpm_catatan'])): ?>
                        <div class="p-1.5 rounded text-start mt-0.5" style="background: #FEF3C7; border: 1px solid #FCD34D; max-width: 230px;">
                            <div class="fw-bold text-warning-emphasis d-flex align-items-center gap-1" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">
                                <i class="fas fa-shield-halved text-warning"></i> Catatan GPM:
                            </div>
                            <div class="text-truncate text-secondary" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;" title="<?= htmlspecialchars($doc['gpm_catatan']) ?>">
                                <?= htmlspecialchars($doc['gpm_catatan']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0.5 btn-review-history d-inline-flex align-items-center gap-1 mt-0.5 shadow-2xs" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">
                        <i class="fas fa-history text-secondary"></i> Riwayat Review
                    </button>
                </div>
            </td>
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
                    <a href="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>" 
                       class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                       style="width: 36px; height: 36px;" 
                       title="Edit / Ubah Dokumen">
                        <i class="fas fa-pen text-warning" style="font-size: 0.85rem;"></i>
                    </a>

                    <!-- Aksi 3: Hapus Dokumen -->
                    <form action="<?= base_url('prodi/dokumen/delete/' . $doc['id']) ?>" method="POST" class="d-inline m-0 p-0">
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
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;">
                                        <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($doc['nama_prodi'] ?? $_SESSION['user']['nama_prodi'] ?? 'Program Studi') ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <span class="small text-muted d-block mb-1">Siklus PPEPP & Status:</span>
                                <div class="d-flex align-items-center gap-2">
                                    <?= siklus_badge($doc['siklus']) ?>
                                    <?= review_status_badge($status) ?>
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

                        <!-- Catatan Evaluasi Resmi (Masking LPM sampai Pengesahan Akhir) -->
                        <?php if (!empty($doc['catatan_review']) && $status !== 'revisi_lpm' && ($status === 'sesuai' || $doc['catatan_review'] !== ($doc['gpm_catatan'] ?? ''))): ?>
                            <div class="p-3 rounded-3 mb-3 border" style="background: #F8FAFC;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <span class="small fw-bold text-uppercase text-muted" style="font-size: 0.74rem;">
                                        <i class="fas fa-comments text-primary me-1"></i> <?= ($status === 'sesuai') ? 'Catatan Pengesahan Akhir LPM:' : 'Catatan Evaluasi GPM Fakultas:' ?>
                                    </span>
                                    <?php if (!empty($doc['reviewed_at'])): ?>
                                        <span class="small text-muted" style="font-size: 0.72rem;">
                                            <i class="far fa-clock me-1"></i><?= date('d M Y, H:i', strtotime($doc['reviewed_at'])) ?> WIB
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-dark" style="line-height: 1.55; white-space: pre-wrap;"><?= htmlspecialchars($doc['catatan_review']) ?></div>
                                <div class="small text-muted mt-2 pt-2 border-top" style="font-size: 0.72rem;">
                                    <i class="fas fa-shield-halved text-purple me-1"></i>Reviewer: <?= htmlspecialchars($reviewerName) ?>
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
                        <a href="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>" class="btn btn-primary bg-scu-blue border-0 rounded-pill px-4 btn-sm fw-semibold shadow-xs">
                            <i class="fas fa-pen me-1.5"></i> Edit Dokumen
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <tr id="emptyRow">
        <td colspan="7" class="text-center py-5 text-muted">
            <div class="py-4">
                <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                    <i class="fas fa-folder-open text-secondary"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Ditemukan</h6>
                <p class="small text-muted mb-0">Coba sesuaikan kata kunci pencarian atau filter yang dipilih.</p>
            </div>
        </td>
    </tr>
<?php endif; ?>

