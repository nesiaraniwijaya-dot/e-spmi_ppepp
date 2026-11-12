<?php
/**
 * views/gpm/documents_rows.php
 * Partial baris dokumen & modal review GPM (paginasi per 50 dokumen).
 */

if (!isset($no)) {
    $no = isset($startNum) && $startNum > 0 ? $startNum : 1;
}

if (!empty($documents)):
    foreach ($documents as $doc): 
?>
<tr class="gpm-doc-row" id="doc-row-<?= $doc['id'] ?>">
                                <td class="ps-3 align-top py-3 text-muted fw-semibold small text-center gpm-row-no"><?= $no++ ?></td>

                                <!-- Unit Sasaran Badge -->
                                <td class="align-top py-3">
                                    <?php if ($doc['level'] === 'fakultas'): ?>
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.74rem;">
                                            <i class="fas fa-building-columns"></i> Fakultas
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.74rem;">
                                            <i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($doc['nama_prodi'] ?? '') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Dokumen Mutu & Berkas -->
                                <td class="align-top py-3">
                                    <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.9rem; line-height: 1.35;">
                                        <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap align-items-center gap-2 small text-muted mb-1.5">
                                        <?php if (!empty($doc['nomor_dokumen'])): ?>
                                            <span><i class="fas fa-hashtag text-muted"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                            <span>&bull;</span>
                                        <?php endif; ?>
                                        <span><i class="far fa-calendar text-muted"></i> TA <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                    </div>

                                    <!-- Lampiran Berkas / Links -->
                                    <div class="d-flex flex-wrap align-items-center gap-1.5">
                                        <?php 
                                        $files = $doc['files'] ?? [];
                                        $links = parse_external_links($doc['external_link'] ?? '');
                                        ?>
                                        <?php if (!empty($files)): ?>
                                            <?php if (count($files) === 1): 
                                                $singleF = $files[0];
                                            ?>
                                                <div class="d-inline-flex align-items-center gap-1 flex-wrap">
                                                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#pdfPreviewModal" 
                                                            data-pdf-url="<?= base_url($singleF['file_path']) ?>" 
                                                            data-doc-title="<?= htmlspecialchars($singleF['file_name']) ?>" 
                                                            data-doc-narasi="<?= htmlspecialchars($singleF['narasi'] ?? '') ?>" 
                                                            data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleF['sub_bidang_ids'] ?? null)) ?>" 
                                                            data-public-limit="0" 
                                                            data-can-download="1"
                                                            data-can-access="1"
                                                            data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                                            style="font-size: 0.72rem;" title="Pratinjau Berkas">
                                                        <i class="<?= ($singleF['file_category'] ?? '') === 'photo' ? 'fas fa-image text-info' : 'fas fa-file-pdf' ?>"></i> <?= ($singleF['file_category'] ?? '') === 'photo' ? 'Foto' : 'PDF' ?>
                                                    </button>
                                                    <?php if (!empty($singleF['folder_name'])): ?>
                                                        <span class="badge rounded-pill shadow-2xs" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;" title="Folder: <?= htmlspecialchars($singleF['folder_name']) ?>">
                                                            <i class="fas fa-folder me-0.5"></i><?= htmlspecialchars($singleF['folder_name']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-xs btn-outline-danger dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                            type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                                        <i class="fas fa-folder-open text-danger"></i> <?= count($files) ?> Berkas
                                                    </button>
                                                    <ul class="dropdown-menu shadow-lg border-0 p-2 rounded-3" style="min-width: 280px; max-width: 380px; font-size: 0.78rem;">
                                                        <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS:</li>
                                                        <?php foreach ($files as $fItem): ?>
                                                            <li class="mb-1">
                                                                <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#pdfPreviewModal" 
                                                                        data-pdf-url="<?= base_url($fItem['file_path']) ?>" 
                                                                        data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                                        data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null)) ?>"
                                                                        data-public-limit="0"
                                                                        data-can-download="1"
                                                                        data-can-access="1"
                                                                        data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>">
                                                                    <div class="d-flex align-items-center justify-content-between gap-1.5 mb-0.5">
                                                                        <div class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate">
                                                                            <i class="<?= ($fItem['file_category'] ?? '') === 'photo' ? 'fas fa-image text-info' : 'fas fa-file-pdf text-danger' ?> flex-shrink-0"></i>
                                                                            <span class="text-truncate" style="max-width: 180px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                                        </div>
                                                                        <?php if (!empty($fItem['folder_name'])): ?>
                                                                            <span class="badge rounded-pill shadow-2xs flex-shrink-0" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">
                                                                                <i class="fas fa-folder me-0.5"></i><?= htmlspecialchars($fItem['folder_name']) ?>
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </button>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if (!empty($links)): ?>
                                            <?php if (count($links) === 1): 
                                                $singleL = $links[0];
                                            ?>
                                                <div class="d-inline-flex align-items-center gap-1 flex-wrap">
                                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#pdfPreviewModal" 
                                                            data-is-link="1" 
                                                            data-link-url="<?= htmlspecialchars($singleL['url']) ?>" 
                                                            data-doc-title="<?= htmlspecialchars(!empty($singleL['label']) ? $singleL['label'] : (!empty($singleL['narasi']) ? $singleL['narasi'] : ($doc['nama_dokumen'] ?? 'Dokumen'))) ?>" 
                                                            data-doc-narasi="<?= htmlspecialchars($singleL['narasi'] ?? '') ?>" 
                                                            data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleL['sub_bidang_ids'] ?? null)) ?>" 
                                                            data-can-access="1" 
                                                            data-can-download="1"
                                                            data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                                            style="font-size: 0.72rem;" title="Pratinjau Link GDrive">
                                                        <i class="fab fa-google-drive"></i> GDrive
                                                    </button>
                                                    <?php if (!empty($singleL['folder_name'])): ?>
                                                        <span class="badge rounded-pill shadow-2xs" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;" title="Folder: <?= htmlspecialchars($singleL['folder_name']) ?>">
                                                            <i class="fas fa-folder me-0.5"></i><?= htmlspecialchars($singleL['folder_name']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-xs btn-outline-primary dropdown-toggle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold" 
                                                            type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;">
                                                        <i class="fab fa-google-drive text-primary"></i> <?= count($links) ?> Drive
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 280px; max-width: 380px; font-size: 0.78rem;">
                                                        <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH TAUTAN DRIVE:</li>
                                                        <?php foreach ($links as $lIdx => $lItem): ?>
                                                            <li class="mb-1">
                                                                <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#pdfPreviewModal" 
                                                                        data-is-link="1" 
                                                                        data-link-url="<?= htmlspecialchars($lItem['url']) ?>" 
                                                                        data-doc-title="<?= htmlspecialchars(!empty($lItem['label']) ? $lItem['label'] : (!empty($lItem['narasi']) ? $lItem['narasi'] : ('Tautan GDrive #' . ($lIdx + 1)))) ?>" 
                                                                        data-doc-narasi="<?= htmlspecialchars($lItem['narasi'] ?? '') ?>" 
                                                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($lItem['sub_bidang_ids'] ?? null)) ?>" 
                                                                        data-can-access="1" 
                                                                        data-can-download="1"
                                                                        data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>">
                                                                    <div class="d-flex align-items-center justify-content-between gap-1.5 mb-0.5">
                                                                        <div class="d-flex align-items-center gap-1.5 fw-semibold text-primary text-truncate">
                                                                            <i class="fab fa-google-drive flex-shrink-0"></i>
                                                                            <span class="text-truncate" style="max-width: 180px;"><?= htmlspecialchars(!empty($lItem['label']) ? $lItem['label'] : (!empty($lItem['narasi']) ? $lItem['narasi'] : ('Tautan GDrive #' . ($lIdx + 1)))) ?></span>
                                                                        </div>
                                                                        <?php if (!empty($lItem['folder_name'])): ?>
                                                                            <span class="badge rounded-pill shadow-2xs flex-shrink-0" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 0.65rem; font-weight: 600;">
                                                                                <i class="fas fa-folder me-0.5"></i><?= htmlspecialchars($lItem['folder_name']) ?>
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </button>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if (empty($files) && empty($links)): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">
                                                <i class="fas fa-triangle-exclamation me-1"></i> Belum ada lampiran
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Siklus PPEPP -->
                                <td class="align-top py-3">
                                    <?= siklus_badge($doc['siklus']) ?>
                                </td>

                                <!-- Bidang Standar -->
                                <td class="align-top py-3">
                                    <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                        <?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?>
                                    </div>
                                    <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                        <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                            <i class="fas fa-angle-right text-secondary opacity-75"></i>
                                            <span><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Status & Tahap Review -->
                                <td class="align-top py-3">
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <div class="cursor-pointer btn-review-history d-inline-block" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>" title="Klik untuk melihat riwayat review lengkap">
                                            <?= render_review_status_badge($doc['status_review']) ?>
                                        </div>
                                        
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0.5 btn-review-history d-inline-flex align-items-center gap-1 mt-1 shadow-2xs" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>" style="font-size: 0.68rem;">
                                            <i class="fas fa-history text-secondary"></i> Riwayat Review
                                        </button>

                                        <?php if (!empty($doc['gpm_reviewed_at']) || !empty($doc['lpm_reviewed_at']) || (!empty($doc['reviewed_at']) && !in_array($doc['status_review'], ['draft', 'belum_direview', 'menunggu_review_gpm']))): ?>
                                            <div class="text-muted d-flex align-items-center gap-1 mt-0.5" style="font-size: 0.68rem;">
                                                <i class="far fa-clock text-secondary opacity-75"></i>
                                                <span><?= date('d/m/Y H:i', strtotime($doc['gpm_reviewed_at'] ?? $doc['lpm_reviewed_at'] ?? $doc['reviewed_at'])) ?> WIB</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Aksi -->
                                <td class="pe-3 align-top text-end py-3">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <!-- Detail Modal Trigger -->
                                        <button type="button" class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border shadow-2xs" style="width: 36px; height: 36px;" title="Lihat Rincian Dokumen" data-bs-toggle="modal" data-bs-target="#docModal<?= $doc['id'] ?>">
                                            <i class="fas fa-eye text-primary" style="font-size: 0.85rem;"></i>
                                        </button>

                                        <!-- Review Button -->
                                        <?php if (in_array($doc['status_review'], ['menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview', 'perlu_perbaikan', 'revisi_lpm', 'revisi_gpm'])): ?>
                                            <button type="button" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-2xs text-white" title="Telaah & Review Dokumen Ini" data-bs-toggle="modal" data-bs-target="#gpmReviewModal<?= $doc['id'] ?>" style="font-size: 0.78rem; font-weight: 600;">
                                                <i class="fas fa-clipboard-check"></i> Review
                                            </button>
                                        <?php elseif ($doc['status_review'] === 'menunggu_review_lpm'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1 shadow-2xs" title="Tinjau Status di Antrean LPM" data-bs-toggle="modal" data-bs-target="#gpmReviewModal<?= $doc['id'] ?>" style="font-size: 0.75rem;">
                                                <i class="fas fa-clipboard-list text-primary"></i> Tinjau
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1 text-muted" title="Buka Detail Review" data-bs-toggle="modal" data-bs-target="#gpmReviewModal<?= $doc['id'] ?>" style="font-size: 0.75rem;">
                                                <i class="fas fa-clipboard-list"></i> Tinjau
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr id="gpmEmptyRow">
        <td colspan="7" class="text-center py-5 text-muted">
            <div class="py-4">
                <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                    <i class="fas fa-folder-open text-secondary"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Sesuai</h6>
                <p class="small text-muted mb-0">Coba ubah kata kunci pencarian atau filter yang dipilih.</p>
            </div>
        </td>
    </tr>
<?php endif; ?>

<!-- Modals Container: Terpisah di luar card dan container agar tidak terpengaruh stacking context / z-index card -->
<?php if (!empty($documents)): ?>
    <?php foreach ($documents as $doc): 
                $files = $doc['files'] ?? [];
                $links = $doc['links'] ?? [];
                if (!empty($doc['external_link']) && empty($links)) {
                    $rawLinks = preg_split('/\r\n|\r|\n/', trim($doc['external_link']));
                    foreach ($rawLinks as $rl) {
                        $rl = trim($rl);
                        if (!empty($rl)) {
                            $links[] = ['url' => $rl, 'narasi' => ''];
                        }
                    }
                }
            ?>
                <!-- Modal Detail Dokumen -->
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
                            <div class="modal-body p-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <span class="small text-muted d-block mb-1">Unit Pemilik / Sasaran:</span>
                                        <div>
                                            <?php if ($doc['level'] === 'fakultas'): ?>
                                                <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE;">
                                                    <i class="fas fa-building-columns me-1"></i> Fakultas <?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?>
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
                                            <?= render_review_status_badge($doc['status_review']) ?>
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

                                <!-- Catatan Evaluasi LPM (Khusus Akses GPM) -->
                                <?php 
                                $detailLpmNote = !empty($doc['lpm_catatan']) ? $doc['lpm_catatan'] : (($doc['status_review'] !== 'revisi_gpm' && !empty($doc['catatan_review'])) ? $doc['catatan_review'] : '');
                                if (!empty($detailLpmNote)): ?>
                                    <div class="p-3 rounded-3 mb-3 border <?= in_array($doc['status_review'], ['perlu_perbaikan', 'revisi_lpm', 'revisi_gpm']) ? 'border-danger' : 'border-primary' ?>" style="background: <?= in_array($doc['status_review'], ['perlu_perbaikan', 'revisi_lpm', 'revisi_gpm']) ? '#FFF1F2' : '#EFF6FF' ?>; border-left: 4px solid <?= in_array($doc['status_review'], ['perlu_perbaikan', 'revisi_lpm', 'revisi_gpm']) ? '#DC2626' : '#2563EB' ?> !important;">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                            <span class="small fw-bold text-uppercase <?= in_array($doc['status_review'], ['perlu_perbaikan', 'revisi_lpm', 'revisi_gpm']) ? 'text-danger' : 'text-primary' ?>" style="font-size: 0.74rem;">
                                                <i class="fas fa-lock me-1"></i> Catatan Evaluasi Internal LPM (Akses GPM):
                                            </span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                                <i class="fas fa-award me-0.5"></i> Pusat Penjaminan Mutu LPM
                                            </span>
                                        </div>
                                        <div class="small text-dark mb-2" style="line-height: 1.55; white-space: pre-wrap; font-size: 0.85rem;"><?= htmlspecialchars($detailLpmNote) ?></div>
                                        <div class="small text-muted d-flex align-items-center gap-2 pt-1 border-top" style="font-size: 0.72rem; border-color: rgba(0,0,0,0.06) !important;">
                                            <span><i class="fas fa-building-columns text-primary me-1"></i>Pusat Penjaminan Mutu LPM</span>
                                            <?php if (!empty($doc['reviewed_at']) || !empty($doc['lpm_reviewed_at'])): ?>
                                                <span>&bull;</span>
                                                <span><i class="far fa-clock text-secondary me-1"></i><?= date('d M Y, H:i', strtotime($doc['lpm_reviewed_at'] ?? $doc['reviewed_at'])) ?> WIB</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Berkas Lampiran Fisik (Hanya tampil jika ada berkas) -->
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

                                <!-- Tautan Google Drive Eksternal (Hanya tampil jika ada tautan) -->
                                <?php if (!empty($links)): ?>
                                    <div class="p-3.5 p-md-4 rounded-4 bg-light border mb-4">
                                        <div class="d-flex align-items-center justify-content-between pb-2.5 mb-3 border-bottom border-light-subtle">
                                            <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                                                <i class="fab fa-google-drive text-success"></i> Tautan Google Drive Eksternal (<?= count($links) ?>):
                                            </span>
                                            <span class="badge bg-success bg-opacity-10 text-success border px-2.5 py-1 rounded-pill fw-medium" style="font-size: 0.7rem;">Cloud Storage</span>
                                        </div>
                                        <div class="d-flex flex-column gap-3">
                                            <?php foreach ($links as $lIdx => $l): 
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
                                                                    data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
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
                                <?php if (empty($files) && empty($links)): ?>
                                    <div class="p-3 rounded-3 bg-light border text-center text-muted small mb-3">
                                        <i class="fas fa-circle-info text-secondary me-1"></i> Belum ada berkas fisik maupun tautan yang dilampirkan pada dokumen ini.
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer border-top pt-3">
                                <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary bg-scu-blue border-0 rounded-pill px-4 btn-sm fw-semibold shadow-xs" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#gpmReviewModal<?= $doc['id'] ?>">
                                    <i class="fas fa-clipboard-check me-1.5"></i> <?= in_array($doc['status_review'], ['menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview', 'perlu_perbaikan', 'revisi_lpm', 'revisi_gpm']) ? 'Review Dokumen' : 'Tinjau Review' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Review Dokumen oleh GPM -->
                <div class="modal fade" id="gpmReviewModal<?= $doc['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-0 px-4 py-3" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 100%) !important;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="p-2 rounded-3 bg-white bg-opacity-10 text-warning d-flex align-items-center justify-content-center">
                                        <i class="fas fa-clipboard-check fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="modal-title fw-bold text-white mb-0" style="font-size: 1.05rem;">
                                            <?= in_array($doc['status_review'], ['menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview', 'perlu_perbaikan', 'revisi_lpm', 'revisi_gpm']) ? 'Review Dokumen Mutu (GPM)' : 'Status & Catatan Review Dokumen' ?>
                                        </h6>
                                        <div class="text-white-50 small" style="font-size: 0.72rem;">Gugus Penjaminan Mutu &bull; Penilaian Tingkat Fakultas</div>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4">
                                <!-- Ringkasan Metadata Dokumen -->
                                <div class="p-3 rounded-3 bg-light border mb-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                        <span class="badge rounded-pill px-2.5 py-1 fw-semibold <?= $doc['level'] === 'fakultas' ? 'bg-primary text-white' : 'bg-info text-white' ?>" style="font-size: 0.7rem;">
                                            <i class="fas <?= $doc['level'] === 'fakultas' ? 'fa-building-columns' : 'fa-graduation-cap' ?> me-1"></i>
                                            <?= $doc['level'] === 'fakultas' ? 'Fakultas ' . htmlspecialchars($fakultas['nama_fakultas'] ?? '') : htmlspecialchars(($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? '')) ?>
                                        </span>
                                        <?= render_review_status_badge($doc['status_review'], $doc['tahap_review'] ?? null) ?>
                                    </div>
                                    <h6 class="fw-bold text-dark-blue mb-1" style="line-height: 1.4;"><?= htmlspecialchars($doc['nama_dokumen']) ?></h6>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        <span><i class="fas fa-folder me-1"></i><?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?></span>
                                        <span class="mx-1">&bull;</span>
                                        <span><i class="far fa-calendar me-1"></i>TA <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                        <span class="mx-1">&bull;</span>
                                        <span><i class="fas fa-arrows-spin me-1"></i><?= strtoupper($doc['siklus']) ?></span>
                                    </div>
                                </div>

                                <?php if (in_array($doc['status_review'], ['menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview', 'perlu_perbaikan', 'revisi_lpm', 'revisi_gpm'])): ?>
                                    <!-- FORM TELAAH AKTIF OLEH GPM -->
                                    <?php if ($doc['status_review'] === 'sudah_diperbaiki'): ?>
                                        <div class="alert alert-info border-info border-opacity-25 rounded-3 mb-3 d-flex align-items-start gap-2.5">
                                            <i class="fas fa-clock-rotate-left text-primary mt-1"></i>
                                            <div class="small">
                                                <strong>Dokumen Telah Diperbaiki oleh Unit Sasaran:</strong>
                                                <div class="text-muted mt-0.5">Unit telah mengunggah revisi terbaru berdasarkan evaluasi sebelumnya. Silakan periksa berkas/link dokumen sebelum menyetujui untuk diteruskan ke LPM atau meminta perbaikan lanjutan.</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Catatan Evaluasi Internal LPM (Khusus Akses GPM) jika ada -->
                                    <?php if (!empty($doc['lpm_catatan'])): ?>
                                        <div class="p-3 rounded-3 mb-3 border border-warning-subtle" style="background: #FFFBEB;">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1.5">
                                                <span class="small fw-bold text-warning-emphasis text-uppercase d-flex align-items-center gap-1.5" style="font-size: 0.74rem;">
                                                    <i class="fas fa-lock text-warning"></i> Catatan Evaluasi Internal dari Tim LPM (Khusus GPM):
                                                </span>
                                                <?php if (!empty($doc['lpm_reviewed_at'])): ?>
                                                    <span class="badge bg-white text-secondary border px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                                        <i class="far fa-clock me-1 text-secondary opacity-75"></i><?= date('d M Y, H:i', strtotime($doc['lpm_reviewed_at'])) ?> WIB
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="bg-white p-3 rounded-3 border border-warning border-opacity-25 small text-dark" style="line-height: 1.55; white-space: pre-wrap; font-size: 0.85rem;"><?= htmlspecialchars($doc['lpm_catatan']) ?></div>
                                            <div class="text-muted small mt-1" style="font-size: 0.71rem;">
                                                <i class="fas fa-info-circle text-primary me-1"></i> Catatan asli dari LPM ini <strong>tidak akan diperlihatkan</strong> kepada Program Studi / Dekanat.
                                            </div>
                                        </div>
                                    <?php elseif (!empty($doc['catatan_review'])): ?>
                                        <div class="p-3 rounded-3 mb-3 border border-warning-subtle bg-warning bg-opacity-10">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1.5">
                                                <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-1.5" style="font-size: 0.74rem;">
                                                    <i class="fas fa-comment-dots text-warning"></i> Catatan Evaluasi Sebelumnya:
                                                </span>
                                                <?php if (!empty($doc['gpm_reviewed_at']) || !empty($doc['reviewed_at'])): ?>
                                                    <span class="badge bg-white text-secondary border px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                                        <i class="far fa-clock me-1 text-secondary opacity-75"></i><?= date('d M Y, H:i', strtotime($doc['gpm_reviewed_at'] ?? $doc['reviewed_at'])) ?> WIB
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="bg-white p-3 rounded-3 border border-warning border-opacity-25 small text-dark" style="line-height: 1.55; white-space: pre-wrap; font-size: 0.85rem;"><?= htmlspecialchars($doc['catatan_review']) ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <form action="<?= base_url('gpm/review/submit') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                        <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                                        <input type="hidden" name="redirect_to" value="gpm/dokumen">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark small mb-2">
                                                Keputusan Hasil Review GPM <span class="text-danger">*</span>
                                            </label>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="card h-100 p-3 rounded-3 border cursor-pointer review-decision-card position-relative" style="transition: all 0.2s ease;">
                                                        <input type="radio" name="status_review" value="menunggu_review_lpm" class="form-check-input position-absolute top-0 end-0 m-3" <?= (!in_array($doc['status_review'], ['revisi_gpm', 'perlu_perbaikan', 'revisi_lpm'])) ? 'checked' : '' ?> required>
                                                        <div class="d-flex align-items-center gap-2.5 mb-1.5">
                                                            <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                                <i class="fas fa-check"></i>
                                                            </div>
                                                            <span class="fw-bold text-dark" style="font-size: 0.88rem;">Disetujui GPM</span>
                                                        </div>
                                                        <div class="text-muted small" style="font-size: 0.75rem; line-height: 1.4;">
                                                            Dokumen dinyatakan sesuai kriteria mutu fakultas dan <strong>diteruskan ke antrean review LPM</strong>.
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="card h-100 p-3 rounded-3 border cursor-pointer review-decision-card position-relative" style="transition: all 0.2s ease;">
                                                        <input type="radio" name="status_review" value="revisi_gpm" class="form-check-input position-absolute top-0 end-0 m-3" <?= in_array($doc['status_review'], ['revisi_gpm', 'perlu_perbaikan', 'revisi_lpm']) ? 'checked' : '' ?> required>
                                                        <div class="d-flex align-items-center gap-2.5 mb-1.5">
                                                            <div class="rounded-circle p-2 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                                <i class="fas fa-triangle-exclamation"></i>
                                                            </div>
                                                            <span class="fw-bold text-dark" style="font-size: 0.88rem;">Perlu Revisi</span>
                                                        </div>
                                                        <div class="text-muted small" style="font-size: 0.75rem; line-height: 1.4;">
                                                            Dokumen belum sesuai. Dikembalikan ke unit sasaran untuk <strong>diperbaiki sesuai catatan review</strong>.
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                            $isLpmReturn = in_array($doc['status_review'] ?? '', ['revisi_lpm', 'perlu_perbaikan']);
                                            $hasFreshGpmText = !empty($doc['gpm_catatan']) && (
                                                !$isLpmReturn
                                                || (!empty($doc['gpm_reviewed_at']) && !empty($doc['lpm_reviewed_at']) && strtotime($doc['gpm_reviewed_at']) > strtotime($doc['lpm_reviewed_at']))
                                            );
                                            $prefillGpmText = $hasFreshGpmText ? $doc['gpm_catatan'] : '';
                                            $cleanLpmRef = trim(preg_replace('/^\[CATATAN\s+INTERNAL\s+LPM[^\]]*\]\s*/i', '', (string)($doc['lpm_catatan'] ?? '')));
                                        ?>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                <label for="catatanGpm<?= $doc['id'] ?>" class="form-label fw-bold text-dark small mb-0">
                                                    <?= $isLpmReturn ? 'Arahan Perbaikan Resmi GPM ke Unit' : 'Catatan Evaluasi / Rekomendasi GPM' ?>
                                                </label>
                                                <?php if ($isLpmReturn && !empty($cleanLpmRef)): ?>
                                                    <button type="button" class="btn btn-xs btn-outline-warning text-dark bg-white rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.7rem;"
                                                            data-copy-text="<?= htmlspecialchars($cleanLpmRef, ENT_QUOTES) ?>"
                                                            onclick="const ta=document.getElementById('catatanGpm<?= $doc['id'] ?>'); if(ta){ta.value=this.getAttribute('data-copy-text')||''; ta.focus();}">
                                                        <i class="fas fa-copy text-warning me-1"></i>Salin Catatan LPM
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <textarea name="catatan_review" id="catatanGpm<?= $doc['id'] ?>" rows="3" class="form-control rounded-3" placeholder="Tuliskan arahan resmi GPM atau detail evaluasi perbaikan untuk unit..."><?= htmlspecialchars($prefillGpmText) ?></textarea>
                                            <div class="form-text small" style="font-size: 0.72rem;">
                                                <i class="fas fa-info-circle text-primary me-1"></i> Wajib diisi bila memilih opsi "Perlu Revisi". <?= $isLpmReturn ? 'Unit hanya akan menerima arahan resmi ini atas nama GPM (catatan LPM tetap tersembunyi).' : 'Catatan ini akan terbaca oleh unit sasaran dan reviewer LPM.' ?>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm btn-review-history d-inline-flex align-items-center gap-1.5" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>">
                                                <i class="fas fa-history"></i> Riwayat Review
                                            </button>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary bg-scu-blue border-0 rounded-pill px-4 btn-sm fw-semibold shadow-xs">
                                                    <i class="fas fa-paper-plane me-1.5"></i> Simpan Keputusan Review
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                <?php elseif ($doc['status_review'] === 'menunggu_review_lpm'): ?>
                                    <!-- TAMPILAN DOKUMEN DALAM ANTREAN LPM -->
                                    <div class="p-3.5 rounded-3 mb-3 border border-primary-subtle bg-primary bg-opacity-10">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                            <span class="small fw-bold text-primary text-uppercase d-flex align-items-center gap-1.5">
                                                <i class="fas fa-paper-plane"></i> Telah Disetujui GPM &bull; Dalam Antrean Review LPM
                                            </span>
                                            <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                Sedang Ditinjau LPM
                                            </span>
                                        </div>
                                        <p class="small text-dark mb-2" style="line-height: 1.5;">
                                            Dokumen ini telah disetujui oleh GPM Fakultas dan saat ini sedang menunggu telaah akhir / verifikasi oleh tim Pusat Penjaminan Mutu LPM Universitas.
                                        </p>
                                        <?php if (!empty($doc['gpm_catatan'])): ?>
                                            <div class="bg-white p-2.5 rounded-2 border border-primary border-opacity-25 small text-secondary">
                                                <strong>Catatan / Rekomendasi GPM:</strong><br>
                                                <?= htmlspecialchars($doc['gpm_catatan']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm btn-review-history d-inline-flex align-items-center gap-1.5" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>">
                                            <i class="fas fa-history"></i> Buka Riwayat Review Lengkap
                                        </button>
                                        <button type="button" class="btn btn-light rounded-pill px-4 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                                    </div>

                                <?php else: ?>
                                    <!-- DOKUMEN SESUAI -->
                                    <div class="alert alert-success border-success border-opacity-25 rounded-3 mb-3">
                                        <div class="fw-bold text-success small mb-1"><i class="fas fa-circle-check me-1"></i> Status: Dokumen Telah Disetujui (Sesuai Standar Mutu)</div>
                                        <p class="small text-muted mb-0" style="line-height: 1.5; font-size: 0.78rem;">
                                            Dokumen telah divalidasi dan disetujui oleh Lembaga Penjaminan Mutu (LPM). Siklus mutu pada dokumen ini telah terpenuhi.
                                        </p>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm btn-review-history d-inline-flex align-items-center gap-1.5" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>">
                                            <i class="fas fa-history"></i> Buka Riwayat Review Lengkap
                                        </button>
                                        <button type="button" class="btn btn-light rounded-pill px-4 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>

    <?php endforeach; ?>
<?php else: ?>
    <tr id="gpmEmptyRow">
        <td colspan="7" class="text-center py-5 text-muted">
            <div class="py-4">
                <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                    <i class="fas fa-folder-open text-secondary"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Sesuai</h6>
                <p class="small text-muted mb-0">Coba ubah kata kunci pencarian atau filter yang dipilih.</p>
            </div>
        </td>
    </tr>
<?php endif; ?>
