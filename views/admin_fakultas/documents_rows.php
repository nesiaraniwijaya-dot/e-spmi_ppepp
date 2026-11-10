<?php
/**
 * views/admin_fakultas/documents_rows.php
 * Partial baris dokumen PPEPP Tingkat Fakultas (paginasi per 50 dokumen).
 */

if (!isset($no)) {
    $no = isset($startNum) && $startNum > 0 ? $startNum : 1;
}

if (!empty($documents)):
    foreach ($documents as $doc): 
        $reviewStatus = $doc['status_review'] ?? 'belum_direview';
        $isPerluPerbaikan = in_array($reviewStatus, ['revisi_gpm', 'perlu_perbaikan']);
        $isGpmRev = true;
        $noteText = $doc['gpm_catatan'] ?? $doc['catatan_review'] ?? '';
        $hasNotes = !empty(trim((string)$noteText));
        $reviewerName = ($doc['status_review'] === 'sesuai') ? 'Pusat Penjaminan Mutu LPM' : 'Gugus Penjaminan Mutu (GPM) Fakultas';
        $revDate = !empty($doc['gpm_reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['gpm_reviewed_at'])) : (!empty($doc['reviewed_at']) ? date('d/m/Y H:i', strtotime($doc['reviewed_at'])) : '-');

        $files = $doc['files'] ?? [];
        $fileCount = count($files);
        $gdriveLinks = parse_external_links($doc['external_link'] ?? '');
        $linkCount = count($gdriveLinks);
    ?>
        <tr class="doc-row" 
            data-siklus="<?= $doc['siklus'] ?>" 
            data-bidang="<?= $doc['bidang_id'] ?? '' ?>"
            data-status="<?= $reviewStatus ?>"
            data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . $doc['nomor_dokumen'] . ' ' . ($doc['nama_bidang'] ?? ''))) ?>"
            style="<?= $isPerluPerbaikan ? 'border-left: 4px solid #DC2626 !important; background: #FFFBFB;' : '' ?>">
            
            <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
            
            <td>
                <div class="fw-bold text-dark-blue mb-1">
                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-1.5 text-muted small mb-1" style="font-size: 0.72rem;">
                    <?php if (!empty($doc['nomor_dokumen'])): ?>
                        <span><i class="fas fa-hashtag text-secondary me-0.5"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                        <span>•</span>
                    <?php endif; ?>
                    <span class="badge bg-light text-secondary border px-1.5 py-0.5"><i class="far fa-calendar-alt text-primary me-1"></i>TA <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <?php if (!empty($doc['nama_prodi'])): ?>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem;">
                            <i class="fas fa-graduation-cap me-1"></i><?= htmlspecialchars($doc['nama_prodi']) ?> (<?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?>)
                        </span>
                    <?php else: ?>
                        <span class="badge rounded-pill shadow-2xs px-2 py-0.5" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.68rem; font-weight: 600;">
                            <i class="fas fa-building me-1"></i>Tingkat Dekanat / Fakultas
                        </span>
                    <?php endif; ?>
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
                                                <div class="d-flex gap-1.5 justify-content-end">
                                                    <?php 
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
                                                            data-file-type="<?= in_array(strtolower($fItem['file_extension'] ?? pathinfo($fItem['file_path'], PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp','gif']) ? 'image' : (in_array(strtolower($fItem['file_extension'] ?? pathinfo($fItem['file_path'], PATHINFO_EXTENSION)), ['doc','docx']) ? 'word' : 'pdf') ?>"
                                                            style="font-size: 0.7rem;">
                                                        <i class="fas fa-eye me-1"></i> Lihat
                                                    </button>
                                                    <a href="<?= base_url($fItem['file_path']) ?>" download class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 0.7rem;">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <?php 
                            $singleFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'file_extension' => 'pdf']; 
                            $fExt = strtolower($singleFile['file_extension'] ?? 'pdf');
                            $fLimit = isset($singleFile['is_page_limited']) && (int)$singleFile['is_page_limited'] === 0 ? 0 : (int)($singleFile['public_page_limit'] ?? 1);
                            $fCanDl = (int)($singleFile['can_download_public'] ?? 0);
                            ?>
                            <div class="d-inline-flex align-items-center gap-1">
                                <?php
                                $sIsImg = in_array($fExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                $sIsWord = in_array($fExt, ['doc', 'docx']);
                                $sType = $sIsImg ? 'image' : ($sIsWord ? 'word' : 'pdf');
                                $sIcon = $sIsImg ? 'fas fa-file-image' : ($sIsWord ? 'fas fa-file-word' : ($fExt === 'pdf' ? 'fas fa-file-pdf' : 'fas fa-file-lines'));
                                ?>
                                <button type="button" class="btn btn-xs btn-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#pdfPreviewModal" 
                                        data-pdf-url="<?= base_url($singleFile['file_path']) ?>" 
                                        data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                        data-public-limit="<?= $fLimit ?>"
                                        data-can-download="<?= $fCanDl ?>"
                                        data-file-type="<?= $sType ?>"
                                        style="font-size: 0.72rem;">
                                    <i class="<?= $sIcon ?>"></i> Lihat
                                </button>
                                <a href="<?= base_url($singleFile['file_path']) ?>" download class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 shadow-2xs" style="font-size: 0.72rem;" title="Unduh Berkas">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($hasLinks): ?>
                        <?php if ($linkCount > 1): ?>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1 dropdown-toggle d-inline-flex align-items-center gap-1 shadow-2xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                    <i class="fab fa-google-drive text-success"></i> <?= $linkCount ?> Tautan GDrive
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 300px; font-size: 0.78rem;">
                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH TAUTAN DOKUMEN:</li>
                                    <?php foreach ($gdriveLinks as $lIdx => $lItem): ?>
                                        <li class="mb-1">
                                            <a href="<?= htmlspecialchars($lItem['url']) ?>" target="_blank" rel="noopener noreferrer" class="dropdown-item p-2 rounded-2 text-wrap d-flex align-items-center justify-content-between gap-2 border-bottom border-light">
                                                <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate">
                                                    <i class="fab fa-google-drive text-success flex-shrink-0"></i>
                                                    <span class="text-truncate"><?= htmlspecialchars($lItem['label'] ?? ('Link #' . ($lIdx + 1))) ?></span>
                                                </span>
                                                <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 0.7rem;"></i>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <?php $singleLink = $gdriveLinks[0]; ?>
                            <a href="<?= htmlspecialchars($singleLink['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.72rem;">
                                <i class="fab fa-google-drive"></i> GDrive
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!$hasFiles && !$hasLinks): ?>
                        <span class="text-muted small">-</span>
                    <?php endif; ?>
                </div>
            </td>

            <!-- Status Review Column -->
            <td>
                <div class="d-flex flex-column align-items-start gap-1">
                    <?= review_status_badge($reviewStatus) ?>
                    
                    <?php if ($isPerluPerbaikan): ?>
                        <div class="d-flex align-items-center gap-1 flex-wrap mt-0.5">
                            <?php if ($noteText): ?>
                                <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs btn-fakultas-note" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#fakultasViewNoteModal"
                                        data-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                        data-notes="<?= htmlspecialchars($noteText, ENT_QUOTES) ?>"
                                        data-reviewer="<?= htmlspecialchars($reviewerName, ENT_QUOTES) ?>"
                                        data-date="<?= $revDate ?>"
                                        style="font-size: 0.7rem;">
                                    <i class="fas fa-comment-dots"></i> Catatan <?= $isGpmRev ? 'GPM' : 'LPM' ?>
                                </button>
                            <?php endif; ?>
                            <a href="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>" class="btn btn-xs btn-danger text-white rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1 shadow-2xs text-decoration-none" style="font-size: 0.7rem;">
                                <i class="fas fa-wrench"></i> Perbaiki
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </td>

            <!-- Aksi Column -->
            <td class="text-center text-nowrap" style="width: 130px;">
                <div class="d-inline-flex align-items-center justify-content-center gap-1.5">
                    <button type="button" 
                            class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                            style="width: 36px; height: 36px;" 
                            title="Lihat Rincian Dokumen" 
                            data-bs-toggle="modal" 
                            data-bs-target="#docModal<?= $doc['id'] ?>">
                        <i class="fas fa-eye text-primary" style="font-size: 0.85rem;"></i>
                    </button>

                    <a href="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>" 
                       class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" 
                       style="width: 36px; height: 36px;" 
                       title="Edit Dokumen">
                        <i class="fas fa-pen text-warning" style="font-size: 0.85rem;"></i>
                    </a>

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

                        <!-- Catatan Evaluasi Resmi (Masking LPM sampai Pengesahan Akhir) -->
                        <?php if (!empty($doc['catatan_review']) && $reviewStatus !== 'revisi_lpm'): ?>
                            <div class="p-3 rounded-3 mb-3 border" style="background: #F8FAFC;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <span class="small fw-bold text-uppercase text-muted" style="font-size: 0.74rem;">
                                        <i class="fas fa-comments text-primary me-1"></i> <?= ($reviewStatus === 'sesuai') ? 'Catatan Pengesahan Akhir LPM:' : 'Catatan Evaluasi GPM Fakultas:' ?>
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
<?php else: ?>
    <tr id="emptyRow">
        <td colspan="7" class="text-center py-5 text-muted">
            <div class="py-4">
                <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                    <i class="fas fa-folder-open text-secondary"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Ditemukan</h6>
                <p class="small text-muted mb-0">Belum ada dokumen mutu yang cocok dengan kriteria pencarian atau filter.</p>
            </div>
        </td>
    </tr>
<?php endif; ?>

