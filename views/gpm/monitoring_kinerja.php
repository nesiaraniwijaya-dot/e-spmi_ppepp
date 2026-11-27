<?php
/**
 * View: GPM Monitoring Kinerja Prodi & Dekanat
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

function getCycleBadgeHtml(string $cycle, int $count): string {
    $map = [
        'penetapan'   => ['code' => 'P1', 'name' => 'Penetapan'],
        'pelaksanaan' => ['code' => 'P2', 'name' => 'Pelaksanaan'],
        'evaluasi'    => ['code' => 'E',  'name' => 'Evaluasi'],
        'pengendalian'=> ['code' => 'P3', 'name' => 'Pengendalian'],
        'peningkatan' => ['code' => 'P4', 'name' => 'Peningkatan'],
    ];
    $info = $map[$cycle] ?? ['code' => strtoupper(substr($cycle, 0, 2)), 'name' => ucfirst($cycle)];
    if ($count > 0) {
        return '<span class="badge rounded-pill px-2 py-1 shadow-2xs text-white d-inline-flex align-items-center gap-1" style="background:#059669; font-size:0.7rem;" title="' . htmlspecialchars($info['name']) . ': ' . $count . ' Dokumen">' .
               '<i class="fas fa-check-circle"></i> ' . $info['code'] . ' (' . $count . ')</span>';
    } else {
        return '<span class="badge rounded-pill px-2 py-1 shadow-2xs d-inline-flex align-items-center gap-1" style="background:#FEE2E2; color:#DC2626; border:1px dashed #F87171; font-size:0.7rem;" title="' . htmlspecialchars($info['name']) . ': Belum ada dokumen!">' .
               '<i class="fas fa-circle-exclamation"></i> ' . $info['code'] . ' (0)</span>';
    }
}
?>

<div class="container-fluid p-0">
    <!-- Header Page & Filter Tahun Akademik -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div class="flex-grow-1 min-w-0" style="max-width: 720px;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill small fw-bold mb-2" style="background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;">
                <i class="fas fa-award text-purple"></i>
                <span>Gugus Penjaminan Mutu &bull; <?= htmlspecialchars($fakultas['nama_fakultas']) ?></span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Monitoring Kinerja Dekanat &amp; Program Studi</h3>
            <p class="text-muted small mb-0">Evaluasi kepatuhan mutu SPMI PPEPP untuk unit Dekanat dan seluruh Program Studi di bawah naungan Fakultas <?= htmlspecialchars($fakultas['nama_fakultas']) ?>. Rapor kinerja dapat diteruskan resmi ke masing-masing unit.</p>
        </div>
        
        <!-- Filter Tahun Akademik -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0 bg-white p-2.5 rounded-4 shadow-sm border">
            <span class="small fw-bold text-dark-blue ps-1 text-nowrap">
                <i class="far fa-calendar-alt text-primary me-1"></i> Tahun Akademik:
            </span>
            <form action="<?= base_url('gpm/monitoring-kinerja') ?>" method="GET" class="d-flex align-items-center gap-2 mb-0">
                <select name="tahun_akademik" class="form-select form-select-sm rounded-pill fw-semibold border-primary border-opacity-50" onchange="this.form.submit()" style="min-width: 140px;">
                    <option value="Semua" <?= $tahunAkademik === 'Semua' ? 'selected' : '' ?>>Semua Periode</option>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= htmlspecialchars($ay) ?>" <?= $tahunAkademik === $ay ? 'selected' : '' ?>>TA <?= htmlspecialchars($ay) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 44px; height: 44px; background: #3B82F6;">
                        <i class="fas fa-building-columns fa-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Total Unit Dipantau</div>
                        <div class="fs-4 fw-bold text-dark-blue"><?= $totalUnits ?> Unit</div>
                        <div class="text-muted small" style="font-size: 0.7rem;">1 Dekanat &bull; <?= count($prodiPerfs) ?> Prodi</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 44px; height: 44px; background: #10B981;">
                        <i class="fas fa-gauge-high fa-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Rata-Rata Skor Mutu</div>
                        <div class="fs-4 fw-bold text-success"><?= $avgScore ?> <span class="fs-6 text-muted">/ 100</span></div>
                        <div class="text-muted small" style="font-size: 0.7rem;"><?= $predikatCounts['Sangat Baik'] ?> Sangat Baik &bull; <?= $predikatCounts['Baik'] ?> Baik</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 44px; height: 44px; background: #EF4444;">
                        <i class="fas fa-triangle-exclamation fa-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Siklus Belum Lengkap</div>
                        <div class="fs-4 fw-bold text-danger"><?= $unitsWithEmptyCycles ?> Unit</div>
                        <div class="text-muted small" style="font-size: 0.7rem;">Perlu melengkapi dokumen siklus</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 44px; height: 44px; background: #8B5CF6;">
                        <i class="fas fa-paper-plane fa-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Rapor Diteruskan</div>
                        <div class="fs-4 fw-bold text-purple"><?= $totalForwarded ?> / <?= $totalUnits ?></div>
                        <div class="text-muted small" style="font-size: 0.7rem;">Siap diunduh oleh unit</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation: Dekanat vs Program Studi -->
    <ul class="nav nav-pills custom-pills mb-4 gap-2" id="kinerjaTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2 shadow-2xs" 
                    id="dekanat-tab" data-bs-toggle="pill" data-bs-target="#tab-dekanat" type="button" role="tab">
                <i class="fas fa-landmark"></i>
                <span>Tingkat Dekanat (Fakultas)</span>
                <span class="badge bg-white text-primary rounded-pill px-2 py-0.5" style="font-size:0.68rem;"><?= $fakultasPerf['skor_total'] ?> Poin</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2 shadow-2xs" 
                    id="prodi-tab" data-bs-toggle="pill" data-bs-target="#tab-prodi" type="button" role="tab">
                <i class="fas fa-graduation-cap"></i>
                <span>Program Studi (<?= count($prodiPerfs) ?> Prodi)</span>
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="kinerjaTabContent">
        <!-- ========================================== -->
        <!-- TAB 1: KINERJA DEKANAT (FAKULTAS)          -->
        <!-- ========================================== -->
        <div class="tab-pane fade show active" id="tab-dekanat" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 pb-3 mb-3 border-bottom">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fw-bold" style="font-size:0.72rem;">
                                <i class="fas fa-landmark me-1"></i> Dekanat Fakultas
                            </span>
                            <?= $fakultasPerf['predikat_badge'] ?>
                            <?php if ($fakultasPerf['is_forwarded']): ?>
                                <span class="badge rounded-pill px-2.5 py-1 text-white shadow-2xs" style="background:#059669; font-size:0.72rem;">
                                    <i class="fas fa-check-circle me-1"></i> Rapor Telah Diteruskan ke Dekanat
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2.5 py-1" style="font-size:0.72rem;">
                                    <i class="fas fa-clock me-1"></i> Rapor Belum Diteruskan
                                </span>
                            <?php endif; ?>
                        </div>
                        <h4 class="fw-bold text-dark-blue mb-1">Dekanat Fakultas <?= htmlspecialchars($fakultas['nama_fakultas']) ?></h4>
                        <div class="text-muted small">
                            <span>Dekan: <strong><?= htmlspecialchars($fakultasPerf['pimpinan_name']) ?></strong></span>
                            <span class="mx-1">&bull;</span>
                            <span>Periode: <strong>TA <?= htmlspecialchars($fakultasPerf['tahun_akademik']) ?></strong></span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                data-bs-toggle="modal" data-bs-target="#modalEvaluasiGpm"
                                data-target-type="fakultas"
                                data-target-id="<?= $fakultasPerf['target_id'] ?>"
                                data-unit-name="<?= htmlspecialchars($fakultasPerf['unit_name'], ENT_QUOTES) ?>"
                                data-skor="<?= $fakultasPerf['skor_total'] ?>"
                                data-predikat="<?= htmlspecialchars($fakultasPerf['predikat'], ENT_QUOTES) ?>"
                                data-ta="<?= htmlspecialchars($fakultasPerf['tahun_akademik'], ENT_QUOTES) ?>"
                                data-catatan-gpm="<?= htmlspecialchars($fakultasPerf['catatan_gpm'] ?? '', ENT_QUOTES) ?>"
                                data-catatan-lpm="<?= htmlspecialchars($fakultasPerf['catatan_lpm'] ?? '', ENT_QUOTES) ?>">
                            <i class="fas fa-pen-to-square"></i>
                            <span>Beri Arahan &amp; Teruskan</span>
                        </button>
                        <a href="<?= base_url('gpm/monitoring-kinerja/cetak/fakultas/' . $fakultasPerf['target_id'] . '?tahun_akademik=' . urlencode($fakultasPerf['tahun_akademik'])) ?>" 
                           target="_blank" class="btn btn-primary bg-scu-blue border-0 btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs text-white">
                            <i class="fas fa-file-pdf"></i>
                            <span>Cetak Softfile Rapor</span>
                        </a>
                    </div>
                </div>

                <div class="row g-4 mb-3">
                    <!-- Skor Besar & Progress -->
                    <div class="col-lg-3 col-md-4 text-center border-end">
                        <div class="p-3">
                            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size:0.75rem;">Skor Mutu Dekanat</div>
                            <div class="display-4 fw-bold text-primary mb-1"><?= $fakultasPerf['skor_total'] ?></div>
                            <div class="mb-3"><?= $fakultasPerf['predikat_badge'] ?></div>
                            <div class="text-muted small" style="font-size:0.75rem;">Dihitung dari 4 pilar kepatuhan SPMI PPEPP</div>
                        </div>
                    </div>

                    <!-- 4 Pilar Rincian -->
                    <div class="col-lg-9 col-md-8">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light border h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold text-dark">1. Kelengkapan 5 Siklus PPEPP (35%)</span>
                                        <span class="fw-bold text-primary"><?= $fakultasPerf['score_pilar1'] ?> Poin</span>
                                    </div>
                                    <div class="progress mb-2" style="height:6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $fakultasPerf['score_pilar1'] ?>%"></div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <?php foreach (['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'] as $cyc): ?>
                                            <?= getCycleBadgeHtml($cyc, $fakultasPerf['cycle_counts'][$cyc] ?? 0) ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (!empty($fakultasPerf['siklus_kosong'])): ?>
                                        <div class="text-danger small mt-2 d-flex align-items-center gap-1" style="font-size:0.72rem;">
                                            <i class="fas fa-circle-exclamation"></i>
                                            <span>Siklus Kosong: <?= implode(', ', array_map('ucfirst', $fakultasPerf['siklus_kosong'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light border h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold text-dark">2. Kualitas &amp; Lolos Review (30%)</span>
                                        <span class="fw-bold text-success"><?= $fakultasPerf['score_pilar2'] ?> Poin</span>
                                    </div>
                                    <div class="progress mb-2" style="height:6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $fakultasPerf['score_pilar2'] ?>%"></div>
                                    </div>
                                    <div class="small text-muted" style="font-size:0.74rem;">
                                        <span>Total Dokumen: <strong><?= $fakultasPerf['total_docs'] ?></strong></span> &bull; 
                                        <span>Disetujui: <strong class="text-success"><?= $fakultasPerf['sesuai_count'] ?></strong></span> &bull;
                                        <span>Lolos Langsung: <strong><?= $fakultasPerf['first_pass_count'] ?></strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light border h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold text-dark">3. Ketuntasan Tindak Lanjut Revisi (20%)</span>
                                        <span class="fw-bold text-warning-emphasis"><?= $fakultasPerf['score_pilar3'] ?> Poin</span>
                                    </div>
                                    <div class="progress mb-2" style="height:6px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $fakultasPerf['score_pilar3'] ?>%"></div>
                                    </div>
                                    <div class="small text-muted" style="font-size:0.74rem;">
                                        <span>Revisi Selesai: <strong class="text-success"><?= $fakultasPerf['sudah_diperbaiki_count'] ?></strong></span> &bull;
                                        <span>Revisi Menunggak: <strong class="text-danger"><?= $fakultasPerf['revisi_macet_count'] ?></strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light border h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold text-dark">4. Cakupan Standar Mutu SPMI (15%)</span>
                                        <span class="fw-bold text-info"><?= $fakultasPerf['score_pilar4'] ?> Poin</span>
                                    </div>
                                    <div class="progress mb-2" style="height:6px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= $fakultasPerf['score_pilar4'] ?>%"></div>
                                    </div>
                                    <div class="small text-muted" style="font-size:0.74rem;">
                                        <span>Terisi: <strong><?= $fakultasPerf['distinct_bidang'] ?> / 9 Bidang Standar SPMI</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Arahan Pembinaan Terlampir -->
                <?php if (!empty($fakultasPerf['catatan_lpm']) || !empty($fakultasPerf['catatan_gpm'])): ?>
                    <div class="pt-3 border-top mt-2">
                        <div class="row g-3">
                            <?php if (!empty($fakultasPerf['catatan_lpm'])): ?>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 border border-warning-subtle" style="background:#FFFDF0;">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-0.5 fw-bold" style="font-size:0.68rem;">
                                                <i class="fas fa-building-columns me-1"></i> Pesan dari Tim LPM Universitas
                                            </span>
                                            <span class="text-muted small" style="font-size:0.7rem;">Khusus GPM</span>
                                        </div>
                                        <div class="text-dark small" style="white-space: pre-wrap; line-height:1.5; font-size:0.82rem;">
                                            <?= nl2br(htmlspecialchars($fakultasPerf['catatan_lpm'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($fakultasPerf['catatan_gpm'])): ?>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 border border-purple-subtle" style="background:#FAF5FF;">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                            <span class="badge bg-purple text-white rounded-pill px-2.5 py-0.5 fw-bold" style="font-size:0.68rem;">
                                                <i class="fas fa-shield-halved me-1"></i> Arahan Pembinaan Resmi GPM Fakultas
                                            </span>
                                            <span class="text-muted small" style="font-size:0.7rem;">Diteruskan ke Dekanat</span>
                                        </div>
                                        <div class="text-dark small" style="white-space: pre-wrap; line-height:1.5; font-size:0.82rem;">
                                            <?= nl2br(htmlspecialchars($fakultasPerf['catatan_gpm'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: KINERJA PROGRAM STUDI               -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-prodi" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                    <div>
                        <h5 class="fw-bold text-dark-blue mb-1">Pemantauan Kinerja Seluruh Program Studi</h5>
                        <p class="text-muted small mb-0">Rincian capaian mutu dan pemenuhan siklus PPEPP setiap program studi di Fakultas <?= htmlspecialchars($fakultas['nama_fakultas']) ?>.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <input type="text" class="form-control form-control-sm rounded-pill px-3" id="searchProdiInput" placeholder="Cari nama prodi..." oninput="filterProdiCards()" style="max-width:240px;">
                    </div>
                </div>

                <div class="row g-3" id="prodiCardsContainer">
                    <?php foreach ($prodiPerfs as $p): ?>
                        <div class="col-lg-6 prodi-card-item" data-name="<?= strtolower(htmlspecialchars($p['unit_name'])) ?>">
                            <div class="card border rounded-4 p-3.5 h-100 shadow-2xs hover-shadow transition-all" style="background:#FFFFFF;">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2 pb-2 border-bottom">
                                    <div class="min-w-0">
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                                            <span class="badge rounded-pill px-2.5 py-0.5 fw-bold" style="background:#E0F2FE; color:#0369A1; font-size:0.68rem;">
                                                <i class="fas fa-graduation-cap me-1"></i> Prodi
                                            </span>
                                            <?= $p['predikat_badge'] ?>
                                            <?php if ($p['is_forwarded']): ?>
                                                <span class="badge rounded-pill px-2 py-0.5 text-white" style="background:#059669; font-size:0.65rem;" title="Rapor telah diteruskan ke prodi">
                                                    <i class="fas fa-paper-plane me-0.5"></i> Diteruskan
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold text-dark-blue mb-0.5 text-truncate"><?= htmlspecialchars($p['unit_name']) ?></h6>
                                        <div class="text-muted small" style="font-size:0.72rem;">Kaprodi: <strong><?= htmlspecialchars($p['pimpinan_name']) ?></strong></div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="fs-4 fw-bold text-primary mb-0"><?= $p['skor_total'] ?></div>
                                        <div class="text-muted small" style="font-size:0.68rem;">Skor Mutu</div>
                                    </div>
                                </div>

                                <!-- 5 Siklus Badges Mini -->
                                <div class="mb-2">
                                    <div class="text-muted small fw-semibold mb-1" style="font-size:0.7rem;">Capaian 5 Siklus PPEPP:</div>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <?php foreach (['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'] as $cyc): ?>
                                            <?= getCycleBadgeHtml($cyc, $p['cycle_counts'][$cyc] ?? 0) ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (!empty($p['siklus_kosong'])): ?>
                                        <div class="text-danger small mt-1.5 d-flex align-items-center gap-1" style="font-size:0.7rem;">
                                            <i class="fas fa-circle-exclamation"></i>
                                            <span>Belum ada dokumen: <strong><?= implode(', ', array_map('ucfirst', $p['siklus_kosong'])) ?></strong></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Mini Metrics Breakdown -->
                                <div class="row g-2 p-2 rounded-3 bg-light small mb-3 text-muted" style="font-size:0.72rem;">
                                    <div class="col-4">
                                        <div>Total Dokumen:</div>
                                        <strong class="text-dark"><?= $p['total_docs'] ?> Berkas</strong>
                                    </div>
                                    <div class="col-4">
                                        <div>Disetujui:</div>
                                        <strong class="text-success"><?= $p['sesuai_count'] ?> (<?= $p['approval_rate'] ?>%)</strong>
                                    </div>
                                    <div class="col-4">
                                        <div>Revisi Menunggak:</div>
                                        <strong class="<?= $p['revisi_macet_count'] > 0 ? 'text-danger' : 'text-dark' ?>"><?= $p['revisi_macet_count'] ?></strong>
                                    </div>
                                </div>

                                <!-- Catatan Terakhir GPM jika ada -->
                                <?php if (!empty($p['catatan_gpm'])): ?>
                                    <div class="p-2 rounded-2 border border-purple-subtle small mb-3" style="background:#FAF5FF; font-size:0.75rem;">
                                        <div class="fw-bold text-purple mb-0.5"><i class="fas fa-comment-dots me-1"></i> Catatan GPM:</div>
                                        <div class="text-dark text-truncate"><?= htmlspecialchars($p['catatan_gpm']) ?></div>
                                    </div>
                                <?php endif; ?>

                                <!-- Actions -->
                                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-2 border-top">
                                    <button type="button" class="btn btn-outline-primary btn-xs rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs"
                                            data-bs-toggle="modal" data-bs-target="#modalEvaluasiGpm"
                                            data-target-type="prodi"
                                            data-target-id="<?= $p['target_id'] ?>"
                                            data-unit-name="<?= htmlspecialchars($p['unit_name'], ENT_QUOTES) ?>"
                                            data-skor="<?= $p['skor_total'] ?>"
                                            data-predikat="<?= htmlspecialchars($p['predikat'], ENT_QUOTES) ?>"
                                            data-ta="<?= htmlspecialchars($p['tahun_akademik'], ENT_QUOTES) ?>"
                                            data-catatan-gpm="<?= htmlspecialchars($p['catatan_gpm'] ?? '', ENT_QUOTES) ?>"
                                            data-catatan-lpm="<?= htmlspecialchars($p['catatan_lpm'] ?? '', ENT_QUOTES) ?>">
                                        <i class="fas fa-pen-to-square"></i>
                                        <span>Arahan &amp; Teruskan</span>
                                    </button>

                                    <a href="<?= base_url('gpm/monitoring-kinerja/cetak/prodi/' . $p['target_id'] . '?tahun_akademik=' . urlencode($p['tahun_akademik'])) ?>" 
                                       target="_blank" class="btn btn-light border btn-xs rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs text-secondary">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                        <span>Softfile Rapor</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: INPUT CATATAN PEMBINAAN & TERUSKAN RAPOR OLEH GPM -->
<!-- ======================================================== -->
<div class="modal fade" id="modalEvaluasiGpm" tabindex="-1" aria-labelledby="modalEvaluasiGpmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('gpm/monitoring-kinerja/save') ?>" method="POST" id="formEvaluasiGpm">
                <input type="hidden" name="target_type" id="modalTargetType" value="">
                <input type="hidden" name="target_id" id="modalTargetId" value="">
                <input type="hidden" name="tahun_akademik" id="modalTahunAkademik" value="">
                <input type="hidden" name="action_type" id="modalActionType" value="save">

                <div class="modal-header border-0 py-3.5 px-4 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-3 bg-white bg-opacity-20 text-white flex-shrink-0">
                            <i class="fas fa-award fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0.5" id="modalEvaluasiGpmLabel">Evaluasi &amp; Arahan Kinerja Unit</h5>
                            <p class="text-white text-opacity-75 small mb-0">Gugus Penjaminan Mutu (GPM) Fakultas <?= htmlspecialchars($fakultas['nama_fakultas']) ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-3.5 bg-light">
                    <!-- Unit Meta Summary -->
                    <div class="card border rounded-3 p-3 bg-white shadow-2xs mb-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="text-muted small" style="font-size:0.72rem; text-transform:uppercase; font-weight:700;">Unit Kerja Sasaran</div>
                                <h6 class="fw-bold text-dark-blue mb-0" id="modalUnitName">-</h6>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold fs-6" id="modalSkorBadge">-</span>
                                <div class="text-muted small mt-0.5" id="modalPredikatText">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Pesan LPM jika ada -->
                    <div id="modalLpmBoxWrap" class="mb-3" style="display:none;">
                        <div class="p-3 rounded-3 border border-warning-subtle" style="background:#FFFDF0;">
                            <div class="fw-bold text-warning-emphasis small mb-1 d-flex align-items-center gap-1.5">
                                <i class="fas fa-building-columns"></i>
                                <span>Catatan / Masukan dari Tim LPM Universitas:</span>
                            </div>
                            <div class="text-dark small" id="modalLpmText" style="white-space: pre-wrap; line-height:1.5;">-</div>
                        </div>
                    </div>

                    <!-- Form Catatan Pembinaan GPM -->
                    <div class="mb-3">
                        <label for="catatan_gpm" class="form-label fw-bold text-dark-blue d-flex align-items-center gap-1.5 mb-1.5" style="font-size:0.86rem;">
                            <i class="fas fa-pen-to-square text-primary"></i>
                            <span>Catatan &amp; Arahan Pembinaan GPM untuk Unit:</span>
                        </label>
                        <textarea name="catatan_gpm" id="modalCatatanGpm" rows="4" class="form-control rounded-3" 
                                  placeholder="Contoh: Unit diharapkan segera melengkapi dokumen siklus Evaluasi (hasil AMI) dan Pengendalian (RTM) sebelum batas akhir periode..."></textarea>
                        <div class="form-text text-muted small" style="font-size:0.72rem;">
                            <i class="fas fa-info-circle me-1"></i> Catatan ini akan tertera resmi di softfile Rapor Kinerja dan muncul di akun pengurus unit (Dekanat/Kaprodi) ketika rapor resmi diteruskan.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2.5 px-4 bg-white d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                    
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" onclick="document.getElementById('modalActionType').value='save'" class="btn btn-light border btn-sm rounded-pill px-3.5 py-2 fw-semibold shadow-2xs">
                            <i class="fas fa-floppy-disk me-1 text-primary"></i> Simpan Catatan Saja
                        </button>
                        <button type="submit" onclick="document.getElementById('modalActionType').value='forward'" class="btn btn-primary bg-scu-blue border-0 btn-sm rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                            <i class="fas fa-paper-plane"></i>
                            <span>Simpan &amp; Teruskan Rapor ke Unit</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const evalModal = document.getElementById('modalEvaluasiGpm');
    if (evalModal) {
        evalModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            const targetType = btn.getAttribute('data-target-type') || 'prodi';
            const targetId = btn.getAttribute('data-target-id') || '0';
            const unitName = btn.getAttribute('data-unit-name') || '-';
            const skor = btn.getAttribute('data-skor') || '0';
            const predikat = btn.getAttribute('data-predikat') || '-';
            const ta = btn.getAttribute('data-ta') || 'Semua';
            const catatanGpm = btn.getAttribute('data-catatan-gpm') || '';
            const catatanLpm = btn.getAttribute('data-catatan-lpm') || '';

            document.getElementById('modalTargetType').value = targetType;
            document.getElementById('modalTargetId').value = targetId;
            document.getElementById('modalTahunAkademik').value = ta;
            document.getElementById('modalUnitName').textContent = unitName;
            document.getElementById('modalSkorBadge').textContent = skor + ' Poin';
            document.getElementById('modalPredikatText').textContent = 'Predikat: ' + predikat + ' (TA ' + ta + ')';
            document.getElementById('modalCatatanGpm').value = catatanGpm;

            const lpmBox = document.getElementById('modalLpmBoxWrap');
            const lpmText = document.getElementById('modalLpmText');
            if (catatanLpm.trim()) {
                lpmText.textContent = catatanLpm;
                lpmBox.style.display = 'block';
            } else {
                lpmBox.style.display = 'none';
            }
        });
    }
});

function filterProdiCards() {
    const q = (document.getElementById('searchProdiInput').value || '').toLowerCase().trim();
    document.querySelectorAll('.prodi-card-item').forEach(el => {
        const name = el.getAttribute('data-name') || '';
        if (!q || name.includes(q)) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    });
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
