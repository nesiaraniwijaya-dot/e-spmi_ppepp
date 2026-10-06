<?php
/**
 * Public Sub-Menu Navigasi Unit PPEPP (Fakultas & Program Studi)
 * SPMI PPEPP UNIKA Soegijapranata
 *
 * Desain Responsif & Rapi (2-Tier Carousel):
 * - Baris 1: Header Identitas Fakultas, Status Unit, dan Tombol Ganti Fakultas
 * - Baris 2: Carousel Track Tab Unit dengan Tombol Geser (< dan >) yang tidak menutupi teks
 */

$navFakultas = $fakultas ?? [];
$navProdis   = $prodiList ?? [];
$navFakId    = (int)($navFakultas['id'] ?? 0);
$navFakKode  = $navFakultas['kode_fakultas'] ?? '';
$navFakNama  = $navFakultas['nama_fakultas'] ?? 'Fakultas';
$currentUnit = $activeUnit ?? 'fakultas'; // 'fakultas' or 'prodi'
$currentPId  = (int)($activeProdiId ?? 0);
$fakTotalDocs = (int)($navFakultas['total_dokumen'] ?? 0);
$totalUnits   = count($navProdis) + 1; // Dekanat + Prodis
?>

<div class="unit-pills-bar">
    <div class="container">
        <!-- Baris 1: Header Meta Bar & Tombol Aksi Ganti Fakultas -->
        <div class="unit-nav-header d-flex align-items-center justify-content-between py-2 border-bottom">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="badge bg-dark-blue text-white px-2.5 py-1.5 fw-bold" style="font-size: 0.74rem; letter-spacing: 0.4px;">
                    <i class="fas fa-landmark me-1 text-info"></i> <?= htmlspecialchars($navFakNama) ?>
                </span>
                <span class="text-muted small d-none d-sm-inline" style="font-size: 0.8rem;">&bull;</span>
                <span class="fw-bold text-dark-blue small d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                    <i class="fas fa-layer-group text-primary"></i>
                    <span>Pilih Unit Dokumen Mutu:</span>
                    <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill fw-semibold ms-1" style="font-size: 0.72rem;">
                        <?= $totalUnits ?> Unit Tersedia
                    </span>
                </span>
            </div>

            <!-- Tombol Aksi: Ganti Fakultas (Selalu Tertata di Pojok Kanan) -->
            <div>
                <a href="<?= base_url() ?>" 
                   class="btn-ganti-fakultas"
                   title="Kembali ke Beranda & Pilih Fakultas Lain">
                    <i class="fas fa-arrow-left"></i>
                    <span class="d-none d-sm-inline">Ganti Fakultas</span>
                </a>
            </div>
        </div>

        <!-- Baris 2: Dedicated Carousel Tab Track (Flanked Navigation Buttons - Zero Text Overlap) -->
        <div class="unit-tabs-wrapper d-flex align-items-center gap-2 py-2">
            <!-- Tombol Navigasi Geser Kiri -->
            <button type="button" class="unit-scroll-btn flex-shrink-0" id="unitScrollLeft" aria-label="Geser ke kiri" style="display: none;" title="Geser ke unit sebelumnya">
                <i class="fas fa-chevron-left"></i>
            </button>

            <!-- Track Tab Pills (Scrollable Smooth Ribbon) -->
            <div class="unit-tabs-track flex-grow-1" id="unitTabsTrack">
                <!-- Tab Unit Dekanat Fakultas -->
                <?php $isFakActive = ($currentUnit === 'fakultas'); ?>
                <a href="<?= base_url('fakultas/' . $navFakId) ?>" 
                   class="unit-nav-pill <?= $isFakActive ? 'active' : '' ?>"
                   <?= $isFakActive ? 'id="activeUnitPill"' : '' ?>
                   title="Buka Dokumen Mutu Dekanat <?= htmlspecialchars($navFakNama) ?>">
                    <i class="fas fa-landmark <?= $isFakActive ? 'text-info' : 'text-primary' ?>"></i>
                    <span class="unit-pill-title">Dokumen Dekanat Fakultas</span>
                    <span class="pill-badge"><?= $fakTotalDocs ?> Dok.</span>
                </a>

                <!-- Tab untuk Setiap Program Studi -->
                <?php foreach ($navProdis as $pr): ?>
                    <?php $isPrActive = ($currentUnit === 'prodi' && $currentPId === (int)$pr['id']); ?>
                    <a href="<?= base_url('prodi/' . $pr['id']) ?>" 
                       class="unit-nav-pill <?= $isPrActive ? 'active' : '' ?>"
                       <?= $isPrActive ? 'id="activeUnitPill"' : '' ?>
                       title="Buka Dokumen Mutu Program Studi <?= htmlspecialchars($pr['nama_prodi']) ?>">
                        <i class="fas fa-graduation-cap <?= $isPrActive ? 'text-info' : 'text-primary' ?>"></i>
                        <span class="unit-pill-title">
                            <?= htmlspecialchars(!empty($pr['jenjang']) ? $pr['jenjang'] . ' ' : '') ?><?= htmlspecialchars($pr['nama_prodi']) ?>
                        </span>
                        <span class="pill-badge"><?= (int)($pr['total_dokumen'] ?? 0) ?> Dok.</span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Tombol Navigasi Geser Kanan -->
            <button type="button" class="unit-scroll-btn flex-shrink-0" id="unitScrollRight" aria-label="Geser ke kanan" style="display: none;" title="Geser ke unit berikutnya">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('unitTabsTrack');
    const btnLeft = document.getElementById('unitScrollLeft');
    const btnRight = document.getElementById('unitScrollRight');
    const activePill = document.getElementById('activeUnitPill');

    if (!track) return;

    function updateScrollButtons() {
        if (!btnLeft || !btnRight) return;
        const isOverflowing = track.scrollWidth > (track.clientWidth + 6);
        if (!isOverflowing) {
            btnLeft.style.display = 'none';
            btnRight.style.display = 'none';
            return;
        }
        btnLeft.style.display = track.scrollLeft > 12 ? 'inline-flex' : 'none';
        btnRight.style.display = (track.scrollLeft + track.clientWidth < track.scrollWidth - 12) ? 'inline-flex' : 'none';
    }

    if (btnLeft) {
        btnLeft.addEventListener('click', function() {
            track.scrollBy({ left: -280, behavior: 'smooth' });
        });
    }
    if (btnRight) {
        btnRight.addEventListener('click', function() {
            track.scrollBy({ left: 280, behavior: 'smooth' });
        });
    }

    track.addEventListener('scroll', updateScrollButtons, { passive: true });
    window.addEventListener('resize', updateScrollButtons, { passive: true });

    // Auto-scroll active pill into view with center alignment
    if (activePill) {
        setTimeout(function() {
            if (activePill === track.firstElementChild) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                const pillLeft = activePill.offsetLeft;
                const pillWidth = activePill.offsetWidth;
                const trackWidth = track.clientWidth;
                const scrollTarget = pillLeft - (trackWidth / 2) + (pillWidth / 2);
                track.scrollTo({ left: Math.max(0, scrollTarget), behavior: 'smooth' });
            }
            updateScrollButtons();
        }, 120);
    } else {
        updateScrollButtons();
    }
});
</script>
