<?php
/**
 * Super Admin: Rekapitulasi Dokumen Seluruh Fakultas & Prodi (LPM)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-blue mb-1">Rekapitulasi Dokumen PPEPP Universitas</h3>
            <p class="text-muted small mb-0">Matriks pemenuhan 5 siklus penjaminan mutu internal seluruh program studi UNIKA Soegijapranata.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/monitoring-dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0">
                <i class="fas fa-clock-rotate-left me-1"></i> Monitoring Unggah Dokumen
            </a>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Dashboard Utama
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
        <div class="table-responsive" style="overflow-x: hidden;">
            <table class="table table-bordered table-hover align-middle mb-0 w-100" style="table-layout: fixed; font-size: 0.83rem;">
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" style="width: 4.5%; white-space: nowrap;">No</th>
                        <th rowspan="2" style="width: 16%;">Fakultas</th>
                        <th rowspan="2" style="width: 22%;">Program Studi</th>
                        <th rowspan="2" style="width: 13.5%;">Ketua Prodi</th>
                        <th colspan="5" class="bg-primary text-white py-1">Distribusi Siklus PPEPP</th>
                        <th rowspan="2" style="width: 7.5%; white-space: nowrap;">Total</th>
                        <th rowspan="2" style="width: 9%; white-space: nowrap;">Aksi</th>
                    </tr>
                    <tr class="small text-muted" style="font-size: 0.75rem;">
                        <th style="width: 5.5%;" class="text-primary">P1</th>
                        <th style="width: 5.5%;" class="text-success">P2</th>
                        <th style="width: 5.5%;" class="text-warning">E</th>
                        <th style="width: 5.5%;" class="text-purple" style="color: #7C3AED;">P3</th>
                        <th style="width: 5.5%;" class="text-info">P4</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rekapList)): ?>
                        <tr><td colspan="11" class="text-center py-4 text-muted">Belum ada data prodi.</td></tr>
                    <?php else: ?>
                        <?php 
                        $no = 1; 
                        $jenjangColors = [
                            'S1' => 'background: #1E3E62; color: #FBBF24;',
                            'S2' => 'background: #4338CA; color: #FFFFFF;',
                            'S3' => 'background: #6D28D9; color: #FFFFFF;',
                            'D3' => 'background: #0F766E; color: #FFFFFF;',
                            'Profesi' => 'background: #047857; color: #FFFFFF;',
                        ];
                        foreach ($rekapList as $r): 
                            $jStyle = $jenjangColors[$r['jenjang']] ?? 'background: #1E3E62; color: #FBBF24;';
                        ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold" style="white-space: nowrap;"><?= $no++ ?></td>
                                <td class="small fw-bold text-secondary"><?= htmlspecialchars($r['nama_fakultas']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5 mb-1">
                                        <span class="badge fw-bold px-2 py-0.5 shadow-2xs" style="<?= $jStyle ?> font-size: 0.7rem; min-width: 30px; text-align: center;"><?= htmlspecialchars($r['jenjang']) ?></span>
                                        <span class="fw-bold text-dark-blue"><?= htmlspecialchars($r['nama_prodi']) ?></span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Kode: <span class="font-monospace"><?= htmlspecialchars($r['kode_prodi']) ?></span></div>
                                </td>
                                <td class="small text-secondary"><?= htmlspecialchars($r['nama_kaprodi'] ?: '-') ?></td>
                                <td class="text-center"><span class="badge badge-penetapan"><?= $r['p1_count'] ?></span></td>
                                <td class="text-center"><span class="badge badge-pelaksanaan"><?= $r['p2_count'] ?></span></td>
                                <td class="text-center"><span class="badge badge-evaluasi"><?= $r['e_count'] ?></span></td>
                                <td class="text-center"><span class="badge badge-pengendalian"><?= $r['p3_count'] ?></span></td>
                                <td class="text-center"><span class="badge badge-peningkatan"><?= $r['p4_count'] ?></span></td>
                                <td class="text-center fw-bold text-dark-blue fs-6" style="white-space: nowrap;"><?= $r['total_dokumen'] ?></td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <a href="<?= base_url('prodi/' . $r['id']) ?>" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 text-nowrap shadow-2xs" style="font-size: 0.75rem;">
                                        <i class="fas fa-eye me-1"></i> Buka
                                    </a>
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
