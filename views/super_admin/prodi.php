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
        <?php if (Auth::canActionLpmMenu('master_prodi')): ?>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0" data-bs-toggle="modal" data-bs-target="#modalProdi" onclick="resetProdiForm()">
            <i class="fas fa-plus me-1"></i> Tambah Program Studi Baru
        </button>
        <?php else: ?>
        <span class="badge bg-light text-muted border px-3 py-2 rounded-pill"><i class="fas fa-eye me-1"></i> Mode Lihat Saja</span>
        <?php endif; ?>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
        <div class="table-responsive" style="overflow-x: hidden;">
            <table class="table table-hover align-middle mb-0 w-100" style="table-layout: fixed; font-size: 0.83rem;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 4.5%; white-space: nowrap;">No</th>
                        <th style="width: 14%; white-space: nowrap;">Kode &amp; Jenjang</th>
                        <th style="width: 21%;">Nama Program Studi</th>
                        <th style="width: 14%;">Fakultas</th>
                        <th style="width: 18%;">Pimpinan Prodi</th>
                        <th style="width: 16%;">Akun Terhubung</th>
                        <th class="text-center" style="width: 5%; white-space: nowrap;">Dok.</th>
                        <th class="text-center" style="width: 7.5%; white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($prodiList)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data program studi.</td></tr>
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
                        foreach ($prodiList as $p): 
                            $jStyle = $jenjangColors[$p['jenjang']] ?? 'background: #1E3E62; color: #FBBF24;';
                        ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold" style="white-space: nowrap;"><?= $no++ ?></td>
                                <td style="white-space: nowrap;">
                                    <div class="d-inline-flex align-items-center gap-1.5 flex-nowrap">
                                        <span class="badge fw-bold px-2 py-1 shadow-2xs" style="<?= $jStyle ?> font-size: 0.72rem; min-width: 32px; text-align: center;">
                                            <?= htmlspecialchars($p['jenjang']) ?>
                                        </span>
                                        <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="font-size: 0.72rem; max-width: 95px; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($p['kode_prodi']) ?>">
                                            <?= htmlspecialchars($p['kode_prodi']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="fw-bold text-dark-blue" style="word-break: break-word; line-height: 1.35;">
                                    <?= htmlspecialchars($p['nama_prodi']) ?>
                                </td>
                                <td class="small text-secondary" style="word-break: break-word; line-height: 1.3;">
                                    <?= htmlspecialchars($p['nama_fakultas']) ?>
                                </td>
                                <td style="word-break: break-word; line-height: 1.3;">
                                    <div class="small fw-semibold text-dark text-truncate" title="<?= htmlspecialchars($p['nama_kaprodi'] ?: '-') ?>">
                                        <span class="badge bg-primary-subtle text-primary border me-1" style="font-size: 0.65rem;">Kaprodi</span><?= htmlspecialchars($p['nama_kaprodi'] ?: '-') ?>
                                    </div>
                                    <?php if ($p['nidn_kaprodi']): ?>
                                        <div class="text-muted" style="font-size: 0.7rem;">NIDN: <?= htmlspecialchars($p['nidn_kaprodi']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($p['nama_sekprodi'])): ?>
                                        <div class="small fw-semibold text-dark text-truncate mt-1" title="<?= htmlspecialchars($p['nama_sekprodi']) ?>">
                                            <span class="badge bg-secondary-subtle text-secondary border me-1" style="font-size: 0.65rem;">Sekprodi</span><?= htmlspecialchars($p['nama_sekprodi']) ?>
                                        </div>
                                        <?php if ($p['nidn_sekprodi']): ?>
                                            <div class="text-muted" style="font-size: 0.7rem;">NIDN: <?= htmlspecialchars($p['nidn_sekprodi']) ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <?php if (!empty($p['kaprodi_user_name'])): ?>
                                            <span class="badge bg-success-subtle text-success border text-start text-truncate py-1 d-block" style="font-size:0.72rem;" title="Kaprodi: <?= htmlspecialchars($p['kaprodi_user_name']) ?>">
                                                <i class="fas fa-user-tie me-1"></i><?= htmlspecialchars($p['kaprodi_user_name']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border text-start py-0.5 d-block" style="font-size:0.68rem;"><i class="fas fa-exclamation-triangle me-1"></i>Kaprodi: -</span>
                                        <?php endif; ?>
                                        <?php if (!empty($p['sekprodi_user_name'])): ?>
                                            <span class="badge bg-info-subtle text-info border text-start text-truncate py-1 d-block" style="font-size:0.72rem;" title="Sekprodi: <?= htmlspecialchars($p['sekprodi_user_name']) ?>">
                                                <i class="fas fa-user-edit me-1"></i><?= htmlspecialchars($p['sekprodi_user_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold"><?= $p['total_dokumen'] ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <?php if (Auth::canActionLpmMenu('master_prodi')): ?>
                                        <button type="button" class="btn btn-xs btn-outline-warning rounded-circle p-0 d-inline-flex align-items-center justify-content-center" 
                                                style="width: 26px; height: 26px;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalProdi" 
                                                onclick="editProdi(<?= htmlspecialchars(json_encode($p)) ?>)"
                                                title="Edit Program Studi">
                                            <i class="fas fa-edit" style="font-size: 0.72rem;"></i>
                                        </button>
                                        <?php endif; ?>
                                        <a href="<?= base_url('prodi/' . $p['id']) ?>" target="_blank" class="btn btn-xs btn-outline-info rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;" title="Lihat Dashboard Publik">
                                            <i class="fas fa-external-link-alt" style="font-size: 0.7rem;"></i>
                                        </a>
                                        <?php if (Auth::canActionLpmMenu('master_prodi')): ?>
                                        <form action="<?= base_url('admin/prodi/delete/' . $p['id']) ?>" method="POST" class="d-inline m-0">
                                            <button type="button" class="btn btn-xs btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center btn-delete-confirm" style="width: 26px; height: 26px;" data-name="<?= htmlspecialchars($p['nama_prodi']) ?>" title="Hapus Program Studi">
                                                <i class="fas fa-trash" style="font-size: 0.7rem;"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
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
