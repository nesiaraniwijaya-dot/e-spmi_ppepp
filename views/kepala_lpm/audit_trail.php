<?php
/**
 * Kepala LPM: Audit Trail & Rekam Jejak Sistem
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-warning bg-opacity-10 text-warning small fw-bold mb-2">
                <i class="fas fa-shield-halved"></i> Audit Trail & Akuntabilitas
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Riwayat Aktivitas & Perubahan Data (Audit Trail)</h3>
            <p class="text-muted small mb-0">Pemantauan real-time: siapa (admin), melakukan apa (tambah/ubah/hapus), pada prodi apa, beserta cap waktu detail.</p>
        </div>
        <a href="<?= base_url('kepala/dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Filter Form Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="<?= base_url('kepala/audit') ?>" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Filter Program Studi</label>
                    <select name="prodi_id" class="form-select form-select-sm bg-light">
                        <option value="">-- Semua Program Studi --</option>
                        <?php foreach ($prodiList as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($filters['prodi_id'] == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nama_prodi']) ?> (<?= htmlspecialchars($p['jenjang']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Jenis Aksi</label>
                    <select name="aksi" class="form-select form-select-sm bg-light">
                        <option value="">-- Semua Aksi --</option>
                        <option value="CREATE" <?= ($filters['aksi'] === 'CREATE') ? 'selected' : '' ?>>CREATE (Tambah)</option>
                        <option value="UPDATE" <?= ($filters['aksi'] === 'UPDATE') ? 'selected' : '' ?>>UPDATE (Ubah)</option>
                        <option value="DELETE" <?= ($filters['aksi'] === 'DELETE') ? 'selected' : '' ?>>DELETE (Hapus)</option>
                        <option value="RESTORE" <?= ($filters['aksi'] === 'RESTORE') ? 'selected' : '' ?>>RESTORE (Pulihkan)</option>
                        <option value="LOGIN" <?= ($filters['aksi'] === 'LOGIN') ? 'selected' : '' ?>>LOGIN</option>
                        <option value="LOGOUT" <?= ($filters['aksi'] === 'LOGOUT') ? 'selected' : '' ?>>LOGOUT</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Pengguna / Admin</label>
                    <select name="user_id" class="form-select form-select-sm bg-light">
                        <option value="">-- Semua Pengguna --</option>
                        <?php foreach ($userList as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] == $u['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['role']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Mulai Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($filters['start_date']) ?>">
                </div>

                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill bg-scu-blue border-0 fw-bold">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="<?= base_url('kepala/audit') ?>" class="btn btn-light btn-sm border rounded-pill px-3" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Audit Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark-blue mb-0">
                <i class="fas fa-list-check text-primary me-2"></i> Log Rekam Jejak Aktivitas
            </h5>
            <span class="badge bg-light text-secondary border">Menampilkan <?= count($auditLogs) ?> entri terbaru</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 170px;">Waktu & Tanggal</th>
                        <th>Pengguna (Aktor)</th>
                        <th>Peran (Role)</th>
                        <th>Program Studi</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                        <th>Item & Modul</th>
                        <th class="text-center" style="width: 100px;">Detail Diff</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($auditLogs)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Tidak ada catatan audit yang cocok dengan filter yang dipilih.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($auditLogs as $log): ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td class="text-nowrap">
                                    <div class="fw-bold text-dark-blue"><i class="far fa-calendar-alt text-primary me-1"></i> <?= date('d M Y', strtotime($log['created_at'])) ?></div>
                                    <div class="small text-muted"><i class="far fa-clock text-warning me-1"></i> <?= date('H:i:s', strtotime($log['created_at'])) ?> WIB</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($log['user_name']) ?></div>
                                    <div class="text-muted" style="font-size: 0.72rem;">IP: <?= htmlspecialchars($log['ip_address'] ?: '127.0.0.1') ?></div>
                                </td>
                                <td>
                                    <?php
                                    $rBadge = match($log['user_role']) {
                                        'super_admin' => '<span class="badge bg-danger">Admin LPM</span>',
                                        'kepala_lpm'  => '<span class="badge bg-warning text-dark">Kepala LPM</span>',
                                        'admin_prodi' => '<span class="badge bg-primary">Admin Prodi</span>',
                                        default => '<span class="badge bg-secondary">Guest</span>'
                                    };
                                    echo $rBadge;
                                    ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><?= htmlspecialchars($log['prodi_name'] ?: 'Tingkat Institusi') ?></div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $aBadge = match($log['aksi']) {
                                        'CREATE' => 'bg-success',
                                        'UPDATE' => 'bg-primary',
                                        'DELETE' => 'bg-danger',
                                        'RESTORE'=> 'bg-warning text-dark',
                                        'LOGIN'  => 'bg-info',
                                        'LOGOUT' => 'bg-secondary',
                                        default => 'bg-dark'
                                    };
                                    ?>
                                    <span class="badge <?= $aBadge ?> px-2.5 py-1"><?= $log['aksi'] ?></span>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark"><?= htmlspecialchars($log['modul']) ?></div>
                                    <div class="small text-muted text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($log['target_name'] ?? '') ?>">
                                        <?= htmlspecialchars($log['target_name'] ?: '-') ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($log['old_values']) || !empty($log['new_values'])): ?>
                                        <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalDiff" 
                                                onclick='showDiffModal(<?= json_encode($log, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                            <i class="fas fa-code-compare me-1"></i> Diff
                                        </button>
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
    </div>
</div>

<!-- Modal: Detail Perubahan Data (Diff Viewer) -->
<div class="modal fade" id="modalDiff" tabindex="-1" aria-labelledby="modalDiffLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark-blue text-white">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-warning fa-lg"></i>
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalDiffLabel">Rincian Perubahan Data (Snapshot Audit)</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 bg-light rounded-3 mb-3 border small">
                    <div class="row g-2">
                        <div class="col-sm-6"><strong>Pengguna:</strong> <span id="diffUser">-</span></div>
                        <div class="col-sm-6"><strong>Waktu:</strong> <span id="diffTime">-</span></div>
                        <div class="col-sm-6"><strong>Modul:</strong> <span id="diffModul">-</span></div>
                        <div class="col-sm-6"><strong>Aksi:</strong> <span id="diffAksi">-</span></div>
                        <div class="col-12"><strong>Target Data:</strong> <span id="diffTarget" class="text-primary">-</span></div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark-blue mb-2">Perbandingan Nilai (Before vs After):</h6>
                <div id="diffContentArea"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showDiffModal(log) {
    document.getElementById('diffUser').textContent = log.user_name + ' (' + log.user_role + ')';
    document.getElementById('diffTime').textContent = log.created_at;
    document.getElementById('diffModul').textContent = log.modul;
    document.getElementById('diffAksi').textContent = log.aksi;
    document.getElementById('diffTarget').textContent = log.target_name || '-';

    let oldVal = {};
    let newVal = {};
    try { oldVal = log.old_values ? JSON.parse(log.old_values) : {}; } catch(e) {}
    try { newVal = log.new_values ? JSON.parse(log.new_values) : {}; } catch(e) {}

    const allKeys = Array.from(new Set([...Object.keys(oldVal), ...Object.keys(newVal)]));
    
    let html = '<div class="table-responsive"><table class="table table-bordered table-sm small align-middle mb-0">';
    html += '<thead class="table-secondary"><tr><th style="width: 25%;">Atribut/Field</th><th style="width: 37.5%;" class="text-danger">Sebelumnya (Old)</th><th style="width: 37.5%;" class="text-success">Perubahan Baru (New)</th></tr></thead><tbody>';

    if (allKeys.length === 0) {
        html += '<tr><td colspan="3" class="text-center py-3 text-muted">Tidak ada snapshot payload.</td></tr>';
    } else {
        allKeys.forEach(key => {
            const vOld = oldVal[key] !== undefined ? JSON.stringify(oldVal[key]) : '<em class="text-muted">(kosong)</em>';
            const vNew = newVal[key] !== undefined ? JSON.stringify(newVal[key]) : '<em class="text-muted">(dihapus/tetap)</em>';
            const isChanged = oldVal[key] !== newVal[key];

            html += `<tr class="${isChanged ? 'table-warning' : ''}">
                <td class="fw-bold text-dark">${key}</td>
                <td class="text-break">${vOld}</td>
                <td class="text-break">${vNew}</td>
            </tr>`;
        });
    }

    html += '</tbody></table></div>';
    document.getElementById('diffContentArea').innerHTML = html;
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
