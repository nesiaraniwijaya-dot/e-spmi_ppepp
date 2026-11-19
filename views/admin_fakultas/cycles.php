<?php
/**
 * Admin Fakultas: Halaman Judul yang Sudah Ada & Kelengkapan Tahapan PPEPP Fakultas
 * Menampilkan progress berkelanjutan 5 tahapan PPEPP dan tombol '+' untuk menambah dokumen lanjutan
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$totalCycles = count($existingCycles ?? []);
$completedCycles = 0;
$inProgressCycles = 0;
$sumProgress = 0;

$allTahunAkademik = [];
foreach ($existingCycles as $ec) {
    $sumProgress += (int)$ec['progress_percent'];
    if ((int)$ec['completed_count'] === 5) {
        $completedCycles++;
    } else {
        $inProgressCycles++;
    }
    if (!empty($ec['tahun_akademik'])) {
        $allTahunAkademik[$ec['tahun_akademik']] = true;
    }
}
$avgProgress = $totalCycles > 0 ? round($sumProgress / $totalCycles) : 0;

$distinctTahun = array_keys($allTahunAkademik);
if (function_exists('get_distinct_tahun_akademik')) {
    $globalTa = get_distinct_tahun_akademik();
    foreach ($globalTa as $gta) {
        if (!in_array($gta, $distinctTahun)) {
            $distinctTahun[] = $gta;
        }
    }
}
rsort($distinctTahun);
$bidangList = $bidangList ?? [];
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.76rem;">
                    <i class="fas fa-building-columns"></i> Fakultas <?= htmlspecialchars($fakultas['nama_fakultas']) ?>
                </span>
                <span class="badge bg-light text-secondary border">Tingkat Fakultas</span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">
                Judul yang Sudah Ada &amp; Tahapan PPEPP Fakultas
            </h3>
            <p class="text-muted small mb-0">
                Pemantauan siklus dokumen mutu bertahap Fakultas <strong><?= htmlspecialchars($fakultas['nama_fakultas']) ?></strong>.
                Setiap judul memuat 5 tahapan (Penetapan &rarr; Pelaksanaan &rarr; Evaluasi &rarr; Pengendalian &rarr; Peningkatan).
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('fakultas/dokumen/create') ?>" class="btn btn-warning rounded-pill px-4 py-2.5 text-dark fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.84rem;">
                <i class="fas fa-plus-circle"></i>
                <span>Unggah Dokumen Baru</span>
            </a>
            <a href="<?= base_url('fakultas/dokumen') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2.5 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-arrow-left"></i> <span>Daftar Dokumen</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Cards (Interactive Quick Filters) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 bg-white h-100 transition-all cursor-pointer" 
                 style="border: 1px solid #E2E8F0 !important;" 
                 onclick="resetAllCycleFilters()" 
                 title="Klik untuk menampilkan seluruh judul siklus">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Judul Siklus</span>
                    <span class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-folder-tree"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-dark-blue mb-0"><?= $totalCycles ?></div>
                <div class="text-muted" style="font-size: 0.72rem;">Judul tingkat fakultas terdaftar (klik untuk reset)</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 bg-white h-100" style="border: 1px solid #E2E8F0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Rata-rata Kelengkapan</span>
                    <span class="rounded-3 p-2 bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-chart-pie"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-dark-blue mb-0"><?= $avgProgress ?>%</div>
                <div class="text-muted" style="font-size: 0.72rem;">Progres kelengkapan 5 tahap</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 bg-white h-100 transition-all cursor-pointer" 
                 style="border: 1px solid #E2E8F0 !important;" 
                 onclick="quickFilterCycle('complete')" 
                 title="Klik untuk filter hanya siklus yang sudah lengkap (5/5)">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Siklus Lengkap (5/5)</span>
                    <span class="rounded-3 p-2 bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-circle-check"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-success mb-0"><?= $completedCycles ?></div>
                <div class="text-muted" style="font-size: 0.72rem;">Seluruh 5 tahap terpenuhi (klik untuk filter)</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 bg-white h-100 transition-all cursor-pointer" 
                 style="border: 1px solid #E2E8F0 !important;" 
                 onclick="quickFilterCycle('incomplete')" 
                 title="Klik untuk filter siklus yang masih perlu dilengkapi (< 5)">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Perlu Dilengkapi</span>
                    <span class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-clock-rotate-left"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-warning-emphasis mb-0"><?= $inProgressCycles ?></div>
                <div class="text-muted" style="font-size: 0.72rem;">Memiliki tahap belum lengkap (klik untuk filter)</div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar: Pencarian (Baris 1) & Filter Dropdown Terarah (Baris 2) -->
    <div class="card border-0 shadow-sm rounded-4 p-3.5 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <!-- Baris 1: Pencarian Cepat & Pengaturan Jumlah Tampilan Per Halaman -->
        <div class="row g-2.5 mb-2.5 align-items-center">
            <div class="col-md col-12">
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary border-end-0 px-3"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 py-2" id="searchCycleInput" 
                           placeholder="Cari nama judul siklus, standar, atau deskripsi..." oninput="applyAllCycleFilters()">
                </div>
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                <label for="cyclePerPageSelect" class="small text-muted text-nowrap mb-0 d-none d-sm-inline" style="font-size: 0.78rem;">Tampilkan:</label>
                <select class="form-select form-select-sm" id="cyclePerPageSelect" style="min-width: 105px; width: auto;" onchange="changeCyclePerPage(this.value)">
                    <option value="5" selected>5 judul</option>
                    <option value="10">10 judul</option>
                    <option value="20">20 judul</option>
                    <option value="50">50 judul</option>
                </select>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-2xs" onclick="resetAllCycleFilters()" title="Kembalikan semua filter ke kondisi awal">
                    <i class="fas fa-rotate-left"></i> <span class="d-none d-sm-inline">Reset Filter</span>
                </button>
            </div>
        </div>

        <!-- Baris 2: Dropdown Filter Spesifik Kebutuhan PPEPP -->
        <div class="row g-2.5 align-items-center">
            <!-- Filter 1: Tahun Akademik -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="far fa-calendar-alt"></i></span>
                    <select class="form-select form-select-sm border-start-0 ps-1" id="filterTahunAkademik" onchange="applyAllCycleFilters()">
                        <option value="">Semua Tahun Akademik</option>
                        <?php foreach ($distinctTahun as $ta): ?>
                            <option value="<?= htmlspecialchars($ta) ?>">TA <?= htmlspecialchars($ta) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Filter 2: Bidang Standar Mutu -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-layer-group"></i></span>
                    <select class="form-select form-select-sm border-start-0 ps-1" id="filterBidang" onchange="applyAllCycleFilters()">
                        <option value="">Semua Bidang Standar</option>
                        <?php foreach ($bidangList as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_bidang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Filter 3: Status Kelengkapan Siklus -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-chart-pie"></i></span>
                    <select class="form-select form-select-sm border-start-0 ps-1" id="filterKelengkapan" onchange="applyAllCycleFilters()">
                        <option value="">Semua Kelengkapan</option>
                        <option value="incomplete">Perlu Dilengkapi (&lt; 5 Tahap)</option>
                        <option value="complete">Siklus Lengkap (5/5 Tahap)</option>
                        <option value="single">Baru 1 Tahap (Perlu Lanjutan)</option>
                    </select>
                </div>
            </div>

            <!-- Filter 4: Tahap yang Belum Terisi (Dokumen Lanjutan yang Dibutuhkan) -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-circle-plus text-primary"></i></span>
                    <select class="form-select form-select-sm border-start-0 ps-1" id="filterTahapKosong" onchange="applyAllCycleFilters()">
                        <option value="">Semua Tahapan</option>
                        <option value="pelaksanaan">Tahap Pelaksanaan Belum Ada</option>
                        <option value="evaluasi">Tahap Evaluasi Belum Ada</option>
                        <option value="pengendalian">Tahap Pengendalian Belum Ada</option>
                        <option value="peningkatan">Tahap Peningkatan Belum Ada</option>
                        <option value="penetapan">Tahap Penetapan Belum Ada</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Baris 3: Info Ringkas & Status Filter Aktif -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2.5 mt-2.5 border-top border-light-subtle" style="font-size: 0.76rem;">
            <div class="text-muted d-flex align-items-center gap-1.5">
                <i class="fas fa-info-circle text-primary"></i>
                <span>Gunakan filter untuk menemukan judul siklus yang ingin Anda lengkapi dokumen mutunya. Klik icon <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-1.5 py-0.5"><i class="fas fa-plus"></i> Tambah</span> pada tahap yang kosong untuk mengunggah dokumen lanjutan.</span>
            </div>
            <div id="filterResultCountBadge" class="text-secondary fw-semibold">
                Menampilkan seluruh judul siklus
            </div>
        </div>
    </div>

    <!-- Daftar Judul Siklus PPEPP Cards -->
    <?php if (empty($existingCycles)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center" style="border: 1px solid #E2E8F0 !important;">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary mb-3" style="width: 64px; height: 64px;">
                <i class="fas fa-folder-open fa-2x"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Belum Ada Judul Siklus PPEPP Fakultas</h5>
            <p class="text-muted small mb-4" style="max-width: 520px; margin: 0 auto;">
                Fakultas Anda belum memiliki judul siklus dokumen mutu. Klik tombol di bawah untuk membuat judul baru dan mengunggah dokumen awal.
            </p>
            <div>
                <a href="<?= base_url('fakultas/dokumen/create') ?>" class="btn btn-warning rounded-pill px-4 py-2.5 text-dark fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>Unggah Dokumen Baru Sekarang</span>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-3.5" id="cycleListContainer">
            <?php foreach ($existingCycles as $cyc): 
                $cId = $cyc['id'];
                $cJudul = htmlspecialchars($cyc['judul_siklus']);
                $cBidang = htmlspecialchars($cyc['nama_bidang']);
                $cBidangId = (int)($cyc['bidang_id'] ?? 0);
                $cSub = htmlspecialchars($cyc['nama_sub_bidang'] ?: 'Umum');
                $cTahun = htmlspecialchars($cyc['tahun_akademik']);
                $cProgress = (int)$cyc['progress_percent'];
                $cCompleted = (int)$cyc['completed_count'];

                // Cari tahapan yang masih kosong (belum ada dokumen)
                $stagesList = ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'];
                $missingStages = [];
                foreach ($stagesList as $stg) {
                    if (empty($cyc['stages'][$stg]['has_doc'])) {
                        $missingStages[] = $stg;
                    }
                }
                $missingStagesStr = implode(',', $missingStages);
            ?>
                <div class="cycle-item-card p-4 rounded-4 border bg-white shadow-2xs transition-all" 
                     id="cycle_card_<?= $cId ?>" 
                     data-cycle-title="<?= strtolower($cJudul . ' ' . $cBidang . ' ' . $cSub . ' ' . ($cyc['deskripsi'] ?? '')) ?>"
                     data-cycle-tahun="<?= $cTahun ?>"
                     data-cycle-bidang="<?= $cBidangId ?>"
                     data-cycle-completed="<?= $cCompleted ?>"
                     data-cycle-missing="<?= $missingStagesStr ?>">
                    <!-- Card Header -->
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 pb-3 border-bottom mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 text-primary flex-shrink-0 p-2.5" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                <i class="fas fa-folder-tree"></i>
                            </span>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <h5 class="fw-bold text-dark-blue mb-0" style="font-size: 1.05rem;"><?= $cJudul ?></h5>
                                    <?php if (!empty($cyc['nama_prodi'])): ?>
                                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fw-semibold" style="font-size: 0.74rem;">
                                            <i class="fas fa-graduation-cap me-1"></i><?= htmlspecialchars($cyc['nama_prodi']) ?> (<?= htmlspecialchars($cyc['jenjang'] ?? 'S1') ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 fw-semibold" style="font-size: 0.74rem;">
                                            <i class="fas fa-landmark me-1"></i>Tingkat Fakultas
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge rounded-pill bg-light text-secondary border fw-normal" style="font-size: 0.74rem;">
                                        <i class="far fa-calendar-alt me-1"></i> TA <?= $cTahun ?>
                                    </span>
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background: #F1F5F9; color: #475569; font-size: 0.74rem;">
                                        <?= $cBidang ?> &bull; <?= $cSub ?>
                                    </span>
                                </div>
                                <div class="text-muted small" style="font-size: 0.76rem;">
                                    Terakhir diperbarui: <strong><?= date('d M Y H:i', strtotime($cyc['updated_at'])) ?></strong>
                                    <?php if (!empty($cyc['deskripsi'])): ?>
                                        &bull; <span class="fst-italic"><?= htmlspecialchars(mb_strimwidth($cyc['deskripsi'], 0, 80, '...')) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Progress Metric -->
                        <div class="d-flex align-items-center gap-3 flex-shrink-0">
                            <div class="text-end">
                                <div class="small fw-bold text-dark-blue" style="font-size: 0.82rem;"><?= $cCompleted ?> dari 5 Tahap Selesai</div>
                                <div class="text-muted" style="font-size: 0.72rem;"><?= $cProgress ?>% Kelengkapan PPEPP</div>
                            </div>
                            <div class="progress" style="width: 120px; height: 10px; border-radius: 999px; background: #E2E8F0;">
                                <div class="progress-bar <?= $cProgress == 100 ? 'bg-success' : ($cProgress >= 60 ? 'bg-primary' : 'bg-warning') ?>" role="progressbar" style="width: <?= $cProgress ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 5 Stages Stepper -->
                    <div class="row g-2.5">
                        <?php 
                        $stageDefs = [
                            'penetapan' => ['label' => 'Penetapan', 'code' => 'P1', 'color' => '#2563EB', 'bg' => '#EFF6FF', 'icon' => 'fa-compass-drafting'],
                            'pelaksanaan' => ['label' => 'Pelaksanaan', 'code' => 'P2', 'color' => '#059669', 'bg' => '#ECFDF5', 'icon' => 'fa-person-chalkboard'],
                            'evaluasi' => ['label' => 'Evaluasi', 'code' => 'E', 'color' => '#D97706', 'bg' => '#FFFBEB', 'icon' => 'fa-magnifying-glass-chart'],
                            'pengendalian' => ['label' => 'Pengendalian', 'code' => 'P3', 'color' => '#7C3AED', 'bg' => '#F5F3FF', 'icon' => 'fa-sliders'],
                            'peningkatan' => ['label' => 'Peningkatan', 'code' => 'P4', 'color' => '#0891B2', 'bg' => '#ECFEFF', 'icon' => 'fa-arrow-trend-up'],
                        ];
                        foreach ($stageDefs as $sKey => $sMeta): 
                            $stageInfo = $cyc['stages'][$sKey] ?? ['has_doc' => false, 'docs' => []];
                            $hasDoc = $stageInfo['has_doc'];
                            $docItem = $hasDoc ? $stageInfo['primary_doc'] : null;
                        ?>
                            <div class="col-12 col-sm-6 col-md col-lg">
                                <div class="stage-cell p-3 rounded-3 border h-100 d-flex flex-column justify-content-between position-relative transition-all <?= $hasDoc ? 'bg-light-subtle' : 'border-dashed bg-white' ?>">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                            <span class="badge rounded-pill px-2.5 py-0.5 fw-bold" style="background: <?= $sMeta['bg'] ?>; color: <?= $sMeta['color'] ?>; font-size: 0.72rem;">
                                                <?= $sMeta['code'] ?>
                                            </span>
                                            <?php if ($hasDoc): ?>
                                                <span class="badge rounded-circle p-1 bg-success text-white d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 0.7rem;" title="Dokumen sudah diunggah">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge rounded-circle p-1 bg-secondary bg-opacity-10 text-secondary d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 0.7rem;" title="Tahap ini belum ada dokumen">
                                                    <i class="fas fa-minus"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.84rem;" title="<?= $sMeta['label'] ?>">
                                            <?= $sMeta['label'] ?>
                                        </div>
                                    </div>

                                    <div class="mt-2.5 pt-2.5 border-top" style="border-color: #F1F5F9 !important;">
                                        <?php if ($hasDoc && $docItem): 
                                            $statRev = $docItem['status_review'] ?? 'draft';
                                            $statBadgeClass = 'bg-secondary bg-opacity-10 text-secondary';
                                            $statText = 'Draf';
                                            if ($statRev === 'disetujui_lpm') {
                                                $statBadgeClass = 'bg-success bg-opacity-10 text-success';
                                                $statText = 'Disetujui';
                                            } elseif (str_contains($statRev, 'menunggu')) {
                                                $statBadgeClass = 'bg-warning bg-opacity-10 text-warning-emphasis';
                                                $statText = 'Direview';
                                            } elseif (str_contains($statRev, 'revisi') || str_contains($statRev, 'perbaikan')) {
                                                $statBadgeClass = 'bg-danger bg-opacity-10 text-danger';
                                                $statText = 'Perlu Revisi';
                                            }
                                        ?>
                                            <div class="d-flex align-items-center justify-content-between gap-1">
                                                <span class="badge rounded-pill <?= $statBadgeClass ?> text-truncate px-2.5 py-1" style="font-size: 0.7rem; max-width: 95px;" title="<?= htmlspecialchars($docItem['nama_dokumen']) ?>">
                                                    <?= $statText ?>
                                                </span>
                                                <a href="<?= base_url('fakultas/dokumen/detail/' . $docItem['id']) ?>" target="_blank" class="btn btn-xs btn-outline-secondary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.68rem;" title="Lihat Dokumen">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <a href="<?= base_url('fakultas/dokumen/create?cycle_id=' . $cId . '&siklus=' . $sKey) ?>" 
                                               class="btn btn-sm btn-primary w-100 rounded-pill py-1.5 px-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-1.5 shadow-2xs btn-add-cycle-stage" 
                                               style="font-size: 0.75rem; background: #0284C7; border-color: #0284C7;"
                                               title="Tambah Dokumen untuk Tahap <?= $sMeta['label'] ?>">
                                                <i class="fas fa-plus"></i>
                                                <span>Tambah</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Alert jika pencarian tidak ditemukan -->
        <div id="noCycleMatchAlert" class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center mt-3" style="display: none; border: 1px solid #E2E8F0 !important;">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary mb-3 mx-auto" style="width: 56px; height: 56px;">
                <i class="fas fa-magnifying-glass fa-lg"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Tidak Ada Judul Siklus yang Sesuai</h6>
            <p class="text-muted small mb-0">Coba periksa kembali kata kunci pencarian Anda.</p>
        </div>

        <!-- Navigasi Paginasi 5 Judul per Halaman -->
        <div class="card border-0 shadow-2xs rounded-4 p-3 bg-white mt-4" id="cyclePaginationWrap" style="border: 1px solid #E2E8F0 !important;">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2.5">
                <div class="small text-muted" id="cyclePaginationInfo">
                    Menampilkan <strong id="cycleRangeInfo" class="text-dark">0 - 0</strong> dari <strong id="cycleTotalInfo" class="text-dark">0</strong> Judul Siklus
                </div>
                <nav aria-label="Navigasi Halaman Siklus">
                    <ul class="pagination pagination-sm mb-0 gap-1" id="cyclePaginationList">
                        <!-- Tombol Halaman Di-generate JS -->
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let ITEMS_PER_PAGE = 5;
let currentCyclePage = 1;

function changeCyclePerPage(val) {
    ITEMS_PER_PAGE = parseInt(val, 10) || 5;
    currentCyclePage = 1;
    applyAllCycleFilters();
}

function resetAllCycleFilters() {
    const sInput = document.getElementById('searchCycleInput');
    const fTahun = document.getElementById('filterTahunAkademik');
    const fBidang = document.getElementById('filterBidang');
    const fKelengkapan = document.getElementById('filterKelengkapan');
    const fTahapKosong = document.getElementById('filterTahapKosong');

    if (sInput) sInput.value = '';
    if (fTahun) fTahun.value = '';
    if (fBidang) fBidang.value = '';
    if (fKelengkapan) fKelengkapan.value = '';
    if (fTahapKosong) fTahapKosong.value = '';

    currentCyclePage = 1;
    applyAllCycleFilters();
}

function quickFilterCycle(type) {
    resetAllCycleFilters();
    const fKelengkapan = document.getElementById('filterKelengkapan');
    if (fKelengkapan) {
        fKelengkapan.value = type;
    }
    applyAllCycleFilters();

    const fCard = document.getElementById('searchCycleInput');
    if (fCard) {
        fCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function applyAllCycleFilters() {
    const allCards = Array.from(document.querySelectorAll('.cycle-item-card'));
    const noMatchAlert = document.getElementById('noCycleMatchAlert');
    const paginationWrap = document.getElementById('cyclePaginationWrap');
    const rangeInfo = document.getElementById('cycleRangeInfo');
    const totalInfo = document.getElementById('cycleTotalInfo');
    const resultBadge = document.getElementById('filterResultCountBadge');

    if (!allCards.length) return;

    const q = (document.getElementById('searchCycleInput') ? document.getElementById('searchCycleInput').value : '').toLowerCase().trim();
    const selTahun = (document.getElementById('filterTahunAkademik') ? document.getElementById('filterTahunAkademik').value : '').trim();
    const selBidang = (document.getElementById('filterBidang') ? document.getElementById('filterBidang').value : '').trim();
    const selKelengkapan = (document.getElementById('filterKelengkapan') ? document.getElementById('filterKelengkapan').value : '').trim();
    const selTahapKosong = (document.getElementById('filterTahapKosong') ? document.getElementById('filterTahapKosong').value : '').trim();

    const matchingCards = allCards.filter(card => {
        // 1. Text Search
        if (q) {
            const title = (card.getAttribute('data-cycle-title') || '').toLowerCase();
            if (!title.includes(q)) return false;
        }

        // 2. Filter Tahun Akademik
        if (selTahun) {
            const cTahun = (card.getAttribute('data-cycle-tahun') || '').trim();
            if (cTahun !== selTahun) return false;
        }

        // 3. Filter Bidang
        if (selBidang) {
            const cBidang = (card.getAttribute('data-cycle-bidang') || '').trim();
            if (cBidang !== selBidang) return false;
        }

        // 4. Filter Kelengkapan
        if (selKelengkapan) {
            const cCompleted = parseInt(card.getAttribute('data-cycle-completed') || '0', 10);
            if (selKelengkapan === 'complete' && cCompleted !== 5) return false;
            if (selKelengkapan === 'incomplete' && cCompleted >= 5) return false;
            if (selKelengkapan === 'single' && cCompleted !== 1) return false;
        }

        // 5. Filter Tahap Kosong
        if (selTahapKosong) {
            const missingStr = (card.getAttribute('data-cycle-missing') || '').toLowerCase();
            const missingArr = missingStr.split(',').map(s => s.trim());
            if (!missingArr.includes(selTahapKosong.toLowerCase())) return false;
        }

        return true;
    });

    const totalMatches = matchingCards.length;
    const totalPages = Math.max(1, Math.ceil(totalMatches / ITEMS_PER_PAGE));

    if (currentCyclePage > totalPages) currentCyclePage = totalPages;
    if (currentCyclePage < 1) currentCyclePage = 1;

    allCards.forEach(c => c.style.display = 'none');

    // Update result badge
    if (resultBadge) {
        const isFiltered = (q || selTahun || selBidang || selKelengkapan || selTahapKosong);
        if (isFiltered) {
            resultBadge.innerHTML = `<span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-pill"><i class="fas fa-filter me-1"></i>Ditemukan ${totalMatches} dari ${allCards.length} Judul Siklus</span>`;
        } else {
            resultBadge.textContent = `Menampilkan seluruh ${allCards.length} judul siklus`;
        }
    }

    if (totalMatches === 0) {
        if (noMatchAlert) noMatchAlert.style.display = 'block';
        if (paginationWrap) paginationWrap.style.display = 'none';
        return;
    }

    if (noMatchAlert) noMatchAlert.style.display = 'none';
    if (paginationWrap) paginationWrap.style.display = 'block';

    const startIdx = (currentCyclePage - 1) * ITEMS_PER_PAGE;
    const endIdx = Math.min(startIdx + ITEMS_PER_PAGE, totalMatches);

    for (let i = startIdx; i < endIdx; i++) {
        matchingCards[i].style.display = 'block';
    }

    if (rangeInfo) rangeInfo.textContent = `${startIdx + 1} - ${endIdx}`;
    if (totalInfo) totalInfo.textContent = totalMatches;

    renderCyclePaginationButtons(totalPages, currentCyclePage);
}

function renderCyclePaginationButtons(totalPages, currentPage) {
    const paginationList = document.getElementById('cyclePaginationList');
    const paginationWrap = document.getElementById('cyclePaginationWrap');
    if (!paginationList) return;
    paginationList.innerHTML = '';

    if (totalPages <= 1) {
        if (paginationWrap) paginationWrap.style.display = 'none';
        return;
    }
    if (paginationWrap) paginationWrap.style.display = 'block';

    // Prev Button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage <= 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<button type="button" class="page-link rounded-pill px-2.5 py-1" aria-label="Sebelumnya"><i class="fas fa-chevron-left" style="font-size:0.7rem;"></i></button>`;
    if (currentPage > 1) {
        prevLi.querySelector('button').onclick = () => goToCyclePage(currentPage - 1);
    }
    paginationList.appendChild(prevLi);

    // Page Numbers
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        const firstLi = document.createElement('li');
        firstLi.className = 'page-item';
        firstLi.innerHTML = `<button type="button" class="page-link rounded-pill px-2.5 py-1">1</button>`;
        firstLi.querySelector('button').onclick = () => goToCyclePage(1);
        paginationList.appendChild(firstLi);

        if (startPage > 2) {
            const dotsLi = document.createElement('li');
            dotsLi.className = 'page-item disabled';
            dotsLi.innerHTML = `<span class="page-link rounded-pill border-0 px-2 py-1">&hellip;</span>`;
            paginationList.appendChild(dotsLi);
        }
    }

    for (let p = startPage; p <= endPage; p++) {
        const li = document.createElement('li');
        li.className = `page-item ${p === currentPage ? 'active' : ''}`;
        li.innerHTML = `<button type="button" class="page-link rounded-pill px-2.5 py-1 ${p === currentPage ? 'fw-bold' : ''}">${p}</button>`;
        if (p !== currentPage) {
            li.querySelector('button').onclick = () => goToCyclePage(p);
        }
        paginationList.appendChild(li);
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const dotsLi = document.createElement('li');
            dotsLi.className = 'page-item disabled';
            dotsLi.innerHTML = `<span class="page-link rounded-pill border-0 px-2 py-1">&hellip;</span>`;
            paginationList.appendChild(dotsLi);
        }
        const lastLi = document.createElement('li');
        lastLi.className = 'page-item';
        lastLi.innerHTML = `<button type="button" class="page-link rounded-pill px-2.5 py-1">${totalPages}</button>`;
        lastLi.querySelector('button').onclick = () => goToCyclePage(totalPages);
        paginationList.appendChild(lastLi);
    }

    // Next Button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage >= totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<button type="button" class="page-link rounded-pill px-2.5 py-1" aria-label="Selanjutnya"><i class="fas fa-chevron-right" style="font-size:0.7rem;"></i></button>`;
    if (currentPage < totalPages) {
        nextLi.querySelector('button').onclick = () => goToCyclePage(currentPage + 1);
    }
    paginationList.appendChild(nextLi);
}

function goToCyclePage(page) {
    currentCyclePage = page;
    applyAllCycleFilters();
    const container = document.getElementById('searchCycleInput');
    if (container) {
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function filterCycleList(query) {
    const sInput = document.getElementById('searchCycleInput');
    if (sInput && sInput.value !== query) sInput.value = query;
    currentCyclePage = 1;
    applyAllCycleFilters();
}

document.addEventListener('DOMContentLoaded', function() {
    applyAllCycleFilters();
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
