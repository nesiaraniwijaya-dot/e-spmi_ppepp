<?php
/**
 * View: LPM Monitoring Kinerja Seluruh Unit (Dekanat & Program Studi)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

function getLpmCycleBadgeHtml(string $cycle, int $count): string {
    $map = [
        'penetapan'   => ['code' => 'P1', 'name' => 'Penetapan'],
        'pelaksanaan' => ['code' => 'P2', 'name' => 'Pelaksanaan'],
        'evaluasi'    => ['code' => 'E',  'name' => 'Evaluasi'],
        'pengendalian'=> ['code' => 'P3', 'name' => 'Pengendalian'],
        'peningkatan' => ['code' => 'P4', 'name' => 'Peningkatan'],
    ];
    $info = $map[$cycle] ?? ['code' => strtoupper(substr($cycle, 0, 2)), 'name' => ucfirst($cycle)];
    if ($count > 0) {
        return '<span class="badge rounded-pill px-2 py-1 shadow-2xs text-white d-inline-flex align-items-center gap-1" style="background:#059669; font-size:0.68rem;" title="' . htmlspecialchars($info['name']) . ': ' . $count . ' Dokumen">' .
               '<i class="fas fa-check-circle"></i> ' . $info['code'] . ' (' . $count . ')</span>';
    } else {
        return '<span class="badge rounded-pill px-2 py-1 shadow-2xs d-inline-flex align-items-center gap-1" style="background:#FEE2E2; color:#DC2626; border:1px dashed #F87171; font-size:0.68rem;" title="' . htmlspecialchars($info['name']) . ': Belum ada dokumen!">' .
               '<i class="fas fa-circle-exclamation"></i> ' . $info['code'] . ' (0)</span>';
    }
}
?>

<div class="container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div class="flex-grow-1 min-w-0" style="max-width: 780px;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill small fw-bold mb-2" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE;">
                <i class="fas fa-chart-line text-primary"></i>
                <span>Lembaga Penjaminan Mutu &bull; Monitoring Terpusat Universitas</span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Monitoring Kinerja Unit (Dekanat &amp; Program Studi)</h3>
            <p class="text-muted small mb-0">Evaluasi kepatuhan dan implementasi siklus mutu SPMI PPEPP untuk seluruh Dekanat Fakultas dan Program Studi di tingkat universitas. Skor dihitung 100% otomatis berbasis data dokumen riil.</p>
        </div>
    </div>

    <!-- Filter & Kontrol Pencarian Komprehensif -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <form action="<?= base_url('admin/monitoring-kinerja-unit') ?>" method="GET" class="row g-2 align-items-center">
            <!-- Filter Fakultas -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-building-columns me-1"></i> Fakultas</label>
                <select name="fakultas_id" class="form-select form-select-sm rounded-3">
                    <option value="all">Semua Fakultas</option>
                    <?php foreach ($fakultasList as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= ((string)$currentFakultas === (string)$f['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['nama_fakultas']) ?><?= !empty($f['kode_fakultas']) ? ' (' . htmlspecialchars($f['kode_fakultas']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Tahun Akademik -->
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="far fa-calendar-alt me-1"></i> Tahun Akademik</label>
                <select name="tahun_akademik" class="form-select form-select-sm rounded-3">
                    <option value="Semua" <?= $currentTa === 'Semua' ? 'selected' : '' ?>>Semua Periode</option>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= htmlspecialchars($ay) ?>" <?= $currentTa === $ay ? 'selected' : '' ?>>TA <?= htmlspecialchars($ay) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Tipe Unit -->
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-layer-group me-1"></i> Satuan Kerja</label>
                <select name="target_type" class="form-select form-select-sm rounded-3">
                    <option value="all" <?= $currentType === 'all' ? 'selected' : '' ?>>Semua Unit</option>
                    <option value="fakultas" <?= $currentType === 'fakultas' ? 'selected' : '' ?>>Dekanat Fakultas</option>
                    <option value="prodi" <?= $currentType === 'prodi' ? 'selected' : '' ?>>Program Studi</option>
                </select>
            </div>

            <!-- Filter Predikat -->
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-award me-1"></i> Predikat</label>
                <select name="predikat" class="form-select form-select-sm rounded-3">
                    <option value="all" <?= $currentPredikat === 'all' ? 'selected' : '' ?>>Semua Predikat</option>
                    <option value="Sangat Baik" <?= $currentPredikat === 'Sangat Baik' ? 'selected' : '' ?>>Sangat Baik</option>
                    <option value="Baik" <?= $currentPredikat === 'Baik' ? 'selected' : '' ?>>Baik</option>
                    <option value="Cukup" <?= $currentPredikat === 'Cukup' ? 'selected' : '' ?>>Cukup</option>
                    <option value="Perlu Pembinaan" <?= $currentPredikat === 'Perlu Pembinaan' ? 'selected' : '' ?>>Perlu Pembinaan</option>
                </select>
            </div>

            <!-- Input Pencarian -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Cari Nama Unit</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control rounded-start-3" placeholder="Nama Prodi / Fakultas..." value="<?= htmlspecialchars($currentSearch) ?>">
                    <button type="submit" class="btn btn-primary rounded-end-3 px-3">
                        <i class="fas fa-filter"></i>
                    </button>
                    <?php if ($currentFakultas !== 'all' || $currentTa !== 'Semua' || $currentType !== 'all' || $currentPredikat !== 'all' || $currentSearch !== ''): ?>
                        <a href="<?= base_url('admin/monitoring-kinerja-unit') ?>" class="btn btn-outline-secondary ms-1 rounded-3" title="Reset Filter">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 44px; height: 44px; background: #3B82F6;">
                        <i class="fas fa-building-columns fa-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Total Unit Dievaluasi</div>
                        <div class="fs-4 fw-bold text-dark-blue"><?= $totalUnits ?> Unit</div>
                        <div class="text-muted small" style="font-size: 0.7rem;">Dekanat &amp; Seluruh Prodi</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 44px; height: 44px; background: #10B981;">
                        <i class="fas fa-gauge-high fa-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Rata-Rata Mutu Univ</div>
                        <div class="fs-4 fw-bold text-success"><?= $avgScore ?> <span class="fs-6 text-muted">/ 100</span></div>
                        <div class="text-muted small" style="font-size: 0.7rem;"><?= $predikatCounts['Sangat Baik'] ?> Sangat Baik &bull; <?= $predikatCounts['Baik'] ?> Baik</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 44px; height: 44px; background: #EF4444;">
                        <i class="fas fa-triangle-exclamation fa-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Siklus Belum Lengkap</div>
                        <div class="fs-4 fw-bold text-danger"><?= $emptyCyclesCount ?> Unit</div>
                        <div class="text-muted small" style="font-size: 0.7rem;">Ada siklus 0 dokumen</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 44px; height: 44px; background: #8B5CF6;">
                        <i class="fas fa-paper-plane fa-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Rapor Diteruskan GPM</div>
                        <div class="fs-4 fw-bold text-purple"><?= $forwardedCount ?> Unit</div>
                        <div class="text-muted small" style="font-size: 0.7rem;">Telah diterima oleh unit</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Leaderboard & Rapor Kinerja Unit -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark-blue">
                <i class="fas fa-ranking-star text-warning me-2"></i> Rapor Kinerja Mutu Seluruh Unit
            </h5>
            <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill small">
                Menampilkan <?= count($results) ?> Satuan Kerja
            </span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-muted" style="font-size: 0.78rem; text-transform: uppercase;">
                    <tr>
                        <th class="ps-4" style="width: 50px;">Rank</th>
                        <th style="min-width: 220px;">Unit Kerja &amp; Pimpinan</th>
                        <th style="min-width: 130px;">Fakultas</th>
                        <th style="min-width: 170px;">5 Siklus PPEPP</th>
                        <th class="text-center" style="width: 110px;">Skor Mutu</th>
                        <th class="text-center" style="width: 120px;">Predikat</th>
                        <th class="text-center" style="width: 120px;">Status Rapor</th>
                        <th style="min-width: 180px;">Catatan LPM / GPM</th>
                        <th class="text-end pe-4" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0 fw-semibold">Tidak ada data unit yang sesuai dengan filter pencarian.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results as $idx => $p): 
                            $rank = $idx + 1;
                            $rankBadge = ($rank === 1) ? 'bg-warning text-dark' : (($rank === 2) ? 'bg-secondary text-white' : (($rank === 3) ? 'bg-danger text-white' : 'bg-light text-dark border'));
                            $isFakultas = ($p['target_type'] === 'fakultas');
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold">
                                <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center <?= $rankBadge ?>" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <?= $rank ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 p-1.5 flex-shrink-0 <?= $isFakultas ? 'bg-indigo-subtle text-primary' : 'bg-light text-secondary' ?>" style="font-size: 1rem;">
                                        <i class="fas <?= $isFakultas ? 'fa-building-columns' : 'fa-graduation-cap' ?>"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark-blue text-truncate">
                                            <?= htmlspecialchars($p['unit_name']) ?>
                                            <span class="badge <?= $isFakultas ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary' ?> ms-1" style="font-size: 0.65rem;">
                                                <?= $isFakultas ? 'Dekanat' : 'Prodi' ?>
                                            </span>
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            <i class="fas fa-user-tie me-1"></i> <?= htmlspecialchars($p['pimpinan_name']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small" title="<?= htmlspecialchars($p['fakultas_name']) ?>">
                                    <?= htmlspecialchars($p['fakultas_name']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <?= getLpmCycleBadgeHtml('penetapan', $p['cycle_counts']['penetapan'] ?? 0) ?>
                                    <?= getLpmCycleBadgeHtml('pelaksanaan', $p['cycle_counts']['pelaksanaan'] ?? 0) ?>
                                    <?= getLpmCycleBadgeHtml('evaluasi', $p['cycle_counts']['evaluasi'] ?? 0) ?>
                                    <?= getLpmCycleBadgeHtml('pengendalian', $p['cycle_counts']['pengendalian'] ?? 0) ?>
                                    <?= getLpmCycleBadgeHtml('peningkatan', $p['cycle_counts']['peningkatan'] ?? 0) ?>
                                </div>
                                <?php if (!empty($p['siklus_kosong'])): ?>
                                    <div class="text-danger small mt-1" style="font-size: 0.68rem;">
                                        <i class="fas fa-triangle-exclamation"></i> Kosong: <?= implode(', ', array_map('ucfirst', $p['siklus_kosong'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="fs-6 fw-bold text-dark-blue"><?= number_format($p['skor_total'], 1) ?></span>
                                <div class="text-muted small" style="font-size: 0.65rem;">Total <?= $p['total_docs'] ?> Dokumen</div>
                            </td>
                            <td class="text-center">
                                <?= $p['predikat_badge'] ?>
                            </td>
                            <td class="text-center">
                                <?php if ($p['is_forwarded']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" title="Telah diteruskan oleh GPM ke unit pada <?= htmlspecialchars($p['forwarded_at'] ?? '') ?>">
                                        <i class="fas fa-check-double me-1"></i> Diteruskan
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border px-2 py-1" title="Menunggu GPM memforward rapor ini ke unit">
                                        <i class="fas fa-clock me-1"></i> Draft GPM
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($p['catatan_lpm'])): ?>
                                    <div class="small text-truncate text-primary" style="max-width: 180px;" title="Catatan LPM: <?= htmlspecialchars($p['catatan_lpm']) ?>">
                                        <i class="fas fa-comment-dots me-1"></i> <strong>LPM:</strong> <?= htmlspecialchars($p['catatan_lpm']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($p['catatan_gpm'])): ?>
                                    <div class="small text-truncate text-secondary" style="max-width: 180px;" title="Catatan GPM: <?= htmlspecialchars($p['catatan_gpm']) ?>">
                                        <i class="fas fa-comment me-1"></i> <strong>GPM:</strong> <?= htmlspecialchars($p['catatan_gpm']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (empty($p['catatan_lpm']) && empty($p['catatan_gpm'])): ?>
                                    <span class="text-muted small fst-italic">- Belum ada catatan -</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <!-- Tombol Evaluasi / Catatan LPM -->
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-eval-lpm"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEvaluasiLpm"
                                            data-target-type="<?= $p['target_type'] ?>"
                                            data-target-id="<?= $p['target_id'] ?>"
                                            data-unit-name="<?= htmlspecialchars($p['unit_name']) ?>"
                                            data-fakultas-name="<?= htmlspecialchars($p['fakultas_name']) ?>"
                                            data-pimpinan-name="<?= htmlspecialchars($p['pimpinan_name']) ?>"
                                            data-skor="<?= $p['skor_total'] ?>"
                                            data-predikat="<?= htmlspecialchars($p['predikat']) ?>"
                                            data-catatan-lpm="<?= htmlspecialchars($p['catatan_lpm'] ?? '') ?>"
                                            data-catatan-gpm="<?= htmlspecialchars($p['catatan_gpm'] ?? '') ?>"
                                            data-siklus-kosong="<?= htmlspecialchars(implode(', ', array_map('ucfirst', $p['siklus_kosong']))) ?>"
                                            title="Beri Catatan / Masukan LPM">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- Tombol Pratinjau & Cetak Softfile -->
                                    <a href="<?= base_url('admin/monitoring-kinerja-unit/cetak/' . $p['target_type'] . '/' . $p['target_id'] . '?tahun_akademik=' . urlencode($currentTa)) ?>" 
                                       target="_blank" 
                                       class="btn btn-outline-danger" 
                                       title="Cetak / Pratinjau Softfile Rapor PDF">
                                        <i class="fas fa-print"></i>
                                    </a>
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

<!-- Modal Evaluasi & Catatan LPM -->
<div class="modal fade" id="modalEvaluasiLpm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= base_url('admin/monitoring-kinerja-unit/save') ?>" method="POST">
                <input type="hidden" name="target_type" id="modalLpmTargetType">
                <input type="hidden" name="target_id" id="modalLpmTargetId">
                <input type="hidden" name="tahun_akademik" value="<?= htmlspecialchars($currentTa) ?>">
                <input type="hidden" name="filter_fakultas" value="<?= htmlspecialchars($currentFakultas) ?>">
                <input type="hidden" name="filter_type" value="<?= htmlspecialchars($currentType) ?>">
                <input type="hidden" name="filter_predikat" value="<?= htmlspecialchars($currentPredikat) ?>">
                <input type="hidden" name="filter_q" value="<?= htmlspecialchars($currentSearch) ?>">

                <div class="modal-header bg-primary text-white border-0 py-3 px-4 rounded-top-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-clipboard-check fa-lg"></i>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Catatan &amp; Arahan Pembinaan LPM</h5>
                            <small class="text-white-50">Lembaga Penjaminan Mutu &bull; Periode: TA <?= htmlspecialchars($currentTa) ?></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Info Unit Card -->
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark-blue mb-1" id="modalLpmUnitName">-</h6>
                                <div class="text-muted small" id="modalLpmPimpinan">-</div>
                            </div>
                            <div class="text-end">
                                <span class="fs-5 fw-bold text-primary" id="modalLpmSkor">-</span>
                                <span class="text-muted small"> / 100</span>
                                <div><span class="badge bg-primary" id="modalLpmPredikat">-</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Peringatan Siklus Kosong jika ada -->
                    <div id="modalLpmEmptyAlert" class="alert alert-danger py-2 px-3 small rounded-3 d-none mb-3">
                        <i class="fas fa-triangle-exclamation me-1"></i> <strong>Siklus Belum Lengkap:</strong>
                        <span id="modalLpmEmptyCyclesText"></span>. Disarankan memberi arahan pemenuhan dokumen pada siklus tersebut.
                    </div>

                    <!-- Tampilan Catatan GPM Fakultas jika sudah ada -->
                    <div id="modalLpmGpmNoteBox" class="p-3 rounded-3 bg-warning-subtle border border-warning-subtle mb-3 d-none">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="fas fa-clipboard-user text-warning me-1"></i> Catatan Pembinaan dari GPM Fakultas:
                        </label>
                        <p class="small text-muted mb-0 fst-italic" id="modalLpmGpmNoteText">-</p>
                    </div>

                    <!-- Input Catatan LPM -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark-blue small">
                            <i class="fas fa-pen-to-square text-primary me-1"></i> Catatan &amp; Masukan Resmi LPM Universitas:
                        </label>
                        <textarea name="catatan_lpm" id="modalLpmCatatanText" class="form-control" rows="5" placeholder="Tuliskan evaluasi, masukan, rekomendasi perbaikan, atau apresiasi bagi unit terkait kepatuhan siklus mutu SPMI PPEPP..." required></textarea>
                        <div class="form-text small">Catatan ini akan tersimpan dalam database evaluasi mutu dan akan dicantumkan secara resmi pada Rapor Kinerja Unit.</div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                        <i class="fas fa-floppy-disk me-1"></i> Simpan Catatan LPM
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const evalButtons = document.querySelectorAll('.btn-eval-lpm');
    evalButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetType   = this.getAttribute('data-target-type');
            const targetId     = this.getAttribute('data-target-id');
            const unitName     = this.getAttribute('data-unit-name');
            const pimpinan     = this.getAttribute('data-pimpinan-name');
            const fakultas     = this.getAttribute('data-fakultas-name');
            const skor         = this.getAttribute('data-skor');
            const predikat     = this.getAttribute('data-predikat');
            const catatanLpm   = this.getAttribute('data-catatan-lpm');
            const catatanGpm   = this.getAttribute('data-catatan-gpm');
            const siklusKosong = this.getAttribute('data-siklus-kosong');

            document.getElementById('modalLpmTargetType').value = targetType;
            document.getElementById('modalLpmTargetId').value   = targetId;
            document.getElementById('modalLpmUnitName').textContent = unitName;
            document.getElementById('modalLpmPimpinan').innerHTML = `<i class="fas fa-user-tie me-1"></i> ${pimpinan} &bull; ${fakultas}`;
            document.getElementById('modalLpmSkor').textContent = parseFloat(skor).toFixed(1);
            document.getElementById('modalLpmPredikat').textContent = predikat;
            document.getElementById('modalLpmCatatanText').value = catatanLpm || '';

            // Handle alert siklus kosong
            const alertEmpty = document.getElementById('modalLpmEmptyAlert');
            const textEmpty  = document.getElementById('modalLpmEmptyCyclesText');
            if (siklusKosong && siklusKosong.trim() !== '') {
                alertEmpty.classList.remove('d-none');
                textEmpty.textContent = siklusKosong;
            } else {
                alertEmpty.classList.add('d-none');
            }

            // Handle Catatan GPM Box
            const gpmBox = document.getElementById('modalLpmGpmNoteBox');
            const gpmText = document.getElementById('modalLpmGpmNoteText');
            if (catatanGpm && catatanGpm.trim() !== '') {
                gpmBox.classList.remove('d-none');
                gpmText.textContent = catatanGpm;
            } else {
                gpmBox.classList.add('d-none');
            }
        });
    });
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/admin_footer.php'; ?>
