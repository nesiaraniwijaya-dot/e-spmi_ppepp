<?php
/**
 * Super Admin: Pengaturan Hak Akses & Visibilitas Menu LPM (RBAC Dinamis)
 * SPMI PPEPP UNIKA Soegijapranata
 *
 * Khusus diperuntukkan bagi Administrator Utama (tu.lpm@unika.ac.id)
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">

    <!-- Top Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark-blue mb-0">Hak Akses &amp; Pengaturan Menu LPM</h3>
                <span class="badge bg-danger rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                    <i class="fas fa-lock me-1"></i> Khusus Admin
                </span>
            </div>
            <p class="text-muted small mb-0">
                Atur secara fleksibel siapa saja pejabat struktural LPM yang dapat melihat menu, atau muncul di menu tetapi hanya berstatus <strong>Lihat Saja (Read-Only)</strong>.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs" onclick="resetDefaultConfirm()">
                <i class="fas fa-rotate-left me-1"></i> Reset ke Default
            </button>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm bg-scu-blue border-0 fw-semibold" onclick="document.getElementById('formMenuPermissions').submit()">
                <i class="fas fa-save me-1.5"></i> Simpan Pengaturan Hak Akses
            </button>
        </div>
    </div>

    <!-- Alert Banner Edukasi & Aturan Hak Akses -->
    <div class="card border-0 shadow-xs rounded-4 mb-4" style="background: linear-gradient(135deg, rgba(239, 246, 255, 0.95), rgba(245, 243, 255, 0.95)); border-left: 4px solid var(--purple) !important;">
        <div class="card-body p-3.5 d-flex flex-column flex-md-row align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(99, 102, 241, 0.15); color: #4F46E5; font-size: 1.1rem;">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">
                    Panduan Konfigurasi Matriks Hak Akses LPM:
                </div>
                <div class="row g-2 text-muted" style="font-size: 0.8rem;">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5"><i class="fas fa-eye me-1"></i> Lihat Menu</span>
                            <span>Menentukan apakah menu tersebut tampil di navigasi/sidebar pejabat LPM terkait.</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5"><i class="fas fa-bolt me-1"></i> Izin Aksi</span>
                            <span>Bila dicentang, pejabat boleh menambah/mengubah data &amp; mereview dokumen. Bila mati, halaman berstatus <strong>Lihat Saja</strong>.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Preset Buttons -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 bg-white p-2.5 rounded-3 border shadow-xs">
        <div class="d-flex align-items-center gap-2">
            <span class="small fw-bold text-secondary me-1"><i class="fas fa-wand-magic-sparkles text-primary me-1"></i> Shortcut Cepat:</span>
            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1" onclick="applyPreset('all')">
                <i class="fas fa-check-double me-1"></i> Beri Semua Akses Penuh
            </button>
            <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1" onclick="applyPreset('readonly')">
                <i class="fas fa-eye me-1"></i> Jadikan Semua Lihat Saja (Read-Only)
            </button>
        </div>
        <div class="text-muted small">
            <i class="fas fa-user-shield text-danger me-1"></i> Akun Admin (<code>tu.lpm@unika.ac.id</code>) otomatis memiliki seluruh hak akses sistem.
        </div>
    </div>

    <!-- Form & Matrix Card -->
    <form id="formMenuPermissions" method="POST" action="<?= base_url('admin/menu-permissions/save') ?>">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #0A192F; color: #fff;">
                        <tr>
                            <th style="min-width: 260px; padding: 1rem;" class="fw-bold">
                                <i class="fas fa-layer-group me-1.5 text-warning"></i> Menu / Modul LPM
                            </th>
                            <?php foreach ($lpmRoles as $rKey => $rMeta): ?>
                                <th class="text-center" style="min-width: 175px; padding: 0.85rem 0.5rem;">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="badge <?= $rMeta['badge'] ?> rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                            <i class="<?= $rMeta['icon'] ?> me-1"></i> <?= htmlspecialchars($rMeta['label']) ?>
                                        </span>
                                        <div class="text-white-50" style="font-size: 0.68rem; font-weight: normal;"><?= htmlspecialchars($rMeta['email']) ?></div>
                                        <div class="d-flex align-items-center justify-content-center gap-2 mt-1 text-white-50" style="font-size: 0.65rem;">
                                            <button type="button" class="btn btn-xs btn-link text-white-50 p-0 text-decoration-none hover-text-white" onclick="toggleColumnRole('<?= $rKey ?>', true)" title="Aktifkan seluruh menu role ini">Semua</button>
                                            <span>·</span>
                                            <button type="button" class="btn btn-xs btn-link text-white-50 p-0 text-decoration-none hover-text-white" onclick="toggleColumnRole('<?= $rKey ?>', false)" title="Matikan seluruh menu role ini">Mati</button>
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
                            <tr class="table-light">
                                <td colspan="<?= count($lpmRoles) + 1 ?>" class="fw-bold text-dark px-3 py-2" style="font-size: 0.82rem; letter-spacing: 0.3px;">
                                    <i class="fas fa-folder-tree text-primary me-1.5"></i> <?= strtoupper($currentCategory) ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                            <tr>
                                <!-- Kolom Nama Menu -->
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-start gap-2.5">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" 
                                             style="width: 32px; height: 32px; background: rgba(37, 99, 235, 0.08); color: var(--scu-blue); font-size: 0.9rem;">
                                            <i class="<?= $mMeta['icon'] ?>"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark-blue mb-0.5" style="font-size: 0.88rem;">
                                                <?= htmlspecialchars($mMeta['title']) ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem; line-height: 1.35; max-width: 280px;">
                                                <?= htmlspecialchars($mMeta['desc']) ?>
                                            </div>
                                            <div class="mt-1">
                                                <code style="font-size: 0.68rem; background: rgba(0,0,0,0.04); padding: 1px 4px; border-radius: 4px; color: #475569;">
                                                    <?= htmlspecialchars($mMeta['path']) ?>
                                                </code>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kolom Toggle untuk Tiap Role LPM -->
                                <?php foreach ($lpmRoles as $rKey => $rMeta): 
                                    $isViewChecked = !empty($permissions[$rKey][$mKey]['can_view']);
                                    $isActionChecked = !empty($permissions[$rKey][$mKey]['can_action']);
                                ?>
                                    <td class="text-center py-3 border-start">
                                        <div class="d-inline-flex flex-column align-items-center gap-1.5 p-2 rounded-3" style="background: rgba(248, 250, 252, 0.8); min-width: 140px;">
                                            
                                            <!-- Checkbox 1: Tampilkan Menu (can_view) -->
                                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                <input class="form-check-input perm-view-cb perm-role-<?= $rKey ?>" 
                                                       type="checkbox" 
                                                       id="view_<?= $rKey ?>_<?= $mKey ?>" 
                                                       name="permissions[<?= $rKey ?>][<?= $mKey ?>][can_view]" 
                                                       value="1" 
                                                       <?= $isViewChecked ? 'checked' : '' ?>
                                                       onchange="handleViewToggle('<?= $rKey ?>', '<?= $mKey ?>')">
                                                <label class="form-check-label small fw-semibold text-secondary" for="view_<?= $rKey ?>_<?= $mKey ?>" style="font-size: 0.75rem; cursor: pointer;">
                                                    <i class="fas fa-eye text-primary me-0.5"></i> Lihat
                                                </label>
                                            </div>

                                            <!-- Checkbox 2: Izin Aksi (can_action) jika menu mendukung aksi -->
                                            <?php if ($mMeta['has_action']): ?>
                                                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                    <input class="form-check-input perm-action-cb perm-action-role-<?= $rKey ?>" 
                                                           type="checkbox" 
                                                           id="action_<?= $rKey ?>_<?= $mKey ?>" 
                                                           name="permissions[<?= $rKey ?>][<?= $mKey ?>][can_action]" 
                                                           value="1" 
                                                           <?= ($isViewChecked && $isActionChecked) ? 'checked' : '' ?>
                                                           <?= !$isViewChecked ? 'disabled' : '' ?>
                                                           onchange="handleActionToggle('<?= $rKey ?>', '<?= $mKey ?>')">
                                                    <label class="form-check-label small fw-semibold text-success" for="action_<?= $rKey ?>_<?= $mKey ?>" style="font-size: 0.75rem; cursor: pointer;">
                                                        <i class="fas fa-bolt text-success me-0.5"></i> Aksi
                                                    </label>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-muted" style="font-size: 0.7rem;">
                                                    <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 0.65rem;">Hanya Lihat</span>
                                                    <input type="hidden" name="permissions[<?= $rKey ?>][<?= $mKey ?>][can_action]" value="1">
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Grup Menu Eksklusif Administrator -->
                        <tr class="table-light">
                            <td colspan="<?= count($lpmRoles) + 1 ?>" class="fw-bold text-dark px-3 py-2" style="font-size: 0.82rem; letter-spacing: 0.3px;">
                                <i class="fas fa-lock text-danger me-1.5"></i> MENU EKSKLUSIF ADMINISTRATOR UTAMA (TERKUNCI)
                            </td>
                        </tr>
                        <?php 
                        $lockedMenus = [
                            [
                                'title' => 'Kelola Beranda & Footer Publik',
                                'desc'  => 'Konfigurasi konten beranda publik, branding MITRA, tautan eksternal, dan informasi kontak.',
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
                            <tr style="background: rgba(254, 242, 242, 0.4);">
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-start gap-2.5">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" 
                                             style="width: 32px; height: 32px; background: rgba(220, 38, 38, 0.1); color: #DC2626; font-size: 0.9rem;">
                                            <i class="<?= $lItem['icon'] ?>"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-danger mb-0.5" style="font-size: 0.88rem;">
                                                <i class="fas fa-lock me-1"></i> <?= htmlspecialchars($lItem['title']) ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem; line-height: 1.35; max-width: 280px;">
                                                <?= htmlspecialchars($lItem['desc']) ?>
                                            </div>
                                            <div class="mt-1">
                                                <code style="font-size: 0.68rem; background: rgba(0,0,0,0.04); padding: 1px 4px; border-radius: 4px; color: #475569;">
                                                    <?= htmlspecialchars($lItem['path']) ?>
                                                </code>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="<?= count($lpmRoles) ?>" class="text-center py-3 text-muted border-start">
                                    <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill bg-light border text-danger small fw-semibold" style="font-size: 0.74rem;">
                                        <i class="fas fa-shield-cat"></i> Khusus Admin (<code>tu.lpm@unika.ac.id</code>) · Tidak dapat diakses oleh role LPM lainnya
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <!-- Footer Save Action -->
            <div class="card-footer bg-white p-3 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 border-top">
                <div class="small text-muted">
                    <i class="fas fa-info-circle me-1 text-primary"></i> Perubahan hak akses akan langsung berlaku seketika setelah tombol simpan ditekan.
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm bg-scu-blue border-0 fw-semibold">
                        <i class="fas fa-check me-1.5"></i> Simpan Pengaturan Hak Akses
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>

<!-- Modal Konfirmasi Reset Default -->
<form id="formResetPermissions" method="POST" action="<?= base_url('admin/menu-permissions/reset') ?>">
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
    }
}

function resetDefaultConfirm() {
    Swal.fire({
        title: 'Reset Hak Akses ke Default?',
        text: 'Seluruh role LPM akan dikembalikan ke pengaturan awal institusi (akses penuh untuk seluruh menu).',
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
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/admin_footer.php'; ?>

