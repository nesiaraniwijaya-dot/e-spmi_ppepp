<?php
/**
 * Super Admin: Master Program Studi View
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-blue mb-1">Master Program Studi</h3>
            <p class="text-muted small mb-0">Kelola pemetaan program studi terhadap fakultas dan ketua prodi.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0" data-bs-toggle="modal" data-bs-target="#modalProdi" onclick="resetProdiForm()">
            <i class="fas fa-plus me-1"></i> Tambah Program Studi Baru
        </button>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode & Jenjang</th>
                        <th>Nama Program Studi</th>
                        <th>Fakultas</th>
                        <th>Pimpinan Prodi</th>
                        <th>Akun Login (Kaprodi / Sekprodi)</th>
                        <th class="text-center">Dokumen</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($prodiList)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data program studi.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($prodiList as $p): ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-dark-blue text-warning px-2.5 py-1 fw-bold"><?= htmlspecialchars($p['jenjang']) ?></span>
                                    <span class="badge bg-light text-secondary border"><?= htmlspecialchars($p['kode_prodi']) ?></span>
                                </td>
                                <td class="fw-bold text-dark-blue"><?= htmlspecialchars($p['nama_prodi']) ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($p['nama_fakultas']) ?></td>
                                <td>
                                    <div class="small fw-semibold text-dark"><span class="badge bg-primary-subtle text-primary border me-1">Kaprodi</span><?= htmlspecialchars($p['nama_kaprodi'] ?: '-') ?></div>
                                    <?php if ($p['nidn_kaprodi']): ?>
                                        <div class="text-muted ms-1" style="font-size: 0.72rem;">NIDN: <?= htmlspecialchars($p['nidn_kaprodi']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($p['nama_sekprodi'])): ?>
                                        <div class="small fw-semibold text-dark mt-1"><span class="badge bg-secondary-subtle text-secondary border me-1">Sekprodi</span><?= htmlspecialchars($p['nama_sekprodi']) ?></div>
                                        <?php if ($p['nidn_sekprodi']): ?>
                                            <div class="text-muted ms-1" style="font-size: 0.72rem;">NIDN: <?= htmlspecialchars($p['nidn_sekprodi']) ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <?php if (!empty($p['kaprodi_user_name'])): ?>
                                            <span class="badge bg-success-subtle text-success border text-start py-1" style="font-size:0.75rem;"><i class="fas fa-user-tie me-1"></i> <?= htmlspecialchars($p['kaprodi_user_name']) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border text-start py-1" style="font-size:0.75rem;"><i class="fas fa-exclamation-triangle me-1"></i> Kaprodi: Kosong</span>
                                        <?php endif; ?>
                                        <?php if (!empty($p['sekprodi_user_name'])): ?>
                                            <span class="badge bg-info-subtle text-info border text-start py-1" style="font-size:0.75rem;"><i class="fas fa-user-edit me-1"></i> <?= htmlspecialchars($p['sekprodi_user_name']) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border text-start py-1" style="font-size:0.75rem;"><i class="fas fa-info-circle me-1"></i> Sekprodi: Kosong</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold"><?= $p['total_dokumen'] ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalProdi"
                                                onclick="editProdi(<?= htmlspecialchars(json_encode($p)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('prodi/' . $p['id']) ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2.5" title="Lihat Dashboard Publik">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <form action="<?= base_url('admin/prodi/delete/' . $p['id']) ?>" method="POST" class="d-inline">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 btn-delete-confirm" data-name="<?= htmlspecialchars($p['nama_prodi']) ?>">
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

<!-- Modal: Tambah/Edit Prodi -->
<div class="modal fade" id="modalProdi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('admin/prodi/save') ?>" method="POST">
                <input type="hidden" name="id" id="prodi_id" value="">
                
                <div class="modal-header bg-dark-blue text-white">
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalProdiTitle">Tambah Program Studi Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="fakultas_id" class="form-label fw-semibold small text-secondary">Fakultas *</label>
                        <select class="form-select" id="fakultas_id" name="fakultas_id" required>
                            <option value="">-- Pilih Fakultas --</option>
                            <?php foreach ($fakultasList as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nama_fakultas']) ?> (<?= htmlspecialchars($f['kode_fakultas']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label for="kode_prodi" class="form-label fw-semibold small text-secondary">Kode Prodi (Dikti/PDDIKTI) *</label>
                            <input type="text" class="form-control" id="kode_prodi" name="kode_prodi" placeholder="Contoh: 55201" required>
                        </div>
                        <div class="col-md-5">
                            <label for="jenjang" class="form-label fw-semibold small text-secondary">Jenjang *</label>
                            <select class="form-select" id="jenjang" name="jenjang" required>
                                <option value="S1">S1 (Sarjana)</option>
                                <option value="D3">D3 (Diploma)</option>
                                <option value="S2">S2 (Magister)</option>
                                <option value="S3">S3 (Doktor)</option>
                                <option value="Profesi">Profesi</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nama_prodi" class="form-label fw-semibold small text-secondary">Nama Program Studi *</label>
                        <input type="text" class="form-control" id="nama_prodi" name="nama_prodi" placeholder="Contoh: Teknik Informatika" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label for="nama_kaprodi" class="form-label fw-semibold small text-secondary">Ketua Prodi (Kaprodi)</label>
                            <input type="text" class="form-control" id="nama_kaprodi" name="nama_kaprodi" placeholder="Nama lengkap & gelar">
                        </div>
                        <div class="col-md-5">
                            <label for="nidn_kaprodi" class="form-label fw-semibold small text-secondary">NIDN Kaprodi</label>
                            <input type="text" class="form-control" id="nidn_kaprodi" name="nidn_kaprodi" placeholder="Nomor NIDN">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label for="nama_sekprodi" class="form-label fw-semibold small text-secondary">Sekretaris Prodi (Sekprodi)</label>
                            <input type="text" class="form-control" id="nama_sekprodi" name="nama_sekprodi" placeholder="Nama lengkap & gelar">
                        </div>
                        <div class="col-md-5">
                            <label for="nidn_sekprodi" class="form-label fw-semibold small text-secondary">NIDN Sekprodi</label>
                            <input type="text" class="form-control" id="nidn_sekprodi" name="nidn_sekprodi" placeholder="Nomor NIDN">
                        </div>
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
function resetProdiForm() {
    document.getElementById('modalProdiTitle').textContent = 'Tambah Program Studi Baru';
    document.getElementById('prodi_id').value = '';
    document.getElementById('fakultas_id').value = '';
    document.getElementById('kode_prodi').value = '';
    document.getElementById('jenjang').value = 'S1';
    document.getElementById('nama_prodi').value = '';
    document.getElementById('nama_kaprodi').value = '';
    document.getElementById('nidn_kaprodi').value = '';
    document.getElementById('nama_sekprodi').value = '';
    document.getElementById('nidn_sekprodi').value = '';
}

function editProdi(data) {
    document.getElementById('modalProdiTitle').textContent = 'Ubah Data Program Studi';
    document.getElementById('prodi_id').value = data.id;
    document.getElementById('fakultas_id').value = data.fakultas_id;
    document.getElementById('kode_prodi').value = data.kode_prodi;
    document.getElementById('jenjang').value = data.jenjang;
    document.getElementById('nama_prodi').value = data.nama_prodi;
    document.getElementById('nama_kaprodi').value = data.nama_kaprodi || '';
    document.getElementById('nidn_kaprodi').value = data.nidn_kaprodi || '';
    document.getElementById('nama_sekprodi').value = data.nama_sekprodi || '';
    document.getElementById('nidn_sekprodi').value = data.nidn_sekprodi || '';
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
