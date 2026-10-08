<?php
/**
 * Super Admin Dashboard View
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Welcome Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-blue mb-1">Dashboard Super Admin / LPM</h3>
            <p class="text-muted small mb-0">Pusat kendali penjaminan mutu internal universitas dan pengelolaan hak akses.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-users-gear me-1"></i> Manajemen Pengguna
            </a>
        </div>
    </div>

    <!-- Stat Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-landmark"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark-blue"><?= $totalFakultas ?></div>
                    <div class="small text-muted fw-semibold">Total Fakultas</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark-blue"><?= $totalProdi ?></div>
                    <div class="small text-muted fw-semibold">Total Program Studi</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                    <i class="fas fa-file-shield"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark-blue"><?= $totalDokumen ?></div>
                    <div class="small text-muted fw-semibold">Dokumen Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-users-gear"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark-blue"><?= $totalUsers ?></div>
                    <div class="small text-muted fw-semibold">Pengguna Terdaftar</div>
                </div>
            </div>
        </div>
    </div>



    <!-- Executive Review Summary Widget -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, rgba(238,242,255,0.7), rgba(245,243,255,0.7)); border: 1px solid rgba(199,210,254,0.5) !important;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                        <i class="fas fa-clipboard-check me-1"></i> MONITORING TERPADU LPM
                    </span>
                    <?php if ($reviewStats['perlu_tindakan'] > 0): ?>
                        <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                            <?= $reviewStats['perlu_tindakan'] ?> Menunggu Verifikasi
                        </span>
                    <?php endif; ?>
                </div>
                <h5 class="fw-bold text-dark-blue mt-2 mb-1">
                    Ringkasan Eksekutif Review Dokumen Mutu PPEPP
                </h5>
                <p class="text-muted small mb-0">Status verifikasi dokumen mutu seluruh program studi dan dekanat fakultas.</p>
            </div>
            <div>
                <a href="<?= base_url('admin/review') ?>" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 0.85rem;">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Buka Pusat Review Dokumen</span>
                    <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>

        <div class="row g-3">
            <!-- Perlu Tindakan Segera -->
            <div class="col-xl-3 col-sm-6">
                <a href="<?= base_url('admin/review') ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-2xs rounded-3 p-3 bg-white h-100 border-start border-4 border-danger">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="small fw-bold text-muted" style="font-size: 0.72rem; text-transform: uppercase;">Perlu Tindakan Segera</span>
                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger p-1.5"><i class="fas fa-bell"></i></span>
                        </div>
                        <div class="fs-3 fw-bolder text-danger"><?= $reviewStats['perlu_tindakan'] ?></div>
                        <div class="small text-muted" style="font-size: 0.72rem;">Menunggu verifikasi LPM</div>
                    </div>
                </a>
            </div>

            <!-- Belum Pernah Direview -->
            <div class="col-xl-3 col-sm-6">
                <a href="<?= base_url('admin/review') ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-2xs rounded-3 p-3 bg-white h-100 border-start border-4 border-warning">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="small fw-bold text-muted" style="font-size: 0.72rem; text-transform: uppercase;">Belum Direview</span>
                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning p-1.5"><i class="fas fa-clock"></i></span>
                        </div>
                        <div class="fs-3 fw-bolder text-warning"><?= $reviewStats['belum_direview'] ?></div>
                        <div class="small text-muted" style="font-size: 0.72rem;">Unggahan baru unit kerja</div>
                    </div>
                </a>
            </div>

            <!-- Sedang Direvisi Prodi -->
            <div class="col-xl-3 col-sm-6">
                <a href="<?= base_url('admin/review') ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-2xs rounded-3 p-3 bg-white h-100 border-start border-4 border-warning">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="small fw-bold text-muted" style="font-size: 0.72rem; text-transform: uppercase;">Sedang Direvisi Unit</span>
                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning p-1.5"><i class="fas fa-triangle-exclamation"></i></span>
                        </div>
                        <div class="fs-3 fw-bolder text-warning"><?= $reviewStats['perlu_perbaikan'] ?></div>
                        <div class="small text-muted" style="font-size: 0.72rem;">Menunggu unggahan revisi</div>
                    </div>
                </a>
            </div>

            <!-- Selesai Sesuai Standar -->
            <div class="col-xl-3 col-sm-6">
                <a href="<?= base_url('admin/review') ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-2xs rounded-3 p-3 bg-white h-100 border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="small fw-bold text-muted" style="font-size: 0.72rem; text-transform: uppercase;">Sesuai Standar Mutu</span>
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success p-1.5"><i class="fas fa-circle-check"></i></span>
                        </div>
                        <div class="fs-3 fw-bolder text-success"><?= $reviewStats['sesuai'] ?></div>
                        <div class="small text-muted" style="font-size: 0.72rem;">Disetujui &amp; memenuhi SPMI</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Antrean Dokumen Mendesak (Top Urgent Review Queue) -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h5 class="fw-bold text-dark-blue mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-fire-flame-curved text-danger"></i> Antrean Dokumen Menunggu Review
                </h5>
                <p class="text-muted small mb-0">Dokumen terbaru yang memerlukan verifikasi segera dari Kepala LPM / Admin LPM.</p>
            </div>
            <div>
                <a href="<?= base_url('admin/review') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-2xs">
                    <i class="fas fa-list-check me-1"></i> Buka Antrean Lengkap (<?= $reviewStats['perlu_tindakan'] ?>)
                </a>
            </div>
        </div>

        <?php if (empty($urgentReviewDocs)): ?>
            <div class="p-4 text-center rounded-3 bg-light border border-light-subtle">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px; font-size: 1.4rem;">
                    <i class="fas fa-circle-check"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Seluruh Dokumen Telah Selesai Direview!</h6>
                <p class="text-muted small mb-0">Tidak ada antrean dokumen yang tertunda saat ini. Anda dapat memeriksa seluruh riwayat dokumen di Pusat Review.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light text-secondary" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-3" style="width: 45px;">No</th>
                            <th>Dokumen Mutu</th>
                            <th>Unit Kerja</th>
                            <th class="text-center" style="width: 105px;">Siklus</th>
                            <th class="text-center" style="width: 110px;">Berkas</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-center pe-3" style="width: 160px;">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($urgentReviewDocs as $udoc): 
                            $isFakDoc = ($udoc['level'] === 'fakultas' || empty($udoc['prodi_id']));
                            $siklusColors = [
                                'penetapan'    => 'primary',
                                'pelaksanaan'  => 'success',
                                'evaluasi'     => 'warning',
                                'pengendalian' => 'danger',
                                'peningkatan'  => 'info'
                            ];
                            $cColor = $siklusColors[$udoc['siklus'] ?? ''] ?? 'secondary';
                            $files = $udoc['files'] ?? [];
                            $filesCount = count($files);
                        ?>
                            <tr>
                                <td class="ps-3 text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($udoc['nama_dokumen']) ?></div>
                                    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.7rem;">
                                        <span><i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($udoc['uploader_name'] ?: 'Unit Kerja') ?></span>
                                        &bull; <span><?= date('d M Y H:i', strtotime($udoc['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($isFakDoc): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.65rem; font-weight: 700;">FAKULTAS</span>
                                        <div class="fw-semibold text-dark small"><?= htmlspecialchars($udoc['nama_fakultas'] ?? 'Fakultas') ?></div>
                                    <?php else: ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 0.65rem; font-weight: 700;">PRODI <?= htmlspecialchars($udoc['jenjang'] ?? '') ?></span>
                                        <div class="fw-bold text-dark-blue small"><?= htmlspecialchars($udoc['nama_prodi'] ?? 'Prodi') ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $cColor ?> bg-opacity-10 text-<?= $cColor ?> border border-<?= $cColor ?> border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.72rem; text-transform: capitalize;">
                                        <?= htmlspecialchars($udoc['siklus'] ?? '') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($files)): ?>
                                        <?php $firstF = $files[0]; ?>
                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold"
                                                data-bs-toggle="modal" data-bs-target="#dashPdfPreviewModal"
                                                data-pdf-url="<?= base_url($firstF['file_path']) ?>"
                                                data-doc-title="<?= htmlspecialchars($firstF['file_name']) ?>"
                                                style="font-size: 0.72rem;">
                                            <i class="fas fa-file-pdf text-danger"></i> <?= $filesCount > 1 ? $filesCount . ' Berkas' : 'PDF' ?>
                                        </button>
                                    <?php elseif (!empty($udoc['file_path'])): ?>
                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs fw-semibold"
                                                data-bs-toggle="modal" data-bs-target="#dashPdfPreviewModal"
                                                data-pdf-url="<?= base_url($udoc['file_path']) ?>"
                                                data-doc-title="<?= htmlspecialchars($udoc['nama_dokumen']) ?>"
                                                style="font-size: 0.72rem;">
                                            <i class="fas fa-file-pdf text-danger"></i> PDF
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($udoc['status_review'] === 'sudah_diperbaiki'): ?>
                                        <span class="badge rounded-pill" style="background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; font-size: 0.72rem; font-weight: 700;">
                                            <i class="fas fa-arrows-rotate text-warning me-1"></i> Revisi Masuk
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">
                                            <i class="fas fa-clock text-warning"></i> Belum Direview
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-3" style="width: 200px; min-width: 190px;">
                                    <div class="d-flex align-items-center justify-content-center" style="gap: 10px;">
                                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold shadow-2xs d-inline-flex align-items-center gap-1.5"
                                                style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#dashQuickApproveModal"
                                                data-id="<?= $udoc['id'] ?>"
                                                data-title="<?= htmlspecialchars($udoc['nama_dokumen'], ENT_QUOTES) ?>">
                                            <i class="fas fa-circle-check"></i> Setujui
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-bold shadow-2xs d-inline-flex align-items-center gap-1.5"
                                                style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#dashQuickRevisionModal"
                                                data-id="<?= $udoc['id'] ?>"
                                                data-title="<?= htmlspecialchars($udoc['nama_dokumen'], ENT_QUOTES) ?>"
                                                data-notes="<?= htmlspecialchars($udoc['catatan_review'] ?? '', ENT_QUOTES) ?>">
                                            <i class="fas fa-pen-to-square"></i> Revisi
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center pt-3 border-top mt-3">
                <a href="<?= base_url('admin/review') ?>" class="text-decoration-none fw-semibold small text-primary">
                    Lihat Semua Dokumen Antrean di Pusat Review Dokumen <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Matriks Kepatuhan PPEPP per Fakultas (Modern Spacious Overview Grid) -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                        <i class="fas fa-chart-column me-1"></i> STATUS KEPATUHAN INSTITUSI
                    </span>
                </div>
                <h5 class="fw-bold text-dark-blue mb-1 fs-5">
                    Matriks Progres Kepatuhan PPEPP per Fakultas
                </h5>
                <p class="text-muted small mb-0">Pemantauan kepatuhan dokumen mutu seluruh fakultas secara komparatif, transparan, dan terstruktur.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('admin/fakultas') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 shadow-2xs fw-semibold">
                    <i class="fas fa-landmark me-1"></i> Master Fakultas
                </a>
                <a href="<?= base_url('admin/review') ?>" class="btn btn-primary rounded-pill px-3 py-1.5 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Pusat Review Dokumen</span>
                </a>
            </div>
        </div>

        <div class="row g-4 mb-2">
            <?php foreach ($fakultasGroups as $fak): 
                $fId = $fak['id'];
                $prodis = $prodisByFakultas[$fId] ?? [];
                $fakSummary = $fakultasPpeppSummaries[$fId] ?? [];
                
                $fakTotalDocs = 0;
                $fakPerluReview = (int)($fakSummary['perlu_review_count'] ?? 0);
                $fakRevisi = (int)($fakSummary['revisi_count'] ?? 0);
                $fakSesuai = (int)($fakSummary['sesuai_count'] ?? 0);
                $fakTotalDocs += (int)($fakSummary['total_dokumen_fakultas'] ?? 0);

                foreach ($prodis as $p) {
                    $fakTotalDocs += (int)$p['total_dokumen'];
                    $fakPerluReview += (int)$p['perlu_review_count'];
                    $fakRevisi += (int)$p['revisi_count'];
                    $fakSesuai += (int)$p['sesuai_count'];
                }

                $compliancePct = $fakTotalDocs > 0 ? round(($fakSesuai / $fakTotalDocs) * 100) : 0;

                // Color schemes based on compliance rate
                $rateBadgeClass = 'bg-light text-muted border';
                $progressBarClass = 'bg-secondary';
                if ($compliancePct >= 80) {
                    $rateBadgeClass = 'badge-status-sesuai';
                    $progressBarClass = 'bg-success';
                } elseif ($compliancePct >= 50) {
                    $rateBadgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                    $progressBarClass = 'bg-primary';
                } elseif ($compliancePct > 0) {
                    $rateBadgeClass = 'badge-status-review-ulang';
                    $progressBarClass = 'bg-warning';
                }
            ?>
                <div class="col-xxl-3 col-xl-4 col-md-6">
                    <div class="card border-0 shadow-xs rounded-4 p-4 h-100 bg-white faculty-matrix-card d-flex flex-column justify-content-between" style="border: 1px solid #E2E8F0 !important; min-height: 250px;">
                        <div>
                            <!-- Header Bar: Kode Fakultas & Badge Persentase -->
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-2.5 py-1.5 fw-bold rounded-pill" style="font-size: 0.75rem;">
                                    <i class="fas fa-landmark me-1"></i> <?= htmlspecialchars($fak['kode_fakultas']) ?>
                                </span>
                                <span class="badge rounded-pill px-3 py-1.5 fw-bolder <?= $rateBadgeClass ?>" style="font-size: 0.8rem;">
                                    <?= $compliancePct ?>% Selesai
                                </span>
                            </div>

                            <!-- Nama Fakultas -->
                            <h6 class="fw-bold text-dark-blue fs-6 mb-1 text-truncate" title="<?= htmlspecialchars($fak['nama_fakultas']) ?>">
                                <?= htmlspecialchars($fak['nama_fakultas']) ?>
                            </h6>

                            <!-- Subtitle: Jumlah Prodi & Dokumen -->
                            <div class="text-muted small d-flex align-items-center gap-2 mb-3" style="font-size: 0.78rem;">
                                <span><i class="fas fa-graduation-cap text-primary opacity-75 me-1"></i><?= count($prodis) ?> Program Studi</span>
                                <span class="text-secondary opacity-50">&bull;</span>
                                <span><i class="fas fa-file-lines text-secondary opacity-75 me-1"></i><?= $fakTotalDocs ?> Dokumen</span>
                            </div>

                            <!-- Progress Bar Kepatuhan dengan Label -->
                            <div class="mb-3.5">
                                <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.72rem;">
                                    <span class="text-muted fw-semibold">Kepatuhan Standar Mutu</span>
                                    <span class="fw-bold text-dark"><?= $fakSesuai ?> / <?= $fakTotalDocs ?> Sesuai</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px; background-color: #F1F5F9;">
                                    <div class="progress-bar rounded-pill <?= $progressBarClass ?>" 
                                         role="progressbar" 
                                         style="width: <?= $compliancePct ?>%" 
                                         aria-valuenow="<?= $compliancePct ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100"></div>
                                </div>
                            </div>

                            <!-- Mini Status Badges dengan Jarak yang Longgar -->
                            <div class="d-flex flex-wrap align-items-center mb-3" style="gap: 8px;">
                                <?php if ($fakPerluReview > 0): ?>
                                    <span class="badge rounded-pill px-2.5 py-1.5" style="background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; font-size: 0.72rem; font-weight: 700;">
                                        <i class="fas fa-clock text-warning me-1"></i> <?= $fakPerluReview ?> Review
                                    </span>
                                <?php endif; ?>
                                <?php if ($fakRevisi > 0): ?>
                                    <span class="badge rounded-pill px-2.5 py-1.5" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; font-size: 0.72rem; font-weight: 700;">
                                        <i class="fas fa-triangle-exclamation text-danger me-1"></i> <?= $fakRevisi ?> Revisi
                                    </span>
                                <?php endif; ?>
                                <?php if ($fakSesuai > 0): ?>
                                    <span class="badge rounded-pill px-2.5 py-1.5" style="background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; font-size: 0.72rem; font-weight: 700;">
                                        <i class="fas fa-circle-check text-success me-1"></i> <?= $fakSesuai ?> Sesuai
                                    </span>
                                <?php endif; ?>
                                <?php if ($fakTotalDocs == 0): ?>
                                    <span class="badge rounded-pill px-2.5 py-1.5 bg-light text-muted border" style="font-size: 0.72rem;">
                                        <i class="fas fa-inbox me-1 opacity-50"></i> Belum Ada Dokumen
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Footer dengan Border Bersih & Padding Nyaman -->
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-3 border-top mt-auto" style="border-color: #F1F5F9 !important;">
                            <a href="<?= base_url('admin/review-fakultas/' . $fId) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.75rem;">
                                <i class="fas fa-landmark me-1"></i> Dokumen Dekanat
                            </a>
                            <a href="<?= base_url('admin/review') ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.75rem;">
                                <span>Review</span>
                                <i class="fas fa-arrow-right small"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- ======================================================= -->
<!-- DASHBOARD MODAL 1: QUICK APPROVE                        -->
<!-- ======================================================= -->
<div class="modal fade" id="dashQuickApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('admin/review-dokumen/submit') ?>" method="POST">
                <input type="hidden" name="document_id" id="dashApproveDocId" value="">
                <input type="hidden" name="status_review" value="sesuai">
                <input type="hidden" name="redirect_to" value="admin/dashboard">

                <div class="modal-header py-3.5 px-4 bg-white border-bottom" style="border-color: #F1F5F9 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark-blue fs-6 mb-0">
                                Setujui Dokumen Mutu
                            </h5>
                            <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.78rem;">
                                Dokumen ini akan diverifikasi memenuhi standar mutu PPEPP.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Dokumen Info Card -->
                    <div class="p-3.5 rounded-3 mb-4 border" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 bg-white border shadow-2xs text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                                <i class="fas fa-file-circle-check"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Dokumen Yang Diverifikasi</span>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.68rem;">Status: Disetujui</span>
                                </div>
                                <div class="fw-bold text-dark-blue fs-6 mb-0" id="dashApproveDocTitle" style="line-height: 1.45;">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="dashApproveCatatan" class="form-label fw-bold text-dark-blue small mb-1.5" style="font-size: 0.82rem;">
                            Catatan Verifikator LPM <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <input type="text" class="form-control rounded-3 p-2.5 text-dark" id="dashApproveCatatan" name="catatan_review" 
                               value="Dokumen telah diverifikasi dan disetujui sesuai standar mutu."
                               style="font-size: 0.85rem; border-color: #CBD5E1;">
                        <div class="form-text mt-1 text-muted" style="font-size: 0.72rem;">
                            Catatan akan tersimpan di riwayat review dan dapat dibaca oleh unit kerja pengunggah.
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 bg-light bg-opacity-60 border-top d-flex justify-content-between align-items-center" style="border-color: #F1F5F9 !important;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.82rem;">
                        <i class="fas fa-circle-check"></i> Ya, Setujui Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- DASHBOARD MODAL 2: QUICK REVISION                       -->
<!-- ======================================================= -->
<div class="modal fade" id="dashQuickRevisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('admin/review-dokumen/submit') ?>" method="POST">
                <input type="hidden" name="document_id" id="dashRevDocId" value="">
                <input type="hidden" name="status_review" value="perlu_perbaikan">
                <input type="hidden" name="redirect_to" value="admin/dashboard">

                <div class="modal-header py-3.5 px-4 bg-white border-bottom" style="border-color: #F1F5F9 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark-blue fs-6 mb-0">
                                Minta Perbaikan / Revisi Dokumen
                            </h5>
                            <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.78rem;">
                                Berikan instruksi revisi yang jelas untuk unit pengunggah.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Dokumen Yang Direvisi Info Card -->
                    <div class="p-3.5 rounded-3 mb-4 border" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 bg-white border shadow-2xs text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                                <i class="fas fa-file-lines"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Dokumen Yang Direvisi</span>
                                    <span class="badge rounded-pill" style="background: #FEE2E2 !important; color: #991B1B !important; border: 1px solid #FCA5A5 !important; font-size: 0.72rem; font-weight: 700; padding: 0.35em 0.75em;">Status: Perlu Revisi</span>
                                </div>
                                <div class="fw-bold text-dark-blue fs-6 mb-0" id="dashRevDocTitle" style="line-height: 1.45;">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan / Arahan Input Form -->
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="dashRevCatatan" class="form-label fw-bold text-dark-blue small mb-0" style="font-size: 0.82rem;">
                                Catatan &amp; Poin-Poin Perbaikan <span class="text-danger">*</span>
                            </label>
                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.68rem;">Wajib Diisi</span>
                        </div>
                        <textarea class="form-control rounded-3 p-3 text-dark" id="dashRevCatatan" name="catatan_review" rows="5" 
                                  style="font-size: 0.86rem; line-height: 1.6; border-color: #CBD5E1; resize: vertical;"
                                  placeholder="Tuliskan catatan perbaikan secara spesifik... Contoh: Nomor SK belum tercantum, harap lengkapi tanda tangan pengesahan pada lembar akhir." required></textarea>
                        <div class="d-flex align-items-center gap-2 mt-2.5 p-2.5 rounded-2 bg-light border text-muted" style="font-size: 0.74rem;">
                            <i class="fas fa-circle-info text-primary flex-shrink-0"></i>
                            <span>Catatan ini akan otomatis muncul sebagai instruksi revisi di dashboard unit pengunggah saat memperbarui dokumen.</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 bg-light bg-opacity-60 border-top d-flex justify-content-between align-items-center" style="border-color: #F1F5F9 !important;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.82rem;">
                        <i class="fas fa-paper-plane"></i> Kirim Catatan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- DASHBOARD MODAL 3: PDF PREVIEW                          -->
<!-- ======================================================= -->
<div class="modal fade" id="dashPdfPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90vw; height: 90vh;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden h-100 d-flex flex-column">
            <div class="modal-header bg-dark-blue text-white py-3 px-4 flex-shrink-0">
                <div class="d-flex align-items-center gap-2 text-truncate me-3">
                    <i class="fas fa-file-pdf text-danger fs-5"></i>
                    <h6 class="modal-title fw-bold text-truncate mb-0" id="dashPdfTitle">Pratinjau Dokumen</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 flex-grow-1 bg-light position-relative">
                <iframe id="dashPdfViewerIframe" src="about:blank" class="w-100 h-100 border-0" style="min-height: 500px;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const approveModal = document.getElementById('dashQuickApproveModal');
    if (approveModal) {
        approveModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            document.getElementById('dashApproveDocId').value = btn.getAttribute('data-id') || '';
            document.getElementById('dashApproveDocTitle').textContent = btn.getAttribute('data-title') || '-';
        });
    }

    const revModal = document.getElementById('dashQuickRevisionModal');
    if (revModal) {
        revModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            document.getElementById('dashRevDocId').value = btn.getAttribute('data-id') || '';
            document.getElementById('dashRevDocTitle').textContent = btn.getAttribute('data-title') || '-';
            document.getElementById('dashRevCatatan').value = btn.getAttribute('data-notes') || '';
        });
    }

    const pdfModal = document.getElementById('dashPdfPreviewModal');
    if (pdfModal) {
        pdfModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            document.getElementById('dashPdfTitle').textContent = btn.getAttribute('data-doc-title') || 'Pratinjau Dokumen';
            document.getElementById('dashPdfViewerIframe').src = btn.getAttribute('data-pdf-url') || '';
        });
        pdfModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('dashPdfViewerIframe').src = 'about:blank';
        });
    }
});
</script>

<style>
.faculty-matrix-card {
    transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
}
.faculty-matrix-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(30, 58, 138, 0.08) !important;
    border-color: #CBD5E1 !important;
}
.gap-1\.5 {
    gap: 0.375rem !important;
}
</style>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
