<?php
/**
 * Views: Kelola Beranda Publik & Footer (LPM CMS)
 * Path: views/super_admin/landing_settings.php
 * Design: LPM Ultra-Modern Design System
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';
?>

<div class="container-fluid p-0 landing-settings-wrapper">

    <!-- ========================================================
         PAGE HEADER BANNER
    ======================================================== -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0A192F 0%, #162447 55%, #1F4068 100%);">
        <!-- Decorative Glow Circles -->
        <div class="position-absolute top-0 end-0 translate-middle-y me-n5 mt-n5 rounded-circle" style="width: 320px; height: 320px; background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0) 70%); pointer-events: none;"></div>
        <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n5 rounded-circle" style="width: 400px; height: 250px; background: radial-gradient(circle, rgba(106, 27, 154, 0.25) 0%, transparent 70%); pointer-events: none;"></div>

        <div class="card-body p-4 p-lg-5 position-relative z-1 text-white">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: rgba(245, 158, 11, 0.2); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); font-size: 0.78rem;">
                            <i class="fas fa-sliders text-warning"></i> CMS Penjaminan Mutu
                        </span>
                        <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); font-size: 0.78rem;">
                            <i class="fas fa-circle-check text-success"></i> Sinkronisasi Real-Time
                        </span>
                        <?php if (!empty($lastUpdated)): ?>
                            <span class="text-white-50 small d-inline-flex align-items-center gap-1">
                                <i class="fas fa-clock-rotate-left"></i> Terakhir diubah: <?= date('d M Y, H:i', strtotime($lastUpdated)) ?> WIB
                            </span>
                        <?php endif; ?>
                    </div>

                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px; font-family: var(--font-heading);">
                        Kelola Konten Beranda &amp; Footer Publik
                    </h2>
                    <p class="text-white-50 mb-0" style="max-width: 720px; font-size: 0.95rem; line-height: 1.6;">
                        Konfigurasikan judul hero, teks badge, tombol navigasi, deskripsi 5 siklus PPEPP, label statistik, dan identitas kontak footer secara dinamis. Perubahan yang disimpan langsung aktif di halaman publik.
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                        <a href="<?= base_url() ?>" target="_blank" class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" style="backdrop-filter: blur(4px); font-size: 0.85rem;" title="Buka Halaman Muka Publik">
                            <i class="fas fa-arrow-up-right-from-square text-warning"></i>
                            <span>Pratinjau Publik</span>
                        </a>
                        <button type="button" class="btn btn-outline-danger text-light border-danger-subtle rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" style="background: rgba(220, 38, 38, 0.2); font-size: 0.85rem;" onclick="confirmResetDefaults()" title="Kembalikan semua nilai ke standar baku">
                            <i class="fas fa-rotate-left text-danger"></i>
                            <span>Reset Standar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================
         MAIN CARD CONTAINER WITH MODERN TABS
    ======================================================== -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 landing-card-main">
        
        <!-- Navigation Tabs Bar -->
        <div class="card-header bg-white border-bottom border-1 p-2 px-md-4 pt-md-3">
            <ul class="nav nav-pills custom-settings-pills gap-2" id="landingSettingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2" id="hero-tab" data-bs-toggle="pill" data-bs-target="#tab-hero" type="button" role="tab" aria-controls="tab-hero" aria-selected="true">
                        <span class="tab-icon-badge bg-primary text-white"><i class="fas fa-desktop"></i></span>
                        <span>1. Hero Banner</span>
                        <span class="badge bg-light text-dark rounded-pill px-2 py-0.5 ms-1">6</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 text-secondary" id="siklus-tab" data-bs-toggle="pill" data-bs-target="#tab-siklus" type="button" role="tab" aria-controls="tab-siklus" aria-selected="false">
                        <span class="tab-icon-badge bg-success text-white"><i class="fas fa-arrows-spin"></i></span>
                        <span>2. Alur 5 Siklus PPEPP</span>
                        <span class="badge bg-light text-dark rounded-pill px-2 py-0.5 ms-1">13</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 text-secondary" id="direktori-tab" data-bs-toggle="pill" data-bs-target="#tab-direktori" type="button" role="tab" aria-controls="tab-direktori" aria-selected="false">
                        <span class="tab-icon-badge bg-warning text-dark"><i class="fas fa-landmark"></i></span>
                        <span>3. Direktori &amp; Metrik</span>
                        <span class="badge bg-light text-dark rounded-pill px-2 py-0.5 ms-1">7</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 text-secondary" id="footer-tab" data-bs-toggle="pill" data-bs-target="#tab-footer" type="button" role="tab" aria-controls="tab-footer" aria-selected="false">
                        <span class="tab-icon-badge bg-info text-white"><i class="fas fa-shoe-prints"></i></span>
                        <span>4. Footer &amp; Kontak</span>
                        <span class="badge bg-light text-dark rounded-pill px-2 py-0.5 ms-1">8</span>
                    </button>
                </li>
            </ul>
        </div>

        <!-- Settings Form -->
        <form action="<?= base_url('admin/landing-settings/save') ?>" method="POST" id="formLandingSettings">
            <div class="card-body p-3 p-md-4 p-lg-5">
                <div class="tab-content" id="landingSettingContent">

                    <!-- ========================================================
                         TAB 1: HERO BANNER
                    ======================================================== -->
                    <div class="tab-pane fade show active" id="tab-hero" role="tabpanel" aria-labelledby="hero-tab">
                        
                        <!-- LIVE PREVIEW HERO -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="small text-uppercase fw-bold text-muted d-flex align-items-center gap-1.5">
                                    <i class="fas fa-eye text-primary"></i> Pratinjau Interaktif Hero (Live Visual)
                                </label>
                                <span class="badge bg-primary bg-opacity-10 text-primary small">Memperbarui Otomatis Saat Mengetik</span>
                            </div>

                            <div class="hero-preview-box rounded-4 p-4 p-md-5 text-center text-white position-relative overflow-hidden">
                                <div class="hero-preview-glow"></div>
                                <div class="position-relative z-1">
                                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); font-size: 0.8rem; color: #FCD34D;">
                                        <span class="preview-dot-pulse"></span>
                                        <span id="previewHeroBadge"><?= htmlspecialchars($settings['hero_badge'] ?? 'Lembaga Penjaminan Mutu • UNIKA Soegijapranata') ?></span>
                                    </div>
                                    <h3 class="fw-extrabold mb-1" id="previewHeroTitle" style="font-family: var(--font-heading); letter-spacing: -0.5px;">
                                        <?= htmlspecialchars($settings['hero_title'] ?? 'Sistem Penjaminan Mutu Internal') ?>
                                    </h3>
                                    <h3 class="fw-extrabold mb-3 text-warning" id="previewHeroHighlight" style="font-family: var(--font-heading); letter-spacing: -0.5px; background: linear-gradient(90deg, #F59E0B, #FBBF24); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                        <?= htmlspecialchars($settings['hero_highlight'] ?? 'Siklus PPEPP Berkelanjutan') ?>
                                    </h3>
                                    <p class="text-white-50 mx-auto mb-4" id="previewHeroDesc" style="max-width: 680px; font-size: 0.9rem; line-height: 1.6;">
                                        <?= htmlspecialchars($settings['hero_desc'] ?? 'Portal terpadu pengawasan dan repositori dokumen mutu...') ?>
                                    </p>
                                    <div class="d-flex justify-content-center gap-3">
                                        <button type="button" class="btn btn-warning btn-sm rounded-pill px-4 py-2 fw-bold text-dark" id="previewHeroBtn1">
                                            <i class="fas fa-compass me-1"></i> <span><?= htmlspecialchars($settings['hero_btn1_text'] ?? 'Jelajahi Direktori Fakultas') ?></span>
                                        </button>
                                        <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-4 py-2 fw-semibold" id="previewHeroBtn2">
                                            <i class="fas fa-folder-open me-1"></i> <span><?= htmlspecialchars($settings['hero_btn2_text'] ?? 'Repositori Dokumen Mutu') ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hero Input Fields -->
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-group-card p-3 p-md-4 rounded-3 border bg-light-subtle">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="stat-mini-icon bg-primary text-white rounded-circle"><i class="fas fa-heading"></i></div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Teks Judul &amp; Badge Pill</h6>
                                            <small class="text-muted">Atur teks utama yang menarik perhatian pengunjung pertama kali.</small>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-secondary">Teks Badge Pill (Paling Atas)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-tag"></i></span>
                                                <input type="text" name="settings[hero_badge]" id="inputHeroBadge" class="form-control border-start-0 ps-0" value="<?= htmlspecialchars($settings['hero_badge'] ?? '') ?>" placeholder="Contoh: Lembaga Penjaminan Mutu • UNIKA Soegijapranata" required>
                                            </div>
                                            <div class="form-text small">Teks di dalam kapsul transparan dengan efek titik kuning bercahaya.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-secondary">Judul Utama (Baris 1 - Putih)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-t"></i></span>
                                                <input type="text" name="settings[hero_title]" id="inputHeroTitle" class="form-control border-start-0 ps-0 fw-semibold" value="<?= htmlspecialchars($settings['hero_title'] ?? '') ?>" placeholder="Sistem Penjaminan Mutu Internal" required>
                                            </div>
                                            <div class="form-text small">Baris pertama judul hero dengan ukuran font besar.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-secondary">Judul Highlight Emas (Baris 2 - Gradasi Emas)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-end-0 text-warning"><i class="fas fa-sparkles"></i></span>
                                                <input type="text" name="settings[hero_highlight]" id="inputHeroHighlight" class="form-control border-start-0 ps-0 fw-bold text-warning" style="background: #0A192F;" value="<?= htmlspecialchars($settings['hero_highlight'] ?? '') ?>" placeholder="Siklus PPEPP Berkelanjutan" required>
                                            </div>
                                            <div class="form-text small">Baris kedua dengan gradasi warna emas/amber yang menyala.</div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-secondary">Deskripsi Pengantar Hero</label>
                                            <textarea name="settings[hero_desc]" id="inputHeroDesc" class="form-control" rows="3" placeholder="Narasi ringkas tujuan dan fungsi sistem penjaminan mutu..." required><?= htmlspecialchars($settings['hero_desc'] ?? '') ?></textarea>
                                            <div class="form-text small">Paragraf penjelas yang tampil tepat di bawah judul utama.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-card p-3 p-md-4 rounded-3 border bg-white h-100 shadow-2xs">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="stat-mini-icon bg-warning text-dark rounded-circle"><i class="fas fa-hand-pointer"></i></div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Tombol Aksi Utama (Kuning)</h6>
                                            <small class="text-muted">Tombol utama dengan aksen emas.</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Teks Label Tombol</label>
                                        <input type="text" name="settings[hero_btn1_text]" id="inputHeroBtn1Text" class="form-control" value="<?= htmlspecialchars($settings['hero_btn1_text'] ?? '') ?>" placeholder="Jelajahi Direktori Fakultas">
                                    </div>

                                    <div>
                                        <label class="form-label small fw-semibold text-secondary">URL / Anchor Tujuan</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted small"><i class="fas fa-link"></i></span>
                                            <input type="text" name="settings[hero_btn1_url]" class="form-control" value="<?= htmlspecialchars($settings['hero_btn1_url'] ?? '') ?>" placeholder="#direktoriFakultas">
                                        </div>
                                        <div class="form-text small">Gunakan <code>#direktoriFakultas</code> untuk scroll halus ke direktori.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-card p-3 p-md-4 rounded-3 border bg-white h-100 shadow-2xs">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="stat-mini-icon bg-secondary text-white rounded-circle"><i class="fas fa-folder-open"></i></div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Tombol Aksi Sekunder (Outline)</h6>
                                            <small class="text-muted">Tombol pendukung dengan border putih/outline.</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Teks Label Tombol</label>
                                        <input type="text" name="settings[hero_btn2_text]" id="inputHeroBtn2Text" class="form-control" value="<?= htmlspecialchars($settings['hero_btn2_text'] ?? '') ?>" placeholder="Repositori Dokumen Mutu">
                                    </div>

                                    <div>
                                        <label class="form-label small fw-semibold text-secondary">URL / Halaman Tujuan</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted small"><i class="fas fa-link"></i></span>
                                            <input type="text" name="settings[hero_btn2_url]" class="form-control" value="<?= htmlspecialchars($settings['hero_btn2_url'] ?? '') ?>" placeholder="dokumen">
                                        </div>
                                        <div class="form-text small">Gunakan <code>dokumen</code> untuk membuka repositori dokumen publik.</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- ========================================================
                         TAB 2: ALUR 5 SIKLUS PPEPP
                    ======================================================== -->
                    <div class="tab-pane fade" id="tab-siklus" role="tabpanel" aria-labelledby="siklus-tab">
                        
                        <!-- Header Section Info -->
                        <div class="form-group-card p-3 p-md-4 rounded-3 border bg-light-subtle mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="stat-mini-icon bg-success text-white rounded-circle"><i class="fas fa-arrows-spin"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Header Seksi Alur 5 Siklus PPEPP</h6>
                                    <small class="text-muted">Ubah label tag, judul besar, dan paragraf pembuka untuk seksi alur PPEPP.</small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-secondary">Tag Kategori Seksi</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="fas fa-bookmark"></i></span>
                                        <input type="text" name="settings[siklus_section_tag]" class="form-control font-monospace" value="<?= htmlspecialchars($settings['siklus_section_tag'] ?? '') ?>" placeholder="ALUR PENJAMINAN MUTU">
                                    </div>
                                </div>

                                <div class="col-md-9">
                                    <label class="form-label fw-bold small text-secondary">Judul Besar Seksi 5 Siklus</label>
                                    <input type="text" name="settings[siklus_section_title]" class="form-control fw-bold" value="<?= htmlspecialchars($settings['siklus_section_title'] ?? '') ?>" placeholder="5 Siklus Penjaminan Mutu Internal (PPEPP)">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">Deskripsi Pengantar Siklus PPEPP</label>
                                    <textarea name="settings[siklus_section_desc]" class="form-control" rows="2" placeholder="Implementasi siklus berkelanjutan (CQI) sesuai pedoman Permendikbudristek No. 53 Tahun 2023..."><?= htmlspecialchars($settings['siklus_section_desc'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 5 Cycle Cards Configuration -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-cards-blank text-primary me-1.5"></i> Konfigurasi Kartu 5 Fase PPEPP</h6>
                            <span class="badge bg-light text-muted border">5 Kartu Alur Berurutan</span>
                        </div>

                        <div class="row g-4">
                            <!-- Card P1: Penetapan -->
                            <div class="col-lg-6">
                                <div class="card ppepp-config-card ppepp-p1 border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-header border-0 bg-transparent pt-3 pb-0 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="ppepp-badge-pill ppepp-bg-p1">P1</span>
                                            <h6 class="fw-bold mb-0 text-dark">Siklus 1: Penetapan</h6>
                                        </div>
                                        <i class="fas fa-shield-halved text-purple fa-lg opacity-50"></i>
                                    </div>
                                    <div class="card-body p-3 p-md-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-secondary">Judul Kartu</label>
                                            <input type="text" name="settings[siklus_p1_title]" class="form-control" value="<?= htmlspecialchars($settings['siklus_p1_title'] ?? '') ?>" placeholder="Penetapan">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-semibold text-secondary">Deskripsi / Uraian Kartu</label>
                                            <textarea name="settings[siklus_p1_desc]" class="form-control" rows="3" placeholder="Perumusan standar, manual mutu, kebijakan SPMI..."><?= htmlspecialchars($settings['siklus_p1_desc'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card P2: Pelaksanaan -->
                            <div class="col-lg-6">
                                <div class="card ppepp-config-card ppepp-p2 border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-header border-0 bg-transparent pt-3 pb-0 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="ppepp-badge-pill ppepp-bg-p2">P2</span>
                                            <h6 class="fw-bold mb-0 text-dark">Siklus 2: Pelaksanaan</h6>
                                        </div>
                                        <i class="fas fa-play-circle text-success fa-lg opacity-50"></i>
                                    </div>
                                    <div class="card-body p-3 p-md-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-secondary">Judul Kartu</label>
                                            <input type="text" name="settings[siklus_p2_title]" class="form-control" value="<?= htmlspecialchars($settings['siklus_p2_title'] ?? '') ?>" placeholder="Pelaksanaan">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-semibold text-secondary">Deskripsi / Uraian Kartu</label>
                                            <textarea name="settings[siklus_p2_desc]" class="form-control" rows="3" placeholder="Implementasi kurikulum, RPS, SOP pembelajaran..."><?= htmlspecialchars($settings['siklus_p2_desc'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card E: Evaluasi -->
                            <div class="col-lg-4">
                                <div class="card ppepp-config-card ppepp-e border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-header border-0 bg-transparent pt-3 pb-0 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="ppepp-badge-pill ppepp-bg-e">E</span>
                                            <h6 class="fw-bold mb-0 text-dark">Siklus 3: Evaluasi</h6>
                                        </div>
                                        <i class="fas fa-magnifying-glass-chart text-warning fa-lg opacity-50"></i>
                                    </div>
                                    <div class="card-body p-3 p-md-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-secondary">Judul Kartu</label>
                                            <input type="text" name="settings[siklus_e_title]" class="form-control" value="<?= htmlspecialchars($settings['siklus_e_title'] ?? '') ?>" placeholder="Evaluasi">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-semibold text-secondary">Deskripsi / Uraian Kartu</label>
                                            <textarea name="settings[siklus_e_desc]" class="form-control" rows="4" placeholder="Audit Mutu Internal (AMI), monitoring berkala..."><?= htmlspecialchars($settings['siklus_e_desc'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card P3: Pengendalian -->
                            <div class="col-lg-4">
                                <div class="card ppepp-config-card ppepp-p3 border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-header border-0 bg-transparent pt-3 pb-0 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="ppepp-badge-pill ppepp-bg-p3">P3</span>
                                            <h6 class="fw-bold mb-0 text-dark">Siklus 4: Pengendalian</h6>
                                        </div>
                                        <i class="fas fa-compass-drafting text-primary fa-lg opacity-50"></i>
                                    </div>
                                    <div class="card-body p-3 p-md-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-secondary">Judul Kartu</label>
                                            <input type="text" name="settings[siklus_p3_title]" class="form-control" value="<?= htmlspecialchars($settings['siklus_p3_title'] ?? '') ?>" placeholder="Pengendalian">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-semibold text-secondary">Deskripsi / Uraian Kartu</label>
                                            <textarea name="settings[siklus_p3_desc]" class="form-control" rows="4" placeholder="Tindakan koreksi terhadap deviasi standar, evaluasi akar masalah..."><?= htmlspecialchars($settings['siklus_p3_desc'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card P4: Peningkatan -->
                            <div class="col-lg-4">
                                <div class="card ppepp-config-card ppepp-p4 border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-header border-0 bg-transparent pt-3 pb-0 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="ppepp-badge-pill ppepp-bg-p4">P4</span>
                                            <h6 class="fw-bold mb-0 text-dark">Siklus 5: Peningkatan</h6>
                                        </div>
                                        <i class="fas fa-chart-line text-info fa-lg opacity-50"></i>
                                    </div>
                                    <div class="card-body p-3 p-md-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-secondary">Judul Kartu</label>
                                            <input type="text" name="settings[siklus_p4_title]" class="form-control" value="<?= htmlspecialchars($settings['siklus_p4_title'] ?? '') ?>" placeholder="Peningkatan">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-semibold text-secondary">Deskripsi / Uraian Kartu</label>
                                            <textarea name="settings[siklus_p4_desc]" class="form-control" rows="4" placeholder="Pembaruan dan peningkatan standar mutu secara berkelanjutan..."><?= htmlspecialchars($settings['siklus_p4_desc'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- ========================================================
                         TAB 3: DIREKTORI & METRIK
                    ======================================================== -->
                    <div class="tab-pane fade" id="tab-direktori" role="tabpanel" aria-labelledby="direktori-tab">
                        
                        <!-- Header Direktori -->
                        <div class="form-group-card p-3 p-md-4 rounded-3 border bg-light-subtle mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="stat-mini-icon bg-warning text-dark rounded-circle"><i class="fas fa-landmark"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Header Bagian Direktori Akademik</h6>
                                    <small class="text-muted">Judul dan pengantar pada seksi direktori fakultas dan program studi publik.</small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-secondary">Tag Kategori Direktori</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="fas fa-bookmark"></i></span>
                                        <input type="text" name="settings[direktori_section_tag]" class="form-control font-monospace" value="<?= htmlspecialchars($settings['direktori_section_tag'] ?? '') ?>" placeholder="DIREKTORI AKADEMIK">
                                    </div>
                                </div>

                                <div class="col-md-9">
                                    <label class="form-label fw-bold small text-secondary">Judul Seksi Direktori</label>
                                    <input type="text" name="settings[direktori_section_title]" class="form-control fw-bold" value="<?= htmlspecialchars($settings['direktori_section_title'] ?? '') ?>" placeholder="Fakultas &amp; Program Studi">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">Deskripsi Pengantar Direktori</label>
                                    <textarea name="settings[direktori_section_desc]" class="form-control" rows="2" placeholder="Pilih fakultas di bawah untuk meninjau profil kepemimpinan, sebaran dokumen mutu 5 siklus PPEPP..."><?= htmlspecialchars($settings['direktori_section_desc'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Baris Metrik Live & Label Customizer -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-chart-pie text-primary me-1.5"></i> Label Pita Statistik Beranda</h6>
                                <small class="text-muted">Angka dihitung secara real-time dari database. Sesuaikan label penjelas pada masing-masing metrik.</small>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20">Live System Counts</span>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Metrik 1: Fakultas -->
                            <div class="col-md-6 col-lg-3">
                                <div class="card border rounded-4 p-3 bg-white shadow-2xs h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 small">
                                            <i class="fas fa-landmark me-1"></i> Data Riil
                                        </span>
                                        <div class="fs-4 fw-extrabold text-primary"><?= $totalFakultas ?></div>
                                    </div>
                                    <label class="form-label small fw-bold text-dark mb-1">Label Teks Fakultas</label>
                                    <input type="text" name="settings[stats_fakultas_label]" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['stats_fakultas_label'] ?? '') ?>" placeholder="Fakultas &amp; Pascasarjana">
                                    <div class="form-text small text-muted">Contoh: Fakultas &amp; Pascasarjana</div>
                                </div>
                            </div>

                            <!-- Metrik 2: Prodi -->
                            <div class="col-md-6 col-lg-3">
                                <div class="card border rounded-4 p-3 bg-white shadow-2xs h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 small">
                                            <i class="fas fa-graduation-cap me-1"></i> Data Riil
                                        </span>
                                        <div class="fs-4 fw-extrabold text-info"><?= $totalProdi ?></div>
                                    </div>
                                    <label class="form-label small fw-bold text-dark mb-1">Label Teks Program Studi</label>
                                    <input type="text" name="settings[stats_prodi_label]" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['stats_prodi_label'] ?? '') ?>" placeholder="Program Studi Aktif">
                                    <div class="form-text small text-muted">Contoh: Program Studi Aktif</div>
                                </div>
                            </div>

                            <!-- Metrik 3: Dokumen -->
                            <div class="col-md-6 col-lg-3">
                                <div class="card border rounded-4 p-3 bg-white shadow-2xs h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 small">
                                            <i class="fas fa-file-shield me-1"></i> Data Riil
                                        </span>
                                        <div class="fs-4 fw-extrabold text-success"><?= $totalDokumen ?></div>
                                    </div>
                                    <label class="form-label small fw-bold text-dark mb-1">Label Teks Dokumen</label>
                                    <input type="text" name="settings[stats_dokumen_label]" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['stats_dokumen_label'] ?? '') ?>" placeholder="Dokumen Mutu Terverifikasi">
                                    <div class="form-text small text-muted">Contoh: Dokumen Mutu Terverifikasi</div>
                                </div>
                            </div>

                            <!-- Metrik 4: Siklus -->
                            <div class="col-md-6 col-lg-3">
                                <div class="card border rounded-4 p-3 bg-white shadow-2xs h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 small">
                                            <i class="fas fa-arrows-spin me-1"></i> Standar
                                        </span>
                                        <div class="fs-4 fw-extrabold text-warning">5 Siklus</div>
                                    </div>
                                    <label class="form-label small fw-bold text-dark mb-1">Label Teks Siklus PPEPP</label>
                                    <input type="text" name="settings[stats_siklus_label]" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['stats_siklus_label'] ?? '') ?>" placeholder="Siklus PPEPP Terintegrasi">
                                    <div class="form-text small text-muted">Contoh: Siklus PPEPP Terintegrasi</div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ========================================================
                         TAB 4: FOOTER & KONTAK
                    ======================================================== -->
                    <div class="tab-pane fade" id="tab-footer" role="tabpanel" aria-labelledby="footer-tab">
                        
                        <!-- LIVE FOOTER PREVIEW -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="small text-uppercase fw-bold text-muted d-flex align-items-center gap-1.5">
                                    <i class="fas fa-eye text-info"></i> Pratinjau Interaktif Footer Publik
                                </label>
                                <span class="badge bg-info bg-opacity-10 text-info small">Format Bersih &amp; Halus (Smooth &amp; Clean)</span>
                            </div>

                            <div class="footer-preview-box rounded-4 p-4 text-white position-relative">
                                <div class="row g-4 align-items-start">
                                    <div class="col-lg-6">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="preview-footer-icon"><i class="fas fa-award text-warning"></i></div>
                                            <div>
                                                <div class="fw-bold fs-5 text-white" id="previewFooterBrand"><?= htmlspecialchars($settings['footer_brand_title'] ?? 'PETRA') ?></div>
                                                <div class="text-white-50 small" id="previewFooterSub"><?= htmlspecialchars($settings['footer_brand_sub'] ?? 'PEmantauan Tahapan PPEPP & Rencana Aksi') ?></div>
                                            </div>
                                        </div>
                                        <p class="text-white-50 small mb-3" id="previewFooterDesc" style="line-height: 1.5;">
                                            <?= htmlspecialchars($settings['footer_desc'] ?? 'PETRA = PEmantauan Tahapan PPEPP & Rencana Aksi. PETRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.') ?>
                                        </p>
                                        <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); color: #FCD34D; font-size: 0.75rem;">
                                            <i class="fas fa-certificate text-warning"></i>
                                            <span id="previewFooterAkreditasi"><?= htmlspecialchars($settings['footer_akreditasi'] ?? 'Terakreditasi UNGGUL • BAN-PT') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                            <div class="text-white-50 small mb-2 d-flex align-items-start gap-2">
                                                <i class="fas fa-location-dot text-danger mt-1"></i>
                                                <span id="previewFooterAddress"><?= htmlspecialchars($settings['footer_address'] ?? 'Ruang Lembaga Penjaminan Mutu, Gedung Thomas Aquinas Lantai 5, Kampus Universitas Katolik Soegijapranata, Jalan Pawiyatan Luhur IV/1 Bendan Duwur, Semarang 50234') ?></span>
                                            </div>
                                            <div class="text-white-50 small mb-2 d-flex align-items-center gap-2">
                                                <i class="fas fa-envelope text-info"></i>
                                                <span id="previewFooterEmail"><?= htmlspecialchars($settings['footer_email'] ?? 'lpm@unika.ac.id') ?></span>
                                            </div>
                                            <div class="text-white-50 small mb-2 d-flex align-items-center gap-2">
                                                <i class="fas fa-phone text-success"></i>
                                                <span id="previewFooterPhone"><?= htmlspecialchars($settings['footer_phone'] ?? '024-8441555 Ext 1473') ?></span>
                                            </div>
                                            <div class="mt-3">
                                                <span class="btn btn-outline-light btn-xs rounded-pill px-3 py-1 small" style="font-size:0.75rem;">
                                                    <i class="fas fa-globe me-1"></i> <span id="previewFooterWeb"><?= htmlspecialchars($settings['footer_website_text'] ?? 'Website Utama SCU') ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Inputs Form -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group-card p-3 p-md-4 rounded-3 border bg-white h-100 shadow-2xs">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="stat-mini-icon bg-info text-white rounded-circle"><i class="fas fa-building-columns"></i></div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Brand &amp; Identitas Footer</h6>
                                            <small class="text-muted">Nama sistem dan sub-judul institusi.</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Judul Brand Footer</label>
                                        <input type="text" name="settings[footer_brand_title]" id="inputFooterBrand" class="form-control fw-bold" value="<?= htmlspecialchars($settings['footer_brand_title'] ?? '') ?>" placeholder="PETRA" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Sub-Judul Brand Footer</label>
                                        <input type="text" name="settings[footer_brand_sub]" id="inputFooterSub" class="form-control" value="<?= htmlspecialchars($settings['footer_brand_sub'] ?? '') ?>" placeholder="PEmantauan Tahapan PPEPP &amp; Rencana Aksi" required>
                                    </div>

                                    <div>
                                        <label class="form-label small fw-semibold text-secondary">Badge Status Akreditasi Institusi</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-end-0 text-warning"><i class="fas fa-certificate"></i></span>
                                            <input type="text" name="settings[footer_akreditasi]" id="inputFooterAkreditasi" class="form-control border-start-0 ps-0 fw-bold text-warning" style="background: #0A192F;" value="<?= htmlspecialchars($settings['footer_akreditasi'] ?? '') ?>" placeholder="Terakreditasi UNGGUL • BAN-PT">
                                        </div>
                                        <div class="form-text small">Teks di dalam badge emas akreditasi BAN-PT.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-card p-3 p-md-4 rounded-3 border bg-white h-100 shadow-2xs">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="stat-mini-icon bg-success text-white rounded-circle"><i class="fas fa-address-book"></i></div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Kontak &amp; Tautan Resmi</h6>
                                            <small class="text-muted">Email, telepon, dan portal universitas.</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Email Resmi Layanan Mutu</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fas fa-envelope text-info"></i></span>
                                            <input type="email" name="settings[footer_email]" id="inputFooterEmail" class="form-control" value="<?= htmlspecialchars($settings['footer_email'] ?? '') ?>" placeholder="lpm@unika.ac.id">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary">Nomor Telepon / Hotline</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fas fa-phone text-success"></i></span>
                                            <input type="text" name="settings[footer_phone]" id="inputFooterPhone" class="form-control" value="<?= htmlspecialchars($settings['footer_phone'] ?? '') ?>" placeholder="024-8441555 Ext 1473">
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold text-secondary">Label Tautan Web</label>
                                            <input type="text" name="settings[footer_website_text]" id="inputFooterWeb" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['footer_website_text'] ?? '') ?>" placeholder="Website Utama SCU">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold text-secondary">URL Website SCU</label>
                                            <input type="url" name="settings[footer_website_url]" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['footer_website_url'] ?? '') ?>" placeholder="https://www.unika.ac.id">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group-card p-3 p-md-4 rounded-3 border bg-light-subtle">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Uraian / Deskripsi Penjaminan Mutu Footer</label>
                                            <textarea name="settings[footer_desc]" id="inputFooterDesc" class="form-control" rows="3" placeholder="Sistem Informasi Manajemen Siklus PPEPP mendukung transparansi penjaminan mutu..."><?= htmlspecialchars($settings['footer_desc'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Alamat Lengkap &amp; Lokasi Kantor LPM</label>
                                            <textarea name="settings[footer_address]" id="inputFooterAddress" class="form-control" rows="3" placeholder="Ruang Lembaga Penjaminan Mutu, Gedung Thomas Aquinas Lantai 5, Kampus Universitas Katolik Soegijapranata, Jalan Pawiyatan Luhur IV/1 Bendan Duwur, Semarang 50234"><?= htmlspecialchars($settings['footer_address'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- Sticky Bottom Actions Bar -->
            <div class="card-footer bg-white border-top p-3 px-md-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 sticky-bottom shadow-lg">
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <span class="preview-dot-pulse-green"></span>
                    <span>Siap diterapkan: perubahan tersimpan langsung sinkron tanpa perlu restart server.</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold" onclick="confirmResetDefaults()">
                        <i class="fas fa-rotate-left me-1"></i> Reset Standar
                    </button>
                    <button type="submit" id="btnSubmitSettings" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="fas fa-floppy-disk"></i>
                        <span>Simpan Seluruh Pengaturan</span>
                    </button>
                </div>
            </div>
        </form>

    </div>

</div>

<!-- Hidden Form for Reset Action -->
<form action="<?= base_url('admin/landing-settings/reset') ?>" method="POST" id="formResetSettings" style="display:none;"></form>

<style>
/* Modern Styling Tokens */
.landing-settings-wrapper {
    --primary-blue: #0284C7;
    --navy-dark: #0A192F;
    --card-border: #E2E8F0;
}

/* Tab Pills Styling */
.custom-settings-pills .nav-link {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    transition: all 0.22s ease-in-out;
    font-size: 0.88rem;
    color: #475569;
}
.custom-settings-pills .nav-link:hover {
    background: #F1F5F9;
    border-color: #CBD5E1;
    transform: translateY(-1px);
}
.custom-settings-pills .nav-link.active {
    background: linear-gradient(135deg, #0A192F 0%, #1E3A8A 100%) !important;
    border-color: #1E3A8A !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 14px rgba(10, 25, 47, 0.15);
}
.custom-settings-pills .nav-link.active .badge {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #FFFFFF !important;
}
.tab-icon-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 0.72rem;
}

/* Stat & Mini Icons */
.stat-mini-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
}

/* Live Hero Preview Box */
.hero-preview-box {
    background: linear-gradient(135deg, #0A192F 0%, #0F172A 50%, #1E293B 100%);
    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 10px 30px rgba(10, 25, 47, 0.25);
}
.hero-preview-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 380px;
    height: 220px;
    background: radial-gradient(circle, rgba(245, 158, 11, 0.12) 0%, transparent 70%);
    pointer-events: none;
}
.preview-dot-pulse {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #F59E0B;
    box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
    animation: pulse-gold 1.8s infinite;
}
.preview-dot-pulse-green {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #10B981;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse-green 1.8s infinite;
}
@keyframes pulse-gold {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}
@keyframes pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Live Footer Preview Box */
.footer-preview-box {
    background: linear-gradient(135deg, #070F1E 0%, #0A192F 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 24px rgba(7, 15, 30, 0.3);
}
.preview-footer-icon {
    width: 40px;
    height: 40px;
    background: rgba(245, 158, 11, 0.15);
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* PPEPP Cards Visual Styling */
.ppepp-config-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.ppepp-config-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(30, 41, 59, 0.08) !important;
}
.ppepp-badge-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    font-weight: 800;
    font-size: 0.8rem;
    color: #FFFFFF;
}
.ppepp-p1 { background: #FAF5FF; border-left: 4px solid #7C3AED !important; }
.ppepp-bg-p1 { background: #7C3AED; }
.ppepp-p2 { background: #F0FDF4; border-left: 4px solid #059669 !important; }
.ppepp-bg-p2 { background: #059669; }
.ppepp-e  { background: #FFFBEB; border-left: 4px solid #D97706 !important; }
.ppepp-bg-e  { background: #D97706; }
.ppepp-p3 { background: #EEF2FF; border-left: 4px solid #4F46E5 !important; }
.ppepp-bg-p3 { background: #4F46E5; }
.ppepp-p4 { background: #ECFEFF; border-left: 4px solid #0891B2 !important; }
.ppepp-bg-p4 { background: #0891B2; }

/* Form Control Focus Glow */
.form-control:focus, .form-select:focus {
    border-color: #0284C7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.shadow-2xs {
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
</style>

<script>
// Confirm SweetAlert Reset Defaults
function confirmResetDefaults() {
    Swal.fire({
        title: 'Kembalikan ke Standar Awal?',
        text: 'Seluruh teks beranda dan footer akan di-reset ke nilai default PETRA UNIKA Soegijapranata.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-rotate-left me-1"></i> Ya, Reset ke Standar',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sedang Mereset...',
                text: 'Mohon tunggu sejenak.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('formResetSettings').submit();
        }
    });
}

// Interactive Real-Time Preview Binder
document.addEventListener('DOMContentLoaded', function() {
    // Hero Elements
    const inputBadge = document.getElementById('inputHeroBadge');
    const inputTitle = document.getElementById('inputHeroTitle');
    const inputHighlight = document.getElementById('inputHeroHighlight');
    const inputDesc = document.getElementById('inputHeroDesc');
    const inputBtn1 = document.getElementById('inputHeroBtn1Text');
    const inputBtn2 = document.getElementById('inputHeroBtn2Text');

    const previewBadge = document.getElementById('previewHeroBadge');
    const previewTitle = document.getElementById('previewHeroTitle');
    const previewHighlight = document.getElementById('previewHeroHighlight');
    const previewDesc = document.getElementById('previewHeroDesc');
    const previewBtn1 = document.querySelector('#previewHeroBtn1 span');
    const previewBtn2 = document.querySelector('#previewHeroBtn2 span');

    if (inputBadge && previewBadge) inputBadge.addEventListener('input', e => previewBadge.textContent = e.target.value || 'Lembaga Penjaminan Mutu');
    if (inputTitle && previewTitle) inputTitle.addEventListener('input', e => previewTitle.textContent = e.target.value || 'Sistem Penjaminan Mutu Internal');
    if (inputHighlight && previewHighlight) inputHighlight.addEventListener('input', e => previewHighlight.textContent = e.target.value || 'Siklus PPEPP Berkelanjutan');
    if (inputDesc && previewDesc) inputDesc.addEventListener('input', e => previewDesc.textContent = e.target.value || 'Portal terpadu pengawasan...');
    if (inputBtn1 && previewBtn1) inputBtn1.addEventListener('input', e => previewBtn1.textContent = e.target.value || 'Jelajahi Direktori');
    if (inputBtn2 && previewBtn2) inputBtn2.addEventListener('input', e => previewBtn2.textContent = e.target.value || 'Repositori Dokumen');

    // Footer Elements
    const inputFootBrand = document.getElementById('inputFooterBrand');
    const inputFootSub = document.getElementById('inputFooterSub');
    const inputFootDesc = document.getElementById('inputFooterDesc');
    const inputFootAkreditasi = document.getElementById('inputFooterAkreditasi');
    const inputFootAddr = document.getElementById('inputFooterAddress');
    const inputFootEmail = document.getElementById('inputFooterEmail');
    const inputFootPhone = document.getElementById('inputFooterPhone');
    const inputFootWeb = document.getElementById('inputFooterWeb');

    const previewFootBrand = document.getElementById('previewFooterBrand');
    const previewFootSub = document.getElementById('previewFooterSub');
    const previewFootDesc = document.getElementById('previewFooterDesc');
    const previewFootAkreditasi = document.getElementById('previewFooterAkreditasi');
    const previewFootAddr = document.getElementById('previewFooterAddress');
    const previewFootEmail = document.getElementById('previewFooterEmail');
    const previewFootPhone = document.getElementById('previewFooterPhone');
    const previewFootWeb = document.getElementById('previewFooterWeb');

    if (inputFootBrand && previewFootBrand) inputFootBrand.addEventListener('input', e => previewFootBrand.textContent = e.target.value || 'PETRA');
    if (inputFootSub && previewFootSub) inputFootSub.addEventListener('input', e => previewFootSub.textContent = e.target.value || 'PEmantauan Tahapan PPEPP & Rencana Aksi');
    if (inputFootDesc && previewFootDesc) inputFootDesc.addEventListener('input', e => previewFootDesc.textContent = e.target.value || 'PETRA = PEmantauan Tahapan PPEPP & Rencana Aksi. PETRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.');
    if (inputFootAkreditasi && previewFootAkreditasi) inputFootAkreditasi.addEventListener('input', e => previewFootAkreditasi.textContent = e.target.value || 'Terakreditasi UNGGUL');
    if (inputFootAddr && previewFootAddr) inputFootAddr.addEventListener('input', e => previewFootAddr.textContent = e.target.value || 'Ruang Lembaga Penjaminan Mutu, Gedung Thomas Aquinas Lantai 5, Kampus Universitas Katolik Soegijapranata, Jalan Pawiyatan Luhur IV/1 Bendan Duwur, Semarang 50234');
    if (inputFootEmail && previewFootEmail) inputFootEmail.addEventListener('input', e => previewFootEmail.textContent = e.target.value || 'lpm@unika.ac.id');
    if (inputFootPhone && previewFootPhone) inputFootPhone.addEventListener('input', e => previewFootPhone.textContent = e.target.value || '024-8441555 Ext 1473');
    if (inputFootWeb && previewFootWeb) inputFootWeb.addEventListener('input', e => previewFootWeb.textContent = e.target.value || 'Website Utama SCU');

    // Tab state persistence via hash
    const hash = window.location.hash;
    if (hash) {
        const triggerEl = document.querySelector(`button[data-bs-target="${hash}"]`);
        if (triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }
    }

    const tabButtons = document.querySelectorAll('#landingSettingTabs button[data-bs-toggle="pill"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', event => {
            const target = event.target.getAttribute('data-bs-target');
            if (target) {
                history.replaceState(null, null, target);
            }
        });
    });

    // Form submission loading state
    const form = document.getElementById('formLandingSettings');
    const btnSubmit = document.getElementById('btnSubmitSettings');
    if (form && btnSubmit) {
        form.addEventListener('submit', function() {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1.5"></i> Menyimpan Pengaturan...';
        });
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
