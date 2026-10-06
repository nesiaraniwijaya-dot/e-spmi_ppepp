<?php
/**
 * Kepala LPM: Executive Monitoring Dashboard
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-warning bg-opacity-10 text-warning small fw-bold mb-2">
                <i class="fas fa-shield-halved"></i> Panel Eksekutif
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Monitoring Penjaminan Mutu Internal (SPMI)</h3>
            <p class="text-muted small mb-0">Evaluasi menyeluruh pelaksanaan siklus PPEPP di seluruh fakultas dan program studi.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('kepala/rekapitulasi') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0">
                <i class="fas fa-table-list me-1"></i> Rekapitulasi Dokumen
            </a>
            <a href="<?= base_url('kepala/audit') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-clock-rotate-left me-1"></i> Log Audit Trail
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
                    <div class="small text-muted fw-semibold">Fakultas</div>
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
                    <div class="small text-muted fw-semibold">Program Studi</div>
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
                    <div class="small text-muted fw-semibold">Dokumen Mutu</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark-blue"><?= $totalAudit ?></div>
                    <div class="small text-muted fw-semibold">Rekam Audit Trail</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold text-dark-blue mb-3">
                    <i class="fas fa-chart-pie text-primary me-2"></i> Komposisi Siklus PPEPP
                </h5>
                <div id="kepalaDonutChart" style="min-height: 260px;"></div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold text-dark-blue mb-3">
                    <i class="fas fa-chart-bar text-info me-2"></i> Dokumen per Fakultas
                </h5>
                <div id="kepalaFakultasChart" style="min-height: 260px;"></div>
            </div>
        </div>
    </div>

    <!-- Recent Audit Trail Logs -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark-blue mb-0">
                <i class="fas fa-clock-rotate-left text-warning me-2"></i> Rekam Jejak Aktivitas Terbaru
            </h5>
            <a href="<?= base_url('kepala/audit') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Buka Audit Trail Lengkap</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Pengguna (Admin)</th>
                        <th>Peran (Role)</th>
                        <th>Program Studi</th>
                        <th>Aksi</th>
                        <th>Item Yang Diubah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentLogs)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat aktivitas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentLogs as $log): ?>
                            <tr>
                                <td class="text-nowrap small text-muted">
                                    <i class="far fa-clock me-1 text-secondary"></i> <?= date('d M Y H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                                <td class="fw-bold text-dark-blue"><?= htmlspecialchars($log['user_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($log['user_role']) ?></span></td>
                                <td><?= htmlspecialchars($log['prodi_name'] ?: 'Tingkat Institusi') ?></td>
                                <td>
                                    <?php
                                    $bCls = match($log['aksi']) {
                                        'CREATE' => 'bg-success',
                                        'UPDATE' => 'bg-primary',
                                        'DELETE' => 'bg-danger',
                                        'RESTORE'=> 'bg-warning text-dark',
                                        'LOGIN'  => 'bg-info',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $bCls ?>"><?= $log['aksi'] ?></span>
                                </td>
                                <td class="small text-truncate" style="max-width: 250px;">
                                    <strong><?= htmlspecialchars($log['modul']) ?>:</strong> <?= htmlspecialchars($log['target_name'] ?: '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Donut
    new ApexCharts(document.querySelector("#kepalaDonutChart"), {
        series: [
            <?= (int)$cycleStats['penetapan'] ?>,
            <?= (int)$cycleStats['pelaksanaan'] ?>,
            <?= (int)$cycleStats['evaluasi'] ?>,
            <?= (int)$cycleStats['pengendalian'] ?>,
            <?= (int)$cycleStats['peningkatan'] ?>
        ],
        labels: ['Penetapan (P1)', 'Pelaksanaan (P2)', 'Evaluasi (E)', 'Pengendalian (P3)', 'Peningkatan (P4)'],
        colors: ['#2563EB', '#059669', '#D97706', '#7C3AED', '#0891B2'],
        chart: { type: 'donut', height: 260, fontFamily: 'Plus Jakarta Sans, sans-serif' },
        legend: { position: 'bottom' }
    }).render();

    // Bar Fakultas
    <?php
    $fakNames = array_column($fakultasStats, 'kode_fakultas');
    $fakDocs = array_column($fakultasStats, 'total_dokumen');
    ?>
    new ApexCharts(document.querySelector("#kepalaFakultasChart"), {
        series: [{ name: 'Dokumen', data: <?= json_encode($fakDocs) ?> }],
        chart: { type: 'bar', height: 260, toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans, sans-serif' },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        colors: ['#1E3E62'],
        xaxis: { categories: <?= json_encode($fakNames) ?> }
    }).render();
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
