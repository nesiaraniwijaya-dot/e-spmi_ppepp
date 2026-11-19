<?php
/**
 * Super Admin: Master Fakultas View
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-blue mb-1">Master Data Fakultas</h3>
            <p class="text-muted small mb-0">Kelola daftar fakultas, ikon identitas visual, serta warna aksen kartu beranda di lingkungan <?= INSTITUTION_NAME ?>.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm bg-scu-blue border-0" data-bs-toggle="modal" data-bs-target="#modalFakultas" onclick="resetFakultasForm()">
            <i class="fas fa-plus me-1"></i> Tambah Fakultas Baru
        </button>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th style="width: 140px;">Kode & Ikon</th>
                        <th>Nama Fakultas</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 130px;">Jumlah Prodi</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fakultasList)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data fakultas.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($fakultasList as $fak): 
                            $fakColor = !empty($fak['warna']) ? $fak['warna'] : '#1E3E62';
                            $fakIcon = !empty($fak['icon']) ? $fak['icon'] : 'fa-landmark';
                            $isLetter = str_starts_with($fakIcon, 'letter:') || (strlen($fakIcon) === 1 && !str_starts_with($fakIcon, 'fa-'));
                            $letterChar = $isLetter ? strtoupper(str_replace('letter:', '', $fakIcon)) : '';
                            if (!$isLetter && !str_starts_with($fakIcon, 'fa-')) $fakIcon = 'fa-' . $fakIcon;
                        ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-3 shadow-2xs" 
                                             style="width: 36px; height: 36px; background: <?= htmlspecialchars($fakColor) ?>18; color: <?= htmlspecialchars($fakColor) ?>; font-size: 1rem; flex-shrink: 0;"
                                             title="Ikon Fakultas: <?= htmlspecialchars($fakIcon) ?>">
                                            <?php if ($isLetter): ?>
                                                <span class="fw-bolder" style="font-size: 1.15rem; line-height: 1; font-family: var(--font-heading, sans-serif);"><?= htmlspecialchars($letterChar) ?></span>
                                            <?php else: ?>
                                                <i class="fas <?= htmlspecialchars($fakIcon) ?>"></i>
                                            <?php endif; ?>
                                        </div>
                                        <span class="badge bg-dark-blue text-warning px-2 py-1 fw-bold"><?= htmlspecialchars($fak['kode_fakultas']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark-blue"><?= htmlspecialchars($fak['nama_fakultas']) ?></div>
                                </td>
                                <td class="small text-muted" style="max-width: 320px;"><?= htmlspecialchars($fak['deskripsi'] ?: '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info border px-2.5 py-1 fw-bold"><?= $fak['total_prodi'] ?> Program Studi</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalFakultas"
                                                onclick="editFakultas(<?= htmlspecialchars(json_encode($fak)) ?>)">
                                            <i class="fas fa-edit"></i> Ubah
                                        </button>
                                        <form action="<?= base_url('admin/fakultas/delete/' . $fak['id']) ?>" method="POST" class="d-inline">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 btn-delete-confirm" data-name="<?= htmlspecialchars($fak['nama_fakultas']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

<!-- Modal: Tambah/Edit Fakultas -->
<div class="modal fade" id="modalFakultas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('admin/fakultas/save') ?>" method="POST" id="formFakultas">
                <input type="hidden" name="id" id="fakultas_id" value="">
                <input type="hidden" name="icon" id="fakultas_icon" value="fa-landmark">
                <input type="hidden" name="warna" id="fakultas_warna" value="#1E3E62">
                
                <div class="modal-header bg-dark-blue text-white px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-building-columns text-warning"></i>
                        <h5 class="modal-title fs-6 fw-bold mb-0 text-white" id="modalFakultasTitle">Tambah Fakultas Baru</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <!-- Informasi Utama -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="kode_fakultas" class="form-label fw-semibold small text-secondary">Kode Singkatan Fakultas *</label>
                            <input type="text" class="form-control" id="kode_fakultas" name="kode_fakultas" placeholder="Contoh: FIK, FEB, FAD" maxlength="20" required>
                            <div class="form-text small">Kode unik singkatan fakultas.</div>
                        </div>
                        <div class="col-md-8">
                            <label for="nama_fakultas" class="form-label fw-semibold small text-secondary">Nama Lengkap Fakultas *</label>
                            <input type="text" class="form-control" id="nama_fakultas" name="nama_fakultas" placeholder="Contoh: Fakultas Ilmu Komputer" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold small text-secondary">Deskripsi Singkat</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2" placeholder="Penyelenggara program studi unggul dengan penjaminan mutu terstandarisasi..."></textarea>
                    </div>

                    <!-- Divider: Tampilan Visual -->
                    <div class="d-flex align-items-center gap-2 mb-3 pt-2 border-top">
                        <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">Identitas Visual</span>
                        <h6 class="fw-bold text-dark-blue mb-0">Ikon & Warna Kartu di Halaman Utama</h6>
                    </div>

                    <!-- Live Preview Box -->
                    <div class="card bg-light border-0 rounded-4 p-3 mb-3">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                <div id="liveIconPreview" class="d-flex align-items-center justify-content-center rounded-4 shadow-sm" 
                                     style="width: 60px; height: 60px; background: #1E3E6218; color: #1E3E62; font-size: 1.75rem; transition: all 0.25s ease;">
                                    <i id="previewIconTag" class="fas fa-landmark"></i>
                                    <span id="previewLetterTag" class="fw-bolder" style="font-size: 2rem; line-height: 1; display: none; font-family: var(--font-heading, sans-serif);">G</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="fw-bold text-dark-blue" id="previewTitle">Pratinjau Kartu Fakultas</div>
                                <div class="small text-muted mb-1">
                                    Ikon: <code id="labelSelectedIcon" class="text-primary fw-bold">fa-landmark</code> &bull; 
                                    Warna: <code id="labelSelectedColor" class="fw-bold" style="color: #1E3E62;">#1E3E62</code> <span id="labelSelectedColorName" class="badge bg-white text-secondary border px-2 py-0.5 ms-1" style="font-size:0.7rem;">Navy SCU</span>
                                </div>
                                <div class="small text-secondary" style="font-size:0.75rem;">
                                    Ikon/huruf dan warna ini akan muncul secara serasi pada kartu direktori fakultas di landing page publik.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Warna Aksen (Palet Modern & Estetik) -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary d-flex justify-content-between align-items-center mb-2">
                            <span>Pilih Warna Aksen Tema Fakultas:</span>
                            <span class="text-muted small" style="font-size: 0.74rem;">12 Pilihan warna akademik elegan &amp; sejuk</span>
                        </label>
                        <div class="d-flex flex-wrap align-items-center gap-2" id="colorPaletteContainer">
                            <?php
                            $presetColors = [
                                ['hex' => '#1E3E62', 'name' => 'Navy SCU',      'title' => 'Navy Klasik SCU'],
                                ['hex' => '#2563EB', 'name' => 'Royal Blue',    'title' => 'Biru Royal Terang'],
                                ['hex' => '#0284C7', 'name' => 'Ocean Cyan',    'title' => 'Biru Laut Teduh'],
                                ['hex' => '#0D9488', 'name' => 'Teal Calm',     'title' => 'Toska Harmoni'],
                                ['hex' => '#059669', 'name' => 'Emerald Green', 'title' => 'Hijau Zamrud Sejuk'],
                                ['hex' => '#15803D', 'name' => 'Forest Leaf',   'title' => 'Hijau Daun Alami'],
                                ['hex' => '#D97706', 'name' => 'Warm Amber',    'title' => 'Kuning Amber Hangat'],
                                ['hex' => '#C2410C', 'name' => 'Terracotta',    'title' => 'Oranye Bata Elegan'],
                                ['hex' => '#BE123C', 'name' => 'Crimson Rose',  'title' => 'Merah Marun Rose'],
                                ['hex' => '#7C3AED', 'name' => 'Royal Violet',  'title' => 'Ungu Violet Mewah'],
                                ['hex' => '#4F46E5', 'name' => 'Deep Indigo',   'title' => 'Indigo Prestisius'],
                                ['hex' => '#475569', 'name' => 'Slate Steel',   'title' => 'Abu-abu Grafit Bersih'],
                            ];
                            foreach ($presetColors as $c): ?>
                                <button type="button" class="btn-color-swatch" 
                                        style="background-color: <?= $c['hex'] ?>;" 
                                        data-color="<?= $c['hex'] ?>" 
                                        data-name="<?= $c['name'] ?>"
                                        title="<?= $c['title'] ?> (<?= $c['hex'] ?>)" 
                                        onclick="selectColor('<?= $c['hex'] ?>', '<?= $c['name'] ?>')">
                                </button>
                            <?php endforeach; ?>
                            
                            <!-- Custom Color Picker Swatch -->
                            <div class="position-relative ms-1">
                                <label for="customColorPicker" class="btn-color-swatch-custom" title="Pilih warna kustom lainnya">
                                    <i class="fas fa-eye-dropper text-secondary" style="font-size: 0.78rem;"></i>
                                </label>
                                <input type="color" id="customColorPicker" value="#1E3E62" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Ikon Fakultas (Search + Grid List) -->
                    <div>
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-2">
                            <label class="form-label fw-semibold small text-secondary mb-0">Pilih Ikon Fakultas:</label>
                            <div class="input-group input-group-sm" style="max-width: 280px;">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" id="iconSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari ikon (komputer, gedung, seni...)" autocomplete="off">
                            </div>
                        </div>

                        <!-- Icon Grid Container -->
                        <div class="icon-grid-wrapper border rounded-3 p-3 bg-white" style="max-height: 210px; overflow-y: auto;">
                            <div class="row row-cols-3 row-cols-sm-4 row-cols-md-6 g-2" id="iconGridContainer">
                                <?php
                                $academicIcons = [
                                    // Komputer & Teknologi
                                    ['icon' => 'fa-laptop-code', 'label' => 'Laptop Code', 'kw' => 'komputer coding informatika it programmer teknologi'],
                                    ['icon' => 'fa-desktop', 'label' => 'Komputer', 'kw' => 'desktop pc monitor sistem komputer teknologi'],
                                    ['icon' => 'fa-code', 'label' => 'Coding', 'kw' => 'software program coding pemrograman sintaks'],
                                    ['icon' => 'fa-network-wired', 'label' => 'Jaringan', 'kw' => 'network jaringan ethernet internet infrastruktur'],
                                    ['icon' => 'fa-robot', 'label' => 'Robotika/AI', 'kw' => 'robot kecerdasan buatan ai teknologi masa depan'],
                                    ['icon' => 'fa-database', 'label' => 'Data', 'kw' => 'database sains data sql storage server'],
                                    ['icon' => 'fa-microchip', 'label' => 'Hardware', 'kw' => 'chip processor cpu hardware elektronika'],
                                    ['icon' => 'fa-shield-halved', 'label' => 'Keamanan', 'kw' => 'security cybersecurity keamanan sistem privasi'],

                                    // Bisnis & Ekonomi
                                    ['icon' => 'fa-chart-line', 'label' => 'Grafik Bisnis', 'kw' => 'ekonomi bisnis manajemen statistik pasar tren'],
                                    ['icon' => 'fa-briefcase', 'label' => 'Bisnis', 'kw' => 'manajemen karir usaha profesional kantor'],
                                    ['icon' => 'fa-coins', 'label' => 'Keuangan', 'kw' => 'finansial uang perbankan akuntansi aset'],
                                    ['icon' => 'fa-calculator', 'label' => 'Akuntansi', 'kw' => 'akuntansi hitung matematika pajak keuangan'],
                                    ['icon' => 'fa-handshake', 'label' => 'Kerjasama', 'kw' => 'relasi negosiasi partnership bisnis hukum'],
                                    ['icon' => 'fa-chart-pie', 'label' => 'Diagram', 'kw' => 'analisis data persentase statistik pasar'],

                                    // Teknik & Rekayasa
                                    ['icon' => 'fa-gears', 'label' => 'Mesin & Teknik', 'kw' => 'teknik mekanik mesin sistem manufaktur industri'],
                                    ['icon' => 'fa-compass-drafting', 'label' => 'Arsitektur', 'kw' => 'desain gambar teknik rancang bangun sipil arsitektur'],
                                    ['icon' => 'fa-bolt', 'label' => 'Elektro', 'kw' => 'listrik energi tegangan tenaga power daya'],
                                    ['icon' => 'fa-industry', 'label' => 'Industri', 'kw' => 'pabrik logistik manufaktur produksi supply chain'],
                                    ['icon' => 'fa-hard-hat', 'label' => 'Sipil/Konstruksi', 'kw' => 'teknik sipil bangunan proyek helm keselamatan'],
                                    ['icon' => 'fa-wrench', 'label' => 'Alat Teknik', 'kw' => 'perbaikan perkakas maintenance mekanik mesin'],

                                    // Seni, Desain & Komunikasi
                                    ['icon' => 'fa-palette', 'label' => 'Seni Lukis', 'kw' => 'seni dkv visual lukis kreatif warna'],
                                    ['icon' => 'fa-pen-nib', 'label' => 'Desain/Grafis', 'kw' => 'desain ilustrasi grafis dkv sastra kaligrafi'],
                                    ['icon' => 'fa-shapes', 'label' => 'Bentuk/Desain', 'kw' => 'geometri komposisi visual rancangan pola'],
                                    ['icon' => 'fa-camera', 'label' => 'Fotografi', 'kw' => 'kamera foto sinematografi media dokumentasi'],
                                    ['icon' => 'fa-bullhorn', 'label' => 'Komunikasi', 'kw' => 'humas public relations komunikasi penyiaran broadcast'],
                                    ['icon' => 'fa-newspaper', 'label' => 'Jurnalistik', 'kw' => 'berita media cetak pers publikasi warta'],
                                    ['icon' => 'fa-language', 'label' => 'Bahasa & Sastra', 'kw' => 'bahasa inggris sastra linguistik komunikasi translation'],

                                    // Hukum & Psikologi
                                    ['icon' => 'fa-scale-balanced', 'label' => 'Hukum/Keadilan', 'kw' => 'timbangan hukum advokat pasal undang keadilan norma'],
                                    ['icon' => 'fa-gavel', 'label' => 'Hakim/Pengadilan', 'kw' => 'palu pengadilan vonis peradilan yuridis'],
                                    ['icon' => 'fa-brain', 'label' => 'Psikologi', 'kw' => 'otak pikiran mental kejiwaan konseling neuro'],
                                    ['icon' => 'fa-people-group', 'label' => 'Sosial', 'kw' => 'masyarakat sosiologi komunitas kelompok hubungan'],
                                    ['icon' => 'fa-head-side-virus', 'label' => 'Kesehatan Mental', 'kw' => 'psikiatri perilaku pikiran konseling'],

                                    // Kesehatan & Kedokteran
                                    ['icon' => 'fa-stethoscope', 'label' => 'Kedokteran', 'kw' => 'dokter medis dokter spesialis kesehatan stetoskop'],
                                    ['icon' => 'fa-heart-pulse', 'label' => 'Kesehatan', 'kw' => 'detak jantung klinik kesehatan keperawatan'],
                                    ['icon' => 'fa-hospital', 'label' => 'Rumah Sakit', 'kw' => 'klinik fasilitas medis rawat inap kesehatan'],
                                    ['icon' => 'fa-dna', 'label' => 'Biomedis/Genetik', 'kw' => 'genetika biologi dna sains bioteknologi'],
                                    ['icon' => 'fa-flask', 'label' => 'Laboratorium', 'kw' => 'kimia riset eksperimen sains formula farmasi'],
                                    ['icon' => 'fa-pills', 'label' => 'Farmasi', 'kw' => 'obat farmasi kapsul apotek resep medis'],

                                    // Pertanian & Lingkungan
                                    ['icon' => 'fa-seedling', 'label' => 'Pertanian/Tunas', 'kw' => 'agronomi tanaman pangan bibit benih agro'],
                                    ['icon' => 'fa-leaf', 'label' => 'Lingkungan/Daun', 'kw' => 'ekologi teknologi lingkungan bio hijau alam'],
                                    ['icon' => 'fa-tree', 'label' => 'Hutan/Ekosistem', 'kw' => 'kehutanan pohon lingkungan alam konservasi'],
                                    ['icon' => 'fa-wheat-awn', 'label' => 'Teknologi Pangan', 'kw' => 'padi gandum panen pangan nutrisi pertanian'],
                                    ['icon' => 'fa-droplet', 'label' => 'Tata Air/Sumber Daya', 'kw' => 'air hidrologi fluida kebersihan lingkungan'],

                                    // Pendidikan & Umum
                                    ['icon' => 'fa-graduation-cap', 'label' => 'Pascasarjana', 'kw' => 'wisuda toga pasca magister doktor gelar akademik'],
                                    ['icon' => 'fa-book-open', 'label' => 'Buku/Pendidikan', 'kw' => 'ilmu belajar literasi studi perpustakaan baca'],
                                    ['icon' => 'fa-lightbulb', 'label' => 'Inovasi', 'kw' => 'ide kreativitas riset penemuan solusi inovatif'],
                                    ['icon' => 'fa-landmark', 'label' => 'Gedung/Fakultas', 'kw' => 'gedung pilar universitas institusi kampus fakultas'],
                                    ['icon' => 'fa-building-columns', 'label' => 'Institusi', 'kw' => 'kampus pilar klasik pemerintahan kelembagaan'],
                                    ['icon' => 'fa-microscope', 'label' => 'Penelitian/Riset', 'kw' => 'mikroskop sains sains penelitian telaah ilmiah'],
                                ];

                                foreach ($academicIcons as $ai): ?>
                                    <div class="col icon-picker-col" data-icon="<?= $ai['icon'] ?>" data-keywords="<?= htmlspecialchars($ai['kw'] . ' ' . strtolower($ai['label'])) ?>">
                                        <div class="icon-picker-btn p-2 text-center rounded-3 border d-flex flex-column align-items-center justify-content-center"
                                             data-icon="<?= $ai['icon'] ?>"
                                             onclick="selectIcon('<?= $ai['icon'] ?>')"
                                             style="cursor: pointer; height: 68px; transition: all 0.15s ease;">
                                            <i class="fas <?= $ai['icon'] ?> fs-5 mb-1 icon-glyph"></i>
                                            <span class="small text-truncate w-100 text-muted" style="font-size: 0.65rem;"><?= htmlspecialchars($ai['label']) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div id="iconNotFoundMessage" class="text-center py-4 text-muted small" style="display: none;">
                                <i class="fas fa-circle-question fs-3 opacity-50 mb-1 d-block"></i>
                                Ikon dengan kata kunci tersebut tidak ditemukan dalam daftar rekomendasi.<br>
                                <span class="text-secondary">Anda dapat menggunakan opsi 1 huruf di bawah ini.</span>
                            </div>
                        </div>

                        <!-- Opsi 1 Huruf Saja (Sesuai Permintaan User) -->
                        <div class="mt-3 pt-2.5 border-top d-flex flex-wrap align-items-center gap-2">
                            <span class="small text-secondary fw-semibold">atau hanya menggunakan 1 huruf:</span>
                            <div class="d-inline-flex align-items-center gap-2">
                                <input type="text" id="singleLetterInput" 
                                       class="form-control text-center fw-bolder text-uppercase font-monospace shadow-2xs" 
                                       maxlength="1" 
                                       placeholder="G" 
                                       style="width: 44px; height: 40px; font-size: 1.25rem; border: 2px solid #cbd5e1; border-radius: 10px;"
                                       autocomplete="off"
                                       title="Ketik 1 huruf inisial fakultas">
                                <span class="text-muted small" style="font-size: 0.72rem;">(Ketik 1 karakter inisial, contoh: <strong>F</strong>, <strong>E</strong>, <strong>H</strong>, <strong>K</strong>, <strong>T</strong>)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill bg-scu-blue border-0 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Data Fakultas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Modern Color Swatches Styling */
.btn-color-swatch {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 3px solid #ffffff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.08);
    padding: 0;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-color-swatch:hover {
    transform: translateY(-2px) scale(1.18);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.22);
}
.btn-color-swatch.active {
    transform: translateY(-2px) scale(1.22);
    box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px currentColor;
}
.btn-color-swatch.active::after {
    content: "✓";
    color: #ffffff;
    font-weight: 900;
    font-size: 0.85rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}
.btn-color-swatch-custom {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px dashed #94a3b8;
    background: #f8fafc;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.btn-color-swatch-custom:hover {
    background: #f1f5f9;
    border-color: #475569;
    transform: translateY(-2px) scale(1.15);
}

/* Icon Picker Button Styling */
.icon-picker-btn:hover {
    background-color: #f8fafc;
    border-color: #93c5fd !important;
    color: #2563EB;
}
.icon-picker-btn.active {
    background-color: #eff6ff !important;
    border-color: #2563EB !important;
    border-width: 2px !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
}
.icon-picker-btn.active .icon-glyph {
    color: #2563EB !important;
}
.icon-picker-btn.active span {
    color: #1e40af !important;
    font-weight: 700;
}
</style>

<script>
// State Icon & Warna
let selectedIcon = 'fa-landmark';
let selectedColor = '#1E3E62';
let selectedColorName = 'Navy SCU';

function updateVisualPreview() {
    // Hidden inputs
    document.getElementById('fakultas_icon').value = selectedIcon;
    document.getElementById('fakultas_warna').value = selectedColor;

    // Check if letter icon
    const isLetter = selectedIcon.startsWith('letter:');
    const letterChar = isLetter ? selectedIcon.replace('letter:', '').toUpperCase() : '';

    // Live preview elements
    const previewBox = document.getElementById('liveIconPreview');
    const previewIcon = document.getElementById('previewIconTag');
    const previewLetter = document.getElementById('previewLetterTag');
    const labelIcon = document.getElementById('labelSelectedIcon');
    const labelColor = document.getElementById('labelSelectedColor');
    const labelColorName = document.getElementById('labelSelectedColorName');

    if (previewBox && previewIcon && previewLetter) {
        previewBox.style.background = selectedColor + '18';
        previewBox.style.color = selectedColor;

        if (isLetter) {
            previewIcon.style.display = 'none';
            previewLetter.style.display = 'block';
            previewLetter.textContent = letterChar;
        } else {
            previewLetter.style.display = 'none';
            previewIcon.style.display = 'block';
            previewIcon.className = 'fas ' + selectedIcon;
        }
    }
    if (labelIcon) {
        labelIcon.textContent = isLetter ? `Huruf "${letterChar}"` : selectedIcon;
    }
    if (labelColor) {
        labelColor.textContent = selectedColor;
        labelColor.style.color = selectedColor;
    }
    if (labelColorName) {
        labelColorName.textContent = selectedColorName;
    }

    // Update active state on color buttons
    document.querySelectorAll('.btn-color-swatch').forEach(btn => {
        if (btn.getAttribute('data-color').toLowerCase() === selectedColor.toLowerCase()) {
            btn.classList.add('active');
            btn.style.color = selectedColor;
        } else {
            btn.classList.remove('active');
        }
    });

    // Update active state on icon grid
    document.querySelectorAll('.icon-picker-btn').forEach(btn => {
        if (!isLetter && btn.getAttribute('data-icon') === selectedIcon) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Sync single letter input
    const singleLetterInput = document.getElementById('singleLetterInput');
    if (singleLetterInput) {
        if (isLetter) {
            singleLetterInput.value = letterChar;
            singleLetterInput.style.borderColor = selectedColor;
            singleLetterInput.style.color = selectedColor;
        } else {
            singleLetterInput.value = '';
            singleLetterInput.style.borderColor = '#cbd5e1';
            singleLetterInput.style.color = '';
        }
    }

    // Sync custom color input
    const customPicker = document.getElementById('customColorPicker');
    if (customPicker && customPicker.value.toLowerCase() !== selectedColor.toLowerCase()) {
        customPicker.value = selectedColor;
    }
}

function selectIcon(iconName) {
    if (!iconName.startsWith('fa-')) {
        iconName = 'fa-' + iconName;
    }
    selectedIcon = iconName;
    const singleLetterInput = document.getElementById('singleLetterInput');
    if (singleLetterInput) {
        singleLetterInput.value = '';
    }
    updateVisualPreview();
}

function selectColor(colorHex, colorName = 'Kustom') {
    selectedColor = colorHex;
    selectedColorName = colorName;
    updateVisualPreview();
}

function resetFakultasForm() {
    document.getElementById('modalFakultasTitle').textContent = 'Tambah Fakultas Baru';
    document.getElementById('fakultas_id').value = '';
    document.getElementById('kode_fakultas').value = '';
    document.getElementById('nama_fakultas').value = '';
    document.getElementById('deskripsi').value = '';

    // Reset visual
    selectedIcon = 'fa-landmark';
    selectedColor = '#1E3E62';
    selectedColorName = 'Navy SCU';

    const singleLetterInput = document.getElementById('singleLetterInput');
    if (singleLetterInput) {
        singleLetterInput.value = '';
        singleLetterInput.style.borderColor = '#cbd5e1';
    }

    // Reset search
    const searchInput = document.getElementById('iconSearchInput');
    if (searchInput) {
        searchInput.value = '';
        filterIcons('');
    }

    updateVisualPreview();
}

function editFakultas(data) {
    document.getElementById('modalFakultasTitle').textContent = 'Ubah Data Fakultas';
    document.getElementById('fakultas_id').value = data.id;
    document.getElementById('kode_fakultas').value = data.kode_fakultas;
    document.getElementById('nama_fakultas').value = data.nama_fakultas;
    document.getElementById('deskripsi').value = data.deskripsi || '';

    // Set visual from existing data
    let existingIcon = data.icon || 'fa-landmark';
    if (existingIcon.startsWith('letter:')) {
        selectedIcon = existingIcon;
    } else if (existingIcon.length === 1) {
        selectedIcon = 'letter:' + existingIcon.toUpperCase();
    } else if (existingIcon.startsWith('fa-') && existingIcon.length === 4) {
        // e.g. fa-g from previous edit
        const possibleChar = existingIcon.substring(3).toUpperCase();
        if (/^[A-Z0-9]$/.test(possibleChar)) {
            selectedIcon = 'letter:' + possibleChar;
        } else {
            selectedIcon = existingIcon;
        }
    } else {
        if (!existingIcon.startsWith('fa-')) existingIcon = 'fa-' + existingIcon;
        selectedIcon = existingIcon;
    }

    selectedColor = data.warna || '#1E3E62';
    selectedColorName = 'Pilihan';

    // Find if color matches preset
    const matchingSwatch = document.querySelector(`.btn-color-swatch[data-color="${selectedColor}"]`);
    if (matchingSwatch) {
        selectedColorName = matchingSwatch.getAttribute('data-name') || 'Pilihan';
    }

    // Reset search
    const searchInput = document.getElementById('iconSearchInput');
    if (searchInput) {
        searchInput.value = '';
        filterIcons('');
    }

    updateVisualPreview();
}

function filterIcons(query) {
    const term = query.trim().toLowerCase();
    const cols = document.querySelectorAll('.icon-picker-col');
    let visibleCount = 0;

    cols.forEach(col => {
        const iconName = col.getAttribute('data-icon') || '';
        const keywords = col.getAttribute('data-keywords') || '';
        if (!term || iconName.toLowerCase().includes(term) || keywords.toLowerCase().includes(term)) {
            col.style.display = '';
            visibleCount++;
        } else {
            col.style.display = 'none';
        }
    });

    const notFound = document.getElementById('iconNotFoundMessage');
    if (notFound) {
        notFound.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Search input listener
    const searchInput = document.getElementById('iconSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterIcons(this.value);
        });
    }

    // Single letter input listener (Sesuai Permintaan User: Hanya 1 huruf)
    const singleLetterInput = document.getElementById('singleLetterInput');
    if (singleLetterInput) {
        singleLetterInput.addEventListener('input', function() {
            let val = this.value.trim().toUpperCase();
            if (val.length >= 1) {
                val = val.charAt(0);
                this.value = val;
                selectedIcon = 'letter:' + val;
                // De-select icon grid
                document.querySelectorAll('.icon-picker-btn').forEach(btn => btn.classList.remove('active'));
                updateVisualPreview();
            } else {
                selectedIcon = 'fa-landmark';
                updateVisualPreview();
            }
        });
    }

    // Custom color picker listener
    const customPicker = document.getElementById('customColorPicker');
    if (customPicker) {
        customPicker.addEventListener('input', function() {
            selectColor(this.value, 'Kustom (' + this.value.toUpperCase() + ')');
        });
    }

    // Initial preview setup
    updateVisualPreview();
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
