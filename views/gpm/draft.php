<?php
/**
 * GPM (Gugus Penjaminan Mutu): Ruang Kerja Draf Dokumen Mutu
 * Mengelola draf dokumen internal sebelum diajukan ke LPM (Fakultas & Prodi)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$draftDocs  = $draftDocs ?? [];
$fakultas   = $fakultas ?? [];
$prodisList = $prodisList ?? [];
$unitFilter = $unitFilter ?? 'all';
$totalDraft = count($draftDocs);

$readyCount = 0;
$incompleteCount = 0;
foreach ($draftDocs as $d) {
    $hasFiles = count($d['files'] ?? []) > 0 || !empty($d['external_link']) || !empty($d['file_path']);
    if ($hasFiles) {
        $readyCount++;
    } else {
        $incompleteCount++;
    }
}
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1.5">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #F3E8FF; color: #7E22CE; border: 1px solid #E9D5FF; font-size: 0.76rem;">
                    <i class="fas fa-shield-halved"></i> GPM <?= htmlspecialchars($fakultas['singkatan'] ?? $fakultas['nama_fakultas'] ?? '') ?>
                </span>
                <span class="text-muted small">&bull;</span>
                <span class="badge rounded-pill px-2.5 py-1 fw-semibold text-secondary d-inline-flex align-items-center gap-1" style="background: #F1F5F9; border: 1px solid #E2E8F0; font-size: 0.75rem;">
                    <i class="fas fa-file-pen text-primary"></i> Draf Dokumen Internal
                </span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">Draf Dokumen Mutu</h3>
            <p class="text-muted small mb-0" style="line-height: 1.5;">
                Kelola berkas draf dokumen mutu tingkat <strong>Fakultas</strong> dan seluruh <strong>Program Studi</strong> di bawah <strong><?= htmlspecialchars($fakultas['nama_fakultas'] ?? '') ?></strong> sebelum diajukan secara resmi ke Lembaga Penjaminan Mutu (LPM).
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('gpm/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-plus"></i> Unggah Dokumen Baru
            </a>
            <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-folder-open"></i> Repositori Dokumen Aktif
            </a>
        </div>
    </div>

    <!-- Informative Alert Callout -->
    <div class="alert rounded-4 p-4 mb-4 d-flex align-items-start gap-3.5 bg-white shadow-2xs" style="border: 1px solid #E2E8F0; border-left: 5px solid #2563EB !important;">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.1rem; background: #EFF6FF; color: #2563EB;">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div class="small flex-grow-1" style="line-height: 1.65; color: #475569;">
            <strong class="text-dark d-block mb-1" style="font-size: 0.92rem;">Tentang Ruang Draf Dokumen GPM:</strong>
            Dokumen yang berstatus <em>Draf</em> bersifat privat bagi pengelola mutu internal (Fakultas & Prodi) dan <strong>belum dapat dilihat oleh pengunjung umum ataupun Tim Penjaminan Mutu (LPM)</strong>. Dokumen dapat disiapkan terlebih dahulu, lalu diajukan ke LPM melalui tombol <strong>"Ajukan ke LPM"</strong> saat berkas lampiran sudah lengkap.
        </div>
    </div>

    <!-- Metric KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="saas-stat-card" style="border-top: 4px solid #002B49 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-dark-blue">Total Dokumen Draf</span>
                    <div class="saas-stat-icon-wrap" style="background: #EEF2F6; color: #002B49;">
                        <i class="fas fa-file-pen"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-dark-blue"><?= $totalDraft ?></span>
                        <span class="badge rounded-pill bg-light text-secondary border px-2.5 py-0.5" style="font-size: 0.72rem;">Draf Internal</span>
                    </div>
                    <div class="saas-stat-help">Semua draf dokumen yang belum diajukan ke LPM</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="saas-stat-card" style="border-top: 4px solid #059669 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-success">Siap Diajukan ke LPM</span>
                    <div class="saas-stat-icon-wrap" style="background: #ECFDF5; color: #059669;">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-success"><?= $readyCount ?></span>
                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-0.5" style="font-size: 0.72rem;">Berkas Lengkap</span>
                    </div>
                    <div class="saas-stat-help">Sudah melampirkan berkas dan dapat langsung diajukan</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="saas-stat-card" style="border-top: 4px solid #D97706 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="saas-stat-label text-warning">Perlu Dilengkapi Berkas</span>
                    <div class="saas-stat-icon-wrap" style="background: #FEF3C7; color: #D97706;">
                        <i class="fas fa-circle-exclamation"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="saas-stat-number text-warning"><?= $incompleteCount ?></span>
                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-0.5" style="font-size: 0.72rem;">Belum Ada File</span>
                    </div>
                    <div class="saas-stat-help">Harap edit dokumen untuk mengunggah berkas/tautan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Unit Kerja GPM: Dropdown Rapi, User-Friendly & Ringkas -->
    <div class="card shadow-2xs rounded-4 p-3 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 flex-grow-1" style="max-width: 580px;">
                <div class="rounded-3 bg-light text-primary d-flex align-items-center justify-content-center flex-shrink-0 shadow-2xs" style="width: 42px; height: 42px; border: 1px solid #E2E8F0; font-size: 1.15rem;">
                    <i class="fas fa-building-columns"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label for="filterUnitKerjaDraft" class="form-label mb-0 fw-bold text-dark small d-flex align-items-center gap-1.5">
                            <span>Filter Unit Kerja:</span>
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem;">GPM Scope</span>
                        </label>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #CBD5E1;"><i class="fas fa-filter text-scu-gold"></i></span>
                        <select id="filterUnitKerjaDraft" class="form-select border-start-0 fw-semibold" style="border-color: #CBD5E1; font-size: 0.84rem;" onchange="window.location.href = this.value">
                            <option value="<?= base_url('gpm/draft?unit=all') ?>" <?= $unitFilter === 'all' ? 'selected' : '' ?>>
                                📁 Semua Unit Kerja (Fakultas & Seluruh Program Studi)
                            </option>
                            <option value="<?= base_url('gpm/draft?unit=fakultas') ?>" <?= $unitFilter === 'fakultas' ? 'selected' : '' ?>>
                                🏢 Tingkat Fakultas (<?= htmlspecialchars($fakultas['nama_fakultas'] ?? 'Dekanat') ?>)
                            </option>
                            <optgroup label="── Program Studi di Bawah Fakultas ──">
                                <?php foreach ($prodisList as $p): ?>
                                    <option value="<?= base_url('gpm/draft?unit=' . $p['id']) ?>" <?= ((string)$unitFilter === (string)$p['id']) ? 'selected' : '' ?>>
                                        🎓 <?= htmlspecialchars($p['jenjang'] . ' ' . $p['nama_prodi']) ?>
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
                            <span class="text-dark">Semua Draf (Fakultas & Seluruh Prodi)</span>
                        <?php elseif ($unitFilter === 'fakultas'): ?>
                            <span class="text-primary"><i class="fas fa-building-columns me-1"></i>Draf Tingkat Fakultas</span>
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

    <!-- Table Card -->
    <?php if (empty($draftDocs)): ?>
        <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #E2E8F0 !important;">
            <div class="py-4">
                <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                    <i class="fas fa-file-circle-check text-secondary"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Tidak Ada Draf Dokumen</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width: 480px; line-height: 1.5;">
                    Saat ini tidak ada draf dokumen yang tersimpan pada filter unit yang dipilih. Saat mengunggah dokumen baru, Anda dapat memilih tombol <strong>"Simpan sebagai Draf"</strong> bila berkas masih dalam tahap penyusunan.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= base_url('gpm/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 bg-scu-blue border-0 shadow-xs fw-semibold">
                        <i class="fas fa-plus me-1.5"></i> Buat Dokumen Baru
                    </a>
                    <a href="<?= base_url('gpm/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2 fw-semibold">
                        <i class="fas fa-folder-open me-1.5"></i> Repositori Dokumen
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card rounded-4 shadow-sm bg-white overflow-hidden" style="border: 1px solid #E2E8F0 !important;">
            <!-- Sub-Toolbar Draf: Jumlah Tampilan & Search -->
            <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #002B49; color: #FFFFFF; font-size: 0.8rem;">
                        <i class="fas fa-file-pen text-warning"></i> Total: <strong><?= $totalDraft ?> Draf Dokumen</strong>
                    </span>
                    <span class="text-muted small d-none d-md-inline">Tersimpan secara privat & siap diajukan ke LPM</span>
                </div>
                <div class="d-flex align-items-center gap-2 w-100 w-sm-auto justify-content-between justify-content-sm-end flex-wrap">
                    <div class="d-flex align-items-center gap-1.5">
                        <label for="perPageDraftGpm" class="small text-muted text-nowrap mb-0" style="font-size: 0.78rem;">Tampilkan:</label>
                        <select id="perPageDraftGpm" class="form-select form-select-sm form-select-perpage shadow-none" style="min-width: 108px; width: auto; font-size: 0.8rem; border-color: #CBD5E1;" onchange="changeDraftGpmPageSize(this.value)">
                            <option value="10" selected>10 data</option>
                            <option value="25">25 data</option>
                            <option value="50">50 data</option>
                            <option value="100">100 data</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 250px;">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0 ps-1" id="searchDraftGpmInput" placeholder="Cari draf dokumen..." oninput="filterDraftGpmTable()" style="border-color: #CBD5E1;">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="draftGpmTable">
                    <thead class="table-light text-uppercase small text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3" style="width: 140px;">Unit Sasaran</th>
                            <th class="py-3">Dokumen & Siklus</th>
                            <th class="py-3">Bidang</th>
                            <th class="py-3">Kelengkapan Berkas</th>
                            <th class="py-3">Terakhir Diperbarui</th>
                            <th class="text-end pe-4 py-3" style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($draftDocs as $doc): ?>
                            <?php 
                            $files = $doc['files'] ?? [];
                            $links = parse_external_links($doc['external_link'] ?? '');
                            $hasAttached = (!empty($files) || !empty($links) || !empty($doc['file_path']));
                            ?>
                            <tr class="draft-gpm-row">
                                <!-- Unit Sasaran Badge -->
                                <td class="ps-4 align-top py-3">
                                    <?php if ($doc['level'] === 'fakultas'): ?>
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.74rem;">
                                            <i class="fas fa-building-columns"></i> Fakultas
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.74rem;">
                                            <i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($doc['jenjang'] ?? 'S1') ?> <?= htmlspecialchars($doc['nama_prodi'] ?? '') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Dokumen & Siklus -->
                                <td class="align-top py-3">
                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">
                                        <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-1.5 small">
                                        <?= siklus_badge($doc['siklus']) ?>
                                        <?php if (!empty($doc['nomor_dokumen'])): ?>
                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                <i class="fas fa-hashtag text-muted"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            TA <?= htmlspecialchars($doc['tahun_akademik'] ?? '-') ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Bidang Standar -->
                                <td class="align-top py-3">
                                    <span class="badge bg-light text-dark border fw-medium" style="font-size: 0.74rem;">
                                        <?= htmlspecialchars($doc['nama_bidang'] ?? 'Standar Mutu') ?>
                                    </span>
                                </td>

                                <!-- Kelengkapan Berkas -->
                                <td class="align-top py-3">
                                    <?php if ($hasAttached): ?>
                                        <div class="d-flex flex-column align-items-start gap-1">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                                <i class="fas fa-circle-check me-1"></i> Berkas Terlampir
                                            </span>
                                            <span class="text-muted" style="font-size: 0.72rem;">
                                                <?= count($files) ?> file <?= !empty($links) ? ', ' . count($links) . ' GDrive' : '' ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex flex-column align-items-start gap-1">
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                                <i class="fas fa-triangle-exclamation me-1"></i> Belum Ada Berkas
                                            </span>
                                            <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="small text-primary fw-medium" style="font-size: 0.72rem;">
                                                + Unggah Berkas
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Waktu Diperbarui -->
                                <td class="align-top py-3 small text-muted">
                                    <i class="far fa-clock me-1"></i> <?= date('d M Y, H:i', strtotime($doc['updated_at'])) ?>
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="pe-4 align-top text-end py-3">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <!-- Tombol Ajukan ke LPM -->
                                        <?php if ($hasAttached): ?>
                                            <button type="button" class="btn btn-primary bg-scu-blue border-0 btn-sm rounded-pill px-3 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#ajukanModal<?= $doc['id'] ?>">
                                                <i class="fas fa-paper-plane"></i> Ajukan ke LPM
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-light text-muted border btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" disabled title="Lampirkan berkas terlebih dahulu">
                                                <i class="fas fa-paper-plane"></i> Ajukan
                                            </button>
                                        <?php endif; ?>

                                        <!-- Edit -->
                                        <a href="<?= base_url('gpm/dokumen/edit/' . $doc['id']) ?>" class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;" title="Edit Dokumen">
                                            <i class="fas fa-pen text-primary" style="font-size: 0.8rem;"></i>
                                        </a>

                                        <!-- Delete Soft -->
                                        <button type="button" class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;" title="Pindahkan ke Arsip" data-bs-toggle="modal" data-bs-target="#delModal<?= $doc['id'] ?>">
                                            <i class="fas fa-trash-can text-danger" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Konfirmasi Ajukan ke LPM -->
                            <div class="modal fade" id="ajukanModal<?= $doc['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom pb-3">
                                            <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                                <i class="fas fa-paper-plane text-primary"></i> Ajukan Dokumen ke Tim LPM?
                                            </h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <p class="text-muted small mb-3">
                                                Dokumen mutu berikut akan diajukan ke Lembaga Penjaminan Mutu (LPM) untuk ditinjau:
                                            </p>
                                            <div class="p-3 rounded-3 bg-light border mb-3">
                                                <div class="fw-bold text-dark-blue mb-1"><?= htmlspecialchars($doc['nama_dokumen']) ?></div>
                                                <div class="small text-muted">
                                                    Sasaran: <strong><?= $doc['level'] === 'fakultas' ? 'Tingkat Fakultas' : ($doc['jenjang'] . ' ' . $doc['nama_prodi']) ?></strong> &bull; Siklus: <?= ucfirst($doc['siklus']) ?>
                                                </div>
                                            </div>
                                            <p class="text-muted small mb-0">
                                                Setelah diajukan, status dokumen akan berubah menjadi <strong>"Belum Direview"</strong> dan masuk ke antrean verifikasi tim LPM.
                                            </p>
                                        </div>
                                        <div class="modal-footer border-top pt-3">
                                            <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                                            <a href="<?= base_url('gpm/dokumen/ajukan/' . $doc['id']) ?>" class="btn btn-primary bg-scu-blue border-0 rounded-pill px-4 btn-sm fw-semibold shadow-xs">
                                                <i class="fas fa-paper-plane me-1.5"></i> Ya, Ajukan Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Konfirmasi Hapus Soft -->
                            <div class="modal fade" id="delModal<?= $doc['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-body p-4 text-center">
                                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                                                <i class="fas fa-trash-can"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Hapus Draf Dokumen?</h6>
                                            <p class="small text-muted mb-4" style="line-height: 1.45;">
                                                Draf <strong>"<?= htmlspecialchars($doc['nama_dokumen']) ?>"</strong> akan dipindahkan ke arsip.
                                            </p>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-light rounded-pill px-3 btn-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                                                <form action="<?= base_url('gpm/dokumen/delete/' . $doc['id']) ?>" method="POST" class="d-inline">
                                                    <button type="submit" class="btn btn-danger rounded-pill px-3 btn-sm fw-semibold shadow-xs">
                                                        Ya, Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="p-3.5 px-4 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 bg-white" id="draftGpmPaginationWrapper" style="border-color: #E2E8F0 !important;">
                <div class="small text-muted" id="draftGpmPageInfo">
                    Menampilkan <strong>1 - <?= min(10, $totalDraft) ?></strong> dari <strong><?= $totalDraft ?></strong> draf dokumen
                </div>
                <nav aria-label="Navigasi Halaman Draf">
                    <ul class="pagination pagination-sm mb-0 gap-1" id="draftGpmPaginationUl">
                        <!-- Rendered by JS -->
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let currentDraftGpmPage = 1;
let draftGpmPerPage = 10;

function changeDraftGpmPageSize(val) {
    draftGpmPerPage = (val === 'all') ? 999999 : parseInt(val, 10);
    currentDraftGpmPage = 1;
    filterDraftGpmTable();
}

function filterDraftGpmTable() {
    const q = (document.getElementById('searchDraftGpmInput')?.value || '').toLowerCase().trim();
    const rows = Array.from(document.querySelectorAll('#draftGpmTable tbody tr.draft-gpm-row'));
    
    const matchedRows = [];
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        if (!q || text.includes(q)) {
            matchedRows.push(row);
        } else {
            row.style.display = 'none';
        }
    });

    const totalMatched = matchedRows.length;
    const totalPages = draftGpmPerPage >= 999999 ? 1 : Math.max(1, Math.ceil(totalMatched / draftGpmPerPage));
    if (currentDraftGpmPage > totalPages) currentDraftGpmPage = totalPages;

    const startIdx = (currentDraftGpmPage - 1) * draftGpmPerPage;
    const endIdx = startIdx + draftGpmPerPage;

    matchedRows.forEach((row, idx) => {
        if (idx >= startIdx && idx < endIdx) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Update info
    const infoEl = document.getElementById('draftGpmPageInfo');
    if (infoEl) {
        if (totalMatched === 0) {
            infoEl.innerHTML = 'Tidak ada draf dokumen yang cocok dengan pencarian';
        } else {
            const displayStart = startIdx + 1;
            const displayEnd = Math.min(totalMatched, endIdx);
            infoEl.innerHTML = `Menampilkan <strong>${displayStart} - ${displayEnd}</strong> dari <strong>${totalMatched}</strong> draf dokumen`;
        }
    }

    renderDraftGpmPagination(totalPages, totalMatched);
}

function renderDraftGpmPagination(totalPages, totalMatched) {
    const ul = document.getElementById('draftGpmPaginationUl');
    if (!ul) return;
    ul.innerHTML = '';
    if (totalPages <= 1) return;

    // Prev
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentDraftGpmPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<button class="page-link rounded-2 px-2.5 py-1" onclick="goDraftGpmPage(${currentDraftGpmPage - 1})"><i class="fas fa-chevron-left"></i></button>`;
    ul.appendChild(prevLi);

    for (let p = 1; p <= totalPages; p++) {
        if (p === 1 || p === totalPages || (p >= currentDraftGpmPage - 1 && p <= currentDraftGpmPage + 1)) {
            const li = document.createElement('li');
            li.className = `page-item ${p === currentDraftGpmPage ? 'active' : ''}`;
            li.innerHTML = `<button class="page-link rounded-2 px-3 py-1 fw-medium" onclick="goDraftGpmPage(${p})">${p}</button>`;
            ul.appendChild(li);
        } else if (p === currentDraftGpmPage - 2 || p === currentDraftGpmPage + 2) {
            const li = document.createElement('li');
            li.className = 'page-item disabled';
            li.innerHTML = '<span class="page-link border-0">...</span>';
            ul.appendChild(li);
        }
    }

    // Next
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentDraftGpmPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<button class="page-link rounded-2 px-2.5 py-1" onclick="goDraftGpmPage(${currentDraftGpmPage + 1})"><i class="fas fa-chevron-right"></i></button>`;
    ul.appendChild(nextLi);
}

function goDraftGpmPage(p) {
    currentDraftGpmPage = p;
    filterDraftGpmTable();
}

document.addEventListener('DOMContentLoaded', () => {
    filterDraftGpmTable();
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
