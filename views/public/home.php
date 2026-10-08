<?php
/**
 * Public Landing Page: Direktori Fakultas & Monitoring Siklus PPEPP
 * Universitas Katolik Soegijapranata (UNIKA / SCU)
 * Lembaga Penjaminan Mutu (LPM)
 */
require_once ROOT_PATH . '/views/layouts/header.php';

// Calculate cycle progress percentage based on maximum or standard
$maxDocs = max(1, ...array_values($cycleCounts));
$totalDokumenSafe = max(1, $totalDokumen);
?>

<!-- ============================================================
     1. HERO SECTION: ATMOSPHERIC INSTITUTIONAL PORTAL
============================================================ -->
<!-- ============================================================
     1. HERO SECTION: ATMOSPHERIC INSTITUTIONAL PORTAL
============================================================ -->
<section class="hero-banner">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            
            <div class="col-lg-10 col-xl-9">
                <div class="mb-3 text-white text-opacity-80 fw-semibold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">
                    <?= htmlspecialchars(get_landing_setting('hero_badge', 'Lembaga Penjaminan Mutu • UNIKA Soegijapranata')) ?>
                </div>

                <h1 class="hero-title">
                    <?= htmlspecialchars(get_landing_setting('hero_title', 'Sistem Penjaminan Mutu Internal')) ?> <br>
                    <span class="hero-highlight"><?= htmlspecialchars(get_landing_setting('hero_highlight', 'Siklus PPEPP Berkelanjutan')) ?></span>
                </h1>

                <p class="hero-desc mx-auto">
                    <?= nl2br(htmlspecialchars(get_landing_setting('hero_desc', 'Portal terpadu pengawasan dan repositori dokumen Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan mutu tridharma di seluruh program studi ' . INSTITUTION_NAME . '.'))) ?>
                </p>

                <div class="d-flex justify-content-center flex-wrap gap-3 pt-2">
                    <?php
                        $btn1Url = get_landing_setting('hero_btn1_url', '#direktoriFakultas');
                        $btn1IsExt = str_starts_with($btn1Url, 'http://') || str_starts_with($btn1Url, 'https://');
                        $btn1Target = ($btn1IsExt || str_starts_with($btn1Url, '#')) ? $btn1Url : base_url($btn1Url);
                    ?>
                    <a href="<?= htmlspecialchars($btn1Target) ?>" <?= $btn1IsExt ? 'target="_blank" rel="noopener noreferrer"' : '' ?> class="btn btn-warning fw-bold px-4 py-2.5 rounded-pill shadow-sm" style="font-family:var(--font-heading);">
                        <i class="fas fa-landmark me-2"></i> <?= htmlspecialchars(get_landing_setting('hero_btn1_text', 'Jelajahi Direktori Fakultas')) ?>
                    </a>
                    <?php 
                        $btn2Url = get_landing_setting('hero_btn2_url', 'dokumen');
                        $btn2IsExt = str_starts_with($btn2Url, 'http://') || str_starts_with($btn2Url, 'https://');
                        $btn2Target = ($btn2IsExt || str_starts_with($btn2Url, '#')) ? $btn2Url : base_url($btn2Url);
                    ?>
                    <a href="<?= htmlspecialchars($btn2Target) ?>" <?= $btn2IsExt ? 'target="_blank" rel="noopener noreferrer"' : '' ?> class="btn btn-outline-light fw-semibold px-4 py-2.5 rounded-pill" style="font-family:var(--font-heading);">
                        <i class="fas fa-file-shield me-2"></i> <?= htmlspecialchars(get_landing_setting('hero_btn2_text', 'Repositori Dokumen Mutu')) ?>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     2. STATS BAR: INSTITUTIONAL METRICS
============================================================ -->
<div class="stats-bar">
    <div class="container">
        <div class="row g-0">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-num"><?= $totalFakultas ?><span class="accent">+</span></div>
                    <div class="stat-label"><?= htmlspecialchars(get_landing_setting('stats_fakultas_label', 'Fakultas & Pascasarjana')) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-num"><?= $totalProdi ?><span class="accent">+</span></div>
                    <div class="stat-label"><?= htmlspecialchars(get_landing_setting('stats_prodi_label', 'Program Studi Aktif')) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-num"><?= $totalDokumen ?><span class="accent">+</span></div>
                    <div class="stat-label"><?= htmlspecialchars(get_landing_setting('stats_dokumen_label', 'Dokumen Mutu Terverifikasi')) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-num">5<span class="accent">/5</span></div>
                    <div class="stat-label"><?= htmlspecialchars(get_landing_setting('stats_siklus_label', 'Siklus PPEPP Terintegrasi')) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     3. 5 SIKLUS PPEPP PATHWAY (INTERACTIVE CARDS)
============================================================ -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title"><?= htmlspecialchars(get_landing_setting('siklus_section_title', '5 Siklus Penjaminan Mutu Internal (PPEPP)')) ?></h2>
            <p class="section-desc mx-auto">
                <?= htmlspecialchars(get_landing_setting('siklus_section_desc', 'Implementasi siklus berkelanjutan (Continuous Quality Improvement) sesuai pedoman Permendikbudristek No. 53 Tahun 2023 untuk mencapai akreditasi unggul institusi.')) ?>
            </p>
        </div>

        <div class="cycle-path-grid">
            
            <!-- P1: Penetapan -->
            <a href="<?= base_url('dokumen?siklus=penetapan') ?>" class="cycle-path-card card-p1">
                <div class="cycle-icon-bubble" style="background: #EFF6FF; color: #2563EB;">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div class="cycle-code-tag text-primary">Tahap P1</div>
                <h4 class="cycle-card-title"><?= htmlspecialchars(get_landing_setting('siklus_p1_title', 'Penetapan')) ?></h4>
                <p class="cycle-card-desc"><?= htmlspecialchars(get_landing_setting('siklus_p1_desc', 'Perumusan standar, manual mutu, kebijakan SPMI, dan Capaian Pembelajaran Lulusan (CPL).')) ?></p>
                <div class="cycle-card-footer">
                    <span class="cycle-doc-pill" style="background:#EFF6FF; color:#2563EB;">
                        <?= $cycleCounts['penetapan'] ?? 0 ?> Dokumen
                    </span>
                    <div class="cycle-arrow-btn"><i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

            <!-- P2: Pelaksanaan -->
            <a href="<?= base_url('dokumen?siklus=pelaksanaan') ?>" class="cycle-path-card card-p2">
                <div class="cycle-icon-bubble" style="background: #ECFDF5; color: #059669;">
                    <i class="fas fa-list-check"></i>
                </div>
                <div class="cycle-code-tag text-success">Tahap P2</div>
                <h4 class="cycle-card-title"><?= htmlspecialchars(get_landing_setting('siklus_p2_title', 'Pelaksanaan')) ?></h4>
                <p class="cycle-card-desc"><?= htmlspecialchars(get_landing_setting('siklus_p2_desc', 'Implementasi kurikulum, RPS, SOP pembelajaran, penelitian, dan pengabdian masyarakat prodi.')) ?></p>
                <div class="cycle-card-footer">
                    <span class="cycle-doc-pill" style="background:#ECFDF5; color:#059669;">
                        <?= $cycleCounts['pelaksanaan'] ?? 0 ?> Dokumen
                    </span>
                    <div class="cycle-arrow-btn"><i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

            <!-- E: Evaluasi -->
            <a href="<?= base_url('dokumen?siklus=evaluasi') ?>" class="cycle-path-card card-e">
                <div class="cycle-icon-bubble" style="background: #FFFBEB; color: #D97706;">
                    <i class="fas fa-magnifying-glass-chart"></i>
                </div>
                <div class="cycle-code-tag text-warning">Tahap E</div>
                <h4 class="cycle-card-title"><?= htmlspecialchars(get_landing_setting('siklus_e_title', 'Evaluasi')) ?></h4>
                <p class="cycle-card-desc"><?= htmlspecialchars(get_landing_setting('siklus_e_desc', 'Audit Mutu Internal (AMI), monitoring berkala, monev pembelajaran, dan pengukuran kepuasan pengguna.')) ?></p>
                <div class="cycle-card-footer">
                    <span class="cycle-doc-pill" style="background:#FFFBEB; color:#D97706;">
                        <?= $cycleCounts['evaluasi'] ?? 0 ?> Dokumen
                    </span>
                    <div class="cycle-arrow-btn"><i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

            <!-- P3: Pengendalian -->
            <a href="<?= base_url('dokumen?siklus=pengendalian') ?>" class="cycle-path-card card-p3">
                <div class="cycle-icon-bubble" style="background: #F3E8FF; color: #7C3AED;">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div class="cycle-code-tag" style="color:#7C3AED;">Tahap P3</div>
                <h4 class="cycle-card-title"><?= htmlspecialchars(get_landing_setting('siklus_p3_title', 'Pengendalian')) ?></h4>
                <p class="cycle-card-desc"><?= htmlspecialchars(get_landing_setting('siklus_p3_desc', 'Tindakan koreksi terhadap deviasi standar, evaluasi akar masalah, dan Rapat Tinjauan Manajemen (RTM).')) ?></p>
                <div class="cycle-card-footer">
                    <span class="cycle-doc-pill" style="background:#F3E8FF; color:#7C3AED;">
                        <?= $cycleCounts['pengendalian'] ?? 0 ?> Dokumen
                    </span>
                    <div class="cycle-arrow-btn"><i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

            <!-- P4: Peningkatan -->
            <a href="<?= base_url('dokumen?siklus=peningkatan') ?>" class="cycle-path-card card-p4">
                <div class="cycle-icon-bubble" style="background: #ECFEFF; color: #0891B2;">
                    <i class="fas fa-arrow-trend-up"></i>
                </div>
                <div class="cycle-code-tag text-info">Tahap P4</div>
                <h4 class="cycle-card-title"><?= htmlspecialchars(get_landing_setting('siklus_p4_title', 'Peningkatan')) ?></h4>
                <p class="cycle-card-desc"><?= htmlspecialchars(get_landing_setting('siklus_p4_desc', 'Pembaruan dan peningkatan standar mutu secara berkelanjutan (Kaizen) melampaui SN-Dikti.')) ?></p>
                <div class="cycle-card-footer">
                    <span class="cycle-doc-pill" style="background:#ECFEFF; color:#0891B2;">
                        <?= $cycleCounts['peningkatan'] ?? 0 ?> Dokumen
                    </span>
                    <div class="cycle-arrow-btn"><i class="fas fa-arrow-right"></i></div>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- ============================================================
     4. DIREKTORI FAKULTAS & PROGRAM STUDI
============================================================ -->
<section class="py-5" id="direktoriFakultas">
    <div class="container">
        
        <!-- Section Header -->
        <div class="text-center mb-4">
            <h2 class="section-title"><?= htmlspecialchars(get_landing_setting('direktori_section_title', 'Fakultas & Program Studi')) ?></h2>
            <p class="section-desc mx-auto">
                <?= htmlspecialchars(get_landing_setting('direktori_section_desc', 'Pilih fakultas di bawah untuk meninjau profil kepemimpinan, sebaran dokumen mutu 5 siklus PPEPP, dan status kepatuhan standar mutu masing-masing prodi.')) ?>
            </p>
        </div>

        <!-- Interactive Search Toolbar -->
        <div class="faculty-toolbar justify-content-center mb-4">
            <div class="faculty-search-input-wrap" style="max-width: 520px; width: 100%;">
                <i class="fas fa-search"></i>
                <input type="text" id="facultySearchInput" class="faculty-search-input" 
                       placeholder="Cari fakultas atau nama prodi..." autocomplete="off">
            </div>
        </div>

        <!-- Faculty Cards Grid -->
        <div class="row g-4" id="facultyCardsContainer">
            <?php 
            // Academic theme configuration per faculty code
            $facultyConfigs = [
                'FIK' => [
                    'icon' => 'fa-laptop-code',
                    'accent' => 'linear-gradient(90deg, #2563EB, #38BDF8)',
                    'color' => '#2563EB',
                    'bg_icon' => '#EFF6FF',
                    'category' => 'saintek',
                    'category_label' => 'Sains & Teknologi'
                ],
                'FEB' => [
                    'icon' => 'fa-chart-line',
                    'accent' => 'linear-gradient(90deg, #059669, #34D399)',
                    'color' => '#059669',
                    'bg_icon' => '#ECFDF5',
                    'category' => 'kreatif',
                    'category_label' => 'Bisnis & Ekonomi'
                ],
                'FAD' => [
                    'icon' => 'fa-compass-drafting',
                    'accent' => 'linear-gradient(90deg, #7C3AED, #C084FC)',
                    'color' => '#7C3AED',
                    'bg_icon' => '#F5F3FF',
                    'category' => 'kreatif',
                    'category_label' => 'Kreatif & Desain'
                ],
                'FT' => [
                    'icon' => 'fa-gears',
                    'accent' => 'linear-gradient(90deg, #D97706, #FBBF24)',
                    'color' => '#D97706',
                    'bg_icon' => '#FFFBEB',
                    'category' => 'saintek',
                    'category_label' => 'Teknik & Rekayasa'
                ],
                'FPSI' => [
                    'icon' => 'fa-brain',
                    'accent' => 'linear-gradient(90deg, #0D9488, #2DD4BF)',
                    'color' => '#0D9488',
                    'bg_icon' => '#F0FDFA',
                    'category' => 'soshum',
                    'category_label' => 'Sosial & Humaniora'
                ],
                'FHK' => [
                    'icon' => 'fa-scale-balanced',
                    'accent' => 'linear-gradient(90deg, #DC2626, #F87171)',
                    'color' => '#DC2626',
                    'bg_icon' => '#FEF2F2',
                    'category' => 'soshum',
                    'category_label' => 'Hukum & Komunikasi'
                ],
                'FBS' => [
                    'icon' => 'fa-language',
                    'accent' => 'linear-gradient(90deg, #EA580C, #FB923C)',
                    'color' => '#EA580C',
                    'bg_icon' => '#FFF7ED',
                    'category' => 'soshum',
                    'category_label' => 'Bahasa & Seni'
                ],
                'FTP' => [
                    'icon' => 'fa-seedling',
                    'accent' => 'linear-gradient(90deg, #16A34A, #4ADE80)',
                    'color' => '#16A34A',
                    'bg_icon' => '#F0FDF4',
                    'category' => 'saintek',
                    'category_label' => 'Agro & Teknologi Pangan'
                ],
                'FK' => [
                    'icon' => 'fa-stethoscope',
                    'accent' => 'linear-gradient(90deg, #0284C7, #38BDF8)',
                    'color' => '#0284C7',
                    'bg_icon' => '#F0F9FF',
                    'category' => 'kesehatan',
                    'category_label' => 'Kesehatan'
                ],
                'PASCA' => [
                    'icon' => 'fa-graduation-cap',
                    'accent' => 'linear-gradient(90deg, #6A1B9A, #A855F7)',
                    'color' => '#6A1B9A',
                    'bg_icon' => '#FAF5FF',
                    'category' => 'kesehatan',
                    'category_label' => 'Pascasarjana'
                ]
            ];

            foreach ($fakultasList as $fak): 
                $code = strtoupper(trim($fak['kode_fakultas']));
                $conf = $facultyConfigs[$code] ?? [
                    'icon' => 'fa-landmark',
                    'accent' => 'linear-gradient(90deg, #1E3E62, #0B192C)',
                    'color' => '#1E3E62',
                    'bg_icon' => '#F1F5F9',
                    'category' => 'soshum',
                    'category_label' => 'Fakultas'
                ];

                // Parse real prodi names into array
                $prodiList = !empty($fak['prodi_names']) ? explode('||', $fak['prodi_names']) : [];
                $searchKeywords = strtolower($fak['nama_fakultas'] . ' ' . $fak['kode_fakultas'] . ' ' . implode(' ', $prodiList));
            ?>
                <div class="col-xl-4 col-lg-6 col-md-6 faculty-grid-item" 
                     data-category="<?= $conf['category'] ?>" 
                     data-keywords="<?= htmlspecialchars($searchKeywords) ?>">
                    
                    <a href="<?= base_url('fakultas/' . $fak['id']) ?>" class="card-fakultas">
                        <!-- Top Accent Stripe -->
                        <div class="card-fakultas-accent" style="background: <?= $conf['accent'] ?>;"></div>
                        
                        <div class="card-fakultas-body">
                            
                            <!-- Top: Icon -->
                            <div class="card-fakultas-top">
                                <div class="card-fakultas-icon" style="background: <?= $conf['bg_icon'] ?>; color: <?= $conf['color'] ?>;">
                                    <i class="fas <?= $conf['icon'] ?>"></i>
                                </div>
                            </div>

                            <!-- Title & Description -->
                            <h3 class="card-fakultas-title"><?= htmlspecialchars($fak['nama_fakultas']) ?></h3>
                            <p class="card-fakultas-desc">
                                <?= htmlspecialchars($fak['deskripsi'] ?: 'Penyelenggara program studi unggul dengan penjaminan mutu terstandarisasi LPM SCU.') ?>
                            </p>

                            <!-- Real Prodi Badges Showcase -->
                            <div class="card-fakultas-prodi-list">
                                <?php if (!empty($prodiList)): ?>
                                    <?php foreach (array_slice($prodiList, 0, 3) as $prodiName): ?>
                                        <span class="prodi-tag-item">
                                            <i class="fas fa-check text-success"></i> <?= htmlspecialchars($prodiName) ?>
                                        </span>
                                    <?php endforeach; ?>
                                    <?php if (count($prodiList) > 3): ?>
                                        <span class="prodi-tag-item" style="color:var(--purple); font-weight:700;">
                                            +<?= count($prodiList) - 3 ?> prodi lainnya
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted small" style="font-size:0.75rem;">Program studi sedang dimutakhirkan</span>
                                <?php endif; ?>
                            </div>

                            <!-- Footer: Stats & Action -->
                            <div class="card-fakultas-footer">
                                <div class="card-fakultas-stats">
                                    <span><i class="fas fa-graduation-cap me-1" style="color:var(--navy);"></i> <?= $fak['total_prodi'] ?> Prodi</span>
                                    <span><i class="fas fa-file-contract me-1" style="color:#059669;"></i> <?= $fak['total_dokumen'] ?> Dokumen</span>
                                </div>
                                <span class="card-fakultas-action">
                                    Lihat Dokumen <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>

                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty Search State -->
        <div id="facultyEmptyState" class="card p-5 text-center border-0 shadow-sm rounded-4 mt-4" style="display:none;">
            <div class="mb-3 text-muted">
                <i class="fas fa-magnifying-glass fa-3x opacity-50"></i>
            </div>
            <h5 class="fw-bold text-dark-blue mb-1">Fakultas atau Program Studi Tidak Ditemukan</h5>
            <p class="text-muted small mb-3">Tidak ada hasil yang sesuai dengan kata kunci yang Anda masukkan.</p>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="resetFacultyFilter()">
                    <i class="fas fa-arrows-rotate me-1"></i> Reset Pencarian
                </button>
            </div>
        </div>

    </div>
</section>

<!-- Client-side Interactive Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('facultySearchInput');
    const facultyItems = document.querySelectorAll('.faculty-grid-item');
    const emptyState = document.getElementById('facultyEmptyState');

    let searchQuery = '';

    function filterFaculties() {
        let visibleCount = 0;

        facultyItems.forEach(item => {
            const itemKeywords = item.getAttribute('data-keywords') || '';
            const matchesSearch = (!searchQuery || itemKeywords.includes(searchQuery));

            if (matchesSearch) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchQuery = this.value.trim().toLowerCase();
            filterFaculties();
        });
    }

    window.resetFacultyFilter = function() {
        if (searchInput) searchInput.value = '';
        searchQuery = '';
        filterFaculties();
    };
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
