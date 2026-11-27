<?php
/**
 * Master Bidang & Standar Mutu Fakultas (GPM)
 * Desain seragam & harmonis dengan Master Bidang & Standar LPM
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-warning bg-opacity-10 text-warning small fw-bold mb-2">
                <i class="fas fa-sliders"></i> Kustomisasi Standar GPM &bull; <?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Master Bidang &amp; Standar Mutu</h3>
            <p class="text-muted small mb-0">Kelola hierarki kriteria bidang PPEPP beserta standar mutu yang menjadi acuan pengunggahan dokumen oleh Fakultas dan Program Studi di bawah naungan Anda.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalBidang" onclick="resetBidangForm()">
                <i class="fas fa-plus me-1"></i> Tambah Bidang
            </button>
        </div>
    </div>

    <!-- Alert Edukasi & Integritas Sistem -->
    <div class="alert bg-white border border-info-subtle rounded-4 p-3.5 mb-4 shadow-sm d-flex align-items-start gap-3">
        <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width: 36px; height: 36px;">
            <i class="fas fa-circle-info fa-lg"></i>
        </div>
        <div class="small text-secondary" style="line-height: 1.55;">
            <strong class="text-dark-blue">Integritas Standar Mutu Berjenjang:</strong>
            Standar baku bertanda <span class="badge bg-secondary-subtle text-secondary border">Baku LPM</span> berlaku permanen di seluruh universitas. Anda dapat menambahkan <strong>Bidang atau Standar Mutu Tambahan</strong> khusus untuk fakultas Anda atau spesifik untuk prodi tertentu. Standar tambahan tersebut akan otomatis muncul pada formulir unggah dokumen unit bersangkutan dan diverifikasi dengan lancar tanpa error pada review GPM, LPM, maupun web publik.
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">NO</th>
                        <th style="width: 140px;">KODE BIDANG</th>
                        <th>NAMA BIDANG</th>
                        <th style="width: 170px;">STANDAR</th>
                        <th>DESKRIPSI STANDAR</th>
                        <th class="text-center" style="width: 110px;">STATUS</th>
                        <th class="text-center" style="width: 90px;">DOKUMEN</th>
                        <th class="text-center" style="width: 130px;">AKSI BIDANG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bidangList)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada bidang mutu terdaftar.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($bidangList as $b): 
                            $isCustom = (($b['scope'] ?? 'universitas') !== 'universitas');
                        ?>
                            <tr id="bidang-row-<?= $b['id'] ?>" class="bidang-main-row">
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace fw-bold">
                                        <?= htmlspecialchars($b['kode_bidang'] ?: 'BIDANG-'.$b['id']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark-blue">
                                        <?= htmlspecialchars($b['nama_bidang']) ?>
                                        <?php if ($isCustom): ?>
                                            <span class="badge bg-purple-subtle text-purple border border-purple-subtle ms-1" style="font-size: 0.65rem;">
                                                <i class="fas fa-sparkles me-1"></i>Khusus Fakultas
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm btn-sub-toggle rounded-pill px-2.5 py-1 text-primary border bg-light d-inline-flex align-items-center gap-2 fw-semibold"
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#subCollapse_<?= $b['id'] ?>" 
                                            aria-expanded="false" 
                                            aria-controls="subCollapse_<?= $b['id'] ?>"
                                            id="toggleBtn_<?= $b['id'] ?>">
                                        <i class="fas fa-sitemap text-info me-1"></i>
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
                                    <?php if ($isCustom): ?>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalBidang" 
                                                title="Ubah Bidang" 
                                                onclick="editBidang(<?= htmlspecialchars(json_encode($b)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?= base_url('gpm/master-standar/bidang/delete/' . $b['id']) ?>" method="POST" class="d-inline">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 btn-delete-confirm" 
                                                    data-name="<?= htmlspecialchars($b['nama_bidang']) ?>" 
                                                    title="Hapus Bidang">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border px-2 py-1 small" title="Standar baku tingkat Universitas (LPM)">
                                            <i class="fas fa-lock me-1"></i> Baku LPM
                                        </span>
                                    <?php endif; ?>
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
                                                    class="btn btn-primary btn-sm rounded-pill px-3 bg-scu-blue border-0 shadow-xs fw-semibold" 
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
                                                        <th style="width: 120px;">KODE</th>
                                                        <th>NAMA STANDAR MUTU</th>
                                                        <th style="width: 160px;">RUANG LINGKUP</th>
                                                        <th>DESKRIPSI / INDIKATOR</th>
                                                        <th class="text-center" style="width: 90px;">STATUS</th>
                                                        <th class="text-center" style="width: 80px;">DOKUMEN</th>
                                                        <th class="text-center" style="width: 100px;">AKSI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (empty($b['sub_bidang'])): ?>
                                                        <tr>
                                                            <td colspan="8" class="text-center py-4 text-muted small">
                                                                <i class="fas fa-folder-open fa-2x text-secondary opacity-50 mb-2 d-block"></i>
                                                                Belum ada standar mutu untuk bidang ini.<br>
                                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 mt-2" 
                                                                        onclick="openAddSubBidang(<?= $b['id'] ?>, '<?= htmlspecialchars(addslashes($b['nama_bidang'])) ?>')">
                                                                    <i class="fas fa-plus me-1"></i> Tambah Standar Sekarang
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php else: ?>
                                                        <?php $sNo = 1; foreach ($b['sub_bidang'] as $sub): 
                                                            $isSubCustom = (($sub['scope'] ?? 'universitas') !== 'universitas');
                                                        ?>
                                                            <tr>
                                                                <td class="text-center text-muted small"><?= $sNo++ ?></td>
                                                                <td>
                                                                    <span class="badge bg-light text-dark border font-monospace fw-bold small">
                                                                        <?= htmlspecialchars($sub['kode_sub_bidang'] ?: '-') ?>
                                                                    </span>
                                                                </td>
                                                                <td class="fw-semibold text-dark small">
                                                                    <?= htmlspecialchars($sub['nama_sub_bidang']) ?>
                                                                </td>
                                                                <td>
                                                                    <?php if (($sub['scope'] ?? '') === 'fakultas'): ?>
                                                                        <span class="badge bg-info-subtle text-info border border-info-subtle small">
                                                                            <i class="fas fa-building-columns me-1"></i> Seluruh Fakultas
                                                                        </span>
                                                                    <?php elseif (($sub['scope'] ?? '') === 'prodi'): ?>
                                                                        <span class="badge bg-success-subtle text-success border border-success-subtle small" title="Khusus <?= htmlspecialchars(($sub['jenjang'] ?? '') . ' ' . ($sub['nama_prodi'] ?? '')) ?>">
                                                                            <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars(($sub['jenjang'] ?? '') . ' ' . ($sub['nama_prodi'] ?? 'Prodi')) ?>
                                                                        </span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-secondary-subtle text-secondary border small">
                                                                            <i class="fas fa-globe me-1"></i> Baku Universitas
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="small text-muted" style="max-width: 260px;">
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
                                                                    <?php if ($isSubCustom): ?>
                                                                    <div class="d-flex justify-content-center gap-1">
                                                                        <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-2" 
                                                                                title="Ubah Standar" 
                                                                                onclick='openEditSubBidang(<?= htmlspecialchars(json_encode($sub), ENT_QUOTES, 'UTF-8') ?>, "<?= htmlspecialchars(addslashes($b['nama_bidang'])) ?>")'>
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                        <form action="<?= base_url('gpm/master-standar/sub-bidang/delete/' . $sub['id']) ?>" method="POST" class="d-inline">
                                                                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 btn-delete-sub-confirm" 
                                                                                    data-name="<?= htmlspecialchars($sub['nama_sub_bidang']) ?>" 
                                                                                    data-docs="<?= $sub['total_dokumen'] ?>" 
                                                                                    title="Hapus Standar">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                    <?php else: ?>
                                                                        <span class="text-muted small" title="Standar baku LPM tidak dapat diubah oleh GPM">
                                                                            <i class="fas fa-lock"></i>
                                                                        </span>
                                                                    <?php endif; ?>
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

<!-- Modal: Tambah/Edit Bidang Mutu (GPM) -->
<div class="modal fade" id="modalBidang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= base_url('gpm/master-standar/bidang/save') ?>" method="POST">
                <input type="hidden" name="id" id="bidang_id" value="">
                
                <div class="modal-header bg-dark-blue text-white rounded-top-4 py-3 px-4">
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalBidangTitle">Tambah Bidang Mutu Khusus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="kode_bidang" class="form-label fw-semibold small text-secondary">Kode Singkat Bidang (Opsional)</label>
                        <input type="text" class="form-control rounded-3" id="kode_bidang" name="kode_bidang" placeholder="Contoh: BF-TI, BF-INOVASI">
                        <div class="form-text small">Jika dikosongkan, sistem akan mengenerate kode otomatis secara unik.</div>
                    </div>
                    <div class="mb-3">
                        <label for="nama_bidang" class="form-label fw-semibold small text-secondary">Nama Bidang Mutu *</label>
                        <input type="text" class="form-control rounded-3" id="nama_bidang" name="nama_bidang" placeholder="Contoh: Tata Kelola Lab &amp; Riset Khusus" required>
                    </div>
                    <div class="mb-3">
                        <label for="bidang_deskripsi" class="form-label fw-semibold small text-secondary">Deskripsi &amp; Ruang Lingkup Bidang</label>
                        <textarea class="form-control rounded-3" id="bidang_deskripsi" name="deskripsi" rows="3" placeholder="Penjelasan bidang mutu baru ini..."></textarea>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="bidang_is_active" name="is_active" value="1" checked>
                        <label class="form-check-label small fw-semibold" for="bidang_is_active">Aktifkan bidang ini dalam pilihan form dokumen</label>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill bg-scu-blue border-0 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Bidang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Tambah/Edit Standar Mutu (GPM) -->
<div class="modal fade" id="modalSubBidang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= base_url('gpm/master-standar/sub-bidang/save') ?>" method="POST">
                <input type="hidden" name="id" id="sub_id" value="">
                
                <div class="modal-header bg-dark-blue text-white rounded-top-4 py-3 px-4">
                    <div>
                        <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalSubBidangTitle">Tambah Standar Mutu</h5>
                        <small class="text-white text-opacity-75" id="modalSubBidangSubtitle">Bidang: -</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="sub_bidang_id" class="form-label fw-semibold small text-secondary">Bidang Mutu Induk *</label>
                            <select class="form-select rounded-3" id="sub_bidang_id" name="bidang_id" required>
                                <?php foreach ($bidangList as $bOpt): ?>
                                    <option value="<?= $bOpt['id'] ?>">
                                        <?= htmlspecialchars(($bOpt['kode_bidang'] ? '['.$bOpt['kode_bidang'].'] ' : '') . $bOpt['nama_bidang']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kode_sub_bidang" class="form-label fw-semibold small text-secondary">Kode Singkat Standar (Opsional)</label>
                            <input type="text" class="form-control rounded-3" id="kode_sub_bidang" name="kode_sub_bidang" placeholder="Contoh: LAB-01, TI-OBE-02">
                        </div>

                        <div class="col-12">
                            <label for="nama_sub_bidang" class="form-label fw-semibold small text-secondary">Nama Standar Mutu *</label>
                            <input type="text" class="form-control rounded-3" id="nama_sub_bidang" name="nama_sub_bidang" placeholder="Contoh: Standar Sertifikasi Kompetensi &amp; Rekognisi Internasional" required>
                        </div>

                        <!-- Ruang Lingkup Standar (Fakultas vs Spesifik Prodi) -->
                        <div class="col-md-6">
                            <label for="sub_scope" class="form-label fw-semibold small text-secondary">Cakupan / Target Berlaku Standar *</label>
                            <select class="form-select rounded-3" id="sub_scope" name="scope" onchange="toggleScopeProdi(this.value)" required>
                                <option value="fakultas">Seluruh Fakultas &amp; Semua Prodi Binaan</option>
                                <option value="prodi">Khusus 1 Program Studi Tertentu</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="wrapperProdiScope" style="display: none;">
                            <label for="sub_prodi_id" class="form-label fw-semibold small text-secondary">Pilih Program Studi Sasaran *</label>
                            <select class="form-select rounded-3" id="sub_prodi_id" name="prodi_id">
                                <option value="">-- Pilih Program Studi --</option>
                                <?php foreach ($prodisList as $pOpt): ?>
                                    <option value="<?= $pOpt['id'] ?>">
                                        <?= htmlspecialchars(($pOpt['jenjang'] ?? '') . ' ' . ($pOpt['nama_prodi'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="sub_deskripsi" class="form-label fw-semibold small text-secondary">Deskripsi &amp; Indikator Ketercapaian</label>
                            <textarea class="form-control rounded-3" id="sub_deskripsi" name="deskripsi" rows="3" placeholder="Penjelasan atau indikator ketercapaian standar ini..."></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" id="sub_is_active" name="is_active" value="1" checked>
                                <label class="form-check-label small fw-semibold" for="sub_is_active">Aktifkan standar ini dalam pilihan form unggah dokumen</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light py-2 px-4 rounded-bottom-4">
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
.bg-purple-subtle {
    background-color: #F3E8FF;
}
.text-purple {
    color: #7E22CE;
}
.border-purple-subtle {
    border-color: #E9D5FF;
}
</style>

<script>
// Modal Bidang Functions
function resetBidangForm() {
    document.getElementById('modalBidangTitle').textContent = 'Tambah Bidang Mutu Khusus';
    document.getElementById('bidang_id').value = '';
    document.getElementById('kode_bidang').value = '';
    document.getElementById('nama_bidang').value = '';
    document.getElementById('bidang_deskripsi').value = '';
    document.getElementById('bidang_is_active').checked = true;
}

function editBidang(bidang) {
    document.getElementById('modalBidangTitle').textContent = 'Ubah Bidang Mutu: ' + bidang.nama_bidang;
    document.getElementById('bidang_id').value = bidang.id;
    document.getElementById('kode_bidang').value = bidang.kode_bidang || '';
    document.getElementById('nama_bidang').value = bidang.nama_bidang;
    document.getElementById('bidang_deskripsi').value = bidang.deskripsi || '';
    document.getElementById('bidang_is_active').checked = (parseInt(bidang.is_active) === 1);
}

// Modal Standar (Sub-Bidang) Functions
function openAddSubBidang(bidangId, namaBidang) {
    document.getElementById('modalSubBidangTitle').textContent = 'Tambah Standar Mutu';
    document.getElementById('modalSubBidangSubtitle').textContent = 'Bidang: ' + namaBidang;
    document.getElementById('sub_id').value = '';
    document.getElementById('sub_bidang_id').value = bidangId;
    document.getElementById('kode_sub_bidang').value = '';
    document.getElementById('nama_sub_bidang').value = '';
    document.getElementById('sub_deskripsi').value = '';
    document.getElementById('sub_scope').value = 'fakultas';
    document.getElementById('sub_prodi_id').value = '';
    toggleScopeProdi('fakultas');
    document.getElementById('sub_is_active').checked = true;

    const modal = new bootstrap.Modal(document.getElementById('modalSubBidang'));
    modal.show();
}

function openEditSubBidang(sub, namaBidang) {
    document.getElementById('modalSubBidangTitle').textContent = 'Ubah Standar Mutu';
    document.getElementById('modalSubBidangSubtitle').textContent = 'Bidang: ' + (namaBidang || '-');
    document.getElementById('sub_id').value = sub.id;
    document.getElementById('sub_bidang_id').value = sub.bidang_id;
    document.getElementById('kode_sub_bidang').value = sub.kode_sub_bidang || '';
    document.getElementById('nama_sub_bidang').value = sub.nama_sub_bidang;
    document.getElementById('sub_deskripsi').value = sub.deskripsi || '';
    
    const scope = sub.scope || 'fakultas';
    document.getElementById('sub_scope').value = scope;
    document.getElementById('sub_prodi_id').value = sub.prodi_id || '';
    toggleScopeProdi(scope);
    
    document.getElementById('sub_is_active').checked = (parseInt(sub.is_active) === 1);

    const modal = new bootstrap.Modal(document.getElementById('modalSubBidang'));
    modal.show();
}

function toggleScopeProdi(scope) {
    const wrapper = document.getElementById('wrapperProdiScope');
    const prodiSelect = document.getElementById('sub_prodi_id');
    if (scope === 'prodi') {
        wrapper.style.display = 'block';
        prodiSelect.required = true;
    } else {
        wrapper.style.display = 'none';
        prodiSelect.required = false;
        prodiSelect.value = '';
    }
}

// Delete confirmations
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const name = this.getAttribute('data-name');
            if (confirm(`Apakah Anda yakin ingin menghapus Bidang Mutu "${name}"? Standar mutu khusus di bawahnya juga akan terhapus.`)) {
                form.submit();
            }
        });
    });

    document.querySelectorAll('.btn-delete-sub-confirm').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const name = this.getAttribute('data-name');
            const docs = parseInt(this.getAttribute('data-docs') || '0');
            let msg = `Apakah Anda yakin ingin menghapus Standar Mutu "${name}"?`;
            if (docs > 0) {
                msg += `\n\nPerhatian: Standar ini telah terkait dengan ${docs} dokumen mutu. Standar akan dinonaktifkan demi menjaga keutuhan riwayat dokumen.`;
            }
            if (confirm(msg)) {
                form.submit();
            }
        });
    });
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/admin_footer.php'; ?>
