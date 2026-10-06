<?php
/**
 * Admin Fakultas: Ruang Kerja Perbaikan Dokumen Mutu (Inbox Revisi dari LPM)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$perluDocs = $perluDocs ?? [];
$sudahDocs = $sudahDocs ?? [];
$documents = $documents ?? [];
$perluCount = count($perluDocs);
$sudahCount = count($sudahDocs);
$totalDocs = count($documents);
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1.5">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; font-size: 0.76rem;">
                    <i class="fas fa-wrench"></i> Ruang Kerja Perbaikan
                </span>
                <span class="text-muted small">&bull;</span>
                <span class="badge bg-dark-blue text-warning px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.75rem;">Tingkat <?= htmlspecialchars($fakultas['kode_fakultas']) ?></span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">Perbaikan Dokumen Mutu Fakultas</h3>
            <p class="text-muted small mb-0" style="line-height: 1.5;">
                Kelola dan tindak lanjuti dokumen mutu tingkat <strong><?= htmlspecialchars($fakultas['nama_fakultas']) ?></strong> yang memperoleh catatan evaluasi perbaikan dari Lembaga Penjaminan Mutu (LPM).
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('fakultas/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-folder-open"></i> Dokumen PPEPP Fakultas
            </a>
            <a href="<?= base_url('fakultas/draft') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-file-pen"></i> Draf Dokumen
            </a>
        </div>
    </div>

    <!-- Metric KPI Cards (SaaS Proportional Height & Breathing Room) -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="saas-stat-card" style="border-top: 4px solid #DC2626 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-danger">Perlu Tindakan Segera</span>
                    <div class="saas-stat-icon-wrap" style="background: #FEE2E2; color: #DC2626;">
                        <i class="fas fa-triangle-exclamation <?= $perluCount > 0 ? 'fa-beat-fade' : '' ?>"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-danger"><?= $perluCount ?></span>
                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-0.5" style="font-size: 0.72rem;">Wajib Revisi</span>
                    </div>
                    <div class="saas-stat-help">Wajib diperbaiki sesuai arahan tim evaluator LPM</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="saas-stat-card" style="border-top: 4px solid #2563EB !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-primary">Menunggu Review Ulang LPM</span>
                    <div class="saas-stat-icon-wrap" style="background: #EFF6FF; color: #2563EB;">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-primary"><?= $sudahCount ?></span>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-0.5" style="font-size: 0.72rem;">Dalam Antrean</span>
                    </div>
                    <div class="saas-stat-help">Sudah diperbaiki & sedang dalam antrean verifikasi</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="saas-stat-card" style="border-top: 4px solid #7C3AED !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label" style="color: #7C3AED;">Total Siklus Revisi</span>
                    <div class="saas-stat-icon-wrap" style="background: #FAF5FF; color: #7C3AED;">
                        <i class="fas fa-code-compare"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number" style="color: #7C3AED;"><?= $totalDocs ?></span>
                        <span class="badge rounded-pill bg-light text-secondary border px-2.5 py-0.5" style="font-size: 0.72rem;">Total Rekam</span>
                    </div>
                    <div class="saas-stat-help">Dokumen fakultas yang pernah/sedang dalam siklus revisi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Navigation Bar -->
    <div class="card shadow-sm rounded-4 p-3 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div class="nav nav-pills d-inline-flex flex-row flex-wrap align-items-center gap-2" id="perbaikanTabs" role="tablist">
                <button class="nav-link rounded-pill active fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2" id="tab-perlu-btn" data-bs-toggle="pill" data-bs-target="#tab-perlu" type="button" role="tab" style="font-size: 0.84rem;">
                    <i class="fas fa-circle-exclamation text-danger"></i>
                    <span>Perlu Tindakan</span>
                    <span class="badge rounded-pill bg-danger text-white px-2 py-0.5" style="font-size: 0.7rem;"><?= $perluCount ?></span>
                </button>
                <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2" id="tab-sudah-btn" data-bs-toggle="pill" data-bs-target="#tab-sudah" type="button" role="tab" style="font-size: 0.84rem;">
                    <i class="fas fa-clock-rotate-left text-primary"></i>
                    <span>Menunggu Review LPM</span>
                    <span class="badge rounded-pill bg-primary text-white px-2 py-0.5" style="font-size: 0.7rem;"><?= $sudahCount ?></span>
                </button>
                <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2" id="tab-all-btn" data-bs-toggle="pill" data-bs-target="#tab-all" type="button" role="tab" style="font-size: 0.84rem;">
                    <i class="fas fa-layer-group text-secondary"></i>
                    <span>Semua (<?= $totalDocs ?>)</span>
                </button>
            </div>

            <div class="d-flex align-items-center gap-2.5 w-100 w-xl-auto justify-content-between justify-content-xl-end flex-wrap">
                <div class="d-flex align-items-center gap-1.5">
                    <label for="perPagePerbaikanFakultas" class="small text-muted text-nowrap mb-0" style="font-size: 0.78rem;">Tampilkan:</label>
                    <select id="perPagePerbaikanFakultas" class="form-select form-select-sm shadow-none" style="width: auto; min-width: 82px; font-size: 0.8rem; border-color: #CBD5E1;">
                        <option value="10" selected>10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                        <option value="100">100 data</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="max-width: 280px;">
                    <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #CBD5E1;"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-1" id="searchPerbaikanInput" placeholder="Cari nama dokumen / catatan..." oninput="filterPerbaikanList()" style="border-color: #CBD5E1;">
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="perbaikanTabsContent">
        <!-- TAB 1: PERLU TINDAKAN SEGERA -->
        <div class="tab-pane fade show active" id="tab-perlu" role="tabpanel">
            <?php if (empty($perluDocs)): ?>
                <div class="card shadow-sm rounded-4 p-5 text-center bg-white" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Luar Biasa! Tidak Ada Dokumen Fakultas yang Perlu Revisi</h5>
                        <p class="text-muted small mx-auto mb-4" style="max-width: 480px;">
                            Seluruh dokumen mutu tingkat fakultas telah sesuai dengan standar atau sedang dalam proses verifikasi tim LPM.
                        </p>
                        <a href="<?= base_url('fakultas/dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 bg-scu-blue border-0 shadow-xs">
                            <i class="fas fa-folder-open me-1.5"></i> Buka Daftar Dokumen Fakultas
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #DC2626; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-wrench"></i> <strong><?= $perluCount ?> Dokumen Wajib Direvisi</strong>
                            </span>
                            <span class="text-muted small">Terdapat catatan evaluasi dari LPM yang harus disesuaikan untuk dokumen tingkat fakultas</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 50px;">No</th>
                                    <th style="min-width: 280px; width: 32%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 130px;">Siklus PPEPP</th>
                                    <th style="min-width: 170px; width: 18%;">Bidang</th>
                                    <th style="min-width: 180px; width: 18%;">Evaluasi LPM</th>
                                    <th class="text-center" style="width: 140px;">Catatan LPM</th>
                                    <th class="text-center" style="width: 130px;">Lampiran</th>
                                    <th class="pe-4 text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($perluDocs as $idx => $pDoc): 
                                    $filesCount = count($pDoc['files'] ?? []);
                                    $hasLink = !empty($pDoc['external_link']);
                                    $hasLegacy = !empty($pDoc['file_path']);
                                    $noteExcerpt = trim($pDoc['catatan_review'] ?? 'Silakan sesuaikan isi dan lampiran dokumen tingkat fakultas.');
                                ?>
                                    <tr class="perbaikan-table-row" data-search="<?= strtolower(htmlspecialchars($pDoc['nama_dokumen'] . ' ' . ($pDoc['nomor_dokumen'] ?? '') . ' ' . ($pDoc['nama_bidang'] ?? '') . ' ' . ($pDoc['reviewer_name'] ?? ''))) ?>">
                                        <td class="ps-4 text-center text-muted small fw-semibold" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.92rem; line-height: 1.4;">
                                                <a href="<?= base_url('fakultas/dokumen/edit/' . $pDoc['id']) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($pDoc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.74rem;">
                                                <?php if (!empty($pDoc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($pDoc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($pDoc['tahun_akademik']) ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($pDoc['siklus']) ?>
                                        </td>
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                                <?= htmlspecialchars($pDoc['nama_bidang'] ?? '-') ?>
                                            </div>
                                            <?php if (!empty($pDoc['nama_sub_bidang'])): ?>
                                                <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                                    <?= htmlspecialchars($pDoc['nama_sub_bidang']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-bold text-dark-blue d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                                                <i class="fas fa-building-columns text-primary"></i>
                                                <span>Pusat Penjaminan Mutu LPM</span>
                                            </div>
                                            <div class="text-muted small mt-1" style="font-size: 0.71rem;">
                                                <i class="far fa-clock text-secondary me-1"></i><?= !empty($pDoc['reviewed_at']) ? date('d M Y, H:i', strtotime($pDoc['reviewed_at'])) . ' WIB' : '-' ?>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                    style="font-size: 0.78rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCatatanEvaluator"
                                                    data-nama="<?= htmlspecialchars($pDoc['nama_dokumen']) ?>"
                                                    data-reviewer="Pusat Penjaminan Mutu LPM"
                                                    data-tanggal="<?= !empty($pDoc['reviewed_at']) ? date('d M Y, H:i', strtotime($pDoc['reviewed_at'])) . ' WIB' : '-' ?>"
                                                    data-catatan="<?= htmlspecialchars($noteExcerpt) ?>"
                                                    data-siklus="<?= htmlspecialchars($pDoc['siklus'] ?? '') ?>"
                                                    data-status="perlu_perbaikan"
                                                    data-edit-url="<?= base_url('fakultas/dokumen/edit/' . $pDoc['id']) ?>">
                                                <i class="fas fa-comment-dots text-danger"></i>
                                                <span>Lihat Catatan</span>
                                            </button>
                                        </td>
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($filesCount > 0): ?>
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs"
                                                        data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                        data-title="<?= htmlspecialchars($pDoc['nama_dokumen']) ?>"
                                                        data-pdf-url="<?= base_url($pDoc['files'][0]['file_path']) ?>"
                                                        data-download-url="<?= base_url($pDoc['files'][0]['file_path']) ?>"
                                                        data-can-download="1" style="font-size: 0.72rem;">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    <span><?= $filesCount ?> Berkas</span>
                                                </button>
                                            <?php elseif ($hasLink): ?>
                                                <a href="<?= htmlspecialchars($pDoc['external_link']) ?>" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill text-decoration-none" style="font-size: 0.72rem;">
                                                    <i class="fab fa-google-drive me-1"></i> GDrive
                                                </a>
                                            <?php elseif ($hasLegacy): ?>
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs"
                                                        data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                        data-title="<?= htmlspecialchars($pDoc['nama_dokumen']) ?>"
                                                        data-pdf-url="<?= base_url($pDoc['file_path']) ?>"
                                                        data-download-url="<?= base_url($pDoc['file_path']) ?>"
                                                        data-can-download="1" style="font-size: 0.72rem;">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    <span>1 Berkas</span>
                                                </button>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-15 text-dark border border-warning px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">
                                                    <i class="fas fa-triangle-exclamation text-warning me-1"></i> Belum Ada
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 py-3 text-center">
                                            <a href="<?= base_url('fakultas/dokumen/edit/' . $pDoc['id']) ?>" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5 text-nowrap" style="font-size: 0.8rem;">
                                                <i class="fas fa-wrench"></i>
                                                <span>Perbaiki</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab 1 -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabPerluFak" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabPerluFak">Menghitung...</div>
                        <nav id="pagTabPerluFak" aria-label="Navigasi Halaman"></nav>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- TAB 2: MENUNGGU REVIEW ULANG -->
        <div class="tab-pane fade" id="tab-sudah" role="tabpanel">
            <?php if (empty($sudahDocs)): ?>
                <div class="card shadow-sm rounded-4 p-5 text-center bg-white" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-inbox text-secondary"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Dokumen Fakultas yang Sedang Menunggu Verifikasi Ulang</h5>
                        <p class="text-muted small mx-auto mb-0" style="max-width: 480px;">
                            Dokumen yang telah Anda perbaiki akan tampil di sini hingga Tim Penjaminan Mutu (LPM) menyatakan statusnya "Sesuai".
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #2563EB; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-clock-rotate-left"></i> <strong><?= $sudahCount ?> Dokumen Dalam Antrean Review</strong>
                            </span>
                            <span class="text-muted small">Perbaikan tingkat fakultas telah dikirim dan sedang menunggu verifikasi ulang LPM</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 50px;">No</th>
                                    <th style="min-width: 280px; width: 32%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 130px;">Siklus PPEPP</th>
                                    <th style="min-width: 170px; width: 18%;">Bidang</th>
                                    <th style="min-width: 180px; width: 18%;">Status & Waktu Kirim</th>
                                    <th class="text-center" style="width: 140px;">Catatan LPM</th>
                                    <th class="text-center" style="width: 130px;">Lampiran</th>
                                    <th class="pe-4 text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sudahDocs as $idx => $sDoc): 
                                    $filesCount = count($sDoc['files'] ?? []);
                                    $hasLink = !empty($sDoc['external_link']);
                                    $hasLegacy = !empty($sDoc['file_path']);
                                    $noteExcerpt = trim($sDoc['catatan_review'] ?? 'Menunggu review ulang LPM.');
                                ?>
                                    <tr class="perbaikan-table-row" data-search="<?= strtolower(htmlspecialchars($sDoc['nama_dokumen'] . ' ' . ($sDoc['nomor_dokumen'] ?? '') . ' ' . ($sDoc['nama_bidang'] ?? '') . ' ' . ($sDoc['reviewer_name'] ?? ''))) ?>">
                                        <td class="ps-4 text-center text-muted small fw-semibold" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.92rem; line-height: 1.4;">
                                                <a href="<?= base_url('fakultas/dokumen/edit/' . $sDoc['id']) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($sDoc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.74rem;">
                                                <?php if (!empty($sDoc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($sDoc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($sDoc['tahun_akademik']) ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($sDoc['siklus']) ?>
                                        </td>
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                                <?= htmlspecialchars($sDoc['nama_bidang'] ?? '-') ?>
                                            </div>
                                            <?php if (!empty($sDoc['nama_sub_bidang'])): ?>
                                                <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                                    <?= htmlspecialchars($sDoc['nama_sub_bidang']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                <i class="fas fa-hourglass-half"></i> Dalam Antrean LPM
                                            </span>
                                            <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                                <i class="far fa-clock text-secondary me-1"></i><?= date('d M Y, H:i', strtotime($sDoc['updated_at'])) ?> WIB
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                    style="font-size: 0.78rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCatatanEvaluator"
                                                    data-nama="<?= htmlspecialchars($sDoc['nama_dokumen']) ?>"
                                                    data-reviewer="Pusat Penjaminan Mutu LPM"
                                                    data-tanggal="<?= !empty($sDoc['reviewed_at']) ? date('d M Y, H:i', strtotime($sDoc['reviewed_at'])) . ' WIB' : '-' ?>"
                                                    data-catatan="<?= htmlspecialchars($noteExcerpt) ?>"
                                                    data-siklus="<?= htmlspecialchars($sDoc['siklus'] ?? '') ?>"
                                                    data-status="sudah_diperbaiki"
                                                    data-edit-url="<?= base_url('fakultas/dokumen/edit/' . $sDoc['id']) ?>">
                                                <i class="fas fa-comment-dots text-primary"></i>
                                                <span>Lihat Catatan</span>
                                            </button>
                                        </td>
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($filesCount > 0): ?>
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs"
                                                        data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                        data-title="<?= htmlspecialchars($sDoc['nama_dokumen']) ?>"
                                                        data-pdf-url="<?= base_url($sDoc['files'][0]['file_path']) ?>"
                                                        data-download-url="<?= base_url($sDoc['files'][0]['file_path']) ?>"
                                                        data-can-download="1" style="font-size: 0.72rem;">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    <span><?= $filesCount ?> Berkas</span>
                                                </button>
                                            <?php elseif ($hasLink): ?>
                                                <a href="<?= htmlspecialchars($sDoc['external_link']) ?>" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill text-decoration-none" style="font-size: 0.72rem;">
                                                    <i class="fab fa-google-drive me-1"></i> GDrive
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 py-3 text-center">
                                            <a href="<?= base_url('fakultas/dokumen/edit/' . $sDoc['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 text-nowrap shadow-2xs" style="font-size: 0.8rem;">
                                                <i class="fas fa-pen-to-square"></i>
                                                <span>Ubah Berkas</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab 2 -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabSudahFak" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabSudahFak">Menghitung...</div>
                        <nav id="pagTabSudahFak" aria-label="Navigasi Halaman"></nav>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- TAB 3: SEMUA DOKUMEN REVISI -->
        <div class="tab-pane fade" id="tab-all" role="tabpanel">
            <?php if (empty($documents)): ?>
                <div class="card shadow-sm rounded-4 p-5 text-center bg-white" style="border: 1px solid #E2E8F0 !important;">
                    <p class="text-muted mb-0">Belum ada riwayat dokumen fakultas yang memerlukan revisi.</p>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #002B49; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-layer-group"></i> <strong>Total: <?= $totalDocs ?> Dokumen</strong>
                            </span>
                            <span class="text-muted small">Seluruh dokumen tingkat fakultas dalam siklus tindak lanjut revisi</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 50px;">No</th>
                                    <th style="min-width: 280px; width: 32%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 150px;">Status Review LPM</th>
                                    <th class="text-center" style="width: 130px;">Siklus PPEPP</th>
                                    <th style="min-width: 170px; width: 18%;">Bidang</th>
                                    <th class="text-center" style="width: 140px;">Catatan LPM</th>
                                    <th class="pe-4 text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $idx => $doc): 
                                    $isPerlu = ($doc['status_review'] === 'perlu_perbaikan');
                                    $noteExcerpt = trim($doc['catatan_review'] ?? ($isPerlu ? 'Silakan sesuaikan isi dokumen sesuai arahan.' : 'Menunggu review ulang LPM.'));
                                ?>
                                    <tr class="perbaikan-table-row" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . ($doc['reviewer_name'] ?? ''))) ?>">
                                        <td class="ps-4 text-center text-muted small fw-semibold" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.92rem; line-height: 1.4;">
                                                <a href="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.74rem;">
                                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($isPerlu): ?>
                                                <span class="badge rounded-pill bg-danger text-white px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">
                                                    <i class="fas fa-wrench me-1"></i> Perlu Perbaikan
                                                </span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-primary text-white px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">
                                                    <i class="fas fa-clock-rotate-left me-1"></i> Menunggu Review LPM
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                                <?= htmlspecialchars($doc['nama_bidang'] ?? '-') ?>
                                            </div>
                                            <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                                <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                                    <?= htmlspecialchars($doc['nama_sub_bidang']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                    style="font-size: 0.78rem;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCatatanEvaluator"
                                                    data-nama="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-reviewer="Pusat Penjaminan Mutu LPM"
                                                    data-tanggal="<?= !empty($doc['reviewed_at']) ? date('d M Y, H:i', strtotime($doc['reviewed_at'])) . ' WIB' : '-' ?>"
                                                    data-catatan="<?= htmlspecialchars($noteExcerpt) ?>"
                                                    data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '') ?>"
                                                    data-status="<?= $isPerlu ? 'perlu_perbaikan' : 'sudah_diperbaiki' ?>"
                                                    data-edit-url="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>">
                                                <i class="fas fa-comment-dots <?= $isPerlu ? 'text-danger' : 'text-primary' ?>"></i>
                                                <span>Lihat Catatan</span>
                                            </button>
                                        </td>
                                        <td class="pe-4 py-3 text-center">
                                            <?php if ($isPerlu): ?>
                                                <a href="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5 text-nowrap" style="font-size: 0.8rem;">
                                                    <i class="fas fa-wrench"></i>
                                                    <span>Perbaiki</span>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('fakultas/dokumen/edit/' . $doc['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 text-nowrap shadow-2xs" style="font-size: 0.8rem;">
                                                    <i class="fas fa-pen-to-square"></i>
                                                    <span>Ubah Berkas</span>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab 3 -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabAllFak" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabAllFak">Menghitung...</div>
                        <nav id="pagTabAllFak" aria-label="Navigasi Halaman"></nav>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Detail Catatan Evaluator LPM -->
    <div class="modal fade" id="modalCatatanEvaluator" tabindex="-1" aria-labelledby="modalCatatanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 py-3.5 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 100%) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" id="evalModalIcon" style="width: 38px; height: 38px; font-size: 1rem; background: #DC2626;">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0.5" id="modalCatatanLabel" style="font-size: 1.1rem; letter-spacing: -0.2px;">
                                Catatan Evaluasi / Arahan LPM
                            </h5>
                            <p class="text-white text-opacity-75 small mb-0" style="font-size: 0.78rem;">Lembaga Penjaminan Mutu UNIKA Soegijapranata</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <!-- Info Dokumen -->
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="small text-muted mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                            Nama Dokumen
                        </div>
                        <h6 class="fw-bold text-dark-blue mb-0" id="evalModalDocTitle" style="font-size: 0.98rem; line-height: 1.45;">-</h6>
                        <div class="d-flex flex-wrap align-items-center gap-3 mt-2 pt-2 border-top text-muted small" style="font-size: 0.76rem;">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="fas fa-building-columns text-primary"></i>
                                <span class="text-dark fw-bold">Pusat Penjaminan Mutu LPM</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="far fa-calendar-check text-secondary"></i>
                                <span>Waktu Review: <strong class="text-dark" id="evalModalTanggal">-</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Isi Catatan Evaluator -->
                    <div>
                        <label class="form-label fw-bold text-dark-blue d-flex align-items-center gap-1.5 mb-2" style="font-size: 0.86rem;">
                            <i class="fas fa-file-lines text-warning"></i>
                            <span>Uraian Catatan Evaluasi &amp; Poin Perbaikan:</span>
                        </label>
                        <div class="p-3.5 rounded-3 bg-white border border-danger-subtle shadow-2xs" 
                             id="evalModalCatatan"
                             style="background-color: #FFFDFD !important; font-size: 0.88rem; line-height: 1.6; color: #1E293B; max-height: 260px; overflow-y: auto; white-space: pre-wrap;">
                            -
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 bg-light bg-opacity-50 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-1.5 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="evalModalEditBtn" class="btn btn-danger btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2">
                        <i class="fas fa-wrench me-1"></i>
                        <span>Perbaiki Dokumen Sekarang</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPerbaikanFakPages = {
    'tab-perlu': 1,
    'tab-sudah': 1,
    'tab-all': 1
};

function paginateFakultasTab(tabId, page) {
    const tabEl = document.getElementById(tabId);
    if (!tabEl) return;

    if (page !== undefined) {
        currentPerbaikanFakPages[tabId] = page;
    }
    const curPage = currentPerbaikanFakPages[tabId] || 1;

    const perPageSelect = document.getElementById('perPagePerbaikanFakultas');
    const perPageVal = perPageSelect ? perPageSelect.value : '10';
    const perPage = (perPageVal === 'all') ? 999999 : parseInt(perPageVal, 10);

    const searchInput = document.getElementById('searchPerbaikanInput');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

    const allRows = Array.from(tabEl.querySelectorAll('tbody tr.perbaikan-table-row'));
    if (allRows.length === 0) return;

    const filteredRows = allRows.filter(row => {
        const text = (row.getAttribute('data-search') || '').toLowerCase();
        return !query || text.includes(query);
    });

    const totalVisible = filteredRows.length;
    const totalPages = Math.ceil(totalVisible / perPage) || 1;
    if (currentPerbaikanFakPages[tabId] > totalPages) {
        currentPerbaikanFakPages[tabId] = totalPages;
    }
    const finalPage = currentPerbaikanFakPages[tabId] || 1;

    const startIdx = (finalPage - 1) * perPage;
    const endIdx = Math.min(startIdx + perPage, totalVisible);

    allRows.forEach(r => r.style.display = 'none');

    filteredRows.forEach((row, idx) => {
        if (idx >= startIdx && idx < endIdx) {
            row.style.display = '';
            const noCol = row.querySelector('td:first-child');
            if (noCol) noCol.textContent = idx + 1;
        } else {
            row.style.display = 'none';
        }
    });

    let infoId = 'infoTabPerluFak';
    let pagId = 'pagTabPerluFak';
    if (tabId === 'tab-sudah') {
        infoId = 'infoTabSudahFak';
        pagId = 'pagTabSudahFak';
    } else if (tabId === 'tab-all') {
        infoId = 'infoTabAllFak';
        pagId = 'pagTabAllFak';
    }

    const infoEl = document.getElementById(infoId);
    const pagEl = document.getElementById(pagId);

    if (infoEl) {
        if (totalVisible === 0) {
            infoEl.textContent = 'Menampilkan 0 dari 0 dokumen';
        } else {
            const startNum = startIdx + 1;
            const endNum = endIdx;
            infoEl.innerHTML = `Menampilkan <strong>${startNum} - ${endNum}</strong> dari total <strong>${totalVisible}</strong> dokumen`;
        }
    }

    if (!pagEl) return;
    if (totalPages <= 1) {
        pagEl.innerHTML = '';
        return;
    }

    let html = '<ul class="pagination pagination-sm mb-0">';
    html += `<li class="page-item ${finalPage === 1 ? 'disabled' : ''}">
        <button type="button" class="page-link rounded-start-pill" onclick="paginateFakultasTab('${tabId}', ${finalPage - 1})" aria-label="Sebelumnya">&laquo;</button>
    </li>`;

    for (let p = 1; p <= totalPages; p++) {
        if (totalPages > 7) {
            if (p !== 1 && p !== totalPages && Math.abs(p - finalPage) > 1) {
                if (p === 2 || p === totalPages - 1) {
                    html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                }
                continue;
            }
        }
        html += `<li class="page-item ${p === finalPage ? 'active' : ''}">
            <button type="button" class="page-link" onclick="paginateFakultasTab('${tabId}', ${p})">${p}</button>
        </li>`;
    }

    html += `<li class="page-item ${finalPage === totalPages ? 'disabled' : ''}">
        <button type="button" class="page-link rounded-end-pill" onclick="paginateFakultasTab('${tabId}', ${finalPage + 1})" aria-label="Selanjutnya">&raquo;</button>
    </li>`;
    html += '</ul>';
    pagEl.innerHTML = html;
}

function filterPerbaikanList() {
    currentPerbaikanFakPages = { 'tab-perlu': 1, 'tab-sudah': 1, 'tab-all': 1 };
    ['tab-perlu', 'tab-sudah', 'tab-all'].forEach(tabId => {
        paginateFakultasTab(tabId);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initial pagination setup
    filterPerbaikanList();

    // Per Page Select Handler
    const perPageSelect = document.getElementById('perPagePerbaikanFakultas');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            filterPerbaikanList();
        });
    }

    // Tab Change Handler
    const tabButtons = document.querySelectorAll('#perbaikanTabs button[data-bs-toggle="pill"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            const targetTab = (e.target.getAttribute('data-bs-target') || '').replace('#', '');
            if (targetTab) {
                paginateFakultasTab(targetTab);
            }
        });
    });

    const modalEl = document.getElementById('modalCatatanEvaluator');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const nama = btn.getAttribute('data-nama') || '-';
            const reviewer = btn.getAttribute('data-reviewer') || '-';
            const tanggal = btn.getAttribute('data-tanggal') || '-';
            const catatan = btn.getAttribute('data-catatan') || 'Tidak ada catatan khusus.';
            const editUrl = btn.getAttribute('data-edit-url') || '#';
            const status = btn.getAttribute('data-status') || 'perlu_perbaikan';

            const titleEl = document.getElementById('evalModalDocTitle');
            const revEl = document.getElementById('evalModalReviewer');
            const tglEl = document.getElementById('evalModalTanggal');
            const catEl = document.getElementById('evalModalCatatan');
            const editBtn = document.getElementById('evalModalEditBtn');
            const iconEl = document.getElementById('evalModalIcon');
            const quoteHeading = document.getElementById('evalModalQuoteHeading');
            const shieldIcon = document.getElementById('evalModalRevShield');

            if (titleEl) titleEl.textContent = nama;
            if (revEl) revEl.textContent = reviewer;
            if (tglEl) tglEl.textContent = tanggal;
            if (catEl) catEl.textContent = catatan;
            
            if (editBtn) {
                editBtn.href = editUrl;
                if (status === 'sudah_diperbaiki') {
                    editBtn.className = 'btn btn-outline-primary btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2';
                    editBtn.innerHTML = '<i class="fas fa-pen-to-square me-1"></i> <span>Ubah / Lengkapi Berkas</span>';
                    if (iconEl) iconEl.style.background = '#2563EB';
                    if (catEl) {
                        catEl.style.background = '#EFF6FF';
                        catEl.style.borderColor = '#BFDBFE';
                        catEl.style.borderLeftColor = '#2563EB';
                    }
                    if (quoteHeading) {
                        quoteHeading.className = 'text-primary fw-bold small text-uppercase mb-1.5 d-flex align-items-center gap-1.5';
                    }
                    if (shieldIcon) {
                        shieldIcon.className = 'fas fa-user-shield text-primary';
                    }
                } else {
                    editBtn.className = 'btn btn-danger btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2';
                    editBtn.innerHTML = '<i class="fas fa-wrench me-1"></i> <span>Perbaiki Dokumen Sekarang</span>';
                    if (iconEl) iconEl.style.background = '#DC2626';
                    if (catEl) {
                        catEl.style.background = '#FFF1F2';
                        catEl.style.borderColor = '#FECDD3';
                        catEl.style.borderLeftColor = '#DC2626';
                    }
                    if (quoteHeading) {
                        quoteHeading.className = 'text-danger fw-bold small text-uppercase mb-1.5 d-flex align-items-center gap-1.5';
                    }
                    if (shieldIcon) {
                        shieldIcon.className = 'fas fa-user-shield text-danger';
                    }
                }
            }
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
