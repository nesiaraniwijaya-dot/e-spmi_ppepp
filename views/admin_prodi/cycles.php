<?php
/**
 * Admin Prodi: Halaman Judul yang Sudah Ada & Kelengkapan Tahapan PPEPP
 * Menampilkan progress berkelanjutan 5 tahapan PPEPP dan tombol '+' untuk menambah dokumen lanjutan
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$totalCycles = count($existingCycles ?? []);
$completedCycles = 0;
$inProgressCycles = 0;
$sumProgress = 0;

foreach ($existingCycles as $ec) {
    $sumProgress += (int)$ec['progress_percent'];
    if ((int)$ec['completed_count'] === 5) {
        $completedCycles++;
    } else {
        $inProgressCycles++;
    }
}
$avgProgress = $totalCycles > 0 ? round($sumProgress / $totalCycles) : 0;
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
                Judul yang Sudah Ada &amp; Tahapan PPEPP
            </h3>
            <p class="text-muted small mb-0">
                Pemantauan siklus mutu bertahap Program Studi <strong><?= htmlspecialchars($prodi['nama_prodi']) ?></strong>.
                Setiap judul memuat 5 tahapan (Penetapan &rarr; Pelaksanaan &rarr; Evaluasi &rarr; Pengendalian &rarr; Peningkatan).
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('prodi/dokumen/create') ?>" class="btn btn-warning rounded-pill px-4 py-2.5 text-dark fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.84rem;">
                <i class="fas fa-plus-circle"></i>
                <span>Unggah Dokumen Baru</span>
            </a>
            <a href="<?= base_url('prodi/dokumen') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2.5 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-arrow-left"></i> <span>Daftar Dokumen</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 bg-white h-100" style="border: 1px solid #E2E8F0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Judul Siklus</span>
                    <span class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-folder-tree"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-dark-blue mb-0"><?= $totalCycles ?></div>
                <div class="text-muted" style="font-size: 0.72rem;">Judul dokumen terdaftar</div>
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
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 bg-white h-100" style="border: 1px solid #E2E8F0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Siklus Lengkap (5/5)</span>
                    <span class="rounded-3 p-2 bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-circle-check"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-success mb-0"><?= $completedCycles ?></div>
                <div class="text-muted" style="font-size: 0.72rem;">Seluruh 5 tahap terpenuhi</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-2xs rounded-4 p-3.5 bg-white h-100" style="border: 1px solid #E2E8F0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Perlu Dilengkapi</span>
                    <span class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-clock-rotate-left"></i>
                    </span>
                </div>
                <div class="h3 fw-bold text-warning-emphasis mb-0"><?= $inProgressCycles ?></div>
                <div class="text-muted" style="font-size: 0.72rem;">Memiliki tahap belum lengkap</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-3.5 mb-4 bg-white" style="border: 1px solid #E2E8F0 !important;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 500px;">
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary border-end-0 px-3"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 py-2" id="searchCycleInput" placeholder="Cari berdasarkan nama judul dokumen atau standar..." oninput="filterCycleList(this.value)">
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 text-muted small">
                <span>Petunjuk: Klik icon <span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><i class="fas fa-plus"></i></span> pada tahap yang kosong untuk mengunggah dokumen lanjutan.</span>
            </div>
        </div>
    </div>

    <!-- Daftar Judul Siklus PPEPP Cards -->
    <?php if (empty($existingCycles)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center" style="border: 1px solid #E2E8F0 !important;">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary mb-3" style="width: 64px; height: 64px;">
                <i class="fas fa-folder-open fa-2x"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Belum Ada Judul Siklus PPEPP</h5>
            <p class="text-muted small mb-4" style="max-width: 520px; margin: 0 auto;">
                Program Studi Anda belum memiliki judul siklus dokumen mutu. Klik tombol di bawah untuk membuat judul baru dan mengunggah dokumen awal.
            </p>
            <div>
                <a href="<?= base_url('prodi/dokumen/create') ?>" class="btn btn-warning rounded-pill px-4 py-2.5 text-dark fw-bold shadow-sm d-inline-flex align-items-center gap-2">
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
                $cSub = htmlspecialchars($cyc['nama_sub_bidang'] ?: 'Umum');
                $cTahun = htmlspecialchars($cyc['tahun_akademik']);
                $cProgress = (int)$cyc['progress_percent'];
                $cCompleted = (int)$cyc['completed_count'];
            ?>
                <div class="cycle-item-card p-4 rounded-4 border bg-white shadow-2xs transition-all" id="cycle_card_<?= $cId ?>" data-cycle-title="<?= strtolower($cJudul . ' ' . $cBidang . ' ' . $cSub) ?>">
                    <!-- Card Header -->
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 pb-3 border-bottom mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 text-primary flex-shrink-0 p-2.5" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                <i class="fas fa-folder-tree"></i>
                            </span>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <h5 class="fw-bold text-dark-blue mb-0" style="font-size: 1.05rem;"><?= $cJudul ?></h5>
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
                                                <a href="<?= base_url('prodi/dokumen/detail/' . $docItem['id']) ?>" target="_blank" class="btn btn-xs btn-outline-secondary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.68rem;" title="Lihat Dokumen">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <a href="<?= base_url('prodi/dokumen/create?cycle_id=' . $cId . '&siklus=' . $sKey) ?>" 
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
    <?php endif; ?>
</div>

<script>
function filterCycleList(query) {
    const q = (query || '').toLowerCase().trim();
    document.querySelectorAll('.cycle-item-card').forEach(card => {
        const title = card.getAttribute('data-cycle-title') || '';
        card.style.display = (!q || title.includes(q)) ? 'block' : 'none';
    });
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
