<?php
/**
 * View: Rapor Kinerja Mutu SPMI Program Studi
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$cycleLabels = [
    'penetapan'    => 'Penetapan (P1)',
    'pelaksanaan'  => 'Pelaksanaan (P2)',
    'evaluasi'     => 'Evaluasi (P3)',
    'pengendalian' => 'Pengendalian (P4)',
    'peningkatan'  => 'Peningkatan (P5)'
];
?>

<div class="container-fluid p-0">
    <!-- Header & Filter Periode -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div class="flex-grow-1 min-w-0" style="max-width: 750px;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill small fw-bold mb-2" style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A;">
                <i class="fas fa-award text-warning"></i>
                <span>Rapor Kinerja SPMI &bull; <?= htmlspecialchars($prodi['nama_prodi']) ?></span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Rapor Kinerja Mutu SPMI Program Studi</h3>
            <p class="text-muted small mb-0">Hasil pemantauan kepatuhan implementasi siklus mutu PPEPP tingkat Program Studi yang dievaluasi oleh Gugus Penjaminan Mutu (GPM) Fakultas dan Lembaga Penjaminan Mutu (LPM) Universitas.</p>
        </div>

        <!-- Filter Tahun Akademik -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0 bg-white p-2.5 rounded-4 shadow-sm border">
            <span class="small fw-bold text-dark-blue ps-1 text-nowrap">
                <i class="far fa-calendar-alt text-primary me-1"></i> Tahun Akademik:
            </span>
            <form action="<?= base_url('prodi/rapor-kinerja') ?>" method="GET" class="d-flex align-items-center gap-2 mb-0">
                <select name="tahun_akademik" class="form-select form-select-sm rounded-pill fw-semibold border-primary border-opacity-50" onchange="this.form.submit()" style="min-width: 140px;">
                    <option value="Semua" <?= $tahunAkademik === 'Semua' ? 'selected' : '' ?>>Semua Periode</option>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= htmlspecialchars($ay) ?>" <?= $tahunAkademik === $ay ? 'selected' : '' ?>>TA <?= htmlspecialchars($ay) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <!-- Status Banner Penerusan Rapor -->
    <?php if ($perf['is_forwarded']): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                    <i class="fas fa-check-double fa-lg"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-success">Rapor Kinerja Mutu Resmi Diterbitkan &amp; Diteruskan oleh GPM</h6>
                    <small class="text-muted">
                        Diteruskan pada <?= date('d F Y, H:i', strtotime($perf['forwarded_at'])) ?> WIB oleh <strong><?= htmlspecialchars($perf['forwarded_by_name'] ?? 'Ketua GPM') ?></strong>. Dokumen ini sah dan dapat diunduh sebagai softfile resmi.
                    </small>
                </div>
            </div>
            <div class="flex-shrink-0">
                <a href="<?= base_url('prodi/rapor-kinerja/cetak?tahun_akademik=' . urlencode($tahunAkademik)) ?>" target="_blank" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fas fa-file-pdf me-1"></i> Unduh Softfile Rapor (PDF)
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="fas fa-clock fa-lg"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark">Rapor Periode Ini Masih Dalam Tahap Peninjauan GPM</h6>
                <small class="text-muted">
                    Gugus Penjaminan Mutu (GPM) Fakultas sedang meninjau dokumen dan menyusun catatan pembinaan. Hasil resmi dan hak unduh softfile akan aktif otomatis segera setelah GPM memforward rapor ke sistem Program Studi. Di bawah ini ditampilkan kalkulasi sementara untuk kebutuhan pembenahan unit.
                </small>
            </div>
        </div>
    <?php endif; ?>

    <!-- Peringatan Siklus yang Masih Kosong -->
    <?php if (!empty($perf['siklus_kosong'])): ?>
        <div class="card border-danger border-opacity-50 shadow-sm rounded-4 p-3 mb-4" style="background-color: #FEF2F2;">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-3 bg-danger text-white p-2 flex-shrink-0">
                    <i class="fas fa-triangle-exclamation fa-lg"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-danger mb-1">
                        Peringatan: Terdapat <?= count($perf['siklus_kosong']) ?> Siklus Mutu yang Masih Kosong (0 Dokumen)
                    </h6>
                    <p class="text-danger-emphasis small mb-2">
                        Agar siklus penjaminan mutu SPMI PPEPP Program Studi berjalan utuh dan mencapai predikat optimal, segera lengkapi dokumen pada siklus:
                        <strong class="text-decoration-underline">
                            <?= implode(', ', array_map(function($c) use ($cycleLabels) { return $cycleLabels[$c] ?? ucfirst($c); }, $perf['siklus_kosong'])) ?>
                        </strong>.
                    </p>
                    <a href="<?= base_url('prodi/dokumen/create') ?>" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold">
                        <i class="fas fa-cloud-arrow-up me-1"></i> Unggah Dokumen untuk Siklus Kosong
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-success border-opacity-50 shadow-sm rounded-4 p-3 mb-4" style="background-color: #F0FDF4;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success text-white p-2 flex-shrink-0">
                    <i class="fas fa-circle-check fa-lg"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-success mb-0">Siklus PPEPP Utuh (100% Terpenuhi)</h6>
                    <small class="text-success-emphasis">Seluruh 5 siklus penjaminan mutu (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan) telah memiliki dokumen terunggah.</small>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Score & Predikat Big Overview Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="row align-items-center g-4">
            <div class="col-12 col-md-5 text-center text-md-start border-md-end pe-md-4">
                <div class="text-muted small text-uppercase fw-bold mb-1">Indeks Kinerja Mutu SPMI Prodi</div>
                <div class="d-flex align-items-baseline justify-content-center justify-content-md-start gap-2">
                    <span class="display-4 fw-bolder text-dark-blue"><?= number_format($perf['skor_total'], 1) ?></span>
                    <span class="fs-5 text-muted">/ 100</span>
                </div>
                <div class="my-2">
                    <?= $perf['predikat_badge'] ?>
                </div>
                <p class="text-muted small mb-0">Skor dihitung 100% otomatis berdasarkan data riil dokumen, ketuntasan 5 siklus PPEPP, kualitas persetujuan, dan ketuntasan revisi.</p>
            </div>

            <div class="col-12 col-md-7 ps-md-4">
                <div class="small fw-bold text-dark-blue text-uppercase mb-3">Rincian Nilai 4 Pilar Evaluasi</div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">1. Siklus PPEPP (35%)</span>
                                <strong class="small text-primary"><?= $perf['score_pilar1'] ?>%</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: <?= $perf['score_pilar1'] ?>%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">2. Kualitas Dokumen (30%)</span>
                                <strong class="small text-success"><?= $perf['score_pilar2'] ?>%</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: <?= $perf['score_pilar2'] ?>%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">3. Ketuntasan Revisi (20%)</span>
                                <strong class="small text-warning"><?= $perf['score_pilar3'] ?>%</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: <?= $perf['score_pilar3'] ?>%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">4. Standar Mutu (15%)</span>
                                <strong class="small text-info"><?= $perf['score_pilar4'] ?>%</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: <?= $perf['score_pilar4'] ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pemenuhan 5 Siklus Cards -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <h5 class="fw-bold text-dark-blue mb-3">
            <i class="fas fa-arrows-spin text-primary me-2"></i> Keterisian Dokumen pada 5 Siklus PPEPP
        </h5>
        <div class="row g-3">
            <?php foreach ($cycleLabels as $cycleKey => $cycleName): 
                $docCount = $perf['cycle_counts'][$cycleKey] ?? 0;
                $isFulfilled = ($docCount > 0);
            ?>
            <div class="col-12 col-sm-6 col-lg">
                <div class="card h-100 border rounded-4 p-3 <?= $isFulfilled ? 'border-success-subtle bg-success-subtle bg-opacity-25' : 'border-danger-subtle bg-danger-subtle bg-opacity-25' ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge <?= $isFulfilled ? 'bg-success' : 'bg-danger' ?> rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                            <?= $isFulfilled ? '<i class="fas fa-check me-1"></i> Terpenuhi' : '<i class="fas fa-times me-1"></i> Kosong' ?>
                        </span>
                        <span class="fs-5 fw-bold <?= $isFulfilled ? 'text-success' : 'text-danger' ?>"><?= $docCount ?></span>
                    </div>
                    <div class="fw-bold text-dark-blue" style="font-size: 0.9rem;"><?= $cycleName ?></div>
                    <small class="text-muted" style="font-size: 0.72rem;">
                        <?= $isFulfilled ? $docCount . ' dokumen mutu tersimpan' : 'Belum ada dokumen yang diunggah' ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Catatan Arahan Pembinaan dari GPM & LPM -->
    <div class="row g-4 mb-4">
        <!-- Arahan GPM Fakultas -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning">
                        <i class="fas fa-clipboard-user fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark-blue mb-0">Arahan Pembinaan GPM Fakultas</h6>
                        <small class="text-muted">Gugus Penjaminan Mutu &bull; <?= htmlspecialchars($prodi['nama_fakultas'] ?? 'Fakultas') ?></small>
                    </div>
                </div>
                <div class="p-3 rounded-3 bg-light border flex-grow-1">
                    <?php if (!empty($perf['catatan_gpm'])): ?>
                        <div class="text-dark small lh-base"><?= nl2br(htmlspecialchars($perf['catatan_gpm'])) ?></div>
                    <?php else: ?>
                        <em class="text-muted small">Belum ada arahan pembinaan tertulis dari GPM Fakultas untuk periode ini.</em>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Masukan LPM Universitas -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary">
                        <i class="fas fa-university fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark-blue mb-0">Masukan &amp; Catatan LPM Universitas</h6>
                        <small class="text-muted">Lembaga Penjaminan Mutu &bull; Tingkat Universitas</small>
                    </div>
                </div>
                <div class="p-3 rounded-3 bg-light border flex-grow-1">
                    <?php if (!empty($perf['catatan_lpm'])): ?>
                        <div class="text-dark small lh-base"><?= nl2br(htmlspecialchars($perf['catatan_lpm'])) ?></div>
                    <?php else: ?>
                        <em class="text-muted small">Belum ada catatan evaluasi khusus dari LPM Universitas untuk periode ini.</em>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/admin_footer.php'; ?>
