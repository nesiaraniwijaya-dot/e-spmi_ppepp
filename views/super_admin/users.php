<?php
/**
 * Super Admin: Manajemen Pengguna & Hak Akses
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-blue mb-1">Manajemen Pengguna & Hak Akses</h3>
            <p class="text-muted small mb-0">Kelola akun Admin LPM, Dekanat, GPM, serta penugasan Admin Program Studi institusi.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0" data-bs-toggle="modal" data-bs-target="#modalUser" onclick="resetUserForm()">
            <i class="fas fa-user-plus me-1"></i> Tambah Pengguna Baru
        </button>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <div class="row g-2 align-items-center">
            <!-- Pencarian Cepat (Nama / Email) -->
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="fas fa-search"></i></span>
                    <input type="text" id="filterSearch" class="form-control border-start-0 ps-1" placeholder="Cari nama atau email pengguna..." oninput="applyUserFilters()">
                    <button class="btn btn-outline-secondary border-start-0" type="button" id="btnClearSearch" onclick="clearSearchInput()" style="display: none;" title="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Filter Role / Hak Akses -->
            <div class="col-12 col-sm-6 col-md-3">
                <select id="filterRole" class="form-select form-select-sm" onchange="applyUserFilters()">
                    <option value="">Semua Hak Akses / Role</option>
                    <optgroup label="Tingkat LPM (Universitas)">
                        <option value="super_admin">Admin (Super Admin)</option>
                        <option value="kepala_lpm">Kepala LPM</option>
                        <option value="sekretaris_lpm">Sekretaris LPM</option>
                        <option value="ketua_pusat_spm">Ketua Pusat SPM</option>
                        <option value="ketua_pusat_ami">Ketua Pusat AMI</option>
                        <option value="ketua_pusat_pemeringkatan">Ketua Pusat Pemeringkatan</option>
                        <option value="tu_lpm">TU LPM</option>
                    </optgroup>
                    <optgroup label="Tingkat Fakultas">
                        <option value="dekan">Dekan Fakultas</option>
                        <option value="wadek">Wakil Dekan Fakultas</option>
                        <option value="gpm">GPM Fakultas</option>
                    </optgroup>
                    <optgroup label="Tingkat Program Studi">
                        <option value="kaprodi">Kaprodi</option>
                        <option value="sekprodi">Sekprodi</option>
                    </optgroup>
                    <optgroup label="Akses Publik &amp; Khusus">
                        <option value="pengguna">Pengguna (Akses Penuh Dokumen)</option>
                        <option value="testing">Akun Testing (Multi-Role)</option>
                    </optgroup>
                </select>
            </div>

            <!-- Filter Unit Kerja / Fakultas -->
            <div class="col-12 col-sm-6 col-md-3">
                <select id="filterFakultas" class="form-select form-select-sm" onchange="applyUserFilters()">
                    <option value="">Semua Unit Kerja / Fakultas</option>
                    <option value="lpm">Lembaga Penjaminan Mutu (LPM / Universitas)</option>
                    <?php foreach ($fakultasList as $fk): ?>
                        <option value="<?= $fk['id'] ?>"><?= htmlspecialchars($fk['nama_fakultas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Status Akun -->
            <div class="col-12 col-sm-6 col-md-2">
                <select id="filterStatus" class="form-select form-select-sm" onchange="applyUserFilters()">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Non-Aktif</option>
                </select>
            </div>
        </div>

        <!-- Filter Meta Bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 mt-2 border-top">
            <div class="small text-muted d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark-blue border px-2.5 py-1.5 rounded-pill">
                    <i class="fas fa-users text-primary me-1"></i>
                    Menampilkan <strong id="visibleUsersCount"><?= count($usersList) ?></strong> dari <?= count($usersList) ?> pengguna
                </span>
                <span id="activeFilterBadge" class="badge bg-primary-subtle text-primary border rounded-pill px-2.5 py-1.5" style="display: none;">
                    <i class="fas fa-filter me-1"></i> Filter Aktif
                </span>
            </div>
            <div>
                <button type="button" id="btnResetFilters" class="btn btn-sm btn-link text-decoration-none text-muted p-0" onclick="resetUserFilters()" style="display: none;">
                    <i class="fas fa-undo-alt me-1"></i> Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Pengguna</th>
                        <th>Email Institusi</th>
                        <th>Hak Akses (Role)</th>
                        <th>Penugasan Unit / Prodi</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php if (empty($usersList)): ?>
                        <tr id="emptyInitialRow"><td colspan="7" class="text-center py-4 text-muted">Belum ada data pengguna.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($usersList as $u): 
                            $isLpmRole = in_array($u['role'], ['super_admin', 'admin_lpm', 'kepala_lpm', 'sekretaris_lpm', 'ketua_pusat_spm', 'ketua_pusat_ami', 'ketua_pusat_pemeringkatan', 'tu_lpm']);
                            $unitType = $isLpmRole ? 'lpm' : (in_array($u['role'], ['dekan', 'wadek', 'gpm']) ? 'fakultas' : (in_array($u['role'], ['kaprodi', 'sekprodi']) ? 'prodi' : 'khusus'));
                            $searchHaystack = strtolower(htmlspecialchars($u['name'] . ' ' . $u['email'] . ' ' . ($u['nama_prodi'] ?? '') . ' ' . ($u['nama_fakultas'] ?? '') . ' ' . $u['role']));
                        ?>
                            <tr class="user-row" 
                                data-id="<?= $u['id'] ?>"
                                data-name="<?= htmlspecialchars(strtolower($u['name'])) ?>"
                                data-email="<?= htmlspecialchars(strtolower($u['email'])) ?>"
                                data-role="<?= htmlspecialchars($u['role']) ?>"
                                data-fakultas-id="<?= htmlspecialchars((string)($u['resolved_fakultas_id'] ?? '')) ?>"
                                data-unit-type="<?= $unitType ?>"
                                data-status="<?= $u['is_active'] ? '1' : '0' ?>"
                                data-search="<?= $searchHaystack ?>">
                                <td class="text-center text-muted fw-semibold user-no"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark-blue"><?= htmlspecialchars($u['name']) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;">ID Akun: #<?= $u['id'] ?></div>
                                </td>
                                <td>
                                    <span class="user-email-text"><?= htmlspecialchars($u['email']) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $roleBadge = match($u['role']) {
                                        'testing'                   => '<span class="badge text-white" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);"><i class="fas fa-flask me-1"></i>Akun Testing (Multi-Role)</span>',
                                        'kepala_lpm'                => '<span class="badge bg-warning text-dark"><i class="fas fa-user-tie me-1"></i>Kepala LPM</span>',
                                        'sekretaris_lpm'            => '<span class="badge text-white" style="background:#2563EB;"><i class="fas fa-user-check me-1"></i>Sekretaris LPM</span>',
                                        'ketua_pusat_spm'           => '<span class="badge text-white" style="background:#4338CA;"><i class="fas fa-award me-1"></i>Ketua Pusat SPM</span>',
                                        'ketua_pusat_ami'           => '<span class="badge text-white" style="background:#0F766E;"><i class="fas fa-clipboard-check me-1"></i>Ketua Pusat AMI</span>',
                                        'ketua_pusat_pemeringkatan' => '<span class="badge text-white" style="background:#7C3AED;"><i class="fas fa-medal me-1"></i>Ketua Pusat Pemeringkatan</span>',
                                        'tu_lpm'                    => '<span class="badge bg-info text-dark"><i class="fas fa-briefcase me-1"></i>TU LPM</span>',
                                        'super_admin'               => '<span class="badge bg-danger"><i class="fas fa-user-shield me-1"></i>Admin</span>',
                                        'admin_lpm'                 => '<span class="badge bg-danger"><i class="fas fa-user-shield me-1"></i>Admin</span>',
                                        'kepala_pusat_mutu'         => '<span class="badge text-white" style="background:#4338CA;"><i class="fas fa-award me-1"></i>Ketua Pusat Mutu</span>',
                                        'dekan'                     => '<span class="badge bg-primary"><i class="fas fa-landmark me-1"></i>Dekan</span>',
                                        'wadek'                     => '<span class="badge text-white" style="background:#0284C7;"><i class="fas fa-user-tie me-1"></i>Wakil Dekan</span>',
                                        'gpm'                       => '<span class="badge text-white" style="background: #8B5CF6;"><i class="fas fa-shield-halved me-1"></i>GPM Fakultas</span>',
                                        'kaprodi'                   => '<span class="badge bg-success"><i class="fas fa-graduation-cap me-1"></i>Kaprodi</span>',
                                        'sekprodi'                  => '<span class="badge text-white" style="background:#0D9488;"><i class="fas fa-signature me-1"></i>Sekprodi</span>',
                                        'pengguna'                  => '<span class="badge text-white" style="background:#059669;"><i class="fas fa-user-check me-1"></i>Pengguna (Akses Penuh)</span>',
                                        default                     => '<span class="badge bg-secondary">' . htmlspecialchars(ucfirst($u['role'])) . '</span>'
                                    };
                                    echo $roleBadge;
                                    ?>
                                </td>
                                <td>
                                    <?php if ($u['role'] === 'testing'): ?>
                                        <span class="badge bg-primary-subtle text-primary border"><i class="fas fa-arrows-split-up-and-left me-1"></i>Seluruh Unit &amp; Multi-Role</span>
                                        <div class="text-muted" style="font-size: 0.72rem;">Dapat berganti &amp; akses seluruh peran</div>
                                    <?php elseif (in_array($u['role'], ['kaprodi', 'sekprodi'])): ?>
                                        <?php if ($u['nama_prodi']): ?>
                                            <div class="small fw-semibold text-dark"><?= htmlspecialchars($u['nama_prodi']) ?> (<?= htmlspecialchars($u['jenjang']) ?>)</div>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($u['nama_fakultas']) ?></div>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border">Belum dipetakan</span>
                                        <?php endif; ?>
                                    <?php elseif (in_array($u['role'], ['dekan', 'wadek', 'gpm'])): ?>
                                        <?php if ($u['nama_fakultas']): ?>
                                            <div class="small fw-semibold text-dark"><i class="fas fa-landmark me-1 text-info"></i><?= htmlspecialchars($u['nama_fakultas']) ?></div>
                                            <div class="text-muted" style="font-size: 0.72rem;">Tingkat Fakultas (Dekanat &amp; GPM)</div>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border">Belum dipetakan</span>
                                        <?php endif; ?>
                                    <?php elseif ($u['role'] === 'pengguna'): ?>
                                        <span class="text-success small fw-semibold"><i class="fas fa-unlock-keyhole text-success me-1"></i>Akses Dokumen Web Publik Penuh</span>
                                        <div class="text-muted" style="font-size: 0.72rem;">Tanpa Batasan Halaman Pratinjau</div>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-building-columns text-primary me-1"></i>Tingkat LPM (Universitas)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($u['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalUser"
                                                title="Ubah Data Pengguna"
                                                onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($u['id'] != Auth::id()): ?>
                                            <form action="<?= base_url('admin/users/delete/' . $u['id']) ?>" method="POST" class="d-inline">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 btn-delete-confirm" 
                                                        title="Hapus Pengguna"
                                                        data-name="<?= htmlspecialchars($u['name']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Empty state row when filters find no match -->
                    <tr id="emptyUserFilterRow" style="display: none;">
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                            <h6 class="fw-bold mb-1">Pengguna Tidak Ditemukan</h6>
                            <p class="small text-muted mb-3">Tidak ada data pengguna yang cocok dengan kriteria pencarian atau filter yang dipilih.</p>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="resetUserFilters()">
                                <i class="fas fa-undo-alt me-1"></i> Reset Semua Filter
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Tambah/Edit Pengguna -->
<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('admin/users/save') ?>" method="POST" id="formUser" onsubmit="return validateUserSubmit()">
                <input type="hidden" name="id" id="user_id" value="">
                
                <div class="modal-header bg-dark-blue text-white">
                    <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalUserTitle">Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="user_name" class="form-label fw-semibold small text-secondary">Nama Lengkap & Gelar *</label>
                        <input type="text" class="form-control" id="user_name" name="name" placeholder="Nama pengguna" required>
                    </div>

                    <div class="mb-3">
                        <label for="user_email" class="form-label fw-semibold small text-secondary">Alamat Email Institusi (@unika.ac.id) *</label>
                        <input type="email" class="form-control" id="user_email" name="email" placeholder="nama@unika.ac.id" required oninput="checkDuplicateEmailLive()">
                        
                        <!-- Peringatan Duplikat Email (Live UI) -->
                        <div id="emailDuplicateWarning" class="alert alert-danger py-2 px-3 mt-2 small rounded-3" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <span id="emailDuplicateMsg">Alamat email ini sudah terdaftar dalam sistem.</span>
                        </div>

                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                            <i class="fab fa-google text-primary me-1"></i> Autentikasi menggunakan <strong>Google SSO (@unika.ac.id)</strong> resmi universitas tanpa memerlukan kata sandi manual.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="user_role" class="form-label fw-semibold small text-secondary">Peran &amp; Hak Akses (Role) *</label>
                        <select class="form-select" id="user_role" name="role" required onchange="handleRoleChange(this.value)">
                            <optgroup label="Tingkat LPM (Universitas)">
                                <option value="kepala_lpm">Kepala Lembaga Penjaminan Mutu</option>
                                <option value="sekretaris_lpm">Sekretaris Lembaga Penjaminan Mutu</option>
                                <option value="ketua_pusat_spm">Ketua Pusat Pengembangan Sistem Penjaminan Mutu</option>
                                <option value="ketua_pusat_ami">Ketua Pusat Audit Mutu Internal</option>
                                <option value="ketua_pusat_pemeringkatan">Ketua Pusat Pemeringkatan</option>
                                <option value="tu_lpm">TU Lembaga Penjaminan Mutu</option>
                                <option value="super_admin">Admin (Super Admin - Akses Penuh)</option>
                            </optgroup>
                            <optgroup label="Tingkat Fakultas">
                                <option value="dekan">Dekan Fakultas</option>
                                <option value="wadek">Wakil Dekan Fakultas</option>
                                <option value="gpm">Gugus Penjaminan Mutu (GPM)</option>
                            </optgroup>
                            <optgroup label="Tingkat Program Studi">
                                <option value="kaprodi" selected>Ketua Program Studi (Kaprodi)</option>
                                <option value="sekprodi">Sekretaris Program Studi (Sekprodi)</option>
                            </optgroup>
                            <optgroup label="Akses Publik &amp; Khusus">
                                <option value="pengguna">Pengguna (Akses Penuh Dokumen Web Publik)</option>
                                <option value="testing">Akun Testing (Multi-Role)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-3" id="fakultasSelectContainer" style="display:none;">
                        <label for="user_fakultas_id" class="form-label fw-semibold small text-secondary">Penetapan Fakultas *</label>
                        <select class="form-select" id="user_fakultas_id" name="fakultas_id">
                            <option value="">-- Pilih Fakultas --</option>
                            <?php foreach ($fakultasList as $fk): ?>
                                <option value="<?= $fk['id'] ?>"><?= htmlspecialchars($fk['nama_fakultas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Dekan, Wakil Dekan, atau GPM mengelola PPEPP tingkat fakultas dan profil Dekanat.</div>
                    </div>

                    <div class="mb-3" id="prodiSelectContainer">
                        <label for="user_prodi_id" class="form-label fw-semibold small text-secondary">Penetapan Program Studi *</label>
                        <select class="form-select" id="user_prodi_id" name="prodi_id">
                            <option value="">-- Pilih Program Studi --</option>
                            <?php foreach ($prodiList as $pr): ?>
                                <option value="<?= $pr['id'] ?>"><?= htmlspecialchars($pr['nama_prodi']) ?> (<?= htmlspecialchars($pr['jenjang']) ?> - <?= htmlspecialchars($pr['nama_fakultas']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Kaprodi dan Sekprodi mengelola siklus PPEPP dan dokumen mutu program studi.</div>
                    </div>

                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="user_is_active" name="is_active" value="1" checked>
                        <label class="form-check-label small fw-semibold" for="user_is_active">Status Akun Aktif</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitUser" class="btn btn-primary btn-sm px-4 rounded-pill bg-scu-blue border-0 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Data pengguna yang sudah ada untuk validasi cepat di client-side
const existingUsersList = <?= json_encode(array_map(function($u) {
    return [
        'id' => (int)$u['id'],
        'name' => $u['name'],
        'email' => strtolower(trim($u['email'])),
        'role' => $u['role']
    ];
}, $usersList ?? [])) ?>;

function handleRoleChange(role) {
    const prodiContainer = document.getElementById('prodiSelectContainer');
    const prodiSelect = document.getElementById('user_prodi_id');
    const fakultasContainer = document.getElementById('fakultasSelectContainer');
    const fakultasSelect = document.getElementById('user_fakultas_id');

    if (['kaprodi', 'sekprodi'].includes(role)) {
        prodiContainer.style.display = 'block';
        prodiSelect.required = true;
        fakultasContainer.style.display = 'none';
        fakultasSelect.required = false;
        fakultasSelect.value = '';
    } else if (['dekan', 'wadek', 'gpm'].includes(role)) {
        prodiContainer.style.display = 'none';
        prodiSelect.required = false;
        prodiSelect.value = '';
        fakultasContainer.style.display = 'block';
        fakultasSelect.required = true;
    } else {
        prodiContainer.style.display = 'none';
        prodiSelect.required = false;
        prodiSelect.value = '';
        fakultasContainer.style.display = 'none';
        fakultasSelect.required = false;
        fakultasSelect.value = '';
    }
}

function resetUserForm() {
    document.getElementById('modalUserTitle').textContent = 'Tambah Pengguna Baru';
    document.getElementById('user_id').value = '';
    document.getElementById('user_name').value = '';
    document.getElementById('user_email').value = '';
    document.getElementById('user_role').value = 'kaprodi';
    document.getElementById('user_prodi_id').value = '';
    document.getElementById('user_fakultas_id').value = '';
    document.getElementById('user_is_active').checked = true;
    
    // Sembunyikan peringatan email duplikat
    hideDuplicateEmailWarning();
    handleRoleChange('kaprodi');
}

function editUser(data) {
    document.getElementById('modalUserTitle').textContent = 'Ubah Data Pengguna';
    document.getElementById('user_id').value = data.id;
    document.getElementById('user_name').value = data.name;
    document.getElementById('user_email').value = data.email;
    document.getElementById('user_role').value = data.role;
    document.getElementById('user_prodi_id').value = data.prodi_id || '';
    document.getElementById('user_fakultas_id').value = data.fakultas_id || data.resolved_fakultas_id || '';
    document.getElementById('user_is_active').checked = data.is_active == 1;
    
    // Sembunyikan peringatan email duplikat
    hideDuplicateEmailWarning();
    handleRoleChange(data.role);
}

function checkDuplicateEmailLive() {
    const emailInput = document.getElementById('user_email');
    const currentId = parseInt(document.getElementById('user_id').value) || 0;
    const typedEmail = (emailInput.value || '').trim().toLowerCase();
    const warnBox = document.getElementById('emailDuplicateWarning');
    const warnMsg = document.getElementById('emailDuplicateMsg');
    const submitBtn = document.getElementById('btnSubmitUser');

    if (!typedEmail) {
        hideDuplicateEmailWarning();
        return false;
    }

    const matched = existingUsersList.find(u => u.email === typedEmail && u.id !== currentId);
    if (matched) {
        warnMsg.innerHTML = `Peringatan: Alamat email <strong>${typedEmail}</strong> sudah terdaftar atas nama <strong>${matched.name}</strong>. Silakan gunakan email lain.`;
        warnBox.style.display = 'block';
        emailInput.classList.add('is-invalid');
        submitBtn.disabled = true;
        return true;
    } else {
        hideDuplicateEmailWarning();
        return false;
    }
}

function hideDuplicateEmailWarning() {
    const warnBox = document.getElementById('emailDuplicateWarning');
    const emailInput = document.getElementById('user_email');
    const submitBtn = document.getElementById('btnSubmitUser');
    if (warnBox) warnBox.style.display = 'none';
    if (emailInput) emailInput.classList.remove('is-invalid');
    if (submitBtn) submitBtn.disabled = false;
}

function validateUserSubmit() {
    if (checkDuplicateEmailLive()) {
        alert('Alamat email tersebut sudah terdaftar dalam sistem. Mohon gunakan email lain.');
        return false;
    }
    return true;
}

// ==================== FILTERING SISTEM ====================
function applyUserFilters() {
    const searchVal = (document.getElementById('filterSearch').value || '').trim().toLowerCase();
    const roleVal = document.getElementById('filterRole').value;
    const fakultasVal = document.getElementById('filterFakultas').value;
    const statusVal = document.getElementById('filterStatus').value;

    const btnClearSearch = document.getElementById('btnClearSearch');
    if (btnClearSearch) {
        btnClearSearch.style.display = searchVal ? 'block' : 'none';
    }

    const rows = document.querySelectorAll('.user-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const rowSearch = row.getAttribute('data-search') || '';
        const rowRole = row.getAttribute('data-role') || '';
        const rowFakultasId = row.getAttribute('data-fakultas-id') || '';
        const rowUnitType = row.getAttribute('data-unit-type') || '';
        const rowStatus = row.getAttribute('data-status') || '';

        // 1. Cek Pencarian Teks
        let matchSearch = true;
        if (searchVal) {
            matchSearch = rowSearch.includes(searchVal);
        }

        // 2. Cek Filter Role
        let matchRole = true;
        if (roleVal) {
            if (roleVal === 'super_admin') {
                matchRole = (rowRole === 'super_admin' || rowRole === 'admin_lpm');
            } else {
                matchRole = (rowRole === roleVal);
            }
        }

        // 3. Cek Filter Fakultas / Unit
        let matchFakultas = true;
        if (fakultasVal) {
            if (fakultasVal === 'lpm') {
                matchFakultas = (rowUnitType === 'lpm');
            } else {
                matchFakultas = (rowFakultasId === fakultasVal);
            }
        }

        // 4. Cek Filter Status
        let matchStatus = true;
        if (statusVal !== '') {
            matchStatus = (rowStatus === statusVal);
        }

        if (matchSearch && matchRole && matchFakultas && matchStatus) {
            row.style.display = '';
            visibleCount++;
            const noCell = row.querySelector('.user-no');
            if (noCell) noCell.textContent = visibleCount;
        } else {
            row.style.display = 'none';
        }
    });

    // Update Counter & Indikator
    const countEl = document.getElementById('visibleUsersCount');
    if (countEl) countEl.textContent = visibleCount;

    const emptyRow = document.getElementById('emptyUserFilterRow');
    if (emptyRow) {
        emptyRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }

    const hasActiveFilters = Boolean(searchVal || roleVal || fakultasVal || statusVal !== '');
    const activeBadge = document.getElementById('activeFilterBadge');
    const resetBtn = document.getElementById('btnResetFilters');
    if (activeBadge) activeBadge.style.display = hasActiveFilters ? 'inline-block' : 'none';
    if (resetBtn) resetBtn.style.display = hasActiveFilters ? 'inline-block' : 'none';
}

function clearSearchInput() {
    const input = document.getElementById('filterSearch');
    if (input) input.value = '';
    applyUserFilters();
}

function resetUserFilters() {
    const searchInput = document.getElementById('filterSearch');
    const roleSelect = document.getElementById('filterRole');
    const fakultasSelect = document.getElementById('filterFakultas');
    const statusSelect = document.getElementById('filterStatus');

    if (searchInput) searchInput.value = '';
    if (roleSelect) roleSelect.value = '';
    if (fakultasSelect) fakultasSelect.value = '';
    if (statusSelect) statusSelect.value = '';

    applyUserFilters();
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>