<?php
/**
 * Admin Prodi: Form Upload / Edit Dokumen Mutu PPEPP
 * Diselaraskan mengikuti standar visual & UX GPM (Clean, User-Friendly, Responsive)
 * Mendukung Single Edit dan Batch Multi-Upload hingga 10 Dokumen
 * SPMI PPEPP UNIKA Soegijapranata
 */
$isEdit = !empty($doc);
$defaultTahun = date('Y') . '/' . (date('Y') + 1);
$currentTahun = $isEdit ? ($doc['tahun_akademik'] ?? $defaultTahun) : $defaultTahun;
$currentSiklus = $isEdit ? ($doc['siklus'] ?? '') : '';
$currentJenis = $isEdit ? ($doc['jenis_upload'] ?? 'file') : 'file';
$isLimited = $isEdit ? (!empty($doc['public_page_limit']) && (int)$doc['public_page_limit'] > 0) : true;
$pageLimitVal = $isLimited ? (int)($doc['public_page_limit'] ?? 1) : 1;

require_once ROOT_PATH . '/views/layouts/admin_header.php';

$siklusDefinitions = [
    'penetapan' => [
        'title' => 'Penetapan',
        'code' => 'P1',
        'icon' => 'fa-compass-drafting',
        'theme' => 'penetapan',
        'desc' => 'Penetapan Standar SPMI'
    ],
    'pelaksanaan' => [
        'title' => 'Pelaksanaan',
        'code' => 'P2',
        'icon' => 'fa-person-chalkboard',
        'theme' => 'pelaksanaan',
        'desc' => 'Pelaksanaan & Pemenuhan'
    ],
    'evaluasi' => [
        'title' => 'Evaluasi',
        'code' => 'E',
        'icon' => 'fa-magnifying-glass-chart',
        'theme' => 'evaluasi',
        'desc' => 'Evaluasi Pelaksanaan Standar'
    ],
    'pengendalian' => [
        'title' => 'Pengendalian',
        'code' => 'P3',
        'icon' => 'fa-sliders',
        'theme' => 'pengendalian',
        'desc' => 'Pengendalian & Koreksi'
    ],
    'peningkatan' => [
        'title' => 'Peningkatan',
        'code' => 'P4',
        'icon' => 'fa-arrow-trend-up',
        'theme' => 'peningkatan',
        'desc' => 'Peningkatan Mutu Berkelanjutan'
    ],
];
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.76rem;">
                    <i class="fas fa-graduation-cap"></i> Program Studi <?= htmlspecialchars($prodi['nama_prodi']) ?>
                </span>
                <span class="badge bg-light text-secondary border"><?= htmlspecialchars($prodi['jenjang']) ?> &bull; <?= htmlspecialchars($prodi['nama_fakultas'] ?? '') ?></span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">
                <?= $isEdit ? 'Ubah Dokumen Mutu' : 'Unggah Dokumen Mutu Baru' ?>
            </h3>
            <p class="text-muted small mb-0">
                Dokumen mutu resmi Program Studi <strong><?= htmlspecialchars($prodi['nama_prodi']) ?></strong>.
                <?php if (!$isEdit): ?>
                    &bull; <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold" style="font-size:0.75rem;">Mendukung Batch Upload hingga 10 Dokumen</span>
                <?php endif; ?>
            </p>
        </div>
        <a href="<?= base_url('prodi/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Dokumen
        </a>
    </div>

    <?php if ($isEdit): ?>
        <!-- ========================================== -->
        <!-- MODE EDIT DOKUMEN TUNGGAL (ADMIN PRODI)    -->
        <!-- ========================================== -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white" style="border: 1px solid #E2E8F0 !important;">
            <form action="<?= base_url('prodi/dokumen/save') ?>" method="POST" enctype="multipart/form-data" id="documentUploadForm" novalidate>
                <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                
                <?php if (($doc['status_review'] ?? '') === 'perlu_perbaikan'): ?>
                    <!-- Executive Review Notice Callout -->
                    <div class="rounded-4 mb-4 p-3.5 p-md-4 shadow-sm" style="background: #FFFDFD; border: 1px solid #FECACA; border-left: 6px solid #DC2626;">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 pb-2.5 border-bottom" style="border-color: #FEE2E2 !important;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger text-white rounded-pill px-3 py-1.5 fw-bold shadow-2xs" style="font-size: 0.78rem;">
                                    <i class="fas fa-triangle-exclamation me-1.5"></i> Perlu Revisi Dokumen
                                </span>
                                <span class="fw-bold text-dark-blue small">
                                    Catatan Evaluasi Penjaminan Mutu (LPM)
                                </span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2 small" style="font-size: 0.76rem;">
                                <span class="d-inline-flex align-items-center gap-1.5 bg-white border border-danger-subtle rounded-pill px-3 py-1 text-dark shadow-2xs">
                                    <i class="fas fa-building-columns text-primary"></i>
                                    <strong class="text-dark">Pusat Penjaminan Mutu LPM</strong>
                                </span>
                                <?php if (!empty($doc['reviewed_at'])): ?>
                                    <span class="d-inline-flex align-items-center gap-1 bg-white border border-danger-subtle rounded-pill px-2.5 py-1 text-muted shadow-2xs">
                                        <i class="far fa-clock text-secondary"></i>
                                        <?= date('d M Y, H:i', strtotime($doc['reviewed_at'])) ?> WIB
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Feedback Content Area -->
                        <div class="p-3 rounded-3 bg-white border border-danger-subtle mb-3">
                            <div class="text-muted fw-semibold mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.76rem;">
                                <i class="fas fa-comment-dots text-danger"></i> Poin-Poin Perbaikan yang Harus Dipenuhi:
                            </div>
                            <div class="text-dark fw-medium lh-base text-break" style="font-size: 0.88rem; white-space: pre-line;">
                                <?= htmlspecialchars($doc['catatan_review'] ?? 'Silakan lakukan revisi sesuai standar penjaminan mutu SPMI.') ?>
                            </div>
                        </div>

                        <!-- Directive Sub-banner -->
                        <div class="d-flex align-items-center gap-2 text-danger small pt-1">
                            <i class="fas fa-circle-info flex-shrink-0"></i>
                            <span>Silakan perbarui isian atau berkas di bawah ini, lalu klik tombol <strong>"Simpan &amp; Ajukan Ulang ke LPM"</strong> untuk verifikasi lanjutan.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <!-- Left Column: Identitas & Unit Dokumen -->
                    <div class="col-lg-7 border-end-lg pe-lg-4">
                        <div class="d-flex align-items-center justify-content-between pb-3 mb-3.5 border-bottom">
                            <h5 class="fw-bold text-dark-blue mb-0 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary p-2" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fas fa-file-lines"></i>
                                </span>
                                <span>Identitas Dokumen Mutu</span>
                            </h5>
                        </div>

                        <!-- Fixed Unit Info Card -->
                        <div class="p-3.5 rounded-4 mb-4 border" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="small text-muted fw-semibold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Unit Pengunggah Dokumen:</span>
                                    <div class="fw-bold text-dark-blue fs-6 mt-0.5">
                                        <i class="fas fa-graduation-cap text-success me-1.5"></i> <?= htmlspecialchars($prodi['nama_prodi']) ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        Jenjang: <?= htmlspecialchars($prodi['jenjang']) ?> &bull; Kode: <?= htmlspecialchars($prodi['kode_prodi']) ?> &bull; <?= htmlspecialchars($prodi['nama_fakultas'] ?? '') ?>
                                    </div>
                                </div>
                                <span class="badge rounded-pill px-3 py-1.5 fw-semibold shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.78rem;">
                                    Program Studi
                                </span>
                            </div>
                        </div>

                        <!-- Nama Dokumen -->
                        <div class="mb-3">
                            <label for="nama_dokumen" class="form-label fw-semibold small text-secondary">Nama Dokumen Mutu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" 
                                   value="<?= htmlspecialchars($doc['nama_dokumen'] ?? '') ?>" 
                                   placeholder="Contoh: Standar Penilaian Pembelajaran Mahasiswa" required>
                        </div>

                        <!-- Nomor Dokumen & Tahun Akademik -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label for="nomor_dokumen" class="form-label fw-semibold small text-secondary">Nomor Dokumen / SK (Opsional)</label>
                                <input type="text" class="form-control" id="nomor_dokumen" name="nomor_dokumen" 
                                       value="<?= htmlspecialchars($doc['nomor_dokumen'] ?? '') ?>" 
                                       placeholder="Contoh: SK-PRODI/2026/01">
                            </div>
                            <div class="col-md-5">
                                <label for="tahun_akademik" class="form-label fw-semibold small text-secondary">
                                    Tahun Akademik <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik" 
                                       value="<?= htmlspecialchars($currentTahun) ?>" 
                                       placeholder="Contoh: 2024/2025" required>
                                <div class="form-text small" style="font-size: 0.72rem;">Format: YYYY/YYYY (Wajib)</div>
                            </div>
                        </div>

                        <!-- Siklus PPEPP (5 Interactive Cards) -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold small text-secondary mb-0">
                                    Kategori Siklus PPEPP <span class="text-danger">*</span>
                                </label>
                                <span class="text-muted small" style="font-size: 0.72rem;">Wajib pilih salah satu tahapan</span>
                            </div>
                            
                            <div class="ppepp-cycle-grid" id="cycleGrid">
                                <?php foreach ($siklusDefinitions as $val => $opt): 
                                    $isSelected = ($currentSiklus === $val);
                                ?>
                                    <label class="ppepp-cycle-card <?= $opt['theme'] ?> <?= $isSelected ? 'selected' : '' ?>">
                                        <input type="radio" name="siklus" value="<?= $val ?>" class="ppepp-cycle-radio" <?= $isSelected ? 'checked' : '' ?> onchange="updateSiklusSelection(this)">
                                        <div class="ppepp-cycle-badge"><?= $opt['code'] ?></div>
                                        <div class="ppepp-cycle-icon-wrap">
                                            <i class="fas <?= $opt['icon'] ?>"></i>
                                        </div>
                                        <div class="ppepp-cycle-title"><?= $opt['title'] ?></div>
                                        <div class="ppepp-cycle-sub"><?= $opt['code'] ?></div>
                                        <div class="ppepp-cycle-check"><i class="fas fa-check"></i></div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Bidang Dropdown (WAJIB DIISI) -->
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label for="bidang_id" class="form-label fw-semibold small text-secondary">
                                    Bidang <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="bidang_id" name="bidang_id" required onchange="handleSingleBidangChange(this.value)">
                                    <option value="">-- Pilih Bidang --</option>
                                    <?php foreach ($bidangList as $b): ?>
                                        <option value="<?= $b['id'] ?>" <?= (($doc['bidang_id'] ?? '') == $b['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($b['nama_bidang']) ?> <?= !empty($b['kode_bidang']) ? '('.htmlspecialchars($b['kode_bidang']).')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text small" style="font-size: 0.72rem;">Standar mutu berkas &amp; tautan di samping akan otomatis disesuaikan dengan Bidang yang dipilih.</div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Berkas & Kontrol Akses -->
                    <div class="col-lg-5" id="rightUploadCol">
                        <div class="p-4 p-md-4.5 bg-light rounded-4 border h-100 d-flex flex-column gap-3.5" style="background: #F8FAFC !important; border-color: #E2E8F0 !important;">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-1 border-bottom">
                                <h5 class="fw-bold text-dark-blue mb-0 d-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-15 text-warning p-2" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                        <i class="fas fa-paperclip"></i>
                                    </span>
                                    <span>Metode &amp; Berkas Dokumen</span>
                                </h5>
                            </div>

                            <!-- Interactive Method Cards -->
                            <div class="mb-2">
                                <label class="form-label fw-bold small text-dark mb-2.5 d-flex align-items-center gap-1.5">
                                    <i class="fas fa-layer-group text-primary"></i> Pilih Metode Berkas <span class="text-danger">*</span>
                                </label>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative <?= $currentJenis === 'file' ? 'active' : '' ?>" 
                                             id="card_method_file" onclick="toggleUploadMethod('file')">
                                            <input type="radio" class="d-none radio-jenis-upload" name="jenis_upload" id="jenis_file" value="file" <?= $currentJenis === 'file' ? 'checked' : '' ?>>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background: #FEE2E2; color: #DC2626;">
                                                    <i class="fas fa-file-arrow-up fa-lg"></i>
                                                </span>
                                                <div class="lh-sm pe-3">
                                                    <div class="fw-bold text-dark-blue small">Unggah Berkas</div>
                                                    <div class="text-muted" style="font-size: 0.72rem; margin-top: 2px;">PDF, DOCX, XLSX</div>
                                                </div>
                                            </div>
                                            <div class="method-check-badge position-absolute top-0 end-0 p-1.5" style="<?= $currentJenis === 'file' ? '' : 'display:none;' ?>">
                                                <i class="fas fa-circle-check text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative <?= $currentJenis === 'link' ? 'active' : '' ?>" 
                                             id="card_method_link" onclick="toggleUploadMethod('link')">
                                            <input type="radio" class="d-none radio-jenis-upload" name="jenis_upload" id="jenis_link" value="link" <?= $currentJenis === 'link' ? 'checked' : '' ?>>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background: #E0F2FE; color: #0284C7;">
                                                    <i class="fab fa-google-drive fa-lg"></i>
                                                </span>
                                                <div class="lh-sm pe-3">
                                                    <div class="fw-bold text-dark-blue small">Link GDrive</div>
                                                    <div class="text-muted" style="font-size: 0.72rem; margin-top: 2px;">Tautan Cloud</div>
                                                </div>
                                            </div>
                                            <div class="method-check-badge position-absolute top-0 end-0 p-1.5" style="<?= $currentJenis === 'link' ? '' : 'display:none;' ?>">
                                                <i class="fas fa-circle-check text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Unggah Berkas Fisik -->
                            <div id="section_file" style="<?= $currentJenis === 'file' ? '' : 'display:none;' ?>">
                                <!-- Existing Files -->
                                <?php if (!empty($docFiles)): ?>
                                    <div class="mb-3.5">
                                        <label class="form-label fw-semibold small text-secondary d-flex justify-content-between mb-2">
                                            <span>Berkas Tersimpan Saat Ini:</span>
                                            <span class="badge bg-light text-secondary border"><?= count($docFiles) ?> Berkas</span>
                                        </label>
                                        <div class="d-flex flex-column gap-3" id="existingFilesContainer">
                                            <?php foreach ($docFiles as $f): 
                                                $fLimited = isset($f['is_page_limited']) ? (int)$f['is_page_limited'] === 1 : true;
                                                $fLimitVal = isset($f['public_page_limit']) && (int)$f['public_page_limit'] > 0 ? (int)$f['public_page_limit'] : 1;
                                                $fCanDownload = !empty($f['can_download_public']) && (int)$f['can_download_public'] === 1;
                                                $fSubIds = !empty($f['sub_bidang_ids']) 
                                                    ? (is_array($f['sub_bidang_ids']) ? $f['sub_bidang_ids'] : json_decode($f['sub_bidang_ids'], true)) 
                                                    : (!empty($doc['sub_bidang_id']) ? [(int)$doc['sub_bidang_id']] : []);
                                                if (!is_array($fSubIds)) $fSubIds = [];
                                                $fSize = !empty($f['file_size']) ? (is_numeric($f['file_size']) ? number_format($f['file_size']/1024, 1).' KB' : $f['file_size']) : 'PDF';
                                            ?>
                                                <div class="card p-3.5 bg-white border rounded-4 shadow-2xs" id="existingFileRow_<?= $f['id'] ?>">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 pb-2.5 border-bottom">
                                                        <div class="d-flex align-items-center gap-2.5 text-truncate" style="min-width: 0;">
                                                            <div class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                                <i class="fas fa-file-pdf fa-lg"></i>
                                                            </div>
                                                            <div class="text-truncate">
                                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;"><?= htmlspecialchars($f['file_name']) ?></div>
                                                                <div class="text-muted" style="font-size: 0.74rem;"><?= $fSize ?></div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                            <a href="<?= base_url($f['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill d-inline-flex align-items-center gap-1.5 fw-semibold" title="Unduh / Lihat" style="font-size: 0.78rem;">
                                                                <i class="fas fa-eye"></i> <span>Lihat</span>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill d-inline-flex align-items-center gap-1.5 fw-semibold transition-all" id="delBtn_<?= $f['id'] ?>" title="Hapus Berkas Ini" onclick="markFileForDeletion(<?= $f['id'] ?>)" style="font-size: 0.78rem;">
                                                                <i class="fas fa-trash-can"></i> <span>Hapus</span>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="mt-2.5">
                                                        <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                                            <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Berkas Ini:
                                                        </label>
                                                        <textarea class="form-control py-2" name="existing_file_narasi[<?= $f['id'] ?>]" rows="1" placeholder="Tuliskan keterangan isi berkas ini..."><?= htmlspecialchars($f['narasi'] ?? '') ?></textarea>
                                                    </div>

                                                    <!-- Standar Mutu Picker Per-File -->
                                                    <?= render_sub_standar_picker_html("existing_file_sub_bidang[{$f['id']}][]", $fSubIds, "ex_file_{$f['id']}", $bidangList, $subBidangList, $doc['bidang_id'] ?? null) ?>

                                                    <!-- Per-File Access Settings (Lega, Bersih, Rapi) -->
                                                    <div class="p-3 rounded-3 bg-light border mt-2.5">
                                                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                                            <div class="d-flex align-items-center justify-content-between gap-2.5 flex-grow-1">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="form-check form-switch m-0">
                                                                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="ex_limit_sw_<?= $f['id'] ?>" name="existing_file_is_limited[<?= $f['id'] ?>]" value="1" <?= $fLimited ? 'checked' : '' ?> onchange="document.getElementById('ex_limit_wrap_<?= $f['id'] ?>').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                                                                    </div>
                                                                    <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="ex_limit_sw_<?= $f['id'] ?>" style="font-size: 0.82rem;">
                                                                        Batasi Pratinjau
                                                                    </label>
                                                                </div>
                                                                <div class="input-group input-group-sm" id="ex_limit_wrap_<?= $f['id'] ?>" style="width: 95px; <?= $fLimited ? 'display:inline-flex;' : 'display:none;' ?>">
                                                                    <input type="number" class="form-control text-center py-1 fw-bold" name="existing_file_page_limit[<?= $f['id'] ?>]" min="1" max="50" value="<?= $fLimitVal ?>" style="font-size: 0.78rem;">
                                                                    <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                                                                </div>
                                                            </div>

                                                            <div class="vr d-none d-sm-block bg-secondary opacity-25" style="height: 28px;"></div>

                                                            <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2.5">
                                                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="ex_dl_sw_<?= $f['id'] ?>" name="existing_file_can_download[<?= $f['id'] ?>]" value="1" <?= $fCanDownload ? 'checked' : '' ?> style="width: 2.2em; height: 1.2em;">
                                                                    <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="ex_dl_sw_<?= $f['id'] ?>" style="font-size: 0.82rem;">
                                                                        Unduh Publik
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="delete-indicator text-danger small fw-semibold d-none mt-2" id="delInd_<?= $f['id'] ?>" style="font-size: 0.78rem;">
                                                        <i class="fas fa-exclamation-triangle me-1"></i> Berkas akan dihapus saat disimpan. Klik Batal Hapus untuk membatalkan.
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Dropzone Berkas Baru -->
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2.5">
                                        <label class="form-label fw-bold small text-dark mb-0 d-flex align-items-center gap-1.5">
                                            <i class="fas fa-file-arrow-up text-secondary"></i>
                                            <span><?= !empty($docFiles) ? 'Tambah Berkas Baru' : 'Berkas Dokumen *' ?></span>
                                        </label>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bisa > 1 File</span>
                                    </div>
                                    <div class="upload-dropzone-box text-center cursor-pointer shadow-2xs mb-2" id="fileDropZone" onclick="document.getElementById('single_files').click()">
                                        <div class="mb-2">
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary shadow-2xs" style="width: 50px; height: 50px;">
                                                <i class="fas fa-cloud-arrow-up fa-lg"></i>
                                            </span>
                                        </div>
                                        <div class="fw-bold text-dark mb-2 small">Pilih Berkas Dokumen &amp; Lampiran</div>
                                        <div class="d-flex justify-content-center gap-1.5 flex-wrap my-2.5">
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;"><i class="fas fa-file-pdf me-0.5"></i> PDF</span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;"><i class="fas fa-file-word me-0.5"></i> DOCX</span>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;"><i class="fas fa-file-excel me-0.5"></i> XLSX</span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-0.5" style="font-size: 0.7rem;">Maks 25 MB</span>
                                        </div>
                                        <input type="file" id="single_files" name="files[]" multiple class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx" onchange="handleSingleFileInput(this)">
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold shadow-xs my-1" style="font-size: 0.8rem;" onclick="event.stopPropagation(); document.getElementById('single_files').click()">
                                            <i class="fas fa-plus me-1"></i> Telusuri Berkas
                                        </button>
                                        <div class="text-muted mt-2.5 d-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.73rem;">
                                            <i class="fas fa-lightbulb text-warning"></i> <span>Tarik &amp; lepas berkas ke area ini atau klik tombol di atas</span>
                                        </div>
                                    </div>
                                    <div id="newFilesPreviewContainer" class="d-flex flex-column gap-2 mt-2"></div>
                                </div>
                            </div>

                            <!-- Section Tautan Google Drive -->
                            <div id="section_link" style="<?= $currentJenis === 'link' ? '' : 'display:none;' ?>">
                                <div class="alert alert-info border-0 p-3 rounded-3 mb-3 d-flex align-items-start gap-2.5 shadow-2xs" style="background: #EFF6FF; border-left: 3.5px solid #3B82F6 !important;">
                                    <i class="fas fa-circle-info text-primary mt-1 flex-shrink-0" style="font-size: 0.9rem;"></i>
                                    <div class="small" style="font-size: 0.75rem; line-height: 1.45; color: #1E40AF;">
                                        <strong>Panduan Hak Akses:</strong> Pastikan tautan Google Drive disetel ke <em>"Siapa saja yang memiliki tautan"</em> (Pelihat/Viewer) agar berkas dapat diakses dan ditinjau oleh tim penjaminan mutu.
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2.5">
                                    <label class="form-label fw-bold small text-dark mb-0 d-flex align-items-center gap-1.5">
                                        <i class="fab fa-google-drive text-primary"></i> Daftar Tautan Dokumen <span class="text-danger">*</span>
                                    </label>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bisa > 1 Link</span>
                                </div>
                                
                                <div id="gdriveLinksContainer" class="d-flex flex-column gap-3 mb-3">
                                    <?php 
                                    $savedLinks = parse_external_links($doc['external_link'] ?? '');
                                    if (empty($savedLinks)):
                                        $initSubIds = !empty($doc['sub_bidang_id']) ? [(int)$doc['sub_bidang_id']] : [];
                                    ?>
                                        <div class="gdrive-link-row card p-3.5 bg-white border rounded-4 shadow-2xs">
                                            <div class="d-flex align-items-center justify-content-between pb-2.5 border-bottom">
                                                <span class="fw-bold text-dark-blue d-flex align-items-center gap-2 gdrive-row-title" style="font-size: 0.88rem;">
                                                    <div class="p-1.5 rounded-3 bg-success bg-opacity-10 text-success flex-shrink-0">
                                                        <i class="fab fa-google-drive"></i>
                                                    </div>
                                                    <span>Tautan Berkas #1</span>
                                                </span>
                                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill fw-semibold" style="font-size: 0.78rem;" onclick="removeSingleGdriveLinkRow(this)" title="Batalkan / Hapus tautan ini">
                                                    <i class="fas fa-trash-can me-1"></i> Hapus
                                                </button>
                                            </div>
                                            <div class="mt-2.5 mb-2">
                                                <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                                    <i class="fas fa-link text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                                                    <input type="url" class="form-control py-2" name="external_links[]" placeholder="https://drive.google.com/file/d/...">
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                                    <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Tautan: <span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control py-2" name="external_link_narasi[]" rows="1" placeholder="Keterangan / Narasi tautan dokumen ini..."></textarea>
                                            </div>
                                            <!-- Standar Mutu Picker Per-Link -->
                                            <?= render_sub_standar_picker_html("external_link_sub_bidang[0][]", $initSubIds, "init_link_0", $bidangList, $subBidangList, $doc['bidang_id'] ?? null) ?>
                                            
                                            <!-- Per-Link Access Settings (Seragam dengan Softfile) -->
                                            <div class="p-3 rounded-3 bg-light border mt-2.5">
                                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                                    <div class="d-flex align-items-center justify-content-between gap-2.5 flex-grow-1">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="form-check form-switch m-0">
                                                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="init_link_limit_sw_0" name="external_link_is_limited[0]" value="1" checked onchange="document.getElementById('init_link_limit_wrap_0').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                                                            </div>
                                                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="init_link_limit_sw_0" style="font-size: 0.82rem;">
                                                                Batasi Pratinjau
                                                            </label>
                                                        </div>
                                                        <div class="input-group input-group-sm" id="init_link_limit_wrap_0" style="width: 95px;">
                                                            <input type="number" class="form-control text-center py-1 fw-bold" name="external_link_page_limit[0]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                                                            <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                                                        </div>
                                                    </div>

                                                    <div class="vr d-none d-sm-block bg-secondary opacity-25" style="height: 28px;"></div>

                                                    <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2.5">
                                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="init_link_dl_sw_0" name="external_link_can_download[0]" value="1" style="width: 2.2em; height: 1.2em;">
                                                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="init_link_dl_sw_0" style="font-size: 0.82rem;">
                                                                Unduh Publik
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: foreach ($savedLinks as $lIdx => $l): 
                                        $lSubIds = !empty($l['sub_bidang_ids']) 
                                            ? (is_array($l['sub_bidang_ids']) ? $l['sub_bidang_ids'] : json_decode($l['sub_bidang_ids'], true)) 
                                            : (!empty($doc['sub_bidang_id']) ? [(int)$doc['sub_bidang_id']] : []);
                                        if (!is_array($lSubIds)) $lSubIds = [];
                                        $lLimited = isset($l['is_page_limited']) ? (int)$l['is_page_limited'] === 1 : true;
                                        $lLimitVal = isset($l['public_page_limit']) && (int)$l['public_page_limit'] > 0 ? (int)$l['public_page_limit'] : 1;
                                        $lCanDownload = !empty($l['can_download_public']) && (int)$l['can_download_public'] === 1;
                                    ?>
                                        <div class="gdrive-link-row card p-3.5 bg-white border rounded-4 shadow-2xs">
                                            <div class="d-flex align-items-center justify-content-between pb-2.5 border-bottom">
                                                <span class="fw-bold text-dark-blue d-flex align-items-center gap-2 gdrive-row-title" style="font-size: 0.88rem;">
                                                    <div class="p-1.5 rounded-3 bg-success bg-opacity-10 text-success flex-shrink-0">
                                                        <i class="fab fa-google-drive"></i>
                                                    </div>
                                                    <span>Tautan Berkas #<?= $lIdx + 1 ?></span>
                                                </span>
                                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill fw-semibold" style="font-size: 0.78rem;" onclick="removeSingleGdriveLinkRow(this)" title="Batalkan / Hapus tautan ini">
                                                    <i class="fas fa-trash-can me-1"></i> Hapus
                                                </button>
                                            </div>
                                            <div class="mt-2.5 mb-2">
                                                <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                                    <i class="fas fa-link text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                                                    <input type="url" class="form-control py-2" name="external_links[]" value="<?= htmlspecialchars($l['url']) ?>" placeholder="https://drive.google.com/file/d/...">
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                                    <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Tautan: <span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control py-2" name="external_link_narasi[]" rows="1" placeholder="Keterangan / Narasi tautan dokumen ini..."><?= htmlspecialchars($l['narasi'] ?? '') ?></textarea>
                                            </div>
                                            <!-- Standar Mutu Picker Per-Link -->
                                            <?= render_sub_standar_picker_html("external_link_sub_bidang[{$lIdx}][]", $lSubIds, "ex_link_{$lIdx}", $bidangList, $subBidangList, $doc['bidang_id'] ?? null) ?>

                                            <!-- Per-Link Access Settings (Seragam dengan Softfile) -->
                                            <div class="p-3 rounded-3 bg-light border mt-2.5">
                                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                                    <div class="d-flex align-items-center justify-content-between gap-2.5 flex-grow-1">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="form-check form-switch m-0">
                                                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="ex_link_limit_sw_<?= $lIdx ?>" name="external_link_is_limited[<?= $lIdx ?>]" value="1" <?= $lLimited ? 'checked' : '' ?> onchange="document.getElementById('ex_link_limit_wrap_<?= $lIdx ?>').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                                                            </div>
                                                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="ex_link_limit_sw_<?= $lIdx ?>" style="font-size: 0.82rem;">
                                                                Batasi Pratinjau
                                                            </label>
                                                        </div>
                                                        <div class="input-group input-group-sm" id="ex_link_limit_wrap_<?= $lIdx ?>" style="width: 95px; <?= $lLimited ? 'display:inline-flex;' : 'display:none;' ?>">
                                                            <input type="number" class="form-control text-center py-1 fw-bold" name="external_link_page_limit[<?= $lIdx ?>]" min="1" max="50" value="<?= $lLimitVal ?>" style="font-size: 0.78rem;">
                                                            <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                                                        </div>
                                                    </div>

                                                    <div class="vr d-none d-sm-block bg-secondary opacity-25" style="height: 28px;"></div>

                                                    <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2.5">
                                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="ex_link_dl_sw_<?= $lIdx ?>" name="external_link_can_download[<?= $lIdx ?>]" value="1" <?= $lCanDownload ? 'checked' : '' ?> style="width: 2.2em; height: 1.2em;">
                                                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="ex_link_dl_sw_<?= $lIdx ?>" style="font-size: 0.82rem;">
                                                                Unduh Publik
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill w-100 py-2.5 fw-semibold mb-2" onclick="addSingleGdriveLinkRow()">
                                    <i class="fas fa-plus me-1.5"></i> Tambah Tautan Lain
                                </button>
                            </div>



                            <!-- Tombol Submit Form Edit -->
                            <div class="mt-auto pt-4 border-top">
                                <div class="d-flex flex-column gap-2">
                                    <button type="submit" name="action_submit" value="publish" class="btn btn-warning w-100 py-3 fw-bold rounded-pill text-dark shadow-sm">
                                        <i class="fas fa-paper-plane me-1"></i> <?= ($doc['status_review'] === 'perlu_perbaikan') ? 'Simpan &amp; Ajukan Ulang ke LPM' : 'Simpan &amp; Ajukan ke LPM' ?>
                                    </button>
                                    <button type="submit" name="action_submit" value="draft" class="btn btn-outline-secondary w-100 py-2 fw-semibold rounded-pill">
                                        <i class="fas fa-floppy-disk me-1"></i> Simpan Sebagai Draf
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    <?php else: ?>
        <!-- ========================================== -->
        <!-- MODE CREATE: BATCH MULTI-UPLOAD (1-10 DOK)  -->
        <!-- ========================================== -->
        <form action="<?= base_url('prodi/dokumen/save') ?>" method="POST" enctype="multipart/form-data" id="batchDocForm" novalidate>
            
            <!-- Quick Helper & Action Bar -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3.5 rounded-4 bg-white border shadow-2xs mb-4 gap-3" style="border: 1px solid #E2E8F0 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:42px; height:42px; background: #059669;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark-blue small">Formulir Multi-Unggah Dokumen Mutu Program Studi</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Anda dapat mengunggah hingga 10 dokumen mutu sekaligus dalam satu kali proses simpan.</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3.5 py-2 fw-semibold d-flex align-items-center gap-1.5 shadow-xs" onclick="addNewDocCard()">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Kartu Dokumen</span>
                    </button>
                </div>
            </div>

            <!-- Container Kartu-Kartu Dokumen -->
            <div id="batchCardsContainer" class="d-flex flex-column gap-4 mb-4">
                <!-- Kartu pertama akan di-render di sini oleh JavaScript -->
            </div>

            <!-- Bottom Floating Action Bar -->
            <div class="card bg-white border shadow-lg rounded-4 p-3.5 sticky-bottom" style="z-index: 1020; margin-bottom: 2rem; border-color: #E2E8F0 !important;">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill" style="font-size: 0.78rem;">
                            Total Kartu: <strong id="totalCardCounter" class="text-dark">1</strong> / 10 Dokumen
                        </span>
                        <span class="text-muted small d-none d-md-inline" style="font-size: 0.75rem;">
                            Pastikan kolom bertanda bintang (*) termasuk Standar Mutu telah terisi.
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 w-100 w-sm-auto justify-content-end">
                        <button type="submit" name="action_submit" value="draft" class="btn btn-outline-secondary rounded-pill px-3.5 py-2.5 fw-semibold d-flex align-items-center gap-2" id="btnSubmitDraftBatch">
                            <i class="fas fa-floppy-disk"></i>
                            <span>Simpan Sebagai Draf</span>
                        </button>
                        <button type="submit" name="action_submit" value="publish" class="btn btn-warning rounded-pill px-4 py-2.5 fw-bold text-dark shadow-sm d-flex align-items-center gap-2" id="btnSubmitPublishBatch">
                            <i class="fas fa-paper-plane"></i>
                            <span>Terbitkan &amp; Ajukan ke LPM</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    <?php endif; ?>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT LOGIC FORM ADMIN PRODI          -->
<!-- ========================================== -->
<script>
const SIKLUS_DEFS = <?= json_encode($siklusDefinitions) ?>;
const BIDANGS_DATA = <?= json_encode($bidangList ?? []) ?>;
const SUB_BIDANGS_DATA = <?= json_encode($subBidangList ?? []) ?>;
const PRODI_NAME = <?= json_encode($prodi['nama_prodi'] ?? 'Program Studi') ?>;
const PRODI_JENJANG = <?= json_encode($prodi['jenjang'] ?? 'S1') ?>;
const IS_EDIT_MODE = <?= $isEdit ? 'true' : 'false' ?>;

// Sub-bidang filtering helper
function getFilteredSubBidang(bidangId) {
    if (!bidangId) return [];
    return SUB_BIDANGS_DATA.filter(sb => sb.bidang_id == bidangId);
}

// Single Edit Mode Helpers
function handleSingleBidangChange(bidangId) {
    const rightCol = document.getElementById('rightUploadCol');
    if (rightCol) {
        updateSubStandarPickersInContainer(rightCol, bidangId);
    }
}

function updateSiklusSelection(radio) {
    document.querySelectorAll('.ppepp-cycle-card').forEach(c => c.classList.remove('selected'));
    const parentCard = radio.closest('.ppepp-cycle-card');
    if (parentCard) parentCard.classList.add('selected');
}

function toggleUploadMethod(method) {
    const isFile = (method === 'file');
    const fileSec = document.getElementById('section_file');
    const linkSec = document.getElementById('section_link');
    if (fileSec) fileSec.style.display = isFile ? 'block' : 'none';
    if (linkSec) linkSec.style.display = isFile ? 'none' : 'block';

    const rFile = document.getElementById('jenis_file');
    const rLink = document.getElementById('jenis_link');
    if (rFile && rLink) {
        if (isFile) rFile.checked = true;
        else rLink.checked = true;
    }

    const cFile = document.getElementById('card_method_file') || document.getElementById('lbl_method_file');
    const cLink = document.getElementById('card_method_link') || document.getElementById('lbl_method_link');
    if (cFile && cLink) {
        const bFile = cFile.querySelector('.method-check-badge');
        const bLink = cLink.querySelector('.method-check-badge');
        if (isFile) {
            cFile.classList.add('active');
            cLink.classList.remove('active');
            if (bFile) bFile.style.display = 'block';
            if (bLink) bLink.style.display = 'none';
        } else {
            cLink.classList.add('active');
            cFile.classList.remove('active');
            if (bFile) bFile.style.display = 'none';
            if (bLink) bLink.style.display = 'block';
        }
    }
}

function toggleSinglePageLimit(checked) {
    const wrap = document.getElementById('singlePageLimitWrap');
    if (wrap) wrap.style.display = checked ? 'block' : 'none';
}

function markFileForDeletion(fileId) {
    const row = document.getElementById(`existingFileRow_${fileId}`);
    if (!row) return;
    let input = document.getElementById(`delFileInput_${fileId}`);
    const indicator = document.getElementById(`delInd_${fileId}`);
    const btn = document.getElementById(`delBtn_${fileId}`);
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_file_ids[]';
        input.value = fileId;
        input.id = `delFileInput_${fileId}`;
        row.appendChild(input);
        row.style.opacity = '0.65';
        row.style.background = '#FEE2E2';
        row.style.borderColor = '#F87171';
        if (indicator) indicator.classList.remove('d-none');
        if (btn) {
            btn.className = 'btn btn-sm btn-danger text-white py-1 px-2.5 rounded-pill d-inline-flex align-items-center gap-1.5 shadow-xs';
            btn.style.backgroundColor = '#DC2626';
            btn.style.borderColor = '#DC2626';
            btn.style.color = '#FFFFFF';
            btn.innerHTML = '<i class="fas fa-trash-can text-white"></i> <span>Batal Hapus</span>';
            btn.title = 'Batalkan Hapus Berkas Ini';
        }
    } else {
        input.remove();
        row.style.opacity = '1';
        row.style.background = '';
        row.style.borderColor = '';
        if (indicator) indicator.classList.add('d-none');
        if (btn) {
            btn.className = 'btn btn-sm btn-outline-danger py-1 px-2.5 rounded-pill d-inline-flex align-items-center gap-1.5 transition-all';
            btn.style.backgroundColor = '';
            btn.style.borderColor = '';
            btn.style.color = '';
            btn.innerHTML = '<i class="fas fa-trash-can text-danger"></i> <span>Hapus</span>';
            btn.title = 'Hapus Berkas Ini';
        }
    }
}

function removeSingleStagedFile(fileIndex) {
    const input = document.getElementById('single_files') || document.getElementById('fileInput');
    if (!input || !input.files) return;
    const dt = new DataTransfer();
    Array.from(input.files).forEach((file, idx) => {
        if (idx !== fileIndex) {
            dt.items.add(file);
        }
    });
    input.files = dt.files;
    handleSingleFileInput(input);
}

function handleSingleFileInput(input) {
    const container = document.getElementById('newFilesPreviewContainer');
    if (!container || !input.files) return;
    container.innerHTML = '';
    const curBidangId = document.getElementById('bidang_id')?.value || null;

    Array.from(input.files).forEach((file, idx) => {
        const ext = file.name.split('.').pop().toLowerCase();
        let iconClass = 'fa-file-pdf text-danger';
        let bgClass = 'bg-danger bg-opacity-10 text-danger';
        if (['doc', 'docx'].includes(ext)) {
            iconClass = 'fa-file-word text-primary';
            bgClass = 'bg-primary bg-opacity-10 text-primary';
        } else if (['xls', 'xlsx'].includes(ext)) {
            iconClass = 'fa-file-excel text-success';
            bgClass = 'bg-success bg-opacity-10 text-success';
        } else if (['ppt', 'pptx'].includes(ext)) {
            iconClass = 'fa-file-powerpoint text-warning';
            bgClass = 'bg-warning bg-opacity-10 text-warning';
        }

        const pickerHtml = renderSubStandarPicker(`new_file_sub_bidang[${idx}][]`, [], `new_file_${idx}`, curBidangId);

        const div = document.createElement('div');
        div.className = 'card p-3.5 bg-white border rounded-4 shadow-2xs';
        div.innerHTML = `
            <div class="d-flex align-items-center justify-content-between gap-2 pb-2.5 border-bottom">
                <div class="d-flex align-items-center gap-2.5 text-truncate" style="min-width: 0;">
                    <div class="p-2 rounded-3 ${bgClass} flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas ${iconClass} fa-lg"></i>
                    </div>
                    <div class="text-truncate">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">${file.name}</div>
                        <div class="text-muted" style="font-size: 0.74rem;">${(file.size / (1024*1024)).toFixed(2)} MB</div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill d-inline-flex align-items-center gap-1.5 fw-semibold transition-all" style="font-size: 0.78rem;" onclick="removeSingleStagedFile(${idx})" title="Batalkan unggah berkas ini">
                        <i class="fas fa-times"></i> <span>Batal</span>
                    </button>
                </div>
            </div>

            <div class="mt-2.5">
                <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                    <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Berkas Ini: <span class="text-danger">*</span>
                </label>
                <textarea class="form-control py-2" name="new_file_narasi[${idx}]" rows="1" placeholder="Tuliskan keterangan isi berkas '${file.name}'..." required></textarea>
            </div>

            ${pickerHtml}

            <!-- Per-File Access Settings (Lega, Bersih, Rapi) -->
            <div class="p-3 rounded-3 bg-light border mt-2.5">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div class="d-flex align-items-center justify-content-between gap-2.5 flex-grow-1">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="new_limit_sw_${idx}" name="new_file_is_limited[${idx}]" value="1" checked onchange="document.getElementById('new_limit_wrap_${idx}').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                            </div>
                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="new_limit_sw_${idx}" style="font-size: 0.82rem;">
                                Batasi Pratinjau
                            </label>
                        </div>
                        <div class="input-group input-group-sm" id="new_limit_wrap_${idx}" style="width: 95px;">
                            <input type="number" class="form-control text-center py-1 fw-bold" name="new_file_page_limit[${idx}]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                            <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                        </div>
                    </div>

                    <div class="vr d-none d-sm-block bg-secondary opacity-25" style="height: 28px;"></div>

                    <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2.5">
                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="new_dl_sw_${idx}" name="new_file_can_download[${idx}]" value="1" style="width: 2.2em; height: 1.2em;">
                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="new_dl_sw_${idx}" style="font-size: 0.82rem;">
                                Unduh Publik
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
    });
}

function removeSingleGdriveLinkRow(btn) {
    const container = document.getElementById('gdriveLinksContainer');
    if (!container) return;
    const row = btn.closest('.gdrive-link-row');
    if (row) {
        if (container.querySelectorAll('.gdrive-link-row').length > 1) {
            row.remove();
            renumberSingleGdriveLinks();
        } else {
            row.querySelectorAll('input, textarea').forEach(inp => inp.value = '');
        }
    }
}

function renumberSingleGdriveLinks() {
    const rows = document.querySelectorAll('#gdriveLinksContainer .gdrive-link-row');
    rows.forEach((row, i) => {
        const title = row.querySelector('.gdrive-row-title');
        if (title) title.innerHTML = `<div class="p-1.5 rounded-3 bg-success bg-opacity-10 text-success flex-shrink-0"><i class="fab fa-google-drive"></i></div> <span>Tautan Berkas #${i + 1}</span>`;
    });
}

function addSingleGdriveLinkRow() {
    const container = document.getElementById('gdriveLinksContainer');
    if (!container) return;
    const count = container.querySelectorAll('.gdrive-link-row').length + 1;
    const row = document.createElement('div');
    row.className = 'gdrive-link-row card p-3.5 bg-white border rounded-4 shadow-2xs';
    const curBidangId = document.getElementById('bidang_id')?.value || null;
    const pickerHtml = renderSubStandarPicker(`external_link_sub_bidang[${count - 1}][]`, [], `new_link_${count}_${Math.random().toString(36).substring(2,6)}`, curBidangId);

    row.innerHTML = `
        <div class="d-flex align-items-center justify-content-between pb-2.5 border-bottom">
            <span class="fw-bold text-dark-blue d-flex align-items-center gap-2 gdrive-row-title" style="font-size: 0.88rem;">
                <div class="p-1.5 rounded-3 bg-success bg-opacity-10 text-success flex-shrink-0">
                    <i class="fab fa-google-drive"></i>
                </div>
                <span>Tautan Berkas #${count}</span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill fw-semibold" style="font-size: 0.78rem;" onclick="removeSingleGdriveLinkRow(this)" title="Batalkan / Hapus tautan ini">
                <i class="fas fa-trash-can me-1"></i> Hapus
            </button>
        </div>
        <div class="mt-2.5 mb-2">
            <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                <i class="fas fa-link text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                <input type="url" class="form-control py-2" name="external_links[]" placeholder="https://drive.google.com/file/d/...">
            </div>
        </div>
        <div class="mb-2">
            <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Tautan: <span class="text-danger">*</span>
            </label>
            <textarea class="form-control py-2" name="external_link_narasi[]" rows="1" placeholder="Keterangan / Narasi tautan dokumen ini..."></textarea>
        </div>
        ${pickerHtml}

        <!-- Per-Link Access Settings (Seragam dengan Softfile) -->
        <div class="p-3 rounded-3 bg-light border mt-2.5">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                <div class="d-flex align-items-center justify-content-between gap-2.5 flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="new_link_limit_sw_${count}" name="external_link_is_limited[${count - 1}]" value="1" checked onchange="document.getElementById('new_link_limit_wrap_${count}').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                        </div>
                        <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="new_link_limit_sw_${count}" style="font-size: 0.82rem;">
                            Batasi Pratinjau
                        </label>
                    </div>
                    <div class="input-group input-group-sm" id="new_link_limit_wrap_${count}" style="width: 95px;">
                        <input type="number" class="form-control text-center py-1 fw-bold" name="external_link_page_limit[${count - 1}]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                        <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                    </div>
                </div>

                <div class="vr d-none d-sm-block bg-secondary opacity-25" style="height: 28px;"></div>

                <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2.5">
                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="new_link_dl_sw_${count}" name="external_link_can_download[${count - 1}]" value="1" style="width: 2.2em; height: 1.2em;">
                        <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="new_link_dl_sw_${count}" style="font-size: 0.82rem;">
                            Unduh Publik
                        </label>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.appendChild(row);
}

// ==========================================
// BATCH MULTI-UPLOAD SYSTEM (UP TO 10 CARDS)
// ==========================================
let cardCounter = 0;

function createDocCardTemplate(idx) {
    const initCardLinkPicker = renderSubStandarPicker(`docs[${idx}][external_link_sub_bidang][0][]`, [], `card_${idx}_link_0`, null);
    let bidangOptions = '<option value="">-- Pilih Bidang --</option>';
    BIDANGS_DATA.forEach(b => {
        bidangOptions += `<option value="${b.id}">${b.nama_bidang}</option>`;
    });

    let cycleCardsHtml = '';
    for (const [key, val] of Object.entries(SIKLUS_DEFS)) {
        cycleCardsHtml += `
            <label class="ppepp-cycle-card ${val.theme}" onclick="selectBatchCycle(${idx}, '${key}', this)">
                <input type="radio" name="docs[${idx}][siklus]" value="${key}" class="ppepp-cycle-radio">
                <div class="ppepp-cycle-badge">${val.code}</div>
                <div class="ppepp-cycle-icon-wrap"><i class="fas ${val.icon}"></i></div>
                <div class="ppepp-cycle-title">${val.title}</div>
                <div class="ppepp-cycle-sub">${val.code}</div>
                <div class="ppepp-cycle-check"><i class="fas fa-check"></i></div>
            </label>
        `;
    }

    return `
    <div class="card border-0 shadow-sm rounded-4 doc-batch-card" id="docCard_${idx}" data-card-idx="${idx}" style="border: 1px solid #E2E8F0 !important;">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between rounded-top-4" style="cursor: pointer;" onclick="toggleCardCollapse(${idx}, event)">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 fw-bold" style="font-size:0.8rem;">
                    Dokumen Mutu #${idx + 1}
                </span>
                <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0; font-size:0.75rem;">
                    <i class="fas fa-graduation-cap me-1"></i> ${PRODI_JENJANG} ${PRODI_NAME}
                </span>
                <span class="fw-semibold text-dark-blue small ms-1" id="doc_title_preview_${idx}">
                    (Belum ada judul)
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                ${idx > 0 ? `
                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1.5" style="width:30px;height:30px;" title="Hapus Kartu Ini" onclick="removeDocCard(${idx}, event)">
                    <i class="fas fa-trash-can"></i>
                </button>
                ` : ''}
                <button type="button" class="btn btn-sm btn-light border rounded-circle p-1.5" style="width:30px;height:30px;" id="collapseBtn_${idx}">
                    <i class="fas fa-chevron-up"></i>
                </button>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4 p-md-4.5" id="cardBody_${idx}">
            <div class="row g-4">
                <!-- Left: Identitas & Standar -->
                <div class="col-lg-7 border-end-lg pe-lg-4">
                    <!-- Fixed Unit Scope Display -->
                    <div class="p-3 rounded-4 mb-3.5 border" style="background:#F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small text-muted fw-semibold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Unit Mutu Pengunggah:</span>
                                <div class="fw-bold text-dark-blue small mt-0.5">
                                    <i class="fas fa-graduation-cap text-success me-1"></i> ${PRODI_JENJANG} ${PRODI_NAME}
                                </div>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                Tingkat Program Studi
                            </span>
                        </div>
                    </div>

                    <!-- Nama Dokumen -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Nama Dokumen Mutu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="docs[${idx}][nama_dokumen]" placeholder="Contoh: Standar Penilaian Pembelajaran" required oninput="updateCardTitlePreview(${idx}, this.value)">
                    </div>

                    <!-- Nomor Dokumen & Tahun Akademik -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small text-secondary">Nomor Dokumen / SK (Opsional)</label>
                            <input type="text" class="form-control" name="docs[${idx}][nomor_dokumen]" placeholder="Contoh: SK-PRODI/2026/01">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-secondary">Tahun Akademik <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="docs[${idx}][tahun_akademik]" value="<?= htmlspecialchars($defaultTahun) ?>" required>
                        </div>
                    </div>

                    <!-- Siklus PPEPP -->
                    <div class="mb-3.5">
                        <label class="form-label fw-semibold small text-secondary mb-2">Kategori Siklus PPEPP <span class="text-danger">*</span></label>
                        <div class="ppepp-cycle-grid">
                            ${cycleCardsHtml}
                        </div>
                    </div>

                    <!-- Bidang Dropdown (WAJIB) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Bidang <span class="text-danger">*</span></label>
                        <select class="form-select" name="docs[${idx}][bidang_id]" required onchange="handleBatchCardBidangChange(${idx}, this.value)">
                            ${bidangOptions}
                        </select>
                    </div>
                </div>

                <!-- Right: Berkas & Kontrol Akses -->
                <div class="col-lg-5">
                    <div class="p-4 p-md-4.5 bg-light rounded-4 border h-100 d-flex flex-column gap-3.5" style="background: #F8FAFC !important; border-color: #E2E8F0 !important;">
                        <!-- Interactive Method Cards for Batch Card -->
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-dark mb-2.5 d-flex align-items-center gap-1.5">
                                <i class="fas fa-layer-group text-primary"></i> Pilih Metode Berkas <span class="text-danger">*</span>
                            </label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative active" 
                                         id="method_card_file_${idx}" onclick="toggleCardMethod(${idx}, 'file')">
                                        <input type="radio" class="d-none" name="docs[${idx}][jenis_upload]" id="card_jenis_file_${idx}" value="file" checked>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background: #FEE2E2; color: #DC2626;">
                                                <i class="fas fa-file-arrow-up fa-lg"></i>
                                            </span>
                                            <div class="lh-sm pe-3">
                                                <div class="fw-bold text-dark-blue small">Unggah Berkas</div>
                                                <div class="text-muted" style="font-size: 0.72rem; margin-top: 2px;">PDF, DOCX, XLSX</div>
                                            </div>
                                        </div>
                                        <div class="method-check-badge position-absolute top-0 end-0 p-1.5" id="badge_check_file_${idx}">
                                            <i class="fas fa-circle-check text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative" 
                                         id="method_card_link_${idx}" onclick="toggleCardMethod(${idx}, 'link')">
                                        <input type="radio" class="d-none" name="docs[${idx}][jenis_upload]" id="card_jenis_link_${idx}" value="link">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background: #E0F2FE; color: #0284C7;">
                                                <i class="fab fa-google-drive fa-lg"></i>
                                            </span>
                                            <div class="lh-sm pe-3">
                                                <div class="fw-bold text-dark-blue small">Link GDrive</div>
                                                <div class="text-muted" style="font-size: 0.72rem; margin-top: 2px;">Tautan Cloud</div>
                                            </div>
                                        </div>
                                        <div class="method-check-badge position-absolute top-0 end-0 p-1.5" id="badge_check_link_${idx}" style="display: none;">
                                            <i class="fas fa-circle-check text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dropzone File Card -->
                        <div id="card_section_file_${idx}">
                            <div class="d-flex justify-content-between align-items-center mb-2.5">
                                <label class="form-label fw-bold small text-dark mb-0 d-flex align-items-center gap-1.5">
                                    <i class="fas fa-file-arrow-up text-secondary"></i> Berkas Dokumen <span class="text-danger">*</span>
                                </label>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bisa > 1 File</span>
                            </div>
                            <div class="upload-dropzone-box text-center cursor-pointer shadow-2xs mb-2" onclick="document.getElementById('card_files_input_${idx}').click()">
                                <div class="mb-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary shadow-2xs" style="width: 50px; height: 50px;">
                                        <i class="fas fa-cloud-arrow-up fa-lg"></i>
                                    </span>
                                </div>
                                <div class="fw-bold small text-dark mb-2">Pilih Berkas Dokumen &amp; Lampiran</div>
                                <div class="d-flex justify-content-center gap-1.5 flex-wrap my-2.5">
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">PDF</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">DOCX</span>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">XLSX</span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-0.5" style="font-size: 0.7rem;">Maks 25MB</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 py-2 cursor-pointer fw-semibold shadow-xs my-1" style="font-size: 0.8rem;" onclick="event.stopPropagation(); document.getElementById('card_files_input_${idx}').click()">
                                    <i class="fas fa-plus me-1"></i> Telusuri Berkas
                                </button>
                                <div class="text-muted mt-2.5 d-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.73rem;">
                                    <i class="fas fa-lightbulb text-warning"></i> <span>Tarik &amp; lepas berkas ke sini atau klik tombol di atas</span>
                                </div>
                            </div>
                            <input type="file" id="card_files_input_${idx}" name="files_${idx}[]" multiple class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx" onchange="handleCardFileInput(${idx}, this)">
                            <div id="card_files_preview_${idx}" class="d-flex flex-column gap-2 mt-2"></div>
                        </div>

                        <!-- GDrive Link Card -->
                        <div id="card_section_link_${idx}" style="display: none;">
                            <div class="alert alert-info border-0 p-3 rounded-3 mb-3 d-flex align-items-start gap-2.5 shadow-2xs" style="background: #EFF6FF; border-left: 3.5px solid #3B82F6 !important;">
                                <i class="fas fa-circle-info text-primary mt-1 flex-shrink-0" style="font-size: 0.9rem;"></i>
                                <div style="font-size: 0.75rem; line-height: 1.45; color: #1E40AF;">
                                    Pastikan setelan berbagi Google Drive: <strong>"Siapa saja yang memiliki tautan"</strong> (Akses Pelihat/Viewer) agar berkas dapat ditinjau oleh pihak LPM & Auditor.
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2.5">
                                <label class="form-label fw-bold small text-dark mb-0 d-flex align-items-center gap-1.5">
                                    <i class="fab fa-google-drive text-primary"></i> Tautan Google Drive <span class="text-danger">*</span>
                                </label>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bisa > 1 Tautan</span>
                            </div>
                            <div id="card_gdrive_links_${idx}" class="d-flex flex-column gap-2.5 mb-3">
                                <div class="card-gdrive-row card p-3.5 bg-white border rounded-4 shadow-2xs">
                                    <div class="d-flex align-items-center justify-content-between pb-2.5 border-bottom">
                                        <span class="fw-bold text-dark-blue d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                                            <div class="p-1.5 rounded-3 bg-success bg-opacity-10 text-success flex-shrink-0">
                                                <i class="fab fa-google-drive"></i>
                                            </div>
                                            <span>Tautan Berkas</span>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill fw-semibold" style="font-size: 0.78rem;" onclick="removeCardGdriveLink(this, ${idx})" title="Batalkan / Hapus tautan ini">
                                            <i class="fas fa-trash-can me-1"></i> Hapus
                                        </button>
                                    </div>
                                    <div class="mt-2.5 mb-2">
                                        <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                            <i class="fas fa-link text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                                            <input type="url" class="form-control py-2" name="docs[${idx}][external_links][]" placeholder="https://drive.google.com/file/d/...">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                            <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Tautan: <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control py-2" name="docs[${idx}][external_link_narasi][]" rows="1" placeholder="Narasi / Keterangan Tautan..."></textarea>
                                    </div>
                                    ${initCardLinkPicker}

                                    <!-- Per-Link Access Settings -->
                                    <div class="p-3 rounded-3 bg-light border mt-2.5">
                                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center justify-content-between gap-2.5 flex-grow-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="form-check form-switch m-0">
                                                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_link_limit_sw_${idx}_0" name="docs[${idx}][external_link_is_limited][0]" value="1" checked onchange="document.getElementById('card_link_limit_wrap_${idx}_0').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                                                    </div>
                                                    <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_link_limit_sw_${idx}_0" style="font-size: 0.82rem;">
                                                        Batasi Pratinjau
                                                    </label>
                                                </div>
                                                <div class="input-group input-group-sm" id="card_link_limit_wrap_${idx}_0" style="width: 95px;">
                                                    <input type="number" class="form-control text-center py-1 fw-bold" name="docs[${idx}][external_link_page_limit][0]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                                                    <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                                                </div>
                                            </div>

                                            <div class="vr d-none d-sm-block bg-secondary opacity-25" style="height: 28px;"></div>

                                            <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2.5">
                                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_link_dl_sw_${idx}_0" name="docs[${idx}][external_link_can_download][0]" value="1" style="width: 2.2em; height: 1.2em;">
                                                    <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_link_dl_sw_${idx}_0" style="font-size: 0.82rem;">
                                                        Unduh Publik
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill w-100 py-2.5 fw-semibold mb-2" onclick="addCardGdriveLink(${idx})">
                                <i class="fas fa-plus me-1.5"></i> Tambah Tautan Lain
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;
}

function updateCardTitlePreview(idx, text) {
    const preview = document.getElementById(`doc_title_preview_${idx}`);
    if (preview) {
        preview.textContent = text.trim() ? text.trim() : '(Belum ada judul)';
    }
}

function selectBatchCycle(idx, cycleKey, elem) {
    const card = document.getElementById(`docCard_${idx}`);
    if (!card) return;
    card.querySelectorAll('.ppepp-cycle-card').forEach(c => c.classList.remove('selected'));
    elem.classList.add('selected');
    const radio = elem.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
}

function handleBatchCardBidangChange(idx, bidangId) {
    const card = document.getElementById(`docCard_${idx}`);
    if (!card) return;
    updateSubStandarPickersInContainer(card, bidangId);
}

function toggleCardMethod(idx, method) {
    const isFile = (method === 'file');
    const fSec = document.getElementById(`card_section_file_${idx}`);
    const lSec = document.getElementById(`card_section_link_${idx}`);
    if (fSec) fSec.style.display = isFile ? 'block' : 'none';
    if (lSec) lSec.style.display = isFile ? 'none' : 'block';

    const rFile = document.getElementById(`card_jenis_file_${idx}`);
    const rLink = document.getElementById(`card_jenis_link_${idx}`);
    if (rFile && rLink) {
        if (isFile) rFile.checked = true;
        else rLink.checked = true;
    }

    const cFile = document.getElementById(`method_card_file_${idx}`) || document.getElementById(`lbl_method_file_${idx}`);
    const cLink = document.getElementById(`method_card_link_${idx}`) || document.getElementById(`lbl_method_link_${idx}`);
    const bFile = document.getElementById(`badge_check_file_${idx}`);
    const bLink = document.getElementById(`badge_check_link_${idx}`);
    
    if (cFile && cLink) {
        if (isFile) {
            cFile.classList.add('active');
            cLink.classList.remove('active');
            if (bFile) bFile.style.display = 'block';
            if (bLink) bLink.style.display = 'none';
        } else {
            cLink.classList.add('active');
            cFile.classList.remove('active');
            if (bFile) bFile.style.display = 'none';
            if (bLink) bLink.style.display = 'block';
        }
    }
}

function toggleCardLimit(idx, checked) {
    const wrap = document.getElementById(`limit_wrap_${idx}`);
    if (wrap) wrap.style.display = checked ? 'flex' : 'none';
}

function toggleCardCollapse(idx, evt) {
    if (evt.target.closest('button')) return;
    const body = document.getElementById(`cardBody_${idx}`);
    const btn = document.getElementById(`collapseBtn_${idx}`);
    if (!body || !btn) return;
    if (body.style.display === 'none') {
        body.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-chevron-up"></i>';
    } else {
        body.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-chevron-down"></i>';
    }
}

function removeCardStagedFile(cardIdx, fileIndex) {
    const card = document.getElementById(`docCard_${cardIdx}`);
    const input = card ? (card.querySelector(`input[name="files_${cardIdx}[]"]`) || card.querySelector('input[type="file"]')) : null;
    if (!input || !input.files) return;
    const dt = new DataTransfer();
    Array.from(input.files).forEach((file, idx) => {
        if (idx !== fileIndex) {
            dt.items.add(file);
        }
    });
    input.files = dt.files;
    handleCardFileInput(cardIdx, input);
}

function handleCardFileInput(idx, input) {
    const preview = document.getElementById(`card_files_preview_${idx}`);
    if (!preview || !input.files) return;
    preview.innerHTML = '';
    const card = document.getElementById(`docCard_${idx}`);
    const cardBidangId = card ? card.querySelector(`select[name="docs[${idx}][bidang_id]"]`)?.value : null;

    Array.from(input.files).forEach((file, fIdx) => {
        const ext = file.name.split('.').pop().toLowerCase();
        let iconClass = 'fa-file-pdf text-danger';
        let bgClass = 'bg-danger bg-opacity-10';
        if (['doc', 'docx'].includes(ext)) {
            iconClass = 'fa-file-word text-primary';
            bgClass = 'bg-primary bg-opacity-10';
        } else if (['xls', 'xlsx'].includes(ext)) {
            iconClass = 'fa-file-excel text-success';
            bgClass = 'bg-success bg-opacity-10';
        }

        const pickerHtml = renderSubStandarPicker(`docs[${idx}][file_sub_bidang][${fIdx}][]`, [], `card_${idx}_file_${fIdx}`, cardBidangId);

        const div = document.createElement('div');
        div.className = 'card p-3.5 bg-white border rounded-4 shadow-2xs mb-3';
        div.innerHTML = `
            <div class="d-flex align-items-center justify-content-between pb-2.5 border-bottom gap-2">
                <div class="d-flex align-items-center gap-2.5 text-truncate" style="min-width: 0;">
                    <div class="p-2 rounded-3 ${bgClass} flex-shrink-0">
                        <i class="fas ${iconClass} fa-lg"></i>
                    </div>
                    <div class="text-truncate">
                        <div class="text-truncate fw-bold text-dark" style="font-size:0.88rem;">${file.name}</div>
                        <span class="badge bg-light text-muted border mt-0.5" style="font-size: 0.72rem;">${(file.size / (1024*1024)).toFixed(2)} MB</span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill fw-semibold flex-shrink-0" style="font-size: 0.78rem;" onclick="removeCardStagedFile(${idx}, ${fIdx})" title="Batalkan berkas ini">
                    <i class="fas fa-trash-can me-1"></i> Batal
                </button>
            </div>
            
            <div class="mt-2.5 mb-2">
                <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                    <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Berkas: <span class="text-danger">*</span>
                </label>
                <textarea class="form-control py-2" name="docs[${idx}][file_narasi][${fIdx}]" rows="1" placeholder="Keterangan / narasi berkas ini..." required></textarea>
            </div>

            ${pickerHtml}

            <!-- Per-File Access Settings (Lega, Bersih, Rapi) -->
            <div class="p-3 rounded-3 bg-light border mt-2.5">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div class="d-flex align-items-center justify-content-between gap-2.5 flex-grow-1">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_limit_sw_${idx}_${fIdx}" name="docs[${idx}][file_is_limited][${fIdx}]" value="1" checked onchange="document.getElementById('card_limit_wrap_${idx}_${fIdx}').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                            </div>
                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_limit_sw_${idx}_${fIdx}" style="font-size: 0.82rem;">
                                Batasi Pratinjau
                            </label>
                        </div>
                        <div class="input-group input-group-sm" id="card_limit_wrap_${idx}_${fIdx}" style="width: 95px;">
                            <input type="number" class="form-control text-center py-1 fw-bold" name="docs[${idx}][file_page_limit][${fIdx}]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                            <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                        </div>
                    </div>

                    <div class="vr d-none d-sm-block bg-secondary opacity-25" style="height: 28px;"></div>

                    <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2.5">
                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_dl_sw_${idx}_${fIdx}" name="docs[${idx}][file_can_download][${fIdx}]" value="1" style="width: 2.2em; height: 1.2em;">
                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_dl_sw_${idx}_${fIdx}" style="font-size: 0.82rem;">
                                Unduh Publik
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        preview.appendChild(div);
    });
}

function addCardGdriveLink(idx) {
    const container = document.getElementById(`card_gdrive_links_${idx}`);
    if (!container) return;
    const count = container.querySelectorAll('.card-gdrive-row').length + 1;
    const card = document.getElementById(`docCard_${idx}`);
    const cardBidangId = card ? card.querySelector(`select[name="docs[${idx}][bidang_id]"]`)?.value : null;
    const pickerHtml = renderSubStandarPicker(`docs[${idx}][external_link_sub_bidang][${count - 1}][]`, [], `card_${idx}_link_${count}_${Math.random().toString(36).substring(2,6)}`, cardBidangId);

    const div = document.createElement('div');
    div.className = 'card-gdrive-row card p-3.5 bg-white border rounded-4 shadow-2xs mb-3';
    div.innerHTML = `
        <div class="d-flex align-items-center justify-content-between pb-2.5 border-bottom">
            <span class="fw-bold text-dark-blue d-flex align-items-center gap-2 card-gdrive-title" style="font-size: 0.88rem;">
                <div class="p-1.5 rounded-3 bg-success bg-opacity-10 text-success flex-shrink-0">
                    <i class="fab fa-google-drive"></i>
                </div>
                <span>Tautan Berkas #${count}</span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill fw-semibold" style="font-size: 0.78rem;" onclick="removeCardGdriveLink(this, ${idx})" title="Batalkan / Hapus tautan ini">
                <i class="fas fa-trash-can me-1"></i> Hapus
            </button>
        </div>
        <div class="mt-2.5 mb-2">
            <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                <i class="fas fa-link text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                <input type="url" class="form-control py-2" name="docs[${idx}][external_links][]" placeholder="https://drive.google.com/file/d/...">
            </div>
        </div>
        <div class="mb-2">
            <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Tautan: <span class="text-danger">*</span>
            </label>
            <textarea class="form-control py-2" name="docs[${idx}][external_link_narasi][]" rows="1" placeholder="Keterangan / Narasi tautan dokumen ini..."></textarea>
        </div>
        ${pickerHtml}

        <!-- Per-Link Access Settings (Seragam dengan Softfile) -->
        <div class="p-3 rounded-3 bg-light border mt-2.5">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                <div class="d-flex align-items-center justify-content-between gap-2.5 flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_link_limit_sw_${idx}_${count}" name="docs[${idx}][external_link_is_limited][${count - 1}]" value="1" checked onchange="document.getElementById('card_link_limit_wrap_${idx}_${count}').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                        </div>
                        <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_link_limit_sw_${idx}_${count}" style="font-size: 0.82rem;">
                            Batasi Pratinjau
                        </label>
                    </div>
                    <div class="input-group input-group-sm" id="card_link_limit_wrap_${idx}_${count}" style="width: 95px;">
                        <input type="number" class="form-control text-center py-1 fw-bold" name="docs[${idx}][external_link_page_limit][${count - 1}]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                        <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                    </div>
                </div>

                <div class="vr d-none d-sm-block bg-secondary opacity-25" style="height: 28px;"></div>

                <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2.5">
                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_link_dl_sw_${idx}_${count}" name="docs[${idx}][external_link_can_download][${count - 1}]" value="1" style="width: 2.2em; height: 1.2em;">
                        <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_link_dl_sw_${idx}_${count}" style="font-size: 0.82rem;">
                            Unduh Publik
                        </label>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.appendChild(div);
}

function removeCardGdriveLink(btn, idx) {
    const container = document.getElementById(`card_gdrive_links_${idx}`);
    if (!container) return;
    const row = btn.closest('.card-gdrive-row');
    if (row) {
        if (container.querySelectorAll('.card-gdrive-row').length > 1) {
            row.remove();
        } else {
            row.querySelectorAll('input, textarea').forEach(inp => inp.value = '');
        }
    }
}

function addNewDocCard() {
    const currentCards = document.querySelectorAll('.doc-batch-card');
    if (currentCards.length >= 10) {
        alert('Maksimal pengunggahan sekaligus adalah 10 dokumen.');
        return;
    }
    const container = document.getElementById('batchCardsContainer');
    if (!container) return;
    const template = createDocCardTemplate(cardCounter);
    container.insertAdjacentHTML('beforeend', template);
    cardCounter++;
    updateTotalCardCounter();
}

function removeDocCard(idx, evt) {
    evt.stopPropagation();
    const card = document.getElementById(`docCard_${idx}`);
    if (card) {
        card.remove();
        updateTotalCardCounter();
    }
}

function updateTotalCardCounter() {
    const cards = document.querySelectorAll('.doc-batch-card');
    const counter = document.getElementById('totalCardCounter');
    if (counter) counter.textContent = cards.length;
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    if (!IS_EDIT_MODE) {
        addNewDocCard(); // Initialize card #1
    } else {
        const initialBidangId = document.getElementById('bidang_id')?.value;
        if (initialBidangId) filterSubBidangOptions(initialBidangId);

        // Drag and Drop for single upload dropzone
        const dropZone = document.getElementById('fileDropZone');
        const singleInput = document.getElementById('single_files');
        if (dropZone && singleInput) {
            ['dragenter', 'dragover'].forEach(name => {
                dropZone.addEventListener(name, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.add('bg-light', 'border-primary');
                }, false);
            });
            ['dragleave', 'drop'].forEach(name => {
                dropZone.addEventListener(name, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.remove('bg-light', 'border-primary');
                }, false);
            });
            dropZone.addEventListener('drop', (e) => {
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    singleInput.files = e.dataTransfer.files;
                    handleSingleFileInput(singleInput);
                }
            }, false);
        }
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
