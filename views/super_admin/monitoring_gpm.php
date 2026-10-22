<?php
/**
 * Halaman Monitoring Antrean Telaah GPM Terpusat untuk LPM
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/review-hub') ?>" class="text-decoration-none text-muted">Review Hub</a></li>
                    <li class="breadcrumb-item active text-dark-blue fw-semibold" aria-current="page">Monitoring Telaah GPM</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark-blue mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-shield-halved text-primary"></i> Monitoring Antrean Telaah GPM Fakultas
                <?php if ($totalAntrean > 0): ?>
                    <span class="badge bg-danger rounded-pill fs-6 px-2.5 py-1 shadow-2xs">
                        <?= $totalAntrean ?> Dokumen Antre
                    </span>
                <?php else: ?>
                    <span class="badge bg-success rounded-pill fs-6 px-2.5 py-1 shadow-2xs">
                        <i class="fas fa-check me-1"></i> Antrean Bersih
                    </span>
                <?php endif; ?>
            </h3>
            <p class="text-muted small mb-0">Pantau seluruh dokumen mutu yang sedang menunggu verifikasi oleh Gugus Penjaminan Mutu (GPM) di 10 Fakultas sebelum diajukan ke LPM.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-2xs" onclick="window.location.reload();">
                <i class="fas fa-rotate me-1"></i> Muat Ulang
            </button>
            <a href="<?= base_url('admin/review-hub') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-2xs">
                <i class="fas fa-clipboard-check me-1"></i> Review Hub LPM
            </a>
            <a href="<?= base_url('admin/upload-access') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-2xs">
                <i class="fas fa-key me-1"></i> Kelola Akses &amp; Tenggat
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

    <!-- KPI Metric Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Antrean GPM -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Menunggu GPM</span>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary p-2"><i class="fas fa-inbox fa-lg"></i></span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bolder text-dark mb-0"><?= $totalAntrean ?></div>
                    <span class="text-muted small">dokumen</span>
                </div>
                <div class="text-muted small mt-1" style="font-size: 0.74rem;">Seluruh fakultas &amp; program studi</div>
            </div>
        </div>

        <!-- 2. Dokumen Baru (Belum Ditelaah) -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Dokumen Baru</span>
                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info p-2"><i class="fas fa-file-circle-plus fa-lg"></i></span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bolder text-info mb-0"><?= $totalBaru ?></div>
                    <span class="text-muted small">dokumen baru</span>
                </div>
                <div class="text-muted small mt-1" style="font-size: 0.74rem;">Pengajuan pertama kali oleh unit</div>
            </div>
        </div>

        <!-- 3. Perbaikan Masuk Kembali -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Perbaikan Selesai</span>
                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning p-2"><i class="fas fa-rotate-left fa-lg"></i></span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bolder text-warning mb-0"><?= $totalPerbaikan ?></div>
                    <span class="text-muted small">telah diperbaiki</span>
                </div>
                <div class="text-muted small mt-1" style="font-size: 0.74rem;">Menunggu telaah ulang GPM</div>
            </div>
        </div>

        <!-- 4. Urgensi Aging (>= 7 Hari) -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Tunggakan &gt; 7 Hari</span>
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger p-2"><i class="fas fa-triangle-exclamation fa-lg"></i></span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bolder text-danger mb-0"><?= $totalUrgentAging ?></div>
                    <span class="text-muted small">dokumen mendesak</span>
                </div>
                <div class="text-danger small mt-1 fw-semibold" style="font-size: 0.74rem;">
                    <?= $totalUrgentAging > 0 ? '<span class="sidebar-pulse-dot me-1"></span>Perlu dikirimkan pengingat ke GPM' : 'Semua antrean dalam batas wajar' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Status Antrean per Fakultas (10 Fakultas UNIKA Soegijapranata) -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
            <div>
                <h5 class="fw-bold text-dark-blue mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-landmark text-primary"></i> Sebaran Antrean di 10 Fakultas
                </h5>
                <div class="text-muted small" style="font-size: 0.78rem;">Klik pada kartu fakultas untuk memfilter antrean dokumen secara instan</div>
            </div>
            <?php if (!empty($filters['fakultas_id'])): ?>
                <a href="<?= base_url('admin/monitoring-gpm') ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                    <i class="fas fa-times me-1"></i> Hapus Filter Fakultas
                </a>
            <?php endif; ?>
        </div>

        <div class="row g-2.5">
            <?php foreach ($allFakultas as $fak): 
                $fId = (int)$fak['id'];
                $summary = $fakultasQueueSummary[$fId] ?? null;
                $fCount = (int)($summary['total_antrean'] ?? 0);
                $fUrgent = (int)($summary['count_urgent'] ?? 0);
                $fGpmList = $gpmByFakultas[$fId] ?? [];
                $isSelected = ($filters['fakultas_id'] == $fId);
            ?>
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <a href="<?= base_url('admin/monitoring-gpm?fakultas_id=' . $fId) ?>" 
                       class="text-decoration-none card p-2.5 rounded-3 border transition-all h-100 <?= $isSelected ? 'border-primary shadow-xs' : 'border-light-subtle' ?>"
                       style="background: <?= $isSelected ? '#EFF6FF' : ($fCount > 0 ? '#FFFFFF' : '#F8FAFC') ?>;">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="badge rounded-pill <?= $fCount > 0 ? ($fUrgent > 0 ? 'bg-danger text-white' : 'bg-primary text-white') : 'bg-light text-muted border' ?>" style="font-size: 0.7rem;">
                                <?= $fak['kode_fakultas'] ?: 'FAK' ?>
                            </span>
                            <span class="fw-bolder <?= $fCount > 0 ? ($fUrgent > 0 ? 'text-danger' : 'text-primary') : 'text-muted' ?>" style="font-size: 0.95rem;">
                                <?= $fCount ?> Antrean
                            </span>
                        </div>
                        <div class="fw-bold text-dark text-truncate small mb-1" title="<?= htmlspecialchars($fak['nama_fakultas']) ?>">
                            <?= htmlspecialchars($fak['nama_fakultas']) ?>
                        </div>
                        <div class="text-muted text-truncate" style="font-size: 0.7rem;" title="<?= !empty($fGpmList) ? htmlspecialchars($fGpmList[0]['name']) : 'Belum ada akun GPM' ?>">
                            <i class="fas fa-user-shield me-1"></i>
                            <?= !empty($fGpmList) ? htmlspecialchars($fGpmList[0]['name']) : '<span class="text-danger italic">Belum diset</span>' ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Filter & Toolbar Data Antrean -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <form action="<?= base_url('admin/monitoring-gpm') ?>" method="GET" class="row g-3 align-items-end">
            <!-- Search Keyword -->
            <div class="col-xl-3 col-md-6">
                <label class="form-label fw-semibold small text-secondary">Cari Dokumen / Prodi / Fakultas</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 py-2" placeholder="Nama berkas, nomor SK..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
            </div>

            <!-- Fakultas Filter -->
            <div class="col-xl-2 col-md-3 col-sm-6">
                <label class="form-label fw-semibold small text-secondary">Fakultas</label>
                <select name="fakultas_id" class="form-select form-select-sm py-2" onchange="this.form.submit()">
                    <option value="">Semua Fakultas</option>
                    <?php foreach ($allFakultas as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= ($filters['fakultas_id'] == $f['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['nama_fakultas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Program Studi Filter -->
            <div class="col-xl-2 col-md-3 col-sm-6">
                <label class="form-label fw-semibold small text-secondary">Program Studi</label>
                <select name="prodi_id" class="form-select form-select-sm py-2" onchange="this.form.submit()">
                    <option value="">Semua Program Studi</option>
                    <?php foreach ($prodisList as $p): 
                        if (!empty($filters['fakultas_id']) && $p['fakultas_id'] != $filters['fakultas_id']) continue;
                    ?>
                        <option value="<?= $p['id'] ?>" <?= ($filters['prodi_id'] == $p['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['jenjang'] . ' ' . $p['nama_prodi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Siklus PPEPP Filter -->
            <div class="col-xl-2 col-md-3 col-sm-6">
                <label class="form-label fw-semibold small text-secondary">Siklus PPEPP</label>
                <select name="siklus" class="form-select form-select-sm py-2" onchange="this.form.submit()">
                    <option value="">Semua Siklus</option>
                    <option value="penetapan" <?= ($filters['siklus'] === 'penetapan') ? 'selected' : '' ?>>P - Penetapan</option>
                    <option value="pelaksanaan" <?= ($filters['siklus'] === 'pelaksanaan') ? 'selected' : '' ?>>P - Pelaksanaan</option>
                    <option value="evaluasi" <?= ($filters['siklus'] === 'evaluasi') ? 'selected' : '' ?>>E - Evaluasi</option>
                    <option value="pengendalian" <?= ($filters['siklus'] === 'pengendalian') ? 'selected' : '' ?>>P - Pengendalian</option>
                    <option value="peningkatan" <?= ($filters['siklus'] === 'peningkatan') ? 'selected' : '' ?>>P - Peningkatan</option>
                </select>
            </div>

            <!-- Tingkat Aging Filter -->
            <div class="col-xl-2 col-md-3 col-sm-6">
                <label class="form-label fw-semibold small text-secondary">Lama Tunggu (Aging)</label>
                <select name="aging" class="form-select form-select-sm py-2" onchange="this.form.submit()">
                    <option value="">Semua Durasi</option>
                    <option value="urgent" <?= ($filters['aging'] === 'urgent') ? 'selected' : '' ?>>🚨 &gt;= 7 Hari (Mendesak)</option>
                    <option value="warning" <?= ($filters['aging'] === 'warning') ? 'selected' : '' ?>>⚠️ 3 - 6 Hari (Perhatian)</option>
                    <option value="normal" <?= ($filters['aging'] === 'normal') ? 'selected' : '' ?>>✅ &lt; 3 Hari (Baru)</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="col-xl-1 col-md-12 d-flex gap-1.5">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 py-2 fw-semibold" title="Terapkan Filter">
                    <i class="fas fa-filter"></i>
                </button>
                <a href="<?= base_url('admin/monitoring-gpm') ?>" class="btn btn-outline-secondary btn-sm rounded-pill py-2 px-2.5" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <?php
    // -------------------------------------------------------------
    // PENGELOMPOKAN HIERARKIS: Fakultas -> Tingkat Fakultas & Prodi
    // -------------------------------------------------------------
    $hierarchicalQueues = [];
    foreach ($allFakultas as $fak) {
        $fId = (int)$fak['id'];
        $hierarchicalQueues[$fId] = [
            'fakultas' => $fak,
            'gpm' => $gpmByFakultas[$fId] ?? [],
            'fakultas_docs' => [],
            'prodi_groups' => [],
            'total_docs' => 0,
            'urgent_count' => 0
        ];
    }

    foreach ($pendingDocuments as $doc) {
        $fId = (int)$doc['calculated_fakultas_id'];
        if (!isset($hierarchicalQueues[$fId])) {
            $hierarchicalQueues[$fId] = [
                'fakultas' => ['id' => $fId, 'nama_fakultas' => $doc['nama_fakultas'] ?? 'Fakultas', 'kode_fakultas' => $doc['kode_fakultas'] ?? 'FAK'],
                'gpm' => $gpmByFakultas[$fId] ?? [],
                'fakultas_docs' => [],
                'prodi_groups' => [],
                'total_docs' => 0,
                'urgent_count' => 0
            ];
        }
        $hierarchicalQueues[$fId]['total_docs']++;
        if ((int)($doc['aging_days'] ?? 0) >= 7) {
            $hierarchicalQueues[$fId]['urgent_count']++;
        }

        if (($doc['level'] ?? '') === 'fakultas') {
            $hierarchicalQueues[$fId]['fakultas_docs'][] = $doc;
        } else {
            $pId = (int)($doc['prodi_id'] ?? 0);
            if (!isset($hierarchicalQueues[$fId]['prodi_groups'][$pId])) {
                $hierarchicalQueues[$fId]['prodi_groups'][$pId] = [
                    'prodi_id' => $pId,
                    'nama_prodi' => trim(($doc['jenjang'] ?? '') . ' ' . ($doc['nama_prodi'] ?? 'Program Studi')),
                    'kode_prodi' => $doc['kode_prodi'] ?? '',
                    'docs' => []
                ];
            }
            $hierarchicalQueues[$fId]['prodi_groups'][$pId]['docs'][] = $doc;
        }
    }
    ?>

    <!-- View Mode Switch & Section Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold text-dark-blue mb-0">Antrean Dokumen Telaah GPM</h5>
            <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                <?= count($pendingDocuments) ?> Dokumen Terdata
            </span>
        </div>
        <div class="btn-group p-1 bg-white border rounded-pill shadow-2xs" role="group">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold btn-primary text-white" id="btnModeStructured" onclick="switchQueueView('structured')">
                <i class="fas fa-sitemap me-1.5"></i> Struktur Fakultas &amp; Prodi
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold text-secondary" id="btnModeTable" onclick="switchQueueView('table')">
                <i class="fas fa-table-list me-1.5"></i> Tabel Ringkas Seluruh Antrean
            </button>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- VIEW 1: TAMPILAN TERSTRUKTUR (PER FAKULTAS & PRODI-PRODI) -->
    <!-- ======================================================== -->
    <div id="queueViewStructured" class="mb-4">
        <?php if (empty($pendingDocuments)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center">
                <div class="text-muted opacity-50 mb-3"><i class="fas fa-circle-check fa-4x text-success"></i></div>
                <h5 class="fw-bold text-dark">Tidak Ada Antrean Dokumen</h5>
                <p class="text-muted small mb-0" style="max-width: 450px; margin: 0 auto;">
                    Saat ini seluruh dokumen mutu telah selesai ditelaah oleh Gugus Penjaminan Mutu (GPM) Fakultas, atau filter yang Anda pilih tidak menemukan data.
                </p>
                <?php if (!empty(array_filter($filters))): ?>
                    <div class="mt-3">
                        <a href="<?= base_url('admin/monitoring-gpm') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="fas fa-rotate-left me-1"></i> Reset Semua Filter
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-4">
                <?php foreach ($hierarchicalQueues as $fId => $hData): 
                    // Hanya tampilkan fakultas yang memiliki antrean atau jika difilter secara eksplisit
                    if ($hData['total_docs'] === 0 && (empty($filters['fakultas_id']) || $filters['fakultas_id'] != $fId)) {
                        continue;
                    }
                    $fak = $hData['fakultas'];
                    $gpmList = $hData['gpm'];
                    $primaryPic = !empty($gpmList) ? $gpmList[0] : null;
                ?>
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <!-- Header Fakultas -->
                        <div class="card-header bg-white p-3.5 p-md-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-4 p-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 52px; height: 52px;">
                                    <i class="fas fa-landmark fa-xl"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-0.5">
                                        <h5 class="fw-bold text-dark-blue mb-0"><?= htmlspecialchars($fak['nama_fakultas']) ?></h5>
                                        <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.72rem;">
                                            <?= htmlspecialchars($fak['kode_fakultas'] ?? 'FAK') ?>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 text-muted small flex-wrap" style="font-size: 0.78rem;">
                                        <span>
                                            <i class="fas fa-user-shield text-primary me-1"></i>
                                            Koordinator GPM: <strong><?= $primaryPic ? htmlspecialchars($primaryPic['name']) : '<span class="text-danger">Belum Ditetapkan</span>' ?></strong>
                                        </span>
                                        <?php if ($primaryPic && !empty($primaryPic['email'])): ?>
                                            <span>
                                                <i class="far fa-envelope text-secondary me-1"></i><?= htmlspecialchars($primaryPic['email']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                    <?= $hData['total_docs'] ?> Dokumen Antre
                                </span>
                                <?php if ($hData['urgent_count'] > 0): ?>
                                    <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-semibold shadow-2xs" style="font-size: 0.78rem;">
                                        <i class="fas fa-triangle-exclamation me-1"></i> <?= $hData['urgent_count'] ?> Mendesak (&ge; 7 Hari)
                                    </span>
                                <?php endif; ?>
                                <?php if ($primaryPic): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                            onclick="openNudgeModal(<?= htmlspecialchars(json_encode([
                                                'doc_name' => "Seluruh Antrean Dokumen ({$hData['total_docs']} berkas)",
                                                'unit' => 'Fakultas & Prodi ' . $fak['nama_fakultas'],
                                                'fakultas' => $fak['nama_fakultas'],
                                                'gpm_name' => $primaryPic['name'],
                                                'gpm_email' => $primaryPic['email'],
                                                'gpm_phone' => $primaryPic['phone'] ?? '',
                                                'aging' => 7,
                                                'siklus' => 'PPEPP',
                                                'doc_url' => base_url('gpm/dokumen')
                                            ])) ?>)">
                                        <i class="fas fa-bell"></i> Ingatkan GPM
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-body p-3.5 p-md-4">
                            <!-- Kategori A: Dokumen Tingkat Fakultas (Dekanat) -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-2.5 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-building-columns text-primary"></i>
                                        <strong class="text-dark-blue small">Dokumen Mutu Tingkat Fakultas (Dekanat)</strong>
                                    </div>
                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                        <?= count($hData['fakultas_docs']) ?> Dokumen
                                    </span>
                                </div>

                                <?php if (empty($hData['fakultas_docs'])): ?>
                                    <div class="p-2.5 bg-light rounded-3 text-muted text-center small" style="font-size: 0.76rem;">
                                        <i class="fas fa-circle-check text-success me-1"></i> Tidak ada dokumen tingkat fakultas yang menunggu telaah GPM.
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive rounded-3 border">
                                        <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                            <thead class="table-light text-secondary" style="font-size: 0.72rem; text-transform: uppercase;">
                                                <tr>
                                                    <th class="ps-3 py-2">Dokumen Mutu</th>
                                                    <th class="py-2">Siklus &amp; Standar</th>
                                                    <th class="py-2">Lama Antre</th>
                                                    <th class="py-2">Status</th>
                                                    <th class="py-2 text-end pe-3">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($hData['fakultas_docs'] as $fDoc): 
                                                    $aging = (int)($fDoc['aging_days'] ?? 0);
                                                    $agingBadgeClass = match(true) {
                                                        $aging >= 7 => 'bg-danger text-white border-danger',
                                                        $aging >= 3 => 'bg-warning bg-opacity-20 text-warning-emphasis border-warning',
                                                        default     => 'bg-success bg-opacity-10 text-success border-success'
                                                    };
                                                    $docFilesList = $fDoc['files'] ?? [];
                                                    $primaryFile = !empty($docFilesList) ? $docFilesList[0] : null;
                                                    $fileUrl = $primaryFile ? base_url($primaryFile['file_path']) : (!empty($fDoc['file_path']) ? base_url($fDoc['file_path']) : '');
                                                    $isLink = ($fDoc['jenis_upload'] === 'link' || empty($fileUrl));
                                                    $linkTarget = !empty($fDoc['external_link']) ? $fDoc['external_link'] : '';
                                                ?>
                                                    <tr>
                                                        <td class="ps-3">
                                                            <div class="fw-bold text-dark mb-0.5"><?= htmlspecialchars($fDoc['nama_dokumen']) ?></div>
                                                            <div class="d-flex align-items-center gap-2 small text-muted" style="font-size: 0.7rem;">
                                                                <?php if (!empty($fDoc['nomor_dokumen'])): ?>
                                                                    <span><i class="fas fa-hashtag me-0.5"></i><?= htmlspecialchars($fDoc['nomor_dokumen']) ?></span>
                                                                <?php endif; ?>
                                                                <span class="badge bg-light text-secondary border px-1.5 py-0.5">TA <?= htmlspecialchars($fDoc['tahun_akademik']) ?></span>
                                                                <?php if (!empty($fDoc['tanggal_dokumen'])): ?>
                                                                    <span><i class="far fa-calendar-check me-0.5"></i><?= date('d M Y', strtotime($fDoc['tanggal_dokumen'])) ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mb-0.5"><?= render_siklus_badge($fDoc['siklus']) ?></div>
                                                            <div class="text-muted text-truncate" style="max-width: 180px; font-size: 0.7rem;">
                                                                <i class="fas fa-tag me-1"></i><?= htmlspecialchars($fDoc['nama_bidang'] ?? '-') ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge rounded-pill border px-2 py-0.5 fw-bold <?= $agingBadgeClass ?>" style="font-size: 0.7rem;">
                                                                <?= $aging ?> Hari Lalu
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 0.7rem;">
                                                                <?= $fDoc['status_review'] === 'sudah_diperbaiki' ? 'Perbaikan Selesai' : 'Telaah Baru' ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-end pe-3">
                                                            <?php if (!empty($fileUrl) || !empty($linkTarget)): ?>
                                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-2xs"
                                                                        style="width: 30px; height: 30px;"
                                                                        title="Pratinjau Berkas"
                                                                        data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                                        data-pdf-url="<?= htmlspecialchars($fileUrl) ?>"
                                                                        data-doc-title="<?= htmlspecialchars($fDoc['nama_dokumen']) ?>"
                                                                        data-file-type="pdf"
                                                                        data-is-link="<?= $isLink ? '1' : '0' ?>"
                                                                        data-link-url="<?= htmlspecialchars($linkTarget) ?>"
                                                                        data-can-access="1" data-can-download="1" data-public-limit="0">
                                                                    <i class="fas fa-eye" style="font-size: 0.75rem;"></i>
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

                            <!-- Kategori B: Dokumen Program Studi (Dikelompokkan per Prodi) -->
                            <div>
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-2.5 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-graduation-cap text-primary"></i>
                                        <strong class="text-dark-blue small">Dokumen Mutu Program Studi di Bawah Fakultas</strong>
                                    </div>
                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                        <?= count($hData['prodi_groups']) ?> Program Studi Aktif Antre
                                    </span>
                                </div>

                                <?php if (empty($hData['prodi_groups'])): ?>
                                    <div class="p-2.5 bg-light rounded-3 text-muted text-center small" style="font-size: 0.76rem;">
                                        <i class="fas fa-circle-check text-success me-1"></i> Seluruh program studi di bawah fakultas ini tidak memiliki antrean dokumen.
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex flex-column gap-3">
                                        <?php foreach ($hData['prodi_groups'] as $pGroup): ?>
                                            <div class="rounded-3 border p-3 bg-light bg-opacity-40">
                                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1.5 border-bottom">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge rounded-pill bg-white text-dark-blue border fw-bold px-2.5 py-1" style="font-size: 0.75rem;">
                                                            <i class="fas fa-graduation-cap text-primary me-1"></i> <?= htmlspecialchars($pGroup['nama_prodi']) ?>
                                                        </span>
                                                        <?php if (!empty($pGroup['kode_prodi'])): ?>
                                                            <span class="text-muted small" style="font-size: 0.72rem;">(<?= htmlspecialchars($pGroup['kode_prodi']) ?>)</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                                        <?= count($pGroup['docs']) ?> Dokumen Antre
                                                    </span>
                                                </div>

                                                <div class="table-responsive bg-white rounded-3 border">
                                                    <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                                        <thead class="table-light text-secondary" style="font-size: 0.72rem; text-transform: uppercase;">
                                                            <tr>
                                                                <th class="ps-3 py-2">Dokumen Mutu</th>
                                                                <th class="py-2">Siklus &amp; Standar</th>
                                                                <th class="py-2">Lama Antre</th>
                                                                <th class="py-2">Status</th>
                                                                <th class="py-2 text-end pe-3">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($pGroup['docs'] as $pDoc): 
                                                                $aging = (int)($pDoc['aging_days'] ?? 0);
                                                                $agingBadgeClass = match(true) {
                                                                    $aging >= 7 => 'bg-danger text-white border-danger',
                                                                    $aging >= 3 => 'bg-warning bg-opacity-20 text-warning-emphasis border-warning',
                                                                    default     => 'bg-success bg-opacity-10 text-success border-success'
                                                                };
                                                                $docFilesList = $pDoc['files'] ?? [];
                                                                $primaryFile = !empty($docFilesList) ? $docFilesList[0] : null;
                                                                $fileUrl = $primaryFile ? base_url($primaryFile['file_path']) : (!empty($pDoc['file_path']) ? base_url($pDoc['file_path']) : '');
                                                                $isLink = ($pDoc['jenis_upload'] === 'link' || empty($fileUrl));
                                                                $linkTarget = !empty($pDoc['external_link']) ? $pDoc['external_link'] : '';
                                                            ?>
                                                                <tr>
                                                                    <td class="ps-3">
                                                                        <div class="fw-bold text-dark mb-0.5"><?= htmlspecialchars($pDoc['nama_dokumen']) ?></div>
                                                                        <div class="d-flex align-items-center gap-2 small text-muted" style="font-size: 0.7rem;">
                                                                            <?php if (!empty($pDoc['nomor_dokumen'])): ?>
                                                                                <span><i class="fas fa-hashtag me-0.5"></i><?= htmlspecialchars($pDoc['nomor_dokumen']) ?></span>
                                                                            <?php endif; ?>
                                                                            <span class="badge bg-light text-secondary border px-1.5 py-0.5">TA <?= htmlspecialchars($pDoc['tahun_akademik']) ?></span>
                                                                            <?php if (!empty($pDoc['tanggal_dokumen'])): ?>
                                                                                <span><i class="far fa-calendar-check me-0.5"></i><?= date('d M Y', strtotime($pDoc['tanggal_dokumen'])) ?></span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="mb-0.5"><?= render_siklus_badge($pDoc['siklus']) ?></div>
                                                                        <div class="text-muted text-truncate" style="max-width: 180px; font-size: 0.7rem;">
                                                                            <i class="fas fa-tag me-1"></i><?= htmlspecialchars($pDoc['nama_bidang'] ?? '-') ?>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge rounded-pill border px-2 py-0.5 fw-bold <?= $agingBadgeClass ?>" style="font-size: 0.7rem;">
                                                                            <?= $aging ?> Hari Lalu
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 0.7rem;">
                                                                            <?= $pDoc['status_review'] === 'sudah_diperbaiki' ? 'Perbaikan Selesai' : 'Telaah Baru' ?>
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-end pe-3">
                                                                        <div class="d-inline-flex align-items-center gap-1">
                                                                            <?php if (!empty($fileUrl) || !empty($linkTarget)): ?>
                                                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-2xs"
                                                                                        style="width: 30px; height: 30px;"
                                                                                        title="Pratinjau Berkas"
                                                                                        data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                                                        data-pdf-url="<?= htmlspecialchars($fileUrl) ?>"
                                                                                        data-doc-title="<?= htmlspecialchars($pDoc['nama_dokumen']) ?>"
                                                                                        data-file-type="pdf"
                                                                                        data-is-link="<?= $isLink ? '1' : '0' ?>"
                                                                                        data-link-url="<?= htmlspecialchars($linkTarget) ?>"
                                                                                        data-can-access="1" data-can-download="1" data-public-limit="0">
                                                                                    <i class="fas fa-eye" style="font-size: 0.75rem;"></i>
                                                                                </button>
                                                                            <?php endif; ?>

                                                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-2xs"
                                                                                    style="width: 30px; height: 30px;"
                                                                                    title="Riwayat Catatan &amp; Evaluasi"
                                                                                    onclick="openReviewHistoryModal(this)"
                                                                                    data-doc-id="<?= $pDoc['id'] ?>"
                                                                                    data-doc-name="<?= htmlspecialchars($pDoc['nama_dokumen']) ?>">
                                                                                <i class="fas fa-clock-rotate-left" style="font-size: 0.75rem;"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ======================================================== -->
    <!-- VIEW 2: TAMPILAN TABEL RINGKAS (SEMUA ANTREAN DOKUMEN)   -->
    <!-- ======================================================== -->
    <div id="queueViewTable" class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4 d-none">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <h6 class="fw-bold text-dark-blue mb-0">Daftar Dokumen dalam Antrean GPM</h6>
                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <?= count($pendingDocuments) ?> Dokumen Ditemukan
                </span>
            </div>
        </div>

        <?php if (empty($pendingDocuments)): ?>
            <div class="text-center py-5 px-3">
                <div class="text-muted opacity-50 mb-3"><i class="fas fa-circle-check fa-4x text-success"></i></div>
                <h5 class="fw-bold text-dark">Tidak Ada Antrean Dokumen</h5>
                <p class="text-muted small mb-0" style="max-width: 450px; margin: 0 auto;">
                    Saat ini seluruh dokumen mutu telah ditelaah oleh Gugus Penjaminan Mutu (GPM) Fakultas.
                </p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                    <thead class="table-light text-secondary fw-semibold" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3" style="width: 40px;">#</th>
                            <th class="py-3" style="min-width: 280px;">Dokumen Mutu</th>
                            <th class="py-3" style="min-width: 180px;">Unit Pengunggah</th>
                            <th class="py-3" style="min-width: 140px;">Siklus &amp; Standar</th>
                            <th class="py-3" style="min-width: 170px;">PIC GPM Fakultas</th>
                            <th class="py-3" style="min-width: 150px;">Lama Antre (Aging)</th>
                            <th class="py-3 text-end pe-4" style="min-width: 160px;">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($pendingDocuments as $doc): 
                            $fId = (int)$doc['calculated_fakultas_id'];
                            $pics = $gpmByFakultas[$fId] ?? [];
                            $primaryPic = !empty($pics) ? $pics[0] : null;
                            $aging = (int)($doc['aging_days'] ?? 0);
                            $isBaru = in_array($doc['status_review'], ['menunggu_review_gpm', 'belum_direview']);

                            $agingBadgeClass = match(true) {
                                $aging >= 7 => 'bg-danger text-white border-danger',
                                $aging >= 3 => 'bg-warning bg-opacity-20 text-warning-emphasis border-warning',
                                default     => 'bg-success bg-opacity-10 text-success border-success'
                            };

                            $docFilesList = $doc['files'] ?? [];
                            $primaryFile = !empty($docFilesList) ? $docFilesList[0] : null;
                            $fileUrl = $primaryFile ? base_url($primaryFile['file_path']) : (!empty($doc['file_path']) ? base_url($doc['file_path']) : '');
                            $isLink = ($doc['jenis_upload'] === 'link' || empty($fileUrl));
                            $linkTarget = !empty($doc['external_link']) ? $doc['external_link'] : '';
                        ?>
                            <tr>
                                <td class="ps-4 text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark mb-0.5" title="<?= htmlspecialchars($doc['nama_dokumen']) ?>">
                                        <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.72rem;">
                                        <?php if (!empty($doc['nomor_dokumen'])): ?>
                                            <span class="text-muted"><i class="fas fa-hashtag me-0.5"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                        <?php endif; ?>
                                        <span class="badge bg-light text-secondary border px-1.5 py-0.5">
                                            TA <?= htmlspecialchars($doc['tahun_akademik']) ?>
                                        </span>
                                        <?php if (!empty($doc['tanggal_dokumen'])): ?>
                                            <span class="text-muted"><i class="far fa-calendar-check me-0.5"></i><?= date('d M Y', strtotime($doc['tanggal_dokumen'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark mb-0.5">
                                        <?= !empty($doc['nama_prodi']) ? htmlspecialchars($doc['jenjang'] . ' ' . $doc['nama_prodi']) : 'Dekanat ' . htmlspecialchars($doc['nama_fakultas'] ?? 'Fakultas') ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">
                                        <i class="fas fa-landmark me-1"></i><?= htmlspecialchars($doc['nama_fakultas'] ?? 'Fakultas') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <?= render_siklus_badge($doc['siklus']) ?>
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem; max-width: 160px;" title="<?= htmlspecialchars($doc['nama_bidang'] ?? '') ?>">
                                        <i class="fas fa-tag me-1"></i><?= htmlspecialchars($doc['nama_bidang'] ?? '-') ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($primaryPic): ?>
                                        <div class="fw-semibold text-dark mb-0.5" style="font-size: 0.8rem;">
                                            <i class="fas fa-user-shield text-primary me-1"></i><?= htmlspecialchars($primaryPic['name']) ?>
                                        </div>
                                        <div class="text-muted text-truncate" style="font-size: 0.7rem; max-width: 170px;">
                                            <a href="mailto:<?= htmlspecialchars($primaryPic['email']) ?>" class="text-decoration-none text-muted">
                                                <i class="far fa-envelope me-1"></i><?= htmlspecialchars($primaryPic['email']) ?>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size: 0.7rem;">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Belum Diset
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5 mb-1">
                                        <span class="badge rounded-pill border px-2 py-1 fw-bold <?= $agingBadgeClass ?>" style="font-size: 0.72rem;">
                                            <?php if ($aging >= 7): ?>
                                                <span class="sidebar-pulse-dot me-1"></span>
                                            <?php endif; ?>
                                            <?= $aging ?> Hari Lalu
                                        </span>
                                    </div>
                                    <div>
                                        <?php if ($isBaru): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                <i class="fas fa-sparkles me-0.5"></i> Telaah Baru
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                <i class="fas fa-wrench me-0.5"></i> Perbaikan Masuk
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <?php if (!empty($fileUrl) || !empty($linkTarget)): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-2xs"
                                                    style="width: 32px; height: 32px;"
                                                    title="Pratinjau Berkas Dokumen"
                                                    data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                    data-pdf-url="<?= htmlspecialchars($fileUrl) ?>"
                                                    data-doc-title="<?= htmlspecialchars($doc['nama_dokumen']) ?>"
                                                    data-file-type="pdf"
                                                    data-is-link="<?= $isLink ? '1' : '0' ?>"
                                                    data-link-url="<?= htmlspecialchars($linkTarget) ?>"
                                                    data-can-access="1" data-can-download="1" data-public-limit="0">
                                                <i class="fas fa-eye" style="font-size: 0.8rem;"></i>
                                            </button>
                                        <?php endif; ?>

                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle p-0 d-inline-flex align-items-center justify-content-center shadow-2xs"
                                                style="width: 32px; height: 32px;"
                                                title="Riwayat Catatan &amp; Evaluasi"
                                                onclick="openReviewHistoryModal(this)"
                                                data-doc-id="<?= $doc['id'] ?>"
                                                data-doc-name="<?= htmlspecialchars($doc['nama_dokumen']) ?>">
                                            <i class="fas fa-clock-rotate-left" style="font-size: 0.8rem;"></i>
                                        </button>

                                        <?php if ($primaryPic): ?>
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-2xs"
                                                    style="font-size: 0.75rem;"
                                                    onclick="openNudgeModal(<?= htmlspecialchars(json_encode([
                                                        'doc_name' => $doc['nama_dokumen'],
                                                        'unit' => !empty($doc['nama_prodi']) ? $doc['jenjang'] . ' ' . $doc['nama_prodi'] : 'Dekanat ' . $doc['nama_fakultas'],
                                                        'fakultas' => $doc['nama_fakultas'],
                                                        'gpm_name' => $primaryPic['name'],
                                                        'gpm_email' => $primaryPic['email'],
                                                        'gpm_phone' => $primaryPic['phone'] ?? '',
                                                        'aging' => $aging,
                                                        'siklus' => ucfirst($doc['siklus']),
                                                        'doc_url' => base_url('gpm/dokumen')
                                                    ])) ?>)">
                                                <i class="fas fa-bell"></i>
                                                <span class="d-none d-md-inline">Ingatkan</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Nudge / Template Pengingat GPM -->
<div class="modal fade" id="nudgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-navy text-white px-4 py-3 border-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-1.5 rounded-3 bg-white bg-opacity-15 text-warning">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Kirim Pengingat Telaah GPM</h6>
                        <div class="small text-white-50" style="font-size: 0.72rem;">Pesan resmi LPM untuk mempercepat proses telaah mutu</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Penerima Pengingat</label>
                    <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold text-dark small" id="nudgeGpmName"></div>
                            <div class="text-muted" style="font-size: 0.72rem;" id="nudgeGpmEmail"></div>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill" id="nudgeFakultasBadge"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Isi Teks Pengingat (Siap Salin)</label>
                    <textarea class="form-control" id="nudgeMessageText" rows="6" style="font-size: 0.8rem; font-family: monospace; line-height: 1.5;"></textarea>
                </div>

                <div class="alert alert-info border-0 p-2.5 rounded-3 mb-0 d-flex align-items-center gap-2" style="font-size: 0.74rem;">
                    <i class="fas fa-info-circle text-primary flex-shrink-0"></i>
                    <span>Teks di atas dapat langsung disalin ke WhatsApp atau Email penelaah GPM Fakultas terkait.</span>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-2.5 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="copyNudgeText()">
                        <i class="fas fa-copy me-1"></i> <span id="btnCopyLabel">Salin Teks</span>
                    </button>
                    <a href="#" id="btnSendWa" target="_blank" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold">
                        <i class="fab fa-whatsapp me-1"></i> Buka WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openNudgeModal(data) {
    document.getElementById('nudgeGpmName').textContent = data.gpm_name;
    document.getElementById('nudgeGpmEmail').textContent = data.gpm_email;
    document.getElementById('nudgeFakultasBadge').textContent = data.fakultas;

    const text = `Halo Bapak/Ibu ${data.gpm_name} (GPM ${data.fakultas}),\n\n` +
                 `Salam Penjaminan Mutu.\n` +
                 `Melalui sistem MITRA SPMI PPEPP, kami menginformasikan terdapat dokumen mutu yang sedang menunggu telaah Anda:\n\n` +
                 `📄 Dokumen: ${data.doc_name}\n` +
                 `🏢 Unit Pengunggah: ${data.unit}\n` +
                 `🔄 Tahap Siklus: ${data.siklus}\n` +
                 `⏳ Waktu Menunggu: ${data.aging} hari yang lalu\n\n` +
                 `Mohon perkenan Bapak/Ibu untuk memeriksa dan menelaah dokumen tersebut melalui portal MITRA: ${data.doc_url}\n\n` +
                 `Terima kasih atas kerja samanya dalam pengawalan mutu institusi kita.\n` +
                 `Lembaga Penjaminan Mutu (LPM) SCU`;

    document.getElementById('nudgeMessageText').value = text;

    const phoneClean = (data.gpm_phone || '').replace(/[^0-9]/g, '');
    const waUrl = phoneClean ? `https://wa.me/${phoneClean}?text=${encodeURIComponent(text)}` : `https://wa.me/?text=${encodeURIComponent(text)}`;
    document.getElementById('btnSendWa').href = waUrl;

    const modal = new bootstrap.Modal(document.getElementById('nudgeModal'));
    modal.show();
}

function copyNudgeText() {
    const textEl = document.getElementById('nudgeMessageText');
    textEl.select();
    navigator.clipboard.writeText(textEl.value).then(() => {
        const lbl = document.getElementById('btnCopyLabel');
        lbl.textContent = 'Tersalin!';
        setTimeout(() => { lbl.textContent = 'Salin Teks'; }, 2000);
    });
}

function switchQueueView(mode) {
    const structEl = document.getElementById('queueViewStructured');
    const tableEl = document.getElementById('queueViewTable');
    const btnStruct = document.getElementById('btnModeStructured');
    const btnTable = document.getElementById('btnModeTable');
    
    if (mode === 'structured') {
        structEl.classList.remove('d-none');
        tableEl.classList.add('d-none');
        btnStruct.className = 'btn btn-sm rounded-pill px-3 py-1 fw-semibold btn-primary text-white';
        btnTable.className = 'btn btn-sm rounded-pill px-3 py-1 fw-semibold text-secondary';
    } else {
        structEl.classList.add('d-none');
        tableEl.classList.remove('d-none');
        btnTable.className = 'btn btn-sm rounded-pill px-3 py-1 fw-semibold btn-primary text-white';
        btnStruct.className = 'btn btn-sm rounded-pill px-3 py-1 fw-semibold text-secondary';
    }
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
