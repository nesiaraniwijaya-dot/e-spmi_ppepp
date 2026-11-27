<?php
/**
 * Super Admin: Manajemen Pengguna & Hak Akses
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
$currentAdminId = Auth::id();
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
                        <th>Peran Utama</th>
                        <th>Penugasan Unit &amp; Prodi</th>
                        <th class="text-center" style="width: 90px;">Status</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
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
                            $hasMulti = !empty($u['assignments']) && count($u['assignments']) > 1;
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
                                    <div class="text-muted" style="font-size: 0.72rem;">ID: #<?= $u['id'] ?><?= $u['id'] == $currentAdminId ? ' <span class="badge bg-secondary-subtle text-secondary ms-1">Akun Anda</span>' : '' ?></div>
                                </td>
                                <td>
                                    <span class="user-email-text small"><?= htmlspecialchars($u['email']) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $roleBadge = match($u['role']) {
                                        'testing'                   => '<span class="badge text-white" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);"><i class="fas fa-flask me-1"></i>Akun Testing</span>',
                                        'kepala_lpm'                => '<span class="badge bg-warning text-dark"><i class="fas fa-user-tie me-1"></i>Kepala LPM</span>',
                                        'sekretaris_lpm'            => '<span class="badge text-white" style="background:#2563EB;"><i class="fas fa-user-check me-1"></i>Sekretaris LPM</span>',
                                        'ketua_pusat_spm'           => '<span class="badge text-white" style="background:#4338CA;"><i class="fas fa-award me-1"></i>Ketua Pusat SPM</span>',
                                        'ketua_pusat_ami'           => '<span class="badge text-white" style="background:#0F766E;"><i class="fas fa-clipboard-check me-1"></i>Ketua Pusat AMI</span>',
                                        'ketua_pusat_pemeringkatan' => '<span class="badge text-white" style="background:#7C3AED;"><i class="fas fa-medal me-1"></i>Ketua Pusat Pemeringkatan</span>',
                                        'tu_lpm'                    => '<span class="badge bg-info text-dark"><i class="fas fa-briefcase me-1"></i>TU LPM</span>',
                                        'super_admin', 'admin_lpm'  => '<span class="badge bg-danger"><i class="fas fa-user-shield me-1"></i>Admin</span>',
                                        'dekan'                     => '<span class="badge bg-primary"><i class="fas fa-landmark me-1"></i>Dekan</span>',
                                        'wadek'                     => '<span class="badge text-white" style="background:#0284C7;"><i class="fas fa-user-tie me-1"></i>Wakil Dekan</span>',
                                        'gpm'                       => '<span class="badge text-white" style="background: #8B5CF6;"><i class="fas fa-shield-halved me-1"></i>GPM Fakultas</span>',
                                        'kaprodi'                   => '<span class="badge bg-success"><i class="fas fa-graduation-cap me-1"></i>Kaprodi</span>',
                                        'sekprodi'                  => '<span class="badge text-white" style="background:#0D9488;"><i class="fas fa-signature me-1"></i>Sekprodi</span>',
                                        'pengguna'                  => '<span class="badge text-white" style="background:#059669;"><i class="fas fa-user-check me-1"></i>Pengguna Publik</span>',
                                        default                     => '<span class="badge bg-secondary">' . htmlspecialchars(ucfirst($u['role'])) . '</span>'
                                    };
                                    echo $roleBadge;
                                    ?>
                                    <?php if ($hasMulti): ?>
                                        <div class="mt-1">
                                            <span class="badge bg-warning-subtle text-dark border" style="font-size: 0.65rem;">
                                                <i class="fas fa-layer-group me-1"></i><?= count($u['assignments']) ?> Penugasan
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($hasMulti): ?>
                                        <!-- Tampilan Daftar Multi-Penugasan -->
                                        <div class="d-flex flex-column gap-1.5">
                                            <?php foreach ($u['assignments'] as $asg): 
                                                $asgRole = $asg['role'];
                                                $isDek = in_array($asgRole, ['dekan', 'wadek']);
                                                $isPrd = in_array($asgRole, ['kaprodi', 'sekprodi']);
                                                $asgTitle = match($asgRole) {
                                                    'dekan' => 'Dekan',
                                                    'wadek' => 'Wakil Dekan',
                                                    'kaprodi' => 'Kaprodi',
                                                    'sekprodi' => 'Sekprodi',
                                                    'gpm' => 'GPM',
                                                    default => ucfirst($asgRole)
                                                };
                                                $badgeStyle = $isDek ? 'background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE;' : ($isPrd ? 'background:#ECFDF5; color:#047857; border:1px solid #A7F3D0;' : 'background:#F3F4F6; color:#374151; border:1px solid #E5E7EB;');
                                                $targetUnit = !empty($asg['nama_prodi']) ? $asg['nama_prodi'] . ' (' . ($asg['jenjang'] ?? 'S1') . ')' : ($asg['nama_fakultas'] ?? '');
                                            ?>
                                                <div class="d-inline-flex flex-column px-2 py-1 rounded-2" style="<?= $badgeStyle ?> font-size: 0.73rem;">
                                                    <div class="d-flex align-items-center gap-1.5">
                                                        <i class="fas <?= $isDek ? 'fa-landmark' : 'fa-graduation-cap' ?>"></i>
                                                        <strong><?= htmlspecialchars($asgTitle) ?></strong> &bull; <?= htmlspecialchars($targetUnit) ?>
                                                    </div>
                                                    <?php if (!empty($asg['nama_lengkap_gelar'])): ?>
                                                        <div class="text-primary mt-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                                            <i class="fas fa-user-tag me-0.5"></i> <?= htmlspecialchars($asg['nama_lengkap_gelar']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif ($u['role'] === 'testing'): ?>
                                        <span class="badge bg-primary-subtle text-primary border"><i class="fas fa-arrows-split-up-and-left me-1"></i>Seluruh Unit &amp; Multi-Role</span>
                                        <div class="text-muted" style="font-size: 0.7rem;">Dapat berganti &amp; akses seluruh peran</div>
                                    <?php elseif (in_array($u['role'], ['kaprodi', 'sekprodi'])): ?>
                                        <?php if ($u['nama_prodi']): ?>
                                            <div class="small fw-semibold text-dark"><?= htmlspecialchars($u['nama_prodi']) ?> (<?= htmlspecialchars($u['jenjang']) ?>)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($u['nama_fakultas']) ?></div>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border">Belum dipetakan</span>
                                        <?php endif; ?>
                                    <?php elseif (in_array($u['role'], ['dekan', 'wadek', 'gpm'])): ?>
                                        <?php if ($u['nama_fakultas']): ?>
                                            <div class="small fw-semibold text-dark"><i class="fas fa-landmark me-1 text-info"></i><?= htmlspecialchars($u['nama_fakultas']) ?></div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Tingkat Fakultas</div>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border">Belum dipetakan</span>
                                        <?php endif; ?>
                                    <?php elseif ($u['role'] === 'pengguna'): ?>
                                        <span class="text-success small fw-semibold"><i class="fas fa-unlock-keyhole text-success me-1"></i>Akses Dokumen Web Publik</span>
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
                                                title="Ubah Data & Penugasan Pengguna"
                                                onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($u['id'] != $currentAdminId): ?>
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

<!-- Modal: Tambah/Edit Pengguna (Opsi A: Multi-Penugasan) -->
<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('admin/users/save') ?>" method="POST" id="formUser" onsubmit="return validateUserSubmit()">
                <input type="hidden" name="id" id="user_id" value="">
                
                <div class="modal-header bg-dark-blue text-white px-4 py-3">
                    <div>
                        <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalUserTitle">Tambah Pengguna Baru</h5>
                        <div class="small text-white-50" style="font-size: 0.72rem;">Atur identitas akun institusi dan penetapan hak akses operasional.</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 bg-light">
                    <!-- Peringatan Proteksi Akun Admin Sendiri -->
                    <div id="selfEditProtectionAlert" class="alert alert-warning py-2.5 px-3 mb-3 rounded-3 small border-warning border-opacity-25" style="display: none;">
                        <i class="fas fa-user-shield me-1"></i> <strong>Perlindungan Akun Admin:</strong> Anda sedang mengedit akun Admin Anda sendiri. Hak akses Admin dilindungi dan tidak dapat diubah demi menjaga integritas sistem.
                    </div>

                    <!-- Card 1: Identitas Akun -->
                    <div class="card border-0 shadow-sm rounded-3 p-3.5 mb-3 bg-white">
                        <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom small d-flex align-items-center gap-2">
                            <i class="fas fa-id-card text-primary"></i> Identitas Akun Pengguna
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="user_name" class="form-label fw-semibold small text-secondary mb-1">Nama Lengkap &amp; Gelar *</label>
                                <input type="text" class="form-control" id="user_name" name="name" placeholder="Contoh: Dr. Nama Dosen, M.Kom." required>
                            </div>
                            <div class="col-md-6">
                                <label for="user_email" class="form-label fw-semibold small text-secondary mb-1">Email Institusi (@unika.ac.id) *</label>
                                <input type="email" class="form-control" id="user_email" name="email" placeholder="nama@unika.ac.id" required oninput="checkDuplicateEmailLive()">
                                
                                <!-- Peringatan Duplikat Email -->
                                <div id="emailDuplicateWarning" class="alert alert-danger py-2 px-3 mt-2 small rounded-3" style="display: none;">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <span id="emailDuplicateMsg">Alamat email ini sudah terdaftar dalam sistem.</span>
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">
                                    <i class="fab fa-google text-primary me-1"></i> Login resmi via Google SSO.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Pengaturan Kategori & Penugasan (Opsi A) -->
                    <div class="card border-0 shadow-sm rounded-3 p-3.5 mb-3 bg-white" id="containerRoleSettings">
                        <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom small d-flex align-items-center justify-content-between">
                            <span class="d-flex align-items-center gap-2"><i class="fas fa-sliders text-primary"></i> Penugasan &amp; Hak Akses Operasional</span>
                            <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.7rem;">Opsi A: Multi-Penugasan</span>
                        </h6>

                        <div class="mb-3">
                            <label for="user_account_type" class="form-label fw-semibold small text-secondary mb-1">Kategori Tingkat Penugasan *</label>
                            <select class="form-select" id="user_account_type" name="account_type" onchange="handleAccountTypeChange(this.value)">
                                <option value="fakultas_prodi" selected>Tingkat Fakultas &amp; Program Studi (Mendukung Multi-Peran Dekanat &amp; Kaprodi)</option>
                                <option value="lpm">Tingkat Lembaga Penjaminan Mutu (LPM / Universitas)</option>
                                <option value="gpm">GPM (Gugus Penjaminan Mutu Fakultas - Independen)</option>
                                <option value="pengguna">Pengguna Publik (Civitas Akademika / Akses Penuh Dokumen)</option>
                                <option value="testing">Akun Pengujian (Testing - Akses Multi-Role Penuh)</option>
                            </select>
                        </div>

                        <!-- SECTION A: FAKULTAS & PRODI (BISA MULTI-PERAN DEKANAT & PRODI) -->
                        <div id="sectionFakultasProdi">
                            <div class="p-3 rounded-3 bg-light border mb-3">
                                <label for="user_fp_fakultas_id" class="form-label fw-bold small text-dark-blue mb-1">
                                    <i class="fas fa-building-columns text-primary me-1"></i> Pilih Fakultas Target *
                                </label>
                                <select class="form-select" id="user_fp_fakultas_id" name="fakultas_id" onchange="handleFakultasSelectionChange(this.value)">
                                    <option value="">-- Pilih Fakultas --</option>
                                    <?php foreach ($fakultasList as $fk): ?>
                                        <option value="<?= $fk['id'] ?>"><?= htmlspecialchars($fk['nama_fakultas']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Penugasan Dekanat dan daftar program studi di bawahnya akan disesuaikan dengan fakultas ini.</div>
                            </div>

                            <div class="row g-3">
                                <!-- Penugasan Tingkat Dekanat -->
                                <div class="col-md-6">
                                    <div class="card h-100 border p-3 rounded-3 bg-white">
                                        <div class="fw-bold small text-dark mb-2 d-flex align-items-center gap-1.5">
                                            <i class="fas fa-landmark text-primary"></i> Penugasan Dekanat (Fakultas)
                                        </div>
                                        <div class="small text-muted mb-2.5" style="font-size: 0.72rem;">Centang peran jika akun bertugas di dekanat:</div>
                                        <div class="mb-2.5">
                                            <div class="form-check mb-1">
                                                <input class="form-check-input dekanat-role-chk" type="checkbox" name="dekanat_roles[]" value="dekan" id="chk_dekan">
                                                <label class="form-check-label small fw-semibold" for="chk_dekan">Dekan Fakultas</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm" name="role_nama_gelar[dekan]" id="nama_gelar_dekan" placeholder="Nama Lengkap &amp; Gelar Dekan (Opsional)" style="font-size: 0.78rem;">
                                        </div>
                                        <div>
                                            <div class="form-check mb-1">
                                                <input class="form-check-input dekanat-role-chk" type="checkbox" name="dekanat_roles[]" value="wadek" id="chk_wadek">
                                                <label class="form-check-label small fw-semibold" for="chk_wadek">Wakil Dekan Fakultas</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm" name="role_nama_gelar[wadek]" id="nama_gelar_wadek" placeholder="Nama Lengkap &amp; Gelar Wakil Dekan (Opsional)" style="font-size: 0.78rem;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Jabatan Prodi (Kaprodi / Sekprodi) -->
                                <div class="col-md-6">
                                    <div class="card h-100 border p-3 rounded-3 bg-white">
                                        <div class="fw-bold small text-dark mb-2 d-flex align-items-center gap-1.5">
                                            <i class="fas fa-user-gear text-success"></i> Jabatan Program Studi
                                        </div>
                                        <div class="small text-muted mb-2.5" style="font-size: 0.72rem;">Centang jabatan prodi yang diemban:</div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input prodi-role-chk" type="checkbox" name="prodi_roles[]" value="kaprodi" id="chk_kaprodi" checked onchange="toggleProdiListRequirement()">
                                            <label class="form-check-label small fw-semibold" for="chk_kaprodi">Ketua Program Studi (Kaprodi)</label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input prodi-role-chk" type="checkbox" name="prodi_roles[]" value="sekprodi" id="chk_sekprodi" onchange="toggleProdiListRequirement()">
                                            <label class="form-check-label small fw-semibold" for="chk_sekprodi">Sekretaris Program Studi (Sekprodi)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Program Studi Spesifik yang Dikelola -->
                            <div class="mt-3" id="prodiCheckboxesContainer">
                                <div class="card border p-3 rounded-3 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-bold small text-dark d-flex align-items-center gap-1.5">
                                            <i class="fas fa-graduation-cap text-success"></i> Program Studi yang Dikelola Akun Ini
                                        </div>
                                        <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">Centang prodi wewenang akun</span>
                                    </div>
                                    <p class="text-muted small mb-2.5" style="font-size: 0.72rem;">
                                        Pilih program studi yang berhak diakses dan dikelola oleh akun ini. <strong class="text-danger">Prodi yang tidak dicentang otomatis terkunci</strong> dan tidak dapat diakses walaupun dalam satu fakultas.
                                    </p>
                                    
                                    <div id="prodiCheckboxesList" class="p-2 rounded-2 bg-light border">
                                        <div class="text-muted small fst-italic p-2">Silakan pilih Fakultas terlebih dahulu untuk memuat daftar program studi.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION B: LPM (UNIVERSITAS) -->
                        <div id="sectionLpm" style="display: none;">
                            <div class="p-3 rounded-3 bg-light border">
                                <label for="user_lpm_role" class="form-label fw-bold small text-dark-blue mb-1">
                                    <i class="fas fa-user-shield text-danger me-1"></i> Jabatan Lembaga Penjaminan Mutu (LPM) *
                                </label>
                                <select class="form-select" id="user_lpm_role" name="lpm_role">
                                    <option value="super_admin">Admin (Super Admin LPM - Akses Penuh)</option>
                                    <option value="kepala_lpm">Kepala Lembaga Penjaminan Mutu</option>
                                    <option value="sekretaris_lpm">Sekretaris Lembaga Penjaminan Mutu</option>
                                    <option value="ketua_pusat_spm">Ketua Pusat Pengembangan Sistem Penjaminan Mutu</option>
                                    <option value="ketua_pusat_ami">Ketua Pusat Audit Mutu Internal</option>
                                    <option value="ketua_pusat_pemeringkatan">Ketua Pusat Pemeringkatan</option>
                                    <option value="tu_lpm">TU Lembaga Penjaminan Mutu</option>
                                </select>
                                <div class="mt-2.5">
                                    <label for="nama_gelar_lpm" class="form-label small fw-semibold text-secondary mb-1">Nama Lengkap &amp; Gelar Pejabat LPM (Opsional)</label>
                                    <input type="text" class="form-control form-control-sm" name="role_nama_gelar[lpm]" id="nama_gelar_lpm" placeholder="Contoh: Prof. Dr. Ir. ..., M.Sc. (Opsional)" style="font-size: 0.8rem;">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION C: GPM (GUGUS PENJAMINAN MUTU FAKULTAS) -->
                        <div id="sectionGpm" style="display: none;">
                            <div class="p-3 rounded-3 bg-light border">
                                <label for="user_gpm_fakultas_id" class="form-label fw-bold small text-dark-blue mb-1">
                                    <i class="fas fa-shield-halved text-purple me-1" style="color: #8B5CF6;"></i> Fakultas Penugasan GPM *
                                </label>
                                <select class="form-select" id="user_gpm_fakultas_id" name="gpm_fakultas_id">
                                    <option value="">-- Pilih Fakultas untuk GPM --</option>
                                    <?php foreach ($fakultasList as $fk): ?>
                                        <option value="<?= $fk['id'] ?>"><?= htmlspecialchars($fk['nama_fakultas']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-2.5">
                                    <label for="nama_gelar_gpm" class="form-label small fw-semibold text-secondary mb-1">Nama Lengkap &amp; Gelar Pejabat GPM (Opsional)</label>
                                    <input type="text" class="form-control form-control-sm" name="role_nama_gelar[gpm]" id="nama_gelar_gpm" placeholder="Contoh: Dr. Ir. ..., M.Kom. (Opsional)" style="font-size: 0.8rem;">
                                </div>
                                <div class="form-text text-muted small mt-1" style="font-size: 0.72rem;">GPM bertindak sebagai auditor/penjamin mutu independen tingkat fakultas.</div>
                            </div>
                        </div>

                        <!-- SECTION D: PENGGUNA PUBLIK ATAU TESTING -->
                        <div id="sectionOther" style="display: none;">
                            <div class="p-3 rounded-3 bg-light border">
                                <label for="user_other_role" class="form-label fw-bold small text-dark-blue mb-1">
                                    <i class="fas fa-user-check text-success me-1"></i> Tipe Akses Khusus *
                                </label>
                                <select class="form-select" id="user_other_role" name="other_role">
                                    <option value="pengguna">Pengguna Publik Civitas (Akses Dokumen Web Tanpa Batas)</option>
                                    <option value="testing">Akun Testing (Simulasi Seluruh Multi-Role)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Switch Status Aktif -->
                    <div class="form-check form-switch ps-4 ms-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="user_is_active" name="is_active" value="1" checked>
                        <label class="form-check-label small fw-semibold" for="user_is_active">Status Akun Aktif</label>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 bg-white border-top">
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitUser" class="btn btn-primary btn-sm px-4 rounded-pill bg-scu-blue border-0 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Penugasan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Data Master Prodi & Pengguna dari Server
const allProdiMaster = <?= json_encode($prodiList ?? []) ?>;
const existingUsersList = <?= json_encode(array_map(function($u) {
    return [
        'id' => (int)$u['id'],
        'name' => $u['name'],
        'email' => strtolower(trim($u['email'])),
        'role' => $u['role']
    ];
}, $usersList ?? [])) ?>;

const loggedInAdminId = <?= (int)$currentAdminId ?>;

function handleAccountTypeChange(type) {
    document.getElementById('sectionFakultasProdi').style.display = (type === 'fakultas_prodi') ? 'block' : 'none';
    document.getElementById('sectionLpm').style.display = (type === 'lpm') ? 'block' : 'none';
    document.getElementById('sectionGpm').style.display = (type === 'gpm') ? 'block' : 'none';
    document.getElementById('sectionOther').style.display = (type === 'pengguna' || type === 'testing') ? 'block' : 'none';
    
    if (type === 'pengguna' || type === 'testing') {
        document.getElementById('user_other_role').value = type;
    }
}

function handleFakultasSelectionChange(fakultasId, selectedProdiIds = [], prodiNameMap = {}) {
    renderProdiCheckboxes(fakultasId, selectedProdiIds, prodiNameMap);
}

function renderProdiCheckboxes(fakultasId, selectedProdiIds = [], prodiNameMap = {}) {
    const container = document.getElementById('prodiCheckboxesList');
    if (!fakultasId) {
        container.innerHTML = '<div class="text-muted small fst-italic p-2">Silakan pilih Fakultas terlebih dahulu untuk memuat daftar program studi.</div>';
        return;
    }

    const filteredProdis = allProdiMaster.filter(p => parseInt(p.fakultas_id || p.resolved_fakultas_id || 0) === parseInt(fakultasId) || (p.nama_fakultas && p.nama_fakultas === getFakultasNameById(fakultasId)));

    if (filteredProdis.length === 0) {
        container.innerHTML = '<div class="text-muted small fst-italic p-2">Tidak ditemukan program studi di bawah fakultas ini.</div>';
        return;
    }

    let html = '<div class="row g-2">';
    filteredProdis.forEach(p => {
        const isChecked = selectedProdiIds.includes(parseInt(p.id));
        const customName = prodiNameMap[p.id] || '';
        html += `
            <div class="col-md-6">
                <div class="p-2 rounded-2 bg-white border">
                    <div class="form-check mb-1">
                        <input class="form-check-input prodi-id-chk ms-0 me-2" type="checkbox" name="prodi_ids[]" value="${p.id}" id="prd_${p.id}" ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label small fw-semibold text-dark text-truncate d-block" for="prd_${p.id}" title="${p.nama_prodi} (${p.jenjang})">
                            ${p.nama_prodi} <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">${p.jenjang}</span>
                        </label>
                    </div>
                    <input type="text" class="form-control form-control-sm mt-1" name="prodi_nama_gelar[${p.id}]" id="nama_gelar_prd_${p.id}" placeholder="Nama & Gelar Pejabat Prodi (Opsional)" value="${escapeHtml(customName)}" style="font-size:0.75rem;">
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

function getFakultasNameById(id) {
    const sel = document.getElementById('user_fp_fakultas_id');
    const opt = sel.querySelector(`option[value="${id}"]`);
    return opt ? opt.textContent.trim() : '';
}

function toggleProdiListRequirement() {
    const isKaprodi = document.getElementById('chk_kaprodi').checked;
    const isSekprodi = document.getElementById('chk_sekprodi').checked;
    const prodiCard = document.getElementById('prodiCheckboxesContainer');
    if (prodiCard) {
        prodiCard.style.opacity = (isKaprodi || isSekprodi) ? '1' : '0.4';
    }
}

function resetUserForm() {
    document.getElementById('modalUserTitle').textContent = 'Tambah Pengguna Baru';
    document.getElementById('user_id').value = '';
    document.getElementById('user_name').value = '';
    document.getElementById('user_email').value = '';
    document.getElementById('user_is_active').checked = true;

    // Reset alert proteksi akun
    document.getElementById('selfEditProtectionAlert').style.display = 'none';
    enableRoleControls(true);

    // Default ke fakultas_prodi
    document.getElementById('user_account_type').value = 'fakultas_prodi';
    handleAccountTypeChange('fakultas_prodi');

    document.getElementById('user_fp_fakultas_id').value = '';
    document.getElementById('chk_dekan').checked = false;
    document.getElementById('chk_wadek').checked = false;
    document.getElementById('chk_kaprodi').checked = true;
    document.getElementById('chk_sekprodi').checked = false;
    
    document.getElementById('nama_gelar_dekan').value = '';
    document.getElementById('nama_gelar_wadek').value = '';
    document.getElementById('nama_gelar_gpm').value = '';
    document.getElementById('nama_gelar_lpm').value = '';

    renderProdiCheckboxes('');

    hideDuplicateEmailWarning();
}

function editUser(data) {
    document.getElementById('modalUserTitle').textContent = 'Ubah Data & Penugasan Pengguna';
    document.getElementById('user_id').value = data.id;
    document.getElementById('user_name').value = data.name;
    document.getElementById('user_email').value = data.email;
    document.getElementById('user_is_active').checked = (data.is_active == 1);

    hideDuplicateEmailWarning();

    // Reset nama gelar fields
    document.getElementById('nama_gelar_dekan').value = '';
    document.getElementById('nama_gelar_wadek').value = '';
    document.getElementById('nama_gelar_gpm').value = '';
    document.getElementById('nama_gelar_lpm').value = '';

    const isSelfAdmin = (parseInt(data.id) === loggedInAdminId);
    if (isSelfAdmin) {
        document.getElementById('selfEditProtectionAlert').style.display = 'block';
        enableRoleControls(false);
    } else {
        document.getElementById('selfEditProtectionAlert').style.display = 'none';
        enableRoleControls(true);
    }

    const assignments = data.assignments || [];
    const role = data.role;
    const lpmRoles = ['super_admin', 'admin_lpm', 'kepala_lpm', 'sekretaris_lpm', 'ketua_pusat_spm', 'ketua_pusat_ami', 'ketua_pusat_pemeringkatan', 'tu_lpm', 'kepala_pusat_mutu'];

    if (lpmRoles.includes(role)) {
        document.getElementById('user_account_type').value = 'lpm';
        handleAccountTypeChange('lpm');
        document.getElementById('user_lpm_role').value = (role === 'admin_lpm') ? 'super_admin' : role;
        const lpmAsg = assignments.find(a => lpmRoles.includes(a.role));
        if (lpmAsg && lpmAsg.nama_lengkap_gelar) {
            document.getElementById('nama_gelar_lpm').value = lpmAsg.nama_lengkap_gelar;
        }
    } else if (role === 'gpm') {
        document.getElementById('user_account_type').value = 'gpm';
        handleAccountTypeChange('gpm');
        document.getElementById('user_gpm_fakultas_id').value = data.fakultas_id || data.resolved_fakultas_id || '';
        const gpmAsg = assignments.find(a => a.role === 'gpm');
        if (gpmAsg && gpmAsg.nama_lengkap_gelar) {
            document.getElementById('nama_gelar_gpm').value = gpmAsg.nama_lengkap_gelar;
        }
    } else if (role === 'pengguna' || role === 'testing') {
        document.getElementById('user_account_type').value = role;
        handleAccountTypeChange(role);
        document.getElementById('user_other_role').value = role;
    } else {
        // Tingkat Fakultas & Prodi (bisa multi-penugasan)
        document.getElementById('user_account_type').value = 'fakultas_prodi';
        handleAccountTypeChange('fakultas_prodi');

        const fakId = data.resolved_fakultas_id || data.fakultas_id || (assignments.length > 0 ? (assignments[0].fakultas_id || '') : '');
        document.getElementById('user_fp_fakultas_id').value = fakId;

        // Cek peran dekanat & populate nama gelar
        const dekanAsg = assignments.find(a => a.role === 'dekan');
        const wadekAsg = assignments.find(a => a.role === 'wadek');
        document.getElementById('chk_dekan').checked = !!dekanAsg || role === 'dekan';
        document.getElementById('chk_wadek').checked = !!wadekAsg || role === 'wadek';
        if (dekanAsg && dekanAsg.nama_lengkap_gelar) {
            document.getElementById('nama_gelar_dekan').value = dekanAsg.nama_lengkap_gelar;
        }
        if (wadekAsg && wadekAsg.nama_lengkap_gelar) {
            document.getElementById('nama_gelar_wadek').value = wadekAsg.nama_lengkap_gelar;
        }

        // Cek peran prodi
        const hasKaprodi = assignments.some(a => a.role === 'kaprodi') || role === 'kaprodi';
        const hasSekprodi = assignments.some(a => a.role === 'sekprodi') || role === 'sekprodi';
        document.getElementById('chk_kaprodi').checked = hasKaprodi;
        document.getElementById('chk_sekprodi').checked = hasSekprodi;

        // Kumpulkan ID prodi terpilih dan petakan nama custom
        const selectedProdis = [];
        const prodiNameMap = {};
        assignments.forEach(a => {
            if (a.prodi_id) {
                const prId = parseInt(a.prodi_id);
                if (!selectedProdis.includes(prId)) {
                    selectedProdis.push(prId);
                }
                if (a.nama_lengkap_gelar) {
                    prodiNameMap[prId] = a.nama_lengkap_gelar;
                }
            }
        });
        if (data.prodi_id && !selectedProdis.includes(parseInt(data.prodi_id))) {
            selectedProdis.push(parseInt(data.prodi_id));
        }

        renderProdiCheckboxes(fakId, selectedProdis, prodiNameMap);
    }
}

function enableRoleControls(enabled) {
    const container = document.getElementById('containerRoleSettings');
    if (!container) return;
    const inputs = container.querySelectorAll('input, select');
    inputs.forEach(el => {
        el.disabled = !enabled;
    });
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

    const accType = document.getElementById('user_account_type').value;
    if (accType === 'fakultas_prodi') {
        const fakId = document.getElementById('user_fp_fakultas_id').value;
        if (!fakId) {
            alert('Silakan pilih Fakultas target terlebih dahulu.');
            return false;
        }

        const isDekanChecked = document.getElementById('chk_dekan').checked;
        const isWadekChecked = document.getElementById('chk_wadek').checked;
        const isKaprodiChecked = document.getElementById('chk_kaprodi').checked;
        const isSekprodiChecked = document.getElementById('chk_sekprodi').checked;

        const prodiChks = document.querySelectorAll('.prodi-id-chk:checked');

        const hasDekanat = isDekanChecked || isWadekChecked;
        const hasProdiRole = isKaprodiChecked || isSekprodiChecked;

        if (!hasDekanat && !hasProdiRole) {
            alert('Mohon centang minimal satu penugasan: Dekanat (Dekan/Wadek) atau Jabatan Prodi (Kaprodi/Sekprodi).');
            return false;
        }

        if (hasProdiRole && prodiChks.length === 0) {
            alert('Anda mencentang jabatan Prodi (Kaprodi/Sekprodi), mohon centang minimal satu Program Studi yang dikelola di bawahnya.');
            return false;
        }
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
