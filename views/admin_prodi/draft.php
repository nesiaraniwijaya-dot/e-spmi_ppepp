<?php
/**
 * Admin Prodi: Draf Dokumen Mutu (Belum Diajukan ke LPM)
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/admin_header.php';

$documents = $documents ?? [];
$totalDraft = $totalDraft ?? count($documents);
?>

<div class="container-fluid p-0 admin-container">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1.5">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #EEF2F6; color: #334155; border: 1px solid #CBD5E1; font-size: 0.76rem;">
                    <i class="fas fa-file-pen text-primary"></i> Dokumen Belum Diajukan
                </span>
                <span class="text-muted small">&bull;</span>
                <span class="text-secondary small fw-medium d-inline-flex align-items-center gap-1">
                    <i class="fas fa-building-columns text-muted"></i> <?= htmlspecialchars($prodi['nama_fakultas'] ?? '') ?>
                </span>
            </div>
            <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: -0.3px;">Draf Dokumen Mutu</h3>
            <p class="text-muted small mb-0" style="line-height: 1.5;">
                Kelola berkas draf dokumen mutu <strong><?= htmlspecialchars($prodi['nama_prodi']) ?></strong> sebelum diajukan secara resmi ke Lembaga Penjaminan Mutu (LPM).
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('prodi/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 shadow-sm bg-scu-blue border-0 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-plus"></i> Unggah Dokumen Baru
            </a>
            <a href="<?= base_url('prodi/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-2 shadow-2xs fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                <i class="fas fa-folder-open"></i> Daftar Dokumen Terpublikasi
            </a>
        </div>
    </div>

    <!-- Informative Alert Callout -->
    <div class="alert rounded-4 p-4 mb-4 d-flex align-items-start gap-3.5 bg-white shadow-2xs" style="border: 1px solid #E2E8F0; border-left: 5px solid #2563EB !important;">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.1rem; background: #EFF6FF; color: #2563EB;">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div class="small flex-grow-1" style="line-height: 1.65; color: #475569;">
            <strong class="text-dark d-block mb-1" style="font-size: 0.92rem;">Tentang Ruang Kerja Draf Dokumen:</strong>
            Dokumen yang tersimpan sebagai draf bersifat rahasia internal program studi dan <strong>belum dapat dilihat oleh pengunjung umum ataupun Tim Penjaminan Mutu (LPM)</strong>. Anda dapat mencicil isian formulir serta melengkapi berkas lampiran kapan saja, lalu menekan tombol <strong>Ajukan ke LPM</strong> saat berkas telah lengkap dan siap diverifikasi.
        </div>
    </div>

    <?php 
    $readyCount = 0;
    $incompleteCount = 0;
    foreach ($documents as $d) {
        $hasFiles = count($d['files'] ?? []) > 0 || !empty($d['external_link']) || !empty($d['file_path']);
        if ($hasFiles) {
            $readyCount++;
        } else {
            $incompleteCount++;
        }
    }
    ?>

    <?php if (empty($documents)): ?>
        <!-- Empty State -->
        <div class="card rounded-4 p-5 text-center bg-white shadow-2xs" style="border: 1px solid #CBD5E1 !important;">
            <div class="py-4">
                <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                    <i class="fas fa-file-circle-check text-secondary"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Tidak Ada Dokumen Draf</h5>
                <p class="text-muted small mx-auto mb-4" style="max-width: 480px; line-height: 1.5;">
                    Saat ini tidak ada draf dokumen mutu yang tersimpan. Saat mengunggah dokumen baru, Anda dapat memilih opsi <strong>"Simpan sebagai Draf"</strong> jika berkas masih dalam tahap penyusunan.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= base_url('prodi/dokumen/create') ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 bg-scu-blue border-0 shadow-xs fw-semibold">
                        <i class="fas fa-plus me-1.5"></i> Buat Dokumen Baru
                    </a>
                    <a href="<?= base_url('prodi/dokumen') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2 fw-semibold">
                        <i class="fas fa-folder-open me-1.5"></i> Buka Dokumen PPEPP
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Sekat Kartu Ringkasan Draf (SaaS Proportional Height & Breathing Room) -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="saas-stat-card" style="border-top: 4px solid #002B49 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="saas-stat-label text-dark-blue">Total Draf Dokumen</span>
                        <div class="saas-stat-icon-wrap" style="background: #EEF2F6; color: #002B49;">
                            <i class="fas fa-file-pen"></i>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="saas-stat-number text-dark-blue"><?= $totalDraft ?></span>
                            <span class="badge rounded-pill bg-light text-secondary border px-2.5 py-0.5" style="font-size: 0.72rem;">Draf Internal</span>
                        </div>
                        <div class="saas-stat-help">Tersimpan di ruang kerja draf prodi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="saas-stat-card" style="border-top: 4px solid #16A34A !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="saas-stat-label text-success">Siap Diajukan ke LPM</span>
                        <div class="saas-stat-icon-wrap" style="background: #DCFCE7; color: #16A34A;">
                            <i class="fas fa-file-circle-check"></i>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="saas-stat-number text-success"><?= $readyCount ?></span>
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-0.5" style="font-size: 0.72rem;">Berkas Siap</span>
                        </div>
                        <div class="saas-stat-help">Berkas lampiran / tautan telah lengkap</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="saas-stat-card" style="border-top: 4px solid #D97706 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="saas-stat-label" style="color: #D97706;">Berkas Belum Lengkap</span>
                        <div class="saas-stat-icon-wrap" style="background: #FEF3C7; color: #D97706;">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="saas-stat-number" style="color: #D97706;"><?= $incompleteCount ?></span>
                            <span class="badge rounded-pill px-2.5 py-0.5" style="background: #FEF3C7 !important; color: #92400E !important; border: 1px solid #FCD34D !important; font-size: 0.72rem; font-weight: 700;">Perlu Lampiran</span>
                        </div>
                        <div class="saas-stat-help">Memerlukan berkas dokumen atau tautan GDrive</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Table Draf Bersekat Tegas & Berstandar Web SaaS -->
        <div class="modern-table-card mb-4">
            <div class="p-3.5 px-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #002B49; color: #FFFFFF; font-size: 0.8rem;">
                        <i class="fas fa-file-pen text-warning"></i> Total: <strong><?= $totalDraft ?> Draf Dokumen</strong>
                    </span>
                    <span class="text-muted small d-none d-md-inline">Tersimpan di sistem & siap diajukan</span>
                </div>
                <div class="d-flex align-items-center gap-2 w-100 w-sm-auto justify-content-between justify-content-sm-end flex-wrap">
                    <div class="d-flex align-items-center gap-1.5">
                        <label for="perPageDraftProdi" class="small text-muted text-nowrap mb-0" style="font-size: 0.78rem;">Tampilkan:</label>
                        <select id="perPageDraftProdi" class="form-select form-select-sm form-select-perpage shadow-none" style="min-width: 108px; width: auto; font-size: 0.8rem; border-color: #CBD5E1;" onchange="changeDraftProdiPageSize(this.value)">
                            <option value="10" selected>10 data</option>
                            <option value="25">25 data</option>
                            <option value="50">50 data</option>
                            <option value="100">100 data</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0 ps-1" style="border-color: #CBD5E1;" id="searchDraftInput" placeholder="Cari draf dokumen..." oninput="filterDraftTable()">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-modern-draf" id="draftTable">
                    <thead>
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;">No</th>
                            <th style="min-width: 280px; width: 32%;">Nama Dokumen Mutu</th>
                            <th class="text-center" style="width: 140px;">Siklus PPEPP</th>
                            <th style="min-width: 180px; width: 20%;">Bidang</th>
                            <th class="text-center" style="width: 180px;">Kelengkapan Berkas</th>
                            <th class="text-center" style="width: 160px;">Terakhir Disimpan</th>
                            <th class="pe-4 text-center" style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $idx => $doc): 
                            $filesCount = count($doc['files'] ?? []);
                            $gdriveLinks = parse_external_links($doc['external_link'] ?? '');
                            $linkCount = count($gdriveLinks);
                            $hasFiles = ($filesCount > 0 || (!empty($doc['file_path']) && $doc['jenis_upload'] !== 'link'));
                            $hasLinks = ($linkCount > 0);
                            $isKombinasi = ($doc['jenis_upload'] === 'kombinasi') || ($hasFiles && $hasLinks);
                            $hasAnyAttachment = ($hasFiles || $hasLinks);
                        ?>
                            <tr class="draft-table-row" data-search="<?= strtolower(htmlspecialchars($doc['nama_dokumen'] . ' ' . ($doc['nomor_dokumen'] ?? '') . ' ' . ($doc['nama_bidang'] ?? ''))) ?>" style="border-bottom: 1px solid #E2E8F0;">
                                <td class="ps-4 text-center text-muted small fw-semibold" style="border-end: 1px solid #F1F5F9;"><?= $idx + 1 ?></td>
                                <td class="py-3.5 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                    <div class="fw-bold text-dark-blue mb-1" style="font-size: 0.92rem; line-height: 1.4;">
                                        <a href="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>" class="text-decoration-none text-dark-blue hover-primary">
                                            <?= htmlspecialchars($doc['nama_dokumen']) ?>
                                        </a>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 small text-muted" style="font-size: 0.74rem;">
                                        <?php if (!empty($doc['nomor_dokumen'])): ?>
                                            <span class="d-inline-flex align-items-center gap-0.5"><i class="fas fa-hashtag text-secondary opacity-75"></i><?= htmlspecialchars($doc['nomor_dokumen']) ?></span>
                                            <span>&bull;</span>
                                        <?php endif; ?>
                                        <span>TA: <?= htmlspecialchars($doc['tahun_akademik']) ?></span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                    <?= siklus_badge($doc['siklus']) ?>
                                </td>
                                <td class="py-3.5 px-3.5" style="border-end: 1px solid #F1F5F9;">
                                    <div class="small fw-semibold text-secondary" style="line-height: 1.35;">
                                        <?= htmlspecialchars($doc['nama_bidang'] ?? '-') ?>
                                    </div>
                                    <?php if (!empty($doc['nama_sub_bidang'])): ?>
                                        <div class="text-muted small mt-0.5" style="font-size: 0.72rem; line-height: 1.3;">
                                            <?= htmlspecialchars($doc['nama_sub_bidang']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                    <?php if ($isKombinasi): ?>
                                        <div class="d-flex flex-column align-items-center gap-1">
                                            <span class="badge" style="background: #EDE9FE; color: #7C3AED; border: 1px solid #DDD6FE; font-size: 0.72rem;">
                                                <i class="fas fa-layer-group me-1"></i> Kombinasi
                                            </span>
                                            <span class="text-muted" style="font-size: 0.68rem;">
                                                <?= $filesCount ?: 1 ?> File + <?= $linkCount ?> GDrive
                                            </span>
                                        </div>
                                    <?php elseif ($hasFiles): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                            <i class="fas fa-file-lines me-1"></i> <?= $filesCount > 1 ? $filesCount . ' Berkas' : '1 Berkas' ?> Terlampir
                                        </span>
                                    <?php elseif ($hasLinks): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                            <i class="fab fa-google-drive me-1"></i> <?= $linkCount > 1 ? $linkCount . ' Tautan GDrive' : 'Tautan GDrive' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge px-2.5 py-1 rounded-pill fw-semibold" style="background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; font-size: 0.72rem;">
                                            <i class="fas fa-triangle-exclamation text-warning me-1"></i> Berkas Belum Ada
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-3 text-center" style="border-end: 1px solid #F1F5F9;">
                                    <div class="small text-dark fw-semibold" style="font-size: 0.78rem;">
                                        <?= date('d M Y', strtotime($doc['updated_at'])) ?>
                                    </div>
                                    <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                        <?= date('H:i', strtotime($doc['updated_at'])) ?> WIB
                                    </div>
                                </td>
                                <td class="pe-4 py-3.5 text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-1.5">
                                        <!-- Tombol Ajukan ke LPM -->
                                        <?php if ($hasAnyAttachment): ?>
                                            <form action="<?= base_url('prodi/dokumen/ajukan/' . $doc['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Ajukan draf dokumen \'<?= htmlspecialchars(addslashes($doc['nama_dokumen'])) ?>\' ke LPM sekarang?\n\nSetelah diajukan, dokumen akan masuk antrean review Tim Penjaminan Mutu.');">
                                                <button type="submit" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #002B49; color: #FFFFFF; font-size: 0.76rem;" title="Ajukan ke Tim Penjamin Mutu LPM">
                                                    <i class="fas fa-paper-plane text-warning"></i>
                                                    <span>Ajukan ke LPM</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" 
                                                    style="background: #FFFBEB; color: #B45309; border: 1px solid #FCD34D; font-size: 0.76rem;" 
                                                    title="Lengkapi berkas sebelum mengajukan ke LPM"
                                                    onclick="alertBelumAdaBerkas('<?= htmlspecialchars(addslashes($doc['nama_dokumen'])) ?>', '<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>')">
                                                <i class="fas fa-paper-plane text-warning"></i>
                                                <span>Ajukan ke LPM</span>
                                            </button>
                                        <?php endif; ?>

                                        <!-- Tombol Lanjutkan Edit -->
                                        <a href="<?= base_url('prodi/dokumen/edit/' . $doc['id']) ?>" class="btn btn-sm btn-light border text-secondary rounded-circle d-inline-flex align-items-center justify-content-center shadow-2xs" style="width: 32px; height: 32px; font-size: 0.78rem;" title="Lanjutkan Pengisian Draf">
                                            <i class="fas fa-pen-to-square"></i>
                                        </a>

                                        <!-- Tombol Hapus Draf -->
                                        <form action="<?= base_url('prodi/dokumen/delete/' . $doc['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draf dokumen \'<?= htmlspecialchars(addslashes($doc['nama_dokumen'])) ?>\'?');">
                                            <button type="submit" class="btn btn-sm btn-light border border-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center shadow-2xs" style="width: 32px; height: 32px; font-size: 0.78rem;" title="Hapus Draf">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="p-3.5 px-4 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 bg-white" id="draftProdiPaginationWrapper" style="border-color: #E2E8F0 !important;">
                <div class="small text-muted" id="draftProdiPageInfo">
                    Menampilkan <strong>1 - <?= min(10, $totalDraft) ?></strong> dari <strong><?= $totalDraft ?></strong> draf dokumen
                </div>
                <nav aria-label="Navigasi Halaman Draf">
                    <ul class="pagination pagination-sm mb-0 gap-1" id="draftProdiPaginationUl">
                        <!-- Rendered by JS -->
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let currentDraftProdiPage = 1;
let draftProdiPerPage = 10;

function changeDraftProdiPageSize(val) {
    draftProdiPerPage = (val === 'all') ? 999999 : parseInt(val, 10);
    currentDraftProdiPage = 1;
    filterDraftTable();
}

function filterDraftTable() {
    const query = (document.getElementById('searchDraftInput')?.value || '').toLowerCase().trim();
    const rows = Array.from(document.querySelectorAll('#draftTable tbody tr.draft-table-row'));

    const matchedRows = [];
    rows.forEach(row => {
        const text = (row.getAttribute('data-search') || row.innerText).toLowerCase();
        if (!query || text.includes(query)) {
            matchedRows.push(row);
        } else {
            row.style.display = 'none';
        }
    });

    const totalMatched = matchedRows.length;
    const totalPages = draftProdiPerPage >= 999999 ? 1 : Math.max(1, Math.ceil(totalMatched / draftProdiPerPage));
    if (currentDraftProdiPage > totalPages) currentDraftProdiPage = totalPages;

    const startIdx = (currentDraftProdiPage - 1) * draftProdiPerPage;
    const endIdx = startIdx + draftProdiPerPage;

    matchedRows.forEach((row, idx) => {
        if (idx >= startIdx && idx < endIdx) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Update info
    const infoEl = document.getElementById('draftProdiPageInfo');
    if (infoEl) {
        if (totalMatched === 0) {
            infoEl.innerHTML = 'Tidak ada draf dokumen yang cocok dengan pencarian';
        } else {
            const displayStart = startIdx + 1;
            const displayEnd = Math.min(totalMatched, endIdx);
            infoEl.innerHTML = `Menampilkan <strong>${displayStart} - ${displayEnd}</strong> dari <strong>${totalMatched}</strong> draf dokumen`;
        }
    }

    renderDraftProdiPagination(totalPages, totalMatched);
}

function renderDraftProdiPagination(totalPages, totalMatched) {
    const ul = document.getElementById('draftProdiPaginationUl');
    if (!ul) return;
    ul.innerHTML = '';
    if (totalPages <= 1) return;

    // Prev
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentDraftProdiPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<button class="page-link rounded-2 px-2.5 py-1" onclick="goDraftProdiPage(${currentDraftProdiPage - 1})"><i class="fas fa-chevron-left"></i></button>`;
    ul.appendChild(prevLi);

    for (let p = 1; p <= totalPages; p++) {
        if (p === 1 || p === totalPages || (p >= currentDraftProdiPage - 1 && p <= currentDraftProdiPage + 1)) {
            const li = document.createElement('li');
            li.className = `page-item ${p === currentDraftProdiPage ? 'active' : ''}`;
            li.innerHTML = `<button class="page-link rounded-2 px-3 py-1 fw-medium" onclick="goDraftProdiPage(${p})">${p}</button>`;
            ul.appendChild(li);
        } else if (p === currentDraftProdiPage - 2 || p === currentDraftProdiPage + 2) {
            const li = document.createElement('li');
            li.className = 'page-item disabled';
            li.innerHTML = '<span class="page-link border-0">...</span>';
            ul.appendChild(li);
        }
    }

    // Next
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentDraftProdiPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<button class="page-link rounded-2 px-2.5 py-1" onclick="goDraftProdiPage(${currentDraftProdiPage + 1})"><i class="fas fa-chevron-right"></i></button>`;
    ul.appendChild(nextLi);
}

function goDraftProdiPage(p) {
    currentDraftProdiPage = p;
    filterDraftTable();
}

document.addEventListener('DOMContentLoaded', () => {
    filterDraftTable();
});

function alertBelumAdaBerkas(docName, editUrl) {
    if (window.Swal) {
        Swal.fire({
            icon: 'warning',
            title: 'Berkas Belum Lengkap',
            html: `Dokumen <strong>"${docName}"</strong> belum memiliki berkas terlampir (PDF/Word/Excel) atau tautan Google Drive.<br><br>Harap lengkapi berkas dokumen terlebih dahulu sebelum mengajukannya ke LPM.`,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-wrench me-1"></i> Lengkapi Berkas Sekarang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563EB'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = editUrl;
            }
        });
    } else {
        if (confirm(`Dokumen "${docName}" belum memiliki berkas dokumen atau link Google Drive.\n\nBuka halaman pengisian untuk melengkapi berkas sekarang?`)) {
            window.location.href = editUrl;
        }
    }
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
