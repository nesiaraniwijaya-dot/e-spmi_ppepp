<?php
/**
 * Halaman Manajemen Akses & Tenggat Waktu Unggah Dokumen (LPM)
 * SPMI PPEPP (MITRA) Universitas Katolik Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-decoration-none text-muted"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark-blue fw-semibold" aria-current="page">Akses &amp; Tenggat Upload</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark-blue mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-key text-primary"></i> Manajemen Akses &amp; Tenggat Waktu Unggah
            </h3>
            <p class="text-muted small mb-0">Kelola tahun akademik yang aktif, batas waktu tenggat pengunggahan (deadline), dan persetujuan dispensasi upload dokumen tahun lampau untuk Program Studi dan Fakultas.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-2xs" data-bs-toggle="modal" data-bs-target="#modalAcademicYear" onclick="openCreateYearModal()">
                <i class="fas fa-plus me-1"></i> Atur Tahun Akademik
            </button>
            <a href="<?= base_url('admin/monitoring-gpm') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-2xs">
                <i class="fas fa-shield-halved me-1"></i> Monitoring GPM
            </a>
        </div>
    </div>

    <!-- Alert Flash Messages -->
    <?php if ($flash = Auth::getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-<?= $flash['type'] === 'success' ? 'circle-check text-success' : 'triangle-exclamation text-danger' ?> fs-5"></i>
                <div><?= $flash['message'] ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Summary KPI Header Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase;">Tahun Akademik Aktif</span>
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success p-2"><i class="fas fa-calendar-check fa-lg"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-dark mb-0"><?= htmlspecialchars($activeAy['tahun_akademik'] ?? 'Belum Diset') ?></div>
                <div class="text-muted small mt-1" style="font-size: 0.74rem;">
                    Batas Unggah: <strong><?= !empty($activeAy['deadline_upload']) ? date('d M Y, H:i', strtotime($activeAy['deadline_upload'])) . ' WIB' : 'Tidak Dibatasi' ?></strong>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase;">Permohonan Menunggu</span>
                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning p-2"><i class="fas fa-hourglass-half fa-lg"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-warning mb-0"><?= $pendingCount ?></div>
                <div class="text-muted small mt-1" style="font-size: 0.74rem;">Pengajuan izin buka akses upload lampau</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase;">Izin Disetujui</span>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary p-2"><i class="fas fa-unlock fa-lg"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-primary mb-0"><?= $approvedCount ?></div>
                <div class="text-muted small mt-1" style="font-size: 0.74rem;">Dispensasi akses yang telah diberikan</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase;">Total Master Tahun</span>
                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info p-2"><i class="fas fa-database fa-lg"></i></span>
                </div>
                <div class="fs-3 fw-bolder text-info mb-0"><?= count($deadlines) ?></div>
                <div class="text-muted small mt-1" style="font-size: 0.74rem;">Tahun akademik terdaftar dalam sistem</div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs Section -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <ul class="nav nav-pills gap-2 mb-4 pb-2 border-bottom" id="accessTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 py-2 fw-semibold d-flex align-items-center gap-2" id="tab-requests-btn" data-bs-toggle="pill" data-bs-target="#tab-requests" type="button" role="tab">
                    <i class="fas fa-key"></i> Permohonan Akses Upload Unit
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 0.68rem;"><?= $pendingCount ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 py-2 fw-semibold d-flex align-items-center gap-2" id="tab-deadlines-btn" data-bs-toggle="pill" data-bs-target="#tab-deadlines" type="button" role="tab">
                    <i class="fas fa-calendar-days"></i> Master Tahun Akademik &amp; Tenggat (Deadline)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="accessTabsContent">
            <!-- TAB 1: Permohonan Akses dari Unit -->
            <div class="tab-pane fade show active" id="tab-requests" role="tabpanel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold text-dark-blue mb-0">Daftar Pengajuan Izin Upload Dokumen Tahun Lampau / Susulan</h6>
                        <div class="text-muted small" style="font-size: 0.75rem;">Program Studi atau Fakultas yang ingin mengunggah dokumen di luar tahun aktif atau lewat tenggat mengajukan permohonan di sini.</div>
                    </div>
                </div>

                <?php if (empty($requests)): ?>
                    <div class="text-center py-5">
                        <div class="text-muted opacity-50 mb-2"><i class="fas fa-inbox fa-3x"></i></div>
                        <h6 class="fw-bold text-dark">Belum Ada Permohonan Akses</h6>
                        <p class="text-muted small mb-0">Belum ada Program Studi atau Fakultas yang mengajukan permohonan buka akses upload.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                            <thead class="table-light text-secondary fw-semibold" style="font-size: 0.74rem; text-transform: uppercase;">
                                <tr>
                                    <th class="ps-3 py-3" style="width: 40px;">#</th>
                                    <th class="py-3" style="min-width: 200px;">Unit Pemohon</th>
                                    <th class="py-3" style="min-width: 140px;">Tahun Akademik</th>
                                    <th class="py-3" style="min-width: 260px;">Alasan Pengajuan</th>
                                    <th class="py-3" style="min-width: 140px;">Tanggal Diajukan</th>
                                    <th class="py-3" style="min-width: 140px;">Status Permohonan</th>
                                    <th class="py-3" style="min-width: 180px;">Dispensasi Akses</th>
                                    <th class="py-3 text-end pe-3" style="min-width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rNo = 1;
                                foreach ($requests as $r): 
                                    $isPending = ($r['status'] === 'pending');
                                    $isApproved = ($r['status'] === 'approved');
                                    $isRejected = ($r['status'] === 'rejected');
                                    $isExpired = false;
                                    if ($isApproved && !empty($r['akses_berlaku_sampai'])) {
                                        $isExpired = (time() > strtotime($r['akses_berlaku_sampai']));
                                    }
                                ?>
                                    <tr>
                                        <td class="ps-3 text-muted fw-bold"><?= $rNo++ ?></td>
                                        <td>
                                            <div class="fw-bold text-dark mb-0.5">
                                                <?= !empty($r['nama_prodi']) ? htmlspecialchars($r['jenjang'] . ' ' . $r['nama_prodi']) : 'Dekanat ' . htmlspecialchars($r['nama_fakultas'] ?? 'Fakultas') ?>
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.72rem;">
                                                <i class="fas fa-user me-1"></i><?= htmlspecialchars($r['pemohon_name']) ?> (<?= htmlspecialchars($r['pemohon_role']) ?>)
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-primary border fw-bold px-2 py-1" style="font-size: 0.75rem;">
                                                TA <?= htmlspecialchars($r['tahun_akademik']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-dark small" style="line-height: 1.45; white-space: pre-wrap; font-size: 0.8rem;">
                                                <?= htmlspecialchars($r['alasan']) ?>
                                            </div>
                                            <?php if (!empty($r['catatan_lpm'])): ?>
                                                <div class="p-1.5 mt-1 rounded bg-light border text-muted small" style="font-size: 0.72rem;">
                                                    <strong>Catatan LPM:</strong> <?= htmlspecialchars($r['catatan_lpm']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                <i class="far fa-clock me-1"></i><?= date('d M Y, H:i', strtotime($r['created_at'])) ?> WIB
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($isPending): ?>
                                                <span class="badge bg-warning bg-opacity-20 text-warning-emphasis border border-warning rounded-pill px-2.5 py-1">
                                                    <i class="fas fa-hourglass-half me-1"></i> Menunggu
                                                </span>
                                            <?php elseif ($isApproved): ?>
                                                <?php if ($isExpired): ?>
                                                    <span class="badge bg-secondary rounded-pill px-2.5 py-1">
                                                        <i class="fas fa-lock me-1"></i> Telah Berakhir
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success rounded-pill px-2.5 py-1">
                                                        <i class="fas fa-check-circle me-1"></i> Disetujui (Aktif)
                                                    </span>
                                                <?php endif; ?>
                                            <?php elseif ($isRejected): ?>
                                                <span class="badge bg-danger rounded-pill px-2.5 py-1">
                                                    <i class="fas fa-times-circle me-1"></i> Ditolak
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($isApproved && !empty($r['akses_berlaku_sampai'])): ?>
                                                <div class="small fw-semibold <?= $isExpired ? 'text-muted' : 'text-success' ?>" style="font-size: 0.75rem;">
                                                    <i class="far fa-calendar-check me-1"></i>s/d <?= date('d M Y, H:i', strtotime($r['akses_berlaku_sampai'])) ?> WIB
                                                </div>
                                                <?php if (!empty($r['processor_name'])): ?>
                                                    <div class="text-muted" style="font-size: 0.68rem;">Oleh: <?= htmlspecialchars($r['processor_name']) ?></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <?php if ($isPending): ?>
                                                <div class="d-inline-flex gap-1">
                                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 fw-semibold shadow-2xs"
                                                            style="font-size: 0.75rem;"
                                                            onclick="openApproveModal(<?= htmlspecialchars(json_encode($r)) ?>)">
                                                        <i class="fas fa-check me-1"></i> Setujui
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold shadow-2xs"
                                                            style="font-size: 0.75rem;"
                                                            onclick="openRejectModal(<?= htmlspecialchars(json_encode($r)) ?>)">
                                                        <i class="fas fa-times me-1"></i> Tolak
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 shadow-2xs"
                                                        style="font-size: 0.75rem;"
                                                        onclick="openApproveModal(<?= htmlspecialchars(json_encode($r)) ?>)">
                                                    <i class="fas fa-pen-to-square me-1"></i> Sesuaikan
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 2: Master Tahun Akademik & Deadline -->
            <div class="tab-pane fade" id="tab-deadlines" role="tabpanel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold text-dark-blue mb-0">Daftar Tahun Akademik &amp; Batas Tenggat Pengunggahan Dokumen</h6>
                        <div class="text-muted small" style="font-size: 0.75rem;">Hanya Tahun Akademik yang berstatus Aktif yang dapat diunggah secara bebas oleh unit sebelum batas tenggat berakhir.</div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-2xs" data-bs-toggle="modal" data-bs-target="#modalAcademicYear" onclick="openCreateYearModal()">
                        <i class="fas fa-plus me-1"></i> Tambah Tahun Akademik
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                        <thead class="table-light text-secondary fw-semibold" style="font-size: 0.74rem; text-transform: uppercase;">
                            <tr>
                                <th class="ps-3 py-3" style="width: 40px;">#</th>
                                <th class="py-3" style="min-width: 150px;">Tahun Akademik</th>
                                <th class="py-3" style="min-width: 140px;">Status Aktif</th>
                                <th class="py-3" style="min-width: 220px;">Tenggat Waktu Unggah (Deadline)</th>
                                <th class="py-3" style="min-width: 200px;">Keterangan</th>
                                <th class="py-3 text-end pe-3" style="min-width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $dNo = 1;
                            foreach ($deadlines as $d): 
                                $isAct = ($d['is_active'] == 1);
                                $isPastDeadline = false;
                                if (!empty($d['deadline_upload'])) {
                                    $isPastDeadline = (time() > strtotime($d['deadline_upload']));
                                }
                            ?>
                                <tr>
                                    <td class="ps-3 text-muted fw-bold"><?= $dNo++ ?></td>
                                    <td>
                                        <span class="fs-6 fw-bold text-dark"><?= htmlspecialchars($d['tahun_akademik']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($isAct): ?>
                                            <span class="badge bg-success rounded-pill px-2.5 py-1 fw-bold shadow-2xs">
                                                <i class="fas fa-circle-check me-1"></i> Aktif Berjalan
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1">
                                                Tahun Lampau (Terkunci)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($d['deadline_upload'])): ?>
                                            <div class="fw-semibold <?= $isPastDeadline ? 'text-danger' : 'text-dark' ?>" style="font-size: 0.8rem;">
                                                <i class="far fa-calendar-alt me-1"></i><?= date('d M Y, H:i', strtotime($d['deadline_upload'])) ?> WIB
                                            </div>
                                            <div class="small <?= $isPastDeadline ? 'text-danger' : 'text-success' ?>" style="font-size: 0.7rem;">
                                                <?= $isPastDeadline ? '<i class="fas fa-lock me-1"></i>Telah Berakhir (Terkunci Otomatis)' : '<i class="fas fa-unlock me-1"></i>Unggah Dibuka' ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small">Tidak dibatasi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-muted small"><?= htmlspecialchars($d['keterangan'] ?? '-') ?></span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-2xs"
                                                onclick="openEditYearModal(<?= htmlspecialchars(json_encode($d)) ?>)">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Tahun Akademik -->
<div class="modal fade" id="modalAcademicYear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-navy text-white px-4 py-3 border-0">
                <h6 class="modal-title text-white fw-bold mb-0" id="ayModalTitle" style="color: #ffffff !important;">
                    <i class="fas fa-calendar-plus me-1 text-info"></i> Atur Tahun Akademik &amp; Tenggat Waktu
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/upload-access/save-deadline') ?>" method="POST">
                <input type="hidden" name="id" id="ay_id" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-tahun-akademik" id="ay_tahun_akademik" name="tahun_akademik" placeholder="Contoh: 2026/2027" maxlength="9" required oninput="autoPairTahunAkademik(this, event)">
                        <div class="form-text small" style="font-size: 0.72rem;">Format: <code>YYYY/YYYY</code> (Contoh: 2026/2027)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Batas Waktu Tenggat Unggah (Deadline)</label>
                        <input type="datetime-local" class="form-control" id="ay_deadline_upload" name="deadline_upload">
                        <div class="form-text small" style="font-size: 0.72rem;">Setelah tanggal &amp; jam ini, pengunggahan dokumen pada tahun akademik ini otomatis terkunci.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Keterangan / Catatan</label>
                        <input type="text" class="form-control" id="ay_keterangan" name="keterangan" placeholder="Contoh: Tahun Akademik Berjalan SCU">
                    </div>

                    <div class="form-check form-switch p-3 rounded-3 bg-light border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="ay_is_active" name="is_active" value="1">
                        <label class="form-check-label fw-bold text-dark small" for="ay_is_active">
                            Jadikan sebagai Tahun Akademik Aktif Saat Ini
                        </label>
                        <div class="text-muted small ps-4 mt-0.5" style="font-size: 0.72rem;">Jika dicentang, tahun akademik lain otomatis dinonaktifkan.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-2.5">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Setujui Permohonan Akses (Approve) -->
<div class="modal fade" id="modalApproveRequest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white px-4 py-3 border-0">
                <h6 class="modal-title text-white fw-bold mb-0" style="color: #ffffff !important;">
                    <i class="fas fa-unlock me-1"></i> Persetujuan Akses Unggah Dokumen
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/upload-access/process-request') ?>" method="POST">
                <input type="hidden" name="request_id" id="approve_req_id" value="">
                <input type="hidden" name="action" value="approve">
                <div class="modal-body p-4">
                    <div class="alert alert-success border-0 p-3 rounded-3 mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle fs-5"></i>
                        <div class="small">
                            Menyetujui permohonan akses unggah untuk <strong id="approve_unit_name"></strong> pada Tahun Akademik <strong id="approve_ta_name"></strong>.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Durasi Masa Berlaku Akses Dispensasi <span class="text-danger">*</span></label>
                        <select class="form-select" name="durasi_hari" id="approve_durasi" onchange="toggleCustomExpiry(this.value)">
                            <option value="3">3 Hari ke Depan (Standar Pengunggahan Cepat)</option>
                            <option value="7" selected>7 Hari ke Depan (1 Minggu Penuh)</option>
                            <option value="14">14 Hari ke Depan (2 Minggu)</option>
                            <option value="30">30 Hari (1 Bulan Penuh)</option>
                            <option value="custom">Pilih Tanggal &amp; Jam Kustom...</option>
                        </select>
                    </div>

                    <div class="mb-3" id="custom_expiry_wrap" style="display: none;">
                        <label class="form-label fw-semibold small text-secondary">Tanggal &amp; Waktu Berakhir Kustom <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="custom_expired_at" id="custom_expired_at">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Catatan Persetujuan LPM (Opsional)</label>
                        <textarea class="form-control" name="catatan_lpm" rows="2" placeholder="Contoh: Diberikan akses dispensasi untuk kelengkapan berkas akreditasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-2.5">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 fw-semibold text-white">
                        <i class="fas fa-check me-1"></i> Buka Akses Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tolak Permohonan Akses (Reject) -->
<div class="modal fade" id="modalRejectRequest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white px-4 py-3 border-0">
                <h6 class="modal-title text-white fw-bold mb-0" style="color: #ffffff !important;">
                    <i class="fas fa-ban me-1"></i> Tolak Permohonan Akses Unggah
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/upload-access/process-request') ?>" method="POST">
                <input type="hidden" name="request_id" id="reject_req_id" value="">
                <input type="hidden" name="action" value="reject">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="catatan_lpm" rows="3" required placeholder="Tuliskan alasan mengapa permohonan ini ditolak agar unit dapat memahaminya..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-2.5">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-semibold">
                        <i class="fas fa-ban me-1"></i> Tolak Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateYearModal() {
    document.getElementById('ayModalTitle').innerHTML = '<i class="fas fa-calendar-plus me-1 text-info"></i> Tambah Tahun Akademik';
    document.getElementById('ay_id').value = '';
    document.getElementById('ay_tahun_akademik').value = '';
    document.getElementById('ay_deadline_upload').value = '';
    document.getElementById('ay_keterangan').value = '';
    document.getElementById('ay_is_active').checked = false;
}

function openEditYearModal(data) {
    document.getElementById('ayModalTitle').innerHTML = '<i class="fas fa-edit me-1 text-info"></i> Edit Tahun Akademik: ' + data.tahun_akademik;
    document.getElementById('ay_id').value = data.id || '';
    document.getElementById('ay_tahun_akademik').value = data.tahun_akademik || '';
    
    // Format datetime-local (YYYY-MM-DDTHH:MM)
    if (data.deadline_upload) {
        document.getElementById('ay_deadline_upload').value = data.deadline_upload.replace(' ', 'T').substring(0, 16);
    } else {
        document.getElementById('ay_deadline_upload').value = '';
    }
    document.getElementById('ay_keterangan').value = data.keterangan || '';
    document.getElementById('ay_is_active').checked = (data.is_active == 1);

    const modal = new bootstrap.Modal(document.getElementById('modalAcademicYear'));
    modal.show();
}

function openApproveModal(req) {
    document.getElementById('approve_req_id').value = req.id;
    const unitName = req.nama_prodi ? (req.jenjang + ' ' + req.nama_prodi) : ('Dekanat ' + (req.nama_fakultas || 'Fakultas'));
    document.getElementById('approve_unit_name').textContent = unitName;
    document.getElementById('approve_ta_name').textContent = req.tahun_akademik;
    
    const modal = new bootstrap.Modal(document.getElementById('modalApproveRequest'));
    modal.show();
}

function openRejectModal(req) {
    document.getElementById('reject_req_id').value = req.id;
    const modal = new bootstrap.Modal(document.getElementById('modalRejectRequest'));
    modal.show();
}

function toggleCustomExpiry(val) {
    document.getElementById('custom_expiry_wrap').style.display = (val === 'custom') ? 'block' : 'none';
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
