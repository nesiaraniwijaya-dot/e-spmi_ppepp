<?php
/**
 * Super Admin & LPM: Monitoring Kinerja Anggota GPM (Tampilan Kartu / Kotak-Kotak)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

function formatHoursToDays(?float $hours): string {
    if ($hours === null || $hours <= 0) return '-';
    if ($hours < 24) return round($hours, 1) . ' Jam';
    $days = round($hours / 24, 1);
    return "{$days} Hari";
}
?>

<div class="container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill bg-scu-blue bg-opacity-10 text-primary small fw-bold mb-2">
                <i class="fas fa-users-gear"></i> Gugus Penjaminan Mutu (GPM)
            </div>
            <h3 class="fw-bold text-dark-blue mb-1">Monitoring Kinerja Anggota GPM</h3>
            <p class="text-muted small mb-0">Direktori seluruh anggota Gugus Penjaminan Mutu (GPM) fakultas: status telaah dokumen, antrean pending, dan riwayat catatan perbaikan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs">
                <i class="fas fa-user-plus"></i>
                <span>Kelola Akun GPM</span>
            </a>
            <a href="<?= base_url('admin/monitoring-dokumen') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5">
                <i class="fas fa-file-waveform"></i>
                <span>Monitoring Dokumen</span>
            </a>
        </div>
    </div>

    <!-- Filter & Toolbar Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-scu-blue bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold" style="font-size: 0.8rem;">
                    <i class="fas fa-users me-1.5"></i> <?= count($gpmMembers) ?> Anggota GPM Terdaftar
                </span>
            </div>
            <div class="flex-grow-1" style="max-width: 380px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted px-2.5">
                        <i class="fas fa-magnifying-glass"></i>
                    </span>
                    <input type="text" class="form-control form-control-sm border-start-0 ps-1" id="searchGpmInput" 
                           placeholder="Cari nama anggota atau fakultas..." oninput="filterGpmCards(this.value)">
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Kartu Anggota GPM (Kotak-Kotak) -->
    <?php if (empty($gpmMembers)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <div class="py-4 text-muted">
                <i class="fas fa-user-slash fa-3x mb-3 text-secondary opacity-50"></i>
                <h6 class="fw-bold text-dark">Belum Ada Anggota GPM</h6>
                <p class="small text-muted mb-0">Belum ada pengguna dengan hak akses <strong>GPM</strong> terdaftar di sistem.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3.5" id="gpmCardsContainer">
            <?php foreach ($gpmMembers as $m): 
                $avgDaysStr = formatHoursToDays(isset($m['avg_review_hours']) ? (float)$m['avg_review_hours'] : null);
                $avatarUrl = (!empty($m['avatar']) && file_exists(ROOT_PATH . '/' . $m['avatar'])) ? base_url($m['avatar']) : null;
                $initials = strtoupper(substr(trim($m['name']), 0, 1));
            ?>
                <div class="col-12 col-md-6 col-xl-4 gpm-card-item" 
                     data-name="<?= htmlspecialchars(strtolower($m['name'])) ?>" 
                     data-fakultas="<?= htmlspecialchars(strtolower($m['nama_fakultas'] ?? '')) ?>"
                     data-email="<?= htmlspecialchars(strtolower($m['email'])) ?>">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden transition-all hover-shadow gpm-card-box">
                        <!-- Top Accent Border Bar -->
                        <div class="w-100" style="height: 4px; background: linear-gradient(90deg, #1E3A8A 0%, #3B82F6 100%);"></div>

                        <div class="p-3.5 p-md-4 d-flex flex-column h-100">
                            <!-- Profil Header: Foto, Nama, Email, Fakultas -->
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <!-- Foto Profil -->
                                <div class="position-relative flex-shrink-0">
                                    <?php if ($avatarUrl): ?>
                                        <img src="<?= $avatarUrl ?>" alt="<?= htmlspecialchars($m['name']) ?>" 
                                             class="rounded-circle shadow-xs border border-2 border-white" 
                                             style="width: 58px; height: 58px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle shadow-xs d-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width: 58px; height: 58px; font-size: 1.35rem; background: linear-gradient(135deg, #1E3A8A, #3B82F6);">
                                            <?= $initials ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle" 
                                          title="Akun GPM Aktif" style="width: 14px; height: 14px;"></span>
                                </div>

                                <!-- Nama, Email, Fakultas -->
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="fw-bold text-dark-blue mb-0.5 text-truncate" title="<?= htmlspecialchars($m['name']) ?>" style="font-size: 0.95rem;">
                                        <?= htmlspecialchars($m['name']) ?>
                                    </h6>
                                    <div class="text-muted small text-truncate mb-1.5" style="font-size: 0.76rem;" title="<?= htmlspecialchars($m['email']) ?>">
                                        <i class="far fa-envelope me-1 text-secondary opacity-75"></i><?= htmlspecialchars($m['email']) ?>
                                    </div>
                                    <div>
                                        <?php if (!empty($m['nama_fakultas'])): ?>
                                            <span class="badge rounded-pill px-2.5 py-1 fw-semibold text-truncate d-inline-block" 
                                                  style="background:#EDE9FE; color:#6D28D9; border:1px solid #DDD6FE; font-size:0.72rem; max-width: 100%;"
                                                  title="<?= htmlspecialchars($m['nama_fakultas']) ?>">
                                                <i class="fas fa-building-columns me-1"></i> <?= htmlspecialchars($m['nama_fakultas']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Belum Dipetakan</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-2 opacity-10">

                            <!-- 3 Main Metrics: Approve, Pending, Revisi -->
                            <div class="row g-2 text-center mb-3">
                                <!-- Approve -->
                                <div class="col-4">
                                    <div class="p-2.5 rounded-3 border h-100" style="background: #F0FDF4; border-color: #BBF7D0 !important;">
                                        <div class="d-flex align-items-center justify-content-center gap-1 text-success mb-1" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">
                                            <i class="fas fa-circle-check"></i> Approve
                                        </div>
                                        <div class="fw-bold fs-4 text-success line-height-1">
                                            <?= (int)$m['total_diteruskan'] ?>
                                        </div>
                                        <span class="text-muted" style="font-size: 0.65rem;">Disetujui GPM</span>
                                    </div>
                                </div>

                                <!-- Pending -->
                                <div class="col-4">
                                    <div class="p-2.5 rounded-3 border h-100" style="background: #FEFCE8; border-color: #FEF08A !important;">
                                        <div class="d-flex align-items-center justify-content-center gap-1 text-warning mb-1" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">
                                            <i class="fas fa-clock"></i> Pending
                                        </div>
                                        <div class="fw-bold fs-4 text-warning line-height-1">
                                            <?= (int)$m['pending_review'] ?>
                                        </div>
                                        <span class="text-muted" style="font-size: 0.65rem;">Antre Telaah</span>
                                    </div>
                                </div>

                                <!-- Revisi -->
                                <div class="col-4">
                                    <div class="p-2.5 rounded-3 border h-100" style="background: #FEF2F2; border-color: #FECACA !important;">
                                        <div class="d-flex align-items-center justify-content-center gap-1 text-danger mb-1" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">
                                            <i class="fas fa-rotate-left"></i> Revisi
                                        </div>
                                        <div class="fw-bold fs-4 text-danger line-height-1">
                                            <?= (int)$m['total_revisi'] ?>
                                        </div>
                                        <span class="text-muted" style="font-size: 0.65rem;">Minta Perbaikan</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Rata-rata respon & Review Terakhir -->
                            <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 border bg-light-subtle mb-3 mt-auto" style="font-size: 0.75rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="p-1.5 rounded-circle bg-primary bg-opacity-10 text-primary">
                                        <i class="fas fa-stopwatch"></i>
                                    </span>
                                    <div>
                                        <div class="text-muted" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase;">Rata-rata Respon</div>
                                        <div class="fw-bold text-dark font-monospace"><?= $avgDaysStr ?></div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase;">Review Terakhir</div>
                                    <div class="fw-semibold text-secondary" style="font-size: 0.72rem;">
                                        <?= !empty($m['last_reviewed_at']) ? date('d M Y', strtotime($m['last_reviewed_at'])) : 'Belum Ada' ?>
                                    </div>
                                </div>
                            </div>

                            <!-- More Detail Button -->
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-semibold py-2 d-inline-flex align-items-center justify-content-center gap-2 shadow-2xs" 
                                    onclick="openGpmHistoryModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['name'])) ?>', '<?= htmlspecialchars(addslashes($m['nama_fakultas'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($m['email'])) ?>', '<?= htmlspecialchars(addslashes($avatarUrl ?? '')) ?>')">
                                <i class="fas fa-clock-rotate-left"></i>
                                <span>More Detail</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL: MORE DETAIL RIWAYAT REVIEW GPM -->
<div class="modal fade" id="modalGpmHistory" tabindex="-1" aria-labelledby="modalGpmHistoryTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <!-- Modal Header -->
            <div class="modal-header border-bottom pb-3 bg-light rounded-top-4">
                <div class="d-flex align-items-center gap-3">
                    <div id="modalGpmAvatarWrap" class="flex-shrink-0">
                        <!-- Populated by JS -->
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark-blue mb-0" id="modalGpmName">-</h6>
                        <div class="text-muted small" style="font-size: 0.75rem;" id="modalGpmEmail">-</div>
                        <div class="mt-1" id="modalGpmFakultasBadge">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Filter Bar -->
            <div class="px-4 py-2.5 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="small fw-bold text-dark d-flex align-items-center gap-1.5">
                    <i class="fas fa-list-check text-primary"></i>
                    <span>Riwayat Telaah Dokumen:</span>
                    <span class="badge bg-light text-dark border ms-1" id="modalHistoryCount">0 Dokumen</span>
                </div>
                <div class="btn-group btn-group-sm" role="group" id="modalHistoryFilterGroup">
                    <button type="button" class="btn btn-primary active py-1 px-2.5" onclick="filterModalHistory('all', this)">Semua</button>
                    <button type="button" class="btn btn-outline-success py-1 px-2.5" onclick="filterModalHistory('approve', this)">Approve</button>
                    <button type="button" class="btn btn-outline-danger py-1 px-2.5" onclick="filterModalHistory('revisi', this)">Revisi</button>
                </div>
            </div>

            <!-- Modal Body: History List -->
            <div class="modal-body p-4 bg-light-subtle" id="modalGpmHistoryBody" style="min-height: 280px;">
                <!-- Loading State / Content populated by JS -->
                <div class="text-center py-5" id="modalGpmLoading">
                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
                    <div class="small text-muted">Memuat data riwayat telaah GPM...</div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-top bg-white rounded-bottom-4 py-2.5">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3 py-1.5 fw-semibold" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.gpm-card-box {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}
.gpm-card-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
}
.min-w-0 {
    min-width: 0;
}
</style>

<script>
window.escapeHtml = window.escapeHtml || function(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};
var escapeHtml = window.escapeHtml;

let currentGpmHistoryData = [];

// Filter real-time kotak kartu GPM
function filterGpmCards(keyword) {
    const q = (keyword || '').toLowerCase().trim();
    const items = document.querySelectorAll('.gpm-card-item');
    items.forEach(card => {
        const name = card.dataset.name || '';
        const fak = card.dataset.fakultas || '';
        const email = card.dataset.email || '';
        if (!q || name.includes(q) || fak.includes(q) || email.includes(q)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Buka Modal Riwayat More Detail GPM
function openGpmHistoryModal(gpmId, name, fakultas, email, avatarUrl) {
    document.getElementById('modalGpmName').textContent = name || '-';
    document.getElementById('modalGpmEmail').innerHTML = `<i class="far fa-envelope me-1"></i>${escapeHtml(email || '-')}`;
    
    // Fakultas badge
    const badgeContainer = document.getElementById('modalGpmFakultasBadge');
    if (fakultas) {
        badgeContainer.innerHTML = `<span class="badge rounded-pill px-2.5 py-0.5 fw-semibold" style="background:#EDE9FE; color:#6D28D9; border:1px solid #DDD6FE; font-size:0.7rem;"><i class="fas fa-building-columns me-1"></i> ${escapeHtml(fakultas)}</span>`;
    } else {
        badgeContainer.innerHTML = `<span class="badge bg-light text-muted border px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Belum Dipetakan</span>`;
    }

    // Avatar
    const avatarWrap = document.getElementById('modalGpmAvatarWrap');
    if (avatarUrl) {
        avatarWrap.innerHTML = `<img src="${avatarUrl}" class="rounded-circle shadow-xs border border-2 border-white" style="width: 48px; height: 48px; object-fit: cover;">`;
    } else {
        const init = (name || 'G').trim().charAt(0).toUpperCase();
        avatarWrap.innerHTML = `<div class="rounded-circle shadow-xs d-flex align-items-center justify-content-center text-white fw-bold" style="width: 48px; height: 48px; font-size: 1.15rem; background: linear-gradient(135deg, #1E3A8A, #3B82F6);">${init}</div>`;
    }

    // Tampilkan modal
    const modalEl = document.getElementById('modalGpmHistory');
    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();

    // Reset list body & state
    const body = document.getElementById('modalGpmHistoryBody');
    body.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
            <div class="small text-muted">Memuat data riwayat telaah GPM...</div>
        </div>
    `;

    // Reset filter buttons
    const filterBtns = document.querySelectorAll('#modalHistoryFilterGroup button');
    filterBtns.forEach(b => b.classList.remove('active', 'btn-primary', 'btn-success', 'btn-danger'));
    filterBtns[0].classList.add('active', 'btn-primary');

    // Fetch history
    fetch(`<?= base_url('admin/monitoring-gpm/history/') ?>${gpmId}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                currentGpmHistoryData = data.history || [];
                renderModalHistoryList(currentGpmHistoryData);
            } else {
                body.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-triangle-exclamation text-danger fa-2x mb-2 d-block"></i>
                        <span>${escapeHtml(data.message || 'Gagal memuat riwayat telaah.')}</span>
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

// Render isi riwayat review
function renderModalHistoryList(list) {
    const body = document.getElementById('modalGpmHistoryBody');
    const countBadge = document.getElementById('modalHistoryCount');
    countBadge.textContent = `${list.length} Dokumen`;

    if (!list || list.length === 0) {
        body.innerHTML = `
            <div class="text-center py-5 text-muted bg-white rounded-4 border p-4">
                <i class="fas fa-folder-open fa-2x text-secondary opacity-50 mb-2 d-block"></i>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Riwayat Telaah</h6>
                <p class="small text-muted mb-0">Belum ada riwayat dokumen yang ditelaah atau disetujui oleh anggota GPM ini.</p>
            </div>
        `;
        return;
    }

    let html = '<div class="d-flex flex-column gap-2.5">';
    list.forEach(item => {
        const isApprove = ['menunggu_review_lpm', 'sesuai'].includes(item.status_hasil);
        const badgeStatus = isApprove 
            ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill fw-bold" style="font-size:0.72rem;"><i class="fas fa-circle-check me-1"></i> Disetujui GPM (Diteruskan ke LPM)</span>'
            : '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 rounded-pill fw-bold" style="font-size:0.72rem;"><i class="fas fa-rotate-left me-1"></i> Minta Revisi ke Unit</span>';

        const waktuFmt = item.created_at ? formatDateTimeStr(item.created_at) : '-';
        const unitName = item.level === 'prodi' 
            ? `${escapeHtml(item.nama_prodi || 'Program Studi')} (${escapeHtml(item.jenjang || 'S1')})`
            : `${escapeHtml(item.nama_fakultas || 'Fakultas')}`;

        html += `
            <div class="card border rounded-3 p-3 bg-white shadow-2xs">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 pb-2 border-bottom">
                    <div>
                        ${badgeStatus}
                        <span class="badge bg-light text-muted border px-2 py-0.5 rounded-pill ms-1" style="font-size: 0.7rem;">
                            TA ${escapeHtml(item.tahun_akademik || '-')}
                        </span>
                        ${item.siklus ? `<span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill ms-0.5" style="font-size: 0.7rem;">${escapeHtml(item.siklus.toUpperCase())}</span>` : ''}
                    </div>
                    <div class="text-muted small" style="font-size: 0.73rem;">
                        <i class="far fa-clock me-1"></i>${waktuFmt}
                    </div>
                </div>

                <div class="mt-2.5">
                    <h6 class="fw-bold text-dark-blue mb-1" style="font-size: 0.88rem;">
                        ${escapeHtml(item.nama_dokumen || 'Dokumen Mutu')}
                    </h6>
                    <div class="d-flex align-items-center gap-2 text-muted small mb-2" style="font-size: 0.75rem;">
                        <span><i class="fas fa-hashtag me-1"></i>${escapeHtml(item.nomor_dokumen || '-')}</span>
                        <span>•</span>
                        <span><i class="fas fa-building me-1"></i>${unitName}</span>
                    </div>

                    <!-- Catatan Review GPM -->
                    <div class="p-2.5 rounded-3 border bg-light-subtle">
                        <div class="text-muted small fw-bold mb-1" style="font-size: 0.7rem; text-transform: uppercase;">
                            <i class="fas fa-comment-dots text-primary me-1"></i> Catatan &amp; Arahan GPM:
                        </div>
                        <div class="small text-dark" style="font-size: 0.8rem; line-height: 1.45;">
                            ${item.catatan ? escapeHtml(item.catatan) : '<span class="text-muted fst-italic">Tidak ada catatan khusus.</span>'}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';

    body.innerHTML = html;
}

// Filter riwayat di modal (All, Approve, Revisi)
function filterModalHistory(type, btn) {
    const btns = document.querySelectorAll('#modalHistoryFilterGroup button');
    btns.forEach(b => {
        b.classList.remove('active', 'btn-primary', 'btn-success', 'btn-danger');
        if (b.textContent.includes('Semua')) b.className = 'btn btn-outline-primary py-1 px-2.5';
        if (b.textContent.includes('Approve')) b.className = 'btn btn-outline-success py-1 px-2.5';
        if (b.textContent.includes('Revisi')) b.className = 'btn btn-outline-danger py-1 px-2.5';
    });

    if (type === 'all') {
        btn.className = 'btn btn-primary active py-1 px-2.5';
        renderModalHistoryList(currentGpmHistoryData);
    } else if (type === 'approve') {
        btn.className = 'btn btn-success active py-1 px-2.5';
        const filtered = currentGpmHistoryData.filter(i => ['menunggu_review_lpm', 'sesuai'].includes(i.status_hasil));
        renderModalHistoryList(filtered);
    } else if (type === 'revisi') {
        btn.className = 'btn btn-danger active py-1 px-2.5';
        const filtered = currentGpmHistoryData.filter(i => i.status_hasil === 'revisi_gpm');
        renderModalHistoryList(filtered);
    }
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
