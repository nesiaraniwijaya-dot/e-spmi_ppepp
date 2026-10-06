<?php
/**
 * Public View: Repositori Dokumen Mutu Keseluruhan
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/header.php';

$initialSearch = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';
$initialSiklus = isset($_GET['siklus']) ? strtolower(htmlspecialchars(trim($_GET['siklus']))) : '';
$maxDocsSafe = max(1, $maxDocs ?? 1);
?>

<!-- ============================================================
     BANNER: PORTAL DOKUMEN MUTU & CAPAIAN PPEPP UNIVERSITAS
============================================================ -->
<section class="page-banner text-white">
    <div class="container position-relative z-index-2">
        <div class="row align-items-center">
            <div class="col-lg-10 col-xl-9">
                <nav aria-label="breadcrumb">
                    <div class="breadcrumb-lpm mb-2">
                        <a href="<?= base_url() ?>"><i class="fas fa-home me-1"></i> Beranda</a>
                        <span>/</span>
                        <span class="current">Portal Dokumen Mutu</span>
                    </div>
                </nav>

                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 text-warning small fw-bold mb-3">
                    <i class="fas fa-file-shield"></i> Repositori Mutu Institusional &bull; PETRA
                </div>

                <h1 class="page-banner-title mb-2">Portal Dokumen Mutu Universitas</h1>
                <p class="text-light opacity-80 small mb-0" style="max-width: 720px; line-height: 1.75; font-size: 0.95rem;">
                    Pusat repositori dokumen <strong>Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan (PPEPP)</strong> seluruh program studi di lingkungan <?= INSTITUTION_NAME ?> guna mendukung transparansi dan penjaminan mutu berkelanjutan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     TABEL DOKUMEN MUTU TERKATEGORISASI BERDASARKAN PPEPP
============================================================ -->
<section class="py-4 py-lg-5">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            
            <!-- 5 Siklus PPEPP Categorized Navigation Tabs Header -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="fw-bold text-dark-blue mb-1">Daftar Dokumen Mutu Terkategorisasi</h4>
                    <p class="text-muted small mb-0">Pilih salah satu tab tahapan siklus PPEPP di bawah ini untuk melihat dokumen sesuai kategorinya.</p>
                </div>
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-1.5 rounded-pill" style="font-size: 0.8rem;">
                        <i class="fas fa-folder-open me-1"></i> <?= $totalDokumen ?? count($allDocuments) ?> Total Dokumen
                    </span>
                </div>
            </div>

            <ul class="nav ppepp-nav-tabs mb-4" id="publicPpeppTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link <?= empty($initialSiklus) ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, '')">
                        <i class="fas fa-layer-group"></i> Semua (<?= $totalDokumen ?? count($allDocuments) ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-penetapan <?= $initialSiklus === 'penetapan' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'penetapan')">
                        <i class="fas fa-file-signature"></i> Penetapan (<?= $cycleCounts['penetapan'] ?? 0 ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-pelaksanaan <?= $initialSiklus === 'pelaksanaan' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'pelaksanaan')">
                        <i class="fas fa-person-chalkboard"></i> Pelaksanaan (<?= $cycleCounts['pelaksanaan'] ?? 0 ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-evaluasi <?= $initialSiklus === 'evaluasi' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'evaluasi')">
                        <i class="fas fa-magnifying-glass-chart"></i> Evaluasi (<?= $cycleCounts['evaluasi'] ?? 0 ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-pengendalian <?= $initialSiklus === 'pengendalian' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'pengendalian')">
                        <i class="fas fa-sliders"></i> Pengendalian (<?= $cycleCounts['pengendalian'] ?? 0 ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link tab-peningkatan <?= $initialSiklus === 'peningkatan' ? 'active' : '' ?>" type="button" onclick="selectPublicSiklusTab(this, 'peningkatan')">
                        <i class="fas fa-arrow-trend-up"></i> Peningkatan (<?= $cycleCounts['peningkatan'] ?? 0 ?>)
                    </button>
                </li>
            </ul>

            <!-- Access Status Notification Banner -->
            <?php if (Auth::check()): ?>
                <div class="alert alert-success border-success border-opacity-25 rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-2xs" style="background: rgba(16, 185, 129, 0.08);">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="fas fa-unlock-keyhole"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark-blue small">Akses Penuh Dokumen Mutu Aktif</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Anda masuk sebagai <strong><?= htmlspecialchars(Auth::user()['name']) ?></strong> (<?= strtoupper(str_replace('_', ' ', Auth::role())) ?>). Seluruh lembar dokumen mutu terbuka penuh tanpa batasan pratinjau.</div>
                        </div>
                    </div>
                    <span class="badge bg-success rounded-pill px-3 py-1.5"><i class="fas fa-circle-check me-1"></i> Akses Penuh Terbuka</span>
                </div>
            <?php else: ?>
                <div class="alert alert-warning border-warning border-opacity-25 rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-2xs" style="background: rgba(245, 158, 11, 0.08);">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark-blue small">Pratinjau Publik Terbatas</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Sebagai tamu publik, dokumen dengan tanda gembok dibatasi lembar awalnya. <strong>Civitas / Pengguna terdaftar</strong> dapat masuk ke akun untuk membaca seluruh lembar dokumen secara lengkap.</div>
                        </div>
                    </div>
                    <a href="<?= base_url('login?return_url=' . urlencode($_SERVER['REQUEST_URI'] ?? 'dokumen')) ?>" class="btn btn-warning btn-sm rounded-pill px-3 py-1.5 fw-bold text-dark shadow-2xs text-decoration-none">
                        <i class="fas fa-right-to-bracket me-1"></i> Masuk untuk Akses Lengkap
                    </a>
                </div>
            <?php endif; ?>

            <!-- Cycle Category Context Banner -->
            <div id="cycleCategoryHeader" class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                <div class="d-flex align-items-center gap-2.5">
                    <span id="cycleCategoryIcon" class="rounded-circle p-2 d-inline-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: #0A192F;">
                        <i class="fas fa-layer-group"></i>
                    </span>
                    <div>
                        <div class="fw-bold text-dark-blue small" id="cycleCategoryTitle">Semua Siklus Dokumen Mutu (PPEPP)</div>
                        <div class="text-muted" style="font-size: 0.73rem;" id="cycleCategoryDesc">Menampilkan seluruh repositori dokumen mutu terverifikasi dari semua tahapan siklus PPEPP.</div>
                    </div>
                </div>
                <span class="badge bg-white text-dark border px-3 py-1.5 rounded-pill shadow-2xs fw-semibold" id="cycleCategoryCountBadge">
                    <?= $totalDokumen ?? count($allDocuments) ?> Dokumen
                </span>
            </div>

            <!-- Toolbar: Pencarian, Filter Fakultas, Filter Prodi, Filter Bidang, Tampilkan -->
            <div class="row g-2.5 align-items-center mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="tableSearchInput" class="form-control bg-light border-start-0 ps-0" 
                               placeholder="Cari nama dokumen, nomor SK, prodi..." 
                               value="<?= $initialSearch ?>">
                    </div>
                </div>

                <!-- Filter Fakultas -->
                <div class="col-lg-3 col-md-6">
                    <select id="tableFakultasFilter" class="form-select bg-light">
                        <option value="">-- Semua Fakultas &amp; Unit --</option>
                        <option value="tingkat_fakultas">🏢 Dokumen Tingkat Fakultas (Dekanat)</option>
                        <?php foreach (($fakultasList ?? []) as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nama_fakultas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Program Studi -->
                <div class="col-lg-2 col-md-4">
                    <select id="tableProdiFilter" class="form-select bg-light">
                        <option value="">-- Semua Program Studi --</option>
                        <?php foreach (($prodiList ?? []) as $p): ?>
                            <option value="<?= $p['id'] ?>" data-fakultas-id="<?= $p['fakultas_id'] ?? '' ?>">
                                <?= htmlspecialchars($p['nama_prodi']) ?> (<?= htmlspecialchars($p['jenjang']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Bidang Standar -->
                <div class="col-lg-2 col-md-4">
                    <select id="tableBidangFilter" class="form-select bg-light">
                        <option value="">-- Semua Bidang --</option>
                        <?php foreach (($bidangList ?? []) as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Per Page Selector -->
                <div class="col-lg-2 col-md-4 d-flex justify-content-md-end align-items-center gap-1.5">
                    <label for="tablePerPageSelect" class="form-label small text-muted text-nowrap mb-0">Tampilkan:</label>
                    <select id="tablePerPageSelect" class="form-select form-select-sm bg-light" style="width: 82px;">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100" selected>100</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0" id="ppeppDocumentTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama &amp; Nomor Dokumen</th>
                            <th>Program Studi</th>
                            <th>Bidang</th>
                            <th>Siklus</th>
                            <th>Jenis</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allDocuments)): ?>
                            <tr class="no-matching-docs">
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-secondary d-block"></i>
                                    <h6 class="fw-bold">Belum ada dokumen</h6>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($allDocuments as $doc): ?>
                                <tr class="doc-row" 
                                    data-title="<?= htmlspecialchars(strtolower($doc['nama_dokumen'])) ?>" 
                                    data-nomor="<?= htmlspecialchars(strtolower($doc['nomor_dokumen'] ?? '')) ?>" 
                                    data-prodi="<?= htmlspecialchars(strtolower($doc['nama_prodi'] ?? '')) ?>"
                                    data-prodi-id="<?= $doc['prodi_id'] ?? '' ?>"
                                    data-fakultas-id="<?= $doc['computed_fakultas_id'] ?? $doc['fakultas_id'] ?? '' ?>"
                                    data-level="<?= $doc['level'] ?? '' ?>"
                                    data-bidang-id="<?= $doc['bidang_id'] ?? '' ?>"
                                    data-siklus="<?= htmlspecialchars(strtolower($doc['siklus'] ?? '')) ?>">
                                    <td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark-blue mb-0.5"><?= htmlspecialchars($doc['nama_dokumen'] ?? '') ?></div>
                                        <?php if (!empty($doc['nomor_dokumen'])): ?>
                                            <div class="small text-muted"><i class="fas fa-hashtag me-1"></i> <?= htmlspecialchars($doc['nomor_dokumen']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($doc['jenjang'])): ?>
                                            <span class="badge bg-dark-blue text-warning"><?= htmlspecialchars($doc['jenjang']) ?></span>
                                        <?php endif; ?>
                                        <div class="small fw-semibold text-dark"><?= htmlspecialchars($doc['nama_prodi'] ?? 'Tingkat Fakultas') ?></div>
                                        <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($doc['nama_fakultas'] ?? '') ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark mb-0.5">
                                            <?= htmlspecialchars($doc['nama_bidang'] ?: 'Umum / Lainnya') ?>
                                        </div>
                                        <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                            <div class="small text-muted d-flex align-items-center gap-1" style="font-size: 0.7rem;">
                                                <i class="fas fa-turn-up fa-rotate-90 text-secondary opacity-50"></i>
                                                <span class="badge bg-light text-secondary border px-1.5 py-0.5"><?= htmlspecialchars($doc['nama_sub_bidang']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= siklus_badge($doc['siklus']) ?></td>
                                    <td>
                                        <?php 
                                        $files = $doc['files'] ?? [];
                                        $filesCount = count($files);
                                        $gdriveLinks = parse_external_links($doc['external_link'] ?? '');
                                        $linkCount = count($gdriveLinks);
                                        if ($doc['jenis_upload'] === 'file'): 
                                        ?>
                                            <?php if ($filesCount > 1): ?>
                                                <span class="badge badge-file"><i class="fas fa-folder-open me-1"></i> <?= $filesCount ?> File PDF</span>
                                            <?php else: ?>
                                                <span class="badge badge-file"><i class="fas fa-file-pdf me-1"></i> File PDF</span>
                                            <?php endif; ?>
                                        <?php elseif ($linkCount > 1): ?>
                                            <span class="badge badge-link"><i class="fab fa-google-drive me-1"></i> <?= $linkCount ?> Link GDrive</span>
                                        <?php else: ?>
                                            <span class="badge badge-link"><i class="fab fa-google-drive me-1"></i> Link GDrive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($doc['jenis_upload'] === 'file'): ?>
                                            <?php if ($filesCount > 1): ?>
                                                <div class="dropdown d-inline-block">
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-eye me-1"></i> Lihat (<?= $filesCount ?>)
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 300px; max-width: min(92vw, 440px); width: max-content; font-size: 0.78rem;">
                                                        <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH BERKAS DOKUMEN:</li>
                                                        <?php foreach ($files as $fItem): 
                                                            $fLimit = isset($fItem['is_page_limited']) && (int)$fItem['is_page_limited'] === 0 ? 0 : (int)($fItem['public_page_limit'] ?? 1);
                                                            $fCanDl = (int)($fItem['can_download_public'] ?? 0);
                                                        ?>
                                                            <li class="mb-1.5">
                                                                <button type="button" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#pdfPreviewModal" 
                                                                        data-pdf-url="<?= base_url($fItem['file_path']) ?>" 
                                                                        data-doc-title="<?= htmlspecialchars($fItem['file_name']) ?>"
                                                                        data-doc-narasi="<?= htmlspecialchars($fItem['narasi'] ?? '') ?>"
                                                                        data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null)) ?>"
                                                                        data-public-limit="<?= $fLimit ?>"
                                                                        data-can-download="<?= $fCanDl ?>">
                                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                                        <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark text-truncate" title="<?= htmlspecialchars($fItem['file_name']) ?>">
                                                                            <i class="fas fa-file-pdf text-danger flex-shrink-0"></i>
                                                                            <span class="text-truncate" style="max-width: 220px;"><?= htmlspecialchars($fItem['file_name']) ?></span>
                                                                        </span>
                                                                        <span class="badge bg-light text-muted border flex-shrink-0" style="font-size: 0.65rem;"><?= htmlspecialchars($fItem['file_size'] ?: 'PDF') ?></span>
                                                                    </div>
                                                                    <?= render_sub_standar_badges($fItem['sub_bidang_ids'] ?? null) ?>
                                                                    <?php if (!empty($fItem['narasi'])): ?>
                                                                        <div class="small text-muted bg-light p-1.5 rounded border border-light-subtle" style="font-size: 0.72rem; line-height: 1.45; white-space: pre-line; word-break: break-word;">
                                                                            <i class="fas fa-quote-left text-primary opacity-50 me-1"></i><?= htmlspecialchars($fItem['narasi']) ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </button>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php else: ?>
                                                <?php 
                                                $singleFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'narasi' => '']; 
                                                $sLimit = isset($singleFile['is_page_limited']) && (int)$singleFile['is_page_limited'] === 0 ? 0 : (int)($singleFile['public_page_limit'] ?? $doc['public_page_limit'] ?? 1);
                                                $sCanDl = (int)($singleFile['can_download_public'] ?? $doc['can_download_public'] ?? 0);
                                                ?>
                                                <div class="d-inline-flex flex-column align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#pdfPreviewModal" 
                                                            data-pdf-url="<?= base_url($singleFile['file_path']) ?>" 
                                                            data-doc-title="<?= htmlspecialchars($singleFile['file_name']) ?>"
                                                            data-doc-narasi="<?= htmlspecialchars($singleFile['narasi'] ?? '') ?>"
                                                            data-doc-standar="<?= htmlspecialchars(render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null)) ?>"
                                                            data-public-limit="<?= $sLimit ?>"
                                                            data-can-download="<?= $sCanDl ?>">
                                                        <i class="fas fa-eye me-1"></i> Lihat
                                                    </button>
                                                    <?php if (!Auth::check() && $sLimit > 0): ?>
                                                        <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-30 rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" title="Pratinjau publik <?= $sLimit ?> halaman. Masuk untuk akses penuh.">
                                                            <i class="fas fa-lock me-1"></i><?= $sLimit ?> Hlm (Terbatas)
                                                        </span>
                                                    <?php elseif (Auth::check()): ?>
                                                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-30 rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                                            <i class="fas fa-unlock me-1"></i>Akses Penuh
                                                        </span>
                                                    <?php endif; ?>
                                                    <?= render_sub_standar_badges($singleFile['sub_bidang_ids'] ?? null) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif ($doc['jenis_upload'] === 'link' && $linkCount > 0): ?>
                                            <?php 
                                            $isLoggedIn = is_user_logged_in();
                                            ?>
                                            <?php if ($linkCount > 1): ?>
                                                <div class="dropdown d-inline-block">
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 dropdown-toggle shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fab fa-google-drive me-1"></i> Tautan (<?= $linkCount ?>)
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 300px; max-width: min(92vw, 440px); width: max-content; font-size: 0.78rem;">
                                                        <li class="dropdown-header text-muted fw-bold px-2 py-1" style="font-size: 0.68rem;">PILIH TAUTAN GDRIVE:</li>
                                                        <?php foreach ($gdriveLinks as $lIdx => $linkObj): 
                                                            $gUrl = $linkObj['url'];
                                                            $gNarasi = $linkObj['narasi'];
                                                            $gCanDl = (int)($linkObj['can_download_public'] ?? 0);
                                                            $canAccessLink = $isLoggedIn || $gCanDl === 1;
                                                        ?>
                                                            <li class="mb-1.5">
                                                                <?php if ($canAccessLink): ?>
                                                                    <a href="<?= htmlspecialchars($gUrl) ?>" target="_blank" class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light">
                                                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                                            <span class="d-flex align-items-center gap-1.5 fw-semibold text-primary text-truncate">
                                                                                <i class="fab fa-google-drive"></i>
                                                                                <span>Tautan GDrive #<?= $lIdx + 1 ?></span>
                                                                            </span>
                                                                            <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 0.68rem;"></i>
                                                                        </div>
                                                                        <?= render_sub_standar_badges($linkObj['sub_bidang_ids'] ?? null) ?>
                                                                        <?php if (!empty($gNarasi)): ?>
                                                                            <div class="small text-muted bg-light p-1.5 rounded border border-light-subtle" style="font-size: 0.72rem; line-height: 1.45; white-space: pre-line; word-break: break-word;">
                                                                                <i class="fas fa-quote-left text-primary opacity-50 me-1"></i><?= htmlspecialchars($gNarasi) ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <div class="dropdown-item p-2 rounded-2 text-wrap text-start border-bottom border-light opacity-75 bg-light" style="cursor: not-allowed;" title="Akses tautan ini dibatasi untuk umum (Perlu Login)">
                                                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                                                            <span class="d-flex align-items-center gap-1.5 fw-semibold text-secondary text-truncate">
                                                                                <i class="fas fa-lock text-muted"></i>
                                                                                <span>Tautan GDrive #<?= $lIdx + 1 ?> (Terkunci)</span>
                                                                            </span>
                                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.65rem;">Login</span>
                                                                        </div>
                                                                        <?= render_sub_standar_badges($linkObj['sub_bidang_ids'] ?? null) ?>
                                                                        <?php if (!empty($gNarasi)): ?>
                                                                            <div class="small text-muted p-1 rounded" style="font-size: 0.72rem; line-height: 1.45;">
                                                                                <?= htmlspecialchars($gNarasi) ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php else: 
                                                $singleLink = $gdriveLinks[0];
                                                $sLinkCanDl = (int)($singleLink['can_download_public'] ?? 0);
                                                $canAccessSingle = $isLoggedIn || $sLinkCanDl === 1;
                                            ?>
                                                <div class="d-inline-flex flex-column align-items-center gap-1">
                                                    <?php if ($canAccessSingle): ?>
                                                        <a href="<?= htmlspecialchars($singleLink['url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" title="<?= htmlspecialchars($singleLink['narasi'] ?? '') ?>">
                                                            <i class="fas fa-external-link-alt me-1"></i> Buka Link
                                                        </a>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-light border text-muted rounded-pill px-3 shadow-sm" disabled title="Tautan dikunci untuk publik (Hanya untuk pengguna terdaftar)">
                                                            <i class="fas fa-lock me-1 text-secondary"></i> Akses Terbatas
                                                        </button>
                                                    <?php endif; ?>
                                                    <?= render_sub_standar_badges($singleLink['sub_bidang_ids'] ?? null) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top mt-3">
                <div id="tableInfo" class="small text-muted"></div>
                <div id="tablePagination"></div>
            </div>

        </div>
    </div>
</section>

<!-- Categorized PPEPP Tab Filter & Search Script -->
<script>
let currentPublicSiklus = <?= json_encode($initialSiklus) ?>;

const siklusMeta = {
    '': {
        icon: 'fa-layer-group',
        bg: '#0A192F',
        title: 'Semua Siklus Dokumen Mutu (PPEPP)',
        desc: 'Menampilkan seluruh repositori dokumen mutu terverifikasi dari semua tahapan siklus PPEPP.'
    },
    'penetapan': {
        icon: 'fa-file-signature',
        bg: '#2563EB',
        title: 'Tahap Penetapan (P1) - Standar & Kebijakan SPMI',
        desc: 'Dokumen kebijakan mutu, standar SPMI, manual standar, dan capaian pembelajaran lulusan (CPL).'
    },
    'pelaksanaan': {
        icon: 'fa-person-chalkboard',
        bg: '#059669',
        title: 'Tahap Pelaksanaan (P2) - Pelaksanaan & Pemenuhan Standar',
        desc: 'Dokumen keterlaksanaan standar tridharma, instruksi kerja, SOP, dan pedoman operasional prodi.'
    },
    'evaluasi': {
        icon: 'fa-magnifying-glass-chart',
        bg: '#D97706',
        title: 'Tahap Evaluasi (E) - Evaluasi Pelaksanaan Standar & AMI',
        desc: 'Dokumen evaluasi berkala, laporan Audit Mutu Internal (AMI), asesmen capaian, dan evaluasi diri prodi.'
    },
    'pengendalian': {
        icon: 'fa-sliders',
        bg: '#7C3AED',
        title: 'Tahap Pengendalian (P3) - Pengendalian Standar & Tindakan Koreksi',
        desc: 'Dokumen Rapat Tinjauan Manajemen (RTM), perumusan rekomendasi perbaikan, dan tindakan korektif.'
    },
    'peningkatan': {
        icon: 'fa-arrow-trend-up',
        bg: '#0891B2',
        title: 'Tahap Peningkatan (P4) - Peningkatan Mutu Berkelanjutan',
        desc: 'Dokumen Continuous Quality Improvement (Kaizen) dan pembaharuan standar mutu melampaui SN-Dikti.'
    }
};

function selectPublicSiklusTab(btn, siklus) {
    const tabButtons = document.querySelectorAll('#publicPpeppTabs .nav-link');
    tabButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentPublicSiklus = siklus;

    // Update Category Banner Header
    const meta = siklusMeta[siklus] || siklusMeta[''];
    const catIcon = document.getElementById('cycleCategoryIcon');
    const catTitle = document.getElementById('cycleCategoryTitle');
    const catDesc = document.getElementById('cycleCategoryDesc');
    
    if (catIcon) {
        catIcon.style.background = meta.bg;
        catIcon.innerHTML = `<i class="fas ${meta.icon}"></i>`;
    }
    if (catTitle) catTitle.textContent = meta.title;
    if (catDesc) catDesc.textContent = meta.desc;

    // Optional update URL parameter cleanly without reloading
    if (window.history.replaceState) {
        const url = new URL(window.location);
        if (siklus) {
            url.searchParams.set('siklus', siklus);
        } else {
            url.searchParams.delete('siklus');
        }
        window.history.replaceState({}, '', url);
    }

    if (typeof window.triggerPublicFilter === 'function') {
        window.triggerPublicFilter();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tableSearchInput');
    const fakultasFilter = document.getElementById('tableFakultasFilter');
    const prodiFilter = document.getElementById('tableProdiFilter');
    const bidangFilter = document.getElementById('tableBidangFilter');
    const perPageSelect = document.getElementById('tablePerPageSelect');
    const rows = Array.from(document.querySelectorAll('#ppeppDocumentTable tbody tr.doc-row'));
    const infoEl = document.getElementById('tableInfo');
    const paginationEl = document.getElementById('tablePagination');
    const countBadgeEl = document.getElementById('cycleCategoryCountBadge');

    let currentPage = 1;
    let perPage = parseInt(perPageSelect.value, 10);

    function getFilteredRows() {
        const query = searchInput.value.trim().toLowerCase();
        const selectedFakultas = fakultasFilter ? fakultasFilter.value.trim() : '';
        const selectedProdi = prodiFilter ? prodiFilter.value.trim() : '';
        const selectedBidang = bidangFilter ? bidangFilter.value.trim() : '';

        return rows.filter(row => {
            const title = row.getAttribute('data-title') || '';
            const nomor = row.getAttribute('data-nomor') || '';
            const prodi = row.getAttribute('data-prodi') || '';
            const rowProdiId = row.getAttribute('data-prodi-id') || '';
            const rowFakultasId = row.getAttribute('data-fakultas-id') || '';
            const rowLevel = row.getAttribute('data-level') || '';
            const rowSiklus = row.getAttribute('data-siklus') || '';
            const rowBidang = row.getAttribute('data-bidang-id') || '';

            const matchQuery = !query || title.includes(query) || nomor.includes(query) || prodi.includes(query);
            const matchSiklus = !currentPublicSiklus || rowSiklus === currentPublicSiklus;
            
            let matchFakultas = true;
            if (selectedFakultas === 'tingkat_fakultas') {
                matchFakultas = (rowLevel === 'fakultas' || !rowProdiId);
            } else if (selectedFakultas) {
                matchFakultas = (rowFakultasId === selectedFakultas);
            }

            const matchProdi = !selectedProdi || rowProdiId === selectedProdi;
            const matchBidang = !selectedBidang || rowBidang === selectedBidang;

            return matchQuery && matchSiklus && matchFakultas && matchProdi && matchBidang;
        });
    }

    if (fakultasFilter && prodiFilter) {
        fakultasFilter.addEventListener('change', function() {
            const selectedFak = this.value;
            const prodiOptions = prodiFilter.querySelectorAll('option');
            
            prodiOptions.forEach(opt => {
                if (!opt.value) return;
                const optFakId = opt.getAttribute('data-fakultas-id') || '';
                if (!selectedFak || selectedFak === 'tingkat_fakultas' || optFakId === selectedFak) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                    if (prodiFilter.value === opt.value) {
                        prodiFilter.value = '';
                    }
                }
            });

            currentPage = 1;
            renderTable();
        });
    }

    function renderTable() {
        const filtered = getFilteredRows();
        const total = filtered.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));

        if (currentPage > totalPages) currentPage = totalPages;
        const startIndex = (currentPage - 1) * perPage;
        const endIndex = startIndex + perPage;

        rows.forEach(r => r.style.display = 'none');

        filtered.slice(startIndex, endIndex).forEach(r => {
            r.style.display = '';
        });

        // Update Counter in Context Banner
        if (countBadgeEl) {
            countBadgeEl.textContent = `${total} Dokumen`;
        }

        // Update Info
        if (total === 0) {
            infoEl.textContent = 'Tidak ada dokumen yang sesuai kriteria.';
        } else {
            const startNum = startIndex + 1;
            const endNum = Math.min(endIndex, total);
            infoEl.textContent = `Menampilkan ${startNum} - ${endNum} dari total ${total} dokumen`;
        }

        // Render Pagination
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        let html = '<ul class="pagination pagination-sm mb-0">';
        
        // Prev
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <button class="page-link" data-page="${currentPage - 1}">&laquo;</button>
        </li>`;

        for (let p = 1; p <= totalPages; p++) {
            if (p === 1 || p === totalPages || (p >= currentPage - 2 && p <= currentPage + 2)) {
                html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                    <button class="page-link" data-page="${p}">${p}</button>
                </li>`;
            } else if (p === currentPage - 3 || p === currentPage + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <button class="page-link" data-page="${currentPage + 1}">&raquo;</button>
        </li>`;
        html += '</ul>';

        paginationEl.innerHTML = html;

        paginationEl.querySelectorAll('button.page-link').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetPage = parseInt(this.getAttribute('data-page'), 10);
                if (targetPage >= 1 && targetPage <= totalPages && targetPage !== currentPage) {
                    currentPage = targetPage;
                    renderTable();
                }
            });
        });
    }

    window.triggerPublicFilter = function() {
        currentPage = 1;
        renderTable();
    };

    // Event listeners
    searchInput.addEventListener('input', () => { currentPage = 1; renderTable(); });
    if (prodiFilter) prodiFilter.addEventListener('change', () => { currentPage = 1; renderTable(); });
    if (bidangFilter) bidangFilter.addEventListener('change', () => { currentPage = 1; renderTable(); });
    perPageSelect.addEventListener('change', () => {
        perPage = parseInt(perPageSelect.value, 10);
        currentPage = 1;
        renderTable();
    });

    // If initial siklus is set, reflect metadata
    if (currentPublicSiklus && siklusMeta[currentPublicSiklus]) {
        const meta = siklusMeta[currentPublicSiklus];
        const catIcon = document.getElementById('cycleCategoryIcon');
        const catTitle = document.getElementById('cycleCategoryTitle');
        const catDesc = document.getElementById('cycleCategoryDesc');
        if (catIcon) {
            catIcon.style.background = meta.bg;
            catIcon.innerHTML = `<i class="fas ${meta.icon}"></i>`;
        }
        if (catTitle) catTitle.textContent = meta.title;
        if (catDesc) catDesc.textContent = meta.desc;
    }

    // Initial table render
    renderTable();
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
