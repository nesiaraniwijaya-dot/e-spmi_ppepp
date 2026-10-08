/**
 * Main Application JS: Search, Filter, Pagination, and PDF Preview
 * SPMI PPEPP UNIKA Soegijapranata
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Interactive Table Filtering & Pagination
    const docTable = document.getElementById('ppeppDocumentTable');
    if (docTable) {
        initDocumentTable(docTable);
    }

    // 2. Global Delete Confirmations with SweetAlert2 (if loaded) or default confirm
    document.querySelectorAll('.btn-delete-confirm').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            const itemName = this.getAttribute('data-name') || 'data ini';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Penghapusan',
                    text: `Apakah Anda yakin ingin menghapus "${itemName}"? Data akan dipindahkan ke daftar Dokumen Terhapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DC2626',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin menghapus "${itemName}"? Data akan dipindahkan ke daftar Dokumen Terhapus.`)) {
                    form.submit();
                }
            }
        });
    });

    // 2b. Global Permanent Delete Confirmations (Hapus Permanen dari Arsip)
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-force-delete-confirm');
        if (!button) return;

        e.preventDefault();
        const form = button.closest('form');
        const itemName = button.getAttribute('data-name') || 'dokumen ini';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Dokumen Permanen?',
                html: `Apakah Anda yakin ingin menghapus <b>"${itemName}"</b> secara permanen?<br><br><span class="text-danger small"><i class="fas fa-triangle-exclamation me-1"></i> Tindakan ini <b>TIDAK DAPAT DIBATALKAN</b>. Dokumen beserta seluruh berkas lampiran fisiknya akan dihapus sepenuhnya dari server.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="fas fa-trash-can me-1"></i> Ya, Hapus Permanen',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm(`PERINGATAN: Apakah Anda yakin ingin menghapus permanen "${itemName}"?\n\nTindakan ini TIDAK DAPAT DIBATALKAN dan seluruh berkas lampiran akan dihapus dari server.`)) {
                form.submit();
            }
        }
    });

    // 3. Interactive PDF.js & Cloud Link Viewer Handling (Fast Loading & Embed Preview)
    const pdfModal = document.getElementById('pdfPreviewModal');
    let currentPdfDoc = null;
    let currentScale = 1.25;
    let currentRenderLimit = 3;
    let currentRenderTaskId = 0;
    let isUserLoggedIn = typeof window.IS_USER_LOGGED_IN !== 'undefined' ? !!window.IS_USER_LOGGED_IN : false;

    // Helper: Convert Google Drive/Docs URL into embedded preview URL
    function toEmbedPreviewUrl(rawUrl) {
        if (!rawUrl) return '';
        let url = rawUrl.trim();
        const driveMatch = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
        if (driveMatch && driveMatch[1]) {
            return `https://drive.google.com/file/d/${driveMatch[1]}/preview`;
        }
        const idMatch = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
        if (idMatch && idMatch[1]) {
            return `https://drive.google.com/file/d/${idMatch[1]}/preview`;
        }
        const docsMatch = url.match(/(https:\/\/docs\.google\.com\/(?:document|spreadsheets|presentation)\/d\/[a-zA-Z0-9_-]+)/);
        if (docsMatch && docsMatch[1]) {
            return `${docsMatch[1]}/preview`;
        }
        return url;
    }

    if (pdfModal) {
        pdfModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const isLinkDoc = button.getAttribute('data-is-link') === '1';
            const pdfUrl = button.getAttribute('data-pdf-url') || '';
            const linkUrl = button.getAttribute('data-link-url') || '';
            const docTitle = button.getAttribute('data-doc-title') || 'Dokumen Mutu';
            const docNarasi = button.getAttribute('data-doc-narasi') || '';
            const docStandar = button.getAttribute('data-doc-standar') || '';
            const publicLimit = parseInt(button.getAttribute('data-public-limit') || '1', 10);
            const canDownload = parseInt(button.getAttribute('data-can-download') || '0', 10);
            const canAccess = parseInt(button.getAttribute('data-can-access') || '1', 10);

            const modalTitle = pdfModal.querySelector('.modal-title');
            const downloadContainer = document.getElementById('pdfDownloadBtnContainer');
            const accessBadge = document.getElementById('pdfAccessBadge');
            const totalInfo = document.getElementById('pdfTotalInfo');
            const loadingIndicator = document.getElementById('pdfLoadingIndicator');
            const pagesContainer = document.getElementById('pdfPagesContainer');
            const iframeWrap = document.getElementById('pdfIframeWrap');
            const iframeEl = document.getElementById('pdfModalIframe');
            const lockedBanner = document.getElementById('pdfLockedBanner');
            const toolbar = pdfModal.querySelector('.pdf-viewer-toolbar');
            const zoomDisplay = document.getElementById('pdfZoomLevelDisplay');
            const narasiWrap = document.getElementById('pdfModalNarasiWrap');
            const narasiRow = document.getElementById('pdfModalNarasiRow');
            const narasiText = document.getElementById('pdfModalNarasiText');
            const standarWrap = document.getElementById('pdfModalStandarWrap');
            const headerIcon = document.getElementById('pdfModalHeaderIcon');

            currentRenderTaskId++; // Cancel any previous rendering tasks

            // Handle stacked modal (e.g. opened from docModal)
            const openModals = document.querySelectorAll('.modal.show');
            if (openModals.length > 0) {
                pdfModal.style.zIndex = '1065';
                setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    if (backdrops.length > 1) {
                        backdrops[backdrops.length - 1].style.zIndex = '1060';
                    }
                }, 10);
            } else {
                pdfModal.style.zIndex = '';
            }

            if (modalTitle) modalTitle.textContent = docTitle;
            if (pagesContainer) pagesContainer.innerHTML = '';
            if (lockedBanner) lockedBanner.style.display = 'none';
            if (loadingIndicator) loadingIndicator.style.display = 'block';
            if (zoomDisplay) zoomDisplay.textContent = '125%';
            currentScale = 1.25;

            // Configure Standar & Narasi display
            let hasRibbon = false;
            if (standarWrap) {
                if (docStandar && docStandar.trim() !== '') {
                    standarWrap.innerHTML = `
                        <span class="small fw-semibold text-white-50 me-1" style="font-size:0.72rem;">
                            <i class="fas fa-bookmark text-info me-1"></i> Standar Mutu:
                        </span>
                        ${docStandar}
                    `;
                    standarWrap.style.display = 'flex';
                    hasRibbon = true;
                } else {
                    standarWrap.innerHTML = '';
                    standarWrap.style.display = 'none';
                }
            }

            if (narasiText) {
                if (docNarasi && docNarasi.trim() !== '') {
                    narasiText.textContent = docNarasi.trim();
                    if (narasiRow) narasiRow.style.display = 'flex';
                    hasRibbon = true;
                } else {
                    narasiText.textContent = '';
                    if (narasiRow) narasiRow.style.display = 'none';
                }
            }

            if (narasiWrap) {
                narasiWrap.style.display = hasRibbon ? 'block' : 'none';
            }

            // Case A: Cloud Link Document (Google Drive / Docs)
            if (isLinkDoc) {
                if (headerIcon) headerIcon.className = 'fab fa-google-drive fa-lg text-info';
                if (toolbar) toolbar.style.display = 'none';
                if (pagesContainer) pagesContainer.style.display = 'none';

                const curPath = window.location.pathname + window.location.search;
                const loginUrlWithReturn = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;

                // Download/External Link Button
                if (downloadContainer) {
                    if (isUserLoggedIn || canDownload === 1) {
                        downloadContainer.innerHTML = `
                            <a href="${linkUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                <i class="fas fa-arrow-up-right-from-square me-1"></i> Buka di Tab Baru
                            </a>
                        `;
                    } else {
                        downloadContainer.innerHTML = `
                            <a href="${loginUrlWithReturn}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold text-white bg-scu-blue border-0" title="Masuk untuk membuka tautan resmi">
                                <i class="fas fa-lock me-1"></i> Login untuk Akses
                            </a>
                        `;
                    }
                }

                // Check access permission
                if (isUserLoggedIn || canAccess === 1) {
                    if (accessBadge) {
                        if (isUserLoggedIn) {
                            accessBadge.className = 'badge bg-success bg-opacity-90 text-white rounded-pill px-2.5 py-1 fw-bold';
                            accessBadge.innerHTML = '<i class="fas fa-unlock me-1"></i> Akses Penuh (Tautan Cloud)';
                        } else {
                            accessBadge.className = 'badge bg-info bg-opacity-90 text-white rounded-pill px-2.5 py-1';
                            accessBadge.innerHTML = '<i class="fab fa-google-drive me-1"></i> Dokumen Cloud Publik';
                        }
                    }
                    if (totalInfo) totalInfo.textContent = 'Pratinjau Langsung Tautan Cloud';

                    if (iframeWrap && iframeEl) {
                        iframeWrap.style.display = 'block';
                        iframeEl.src = toEmbedPreviewUrl(linkUrl);
                        iframeEl.onload = function () {
                            if (loadingIndicator) loadingIndicator.style.display = 'none';
                        };
                        // Fallback safety timeout in case cross-origin iframe onload does not fire
                        setTimeout(() => {
                            if (loadingIndicator) loadingIndicator.style.display = 'none';
                        }, 1200);
                    }
                } else {
                    // Guest user attempting to access a locked link document
                    if (accessBadge) {
                        accessBadge.className = 'badge bg-secondary text-white rounded-pill px-2.5 py-1';
                        accessBadge.innerHTML = '<i class="fas fa-lock me-1"></i> Akses Terbatas';
                    }
                    if (totalInfo) totalInfo.textContent = 'Perlu Login Pengguna';
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                    if (iframeWrap) iframeWrap.style.display = 'none';
                    if (lockedBanner) {
                        lockedBanner.style.display = 'block';
                        const lockedMsg = document.getElementById('pdfLockedMessage');
                        if (lockedMsg) {
                            lockedMsg.innerHTML = `
                                Tautan dokumen mutu ini memiliki pembatasan akses untuk publik. Silakan masuk sebagai pengguna terdaftar untuk membuka isi dokumen dan melihat berkas resmi secara lengkap.
                            `;
                        }
                        const loginBtnInBanner = lockedBanner.querySelector('a.btn-primary');
                        if (loginBtnInBanner) {
                            loginBtnInBanner.href = loginUrlWithReturn;
                        }
                    }
                }
                return;
            }

            // Case B: Local PDF File
            if (headerIcon) headerIcon.className = 'fas fa-file-pdf fa-lg text-info';
            if (toolbar) toolbar.style.display = 'flex';
            if (pagesContainer) pagesContainer.style.display = 'flex';
            if (iframeWrap) {
                iframeWrap.style.display = 'none';
                if (iframeEl) iframeEl.src = 'about:blank';
            }

            // Configure Download Button for PDF
            if (downloadContainer) {
                const curPath = window.location.pathname + window.location.search;
                const loginUrlWithReturn = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;
                if (isUserLoggedIn || canDownload === 1) {
                    downloadContainer.innerHTML = `
                        <a href="${pdfUrl}" target="_blank" download class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="fas fa-download me-1"></i> Unduh Asli
                        </a>
                    `;
                } else {
                    downloadContainer.innerHTML = `
                        <a href="${loginUrlWithReturn}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold text-white bg-scu-blue border-0" title="Masuk ke sistem untuk mengunduh dokumen resmi">
                            <i class="fas fa-lock me-1"></i> Login untuk Unduh
                        </a>
                    `;
                }
            }

            // Configure Access Badge & Page Limit
            if (accessBadge) {
                if (isUserLoggedIn) {
                    accessBadge.className = 'badge bg-success bg-opacity-90 text-white rounded-pill px-2.5 py-1 fw-bold';
                    accessBadge.innerHTML = '<i class="fas fa-unlock me-1"></i> Akses Penuh (Terverifikasi)';
                } else if (publicLimit === 0) {
                    accessBadge.className = 'badge bg-success bg-opacity-75 text-white rounded-pill px-2.5 py-1';
                    accessBadge.innerHTML = '<i class="fas fa-eye me-1"></i> Semua Halaman';
                } else {
                    accessBadge.className = 'badge bg-light text-primary border rounded-pill px-2.5 py-1 fw-bold';
                    accessBadge.innerHTML = `<i class="fas fa-lock me-1"></i> Pratinjau Terbatas (${publicLimit} Hlm)`;
                }
            }

            // Check if PDF.js library is loaded
            if (typeof pdfjsLib === 'undefined') {
                if (pagesContainer) {
                    pagesContainer.innerHTML = `
                        <div class="alert alert-danger m-4">
                            Gagal memuat engine pembaca PDF. Silakan periksa koneksi internet Anda atau muat ulang halaman.
                        </div>
                    `;
                }
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                return;
            }

            // Load Document via PDF.js with instant first-page response
            pdfjsLib.getDocument(pdfUrl).promise.then(function (pdfDoc) {
                currentPdfDoc = pdfDoc;
                const totalPages = pdfDoc.numPages;

                if (!isUserLoggedIn && publicLimit > 0) {
                    currentRenderLimit = Math.min(totalPages, publicLimit);
                } else {
                    currentRenderLimit = totalPages;
                }

                if (totalInfo) {
                    totalInfo.textContent = (isUserLoggedIn || publicLimit === 0 || currentRenderLimit === totalPages)
                        ? `${totalPages} Halaman Lengkap (Akses Penuh)`
                        : `Menampilkan ${currentRenderLimit} dari total ${totalPages} Halaman`;
                }

                const pageCounter = document.getElementById('pdfPageCounterBadge');
                if (pageCounter) {
                    pageCounter.textContent = `1 - ${currentRenderLimit} / ${totalPages}`;
                }

                // Render with fast progressive streaming
                renderAllAllowablePagesFast(pdfDoc, currentRenderLimit, totalPages, currentScale, publicLimit);

            }).catch(function (error) {
                console.error('Error loading PDF:', error);
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                if (pagesContainer) {
                    pagesContainer.innerHTML = `
                        <div class="alert alert-light border m-4 text-center">
                            <i class="fas fa-circle-exclamation fa-2x mb-2 d-block text-secondary"></i>
                            <strong>Tidak dapat menampilkan pratinjau dokumen.</strong><br>
                            <span class="small text-muted">${error.message || 'Format berkas tidak didukung atau berkas tidak ditemukan.'}</span>
                        </div>
                    `;
                }
            });
        });

        pdfModal.addEventListener('hidden.bs.modal', function () {
            currentRenderTaskId++; // Cancel any running page renders
            const pagesContainer = document.getElementById('pdfPagesContainer');
            if (pagesContainer) pagesContainer.innerHTML = '';
            const iframeWrap = document.getElementById('pdfIframeWrap');
            const iframeEl = document.getElementById('pdfModalIframe');
            if (iframeEl) iframeEl.src = 'about:blank';
            if (iframeWrap) iframeWrap.style.display = 'none';
            const lockedBanner = document.getElementById('pdfLockedBanner');
            if (lockedBanner) lockedBanner.style.display = 'none';
            const narasiWrap = document.getElementById('pdfModalNarasiWrap');
            const narasiText = document.getElementById('pdfModalNarasiText');
            if (narasiWrap) narasiWrap.style.display = 'none';
            if (narasiText) narasiText.textContent = '';
            currentPdfDoc = null;
            pdfModal.style.zIndex = '';

            // If another modal is still visible (stacked modal), restore scrolling on body
            if (document.querySelectorAll('.modal.show').length > 0) {
                document.body.classList.add('modal-open');
            }
        });

        // Zoom handlers
        const zoomInBtn = document.getElementById('pdfZoomInBtn');
        const zoomOutBtn = document.getElementById('pdfZoomOutBtn');
        const zoomResetBtn = document.getElementById('pdfZoomResetBtn');
        const zoomDisplay = document.getElementById('pdfZoomLevelDisplay');

        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', function () {
                if (!currentPdfDoc || currentScale >= 2.5) return;
                currentScale += 0.25;
                if (zoomDisplay) zoomDisplay.textContent = Math.round(currentScale * 100) + '%';
                reRenderPages(currentPdfDoc, currentRenderLimit, currentScale);
            });
        }

        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', function () {
                if (!currentPdfDoc || currentScale <= 0.75) return;
                currentScale -= 0.25;
                if (zoomDisplay) zoomDisplay.textContent = Math.round(currentScale * 100) + '%';
                reRenderPages(currentPdfDoc, currentRenderLimit, currentScale);
            });
        }

        if (zoomResetBtn) {
            zoomResetBtn.addEventListener('click', function () {
                if (!currentPdfDoc) return;
                currentScale = 1.25;
                if (zoomDisplay) zoomDisplay.textContent = '125%';
                reRenderPages(currentPdfDoc, currentRenderLimit, currentScale);
            });
        }
    }

    /**
     * Ultra-fast progressive rendering: Page 1 renders and displays instantly (<400ms),
     * followed by background chunk rendering of subsequent pages.
     */
    function renderAllAllowablePagesFast(pdfDoc, limit, totalPages, scale, publicLimit) {
        const pagesContainer = document.getElementById('pdfPagesContainer');
        const loadingIndicator = document.getElementById('pdfLoadingIndicator');
        const lockedBanner = document.getElementById('pdfLockedBanner');
        const lockedMessage = document.getElementById('pdfLockedMessage');

        if (!pagesContainer) return;
        pagesContainer.innerHTML = '';

        const thisTaskId = ++currentRenderTaskId;

        function renderSinglePage(pageNum) {
            return pdfDoc.getPage(pageNum).then(function (page) {
                if (currentRenderTaskId !== thisTaskId) return;

                const viewport = page.getViewport({ scale: scale });

                const pageWrapper = document.createElement('div');
                pageWrapper.className = 'pdf-page-wrapper text-center position-relative';
                pageWrapper.style.marginBottom = '20px';

                const canvas = document.createElement('canvas');
                canvas.className = 'pdf-page-canvas rounded shadow-sm';
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const pageBadge = document.createElement('div');
                pageBadge.className = 'badge bg-dark bg-opacity-75 text-white position-absolute top-0 start-0 m-2 px-2 py-1 small';
                pageBadge.textContent = `Halaman ${pageNum}`;

                pageWrapper.appendChild(canvas);
                pageWrapper.appendChild(pageBadge);
                pagesContainer.appendChild(pageWrapper);

                return page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;
            });
        }

        // Render Page 1 first immediately
        renderSinglePage(1).then(() => {
            if (currentRenderTaskId !== thisTaskId) return;
            // IMMEDIATELY HIDE SPINNER on Page 1 finish! User can begin reading immediately!
            if (loadingIndicator) loadingIndicator.style.display = 'none';

            // Now render remaining pages in a sequential background queue
            let chain = Promise.resolve();
            for (let p = 2; p <= limit; p++) {
                const nextP = p;
                chain = chain.then(() => {
                    if (currentRenderTaskId !== thisTaskId) return Promise.resolve();
                    return renderSinglePage(nextP);
                });
            }

            return chain.then(() => {
                if (currentRenderTaskId !== thisTaskId) return;
                // Append locked banner at the end if user reached limit
                if (!isUserLoggedIn && publicLimit > 0 && totalPages > limit) {
                    if (lockedBanner) {
                        lockedBanner.style.display = 'block';
                        if (lockedMessage) {
                            lockedMessage.innerHTML = `
                                Anda baru saja membaca pratinjau terbatas <strong>${limit} dari total ${totalPages} halaman</strong> dokumen mutu ini. Untuk mengakses seluruh lembar halaman secara lengkap dan mengunduh berkas resminya, silakan masuk ke sistem MITRA sebagai pengguna terdaftar.
                            `;
                        }
                        const loginBtnInBanner = lockedBanner.querySelector('a.btn-primary');
                        if (loginBtnInBanner) {
                            const curPath = window.location.pathname + window.location.search;
                            loginBtnInBanner.href = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;
                        }
                        pagesContainer.appendChild(lockedBanner);
                    }
                } else if (lockedBanner) {
                    lockedBanner.style.display = 'none';
                }
            });
        }).catch(err => {
            console.error('Error rendering page:', err);
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        });
    }

    function reRenderPages(pdfDoc, limit, scale) {
        if (!pdfDoc) return;
        const totalPages = pdfDoc.numPages;
        renderAllAllowablePagesFast(pdfDoc, limit, totalPages, scale);
    }
});

/**
 * Initialize Client-Side Fast Filtering, Search, and Pagination
 */
function initDocumentTable(table) {
    const searchInput = document.getElementById('tableSearchInput');
    const bidangFilter = document.getElementById('tableBidangFilter');
    const perPageSelect = document.getElementById('tablePerPageSelect') || document.getElementById('tablePageSize');
    const tableInfo = document.getElementById('tableInfo');
    const paginationContainer = document.getElementById('tablePagination');
    const tbody = table.querySelector('tbody');
    const allRows = Array.from(tbody.querySelectorAll('tr.doc-row'));

    if (allRows.length === 0) return;

    let currentPage = 1;
    let perPage = perPageSelect ? (perPageSelect.value === 'all' ? 999999 : parseInt(perPageSelect.value, 10)) : 10;
    let filteredRows = [...allRows];
    let selectedCycle = '';
    let selectedReviewStatus = '';

    window.setProdiPpeppCycleFilter = function(cycle) {
        selectedCycle = cycle || '';
        applyFilters();
    };

    window.setProdiPpeppReviewStatusFilter = function(status) {
        selectedReviewStatus = status || '';
        applyFilters();
    };

    function applyFilters() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const selectedBidang = bidangFilter ? bidangFilter.value : '';

        filteredRows = allRows.filter(row => {
            const title = (row.getAttribute('data-title') || '').toLowerCase();
            const nomor = (row.getAttribute('data-nomor') || '').toLowerCase();
            const bidangIds = (row.getAttribute('data-bidang-id') || '').split(',').map(s => s.trim());
            const siklus = row.getAttribute('data-siklus') || '';
            const status = row.getAttribute('data-status-review') || '';
            const textContent = row.textContent.toLowerCase();

            const matchesSearch = !query || title.includes(query) || nomor.includes(query) || textContent.includes(query);
            const matchesBidang = !selectedBidang || bidangIds.includes(selectedBidang);
            const matchesSiklus = !selectedCycle || siklus === selectedCycle;
            const matchesStatus = !selectedReviewStatus || status === selectedReviewStatus;

            return matchesSearch && matchesBidang && matchesSiklus && matchesStatus;
        });

        currentPage = 1;
        render();
    }

    function render() {
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / perPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * perPage;
        const endIndex = Math.min(startIndex + perPage, totalRows);

        // Hide all rows first
        allRows.forEach(row => row.style.display = 'none');

        // Show slice
        for (let i = startIndex; i < endIndex; i++) {
            if (filteredRows[i]) {
                filteredRows[i].style.display = '';
            }
        }

        // Empty state
        const emptyRow = tbody.querySelector('.no-matching-docs');
        if (totalRows === 0) {
            if (!emptyRow) {
                const tr = document.createElement('tr');
                tr.className = 'no-matching-docs';
                tr.innerHTML = `<td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 text-secondary d-block"></i>
                    <h6 class="fw-bold">Tidak ada dokumen yang sesuai</h6>
                    <p class="small mb-0">Coba ubah kata kunci pencarian atau filter bidang yang dipilih.</p>
                </td>`;
                tbody.appendChild(tr);
            } else {
                emptyRow.style.display = '';
            }
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }

        // Update info text
        if (tableInfo) {
            if (totalRows === 0) {
                tableInfo.textContent = 'Menampilkan 0 dari 0 dokumen';
            } else {
                tableInfo.textContent = `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalRows} dokumen`;
            }
        }

        // Render pagination controls
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (!paginationContainer) return;
        paginationContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const ul = document.createElement('ul');
        ul.className = 'pagination pagination-sm mb-0';

        // Prev Button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><i class="fas fa-chevron-left"></i></a>`;
        prevLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                render();
            }
        });
        ul.appendChild(prevLi);

        // Page Numbers
        for (let p = 1; p <= totalPages; p++) {
            if (p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)) {
                const li = document.createElement('li');
                li.className = `page-item ${p === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#">${p}</a>`;
                li.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage = p;
                    render();
                });
                ul.appendChild(li);
            } else if (p === currentPage - 2 || p === currentPage + 2) {
                const li = document.createElement('li');
                li.className = 'page-item disabled';
                li.innerHTML = `<span class="page-link">...</span>`;
                ul.appendChild(li);
            }
        }

        // Next Button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next"><i class="fas fa-chevron-right"></i></a>`;
        nextLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                render();
            }
        });
        ul.appendChild(nextLi);

        paginationContainer.appendChild(ul);
    }

    // Attach listeners
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
    if (bidangFilter) {
        bidangFilter.addEventListener('change', applyFilters);
    }
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function () {
            perPage = (this.value === 'all') ? 999999 : parseInt(this.value, 10);
            currentPage = 1;
            render();
        });
    }

    // Initial render
    applyFilters();
}
