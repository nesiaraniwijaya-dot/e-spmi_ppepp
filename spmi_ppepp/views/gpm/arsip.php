<?php
/**
 * GPM (Gugus Penjaminan Mutu): Arsip Dokumen Terhapus (Soft Deleted Archive)
 * Menampung dokumen terhapus tingkat Fakultas dan Program Studi yang dapat dipulihkan
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$arsipDocs = $arsipDocs ?? [];
$fakultas  = $fakultas ?? [];
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-danger bg-opacity-10 text-danger small fw-bold mb-2">
                <i class="fas fa-trash-can"></i> Riwayat Penghapusan (Soft Delete)
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Dokumen Mutu Terhapus</h3>
            <p class="text-muted small mb-0">
                Dokumen mutu tingkat <strong>Fakultas</strong> dan <strong>Program Studi</strong> di bawah naungan <strong><?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?></strong> yang masuk ke daftar ini dapat dipulihkan kembali atau dihapus secara permanen dari server.
            </p>
        </div>
        <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
            <i class="fas fa-arrow-left"></i> Kembali ke Dokumen Aktif
        </a>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3 py-3" style="width: 50px;">No</th>
                        <th class="py-3" style="width: 140px;">Unit Kerja</th>
                        <th class="py-3">Nama & Nomor Dokumen</th>
                        <th class="py-3">Bidang</th>
                        <th class="py-3">Siklus PPEPP</th>
                        <th class="py-3">Waktu Dihapus</th>
                        <th class="text-center pe-3 py-3" style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (empty($arsipDocs)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                                        <i class="fas fa-trash-can text-secondary"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Tidak Ada Dokumen Terhapus</h6>
                                    <p class="small text-muted mb-0">Semua dokumen aktif dan belum ada dokumen yang dihapus.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($arsipDocs as $doc): ?>
                            <tr>
                                <td class="ps-3 align-top py-3 text-center text-muted fw-semibold small"><?= $no++ ?></td>

                                <!-- Unit Kerja Badge -->
                                <td class="align-top py-3">
                                    <?php if ($doc['level'] === 'fakultas'): ?>
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.74rem;">
                                            <i class="fas fa-building-columns"></i> Fakultas
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.74rem;">
                                            <i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($doc['nama_prodi'] ?? '') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Nama & Nomor Dokumen -->
                                <td class="align-top py-3">
                                    <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.9rem;">
                                        <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                    </div>
                                    <?php if (!empty($doc['nomor_dokumen'])): ?>
                                        <div class="small text-muted"><i class="fas fa-hashtag me-1"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></div>
                                    <?php endif; ?>
                                </td>

                                <!-- Bidang Standar -->
                                <td class="align-top py-3">
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($doc['nama_bidang'] ?: 'Umum') ?>
                                    </span>
                                </td>

                                <!-- Siklus PPEPP -->
                                <td class="align-top py-3"><?= siklus_badge($doc['siklus']) ?></td>

                                <!-- Waktu Dihapus -->
                                <td class="align-top py-3">
                                    <div class="small text-danger fw-bold"><i class="far fa-clock me-1"></i> <?= date('d M Y H:i', strtotime($doc['deleted_at'])) ?></div>
                                </td>

                                <!-- Aksi -->
                                <td class="pe-3 align-top text-center py-3">
                                    <div class="d-inline-flex align-items-center gap-1.5 justify-content-center">
                                        <form action="<?= base_url('gpm/dokumen/restore/' . $doc['id']) ?>" method="POST" class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Pulihkan Dokumen ke Daftar Aktif">
                                                <i class="fas fa-rotate-left"></i> Pulihkan
                                            </button>
                                        </form>
                                        <form action="<?= base_url('gpm/dokumen/force-delete/' . $doc['id']) ?>" method="POST" class="d-inline">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1 btn-force-delete-confirm" data-name="<?= htmlspecialchars($doc['nama_dokumen']) ?>" style="font-size: 0.78rem;" title="Hapus Dokumen Secara Permanen">
                                                <i class="fas fa-trash-can"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
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
