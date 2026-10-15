<?php
/**
 * Admin Fakultas Dashboard View
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header Banner -->
    <?php 
    $perluTindakanDocs = array_values(array_filter($revisiDocs ?? [], fn($d) => in_array($d['status_review'] ?? '', ['perlu_perbaikan', 'revisi_gpm', 'revisi_lpm'])));
    $menungguVerifikasiDocs = array_values(array_filter($revisiDocs ?? [], fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));
    $totalRevisiCount = count($perluTindakanDocs) + count($menungguVerifikasiDocs);
    $perluCount = count($perluTindakanDocs);
    ?>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1.5">
                <span class="badge bg-dark-blue text-warning px-3 py-1">Fakultas <?= htmlspecialchars($fakultas['kode_fakultas']) ?></span>
                <span class="badge bg-light text-secondary border">SPMI PPEPP Tingkat Fakultas</span>
                <?php if ($perluCount > 0): ?>
                    <a href="#sectionPerbaikanFakultas" class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 text-decoration-none fw-semibold shadow-2xs d-inline-flex align-items-center gap-1.5">
                        <i class="fas fa-circle-exclamation"></i> <?= $perluCount ?> Dokumen Butuh Revisi
                    </a>
                <?php endif; ?>
            </div>
            <h3 class="fw-bold text-dark-blue mb-0">Panel Admin: <?= htmlspecialchars($fakultas['nama_fakultas']) ?></h3>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('fakultas/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0">
                <i class="fas fa-cloud-arrow-up me-1"></i> Unggah Dokumen Mutu Baru
            </a>
            <a href="<?= base_url('fakultas/' . $fakultas['id'] . '/ppepp') ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="fas fa-globe me-1"></i> Lihat Web Publik
            </a>
        </div>
    </div>

    <!-- Dekanat Profile Card -->
    <div class="card bg-white border-0 shadow-sm rounded-4 p-3.5 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; font-size: 1.5rem;">
                    <i class="fas fa-landmark"></i>
                </div>
                <div>
                    <div class="small text-muted fw-semibold" style="font-size: 0.75rem;">Struktur Kepemimpinan Dekanat Terdaftar:</div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-0.5">
                        <span class="fw-bold text-dark-blue fs-6">
                            <i class="fas fa-user-tie text-primary me-1"></i>Dekan: <?= htmlspecialchars($fakultas['nama_dekan'] ?: 'Belum diisi') ?>
                        </span>
                        <?php if (!empty($fakultas['nidn_dekan'])): ?>
                            <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">NIDN: <?= htmlspecialchars($fakultas['nidn_dekan']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mt-1" style="font-size: 0.75rem;">
                        <?php if (!empty($fakultas['nama_wadek'])): ?>
                            <span><i class="fas fa-user text-secondary me-1"></i>Wakil Dekan: <?= htmlspecialchars($fakultas['nama_wadek']) ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-calendar-alt text-warning me-1"></i>Periode: <?= htmlspecialchars($fakultas['periode_jabatan'] ?: '2022 - 2026') ?></span>
                    </div>
                </div>
            </div>
            <a href="<?= base_url('fakultas/dekanat') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3.5 align-self-start align-self-md-center">
                <i class="fas fa-pen-to-square me-1"></i> Ubah Profil Dekanat
            </a>
        </div>
    </div>

    <!-- 5 Cycles Metric Cards (Uniform 5-Column Grid on Desktop, Balanced on Tablet/Mobile) -->
    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-penetapan) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(30, 64, 175, 0.1); color: var(--ppepp-penetapan); font-weight: 700; font-size: 0.72rem;">P1</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(30, 64, 175, 0.08); color: var(--ppepp-penetapan);">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['penetapan'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Penetapan</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Fakultas</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-pelaksanaan) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(5, 150, 105, 0.1); color: var(--ppepp-pelaksanaan); font-weight: 700; font-size: 0.72rem;">P2</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(5, 150, 105, 0.08); color: var(--ppepp-pelaksanaan);">
                        <i class="fas fa-person-chalkboard"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['pelaksanaan'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Pelaksanaan</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Fakultas</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-evaluasi) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(217, 119, 6, 0.1); color: var(--ppepp-evaluasi); font-weight: 700; font-size: 0.72rem;">E</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(217, 119, 6, 0.08); color: var(--ppepp-evaluasi);">
                        <i class="fas fa-magnifying-glass-chart"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['evaluasi'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Evaluasi</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Fakultas</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-pengendalian) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(124, 58, 237, 0.1); color: var(--ppepp-pengendalian); font-weight: 700; font-size: 0.72rem;">P3</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(124, 58, 237, 0.08); color: var(--ppepp-pengendalian);">
                        <i class="fas fa-sliders"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['pengendalian'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Pengendalian</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Fakultas</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="ppepp-metric-card border shadow-2xs" style="border-top: 4px solid var(--ppepp-peningkatan) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge rounded-pill" style="background: rgba(8, 145, 178, 0.1); color: var(--ppepp-peningkatan); font-weight: 700; font-size: 0.72rem;">P4</span>
                    <div class="ppepp-metric-icon-wrap" style="background: rgba(8, 145, 178, 0.08); color: var(--ppepp-peningkatan);">
                        <i class="fas fa-arrow-trend-up"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-3 fw-bold text-dark-blue lh-1 mb-1"><?= $cycleStats['peningkatan'] ?></div>
                    <div class="fw-semibold text-secondary small" style="font-size: 0.8rem;">Peningkatan</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Dokumen Fakultas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Perbaikan Dokumen dari LPM (Minimalis & Ringkas) -->
    <?php if ($totalRevisiCount > 0): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" id="sectionPerbaikanFakultas">
            <!-- Header Ringkas & Elegan -->
            <div class="px-4 py-3 bg-white border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center <?= !empty($perluTindakanDocs) ? 'bg-danger bg-opacity-10 text-danger' : 'bg-primary bg-opacity-10 text-primary' ?>" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fas <?= !empty($perluTindakanDocs) ? 'fa-wrench' : 'fa-clock-rotate-left' ?>"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-0.5">
                            <h6 class="fw-bold text-dark-blue mb-0" style="font-size: 0.98rem;">
                                <?= !empty($perluTindakanDocs) ? 'Dokumen Fakultas Perlu Revisi' : 'Dokumen Menunggu Verifikasi LPM' ?>
                            </h6>
                            <span class="badge rounded-pill <?= !empty($perluTindakanDocs) ? 'bg-danger' : 'bg-primary' ?> text-white px-2 py-0.5 fw-bold" style="font-size: 0.7rem;">
                                <?= count($perluTindakanDocs) ?: count($menungguVerifikasiDocs) ?> Dokumen
                            </span>
                        </div>
                        <div class="text-muted small" style="font-size: 0.78rem;">
                            <?= !empty($perluTindakanDocs) ? 'Periksa catatan evaluasi LPM dan perbarui berkas dokumen mutu.' : 'Berkas perbaikan sedang dalam proses verifikasi tim LPM.' ?>
                        </div>
                    </div>
                </div>

                <a href="<?= base_url('fakultas/perbaikan') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 align-self-start align-self-sm-center" style="font-size: 0.82rem;">
                    <span>Buka Ruang Kerja Perbaikan</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- List Ringkasan Maksimal 3 Dokumen Terkini -->
            <div class="p-0 bg-white">
                <?php 
                $displayDocs = array_slice(array_merge($perluTindakanDocs, $menungguVerifikasiDocs), 0, 3);
                foreach ($displayDocs as $item): 
                    $isPerlu = in_array($item['status_review'] ?? '', ['perlu_perbaikan', 'revisi_gpm', 'revisi_lpm']);
                    $isGpmRev = ($item['status_review'] ?? '') === 'revisi_gpm';
                    $revLabel = $isGpmRev ? 'Gugus Penjaminan Mutu (GPM) Fakultas' : 'Pusat Penjaminan Mutu LPM';
                    $revDate = !empty($item['gpm_reviewed_at']) ? date('d M Y, H:i', strtotime($item['gpm_reviewed_at'])) . ' WIB' : (!empty($item['reviewed_at']) ? date('d M Y, H:i', strtotime($item['reviewed_at'])) . ' WIB' : '-');
                    $noteExcerpt = trim($item['gpm_catatan'] ?: ($item['catatan_review'] ?? ''));
                    if (empty($noteExcerpt)) {
                        $noteExcerpt = $isPerlu ? 'Silakan periksa dan sesuaikan berkas dokumen tingkat fakultas.' : 'Menunggu review ulang.';
                    }
                ?>
                    <div class="p-3 px-4 border-bottom d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
                        <div class="d-flex align-items-start gap-3 flex-grow-1" style="min-width: 0;">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 mt-1" 
                                 style="width: 38px; height: 38px; font-size: 1rem; background: <?= $isPerlu ? '#FEE2E2; color: #DC2626;' : '#EFF6FF; color: #2563EB;' ?>">
                                <i class="fas <?= $isPerlu ? 'fa-circle-exclamation' : 'fa-clock-rotate-left' ?>"></i>
                            </div>
                            <div style="min-width: 0;" class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="badge rounded-pill <?= $isPerlu ? 'bg-danger text-white' : 'bg-primary text-white' ?> px-2 py-0.5 fw-bold" style="font-size: 0.68rem;">
                                        <?= $isGpmRev ? 'Revisi GPM Fakultas' : ($isPerlu ? 'Perlu Tindakan' : 'Menunggu Review') ?>
                                    </span>
                                    <?= siklus_badge($item['siklus']) ?>
                                    <?php if (!empty($item['nomor_dokumen'])): ?>
                                        <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.68rem;">
                                            <i class="fas fa-hashtag me-0.5 opacity-75"></i><?= htmlspecialchars($item['nomor_dokumen']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h6 class="mb-1 fw-bold" style="font-size: 0.95rem; line-height: 1.45;">
                                    <a href="<?= base_url('fakultas/dokumen/edit/' . $item['id']) ?>" class="text-dark-blue text-decoration-none hover-primary">
                                        <?= htmlspecialchars($item['nama_dokumen']) ?>
                                    </a>
                                </h6>

                                <div class="d-inline-flex flex-wrap align-items-center gap-2 text-muted" style="font-size: 0.74rem;">
                                    <span><i class="fas <?= $isGpmRev ? 'fa-shield-halved text-purple' : 'fa-building-columns text-primary' ?> me-1"></i><?= htmlspecialchars($revLabel) ?></span>
                                    <?php if ($revDate !== '-'): ?>
                                        <span>&bull;</span>
                                        <span><i class="far fa-calendar-check text-secondary me-1"></i><?= $revDate ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="d-inline-flex align-items-center gap-2 align-self-start align-self-lg-center flex-shrink-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1.5"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalCatatanEvaluator"
                                    data-nama="<?= htmlspecialchars($item['nama_dokumen']) ?>"
                                    data-reviewer="<?= htmlspecialchars($revLabel) ?>"
                                    data-tanggal="<?= $revDate ?>"
                                    data-catatan="<?= htmlspecialchars($noteExcerpt) ?>"
                                    data-siklus="<?= htmlspecialchars($item['siklus'] ?? '') ?>"
                                    data-status="<?= $isPerlu ? 'perlu_perbaikan' : 'sudah_diperbaiki' ?>"
                                    data-is-gpm="<?= $isGpmRev ? '1' : '0' ?>"
                                    data-edit-url="<?= base_url('fakultas/dokumen/edit/' . $item['id']) ?>"
                                    style="font-size: 0.8rem;">
                                <i class="fas fa-comment-dots <?= $isPerlu ? 'text-danger' : 'text-primary' ?>"></i>
                                <span>Lihat Catatan</span>
                            </button>

                            <?php if ($isPerlu): ?>
                                <a href="<?= base_url('fakultas/dokumen/edit/' . $item['id']) ?>" class="btn btn-danger btn-sm rounded-pill px-3.5 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                    <i class="fas fa-wrench"></i>
                                    <span>Perbaiki</span>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                    <i class="fas fa-hourglass-half me-1"></i> Dalam Antrean
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Footer Minimalis -->
                <div class="px-4 py-2.5 bg-light bg-opacity-50 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 small text-muted" style="font-size: 0.78rem;">
                    <span>Menampilkan <strong><?= count($displayDocs) ?></strong> dari total <strong><?= $totalRevisiCount ?></strong> dokumen perbaikan</span>
                    <a href="<?= base_url('fakultas/perbaikan') ?>" class="text-danger fw-semibold text-decoration-none">
                        Lihat Seluruh Dokumen Perbaikan <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Two Columns: Recent Documents & Prodis Under this Faculty -->
    <div class="row g-4 mb-4">
        <!-- Left: Recent Faculty PPEPP Documents -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold text-dark-blue mb-0">
                            <i class="fas fa-calendar-day text-primary me-1.5"></i> Dokumen Mutu Diunggah Hari Ini
                        </h5>
                        <span class="badge bg-light text-primary border px-2.5 py-1 rounded-pill small fw-semibold">
                            <i class="far fa-calendar-check me-1"></i> <?= date('d M Y') ?>
                        </span>
                    </div>
                    <a href="<?= base_url('fakultas/dokumen') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        Lihat Semua (<?= $cycleStats['total'] ?>) <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <?php if (empty($recentDocs)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-check fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Dokumen Mutu yang Diunggah Hari Ini</h6>
                        <p class="small text-muted mb-3">Dokumen mutu tingkat fakultas yang diunggah pada hari ini (<?= date('d M Y') ?>) akan tampil secara otomatis di tabel ini.</p>
                        <a href="<?= base_url('fakultas/dokumen/create') ?>" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5">
                            <i class="fas fa-plus me-1"></i> Unggah Dokumen Baru
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama &amp; Nomor Dokumen</th>
                                    <th>Siklus</th>
                                    <th>Bidang &amp; Standar</th>
                                    <th>Status LPM</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentDocs as $rd): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark-blue mb-0.5"><?= htmlspecialchars($rd['nama_dokumen']) ?></div>
                                            <div class="text-muted small" style="font-size: 0.72rem;">
                                                <?= htmlspecialchars($rd['nomor_dokumen'] ?: '-') ?> | TA <?= htmlspecialchars($rd['tahun_akademik']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?= siklus_badge($rd['siklus']) ?>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark mb-0.5" style="font-size: 0.82rem;">
                                                <?= htmlspecialchars($rd['nama_bidang'] ?: 'Standar Umum') ?>
                                            </div>
                                            <?php if (!empty($rd['nama_sub_bidang'])): ?>
                                                <div class="small text-muted d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                    <i class="fas fa-turn-up fa-rotate-90 text-secondary opacity-50"></i>
                                                    <span class="badge bg-light text-secondary border px-1.5 py-0.5"><?= htmlspecialchars($rd['nama_sub_bidang']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= review_status_badge($rd['status_review'] ?? 'belum_direview') ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= base_url('fakultas/dokumen/edit/' . $rd['id']) ?>" 
                                               class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" 
                                               style="font-size: 0.75rem;" 
                                               title="Ubah / Edit Dokumen">
                                                <i class="fas fa-pen"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Program Studi under this faculty summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark-blue mb-0">Program Studi</h5>
                        <p class="text-muted small mb-0">Di bawah naungan <?= htmlspecialchars($fakultas['kode_fakultas']) ?></p>
                    </div>
                    <span class="badge bg-primary rounded-pill"><?= count($prodiSummary) ?> Prodi</span>
                </div>

                <style>
                .prodi-hover-card {
                    border-color: #E2E8F0 !important;
                    background-color: #F8FAFC !important;
                    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                }
                .prodi-hover-card:hover {
                    background-color: #EFF6FF !important;
                    border-color: #93C5FD !important;
                    transform: translateX(4px);
                    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08) !important;
                }
                .prodi-hover-card:hover .prodi-title {
                    color: #1D4ED8 !important;
                }
                .prodi-hover-card:hover .prodi-doc-badge {
                    background-color: #002B49 !important;
                    color: #FFFFFF !important;
                    border-color: #002B49 !important;
                }
                </style>

                <div class="d-flex flex-column gap-2.5">
                    <?php if (empty($prodiSummary)): ?>
                        <div class="text-center py-4 text-muted small">Belum ada program studi terdaftar.</div>
                    <?php else: ?>
                        <?php foreach ($prodiSummary as $psm): 
                            $hasDocs = (int)$psm['total_doc'] > 0;
                            $publicUrl = base_url('prodi/' . $psm['id']);
                        ?>
                            <a href="<?= $publicUrl ?>" target="_blank" rel="noopener noreferrer" 
                               class="p-2.5 px-3 rounded-3 border d-flex justify-content-between align-items-center text-decoration-none transition-all prodi-hover-card"
                               title="Buka portal publik dokumen <?= htmlspecialchars($psm['jenjang'] . ' ' . $psm['nama_prodi']) ?> (<?= $psm['total_doc'] ?> Dokumen)">
                                <div>
                                    <div class="fw-bold text-dark-blue small d-flex align-items-center gap-1.5 mb-0.5">
                                        <span class="badge rounded-pill" style="background: #F1F5F9 !important; color: #334155 !important; border: 1px solid #CBD5E1 !important; font-size:0.68rem; font-weight:700;"><?= htmlspecialchars($psm['jenjang']) ?></span>
                                        <span class="prodi-title transition-all"><?= htmlspecialchars($psm['nama_prodi']) ?></span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        Kaprodi: <?= htmlspecialchars($psm['nama_kaprodi'] ?: '-') ?>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                    <?php if ($hasDocs): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 shadow-2xs fw-bold px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1.5 transition-all prodi-doc-badge" style="font-size:0.74rem;">
                                            <span><?= $psm['total_doc'] ?> Dok</span>
                                            <i class="fas fa-arrow-up-right-from-square" style="font-size:0.62rem;"></i>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-white border text-muted shadow-2xs fw-medium px-2 py-1 rounded-pill" style="font-size:0.72rem;">
                                            0 Dok
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mt-auto pt-3 border-top">
                    <div class="alert alert-info py-2 px-3 rounded-3 small mb-0 d-flex align-items-center gap-2" style="font-size:0.72rem;">
                        <i class="fas fa-circle-info"></i>
                        <span>Admin Fakultas mengelola dokumen kebijakan tingkat fakultas, sedangkan dokumen operasional dikelola oleh Admin Prodi masing-masing.</span>
                    </div>
                </div>
            </div>
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
                                Catatan Evaluasi / Arahan Reviewer
                            </h5>
                            <p class="text-white text-opacity-75 small mb-0" id="modalCatatanSubLabel" style="font-size: 0.78rem;">Penjaminan Mutu UNIKA Soegijapranata</p>
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
                                <i class="fas fa-building-columns text-primary" id="evalModalReviewerIcon"></i>
                                <span class="text-dark fw-bold" id="evalModalReviewer">Pusat Penjaminan Mutu LPM</span>
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
document.addEventListener('DOMContentLoaded', function() {
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
            const modalLabel = document.getElementById('modalCatatanLabel');
            const modalSubLabel = document.getElementById('modalCatatanSubLabel');
            const revIconEl = document.getElementById('evalModalReviewerIcon');
            const isGpm = btn.getAttribute('data-is-gpm') === '1';

            if (titleEl) titleEl.textContent = nama;
            if (revEl) revEl.textContent = reviewer;
            if (tglEl) tglEl.textContent = tanggal;
            if (catEl) catEl.textContent = catatan;

            if (modalLabel) {
                modalLabel.textContent = isGpm ? 'Catatan Evaluasi / Rekomendasi GPM' : 'Catatan Evaluasi / Arahan LPM';
            }
            if (modalSubLabel) {
                modalSubLabel.textContent = isGpm ? 'Gugus Penjaminan Mutu (GPM) Fakultas' : 'Lembaga Penjaminan Mutu (LPM) UNIKA Soegijapranata';
            }
            if (revIconEl) {
                revIconEl.className = isGpm ? 'fas fa-shield-halved text-purple' : 'fas fa-building-columns text-primary';
            }
            
            if (editBtn) {
                editBtn.href = editUrl;
                if (status === 'sudah_diperbaiki') {
                    editBtn.className = 'btn btn-outline-primary btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2';
                    editBtn.innerHTML = '<i class="fas fa-pen-to-square me-1"></i> <span>Ubah / Lengkapi Berkas</span>';
                    if (iconEl) iconEl.style.background = '#2563EB';
                } else {
                    editBtn.className = 'btn btn-danger btn-sm rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2';
                    editBtn.innerHTML = '<i class="fas fa-wrench me-1"></i> <span>Perbaiki Dokumen Sekarang</span>';
                    if (iconEl) iconEl.style.background = '#DC2626';
                }
            }
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
