<?php
/**
 * Super Admin & LPM: Monitoring Kinerja Otomatis Anggota GPM (Eksklusif Akses LPM)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

function formatHoursToDays(?float $hours): string {
    if ($hours === null || $hours <= 0) return '-';
    if ($hours < 24) return round($hours, 1) . ' Jam';
    $days = round($hours / 24, 1);
    return "{$days} Hari";
}

$predikatStyleMap = [
    'success'   => ['bg' => '#ECFDF5', 'border' => '#6EE7B7', 'text' => '#047857', 'bar' => 'linear-gradient(90deg, #059669 0%, #10B981 100%)', 'icon' => 'fa-award'],
    'primary'   => ['bg' => '#EFF6FF', 'border' => '#93C5FD', 'text' => '#1D4ED8', 'bar' => 'linear-gradient(90deg, #1E3A8A 0%, #3B82F6 100%)', 'icon' => 'fa-thumbs-up'],
    'warning'   => ['bg' => '#FFFBEB', 'border' => '#FCD34D', 'text' => '#B45309', 'bar' => 'linear-gradient(90deg, #D97706 0%, #F59E0B 100%)', 'icon' => 'fa-triangle-exclamation'],
    'danger'    => ['bg' => '#FEF2F2', 'border' => '#FCA5A5', 'text' => '#B91C1C', 'bar' => 'linear-gradient(90deg, #DC2626 0%, #EF4444 100%)', 'icon' => 'fa-circle-exclamation'],
    'secondary' => ['bg' => '#F8FAFC', 'border' => '#CBD5E1', 'text' => '#475569', 'bar' => 'linear-gradient(90deg, #64748B 0%, #94A3B8 100%)', 'icon' => 'fa-hourglass-half'],
];
?>

<div class="container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div class="flex-grow-1 min-w-0" style="max-width: 680px;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill small fw-bold mb-2" style="background: #EEF2FF; color: #1E3A8A; border: 1px solid #C7D2FE;">
                <i class="fas fa-shield-halved text-primary"></i>
                <span>Gugus Penjaminan Mutu (GPM) &bull; Skor Otomatis LPM</span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Monitoring Kinerja Otomatis GPM</h3>
            <p class="text-muted small mb-0">Evaluasi kinerja anggota GPM fakultas secara otomatis berbasis Akurasi Lolos LPM, ketuntasan antrean &amp; penerusan catatan LPM, dan kecepatan respon penelaahan. Data skor ini hanya tampil untuk role LPM.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap flex-sm-nowrap flex-shrink-0">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm text-nowrap">
                <i class="fas fa-user-plus"></i>
                <span>Kelola Akun GPM</span>
            </a>
            <a href="<?= base_url('admin/monitoring-dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-3 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-2 text-nowrap">
                <i class="fas fa-file-waveform"></i>
                <span>Monitoring Dokumen</span>
            </a>
        </div>
    </div>

    <!-- Summary KPIs & Filter Toolbar Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-stretch align-items-xl-center gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="gpm-kpi-chip" style="background: #EEF2FF; color: #1E3A8A; border-color: #C7D2FE;">
                    <i class="fas fa-users text-primary"></i>
                    <span><?= (int)($totalGpm ?? count($gpmMembers)) ?> Anggota GPM</span>
                </span>
                <span class="gpm-kpi-chip" style="background: #ECFDF5; color: #047857; border-color: #A7F3D0;">
                    <i class="fas fa-check-double"></i>
                    <span><?= (int)($totalReviewedAll ?? 0) ?> Aksi Telaah &amp; Penerusan</span>
                </span>
                <span class="gpm-kpi-chip" style="background: #FFFBEB; color: #B45309; border-color: #FDE68A;">
                    <i class="fas fa-hourglass-half"></i>
                    <span><?= (int)($totalPendingAll ?? 0) ?> Antrean Pending</span>
                    <?php if (!empty($totalPendingRelayAll)): ?>
                        <span class="gpm-kpi-subbadge" style="background:#EA580C; color:#FFFFFF;">
                            <?= (int)$totalPendingRelayAll ?> Belum Diteruskan
                        </span>
                    <?php endif; ?>
                </span>
                <span class="gpm-kpi-chip" style="background: #FEF2F2; color: #B91C1C; border-color: #FECACA;">
                    <i class="fas fa-arrow-rotate-left"></i>
                    <span><?= (int)($totalLpmReturnedAll ?? 0) ?>x Dikembalikan LPM</span>
                </span>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center flex-shrink-0">
                <select id="filterPredikatSelect" class="form-select form-select-sm rounded-3" style="min-width: 195px;" onchange="applyGpmFilters()">
                    <option value="all">Semua Predikat &amp; Status</option>
                    <option value="pending_relay">Ada Catatan LPM Belum Diteruskan</option>
                    <option value="Sangat Baik">Predikat: Sangat Baik (&ge; 85)</option>
                    <option value="Baik">Predikat: Baik (70 - 84.9)</option>
                    <option value="Cukup">Predikat: Cukup (55 - 69.9)</option>
                    <option value="Perlu Pembinaan">Predikat: Perlu Pembinaan (&lt; 55)</option>
                    <option value="Belum Ada Data">Belum Ada Data</option>
                </select>
                <div class="input-group input-group-sm" style="min-width: 225px;">
                    <span class="input-group-text bg-light border-end-0 text-muted px-2">
                        <i class="fas fa-magnifying-glass"></i>
                    </span>
                    <input type="text" class="form-control form-control-sm border-start-0 ps-1" id="searchGpmInput"
                           placeholder="Cari nama, email, fakultas..." oninput="applyGpmFilters()">
                </div>
            </div>
        </div>

        <div class="mt-3 pt-3 border-top d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 text-muted" style="font-size: 0.75rem; line-height: 1.55;">
            <div>
                <i class="fas fa-calculator text-primary me-1"></i>
                <strong>Formula Kinerja Otomatis:</strong>
                Skor Akhir = <code>50%</code> Akurasi Lolos LPM + <code>30%</code> Ketuntasan Antrean &amp; Penerusan + <code>20%</code> Kecepatan Respon (dinormalisasi otomatis berdasarkan pilar yang memiliki data).
            </div>
            <div class="text-lg-end flex-shrink-0">
                <i class="fas fa-user-shield me-1" style="color:#6D28D9;"></i>
                Aksi akun <em>Multi-Role</em> otomatis masuk ke fakultas sasaran.
            </div>
        </div>
    </div>

    <!-- Grid Kartu Anggota GPM -->
    <?php if (empty($gpmMembers)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <div class="py-4 text-muted">
                <i class="fas fa-user-slash fa-3x mb-3 text-secondary opacity-50"></i>
                <h6 class="fw-bold text-dark">Belum Ada Anggota GPM</h6>
                <p class="small text-muted mb-0">Belum ada pengguna dengan hak akses <strong>GPM</strong> terdaftar di sistem.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4" id="gpmCardsContainer">
            <?php foreach ($gpmMembers as $m):
                $avgHours = $m['avg_relay_hours'] ?? ($m['avg_review_hours'] ?? null);
                $avgReviewStr = formatHoursToDays(isset($m['avg_review_hours']) ? (float)$m['avg_review_hours'] : null);
                $avgRelayStr = formatHoursToDays(isset($m['avg_relay_hours']) ? (float)$m['avg_relay_hours'] : null);
                $avatarUrl = (!empty($m['avatar']) && file_exists(ROOT_PATH . '/' . $m['avatar'])) ? base_url($m['avatar']) : null;
                $initials = strtoupper(substr(trim($m['name']), 0, 1));

                $predikat = $m['predikat'] ?? 'Belum Ada Data';
                $predColor = $m['predikat_color'] ?? 'secondary';
                $pStyle = $predikatStyleMap[$predColor] ?? $predikatStyleMap['secondary'];

                $combinedScore  = $m['combined_score'] ?? null;
                $autoScore      = $m['auto_score'] ?? null;
                $accuracyLpm    = $m['accuracy_lpm'] ?? null;
                $scoreAntrean   = $m['score_antrean'] ?? null;
                $scoreKecepatan = $m['score_kecepatan'] ?? null;
                $pendingRelay   = (int)($m['pending_relay_lpm'] ?? 0);
                $multiRoleCnt   = (int)($m['multi_role_count'] ?? 0);

                $cardPayload = [
                    'id'             => (int)$m['id'],
                    'name'           => $m['name'],
                    'email'          => $m['email'],
                    'nama_fakultas'  => $m['nama_fakultas'] ?? '',
                    'avatar_url'     => $avatarUrl ?? '',
                    'auto_score'     => $autoScore,
                    'combined_score' => $combinedScore,
                    'predikat'       => $predikat,
                    'predikat_color' => $predColor,
                ];
                $cardPayloadAttr = htmlspecialchars(
                    json_encode($cardPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>
                <div class="col-12 col-xl-6 gpm-card-item"
                     data-name="<?= htmlspecialchars(strtolower($m['name'])) ?>"
                     data-fakultas="<?= htmlspecialchars(strtolower($m['nama_fakultas'] ?? '')) ?>"
                     data-email="<?= htmlspecialchars(strtolower($m['email'])) ?>"
                     data-predikat="<?= htmlspecialchars($predikat) ?>"
                     data-pending-relay="<?= $pendingRelay ?>">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden transition-all gpm-card-box">
                        <!-- Top Accent Bar Colored by Predikat -->
                        <div class="w-100" style="height: 5px; background: <?= $pStyle['bar'] ?>;"></div>

                        <div class="p-3 p-md-4 d-flex flex-column h-100 gap-3">
                            <!-- Header Profil + Kotak Skor Akhir -->
                            <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-3">
                                <div class="d-flex align-items-start gap-3 min-w-0 flex-grow-1">
                                    <div class="position-relative flex-shrink-0">
                                        <?php if ($avatarUrl): ?>
                                            <img src="<?= $avatarUrl ?>" loading="lazy" decoding="async" alt="<?= htmlspecialchars($m['name']) ?>"
                                                 class="rounded-circle shadow-sm border border-2 border-white"
                                                 style="width: 54px; height: 54px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width: 54px; height: 54px; font-size: 1.25rem; background: linear-gradient(135deg, #1E3A8A, #3B82F6);">
                                                <?= $initials ?>
                                            </div>
                                        <?php endif; ?>
                                        <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                                              title="Akun GPM Aktif" style="width: 13px; height: 13px;"></span>
                                    </div>

                                    <div class="min-w-0 flex-grow-1">
                                        <h6 class="fw-bold text-dark-blue mb-1 gpm-card-name" title="<?= htmlspecialchars($m['name']) ?>">
                                            <?= htmlspecialchars($m['name']) ?>
                                        </h6>
                                        <div class="text-muted small text-truncate mb-2" style="font-size: 0.77rem;" title="<?= htmlspecialchars($m['email']) ?>">
                                            <i class="far fa-envelope me-1 text-secondary opacity-75"></i><?= htmlspecialchars($m['email']) ?>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-1 gpm-badge-row">
                                            <?php if (!empty($m['nama_fakultas'])): ?>
                                                <span class="gpm-meta-badge"
                                                      style="background:#EDE9FE; color:#6D28D9; border-color:#DDD6FE;"
                                                      title="<?= htmlspecialchars($m['nama_fakultas']) ?>">
                                                    <i class="fas fa-building-columns"></i>
                                                    <span><?= htmlspecialchars($m['nama_fakultas']) ?></span>
                                                </span>
                                            <?php else: ?>
                                                <span class="gpm-meta-badge bg-light text-muted">Belum Dipetakan</span>
                                            <?php endif; ?>

                                            <?php if ($multiRoleCnt > 0): ?>
                                                <span class="gpm-meta-badge"
                                                      style="background:#E0F2FE; color:#0369A1; border-color:#BAE6FD;"
                                                      title="Termasuk aksi review menggunakan akun Multi-Role yang memilih fakultas ini">
                                                    <i class="fas fa-user-shield"></i>
                                                    <span>Multi-Role: <?= $multiRoleCnt ?></span>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Score & Predikat Pill -->
                                <div class="gpm-score-box text-center rounded-3 flex-shrink-0"
                                     style="background: <?= $pStyle['bg'] ?>; border: 1px solid <?= $pStyle['border'] ?>; color: <?= $pStyle['text'] ?>;">
                                    <div class="fw-bold lh-1" style="font-size: 1.35rem;">
                                        <?= $combinedScore !== null ? number_format((float)$combinedScore, 1) : '-' ?>
                                    </div>
                                    <div style="font-size: 0.65rem; opacity: 0.85; font-weight: 600; margin-top: 0.15rem;">Skor Akhir</div>
                                    <div class="gpm-predikat-pill"
                                         style="background: #FFFFFF; color: <?= $pStyle['text'] ?>; border: 1px solid <?= $pStyle['border'] ?>;">
                                        <i class="fas <?= $pStyle['icon'] ?>"></i>
                                        <span><?= htmlspecialchars($predikat) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Strip jika ada Catatan LPM belum diteruskan -->
                            <?php if ($pendingRelay > 0): ?>
                                <div class="gpm-priority-banner rounded-3 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                    <span class="d-inline-flex align-items-center gap-2">
                                        <i class="fas fa-bell"></i>
                                        <span><?= $pendingRelay ?> Catatan LPM Belum Diteruskan ke Unit</span>
                                    </span>
                                    <span class="gpm-priority-tag">Prioritas</span>
                                </div>
                            <?php endif; ?>

                            <!-- 3-Column Score Breakdown: 3 Pilar Penilaian Otomatis Sistem -->
                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <div class="gpm-stat-tile h-100">
                                        <div class="gpm-stat-label">Lolos LPM (50%)</div>
                                        <div class="gpm-stat-value text-dark-blue">
                                            <?= $accuracyLpm !== null ? number_format((float)$accuracyLpm, 1) . '%' : 'N/A' ?>
                                        </div>
                                        <div class="gpm-stat-sub">
                                            <?= (int)($m['first_pass_sesuai'] ?? 0) ?>/<?= (int)($m['evaluated_by_lpm'] ?? 0) ?> dok
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="gpm-stat-tile h-100">
                                        <div class="gpm-stat-label">Antrean (30%)</div>
                                        <div class="gpm-stat-value text-primary">
                                            <?= $scoreAntrean !== null ? number_format((float)$scoreAntrean, 1) : '-' ?>
                                        </div>
                                        <div class="gpm-stat-sub">
                                            <?= (int)($m['pending_review'] ?? 0) > 0 ? (int)$m['pending_review'] . ' pending' : 'Antrean tuntas' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="gpm-stat-tile h-100">
                                        <div class="gpm-stat-label">Kecepatan (20%)</div>
                                        <div class="gpm-stat-value" style="color: <?= $scoreKecepatan !== null ? '#0F766E' : '#94A3B8' ?>;">
                                            <?= $scoreKecepatan !== null ? number_format((float)$scoreKecepatan, 1) : '-' ?>
                                        </div>
                                        <div class="gpm-stat-sub">
                                            <?= ($avgRelayStr !== '-') ? "Terusan {$avgRelayStr}" : (($avgReviewStr !== '-') ? "Telaah {$avgReviewStr}" : 'Belum ada data') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4 Compact Metrics: Approve, Pending, Revisi+Penerusan, Ditolak LPM -->
                            <div class="row g-2 text-center">
                                <div class="col-6 col-sm-3">
                                    <div class="gpm-activity-tile h-100" style="background: #F0FDF4; border-color: #86EFAC;">
                                        <div class="gpm-activity-num" style="color: #166534;">
                                            <?= (int)$m['total_diteruskan'] ?>
                                        </div>
                                        <div class="gpm-activity-title" style="color: #15803D;">Approve</div>
                                        <div class="gpm-activity-sub" style="color: #16A34A;">ke LPM</div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="gpm-activity-tile h-100" style="background: #FFFBEB; border-color: #FCD34D;">
                                        <div class="gpm-activity-num" style="color: #92400E;">
                                            <?= (int)$m['pending_review'] ?>
                                        </div>
                                        <div class="gpm-activity-title" style="color: #B45309;">Pending</div>
                                        <div class="gpm-activity-sub" style="color: #D97706;">
                                            <?= $pendingRelay > 0 ? "{$pendingRelay} Terusan" : 'Antrean' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="gpm-activity-tile h-100" style="background: #EFF6FF; border-color: #93C5FD;"
                                         title="<?= (int)($m['total_revisi_murni'] ?? 0) ?> temuan GPM + <?= (int)($m['total_relay_lpm'] ?? 0) ?> penerusan catatan LPM">
                                        <div class="gpm-activity-num" style="color: #1E40AF;">
                                            <?= (int)$m['total_revisi'] ?>
                                        </div>
                                        <div class="gpm-activity-title" style="color: #1D4ED8;">Revisi</div>
                                        <div class="gpm-activity-sub" style="color: #2563EB;">
                                            <?= (int)($m['total_relay_lpm'] ?? 0) ?> Terusan
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="gpm-activity-tile h-100" style="background: #FEF2F2; border-color: #FCA5A5;">
                                        <div class="gpm-activity-num" style="color: #991B1B;">
                                            <?= (int)($m['lpm_returned_unique_docs'] ?? 0) ?>
                                        </div>
                                        <div class="gpm-activity-title" style="color: #DC2626;">Ditolak</div>
                                        <div class="gpm-activity-sub" style="color: #EF4444;">
                                            <?= (int)($m['lpm_returned_events'] ?? 0) ?>x LPM
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kecepatan Respon & Penerusan -->
                            <div class="gpm-speed-bar d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <span class="text-muted">
                                    <i class="fas fa-stopwatch text-primary me-1"></i>Rata-rata Telaah: <strong class="text-dark"><?= $avgReviewStr ?></strong>
                                </span>
                                <span class="text-muted d-none d-sm-inline">|</span>
                                <span class="text-muted">
                                    <i class="fas fa-paper-plane text-warning me-1"></i>Terusan LPM: <strong class="text-dark"><?= $avgRelayStr ?></strong>
                                </span>
                            </div>

                            <!-- Card Footer Actions -->
                            <div class="mt-auto pt-1">
                                <button type="button"
                                        class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-semibold py-2 px-3 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm"
                                        data-gpm="<?= $cardPayloadAttr ?>"
                                        onclick="openGpmHistoryFromBtn(this)">
                                    <i class="fas fa-clock-rotate-left"></i>
                                    <span>More Detail (Riwayat Telaah &amp; Penerusan)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL: MORE DETAIL RIWAYAT TELAAH & PENERUSAN GPM -->
<div class="modal fade" id="modalGpmHistory" tabindex="-1" aria-labelledby="modalGpmName" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header gpm-modal-header border-bottom">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-3 min-w-0">
                        <div id="modalGpmAvatarWrap" class="flex-shrink-0"></div>
                        <div class="min-w-0">
                            <h6 class="modal-title fw-bold text-dark-blue mb-1 text-truncate" id="modalGpmName">-</h6>
                            <div class="text-muted small text-truncate" style="font-size: 0.77rem;" id="modalGpmEmail">-</div>
                            <div class="mt-1 d-flex flex-wrap align-items-center gap-1" id="modalGpmFakultasBadge"></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-shrink-0" id="modalGpmScoreBadgeWrap"></div>
                </div>
                <button type="button" class="btn-close gpm-modal-close flex-shrink-0" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Modal Filter Bar -->
            <div class="gpm-modal-toolbar bg-white border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-inline-flex align-items-center gap-2 fw-bold text-dark-blue" style="font-size: 0.85rem;">
                        <i class="fas fa-list-check text-primary"></i>
                        <span>Riwayat Telaah &amp; Penerusan</span>
                        <span class="badge rounded-pill bg-primary text-white" id="modalHistoryCount">0</span>
                    </div>
                    <div class="text-muted small fw-semibold" id="modalHistoryShowingMeta" style="font-size: 0.75rem;"></div>
                </div>

                <div class="gpm-filter-chip-row mt-2 pt-2 border-top" id="modalHistoryFilterGroup" role="group" aria-label="Filter kategori riwayat">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold active" data-cat="all" onclick="filterModalHistory('all', this)">Semua</button>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold" data-cat="approve" onclick="filterModalHistory('approve', this)">Approve</button>
                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-semibold" data-cat="revisi_gpm" onclick="filterModalHistory('revisi_gpm', this)">Temuan GPM</button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" data-cat="relay_lpm" onclick="filterModalHistory('relay_lpm', this)">Diteruskan ke Unit</button>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold" data-cat="returned_lpm" onclick="filterModalHistory('returned_lpm', this)">Ditolak LPM</button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-3 p-md-4" id="modalGpmHistoryBody" style="background: #F1F5F9; min-height: 320px;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
                    <div class="small text-muted">Memuat data riwayat telaah &amp; penerusan GPM...</div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-top bg-white py-3 px-4 justify-content-end">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4 py-2 fw-semibold" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.gpm-card-box {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid rgba(15, 23, 42, 0.08) !important;
}
.gpm-card-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 26px -6px rgba(15, 23, 42, 0.1), 0 6px 12px -4px rgba(15, 23, 42, 0.05) !important;
}
.min-w-0 {
    min-width: 0;
}
.gpm-kpi-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.45rem 0.85rem;
    border-radius: 999px;
    border: 1px solid transparent;
    font-size: 0.8rem;
    font-weight: 600;
    line-height: 1.25;
}
.gpm-kpi-subbadge {
    display: inline-flex;
    align-items: center;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    margin-left: 0.15rem;
}
.gpm-card-name {
    font-size: 0.98rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.gpm-meta-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.24rem 0.6rem;
    border-radius: 999px;
    border: 1px solid #E2E8F0;
    font-size: 0.7rem;
    font-weight: 600;
    line-height: 1.2;
}
.gpm-score-box {
    min-width: 118px;
    padding: 0.65rem 0.75rem;
}
.gpm-predikat-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    margin-top: 0.4rem;
    padding: 0.22rem 0.6rem;
    border-radius: 999px;
    font-size: 0.67rem;
    font-weight: 700;
    white-space: nowrap;
}
.gpm-priority-banner {
    background: #FFF7ED;
    border: 1px solid #FDBA74;
    color: #C2410C;
    padding: 0.55rem 0.85rem;
    font-size: 0.76rem;
    font-weight: 700;
}
.gpm-priority-tag {
    background: #EA580C;
    color: #FFFFFF;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
}
.gpm-stat-tile {
    padding: 0.65rem 0.5rem;
    border-radius: 0.75rem;
    border: 1px solid #E2E8F0;
    background: #F8FAFC;
}
.gpm-stat-label {
    color: #64748B;
    font-weight: 700;
    font-size: 0.66rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    margin-bottom: 0.2rem;
}
.gpm-stat-value {
    font-weight: 700;
    font-size: 1rem;
    line-height: 1.2;
}
.gpm-stat-sub {
    color: #64748B;
    font-size: 0.67rem;
    margin-top: 0.15rem;
}
.gpm-activity-tile {
    padding: 0.65rem 0.45rem;
    border-radius: 0.75rem;
    border: 1px solid transparent;
}
.gpm-activity-num {
    font-weight: 800;
    font-size: 1.15rem;
    line-height: 1.15;
}
.gpm-activity-title {
    font-size: 0.7rem;
    font-weight: 700;
    margin-top: 0.2rem;
}
.gpm-activity-sub {
    font-size: 0.64rem;
    font-weight: 500;
}
.gpm-speed-bar {
    padding: 0.55rem 0.85rem;
    border-radius: 0.75rem;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    font-size: 0.76rem;
}
.gpm-eval-box {
    padding: 0.7rem 0.85rem;
    border-radius: 0.75rem;
    border: 1px solid #DDD6FE;
}
.gpm-eval-empty {
    padding: 0.7rem 0.85rem;
    border-radius: 0.75rem;
    border: 1px dashed #CBD5E1;
    background: #F8FAFC;
    font-size: 0.75rem;
}
.gpm-eval-excerpt {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Modal Header & Close Button Fixes (Overrides global .modal-header filter: invert(1)) */
#modalGpmHistory .gpm-modal-header {
    background: linear-gradient(135deg, #F8FAFC 0%, #EEF2FF 100%) !important;
    color: var(--navy) !important;
    padding: 1.15rem 1.5rem;
    align-items: center;
    gap: 0.85rem;
}
#modalGpmHistory .gpm-modal-header .gpm-modal-close {
    filter: none !important;
    opacity: 0.9;
    margin: 0 !important;
    background-color: #FFFFFF;
    border: 1px solid #CBD5E1;
    border-radius: 50%;
    width: 2.1rem;
    height: 2.1rem;
    padding: 0;
    background-size: 0.72rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    transition: all 0.15s ease;
}
#modalGpmHistory .gpm-modal-header .gpm-modal-close:hover {
    opacity: 1;
    background-color: #FEE2E2;
    border-color: #FCA5A5;
}
.gpm-modal-score-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.45rem 0.9rem;
    border-radius: 999px;
    border: 1px solid #CBD5E1;
    background: #FFFFFF;
    font-size: 0.8rem;
    font-weight: 700;
    color: #0F172A;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    white-space: nowrap;
}
#modalGpmHistory .gpm-modal-toolbar {
    padding: 0.9rem 1.5rem;
}
.gpm-filter-chip-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.45rem;
}
.gpm-filter-chip-row .btn-outline-warning {
    color: #B45309;
    border-color: #F59E0B;
}
.gpm-filter-chip-row .btn-outline-warning:hover,
.gpm-filter-chip-row .btn-warning {
    background-color: #FEF3C7;
    border-color: #D97706;
    color: #92400E;
}
.gpm-history-card {
    border: 1px solid #E2E8F0;
    border-left-width: 4px;
    border-radius: 0.85rem;
    padding: 1rem 1.15rem;
    background: #FFFFFF;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.gpm-history-note {
    padding: 0.75rem 0.95rem;
    border-radius: 0.65rem;
    border: 1px solid transparent;
}
</style>

<script>
window.escapeHtml = window.escapeHtml || function(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};
var escapeHtml = window.escapeHtml;

let currentGpmHistoryData = [];
let currentHistoryCategory = 'all';

// Filter real-time kartu GPM (pencarian + predikat)
function applyGpmFilters() {
    const q = (document.getElementById('searchGpmInput')?.value || '').toLowerCase().trim();
    const predFilter = document.getElementById('filterPredikatSelect')?.value || 'all';
    const items = document.querySelectorAll('.gpm-card-item');

    items.forEach(card => {
        const name = card.dataset.name || '';
        const fak = card.dataset.fakultas || '';
        const email = card.dataset.email || '';
        const predikat = card.dataset.predikat || '';
        const pendingRelay = parseInt(card.dataset.pendingRelay || '0', 10);

        const matchSearch = !q || name.includes(q) || fak.includes(q) || email.includes(q);
        let matchPred = true;
        if (predFilter === 'pending_relay') {
            matchPred = pendingRelay > 0;
        } else if (predFilter !== 'all') {
            matchPred = (predikat === predFilter);
        }

        card.style.display = (matchSearch && matchPred) ? '' : 'none';
    });
}

function parseBtnPayload(btn) {
    try {
        return JSON.parse(btn.getAttribute('data-gpm') || '{}');
    } catch (e) {
        console.error('Invalid GPM payload:', e);
        return {};
    }
}

function openGpmHistoryFromBtn(btn) {
    const gpm = parseBtnPayload(btn);
    openGpmHistoryModal(gpm);
}

// Buka Modal Riwayat More Detail GPM
function openGpmHistoryModal(gpm) {
    const gpmId = gpm.id || 0;
    const name = gpm.name || '-';
    const fakultas = gpm.nama_fakultas || '';
    const email = gpm.email || '-';
    const avatarUrl = gpm.avatar_url || '';

    document.getElementById('modalGpmName').textContent = name;
    document.getElementById('modalGpmEmail').innerHTML = `<i class="far fa-envelope me-1"></i>${escapeHtml(email)}`;

    const badgeContainer = document.getElementById('modalGpmFakultasBadge');
    if (fakultas) {
        badgeContainer.innerHTML = `<span class="gpm-meta-badge" style="background:#EDE9FE; color:#6D28D9; border-color:#DDD6FE;"><i class="fas fa-building-columns"></i><span>${escapeHtml(fakultas)}</span></span>`;
    } else {
        badgeContainer.innerHTML = `<span class="gpm-meta-badge bg-light text-muted">Belum Dipetakan</span>`;
    }

    const predToneMap = {
        success:   { bg: '#ECFDF5', border: '#6EE7B7', text: '#047857' },
        primary:   { bg: '#EFF6FF', border: '#93C5FD', text: '#1D4ED8' },
        warning:   { bg: '#FFFBEB', border: '#FCD34D', text: '#B45309' },
        danger:    { bg: '#FEF2F2', border: '#FCA5A5', text: '#B91C1C' },
        secondary: { bg: '#FFFFFF', border: '#CBD5E1', text: '#475569' }
    };
    const tone = predToneMap[gpm.predikat_color || 'secondary'] || predToneMap.secondary;
    const scoreWrap = document.getElementById('modalGpmScoreBadgeWrap');
    const scoreTxt = (gpm.combined_score !== null && gpm.combined_score !== undefined) ? Number(gpm.combined_score).toFixed(1) : '-';
    scoreWrap.innerHTML = `
        <span class="gpm-modal-score-pill" style="background:${tone.bg}; border-color:${tone.border}; color:${tone.text};">
            <span>Skor Akhir:</span>
            <strong style="font-size:0.9rem;">${scoreTxt}</strong>
            <span style="opacity:0.9; font-weight:600;">(${escapeHtml(gpm.predikat || 'Belum Ada Data')})</span>
        </span>
    `;

    const avatarWrap = document.getElementById('modalGpmAvatarWrap');
    if (avatarUrl) {
        avatarWrap.innerHTML = `<img src="${escapeHtml(avatarUrl)}" class="rounded-circle shadow-sm border border-2 border-white" style="width: 48px; height: 48px; object-fit: cover;" loading="lazy" decoding="async">`;
    } else {
        const init = name.trim().charAt(0).toUpperCase() || 'G';
        avatarWrap.innerHTML = `<div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center text-white fw-bold" style="width: 48px; height: 48px; font-size: 1.15rem; background: linear-gradient(135deg, #1E3A8A, #3B82F6);">${escapeHtml(init)}</div>`;
    }

    const modalEl = document.getElementById('modalGpmHistory');
    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    bsModal.show();

    currentHistoryCategory = 'all';
    resetFilterButtonsState('all');

    const body = document.getElementById('modalGpmHistoryBody');
    body.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
            <div class="small text-muted">Memuat data riwayat telaah &amp; penerusan GPM...</div>
        </div>
    `;

    fetch(`<?= base_url('admin/monitoring-gpm/history/') ?>${gpmId}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                currentGpmHistoryData = data.history || [];
                document.getElementById('modalHistoryCount').textContent = currentGpmHistoryData.length;
                renderActiveHistoryTab();
            } else {
                body.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-triangle-exclamation text-danger fa-2x mb-2 d-block"></i>
                        <span>${escapeHtml(data?.message || 'Gagal memuat riwayat telaah.')}</span>
                    </div>
                `;
            }
        })
        .catch(err => {
            console.error('Error fetching GPM history:', err);
            body.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-triangle-exclamation text-danger fa-2x mb-2 d-block"></i>
                    <span>Terjadi kesalahan saat memuat data riwayat telaah GPM.</span>
                </div>
            `;
        });
}

function renderActiveHistoryTab() {
    const metaEl = document.getElementById('modalHistoryShowingMeta');
    const filtered = currentHistoryCategory === 'all'
        ? currentGpmHistoryData
        : currentGpmHistoryData.filter(i => resolveHistoryCategory(i) === currentHistoryCategory);
    if (metaEl) metaEl.textContent = `Menampilkan ${filtered.length} dari ${currentGpmHistoryData.length} aktivitas`;
    renderModalHistoryList(filtered);
}

function resolveHistoryCategory(item) {
    if (item.action_category) return item.action_category;
    if (item.status_hasil === 'revisi_lpm') return 'returned_lpm';
    if (['menunggu_review_lpm', 'sesuai'].includes(item.status_hasil)) return 'approve';
    if (item.status_hasil === 'revisi_gpm' && item.status_sebelumnya === 'revisi_lpm') return 'relay_lpm';
    return 'revisi_gpm';
}

function resetFilterButtonsState(activeCat) {
    const classMap = {
        all: 'primary',
        approve: 'success',
        revisi_gpm: 'warning',
        relay_lpm: 'primary',
        returned_lpm: 'danger'
    };
    document.querySelectorAll('#modalHistoryFilterGroup button').forEach(b => {
        const cat = b.dataset.cat;
        const tone = classMap[cat] || 'secondary';
        b.className = (cat === activeCat)
            ? `btn btn-sm btn-${tone} rounded-pill px-3 fw-semibold active`
            : `btn btn-sm btn-outline-${tone} rounded-pill px-3 fw-semibold`;
    });
}

function filterModalHistory(type, btn) {
    currentHistoryCategory = type;
    resetFilterButtonsState(type);
    renderActiveHistoryTab();
}

function renderModalHistoryList(list) {
    const body = document.getElementById('modalGpmHistoryBody');

    if (!list || list.length === 0) {
        body.innerHTML = `
            <div class="text-center py-5 text-muted bg-white rounded-4 border p-4">
                <i class="fas fa-folder-open fa-2x text-secondary opacity-50 mb-2 d-block"></i>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Data di Kategori Ini</h6>
                <p class="small text-muted mb-0">Tidak ditemukan log aktivitas telaah, penerusan catatan LPM, atau pengembalian pada filter yang dipilih.</p>
            </div>
        `;
        return;
    }

    const catBadgeMap = {
        approve: {
            accent: '#16A34A',
            badge: '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold" style="font-size:0.72rem;"><i class="fas fa-circle-check me-1"></i> Disetujui GPM &rarr; LPM</span>',
            noteLabel: '<i class="fas fa-comment-dots text-success me-1"></i> Catatan Verifikasi GPM:',
            noteBg: '#F0FDF4',
            noteBorder: '#BBF7D0'
        },
        revisi_gpm: {
            accent: '#D97706',
            badge: '<span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-50 px-3 py-1 rounded-pill fw-bold" style="font-size:0.72rem;"><i class="fas fa-rotate-left text-warning me-1"></i> Temuan Revisi GPM &rarr; Unit</span>',
            noteLabel: '<i class="fas fa-comment-dots text-warning me-1"></i> Arahan Perbaikan GPM:',
            noteBg: '#FFFBEB',
            noteBorder: '#FDE68A'
        },
        relay_lpm: {
            accent: '#2563EB',
            badge: '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill fw-bold" style="font-size:0.72rem;"><i class="fas fa-paper-plane me-1"></i> Diteruskan GPM (LPM &rarr; Unit)</span>',
            noteLabel: '<i class="fas fa-share-from-square text-primary me-1"></i> Arahan Resmi GPM (Tindak Lanjut LPM):',
            noteBg: '#EFF6FF',
            noteBorder: '#BFDBFE'
        },
        returned_lpm: {
            accent: '#DC2626',
            badge: '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill fw-bold" style="font-size:0.72rem;"><i class="fas fa-triangle-exclamation me-1"></i> Dikembalikan LPM &rarr; GPM</span>',
            noteLabel: '<i class="fas fa-lock text-danger me-1"></i> Catatan Evaluasi Internal LPM ke GPM:',
            noteBg: '#FEF2F2',
            noteBorder: '#FECACA'
        }
    };

    let html = '<div class="d-flex flex-column gap-3">';
    list.forEach(item => {
        const cat = resolveHistoryCategory(item);
        const styleCfg = catBadgeMap[cat] || catBadgeMap.revisi_gpm;
        const waktuFmt = item.created_at ? formatDateTimeStr(item.created_at) : '-';
        const unitName = item.level === 'prodi'
            ? `${escapeHtml(item.nama_prodi || 'Program Studi')} (${escapeHtml(item.jenjang || 'S1')})`
            : `${escapeHtml(item.nama_fakultas || 'Fakultas')}`;

        const multiRoleBadge = item.is_multi_role
            ? `<span class="gpm-meta-badge" style="background:#E0F2FE; color:#0369A1; border-color:#BAE6FD;">
                   <i class="fas fa-user-shield"></i><span>Multi-Role: ${escapeHtml(item.actor_name || 'Tim')}</span>
               </span>`
            : '';

        html += `
            <div class="gpm-history-card" style="border-left-color: ${styleCfg.accent};">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 pb-2 border-bottom">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        ${styleCfg.badge}
                        ${multiRoleBadge}
                        <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                            TA ${escapeHtml(item.tahun_akademik || '-')}
                        </span>
                        ${item.siklus ? `<span class="badge bg-light text-secondary border px-2 py-1 rounded-pill" style="font-size: 0.7rem;">${escapeHtml(String(item.siklus).toUpperCase())}</span>` : ''}
                    </div>
                    <div class="text-muted small text-nowrap flex-shrink-0" style="font-size: 0.74rem;">
                        <i class="far fa-clock me-1"></i>${waktuFmt}
                    </div>
                </div>

                <div class="mt-2">
                    <h6 class="fw-bold text-dark-blue mb-1" style="font-size: 0.92rem; line-height: 1.35;">
                        ${escapeHtml(item.nama_dokumen || 'Dokumen Mutu')}
                    </h6>
                    <div class="d-flex flex-wrap align-items-center gap-2 text-muted small mb-2" style="font-size: 0.76rem;">
                        <span><i class="fas fa-hashtag me-1"></i>${escapeHtml(item.nomor_dokumen || '-')}</span>
                        <span>&bull;</span>
                        <span><i class="fas fa-building me-1"></i>${unitName}</span>
                    </div>

                    <div class="gpm-history-note" style="background:${styleCfg.noteBg}; border-color:${styleCfg.noteBorder};">
                        <div class="text-muted small fw-bold mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.02em;">
                            ${styleCfg.noteLabel}
                        </div>
                        <div class="small text-dark" style="font-size: 0.82rem; line-height: 1.55; white-space: pre-line;">${item.catatan ? escapeHtml(item.catatan) : '<span class="text-muted fst-italic">Tidak ada catatan teks.</span>'}</div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    body.innerHTML = html;
}

function formatDateTimeStr(dateStr) {
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const day = d.getDate();
        const month = months[d.getMonth()];
        const year = d.getFullYear();
        const hours = String(d.getHours()).padStart(2, '0');
        const mins = String(d.getMinutes()).padStart(2, '0');
        return `${day} ${month} ${year}, ${hours}:${mins} WIB`;
    } catch (e) {
        return dateStr;
    }
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
