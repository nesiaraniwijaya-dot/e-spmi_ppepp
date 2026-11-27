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

    // 2. Global Delete Confirmations with SweetAlert2 (if loaded) or default confirm (Event Delegation)
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-delete-confirm');
        if (!button) return;

        e.preventDefault();
        const form = button.closest('form');
        if (!form) return;
        const itemName = button.getAttribute('data-name') || 'data ini';

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
        window._currentPdfIsReviewMode = false;

        function initCopiedReviewForm(container, role, triggerBtn) {
            if (!container) return;
            const form = container.querySelector('form');

            // Temukan tombol Simpan Keputusan Review
            const submitBtn = container.querySelector('button[data-action="submit-review"]')
                           || container.querySelector('button[type="submit"]')
                           || container.querySelector('.btn-submit-review')
                           || container.querySelector('button.btn-primary');

            // Cegah binding event berulang pada tombol yang sama
            if (submitBtn && submitBtn.dataset.reviewBound === '1') return;
            if (submitBtn) submitBtn.dataset.reviewBound = '1';

            // Event listener klik radio button untuk feedback border & background card
            const decisionRadios = container.querySelectorAll('input[type="radio"][name="status_review"]');
            decisionRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const cards = container.querySelectorAll('.review-decision-card, .review-choice-label');
                    cards.forEach(c => {
                        c.style.borderColor = '#E2E8F0';
                        c.style.backgroundColor = '#FFFFFF';
                    });
                    const parentCard = this.closest('.review-decision-card, .review-choice-label');
                    if (parentCard) {
                        if (this.value === 'revisi_gpm' || this.value === 'revisi_lpm' || this.value === 'perlu_perbaikan') {
                            parentCard.style.borderColor = '#EF4444';
                            parentCard.style.backgroundColor = '#FEF2F2';
                        } else {
                            parentCard.style.borderColor = '#10B981';
                            parentCard.style.backgroundColor = '#F0FDF4';
                        }
                    }
                });
            });

            async function handleReviewSubmit(e) {
                if (e) e.preventDefault();

                // Ambil nilai radio status review yang dipilih
                const checkedRadio = container.querySelector('input[type="radio"][name="status_review"]:checked');
                const statusReview = checkedRadio ? checkedRadio.value : '';

                // Ambil catatan review
                const ta = container.querySelector('textarea[name="catatan_review"]');
                const catatanReview = (ta ? ta.value : '').trim();

                // Ambil document_id
                const docId = container.querySelector('input[name="document_id"]')?.value
                           || container.querySelector('input[name="id"]')?.value
                           || triggerBtn?.getAttribute('data-doc-id')
                           || '';

                // Ambil CSRF token
                const csrfToken = container.querySelector('input[name="csrf_token"]')?.value
                               || document.querySelector('input[name="csrf_token"]')?.value
                               || '';

                // Validasi Status Keputusan
                if (!statusReview) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih Keputusan Review',
                            text: 'Silakan tentukan keputusan hasil review dokumen mutu terlebih dahulu.',
                            confirmButtonColor: '#2563EB'
                        });
                    } else {
                        alert('Silakan tentukan keputusan hasil review dokumen mutu terlebih dahulu.');
                    }
                    return false;
                }

                // Validasi catatan wajib jika perlu revisi
                if ((statusReview === 'revisi_gpm' || statusReview === 'revisi_lpm' || statusReview === 'perlu_perbaikan') && !catatanReview) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Catatan Review Wajib Diisi',
                            text: 'Silakan tuliskan arahan atau catatan perbaikan untuk unit pengunggah sebelum menyimpan keputusan Perlu Revisi.',
                            confirmButtonColor: '#2563EB'
                        }).then(() => {
                            if (ta) ta.focus();
                        });
                    } else {
                        alert('Catatan review wajib diisi bila memilih opsi Perlu Revisi.');
                        if (ta) ta.focus();
                    }
                    return false;
                }

                // Tentukan target URL
                let targetUrl = (form && form.action) ? form.action : '';
                if (!targetUrl) {
                    const baseUrl = window.BASE_URL || (window.location.origin + (window.location.pathname.startsWith('/spmi_ppepp') ? '/spmi_ppepp' : ''));
                    targetUrl = (role === 'gpm') ? (baseUrl + '/gpm/review/submit') : (baseUrl + '/admin/review-dokumen/submit');
                }

                const formData = new FormData();
                formData.append('document_id', docId);
                formData.append('id', docId);
                formData.append('status_review', statusReview);
                formData.append('catatan_review', catatanReview);
                if (csrfToken) formData.append('csrf_token', csrfToken);

                const origBtnHtml = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1.5"></i> Menyimpan Keputusan...';
                }

                try {
                    const response = await fetch(targetUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (result && result.success) {
                        if (typeof Swal !== 'undefined') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: result.message || 'Keputusan review berhasil disimpan.',
                                timer: 1600,
                                showConfirmButton: false
                            });
                        }
                        // Refresh halaman agar tabel dokumen merefleksikan perubahan status
                        window.location.reload();
                    } else {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = origBtnHtml;
                        }
                        const errMsg = (result && result.message) ? result.message : 'Gagal menyimpan keputusan review.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: errMsg,
                                confirmButtonColor: '#EF4444'
                            });
                        } else {
                            alert(errMsg);
                        }
                    }
                } catch (err) {
                    console.error('Submit review error:', err);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = origBtnHtml;
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Terjadi gangguan jaringan atau kegagalan respon server saat menyimpan keputusan. Silakan coba kembali.',
                            confirmButtonColor: '#EF4444'
                        });
                    } else {
                        alert('Terjadi kesalahan jaringan saat menyimpan keputusan.');
                    }
                }
            }

            if (submitBtn) {
                submitBtn.addEventListener('click', handleReviewSubmit);
            }
            if (form) {
                form.addEventListener('submit', handleReviewSubmit);
            }
        }

        const scrollArea = document.getElementById('pdfViewerScrollArea');
        if (scrollArea) {
            scrollArea.addEventListener('scroll', function() {
                if (window._currentPdfIsReviewMode) {
                    const threshold = 280;
                    if (scrollArea.scrollHeight - (scrollArea.scrollTop + scrollArea.clientHeight) <= threshold) {
                        const reviewBox = document.getElementById('pdfReviewActionBox');
                        if (reviewBox && reviewBox.style.display === 'none') {
                            reviewBox.style.display = 'block';
                            const hint = document.getElementById('pdfReviewStatusHint');
                            if (hint) {
                                hint.innerHTML = '<strong class="text-info"><i class="fas fa-check-circle me-1"></i> Halaman terakhir tercapai.</strong> Silakan berikan keputusan review di bawah.';
                            }
                        }
                    }
                }
            });
        }

        const jumpToReviewBtn = document.getElementById('pdfJumpToReviewBtn');
        if (jumpToReviewBtn) {
            jumpToReviewBtn.addEventListener('click', function() {
                const reviewBox = document.getElementById('pdfReviewActionBox');
                if (reviewBox) {
                    reviewBox.style.display = 'block';
                    reviewBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    const firstInp = reviewBox.querySelector('input[type="radio"]:checked, input[type="radio"], textarea');
                    if (firstInp) setTimeout(() => firstInp.focus(), 350);
                }
            });
        }

        function loadPdfModalContent(button) {
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
            const docTa = button.getAttribute('data-doc-ta') || '';

            const isReviewMode = button.getAttribute('data-is-review') === '1';
            const reviewRole = button.getAttribute('data-review-role') || '';
            const reviewSourceSelector = button.getAttribute('data-review-source') || '';
            const allFilesJson = button.getAttribute('data-all-files') || '';

            const docId = button.getAttribute('data-doc-id') || button.getAttribute('data-id') || '';
            window._currentOpenDocId = docId;

            // Contextual deep linking: perbarui URL browser sesuai tata lokasi halaman aktif + ?doc_id=...
            if (docId) {
                try {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('doc_id', docId);
                    window.history.pushState({ docId: docId }, '', currentUrl.toString());
                } catch(e) {}
            }

            // Reset dan tampilkan tombol Salin Link jika elemen ada
            const copyBtn = document.getElementById('pdfCopyLinkBtn');
            const copyIcon = document.getElementById('pdfCopyLinkIcon');
            const copyText = document.getElementById('pdfCopyLinkText');
            if (copyBtn) {
                copyBtn.style.display = 'inline-flex';
                copyBtn.className = 'btn btn-sm btn-outline-light rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm';
            }
            if (copyIcon) copyIcon.className = 'fas fa-link';
            if (copyText) copyText.textContent = 'Salin Link';

            window._currentPdfIsReviewMode = isReviewMode;

            const modalTitle = pdfModal.querySelector('.modal-title');
            const downloadContainer = document.getElementById('pdfDownloadBtnContainer');
            const accessBadge = document.getElementById('pdfAccessBadge');
            const taBadge = document.getElementById('pdfTaBadge');
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

            const reviewStickyBar = document.getElementById('pdfReviewStickyBar');
            const reviewActionBox = document.getElementById('pdfReviewActionBox');
            const reviewRoleBadge = document.getElementById('pdfReviewRoleBadge');
            const reviewStatusHint = document.getElementById('pdfReviewStatusHint');
            const multiFilesWrap = document.getElementById('pdfMultiFilesWrap');
            const multiFilesContainer = document.getElementById('pdfMultiFilesContainer');
            const noFilesNotice = document.getElementById('pdfNoFilesNotice');

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
            if (taBadge) {
                if (docTa) {
                    taBadge.innerHTML = `<i class="far fa-calendar-alt me-1"></i>TA ${docTa}`;
                    taBadge.style.display = 'inline-flex';
                } else {
                    taBadge.style.display = 'none';
                }
            }
            if (pagesContainer) pagesContainer.innerHTML = '';
            if (lockedBanner) lockedBanner.style.display = 'none';
            if (loadingIndicator) loadingIndicator.style.display = 'block';
            if (zoomDisplay) zoomDisplay.textContent = '125%';
            currentScale = 1.25;

            // Configure Review Mode Elements
            if (isReviewMode) {
                if (reviewStickyBar) reviewStickyBar.style.display = 'flex';
                if (reviewRoleBadge) {
                    reviewRoleBadge.innerHTML = reviewRole === 'gpm' 
                        ? '<i class="fas fa-clipboard-check me-1"></i> Mode Review GPM' 
                        : '<i class="fas fa-clipboard-check me-1"></i> Mode Review LPM';
                }
                if (reviewStatusHint) {
                    reviewStatusHint.innerHTML = 'Silakan baca berkas dokumen hingga halaman terakhir untuk mengisi keputusan review.';
                }

                if (reviewSourceSelector && reviewActionBox) {
                    const srcEl = document.querySelector(reviewSourceSelector);
                    if (srcEl) {
                        reviewActionBox.innerHTML = srcEl.innerHTML;
                        reviewActionBox.style.display = 'none'; // Awalnya sembunyi, tampil di halaman akhir

                        // Default form review kosong: pastikan semua opsi keputusan tidak tercentang & catatan kosong
                        reviewActionBox.querySelectorAll('input[type="radio"][name="status_review"]').forEach(r => {
                            r.checked = false;
                        });
                        const taCatatan = reviewActionBox.querySelector('textarea[name="catatan_review"]');
                        if (taCatatan) {
                            taCatatan.value = '';
                        }
                        reviewActionBox.querySelectorAll('.review-decision-card, .review-choice-label').forEach(c => {
                            c.style.borderColor = '#E2E8F0';
                            c.style.backgroundColor = '#FFFFFF';
                        });

                        initCopiedReviewForm(reviewActionBox, reviewRole, button);
                    }
                }

                // Handle Multiple Files Switcher
                if (allFilesJson && multiFilesWrap && multiFilesContainer) {
                    try {
                        const filesList = JSON.parse(allFilesJson);
                        if (Array.isArray(filesList) && filesList.length > 1) {
                            multiFilesWrap.style.display = 'block';
                            let multiBtns = '<span class="text-white-50 small flex-shrink-0 me-1"><i class="fas fa-layer-group text-info me-1"></i> Berkas Dokumen (' + filesList.length + '):</span>';
                            filesList.forEach((fl, fIdx) => {
                                const isActive = (fIdx === 0);
                                const btnClass = isActive ? 'btn-primary text-white fw-bold active-file-btn' : 'btn-outline-light text-white';
                                const iconClass = fl.type === 'link' ? 'fab fa-google-drive' : 'fas fa-file-pdf';
                                multiBtns += `
                                    <button type="button" class="btn btn-xs rounded-pill px-3 py-1 text-truncate multi-file-switch-btn ${btnClass}" 
                                            style="max-width: 200px; font-size: 0.75rem;" 
                                            data-file-index="${fIdx}"
                                            title="${fl.name}">
                                        <i class="${iconClass} me-1"></i> ${fl.name}
                                    </button>
                                `;
                            });
                            multiFilesContainer.innerHTML = multiBtns;

                            multiFilesContainer.querySelectorAll('.multi-file-switch-btn').forEach(swBtn => {
                                swBtn.onclick = function() {
                                    const targetIdx = parseInt(this.getAttribute('data-file-index'), 10);
                                    const fileObj = filesList[targetIdx];
                                    if (!fileObj) return;

                                    multiFilesContainer.querySelectorAll('.multi-file-switch-btn').forEach(b => {
                                        b.className = 'btn btn-xs rounded-pill px-3 py-1 text-truncate multi-file-switch-btn btn-outline-light text-white';
                                    });
                                    this.className = 'btn btn-xs rounded-pill px-3 py-1 text-truncate multi-file-switch-btn btn-primary text-white fw-bold active-file-btn';

                                    const mockBtn = document.createElement('button');
                                    mockBtn.setAttribute('data-doc-title', fileObj.name || docTitle);
                                    mockBtn.setAttribute('data-doc-narasi', fileObj.narasi || '');
                                    mockBtn.setAttribute('data-doc-standar', fileObj.standar || '');
                                    mockBtn.setAttribute('data-can-download', canDownload);
                                    mockBtn.setAttribute('data-can-access', canAccess);
                                    mockBtn.setAttribute('data-public-limit', publicLimit);
                                    mockBtn.setAttribute('data-doc-ta', docTa);
                                    mockBtn.setAttribute('data-is-review', '1');
                                    mockBtn.setAttribute('data-review-role', reviewRole);
                                    mockBtn.setAttribute('data-review-source', reviewSourceSelector);

                                    if (fileObj.type === 'link') {
                                        mockBtn.setAttribute('data-is-link', '1');
                                        mockBtn.setAttribute('data-link-url', fileObj.url);
                                    } else {
                                        mockBtn.setAttribute('data-is-link', '0');
                                        mockBtn.setAttribute('data-pdf-url', fileObj.url);
                                    }

                                    loadPdfModalContent(mockBtn);
                                };
                            });
                        } else {
                            multiFilesWrap.style.display = 'none';
                            multiFilesContainer.innerHTML = '';
                        }
                    } catch (e) {
                        console.warn('Error parsing all-files:', e);
                        multiFilesWrap.style.display = 'none';
                    }
                }
            } else {
                if (reviewStickyBar) reviewStickyBar.style.display = 'none';
                if (reviewActionBox) {
                    reviewActionBox.style.display = 'none';
                    reviewActionBox.innerHTML = '';
                }
                if (multiFilesWrap) multiFilesWrap.style.display = 'none';
                if (noFilesNotice) noFilesNotice.style.display = 'none';
            }

            // Jika dokumen sama sekali tidak memiliki berkas maupun tautan
            if (!pdfUrl && !linkUrl) {
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                if (toolbar) toolbar.style.display = 'none';
                if (pagesContainer) pagesContainer.style.display = 'none';
                if (iframeWrap) iframeWrap.style.display = 'none';
                if (noFilesNotice) noFilesNotice.style.display = 'block';
                if (reviewActionBox) reviewActionBox.style.display = 'block';
                if (reviewStatusHint) reviewStatusHint.textContent = 'Dokumen belum melampirkan berkas. Silakan berikan catatan evaluasi perbaikan untuk unit.';
                return;
            } else {
                if (noFilesNotice) noFilesNotice.style.display = 'none';
            }

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

                        if (isReviewMode && reviewActionBox) {
                            reviewActionBox.style.display = 'block';
                            if (reviewStatusHint) reviewStatusHint.innerHTML = '<strong class="text-info"><i class="fas fa-check-circle me-1"></i> Tautan Cloud Siap.</strong> Silakan lengkapi keputusan review di bawah.';
                        }
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

            // Configure Download Button for PDF (Only accessible for logged in Unika users)
            if (downloadContainer) {
                const curPath = window.location.pathname + window.location.search;
                const loginUrlWithReturn = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;
                if (isUserLoggedIn) {
                    downloadContainer.innerHTML = `
                        <a href="${pdfUrl}" target="_blank" download class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="fas fa-download me-1"></i> Unduh Asli
                        </a>
                    `;
                } else {
                    downloadContainer.innerHTML = `
                        <a href="${loginUrlWithReturn}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold text-white bg-scu-blue border-0" title="Masuk dengan akun @unika.ac.id untuk mengunduh dokumen resmi">
                            <i class="fas fa-lock me-1"></i> Login untuk Unduh
                        </a>
                    `;
                }
            }

            // Configure Access Badge & Page Limit
            if (accessBadge) {
                if (isUserLoggedIn) {
                    accessBadge.className = 'badge bg-success bg-opacity-90 text-white rounded-pill px-2.5 py-1 fw-bold';
                    accessBadge.innerHTML = '<i class="fas fa-unlock me-1"></i> Akses Penuh (Akun UNIKA)';
                } else {
                    accessBadge.className = 'badge bg-light text-primary border rounded-pill px-2.5 py-1 fw-bold';
                    accessBadge.innerHTML = '<i class="fas fa-lock me-1"></i> Pratinjau Terbatas (1 Hlm)';
                }
            }

            // Check file format & extension
            const fileExtMatch = (pdfUrl || '').split('?')[0].split('#')[0].split('.').pop().toLowerCase();
            const fileTypeAttr = (button.getAttribute('data-file-type') || '').toLowerCase();
            const isImageExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'].includes(fileExtMatch);
            const hasImageInName = /\.(jpe?g|png|webp|gif|bmp)(\s|$|\?|#|\.)/i.test(pdfUrl) || /\.(jpe?g|png|webp|gif|bmp)(\s|$)/i.test(docTitle);
            const isImageFile = isImageExt || fileTypeAttr === 'image' || fileTypeAttr === 'photo' || hasImageInName;
            const isVideoExt = ['mp4', 'mkv', 'mov', 'webm', 'avi', 'm4a'].includes(fileExtMatch);
            const hasVideoInName = /\.(mp4|mkv|mov|webm|avi|m4a)(\s|$|\?|#|\.)/i.test(pdfUrl) || /\.(mp4|mkv|mov|webm|avi|m4a)(\s|$)/i.test(docTitle);
            const isVideoFile = isVideoExt || fileTypeAttr === 'video' || hasVideoInName;
            const isWordFile = ['docx', 'doc'].includes(fileExtMatch) || fileTypeAttr === 'word' || fileTypeAttr === 'docx';
            const isExcelFile = ['xlsx', 'xls', 'csv'].includes(fileExtMatch) || fileTypeAttr === 'excel';
            const isPptFile = ['pptx', 'ppt'].includes(fileExtMatch) || fileTypeAttr === 'ppt' || fileTypeAttr === 'powerpoint';

            function renderImageDirectPreview(url, title) {
                if (headerIcon) headerIcon.className = 'fas fa-file-image fa-lg text-warning';
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                if (totalInfo) totalInfo.textContent = 'Berkas Foto / Gambar Digital';
                const pageCounter = document.getElementById('pdfPageCounterBadge');
                if (pageCounter) pageCounter.textContent = '1 / 1 (Foto)';
                if (toolbar) toolbar.style.display = 'flex';
                if (zoomDisplay) zoomDisplay.textContent = '100%';
                currentScale = 1.0;

                if (accessBadge) {
                    if (isUserLoggedIn) {
                        accessBadge.className = 'badge bg-success bg-opacity-90 text-white rounded-pill px-2.5 py-1 fw-bold';
                        accessBadge.innerHTML = '<i class="fas fa-unlock me-1"></i> Akses Penuh (Akun UNIKA)';
                    } else {
                        accessBadge.className = 'badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1 fw-bold';
                        accessBadge.innerHTML = '<i class="fas fa-image me-1"></i> Foto / Gambar Digital';
                    }
                }

                if (downloadContainer) {
                    const curPath = window.location.pathname + window.location.search;
                    const loginUrlWithReturn = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;
                    if (isUserLoggedIn) {
                        downloadContainer.innerHTML = `
                            <a href="${url}" target="_blank" download class="btn btn-sm btn-outline-light rounded-pill px-3">
                                <i class="fas fa-download me-1"></i> Unduh Asli
                            </a>
                        `;
                    } else {
                        downloadContainer.innerHTML = `
                            <a href="${loginUrlWithReturn}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold text-white bg-scu-blue border-0" title="Masuk dengan akun @unika.ac.id untuk mengunduh dokumen resmi">
                                <i class="fas fa-lock me-1"></i> Login untuk Unduh
                            </a>
                        `;
                    }
                }

                if (pagesContainer) {
                    pagesContainer.style.display = 'flex';
                    pagesContainer.style.background = '#0F172A';
                    pagesContainer.innerHTML = `
                        <div class="d-flex justify-content-center align-items-center w-100 p-3" style="min-height: 65vh;">
                            <img id="pdfViewerImage" src="${url}" alt="${title}" 
                                 class="img-fluid rounded-3 shadow-lg" 
                                 style="max-width: 95%; max-height: 82vh; object-fit: contain; transition: transform 0.2s ease; border: 1px solid rgba(255,255,255,0.2);" />
                        </div>
                    `;
                }

                if (isReviewMode && reviewActionBox) {
                    reviewActionBox.style.display = 'block';
                    if (reviewStatusHint) reviewStatusHint.innerHTML = '<strong class="text-info"><i class="fas fa-check-circle me-1"></i> Foto Siap.</strong> Silakan lengkapi keputusan review di bawah.';
                }
            }

            function renderVideoDirectPreview(url, title) {
                if (headerIcon) headerIcon.className = 'fas fa-file-video fa-lg text-purple';
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                if (totalInfo) totalInfo.textContent = 'Pemutar Video (' + (fileExtMatch.toUpperCase() || 'MP4') + ')';
                const pageCounter = document.getElementById('pdfPageCounterBadge');
                if (pageCounter) pageCounter.textContent = 'Video Playback';
                if (toolbar) toolbar.style.display = 'none';

                if (accessBadge) {
                    if (isUserLoggedIn) {
                        accessBadge.className = 'badge bg-success bg-opacity-90 text-white rounded-pill px-2.5 py-1 fw-bold';
                        accessBadge.innerHTML = '<i class="fas fa-unlock me-1"></i> Akses Penuh (Akun UNIKA)';
                    } else {
                        accessBadge.className = 'badge rounded-pill px-2.5 py-1 fw-bold';
                        accessBadge.style.background = '#EDE9FE';
                        accessBadge.style.color = '#7C3AED';
                        accessBadge.style.border = '1px solid #DDD6FE';
                        accessBadge.innerHTML = '<i class="fas fa-video me-1"></i> Pemutaran Video';
                    }
                }

                if (downloadContainer) {
                    const curPath = window.location.pathname + window.location.search;
                    const loginUrlWithReturn = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;
                    if (isUserLoggedIn) {
                        downloadContainer.innerHTML = `
                            <a href="${url}" target="_blank" download class="btn btn-sm btn-outline-light rounded-pill px-3">
                                <i class="fas fa-download me-1"></i> Unduh Video Asli
                            </a>
                        `;
                    } else {
                        downloadContainer.innerHTML = `
                            <a href="${loginUrlWithReturn}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold text-white bg-scu-blue border-0" title="Masuk dengan akun @unika.ac.id untuk mengunduh dokumen resmi">
                                <i class="fas fa-lock me-1"></i> Login untuk Unduh
                            </a>
                        `;
                    }
                }

                if (pagesContainer) {
                    pagesContainer.style.display = 'flex';
                    pagesContainer.style.background = '#0F172A';
                    pagesContainer.style.padding = '1.5rem';
                    pagesContainer.style.justifyContent = 'center';
                    pagesContainer.style.alignItems = 'center';
                    pagesContainer.innerHTML = `
                        <div class="d-flex flex-column justify-content-center align-items-center w-100" style="min-height: 65vh;">
                            <video id="pdfViewerVideo" controls autoplay playsinline class="rounded-3 shadow-lg" style="max-width: 95%; max-height: 80vh; outline: none; border: 1px solid rgba(255,255,255,0.15); background: #000;">
                                <source src="${url}">
                                Browser Anda tidak mendukung pemutar video HTML5. Silakan unduh berkas untuk menonton.
                            </video>
                            <div class="mt-2 text-white-50 small d-flex align-items-center gap-2">
                                <i class="fas fa-info-circle"></i> Kontrol pemutar video aktif. Anda dapat memutar, menjeda, atau beralih ke layar penuh (fullscreen).
                            </div>
                        </div>
                    `;
                }
            }

            if (isVideoFile) {
                renderVideoDirectPreview(pdfUrl, docTitle);
                return;
            }

            if (isImageFile) {
                renderImageDirectPreview(pdfUrl, docTitle);
                return;
            }

            if (isWordFile) {
                if (headerIcon) headerIcon.className = 'fas fa-file-word fa-lg text-primary';
                if (totalInfo) totalInfo.textContent = 'Dokumen Microsoft Word (' + fileExtMatch.toUpperCase() + ')';
                const pageCounter = document.getElementById('pdfPageCounterBadge');
                if (pageCounter) pageCounter.textContent = 'Word (.docx)';

                if (accessBadge) {
                    if (isUserLoggedIn) {
                        accessBadge.className = 'badge bg-success bg-opacity-90 text-white rounded-pill px-2.5 py-1 fw-bold';
                        accessBadge.innerHTML = '<i class="fas fa-unlock me-1"></i> Akses Penuh (Akun UNIKA)';
                    } else {
                        accessBadge.className = 'badge bg-light text-primary border rounded-pill px-2.5 py-1 fw-bold';
                        accessBadge.innerHTML = '<i class="fas fa-lock me-1"></i> Pratinjau Terbatas (1 Hlm)';
                    }
                }

                if (downloadContainer) {
                    const curPath = window.location.pathname + window.location.search;
                    const loginUrlWithReturn = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;
                    if (isUserLoggedIn) {
                        downloadContainer.innerHTML = `
                            <a href="${pdfUrl}" target="_blank" download class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                <i class="fas fa-download me-1"></i> Unduh Berkas Word
                            </a>
                        `;
                    } else {
                        downloadContainer.innerHTML = `
                            <a href="${loginUrlWithReturn}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold text-white bg-scu-blue border-0" title="Masuk dengan akun @unika.ac.id untuk mengunduh dokumen resmi">
                                <i class="fas fa-lock me-1"></i> Login untuk Unduh
                            </a>
                        `;
                    }
                }

                function renderWordFallback(container) {
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                    if (!container) return;
                    container.style.display = 'block';
                    container.innerHTML = `
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center my-5 mx-auto" style="max-width: 520px; background: #ffffff;">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="fas fa-file-word fa-2x"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">${docTitle}</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 my-2" style="font-size:0.75rem;">Berkas Microsoft Word (.${fileExtMatch.toUpperCase()})</span>
                            <p class="text-muted small mb-4" style="line-height:1.6;">Dokumen mutu ini berformat Microsoft Word. Anda dapat langsung mengunduhnya untuk dibuka dan diedit menggunakan Microsoft Word, Google Docs, atau WPS Office.</p>
                            <a href="${pdfUrl}" download class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2 justify-content-center mx-auto">
                                <i class="fas fa-download"></i> Unduh Berkas Word
                            </a>
                        </div>
                    `;
                }

                // Render via docx-preview if file is docx and library loaded
                if (fileExtMatch === 'docx' && typeof docx !== 'undefined' && docx.renderAsync) {
                    if (toolbar) toolbar.style.display = 'flex';
                    if (zoomDisplay) zoomDisplay.textContent = '100%';
                    currentScale = 1.0;

                    fetch(pdfUrl)
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.blob();
                        })
                        .then(blob => {
                            if (loadingIndicator) loadingIndicator.style.display = 'none';
                            if (pagesContainer) {
                                pagesContainer.style.display = 'block';
                                pagesContainer.style.background = '#525659';
                                pagesContainer.style.padding = '0';
                                pagesContainer.innerHTML = '<div id="docxRenderWrapper" style="width:100%; min-height:100%; display:flex; flex-direction:column; align-items:center; transition:transform 0.15s ease-out; transform-origin: top center;"></div>';
                                const docxContainer = document.getElementById('docxRenderWrapper');
                                docx.renderAsync(blob, docxContainer, null, {
                                    className: 'docx',
                                    inWrapper: true,
                                    ignoreWidth: false,
                                    ignoreHeight: false,
                                    breakPages: true,
                                    renderHeaders: true,
                                    renderFooters: true,
                                    renderComments: false
                                }).then(() => {
                                    // Detect page orientation from rendered sections
                                    const sections = docxContainer.querySelectorAll('section.docx');
                                    let isLandscapeDoc = false;
                                    sections.forEach(sec => {
                                        sec.style.overflowX = 'auto';
                                        sec.style.maxWidth = '100%';
                                        sec.style.boxSizing = 'border-box';
                                        const w = sec.offsetWidth || sec.clientWidth;
                                        const h = sec.offsetHeight || sec.clientHeight;
                                        if (w > h || (sec.style.width && sec.style.width.includes('297mm'))) {
                                            isLandscapeDoc = true;
                                            sec.classList.add('is-landscape');
                                        }
                                    });

                                    // Auto-wrap wide docx tables so they never overflow paper margins
                                    const allTables = docxContainer.querySelectorAll('table');
                                    allTables.forEach(tbl => {
                                        tbl.style.maxWidth = '100%';
                                        tbl.style.boxSizing = 'border-box';
                                        if (tbl.parentElement && !tbl.parentElement.classList.contains('docx-table-wrap')) {
                                            const wrap = document.createElement('div');
                                            wrap.className = 'docx-table-wrap';
                                            wrap.style.cssText = 'max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; margin-bottom: 12px;';
                                            tbl.parentNode.insertBefore(wrap, tbl);
                                            wrap.appendChild(tbl);
                                        }
                                    });

                                    // If not logged in as Unika account, restrict to page 1 only!
                                    if (!isUserLoggedIn && sections.length > 1) {
                                        for (let i = 1; i < sections.length; i++) {
                                            sections[i].style.display = 'none';
                                        }

                                        const lockedNotice = document.createElement('div');
                                        lockedNotice.className = 'p-4 mx-auto my-4 text-center rounded-4 shadow-lg';
                                        lockedNotice.style.cssText = 'max-width: 680px; width: 92%; background: linear-gradient(135deg, #0A192F 0%, #1E3E62 50%, #1E293B 100%); border: 1px solid rgba(255,255,255,0.15); color: #ffffff;';
                                        const curPath = window.location.pathname + window.location.search;
                                        const loginUrl = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;
                                        lockedNotice.innerHTML = `
                                            <div class="lock-icon-circle mx-auto mb-3 d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 text-info rounded-circle" style="width: 58px; height: 58px;">
                                                <i class="fas fa-shield-halved fa-2x"></i>
                                            </div>
                                            <h5 class="fw-bold text-white mb-2">Pratinjau Dibatasi (Halaman 1 dari ${sections.length})</h5>
                                            <p class="text-white text-opacity-75 mx-auto mb-3" style="max-width: 480px; font-size: 0.85rem; line-height: 1.6;">
                                                Anda hanya dapat melihat halaman pertama sebagai pratinjau publik. Silakan masuk menggunakan akun resmi <strong>@unika.ac.id</strong> untuk membaca seluruh halaman secara lengkap dan mengunduh berkas asli.
                                            </p>
                                            <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                                                <a href="${loginUrl}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-white shadow-sm bg-scu-blue border-0">
                                                    <i class="fas fa-right-to-bracket me-1.5"></i> Masuk dengan Akun Unika
                                                </a>
                                            </div>
                                        `;
                                        docxContainer.appendChild(lockedNotice);

                                        const pageCounter = document.getElementById('pdfPageCounterBadge');
                                        if (pageCounter) {
                                            pageCounter.textContent = `1 / ${sections.length} Hlm (Dibatasi)`;
                                        }

                                        if (totalInfo) {
                                            totalInfo.textContent = isLandscapeDoc
                                                ? 'Dokumen Word (Kertas Landscape / Mendatar) - Halaman 1 Saja'
                                                : 'Dokumen Word (Kertas Portrait / Tegak) - Halaman 1 Saja';
                                        }
                                    } else {
                                        const pageCounter = document.getElementById('pdfPageCounterBadge');
                                        if (pageCounter) {
                                            pageCounter.textContent = isLandscapeDoc 
                                                ? `Word (${sections.length} Hlm - Landscape)` 
                                                : `Word (${sections.length} Hlm - Portrait)`;
                                        }

                                        if (totalInfo) {
                                            totalInfo.textContent = isLandscapeDoc
                                                ? 'Dokumen Microsoft Word (Kertas Landscape / Mendatar)'
                                                : 'Dokumen Microsoft Word (Kertas Portrait / Tegak)';
                                        }
                                    }

                                    if (isReviewMode && reviewActionBox) {
                                        reviewActionBox.style.display = 'block';
                                        if (reviewStatusHint) reviewStatusHint.innerHTML = '<strong class="text-info"><i class="fas fa-check-circle me-1"></i> Berkas Word Siap.</strong> Silakan lengkapi keputusan review di bawah.';
                                    }
                                }).catch(err => {
                                    console.warn('docx preview render error:', err);
                                    renderWordFallback(pagesContainer);
                                });
                                if (isReviewMode && reviewActionBox) {
                                    reviewActionBox.style.display = 'block';
                                    if (reviewStatusHint) reviewStatusHint.innerHTML = '<strong class="text-info"><i class="fas fa-check-circle me-1"></i> Spreadsheet Siap.</strong> Silakan lengkapi keputusan review di bawah.';
                                }
                            }
                        })
                        .catch(err => {
                            console.warn('docx fetch error:', err);
                            renderWordFallback(pagesContainer);
                        });
                } else {
                    renderWordFallback(pagesContainer);
                }
                return;
            }

            if (isExcelFile) {
                if (headerIcon) headerIcon.className = 'fas fa-file-excel fa-lg text-success';
                if (toolbar) toolbar.style.display = 'none';
                if (totalInfo) totalInfo.textContent = 'Lembar Kerja Microsoft Excel (' + fileExtMatch.toUpperCase() + ')';
                const pageCounter = document.getElementById('pdfPageCounterBadge');
                if (pageCounter) pageCounter.textContent = 'Spreadsheet (.xlsx)';

                if (accessBadge) {
                    if (isUserLoggedIn) {
                        accessBadge.className = 'badge bg-success bg-opacity-90 text-white rounded-pill px-2.5 py-1 fw-bold';
                        accessBadge.innerHTML = '<i class="fas fa-unlock me-1"></i> Akses Penuh (Akun Terdaftar)';
                    } else {
                        accessBadge.className = 'badge bg-light text-success border rounded-pill px-2.5 py-1 fw-bold';
                        accessBadge.innerHTML = '<i class="fas fa-lock me-1"></i> Pratinjau Terbatas';
                    }
                }

                if (downloadContainer) {
                    const curPath = window.location.pathname + window.location.search;
                    const loginUrlWithReturn = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;
                    if (isUserLoggedIn) {
                        downloadContainer.innerHTML = `
                            <a href="${pdfUrl}" target="_blank" download class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                <i class="fas fa-download me-1"></i> Unduh Berkas Excel
                            </a>
                        `;
                    } else {
                        downloadContainer.innerHTML = `
                            <a href="${loginUrlWithReturn}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold text-white bg-scu-blue border-0" title="Masuk dengan akun institusi untuk mengunduh">
                                <i class="fas fa-lock me-1"></i> Login untuk Unduh
                            </a>
                        `;
                    }
                }

                function renderExcelFallback(container) {
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                    if (!container) return;
                    container.style.display = 'block';
                    container.innerHTML = `
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center my-5 mx-auto" style="max-width: 520px; background: #ffffff;">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="fas fa-file-excel fa-2x"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">${docTitle}</h5>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 my-2" style="font-size:0.75rem;">Berkas Spreadsheet (.${fileExtMatch.toUpperCase()})</span>
                            <p class="text-muted small mb-4" style="line-height:1.6;">Dokumen mutu ini berformat Microsoft Excel. Anda dapat langsung mengunduhnya untuk dibuka menggunakan Microsoft Excel, Google Sheets, atau WPS Office.</p>
                            <a href="${pdfUrl}" download class="btn btn-success rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2 justify-content-center mx-auto">
                                <i class="fas fa-download"></i> Unduh Berkas Excel
                            </a>
                        </div>
                    `;
                }

                if (typeof XLSX !== 'undefined') {
                    fetch(pdfUrl)
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.arrayBuffer();
                        })
                        .then(data => {
                            if (loadingIndicator) loadingIndicator.style.display = 'none';
                            const workbook = XLSX.read(data, { type: 'array' });
                            const sheetNames = workbook.SheetNames || [];
                            if (sheetNames.length === 0) {
                                renderExcelFallback(pagesContainer);
                                return;
                            }

                            if (pagesContainer) {
                                pagesContainer.style.display = 'block';
                                pagesContainer.style.background = '#525659';
                                pagesContainer.style.padding = '20px';

                                let tabsHtml = '';
                                if (sheetNames.length > 1) {
                                    tabsHtml = '<div class="d-flex align-items-center gap-1.5 mb-3 flex-wrap bg-dark p-2 rounded-3">';
                                    sheetNames.forEach((sName, idx) => {
                                        tabsHtml += `<button type="button" class="btn btn-xs rounded-pill px-3 py-1.5 fw-semibold sheet-tab-btn ${idx === 0 ? 'btn-success text-white' : 'btn-outline-light text-white'}" onclick="switchExcelSheet('${sName.replace(/'/g, "\\'")}', this)">${sName}</button>`;
                                    });
                                    tabsHtml += '</div>';
                                }

                                pagesContainer.innerHTML = `
                                    <div class="mx-auto" style="max-width: 96%; width: 1200px;">
                                        ${tabsHtml}
                                        <div id="excelSheetContentContainer" class="excel-sheet-table-wrap"></div>
                                        <div id="excelLockedNoticeContainer"></div>
                                    </div>
                                `;

                                window._currentXlsxWorkbook = workbook;
                                window.switchExcelSheet = function(sName, btnEl) {
                                    if (btnEl) {
                                        document.querySelectorAll('.sheet-tab-btn').forEach(b => {
                                            b.className = 'btn btn-xs rounded-pill px-3 py-1.5 fw-semibold sheet-tab-btn btn-outline-light text-white';
                                        });
                                        btnEl.className = 'btn btn-xs rounded-pill px-3 py-1.5 fw-semibold sheet-tab-btn btn-success text-white';
                                    }

                                    const ws = workbook.Sheets[sName];
                                    if (!ws) return;

                                    let htmlTable = XLSX.utils.sheet_to_html(ws, { editable: false });
                                    const contentEl = document.getElementById('excelSheetContentContainer');
                                    const noticeEl = document.getElementById('excelLockedNoticeContainer');

                                    if (!isUserLoggedIn) {
                                        // Restrict rows for public/unauthenticated guests
                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = htmlTable;
                                        const trs = tempDiv.querySelectorAll('tr');
                                        const maxRows = 15;
                                        if (trs.length > maxRows) {
                                            for (let r = maxRows; r < trs.length; r++) {
                                                trs[r].remove();
                                            }
                                        }
                                        contentEl.innerHTML = tempDiv.innerHTML;

                                        const curPath = window.location.pathname + window.location.search;
                                        const loginUrl = `${window.LOGIN_URL || '/login'}?return_url=${encodeURIComponent(curPath)}`;
                                        noticeEl.innerHTML = `
                                            <div class="p-4 text-center rounded-4 shadow-lg my-3" style="background: linear-gradient(135deg, #0A192F 0%, #1E3E62 50%, #1E293B 100%); border: 1px solid rgba(255,255,255,0.15); color: #ffffff;">
                                                <div class="lock-icon-circle mx-auto mb-2.5 d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 text-info rounded-circle" style="width: 52px; height: 52px;">
                                                    <i class="fas fa-shield-halved fa-lg"></i>
                                                </div>
                                                <h5 class="fw-bold text-white mb-2">Pratinjau Spreadsheet Dibatasi (${maxRows} Baris Pertama)</h5>
                                                <p class="text-white text-opacity-75 mx-auto mb-3" style="max-width: 480px; font-size: 0.85rem; line-height: 1.6;">
                                                    Dokumen spreadsheet ini memiliki data lengkap. Masuk menggunakan akun terdaftar atau civitas akademika untuk membuka seluruh baris dan mengunduh berkas lengkap.
                                                </p>
                                                <a href="${loginUrl}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-white shadow-sm bg-scu-blue border-0">
                                                    <i class="fas fa-right-to-bracket me-1.5"></i> Masuk Sekarang
                                                </a>
                                            </div>
                                        `;
                                    } else {
                                        contentEl.innerHTML = htmlTable;
                                        noticeEl.innerHTML = '';
                                    }
                                };

                                window.switchExcelSheet(sheetNames[0], null);
                                if (isReviewMode && reviewActionBox) {
                                    reviewActionBox.style.display = 'block';
                                    if (reviewStatusHint) reviewStatusHint.innerHTML = '<strong class="text-info"><i class="fas fa-check-circle me-1"></i> Spreadsheet Siap.</strong> Silakan lengkapi keputusan review di bawah.';
                                }
                            }
                        })
                        .catch(err => {
                            console.warn('Excel parse error:', err);
                            renderExcelFallback(pagesContainer);
                        });
                } else {
                    renderExcelFallback(pagesContainer);
                }
                return;
            }

            if (isPptFile) {
                if (headerIcon) headerIcon.className = 'fas fa-file-powerpoint fa-lg text-warning';
                if (toolbar) toolbar.style.display = 'none';
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                if (totalInfo) totalInfo.textContent = 'Presentasi Microsoft PowerPoint (' + fileExtMatch.toUpperCase() + ')';
                const pageCounter = document.getElementById('pdfPageCounterBadge');
                if (pageCounter) pageCounter.textContent = 'PowerPoint (.pptx)';

                if (accessBadge) {
                    accessBadge.className = 'badge bg-warning bg-opacity-90 text-white rounded-pill px-2.5 py-1 fw-bold';
                    accessBadge.innerHTML = '<i class="fas fa-file-powerpoint me-1"></i> Berkas PPT';
                }

                if (downloadContainer) {
                    downloadContainer.innerHTML = `
                        <a href="${pdfUrl}" target="_blank" download class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm text-white">
                            <i class="fas fa-download me-1"></i> Unduh Berkas PPT
                        </a>
                    `;
                }

                if (pagesContainer) {
                    pagesContainer.style.display = 'block';
                    pagesContainer.innerHTML = `
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center my-5 mx-auto" style="max-width: 520px; background: #ffffff;">
                            <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="fas fa-file-powerpoint fa-2x"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">${docTitle}</h5>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 my-2" style="font-size:0.75rem;">Berkas Microsoft PowerPoint (.${fileExtMatch.toUpperCase()})</span>
                            <p class="text-muted small mb-4" style="line-height:1.6;">Dokumen mutu ini berupa slide presentasi. Silakan unduh berkas untuk membukanya di Microsoft PowerPoint atau Google Slides.</p>
                            <a href="${pdfUrl}" download class="btn btn-warning text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2 justify-content-center mx-auto">
                                <i class="fas fa-download"></i> Unduh Berkas PPT
                            </a>
                        </div>
                    `;
                }
                return;
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

                if (!isUserLoggedIn) {
                    currentRenderLimit = Math.min(totalPages, 1);
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
                
                // Fallback auto-recovery: check if the file is actually video or image before showing error
                if (isVideoFile || /\.(mp4|mkv|mov|webm|avi|m4a)/i.test(pdfUrl)) {
                    renderVideoDirectPreview(pdfUrl, docTitle);
                    return;
                }
                // Check if the file is actually an image before showing error
                const testImg = new Image();
                testImg.onload = function() {
                    renderImageDirectPreview(pdfUrl, docTitle);
                };
                testImg.onerror = function() {
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                    if (pagesContainer) {
                        pagesContainer.innerHTML = `
                            <div class="card border-0 shadow-sm rounded-4 p-4 text-center my-5 mx-auto" style="max-width: 520px; background: #ffffff;">
                                <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                    <i class="fas fa-file-arrow-down fa-2x"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">${docTitle}</h5>
                                <p class="text-muted small mb-3" style="line-height:1.6;">Berkas ini tidak dapat dipratinjau langsung di peramban (format non-PDF atau berkas unduhan langsung). Silakan unduh berkas untuk melihat isinya.</p>
                                <a href="${pdfUrl}" download class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2 justify-content-center mx-auto">
                                    <i class="fas fa-download"></i> Unduh Berkas Dokumen
                                </a>
                            </div>
                        `;
                    }
                };
                testImg.src = pdfUrl;
            });
        }

        pdfModal.addEventListener('show.bs.modal', function (event) {
            loadPdfModalContent(event.relatedTarget);
        });

        pdfModal.addEventListener('hidden.bs.modal', function () {
            currentRenderTaskId++; // Cancel any running page renders
            window._currentPdfIsReviewMode = false;

            const activeVideo = pagesContainer ? pagesContainer.querySelector('video') : null;
            if (activeVideo) {
                try {
                    activeVideo.pause();
                    activeVideo.removeAttribute('src');
                    activeVideo.load();
                } catch(e) {}
            }
            const pagesContainer = document.getElementById('pdfPagesContainer');
            if (pagesContainer) {
                pagesContainer.innerHTML = '';
                pagesContainer.style.background = '';
                pagesContainer.style.padding = '';
            }
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

            const reviewStickyBar = document.getElementById('pdfReviewStickyBar');
            if (reviewStickyBar) reviewStickyBar.style.display = 'none';
            const reviewActionBox = document.getElementById('pdfReviewActionBox');
            if (reviewActionBox) {
                reviewActionBox.style.display = 'none';
                reviewActionBox.innerHTML = '';
            }
            const multiFilesWrap = document.getElementById('pdfMultiFilesWrap');
            if (multiFilesWrap) multiFilesWrap.style.display = 'none';
            const multiFilesContainer = document.getElementById('pdfMultiFilesContainer');
            if (multiFilesContainer) multiFilesContainer.innerHTML = '';
            const noFilesNotice = document.getElementById('pdfNoFilesNotice');
            if (noFilesNotice) noFilesNotice.style.display = 'none';

            currentPdfDoc = null;
            pdfModal.style.zIndex = '';

            // Bersihkan parameter ?doc_id= atau ?id= pada URL saat modal ditutup (revert back cleanly)
            window._currentOpenDocId = null;
            try {
                const currentUrl = new URL(window.location.href);
                if (currentUrl.searchParams.has('doc_id') || currentUrl.searchParams.has('id')) {
                    currentUrl.searchParams.delete('doc_id');
                    currentUrl.searchParams.delete('id');
                    window.history.replaceState({}, '', currentUrl.toString());
                }
            } catch(e) {}

            // Reset tampilan tombol Salin Link
            const copyBtn = document.getElementById('pdfCopyLinkBtn');
            const copyIcon = document.getElementById('pdfCopyLinkIcon');
            const copyText = document.getElementById('pdfCopyLinkText');
            if (copyBtn) {
                copyBtn.className = 'btn btn-sm btn-outline-light rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm';
            }
            if (copyIcon) copyIcon.className = 'fas fa-link';
            if (copyText) copyText.textContent = 'Salin Link';

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
                const imgEl = document.getElementById('pdfViewerImage');
                if (imgEl) {
                    if (currentScale >= 2.5) return;
                    currentScale += 0.25;
                    if (zoomDisplay) zoomDisplay.textContent = Math.round(currentScale * 100) + '%';
                    imgEl.style.transform = `scale(${currentScale})`;
                    return;
                }
                const docxEl = document.getElementById('docxRenderWrapper');
                if (docxEl) {
                    if (currentScale >= 2.5) return;
                    currentScale += 0.25;
                    if (zoomDisplay) zoomDisplay.textContent = Math.round(currentScale * 100) + '%';
                    docxEl.style.transform = `scale(${currentScale})`;
                    docxEl.style.transformOrigin = 'top center';
                    return;
                }
                if (!currentPdfDoc || currentScale >= 2.5) return;
                currentScale += 0.25;
                if (zoomDisplay) zoomDisplay.textContent = Math.round(currentScale * 100) + '%';
                reRenderPages(currentPdfDoc, currentRenderLimit, currentScale);
            });
        }

        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', function () {
                const imgEl = document.getElementById('pdfViewerImage');
                if (imgEl) {
                    if (currentScale <= 0.5) return;
                    currentScale -= 0.25;
                    if (zoomDisplay) zoomDisplay.textContent = Math.round(currentScale * 100) + '%';
                    imgEl.style.transform = `scale(${currentScale})`;
                    return;
                }
                const docxEl = document.getElementById('docxRenderWrapper');
                if (docxEl) {
                    if (currentScale <= 0.5) return;
                    currentScale -= 0.25;
                    if (zoomDisplay) zoomDisplay.textContent = Math.round(currentScale * 100) + '%';
                    docxEl.style.transform = `scale(${currentScale})`;
                    docxEl.style.transformOrigin = 'top center';
                    return;
                }
                if (!currentPdfDoc || currentScale <= 0.75) return;
                currentScale -= 0.25;
                if (zoomDisplay) zoomDisplay.textContent = Math.round(currentScale * 100) + '%';
                reRenderPages(currentPdfDoc, currentRenderLimit, currentScale);
            });
        }

        if (zoomResetBtn) {
            zoomResetBtn.addEventListener('click', function () {
                const imgEl = document.getElementById('pdfViewerImage');
                if (imgEl) {
                    currentScale = 1.0;
                    if (zoomDisplay) zoomDisplay.textContent = '100%';
                    imgEl.style.transform = 'scale(1)';
                    return;
                }
                const docxEl = document.getElementById('docxRenderWrapper');
                if (docxEl) {
                    currentScale = 1.0;
                    if (zoomDisplay) zoomDisplay.textContent = '100%';
                    docxEl.style.transform = 'scale(1)';
                    docxEl.style.transformOrigin = 'top center';
                    return;
                }
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
                                Anda baru saja membaca pratinjau terbatas <strong>${limit} dari total ${totalPages} halaman</strong> dokumen mutu ini. Untuk mengakses seluruh lembar halaman secara lengkap dan mengunduh berkas resminya, silakan masuk ke sistem SISTA 2 sebagai pengguna terdaftar.
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

                if (window._currentPdfIsReviewMode) {
                    const reviewBox = document.getElementById('pdfReviewActionBox');
                    if (reviewBox) reviewBox.style.display = 'block';
                    const hint = document.getElementById('pdfReviewStatusHint');
                    if (hint) {
                        hint.innerHTML = '<strong class="text-info"><i class="fas fa-check-circle me-1"></i> Halaman terakhir tercapai.</strong> Silakan lengkapi keputusan review di bawah.';
                    }
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

    // Expose loadPdfModalContent globally for auto-preview triggers
    window.loadPdfModalContent = loadPdfModalContent;

    // Deep Linking Auto-Detector on Page Load (?doc_id=XYZ atau ?id=XYZ)
    (function checkUrlDeepLink() {
        const urlParams = new URLSearchParams(window.location.search);
        const autoDocId = urlParams.get('doc_id') || urlParams.get('id');
        if (!autoDocId) return;

        function tryOpenFromDom() {
            const trigger = document.querySelector(`[data-doc-id="${autoDocId}"], [data-id="${autoDocId}"]`);
            if (trigger) {
                try { trigger.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch(e) {}
                trigger.click();
                return true;
            }
            return false;
        }

        setTimeout(() => {
            if (tryOpenFromDom()) return;

            // Fallback: Jika berkas tidak ada di halaman tabel saat ini (misal paginasi atau halaman lain), ambil via API
            const baseUrl = window.BASE_URL || '';
            fetch(`${baseUrl}/api/public/document/${encodeURIComponent(autoDocId)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.success || !data.doc) return;
                    const doc = data.doc;
                    const dummyBtn = document.createElement('button');
                    dummyBtn.setAttribute('data-doc-id', doc.id);
                    dummyBtn.setAttribute('data-pdf-url', doc.file_path);
                    dummyBtn.setAttribute('data-doc-title', doc.file_name || doc.nama_dokumen);
                    dummyBtn.setAttribute('data-doc-ta', doc.tahun_akademik || '');
                    dummyBtn.setAttribute('data-doc-narasi', doc.narasi || '');
                    dummyBtn.setAttribute('data-doc-standar', doc.standar_html || '');
                    dummyBtn.setAttribute('data-public-limit', doc.public_limit || 1);
                    dummyBtn.setAttribute('data-can-download', doc.can_download || 0);
                    dummyBtn.setAttribute('data-file-type', doc.file_type || 'pdf');
                    if (doc.files && doc.files.length > 1) {
                        dummyBtn.setAttribute('data-all-files', JSON.stringify(doc.files));
                    }

                    if (typeof loadPdfModalContent === 'function') {
                        loadPdfModalContent(dummyBtn);
                        if (pdfModal && window.bootstrap && bootstrap.Modal) {
                            const modalInst = bootstrap.Modal.getOrCreateInstance(pdfModal);
                            modalInst.show();
                        }
                    }
                })
                .catch(err => console.warn('Could not auto-load document deep link:', err));
        }, 450);
    })();
});

/**
 * Salin Tautan Kontekstual Dokumen Mutu Langsung ke Clipboard
 */
window.copyDocumentDirectLink = function() {
    let shareUrl = window.location.href;
    if (window._currentOpenDocId) {
        try {
            const u = new URL(shareUrl);
            u.searchParams.set('doc_id', window._currentOpenDocId);
            shareUrl = u.toString();
        } catch(e) {}
    }

    const copyBtn = document.getElementById('pdfCopyLinkBtn');
    const copyIcon = document.getElementById('pdfCopyLinkIcon');
    const copyText = document.getElementById('pdfCopyLinkText');

    function showSuccess() {
        if (copyBtn) {
            copyBtn.classList.remove('btn-outline-light');
            copyBtn.classList.add('btn-success', 'text-white');
        }
        if (copyIcon) copyIcon.className = 'fas fa-check';
        if (copyText) copyText.textContent = 'Link Tersalin!';

        setTimeout(() => {
            if (copyBtn) {
                copyBtn.classList.remove('btn-success', 'text-white');
                copyBtn.classList.add('btn-outline-light');
            }
            if (copyIcon) copyIcon.className = 'fas fa-link';
            if (copyText) copyText.textContent = 'Salin Link';
        }, 2500);
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(shareUrl).then(showSuccess).catch(() => fallbackCopy(shareUrl));
    } else {
        fallbackCopy(shareUrl);
    }

    function fallbackCopy(text) {
        try {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy');
            textArea.remove();
            showSuccess();
        } catch (err) {
            prompt('Salin tautan dokumen berikut:', text);
        }
    }
};

/**
 * Initialize Client-Side Fast Filtering, Search, and Pagination
 */
function initDocumentTable(table) {
    if (table.dataset.ajaxPagination === '1') return;
    const searchInput = document.getElementById('tableSearchInput');
    const bidangFilter = document.getElementById('tableBidangFilter');
    const tahunFilter = document.getElementById('tableTahunFilter');
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

    function updateCycleTabCountsRealTime(baseRows) {
        const counts = {
            '': baseRows.length,
            penetapan: 0,
            pelaksanaan: 0,
            evaluasi: 0,
            pengendalian: 0,
            peningkatan: 0
        };
        baseRows.forEach(row => {
            const s = (row.getAttribute('data-siklus') || '').toLowerCase().trim();
            if (Object.prototype.hasOwnProperty.call(counts, s)) {
                counts[s]++;
            }
        });
        document.querySelectorAll('#ppeppTabs [data-siklus-tab]').forEach(btn => {
            const key = (btn.getAttribute('data-siklus-tab') || '').toLowerCase().trim();
            const badgeEl = btn.querySelector('.tab-cycle-count');
            if (badgeEl && Object.prototype.hasOwnProperty.call(counts, key)) {
                badgeEl.textContent = `(${counts[key].toLocaleString('id-ID')})`;
            }
        });
    }

    function syncCycleColumnVisibility() {
        const showCycleCol = (selectedCycle === '');
        table.querySelectorAll('.col-siklus-ppepp').forEach(el => {
            el.style.display = showCycleCol ? '' : 'none';
        });
        const emptyRow = tbody.querySelector('.no-matching-docs td');
        if (emptyRow) {
            const totalTh = table.querySelectorAll('thead th').length || 6;
            emptyRow.colSpan = showCycleCol ? totalTh : Math.max(1, totalTh - 1);
        }
    }

    window.setProdiPpeppCycleFilter = function(cycle) {
        selectedCycle = (cycle || '').toLowerCase().trim();
        applyFilters();
    };

    window.setProdiPpeppReviewStatusFilter = function(status) {
        selectedReviewStatus = status || '';
        applyFilters();
    };

    function applyFilters() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const selectedBidang = bidangFilter ? bidangFilter.value : '';
        const selectedTahun = tahunFilter ? tahunFilter.value.trim() : '';

        const baseMatches = allRows.filter(row => {
            const title = (row.getAttribute('data-title') || '').toLowerCase();
            const nomor = (row.getAttribute('data-nomor') || '').toLowerCase();
            const bidangIds = (row.getAttribute('data-bidang-id') || '').split(',').map(s => s.trim());
            const tahun = (row.getAttribute('data-tahun-akademik') || '').trim();
            const status = row.getAttribute('data-status-review') || '';
            const textContent = row.textContent.toLowerCase();

            const matchesSearch = !query || title.includes(query) || nomor.includes(query) || textContent.includes(query);
            const matchesBidang = !selectedBidang || bidangIds.includes(selectedBidang);
            const matchesTahun = !selectedTahun || tahun === selectedTahun;
            const matchesStatus = !selectedReviewStatus || status === selectedReviewStatus;

            return matchesSearch && matchesBidang && matchesTahun && matchesStatus;
        });

        updateCycleTabCountsRealTime(baseMatches);

        filteredRows = selectedCycle
            ? baseMatches.filter(row => ((row.getAttribute('data-siklus') || '').toLowerCase().trim() === selectedCycle))
            : baseMatches;

        currentPage = 1;
        syncCycleColumnVisibility();
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
        const totalCols = table.querySelectorAll('thead th').length || 6;
        const activeColSpan = (selectedCycle === '') ? totalCols : Math.max(1, totalCols - 1);
        const emptyRow = tbody.querySelector('.no-matching-docs');
        if (totalRows === 0) {
            if (!emptyRow) {
                const tr = document.createElement('tr');
                tr.className = 'no-matching-docs';
                tr.innerHTML = `<td colspan="${activeColSpan}" class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 text-secondary d-block"></i>
                    <h6 class="fw-bold">Tidak ada dokumen yang sesuai</h6>
                    <p class="small mb-0">Coba ubah kata kunci pencarian atau filter yang dipilih.</p>
                </td>`;
                tbody.appendChild(tr);
            } else {
                const emptyTd = emptyRow.querySelector('td');
                if (emptyTd) emptyTd.colSpan = activeColSpan;
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
    if (tahunFilter) {
        tahunFilter.addEventListener('change', applyFilters);
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
