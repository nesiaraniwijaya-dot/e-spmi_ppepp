<?php
/**
 * Role Selector View for Multi-Role Testing Account (nesiaraniwijaya@gmail.com)
 * MITRA - UNIKA Soegijapranata
 */
$pageTitle = 'Pilih Peran Akses Sistem - MITRA';
require_once ROOT_PATH . '/views/layouts/header.php';

$fakultasList = $fakultasList ?? [];
$prodiList    = $prodiList ?? [];
$defaultFakultasId = (int)($defaultFakultasId ?? 2);
$defaultProdiId    = (int)($defaultProdiId ?? 1);

$defaultFakName = 'Fakultas Ilmu Komputer (FIKOM)';
foreach ($fakultasList as $f) {
    if ((int)$f['id'] === $defaultFakultasId) {
        $defaultFakName = $f['nama_fakultas'] . (!empty($f['kode_fakultas']) ? ' (' . $f['kode_fakultas'] . ')' : '');
        break;
    }
}

$defaultProdiName = 'Teknik Informatika (S1)';
foreach ($prodiList as $p) {
    if ((int)$p['id'] === $defaultProdiId) {
        $defaultProdiName = $p['nama_prodi'] . (!empty($p['jenjang']) ? ' (' . $p['jenjang'] . ')' : '');
        break;
    }
}

$rolesOptions = [
    [
        'role' => 'kepala_lpm',
        'scope' => 'lpm',
        'title' => 'Kepala Lembaga Penjaminan Mutu',
        'subtitle' => 'lpm@unika.ac.id · Pimpinan LPM Universitas',
        'desc' => 'Akses penuh pusat LPM: monitoring seluruh siklus PPEPP, review dokumen, kinerja GPM, dan audit trail.',
        'badge' => 'Kepala LPM',
        'badge_bg' => 'bg-primary',
        'icon' => 'fas fa-user-tie',
        'color' => '#2563EB'
    ],
    [
        'role' => 'sekretaris_lpm',
        'scope' => 'lpm',
        'title' => 'Sekretaris Lembaga Penjaminan Mutu',
        'subtitle' => 'lpm.sekretaris@unika.ac.id · Sekretariat LPM',
        'desc' => 'Koordinasi administrasi penjaminan mutu, verifikasi dokumen, monitoring GPM, dan laporan universitas.',
        'badge' => 'Sekretaris LPM',
        'badge_bg' => 'bg-info text-dark',
        'icon' => 'fas fa-user-check',
        'color' => '#0284C7'
    ],
    [
        'role' => 'ketua_pusat_spm',
        'scope' => 'lpm',
        'title' => 'Ketua Pusat Pengembangan Sistem Penjaminan Mutu',
        'subtitle' => 'lpm.spm@unika.ac.id · Pusat SPM LPM',
        'desc' => 'Pengembangan standar mutu, evaluasi ketercapaian PPEPP, review dokumen, dan pengawasan mutu.',
        'badge' => 'Ketua Pusat SPM',
        'badge_bg' => 'bg-navy',
        'icon' => 'fas fa-award',
        'color' => '#4338CA'
    ],
    [
        'role' => 'ketua_pusat_ami',
        'scope' => 'lpm',
        'title' => 'Ketua Pusat Audit Mutu Internal',
        'subtitle' => 'lpm.ami@unika.ac.id · Pusat AMI LPM',
        'desc' => 'Pengawasan audit mutu internal, telaah dokumen akreditasi/evaluasi, dan monitoring ketuntasan unit.',
        'badge' => 'Ketua Pusat AMI',
        'badge_bg' => 'bg-success',
        'icon' => 'fas fa-clipboard-check',
        'color' => '#0F766E'
    ],
    [
        'role' => 'ketua_pusat_pemeringkatan',
        'scope' => 'lpm',
        'title' => 'Ketua Pusat Pemeringkatan',
        'subtitle' => 'lpm.pemeringkatan@unika.ac.id · Pusat Pemeringkatan LPM',
        'desc' => 'Monitoring data pendukung pemeringkatan institusi, keterisian instrumen mutu, dan kinerja unit.',
        'badge' => 'Ketua Pemeringkatan',
        'badge_bg' => 'bg-purple',
        'icon' => 'fas fa-medal',
        'color' => '#7C3AED'
    ],
    [
        'role' => 'tu_lpm',
        'scope' => 'lpm',
        'title' => 'TU Lembaga Penjaminan Mutu',
        'subtitle' => 'tu.lpm@unika.ac.id · Tata Usaha LPM',
        'desc' => 'Pengelolaan master data, administrasi berkas PPEPP, manajemen pengguna, dan dukungan operasional LPM.',
        'badge' => 'TU LPM',
        'badge_bg' => 'bg-secondary',
        'icon' => 'fas fa-briefcase',
        'color' => '#475569'
    ],
    [
        'role' => 'super_admin',
        'scope' => 'lpm',
        'title' => 'Super Admin (Multi-Role LPM)',
        'subtitle' => 'Akses Administrator Pusat LPM',
        'desc' => 'Akses penuh ke seluruh master data, verifikasi dokumen, manajemen pengguna, dan audit trail.',
        'badge' => 'Super Admin',
        'badge_bg' => 'bg-danger',
        'icon' => 'fas fa-user-shield',
        'color' => '#DC2626'
    ],
    [
        'role' => 'gpm',
        'scope' => 'fakultas',
        'title' => 'GPM Fakultas (Gugus Penjaminan Mutu)',
        'subtitle' => $defaultFakName,
        'desc' => 'Kelola & verifikasi dokumen mutu tingkat fakultas, review dokumen prodi, dan tindak lanjut perbaikan.',
        'badge' => 'Tingkat Fakultas',
        'badge_bg' => 'bg-success',
        'icon' => 'fas fa-clipboard-check',
        'color' => '#059669'
    ],
    [
        'role' => 'dekan',
        'scope' => 'fakultas',
        'title' => 'Dekan Fakultas',
        'subtitle' => 'Dekan ' . $defaultFakName,
        'desc' => 'Review dan pengesahan dokumen PPEPP tingkat fakultas serta monitoring kinerja prodi.',
        'badge' => 'Dekanat',
        'badge_bg' => 'bg-warning text-dark',
        'icon' => 'fas fa-university',
        'color' => '#D97706'
    ],
    [
        'role' => 'wadek',
        'scope' => 'fakultas',
        'title' => 'Wakil Dekan',
        'subtitle' => 'Wakil Dekan ' . $defaultFakName,
        'desc' => 'Peninjauan dokumen mutu akademik dan pendampingan implementasi standar fakultas.',
        'badge' => 'Dekanat',
        'badge_bg' => 'bg-warning text-dark',
        'icon' => 'fas fa-user-graduate',
        'color' => '#B45309'
    ],
    [
        'role' => 'kaprodi',
        'scope' => 'prodi',
        'title' => 'Kaprodi (Ketua Program Studi)',
        'subtitle' => $defaultProdiName,
        'desc' => 'Unggah dokumen 5 siklus PPEPP prodi, ajukan draft perbaikan, dan kelola arsip prodi.',
        'badge' => 'Tingkat Prodi',
        'badge_bg' => 'bg-secondary',
        'icon' => 'fas fa-book-reader',
        'color' => '#4F46E5'
    ],
    [
        'role' => 'sekprodi',
        'scope' => 'prodi',
        'title' => 'Sekprodi (Sekretaris Prodi)',
        'subtitle' => $defaultProdiName,
        'desc' => 'Membantu pengelolaan dokumen mutu prodi, perbaikan dokumen, dan persiapan audit AMI.',
        'badge' => 'Tingkat Prodi',
        'badge_bg' => 'bg-secondary',
        'icon' => 'fas fa-user-edit',
        'color' => '#7C3AED'
    ],
    [
        'role' => 'pengguna',
        'scope' => 'public',
        'title' => 'Pengguna (Civitas Akademika)',
        'subtitle' => 'Dosen / Mahasiswa / Civitas Terdaftar',
        'desc' => 'Akses penuh ke seluruh dokumen publik institusi tanpa watermark atau pembatasan pratinjau.',
        'badge' => 'Pengguna Publik',
        'badge_bg' => 'bg-info text-dark',
        'icon' => 'fas fa-user-check',
        'color' => '#059669'
    ]
];
?>

<div class="container py-5 my-auto">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <!-- Header Banner -->
                <div class="bg-scu-gradient text-white p-4 p-md-5 position-relative">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-white bg-opacity-20 p-2 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <img src="<?= base_url('assets/images/logo-unika.png') ?>" alt="Logo UNIKA" style="width: 40px; height: 40px; object-fit: contain;">
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-1 rounded-pill"><i class="fas fa-shield-halved me-1"></i> Mode Pengujian Multi-Role Google SSO</span>
                            <h3 class="fw-bold mb-0 text-white">Selamat Datang, <?= htmlspecialchars($_SESSION['user_real_name'] ?? $_SESSION['user_name'] ?? 'Pengguna Testing') ?>!</h3>
                        </div>
                    </div>
                    <p class="text-light opacity-90 mb-0 small" style="max-width: 750px; line-height: 1.6;">
                        Akun Google SSO Anda (<strong><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></strong>) memiliki wewenang khusus untuk melakukan simulasi dan pengujian di seluruh tingkatan peran sistem. Pilih <strong>Fakultas / Program Studi target</strong> di bawah, lalu klik peran yang ingin digunakan.
                    </p>
                </div>

                <!-- Body Role Selector Grid -->
                <div class="card-body p-4 p-md-5 bg-light">
                    <!-- Target Fakultas & Prodi Selector (untuk role GPM, Dekanat, Kaprodi/Sekprodi) -->
                    <div class="card border-0 shadow-sm rounded-4 p-3.5 p-md-4 mb-4 bg-white" style="border-left: 4px solid #2563EB !important;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                <i class="fas fa-sliders me-1"></i> Target Unit Simulasi
                            </span>
                            <span class="small text-muted">Pilih Fakultas &amp; Program Studi tujuan sebelum menekan kartu GPM, Dekan/Wadek, atau Kaprodi/Sekprodi.</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="switchFakultasSelect" class="form-label small fw-bold text-dark-blue mb-1">
                                    <i class="fas fa-building-columns text-primary me-1"></i> Pilih Fakultas (untuk GPM, Dekan, Wadek)
                                </label>
                                <select id="switchFakultasSelect" class="form-select rounded-3">
                                    <?php foreach ($fakultasList as $f): ?>
                                        <option value="<?= (int)$f['id'] ?>"
                                                data-label="<?= htmlspecialchars($f['nama_fakultas'] . (!empty($f['kode_fakultas']) ? ' (' . $f['kode_fakultas'] . ')' : '')) ?>"
                                                <?= (int)$f['id'] === $defaultFakultasId ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($f['nama_fakultas']) ?><?= !empty($f['kode_fakultas']) ? ' (' . htmlspecialchars($f['kode_fakultas']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="switchProdiSelect" class="form-label small fw-bold text-dark-blue mb-1">
                                    <i class="fas fa-graduation-cap text-indigo me-1" style="color:#4F46E5;"></i> Pilih Program Studi (untuk Kaprodi, Sekprodi)
                                </label>
                                <select id="switchProdiSelect" class="form-select rounded-3">
                                    <?php foreach ($prodiList as $p): ?>
                                        <option value="<?= (int)$p['id'] ?>"
                                                data-fakultas-id="<?= (int)$p['fakultas_id'] ?>"
                                                data-label="<?= htmlspecialchars($p['nama_prodi'] . (!empty($p['jenjang']) ? ' (' . $p['jenjang'] . ')' : '')) ?>"
                                                <?= (int)$p['id'] === $defaultProdiId ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['nama_prodi']) ?><?= !empty($p['jenjang']) ? ' (' . htmlspecialchars($p['jenjang']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <?php foreach ($rolesOptions as $r): ?>
                            <div class="col-md-6 col-xl-6">
                                <form action="<?= base_url('auth/select-role') ?>" method="POST" class="h-100 role-switch-form">
                                    <input type="hidden" name="role" value="<?= $r['role'] ?>">
                                    <input type="hidden" name="fakultas_id" class="hidden-fak-id" value="<?= $defaultFakultasId ?>">
                                    <input type="hidden" name="prodi_id" class="hidden-prodi-id" value="<?= $defaultProdiId ?>">
                                    <button type="submit" class="w-100 h-100 text-start border-0 p-0 bg-transparent" style="cursor: pointer;">
                                        <div class="card h-100 border-0 shadow-sm rounded-3 role-card hover-lift transition-all p-3 bg-white border-start border-4 overflow-hidden" style="border-left-color: <?= $r['color'] ?> !important;">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 48px; height: 48px; background-color: <?= $r['color'] ?>;">
                                                    <i class="<?= $r['icon'] ?> fa-lg"></i>
                                                </div>
                                                <div class="flex-grow-1" style="min-width: 0;">
                                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem; line-height: 1.35;"><?= $r['title'] ?></h6>
                                                        <span class="badge <?= $r['badge_bg'] ?> text-capitalize px-2 py-1 flex-shrink-0" style="font-size: 0.68rem;"><?= $r['badge'] ?></span>
                                                    </div>
                                                    <div class="small fw-semibold text-primary mb-1 text-truncate role-dynamic-subtitle"
                                                         data-scope="<?= $r['scope'] ?>"
                                                         data-role="<?= $r['role'] ?>"
                                                         style="font-size: 0.78rem;"><?= htmlspecialchars($r['subtitle']) ?></div>
                                                    <p class="text-muted small mb-0 opacity-75" style="font-size: 0.75rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                        <?= $r['desc'] ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between text-primary small fw-bold">
                                                <span class="text-truncate me-2">Masuk Sebagai <?= $r['title'] ?></span>
                                                <i class="fas fa-arrow-right flex-shrink-0"></i>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="<?= base_url('logout') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                            <i class="fas fa-sign-out-alt me-1"></i> Keluar / Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.role-card {
    transition: all 0.25s ease-in-out;
}
.role-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fakSel = document.getElementById('switchFakultasSelect');
    const prodiSel = document.getElementById('switchProdiSelect');
    if (!fakSel || !prodiSel) return;

    const allProdiOpts = Array.from(prodiSel.querySelectorAll('option')).map(o => ({
        value: o.value,
        fakId: o.getAttribute('data-fakultas-id') || '',
        label: o.getAttribute('data-label') || o.textContent,
        text: o.textContent
    }));

    function filterProdisByFakultas(preserveSelected) {
        const selectedFakId = fakSel.value;
        const prevProdiVal = prodiSel.value;
        const matching = allProdiOpts.filter(o => !selectedFakId || o.fakId === selectedFakId);

        prodiSel.innerHTML = '';
        matching.forEach(o => {
            const opt = new Option(o.text, o.value);
            opt.setAttribute('data-fakultas-id', o.fakId);
            opt.setAttribute('data-label', o.label);
            prodiSel.add(opt);
        });

        if (preserveSelected && matching.some(o => o.value === prevProdiVal)) {
            prodiSel.value = prevProdiVal;
        }
        syncRoleSwitchTargets();
    }

    function syncRoleSwitchTargets() {
        const fakId = fakSel.value || '';
        const prodiId = prodiSel.value || '';
        const fakLabel = fakSel.selectedOptions[0]?.getAttribute('data-label') || 'Fakultas';
        const prodiLabel = prodiSel.selectedOptions[0]?.getAttribute('data-label') || 'Program Studi';

        document.querySelectorAll('.hidden-fak-id').forEach(el => el.value = fakId);
        document.querySelectorAll('.hidden-prodi-id').forEach(el => el.value = prodiId);

        document.querySelectorAll('.role-dynamic-subtitle').forEach(el => {
            const scope = el.getAttribute('data-scope');
            const role = el.getAttribute('data-role');
            if (scope === 'fakultas') {
                if (role === 'dekan') el.textContent = 'Dekan ' + fakLabel;
                else if (role === 'wadek') el.textContent = 'Wakil Dekan ' + fakLabel;
                else el.textContent = fakLabel;
            } else if (scope === 'prodi') {
                el.textContent = prodiLabel;
            }
        });
    }

    fakSel.addEventListener('change', () => filterProdisByFakultas(false));
    prodiSel.addEventListener('change', syncRoleSwitchTargets);

    // Pastikan sinkron saat awal halaman dibuka sesuai prodi/fakultas terpilih
    const initOpt = allProdiOpts.find(o => o.value === '<?= $defaultProdiId ?>');
    if (initOpt && initOpt.fakId && fakSel.value !== initOpt.fakId) {
        // Jika session prodi terakhir berasal dari fakultas tertentu, jaga opsi tetap konsisten
    }
    filterProdisByFakultas(true);
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
