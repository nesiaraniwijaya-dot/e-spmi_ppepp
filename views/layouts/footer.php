<?php
/**
 * Public Layout Footer
 * SPMI PPEPP UNIKA Soegijapranata
 * Design: LPM Design System (Navy + Purple Theme)
 */
?>

    <!-- ============================================================
         GLOBAL MODAL: PDF Viewer Interaktif (Batas Halaman Publik & Proteksi)
    ============================================================ -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1"
         aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="height:92vh;">
            <div class="modal-content h-100 border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                
                <!-- Modal Header -->
                <div class="modal-header bg-navy text-white px-3 py-2.5 d-flex align-items-center justify-content-between border-0">
                    <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                        <div class="p-1.5 rounded-3 bg-white bg-opacity-10 text-warning flex-shrink-0">
                            <i class="fas fa-file-pdf fa-lg"></i>
                        </div>
                        <div class="text-truncate">
                            <h5 class="modal-title fs-6 fw-bold mb-0 text-white text-truncate" id="pdfPreviewModalLabel">
                                Pratinjau Dokumen Mutu
                            </h5>
                            <div class="d-flex align-items-center gap-2 mt-0.5">
                                <span id="pdfAccessBadge" class="badge rounded-pill fw-medium" style="font-size:0.68rem;"></span>
                                <span id="pdfTotalInfo" class="text-white-50 small" style="font-size:0.7rem;"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <div id="pdfDownloadBtnContainer">
                            <!-- Injected dynamically via JS -->
                        </div>
                        <button type="button" class="btn-close btn-close-white"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <!-- PDF Viewer Toolbar (Zoom & Navigation) -->
                <div class="pdf-viewer-toolbar bg-dark text-white px-3 py-2 d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-light py-1 px-2.5 rounded-pill" id="pdfZoomOutBtn" title="Perkecil (-)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span id="pdfZoomLevelDisplay" class="small fw-semibold text-white-50" style="min-width:48px; text-align:center;">100%</span>
                        <button type="button" class="btn btn-sm btn-outline-light py-1 px-2.5 rounded-pill" id="pdfZoomInBtn" title="Perbesar (+)">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 rounded-pill d-none d-sm-inline-block" id="pdfZoomResetBtn" title="Ukuran Normal">
                            Reset
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-2 small text-white-50">
                        <span>Menampilkan:</span>
                        <span class="badge bg-secondary text-white fw-bold px-2 py-1" id="pdfPageCounterBadge">1 / 1</span>
                    </div>
                </div>

                <!-- Standar & Narasi Berkas Ribbon (Responsive & Scrollable) -->
                <div id="pdfModalNarasiWrap" class="px-3 py-2 text-white border-bottom border-secondary border-opacity-25" style="display:none; background:#1e293b;">
                    <div class="d-flex flex-column gap-1.5">
                        <div id="pdfModalStandarWrap" class="d-flex align-items-center gap-1.5 flex-wrap" style="display:none;"></div>
                        <div id="pdfModalNarasiRow" class="d-flex align-items-start gap-2" style="display:none;">
                            <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 mt-0.5 px-2 py-1 flex-shrink-0" style="font-size:0.72rem;">
                                <i class="fas fa-align-left me-1"></i> Narasi
                            </span>
                            <div id="pdfModalNarasiText" class="small" style="white-space:pre-line; word-break:break-word; max-height:85px; overflow-y:auto; line-height:1.55; color:#cbd5e1;"></div>
                        </div>
                    </div>
                </div>

                <!-- PDF Viewer Canvas Container -->
                <div class="modal-body p-0 position-relative" id="pdfViewerScrollArea" style="background:#525659; overflow-y:auto; height:calc(100% - 110px);">
                    <!-- Loading Spinner -->
                    <div id="pdfLoadingIndicator" class="position-absolute top-50 start-50 translate-middle text-center text-white py-5">
                        <div class="spinner-border text-warning mb-3" role="status" style="width:3rem; height:3rem;"></div>
                        <div class="fw-bold fs-6">Memuat Dokumen Mutu...</div>
                        <div class="text-white-50 small mt-1">Menyiapkan halaman dokumen untuk Anda</div>
                    </div>

                    <!-- Canvases Container -->
                    <div id="pdfPagesContainer" class="d-flex flex-column align-items-center py-4 gap-4" style="min-height:100%;">
                        <!-- Pages will be rendered here dynamically -->
                    </div>

                    <!-- Locked Preview Banner (Appears after max public pages if guest) -->
                    <div id="pdfLockedBanner" class="p-4 mx-auto my-4 text-center rounded-4 shadow-lg" style="display:none; max-width:680px; background:linear-gradient(135deg, #0A192F 0%, #1E3E62 50%, #6B21A8 100%); border:2px solid rgba(245,158,11,0.4);">
                        <div class="lock-icon-circle mx-auto mb-3 d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-20 text-warning rounded-circle" style="width:68px; height:68px;">
                            <i class="fas fa-shield-halved fa-2x"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-2" style="font-family:var(--font-heading);">Batas Pratinjau Publik Tercapai</h4>
                        <p class="text-white text-opacity-75 mx-auto mb-3" style="max-width: 520px; font-size: 0.9rem; line-height:1.6;" id="pdfLockedMessage">
                            Anda baru saja membaca pratinjau terbatas halaman awal dokumen mutu ini. Untuk mengakses seluruh lembar halaman secara lengkap dan mengunduh berkas resminya, silakan masuk ke sistem PETRA.
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                            <a href="<?= base_url('login') ?>" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                                <i class="fas fa-right-to-bracket me-1.5"></i> Masuk / Login ke Sistem
                            </a>
                            <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold" data-bs-dismiss="modal">
                                Tutup Pratinjau
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php if (!empty($isAdminLayout)): ?>
    </div><!-- /.admin-main -->
</div><!-- /.admin-content -->
<?php else: ?>
    <!-- ============================================================
         INSTITUTIONAL FOOTER (Public Pages Only) - SMOOTH & CLEAN
    ============================================================ -->
    <footer id="main-footer">
        <div class="container">
            <div class="row g-4 mb-2">

                <!-- Column 1: Institution Info & Structured Location -->
                <div class="col-lg-5 col-md-12">
                    <div class="footer-brand d-flex align-items-center gap-3 mb-3">
                        <div class="footer-logo-wrap">
                            <img src="<?= base_url('assets/images/logo-unika.png') ?>" alt="Logo UNIKA Soegijapranata" style="width:100%;height:100%;object-fit:contain;">
                        </div>
                        <div>
                            <div class="footer-brand-title"><?= htmlspecialchars(get_landing_setting('footer_brand_title', 'PETRA')) ?></div>
                            <div class="footer-brand-sub"><?= htmlspecialchars(get_landing_setting('footer_brand_sub', 'PEmantauan Tahapan PPEPP & Rencana Aksi')) ?></div>
                        </div>
                    </div>
                    <p class="footer-desc">
                        <?= htmlspecialchars(get_landing_setting('footer_desc', 'PETRA = PEmantauan Tahapan PPEPP & Rencana Aksi. PETRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.')) ?>
                    </p>

                    <!-- Real Data Structured Address Box -->
                    <?php
                    $rawAddress = get_landing_setting('footer_address', 'Ruang Lembaga Penjaminan Mutu, Gedung Thomas Aquinas Lantai 5, Kampus Universitas Katolik Soegijapranata, Jalan Pawiyatan Luhur IV/1 Bendan Duwur, Semarang 50234');
                    ?>
                    <div class="footer-address-box">
                        <div class="footer-address-pin">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div class="footer-address-details">
                            <div class="footer-room-name">Ruang Lembaga Penjaminan Mutu (LPM)</div>
                            <div class="footer-building-name">Gedung Thomas Aquinas Lantai 5</div>
                            <div class="footer-campus-name">Kampus Universitas Katolik Soegijapranata</div>
                            <div class="footer-street-name">Jl. Pawiyatan Luhur IV/1, Bendan Duwur, Semarang 50234</div>
                        </div>
                    </div>

                    <div>
                        <div class="footer-akreditasi-pill">
                            <i class="fas fa-award"></i> <?= htmlspecialchars(get_landing_setting('footer_akreditasi', 'Terakreditasi UNGGUL • BAN-PT')) ?>
                        </div>
                    </div>
                </div>

                <!-- Column 2: 5 Siklus PPEPP Pathway (Sleek Luminous Cards) -->
                <div class="col-lg-4 col-md-6">
                    <h5>5 Siklus PPEPP PETRA</h5>
                    <ul class="footer-cycle-list">
                        <li>
                            <a href="<?= base_url('dokumen?siklus=penetapan') ?>" class="footer-cycle-item cycle-p1">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="footer-cycle-badge badge-p1">P1</span>
                                    <div>
                                        <strong class="footer-cycle-title"><?= htmlspecialchars(get_landing_setting('siklus_p1_title', 'Penetapan')) ?></strong>
                                        <span class="footer-cycle-subtitle">Standar, manual mutu &amp; kebijakan</span>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right footer-cycle-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('dokumen?siklus=pelaksanaan') ?>" class="footer-cycle-item cycle-p2">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="footer-cycle-badge badge-p2">P2</span>
                                    <div>
                                        <strong class="footer-cycle-title"><?= htmlspecialchars(get_landing_setting('siklus_p2_title', 'Pelaksanaan')) ?></strong>
                                        <span class="footer-cycle-subtitle">Realisasi tridharma &amp; RPS</span>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right footer-cycle-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('dokumen?siklus=evaluasi') ?>" class="footer-cycle-item cycle-e">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="footer-cycle-badge badge-e">E</span>
                                    <div>
                                        <strong class="footer-cycle-title"><?= htmlspecialchars(get_landing_setting('siklus_e_title', 'Evaluasi')) ?></strong>
                                        <span class="footer-cycle-subtitle">Audit Mutu Internal (AMI) &amp; monev</span>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right footer-cycle-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('dokumen?siklus=pengendalian') ?>" class="footer-cycle-item cycle-p3">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="footer-cycle-badge badge-p3">P3</span>
                                    <div>
                                        <strong class="footer-cycle-title"><?= htmlspecialchars(get_landing_setting('siklus_p3_title', 'Pengendalian')) ?></strong>
                                        <span class="footer-cycle-subtitle">Tindakan koreksi &amp; RTM</span>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right footer-cycle-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('dokumen?siklus=peningkatan') ?>" class="footer-cycle-item cycle-p4">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="footer-cycle-badge badge-p4">P4</span>
                                    <div>
                                        <strong class="footer-cycle-title"><?= htmlspecialchars(get_landing_setting('siklus_p4_title', 'Peningkatan')) ?></strong>
                                        <span class="footer-cycle-subtitle">Kaizen mutu melampaui SN-Dikti</span>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right footer-cycle-arrow"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Quick Links & Frosted Contact Box -->
                <div class="col-lg-3 col-md-6">
                    <h5>Tautan &amp; Akses</h5>
                    <div class="footer-quick-links mb-2">
                        <a href="<?= htmlspecialchars(get_landing_setting('footer_website_url', 'https://www.unika.ac.id')) ?>" target="_blank" rel="noopener noreferrer" class="footer-nav-link">
                            <span class="footer-nav-icon"><i class="fas fa-arrow-up-right-from-square"></i></span>
                            <span><?= htmlspecialchars(get_landing_setting('footer_website_text', 'Website Utama SCU')) ?></span>
                        </a>
                        <a href="<?= base_url('dokumen') ?>" class="footer-nav-link">
                            <span class="footer-nav-icon"><i class="fas fa-folder-open"></i></span>
                            <span>Repositori Dokumen Mutu</span>
                        </a>
                        <a href="<?= base_url('login') ?>" class="footer-nav-link">
                            <span class="footer-nav-icon"><i class="fas fa-right-to-bracket"></i></span>
                            <span>Portal Login Akun</span>
                        </a>
                    </div>

                    <!-- Contact Box - Frosted Glass & Real LPM Contact Data -->
                    <div class="footer-contact-card">
                        <div class="footer-contact-title">
                            <span class="pulse-amber-dot"></span>
                            <i class="fas fa-headset text-warning"></i>
                            <span>Layanan Bantuan LPM</span>
                        </div>
                        <div class="footer-contact-row mb-2.5">
                            <div class="footer-contact-icon email">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="footer-contact-info">
                                <span class="footer-contact-lbl">Email Resmi Layanan:</span>
                                <a href="mailto:<?= htmlspecialchars(get_landing_setting('footer_email', 'lpm@unika.ac.id')) ?>" class="footer-contact-val email-link">
                                    <?= htmlspecialchars(get_landing_setting('footer_email', 'lpm@unika.ac.id')) ?>
                                </a>
                            </div>
                        </div>
                        <div class="footer-contact-row">
                            <div class="footer-contact-icon phone">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="footer-contact-info">
                                <span class="footer-contact-lbl">Telepon Kantor / Hotline:</span>
                                <span class="footer-contact-val">
                                    <?= htmlspecialchars(get_landing_setting('footer_phone', '024-8441555 Ext 1473')) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="container">
            <div class="footer-bottom">
                <div class="footer-copyright">
                    &copy; <?= date('Y') ?> <strong><?= INSTITUTION_NAME ?></strong>. Seluruh Hak Cipta Dilindungi.
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="footer-copyright d-none d-sm-inline">
                        LPM SCU &bull; v<?= APP_VERSION ?>
                    </span>
                    <a href="#" class="footer-back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" title="Kembali ke atas">
                        <i class="fas fa-arrow-up"></i> Ke Atas
                    </a>
                </div>
            </div>
        </div>
    </footer>
    <!-- /Footer -->
<?php endif; ?>

    <!-- ============================================================
         JavaScript Libraries
    ============================================================ -->
    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Mozilla PDF.js CDN Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
    </script>
    <!-- Core Application JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/ppepp_form.js') ?>"></script>
    <script src="<?= asset('js/sub_standar_picker.js') ?>"></script>

    <!-- Navbar scroll effect -->
    <script>
    (function() {
        const navbar = document.getElementById('main-navbar');
        if (navbar) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 30) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            });
        }
    })();
    </script>

</body>
</html>
