<?php
/**
 * Super Admin & LPM: Monitoring Kinerja Anggota GPM
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

function formatHoursToDays(?float $hours): string {
    if ($hours === null || $hours <= 0) return '-';
    if ($hours < 24) return round($hours, 1) . ' Jam';
    $days = round($hours / 24, 1);
    return "{$days} Hari";
}
?>

<div class="container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-scu-blue bg-opacity-10 text-primary small fw-bold mb-2">
                <i class="fas fa-users-gear"></i> Gugus Penjaminan Mutu (GPM)
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Monitoring Kinerja Anggota GPM</h3>
            <p class="text-muted small mb-0">Direktori seluruh anggota Gugus Penjaminan Mutu (GPM) fakultas: pemantauan beban telaah, dokumen selesai, antrean pending, dan kecepatan respon.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5">
                <i class="fas fa-user-plus"></i>
                <span>Kelola Akun GPM</span>
            </a>
            <a href="<?= base_url('admin/monitoring-dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5">
                <i class="fas fa-file-waveform"></i>
                <span>Monitoring Dokumen</span>
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-user-tie fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Anggota GPM</div>
                        <h3 class="fw-bold text-dark-blue mb-0"><?= $totalGpm ?> Personel</h3>
                        <span class="text-muted" style="font-size: 0.72rem;">Terdaftar di seluruh fakultas</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success">
                        <i class="fas fa-clipboard-check fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Dokumen Ditelaah</div>
                        <h3 class="fw-bold text-success mb-0"><?= number_format($totalReviewedAll) ?> Berkas</h3>
                        <span class="text-muted" style="font-size: 0.72rem;">Telah ditinjau oleh GPM</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-hourglass-half fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Antrean Pending</div>
                        <h3 class="fw-bold text-warning mb-0"><?= number_format($totalPendingAll) ?> Berkas</h3>
                        <span class="text-muted" style="font-size: 0.72rem;">Menunggu tindakan GPM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <h5 class="fw-bold text-dark-blue mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-users-viewfinder text-primary"></i> Daftar Anggota GPM &amp; Beban Kerja
            </h5>
            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small">
                Diurutkan Berdasarkan Fakultas
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">No</th>
                        <th style="min-width: 220px;">Anggota GPM</th>
                        <th style="min-width: 180px;">Penugasan Fakultas</th>
                        <th style="width: 120px;" class="text-center">Beban Scope</th>
                        <th style="width: 120px;" class="text-center">Selesai Ditelaah</th>
                        <th style="width: 120px;" class="text-center">Antrean Pending</th>
                        <th style="width: 120px;" class="text-center">Revisi Diberikan</th>
                        <th style="width: 120px;" class="text-center">Diteruskan LPM</th>
                        <th style="width: 130px;" class="text-center">Rata-rata Respon</th>
                        <th style="min-width: 140px;">Review Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($gpmMembers)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-user-slash fa-2x mb-2 d-block text-secondary"></i>
                                Belum ada pengguna dengan hak akses <strong>GPM</strong> terdaftar di sistem.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($gpmMembers as $m): 
                            $avgDaysStr = formatHoursToDays(isset($m['avg_review_hours']) ? (float)$m['avg_review_hours'] : null);
                        ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="p-2 rounded-circle bg-scu-blue bg-opacity-10 text-primary flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark-blue mb-0" style="font-size: 0.88rem;">
                                                <?= htmlspecialchars($m['name']) ?>
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.73rem;">
                                                <i class="far fa-envelope me-1"></i><?= htmlspecialchars($m['email']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($m['nama_fakultas'])): ?>
                                        <span class="badge rounded-pill px-2.5 py-1 fw-semibold text-wrap text-start" style="background:#EDE9FE; color:#6D28D9; border:1px solid #DDD6FE; font-size:0.75rem;">
                                            <i class="fas fa-building-columns me-1"></i> <?= htmlspecialchars($m['nama_fakultas']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.72rem;">Belum Dipetakan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                        <?= (int)$m['total_dokumen_scope'] ?> Dokumen
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                        <i class="fas fa-check me-1"></i> <?= (int)$m['total_reviewed'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php $pCount = (int)$m['pending_review']; ?>
                                    <?php if ($pCount > 0): ?>
                                        <span class="badge bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                            <i class="fas fa-hourglass-half text-warning me-1"></i> <?= $pCount ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 0.72rem;">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.75rem;">
                                        <?= (int)$m['total_revisi'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-purple bg-opacity-10 text-purple border px-2 py-0.5 rounded-pill fw-bold" style="color: #7E22CE; font-size: 0.75rem;">
                                        <?= (int)$m['total_diteruskan'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-monospace" style="font-size: 0.75rem;">
                                        <i class="fas fa-stopwatch text-primary me-1"></i> <?= $avgDaysStr ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($m['last_reviewed_at'])): ?>
                                        <div class="small fw-semibold text-dark" style="font-size: 0.78rem;">
                                            <?= date('d M Y', strtotime($m['last_reviewed_at'])) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            <?= date('H:i', strtotime($m['last_reviewed_at'])) ?> WIB
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic" style="font-size: 0.72rem;">Belum ada aktivitas</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
