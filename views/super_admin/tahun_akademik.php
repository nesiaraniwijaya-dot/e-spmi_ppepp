<?php
/**
 * Super Admin & LPM: Manajemen Tahun Akademik & Periode Buka/Tutup Upload
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-primary bg-opacity-10 text-primary small fw-bold mb-2">
                <i class="fas fa-calendar-check"></i> Otoritas Kendali LPM
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Manajemen Tahun Akademik &amp; Periode Upload</h3>
            <p class="text-muted small mb-0">Kontrol penuh LPM untuk membuka atau menutup jatah pengunggahan dokumen PPEPP per tahun ajaran (Siklus 1 September s/d 31 Agustus).</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTahunAkademik" onclick="resetFormTA()">
                <i class="fas fa-plus"></i>
                <span>Tambah Tahun Akademik</span>
            </button>
        </div>
    </div>

    <!-- Callout Ketentuan Kalender Akademik -->
    <div class="card border-0 shadow-sm rounded-4 p-3.5 mb-4" style="background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); border-left: 5px solid #2563EB !important;">
        <div class="d-flex align-items-start gap-3">
            <div class="p-2.5 rounded-circle bg-white text-primary shadow-xs flex-shrink-0">
                <i class="fas fa-info-circle fa-lg"></i>
            </div>
            <div class="lh-sm">
                <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.95rem;">Standar Kalender Siklus Tahun Akademik UNIKA Soegijapranata</div>
                <div class="text-muted small" style="font-size: 0.82rem; line-height: 1.5;">
                    Setiap Tahun Akademik dihitung mulai <strong>1 September</strong> hingga <strong>31 Agustus</strong> tahun berikutnya.
                    Jika status suatu tahun akademik disetel <strong>DITUTUP</strong>, maka seluruh Program Studi maupun Fakultas <strong>secara otomatis akan ditolak</strong> saat mencoba mengunggah dokumen baru pada tahun akademik tersebut.
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <?php
    $totalTA = count($tahunList);
    $openCount = count(array_filter($tahunList, fn($t) => !empty($t['is_open'])));
    $closedCount = $totalTA - $openCount;
    $totalDocsAll = array_sum(array_column($tahunList, 'total_dokumen'));
    ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-calendar-days fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Periode</div>
                        <h4 class="fw-bold text-dark-blue mb-0"><?= $totalTA ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-lock-open fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Upload Dibuka</div>
                        <h4 class="fw-bold text-success mb-0"><?= $openCount ?> TA</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-lock fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Upload Ditutup</div>
                        <h4 class="fw-bold text-danger mb-0"><?= $closedCount ?> TA</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-file-shield fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Dokumen</div>
                        <h4 class="fw-bold text-dark-blue mb-0"><?= number_format($totalDocsAll) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark-blue mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-table-list text-primary"></i> Daftar Pengaturan Tahun Akademik
            </h5>
            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small">
                Urutan Berdasarkan TA Terbaru
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th style="width: 170px;">Tahun Akademik</th>
                        <th style="width: 250px;">Rentang Kalender (1 Sept - 31 Agt)</th>
                        <th style="width: 140px;" class="text-center">Dokumen Mutu</th>
                        <th>Keterangan / Kebijakan LPM</th>
                        <th style="width: 160px;" class="text-center">Status Jatah Upload</th>
                        <th style="width: 180px;" class="text-center">Aksi LPM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tahunList)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada tahun akademik yang disetel.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($tahunList as $t): ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <span class="badge px-3 py-1.5 rounded-pill font-monospace fw-bold fs-6 <?= $t['is_open'] ? 'bg-primary text-white shadow-xs' : 'bg-light text-dark border' ?>">
                                        <?= htmlspecialchars($t['tahun_akademik']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5 small text-dark">
                                        <i class="far fa-calendar text-muted"></i>
                                        <span><?= date('d M Y', strtotime($t['tanggal_mulai'])) ?></span>
                                        <span class="text-muted">s/d</span>
                                        <span><?= date('d M Y', strtotime($t['tanggal_selesai'])) ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill fw-bold">
                                        <i class="fas fa-file-lines text-primary me-1"></i> <?= (int)$t['total_dokumen'] ?> Berkas
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted"><?= htmlspecialchars($t['catatan'] ?: '-') ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($t['is_open']): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs">
                                            <i class="fas fa-circle-check"></i> DIBUKA (Aktif)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1.5 rounded-pill fw-bold d-inline-flex align-items-center gap-1.5">
                                            <i class="fas fa-lock"></i> DITUTUP (Terkunci)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <!-- Tombol Toggle Buka/Tutup -->
                                        <form action="<?= base_url('admin/tahun-akademik/toggle/' . $t['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin <?= $t['is_open'] ? 'MENUTUP' : 'MEMBUKA' ?> jatah upload untuk Tahun Akademik <?= $t['tahun_akademik'] ?>?')">
                                            <?php if ($t['is_open']): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" title="Tutup Jatah Upload">
                                                    <i class="fas fa-lock"></i>
                                                    <span>Tutup</span>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1 text-white shadow-2xs" title="Buka Jatah Upload">
                                                    <i class="fas fa-lock-open"></i>
                                                    <span>Buka</span>
                                                </button>
                                            <?php endif; ?>
                                        </form>

                                        <!-- Tombol Edit -->
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-muted" onclick='editTA(<?= json_encode($t) ?>)' title="Ubah Pengaturan">
                                            <i class="fas fa-pencil"></i>
                                        </button>
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

<!-- Modal Tambah / Edit Tahun Akademik -->
<div class="modal fade" id="modalTahunAkademik" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= base_url('admin/tahun-akademik/save') ?>" method="POST" id="formTA">
                <input type="hidden" name="id" id="ta_id" value="">
                <div class="modal-header border-bottom p-3.5">
                    <h5 class="modal-title fw-bold text-dark-blue d-flex align-items-center gap-2" id="modalTitleTA">
                        <i class="fas fa-calendar-plus text-primary"></i> Tambah Tahun Akademik
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tahun_akademik" id="ta_tahun" placeholder="Contoh: 2026/2027" required pattern="^\d{4}\/\d{4}$" title="Format: YYYY/YYYY (contoh: 2026/2027)" oninput="autoCalculateDates(this.value)">
                        <div class="form-text small">Gunakan format tahun akademik empat digit, misal: <strong>2026/2027</strong>.</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">Tanggal Mulai (1 Sept)</label>
                            <input type="date" class="form-control" name="tanggal_mulai" id="ta_tgl_mulai" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">Tanggal Selesai (31 Agt)</label>
                            <input type="date" class="form-control" name="tanggal_selesai" id="ta_tgl_selesai" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Catatan / Kebijakan Periode</label>
                        <textarea class="form-control" name="catatan" id="ta_catatan" rows="2" placeholder="Catatan kebijakan LPM untuk tahun akademik ini..."></textarea>
                    </div>

                    <div class="form-check form-switch p-3 bg-light rounded-3 border">
                        <input class="form-check-input ms-0 me-2.5 cursor-pointer" type="checkbox" role="switch" id="ta_is_open" name="is_open" value="1" checked style="width: 2.2em; height: 1.2em;">
                        <label class="form-check-label fw-bold text-dark small cursor-pointer" for="ta_is_open">
                            Buka Jatah Pengunggahan Dokumen
                        </label>
                        <div class="text-muted" style="font-size: 0.72rem;">Jika dimatikan, Fakultas dan Program Studi tidak dapat mengunggah berkas untuk TA ini.</div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-3 py-1.5 small" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-1.5 small fw-semibold bg-scu-blue border-0 shadow-xs">
                        <i class="fas fa-floppy-disk me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function autoCalculateDates(val) {
    val = (val || '').trim();
    const m = val.match(/^(\d{4})\/(\d{4})$/);
    if (m) {
        document.getElementById('ta_tgl_mulai').value = m[1] + '-09-01';
        document.getElementById('ta_tgl_selesai').value = m[2] + '-08-31';
    }
}

function resetFormTA() {
    document.getElementById('formTA').reset();
    document.getElementById('ta_id').value = '';
    document.getElementById('modalTitleTA').innerHTML = '<i class="fas fa-calendar-plus text-primary"></i> Tambah Tahun Akademik';
    document.getElementById('ta_is_open').checked = true;
    
    // Default: Tahun berikutnya
    const currentYear = new Date().getFullYear();
    const defaultTA = currentYear + '/' + (currentYear + 1);
    document.getElementById('ta_tahun').value = defaultTA;
    autoCalculateDates(defaultTA);
}

function editTA(data) {
    document.getElementById('ta_id').value = data.id || '';
    document.getElementById('ta_tahun').value = data.tahun_akademik || '';
    document.getElementById('ta_tgl_mulai').value = data.tanggal_mulai || '';
    document.getElementById('ta_tgl_selesai').value = data.tanggal_selesai || '';
    document.getElementById('ta_catatan').value = data.catatan || '';
    document.getElementById('ta_is_open').checked = (parseInt(data.is_open) === 1);
    document.getElementById('modalTitleTA').innerHTML = '<i class="fas fa-pencil text-primary"></i> Ubah Tahun Akademik ' + (data.tahun_akademik || '');
    
    const modalEl = document.getElementById('modalTahunAkademik');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
