<?php
/**
 * Detail Dokumen Mutu PPEPP - Dekanat Fakultas
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$siklusKey = strtolower($doc['siklus'] ?? '');
$siklusBadges = [
    'penetapan' => ['label' => 'Penetapan (P1)', 'bg' => '#2563EB', 'light' => '#EFF6FF'],
    'pelaksanaan' => ['label' => 'Pelaksanaan (P2)', 'bg' => '#059669', 'light' => '#ECFDF5'],
    'evaluasi' => ['label' => 'Evaluasi (P3)', 'bg' => '#D97706', 'light' => '#FFFBEB'],
    'pengendalian' => ['label' => 'Pengendalian (P4)', 'bg' => '#7C3AED', 'light' => '#F5F3FF'],
    'peningkatan' => ['label' => 'Peningkatan (P5)', 'bg' => '#DB2777', 'light' => '#FDF2F8']
];
$siklusMeta = $siklusBadges[$siklusKey] ?? ['label' => strtoupper($doc['siklus'] ?? 'PPEPP'), 'bg' => '#475569', 'light' => '#F8FAFC'];
?>

<div class="container-fluid p-0 admin-container">
    <!-- Top Action Bar & Breadcrumb -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; font-size: 0.76rem;">
                    <i class="fas fa-building-columns"></i> <?= htmlspecialchars($fakultas['nama_fakultas']) ?>
                </span>
                <span class="text-muted small">&bull;</span>
                <span class="text-secondary small fw-medium d-inline-flex align-items-center gap-1">
                    <i class="fas fa-shield-halved text-muted"></i> Tingkat Dekanat Fakultas
                </span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px; line-height: 1.35;">
                <?= htmlspecialchars($doc['nama_dokumen']) ?>
            </h3>
            <p class="text-muted small mb-0">
                Detail dokumen mutu, metadata standar SPMI, berkas lampiran, dan riwayat verifikasi tingkat fakultas.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="<?= base_url('fakultas/dokumen/cycles') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-arrows-spin"></i> Kembali ke Siklus Judul
            </a>
            <a href="<?= base_url('fakultas/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-arrow-left"></i> Daftar Dokumen
            </a>
            <?php if (in_array($doc['status_review'] ?? '', ['draft', 'revisi_gpm', 'perlu_perbaikan'])): ?>
                <a href="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>" class="btn btn-warning text-dark btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                    <i class="fas fa-pen-to-square"></i> Edit Dokumen
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Column: Metadata & Files -->
        <div class="col-lg-8">
            <!-- Metadata Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border: 1px solid #E2E8F0 !important;">
                <div class="card-header bg-white px-4 py-3.5 border-bottom border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                        <i class="fas fa-circle-info text-primary"></i> Informasi &amp; Metadata Dokumen
                    </span>
                    <div>
                        <?= render_review_status_badge($doc['status_review'] ?? 'draft') ?>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Tahapan Siklus PPEPP</div>
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: <?= $siklusMeta['bg'] ?>; color: #FFFFFF; font-size: 0.78rem;">
                                <?= $siklusMeta['label'] ?>
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Tahun Akademik</div>
                            <div class="fw-bold text-dark d-inline-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
                                <i class="far fa-calendar-alt text-primary"></i> TA <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Bidang Standar Mutu</div>
                            <div class="fw-semibold text-dark-blue" style="font-size: 0.88rem;">
                                <i class="fas fa-folder text-warning me-1"></i> <?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Sub-Standar Terkait</div>
                            <div class="fw-medium text-dark" style="font-size: 0.85rem;">
                                <?= htmlspecialchars($doc['nama_sub_bidang'] ?? '-') ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Nomor Dokumen / SK</div>
                            <div class="fw-medium text-dark" style="font-size: 0.85rem;">
                                <?= !empty($doc['nomor_dokumen']) ? htmlspecialchars($doc['nomor_dokumen']) : '<span class="text-muted fst-italic">Tanpa Nomor Dokumen</span>' ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal Dokumen / Pengesahan</div>
                            <div class="fw-medium text-dark" style="font-size: 0.85rem;">
                                <?= !empty($doc['tanggal_dokumen']) ? date('d F Y', strtotime($doc['tanggal_dokumen'])) : '<span class="text-muted">-</span>' ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Unit Pemilik / Sasaran</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-1.5 fw-medium" style="font-size: 0.76rem;">
                                    <i class="fas fa-building-columns text-primary me-1"></i> Fakultas <?= htmlspecialchars($fakultas['nama_fakultas']) ?>
                                    <?php if (!empty($doc['nama_prodi'])): ?>
                                        &bull; Program Studi <?= htmlspecialchars($doc['nama_prodi']) ?>
                                    <?php endif; ?>
                                </span>
                                <?php if (!empty($doc['uploader_name'])): ?>
                                    <span class="text-muted small" style="font-size: 0.75rem;">
                                        Diunggah oleh <strong><?= htmlspecialchars($doc['uploader_name']) ?></strong> pada <?= date('d M Y, H:i', strtotime($doc['created_at'])) ?> WIB
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Evaluasi Reviewer (jika ada) -->
            <?php if (!empty($doc['catatan_review']) || !empty($doc['gpm_catatan'])): ?>
                <?php
                    $displayCatatan = !empty($doc['catatan_review']) ? $doc['catatan_review'] : $doc['gpm_catatan'];
                    $reviewerLabel = ($doc['status_review'] === 'sesuai') ? 'Catatan Pengesahan Standar Mutu LPM' : 'Catatan Arahan Review GPM Fakultas';
                ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border: 1px solid #FCD34D !important; background: #FFFDF5;">
                    <div class="card-header bg-warning bg-opacity-10 px-4 py-3 border-bottom border-warning border-opacity-25 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="small fw-bold text-warning-emphasis text-uppercase d-flex align-items-center gap-2" style="font-size: 0.76rem; letter-spacing: 0.5px;">
                            <i class="fas fa-comment-dots text-warning"></i> <?= $reviewerLabel ?>
                        </span>
                        <?php if (!empty($doc['reviewed_at']) || !empty($doc['gpm_reviewed_at'])): ?>
                            <span class="badge bg-white text-secondary border px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">
                                <i class="far fa-clock me-1 text-muted"></i><?= date('d M Y, H:i', strtotime($doc['gpm_reviewed_at'] ?? $doc['reviewed_at'])) ?> WIB
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-4">
                        <div class="small text-dark" style="line-height: 1.6; white-space: pre-wrap; font-size: 0.86rem;"><?= htmlspecialchars($displayCatatan) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Berkas Lampiran Dokumen -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border: 1px solid #E2E8F0 !important;">
                <div class="card-header bg-white px-4 py-3.5 border-bottom border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                        <i class="fas fa-paperclip text-primary"></i> Berkas Lampiran &amp; Tautan Dokumen (<?= count($files) ?>)
                    </span>
                    <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">
                        Multi-Berkas Terpadu
                    </span>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($files)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-2 text-secondary opacity-50"></i>
                            <div class="small">Belum ada berkas lampiran yang diunggah untuk dokumen ini.</div>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($files as $fIdx => $f): 
                                $isLink = ($f['file_category'] ?? '') === 'link' || !empty($f['external_link']);
                                $fileUrl = $isLink ? ($f['external_link'] ?? $f['url'] ?? '#') : base_url($f['file_path']);
                                $fExt = strtolower(pathinfo($f['file_path'] ?? $f['file_name'], PATHINFO_EXTENSION));
                                $isImg = in_array($fExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                $isWord = in_array($fExt, ['doc', 'docx']);
                                $isExcel = in_array($fExt, ['xls', 'xlsx']);
                                $iconClass = $isLink ? 'fab fa-google-drive text-primary' : ($isImg ? 'fas fa-file-image text-warning' : ($isWord ? 'fas fa-file-word text-primary' : ($isExcel ? 'fas fa-file-excel text-success' : 'fas fa-file-pdf text-danger')));
                            ?>
                                <div class="card border rounded-3 p-3 bg-white shadow-2xs">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                                            <div class="p-2.5 rounded-3 bg-light border flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                <i class="<?= $iconClass ?> fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-bold text-dark text-break mb-1" style="font-size: 0.9rem; line-height: 1.4;">
                                                    <?= htmlspecialchars($f['file_name']) ?>
                                                </div>
                                                <div class="text-muted d-flex align-items-center gap-2 flex-wrap small" style="font-size: 0.74rem;">
                                                    <?php if (!empty($f['folder_name'])): ?>
                                                        <span class="badge rounded-pill bg-light text-secondary border px-2 py-0.5" style="font-size: 0.68rem;">
                                                            <i class="fas fa-folder me-1"></i><?= htmlspecialchars($f['folder_name']) ?>
                                                        </span>
                                                        &bull;
                                                    <?php endif; ?>
                                                    <span><?= htmlspecialchars($f['file_size'] ?: ($isLink ? 'Tautan Google Drive' : 'Dokumen')) ?></span>
                                                    &bull;
                                                    <span><?= date('d M Y, H:i', strtotime($f['created_at'] ?? $doc['created_at'])) ?> WIB</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <?php if (!$isLink): ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#pdfPreviewModal" 
                                                        data-pdf-url="<?= $fileUrl ?>" 
                                                        data-doc-title="<?= htmlspecialchars($f['file_name'], ENT_QUOTES) ?>" 
                                                        data-doc-narasi="<?= htmlspecialchars($f['narasi'] ?? '', ENT_QUOTES) ?>" 
                                                        data-doc-ta="<?= htmlspecialchars($doc['tahun_akademik'] ?? '', ENT_QUOTES) ?>" 
                                                        data-public-limit="0" 
                                                        data-can-download="1"
                                                        style="font-size: 0.78rem;">
                                                    <i class="fas fa-eye"></i> Pratinjau
                                                </button>
                                                <a href="<?= $fileUrl ?>" target="_blank" download class="btn btn-sm btn-outline-secondary rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.78rem;">
                                                    <i class="fas fa-download"></i> Unduh
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3.5 py-1.5 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.78rem;">
                                                    <i class="fab fa-google-drive"></i> Buka Tautan GDrive
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($f['narasi'])): ?>
                                        <div class="mt-2.5 p-2.5 px-3 rounded-3 bg-light border-start border-3 border-primary" style="background-color: #F8FAFC !important;">
                                            <div class="text-secondary small d-flex align-items-start gap-2" style="font-size: 0.8rem; line-height: 1.5; word-break: break-word;">
                                                <i class="fas fa-quote-left text-primary opacity-50 mt-1 flex-shrink-0" style="font-size: 0.7rem;"></i>
                                                <span class="text-dark-blue"><?= nl2br(htmlspecialchars($f['narasi'])) ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Column: Review History Timeline -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 20px; border: 1px solid #E2E8F0 !important;">
                <div class="card-header bg-white px-3.5 py-3 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
                    <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-2" style="font-size: 0.76rem; letter-spacing: 0.5px;">
                        <i class="fas fa-clock-rotate-left text-primary"></i> Riwayat Review (<?= count($reviews) ?>)
                    </span>
                </div>
                <div class="card-body p-3.5" style="max-height: 70vh; overflow-y: auto;">
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-history fa-2x mb-2 text-secondary opacity-50"></i>
                            <div class="small">Belum ada catatan log riwayat review untuk dokumen ini.</div>
                        </div>
                    <?php else: ?>
                        <div class="timeline-stream position-relative ps-3" style="border-left: 2px dashed #CBD5E1; margin-left: 10px;">
                            <?php foreach ($reviews as $rev): ?>
                                <div class="timeline-item mb-3 position-relative ps-3">
                                    <div class="position-absolute rounded-circle bg-primary" style="width: 10px; height: 10px; left: -18px; top: 5px; border: 2px solid #FFFFFF;"></div>
                                    <div class="small fw-bold text-dark mb-0.5" style="font-size: 0.82rem;">
                                        <?= htmlspecialchars($rev['reviewer_name'] ?: ($rev['reviewer_user_name'] ?? 'Peninjau Mutu')) ?>
                                    </div>
                                    <div class="text-muted small mb-1.5" style="font-size: 0.71rem;">
                                        <i class="far fa-clock me-1 text-secondary opacity-75"></i><?= date('d M Y, H:i', strtotime($rev['created_at'])) ?> WIB
                                    </div>
                                    <div class="mb-1.5">
                                        <?= render_review_status_badge($rev['status_hasil'] ?? 'menunggu_review_gpm') ?>
                                    </div>
                                    <?php if (!empty($rev['catatan'])): ?>
                                        <div class="small text-secondary bg-light p-2 rounded-2 border border-light-subtle mt-1" style="font-size: 0.76rem; line-height: 1.45; white-space: pre-wrap;">
                                            <?= htmlspecialchars($rev['catatan']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/admin_footer.php'; ?>

