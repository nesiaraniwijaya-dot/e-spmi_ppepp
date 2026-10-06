<?php
/**
 * Kepala LPM: Rekapitulasi Dokumen Seluruh Fakultas & Prodi
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-blue mb-1">Rekapitulasi Dokumen PPEPP Universitas</h3>
            <p class="text-muted small mb-0">Matriks pemenuhan 5 siklus penjaminan mutu internal seluruh program studi.</p>
        </div>
        <a href="<?= base_url('kepala/dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" style="width: 50px;">No</th>
                        <th rowspan="2">Fakultas</th>
                        <th rowspan="2">Program Studi</th>
                        <th rowspan="2">Ketua Program Studi</th>
                        <th colspan="5" class="bg-primary text-white py-1">Distribusi Siklus PPEPP</th>
                        <th rowspan="2" style="width: 90px;">Total</th>
                        <th rowspan="2" style="width: 100px;">Aksi</th>
                    </tr>
                    <tr class="small text-muted">
                        <th style="width: 70px;" class="text-primary">P1</th>
                        <th style="width: 70px;" class="text-success">P2</th>
                        <th style="width: 70px;" class="text-warning">E</th>
                        <th style="width: 70px;" class="text-purple" style="color: #7C3AED;">P3</th>
                        <th style="width: 70px;" class="text-info">P4</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rekapList)): ?>
                        <tr><td colspan="11" class="text-center py-4 text-muted">Belum ada data prodi.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($rekapList as $r): ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td class="small fw-bold text-secondary"><?= htmlspecialchars($r['nama_fakultas']) ?></td>
                                <td>
                                    <span class="badge bg-dark-blue text-warning me-1"><?= htmlspecialchars($r['jenjang']) ?></span>
                                    <span class="fw-bold text-dark-blue"><?= htmlspecialchars($r['nama_prodi']) ?></span>
                                    <div class="text-muted" style="font-size: 0.72rem;">Kode: <?= htmlspecialchars($r['kode_prodi']) ?></div>
                                </td>
                                <td class="small text-secondary"><?= htmlspecialchars($r['nama_kaprodi'] ?: '-') ?></td>
                                <td class="text-center"><span class="badge badge-penetapan"><?= $r['p1_count'] ?></span></td>
                                <td class="text-center"><span class="badge badge-pelaksanaan"><?= $r['p2_count'] ?></span></td>
                                <td class="text-center"><span class="badge badge-evaluasi"><?= $r['e_count'] ?></span></td>
                                <td class="text-center"><span class="badge badge-pengendalian"><?= $r['p3_count'] ?></span></td>
                                <td class="text-center"><span class="badge badge-peningkatan"><?= $r['p4_count'] ?></span></td>
                                <td class="text-center fw-bold text-dark-blue fs-6"><?= $r['total_dokumen'] ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('prodi/' . $r['id']) ?>" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1" style="font-size: 0.75rem;">
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
