<?php
/**
 * Super Admin: Master Fakultas View
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-blue mb-1">Master Data Fakultas</h3>
            <p class="text-muted small mb-0">Kelola daftar fakultas di lingkungan <?= INSTITUTION_NAME ?>.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0" data-bs-toggle="modal" data-bs-target="#modalFakultas" onclick="resetFakultasForm()">
            <i class="fas fa-plus me-1"></i> Tambah Fakultas Baru
        </button>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Fakultas</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 140px;">Jumlah Prodi</th>
                        <th class="text-center" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fakultasList)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data fakultas.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($fakultasList as $fak): ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td><span class="badge bg-dark-blue text-warning px-2.5 py-1.5 fw-bold"><?= htmlspecialchars($fak['kode_fakultas']) ?></span></td>
                                <td class="fw-bold text-dark-blue"><?= htmlspecialchars($fak['nama_fakultas']) ?></td>
                                <td class="small text-muted" style="max-width: 300px;"><?= htmlspecialchars($fak['deskripsi'] ?: '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info border px-2.5 py-1 fw-bold"><?= $fak['total_prodi'] ?> Program Studi</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalFakultas"
                                                onclick="editFakultas(<?= htmlspecialchars(json_encode($fak)) ?>)">
                                            <i class="fas fa-edit"></i> Ubah
                                        </button>
                                        <form action="<?= base_url('admin/fakultas/delete/' . $fak['id']) ?>" method="POST" class="d-inline">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 btn-delete-confirm" data-name="<?= htmlspecialchars($fak['nama_fakultas']) ?>">
                                                <i class="fas fa-trash"></i>
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

<!-- Modal: Tambah/Edit Fakultas -->
<div class="modal fade" id="modalFakultas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('admin/fakultas/save') ?>" method="POST">
                <input type="hidden" name="id" id="fakultas_id" value="">
                
                <div class="modal-header bg-dark-blue text-white">
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalFakultasTitle">Tambah Fakultas Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="kode_fakultas" class="form-label fw-semibold small text-secondary">Kode Singkatan Fakultas *</label>
                        <input type="text" class="form-control" id="kode_fakultas" name="kode_fakultas" placeholder="Contoh: FIK, FEB, FAD" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_fakultas" class="form-label fw-semibold small text-secondary">Nama Lengkap Fakultas *</label>
                        <input type="text" class="form-control" id="nama_fakultas" name="nama_fakultas" placeholder="Contoh: Fakultas Ilmu Komputer" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-semibold small text-secondary">Deskripsi Singkat</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Profil fakultas..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill bg-scu-blue border-0 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetFakultasForm() {
    document.getElementById('modalFakultasTitle').textContent = 'Tambah Fakultas Baru';
    document.getElementById('fakultas_id').value = '';
    document.getElementById('kode_fakultas').value = '';
    document.getElementById('nama_fakultas').value = '';
    document.getElementById('deskripsi').value = '';
}

function editFakultas(data) {
    document.getElementById('modalFakultasTitle').textContent = 'Ubah Data Fakultas';
    document.getElementById('fakultas_id').value = data.id;
    document.getElementById('kode_fakultas').value = data.kode_fakultas;
    document.getElementById('nama_fakultas').value = data.nama_fakultas;
    document.getElementById('deskripsi').value = data.deskripsi || '';
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
