<?php
/**
 * Public Layout Footer
 * SPMI PPEPP UNIKA Soegijapranata
 * Design: LPM Design System (Navy + Purple Theme)
 */
?>

<?php if (!empty($isAdminLayout)): ?>
    </div><!-- /.admin-main -->
</div><!-- /.admin-content -->
<?php endif; ?>

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
                        <div class="p-1.5 rounded-3 bg-white bg-opacity-10 text-info flex-shrink-0" id="pdfModalHeaderIconWrap">
                            <i class="fas fa-file-pdf fa-lg" id="pdfModalHeaderIcon"></i>
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
                    <div id="pdfLoadingIndicator" class="position-absolute top-50 start-50 translate-middle text-center text-white py-5" style="z-index: 10;">
                        <div class="spinner-border text-info mb-3" role="status" style="width:3rem; height:3rem;"></div>
                        <div class="fw-bold fs-6">Memuat Dokumen Mutu...</div>
                        <div class="text-white-50 small mt-1">Menyiapkan dokumen untuk Anda</div>
                    </div>

                    <!-- Canvases Container (Local PDF Documents) -->
                    <div id="pdfPagesContainer" class="d-flex flex-column align-items-center py-4 gap-4" style="min-height:100%;">
                        <!-- Pages will be rendered here dynamically -->
                    </div>

                    <!-- Iframe Container (Google Drive / Cloud Link Documents) -->
                    <div id="pdfIframeWrap" class="w-100 h-100" style="display:none; min-height:70vh;">
                        <iframe id="pdfModalIframe" src="" class="w-100 h-100 border-0" allow="autoplay" allowfullscreen style="min-height:70vh;"></iframe>
                    </div>

                    <!-- Locked Preview Banner (Appears if access is restricted) -->
                    <div id="pdfLockedBanner" class="p-4 mx-auto my-4 text-center rounded-4 shadow-lg" style="display:none; max-width:680px; background:linear-gradient(135deg, #0A192F 0%, #1E3E62 50%, #1E293B 100%); border:1px solid rgba(255,255,255,0.15);">
                        <div class="lock-icon-circle mx-auto mb-3 d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 text-info rounded-circle" style="width:68px; height:68px;">
                            <i class="fas fa-shield-halved fa-2x"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-2" style="font-family:var(--font-heading);">Akses Dokumen Terbatas</h4>
                        <p class="text-white text-opacity-75 mx-auto mb-3" style="max-width: 520px; font-size: 0.9rem; line-height:1.6;" id="pdfLockedMessage">
                            Dokumen mutu ini memiliki pembatasan akses untuk publik. Silakan masuk sebagai pengguna terdaftar untuk membuka isi dokumen dan mengakses tautan resmi secara penuh.
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                            <a href="<?= base_url('login') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-white shadow-sm bg-scu-blue border-0">
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

    <!-- ============================================================
         GLOBAL MODAL: Riwayat Catatan Revisi & Evaluasi Dokumen
    ============================================================ -->
    <div class="modal fade" id="reviewHistoryModal" tabindex="-1" aria-labelledby="reviewHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white px-4 py-3 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);">
                    <div class="d-flex align-items-center gap-2.5 overflow-hidden me-2">
                        <div class="p-2 rounded-3 bg-white bg-opacity-15 text-white flex-shrink-0">
                            <i class="fas fa-clock-rotate-left fa-lg"></i>
                        </div>
                        <div class="text-truncate">
                            <h5 class="modal-title fs-6 fw-bold mb-0 text-white text-truncate" id="reviewHistoryModalLabel">
                                Riwayat Catatan Revisi &amp; Verifikasi Dokumen
                            </h5>
                            <div class="d-flex align-items-center gap-2 mt-0.5 small text-white-50 text-truncate">
                                <span id="revHistDocName" class="text-truncate fw-medium text-white"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- Status Header Card -->
                    <div class="card border rounded-3 p-3 mb-3 bg-white shadow-2xs d-flex flex-row align-items-center justify-content-between gap-3">
                        <div>
                            <div class="text-muted small mb-0.5" style="font-size: 0.72rem;">STATUS DOKUMEN SAAT INI:</div>
                            <div id="revHistCurrentBadge"></div>
                        </div>
                        <div class="text-end text-muted small" id="revHistTotalCount" style="font-size: 0.75rem;">
                        </div>
                    </div>

                    <!-- Timeline Content -->
                    <div id="revHistLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat riwayat...</span>
                        </div>
                        <div class="text-muted small mt-2">Mengambil riwayat catatan revisi...</div>
                    </div>
                    <div id="revHistEmpty" class="text-center py-5 d-none">
                        <div class="text-muted opacity-50 mb-2"><i class="fas fa-comments fa-3x"></i></div>
                        <h6 class="fw-bold text-secondary mb-1">Belum Ada Riwayat Catatan</h6>
                        <p class="text-muted small mb-0">Dokumen ini belum memiliki riwayat catatan revisi atau telaah sebelumnya.</p>
                    </div>
                    <div id="revHistTimeline" class="review-history-timeline d-none">
                        <!-- Items injected dynamically via JS -->
                    </div>
                </div>
                <div class="modal-footer bg-white border-top px-4 py-2.5">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

<?php if (empty($isAdminLayout)): ?>
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
                            <div class="footer-brand-title"><?= htmlspecialchars(get_landing_setting('footer_brand_title', 'MITRA')) ?></div>
                            <div class="footer-brand-sub"><?= htmlspecialchars(get_landing_setting('footer_brand_sub', 'Monitoring dan Implementasi Tahapan PPEPP & Rencana Aksi')) ?></div>
                        </div>
                    </div>
                    <p class="footer-desc">
                        <?= htmlspecialchars(get_landing_setting('footer_desc', 'MITRA = Monitoring dan Implementasi Tahapan PPEPP & Rencana Aksi. MITRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.')) ?>
                    </p>

                    <!-- Real Data Structured Address Box -->
                    <?php
                    $rawAddress = get_landing_setting('footer_address', "Ruang Lembaga Penjaminan Mutu, Gedung Thomas Aquinas Lantai 5, Kampus Universitas Katolik Soegijapranata, Jalan Pawiyatan Luhur IV/1 Bendan Duwur, Semarang 50234");
                    $addressLines = preg_split('/\r\n|\r|\n/', trim($rawAddress));
                    if (count($addressLines) === 1 && strpos($rawAddress, ',') !== false) {
                        $parts = array_map('trim', explode(',', $rawAddress));
                        if (count($parts) >= 3) {
                            $addressLines = $parts;
                        }
                    }
                    ?>
                    <div class="footer-address-box">
                        <div class="footer-address-pin">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div class="footer-address-details">
                            <?php if (count($addressLines) > 1): ?>
                                <?php foreach ($addressLines as $idx => $line): ?>
                                    <?php if ($idx === 0): ?>
                                        <div class="footer-room-name"><?= htmlspecialchars($line) ?></div>
                                    <?php elseif ($idx === 1): ?>
                                        <div class="footer-building-name"><?= htmlspecialchars($line) ?></div>
                                    <?php elseif ($idx === 2): ?>
                                        <div class="footer-campus-name"><?= htmlspecialchars($line) ?></div>
                                    <?php else: ?>
                                        <div class="footer-street-name"><?= htmlspecialchars($line) ?></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="footer-street-name" style="color:#CBD5E1; line-height:1.55;"><?= nl2br(htmlspecialchars($rawAddress)) ?></div>
                            <?php endif; ?>
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
                    <h5><?= htmlspecialchars(get_landing_setting('footer_siklus_heading', '5 Siklus PPEPP MITRA')) ?></h5>
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
                        <?php 
                        $scuUrl = get_landing_setting('footer_website_url', 'https://www.unika.ac.id');
                        $scuText = get_landing_setting('footer_website_text', 'Website Utama SCU');
                        if (!empty($scuUrl)):
                            $hrefScu = (strpos($scuUrl, 'http://') === 0 || strpos($scuUrl, 'https://') === 0) ? $scuUrl : 'https://' . $scuUrl;
                        ?>
                        <a href="<?= htmlspecialchars($hrefScu) ?>" target="_blank" rel="noopener noreferrer" class="footer-nav-link">
                            <span class="footer-nav-icon"><i class="fas fa-arrow-up-right-from-square"></i></span>
                            <span><?= htmlspecialchars($scuText) ?></span>
                        </a>
                        <?php endif; ?>

                        <?php 
                        $lpmUrl = get_landing_setting('footer_website_lpm_url', 'https://lpm.unika.ac.id');
                        $lpmText = get_landing_setting('footer_website_lpm_text', 'Website Resmi LPM');
                        if (!empty($lpmUrl)): 
                            $hrefLpm = (strpos($lpmUrl, 'http://') === 0 || strpos($lpmUrl, 'https://') === 0) ? $lpmUrl : 'https://' . $lpmUrl;
                        ?>
                        <a href="<?= htmlspecialchars($hrefLpm) ?>" target="_blank" rel="noopener noreferrer" class="footer-nav-link">
                            <span class="footer-nav-icon"><i class="fas fa-shield-halved"></i></span>
                            <span><?= htmlspecialchars($lpmText) ?></span>
                        </a>
                        <?php endif; ?>

                        <?php 
                        $customUrl = get_landing_setting('footer_website_custom_url', '');
                        $customText = get_landing_setting('footer_website_custom_text', '');
                        if (!empty($customUrl) && !empty($customText)): 
                            $hrefCustom = (strpos($customUrl, 'http://') === 0 || strpos($customUrl, 'https://') === 0) ? $customUrl : 'https://' . $customUrl;
                        ?>
                        <a href="<?= htmlspecialchars($hrefCustom) ?>" target="_blank" rel="noopener noreferrer" class="footer-nav-link">
                            <span class="footer-nav-icon"><i class="fas fa-link"></i></span>
                            <span><?= htmlspecialchars($customText) ?></span>
                        </a>
                        <?php endif; ?>

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
                            <span><?= htmlspecialchars(get_landing_setting('footer_contact_title', 'Layanan Bantuan LPM')) ?></span>
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
                    &copy; <?= date('Y') ?> <strong><?= htmlspecialchars(get_landing_setting('footer_copyright_text', INSTITUTION_NAME)) ?></strong>. Seluruh Hak Cipta Dilindungi.
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

    <!-- Global Modal Auto-Hoist & Review History Handler -->
    <script>
    // Pastikan setiap modal selalu menjadi anak langsung dari <body> agar tidak terperangkap z-index backdrop
    document.addEventListener('show.bs.modal', function(e) {
        if (e.target && e.target.classList.contains('modal') && e.target.parentElement !== document.body) {
            document.body.appendChild(e.target);
        }
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-review-history');
        if (!btn) return;
        e.preventDefault();
        const docId = btn.getAttribute('data-doc-id');
        const docName = btn.getAttribute('data-doc-name') || '';
        if (!docId) return;

        const modalEl = document.getElementById('reviewHistoryModal');
        if (!modalEl) return;

        // Jika tombol diklik dari dalam modal lain (misal modal review / modal rincian), tutup modal sebelumnya
        const currentOpenModal = btn.closest('.modal');
        if (currentOpenModal && currentOpenModal.id !== 'reviewHistoryModal') {
            const openInst = bootstrap.Modal.getInstance(currentOpenModal);
            if (openInst) openInst.hide();
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        
        document.getElementById('revHistDocName').textContent = docName;
        document.getElementById('revHistLoading').classList.remove('d-none');
        document.getElementById('revHistEmpty').classList.add('d-none');
        document.getElementById('revHistTimeline').classList.add('d-none');
        document.getElementById('revHistTimeline').innerHTML = '';
        document.getElementById('revHistCurrentBadge').innerHTML = '';
        document.getElementById('revHistTotalCount').textContent = '';

        modal.show();

        fetch('<?= base_url("api/document-review-history/") ?>' + docId)
            .then(res => res.json())
            .then(data => {
                document.getElementById('revHistLoading').classList.add('d-none');
                if (!data || !data.success) {
                    document.getElementById('revHistEmpty').classList.remove('d-none');
                    return;
                }

                if (data.document) {
                    document.getElementById('revHistDocName').textContent = data.document.nama_dokumen || docName;
                    document.getElementById('revHistCurrentBadge').innerHTML = data.document.status_badge || '';
                }

                const items = data.history || [];
                document.getElementById('revHistTotalCount').textContent = items.length + ' Catatan / Evaluasi';

                if (items.length === 0) {
                    document.getElementById('revHistEmpty').classList.remove('d-none');
                    return;
                }

                let html = '<div class="d-flex flex-column gap-3">';
                items.forEach((item, idx) => {
                    const isGpm = (item.reviewer_role === 'gpm' || item.tahap === 'gpm');
                    const isLpm = (item.reviewer_role === 'lpm' || item.reviewer_role === 'super_admin' || item.reviewer_role === 'kepala_lpm');
                    const isResubmit = (item.tahap === 'resubmission');

                    const roleBadge = isResubmit
                        ? '<span class="badge rounded-pill px-2.5 py-1" style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD;"><i class="fas fa-rotate-left me-1"></i> Perbaikan Unit</span>'
                        : (isGpm 
                            ? '<span class="badge rounded-pill px-2.5 py-1" style="background:#F3E8FF; color:#7E22CE; border:1px solid #E9D5FF;"><i class="fas fa-shield-halved me-1"></i> GPM Fakultas</span>'
                            : (isLpm 
                                ? '<span class="badge rounded-pill px-2.5 py-1" style="background:#DBEAFE; color:#1E40AF; border:1px solid #BFDBFE;"><i class="fas fa-award me-1"></i> Lembaga Penjaminan Mutu (LPM)</span>'
                                : '<span class="badge rounded-pill px-2.5 py-1 bg-secondary text-white"><i class="fas fa-cloud-arrow-up me-1"></i> Pengunggah</span>'));

                    let statusBadge = '';
                    if (item.status_hasil === 'revisi_gpm') {
                        statusBadge = '<span class="badge bg-danger rounded-pill px-2.5 py-1 text-white"><i class="fas fa-wrench me-1"></i> Minta Perbaikan (GPM)</span>';
                    } else if (item.status_hasil === 'menunggu_review_lpm') {
                        statusBadge = '<span class="badge bg-primary rounded-pill px-2.5 py-1 text-white"><i class="fas fa-paper-plane me-1"></i> Diteruskan ke LPM</span>';
                    } else if (item.status_hasil === 'revisi_lpm' || item.status_hasil === 'perlu_perbaikan') {
                        statusBadge = '<span class="badge bg-danger rounded-pill px-2.5 py-1 text-white"><i class="fas fa-triangle-exclamation me-1"></i> Minta Perbaikan (LPM)</span>';
                    } else if (item.status_hasil === 'sudah_diperbaiki') {
                        statusBadge = '<span class="badge rounded-pill px-2.5 py-1 text-white" style="background:#0284C7;"><i class="fas fa-paper-plane me-1"></i> Diajukan ke GPM</span>';
                    } else if (item.status_hasil === 'sesuai') {
                        statusBadge = '<span class="badge bg-success rounded-pill px-2.5 py-1 text-white"><i class="fas fa-circle-check me-1"></i> Disetujui LPM</span>';
                    } else {
                        statusBadge = '<span class="badge bg-secondary rounded-pill px-2.5 py-1 text-white">' + (item.status_hasil || '') + '</span>';
                    }

                    html += `
                        <div class="card border rounded-3 p-3.5 bg-white shadow-2xs position-relative">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    ${roleBadge}
                                    <span class="fw-bold text-dark small">${item.reviewer_name || 'Verifikator'}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    ${statusBadge}
                                    <span class="text-muted small" style="font-size:0.75rem;"><i class="far fa-calendar-alt me-1"></i>${item.created_at_formatted || (item.created_at ? item.created_at + ' WIB' : '')}</span>
                                </div>
                            </div>
                            <div class="p-2.5 rounded-3 bg-light border text-dark small" style="line-height:1.6; white-space:pre-wrap; font-size:0.83rem;">${item.catatan || '(Tidak ada catatan tertulis)'}</div>
                            ${(item.catatan_perbaikan || item.revised_at) ? `
                                <div class="mt-2.5 pt-2.5 border-top border-dashed">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1" style="font-size:0.7rem;">
                                            <i class="fas fa-file-arrow-up me-1"></i> Perbaikan Telah Diunggah
                                        </span>
                                        <span class="text-muted small" style="font-size:0.72rem;">
                                            <i class="far fa-clock me-1"></i> ${item.revised_at ? (item.revised_at.includes('WIB') ? item.revised_at : item.revised_at + ' WIB') : ''}
                                        </span>
                                    </div>
                                    ${item.uploader_name ? `<div class="small text-muted mb-1" style="font-size:0.75rem;">Diunggah oleh: <strong class="text-dark">${item.uploader_name}</strong></div>` : ''}
                                    ${item.catatan_perbaikan ? `
                                        <div class="small text-secondary p-2 bg-light rounded-2 border" style="font-size:0.8rem; line-height:1.5;">
                                            <strong>Tanggapan / Catatan Unit:</strong> ${item.catatan_perbaikan}
                                        </div>
                                    ` : ''}
                                </div>
                            ` : ''}
                        </div>
                    `;
                });
                html += '</div>';

                document.getElementById('revHistTimeline').innerHTML = html;
                document.getElementById('revHistTimeline').classList.remove('d-none');
            })
            .catch(err => {
                document.getElementById('revHistLoading').classList.add('d-none');
                document.getElementById('revHistEmpty').classList.remove('d-none');
            });
    });
    </script>

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

    <!-- Global Helper: Auto-Pair Format Tahun Akademik YYYY/YYYY (misal ketik 2025 langsung jadi 2025/2026) -->
    <script>
    window.autoPairTahunAkademik = function(input, event) {
        if (!input) return;

        // Cegah auto-pair jika user sedang menekan tombol Backspace atau Delete saat mengetik
        if (event && (
            event.inputType === 'deleteContentBackward' || 
            event.inputType === 'deleteContentForward' || 
            event.key === 'Backspace' || 
            event.key === 'Delete'
        )) {
            return;
        }

        let val = (input.value || '').trim();
        if (!val) return;

        let formatted = null;

        // 1. Ketik 4 digit tahun awal persis (contoh: "2025", "2021", "2026")
        if (/^\d{4}$/.test(val)) {
            const y1 = parseInt(val, 10);
            if (y1 >= 1990 && y1 <= 2099) {
                formatted = y1 + '/' + (y1 + 1);
            }
        }
        // 2. Ketik 4 digit diikuti slash atau dash (contoh: "2025/", "2025-")
        else if (/^(\d{4})[\/-]$/.test(val)) {
            const y1 = parseInt(val.substring(0, 4), 10);
            if (y1 >= 1990 && y1 <= 2099) {
                formatted = y1 + '/' + (y1 + 1);
            }
        }
        // 3. Format 2025-2026 -> 2025/2026
        else if (/^(\d{4})-(\d{4})$/.test(val)) {
            const m = val.match(/^(\d{4})-(\d{4})$/);
            formatted = m[1] + '/' + m[2];
        }
        // 4. Format 2025/26 atau 2025-26 -> 2025/2026
        else if (/^(\d{4})[\/-](\d{2})$/.test(val)) {
            const m = val.match(/^(\d{4})[\/-](\d{2})$/);
            const century = m[1].substring(0, 2);
            formatted = m[1] + '/' + century + m[2];
        }
        // 5. Khusus saat blur / focusout: jika masih berupa 4 digit atau format parsial
        else if (event && (event.type === 'blur' || event.type === 'focusout' || event.type === 'change')) {
            const match4 = val.match(/^(\d{4})/);
            if (match4) {
                const y1 = parseInt(match4[1], 10);
                if (y1 >= 1990 && y1 <= 2099) {
                    formatted = y1 + '/' + (y1 + 1);
                }
            }
        }

        if (formatted && formatted !== input.value) {
            input.value = formatted;
            // Letakkan kursor di akhir
            try {
                if (typeof input.setSelectionRange === 'function') {
                    input.setSelectionRange(input.value.length, input.value.length);
                }
            } catch (err) {}
            // Trigger change event agar live preview / validasi sinkron
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };

    (function() {
        function isTahunAkademikInput(el) {
            if (!el || el.tagName !== 'INPUT') return false;
            return el.classList.contains('input-tahun-akademik') ||
                   el.id === 'tahun_akademik' ||
                   (el.name && el.name.includes('tahun_akademik'));
        }

        // Global Event Delegation di seluruh document
        ['input', 'keyup', 'focusout', 'change', 'paste'].forEach(function(evtName) {
            document.addEventListener(evtName, function(e) {
                const el = e.target;
                if (!isTahunAkademikInput(el)) return;
                window.autoPairTahunAkademik(el, e);
            });
        });
    })();
    </script>

</body>
</html>
