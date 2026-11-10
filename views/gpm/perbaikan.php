<?php
/**
 * GPM (Gugus Penjaminan Mutu): Ruang Kerja Perbaikan Dokumen Mutu
 * Mengelola revisi dokumen dari LPM untuk tingkat Fakultas dan seluruh Program Studi
 * Alur Berjenjang Multi-Tier: LPM -> GPM Fakultas -> Prodi / Dekanat
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$revisiDocs     = $revisiDocs ?? [];
$lpmRevisiDocs  = $lpmRevisiDocs ?? array_values(array_filter($revisiDocs, fn($d) => in_array($d['status_review'] ?? '', ['revisi_lpm', 'perlu_perbaikan'])));
$unitRevisiDocs = $unitRevisiDocs ?? array_values(array_filter($revisiDocs, fn($d) => ($d['status_review'] ?? '') === 'revisi_gpm'));
$sudahDocs      = $sudahDocs ?? array_values(array_filter($revisiDocs, fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));
$perluDocs      = $perluDocs ?? array_values(array_filter($revisiDocs, fn($d) => in_array($d['status_review'] ?? '', ['revisi_lpm', 'revisi_gpm', 'perlu_perbaikan'])));

$fakultas      = $fakultas ?? [];
$prodisList    = $prodisList ?? [];
$unitFilter    = $unitFilter ?? 'all';
$totalPerlu    = $totalPerlu ?? count($perluDocs);
$totalRevisiGpm = count($unitRevisiDocs);
$totalRevisiLpm = count($lpmRevisiDocs);
$totalSudah    = count($sudahDocs);
$fakultasPerlu = $fakultasPerlu ?? 0;
$prodiPerlu    = $prodiPerlu ?? 0;

// Tab aktif default: Jika ada catatan baru dari LPM, prioritaskan tab LPM
$activeTab = ($totalRevisiLpm > 0) ? 'tab-lpm' : (($totalRevisiGpm > 0) ? 'tab-unit' : (($totalSudah > 0) ? 'tab-sudah' : 'tab-all'));
?>

<style>
.gpm-note-card {
    background: linear-gradient(135deg, #FFFDF7 0%, #FFF7ED 100%);
    border: 1px solid #FED7AA;
    border-left: 4px solid #EA580C !important;
    border-radius: 12px;
    padding: 10px 12px;
    box-shadow: 0 2px 8px rgba(234, 88, 12, 0.06);
    max-width: 310px;
}
.gpm-note-card.is-gpm {
    background: linear-gradient(135deg, #FAF5FF 0%, #F5F3FF 100%);
    border-color: #DDD6FE;
    border-left-color: #7C3AED !important;
    box-shadow: 0 2px 8px rgba(124, 58, 237, 0.06);
}
.gpm-note-card-inner {
    background: #FFFFFF;
    border: 1px solid #FFEDD5;
    border-radius: 8px;
    padding: 8px 10px;
}
.gpm-note-card.is-gpm .gpm-note-card-inner {
    border-color: #EDE9FE;
}
.gpm-note-clamp {
    font-size: 0.78rem;
    line-height: 1.5;
    color: #1E293B;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
}
.gpm-note-btn-detail {
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    color: #0F172A;
    font-size: 0.72rem;
    font-weight: 600;
    transition: all 0.16s ease;
}
.gpm-note-btn-detail:hover {
    background: #0A192F;
    border-color: #0A192F;
    color: #FFFFFF !important;
}
.gpm-btn-forward {
    background: linear-gradient(135deg, #EA580C 0%, #D97706 100%);
    color: #FFFFFF !important;
    border: none;
    font-size: 0.77rem;
    font-weight: 700;
    box-shadow: 0 3px 10px rgba(234, 88, 12, 0.25);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.gpm-btn-forward:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 14px rgba(234, 88, 12, 0.35);
}
.gpm-step-badge {
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    padding: 0.35em 0.7em;
    border-radius: 999px;
    text-transform: uppercase;
}
</style>

<div class="container-fluid p-0 admin-container">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1.5">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #F3E8FF; color: #7E22CE; border: 1px solid #E9D5FF; font-size: 0.76rem;">
                    <i class="fas fa-shield-halved"></i> GPM <?= htmlspecialchars($fakultas['singkatan'] ?? $fakultas['nama_fakultas'] ?? '') ?>
                </span>
                <span class="text-muted small">&bull;</span>
                <span class="badge rounded-pill px-2.5 py-1 fw-semibold text-danger d-inline-flex align-items-center gap-1" style="background: #FEF2F2; border: 1px solid #FECACA; font-size: 0.75rem;">
                    <i class="fas fa-clipboard-check"></i> Monitoring Revisi Dokumen Berjenjang
                </span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">Monitoring Revisi Dokumen Mutu</h3>
            <p class="text-muted small mb-0" style="line-height: 1.5;">
                Kelola tindak lanjut catatan evaluasi mutu berjenjang untuk <strong>Fakultas</strong> dan seluruh <strong>Program Studi</strong> di bawah <strong><?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?></strong>. Catatan evaluasi dari LPM ditelaah oleh GPM sebelum dirumuskan resmi ke unit sasaran.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-clipboard-check"></i> Ruang Review Dokumen
            </a>
            <a href="<?= base_url('gpm/dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Metric KPI Cards (4 Kolom: Catatan Baru LPM, Sedang Dikerjakan Unit, Siap Telaah Ulang, Total Siklus Revisi) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #EA580C !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-warning" style="color: #EA580C !important;">Catatan Baru LPM</span>
                    <div class="saas-stat-icon-wrap" style="background: #FFEDD5; color: #EA580C;">
                        <i class="fas fa-reply <?= $totalRevisiLpm > 0 ? 'fa-beat-fade' : '' ?>"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number" style="color: #EA580C;"><?= $totalRevisiLpm ?></span>
                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem; color: #C2410C !important;">Perlu Diteruskan</span>
                    </div>
                    <div class="saas-stat-help">Catatan evaluasi LPM yang perlu diteruskan GPM ke unit</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #DC2626 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-danger">Sedang Dikerjakan Unit</span>
                    <div class="saas-stat-icon-wrap" style="background: #FEE2E2; color: #DC2626;">
                        <i class="fas fa-wrench"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-danger"><?= $totalRevisiGpm ?></span>
                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">Dalam Pengerjaan</span>
                    </div>
                    <div class="saas-stat-help">Telah diteruskan ke unit &amp; menunggu upload revisi</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #2563EB !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-primary">Telah Diperbaiki Unit</span>
                    <div class="saas-stat-icon-wrap" style="background: #EFF6FF; color: #2563EB;">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-primary"><?= $totalSudah ?></span>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">Siap Telaah Ulang</span>
                    </div>
                    <div class="saas-stat-help">Telah diperbaiki unit &amp; siap diajukan kembali ke LPM</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card h-100" style="border-top: 4px solid #059669 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-success">Total Riwayat Revisi</span>
                    <div class="saas-stat-icon-wrap" style="background: #ECFDF5; color: #059669;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-success"><?= count($revisiDocs) ?></span>
                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5" style="font-size: 0.7rem;">Total Rekam</span>
                    </div>
                    <div class="saas-stat-help">Dari <?= count($prodisList) ?> prodi &amp; 1 tingkat dekanat</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Unit Kerja GPM -->
    <div class="card shadow-2xs rounded-4 p-3 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 flex-grow-1" style="max-width: 580px;">
                <div class="rounded-3 bg-light text-primary d-flex align-items-center justify-content-center flex-shrink-0 shadow-2xs" style="width: 42px; height: 42px; border: 1px solid #E2E8F0; font-size: 1.15rem;">
                    <i class="fas fa-building-columns"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label for="filterUnitKerjaPerbaikan" class="form-label mb-0 fw-bold text-dark small d-flex align-items-center gap-1.5">
                            <span>Filter Unit Kerja:</span>
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem;">GPM Scope</span>
                        </label>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #CBD5E1;"><i class="fas fa-filter text-scu-gold"></i></span>
                        <select id="filterUnitKerjaPerbaikan" class="form-select border-start-0 fw-semibold" style="border-color: #CBD5E1; font-size: 0.84rem;" onchange="window.location.href = this.value">
                            <option value="<?= base_url('gpm/perbaikan?unit=all') ?>" <?= $unitFilter === 'all' ? 'selected' : '' ?>>
                                Semua Unit Kerja (Fakultas & Seluruh Program Studi)
                            </option>
                            <option value="<?= base_url('gpm/perbaikan?unit=fakultas') ?>" <?= $unitFilter === 'fakultas' ? 'selected' : '' ?>>
                                Tingkat Fakultas (<?= htmlspecialchars($fakultas['nama_fakultas'] ?? 'Dekanat') ?>)
                            </option>
                            <optgroup label="── Program Studi di Bawah Fakultas ──">
                                <?php foreach ($prodisList as $p): ?>
                                    <option value="<?= base_url('gpm/perbaikan?unit=' . $p['id']) ?>" <?= ((string)$unitFilter === (string)$p['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['jenjang'] . ' ' . $p['nama_prodi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Current Scope Status indicator -->
            <div class="small text-muted d-flex align-items-center gap-2.5 ps-lg-3 border-start-lg">
                <span class="badge rounded-circle p-2 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fas fa-info fa-sm"></i>
                </span>
                <div style="line-height: 1.35;">
                    <div class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Cakupan Dokumen:</div>
                    <div class="fw-bold" style="font-size: 0.85rem;">
                        <?php if ($unitFilter === 'all'): ?>
                            <span class="text-dark">Semua Unit (Fakultas & Seluruh Prodi)</span>
                        <?php elseif ($unitFilter === 'fakultas'): ?>
                            <span class="text-primary"><i class="fas fa-building-columns me-1"></i>Tingkat Fakultas</span>
                        <?php else: ?>
                            <?php 
                            $curProdi = array_values(array_filter($prodisList, fn($x) => (string)$x['id'] === (string)$unitFilter));
                            $curProdiName = !empty($curProdi) ? ($curProdi[0]['jenjang'] . ' ' . $curProdi[0]['nama_prodi']) : 'Prodi';
                            ?>
                            <span class="text-success"><i class="fas fa-graduation-cap me-1"></i><?= htmlspecialchars($curProdiName) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Tabs, Jumlah Tampilan & Live Search Box -->
    <div class="card shadow-2xs rounded-4 p-3.5 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="d-flex flex-column gap-3">
            <!-- Baris 1: Status Tabs Filter Berjenjang -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-2.5 border-bottom" style="border-color: #F1F5F9 !important;">
                <div class="nav nav-pills d-flex flex-wrap align-items-center gap-2" id="perbaikanTabs" role="tablist">
                    <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 <?= $activeTab === 'tab-lpm' ? 'active' : '' ?>" id="tab-lpm-btn" data-bs-toggle="pill" data-bs-target="#tab-lpm" type="button" role="tab" style="font-size: 0.84rem;">
                        <i class="fas fa-reply text-warning"></i>
                        <span>Catatan dari LPM (Perlu Diteruskan)</span>
                        <span class="badge rounded-pill bg-warning text-dark px-2 py-0.5 fw-bold" style="font-size: 0.7rem;"><?= count($lpmRevisiDocs) ?></span>
                    </button>
                    <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 <?= $activeTab === 'tab-unit' ? 'active' : '' ?>" id="tab-unit-btn" data-bs-toggle="pill" data-bs-target="#tab-unit" type="button" role="tab" style="font-size: 0.84rem;">
                        <i class="fas fa-wrench text-danger"></i>
                        <span>Sedang Diperbaiki Unit</span>
                        <span class="badge rounded-pill bg-danger text-white px-2 py-0.5" style="font-size: 0.7rem;"><?= count($unitRevisiDocs) ?></span>
                    </button>
                    <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 <?= $activeTab === 'tab-sudah' ? 'active' : '' ?>" id="tab-sudah-btn" data-bs-toggle="pill" data-bs-target="#tab-sudah" type="button" role="tab" style="font-size: 0.84rem;">
                        <i class="fas fa-clock-rotate-left text-primary"></i>
                        <span>Hasil Perbaikan (Siap Telaah GPM)</span>
                        <span class="badge rounded-pill bg-primary text-white px-2 py-0.5" style="font-size: 0.7rem;"><?= count($sudahDocs) ?></span>
                    </button>
                    <button class="nav-link rounded-pill fw-semibold d-inline-flex align-items-center gap-2 px-3.5 py-2 <?= $activeTab === 'tab-all' ? 'active' : '' ?>" id="tab-all-btn" data-bs-toggle="pill" data-bs-target="#tab-all" type="button" role="tab" style="font-size: 0.84rem;">
                        <i class="fas fa-layer-group text-secondary"></i>
                        <span>Semua Status (<?= count($revisiDocs) ?>)</span>
                    </button>
                </div>
            </div>

            <!-- Controls Toolbar: Pencarian & Pengaturan Data -->
            <div class="row g-2.5 mb-1 align-items-center">
                <div class="col">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted px-3">
                            <i class="fas fa-magnifying-glass"></i>
                        </span>
                        <input type="text" id="liveSearchPerbaikan" class="form-control bg-light border-start-0 py-2 small ps-1" placeholder="Cari nama dokumen, nomor, atau bidang standar..." style="font-size: 0.82rem;">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <select id="filterTahunPerbaikanGpm" class="form-select form-select-sm shadow-none w-100" style="font-size: 0.82rem; border-color: #CBD5E1;" onchange="filterPerbaikanList()">
                        <option value="">Semua Tahun Akademik</option>
                        <?php foreach (get_distinct_tahun_akademik() as $ta): ?>
                            <option value="<?= htmlspecialchars($ta) ?>">Tahun Akademik <?= htmlspecialchars($ta) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto d-flex align-items-center gap-1.5">
                    <label for="perPagePerbaikan" class="small text-muted text-nowrap mb-0 d-none d-sm-inline" style="font-size: 0.78rem;">Tampilkan:</label>
                    <select id="perPagePerbaikan" class="form-select form-select-sm form-select-perpage shadow-none" style="min-width: 95px; width: auto; font-size: 0.8rem; border-color: #CBD5E1;">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50" selected>50 data</option>
                        <option value="100">100 data</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Contents Container -->
    <div class="tab-content" id="perbaikanTabsContent">
        
        <!-- ======================================================= -->
        <!-- TAB 1: CATATAN EVALUASI DARI LPM (PERLU DITERUSKAN)     -->
        <!-- ======================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'tab-lpm' ? 'show active' : '' ?>" id="tab-lpm" role="tabpanel">
            <?php if (empty($lpmRevisiDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Catatan Evaluasi dari LPM yang Perlu Diteruskan</h5>
                        <p class="text-muted small mx-auto mb-3" style="max-width: 480px; line-height: 1.5;">
                            Saat ini tidak ada dokumen yang dikembalikan oleh LPM untuk dirumuskan ke unit pengunggah.
                        </p>
                        <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 bg-scu-blue border-0 shadow-xs fw-semibold">
                            <i class="fas fa-folder-open me-1.5"></i> Buka Antrean Dokumen PPEPP
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #FFFBEB; border-color: #FDE68A !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #EA580C; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-reply"></i> <strong><?= count($lpmRevisiDocs) ?> Dokumen Memerlukan Tindakan GPM</strong>
                            </span>
                            <span class="text-dark small">Tinjau catatan evaluasi internal LPM berikut, lalu teruskan arahan resmi atas nama GPM ke Prodi/Dekanat.</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 44px;">No</th>
                                    <th style="width: 155px;">Unit Sasaran</th>
                                    <th style="min-width: 210px; width: 22%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 110px;">Siklus</th>
                                    <th style="min-width: 145px; width: 14%;">Bidang</th>
                                    <th style="min-width: 295px; width: 29%;">Catatan Internal LPM (Akses GPM)</th>
                                    <th class="text-center text-nowrap" style="width: 98px;">Lampiran</th>
                                    <th class="pe-4 text-center text-nowrap" style="width: 185px;">Aksi GPM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lpmRevisiDocs as $idx => $doc): 
                                    $filesCount = count($doc['files'] ?? []);
                                    $hasLink = !empty($doc['external_link']);
                                    $lpmNote = trim($doc['lpm_catatan'] ?: ($doc['catatan_review'] ?? ''));
                                    $lpmNoteClean = trim(preg_replace('/^\[CATATAN\s+INTERNAL\s+LPM[^\]]*\]\s*/i', '', $lpmNote));
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                    $lpmTs = !empty($doc['lpm_reviewed_at']) ? $doc['lpm_reviewed_at'] : ($doc['reviewed_at'] ?? null);
                                    $lpmDate = !empty($lpmTs) ? date('d M Y, H:i', strtotime($lpmTs)) . ' WIB' : '-';
                                    $lpmDateShort = !empty($lpmTs) ? date('d M, H:i', strtotime($lpmTs)) : '-';
                                    $hasFreshGpmRevisionNote = !empty($doc['gpm_catatan']) && !empty($doc['gpm_reviewed_at']) && !empty($doc['lpm_reviewed_at']) && strtotime($doc['gpm_reviewed_at']) > strtotime($doc['lpm_reviewed_at']);
                                ?>
                                    <tr class="perbaikan-row" data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . $unitLabel . ' ' . $lpmNote)) ?>">
                                        <td class="ps-4 text-center text-muted fw-bold small" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <!-- Unit Sasaran -->
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-2xs text-nowrap" 
                                                  style="font-size: 0.72rem; <?= ($doc['level'] === 'fakultas') ? 'background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' ?>">
                                                <i class="fas <?= ($doc['level'] === 'fakultas') ? 'fa-building-columns' : 'fa-graduation-cap' ?>"></i>
                                                <span><?= htmlspecialchars($unitLabel) ?></span>
                                            </span>
                                        </td>

                                        <!-- Nama Dokumen Mutu -->
                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="line-height: 1.4; font-size: 0.87rem;">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.73rem;">
                                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                            </div>
                                        </td>

                                        <!-- Siklus PPEPP -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

                                        <!-- Bidang Standar -->
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                                <?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?>
                                            </div>
                                            <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                                <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.71rem;">
                                                    <i class="fas fa-angle-right text-secondary opacity-75"></i>
                                                    <span><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Catatan LPM (Terbaca Langsung oleh GPM + Modal Detail) -->
                                        <td class="py-3 px-3 text-start" style="border-end: 1px solid #F1F5F9;">
                                            <div class="gpm-note-card">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1.5">
                                                    <span class="badge rounded-pill px-2 py-0.5 fw-bold d-inline-flex align-items-center gap-1" style="background: #FFEDD5; color: #9A3412; font-size: 0.66rem;">
                                                        <i class="fas fa-lock" style="font-size: 0.62rem;"></i> Internal LPM
                                                    </span>
                                                    <span class="text-muted text-nowrap" style="font-size: 0.66rem;">
                                                        <i class="far fa-clock me-0.5"></i><?= htmlspecialchars($lpmDateShort) ?>
                                                    </span>
                                                </div>
                                                <div class="gpm-note-card-inner mb-2">
                                                    <div class="gpm-note-clamp" title="<?= htmlspecialchars($lpmNoteClean ?: 'Tidak ada catatan tertulis.') ?>">
                                                        <?= htmlspecialchars($lpmNoteClean ?: 'Tidak ada catatan tertulis.') ?>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-xs gpm-note-btn-detail rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalCatatanEvaluator"
                                                        data-doc-id="<?= $doc['id'] ?>"
                                                        data-nama="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                        data-unit="<?= htmlspecialchars($unitLabel, ENT_QUOTES) ?>"
                                                        data-reviewer="Pusat Penjaminan Mutu LPM (Internal GPM)"
                                                        data-tanggal="<?= htmlspecialchars($lpmDate, ENT_QUOTES) ?>"
                                                        data-catatan="<?= htmlspecialchars($lpmNoteClean ?: 'Tidak ada catatan tertulis.', ENT_QUOTES) ?>"
                                                        data-gpm-note="<?= $hasFreshGpmRevisionNote ? htmlspecialchars($doc['gpm_catatan'], ENT_QUOTES) : '' ?>"
                                                        data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '', ENT_QUOTES) ?>"
                                                        data-status="revisi_lpm"
                                                        data-is-gpm="0"
                                                        data-edit-url="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>">
                                                    <i class="fas fa-book-open"></i>
                                                    <span>Baca Detail Lengkap</span>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Lampiran -->
                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($filesCount > 0): ?>
                                                <a href="<?= base_url($doc['files'][0]['file_path']) ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs text-nowrap" style="font-size: 0.72rem;">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    <span><?= $filesCount ?> Berkas</span>
                                                </a>
                                            <?php elseif ($hasLink): ?>
                                                <a href="<?= htmlspecialchars(explode("\n", $doc['external_link'])[0]) ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs text-primary text-nowrap" style="font-size: 0.72rem;">
                                                    <i class="fab fa-google-drive text-success"></i>
                                                    <span>GDrive</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="pe-4 py-3 text-center">
                                            <div class="d-inline-flex align-items-center gap-1.5 text-nowrap">
                                                <button type="button" class="btn btn-sm gpm-btn-forward rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 text-nowrap btn-open-forward"
                                                        data-doc-id="<?= $doc['id'] ?>"
                                                        data-doc-title="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                        data-unit-name="<?= htmlspecialchars($unitLabel, ENT_QUOTES) ?>"
                                                        data-lpm-note="<?= htmlspecialchars($lpmNoteClean ?: $lpmNote, ENT_QUOTES) ?>"
                                                        data-gpm-note="<?= $hasFreshGpmRevisionNote ? htmlspecialchars($doc['gpm_catatan'], ENT_QUOTES) : '' ?>">
                                                    <i class="fas fa-paper-plane"></i>
                                                    <span>Teruskan ke Unit</span>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-circle p-0 btn-review-history d-inline-flex align-items-center justify-content-center shadow-2xs" data-doc-id="<?= $doc['id'] ?>" title="Riwayat Review" style="width: 32px; height: 32px; font-size: 0.76rem;">
                                                    <i class="fas fa-history text-secondary"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab LPM -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabLpm" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabLpm">Menghitung...</div>
                        <div class="table-pagination-control">
                            <div class="table-perpage-wrap">
                                <label class="table-perpage-label">Ukuran Halaman:</label>
                                <select class="form-select table-perpage-select shadow-none" onchange="changePerbaikanPageSize(this.value)">
                                    <option value="10">10 data</option>
                                    <option value="25">25 data</option>
                                    <option value="50" selected>50 data</option>
                                    <option value="100">100 data</option>
                                </select>
                            </div>
                            <nav id="pagTabLpm" aria-label="Navigasi Halaman"></nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 2: SEDANG DIPERBAIKI OLEH UNIT (REVISI GPM)         -->
        <!-- ======================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'tab-unit' ? 'show active' : '' ?>" id="tab-unit" role="tabpanel">
            <?php if (empty($unitRevisiDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Sedang Dikerjakan Unit</h5>
                        <p class="text-muted small mx-auto mb-0" style="max-width: 480px; line-height: 1.5;">
                            Saat ini seluruh dokumen yang diteruskan telah diselesaikan atau belum ada arahan perbaikan aktif ke unit.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #FEF2F2; border-color: #FECACA !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #DC2626; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-wrench"></i> <strong><?= count($unitRevisiDocs) ?> Dokumen Dalam Pengerjaan Unit</strong>
                            </span>
                            <span class="text-muted small">Unit telah menerima arahan perbaikan GPM dan sedang menyusun berkas revisi baru</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 44px;">No</th>
                                    <th style="width: 155px;">Unit Sasaran</th>
                                    <th style="min-width: 210px; width: 22%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 110px;">Siklus</th>
                                    <th style="min-width: 145px; width: 14%;">Bidang</th>
                                    <th style="min-width: 270px; width: 27%;">Arahan GPM ke Unit</th>
                                    <th class="text-center text-nowrap" style="width: 120px;">Status Pengerjaan</th>
                                    <th class="pe-4 text-center text-nowrap" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unitRevisiDocs as $idx => $doc): 
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                    $gpmTs = !empty($doc['gpm_reviewed_at']) ? $doc['gpm_reviewed_at'] : ($doc['reviewed_at'] ?? null);
                                    $gpmDate = !empty($gpmTs) ? date('d M Y, H:i', strtotime($gpmTs)) . ' WIB' : '-';
                                    $gpmDateShort = !empty($gpmTs) ? date('d M, H:i', strtotime($gpmTs)) : '-';
                                    $gpmNote = trim($doc['gpm_catatan'] ?: ($doc['catatan_review'] ?? 'Silakan sesuaikan isi dan lampiran dokumen mutu sesuai standar.'));
                                    $unitLpmNoteRaw = trim($doc['lpm_catatan'] ?? '');
                                    $unitLpmNoteClean = trim(preg_replace('/^\[CATATAN\s+INTERNAL\s+LPM[^\]]*\]\s*/i', '', $unitLpmNoteRaw));
                                    $unitLpmTs = !empty($doc['lpm_reviewed_at']) ? date('d M Y, H:i', strtotime($doc['lpm_reviewed_at'])) . ' WIB' : '-';
                                ?>
                                    <tr class="perbaikan-row" data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . $unitLabel)) ?>">
                                        <td class="ps-4 text-center text-muted fw-bold small" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-2xs text-nowrap" 
                                                  style="font-size: 0.72rem; <?= ($doc['level'] === 'fakultas') ? 'background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' ?>">
                                                <i class="fas <?= ($doc['level'] === 'fakultas') ? 'fa-building-columns' : 'fa-graduation-cap' ?>"></i>
                                                <span><?= htmlspecialchars($unitLabel) ?></span>
                                            </span>
                                        </td>

                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="line-height: 1.4; font-size: 0.87rem;">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.73rem;">
                                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                            </div>
                                        </td>

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                                <?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?>
                                            </div>
                                            <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                                <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.71rem;">
                                                    <i class="fas fa-angle-right text-secondary opacity-75"></i>
                                                    <span><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td class="py-3 px-3 text-start" style="border-end: 1px solid #F1F5F9;">
                                            <div class="gpm-note-card is-gpm">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1.5">
                                                    <span class="badge rounded-pill px-2 py-0.5 fw-bold d-inline-flex align-items-center gap-1" style="background: #EDE9FE; color: #5B21B6; font-size: 0.66rem;">
                                                        <i class="fas fa-shield-halved" style="font-size: 0.62rem;"></i> Arahan GPM
                                                    </span>
                                                    <span class="text-muted text-nowrap" style="font-size: 0.66rem;">
                                                        <i class="far fa-clock me-0.5"></i><?= htmlspecialchars($gpmDateShort) ?>
                                                    </span>
                                                </div>
                                                <div class="gpm-note-card-inner mb-2">
                                                    <div class="gpm-note-clamp" title="<?= htmlspecialchars($gpmNote) ?>">
                                                        <?= htmlspecialchars($gpmNote) ?>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-wrap align-items-center gap-1.5">
                                                    <button type="button" class="btn btn-xs gpm-note-btn-detail rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalCatatanEvaluator"
                                                            data-nama="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                            data-unit="<?= htmlspecialchars($unitLabel, ENT_QUOTES) ?>"
                                                            data-reviewer="Gugus Penjaminan Mutu (GPM) Fakultas"
                                                            data-tanggal="<?= htmlspecialchars($gpmDate, ENT_QUOTES) ?>"
                                                            data-catatan="<?= htmlspecialchars($gpmNote, ENT_QUOTES) ?>"
                                                            data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '', ENT_QUOTES) ?>"
                                                            data-status="revisi_gpm"
                                                            data-is-gpm="1"
                                                            data-edit-url="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>">
                                                        <i class="fas fa-book-open"></i>
                                                        <span>Arahan GPM</span>
                                                    </button>
                                                    <?php if (!empty($unitLpmNoteClean)): ?>
                                                        <button type="button" class="btn btn-xs btn-outline-warning text-dark bg-white rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 fw-semibold"
                                                                style="font-size: 0.69rem;"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalCatatanEvaluator"
                                                                data-nama="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                                data-unit="<?= htmlspecialchars($unitLabel, ENT_QUOTES) ?>"
                                                                data-reviewer="Pusat Penjaminan Mutu LPM (Internal GPM)"
                                                                data-tanggal="<?= htmlspecialchars($unitLpmTs, ENT_QUOTES) ?>"
                                                                data-catatan="<?= htmlspecialchars($unitLpmNoteClean, ENT_QUOTES) ?>"
                                                                data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '', ENT_QUOTES) ?>"
                                                                data-status="revisi_lpm"
                                                                data-is-gpm="0"
                                                                data-edit-url="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>">
                                                            <i class="fas fa-lock text-warning"></i> LPM
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill text-dark border px-2.5 py-1 text-nowrap" style="background: #FEE2E2; color: #991B1B; border-color: #FCA5A5 !important; font-size: 0.72rem;">
                                                <i class="fas fa-clock text-danger me-1"></i> Menunggu Unit
                                            </span>
                                        </td>

                                        <td class="pe-4 py-3 text-center">
                                            <div class="d-inline-flex align-items-center gap-1.5 text-nowrap">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3 py-1 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                                    <i class="fas fa-clipboard-check"></i> Review
                                                </a>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-circle p-0 btn-review-history d-inline-flex align-items-center justify-content-center shadow-2xs" data-doc-id="<?= $doc['id'] ?>" title="Riwayat Review" style="width: 32px; height: 32px; font-size: 0.76rem;">
                                                    <i class="fas fa-history text-secondary"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab Unit -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabUnit" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabUnit">Menghitung...</div>
                        <div class="table-pagination-control">
                            <div class="table-perpage-wrap">
                                <label class="table-perpage-label">Ukuran Halaman:</label>
                                <select class="form-select table-perpage-select shadow-none" onchange="changePerbaikanPageSize(this.value)">
                                    <option value="10">10 data</option>
                                    <option value="25">25 data</option>
                                    <option value="50" selected>50 data</option>
                                    <option value="100">100 data</option>
                                </select>
                            </div>
                            <nav id="pagTabUnit" aria-label="Navigasi Halaman"></nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 3: HASIL PERBAIKAN UNIT (SIAP TELAAH ULANG GPM)     -->
        <!-- ======================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'tab-sudah' ? 'show active' : '' ?>" id="tab-sudah" role="tabpanel">
            <?php if (empty($sudahDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                            <i class="fas fa-inbox text-secondary"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Menunggu Telaah Ulang</h5>
                        <p class="text-muted small mx-auto mb-0" style="max-width: 480px; line-height: 1.5;">
                            Dokumen yang telah diperbaiki oleh unit kerja akan muncul di sini untuk diperiksa ulang GPM sebelum diteruskan ke LPM.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #EFF6FF; border-color: #BFDBFE !important;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #2563EB; color: #FFFFFF; font-size: 0.8rem;">
                                <i class="fas fa-clock-rotate-left"></i> <strong><?= count($sudahDocs) ?> Dokumen Siap Telaah Ulang GPM</strong>
                            </span>
                            <span class="text-dark small">Periksa hasil perbaikan unit sasaran. Jika telah memenuhi standar, teruskan kembali ke LPM.</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 44px;">No</th>
                                    <th style="width: 155px;">Unit Sasaran</th>
                                    <th style="min-width: 210px; width: 22%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 110px;">Siklus</th>
                                    <th style="min-width: 145px; width: 14%;">Bidang</th>
                                    <th style="min-width: 270px; width: 27%;">Catatan Arahan Sebelumnya</th>
                                    <th class="text-center text-nowrap" style="width: 105px;">Lampiran Baru</th>
                                    <th class="pe-4 text-center text-nowrap" style="width: 165px;">Aksi Telaah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sudahDocs as $idx => $doc): 
                                    $filesCount = count($doc['files'] ?? []);
                                    $hasLink = !empty($doc['external_link']);
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                    $sTs = !empty($doc['gpm_reviewed_at']) ? $doc['gpm_reviewed_at'] : ($doc['reviewed_at'] ?? null);
                                    $sRevDate = !empty($sTs) ? date('d M Y, H:i', strtotime($sTs)) . ' WIB' : '-';
                                    $sRevDateShort = !empty($sTs) ? date('d M Y, H:i', strtotime($sTs)) : '-';
                                    $sNote = trim($doc['gpm_catatan'] ?: ($doc['catatan_review'] ?? 'Silakan sesuaikan dokumen mutu sesuai standar penjaminan mutu.'));
                                    $sLpmRaw = trim($doc['lpm_catatan'] ?? '');
                                    $sLpmClean = trim(preg_replace('/^\[CATATAN\s+INTERNAL\s+LPM[^\]]*\]\s*/i', '', $sLpmRaw));
                                    $sLpmTs = !empty($doc['lpm_reviewed_at']) ? date('d M Y, H:i', strtotime($doc['lpm_reviewed_at'])) . ' WIB' : '-';
                                ?>
                                    <tr class="perbaikan-row" data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . $unitLabel)) ?>">
                                        <td class="ps-4 text-center text-muted fw-bold small" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-2xs text-nowrap" 
                                                  style="font-size: 0.72rem; <?= ($doc['level'] === 'fakultas') ? 'background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' ?>">
                                                <i class="fas <?= ($doc['level'] === 'fakultas') ? 'fa-building-columns' : 'fa-graduation-cap' ?>"></i>
                                                <span><?= htmlspecialchars($unitLabel) ?></span>
                                            </span>
                                        </td>

                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="line-height: 1.4; font-size: 0.87rem;">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.73rem;">
                                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                            </div>
                                        </td>

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                                <?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?>
                                            </div>
                                            <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                                <div class="text-muted small mt-1 d-flex align-items-center gap-1" style="font-size: 0.71rem;">
                                                    <i class="fas fa-angle-right text-secondary opacity-75"></i>
                                                    <span><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td class="py-3 px-3 text-start" style="border-end: 1px solid #F1F5F9;">
                                            <div class="gpm-note-card is-gpm">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1.5">
                                                    <span class="badge rounded-pill px-2 py-0.5 fw-bold d-inline-flex align-items-center gap-1" style="background: #EDE9FE; color: #5B21B6; font-size: 0.66rem;">
                                                        <i class="fas fa-shield-halved" style="font-size: 0.62rem;"></i> Arahan GPM
                                                    </span>
                                                    <span class="text-muted text-nowrap" style="font-size: 0.66rem;">
                                                        <i class="far fa-clock me-0.5"></i><?= htmlspecialchars($sRevDateShort) ?>
                                                    </span>
                                                </div>
                                                <div class="gpm-note-card-inner mb-2">
                                                    <div class="gpm-note-clamp" title="<?= htmlspecialchars($sNote) ?>">
                                                        <?= htmlspecialchars($sNote) ?>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-wrap align-items-center gap-1.5">
                                                    <button type="button" class="btn btn-xs gpm-note-btn-detail rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalCatatanEvaluator"
                                                            data-nama="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                            data-unit="<?= htmlspecialchars($unitLabel, ENT_QUOTES) ?>"
                                                            data-reviewer="Gugus Penjaminan Mutu (GPM) Fakultas"
                                                            data-tanggal="<?= htmlspecialchars($sRevDate, ENT_QUOTES) ?>"
                                                            data-catatan="<?= htmlspecialchars($sNote, ENT_QUOTES) ?>"
                                                            data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '', ENT_QUOTES) ?>"
                                                            data-status="sudah_diperbaiki"
                                                            data-is-gpm="1"
                                                            data-edit-url="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>">
                                                        <i class="fas fa-book-open"></i>
                                                        <span>Arahan GPM</span>
                                                    </button>
                                                    <?php if (!empty($sLpmClean)): ?>
                                                        <button type="button" class="btn btn-xs btn-outline-warning text-dark bg-white rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 fw-semibold"
                                                                style="font-size: 0.69rem;"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalCatatanEvaluator"
                                                                data-nama="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                                data-unit="<?= htmlspecialchars($unitLabel, ENT_QUOTES) ?>"
                                                                data-reviewer="Pusat Penjaminan Mutu LPM (Internal GPM)"
                                                                data-tanggal="<?= htmlspecialchars($sLpmTs, ENT_QUOTES) ?>"
                                                                data-catatan="<?= htmlspecialchars($sLpmClean, ENT_QUOTES) ?>"
                                                                data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '', ENT_QUOTES) ?>"
                                                                data-status="revisi_lpm"
                                                                data-is-gpm="0"
                                                                data-edit-url="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>">
                                                            <i class="fas fa-lock text-warning"></i> LPM
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?php if ($filesCount > 0): ?>
                                                <a href="<?= base_url($doc['files'][0]['file_path']) ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs text-nowrap" style="font-size: 0.72rem;">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    <span><?= $filesCount ?> Berkas</span>
                                                </a>
                                            <?php elseif ($hasLink): ?>
                                                <a href="<?= htmlspecialchars(explode("\n", $doc['external_link'])[0]) ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1 shadow-2xs text-primary text-nowrap" style="font-size: 0.72rem;">
                                                    <i class="fab fa-google-drive text-success"></i>
                                                    <span>GDrive</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="pe-4 py-3 text-center">
                                            <div class="d-inline-flex align-items-center gap-1.5 text-nowrap">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3 py-1 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1 text-white" style="font-size: 0.78rem;">
                                                    <i class="fas fa-clipboard-check"></i> Telaah &amp; Putuskan
                                                </a>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-circle p-0 btn-review-history d-inline-flex align-items-center justify-content-center shadow-2xs" data-doc-id="<?= $doc['id'] ?>" title="Riwayat Review" style="width: 32px; height: 32px; font-size: 0.76rem;">
                                                    <i class="fas fa-history text-secondary"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab Sudah -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabSudah" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabSudah">Menghitung...</div>
                        <div class="table-pagination-control">
                            <div class="table-perpage-wrap">
                                <label class="table-perpage-label">Ukuran Halaman:</label>
                                <select class="form-select table-perpage-select shadow-none" onchange="changePerbaikanPageSize(this.value)">
                                    <option value="10">10 data</option>
                                    <option value="25">25 data</option>
                                    <option value="50" selected>50 data</option>
                                    <option value="100">100 data</option>
                                </select>
                            </div>
                            <nav id="pagTabSudah" aria-label="Navigasi Halaman"></nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 4: SEMUA STATUS REVISI                             -->
        <!-- ======================================================= -->
        <div class="tab-pane fade <?= $activeTab === 'tab-all' ? 'show active' : '' ?>" id="tab-all" role="tabpanel">
            <?php if (empty($revisiDocs)): ?>
                <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
                    <div class="py-4">
                        <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark mb-1">Belum Ada Rekam Revisi</h5>
                        <p class="text-muted small mb-0">Belum ada riwayat dokumen yang perlu atau telah diperbaiki pada filter ini.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="modern-table-card mb-4">
                    <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #002B49; color: #FFFFFF;">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs bg-white text-dark-blue" style="font-size: 0.8rem;">
                                <i class="fas fa-layer-group"></i> <strong>Total: <?= count($revisiDocs) ?> Dokumen</strong>
                            </span>
                            <span class="small text-white text-opacity-75">Seluruh dokumen yang berada dalam siklus tindak lanjut revisi</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern-perbaikan">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-center" style="width: 44px;">No</th>
                                    <th style="width: 155px;">Unit Sasaran</th>
                                    <th style="min-width: 210px; width: 23%;">Nama Dokumen Mutu</th>
                                    <th class="text-center" style="width: 155px;">Status Alur Revisi</th>
                                    <th class="text-center" style="width: 110px;">Siklus</th>
                                    <th style="min-width: 270px; width: 26%;">Catatan LPM / GPM</th>
                                    <th class="pe-4 text-center text-nowrap" style="width: 145px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revisiDocs as $idx => $doc): 
                                    $unitLabel = ($doc['level'] === 'fakultas') ? 'Tingkat Fakultas' : (($doc['jenjang'] ?? 'S1') . ' ' . ($doc['nama_prodi'] ?? ''));
                                    $allLpmNoteRaw = trim($doc['lpm_catatan'] ?? '');
                                    $allLpmNote = trim(preg_replace('/^\[CATATAN\s+INTERNAL\s+LPM[^\]]*\]\s*/i', '', $allLpmNoteRaw));
                                    $allGpmNote = trim($doc['gpm_catatan'] ?? '');
                                    $isLpmStage = in_array($doc['status_review'] ?? '', ['revisi_lpm', 'perlu_perbaikan']);
                                    $allNote = $isLpmStage ? ($allLpmNote ?: ($doc['catatan_review'] ?? '')) : ($allGpmNote ?: ($allLpmNote ?: ($doc['catatan_review'] ?? '')));
                                    $allReviewer = $isLpmStage ? 'Pusat Penjaminan Mutu LPM (Internal GPM)' : 'Gugus Penjaminan Mutu (GPM) Fakultas';
                                    $allTs = $isLpmStage ? ($doc['lpm_reviewed_at'] ?? null) : ($doc['gpm_reviewed_at'] ?? null);
                                    $allDate = !empty($allTs) ? date('d M Y, H:i', strtotime($allTs)) . ' WIB' : '-';
                                    $allDateShort = !empty($allTs) ? date('d M, H:i', strtotime($allTs)) : '-';
                                ?>
                                    <tr class="perbaikan-row" data-tahun="<?= htmlspecialchars($doc['tahun_akademik'] ?? '') ?>" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? '') . ' ' . ($doc['nama_sub_bidang'] ?? '') . ' ' . $unitLabel)) ?>">
                                        <td class="ps-4 text-center text-muted fw-bold small" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                        
                                        <td class="py-3 px-3" style="border-end: 1px solid #F1F5F9;">
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-2xs text-nowrap" 
                                                  style="font-size: 0.72rem; <?= ($doc['level'] === 'fakultas') ? 'background: #FAF5FF; color: #7C3AED; border: 1px solid #E9D5FF;' : 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;' ?>">
                                                <i class="fas <?= ($doc['level'] === 'fakultas') ? 'fa-building-columns' : 'fa-graduation-cap' ?>"></i>
                                                <span><?= htmlspecialchars($unitLabel) ?></span>
                                            </span>
                                        </td>

                                        <td class="py-3 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                            <div class="fw-bold text-dark-blue mb-1" style="line-height: 1.4; font-size: 0.87rem;">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                                    <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.73rem;">
                                                <?php if (!empty($doc['nomor_dokumen'])): ?>
                                                    <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                                    <span>&bull;</span>
                                                <?php endif; ?>
                                                <span>TA: <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?></span>
                                            </div>
                                        </td>

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= render_review_status_badge($doc['status_review'], '', 'gpm') ?>
                                        </td>

                                        <td class="py-3 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                            <?= siklus_badge($doc['siklus']) ?>
                                        </td>

                                        <td class="py-3 px-3 text-start" style="border-end: 1px solid #F1F5F9;">
                                            <?php if (!empty($allNote)): ?>
                                                <div class="gpm-note-card <?= $isLpmStage ? '' : 'is-gpm' ?>">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1.5">
                                                        <span class="badge rounded-pill px-2 py-0.5 fw-bold d-inline-flex align-items-center gap-1" style="background: <?= $isLpmStage ? '#FFEDD5; color: #9A3412;' : '#EDE9FE; color: #5B21B6;' ?> font-size: 0.66rem;">
                                                            <i class="fas <?= $isLpmStage ? 'fa-lock' : 'fa-shield-halved' ?>" style="font-size: 0.62rem;"></i> <?= $isLpmStage ? 'Internal LPM' : 'Arahan GPM' ?>
                                                        </span>
                                                        <span class="text-muted text-nowrap" style="font-size: 0.66rem;">
                                                            <i class="far fa-clock me-0.5"></i><?= htmlspecialchars($allDateShort) ?>
                                                        </span>
                                                    </div>
                                                    <div class="gpm-note-card-inner mb-2">
                                                        <div class="gpm-note-clamp" title="<?= htmlspecialchars($allNote) ?>">
                                                            <?= htmlspecialchars($allNote) ?>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-xs gpm-note-btn-detail rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalCatatanEvaluator"
                                                            data-doc-id="<?= $doc['id'] ?>"
                                                            data-nama="<?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES) ?>"
                                                            data-unit="<?= htmlspecialchars($unitLabel, ENT_QUOTES) ?>"
                                                            data-reviewer="<?= htmlspecialchars($allReviewer, ENT_QUOTES) ?>"
                                                            data-tanggal="<?= htmlspecialchars($allDate, ENT_QUOTES) ?>"
                                                            data-catatan="<?= htmlspecialchars($allNote, ENT_QUOTES) ?>"
                                                            data-siklus="<?= htmlspecialchars($doc['siklus'] ?? '', ENT_QUOTES) ?>"
                                                            data-status="<?= htmlspecialchars($doc['status_review'] ?? '', ENT_QUOTES) ?>"
                                                            data-is-gpm="<?= $isLpmStage ? '0' : '1' ?>"
                                                            data-edit-url="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>">
                                                        <i class="fas fa-book-open"></i>
                                                        <span>Baca Detail Lengkap</span>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="pe-4 py-3 text-center">
                                            <div class="d-inline-flex align-items-center gap-1.5">
                                                <a href="<?= base_url('gpm/dokumen?search=' . urlencode($doc['nama_dokumen'])) ?>" class="btn btn-sm btn-primary bg-scu-blue border-0 rounded-pill px-3 py-1 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1 text-white" style="font-size: 0.78rem;">
                                                    <i class="fas fa-clipboard-check"></i> Review
                                                </a>
                                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-review-history d-inline-flex align-items-center gap-1 shadow-2xs" data-doc-id="<?= $doc['id'] ?>" title="Riwayat Review" style="font-size: 0.72rem;">
                                                    <i class="fas fa-history text-secondary"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination Tab All -->
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" id="pagWrapTabAll" style="border-color: #E2E8F0 !important;">
                        <div class="small text-muted" id="infoTabAll">Menghitung...</div>
                        <div class="table-pagination-control">
                            <div class="table-perpage-wrap">
                                <label class="table-perpage-label">Ukuran Halaman:</label>
                                <select class="form-select table-perpage-select shadow-none" onchange="changePerbaikanPageSize(this.value)">
                                    <option value="10">10 data</option>
                                    <option value="25">25 data</option>
                                    <option value="50" selected>50 data</option>
                                    <option value="100">100 data</option>
                                </select>
                            </div>
                            <nav id="pagTabAll" aria-label="Navigasi Halaman"></nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 1: FORWARD ARAHAN REVISI GPM KE UNIT (MASKING LPM)-->
<!-- ======================================================= -->
<div class="modal fade" id="modalForwardLpmRevision" tabindex="-1" aria-labelledby="forwardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3.5 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.05rem; background: linear-gradient(135deg, #EA580C 0%, #D97706 100%);">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0.5" id="forwardModalLabel" style="font-size: 1.08rem; letter-spacing: -0.2px;">
                            Teruskan Arahan Revisi ke Unit Sasaran
                        </h5>
                        <p class="text-white text-opacity-75 small mb-0" style="font-size: 0.77rem;">
                            GPM <?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?> &bull; Privasi Catatan LPM Terlindungi
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="<?= base_url('gpm/review/forward-lpm') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="document_id" id="forwardModalDocId" value="">
                <input type="hidden" name="redirect_to" value="gpm/perbaikan">

                <div class="modal-body p-4" style="background: #F8FAFC;">
                    <!-- Card 1: Dokumen Sasaran -->
                    <div class="card border shadow-2xs rounded-4 p-3.5 mb-3 bg-white" style="border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                <i class="fas fa-file-lines"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.06em;">Dokumen Sasaran</span>
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" id="forwardModalUnitBadge" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; font-size: 0.72rem;">-</span>
                                </div>
                                <h6 class="fw-bold text-dark-blue mb-0" id="forwardModalDocTitle" style="font-size: 0.96rem; line-height: 1.45;">-</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Langkah 1 - Catatan Internal LPM -->
                    <div class="card border shadow-2xs rounded-4 p-3.5 mb-3" style="background: #FFFBEB; border-color: #FCD34D !important; border-left: 4px solid #EA580C !important;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2.5">
                            <div class="d-flex align-items-center gap-2">
                                <span class="gpm-step-badge text-white" style="background: #EA580C;">Langkah 1 &bull; Internal GPM</span>
                                <span class="fw-bold text-warning-emphasis small" style="font-size: 0.82rem;">Catatan Asli dari Tim LPM</span>
                            </div>
                            <button type="button" id="btnCopyLpmToGpm" class="btn btn-sm bg-white border rounded-pill px-3 py-1 fw-bold shadow-2xs d-inline-flex align-items-center gap-1.5 text-dark" style="border-color: #F59E0B !important; font-size: 0.75rem;" onclick="copyLpmToGpm()">
                                <i class="fas fa-copy text-warning"></i> <span>Salin ke Arahan GPM</span>
                            </button>
                        </div>
                        <div class="p-3 rounded-3 bg-white border text-dark mb-2" id="forwardModalLpmNote" style="border-color: #FDE68A !important; line-height: 1.65; max-height: 165px; overflow-y: auto; white-space: pre-wrap; font-size: 0.85rem;">-</div>
                        <div class="d-flex align-items-center gap-1.5 text-warning-emphasis small" style="font-size: 0.72rem;">
                            <i class="fas fa-eye-slash"></i>
                            <span>Catatan internal LPM ini <strong>dirahasiakan</strong> dan tidak akan tampil kepada Prodi / Dekanat.</span>
                        </div>
                    </div>

                    <!-- Card 3: Langkah 2 - Rumuskan Arahan Resmi GPM -->
                    <div class="card border shadow-2xs rounded-4 p-3.5 bg-white" style="border-color: #CBD5E1 !important;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="gpm-step-badge text-white" style="background: #0A192F;">Langkah 2 &bull; Kirim ke Unit</span>
                                <label for="forwardGpmNoteInput" class="fw-bold text-dark-blue small mb-0" style="font-size: 0.83rem;">
                                    Arahan Perbaikan Resmi GPM <span class="text-danger">*</span>
                                </label>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background: #F3E8FF; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.7rem;">
                                <i class="fas fa-shield-halved me-1"></i> Tampil atas nama GPM
                            </span>
                        </div>
                        <textarea name="catatan_gpm" id="forwardGpmNoteInput" rows="4" class="form-control rounded-3 p-3 text-dark" style="font-size: 0.86rem; line-height: 1.6; border-color: #CBD5E1;" placeholder="Rumuskan atau klik tombol 'Salin ke Arahan GPM' di atas untuk meneruskan arahan perbaikan atas nama GPM Fakultas..." required></textarea>
                        <div class="d-flex align-items-center gap-1.5 text-muted small mt-2" style="font-size: 0.73rem;">
                            <i class="fas fa-circle-check text-success"></i>
                            <span>Unit sasaran menerima arahan ini murni atas nama <strong>GPM <?= htmlspecialchars($fakultas['nama_fakultas'] ?? 'Fakultas') ?></strong>.</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4 bg-white d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light border rounded-pill px-4 py-2 fw-semibold small" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary bg-scu-blue border-0 rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2 small">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Arahan Revisi ke Unit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL 2: DETAIL CATATAN EVALUATOR (VIEW & QUICK ACTION) -->
<!-- ======================================================= -->
<div class="modal fade" id="modalCatatanEvaluator" tabindex="-1" aria-labelledby="evalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3.5 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" id="evalModalIcon" style="width: 42px; height: 42px; font-size: 1.05rem; background: #EA580C;">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0.5" id="evalModalLabel" style="font-size: 1.08rem; letter-spacing: -0.2px;">
                            Catatan Evaluasi / Arahan Perbaikan
                        </h5>
                        <p class="text-white text-opacity-75 small mb-0" id="evalModalSubLabel" style="font-size: 0.77rem;">SPMI PPEPP UNIKA Soegijapranata</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #F8FAFC;">
                <!-- Card Identitas Dokumen & Evaluator -->
                <div class="card border shadow-2xs rounded-4 p-3.5 mb-3 bg-white" style="border-color: #E2E8F0 !important;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1.5">
                        <span class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.06em;">Dokumen yang Dievaluasi</span>
                        <span class="badge rounded-pill px-2.5 py-0.5 fw-bold" id="evalModalUnitBadge" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; font-size: 0.72rem;">-</span>
                    </div>
                    <h6 class="fw-bold text-dark-blue mb-2.5" id="evalModalDocTitle" style="font-size: 0.98rem; line-height: 1.45;">-</h6>
                    <div class="d-flex flex-wrap align-items-center gap-2 pt-2.5 border-top" style="border-color: #F1F5F9 !important;">
                        <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.74rem;">
                            <i class="fas fa-shield-halved text-primary" id="evalModalReviewerIcon"></i>
                            <span id="evalModalReviewer">-</span>
                        </span>
                        <span class="badge rounded-pill bg-light text-muted border px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.74rem;">
                            <i class="far fa-calendar-check text-secondary"></i>
                            <span><strong class="text-dark" id="evalModalTanggal">-</strong></span>
                        </span>
                    </div>
                </div>

                <!-- Card Isi Catatan -->
                <div class="card border shadow-2xs rounded-4 p-3.5" id="evalModalNoteCard" style="background: #FFFBEB; border-color: #FCD34D !important; border-left: 4px solid #EA580C !important;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <span class="fw-bold small d-inline-flex align-items-center gap-1.5" id="evalModalNoteHeader" style="color: #9A3412; font-size: 0.82rem;">
                            <i class="fas fa-file-lines"></i>
                            <span>Uraian Catatan Evaluasi &amp; Poin Perbaikan</span>
                        </span>
                        <span class="badge rounded-pill px-2.5 py-0.5 fw-bold" id="evalModalScopePill" style="background: #FFEDD5; color: #9A3412; font-size: 0.68rem;">
                            <i class="fas fa-lock me-1"></i> Akses Internal GPM
                        </span>
                    </div>
                    <div class="p-3.5 rounded-3 bg-white border text-dark" id="evalModalCatatan" style="border-color: #FDE68A !important; font-size: 0.88rem; line-height: 1.7; max-height: 280px; overflow-y: auto; white-space: pre-wrap;">-</div>
                    <div class="d-flex align-items-center gap-1.5 mt-2 small text-warning-emphasis" id="evalModalPrivacyNote" style="font-size: 0.73rem;">
                        <i class="fas fa-eye-slash"></i>
                        <span>Catatan ini khusus untuk ditelaah oleh GPM sebelum diteruskan resmi ke unit pengunggah.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-3 px-4 bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                <button type="button" class="btn btn-light border rounded-pill px-3.5 py-2 fw-semibold small" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="#" id="evalModalEditBtn" class="btn btn-outline-primary rounded-pill px-3.5 py-2 fw-semibold small d-inline-flex align-items-center gap-1.5">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Buka di Ruang Review</span>
                    </a>
                    <button type="button" id="evalModalForwardBtn" class="btn gpm-btn-forward rounded-pill px-4 py-2 d-none align-items-center gap-1.5">
                        <i class="fas fa-paper-plane"></i>
                        <span>Teruskan ke Unit Sasaran</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPerbaikanPages = {
    'tab-lpm': 1,
    'tab-unit': 1,
    'tab-sudah': 1,
    'tab-all': 1
};

function paginateTab(tabId, page) {
    const tabEl = document.getElementById(tabId);
    if (!tabEl) return;

    if (page !== undefined) {
        currentPerbaikanPages[tabId] = page;
    }

    const perPageSelect = document.getElementById('perPagePerbaikan');
    const perPageVal = perPageSelect ? perPageSelect.value : '50';
    const perPage = (perPageVal === 'all') ? 999999 : parseInt(perPageVal, 10);

    const searchInput = document.getElementById('liveSearchPerbaikan');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

    const allRows = Array.from(tabEl.querySelectorAll('tbody tr.perbaikan-row'));
    if (allRows.length === 0) return;

    const filterTahun = document.getElementById('filterTahunPerbaikanGpm') ? document.getElementById('filterTahunPerbaikanGpm').value.trim() : '';

    // Filter by search query and tahun
    const filteredRows = allRows.filter(row => {
        const text = (row.getAttribute('data-search') || '').toLowerCase();
        const rowTahun = (row.getAttribute('data-tahun') || '').trim();
        const matchSearch = !query || text.includes(query);
        const matchTahun = !filterTahun || rowTahun === filterTahun;
        return matchSearch && matchTahun;
    });

    const totalVisible = filteredRows.length;
    const totalPages = Math.ceil(totalVisible / perPage) || 1;
    if (currentPerbaikanPages[tabId] > totalPages) {
        currentPerbaikanPages[tabId] = totalPages;
    }
    const finalPage = currentPerbaikanPages[tabId] || 1;

    const startIdx = (finalPage - 1) * perPage;
    const endIdx = Math.min(startIdx + perPage, totalVisible);

    allRows.forEach(r => r.style.display = 'none');

    filteredRows.forEach((row, idx) => {
        if (idx >= startIdx && idx < endIdx) {
            row.style.display = '';
            const noCol = row.querySelector('td:first-child');
            if (noCol) noCol.textContent = idx + 1;
        } else {
            row.style.display = 'none';
        }
    });

    let infoId = 'infoTabLpm';
    let pagId = 'pagTabLpm';
    if (tabId === 'tab-unit') {
        infoId = 'infoTabUnit';
        pagId = 'pagTabUnit';
    } else if (tabId === 'tab-sudah') {
        infoId = 'infoTabSudah';
        pagId = 'pagTabSudah';
    } else if (tabId === 'tab-all') {
        infoId = 'infoTabAll';
        pagId = 'pagTabAll';
    }

    const infoEl = document.getElementById(infoId);
    const pagEl = document.getElementById(pagId);

    if (infoEl) {
        if (totalVisible === 0) {
            infoEl.textContent = 'Menampilkan 0 dari 0 dokumen';
        } else {
            const startNum = startIdx + 1;
            const endNum = endIdx;
            infoEl.innerHTML = `Menampilkan <strong>${startNum} - ${endNum}</strong> dari total <strong>${totalVisible}</strong> dokumen`;
        }
    }

    if (!pagEl) return;
    if (totalPages <= 1) {
        pagEl.innerHTML = '';
        return;
    }

    let html = '<ul class="pagination-segmented mb-0">';
    html += '<li class="page-item ' + (finalPage <= 1 ? 'disabled' : '') + '">';
    html += '<button type="button" class="page-link" ' + (finalPage > 1 ? 'onclick="paginateTab(\'' + tabId + '\', ' + (finalPage - 1) + ')\"' : 'disabled') + ' aria-label="Sebelumnya"><i class="fas fa-chevron-left" style="font-size: 0.72rem;"></i></button>';
    html += '</li>';

    let startPage = Math.max(1, finalPage - 2);
    let endPage = Math.min(totalPages, finalPage + 2);

    if (startPage > 1) {
        html += '<li class="page-item"><button type="button" class="page-link" onclick="paginateTab(\'' + tabId + '\', 1)">1</button></li>';
        if (startPage > 2) {
            html += '<li class="page-item ellipsis disabled"><span class="page-link dots">&hellip;</span></li>';
        }
    }

    for (let p = startPage; p <= endPage; p++) {
        html += '<li class="page-item ' + (p === finalPage ? 'active' : '') + '">';
        html += '<button type="button" class="page-link" onclick="paginateTab(\'' + tabId + '\', ' + p + ')">' + p + '</button>';
        html += '</li>';
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += '<li class="page-item ellipsis disabled"><span class="page-link dots">&hellip;</span></li>';
        }
        html += '<li class="page-item"><button type="button" class="page-link" onclick="paginateTab(\'' + tabId + '\', ' + totalPages + ')">' + totalPages + '</button></li>';
    }

    html += '<li class="page-item ' + (finalPage >= totalPages ? 'disabled' : '') + '">';
    html += '<button type="button" class="page-link" ' + (finalPage < totalPages ? 'onclick="paginateTab(\'' + tabId + '\', ' + (finalPage + 1) + ')\"' : 'disabled') + ' aria-label="Selanjutnya"><i class="fas fa-chevron-right" style="font-size: 0.72rem;"></i></button>';
    html += '</li>';
    html += '</ul>';

    pagEl.innerHTML = html;
}

function refreshAllPerbaikanTabs() {
    ['tab-lpm', 'tab-unit', 'tab-sudah', 'tab-all'].forEach(id => paginateTab(id, 1));
}

function filterPerbaikanList() {
    refreshAllPerbaikanTabs();
}

function changePerbaikanPageSize(val) {
    const sel = document.getElementById('perPagePerbaikan');
    if (sel) sel.value = val;
    document.querySelectorAll('.table-perpage-select').forEach(s => s.value = val);
    refreshAllPerbaikanTabs();
}

function copyLpmToGpm() {
    let lpmText = document.getElementById('forwardModalLpmNote')?.textContent || '';
    lpmText = lpmText.replace(/^\[CATATAN\s+INTERNAL\s+LPM[^\]]*\]\s*/i, '').trim();
    const gpmInput = document.getElementById('forwardGpmNoteInput');
    if (gpmInput) {
        gpmInput.value = lpmText;
        gpmInput.focus();
    }
    const copyBtn = document.getElementById('btnCopyLpmToGpm');
    if (copyBtn) {
        const origHtml = '<i class="fas fa-copy text-warning"></i> <span>Salin ke Arahan GPM</span>';
        copyBtn.innerHTML = '<i class="fas fa-check text-success"></i> <span>Tersalin ke Arahan GPM!</span>';
        setTimeout(() => { copyBtn.innerHTML = origHtml; }, 1600);
    }
}

function openForwardRevisionModal(docId, docTitle, unitName, lpmNote, gpmNote) {
    document.getElementById('forwardModalDocId').value = docId || '';
    document.getElementById('forwardModalDocTitle').textContent = docTitle || '-';
    document.getElementById('forwardModalUnitBadge').textContent = unitName || '-';
    document.getElementById('forwardModalLpmNote').textContent = lpmNote || '-';
    document.getElementById('forwardGpmNoteInput').value = gpmNote || '';

    const modalEl = document.getElementById('modalForwardLpmRevision');
    if (modalEl && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi awal pagination untuk seluruh tab
    refreshAllPerbaikanTabs();

    // Event listener search live
    const searchInput = document.getElementById('liveSearchPerbaikan');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            refreshAllPerbaikanTabs();
        });
    }

    // Event listener per page
    const perPageSelect = document.getElementById('perPagePerbaikan');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            changePerbaikanPageSize(this.value);
        });
    }

    // Tab change re-pagination
    const tabBtns = document.querySelectorAll('#perbaikanTabs button[data-bs-toggle="pill"]');
    tabBtns.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            const targetId = e.target.getAttribute('data-bs-target').replace('#', '');
            paginateTab(targetId);
        });
    });

    // Handler tombol Forward LPM Revision (delegated)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-open-forward');
        if (!btn) return;
        openForwardRevisionModal(
            btn.getAttribute('data-doc-id'),
            btn.getAttribute('data-doc-title'),
            btn.getAttribute('data-unit-name'),
            btn.getAttribute('data-lpm-note'),
            btn.getAttribute('data-gpm-note')
        );
    });

    // Handler modal detail evaluator
    const evalModal = document.getElementById('modalCatatanEvaluator');
    const evalForwardBtn = document.getElementById('evalModalForwardBtn');
    let evalCurrentPayload = null;

    if (evalModal) {
        evalModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            const docId = btn.getAttribute('data-doc-id') || '';
            const nama = btn.getAttribute('data-nama') || '-';
            const unit = btn.getAttribute('data-unit') || '-';
            const reviewer = btn.getAttribute('data-reviewer') || '-';
            const tanggal = btn.getAttribute('data-tanggal') || '-';
            const catatan = btn.getAttribute('data-catatan') || 'Tidak ada catatan evaluasi.';
            const gpmNote = btn.getAttribute('data-gpm-note') || '';
            const editUrl = btn.getAttribute('data-edit-url') || '#';
            const status = btn.getAttribute('data-status') || '';
            const isGpm = btn.getAttribute('data-is-gpm') === '1';

            evalCurrentPayload = { docId, nama, unit, catatan, gpmNote };

            document.getElementById('evalModalDocTitle').textContent = nama;
            const unitBadgeEl = document.getElementById('evalModalUnitBadge');
            if (unitBadgeEl) unitBadgeEl.textContent = unit;
            document.getElementById('evalModalReviewer').textContent = reviewer;
            document.getElementById('evalModalTanggal').textContent = tanggal;
            document.getElementById('evalModalCatatan').textContent = catatan;
            document.getElementById('evalModalEditBtn').href = editUrl;

            const modalLabel = document.getElementById('evalModalLabel');
            const iconEl = document.getElementById('evalModalIcon');
            const revIconEl = document.getElementById('evalModalReviewerIcon');
            const noteCard = document.getElementById('evalModalNoteCard');
            const noteHeader = document.getElementById('evalModalNoteHeader');
            const scopePill = document.getElementById('evalModalScopePill');
            const noteBox = document.getElementById('evalModalCatatan');
            const privacyNote = document.getElementById('evalModalPrivacyNote');

            if (isGpm) {
                if (modalLabel) modalLabel.textContent = 'Catatan Arahan Resmi GPM Fakultas';
                if (iconEl) iconEl.style.background = 'linear-gradient(135deg, #7C3AED 0%, #5B21B6 100%)';
                if (revIconEl) revIconEl.className = 'fas fa-shield-halved text-purple';
                if (noteCard) {
                    noteCard.style.background = '#FAF5FF';
                    noteCard.style.setProperty('border-color', '#DDD6FE', 'important');
                    noteCard.style.setProperty('border-left', '4px solid #7C3AED', 'important');
                }
                if (noteHeader) noteHeader.style.color = '#5B21B6';
                if (scopePill) {
                    scopePill.style.background = '#EDE9FE';
                    scopePill.style.color = '#5B21B6';
                    scopePill.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Terbaca oleh Unit';
                }
                if (noteBox) noteBox.style.setProperty('border-color', '#DDD6FE', 'important');
                if (privacyNote) privacyNote.classList.add('d-none');
            } else {
                if (modalLabel) modalLabel.textContent = 'Catatan Evaluasi Internal Tim LPM (Akses GPM)';
                if (iconEl) iconEl.style.background = 'linear-gradient(135deg, #EA580C 0%, #D97706 100%)';
                if (revIconEl) revIconEl.className = 'fas fa-lock text-warning';
                if (noteCard) {
                    noteCard.style.background = '#FFFBEB';
                    noteCard.style.setProperty('border-color', '#FCD34D', 'important');
                    noteCard.style.setProperty('border-left', '4px solid #EA580C', 'important');
                }
                if (noteHeader) noteHeader.style.color = '#9A3412';
                if (scopePill) {
                    scopePill.style.background = '#FFEDD5';
                    scopePill.style.color = '#9A3412';
                    scopePill.innerHTML = '<i class="fas fa-lock me-1"></i> Akses Internal GPM';
                }
                if (noteBox) noteBox.style.setProperty('border-color', '#FDE68A', 'important');
                if (privacyNote) privacyNote.classList.remove('d-none');
            }

            if (evalForwardBtn) {
                if (docId && (status === 'revisi_lpm' || status === 'perlu_perbaikan')) {
                    evalForwardBtn.classList.remove('d-none');
                    evalForwardBtn.classList.add('d-inline-flex');
                } else {
                    evalForwardBtn.classList.add('d-none');
                    evalForwardBtn.classList.remove('d-inline-flex');
                }
            }
        });
    }

    if (evalForwardBtn) {
        evalForwardBtn.addEventListener('click', function() {
            if (!evalCurrentPayload || !evalCurrentPayload.docId) return;
            const detailInstance = bootstrap.Modal.getInstance(evalModal);
            const payload = { ...evalCurrentPayload };
            if (detailInstance) {
                evalModal.addEventListener('hidden.bs.modal', function onHidden() {
                    evalModal.removeEventListener('hidden.bs.modal', onHidden);
                    openForwardRevisionModal(payload.docId, payload.nama, payload.unit, payload.catatan, payload.gpmNote);
                });
                detailInstance.hide();
            } else {
                openForwardRevisionModal(payload.docId, payload.nama, payload.unit, payload.catatan, payload.gpmNote);
            }
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
