<?php
/**
 * Super Admin: Pengaturan Hak Akses & Visibilitas Menu LPM (RBAC Dinamis)
 * SPMI PPEPP UNIKA Soegijapranata
 *
 * Khusus diperuntukkan bagi Administrator Utama (tu.lpm@unika.ac.id)
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<style>
/* Styling Khusus Matriks Hak Akses LPM */
.menu-permissions-page {
    max-width: 100%;
}

.perm-matrix-card {
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -2px rgba(15, 23, 42, 0.04);
    background: #FFFFFF;
}

.perm-matrix-table {
    border-collapse: separate;
    border-spacing: 0;
}

.perm-matrix-table thead tr th {
    background: #0B192C !important;
    color: #FFFFFF !important;
    vertical-align: middle;
    border-bottom: 2px solid #1E293B !important;
    border-top: none !important;
}

.perm-matrix-table thead tr th:first-child {
    border-top-left-radius: 0;
}

.perm-matrix-table thead tr th:last-child {
    border-top-right-radius: 0;
}

.perm-matrix-table thead .role-header-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.5rem;
}

.perm-matrix-table thead .role-email {
    color: #94A3B8 !important;
    font-size: 0.68rem;
    font-weight: 500;
    font-family: var(--font-mono, monospace);
    letter-spacing: -0.2px;
}

.perm-matrix-table thead .role-toggle-btn {
    font-size: 0.68rem;
    padding: 2px 7px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.08);
    color: #38BDF8 !important;
    border: 1px solid rgba(56, 189, 248, 0.25);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.15s ease;
}

.perm-matrix-table thead .role-toggle-btn:hover {
    background: #38BDF8;
    color: #0B192C !important;
    border-color: #38BDF8;
}

.perm-matrix-table thead .role-toggle-btn.btn-turn-off {
    color: #FCA5A5 !important;
    border-color: rgba(248, 113, 113, 0.25);
}

.perm-matrix-table thead .role-toggle-btn.btn-turn-off:hover {
    background: #EF4444;
    color: #FFFFFF !important;
    border-color: #EF4444;
}

/* Category Separator Header */
.perm-matrix-table tr.category-header-row td {
    background: #F1F5F9 !important;
    color: #1E293B !important;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 0.75rem 1.5rem !important;
    border-top: 1px solid #E2E8F0 !important;
    border-bottom: 1px solid #CBD5E1 !important;
}

/* Toggle Card in Cell */
.perm-toggle-box {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.6rem 0.85rem;
    border-radius: 10px;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    min-width: 145px;
    max-width: 165px;
    margin: 0 auto;
    transition: all 0.15s ease;
}

.perm-toggle-box:hover {
    background: #F1F5F9;
    border-color: #CBD5E1;
}

.perm-toggle-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    margin: 0 !important;
}

.perm-toggle-item .form-check-input {
    cursor: pointer;
    margin-top: 0;
    width: 2.1em;
    height: 1.15em;
}

.perm-toggle-item .form-check-label {
    cursor: pointer;
    font-size: 0.74rem;
    font-weight: 600;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    user-select: none;
}

/* Sub-code Badge */
.perm-route-code {
    font-size: 0.68rem;
    font-family: var(--font-mono, monospace);
    background: #F1F5F9;
    color: #475569;
    border: 1px solid #E2E8F0;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
}

/* Hover effect on data row */
.perm-data-row:hover td {
    background-color: #F8FAFC !important;
}

/* Locked Admin Row */
.perm-locked-row td {
    background-color: #FEF2F2 !important;
}
.perm-locked-row:hover td {
    background-color: #FEE2E2 !important;
}
</style>

<div class="menu-permissions-page px-2 px-md-3 py-1">

    <!-- Top Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4 mt-1">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                    <i class="fas fa-user-shield fs-5"></i>
                </div>
                <h3 class="fw-bold text-dark-blue mb-0">Hak Akses &amp; Pengaturan Menu LPM</h3>
                <span class="badge bg-danger rounded-pill px-3 py-1" style="font-size: 0.74rem;">
                    <i class="fas fa-lock me-1"></i> Khusus Administrator
                </span>
            </div>
            <p class="text-muted small mb-0 ps-lg-5">
                Atur secara fleksibel visibilitas modul dan izin aksi untuk setiap jabatan struktural Lembaga Penjaminan Mutu (LPM).
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 shadow-xs fw-semibold" onclick="resetDefaultConfirm()" title="Kembalikan semua hak akses ke pengaturan awal institusi">
                <i class="fas fa-rotate-left me-1.5 text-secondary"></i> Reset ke Default
            </button>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 py-1.5 shadow-sm bg-scu-blue border-0 fw-semibold" onclick="document.getElementById('formMenuPermissions').submit()">
                <i class="fas fa-save me-1.5"></i> Simpan Pengaturan Hak Akses
            </button>
        </div>
    </div>

    <!-- Alert Banner Edukasi & Aturan Hak Akses -->
    <div class="card border-0 shadow-xs rounded-4 mb-3.5" style="background: linear-gradient(135deg, rgba(239, 246, 255, 0.95), rgba(245, 243, 255, 0.95)); border-left: 4px solid #4F46E5 !important;">
        <div class="card-body p-3.5 d-flex flex-column flex-md-row align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(99, 102, 241, 0.15); color: #4F46E5; font-size: 1.15rem;">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold text-dark mb-1" style="font-size: 0.92rem;">
                    Panduan Konfigurasi Matriks Hak Akses LPM:
                </div>
                <div class="row g-2 text-muted" style="font-size: 0.82rem;">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-2 mb-1">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5 flex-shrink-0"><i class="fas fa-eye me-1"></i> Lihat Menu</span>
                            <span>Menentukan apakah menu tersebut tampil di navigasi/sidebar pejabat LPM terkait.</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-2 mb-1">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5 flex-shrink-0"><i class="fas fa-bolt me-1"></i> Izin Aksi</span>
                            <span>Bila dicentang, pejabat boleh menambah/mengubah data &amp; mereview dokumen. Bila mati, modul berstatus <strong>Lihat Saja</strong>.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Preset Buttons Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2.5 mb-3.5 bg-white p-3 rounded-4 border shadow-xs">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="small fw-bold text-secondary me-1"><i class="fas fa-wand-magic-sparkles text-primary me-1"></i> Shortcut Cepat:</span>
            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1" onclick="applyPreset('all')">
                <i class="fas fa-check-double me-1"></i> Beri Semua Akses Penuh
            </button>
            <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-3 py-1" onclick="applyPreset('readonly')">
                <i class="fas fa-eye me-1"></i> Jadikan Semua Lihat Saja (Read-Only)
            </button>
            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3 py-1" onclick="applyPreset('reset_view')">
                <i class="fas fa-arrows-rotate me-1"></i> Setel Ulang di Form
            </button>
        </div>
        <div class="text-muted small">
            <i class="fas fa-user-shield text-danger me-1"></i> Akun Utama (<code>tu.lpm@unika.ac.id</code>) otomatis memiliki seluruh hak akses sistem.
        </div>
    </div>

    <!-- Form & Matrix Card -->
    <form id="formMenuPermissions" method="POST" action="<?= base_url('admin/menu-permissions/save') ?>">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 perm-matrix-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 perm-matrix-table">
                    <thead>
                        <tr>
                            <th style="min-width: 280px; padding: 1.15rem 1.25rem;" class="fw-bold">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-layer-group text-warning fs-6"></i>
                                    <span style="font-size: 0.88rem; letter-spacing: 0.3px;">MENU / MODUL LPM</span>
                                </div>
                            </th>
                            <?php foreach ($lpmRoles as $rKey => $rMeta): ?>
                                <th class="text-center" style="min-width: 175px; padding: 1rem 0.65rem;">
                                    <div class="role-header-box">
                                        <span class="badge <?= $rMeta['badge'] ?> rounded-pill px-3 py-1" style="font-size: 0.74rem;">
                                            <i class="<?= $rMeta['icon'] ?> me-1"></i> <?= htmlspecialchars($rMeta['label']) ?>
                                        </span>
                                        <div class="role-email"><?= htmlspecialchars($rMeta['email']) ?></div>
                                        <div class="d-flex align-items-center justify-content-center gap-1.5 mt-1">
                                            <button type="button" class="role-toggle-btn" onclick="toggleColumnRole('<?= $rKey ?>', true)" title="Aktifkan seluruh menu role <?= htmlspecialchars($rMeta['label']) ?>">
                                                <i class="fas fa-check me-0.5"></i> Semua
                                            </button>
                                            <button type="button" class="role-toggle-btn btn-turn-off" onclick="toggleColumnRole('<?= $rKey ?>', false)" title="Matikan seluruh menu role <?= htmlspecialchars($rMeta['label']) ?>">
                                                <i class="fas fa-xmark me-0.5"></i> Mati
                                            </button>
                                        </div>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                        $currentCategory = '';
                        foreach ($menuList as $mKey => $mMeta): 
                            if ($currentCategory !== $mMeta['category']):
                                $currentCategory = $mMeta['category'];
                        ?>
                            <tr class="category-header-row">
                                <td colspan="<?= count($lpmRoles) + 1 ?>">
                                    <i class="fas fa-folder-tree text-primary me-2"></i> <?= strtoupper($currentCategory) ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                            <tr class="perm-data-row">
                                <!-- Kolom Nama Menu -->
                                <td class="ps-4 pe-3 py-3.5">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5 shadow-2xs" 
                                             style="width: 36px; height: 36px; background: rgba(37, 99, 235, 0.08); color: var(--scu-blue); font-size: 0.95rem; border: 1px solid rgba(37, 99, 235, 0.15);">
                                            <i class="<?= $mMeta['icon'] ?>"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark-blue mb-0.5" style="font-size: 0.9rem;">
                                                <?= htmlspecialchars($mMeta['title']) ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.74rem; line-height: 1.4; max-width: 300px;">
                                                <?= htmlspecialchars($mMeta['desc']) ?>
                                            </div>
                                            <div class="mt-1.5">
                                                <span class="perm-route-code">
                                                    <i class="fas fa-link me-1 opacity-75"></i><?= htmlspecialchars($mMeta['path']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kolom Toggle untuk Tiap Role LPM -->
                                <?php foreach ($lpmRoles as $rKey => $rMeta): 
                                    $isViewChecked = !empty($permissions[$rKey][$mKey]['can_view']);
                                    $isActionChecked = !empty($permissions[$rKey][$mKey]['can_action']);
                                ?>
                                    <td class="text-center py-3.5 px-2 border-start">
                                        <div class="perm-toggle-box">
                                            
                                            <!-- Checkbox 1: Tampilkan Menu (can_view) -->
                                            <div class="form-check form-switch perm-toggle-item">
                                                <label class="form-check-label text-secondary" for="view_<?= $rKey ?>_<?= $mKey ?>">
                                                    <i class="fas fa-eye text-primary"></i>
                                                    <span>Lihat</span>
                                                </label>
                                                <input class="form-check-input perm-view-cb perm-role-<?= $rKey ?>" 
                                                       type="checkbox" 
                                                       id="view_<?= $rKey ?>_<?= $mKey ?>" 
                                                       name="permissions[<?= $rKey ?>][<?= $mKey ?>][can_view]" 
                                                       value="1" 
                                                       <?= $isViewChecked ? 'checked' : '' ?>
                                                       onchange="handleViewToggle('<?= $rKey ?>', '<?= $mKey ?>')">
                                            </div>

                                            <!-- Checkbox 2: Izin Aksi (can_action) jika menu mendukung aksi -->
                                            <?php if ($mMeta['has_action']): ?>
                                                <div class="form-check form-switch perm-toggle-item">
                                                    <label class="form-check-label text-success" for="action_<?= $rKey ?>_<?= $mKey ?>">
                                                        <i class="fas fa-bolt text-success"></i>
                                                        <span>Aksi</span>
                                                    </label>
                                                    <input class="form-check-input perm-action-cb perm-action-role-<?= $rKey ?>" 
                                                           type="checkbox" 
                                                           id="action_<?= $rKey ?>_<?= $mKey ?>" 
                                                           name="permissions[<?= $rKey ?>][<?= $mKey ?>][can_action]" 
                                                           value="1" 
                                                           <?= ($isViewChecked && $isActionChecked) ? 'checked' : '' ?>
                                                           <?= !$isViewChecked ? 'disabled' : '' ?>
                                                           onchange="handleActionToggle('<?= $rKey ?>', '<?= $mKey ?>')">
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex align-items-center justify-content-between w-100 px-1 pt-0.5">
                                                    <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 0.66rem;">Hanya Lihat</span>
                                                    <input type="hidden" name="permissions[<?= $rKey ?>][<?= $mKey ?>][can_action]" value="1">
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Grup Menu Eksklusif Administrator -->
                        <tr class="category-header-row" style="background: #FEE2E2 !important; border-top: 2px solid #FCA5A5 !important; border-bottom: 2px solid #FCA5A5 !important;">
                            <td colspan="<?= count($lpmRoles) + 1 ?>" class="text-danger fw-bold">
                                <i class="fas fa-lock text-danger me-2"></i> MENU EKSKLUSIF ADMINISTRATOR UTAMA (TERKUNCI KE TU.LPM@UNIKA.AC.ID)
                            </td>
                        </tr>
                        <?php 
                        $lockedMenus = [
                            [
                                'title' => 'Kelola Beranda & Footer Publik',
                                'desc'  => 'Konfigurasi konten beranda publik, branding SISTA 2, tautan eksternal, dan informasi kontak.',
                                'path'  => 'admin/landing-settings',
                                'icon'  => 'fas fa-sliders'
                            ],
                            [
                                'title' => 'Manajemen Pengguna & Hak Akses',
                                'desc'  => 'Kelola data pengguna, aktivasi akun, penetapan unit, reset kata sandi, dan role institusi.',
                                'path'  => 'admin/users',
                                'icon'  => 'fas fa-users-gear'
                            ],
                            [
                                'title' => 'Hak Akses Menu LPM',
                                'desc'  => 'Halaman manajemen matriks hak akses ini (khusus root admin tu.lpm@unika.ac.id).',
                                'path'  => 'admin/menu-permissions',
                                'icon'  => 'fas fa-shield-halved'
                            ]
                        ];
                        foreach ($lockedMenus as $lItem):
                        ?>
                            <tr class="perm-locked-row">
                                <td class="ps-4 pe-3 py-3.5">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5 shadow-2xs" 
                                             style="width: 36px; height: 36px; background: rgba(220, 38, 38, 0.1); color: #DC2626; font-size: 0.95rem; border: 1px solid rgba(220, 38, 38, 0.2);">
                                            <i class="<?= $lItem['icon'] ?>"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-danger mb-0.5" style="font-size: 0.9rem;">
                                                <i class="fas fa-lock me-1"></i> <?= htmlspecialchars($lItem['title']) ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.74rem; line-height: 1.4; max-width: 300px;">
                                                <?= htmlspecialchars($lItem['desc']) ?>
                                            </div>
                                            <div class="mt-1.5">
                                                <span class="perm-route-code" style="background: #FEE2E2; border-color: #FECACA; color: #991B1B;">
                                                    <i class="fas fa-lock me-1 opacity-75"></i><?= htmlspecialchars($lItem['path']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="<?= count($lpmRoles) ?>" class="text-center py-3.5 px-3 text-muted border-start">
                                    <div class="d-inline-flex align-items-center gap-2 px-3.5 py-2 rounded-pill bg-white border border-danger border-opacity-25 text-danger shadow-2xs" style="font-size: 0.76rem; font-weight: 600;">
                                        <i class="fas fa-shield-halved text-danger"></i>
                                        <span>Khusus Super Admin (<code>tu.lpm@unika.ac.id</code>) &bull; Terkunci Otomatis</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <!-- Footer Save Action -->
            <div class="card-footer bg-white p-3.5 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 border-top">
                <div class="small text-muted d-flex align-items-center gap-2">
                    <i class="fas fa-circle-info text-primary fs-6"></i>
                    <span>Perubahan matriks hak akses akan langsung aktif seketika setelah tombol simpan ditekan.</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 py-2 shadow-sm bg-scu-blue border-0 fw-semibold">
                        <i class="fas fa-save me-1.5"></i> Simpan Pengaturan Hak Akses
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>

<!-- Form Request Reset Default (POST) -->
<form id="formResetPermissions" method="POST" action="<?= base_url('admin/menu-permissions/reset') ?>" style="display:none;">
</form>

<script>
function handleViewToggle(role, menu) {
    const viewCb = document.getElementById(`view_${role}_${menu}`);
    const actionCb = document.getElementById(`action_${role}_${menu}`);
    if (!viewCb) return;

    if (!viewCb.checked) {
        if (actionCb) {
            actionCb.checked = false;
            actionCb.disabled = true;
        }
    } else {
        if (actionCb) {
            actionCb.disabled = false;
            actionCb.checked = true; // Default aktifkan aksi saat menu dimunculkan
        }
    }
}

function handleActionToggle(role, menu) {
    const viewCb = document.getElementById(`view_${role}_${menu}`);
    const actionCb = document.getElementById(`action_${role}_${menu}`);
    if (!actionCb || !viewCb) return;

    if (actionCb.checked && !viewCb.checked) {
        viewCb.checked = true;
    }
}

function toggleColumnRole(role, enable) {
    const viewCheckboxes = document.querySelectorAll(`.perm-role-${role}`);
    const actionCheckboxes = document.querySelectorAll(`.perm-action-role-${role}`);

    viewCheckboxes.forEach(cb => {
        cb.checked = enable;
    });

    actionCheckboxes.forEach(cb => {
        cb.disabled = !enable;
        cb.checked = enable;
    });
}

function applyPreset(preset) {
    const allViewCbs = document.querySelectorAll('.perm-view-cb');
    const allActionCbs = document.querySelectorAll('.perm-action-cb');

    if (preset === 'all') {
        allViewCbs.forEach(cb => cb.checked = true);
        allActionCbs.forEach(cb => {
            cb.disabled = false;
            cb.checked = true;
        });
    } else if (preset === 'readonly') {
        allViewCbs.forEach(cb => cb.checked = true);
        allActionCbs.forEach(cb => {
            cb.disabled = false;
            cb.checked = false;
        });
    } else if (preset === 'reset_view') {
        allViewCbs.forEach(cb => cb.checked = true);
        allActionCbs.forEach(cb => {
            cb.disabled = false;
            cb.checked = true;
        });
    }
}

function resetDefaultConfirm() {
    const confirmMsg = 'Seluruh role LPM akan dikembalikan ke pengaturan awal institusi (semua menu aktif dengan hak akses penuh). Apakah Anda yakin?';
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Reset Hak Akses ke Default?',
            text: 'Seluruh role LPM akan dikembalikan ke pengaturan awal institusi (semua menu aktif dengan akses penuh).',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563EB',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Reset ke Default',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formResetPermissions').submit();
            }
        });
    } else {
        if (confirm(confirmMsg)) {
            document.getElementById('formResetPermissions').submit();
        }
    }
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
