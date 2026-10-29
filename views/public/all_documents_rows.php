<?php
/**
 * Partial: Table Rows for Portal Dokumen Mutu Universitas
 * Variables expected:
 * - $documents: array of documents
 * - $startNum: integer starting index
 */

if (!function_exists('get_portal_file_meta')) {
    function get_portal_file_meta(string $path, string $extFallback = ''): array {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: $extFallback);
        switch ($ext) {
            case 'docx':
            case 'doc':
                return [
                    'ext' => $ext,
                    'type' => 'word',
                    'label' => 'File Word',
                    'tag' => strtoupper($ext),
                    'icon' => 'fas fa-file-word',
                    'color_class' => 'text-primary',
                    'badge_style' => 'background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE;',
                    'can_preview' => true,
                    'btn_text' => 'View'
                ];
            case 'xlsx':
            case 'xls':
            case 'csv':
                return [
                    'ext' => $ext,
                    'type' => 'excel',
                    'label' => 'File Excel',
                    'tag' => strtoupper($ext),
                    'icon' => 'fas fa-file-excel',
                    'color_class' => 'text-success',
                    'badge_style' => 'background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0;',
                    'can_preview' => true,
                    'btn_text' => 'View'
                ];
            case 'pptx':
            case 'ppt':
                return [
                    'ext' => $ext,
                    'type' => 'ppt',
                    'label' => 'File PPT',
                    'tag' => strtoupper($ext),
                    'icon' => 'fas fa-file-powerpoint',
                    'color_class' => 'text-warning',
                    'badge_style' => 'background: #FFF7ED; color: #EA580C; border: 1px solid #FED7AA;',
                    'can_preview' => true,
                    'btn_text' => 'View'
                ];
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'webp':
            case 'gif':
                return [
                    'ext' => $ext,
                    'type' => 'image',
                    'label' => 'Gambar',
                    'tag' => 'FOTO',
                    'icon' => 'fas fa-file-image',
                    'color_class' => 'text-warning',
                    'badge_style' => 'background: #FEF3C7; color: #D97706; border: 1px solid #FDE68A;',
                    'can_preview' => true,
                    'btn_text' => 'View'
                ];
            case 'pdf':
            default:
                return [
                    'ext' => $ext ?: 'pdf',
                    'type' => 'pdf',
                    'label' => 'File PDF',
                    'tag' => 'PDF',
                    'icon' => 'fas fa-file-pdf',
                    'color_class' => 'text-danger',
                    'badge_style' => '',
                    'can_preview' => true,
                    'btn_text' => 'View'
                ];
        }
    }
}

$isUnikaUser = is_unika_user_logged_in();
$loginUrlForDoc = base_url('login?return_url=' . urlencode($_SERVER['REQUEST_URI'] ?? '/dokumen'));
?>
<?php if (empty($documents)): ?>
    <tr class="no-matching-docs">
        <td colspan="7" class="text-center py-5 text-muted">
            <i class="fas fa-folder-open fa-3x mb-3 text-secondary d-block"></i>
            <h6 class="fw-bold">Tidak ada dokumen yang sesuai kriteria</h6>
            <p class="small text-muted mb-0">Coba ubah kata kunci pencarian atau sesuaikan filter Anda.</p>
        </td>
    </tr>
<?php else: ?>
    <?php $no = $startNum ?? 1; foreach ($documents as $doc): 
        $adaptive = get_doc_adaptive_standards_and_bidang($doc);
    ?>
        <tr class="doc-row" 
            data-title="<?= htmlspecialchars(strtolower($doc['nama_dokumen'] . ' ' . $adaptive['standards_text'])) ?>" 
            data-nomor="<?= htmlspecialchars(strtolower($doc['nomor_dokumen'] ?? '')) ?>" 
            data-prodi="<?= htmlspecialchars(strtolower($doc['nama_prodi'] ?? '')) ?>"
            data-prodi-id="<?= $doc['prodi_id'] ?? '' ?>"
            data-fakultas-id="<?= $doc['computed_fakultas_id'] ?? $doc['fakultas_id'] ?? '' ?>"
            data-level="<?= $doc['level'] ?? '' ?>"
            data-bidang-id="<?= htmlspecialchars(implode(',', $adaptive['bidang_ids'])) ?>"
            data-tahun-akademik="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
            data-siklus="<?= htmlspecialchars(strtolower(trim($doc['siklus'] ?? ''))) ?>">
            <td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
            <td>
                <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($doc['nama_dokumen'] ?? '') ?></div>
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
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
            </td>
            <td>
                <?php if (!empty($doc['jenjang'])): ?>
                    <span class="badge bg-light text-primary border" style="font-size: 0.72rem;"><?= htmlspecialchars($doc['jenjang']) ?></span>
                <?php endif; ?>
                <div class="small fw-semibold text-dark"><?= htmlspecialchars($doc['nama_prodi'] ?? 'Tingkat Fakultas') ?></div>
                <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($doc['nama_fakultas'] ?? '') ?></div>
            </td>
            <td><?= siklus_badge($doc['siklus']) ?></td>
            <td>
                <?= $adaptive['bidang_html'] ?>
                <?php if (!empty($adaptive['standards_html'])): ?>
                    <div class="mt-1">
                        <?= $adaptive['standards_html'] ?>
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

                if ($isKombinasi): 
                ?>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge" style="background: #EDE9FE; color: #7C3AED; border: 1px solid #DDD6FE; font-size: 0.72rem;">
                            <i class="fas fa-folder-open me-1"></i><?= $filesCount ?: 1 ?> Berkas
                        </span>
                        <span class="badge badge-link" style="font-size: 0.72rem;">
                            <i class="fab fa-google-drive me-1"></i><?= $linkCount ?> Drive
                        </span>
                    </div>
                <?php elseif ($hasFiles): ?>
                    <?php if ($filesCount > 1): ?>
                        <span class="badge badge-file" style="font-size: 0.72rem;">
                            <i class="fas fa-folder-open me-1"></i> <?= $filesCount ?> Berkas
                        </span>
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
                        <span class="badge badge-link" style="font-size: 0.72rem;"><i class="fab fa-google-drive me-1"></i> <?= $linkCount ?> Link GDrive</span>
                    <?php else: ?>
                        <span class="badge badge-link" style="font-size: 0.72rem;"><i class="fab fa-google-drive me-1"></i> Link GDrive</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-muted small">-</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <div class="d-inline-flex align-items-center justify-content-center gap-2" style="width: 132px;">
                    <?php if ($hasFiles): ?>
                        <?php if ($filesCount > 1): ?>
                            <!-- Dropdown Berkas Banyak: Lebar 132px -->
                            <div class="dropdown d-inline-block w-100">
                                <button type="button" class="btn btn-sm btn-primary rounded-pill shadow-sm dropdown-toggle w-100 d-inline-flex align-items-center justify-content-center text-nowrap" 
                                        style="height: 32px; font-size: 0.8rem; font-weight: 600;" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-folder-open me-1.5"></i> <?= $isKombinasi ? 'Berkas (' . $filesCount . ')' : 'Pilih (' . $filesCount . ')' ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 320px; max-width: min(92vw, 450px); width: max-content; font-size: 0.78rem;">
                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS DOKUMEN:</li>
                                    <?php foreach ($files as $fItem): 
                                        $fLimit = $isUnikaUser ? 0 : 1;
                                        $fCanDl = $isUnikaUser ? 1 : 0;
                                        $itemMeta = get_portal_file_meta($fItem['file_path'] ?? '', $fItem['file_extension'] ?? '');
                                    ?>
                                        <li class="mb-1.5">
                                            <?php if (!$itemMeta['can_preview']): ?>
                                                <?php if ($isUnikaUser): ?>
                                                    <a href="<?= base_url($fItem['file_path']) ?>" download class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light d-flex align-items-center justify-content-between gap-2">
                                                        <div class="d-flex align-items-center gap-1.5 text-dark text-truncate" style="max-width: 230px;">
                                                            <i class="<?= $itemMeta['icon'] ?> <?= $itemMeta['color_class'] ?> flex-shrink-0"></i>
                                                            <span class="text-truncate fw-semibold"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                        </div>
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 flex-shrink-0" style="font-size: 0.65rem;">
                                                            <i class="fas fa-download me-0.5"></i> Unduh
                                                        </span>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= $loginUrlForDoc ?>" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light d-flex align-items-center justify-content-between gap-2" title="Masuk dengan akun @unika.ac.id untuk mengunduh">
                                                        <div class="d-flex align-items-center gap-1.5 text-dark text-truncate" style="max-width: 230px;">
                                                            <i class="<?= $itemMeta['icon'] ?> <?= $itemMeta['color_class'] ?> flex-shrink-0"></i>
                                                            <span class="text-truncate fw-semibold"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                        </div>
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border flex-shrink-0" style="font-size: 0.65rem;">
                                                            <i class="fas fa-lock me-0.5"></i> Login Unduh
                                                        </span>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#pdfPreviewModal" 
                                                        data-pdf-url="<?= base_url($fItem['file_path']) ?>" 
                                                        data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                        data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                                        data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null)) ?>"
                                                        data-public-limit="<?= $fLimit ?>"
                                                        data-can-download="<?= $fCanDl ?>"
                                                        data-file-type="<?= $itemMeta['type'] ?>">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                        <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate" title="<?= htmlspecialchars($fItem['file_name']) ?>">
                                                            <i class="<?= $itemMeta['icon'] ?> <?= $itemMeta['color_class'] ?> flex-shrink-0"></i>
                                                            <span class="text-truncate" style="max-width: 220px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                        </span>
                                                        <span class="badge bg-light text-muted border flex-shrink-0" style="font-size: 0.65rem;"><?= htmlspecialchars($itemMeta['tag']) ?></span>
                                                    </div>
                                                    <?= render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null) ?>
                                                    <?php if (!empty($fItem['narasi'])): ?>
                                                        <div class="small text-muted bg-light p-1.5 rounded border border-light-subtle" style="font-size: 0.72rem; line-height: 1.45; white-space: pre-line; word-break: break-word;">
                                                            <i class="fas fa-quote-left text-primary opacity-50 me-1"></i><?= htmlspecialchars($fItem['narasi']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </button>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <?php 
                            $singleFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'narasi' => '']; 
                            $sLimit = $isUnikaUser ? 0 : 1;
                            $sCanDl = $isUnikaUser ? 1 : 0;
                            $singleMeta = get_portal_file_meta($singleFile['file_path'] ?? '', $singleFile['file_extension'] ?? '');
                            ?>
                            
                            <?php if (!$singleMeta['can_preview']): ?>
                                <!-- Hanya Ada Tombol Unduh: Lebar 132px persis menyamai 2 tombol View + Download -->
                                <?php if ($isUnikaUser): ?>
                                    <a href="<?= base_url($singleFile['file_path']) ?>" download 
                                       class="btn btn-sm btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center text-nowrap w-100" 
                                       style="height: 32px; font-size: 0.8rem; font-weight: 600;" 
                                       title="Unduh Berkas <?= htmlspecialchars($singleMeta['tag']) ?>">
                                        <i class="fas fa-download me-1.5"></i> Download
                                    </a>
                                <?php else: ?>
                                    <a href="<?= $loginUrlForDoc ?>" 
                                       class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center text-nowrap w-100" 
                                       style="height: 32px; font-size: 0.8rem; font-weight: 600;" 
                                       title="Masuk dengan akun @unika.ac.id untuk mengunduh">
                                        <i class="fas fa-lock me-1.5"></i> Login Unduh
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- Berkas yang bisa di-preview (PDF, Word, Gambar): Tombol View (88px) + Tombol Download (36px) = Total 132px -->
                                <button type="button" class="btn btn-sm btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center text-nowrap" 
                                        style="width: 88px; height: 32px; font-size: 0.8rem; font-weight: 600;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#pdfPreviewModal" 
                                        data-pdf-url="<?= base_url($singleFile['file_path']) ?>" 
                                        data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                        data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                        data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null)) ?>"
                                        data-public-limit="<?= $sLimit ?>"
                                        data-can-download="<?= $sCanDl ?>"
                                        data-file-type="<?= $singleMeta['type'] ?>"
                                        title="Lihat Pratinjau Dokumen">
                                    <i class="fas fa-eye me-1.5"></i> View
                                </button>
                                <?php if ($isUnikaUser): ?>
                                    <a href="<?= base_url($singleFile['file_path']) ?>" download 
                                       class="btn btn-sm btn-outline-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center" 
                                       style="width: 36px; height: 32px; padding: 0; font-size: 0.8rem;" 
                                       title="Unduh Berkas Asli">
                                        <i class="fas fa-download"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= $loginUrlForDoc ?>" 
                                       class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center" 
                                       style="width: 36px; height: 32px; padding: 0; font-size: 0.8rem;" 
                                       title="Masuk dengan akun @unika.ac.id untuk mengunduh dokumen resmi">
                                        <i class="fas fa-lock"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($hasLinks): ?>
                        <?php if ($linkCount > 1): ?>
                            <!-- Dropdown Tautan Banyak: Lebar 132px -->
                            <div class="dropdown d-inline-block w-100">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm dropdown-toggle w-100 d-inline-flex align-items-center justify-content-center text-nowrap" 
                                        style="height: 32px; font-size: 0.8rem; font-weight: 600;"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fab fa-google-drive me-1.5"></i> Drive (<?= $linkCount ?>)
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 300px; max-width: min(92vw, 440px); width: max-content; font-size: 0.78rem;">
                                    <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH TAUTAN DOKUMEN:</li>
                                    <?php foreach ($gdriveLinks as $lIdx => $linkObj): 
                                        $gUrl = $linkObj['url'];
                                        $gNarasi = $linkObj['narasi'];
                                        $canAccessLink = $isUnikaUser;
                                        $gCanDl = $isUnikaUser ? 1 : 0;
                                    ?>
                                        <li class="mb-1.5">
                                            <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#pdfPreviewModal" 
                                                    data-is-link="1"
                                                    data-link-url="<?= htmlspecialchars($gUrl) ?>"
                                                    data-doc-title="<?= htmlspecialchars($doc['nama_dokumen']) . ' (Tautan #' . ($lIdx + 1) . ')' ?>"
                                                    data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>"
                                                    data-doc-narasi="<?= htmlspecialchars($gNarasi) ?>"
                                                    data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($linkObj['sub_bidang_ids'] ?? null)) ?>"
                                                    data-can-access="<?= $canAccessLink ? 1 : 0 ?>"
                                                    data-can-download="<?= $gCanDl ?>">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                    <span class="d-flex align-items-center gap-1.5 fw-semibold text-primary text-truncate">
                                                        <i class="fab fa-google-drive flex-shrink-0"></i>
                                                        <span>Tautan GDrive #<?= $lIdx + 1 ?></span>
                                                    </span>
                                                    <?php if ($canAccessLink): ?>
                                                        <span class="badge bg-light text-primary border flex-shrink-0" style="font-size: 0.65rem;">View</span>
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
                            $canAccessSingle = $isUnikaUser;
                            $sLinkCanDl = $isUnikaUser ? 1 : 0;
                        ?>
                            <!-- Tautan Tunggal GDrive: Lebar 132px -->
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center text-nowrap w-100" 
                                    style="height: 32px; font-size: 0.8rem; font-weight: 600;"
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
                                <i class="fab fa-google-drive me-1.5"></i> GDrive
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
