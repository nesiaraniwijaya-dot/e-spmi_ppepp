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
                        <div class="text-muted small" style="font-size: 0.75rem;">Aktifkan tahun akademik berjalan dengan tombol switch. Atur hidupnya batas tenggat waktu unggah (deadline) melalui tombol Atur Tenggat.</div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-2xs" onclick="openCreateYearModal()">
                        <i class="fas fa-plus me-1"></i> Tambah Tahun Akademik
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                        <thead class="table-light text-secondary fw-semibold" style="font-size: 0.74rem; text-transform: uppercase;">
                            <tr>
                                <th class="ps-3 py-3" style="width: 40px;">#</th>
                                <th class="py-3" style="min-width: 140px;">Tahun Akademik</th>
                                <th class="py-3" style="min-width: 180px;">Status Aktif (Berjalan)</th>
                                <th class="py-3" style="min-width: 250px;">Batas Waktu Tenggat Unggah (Deadline)</th>
                                <th class="py-3 text-end pe-3" style="min-width: 130px;">Aksi</th>
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
                                        <!-- Switch On/Off Tahun Akademik Aktif -->
                                        <div class="form-check form-switch d-inline-flex align-items-center gap-2 m-0 p-0">
                                            <input class="form-check-input ms-0 shadow-2xs switch-ta-active" 
                                                   type="checkbox" 
                                                   role="switch" 
                                                   id="switch_ta_<?= $d['id'] ?>" 
                                                   <?= $isAct ? 'checked' : '' ?>
                                                   style="cursor: pointer; width: 2.5em; height: 1.3em;"
                                                   data-id="<?= $d['id'] ?>"
                                                   data-ta="<?= htmlspecialchars($d['tahun_akademik']) ?>"
                                                   onchange="handleToggleActiveYear(<?= $d['id'] ?>, '<?= htmlspecialchars($d['tahun_akademik']) ?>', this)">
                                            <label class="form-check-label small fw-semibold cursor-pointer user-select-none mb-0" for="switch_ta_<?= $d['id'] ?>" id="label_ta_<?= $d['id'] ?>">
                                                <?php if ($isAct): ?>
                                                    <span class="badge bg-success rounded-pill px-2.5 py-1 shadow-2xs">
                                                        <i class="fas fa-circle-check me-1"></i> Aktif Berjalan
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1">
                                                        Nonaktif
                                                    </span>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($d['deadline_upload'])): ?>
                                            <div class="fw-semibold <?= $isPastDeadline ? 'text-danger' : 'text-dark' ?>" style="font-size: 0.82rem;">
                                                <i class="far fa-calendar-alt me-1 text-primary"></i><?= date('d M Y, H:i', strtotime($d['deadline_upload'])) ?> WIB
                                            </div>
                                            <div class="small <?= $isPastDeadline ? 'text-danger' : 'text-success' ?>" style="font-size: 0.72rem;">
                                                <?= $isPastDeadline ? '<i class="fas fa-lock me-1"></i>Telah Berakhir (Terkunci Otomatis)' : '<i class="fas fa-unlock me-1"></i>Unggah Dibuka Bebas' ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="d-inline-flex align-items-center gap-1.5">
                                                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                                                    <i class="fas fa-infinity me-1 text-muted"></i> Tidak Dibatasi (Bebas Tenggat)
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-2xs fw-semibold"
                                                style="font-size: 0.78rem;"
                                                onclick="openDeadlineModal(<?= htmlspecialchars(json_encode($d)) ?>)">
                                            <i class="fas fa-stopwatch me-1"></i> Atur Tenggat
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

<!-- Modal Atur Batas Waktu Tenggat Unggah (Deadline) -->
<div class="modal fade" id="modalDeadlineSetting" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-navy text-white px-4 py-3 border-0">
                <h6 class="modal-title text-white fw-bold mb-0" id="deadlineModalTitle" style="color: #ffffff !important;">
                    <i class="fas fa-stopwatch me-1.5 text-warning"></i> Atur Batas Waktu Tenggat Unggah (Deadline)
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/upload-access/save-deadline') ?>" method="POST" id="formDeadlineSetting">
                <input type="hidden" name="id" id="deadline_ay_id" value="">
                <input type="hidden" name="tahun_akademik" id="deadline_ay_ta" value="">

                <div class="modal-body p-4">
                    <!-- Info Box Tahun Akademik -->
                    <div class="p-3 rounded-3 bg-light border mb-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small mb-0.5" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Tahun Akademik</div>
                            <div class="fs-5 fw-bold text-dark-blue" id="deadline_display_ta">-</div>
                        </div>
                        <div id="deadline_display_badge"></div>
                    </div>

                    <!-- Switch Hidupkan / Matikan Batas Waktu Tenggat (Deadline) -->
                    <div class="card p-3 rounded-3 border mb-3 bg-white shadow-2xs">
                        <div class="form-check form-switch d-flex align-items-center justify-content-between p-0 m-0">
                            <label class="form-check-label fw-bold text-dark small mb-0 pe-2" for="deadline_has_deadline" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-1.5 text-dark-blue">
                                    <i class="fas fa-hourglass-end text-primary"></i> Hidupkan Batas Waktu Tenggat (Deadline)
                                </div>
                                <div class="text-muted fw-normal mt-0.5" style="font-size: 0.72rem;">
                                    Aktifkan jika ingin membatasi batas akhir unggah dokumen untuk tahun akademik ini.
                                </div>
                            </label>
                            <input class="form-check-input ms-2 switch-ta-active" type="checkbox" role="switch" id="deadline_has_deadline" name="has_deadline" value="1" style="cursor: pointer; width: 2.8em; height: 1.4em;" onchange="toggleDeadlineContainer(this.checked)">
                        </div>
                    </div>

                    <!-- Container Input Tanggal & Jam Tenggat (Muncul jika deadline hidup) -->
                    <div id="deadlineContainer" class="mb-2" style="display: none;">
                        <label class="form-label fw-semibold small text-secondary">
                            Batas Tanggal &amp; Jam Tenggat Unggah (WIB) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-white text-primary"><i class="far fa-calendar-check"></i></span>
                            <input type="datetime-local" class="form-control" id="deadline_datetime_input" name="deadline_upload">
                        </div>

                        <!-- Shortcut Preset Deadline -->
                        <div class="d-flex align-items-center gap-1.5 flex-wrap mt-2 pt-1">
                            <span class="text-muted small me-1" style="font-size: 0.72rem;">Preset Cepat:</span>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;" onclick="setDeadlinePreset('end_ay')">
                                <i class="fas fa-calendar-day me-1"></i>31 Agustus 23:59 (Akhir TA)
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;" onclick="setDeadlinePreset('plus_7')">
                                +7 Hari
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;" onclick="setDeadlinePreset('plus_30')">
                                +30 Hari
                            </button>
                        </div>
                    </div>

                    <div id="deadlineContainerOff" class="alert alert-info py-2 px-3 rounded-3 small mb-0" style="font-size: 0.75rem;">
                        <i class="fas fa-info-circle me-1"></i> Status <strong>Tidak Dibatasi</strong>: Pengunggahan dokumen pada tahun akademik ini dapat dilakukan bebas kapan saja tanpa batas tenggat waktu.
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-2.5 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-2xs">
                        <i class="fas fa-save me-1"></i> Simpan Tenggat Waktu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Tahun Akademik Baru -->
<div class="modal fade" id="modalAddAcademicYear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-navy text-white px-4 py-3 border-0">
                <h6 class="modal-title text-white fw-bold mb-0" style="color: #ffffff !important;">
                    <i class="fas fa-calendar-plus me-1.5 text-info"></i> Tambah Tahun Akademik Baru
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/upload-access/save-deadline') ?>" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-tahun-akademik" id="new_ay_input" name="tahun_akademik" placeholder="Contoh: 2027/2028" maxlength="9" required oninput="autoPairTahunAkademik(this, event)">
                        <div class="form-text small" style="font-size: 0.72rem;">Format: <code>YYYY/YYYY</code> (Contoh: 2027/2028)</div>
                    </div>

                    <div class="form-check form-switch p-3 rounded-3 bg-light border mb-3">
                        <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="new_ay_is_active" name="is_active" value="1">
                        <label class="form-check-label fw-bold text-dark small" for="new_ay_is_active" style="cursor: pointer;">
                            Langsung Jadikan sebagai Tahun Akademik Aktif Saat Ini
                        </label>
                        <div class="text-muted small ps-4 mt-0.5" style="font-size: 0.72rem;">Jika diaktifkan, tahun akademik lain otomatis dinonaktifkan.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-secondary">Batas Waktu Tenggat Unggah (Opsional)</label>
                        <input type="datetime-local" class="form-control" id="new_ay_deadline" name="deadline_upload">
                        <div class="form-text small" style="font-size: 0.72rem;">Kosongkan jika pengunggahan dokumen tidak dibatasi waktu.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-2.5 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-2xs">
                        <i class="fas fa-plus me-1"></i> Tambahkan
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
// Toggle switch aktif/nonaktif tahun akademik via AJAX
function handleToggleActiveYear(id, taName, switchEl) {
    const isChecked = switchEl.checked;
    
    // Jika user mencoba mematikan switch yang sedang aktif
    if (!isChecked) {
        switchEl.checked = true;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Tahun Akademik Aktif',
                text: 'Minimal harus ada 1 Tahun Akademik yang aktif berjalan. Untuk menggantinya, silakan aktifkan tahun akademik lain melalui tombol switch-nya.',
                confirmButtonText: 'Mengerti'
            });
        } else {
            alert('Minimal harus ada 1 Tahun Akademik yang aktif berjalan. Silakan aktifkan tahun akademik lain.');
        }
        return;
    }

    switchEl.disabled = true;
    const labelEl = document.getElementById('label_ta_' + id);
    const oldLabelHtml = labelEl ? labelEl.innerHTML : '';
    if (labelEl) {
        labelEl.innerHTML = '<span class="badge bg-secondary rounded-pill px-2 py-0.5"><i class="fas fa-spinner fa-spin me-1"></i>Memproses...</span>';
    }

    fetch('<?= base_url('admin/upload-access/toggle-active') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            id: id,
            is_active: 1
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.switch-ta-active').forEach(sw => {
                const swId = sw.getAttribute('data-id');
                const lbl = document.getElementById('label_ta_' + swId);
                if (parseInt(swId) === parseInt(id)) {
                    sw.checked = true;
                    if (lbl) {
                        lbl.innerHTML = '<span class="badge bg-success rounded-pill px-2.5 py-1 shadow-2xs"><i class="fas fa-circle-check me-1"></i> Aktif Berjalan</span>';
                    }
                } else if (swId) {
                    sw.checked = false;
                    if (lbl) {
                        lbl.innerHTML = '<span class="badge bg-light text-muted border rounded-pill px-2.5 py-1">Nonaktif</span>';
                    }
                }
                sw.disabled = false;
            });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2000
                });
            }
            setTimeout(() => {
                window.location.hash = '#tab-deadlines';
                window.location.reload();
            }, 700);
        } else {
            throw new Error(data.message || 'Gagal mengubah status aktif.');
        }
    })
    .catch(err => {
        switchEl.checked = !isChecked;
        switchEl.disabled = false;
        if (labelEl) labelEl.innerHTML = oldLabelHtml;
        alert(err.message || 'Terjadi kesalahan sistem.');
    });
}

// Buka Modal Atur Tenggat Waktu (Deadline)
function openDeadlineModal(data) {
    document.getElementById('deadline_ay_id').value = data.id || '';
    document.getElementById('deadline_ay_ta').value = data.tahun_akademik || '';
    document.getElementById('deadline_display_ta').textContent = data.tahun_akademik || '-';
    
    const badgeWrap = document.getElementById('deadline_display_badge');
    if (data.is_active == 1) {
        badgeWrap.innerHTML = '<span class="badge bg-success rounded-pill px-2.5 py-1 shadow-2xs"><i class="fas fa-circle-check me-1"></i> Aktif Berjalan</span>';
    } else {
        badgeWrap.innerHTML = '<span class="badge bg-light text-muted border rounded-pill px-2.5 py-1">Nonaktif (Terkunci)</span>';
    }

    const hasDeadline = !!(data.deadline_upload && data.deadline_upload.trim() !== '');
    const switchEl = document.getElementById('deadline_has_deadline');
    switchEl.checked = hasDeadline;
    toggleDeadlineContainer(hasDeadline);

    if (hasDeadline) {
        document.getElementById('deadline_datetime_input').value = data.deadline_upload.replace(' ', 'T').substring(0, 16);
    } else {
        document.getElementById('deadline_datetime_input').value = '';
    }

    const modal = new bootstrap.Modal(document.getElementById('modalDeadlineSetting'));
    modal.show();
}

function toggleDeadlineContainer(isEnabled) {
    const box = document.getElementById('deadlineContainer');
    const boxOff = document.getElementById('deadlineContainerOff');
    const input = document.getElementById('deadline_datetime_input');
    
    if (isEnabled) {
        box.style.display = 'block';
        boxOff.style.display = 'none';
        input.required = true;
        if (!input.value) {
            setDeadlinePreset('end_ay');
        }
    } else {
        box.style.display = 'none';
        boxOff.style.display = 'block';
        input.required = false;
    }
}

function setDeadlinePreset(type) {
    const taVal = document.getElementById('deadline_ay_ta').value || '';
    const now = new Date();
    let targetDate = new Date();

    if (type === 'end_ay') {
        let endYear = now.getFullYear();
        if (taVal && taVal.includes('/')) {
            const parts = taVal.split('/');
            if (parts[1] && parts[1].length === 4) {
                endYear = parseInt(parts[1], 10);
            }
        }
        targetDate = new Date(endYear, 7, 31, 23, 59, 0); // 31 Agustus 23:59
    } else if (type === 'plus_7') {
        targetDate.setDate(targetDate.getDate() + 7);
        targetDate.setHours(23, 59, 0, 0);
    } else if (type === 'plus_30') {
        targetDate.setDate(targetDate.getDate() + 30);
        targetDate.setHours(23, 59, 0, 0);
    }

    const y = targetDate.getFullYear();
    const m = String(targetDate.getMonth() + 1).padStart(2, '0');
    const d = String(targetDate.getDate()).padStart(2, '0');
    const hh = String(targetDate.getHours()).padStart(2, '0');
    const mm = String(targetDate.getMinutes()).padStart(2, '0');
    document.getElementById('deadline_datetime_input').value = `${y}-${m}-${d}T${hh}:${mm}`;
}

function openCreateYearModal() {
    document.getElementById('new_ay_input').value = '';
    document.getElementById('new_ay_is_active').checked = false;
    document.getElementById('new_ay_deadline').value = '';
    const modal = new bootstrap.Modal(document.getElementById('modalAddAcademicYear'));
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

// Menjaga tab tetap di #tab-deadlines jika URL hash ada
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#tab-deadlines') {
        const triggerEl = document.querySelector('#tab-deadlines-btn');
        if (triggerEl) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
