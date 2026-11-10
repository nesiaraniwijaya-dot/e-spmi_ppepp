<?php
/**
 * Super Admin & LPM: Monitoring Lalu Lintas Berkas & Turnaround SPMI
 * Memantau dokumen tertahan, jarak waktu review GPM vs Upload, dan unit belum upload
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

// Helper format durasi waktu
function formatDurationHours(?int $hours): string {
    if ($hours === null) return '-';
    if ($hours < 1) return '< 1 Jam';
    $days = floor($hours / 24);
    $remHours = $hours % 24;
    if ($days > 0) {
        return "{$days} Hari " . ($remHours > 0 ? "{$remHours} Jam" : '');
    }
    return "{$hours} Jam";
}
?>

<div class="container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-scu-blue bg-opacity-10 text-primary small fw-bold mb-2">
                <i class="fas fa-file-waveform"></i> Pengawasan &amp; Status Dokumen Mutu
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Monitoring Dokumen Mutu</h3>
            <p class="text-muted small mb-0">Pemantauan status dokumen mutu: waktu tunggu, antrean review GPM, durasi respon penelaahan, serta unit yang belum mengunggah.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="<?= base_url('admin/tahun-akademik') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5">
                <i class="fas fa-calendar-gear"></i>
                <span>Kelola TA &amp; Jatah Upload</span>
            </a>
            <a href="<?= base_url('admin/monitoring-gpm') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5">
                <i class="fas fa-users-gear"></i>
                <span>Kinerja Anggota GPM</span>
            </a>
        </div>
    </div>

    <!-- KPI Summary Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="text-muted small fw-semibold">Total Berjalan</div>
                <h3 class="fw-bold text-dark-blue mb-1"><?= number_format($stats['total_berjalan']) ?></h3>
                <span class="text-muted" style="font-size: 0.72rem;">Dokumen diajukan</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="text-muted small fw-semibold">Tertahan di GPM</div>
                <h3 class="fw-bold text-warning mb-1"><?= number_format($stats['menunggu_gpm']) ?></h3>
                <span class="text-muted" style="font-size: 0.72rem;">Menunggu telaah GPM</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="text-muted small fw-semibold">Rata-rata Respon GPM</div>
                <h3 class="fw-bold text-dark-blue mb-1"><?= number_format($stats['avg_gpm_days'], 1) ?> <span class="fs-6 fw-normal text-muted">Hari</span></h3>
                <span class="text-muted" style="font-size: 0.72rem;">Upload ke telaah GPM</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-danger">
                <div class="text-muted small fw-semibold">Revisi di Unit</div>
                <h3 class="fw-bold text-danger mb-1"><?= number_format($stats['revisi_unit']) ?></h3>
                <span class="text-muted" style="font-size: 0.72rem;">Tertahan di Prodi/Fak</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-purple" style="border-left-color: #9333EA !important;">
                <div class="text-muted small fw-semibold">Antrean di LPM</div>
                <h3 class="fw-bold text-purple mb-1" style="color: #9333EA;"><?= number_format($stats['menunggu_lpm']) ?></h3>
                <span class="text-muted" style="font-size: 0.72rem;">Menunggu vonis LPM</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="text-muted small fw-semibold">Sah / Sesuai</div>
                <h3 class="fw-bold text-success mb-1"><?= number_format($stats['sesuai']) ?></h3>
                <span class="text-muted" style="font-size: 0.72rem;">Selesai diverifikasi</span>
            </div>
        </div>
    </div>

    <!-- Filter & Toolbar Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3.5 mb-4 bg-white">
        <form method="GET" action="<?= base_url('admin/monitoring-berkas') ?>" class="row g-2.5 align-items-end">
            <div class="col-md-3 col-lg-2">
                <label class="form-label small fw-bold text-dark mb-1">Tahun Akademik</label>
                <select name="tahun_akademik" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" <?= $activeTA === 'all' ? 'selected' : '' ?>>Semua TA</option>
                    <?php foreach ($tahunList as $ta): ?>
                        <option value="<?= htmlspecialchars($ta['tahun_akademik']) ?>" <?= $activeTA === $ta['tahun_akademik'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ta['tahun_akademik']) ?> <?= $ta['is_open'] ? '• Aktif' : '• Tutup' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3 col-lg-2">
                <label class="form-label small fw-bold text-dark mb-1">Status Penelaahan</label>
                <select name="kategori_traffic" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="menunggu_gpm" <?= $trafficFilter === 'menunggu_gpm' ? 'selected' : '' ?>>⏳ Tertahan di GPM</option>
                    <option value="revisi_unit" <?= $trafficFilter === 'revisi_unit' ? 'selected' : '' ?>>Perlu Revisi di Unit</option>
                    <option value="menunggu_lpm" <?= $trafficFilter === 'menunggu_lpm' ? 'selected' : '' ?>>Menunggu Telaah LPM</option>
                    <option value="sesuai" <?= $trafficFilter === 'sesuai' ? 'selected' : '' ?>>Sah / Selesai</option>
                </select>
            </div>

            <div class="col-md-3 col-lg-2">
                <label class="form-label small fw-bold text-dark mb-1">Tingkat / Level</label>
                <select name="level" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Tingkat</option>
                    <option value="fakultas" <?= $level === 'fakultas' ? 'selected' : '' ?>>Tingkat Fakultas</option>
                    <option value="prodi" <?= $level === 'prodi' ? 'selected' : '' ?>>Tingkat Program Studi</option>
                </select>
            </div>

            <div class="col-md-3 col-lg-2">
                <label class="form-label small fw-bold text-dark mb-1">Siklus PPEPP</label>
                <select name="siklus" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Siklus</option>
                    <option value="penetapan" <?= $siklus === 'penetapan' ? 'selected' : '' ?>>P1 - Penetapan</option>
                    <option value="pelaksanaan" <?= $siklus === 'pelaksanaan' ? 'selected' : '' ?>>P2 - Pelaksanaan</option>
                    <option value="evaluasi" <?= $siklus === 'evaluasi' ? 'selected' : '' ?>>E - Evaluasi</option>
                    <option value="pengendalian" <?= $siklus === 'pengendalian' ? 'selected' : '' ?>>P3 - Pengendalian</option>
                    <option value="peningkatan" <?= $siklus === 'peningkatan' ? 'selected' : '' ?>>P4 - Peningkatan</option>
                </select>
            </div>

            <div class="col-md-6 col-lg-3">
                <label class="form-label small fw-bold text-dark mb-1">Fakultas</label>
                <select name="fakultas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Fakultas</option>
                    <?php foreach ($fakultasList as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= $fakultasId == $f['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['nama_fakultas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 col-lg-1 d-flex gap-1">
                <a href="<?= base_url('admin/monitoring-berkas') ?>" class="btn btn-sm btn-outline-secondary rounded-pill w-100 py-1.5" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Navigation Tabs: Live Traffic vs Unit Belum Upload -->
    <ul class="nav nav-pills gap-2 mb-3" id="trafficTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-2xs" id="traffic-live-tab" data-bs-toggle="tab" data-bs-target="#traffic-live" type="button" role="tab">
                <i class="fas fa-file-lines text-primary"></i>
                <span>Status Berkas &amp; Turnaround (<?= count($documents) ?>)</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-2xs position-relative" id="traffic-deficit-tab" data-bs-toggle="tab" data-bs-target="#traffic-deficit" type="button" role="tab">
                <i class="fas fa-triangle-exclamation text-danger"></i>
                <span>Unit Belum Upload Dokumen</span>
                <?php $totalDeficit = count($fakultasBelumUpload) + count($prodiBelumUpload); if ($totalDeficit > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-1"><?= $totalDeficit ?></span>
                <?php endif; ?>
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="trafficTabsContent">
        <!-- TAB 1: LIVE TRAFFIC & TURNAROUND SLA -->
        <div class="tab-pane fade show active" id="traffic-live" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th style="min-width: 250px;">Dokumen Mutu &amp; Kategori</th>
                                <th style="min-width: 170px;">Unit Pengunggah</th>
                                <th style="min-width: 140px;">Waktu Upload</th>
                                <th style="min-width: 200px;">Review GPM &amp; Turnaround</th>
                                <th style="min-width: 170px;">Review Final LPM</th>
                                <th style="width: 120px;" class="text-center">Aksi LPM</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php 
                            $dataForRows = [
                                'documents' => $documents,
                                'startNum'  => $startNum ?? 1
                            ];
                            extract($dataForRows);
                            include ROOT_PATH . '/views/super_admin/monitoring_lalulintas_rows.php'; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: UNIT BELUM UPLOAD DOKUMEN -->
        <div class="tab-pane fade" id="traffic-deficit" role="tabpanel">
            <div class="row g-4">
                <!-- Fakultas Belum Upload -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 p-3.5 p-md-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold text-dark-blue mb-0">
                                    <i class="fas fa-building-columns text-danger me-1.5"></i> Fakultas Belum Ada Dokumen
                                </h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Periode Tahun Akademik <?= htmlspecialchars($activeTA) ?></span>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5 py-1 fw-bold">
                                <?= count($fakultasBelumUpload) ?> Fakultas
                            </span>
                        </div>
                        <?php if (empty($fakultasBelumUpload)): ?>
                            <div class="text-center py-4 text-success small">
                                <i class="fas fa-circle-check fa-2x mb-2 d-block"></i>
                                Seluruh Fakultas telah mengunggah minimal 1 dokumen untuk periode ini!
                            </div>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($fakultasBelumUpload as $f): ?>
                                    <li class="list-group-item px-0 py-2.5 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($f['kode_fakultas']) ?></span>
                                            <span class="fw-semibold text-dark small"><?= htmlspecialchars($f['nama_fakultas']) ?></span>
                                        </div>
                                        <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.7rem;">0 Berkas</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Prodi Belum Upload -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-3.5 p-md-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold text-dark-blue mb-0">
                                    <i class="fas fa-graduation-cap text-danger me-1.5"></i> Program Studi Belum Ada Dokumen
                                </h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Periode Tahun Akademik <?= htmlspecialchars($activeTA) ?></span>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5 py-1 fw-bold">
                                <?= count($prodiBelumUpload) ?> Prodi
                            </span>
                        </div>
                        <?php if (empty($prodiBelumUpload)): ?>
                            <div class="text-center py-4 text-success small">
                                <i class="fas fa-circle-check fa-2x mb-2 d-block"></i>
                                Seluruh Program Studi telah mengunggah minimal 1 dokumen untuk periode ini!
                            </div>
                        <?php else: ?>
                            <div class="table-responsive" style="max-height: 450px;">
                                <table class="table table-hover align-middle table-sm mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Program Studi</th>
                                            <th>Fakultas</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prodiBelumUpload as $p): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark" style="font-size: 0.82rem;"><?= htmlspecialchars($p['nama_prodi']) ?></div>
                                                    <span class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($p['jenjang'] ?? 'S1') ?> • Kode <?= htmlspecialchars($p['kode_prodi']) ?></span>
                                                </td>
                                                <td class="text-muted small" style="font-size: 0.78rem;">
                                                    <?= htmlspecialchars($p['nama_fakultas']) ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                                        Belum Upload
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                
                <!-- Pagination Footer Bar -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 border-top mt-3" id="paginationContainer">
                    <div class="text-muted small">
                        Menampilkan <span class="fw-semibold text-dark" id="infoStart"><?= $startNum ?? 0 ?></span> - 
                        <span class="fw-semibold text-dark" id="infoEnd"><?= $endNum ?? 0 ?></span> dari 
                        <span class="fw-semibold text-dark" id="infoTotal"><?= $totalFiltered ?? count($documents) ?></span> berkas dokumen
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;" onchange="changePerPage(this.value)">
                            <option value="25" <?= ($perPage ?? 50) == 25 ? 'selected' : '' ?>>25 per hal</option>
                            <option value="50" <?= ($perPage ?? 50) == 50 ? 'selected' : '' ?>>50 per hal</option>
                            <option value="100" <?= ($perPage ?? 50) == 100 ? 'selected' : '' ?>>100 per hal</option>
                        </select>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0" id="paginationUl">
                                <?php 
                                $tp = $totalPages ?? 1;
                                $cp = $page ?? 1;
                                ?>
                                <li class="page-item <?= ($cp <= 1) ? 'disabled' : '' ?>">
                                    <button class="page-link" onclick="goToPage(<?= $cp - 1 ?>)" aria-label="Previous">
                                        <i class="fas fa-chevron-left small"></i>
                                    </button>
                                </li>
                                <?php for($p = max(1, $cp - 2); $p <= min($tp, $cp + 2); $p++): ?>
                                    <li class="page-item <?= ($p == $cp) ? 'active' : '' ?>">
                                        <button class="page-link" onclick="goToPage(<?= $p ?>)"><?= $p ?></button>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= ($cp >= $tp) ? 'disabled' : '' ?>">
                                    <button class="page-link" onclick="goToPage(<?= $cp + 1 ?>)" aria-label="Next">
                                        <i class="fas fa-chevron-right small"></i>
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
</div>
            </div>
        </div>
    </div>
</div>
<script>
let currentPage = <?= $page ?? 1 ?>;
let currentPerPage = <?= $perPage ?? 50 ?>;
let totalPages = <?= $totalPages ?? 1 ?>;

function goToPage(p) {
    if (p < 1 || (totalPages > 0 && p > totalPages) || p === currentPage) return;
    currentPage = p;
    fetchLaluLintasData();
}

function changePerPage(limit) {
    currentPerPage = parseInt(limit);
    currentPage = 1;
    fetchLaluLintasData();
}

function fetchLaluLintasData() {
    const url = new URL(window.location.href);
    url.searchParams.set('ajax', '1');
    url.searchParams.set('page', currentPage);
    url.searchParams.set('per_page', currentPerPage);

    const tbody = document.getElementById('tableBody');
    if (tbody) {
        tbody.style.opacity = '0.5';
    }

    fetch(url.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (tbody) {
            tbody.innerHTML = data.html;
            tbody.style.opacity = '1';
        }
        currentPage = data.page;
        totalPages = data.totalPages;

        const infoStart = document.getElementById('infoStart');
        const infoEnd = document.getElementById('infoEnd');
        const infoTotal = document.getElementById('infoTotal');
        if (infoStart) infoStart.textContent = data.startNum;
        if (infoEnd) infoEnd.textContent = data.endNum;
        if (infoTotal) infoTotal.textContent = data.totalFiltered;

        renderPaginationUl(data.page, data.totalPages);
    })
    .catch(err => {
        console.error('Fetch error:', err);
        if (tbody) tbody.style.opacity = '1';
    });
}

function renderPaginationUl(cp, tp) {
    const ul = document.getElementById('paginationUl');
    if (!ul) return;
    let html = '';
    
    html += `<li class="page-item ${cp <= 1 ? 'disabled' : ''}">
        <button class="page-link" onclick="goToPage(${cp - 1})" aria-label="Previous">
            <i class="fas fa-chevron-left small"></i>
        </button>
    </li>`;

    const start = Math.max(1, cp - 2);
    const end = Math.min(tp, cp + 2);
    for (let p = start; p <= end; p++) {
        html += `<li class="page-item ${p === cp ? 'active' : ''}">
            <button class="page-link" onclick="goToPage(${p})">${p}</button>
        </li>`;
    }

    html += `<li class="page-item ${cp >= tp ? 'disabled' : ''}">
        <button class="page-link" onclick="goToPage(${cp + 1})" aria-label="Next">
            <i class="fas fa-chevron-right small"></i>
        </button>
    </li>`;

    ul.innerHTML = html;
}
</script>


<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
