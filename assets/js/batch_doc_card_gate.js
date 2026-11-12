/**
 * Batch Document Card Gatekeeper (Prodi & Dekanat)
 * Memastikan seluruh isian wajib kartu dokumen yang ada telah terisi lengkap
 * sebelum tombol "+ Tambah Kartu Dokumen" dapat digunakan.
 */
(function(window, document) {
    'use strict';

    function escapeTextHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function isValidExternalDocUrl(rawUrl) {
        const val = (rawUrl || '').trim();
        if (!val) {
            return { valid: false, reason: 'URL Tautan GDrive' };
        }
        if (/\/folders\//i.test(val) || /drive\/.*folders/i.test(val)) {
            return { valid: false, reason: 'URL Tautan bukan link folder' };
        }
        if (!/^https?:\/\/\S+/i.test(val)) {
            return { valid: false, reason: 'Format URL Tautan valid (https://...)' };
        }
        if (/(drive|docs)\.google\.com/i.test(val)) {
            const isDocPattern = /\/(file|document|spreadsheets|presentation|forms|drawings)\/d\/[a-zA-Z0-9_-]+/i.test(val)
                || /drive\.google\.com\/open\?id=[a-zA-Z0-9_-]+/i.test(val)
                || /drive\.google\.com\/uc\?id=[a-zA-Z0-9_-]+/i.test(val);
            if (!isDocPattern) {
                return { valid: false, reason: 'URL Tautan Google Drive harus link file' };
            }
        }
        return { valid: true, reason: '' };
    }

    function validateBatchCardElement(cardEl, displayNumber) {
        const idx = cardEl.getAttribute('data-card-idx');
        const missing = [];
        let firstFocusTarget = null;

        function addMissing(label, targetEl) {
            if (!missing.includes(label)) {
                missing.push(label);
            }
            if (!firstFocusTarget && targetEl) {
                firstFocusTarget = targetEl;
            }
        }

        // 1. Bagian 1: Identitas & Klasifikasi
        const namaInp = cardEl.querySelector(`input[name="docs[${idx}][nama_dokumen]"]`);
        if (!namaInp || !(namaInp.value || '').trim()) {
            addMissing('Nama Dokumen Mutu', namaInp || cardEl);
        }

        const tglInp = cardEl.querySelector(`input[name="docs[${idx}][tanggal_dokumen]"]`);
        if (!tglInp || !(tglInp.value || '').trim()) {
            addMissing('Tanggal Dokumen / SK', tglInp || cardEl);
        }

        const taInp = cardEl.querySelector(`input[name="docs[${idx}][tahun_akademik]"]`);
        if (!taInp || !(taInp.value || '').trim()) {
            addMissing('Tahun Akademik', taInp || cardEl);
        }

        const checkedCycle = cardEl.querySelector(`input[name="docs[${idx}][siklus]"]:checked`);
        if (!checkedCycle) {
            addMissing('Kategori Siklus PPEPP', cardEl.querySelector('.ppepp-cycle-grid') || cardEl);
        }

        const bidangSel = cardEl.querySelector(`select[name="docs[${idx}][bidang_id]"]`);
        if (!bidangSel || !(bidangSel.value || '').trim()) {
            addMissing('Bidang', bidangSel || cardEl);
        }

        // 2. Bagian 2: Folder Berkas & Tautan Lampiran
        const folderBoxes = cardEl.querySelectorAll(`[id^="folder_box_${idx}_"]`);
        if (folderBoxes.length === 0) {
            addMissing('Folder & Lampiran Berkas/Tautan', cardEl);
        } else {
            folderBoxes.forEach(folderBox => {
                const folderNameInp = folderBox.querySelector('.folder-name-input');
                const folderName = folderNameInp ? (folderNameInp.value || '').trim() : 'Folder';
                if (folderNameInp && !folderName) {
                    addMissing('Nama Folder', folderNameInp);
                }

                const folderFiles = folderBox.querySelectorAll(`[id^="file_row_${idx}_"]`);
                const folderLinks = folderBox.querySelectorAll(`[id^="link_row_${idx}_"]`);
                if (folderFiles.length + folderLinks.length === 0) {
                    const addFileBtn = folderBox.querySelector('button[onclick*="triggerAddFolderFiles"]');
                    const emptyLabel = folderBoxes.length > 1
                        ? `Berkas/Tautan di "${folderName || 'Folder'}" (atau hapus folder kosong)`
                        : 'Minimal 1 Berkas Dokumen / Link GDrive';
                    addMissing(emptyLabel, addFileBtn || folderBox);
                }
            });
        }

        // 3. Validasi setiap item File yang sudah ditambahkan
        cardEl.querySelectorAll(`[id^="file_row_${idx}_"]`).forEach(fileRow => {
            const narasiInp = fileRow.querySelector('textarea.file-narasi-inp');
            if (!narasiInp || !(narasiInp.value || '').trim()) {
                addMissing('Narasi / Keterangan Berkas', narasiInp || fileRow);
            }

            const pickerWrap = fileRow.querySelector('.sub-standar-picker-wrap');
            if (pickerWrap) {
                const allSubOpts = pickerWrap.querySelectorAll('.sub-check-input');
                const checkedSubOpts = pickerWrap.querySelectorAll('.sub-check-input:checked');
                if (allSubOpts.length > 0 && checkedSubOpts.length === 0) {
                    addMissing('Standar Mutu Berkas', pickerWrap.querySelector('.dropdown-toggle-custom') || pickerWrap);
                }
            }

            const limitSw = fileRow.querySelector('input.file-limit-sw');
            const limitValInp = fileRow.querySelector('input.file-limit-val');
            if (limitSw && limitSw.checked && limitValInp) {
                const nVal = parseInt(limitValInp.value, 10);
                if (isNaN(nVal) || nVal < 1) {
                    addMissing('Jumlah Halaman Pratinjau Berkas', limitValInp);
                }
            }
        });

        // 4. Validasi setiap item Link GDrive yang sudah ditambahkan
        cardEl.querySelectorAll(`[id^="link_row_${idx}_"]`).forEach(linkRow => {
            const urlInp = linkRow.querySelector('input.gdrive-doc-input');
            const urlCheck = isValidExternalDocUrl(urlInp ? urlInp.value : '');
            if (!urlCheck.valid) {
                addMissing(urlCheck.reason, urlInp || linkRow);
            }

            const narasiInp = linkRow.querySelector('textarea.link-narasi-inp');
            if (!narasiInp || !(narasiInp.value || '').trim()) {
                addMissing('Narasi / Keterangan Tautan', narasiInp || linkRow);
            }

            const pickerWrap = linkRow.querySelector('.sub-standar-picker-wrap');
            if (pickerWrap) {
                const allSubOpts = pickerWrap.querySelectorAll('.sub-check-input');
                const checkedSubOpts = pickerWrap.querySelectorAll('.sub-check-input:checked');
                if (allSubOpts.length > 0 && checkedSubOpts.length === 0) {
                    addMissing('Standar Mutu Tautan', pickerWrap.querySelector('.dropdown-toggle-custom') || pickerWrap);
                }
            }

            const limitSw = linkRow.querySelector('input.link-limit-sw');
            const limitValInp = linkRow.querySelector('input.link-limit-val');
            if (limitSw && limitSw.checked && limitValInp) {
                const nVal = parseInt(limitValInp.value, 10);
                if (isNaN(nVal) || nVal < 1) {
                    addMissing('Jumlah Halaman Pratinjau Tautan', limitValInp);
                }
            }
        });

        return {
            valid: missing.length === 0,
            idx,
            displayNumber,
            missing,
            firstFocusTarget
        };
    }

    window.validateAllBatchCardsComplete = function(maxCards = 10) {
        const cards = document.querySelectorAll('#batchCardsContainer .doc-batch-card');
        if (!cards || cards.length === 0) {
            return {
                valid: true,
                cardCount: 0,
                reasonType: null,
                firstInvalid: null,
                message: ''
            };
        }
        if (cards.length >= maxCards) {
            return {
                valid: false,
                cardCount: cards.length,
                reasonType: 'max_limit',
                firstInvalid: null,
                message: `Maksimal pengunggahan sekaligus adalah ${maxCards} dokumen.`
            };
        }

        let firstInvalid = null;
        cards.forEach((cardEl, pos) => {
            const res = validateBatchCardElement(cardEl, pos + 1);
            if (!res.valid && !firstInvalid) {
                firstInvalid = res;
            }
        });

        if (firstInvalid) {
            return {
                valid: false,
                cardCount: cards.length,
                reasonType: 'incomplete',
                firstInvalid,
                message: `Harap lengkapi seluruh isian wajib pada Dokumen #${firstInvalid.displayNumber} terlebih dahulu:\n\n- ${firstInvalid.missing.join('\n- ')}`
            };
        }

        return {
            valid: true,
            cardCount: cards.length,
            reasonType: null,
            firstInvalid: null,
            message: ''
        };
    };

    window.refreshBatchAddCardState = function() {
        const btn = document.getElementById('btnAddDocCard');
        const hint = document.getElementById('addCardRequirementHint');
        if (!btn) return;

        const check = window.validateAllBatchCardsComplete(10);
        if (check.valid) {
            btn.disabled = false;
            btn.classList.remove('btn-secondary', 'opacity-75');
            btn.classList.add('btn-primary', 'bg-scu-blue');
            btn.style.cursor = '';
            btn.title = 'Tambahkan kartu dokumen baru';
            if (hint) {
                hint.classList.add('d-none');
                hint.innerHTML = '';
            }
            return;
        }

        btn.disabled = true;
        btn.classList.remove('btn-primary', 'bg-scu-blue');
        btn.classList.add('btn-secondary', 'opacity-75');
        btn.style.cursor = 'not-allowed';

        if (check.reasonType === 'max_limit') {
            btn.title = check.message;
            if (hint) {
                hint.classList.remove('d-none');
                hint.innerHTML = `<i class="fas fa-circle-info me-1"></i>Batas maksimal 10 kartu dokumen telah tercapai.`;
            }
        } else if (check.firstInvalid) {
            const inv = check.firstInvalid;
            const summaryItems = inv.missing.slice(0, 3).join(', ')
                + (inv.missing.length > 3 ? ` (+${inv.missing.length - 3} lainnya)` : '');
            btn.title = `Lengkapi Dokumen #${inv.displayNumber}: ${inv.missing.join(', ')}`;
            if (hint) {
                hint.classList.remove('d-none');
                hint.innerHTML = `<i class="fas fa-circle-info me-1"></i>Lengkapi <strong>Dokumen #${inv.displayNumber}</strong> dulu: ${escapeTextHtml(summaryItems)}`;
            }
        }
    };

    window.focusFirstIncompleteBatchField = function(checkResult) {
        if (!checkResult || !checkResult.firstInvalid) return;
        const inv = checkResult.firstInvalid;
        const body = document.getElementById(`cardBody_${inv.idx}`);
        const collapseBtn = document.getElementById(`collapseBtn_${inv.idx}`);
        if (body && body.style.display === 'none') {
            body.style.display = 'block';
            if (collapseBtn) collapseBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
        }
        const target = inv.firstFocusTarget;
        if (target && typeof target.scrollIntoView === 'function') {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (typeof target.focus === 'function' && /^(INPUT|SELECT|TEXTAREA|BUTTON)$/i.test(target.tagName)) {
                setTimeout(() => {
                    try { target.focus(); } catch (e) {}
                }, 200);
            }
        }
    };

    window.canProceedAddDocCard = function() {
        const cards = document.querySelectorAll('#batchCardsContainer .doc-batch-card');
        if (!cards || cards.length === 0) return true;

        const check = window.validateAllBatchCardsComplete(10);
        window.refreshBatchAddCardState();
        if (!check.valid) {
            window.focusFirstIncompleteBatchField(check);
            alert(check.message);
            return false;
        }
        return true;
    };

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('batchCardsContainer');
        if (container) {
            ['input', 'change', 'focusout'].forEach(evtName => {
                container.addEventListener(evtName, function() {
                    window.refreshBatchAddCardState();
                });
            });
            setTimeout(function() {
                window.refreshBatchAddCardState();
            }, 0);
        }
    });
})(window, document);

