<?php
/**
 * Public View: Tentang PPEPP SPMI
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once ROOT_PATH . '/views/layouts/header.php';
?>

<section class="bg-scu-gradient text-white py-5 shadow-sm">
    <div class="container text-center">
        <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2">Penjaminan Mutu Internal</span>
        <h1 class="display-6 fw-bold mb-3">Tentang Siklus PPEPP</h1>
        <p class="lead text-light opacity-90 mx-auto" style="max-width: 750px;">
            Siklus PPEPP adalah pondasi pelaksanaan Sistem Penjaminan Mutu Internal (SPMI) di <?= INSTITUTION_NAME ?> dalam mewujudkan tridharma perguruan tinggi yang unggul dan berintegritas.
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4" style="border-top: 4px solid var(--ppepp-penetapan) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge badge-penetapan fs-6 p-2">P1</span>
                        <h5 class="fw-bold text-dark-blue mb-0">1. Penetapan Standar</h5>
                    </div>
                    <p class="text-muted small">
                        Langkah awal penetapan seluruh kebijakan SPMI, manual mutu, standar mutu akademik & non-akademik, serta rumusan Capaian Pembelajaran Lulusan (CPL) oleh LPM dan pimpinan institusi.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4" style="border-top: 4px solid var(--ppepp-pelaksanaan) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge badge-pelaksanaan fs-6 p-2">P2</span>
                        <h5 class="fw-bold text-dark-blue mb-0">2. Pelaksanaan Standar</h5>
                    </div>
                    <p class="text-muted small">
                        Realisasi dan operasionalisasi seluruh standar yang telah ditetapkan dalam aktivitas perkuliahan, riset, pengabdian masyarakat, pengelolaan tata pamong, dan pelayanan mahasiswa.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4" style="border-top: 4px solid var(--ppepp-evaluasi) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge badge-evaluasi fs-6 p-2">E</span>
                        <h5 class="fw-bold text-dark-blue mb-0">3. Evaluasi Pelaksanaan</h5>
                    </div>
                    <p class="text-muted small">
                        Pelaksanaan Audit Mutu Internal (AMI), monitoring pembelajaran berkala, evaluasi kepuasan pemangku kepentingan (tracer study), dan asesmen ketercapaian target standar mutu.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4" style="border-top: 4px solid var(--ppepp-pengendalian) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge badge-pengendalian fs-6 p-2">P3</span>
                        <h5 class="fw-bold text-dark-blue mb-0">4. Pengendalian Pelaksanaan</h5>
                    </div>
                    <p class="text-muted small">
                        Perumusan Tindakan Koreksi dan Pencegahan (PTKP) serta Rapat Tinjauan Manajemen (RTM) untuk menindaklanjuti ketidaksesuaian/temuan audit agar standar dapat kembali terpenuhi secara optimal.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4" style="border-top: 4px solid var(--ppepp-peningkatan) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge badge-peningkatan fs-6 p-2">P4</span>
                        <h5 class="fw-bold text-dark-blue mb-0">5. Peningkatan Standar</h5>
                    </div>
                    <p class="text-muted small">
                        Penerapan prinsip continuous improvement (Kaizen) dengan menaikkan atau memperbaharui standar mutu yang telah tercapai agar melampaui Standar Nasional Pendidikan Tinggi (SN-Dikti).
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
