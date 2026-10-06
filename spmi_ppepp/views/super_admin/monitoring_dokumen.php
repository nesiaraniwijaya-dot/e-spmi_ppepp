<?php
/**
 * Super Admin: Monitoring Unggah Dokumen PPEPP oleh Admin Prodi
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-primary bg-opacity-10 text-primary small fw-bold mb-2">
                <i class="fas fa-file-circle-check"></i> Monitoring Dokumen PPEPP
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Monitoring Unggah Dokumen Program Studi</h3>
            <p class="text-muted small mb-0">Pemantauan riwayat kapan Admin Prodi mengunggah, memperbarui, atau mengarsipkan dokumen 5 siklus PPEPP.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Dashboard Utama
            </a>
        </div>
    </div>

    <!-- Stat Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-cloud-arrow-up"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark-blue"><?= number_format($stats['total_uploads']) ?></div>
                    <div class="small text-muted fw-semibold">Total Unggahan Baru</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark-blue"><?= number_format($stats['this_month']) ?></div>
                    <div class="small text-muted fw-semibold">Unggahan Bulan Ini</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark-blue"><?= number_format($stats['total_updates']) ?></div>
                    <div class="small text-muted fw-semibold">Pembaruan Dokumen</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-box-archive"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold text-dark-blue"><?= number_format($stats['total_archives']) ?></div>
                    <div class="small text-muted fw-semibold">Dokumen Diarsipkan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div class="fw-bold text-dark-blue" style="font-size: 0.95rem;">
                <i class="fas fa-filter text-primary me-2"></i> Filter Aktivitas Unggah Dokumen
            </div>
            <?php if (!empty(array_filter($filters))): ?>
                <a href="<?= base_url('admin/monitoring-dokumen') ?>" class="small text-danger text-decoration-none fw-semibold">
                    <i class="fas fa-xmark me-1"></i> Reset Filter
                </a>
            <?php endif; ?>
        </div>

        <form action="<?= base_url('admin/monitoring-dokumen') ?>" method="GET">
            <div class="row g-3 align-items-end">
                <!-- Fakultas -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Fakultas</label>
                    <select name="fakultas_id" class="form-select form-select-sm bg-light" id="filterFakultas" onchange="filterProdiByFakultas()">
                        <option value="">-- Semua Fakultas --</option>
                        <?php foreach ($fakultasList as $f): ?>
                            <option value="<?= $f['id'] ?>" <?= ($filters['fakultas_id'] == $f['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['nama_fakultas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Program Studi -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Program Studi</label>
                    <select name="prodi_id" class="form-select form-select-sm bg-light" id="filterProdi">
                        <option value="">-- Semua Program Studi --</option>
                        <?php foreach ($prodiList as $p): ?>
                            <option value="<?= $p['id'] ?>" data-fakultas="<?= $p['fakultas_id'] ?>" <?= ($filters['prodi_id'] == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nama_prodi']) ?> (<?= htmlspecialchars($p['jenjang']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Siklus PPEPP -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Siklus PPEPP</label>
                    <select name="siklus" class="form-select form-select-sm bg-light">
                        <option value="">-- Semua Siklus --</option>
                        <option value="penetapan" <?= ($filters['siklus'] === 'penetapan') ? 'selected' : '' ?>>Penetapan (P1)</option>
                        <option value="pelaksanaan" <?= ($filters['siklus'] === 'pelaksanaan') ? 'selected' : '' ?>>Pelaksanaan (P2)</option>
                        <option value="evaluasi" <?= ($filters['siklus'] === 'evaluasi') ? 'selected' : '' ?>>Evaluasi (E)</option>
                        <option value="pengendalian" <?= ($filters['siklus'] === 'pengendalian') ? 'selected' : '' ?>>Pengendalian (P3)</option>
                        <option value="peningkatan" <?= ($filters['siklus'] === 'peningkatan') ? 'selected' : '' ?>>Peningkatan (P4)</option>
                    </select>
                </div>

                <!-- Jenis Aksi -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Jenis Aktivitas</label>
                    <select name="aksi" class="form-select form-select-sm bg-light">
                        <option value="">-- Semua Aktivitas --</option>
                        <option value="CREATE" <?= ($filters['aksi'] === 'CREATE') ? 'selected' : '' ?>>Unggah Baru (CREATE)</option>
                        <option value="UPDATE" <?= ($filters['aksi'] === 'UPDATE') ? 'selected' : '' ?>>Perbarui Dokumen (UPDATE)</option>
                        <option value="DELETE" <?= ($filters['aksi'] === 'DELETE') ? 'selected' : '' ?>>Arsipkan (DELETE)</option>
                        <option value="RESTORE" <?= ($filters['aksi'] === 'RESTORE') ? 'selected' : '' ?>>Pulihkan (RESTORE)</option>
                    </select>
                </div>

                <!-- Tombol Submit -->
                <div class="col-lg-2 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 shadow-sm bg-scu-blue border-0 py-1.5">
                        <i class="fas fa-magnifying-glass me-1"></i> Filter
                    </button>
                    <a href="<?= base_url('admin/monitoring-dokumen') ?>" class="btn btn-light btn-sm rounded-pill border px-3 py-1.5" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            </div>

            <!-- Rentang Tanggal -->
            <div class="row g-3 mt-1 pt-2 border-top">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-semibold small text-secondary">Dari Tanggal Unggah</label>
                    <input type="date" name="start_date" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($filters['start_date']) ?>">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-semibold small text-secondary">Sampai Tanggal Unggah</label>
                    <input type="date" name="end_date" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($filters['end_date']) ?>">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <span class="small text-muted">
                        <i class="fas fa-circle-info text-primary me-1"></i> Filter waktu membantu memantau ketepatan waktu unggah dokumen mutu prodi sesuai kalender SPMI.
                    </span>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card: Riwayat Unggah Dokumen -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark-blue mb-0">
                <i class="fas fa-list-check text-primary me-2"></i> Log Aktivitas Unggah Dokumen PPEPP (<?= count($activities) ?> data)
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 170px;">Waktu Unggah</th>
                        <th>Program Studi &amp; Fakultas</th>
                        <th>Pengunggah (Admin Prodi)</th>
                        <th>Nama Dokumen Mutu</th>
                        <th class="text-center">Siklus</th>
                        <th class="text-center">Aktivitas</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activities)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-folder-open fa-3x opacity-25"></i></div>
                                <div>Tidak ditemukan riwayat aktivitas dokumen sesuai filter yang dipilih.</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activities as $act): ?>
                            <?php
                            $badgeAksi = match($act['aksi']) {
                                'CREATE' => ['bg-success-subtle text-success border-success', 'Unggah Baru'],
                                'UPDATE' => ['bg-warning-subtle text-warning-emphasis border-warning', 'Pembaruan'],
                                'DELETE' => ['bg-danger-subtle text-danger border-danger', 'Arsipkan'],
                                'RESTORE' => ['bg-info-subtle text-info border-info', 'Pulihkan'],
                                default => ['bg-secondary-subtle text-secondary border-secondary', $act['aksi']]
                            };
                            $docName = $act['doc_nama'] ?: ($act['target_name'] ?: 'Dokumen PPEPP #' . $act['target_id']);
                            $siklusVal = $act['doc_siklus'] ?: '';
                            if (!$siklusVal && !empty($act['new_values'])) {
                                $nv = json_decode($act['new_values'], true);
                                $siklusVal = $nv['siklus'] ?? '';
                            }
                            ?>
                            <tr>
                                <!-- Waktu Unggah Detail -->
                                <td>
                                    <div class="fw-bold text-dark-blue text-nowrap">
                                        <?= date('d M Y', strtotime($act['created_at'])) ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        <i class="fas fa-clock text-primary me-1"></i><?= date('H:i:s', strtotime($act['created_at'])) ?> WIB
                                    </div>
                                </td>

                                <!-- Program Studi & Fakultas -->
                                <td>
                                    <div class="fw-bold text-dark-blue">
                                        <?= htmlspecialchars($act['nama_prodi'] ?: ($act['prodi_name'] ?: '-')) ?>
                                        <?php if (!empty($act['jenjang'])): ?>
                                            <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;"><?= htmlspecialchars($act['jenjang']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        <?= htmlspecialchars($act['nama_fakultas'] ?: '-') ?>
                                    </div>
                                </td>

                                <!-- Pengunggah -->
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="fas fa-user-circle text-secondary"></i>
                                        <span class="fw-semibold text-secondary"><?= htmlspecialchars($act['user_name']) ?></span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($act['ip_address'] ?? '127.0.0.1') ?></div>
                                </td>

                                <!-- Nama Dokumen Mutu -->
                                <td>
                                    <div class="fw-bold text-dark-blue" style="max-width: 320px;">
                                        <?= htmlspecialchars($docName) ?>
                                    </div>
                                    <?php if (!empty($act['nama_bidang'])): ?>
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">
                                            <i class="fas fa-layer-group me-1"></i><?= htmlspecialchars($act['nama_bidang']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Siklus PPEPP -->
                                <td class="text-center">
                                    <?php if ($siklusVal): ?>
                                        <?= siklus_badge($siklusVal) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Jenis Aktivitas -->
                                <td class="text-center">
                                    <span class="badge border <?= $badgeAksi[0] ?> px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                        <?= $badgeAksi[1] ?>
                                    </span>
                                </td>

                                <!-- Aksi Cepat -->
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Lihat File PDF / Link -->
                                        <?php if (!empty($act['file_path'])): ?>
                                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1"
                                                    data-bs-toggle="modal" data-bs-target="#pdfPreviewModal"
                                                    onclick="previewPdf('<?= base_url($act['file_path']) ?>', '<?= htmlspecialchars($docName, ENT_QUOTES) ?>')"
                                                    title="Pratinjau File PDF Dokumen">
                                                <i class="fas fa-file-pdf text-danger"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php 
                                        $actLinks = parse_external_links($act['external_link'] ?? '');
                                        $actLinkCount = count($actLinks);
                                        if ($actLinkCount === 1): 
                                            $actSingle = $actLinks[0];
                                        ?>
                                            <a href="<?= htmlspecialchars($actSingle['url']) ?>" target="_blank" rel="noopener noreferrer"
                                               class="btn btn-xs btn-outline-info rounded-pill px-2 py-1"
                                               title="<?= htmlspecialchars($actSingle['narasi'] ?: 'Buka Tautan Google Drive / Cloud') ?>">
                                                <i class="fab fa-google-drive"></i>
                                            </a>
                                        <?php elseif ($actLinkCount > 1): ?>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-xs btn-outline-info rounded-pill px-2 py-1 dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                        title="<?= $actLinkCount ?> Tautan Google Drive">
                                                    <i class="fab fa-google-drive me-1"></i><?= $actLinkCount ?>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 240px; font-size: 0.8rem;">
                                                    <li class="dropdown-header text-uppercase fw-bold text-muted" style="font-size: 0.68rem;">
                                                        <i class="fab fa-google-drive me-1 text-primary"></i>Link Dokumen (<?= $actLinkCount ?>)
                                                    </li>
                                                    <?php foreach ($actLinks as $lIdx => $lItem): ?>
                                                        <li>
                                                            <a class="dropdown-item py-1.5 px-3 d-flex flex-column align-items-start" href="<?= htmlspecialchars($lItem['url']) ?>" target="_blank" rel="noopener noreferrer">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-external-link-alt text-info me-2"></i>
                                                                    <span class="text-truncate" style="max-width: 150px;">Link <?= $lIdx + 1 ?></span>
                                                                </div>
                                                                <?php if (!empty($lItem['narasi'])): ?>
                                                                    <div class="small text-muted text-truncate mt-0.5" style="max-width: 200px; font-size: 0.7rem;">
                                                                        <?= htmlspecialchars($lItem['narasi']) ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?= render_sub_standar_badges($lItem['sub_bidang_ids'] ?? null) ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Tombol Detail Log / Diff -->
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1"
                                                onclick="showActivityDetail(<?= htmlspecialchars(json_encode($act), ENT_QUOTES, 'UTF-8') ?>)"
                                                title="Detail Snapshot Perubahan">
                                            <i class="fas fa-circle-info"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Detail Snapshot Perubahan Data -->
<div class="modal fade" id="activityDetailModal" tabindex="-1" aria-labelledby="activityDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark-blue text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-circle-info text-warning"></i>
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="activityDetailModalLabel">
                        Detail Aktivitas Dokumen PPEPP
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3 p-3 bg-light rounded-3 small">
                    <div class="col-sm-6">
                        <div class="text-muted">Waktu Pencatatan:</div>
                        <div class="fw-bold text-dark-blue" id="modalWaktu">-</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted">Pengunggah / Admin:</div>
                        <div class="fw-bold text-dark-blue" id="modalPengguna">-</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted">Program Studi:</div>
                        <div class="fw-bold text-dark-blue" id="modalProdi">-</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted">Jenis Aktivitas:</div>
                        <div id="modalAksi">-</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted">Nama Dokumen Mutu:</div>
                        <div class="fw-bold text-dark-blue fs-6" id="modalDokumen">-</div>
                    </div>
                </div>

                <div class="row g-3" id="diffSection">
                    <div class="col-md-6">
                        <div class="fw-bold text-danger mb-2 small"><i class="fas fa-clock-rotate-left me-1"></i> Data Sebelum Perubahan (Old):</div>
                        <pre id="modalOldValues" class="bg-light p-3 rounded-3 border small" style="max-height: 220px; overflow-y: auto; font-family: monospace;"></pre>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-bold text-success mb-2 small"><i class="fas fa-check-circle me-1"></i> Data Setelah Perubahan (New):</div>
                        <pre id="modalNewValues" class="bg-light p-3 rounded-3 border small" style="max-height: 220px; overflow-y: auto; font-family: monospace;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
// Filter prodi dropdown based on selected fakultas
function filterProdiByFakultas() {
    const fakSelect = document.getElementById('filterFakultas');
    const prodiSelect = document.getElementById('filterProdi');
    const selectedFakId = fakSelect.value;

    for (let i = 0; i < prodiSelect.options.length; i++) {
        const opt = prodiSelect.options[i];
        if (!opt.value) continue;
        const fakId = opt.getAttribute('data-fakultas');
        if (!selectedFakId || fakId === selectedFakId) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    }
}

// Show Activity Snapshot Detail Modal
function showActivityDetail(data) {
    document.getElementById('modalWaktu').textContent = data.created_at + ' WIB';
    document.getElementById('modalPengguna').textContent = data.user_name + ' (IP: ' + (data.ip_address || '127.0.0.1') + ')';
    document.getElementById('modalProdi').textContent = (data.nama_prodi || data.prodi_name || '-') + (data.nama_fakultas ? ' - ' + data.nama_fakultas : '');
    document.getElementById('modalDokumen').textContent = data.doc_nama || data.target_name || ('Dokumen #' + data.target_id);

    const aksiBadge = document.getElementById('modalAksi');
    aksiBadge.innerHTML = '<span class="badge bg-primary">' + data.aksi + '</span>';

    // Parse JSON values
    let oldObj = null;
    let newObj = null;
    try { if (data.old_values) oldObj = JSON.parse(data.old_values); } catch (e) { oldObj = data.old_values; }
    try { if (data.new_values) newObj = JSON.parse(data.new_values); } catch (e) { newObj = data.new_values; }

    document.getElementById('modalOldValues').textContent = oldObj ? JSON.stringify(oldObj, null, 2) : '(Tidak ada data sebelumnya / Unggah Baru)';
    document.getElementById('modalNewValues').textContent = newObj ? JSON.stringify(newObj, null, 2) : '(Data dihapus / diarsipkan)';

    new bootstrap.Modal(document.getElementById('activityDetailModal')).show();
}

// PDF Viewer trigger
function previewPdf(url, title) {
    const modalTitle = document.getElementById('pdfPreviewModalLabel');
    const modalFrame = document.getElementById('pdfViewerFrame');
    const downloadBtn = document.getElementById('pdfDirectDownloadBtn');
    if (modalTitle) modalTitle.textContent = title || 'Pratinjau Dokumen Mutu';
    if (modalFrame) modalFrame.src = url;
    if (downloadBtn) downloadBtn.href = url;
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
