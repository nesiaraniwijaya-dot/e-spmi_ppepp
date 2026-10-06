<?php
/**
 * Super Admin & Kepala LPM: Master Bidang & Standar Mutu
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-warning bg-opacity-10 text-warning small fw-bold mb-2">
                <i class="fas fa-sliders"></i> Kustomisasi Standar LPM
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Master Bidang & Standar Mutu</h3>
            <p class="text-muted small mb-0">Kelola hierarki kriteria bidang PPEPP beserta standar mutu yang menjadi acuan pengunggahan dokumen oleh Admin Program Studi.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalBidang" onclick="resetBidangForm()">
                <i class="fas fa-plus me-1"></i> Tambah Bidang
            </button>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th style="width: 140px;">Kode Bidang</th>
                        <th>Nama Bidang</th>
                        <th style="width: 170px;">Standar</th>
                        <th>Deskripsi Standar</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 90px;">Dokumen</th>
                        <th class="text-center" style="width: 130px;">Aksi Bidang</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bidangList)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada bidang mutu terdaftar.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($bidangList as $b): ?>
                            <tr id="bidang-row-<?= $b['id'] ?>" class="bidang-main-row">
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace fw-bold">
                                        <?= htmlspecialchars($b['kode_bidang'] ?: 'BIDANG-'.$b['id']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark-blue"><?= htmlspecialchars($b['nama_bidang']) ?></div>
                                </td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm btn-sub-toggle rounded-pill px-2.5 py-1 text-primary border bg-light d-inline-flex align-items-center gap-1.5 fw-semibold"
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#subCollapse_<?= $b['id'] ?>" 
                                            aria-expanded="false" 
                                            aria-controls="subCollapse_<?= $b['id'] ?>"
                                            id="toggleBtn_<?= $b['id'] ?>">
                                        <i class="fas fa-sitemap text-info"></i>
                                        <span><?= $b['total_sub'] ?> Standar</span>
                                        <i class="fas fa-chevron-down sub-chevron small ms-1 text-secondary"></i>
                                    </button>
                                </td>
                                <td class="small text-muted" style="max-width: 280px;"><?= htmlspecialchars($b['deskripsi'] ?: '-') ?></td>
                                <td class="text-center">
                                    <?php if ($b['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border"><?= $b['total_dokumen'] ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalBidang" 
                                                title="Ubah Bidang" 
                                                onclick="editBidang(<?= htmlspecialchars(json_encode($b)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?= base_url('admin/bidang/delete/' . $b['id']) ?>" method="POST" class="d-inline">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 btn-delete-confirm" 
                                                    data-name="<?= htmlspecialchars($b['nama_bidang']) ?>" 
                                                    title="Hapus Bidang">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Collapsible Standar Row -->
                            <tr class="sub-collapse-row">
                                <td colspan="8" class="p-0 border-0">
                                    <div class="collapse p-3 p-md-4 bg-light-subtle border-start border-4 border-primary rounded-3 m-2 shadow-sm" id="subCollapse_<?= $b['id'] ?>">
                                        <!-- Header Standar Panel -->
                                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-circle">
                                                    <i class="fas fa-list-check"></i>
                                                </span>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0">
                                                        Standar Mutu: <span class="text-primary"><?= htmlspecialchars($b['nama_bidang']) ?></span>
                                                    </h6>
                                                    <small class="text-muted">Total terdaftar: <?= $b['total_sub'] ?> standar mutu</small>
                                                </div>
                                            </div>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm rounded-pill px-3 bg-scu-blue border-0 shadow-xs" 
                                                    onclick="openAddSubBidang(<?= $b['id'] ?>, '<?= htmlspecialchars(addslashes($b['nama_bidang'])) ?>')">
                                                <i class="fas fa-plus me-1"></i> Tambah Standar
                                            </button>
                                        </div>

                                        <!-- Sub Table -->
                                        <div class="table-responsive bg-white rounded-3 border">
                                            <table class="table table-sm table-hover align-middle mb-0">
                                                <thead class="table-light text-muted small">
                                                    <tr>
                                                        <th style="width: 40px;" class="text-center">#</th>
                                                        <th style="width: 120px;">Kode</th>
                                                        <th>Nama Standar Mutu</th>
                                                        <th>Deskripsi / Ruang Lingkup</th>
                                                        <th class="text-center" style="width: 100px;">Status</th>
                                                        <th class="text-center" style="width: 90px;">Dokumen</th>
                                                        <th class="text-center" style="width: 110px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (empty($b['sub_bidang'])): ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center py-4 text-muted small">
                                                                <i class="fas fa-folder-open fa-2x text-secondary opacity-50 mb-2 d-block"></i>
                                                                Belum ada standar mutu untuk bidang ini.<br>
                                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 mt-2" 
                                                                        onclick="openAddSubBidang(<?= $b['id'] ?>, '<?= htmlspecialchars(addslashes($b['nama_bidang'])) ?>')">
                                                                    <i class="fas fa-plus me-1"></i> Tambah Standar Sekarang
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php else: ?>
                                                        <?php $sNo = 1; foreach ($b['sub_bidang'] as $sub): ?>
                                                            <tr>
                                                                <td class="text-center text-muted small"><?= $sNo++ ?></td>
                                                                <td>
                                                                    <span class="badge bg-light text-dark border font-monospace fw-bold small">
                                                                        <?= htmlspecialchars($sub['kode_sub_bidang'] ?: '-') ?>
                                                                    </span>
                                                                </td>
                                                                <td class="fw-semibold text-dark small"><?= htmlspecialchars($sub['nama_sub_bidang']) ?></td>
                                                                <td class="small text-muted" style="max-width: 320px;">
                                                                    <?= htmlspecialchars($sub['deskripsi'] ?: '-') ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php if ($sub['is_active']): ?>
                                                                        <span class="badge bg-success-subtle text-success border small">Aktif</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-secondary-subtle text-secondary border small">Non-Aktif</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-light text-secondary border small"><?= $sub['total_dokumen'] ?></span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center gap-1">
                                                                        <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-2" 
                                                                                title="Ubah Standar" 
                                                                                onclick='openEditSubBidang(<?= htmlspecialchars(json_encode($sub), ENT_QUOTES, 'UTF-8') ?>, "<?= htmlspecialchars(addslashes($b['nama_bidang'])) ?>")'>
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                        <form action="<?= base_url('admin/sub-bidang/delete/' . $sub['id']) ?>" method="POST" class="d-inline">
                                                                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 btn-delete-sub-confirm" 
                                                                                    data-name="<?= htmlspecialchars($sub['nama_sub_bidang']) ?>" 
                                                                                    data-docs="<?= $sub['total_dokumen'] ?>" 
                                                                                    title="Hapus Standar">
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
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Tambah/Edit Bidang Mutu -->
<div class="modal fade" id="modalBidang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('admin/bidang/save') ?>" method="POST">
                <input type="hidden" name="id" id="bidang_id" value="">
                
                <div class="modal-header bg-dark-blue text-white">
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalBidangTitle">Tambah Bidang Mutu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="kode_bidang" class="form-label fw-semibold small text-secondary">Kode Singkat (Opsional)</label>
                        <input type="text" class="form-control" id="kode_bidang" name="kode_bidang" placeholder="Contoh: PENDIDIKAN, SDM, RIS">
                    </div>
                    <div class="mb-3">
                        <label for="nama_bidang" class="form-label fw-semibold small text-secondary">Nama Bidang Mutu *</label>
                        <input type="text" class="form-control" id="nama_bidang" name="nama_bidang" placeholder="Contoh: Pendidikan dan Kurikulum" required>
                    </div>
                    <div class="mb-3">
                        <label for="bidang_deskripsi" class="form-label fw-semibold small text-secondary">Deskripsi & Ruang Lingkup Bidang</label>
                        <textarea class="form-control" id="bidang_deskripsi" name="deskripsi" rows="3" placeholder="Penjelasan bidang..."></textarea>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="bidang_is_active" name="is_active" value="1" checked>
                        <label class="form-check-label small fw-semibold" for="bidang_is_active">Aktifkan bidang ini dalam pilihan form dokumen</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill bg-scu-blue border-0 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Bidang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Tambah/Edit Standar Mutu -->
<div class="modal fade" id="modalSubBidang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('admin/sub-bidang/save') ?>" method="POST">
                <input type="hidden" name="id" id="sub_id" value="">
                
                <div class="modal-header bg-dark-blue text-white">
                    <div>
                        <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalSubBidangTitle">Tambah Standar Mutu</h5>
                        <small class="text-white text-opacity-75" id="modalSubBidangSubtitle">Bidang: -</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="sub_bidang_id" class="form-label fw-semibold small text-secondary">Bidang Mutu Induk *</label>
                        <select class="form-select" id="sub_bidang_id" name="bidang_id" required>
                            <?php foreach ($bidangList as $bOpt): ?>
                                <option value="<?= $bOpt['id'] ?>"><?= htmlspecialchars(($bOpt['kode_bidang'] ? '['.$bOpt['kode_bidang'].'] ' : '') . $bOpt['nama_bidang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kode_sub_bidang" class="form-label fw-semibold small text-secondary">Kode Singkat Standar (Opsional)</label>
                        <input type="text" class="form-control" id="kode_sub_bidang" name="kode_sub_bidang" placeholder="Contoh: VMTS-01, DIK-03, SDM-02">
                        <div class="form-text small">Membantu pengurutan dan pelabelan pada borang akreditasi.</div>
                    </div>
                    <div class="mb-3">
                        <label for="nama_sub_bidang" class="form-label fw-semibold small text-secondary">Nama Standar Mutu *</label>
                        <input type="text" class="form-control" id="nama_sub_bidang" name="nama_sub_bidang" placeholder="Contoh: Standar Kurikulum dan Pembelajaran OBE" required>
                    </div>
                    <div class="mb-3">
                        <label for="sub_deskripsi" class="form-label fw-semibold small text-secondary">Deskripsi & Ruang Lingkup Standar</label>
                        <textarea class="form-control" id="sub_deskripsi" name="deskripsi" rows="3" placeholder="Penjelasan atau indikator ketercapaian standar..."></textarea>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="sub_is_active" name="is_active" value="1" checked>
                        <label class="form-check-label small fw-semibold" for="sub_is_active">Aktifkan standar ini dalam pilihan form dokumen prodi</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill bg-scu-blue border-0 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Standar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-sub-toggle {
    transition: all 0.2s ease-in-out;
}
.btn-sub-toggle:hover {
    background-color: #EBF3FF !important;
    border-color: #002B66 !important;
}
.btn-sub-toggle[aria-expanded="true"] {
    background-color: #002B66 !important;
    color: #fff !important;
    border-color: #002B66 !important;
}
.btn-sub-toggle[aria-expanded="true"] .text-info,
.btn-sub-toggle[aria-expanded="true"] .sub-chevron {
    color: #fff !important;
}
.btn-sub-toggle[aria-expanded="true"] .sub-chevron {
    transform: rotate(180deg);
}
.sub-chevron {
    transition: transform 0.25s ease-in-out;
}
</style>

<script>
// Modal Bidang Functions
function resetBidangForm() {
    document.getElementById('modalBidangTitle').textContent = 'Tambah Bidang Mutu';
    document.getElementById('bidang_id').value = '';
    document.getElementById('kode_bidang').value = '';
    document.getElementById('nama_bidang').value = '';
    document.getElementById('bidang_deskripsi').value = '';
    document.getElementById('bidang_is_active').checked = true;
}

function editBidang(data) {
    document.getElementById('modalBidangTitle').textContent = 'Ubah Bidang Mutu';
    document.getElementById('bidang_id').value = data.id;
    document.getElementById('kode_bidang').value = data.kode_bidang || '';
    document.getElementById('nama_bidang').value = data.nama_bidang;
    document.getElementById('bidang_deskripsi').value = data.deskripsi || '';
    document.getElementById('bidang_is_active').checked = data.is_active == 1;
}

// Modal Standar Functions
function openAddSubBidang(bidangId, bidangNama) {
    document.getElementById('modalSubBidangTitle').textContent = 'Tambah Standar Mutu';
    document.getElementById('modalSubBidangSubtitle').textContent = 'Bidang: ' + bidangNama;
    document.getElementById('sub_id').value = '';
    document.getElementById('sub_bidang_id').value = bidangId;
    document.getElementById('kode_sub_bidang').value = '';
    document.getElementById('nama_sub_bidang').value = '';
    document.getElementById('sub_deskripsi').value = '';
    document.getElementById('sub_is_active').checked = true;

    const modal = new bootstrap.Modal(document.getElementById('modalSubBidang'));
    modal.show();
}

function openEditSubBidang(subData, bidangNama) {
    document.getElementById('modalSubBidangTitle').textContent = 'Ubah Standar Mutu';
    document.getElementById('modalSubBidangSubtitle').textContent = 'Bidang: ' + bidangNama;
    document.getElementById('sub_id').value = subData.id;
    document.getElementById('sub_bidang_id').value = subData.bidang_id;
    document.getElementById('kode_sub_bidang').value = subData.kode_sub_bidang || '';
    document.getElementById('nama_sub_bidang').value = subData.nama_sub_bidang;
    document.getElementById('sub_deskripsi').value = subData.deskripsi || '';
    document.getElementById('sub_is_active').checked = subData.is_active == 1;

    const modal = new bootstrap.Modal(document.getElementById('modalSubBidang'));
    modal.show();
}

// Auto open collapse if URL hash is present (e.g. #bidang-3)
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#bidang-')) {
        const bId = hash.replace('#bidang-', '');
        const collapseEl = document.getElementById('subCollapse_' + bId);
        if (collapseEl) {
            const bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: true });
            const mainRow = document.getElementById('bidang-row-' + bId);
            if (mainRow) {
                setTimeout(() => {
                    mainRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 200);
            }
        }
    }

    // Standar deletion handler with relational document check
    document.querySelectorAll('.btn-delete-sub-confirm').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            const itemName = this.getAttribute('data-name') || 'standar ini';
            const docsCount = parseInt(this.getAttribute('data-docs') || '0', 10);

            if (docsCount > 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Tidak Dapat Menghapus',
                        text: `Standar "${itemName}" masih digunakan oleh ${docsCount} dokumen aktif. Anda dapat mengubah statusnya menjadi 'Non-Aktif' jika tidak ingin digunakan lagi.`,
                        icon: 'warning',
                        confirmButtonColor: '#002B66',
                        confirmButtonText: '<i class="fas fa-check me-1"></i> Mengerti'
                    });
                } else {
                    alert(`Tidak dapat menghapus: Standar "${itemName}" masih digunakan oleh ${docsCount} dokumen aktif. Silakan nonaktifkan status.`);
                }
                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Penghapusan',
                    text: `Apakah Anda yakin ingin menghapus standar "${itemName}"? Tindakan ini tidak dapat dibatalkan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DC2626',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin menghapus standar "${itemName}"?`)) {
                    form.submit();
                }
            }
        });
    });
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
