<?php
/**
 * views/public/doc_table_partial.php
 * Partial template untuk rendering tabel dokumen publik PPEPP (Prodi & Fakultas).
 */

$docs = $docs ?? $allDocuments ?? [];
$tableId = $tableId ?? 'ppeppDocumentTable';
$unitTypeLabel = $unitTypeLabel ?? 'Admin Unit';
?>
<div class="table-responsive">
    <table class="table table-custom table-hover align-middle mb-0" <?= $tableId ? 'id="'.$tableId.'"' : '' ?>>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nama &amp; Nomor Dokumen</th>
                <th class="col-siklus-ppepp" style="width: 135px;">Siklus PPEPP</th>
                <th>Bidang</th>
                <th>Jenis</th>
                <th class="text-center" style="width: 140px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($docs)): ?>
                <tr class="no-matching-docs">
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3 text-secondary d-block"></i>
                        <h6 class="fw-bold">Belum ada dokumen dalam kategori ini</h6>
                        <p class="small mb-0">Dokumen mutu akan ditampilkan setelah diunggah oleh <?= htmlspecialchars($unitTypeLabel) ?>.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($docs as $doc): 
                    $adaptive = get_doc_adaptive_standards_and_bidang($doc);
                ?>
                    <tr class="doc-row" 
                        data-title="<?= htmlspecialchars($doc['nama_dokumen'] . ' ' . $adaptive['standards_text']) ?>" 
                        data-nomor="<?= htmlspecialchars($doc['nomor_dokumen'] ?? '') ?>" 
                        data-bidang-id="<?= htmlspecialchars(implode(',', $adaptive['bidang_ids'])) ?>"
                        data-tahun-akademik="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                        data-siklus="<?= strtolower($doc['siklus'] ?? '') ?>">
                        <td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($doc['nama_dokumen']) ?></div>
                            <div class="d-flex flex-wrap align-items-center gap-1.5 mb-1">
                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                    <span class="small text-muted" style="font-size: 0.72rem;"><i class="fas fa-hashtag me-0.5"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                    <span class="text-muted small">•</span>
                                <?php endif; ?>
                                <?php if (!empty($doc['tahun_akademik'])): ?>
                                    <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.7rem;">
                                        <i class="far fa-calendar-alt text-primary me-1"></i>TA <?= htmlspecialchars($doc['tahun_akademik']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($adaptive['standards_html'])): ?>
                                <div class="mt-1">
                                    <?= $adaptive['standards_html'] ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="col-siklus-ppepp">
                            <?= siklus_badge($doc['siklus'] ?? '') ?>
                        </td>
                        <td>
                            <?= $adaptive['bidang_html'] ?>
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

                            if ($isKombinasi): 
                            ?>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge" style="background: #EDE9FE; color: #7C3AED; border: 1px solid #DDD6FE; font-size: 0.72rem;">
                                        <i class="fas fa-folder-open me-1"></i><?= $filesCount ?: 1 ?> File
                                    </span>
                                    <span class="badge badge-link" style="font-size: 0.72rem;">
                                        <i class="fab fa-google-drive me-1"></i><?= $linkCount ?> Drive
                                    </span>
                                </div>
                            <?php elseif ($hasFiles): ?>
                                <?php if ($filesCount > 1): ?>
                                    <span class="badge badge-file"><i class="fas fa-folder-open me-1"></i> <?= $filesCount ?> Berkas</span>
                                <?php else: 
                                    $firstFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_extension' => $doc['file_extension'] ?? ''];
                                    $fMeta = get_portal_file_meta($firstFile['file_path'] ?? '', $firstFile['file_extension'] ?? '');
                                ?>
                                    <?php if (!empty($fMeta['badge_style'])): ?>
                                        <span class="badge" style="<?= $fMeta['badge_style'] ?> font-size: 0.72rem;">
                                            <i class="<?= $fMeta['icon'] ?> me-1"></i> <?= $fMeta['label'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-file" style="font-size: 0.72rem;">
                                            <i class="<?= $fMeta['icon'] ?> me-1"></i> <?= $fMeta['label'] ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php elseif ($hasLinks): ?>
                                <?php if ($linkCount > 1): ?>
                                    <span class="badge badge-link"><i class="fab fa-google-drive me-1"></i> <?= $linkCount ?> Link GDrive</span>
                                <?php else: ?>
                                    <span class="badge badge-link"><i class="fab fa-google-drive me-1"></i> Link GDrive</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex align-items-center justify-content-center gap-1.5 flex-wrap">
                                <?php if ($hasFiles): ?>
                                    <?php if ($filesCount > 1): ?>
                                        <div class="dropdown d-inline-block">
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-eye me-1"></i> <?= $isKombinasi ? 'File (' . $filesCount . ')' : 'Lihat (' . $filesCount . ')' ?>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 300px; max-width: min(92vw, 440px); width: max-content; font-size: 0.78rem;">
                                                <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS DOKUMEN:</li>
                                                <?php foreach ($files as $fItem): 
                                                    $fLimit = isset($fItem['is_page_limited']) && (int)$fItem['is_page_limited'] === 0 ? 0 : (int)($fItem['public_page_limit'] ?? 1);
                                                    $fCanDl = (int)($fItem['can_download_public'] ?? 0);
                                                ?>
                                                    <li class="mb-1.5">
                                                        <?php
                                                        $fMeta = get_portal_file_meta($fItem['file_path'] ?? '', $fItem['file_extension'] ?? '');
                                                        $fType = $fMeta['type'];
                                                        $fIcon = $fMeta['icon'] . ' ' . $fMeta['color_class'];
                                                        $fTag = $fMeta['tag'];
                                                        ?>
                                                        <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#pdfPreviewModal" 
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-file-id="<?= $fItem['id'] ?? '' ?>"
                                                                data-pdf-url="<?= base_url($fItem['file_path']) ?>" 
                                                                data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                                data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                                                data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                                data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null)) ?>"
                                                                data-public-limit="<?= $fLimit ?>"
                                                                data-can-download="<?= $fCanDl ?>"
                                                                data-file-type="<?= $fType ?>">
                                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                                <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate" title="<?= htmlspecialchars($fItem['file_name']) ?>">
                                                                    <i class="<?= $fIcon ?> flex-shrink-0"></i>
                                                                    <span class="text-truncate" style="max-width: 220px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                                </span>
                                                                <span class="badge bg-light text-muted border flex-shrink-0" style="font-size: 0.65rem;"><?= htmlspecialchars($fItem['file_size'] ?: $fTag) ?></span>
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
                                        <div class="d-inline-flex align-items-center gap-1.5">
                                            <?php
                                            $sMeta = get_portal_file_meta($singleFile['file_path'] ?? '', $singleFile['file_extension'] ?? '');
                                            $sType = $sMeta['type'];
                                            ?>
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill shadow-sm px-3" 
                                                    style="height: 32px; font-size: 0.8rem; font-weight: 600;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#pdfPreviewModal" 
                                                    data-doc-id="<?= $doc['id'] ?>"
                                                    data-file-id="<?= $singleFile['id'] ?? '' ?>"
                                                    data-pdf-url="<?= base_url($singleFile['file_path']) ?>" 
                                                    data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                                    data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                                    data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                                    data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null)) ?>"
                                                    data-public-limit="<?= is_unika_user_logged_in() ? 0 : 1 ?>"
                                                    data-can-download="<?= is_unika_user_logged_in() ? 1 : 0 ?>"
                                                    data-file-type="<?= $sType ?>"
                                                    title="Lihat Pratinjau Dokumen">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>
                                            <?php if (is_unika_user_logged_in()): ?>
                                                <a href="<?= base_url($singleFile['file_path']) ?>" download 
                                                   class="btn btn-sm btn-outline-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                   style="width: 34px; height: 32px; padding: 0;" 
                                                   title="Unduh Berkas Asli">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('login?return_url=' . urlencode($_SERVER['REQUEST_URI'] ?? '/')) ?>" 
                                                   class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                   style="width: 34px; height: 32px; padding: 0;" 
                                                   title="Masuk dengan akun institusi untuk mengunduh">
                                                    <i class="fas fa-lock"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($hasLinks): ?>
                                    <?php 
                                    $isLoggedIn = is_user_logged_in();
                                    ?>
                                    <?php if ($linkCount > 1): ?>
                                        <div class="dropdown d-inline-block">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 dropdown-toggle shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fab fa-google-drive me-1"></i> <?= $isKombinasi ? 'Drive (' . $linkCount . ')' : 'Tautan (' . $linkCount . ')' ?>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 300px; max-width: min(92vw, 440px); width: max-content; font-size: 0.78rem;">
                                                <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH TAUTAN DOKUMEN:</li>
                                                <?php foreach ($gdriveLinks as $lIdx => $linkObj): 
                                                    $gUrl = $linkObj['url'];
                                                    $gNarasi = $linkObj['narasi'];
                                                    $gCanDl = (int)($linkObj['can_download_public'] ?? 0);
                                                    $canAccessLink = $isLoggedIn || $gCanDl === 1;
                                                ?>
                                                    <li class="mb-1.5">
                                                        <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#pdfPreviewModal" 
                                                                data-is-link="1"
                                                                data-link-url="<?= htmlspecialchars($gUrl) ?>" 
                                                                data-doc-title="<?= htmlspecialchars($doc['nama_dokumen']) . ' (Tautan #' . ($lIdx + 1) . ')' ?>"
                                                                data-doc-narasi="<?= htmlspecialchars($gNarasi) ?>"
                                                                data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($linkObj['sub_bidang_ids'] ?? null)) ?>"
                                                                data-can-access="<?= $canAccessLink ? 1 : 0 ?>"
                                                                data-can-download="<?= $gCanDl ?>"
                                                                data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>">
                                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                                <span class="d-flex align-items-center gap-1.5 fw-semibold text-primary text-truncate">
                                                                    <i class="fab fa-google-drive flex-shrink-0"></i>
                                                                    <span>Tautan GDrive #<?= $lIdx + 1 ?></span>
                                                                </span>
                                                                <?php if ($canAccessLink): ?>
                                                                    <span class="badge bg-light text-primary border flex-shrink-0" style="font-size: 0.65rem;">Lihat</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary flex-shrink-0" style="font-size: 0.65rem;"><i class="fas fa-lock me-0.5"></i> Terbatas</span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?= render_sub_standar_badges($linkObj['sub_bidang_ids'] ?? null) ?>
                                                            <?php if (!empty($gNarasi)): ?>
                                                                <div class="small text-muted bg-light p-1.5 rounded border border-light-subtle" style="font-size: 0.72rem; line-height: 1.45; white-space: pre-line; word-break: break-word;">
                                                                    <i class="fas fa-quote-left text-primary opacity-50 me-1"></i><?= htmlspecialchars($gNarasi) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php else: 
                                        $singleLink = $gdriveLinks[0];
                                        $sLinkCanDl = (int)($singleLink['can_download_public'] ?? 0);
                                        $canAccessSingle = $isLoggedIn || $sLinkCanDl === 1;
                                    ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#pdfPreviewModal" 
                                                data-is-link="1"
                                                data-link-url="<?= htmlspecialchars($singleLink['url']) ?>" 
                                                data-doc-title="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                                data-doc-narasi="<?= htmlspecialchars($singleLink['narasi'] ?? '') ?>"
                                                data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleLink['sub_bidang_ids'] ?? null)) ?>"
                                                data-can-access="<?= $canAccessSingle ? 1 : 0 ?>"
                                                data-can-download="<?= $sLinkCanDl ?>"
                                                title="<?= htmlspecialchars($singleLink['narasi'] ?? '') ?>">
                                            <i class="fab fa-google-drive me-1"></i> <?= $isKombinasi ? 'GDrive' : 'Lihat' ?>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (!$hasFiles && !$hasLinks): ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
