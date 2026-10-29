<?php if (empty($documents)): ?>
    <tr>
        <td colspan="7" class="text-center py-5 text-muted">
            <div class="mb-2"><i class="fas fa-inbox fa-3x text-light-subtle"></i></div>
            Tidak ada berkas dokumen yang sesuai dengan filter pencarian.
        </td>
    </tr>
<?php else: ?>
    <?php $no = $startNum ?? 1; foreach ($documents as $d): 
        $isFakultas = ($d['level'] === 'fakultas');
        $unitName = $isFakultas ? $d['nama_fakultas'] : ($d['nama_prodi'] . ' (' . ($d['jenjang'] ?? 'S1') . ')');
        $reviewUrl = $isFakultas ? base_url('admin/review-fakultas/' . $d['fakultas_id']) : base_url('admin/review-prodi/' . $d['prodi_id']);
        
        // Turnaround calculation
        $hasGpmReview = !empty($d['gpm_reviewed_at']);
        $gpmDuration = $hasGpmReview && function_exists('formatDurationHours') ? formatDurationHours((int)($d['diff_hours_upload_to_gpm'] ?? 0)) : null;
    ?>
        <tr>
            <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
            <td>
                <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.9rem;">
                    <a href="<?= $reviewUrl ?>" class="text-dark-blue text-decoration-none hover-primary">
                        <?= htmlspecialchars($d['nama_dokumen']) ?>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">
                        <i class="fas fa-tag me-1"></i><?= htmlspecialchars($d['nama_bidang'] ?? 'Umum') ?>
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.7rem;">
                        TA <?= htmlspecialchars($d['tahun_akademik']) ?>
                    </span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.7rem;">
                        <?= strtoupper($d['siklus']) ?>
                    </span>
                    <?php if (!empty($d['files_count'])): ?>
                        <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                            <i class="fas fa-paperclip text-muted me-1"></i><?= $d['files_count'] ?> Berkas
                        </span>
                    <?php endif; ?>
                </div>
            </td>
            <td>
                <div class="fw-semibold text-dark mb-0.5" style="font-size: 0.85rem;">
                    <?= htmlspecialchars($unitName) ?>
                </div>
                <div class="text-muted small" style="font-size: 0.73rem;">
                    <i class="fas fa-user-pen me-1"></i><?= htmlspecialchars($d['uploader_name'] ?? 'Uploader') ?>
                </div>
            </td>
            <td>
                <div class="fw-semibold text-dark small">
                    <?= date('d M Y', strtotime($d['created_at'])) ?>
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                    <?= date('H:i', strtotime($d['created_at'])) ?> WIB
                </div>
            </td>
            <td>
                <?php if ($hasGpmReview): ?>
                    <div class="d-flex align-items-center gap-1.5 mb-1">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                            <i class="fas fa-check me-1"></i> Direview
                        </span>
                        <?php if ($gpmDuration): ?>
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.72rem;" title="Durasi sejak diunggah hingga direview GPM">
                                <i class="fas fa-stopwatch text-primary me-1"></i> <?= $gpmDuration ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="small fw-semibold text-dark" style="font-size: 0.78rem;">
                        <i class="fas fa-user-check text-success me-1"></i><?= htmlspecialchars($d['gpm_reviewer_name'] ?? 'Reviewer GPM') ?>
                    </div>
                    <div class="text-muted" style="font-size: 0.72rem;">
                        <?= date('d M Y H:i', strtotime($d['gpm_reviewed_at'])) ?>
                    </div>
                    <?php if (!empty($d['gpm_catatan'])): ?>
                        <div class="text-muted fst-italic mt-1 p-1.5 rounded bg-light border" style="font-size: 0.72rem; max-width: 250px;">
                            "<?= htmlspecialchars(mb_strimwidth($d['gpm_catatan'], 0, 70, '...')) ?>"
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php $pDays = (int)($d['pending_days_gpm'] ?? 0); ?>
                    <div class="mb-1">
                        <span class="badge bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                            <i class="fas fa-hourglass-half text-warning me-1"></i> Menunggu GPM
                        </span>
                    </div>
                    <div class="text-muted small" style="font-size: 0.72rem;">
                        Menunggu <?= $pDays ?> hari
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?php 
                $st = $d['status_review'];
                if ($st === 'sesuai'): ?>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                        <i class="fas fa-check-circle me-1"></i> Sesuai (Disetujui)
                    </span>
                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                        <?= htmlspecialchars($d['lpm_reviewer_name'] ?? 'LPM') ?>
                    </div>
                <?php elseif (in_array($st, ['revisi_gpm', 'revisi_lpm', 'perlu_perbaikan'])): ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                        <i class="fas fa-circle-exclamation me-1"></i> Perlu Revisi
                    </span>
                <?php elseif ($st === 'menunggu_review_lpm'): ?>
                    <span class="badge bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                        <i class="fas fa-clock me-1"></i> Antrean LPM
                    </span>
                <?php else: ?>
                    <span class="badge bg-secondary bg-opacity-15 text-secondary rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                        <?= ucfirst(str_replace('_', ' ', $st)) ?>
                    </span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <a href="<?= $reviewUrl ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-none fw-bold" style="font-size: 0.75rem;">
                    Buka <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
