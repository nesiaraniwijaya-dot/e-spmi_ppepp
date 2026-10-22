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
$savedLinks = $isEdit ? parse_external_links($doc['external_link'] ?? '') : [];
$currentJenis = $isEdit ? ($doc['jenis_upload'] ?? 'file') : 'file';
if ($isEdit && !empty($docFiles) && !empty($savedLinks)) {
    $currentJenis = 'kombinasi';
}
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
            <form action="<?= base_url('prodi/dokumen/save') ?>" method="POST" id="documentUploadForm" novalidate>
                <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                
                <?php if (in_array($doc['status_review'] ?? '', ['perlu_perbaikan', 'revisi_gpm', 'revisi_lpm'])): 
                    $isGpmFormRev = (($doc['status_review'] ?? '') === 'revisi_gpm');
                    $formRevLabel = $isGpmFormRev ? 'Gugus Penjaminan Mutu (GPM) Fakultas' : 'Pusat Penjaminan Mutu LPM';
                    $formRevDate = $isGpmFormRev ? ($doc['gpm_reviewed_at'] ?? $doc['reviewed_at']) : ($doc['lpm_reviewed_at'] ?? $doc['reviewed_at'] ?? $doc['gpm_reviewed_at']);
                    $formRevNote = $isGpmFormRev 
                        ? trim($doc['gpm_catatan'] ?: ($doc['catatan_review'] ?? 'Silakan lakukan revisi sesuai standar penjaminan mutu SPMI.'))
                        : trim($doc['lpm_catatan'] ?: ($doc['catatan_review'] ?: ($doc['gpm_catatan'] ?? 'Silakan lakukan revisi sesuai standar penjaminan mutu SPMI.')));
                ?>
                    <!-- Executive Review Notice Callout -->
                    <div class="rounded-4 mb-4 p-3.5 p-md-4 shadow-sm" style="background: #FFFDFD; border: 1px solid #FECACA; border-left: 6px solid #DC2626;">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 pb-2.5 border-bottom" style="border-color: #FEE2E2 !important;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger text-white rounded-pill px-3 py-1.5 fw-bold shadow-2xs" style="font-size: 0.78rem;">
                                    <i class="fas fa-triangle-exclamation me-1.5"></i> Perlu Revisi Dokumen
                                </span>
                                <span class="fw-bold text-dark-blue small">
                                    Catatan Evaluasi <?= $isGpmFormRev ? 'GPM Fakultas' : 'Penjaminan Mutu (LPM)' ?>
                                </span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2 small" style="font-size: 0.76rem;">
                                <span class="d-inline-flex align-items-center gap-1.5 bg-white border border-danger-subtle rounded-pill px-3 py-1 text-dark shadow-2xs">
                                    <i class="fas <?= $isGpmFormRev ? 'fa-shield-halved text-purple' : 'fa-building-columns text-primary' ?>"></i>
                                    <strong class="text-dark"><?= htmlspecialchars($formRevLabel) ?></strong>
                                </span>
                                <?php if (!empty($formRevDate)): ?>
                                    <span class="d-inline-flex align-items-center gap-1 bg-white border border-danger-subtle rounded-pill px-2.5 py-1 text-muted shadow-2xs">
                                        <i class="far fa-clock text-secondary"></i>
                                        <?= date('d M Y, H:i', strtotime($formRevDate)) ?> WIB
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
                                <?= htmlspecialchars($formRevNote) ?>
                            </div>
                        </div>

                        <!-- Directive Sub-banner -->
                        <div class="d-flex align-items-center gap-2 text-danger small pt-1">
                            <i class="fas fa-circle-info flex-shrink-0"></i>
                            <span>Silakan perbarui isian atau berkas di bawah ini, lalu klik tombol <strong>"Simpan &amp; Ajukan Ulang <?= $isGpmFormRev ? 'ke GPM' : 'ke LPM' ?>"</strong> untuk verifikasi lanjutan.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-column gap-4">
                    <!-- Bagian 1: Identitas & Klasifikasi Dokumen Mutu -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-4.5 bg-white">
                        <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                            <h5 class="fw-bold text-dark-blue mb-0">
                                Bagian 1: Identitas &amp; Klasifikasi Dokumen Mutu
                            </h5>
                            <span class="badge bg-light text-muted border px-3 py-1.5 rounded-pill small">Langkah 1 dari 2</span>
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
                                <input type="text" class="form-control input-tahun-akademik" id="tahun_akademik" name="tahun_akademik" 
                                       value="<?= htmlspecialchars($currentTahun) ?>" 
                                       placeholder="Contoh: 2026/2027" maxlength="9" required
                                       oninput="autoPairTahunAkademik(this, event)" onblur="autoPairTahunAkademik(this, event)">
                                <div class="form-text small" style="font-size: 0.72rem;">Format: <code>YYYY/YYYY</code> (Ketik 4 angka tahun awal, otomatis dipasangkan)</div>
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
                        <div class="mb-2">
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

                    <!-- Bagian 2: Berkas Lampiran & Kontrol Akses -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-4.5 bg-white" id="rightUploadCol">
                        <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                            <h5 class="fw-bold text-dark-blue mb-0">
                                Bagian 2: Berkas Lampiran &amp; Hak Akses Publik
                            </h5>
                            <span class="badge bg-light text-muted border px-3 py-1.5 rounded-pill small">Langkah 2 dari 2</span>
                        </div>

                        <!-- Interactive Method Cards -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark mb-2.5 d-flex align-items-center gap-1.5">
                                <i class="fas fa-layer-group text-primary"></i> Pilih Metode Penyediaan Berkas <span class="text-danger">*</span>
                            </label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative <?= ($currentJenis === 'file') ? 'active' : '' ?>" 
                                         id="card_method_file" onclick="toggleUploadMethod('file')">
                                        <input type="radio" class="d-none radio-jenis-upload" name="jenis_upload" id="jenis_file" value="file" <?= ($currentJenis === 'file') ? 'checked' : '' ?>>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #FEE2E2; color: #DC2626; font-size: 0.9rem;">
                                                <i class="fas fa-file-arrow-up"></i>
                                            </span>
                                            <div class="lh-sm">
                                                <div class="fw-bold text-dark-blue" style="font-size: 0.85rem;">Berkas Dokumen</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">PDF, DOCX, XLSX</div>
                                            </div>
                                        </div>
                                        <div class="method-check-badge position-absolute top-0 end-0 p-2" style="<?= ($currentJenis === 'file') ? '' : 'display:none;' ?>">
                                            <i class="fas fa-circle-check text-primary" style="font-size: 0.85rem;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative <?= ($currentJenis === 'link') ? 'active' : '' ?>" 
                                         id="card_method_link" onclick="toggleUploadMethod('link')">
                                        <input type="radio" class="d-none radio-jenis-upload" name="jenis_upload" id="jenis_link" value="link" <?= ($currentJenis === 'link') ? 'checked' : '' ?>>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #E0F2FE; color: #0284C7; font-size: 0.9rem;">
                                                <i class="fab fa-google-drive"></i>
                                            </span>
                                            <div class="lh-sm">
                                                <div class="fw-bold text-dark-blue" style="font-size: 0.85rem;">Google Drive</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Tautan Berbagi Link</div>
                                            </div>
                                        </div>
                                        <div class="method-check-badge position-absolute top-0 end-0 p-2" style="<?= ($currentJenis === 'link') ? '' : 'display:none;' ?>">
                                            <i class="fas fa-circle-check text-primary" style="font-size: 0.85rem;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative <?= ($currentJenis === 'kombinasi') ? 'active' : '' ?>" 
                                         id="card_method_kombinasi" onclick="toggleUploadMethod('kombinasi')">
                                        <input type="radio" class="d-none radio-jenis-upload" name="jenis_upload" id="jenis_kombinasi" value="kombinasi" <?= ($currentJenis === 'kombinasi') ? 'checked' : '' ?>>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #F3E8FF; color: #7E22CE; font-size: 0.9rem;">
                                                <i class="fas fa-layer-group"></i>
                                            </span>
                                            <div class="lh-sm">
                                                <div class="fw-bold text-dark-blue" style="font-size: 0.85rem;">Kombinasi</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Berkas &amp; Link GDrive</div>
                                            </div>
                                        </div>
                                        <div class="method-check-badge position-absolute top-0 end-0 p-2" style="<?= ($currentJenis === 'kombinasi') ? '' : 'display:none;' ?>">
                                            <i class="fas fa-circle-check text-primary" style="font-size: 0.85rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!-- Section Unggah Berkas Fisik -->
                            <div id="section_file" style="<?= ($currentJenis === 'file' || $currentJenis === 'kombinasi') ? '' : 'display:none;' ?>">
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

                                                    <!-- Per-File Access Settings (2-Card Pill System) -->
                                                    <div class="row g-2.5 mt-2.5">
                                                        <div class="col-md-7">
                                                            <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                                                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="ex_limit_sw_<?= $f['id'] ?>" name="existing_file_is_limited[<?= $f['id'] ?>]" value="1" <?= $fLimited ? 'checked' : '' ?> onchange="document.getElementById('ex_limit_wrap_<?= $f['id'] ?>').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                                                                    <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="ex_limit_sw_<?= $f['id'] ?>" style="font-size: 0.82rem;">
                                                                        Batasi Pratinjau
                                                                    </label>
                                                                </div>
                                                                <div class="input-group input-group-sm" id="ex_limit_wrap_<?= $f['id'] ?>" style="width: 100px; <?= $fLimited ? 'display:inline-flex;' : 'display:none;' ?>">
                                                                    <input type="number" class="form-control text-center py-1 fw-bold" name="existing_file_page_limit[<?= $f['id'] ?>]" min="1" max="50" value="<?= $fLimitVal ?>" style="font-size: 0.78rem;">
                                                                    <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                                                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="ex_dl_sw_<?= $f['id'] ?>" name="existing_file_can_download[<?= $f['id'] ?>]" value="1" <?= $fCanDownload ? 'checked' : '' ?> style="width: 2.2em; height: 1.2em;">
                                                                    <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="ex_dl_sw_<?= $f['id'] ?>" style="font-size: 0.82rem;">
                                                                        Unduh Publik
                                                                    </label>
                                                                </div>
                                                                <span class="badge bg-light text-muted border small d-none d-sm-inline">Publik</span>
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
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold shadow-xs my-1" style="font-size: 0.8rem;" onclick="event.stopPropagation(); triggerAddMoreSingleFiles()">
                                            <i class="fas fa-plus me-1"></i> Telusuri &amp; Tambah Berkas
                                        </button>
                                        <div class="text-muted mt-2.5 d-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.73rem;">
                                            <i class="fas fa-lightbulb text-warning"></i> <span>Tarik &amp; lepas berkas ke area ini atau klik tombol di atas</span>
                                        </div>
                                    </div>
                                    <div id="newFilesPreviewContainer" class="d-flex flex-column gap-2 mt-2"></div>
                                    <button type="button" id="btnAddMoreSingleFilesBtn" class="btn btn-sm btn-outline-primary rounded-pill w-100 py-2 fw-semibold mt-2" style="display:none;" onclick="triggerAddMoreSingleFiles()">
                                        <i class="fas fa-plus me-1.5"></i> Tambah Berkas Lainnya
                                    </button>
                                </div>
                            </div>

                            <!-- Section Tautan Google Drive -->
                            <div id="section_link" style="<?= ($currentJenis === 'link' || $currentJenis === 'kombinasi') ? '' : 'display:none;' ?>">
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
                                                    <i class="fas fa-file-lines text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                                                    <input type="url" class="form-control py-2 gdrive-doc-input" name="external_links[]" placeholder="https://drive.google.com/file/d/... (Khusus dokumen, bukan folder)" oninput="validateGdriveDocInput(this)" onblur="validateGdriveDocInput(this)">
                                                </div>
                                                <div class="gdrive-validation-feedback text-danger small mt-1 fw-semibold" style="display: none; font-size: 0.76rem;"></div>
                                                <div class="form-text text-muted small mt-1" style="font-size: 0.74rem;">
                                                    <i class="fas fa-circle-info text-primary me-1"></i> Khusus tautan <strong>dokumen / file langsung</strong> (contoh: <code>drive.google.com/file/d/...</code> atau Google Docs/Sheets). <strong>Tautan folder tidak diperbolehkan.</strong>
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
                                            
                                            <!-- Per-Link Access Settings -->
                                            <div class="row g-2.5 mt-2.5">
                                                <div class="col-md-7">
                                                    <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="init_link_limit_sw_0" name="external_link_is_limited[0]" value="1" checked onchange="document.getElementById('init_link_limit_wrap_0').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                                                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="init_link_limit_sw_0" style="font-size: 0.82rem;">
                                                                Batasi Pratinjau
                                                            </label>
                                                        </div>
                                                        <div class="input-group input-group-sm" id="init_link_limit_wrap_0" style="width: 100px;">
                                                            <input type="number" class="form-control text-center py-1 fw-bold" name="external_link_page_limit[0]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                                                            <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="init_link_dl_sw_0" name="external_link_can_download[0]" value="1" style="width: 2.2em; height: 1.2em;">
                                                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="init_link_dl_sw_0" style="font-size: 0.82rem;">
                                                                Unduh Publik
                                                            </label>
                                                        </div>
                                                        <span class="badge bg-light text-muted border small d-none d-sm-inline">Publik</span>
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
                                                    <i class="fas fa-file-lines text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                                                    <input type="url" class="form-control py-2 gdrive-doc-input" name="external_links[]" value="<?= htmlspecialchars($l['url']) ?>" placeholder="https://drive.google.com/file/d/... (Khusus dokumen, bukan folder)" oninput="validateGdriveDocInput(this)" onblur="validateGdriveDocInput(this)">
                                                </div>
                                                <div class="gdrive-validation-feedback text-danger small mt-1 fw-semibold" style="display: none; font-size: 0.76rem;"></div>
                                                <div class="form-text text-muted small mt-1" style="font-size: 0.74rem;">
                                                    <i class="fas fa-circle-info text-primary me-1"></i> Khusus tautan <strong>dokumen / file langsung</strong> (contoh: <code>drive.google.com/file/d/...</code> atau Google Docs/Sheets). <strong>Tautan folder tidak diperbolehkan.</strong>
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

                                            <!-- Per-Link Access Settings -->
                                            <div class="row g-2.5 mt-2.5">
                                                <div class="col-md-7">
                                                    <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="ex_link_limit_sw_<?= $lIdx ?>" name="external_link_is_limited[<?= $lIdx ?>]" value="1" <?= $lLimited ? 'checked' : '' ?> onchange="document.getElementById('ex_link_limit_wrap_<?= $lIdx ?>').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                                                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="ex_link_limit_sw_<?= $lIdx ?>" style="font-size: 0.82rem;">
                                                                Batasi Pratinjau
                                                            </label>
                                                        </div>
                                                        <div class="input-group input-group-sm" id="ex_link_limit_wrap_<?= $lIdx ?>" style="width: 100px; <?= $lLimited ? 'display:inline-flex;' : 'display:none;' ?>">
                                                            <input type="number" class="form-control text-center py-1 fw-bold" name="external_link_page_limit[<?= $lIdx ?>]" min="1" max="50" value="<?= $lLimitVal ?>" style="font-size: 0.78rem;">
                                                            <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="ex_link_dl_sw_<?= $lIdx ?>" name="external_link_can_download[<?= $lIdx ?>]" value="1" <?= $lCanDownload ? 'checked' : '' ?> style="width: 2.2em; height: 1.2em;">
                                                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="ex_link_dl_sw_<?= $lIdx ?>" style="font-size: 0.82rem;">
                                                                Unduh Publik
                                                            </label>
                                                        </div>
                                                        <span class="badge bg-light text-muted border small d-none d-sm-inline">Publik</span>
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

                            <!-- Action Submit Buttons -->
                            <div class="mt-4 pt-3.5 border-top">
                                <div class="d-flex flex-column flex-sm-row justify-content-end align-items-center gap-2.5">
                                    <button type="submit" name="action_submit" value="draft" class="btn btn-outline-secondary px-4 py-2.5 fw-semibold rounded-pill w-100 w-sm-auto order-2 order-sm-1">
                                        <i class="fas fa-floppy-disk me-1.5"></i> Simpan Sebagai Draf
                                    </button>
                                    <button type="submit" name="action_submit" value="publish" class="btn btn-warning px-4 py-2.5 fw-bold rounded-pill text-dark shadow-sm w-100 w-sm-auto order-1 order-sm-2">
                                        <i class="fas fa-paper-plane me-1.5"></i> <?= in_array($doc['status_review'] ?? '', ['perlu_perbaikan', 'revisi_gpm', 'revisi_lpm']) ? ('Simpan &amp; Ajukan Ulang ' . (($doc['status_review'] ?? '') === 'revisi_gpm' ? 'ke GPM' : 'ke LPM')) : 'Simpan &amp; Ajukan ke LPM' ?>
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
        <!-- INFO TAUTAN KE JUDUL SIKLUS PPEPP          -->
        <!-- ========================================== -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important; background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);">
            <div class="card-body p-3.5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="fas fa-folder-tree fa-lg"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark-blue small mb-0.5">Ingin Melanjutkan Dokumen dari Judul yang Sudah Ada?</div>
                        <div class="text-muted" style="font-size: 0.78rem;">
                            Setiap judul siklus PPEPP dapat dilengkapi bertahap (Penetapan &rarr; Pelaksanaan &rarr; Evaluasi &rarr; Pengendalian &rarr; Peningkatan). Anda dapat melihat progres kelengkapan judul siklus dan menambahkan dokumen lanjutan di menu terpisah.
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('prodi/dokumen/cycles') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2 flex-shrink-0 text-nowrap shadow-2xs">
                    <i class="fas fa-list-check"></i>
                    <span>Lihat Judul yang Sudah Ada</span>
                    <i class="fas fa-arrow-right ms-1 small"></i>
                </a>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODE CREATE: BATCH MULTI-UPLOAD (1-10 DOK)  -->
        <!-- ========================================== -->
        <form action="<?= base_url('prodi/dokumen/save') ?>" method="POST" id="batchDocForm" novalidate>
            
            <!-- Banner Mode Kelanjutan Judul PPEPP -->
            <div id="activeContinuationBanner" class="card border-0 shadow-sm rounded-4 p-3.5 mb-4 text-white" style="background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%); display: none;">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-white text-primary flex-shrink-0 shadow-sm" style="width: 40px; height: 40px; font-size: 1.1rem;">
                            <i class="fas fa-link"></i>
                        </div>
                        <div>
                            <div class="badge rounded-pill px-2.5 py-0.5 bg-white bg-opacity-25 text-white fw-semibold mb-1" style="font-size: 0.72rem;">
                                Mode Dokumen Lanjutan PPEPP Aktif
                            </div>
                            <div class="fw-bold" style="font-size: 0.95rem;">
                                Melanjutkan Judul: <span id="continuationJudulText" class="text-warning fw-bold">-</span>
                            </div>
                            <div class="small text-white-50" style="font-size: 0.76rem;">
                                Tahap: <strong id="continuationStageText" class="text-white">-</strong> &bull; Standar: <span id="continuationBidangText" class="text-white">-</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-light text-dark fw-bold rounded-pill px-3 py-1.5 shadow-2xs d-inline-flex align-items-center gap-1.5" onclick="resetFormToNewCycle()">
                        <i class="fas fa-xmark"></i> Batal / Buat Judul Baru
                    </button>
                </div>
            </div>

            <!-- Quick Helper & Action Bar -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3.5 rounded-4 bg-white border shadow-2xs mb-4 gap-3" style="border: 1px solid #E2E8F0 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:42px; height:42px; background: #059669;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark-blue small" id="formHeaderTitle">Formulir Multi-Unggah Dokumen Mutu Program Studi</div>
                        <div class="text-muted" style="font-size: 0.72rem;" id="formHeaderSubtitle">Anda dapat mengunggah hingga 10 dokumen mutu sekaligus dalam satu kali proses simpan.</div>
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
const SIKLUS_DEFS = <?= json_encode($siklusDefinitions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || {};
const BIDANGS_DATA = <?= json_encode($bidangList ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || [];
const SUB_BIDANGS_DATA = <?= json_encode($subBidangList ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || [];
const PRODI_NAME = <?= json_encode($prodi['nama_prodi'] ?? 'Program Studi') ?>;
const PRODI_JENJANG = <?= json_encode($prodi['jenjang'] ?? 'S1') ?>;
const IS_EDIT_MODE = <?= $isEdit ? 'true' : 'false' ?>;
const PRESET_CYCLE = <?= json_encode($presetCycle ?? null) ?>;
const PRESET_SIKLUS = <?= json_encode($presetSiklus ?? null) ?>;

// Sub-bidang filtering helper
function getFilteredSubBidang(bidangId) {
    if (!bidangId || !Array.isArray(SUB_BIDANGS_DATA)) return [];
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
    const fileSec = document.getElementById('section_file');
    const linkSec = document.getElementById('section_link');
    if (fileSec) fileSec.style.display = (method === 'file' || method === 'kombinasi') ? 'block' : 'none';
    if (linkSec) linkSec.style.display = (method === 'link' || method === 'kombinasi') ? 'block' : 'none';

    const rFile = document.getElementById('jenis_file');
    const rLink = document.getElementById('jenis_link');
    const rKomb = document.getElementById('jenis_kombinasi');
    if (rFile) rFile.checked = (method === 'file');
    if (rLink) rLink.checked = (method === 'link');
    if (rKomb) rKomb.checked = (method === 'kombinasi');

    ['file', 'link', 'kombinasi'].forEach(m => {
        const card = document.getElementById(`card_method_${m}`);
        if (card) {
            const badge = card.querySelector('.method-check-badge');
            if (m === method) {
                card.classList.add('active');
                if (badge) badge.style.display = 'block';
            } else {
                card.classList.remove('active');
                if (badge) badge.style.display = 'none';
            }
        }
    });
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

// ==========================================
// SINGLE EDIT STAGING LOGIC
// ==========================================
let singleStagedFiles = [];

function saveSingleStagedState() {
    const state = [];
    singleStagedFiles.forEach((_, idx) => {
        const narasiEl = document.querySelector(`textarea[name="new_file_narasi[${idx}]"]`);
        const limitSw = document.getElementById(`new_limit_sw_${idx}`);
        const limitVal = document.querySelector(`input[name="new_file_page_limit[${idx}]"]`);
        const dlSw = document.getElementById(`new_dl_sw_${idx}`);
        
        const checkedSub = [];
        document.querySelectorAll(`input[name="new_file_sub_bidang[${idx}][]"]:checked`).forEach(cb => {
            checkedSub.push(parseInt(cb.value));
        });

        state[idx] = {
            narasi: narasiEl ? narasiEl.value : '',
            is_limited: limitSw ? limitSw.checked : true,
            page_limit: limitVal ? limitVal.value : '1',
            can_download: dlSw ? dlSw.checked : false,
            sub_bidang_ids: checkedSub
        };
    });
    return state;
}

function syncSingleInputFiles() {
    const input = document.getElementById('single_files');
    if (!input) return;
    const dt = new DataTransfer();
    singleStagedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
}

function triggerAddMoreSingleFiles() {
    const tempInput = document.createElement('input');
    tempInput.type = 'file';
    tempInput.multiple = true;
    tempInput.accept = '.pdf,.doc,.docx,.xls,.xlsx';
    tempInput.onchange = function() {
        if (tempInput.files && tempInput.files.length > 0) {
            const savedState = saveSingleStagedState();
            Array.from(tempInput.files).forEach(f => {
                singleStagedFiles.push(f);
            });
            syncSingleInputFiles();
            renderSingleStagedFiles(savedState);
        }
    };
    tempInput.click();
}

function handleSingleFileInput(input) {
    if (!input.files || input.files.length === 0) return;
    const savedState = saveSingleStagedState();
    Array.from(input.files).forEach(f => {
        singleStagedFiles.push(f);
    });
    syncSingleInputFiles();
    renderSingleStagedFiles(savedState);
}

function removeSingleStagedFile(fileIndex) {
    const savedState = saveSingleStagedState();
    singleStagedFiles.splice(fileIndex, 1);
    savedState.splice(fileIndex, 1);
    syncSingleInputFiles();
    renderSingleStagedFiles(savedState);
}

function renderSingleStagedFiles(savedState = []) {
    const container = document.getElementById('newFilesPreviewContainer');
    const addMoreBtn = document.getElementById('btnAddMoreSingleFilesBtn');
    if (!container) return;
    container.innerHTML = '';
    const curBidangId = document.getElementById('bidang_id')?.value || null;

    if (singleStagedFiles.length > 0) {
        if (addMoreBtn) addMoreBtn.style.display = 'block';
    } else {
        if (addMoreBtn) addMoreBtn.style.display = 'none';
    }

    singleStagedFiles.forEach((file, idx) => {
        const st = savedState[idx] || {};
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

        const selectedSubs = st.sub_bidang_ids || [];
        const pickerHtml = renderSubStandarPicker(`new_file_sub_bidang[${idx}][]`, selectedSubs, `new_file_${idx}`, curBidangId);
        const isLimited = (st.is_limited !== undefined) ? st.is_limited : true;
        const pageLimit = st.page_limit || 1;
        const canDownload = (st.can_download !== undefined) ? st.can_download : false;
        const narasiVal = (st.narasi !== undefined) ? st.narasi : '';

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
                <textarea class="form-control py-2" name="new_file_narasi[${idx}]" rows="1" placeholder="Tuliskan keterangan isi berkas '${file.name}'..." required>${narasiVal ? String(narasiVal).replace(/</g, '&lt;').replace(/>/g, '&gt;') : ''}</textarea>
            </div>

            ${pickerHtml}

            <!-- Per-File Access Settings (2-Card Pill System) -->
            <div class="row g-2.5 mt-2.5">
                <div class="col-md-7">
                    <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="new_limit_sw_${idx}" name="new_file_is_limited[${idx}]" value="1" ${isLimited ? 'checked' : ''} onchange="document.getElementById('new_limit_wrap_${idx}').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="new_limit_sw_${idx}" style="font-size: 0.82rem;">
                                Batasi Pratinjau
                            </label>
                        </div>
                        <div class="input-group input-group-sm" id="new_limit_wrap_${idx}" style="width: 100px; ${isLimited ? 'display:inline-flex;' : 'display:none;'}">
                            <input type="number" class="form-control text-center py-1 fw-bold" name="new_file_page_limit[${idx}]" min="1" max="50" value="${pageLimit}" style="font-size: 0.78rem;">
                            <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="new_dl_sw_${idx}" name="new_file_can_download[${idx}]" value="1" ${canDownload ? 'checked' : ''} style="width: 2.2em; height: 1.2em;">
                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="new_dl_sw_${idx}" style="font-size: 0.82rem;">
                                Unduh Publik
                            </label>
                        </div>
                        <span class="badge bg-light text-muted border small d-none d-sm-inline">Publik</span>
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
                <i class="fas fa-file-lines text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                <input type="url" class="form-control py-2 gdrive-doc-input" name="external_links[]" placeholder="https://drive.google.com/file/d/... (Khusus dokumen, bukan folder)" oninput="validateGdriveDocInput(this)" onblur="validateGdriveDocInput(this)">
            </div>
            <div class="gdrive-validation-feedback text-danger small mt-1 fw-semibold" style="display: none; font-size: 0.76rem;"></div>
            <div class="form-text text-muted small mt-1" style="font-size: 0.74rem;">
                <i class="fas fa-circle-info text-primary me-1"></i> Khusus tautan <strong>dokumen / file langsung</strong> (contoh: <code>drive.google.com/file/d/...</code>). <strong>Tautan folder tidak diperbolehkan.</strong>
            </div>
        </div>
        <div class="mb-2">
            <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Tautan: <span class="text-danger">*</span>
            </label>
            <textarea class="form-control py-2" name="external_link_narasi[]" rows="1" placeholder="Keterangan / Narasi tautan dokumen ini..."></textarea>
        </div>
        ${pickerHtml}

        <!-- Per-Link Access Settings -->
        <div class="row g-2.5 mt-2.5">
            <div class="col-md-7">
                <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="new_link_limit_sw_${count}" name="external_link_is_limited[${count - 1}]" value="1" checked onchange="document.getElementById('new_link_limit_wrap_${count}').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                        <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="new_link_limit_sw_${count}" style="font-size: 0.82rem;">
                            Batasi Pratinjau
                        </label>
                    </div>
                    <div class="input-group input-group-sm" id="new_link_limit_wrap_${count}" style="width: 100px;">
                        <input type="number" class="form-control text-center py-1 fw-bold" name="external_link_page_limit[${count - 1}]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                        <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="new_link_dl_sw_${count}" name="external_link_can_download[${count - 1}]" value="1" style="width: 2.2em; height: 1.2em;">
                        <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="new_link_dl_sw_${count}" style="font-size: 0.82rem;">
                            Unduh Publik
                        </label>
                    </div>
                    <span class="badge bg-light text-muted border small d-none d-sm-inline">Publik</span>
                </div>
            </div>
        </div>
    `;
    container.appendChild(row);
}

// ==========================================
// FALLBACK SUB STANDAR MUTU PICKER
// Mencegah kegagalan render kartu jika file assets/js/sub_standar_picker.js belum terunggah ke hosting
// ==========================================
if (typeof escapeHtml !== 'function') {
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
}

if (typeof renderSubStandarPicker !== 'function') {
    window.renderSubStandarPicker = function(inputName, selectedSubIds = [], uniqueKey = '', currentBidangId = null) {
        if (!uniqueKey) uniqueKey = 'picker_' + Math.random().toString(36).substring(2, 9);
        selectedSubIds = (selectedSubIds || []).map(id => parseInt(id)).filter(id => !isNaN(id) && id > 0);

        const bData = (typeof BIDANGS_DATA !== 'undefined' && Array.isArray(BIDANGS_DATA)) ? BIDANGS_DATA : [];
        let sbData = (typeof SUB_BIDANGS_DATA !== 'undefined' && Array.isArray(SUB_BIDANGS_DATA)) ? SUB_BIDANGS_DATA : [];
        const parsedBidangId = (currentBidangId && parseInt(currentBidangId) > 0) ? parseInt(currentBidangId) : null;

        if (parsedBidangId) {
            sbData = sbData.filter(s => parseInt(s.bidang_id) === parsedBidangId);
            const validIds = sbData.map(s => parseInt(s.id));
            selectedSubIds = selectedSubIds.filter(id => validIds.includes(id));
        } else {
            selectedSubIds = [];
        }

        let badgesHtml = '';
        selectedSubIds.forEach(sId => {
            const sb = sbData.find(s => parseInt(s.id) === sId);
            if (sb) {
                const name = escapeHtml(sb.nama_sub_bidang);
                badgesHtml += `
                    <span class="badge rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5 border shadow-2xs me-1 mb-1 text-wrap text-start lh-sm" 
                          style="background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE !important; font-size: 0.78rem; font-weight: 600; max-width: 100%; word-break: break-word;" 
                          id="badge_${uniqueKey}_${sb.id}" title="${name}">
                        <i class="fas fa-bookmark text-primary opacity-75 flex-shrink-0" style="font-size: 0.7rem;"></i>
                        <span>${name}</span>
                        <i class="fas fa-xmark cursor-pointer text-muted hover-danger ms-1 flex-shrink-0" 
                           onclick="removeSubStandarTag('${uniqueKey}', ${sb.id})" title="Hapus standar ini"></i>
                    </span>
                `;
            }
        });

        let optionsHtml = '';
        if (!parsedBidangId) {
            optionsHtml = `
                <div class="px-3 py-3 text-center text-muted small fst-italic no-bidang-notice">
                    <i class="fas fa-arrow-left text-primary me-1"></i> Silakan pilih Bidang pada dokumen terlebih dahulu
                </div>
            `;
        } else {
            const curBidang = bData.find(b => parseInt(b.id) === parsedBidangId);
            const bName = curBidang ? curBidang.nama_bidang : 'Bidang Terpilih';
            if (sbData.length > 0) {
                optionsHtml += `
                    <div class="px-2.5 py-1.5 mt-1 mb-1 fw-bold text-dark-blue rounded bg-light border-bottom d-flex align-items-center justify-content-between" style="font-size: 0.76rem; letter-spacing: 0.2px;">
                        <span class="text-truncate"><i class="fas fa-layer-group text-primary me-1"></i> ${escapeHtml(bName)}</span>
                        <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill" style="font-size: 0.68rem;">${sbData.length} Standar</span>
                    </div>
                `;
                sbData.forEach(sb => {
                    const isChecked = selectedSubIds.includes(parseInt(sb.id));
                    const searchStr = `${sb.nama_sub_bidang || ''} ${bName}`.toLowerCase();
                    optionsHtml += `
                        <label class="dropdown-item d-flex align-items-start gap-2 py-2 px-2.5 rounded cursor-pointer sub-option-item" 
                               style="font-size: 0.8rem; white-space: normal;" 
                               data-search-text="${escapeHtml(searchStr)}">
                            <input type="checkbox" class="form-check-input flex-shrink-0 mt-0.5 sub-check-input" 
                                   name="${inputName}" value="${sb.id}" 
                                   onchange="handleSubStandarCheckChange('${uniqueKey}', this, ${sb.id}, '', '${escapeHtml(sb.nama_sub_bidang)}')">
                            <span class="lh-sm text-dark">${escapeHtml(sb.nama_sub_bidang)}</span>
                        </label>
                    `;
                });
            } else {
                optionsHtml = `
                    <div class="px-3 py-3 text-center text-muted small fst-italic">
                        Belum ada standar mutu yang terdaftar untuk bidang ini.
                    </div>
                `;
            }
        }

        const noSubPlaceholder = '<span class="text-muted fst-italic no-sub-placeholder" style="font-size: 0.78rem;">Belum ada standar dipilih (Bisa pilih 1 atau lebih)</span>';
        const buttonLabel = parsedBidangId
            ? '<i class="fas fa-plus-circle text-primary me-1.5"></i> Pilih / Tambah Standar...'
            : '<i class="fas fa-info-circle text-muted me-1.5"></i> Pilih Bidang terlebih dahulu...';

        return `
            <div class="sub-standar-picker-wrap p-3 rounded-3 bg-white border shadow-2xs mt-2" 
                 id="picker_wrap_${uniqueKey}" 
                 data-picker-key="${uniqueKey}" 
                 data-input-name="${inputName}" 
                 data-current-bidang="${parsedBidangId || ''}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="form-label fw-bold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                        <i class="fas fa-tags text-primary"></i>
                        <span>Standar Mutu Berkas Ini: <span class="text-danger">*</span></span>
                    </label>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bisa > 1 Standar</span>
                </div>
                <div class="selected-sub-badges d-flex flex-wrap gap-1 mb-2" id="badges_container_${uniqueKey}">
                    ${badgesHtml || noSubPlaceholder}
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-start d-flex justify-content-between align-items-center py-2 px-3 rounded-3 dropdown-toggle-custom" 
                            id="btn_toggle_${uniqueKey}"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="font-size: 0.82rem; border-style: dashed; border-width: 1.5px;">
                        <span class="text-truncate text-secondary dropdown-label-text">
                            ${buttonLabel}
                        </span>
                        <i class="fas fa-chevron-down text-muted ms-1" style="font-size: 0.75rem;"></i>
                    </button>
                    <div class="dropdown-menu p-2.5 shadow-lg border rounded-3 w-100" style="max-height: 300px; overflow-y: auto; font-size: 0.8rem; z-index: 1060;">
                        <div class="px-1 pb-2 mb-1.5 border-bottom">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted px-2.5"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control py-1 px-2.5" placeholder="Cari nama standar..." onkeyup="filterSubStandarList('${uniqueKey}', this.value)">
                            </div>
                        </div>
                        <div class="sub-standar-options-list" id="options_list_${uniqueKey}">
                            ${optionsHtml}
                        </div>
                    </div>
                </div>
            </div>
        `;
    };

    window.updateSubStandarPickerOptions = function(uniqueKey, newBidangId, inputName) {
        const wrap = document.getElementById(`picker_wrap_${uniqueKey}`);
        if (!wrap) return;
        if (!inputName) inputName = wrap.getAttribute('data-input-name') || '';

        const bData = (typeof BIDANGS_DATA !== 'undefined' && Array.isArray(BIDANGS_DATA)) ? BIDANGS_DATA : [];
        const sbData = (typeof SUB_BIDANGS_DATA !== 'undefined' && Array.isArray(SUB_BIDANGS_DATA)) ? SUB_BIDANGS_DATA : [];
        const parsedBidangId = (newBidangId && parseInt(newBidangId) > 0) ? parseInt(newBidangId) : null;
        wrap.setAttribute('data-current-bidang', parsedBidangId || '');

        const container = document.getElementById(`badges_container_${uniqueKey}`);
        const optionsContainer = document.getElementById(`options_list_${uniqueKey}`);
        const toggleBtn = document.getElementById(`btn_toggle_${uniqueKey}`);
        const labelSpan = toggleBtn ? toggleBtn.querySelector('.dropdown-label-text') : null;

        if (container) {
            const validSubs = parsedBidangId ? sbData.filter(s => parseInt(s.bidang_id) === parsedBidangId) : [];
            const validIds = validSubs.map(s => parseInt(s.id));
            container.querySelectorAll('.badge').forEach(b => {
                const m = b.id.match(new RegExp(`badge_${uniqueKey}_(\\d+)`));
                if (m && !validIds.includes(parseInt(m[1]))) b.remove();
            });
            if (container.querySelectorAll('.badge').length === 0) {
                container.innerHTML = '<span class="text-muted fst-italic no-sub-placeholder" style="font-size: 0.78rem;">Belum ada standar dipilih (Bisa pilih 1 atau lebih)</span>';
            }
        }

        let newOptionsHtml = '';
        if (!parsedBidangId) {
            newOptionsHtml = '<div class="px-3 py-3 text-center text-muted small fst-italic no-bidang-notice"><i class="fas fa-arrow-left text-primary me-1"></i> Silakan pilih Bidang pada dokumen terlebih dahulu</div>';
            if (labelSpan) labelSpan.innerHTML = '<i class="fas fa-info-circle text-muted me-1.5"></i> Pilih Bidang terlebih dahulu...';
        } else {
            const curBidang = bData.find(b => parseInt(b.id) === parsedBidangId);
            const bName = curBidang ? curBidang.nama_bidang : 'Bidang Terpilih';
            const filteredSubs = sbData.filter(s => parseInt(s.bidang_id) === parsedBidangId);

            if (filteredSubs.length > 0) {
                newOptionsHtml += `
                    <div class="px-2.5 py-1.5 mt-1 mb-1 fw-bold text-dark-blue rounded bg-light border-bottom d-flex align-items-center justify-content-between" style="font-size: 0.76rem; letter-spacing: 0.2px;">
                        <span class="text-truncate"><i class="fas fa-layer-group text-primary me-1"></i> ${escapeHtml(bName)}</span>
                        <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill" style="font-size: 0.68rem;">${filteredSubs.length} Standar</span>
                    </div>
                `;
                filteredSubs.forEach(sb => {
                    const searchStr = `${sb.nama_sub_bidang || ''} ${bName}`.toLowerCase();
                    newOptionsHtml += `
                        <label class="dropdown-item d-flex align-items-start gap-2 py-2 px-2.5 rounded cursor-pointer sub-option-item" 
                               style="font-size: 0.8rem; white-space: normal;" 
                               data-search-text="${escapeHtml(searchStr)}">
                            <input type="checkbox" class="form-check-input flex-shrink-0 mt-0.5 sub-check-input" 
                                   name="${inputName}" value="${sb.id}" 
                                   onchange="handleSubStandarCheckChange('${uniqueKey}', this, ${sb.id}, '', '${escapeHtml(sb.nama_sub_bidang)}')">
                            <span class="lh-sm text-dark">${escapeHtml(sb.nama_sub_bidang)}</span>
                        </label>
                    `;
                });
            } else {
                newOptionsHtml = '<div class="px-3 py-3 text-center text-muted small fst-italic">Belum ada standar mutu yang terdaftar untuk bidang ini.</div>';
            }
            if (labelSpan) labelSpan.innerHTML = '<i class="fas fa-plus-circle text-primary me-1.5"></i> Pilih / Tambah Standar...';
        }
        if (optionsContainer) optionsContainer.innerHTML = newOptionsHtml;
    };

    window.updateSubStandarPickersInContainer = function(containerOrSelector, newBidangId) {
        let container = typeof containerOrSelector === 'string' ? document.querySelector(containerOrSelector) : containerOrSelector;
        if (!container) return;
        container.querySelectorAll('.sub-standar-picker-wrap').forEach(picker => {
            const key = picker.getAttribute('data-picker-key') || picker.id.replace('picker_wrap_', '');
            const inputName = picker.getAttribute('data-input-name') || '';
            updateSubStandarPickerOptions(key, newBidangId, inputName);
        });
    };

    window.handleSubStandarCheckChange = function(uniqueKey, checkbox, subId, subCode, subName) {
        const container = document.getElementById(`badges_container_${uniqueKey}`);
        if (!container) return;
        const placeholder = container.querySelector('.no-sub-placeholder');
        if (placeholder) placeholder.remove();

        const existingBadge = document.getElementById(`badge_${uniqueKey}_${subId}`);
        if (checkbox.checked) {
            if (!existingBadge) {
                const badge = document.createElement('span');
                badge.className = 'badge rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5 border shadow-2xs me-1 mb-1 text-wrap text-start lh-sm';
                badge.style.cssText = 'background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE !important; font-size: 0.78rem; font-weight: 600; max-width: 100%; word-break: break-word;';
                badge.id = `badge_${uniqueKey}_${subId}`;
                badge.title = subName;
                badge.innerHTML = `
                    <i class="fas fa-bookmark text-primary opacity-75 flex-shrink-0" style="font-size: 0.7rem;"></i>
                    <span>${escapeHtml(subName)}</span>
                    <i class="fas fa-xmark cursor-pointer text-muted hover-danger ms-1 flex-shrink-0" 
                       onclick="removeSubStandarTag('${uniqueKey}', ${subId})" title="Hapus standar ini"></i>
                `;
                container.appendChild(badge);
            }
        } else {
            if (existingBadge) existingBadge.remove();
            if (container.querySelectorAll('.badge').length === 0) {
                container.innerHTML = '<span class="text-muted fst-italic no-sub-placeholder" style="font-size: 0.78rem;">Belum ada standar dipilih (Bisa pilih 1 atau lebih)</span>';
            }
        }
    };

    window.removeSubStandarTag = function(uniqueKey, subId) {
        const badge = document.getElementById(`badge_${uniqueKey}_${subId}`);
        if (badge) badge.remove();
        const checkbox = document.querySelector(`#options_list_${uniqueKey} input[value="${subId}"]`);
        if (checkbox) checkbox.checked = false;
        const container = document.getElementById(`badges_container_${uniqueKey}`);
        if (container && container.querySelectorAll('.badge').length === 0) {
            container.innerHTML = '<span class="text-muted fst-italic no-sub-placeholder" style="font-size: 0.78rem;">Belum ada standar dipilih (Bisa pilih 1 atau lebih)</span>';
        }
    };

    window.filterSubStandarList = function(uniqueKey, query) {
        query = (query || '').toLowerCase().trim();
        document.querySelectorAll(`#options_list_${uniqueKey} .sub-option-item`).forEach(item => {
            const text = item.getAttribute('data-search-text') || '';
            item.style.display = text.includes(query) ? 'flex' : 'none';
        });
    };
}

// ==========================================
// BATCH MULTI-UPLOAD SYSTEM (UP TO 10 CARDS)
// ==========================================
let cardCounter = 0;
let batchStagedFiles = {}; // idx => [File]

function createDocCardTemplate(idx, presetData = null) {
    const defaultTahunVal = <?= json_encode($defaultTahun) ?>;
    const initCardLinkPicker = (typeof renderSubStandarPicker === 'function') 
        ? renderSubStandarPicker(`docs[${idx}][external_link_sub_bidang][0][]`, [], `card_${idx}_link_0`, presetData ? presetData.bidang_id : null)
        : '';
    
    let bidangOptions = '<option value="">-- Pilih Bidang --</option>';
    if (Array.isArray(BIDANGS_DATA)) {
        BIDANGS_DATA.forEach(b => {
            const isSelBid = (presetData && presetData.bidang_id == b.id) ? 'selected' : '';
            bidangOptions += `<option value="${b.id}" ${isSelBid}>${b.nama_bidang}</option>`;
        });
    }

    let cycleCardsHtml = '';
    for (const [key, val] of Object.entries(SIKLUS_DEFS)) {
        const isSelCyc = (presetData && presetData.siklus === key);
        cycleCardsHtml += `
            <label class="ppepp-cycle-card ${val.theme} ${isSelCyc ? 'selected' : ''}" onclick="selectBatchCycle(${idx}, '${key}', this)">
                <input type="radio" name="docs[${idx}][siklus]" value="${key}" class="ppepp-cycle-radio" ${isSelCyc ? 'checked' : ''}>
                <div class="ppepp-cycle-badge">${val.code}</div>
                <div class="ppepp-cycle-icon-wrap"><i class="fas ${val.icon}"></i></div>
                <div class="ppepp-cycle-title">${val.title}</div>
                <div class="ppepp-cycle-sub">${val.code}</div>
                <div class="ppepp-cycle-check"><i class="fas fa-check"></i></div>
            </label>
        `;
    }

    const docTitleVal = presetData && presetData.judul_siklus ? presetData.judul_siklus : '';
    const cycleIdVal = presetData && presetData.cycle_id ? presetData.cycle_id : '';
    const tahunAkadVal = presetData && presetData.tahun_akademik ? presetData.tahun_akademik : defaultTahunVal;
    const titlePreview = docTitleVal ? docTitleVal : '(Belum ada judul)';

    return `
    <div class="card border-0 shadow-sm rounded-4 doc-batch-card" id="docCard_${idx}" data-card-idx="${idx}" style="border: 1px solid #E2E8F0 !important;">
        <input type="hidden" name="docs[${idx}][cycle_id]" id="doc_cycle_id_${idx}" value="${cycleIdVal}">
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
                    ${titlePreview}
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
            <div class="d-flex flex-column gap-4">
                <!-- Bagian 1: Identitas & Klasifikasi Dokumen Mutu -->
                <div class="p-3.5 p-md-4 rounded-4 border bg-white shadow-2xs">
                    <div class="d-flex align-items-center justify-content-between pb-2.5 mb-3.5 border-bottom">
                        <h6 class="fw-bold text-dark-blue mb-0 d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary p-2" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                <i class="fas fa-file-lines"></i>
                            </span>
                            <span>Bagian 1: Identitas &amp; Klasifikasi Dokumen Mutu</span>
                        </h6>
                    </div>

                    <!-- Fixed Unit Scope Display -->
                    <div class="p-3 rounded-4 mb-3.5 border" style="background:#F8FAFC; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small text-muted fw-semibold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Unit Mutu Pengunggah:</span>
                                <div class="fw-bold text-dark-blue small mt-0.5">
                                    <i class="fas fa-graduation-cap text-success me-1"></i> ${PRODI_JENJANG} ${PRODI_NAME}
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-1.5 fw-semibold shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.78rem;">
                                Tingkat Program Studi
                            </span>
                        </div>
                    </div>

                    <!-- Nama Dokumen -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Nama Dokumen Mutu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="docs[${idx}][nama_dokumen]" id="doc_nama_${idx}" value="${docTitleVal.replace(/"/g, '&quot;')}" placeholder="Contoh: Standar Penilaian Pembelajaran" required oninput="updateCardTitlePreview(${idx}, this.value)">
                        ${cycleIdVal ? `
                            <div class="form-text text-primary small d-flex align-items-center gap-1.5 mt-1">
                                <i class="fas fa-link"></i> Terhubung ke Siklus Judul: <strong>${docTitleVal}</strong>
                            </div>
                        ` : ''}
                    </div>

                    <!-- Nomor Dokumen & Tahun Akademik -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small text-secondary">Nomor Dokumen / SK (Opsional)</label>
                            <input type="text" class="form-control" name="docs[${idx}][nomor_dokumen]" placeholder="Contoh: SK-PRODI/2026/01">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-secondary">Tahun Akademik <span class="text-danger">*</span></label>
                            <input type="text" class="form-control input-tahun-akademik" name="docs[${idx}][tahun_akademik]" value="${tahunAkadVal}" placeholder="Contoh: 2026/2027" maxlength="9" required oninput="autoPairTahunAkademik(this, event)" onblur="autoPairTahunAkademik(this, event)">
                            <div class="form-text small" style="font-size: 0.68rem;">Format: <code>YYYY/YYYY</code> (Otomatis berpasangan)</div>
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
                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-secondary">Bidang <span class="text-danger">*</span></label>
                        <select class="form-select" name="docs[${idx}][bidang_id]" required onchange="handleBatchCardBidangChange(${idx}, this.value)">
                            ${bidangOptions}
                        </select>
                    </div>
                </div>

                <!-- Bagian 2: Berkas & Kontrol Akses -->
                <div class="p-3.5 p-md-4 rounded-4 border shadow-2xs" style="background: #F8FAFC !important; border-color: #E2E8F0 !important;">
                    <div class="d-flex align-items-center justify-content-between pb-2.5 mb-3.5 border-bottom">
                        <h6 class="fw-bold text-dark-blue mb-0">
                            Bagian 2: Berkas Lampiran &amp; Hak Akses Publik
                        </h6>
                    </div>

                    <!-- Interactive Method Cards for Batch Card (3 Pilihan) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-dark mb-2.5 d-flex align-items-center gap-1.5">
                            <i class="fas fa-layer-group text-primary"></i> Pilih Metode Penyediaan Berkas <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative active" 
                                     id="method_card_file_${idx}" onclick="toggleCardMethod(${idx}, 'file')">
                                    <input type="radio" class="d-none" name="docs[${idx}][jenis_upload]" id="card_jenis_file_${idx}" value="file" checked>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #FEE2E2; color: #DC2626; font-size: 0.9rem;">
                                            <i class="fas fa-file-arrow-up"></i>
                                        </span>
                                        <div class="lh-sm">
                                            <div class="fw-bold text-dark-blue" style="font-size: 0.85rem;">Berkas Dokumen</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">PDF, DOCX, XLSX</div>
                                        </div>
                                    </div>
                                    <div class="method-check-badge position-absolute top-0 end-0 p-2" id="badge_check_file_${idx}">
                                        <i class="fas fa-circle-check text-primary" style="font-size: 0.85rem;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative" 
                                     id="method_card_link_${idx}" onclick="toggleCardMethod(${idx}, 'link')">
                                    <input type="radio" class="d-none" name="docs[${idx}][jenis_upload]" id="card_jenis_link_${idx}" value="link">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #E0F2FE; color: #0284C7; font-size: 0.9rem;">
                                            <i class="fab fa-google-drive"></i>
                                        </span>
                                        <div class="lh-sm">
                                            <div class="fw-bold text-dark-blue" style="font-size: 0.85rem;">Google Drive</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">Tautan Berbagi Link</div>
                                        </div>
                                    </div>
                                    <div class="method-check-badge position-absolute top-0 end-0 p-2" id="badge_check_link_${idx}" style="display: none;">
                                        <i class="fas fa-circle-check text-primary" style="font-size: 0.85rem;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="method-card p-3 rounded-3 border bg-white cursor-pointer h-100 transition-all shadow-2xs position-relative" 
                                     id="method_card_kombinasi_${idx}" onclick="toggleCardMethod(${idx}, 'kombinasi')">
                                    <input type="radio" class="d-none" name="docs[${idx}][jenis_upload]" id="card_jenis_kombinasi_${idx}" value="kombinasi">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <span class="method-card-icon rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: #F3E8FF; color: #7E22CE; font-size: 0.9rem;">
                                            <i class="fas fa-layer-group"></i>
                                        </span>
                                        <div class="lh-sm">
                                            <div class="fw-bold text-dark-blue" style="font-size: 0.85rem;">Kombinasi</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">Berkas &amp; Link GDrive</div>
                                        </div>
                                    </div>
                                    <div class="method-check-badge position-absolute top-0 end-0 p-2" id="badge_check_kombinasi_${idx}" style="display: none;">
                                        <i class="fas fa-circle-check text-primary" style="font-size: 0.85rem;"></i>
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
                        <div class="upload-dropzone-box text-center cursor-pointer shadow-2xs mb-2" onclick="triggerAddMoreCardFiles(${idx})">
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
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 py-2 cursor-pointer fw-semibold shadow-xs my-1" style="font-size: 0.8rem;" onclick="event.stopPropagation(); triggerAddMoreCardFiles(${idx})">
                                <i class="fas fa-plus me-1"></i> Telusuri Berkas
                            </button>
                            <div class="text-muted mt-2.5 d-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.73rem;">
                                <i class="fas fa-lightbulb text-warning"></i> <span>Klik tombol di atas untuk menambah berkas</span>
                            </div>
                        </div>
                        <input type="file" id="card_files_input_${idx}" name="files_${idx}[]" multiple class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx" onchange="handleCardFileInput(${idx}, this)">
                        <div id="card_files_preview_${idx}" class="d-flex flex-column gap-2 mt-2"></div>
                        <button type="button" id="card_add_more_btn_${idx}" class="btn btn-sm btn-outline-primary rounded-pill w-100 py-2 fw-semibold mt-2" style="display:none;" onclick="triggerAddMoreCardFiles(${idx})">
                            <i class="fas fa-plus me-1.5"></i> Tambah Berkas Lainnya
                        </button>
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
                        <div id="card_gdrive_links_${idx}" class="d-flex flex-column gap-3 mb-3">
                            <div class="card-gdrive-row card p-3.5 bg-white border rounded-4 shadow-2xs">
                                <div class="d-flex align-items-center justify-content-between pb-2.5 border-bottom">
                                    <span class="fw-bold text-dark-blue d-flex align-items-center gap-2 card-gdrive-title" style="font-size: 0.88rem;">
                                        <div class="p-1.5 rounded-3 bg-success bg-opacity-10 text-success flex-shrink-0">
                                            <i class="fab fa-google-drive"></i>
                                        </div>
                                        <span>Tautan Berkas #1</span>
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill fw-semibold" style="font-size: 0.78rem;" onclick="removeCardGdriveLink(this, ${idx})" title="Batalkan / Hapus tautan ini">
                                        <i class="fas fa-trash-can me-1"></i> Hapus
                                    </button>
                                </div>
                                <div class="mt-2.5 mb-2">
                                    <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                        <i class="fas fa-file-lines text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                                        <input type="url" class="form-control py-2 gdrive-doc-input" name="docs[${idx}][external_links][]" placeholder="https://drive.google.com/file/d/... (Khusus dokumen, bukan folder)" oninput="validateGdriveDocInput(this)" onblur="validateGdriveDocInput(this)">
                                    </div>
                                    <div class="gdrive-validation-feedback text-danger small mt-1 fw-semibold" style="display: none; font-size: 0.76rem;"></div>
                                    <div class="form-text text-muted small mt-1" style="font-size: 0.74rem;">
                                        <i class="fas fa-circle-info text-primary me-1"></i> Khusus tautan <strong>dokumen / file langsung</strong> (contoh: <code>drive.google.com/file/d/...</code> atau Google Docs/Sheets). <strong>Tautan folder tidak diperbolehkan.</strong>
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
                                <div class="row g-2.5 mt-2.5">
                                    <div class="col-md-7">
                                        <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_link_limit_sw_${idx}_0" name="docs[${idx}][external_link_is_limited][0]" value="1" checked onchange="document.getElementById('card_link_limit_wrap_${idx}_0').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                                                <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_link_limit_sw_${idx}_0" style="font-size: 0.82rem;">
                                                    Batasi Pratinjau
                                                </label>
                                            </div>
                                            <div class="input-group input-group-sm" id="card_link_limit_wrap_${idx}_0" style="width: 100px;">
                                                <input type="number" class="form-control text-center py-1 fw-bold" name="docs[${idx}][external_link_page_limit][0]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                                                <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_link_dl_sw_${idx}_0" name="docs[${idx}][external_link_can_download][0]" value="1" style="width: 2.2em; height: 1.2em;">
                                                <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_link_dl_sw_${idx}_0" style="font-size: 0.82rem;">
                                                    Unduh Publik
                                                </label>
                                            </div>
                                            <span class="badge bg-light text-muted border small d-none d-sm-inline">Publik</span>
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
    const fSec = document.getElementById(`card_section_file_${idx}`);
    const lSec = document.getElementById(`card_section_link_${idx}`);
    if (fSec) fSec.style.display = (method === 'file' || method === 'kombinasi') ? 'block' : 'none';
    if (lSec) lSec.style.display = (method === 'link' || method === 'kombinasi') ? 'block' : 'none';

    const rFile = document.getElementById(`card_jenis_file_${idx}`);
    const rLink = document.getElementById(`card_jenis_link_${idx}`);
    const rKomb = document.getElementById(`card_jenis_kombinasi_${idx}`);
    if (rFile) rFile.checked = (method === 'file');
    if (rLink) rLink.checked = (method === 'link');
    if (rKomb) rKomb.checked = (method === 'kombinasi');

    ['file', 'link', 'kombinasi'].forEach(m => {
        const card = document.getElementById(`method_card_${m}_${idx}`);
        const badge = document.getElementById(`badge_check_${m}_${idx}`);
        if (card) {
            if (m === method) {
                card.classList.add('active');
                if (badge) badge.style.display = 'block';
            } else {
                card.classList.remove('active');
                if (badge) badge.style.display = 'none';
            }
        }
    });
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

// ==========================================
// BATCH STAGING LOGIC
// ==========================================
function saveCardStagedState(idx) {
    const files = batchStagedFiles[idx] || [];
    const state = [];
    files.forEach((_, fIdx) => {
        const narasiEl = document.querySelector(`textarea[name="docs[${idx}][file_narasi][${fIdx}]"]`);
        const limitSw = document.getElementById(`card_limit_sw_${idx}_${fIdx}`);
        const limitVal = document.querySelector(`input[name="docs[${idx}][file_page_limit][${fIdx}]"]`);
        const dlSw = document.getElementById(`card_dl_sw_${idx}_${fIdx}`);
        
        const checkedSub = [];
        document.querySelectorAll(`input[name="docs[${idx}][file_sub_bidang][${fIdx}][]"]:checked`).forEach(cb => {
            checkedSub.push(parseInt(cb.value));
        });

        state[fIdx] = {
            narasi: narasiEl ? narasiEl.value : '',
            is_limited: limitSw ? limitSw.checked : true,
            page_limit: limitVal ? limitVal.value : '1',
            can_download: dlSw ? dlSw.checked : false,
            sub_bidang_ids: checkedSub
        };
    });
    return state;
}

function syncCardInputFiles(idx) {
    const input = document.getElementById(`card_files_input_${idx}`);
    if (!input) return;
    const dt = new DataTransfer();
    const files = batchStagedFiles[idx] || [];
    files.forEach(f => dt.items.add(f));
    input.files = dt.files;
}

function triggerAddMoreCardFiles(idx) {
    const tempInput = document.createElement('input');
    tempInput.type = 'file';
    tempInput.multiple = true;
    tempInput.accept = '.pdf,.doc,.docx,.xls,.xlsx';
    tempInput.onchange = function() {
        if (tempInput.files && tempInput.files.length > 0) {
            if (!batchStagedFiles[idx]) batchStagedFiles[idx] = [];
            const savedState = saveCardStagedState(idx);
            Array.from(tempInput.files).forEach(f => {
                batchStagedFiles[idx].push(f);
            });
            syncCardInputFiles(idx);
            renderCardStagedFiles(idx, savedState);
        }
    };
    tempInput.click();
}

function handleCardFileInput(idx, input) {
    if (!input.files || input.files.length === 0) return;
    if (!batchStagedFiles[idx]) batchStagedFiles[idx] = [];
    const savedState = saveCardStagedState(idx);
    Array.from(input.files).forEach(f => {
        batchStagedFiles[idx].push(f);
    });
    syncCardInputFiles(idx);
    renderCardStagedFiles(idx, savedState);
}

function removeCardStagedFile(cardIdx, fileIndex) {
    if (!batchStagedFiles[cardIdx]) return;
    const savedState = saveCardStagedState(cardIdx);
    batchStagedFiles[cardIdx].splice(fileIndex, 1);
    savedState.splice(fileIndex, 1);
    syncCardInputFiles(cardIdx);
    renderCardStagedFiles(cardIdx, savedState);
}

function renderCardStagedFiles(idx, savedState = []) {
    const preview = document.getElementById(`card_files_preview_${idx}`);
    const addMoreBtn = document.getElementById(`card_add_more_btn_${idx}`);
    if (!preview) return;
    preview.innerHTML = '';
    const files = batchStagedFiles[idx] || [];
    if (files.length > 0) {
        if (addMoreBtn) addMoreBtn.style.display = 'block';
    } else {
        if (addMoreBtn) addMoreBtn.style.display = 'none';
    }
    const card = document.getElementById(`docCard_${idx}`);
    const cardBidangId = card ? card.querySelector(`select[name="docs[${idx}][bidang_id]"]`)?.value : null;

    files.forEach((file, fIdx) => {
        const st = savedState[fIdx] || {};
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

        const selectedSubs = st.sub_bidang_ids || [];
        const pickerHtml = renderSubStandarPicker(`docs[${idx}][file_sub_bidang][${fIdx}][]`, selectedSubs, `card_${idx}_file_${fIdx}`, cardBidangId);
        const isLimited = (st.is_limited !== undefined) ? st.is_limited : true;
        const pageLimit = st.page_limit || 1;
        const canDownload = (st.can_download !== undefined) ? st.can_download : false;
        const narasiVal = (st.narasi !== undefined) ? st.narasi : '';

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
                <textarea class="form-control py-2" name="docs[${idx}][file_narasi][${fIdx}]" rows="1" placeholder="Keterangan / narasi berkas ini..." required>${narasiVal ? String(narasiVal).replace(/</g, '&lt;').replace(/>/g, '&gt;') : ''}</textarea>
            </div>

            ${pickerHtml}

            <!-- Per-File Access Settings (2-Card Pill System) -->
            <div class="row g-2.5 mt-2.5">
                <div class="col-md-7">
                    <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_limit_sw_${idx}_${fIdx}" name="docs[${idx}][file_is_limited][${fIdx}]" value="1" ${isLimited ? 'checked' : ''} onchange="document.getElementById('card_limit_wrap_${idx}_${fIdx}').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_limit_sw_${idx}_${fIdx}" style="font-size: 0.82rem;">
                                Batasi Pratinjau
                            </label>
                        </div>
                        <div class="input-group input-group-sm" id="card_limit_wrap_${idx}_${fIdx}" style="width: 100px; ${isLimited ? 'display:inline-flex;' : 'display:none;'}">
                            <input type="number" class="form-control text-center py-1 fw-bold" name="docs[${idx}][file_page_limit][${fIdx}]" min="1" max="50" value="${pageLimit}" style="font-size: 0.78rem;">
                            <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_dl_sw_${idx}_${fIdx}" name="docs[${idx}][file_can_download][${fIdx}]" value="1" ${canDownload ? 'checked' : ''} style="width: 2.2em; height: 1.2em;">
                            <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_dl_sw_${idx}_${fIdx}" style="font-size: 0.82rem;">
                                Unduh Publik
                            </label>
                        </div>
                        <span class="badge bg-light text-muted border small d-none d-sm-inline">Publik</span>
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
                <i class="fas fa-file-lines text-primary"></i> URL Tautan Dokumen (Google Drive): <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-primary px-3"><i class="fas fa-link"></i></span>
                <input type="url" class="form-control py-2 gdrive-doc-input" name="docs[${idx}][external_links][]" placeholder="https://drive.google.com/file/d/... (Khusus dokumen, bukan folder)" oninput="validateGdriveDocInput(this)" onblur="validateGdriveDocInput(this)">
            </div>
            <div class="gdrive-validation-feedback text-danger small mt-1 fw-semibold" style="display: none; font-size: 0.76rem;"></div>
            <div class="form-text text-muted small mt-1" style="font-size: 0.74rem;">
                <i class="fas fa-circle-info text-primary me-1"></i> Khusus tautan <strong>dokumen / file langsung</strong> (contoh: <code>drive.google.com/file/d/...</code>). <strong>Tautan folder tidak diperbolehkan.</strong>
            </div>
        </div>
        <div class="mb-2">
            <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                <i class="fas fa-align-left text-primary"></i> Narasi / Keterangan Tautan: <span class="text-danger">*</span>
            </label>
            <textarea class="form-control py-2" name="docs[${idx}][external_link_narasi][]" rows="1" placeholder="Keterangan / Narasi tautan dokumen ini..."></textarea>
        </div>
        ${pickerHtml}

        <!-- Per-Link Access Settings -->
        <div class="row g-2.5 mt-2.5">
            <div class="col-md-7">
                <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_link_limit_sw_${idx}_${count}" name="docs[${idx}][external_link_is_limited][${count - 1}]" value="1" checked onchange="document.getElementById('card_link_limit_wrap_${idx}_${count}').style.display = this.checked ? 'inline-flex' : 'none'" style="width: 2.2em; height: 1.2em;">
                        <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_link_limit_sw_${idx}_${count}" style="font-size: 0.82rem;">
                            Batasi Pratinjau
                        </label>
                    </div>
                    <div class="input-group input-group-sm" id="card_link_limit_wrap_${idx}_${count}" style="width: 100px;">
                        <input type="number" class="form-control text-center py-1 fw-bold" name="docs[${idx}][external_link_page_limit][${count - 1}]" min="1" max="50" value="1" style="font-size: 0.78rem;">
                        <span class="input-group-text px-1.5 text-muted small">Hlm</span>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="access-setting-pill d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle h-100">
                    <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="card_link_dl_sw_${idx}_${count}" name="docs[${idx}][external_link_can_download][${count - 1}]" value="1" style="width: 2.2em; height: 1.2em;">
                        <label class="form-check-label text-dark fw-bold small mb-0 cursor-pointer" for="card_link_dl_sw_${idx}_${count}" style="font-size: 0.82rem;">
                            Unduh Publik
                        </label>
                    </div>
                    <span class="badge bg-light text-muted border small d-none d-sm-inline">Publik</span>
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

function addNewDocCard(presetData = null) {
    try {
        const currentCards = document.querySelectorAll('.doc-batch-card');
        if (currentCards.length >= 10) {
            alert('Maksimal pengunggahan sekaligus adalah 10 dokumen.');
            return;
        }
        const container = document.getElementById('batchCardsContainer');
        if (!container) return;
        const template = createDocCardTemplate(cardCounter, presetData);
        container.insertAdjacentHTML('beforeend', template);
        
        // Jika ada preset bidang, sinkronkan sub-bidang picker di kartu ini
        if (presetData && presetData.bidang_id) {
            handleBatchCardBidangChange(cardCounter, presetData.bidang_id);
        }

        cardCounter++;
        updateTotalCardCounter();
    } catch (err) {
        console.error('Error saat merender kartu formulir dokumen prodi:', err);
    }
}

function continueCycleUpload(cycleData, stageKey) {
    if (!cycleData) return;
    
    // 1. Tampilkan dan perbarui banner continuation
    const banner = document.getElementById('activeContinuationBanner');
    if (banner) {
        banner.style.display = 'block';
        const jEl = document.getElementById('continuationJudulText');
        const sEl = document.getElementById('continuationStageText');
        const bEl = document.getElementById('continuationBidangText');
        if (jEl) jEl.textContent = cycleData.judul_siklus;
        
        const stageDef = SIKLUS_DEFS[stageKey] || { title: stageKey, code: stageKey.toUpperCase() };
        if (sEl) sEl.textContent = stageDef.title + ' (' + stageDef.code + ')';
        if (bEl) bEl.textContent = cycleData.nama_bidang + (cycleData.nama_sub_bidang ? ' - ' + cycleData.nama_sub_bidang : '');
    }

    // 2. Beri highlight visual pada kartu siklus di daftar
    document.querySelectorAll('.cycle-item-card').forEach(c => c.classList.remove('active-continuation'));
    const targetCard = document.getElementById('cycle_card_' + cycleData.id);
    if (targetCard) targetCard.classList.add('active-continuation');

    // 3. Reset kontainer kartu formulir
    const container = document.getElementById('batchCardsContainer');
    if (container) container.innerHTML = '';
    batchStagedFiles = {};
    cardCounter = 0;

    // 4. Buat kartu baru terisi preset judul dan tahap yang dipilih
    addNewDocCard({
        cycle_id: cycleData.id,
        judul_siklus: cycleData.judul_siklus,
        bidang_id: cycleData.bidang_id,
        sub_bidang_id: cycleData.sub_bidang_id,
        tahun_akademik: cycleData.tahun_akademik,
        siklus: stageKey,
        is_continuation: true
    });

    // 5. Scroll mulus ke banner aktif
    if (banner) {
        banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function resetFormToNewCycle() {
    const banner = document.getElementById('activeContinuationBanner');
    if (banner) banner.style.display = 'none';
    
    document.querySelectorAll('.cycle-item-card').forEach(c => c.classList.remove('active-continuation'));
    
    const container = document.getElementById('batchCardsContainer');
    if (container) container.innerHTML = '';
    batchStagedFiles = {};
    cardCounter = 0;

    addNewDocCard(); // buat kartu kosong baru

    const form = document.getElementById('batchDocForm');
    if (form) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function filterCycleList(query) {
    const q = (query || '').toLowerCase().trim();
    document.querySelectorAll('.cycle-item-card').forEach(card => {
        const title = card.getAttribute('data-cycle-title') || '';
        card.style.display = (!q || title.includes(q)) ? 'block' : 'none';
    });
}

function removeDocCard(idx, evt) {
    evt.stopPropagation();
    const card = document.getElementById(`docCard_${idx}`);
    if (card) {
        card.remove();
        delete batchStagedFiles[idx];
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
        if (PRESET_CYCLE && PRESET_SIKLUS) {
            continueCycleUpload(PRESET_CYCLE, PRESET_SIKLUS);
        } else {
            addNewDocCard(); // Initialize card #1
        }
    } else {
        const initialBidangId = document.getElementById('bidang_id')?.value;
        if (initialBidangId) handleSingleBidangChange(initialBidangId);

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
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('bg-light', 'border-primary');
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    const savedState = saveSingleStagedState();
                    Array.from(e.dataTransfer.files).forEach(f => {
                        singleStagedFiles.push(f);
                    });
                    syncSingleInputFiles();
                    renderSingleStagedFiles(savedState);
                }
            }, false);
        }
    }

    // Attach form submit validation, base64 conversion & auto-formatting
    const batchForm = document.getElementById('batchDocForm');
    if (batchForm) {
        let isBatchSubmitting = false;
        batchForm.addEventListener('submit', async function(e) {
            if (isBatchSubmitting) return;

            batchForm.querySelectorAll('.input-tahun-akademik, input[name*="tahun_akademik"]').forEach(inp => {
                if (typeof window.autoPairTahunAkademik === 'function') {
                    window.autoPairTahunAkademik(inp, { type: 'blur' });
                }
            });
            if (!validateAllGdriveInputs(batchForm)) {
                e.preventDefault();
                return false;
            }

            // Hitung total berkas yang dilampirkan
            let totalFiles = 0;
            Object.values(batchStagedFiles || {}).forEach(arr => {
                if (Array.isArray(arr)) totalFiles += arr.length;
            });

            if (totalFiles === 0) {
                // HANYA LINK: nonaktifkan file input agar form dikirim murni urlencoded tanpa multipart
                batchForm.removeAttribute('enctype');
                batchForm.enctype = 'application/x-www-form-urlencoded';
                batchForm.querySelectorAll('input[type="file"]').forEach(inp => {
                    inp.disabled = true;
                });
                return true;
            }

            // ADA BERKAS: konversi berkas ke Base64 agar dapat dikirim dengan form-urlencoded
            // Ini untuk mengatasi emergency rule OpenResty yang memblokir multipart/form-data
            e.preventDefault();
            const submitBtn = e.submitter || document.activeElement || document.getElementById('btnSubmitPublishBatch');
            const origBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyiapkan Berkas...';
            }

            // Bersihkan input base64 sebelumnya
            batchForm.querySelectorAll('.input-file-base64').forEach(el => el.remove());

            try {
                // Periksa total ukuran berkas agar tidak melebihi kapasitas server (maks 8MB)
                let totalBytes = 0;
                for (const files of Object.values(batchStagedFiles)) {
                    if (Array.isArray(files)) {
                        for (const f of files) totalBytes += f.size;
                    }
                }
                if (totalBytes > 8 * 1024 * 1024) {
                    alert('Total ukuran berkas (' + (totalBytes / (1024 * 1024)).toFixed(1) + ' MB) melebihi batas 8 MB pada hosting.\n\nSilakan gunakan tautan Google Drive untuk berkas berukuran besar.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = origBtnHtml;
                    }
                    return false;
                }

                for (const [idx, files] of Object.entries(batchStagedFiles)) {
                    if (!Array.isArray(files)) continue;
                    for (let fIdx = 0; fIdx < files.length; fIdx++) {
                        const file = files[fIdx];
                        const base64Data = await readFileAsBase64(file);

                        const inpName = document.createElement('input');
                        inpName.type = 'hidden';
                        inpName.className = 'input-file-base64';
                        inpName.name = `docs[${idx}][files_base64][${fIdx}][name]`;
                        inpName.value = file.name;
                        batchForm.appendChild(inpName);

                        const inpData = document.createElement('input');
                        inpData.type = 'hidden';
                        inpData.className = 'input-file-base64';
                        inpData.name = `docs[${idx}][files_base64][${fIdx}][data]`;
                        inpData.value = base64Data;
                        batchForm.appendChild(inpData);
                    }
                }

                // Nonaktifkan file input fisik agar browser tidak mengirim request multipart
                batchForm.querySelectorAll('input[type="file"]').forEach(inp => {
                    inp.disabled = true;
                });

                // Ubah enctype menjadi application/x-www-form-urlencoded
                batchForm.removeAttribute('enctype');
                batchForm.enctype = 'application/x-www-form-urlencoded';

                // Sertakan action_submit button yang ditekan
                if (submitBtn && submitBtn.name && submitBtn.value) {
                    let actionInp = document.getElementById('batch_temp_action_submit');
                    if (!actionInp) {
                        actionInp = document.createElement('input');
                        actionInp.type = 'hidden';
                        actionInp.id = 'batch_temp_action_submit';
                        actionInp.name = submitBtn.name;
                        batchForm.appendChild(actionInp);
                    }
                    actionInp.value = submitBtn.value;
                }

                isBatchSubmitting = true;
                batchForm.submit();
            } catch (err) {
                console.error('Error membaca berkas:', err);
                alert('Gagal memproses berkas: ' + err.message);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origBtnHtml;
                }
            }
        });
    }

    const singleEditForm = document.getElementById('documentUploadForm') || document.getElementById('editDocForm') || document.querySelector('form[action*="/save"]');
    if (singleEditForm) {
        let isSingleSubmitting = false;
        singleEditForm.addEventListener('submit', async function(e) {
            if (isSingleSubmitting) return;

            singleEditForm.querySelectorAll('.input-tahun-akademik, input[name*="tahun_akademik"]').forEach(inp => {
                if (typeof window.autoPairTahunAkademik === 'function') {
                    window.autoPairTahunAkademik(inp, { type: 'blur' });
                }
            });
            if (!validateAllGdriveInputs(singleEditForm)) {
                e.preventDefault();
                return false;
            }

            const singleFiles = (typeof singleStagedFiles !== 'undefined' && Array.isArray(singleStagedFiles)) ? singleStagedFiles : [];
            if (singleFiles.length === 0) {
                singleEditForm.removeAttribute('enctype');
                singleEditForm.enctype = 'application/x-www-form-urlencoded';
                singleEditForm.querySelectorAll('input[type="file"]').forEach(inp => {
                    inp.disabled = true;
                });
                return true;
            }

            e.preventDefault();
            const submitBtn = e.submitter || document.activeElement;
            const origBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyiapkan Berkas...';
            }

            singleEditForm.querySelectorAll('.input-single-file-base64').forEach(el => el.remove());
            try {
                // Periksa total ukuran berkas agar tidak melebihi kapasitas server
                let totalBytes = 0;
                for (const f of singleFiles) totalBytes += f.size;
                if (totalBytes > 8 * 1024 * 1024) {
                    alert('Total ukuran berkas (' + (totalBytes / (1024 * 1024)).toFixed(1) + ' MB) melebihi batas 8 MB pada hosting.\n\nSilakan gunakan tautan Google Drive untuk berkas berukuran besar.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = origBtnHtml;
                    }
                    return false;
                }

                for (let idx = 0; idx < singleFiles.length; idx++) {
                    const file = singleFiles[idx];
                    const base64Data = await readFileAsBase64(file);

                    const inpName = document.createElement('input');
                    inpName.type = 'hidden';
                    inpName.className = 'input-single-file-base64';
                    inpName.name = `new_files_base64[${idx}][name]`;
                    inpName.value = file.name;
                    singleEditForm.appendChild(inpName);

                    const inpData = document.createElement('input');
                    inpData.type = 'hidden';
                    inpData.className = 'input-single-file-base64';
                    inpData.name = `new_files_base64[${idx}][data]`;
                    inpData.value = base64Data;
                    singleEditForm.appendChild(inpData);
                }

                singleEditForm.querySelectorAll('input[type="file"]').forEach(inp => {
                    inp.disabled = true;
                });

                singleEditForm.removeAttribute('enctype');
                singleEditForm.enctype = 'application/x-www-form-urlencoded';

                if (submitBtn && submitBtn.name && submitBtn.value) {
                    let actionInp = document.getElementById('single_temp_action_submit');
                    if (!actionInp) {
                        actionInp = document.createElement('input');
                        actionInp.type = 'hidden';
                        actionInp.id = 'single_temp_action_submit';
                        actionInp.name = submitBtn.name;
                        singleEditForm.appendChild(actionInp);
                    }
                    actionInp.value = submitBtn.value;
                }

                isSingleSubmitting = true;
                singleEditForm.submit();
            } catch (err) {
                console.error('Error membaca berkas single:', err);
                alert('Gagal memproses berkas: ' + err.message);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origBtnHtml;
                }
            }
        });
    }

function readFileAsBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
        reader.readAsDataURL(file);
    });
}
});

/**
 * Validasi tautan Google Drive / Eksternal (khusus dokumen, menolak tautan folder)
 */
function validateGdriveDocInput(input) {
    if (!input) return true;
    const val = (input.value || '').trim();
    const parent = input.closest('.mb-2') || input.parentElement.parentElement;
    let feedback = parent.querySelector('.gdrive-validation-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'gdrive-validation-feedback text-danger small mt-1 fw-semibold';
        feedback.style.fontSize = '0.76rem';
        const group = input.closest('.input-group');
        if (group) group.after(feedback);
        else input.after(feedback);
    }

    if (!val) {
        input.classList.remove('is-invalid', 'is-valid');
        feedback.style.display = 'none';
        feedback.textContent = '';
        return true;
    }

    // Cek apakah tautan berupa folder Google Drive
    const isFolder = /\/folders\//i.test(val) || /drive\/.*folders/i.test(val);
    if (isFolder) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        feedback.innerHTML = '<i class="fas fa-triangle-exclamation me-1"></i> <strong>Tautan Folder Tidak Diperbolehkan!</strong> Harap buka file dokumen di Google Drive, lalu salin tautan file/dokumen spesifik tersebut (contoh: <code>drive.google.com/file/d/...</code>).';
        feedback.style.display = 'block';
        return false;
    }

    // Jika tautan Google Drive / Docs, cek apakah pola link dokumen terpenuhi
    if (/(drive|docs)\.google\.com/i.test(val)) {
        const isDocPattern = /\/(file|document|spreadsheets|presentation|forms|drawings)\/d\/[a-zA-Z0-9_-]+/i.test(val)
                          || /drive\.google\.com\/open\?id=[a-zA-Z0-9_-]+/i.test(val)
                          || /drive\.google\.com\/uc\?id=[a-zA-Z0-9_-]+/i.test(val);
        if (!isDocPattern) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.innerHTML = '<i class="fas fa-triangle-exclamation me-1"></i> Tautan harus berupa link dokumen spesifik (contoh: <code>https://drive.google.com/file/d/...</code> atau Google Docs), bukan folder atau halaman beranda Google Drive.';
            feedback.style.display = 'block';
            return false;
        }
    }

    // Valid
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    feedback.style.display = 'none';
    feedback.textContent = '';
    return true;
}

function validateAllGdriveInputs(container) {
    const inputs = (container || document).querySelectorAll('input[name*="external_links"], .gdrive-doc-input');
    let hasError = false;
    let firstErrorInput = null;

    inputs.forEach(inp => {
        if (!validateGdriveDocInput(inp)) {
            hasError = true;
            if (!firstErrorInput) firstErrorInput = inp;
        }
    });

    if (hasError && firstErrorInput) {
        firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstErrorInput.focus();
        alert('Terdapat tautan Google Drive yang berupa FOLDER atau tidak valid.\n\nSistem hanya menerima tautan dokumen/file langsung (bukan folder). Harap periksa dan ganti tautan tersebut.');
        return false;
    }
    return true;
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
