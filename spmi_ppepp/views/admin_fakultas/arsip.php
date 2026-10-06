<?php
/**
 * Admin Fakultas: Arsip Dokumen Terhapus (Soft Deleted Archive)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-danger bg-opacity-10 text-danger small fw-bold mb-2">
                <i class="fas fa-trash-can"></i> Riwayat Penghapusan Dokumen Fakultas
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Dokumen Mutu Terhapus <?= htmlspecialchars($fakultas['nama_fakultas']) ?></h3>
            <p class="text-muted small mb-0">Dokumen mutu fakultas yang terhapus dapat dipulihkan kembali ke dokumen aktif, atau dihapus secara permanen beserta seluruh berkas lampirannya.</p>
        </div>
        <a href="<?= base_url('fakultas/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dokumen Aktif
        </a>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama & Nomor Dokumen</th>
                        <th>Bidang</th>
                        <th>Siklus</th>
                        <th>Waktu Dihapus</th>
                        <th class="text-center" style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($archivedDocs)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-trash-can fa-3x mb-3 text-secondary d-block opacity-50"></i>
                                <h6 class="fw-bold">Tidak Ada Dokumen Terhapus</h6>
                                <p class="small mb-0">Semua dokumen mutu fakultas aktif dan belum ada dokumen yang dihapus.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($archivedDocs as $doc): ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($doc['nama_dokumen']) ?></div>
                                    <?php if (!empty($doc['nomor_dokumen'])): ?>
                                        <div class="small text-muted"><i class="fas fa-hashtag me-1"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($doc['nama_bidang'] ?: 'Umum') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $doc['siklus'] ?>">
                                        <?= ucfirst($doc['siklus']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-danger fw-bold"><i class="far fa-clock me-1"></i> <?= date('d M Y H:i', strtotime($doc['deleted_at'])) ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1.5 justify-content-center">
                                        <form action="<?= base_url('fakultas/dokumen/restore/' . $doc['id']) ?>" method="POST" class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Pulihkan Dokumen ke Daftar Aktif">
                                                <i class="fas fa-rotate-left"></i> Pulihkan
                                            </button>
                                        </form>
                                        <form action="<?= base_url('fakultas/dokumen/force-delete/' . $doc['id']) ?>" method="POST" class="d-inline">
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
