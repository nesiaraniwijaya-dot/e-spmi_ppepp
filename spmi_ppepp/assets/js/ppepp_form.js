/**
 * PPEPP Document Upload Form Logic (Conditional Rendering & Validation)
 * SPMI PPEPP UNIKA Soegijapranata
 */

document.addEventListener('DOMContentLoaded', function () {
    const uploadTypeRadios = document.querySelectorAll('input[name="jenis_upload"]');
    const fileContainer = document.getElementById('fileUploadContainer');
    const linkContainer = document.getElementById('linkInputContainer');
    const fileInput = document.getElementById('docFileInput');
    const linkInput = document.getElementById('docLinkInput');

    function toggleUploadFields() {
        const selectedType = document.querySelector('input[name="jenis_upload"]:checked')?.value || 'file';

        if (selectedType === 'file') {
            if (fileContainer) {
                fileContainer.style.display = 'block';
                fileContainer.classList.add('animate-fade-in');
            }
            if (linkContainer) {
                linkContainer.style.display = 'none';
            }
            if (fileInput && !fileInput.getAttribute('data-has-existing')) {
                fileInput.required = true;
            }
            if (linkInput) {
                linkInput.required = false;
            }
        } else {
            if (fileContainer) {
                fileContainer.style.display = 'none';
            }
            if (linkContainer) {
                linkContainer.style.display = 'block';
                linkContainer.classList.add('animate-fade-in');
            }
            if (fileInput) {
                fileInput.required = false;
            }
            if (linkInput) {
                linkInput.required = true;
            }
        }
    }

    if (uploadTypeRadios.length > 0) {
        uploadTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleUploadFields);
        });
        // Initial state
        toggleUploadFields();
    }

    // Date range validation & conditional requirement
    const startDateInput = document.getElementById('tanggal_berlaku_mulai');
    const endDateInput = document.getElementById('tanggal_berlaku_selesai');
    const startBadge = document.getElementById('badge_mulai_req');
    const endBadge = document.getElementById('badge_selesai_req');

    function syncDateRequirements() {
        if (!startDateInput || !endDateInput) return;
        const sVal = startDateInput.value.trim();
        const eVal = endDateInput.value.trim();

        if (sVal !== '') {
            endDateInput.required = true;
            if (endBadge) endBadge.innerHTML = '<span class="text-danger fw-bold">* (Wajib diisi)</span>';
            endDateInput.min = sVal;
            if (eVal && sVal > eVal) {
                endDateInput.value = sVal;
            }
        } else {
            if (eVal !== '') {
                startDateInput.required = true;
                if (startBadge) startBadge.innerHTML = '<span class="text-danger fw-bold">* (Wajib diisi)</span>';
                endDateInput.required = true;
                if (endBadge) endBadge.innerHTML = '<span class="text-danger fw-bold">* (Wajib diisi)</span>';
            } else {
                startDateInput.required = false;
                endDateInput.required = false;
                if (startBadge) startBadge.innerHTML = '<span class="text-muted fw-normal">(Opsional)</span>';
                if (endBadge) endBadge.innerHTML = '<span class="text-muted fw-normal">(Opsional)</span>';
                endDateInput.removeAttribute('min');
            }
        }
    }

    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('input', syncDateRequirements);
        startDateInput.addEventListener('change', syncDateRequirements);
        endDateInput.addEventListener('input', syncDateRequirements);
        endDateInput.addEventListener('change', syncDateRequirements);

        endDateInput.addEventListener('change', function () {
            if (startDateInput.value && this.value < startDateInput.value) {
                alert('Peringatan: Tanggal selesai masa berlaku tidak boleh lebih awal dari tanggal mulai.');
                this.value = startDateInput.value;
            }
        });

        syncDateRequirements();
    }

    // Drag & Drop visual feedback for file input
    const dropZone = document.getElementById('fileDropZone');
    if (dropZone && fileInput) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('border-primary', 'bg-light');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('border-primary', 'bg-light');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateFileNameDisplay(files[0]);
            }
        });

        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                updateFileNameDisplay(this.files[0]);
            }
        });
    }

    function updateFileNameDisplay(file) {
        const displayElem = document.getElementById('selectedFileNameDisplay');
        if (displayElem) {
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            displayElem.innerHTML = `
                <div class="alert alert-success d-flex align-items-center justify-content-between p-2 mt-2 mb-0">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-pdf text-danger fa-lg"></i>
                        <div>
                            <div class="fw-bold small">${file.name}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">${sizeInMB} MB</div>
                        </div>
                    </div>
                    <span class="badge bg-success">Siap Diunggah</span>
                </div>
            `;
        }
    }
});
