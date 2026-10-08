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
            <p class="text-muted small mb-0">Kelola akun Admin LPM serta penugasan Admin Program Studi institusi.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0" data-bs-toggle="modal" data-bs-target="#modalUser" onclick="resetUserForm()">
            <i class="fas fa-user-plus me-1"></i> Tambah Pengguna Baru
        </button>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Pengguna</th>
                        <th>Email Institusi</th>
                        <th>Hak Akses (Role)</th>
                        <th>Penugasan Prodi</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usersList)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data pengguna.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($usersList as $u): ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark-blue"><?= htmlspecialchars($u['name']) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;">ID Akun: #<?= $u['id'] ?></div>
                                </td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?php
                                    $roleBadge = match($u['role']) {
                                        'kepala_pusat_mutu' => '<span class="badge text-white" style="background:#4338CA;"><i class="fas fa-award me-1"></i>Ketua Pusat Mutu</span>',
                                        'kepala_lpm'        => '<span class="badge bg-warning text-dark"><i class="fas fa-user-tie me-1"></i>Kepala LPM</span>',
                                        'super_admin', 'admin_lpm' => '<span class="badge bg-danger"><i class="fas fa-user-shield me-1"></i>Admin LPM</span>',
                                        'dekan'             => '<span class="badge bg-primary"><i class="fas fa-landmark me-1"></i>Dekan</span>',
                                        'wadek'             => '<span class="badge text-white" style="background:#0284C7;"><i class="fas fa-user-tie me-1"></i>Wakil Dekan</span>',
                                        'gpm'               => '<span class="badge text-white" style="background: #8B5CF6;"><i class="fas fa-shield-halved me-1"></i>GPM Fakultas</span>',
                                        'kaprodi'           => '<span class="badge bg-success"><i class="fas fa-graduation-cap me-1"></i>Kaprodi</span>',
                                        'sekprodi'          => '<span class="badge text-white" style="background:#0D9488;"><i class="fas fa-signature me-1"></i>Sekprodi</span>',
                                        'pengguna'          => '<span class="badge text-white" style="background:#059669;"><i class="fas fa-user-check me-1"></i>Pengguna (Akses Penuh)</span>',
                                        default             => '<span class="badge bg-secondary">User</span>'
                                    };
                                    echo $roleBadge;
                                    ?>
                                </td>
                                <td>
                                    <?php if (in_array($u['role'], ['kaprodi', 'sekprodi'])): ?>
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
                                                onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($u['id'] != Auth::id()): ?>
                                            <form action="<?= base_url('admin/users/delete/' . $u['id']) ?>" method="POST" class="d-inline">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 btn-delete-confirm" data-name="<?= htmlspecialchars($u['name']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
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

<!-- Modal: Tambah/Edit Pengguna -->
<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('admin/users/save') ?>" method="POST">
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
                        <label for="user_email" class="form-label fw-semibold small text-secondary">Alamat Email Institusi *</label>
                        <input type="email" class="form-control" id="user_email" name="email" placeholder="nama@unika.ac.id" required>
                    </div>

                    <div class="mb-3">
                        <label for="user_password" class="form-label fw-semibold small text-secondary">
                            Kata Sandi (Password) <span class="badge bg-light text-secondary border font-normal">Opsional (Google SSO)</span> <span id="pwdHelp" class="text-muted fw-normal"></span>
                        </label>
                        <div class="input-group mb-1">
                            <input type="password" class="form-control" id="user_password" name="password" placeholder="Kosongkan jika pengguna login via Google SSO" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" id="btnTogglePwdModal" onclick="toggleModalPwdVisibility()" title="Lihat / Sembunyikan Password">
                                <i class="fas fa-eye" id="iconTogglePwdModal"></i>
                            </button>
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                            <i class="fab fa-google text-primary me-1"></i> Pengguna yang login via <strong>Google SSO</strong> tidak memerlukan kata sandi lokal. Kata sandi dikelola secara terpusat oleh akun Google institusi pengguna.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="user_role" class="form-label fw-semibold small text-secondary">Peran &amp; Hak Akses (Role) *</label>
                        <select class="form-select" id="user_role" name="role" required onchange="handleRoleChange(this.value)">
                            <optgroup label="Tingkat LPM (Universitas)">
                                <option value="kepala_pusat_mutu">Ketua Pusat Penjaminan Mutu</option>
                                <option value="kepala_lpm">Kepala LPM</option>
                                <option value="super_admin">Admin LPM (Super Admin)</option>
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
                            <optgroup label="Tingkat Pengguna / Publik">
                                <option value="pengguna">Pengguna / Civitas (Akses Penuh Dokumen Web Publik)</option>
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
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill bg-scu-blue border-0 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
    document.getElementById('user_password').value = '';
    document.getElementById('user_password').placeholder = 'Kosongkan jika pengguna login via Google SSO';
    document.getElementById('user_password').type = 'password';
    document.getElementById('iconTogglePwdModal').className = 'fas fa-eye';
    document.getElementById('user_password').required = false;
    document.getElementById('pwdHelp').textContent = '';
    document.getElementById('user_role').value = 'kaprodi';
    document.getElementById('user_prodi_id').value = '';
    document.getElementById('user_fakultas_id').value = '';
    document.getElementById('user_is_active').checked = true;
    handleRoleChange('kaprodi');
}

function editUser(data) {
    document.getElementById('modalUserTitle').textContent = 'Ubah Data Pengguna';
    document.getElementById('user_id').value = data.id;
    document.getElementById('user_name').value = data.name;
    document.getElementById('user_email').value = data.email;
    document.getElementById('user_password').value = '';
    document.getElementById('user_password').placeholder = 'Kosongkan jika tidak ingin mengubah password';
    document.getElementById('user_password').type = 'password';
    document.getElementById('iconTogglePwdModal').className = 'fas fa-eye';
    document.getElementById('user_password').required = false;
    document.getElementById('pwdHelp').textContent = '';
    document.getElementById('user_role').value = data.role;
    document.getElementById('user_prodi_id').value = data.prodi_id || '';
    document.getElementById('user_fakultas_id').value = data.fakultas_id || data.resolved_fakultas_id || '';
    document.getElementById('user_is_active').checked = data.is_active == 1;
    handleRoleChange(data.role);
}

function toggleModalPwdVisibility() {
    const input = document.getElementById('user_password');
    const icon = document.getElementById('iconTogglePwdModal');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
