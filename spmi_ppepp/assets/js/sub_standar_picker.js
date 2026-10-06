/**
 * Standar Mutu Interactive Multi-Picker (Dinamis per Bidang)
 * SPMI PPEPP UNIKA Soegijapranata
 */

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Render template picker standar mutu untuk berkas / tautan
 */
function renderSubStandarPicker(inputName, selectedSubIds = [], uniqueKey = '', currentBidangId = null) {
    if (!uniqueKey) uniqueKey = 'picker_' + Math.random().toString(36).substring(2, 9);
    selectedSubIds = (selectedSubIds || []).map(id => parseInt(id)).filter(id => !isNaN(id) && id > 0);

    const bData = (typeof BIDANGS_DATA !== 'undefined' && Array.isArray(BIDANGS_DATA)) ? BIDANGS_DATA : [];
    let sbData = (typeof SUB_BIDANGS_DATA !== 'undefined' && Array.isArray(SUB_BIDANGS_DATA)) ? SUB_BIDANGS_DATA : [];

    const parsedBidangId = (currentBidangId && parseInt(currentBidangId) > 0) ? parseInt(currentBidangId) : null;

    // Filter standards if bidang is specified
    if (parsedBidangId) {
        sbData = sbData.filter(s => parseInt(s.bidang_id) === parsedBidangId);
        const validIds = sbData.map(s => parseInt(s.id));
        selectedSubIds = selectedSubIds.filter(id => validIds.includes(id));
    } else {
        selectedSubIds = [];
    }

    // Build badge pills HTML for currently selected
    let badgesHtml = '';
    selectedSubIds.forEach(sId => {
        const sb = sbData.find(s => parseInt(s.id) === sId);
        if (sb) {
            const name = escapeHtml(sb.nama_sub_bidang);
            badgesHtml += `
                <span class="badge rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5 border shadow-2xs me-1 mb-1 text-wrap text-start lh-sm" 
                      style="background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE !important; font-size: 0.78rem; font-weight: 600; max-width: 100%; word-break: break-word;" 
                      id="badge_${uniqueKey}_${sb.id}" title="${name}">
                    <i class="fas fa-bookmark text-primary opacity-75 flex-shrink-0" style="font-size: 0.7rem;"></i>
                    <span>${name}</span>
                    <i class="fas fa-xmark cursor-pointer text-muted hover-danger ms-1 flex-shrink-0" 
                       onclick="removeSubStandarTag('${uniqueKey}', ${sb.id})" title="Hapus standar ini"></i>
                </span>
            `;
        }
    });

    // Build dropdown list
    let optionsHtml = '';
    if (!parsedBidangId) {
        optionsHtml = `
            <div class="px-3 py-3 text-center text-muted small fst-italic no-bidang-notice">
                <i class="fas fa-arrow-left text-primary me-1"></i> Silakan pilih Bidang pada dokumen terlebih dahulu
            </div>
        `;
    } else {
        const curBidang = bData.find(b => parseInt(b.id) === parsedBidangId);
        const bName = curBidang ? curBidang.nama_bidang : 'Bidang Terpilih';
        if (sbData.length > 0) {
            optionsHtml += `
                <div class="px-2.5 py-1.5 mt-1 mb-1 fw-bold text-dark-blue rounded bg-light border-bottom d-flex align-items-center justify-content-between" style="font-size: 0.76rem; letter-spacing: 0.2px;">
                    <span class="text-truncate"><i class="fas fa-layer-group text-primary me-1"></i> ${escapeHtml(bName)}</span>
                    <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill" style="font-size: 0.68rem;">${sbData.length} Standar</span>
                </div>
            `;
            sbData.forEach(sb => {
                const isChecked = selectedSubIds.includes(parseInt(sb.id));
                const searchStr = `${sb.nama_sub_bidang || ''} ${bName}`.toLowerCase();
                optionsHtml += `
                    <label class="dropdown-item d-flex align-items-start gap-2 py-2 px-2.5 rounded cursor-pointer sub-option-item" 
                           style="font-size: 0.8rem; white-space: normal;" 
                           data-search-text="${escapeHtml(searchStr)}">
                        <input type="checkbox" class="form-check-input flex-shrink-0 mt-0.5 sub-check-input" 
                               name="${inputName}" value="${sb.id}" ${isChecked ? 'checked' : ''} 
                               onchange="handleSubStandarCheckChange('${uniqueKey}', this, ${sb.id}, '', '${escapeHtml(sb.nama_sub_bidang)}')">
                        <span class="lh-sm text-dark">${escapeHtml(sb.nama_sub_bidang)}</span>
                    </label>
                `;
            });
        } else {
            optionsHtml = `
                <div class="px-3 py-3 text-center text-muted small fst-italic">
                    Belum ada standar mutu yang terdaftar untuk bidang ini.
                </div>
            `;
        }
    }

    const noSubPlaceholder = '<span class="text-muted fst-italic no-sub-placeholder" style="font-size: 0.78rem;">Belum ada standar dipilih (Bisa pilih 1 atau lebih)</span>';
    const buttonLabel = parsedBidangId
        ? '<i class="fas fa-plus-circle text-primary me-1.5"></i> Pilih / Tambah Standar...'
        : '<i class="fas fa-info-circle text-muted me-1.5"></i> Pilih Bidang terlebih dahulu...';

    return `
        <div class="sub-standar-picker-wrap p-3 rounded-3 bg-white border shadow-2xs mt-2" 
             id="picker_wrap_${uniqueKey}" 
             data-picker-key="${uniqueKey}" 
             data-input-name="${inputName}" 
             data-current-bidang="${parsedBidangId || ''}">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-label fw-bold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                    <i class="fas fa-tags text-primary"></i>
                    <span>Standar Mutu Berkas Ini: <span class="text-danger">*</span></span>
                </label>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bisa > 1 Standar</span>
            </div>

            <!-- Selected Tags List -->
            <div class="selected-sub-badges d-flex flex-wrap gap-1 mb-2" id="badges_container_${uniqueKey}">
                ${badgesHtml || noSubPlaceholder}
            </div>

            <!-- Dropdown Selector Button -->
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-start d-flex justify-content-between align-items-center py-2 px-3 rounded-3 dropdown-toggle-custom" 
                        id="btn_toggle_${uniqueKey}"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="font-size: 0.82rem; border-style: dashed; border-width: 1.5px;">
                    <span class="text-truncate text-secondary dropdown-label-text">
                        ${buttonLabel}
                    </span>
                    <i class="fas fa-chevron-down text-muted ms-1" style="font-size: 0.75rem;"></i>
                </button>
                <div class="dropdown-menu p-2.5 shadow-lg border rounded-3 w-100" style="max-height: 300px; overflow-y: auto; font-size: 0.8rem; z-index: 1060;">
                    <div class="px-1 pb-2 mb-1.5 border-bottom">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted px-2.5"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control py-1 px-2.5" placeholder="Cari nama standar..." onkeyup="filterSubStandarList('${uniqueKey}', this.value)">
                        </div>
                    </div>
                    <div class="sub-standar-options-list" id="options_list_${uniqueKey}">
                        ${optionsHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Update opsi dan pilihan standar mutu pada suatu picker secara dinamis ketika Bidang berubah
 */
function updateSubStandarPickerOptions(uniqueKey, newBidangId, inputName) {
    const wrap = document.getElementById(`picker_wrap_${uniqueKey}`);
    if (!wrap) return;

    if (!inputName) {
        inputName = wrap.getAttribute('data-input-name') || '';
    }

    const bData = (typeof BIDANGS_DATA !== 'undefined' && Array.isArray(BIDANGS_DATA)) ? BIDANGS_DATA : [];
    const sbData = (typeof SUB_BIDANGS_DATA !== 'undefined' && Array.isArray(SUB_BIDANGS_DATA)) ? SUB_BIDANGS_DATA : [];

    const parsedBidangId = (newBidangId && parseInt(newBidangId) > 0) ? parseInt(newBidangId) : null;
    wrap.setAttribute('data-current-bidang', parsedBidangId || '');

    const container = document.getElementById(`badges_container_${uniqueKey}`);
    const optionsContainer = document.getElementById(`options_list_${uniqueKey}`);
    const toggleBtn = document.getElementById(`btn_toggle_${uniqueKey}`);
    const labelSpan = toggleBtn ? toggleBtn.querySelector('.dropdown-label-text') : null;

    // Bersihkan badge yang tidak cocok dengan bidang baru
    if (container) {
        const validSubs = parsedBidangId ? sbData.filter(s => parseInt(s.bidang_id) === parsedBidangId) : [];
        const validIds = validSubs.map(s => parseInt(s.id));

        const badges = container.querySelectorAll('.badge');
        badges.forEach(b => {
            const badgeIdMatch = b.id.match(new RegExp(`badge_${uniqueKey}_(\\d+)`));
            if (badgeIdMatch) {
                const sId = parseInt(badgeIdMatch[1]);
                if (!validIds.includes(sId)) {
                    b.remove();
                }
            }
        });

        if (container.querySelectorAll('.badge').length === 0) {
            container.innerHTML = '<span class="text-muted fst-italic no-sub-placeholder" style="font-size: 0.78rem;">Belum ada standar dipilih (Bisa pilih 1 atau lebih)</span>';
        }
    }

    // Bangun ulang opsi dropdown
    let newOptionsHtml = '';
    if (!parsedBidangId) {
        newOptionsHtml = `
            <div class="px-3 py-3 text-center text-muted small fst-italic no-bidang-notice">
                <i class="fas fa-arrow-left text-primary me-1"></i> Silakan pilih Bidang pada dokumen terlebih dahulu
            </div>
        `;
        if (labelSpan) {
            labelSpan.innerHTML = '<i class="fas fa-info-circle text-muted me-1.5"></i> Pilih Bidang terlebih dahulu...';
        }
    } else {
        const curBidang = bData.find(b => parseInt(b.id) === parsedBidangId);
        const bName = curBidang ? curBidang.nama_bidang : 'Bidang Terpilih';
        const filteredSubs = sbData.filter(s => parseInt(s.bidang_id) === parsedBidangId);

        if (filteredSubs.length > 0) {
            newOptionsHtml += `
                <div class="px-2.5 py-1.5 mt-1 mb-1 fw-bold text-dark-blue rounded bg-light border-bottom d-flex align-items-center justify-content-between" style="font-size: 0.76rem; letter-spacing: 0.2px;">
                    <span class="text-truncate"><i class="fas fa-layer-group text-primary me-1"></i> ${escapeHtml(bName)}</span>
                    <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill" style="font-size: 0.68rem;">${filteredSubs.length} Standar</span>
                </div>
            `;
            filteredSubs.forEach(sb => {
                const searchStr = `${sb.nama_sub_bidang || ''} ${bName}`.toLowerCase();
                newOptionsHtml += `
                    <label class="dropdown-item d-flex align-items-start gap-2 py-2 px-2.5 rounded cursor-pointer sub-option-item" 
                           style="font-size: 0.8rem; white-space: normal;" 
                           data-search-text="${escapeHtml(searchStr)}">
                        <input type="checkbox" class="form-check-input flex-shrink-0 mt-0.5 sub-check-input" 
                               name="${inputName}" value="${sb.id}" 
                               onchange="handleSubStandarCheckChange('${uniqueKey}', this, ${sb.id}, '', '${escapeHtml(sb.nama_sub_bidang)}')">
                        <span class="lh-sm text-dark">${escapeHtml(sb.nama_sub_bidang)}</span>
                    </label>
                `;
            });
        } else {
            newOptionsHtml = `
                <div class="px-3 py-3 text-center text-muted small fst-italic">
                    Belum ada standar mutu yang terdaftar untuk bidang ini.
                </div>
            `;
        }

        if (labelSpan) {
            labelSpan.innerHTML = '<i class="fas fa-plus-circle text-primary me-1.5"></i> Pilih / Tambah Standar...';
        }
    }

    if (optionsContainer) {
        optionsContainer.innerHTML = newOptionsHtml;
    }
}

/**
 * Perbarui seluruh picker standar mutu di dalam suatu container/elemen (kartu atau form)
 */
function updateSubStandarPickersInContainer(containerOrSelector, newBidangId) {
    let container = null;
    if (typeof containerOrSelector === 'string') {
        container = document.querySelector(containerOrSelector);
    } else if (containerOrSelector instanceof HTMLElement) {
        container = containerOrSelector;
    }
    if (!container) return;

    const pickers = container.querySelectorAll('.sub-standar-picker-wrap');
    pickers.forEach(picker => {
        const key = picker.getAttribute('data-picker-key') || picker.id.replace('picker_wrap_', '');
        const inputName = picker.getAttribute('data-input-name') || '';
        updateSubStandarPickerOptions(key, newBidangId, inputName);
    });
}

function handleSubStandarCheckChange(uniqueKey, checkbox, subId, subCode, subName) {
    const container = document.getElementById(`badges_container_${uniqueKey}`);
    if (!container) return;
    const placeholder = container.querySelector('.no-sub-placeholder');
    if (placeholder) placeholder.remove();

    const existingBadge = document.getElementById(`badge_${uniqueKey}_${subId}`);
    if (checkbox.checked) {
        if (!existingBadge) {
            const badge = document.createElement('span');
            badge.className = 'badge rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5 border shadow-2xs me-1 mb-1 text-wrap text-start lh-sm';
            badge.style.cssText = 'background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE !important; font-size: 0.78rem; font-weight: 600; max-width: 100%; word-break: break-word;';
            badge.id = `badge_${uniqueKey}_${subId}`;
            badge.title = subName;
            badge.innerHTML = `
                <i class="fas fa-bookmark text-primary opacity-75 flex-shrink-0" style="font-size: 0.7rem;"></i>
                <span>${escapeHtml(subName)}</span>
                <i class="fas fa-xmark cursor-pointer text-muted hover-danger ms-1 flex-shrink-0" 
                   onclick="removeSubStandarTag('${uniqueKey}', ${subId})" title="Hapus standar ini"></i>
            `;
            container.appendChild(badge);
        }
    } else {
        if (existingBadge) existingBadge.remove();
        if (container.querySelectorAll('.badge').length === 0) {
            container.innerHTML = '<span class="text-muted fst-italic no-sub-placeholder" style="font-size: 0.78rem;">Belum ada standar dipilih (Bisa pilih 1 atau lebih)</span>';
        }
    }
}

function removeSubStandarTag(uniqueKey, subId) {
    const badge = document.getElementById(`badge_${uniqueKey}_${subId}`);
    if (badge) badge.remove();
    const checkbox = document.querySelector(`#options_list_${uniqueKey} input[value="${subId}"]`);
    if (checkbox) checkbox.checked = false;
    const container = document.getElementById(`badges_container_${uniqueKey}`);
    if (container && container.querySelectorAll('.badge').length === 0) {
        container.innerHTML = '<span class="text-muted fst-italic no-sub-placeholder" style="font-size: 0.78rem;">Belum ada standar dipilih (Bisa pilih 1 atau lebih)</span>';
    }
}

function filterSubStandarList(uniqueKey, query) {
    query = (query || '').toLowerCase().trim();
    const items = document.querySelectorAll(`#options_list_${uniqueKey} .sub-option-item`);
    items.forEach(item => {
        const text = item.getAttribute('data-search-text') || '';
        item.style.display = text.includes(query) ? 'flex' : 'none';
    });
}
