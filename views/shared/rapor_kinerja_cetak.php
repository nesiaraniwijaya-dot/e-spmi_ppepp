<?php
/**
 * Cetak / Softfile PDF Rapor Kinerja Mutu SPMI Unit (Dekanat / Prodi)
 * SPMI PPEPP UNIKA Soegijapranata
 */

$cycleLabels = [
    'penetapan'    => 'Penetapan (P1)',
    'pelaksanaan'  => 'Pelaksanaan (P2)',
    'evaluasi'     => 'Evaluasi (P3)',
    'pengendalian' => 'Pengendalian (P4)',
    'peningkatan'  => 'Peningkatan (P5)'
];

$predikatColors = [
    'Sangat Baik'     => '#198754',
    'Baik'            => '#0d6efd',
    'Cukup'           => '#ffc107',
    'Perlu Pembinaan' => '#dc3545'
];
$badgeColor = $predikatColors[$perf['predikat']] ?? '#6c757d';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Rapor Kinerja Mutu SPMI') ?></title>
    <!-- Bootstrap 5 CSS for base typography & layout -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #222;
            background-color: #f8f9fa;
            line-height: 1.4;
            font-size: 13px;
        }

        .paper-sheet {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm 20mm 25mm 20mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            position: relative;
            box-sizing: border-box;
        }

        /* Kop Surat Resmi */
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 85px;
            height: auto;
            object-fit: contain;
        }
        .kop-text h6 {
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: normal;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-text h4 {
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 2px 0;
            color: #1a2a4b;
        }
        .kop-text h5 {
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
            margin: 2px 0;
            color: #0b4578;
        }
        .kop-text p {
            font-family: Arial, sans-serif;
            font-size: 10.5px;
            margin: 0;
            color: #444;
        }

        /* Judul Rapor */
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title h3 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 4px;
            letter-spacing: 0.8px;
        }
        .doc-title .doc-subtitle {
            font-size: 12px;
            color: #555;
            font-weight: 600;
        }

        /* Box Metadata */
        .meta-table {
            width: 100%;
            margin-bottom: 16px;
            font-size: 12.5px;
        }
        .meta-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .meta-table td.label-col {
            width: 170px;
            font-weight: 600;
            color: #333;
        }

        /* Score Badge Seal */
        .score-seal-box {
            border: 2px solid <?= $badgeColor ?>;
            background: #fdfdfd;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .score-seal-number {
            font-size: 32px;
            font-weight: 900;
            color: <?= $badgeColor ?>;
            font-family: Arial, sans-serif;
            line-height: 1;
        }
        .score-seal-predikat {
            font-size: 18px;
            font-weight: bold;
            color: <?= $badgeColor ?>;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tables */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 12px;
        }
        .report-table th, .report-table td {
            border: 1px solid #777;
            padding: 6px 8px;
        }
        .report-table th {
            background-color: #f1f3f5;
            font-weight: bold;
            text-align: center;
            color: #111;
        }

        .alert-box {
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 11.5px;
        }
        .alert-box-warning {
            border: 1px solid #dc3545;
            background-color: #fff5f5;
            color: #991b1b;
        }
        .alert-box-success {
            border: 1px solid #198754;
            background-color: #f0fdf4;
            color: #166534;
        }

        .note-card {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
            background: #fafafa;
            font-size: 12px;
        }
        .note-card-title {
            font-weight: bold;
            font-size: 12px;
            color: #0b4578;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        /* Signatures */
        .sig-section {
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .sig-col {
            text-align: center;
            font-size: 12px;
        }
        .sig-space {
            height: 60px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* No-print bar */
        @media screen {
            .action-bar {
                background: #212529;
                color: #fff;
                padding: 10px 20px;
                position: sticky;
                top: 0;
                z-index: 1000;
                box-shadow: 0 2px 10px rgba(0,0,0,0.25);
            }
        }
        @media print {
            body {
                background: #fff;
                color: #000;
                margin: 0;
                padding: 0;
            }
            .paper-sheet {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .action-bar {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Aksi Layar (Hanya Tampil di Browser) -->
    <div class="action-bar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-file-pdf text-danger fa-lg"></i>
            <div>
                <strong class="d-block" style="font-size: 14px;"><?= htmlspecialchars($perf['unit_name']) ?></strong>
                <small class="text-white-50">Pratinjau Cetak / Unduh Softfile Dokumen Rapor Mutu SPMI</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.history.back()" class="btn btn-sm btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </button>
            <button onclick="window.print()" class="btn btn-sm btn-danger fw-bold">
                <i class="fas fa-print me-1"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen Fisik (A4) -->
    <div class="paper-sheet">

        <!-- KOP SURAT RESMI -->
        <div class="kop-surat d-flex align-items-center gap-3">
            <img src="<?= BASE_URL ?>/assets/images/logo-unika.png" alt="Logo UNIKA" class="kop-logo" onerror="this.style.display='none'">
            <div class="kop-text flex-grow-1 text-center">
                <h6>Yayasan Sandjaja Semarang</h6>
                <h4>UNIVERSITAS KATOLIK SOEGIJAPRANATA</h4>
                <h5>LEMBAGA PENJAMINAN MUTU (LPM)</h5>
                <p>Jl. Pawiyatan Luhur IV/1 Bendan Dhuwur Semarang 50234 | Telp. (024) 8441555 | Email: lpm@unika.ac.id</p>
            </div>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title">
            <h3>RAPOR EVALUASI & PEMANTAUAN KINERJA MUTU SPMI</h3>
            <div class="doc-subtitle">
                IMPLEMENTASI SIKLUS PPEPP &bull; TAHUN AKADEMIK: <?= htmlspecialchars($perf['tahun_akademik']) ?>
            </div>
        </div>

        <!-- METADATA UNIT -->
        <table class="meta-table">
            <tr>
                <td class="label-col">Unit Kerja / Sasaran</td>
                <td style="width: 10px;">:</td>
                <td><strong><?= htmlspecialchars($perf['unit_name']) ?></strong></td>
                <td class="label-col">Tahun Akademik</td>
                <td style="width: 10px;">:</td>
                <td><strong><?= htmlspecialchars($perf['tahun_akademik']) ?></strong></td>
            </tr>
            <tr>
                <td class="label-col">Kategori Satuan</td>
                <td>:</td>
                <td><?= ($perf['target_type'] === 'fakultas' ? 'Tingkat Dekanat (Fakultas)' : 'Tingkat Program Studi') ?></td>
                <td class="label-col">Tanggal Penerbitan</td>
                <td>:</td>
                <td><?= date('d F Y') ?></td>
            </tr>
            <tr>
                <td class="label-col">Fakultas Induk</td>
                <td>:</td>
                <td><?= htmlspecialchars($perf['fakultas_name']) ?></td>
                <td class="label-col">Status Evaluasi</td>
                <td>:</td>
                <td>
                    <?php if ($perf['is_forwarded']): ?>
                        <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Resmi Diteruskan ke Unit</span>
                        <?php if (!empty($perf['forwarded_at'])): ?>
                            <small class="text-muted">(<?= date('d/m/Y H:i', strtotime($perf['forwarded_at'])) ?>)</small>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-secondary"><i class="fas fa-clock me-1"></i> Penilaian Internal GPM/LPM</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="label-col">Pimpinan Unit</td>
                <td>:</td>
                <td colspan="4"><?= htmlspecialchars($perf['pimpinan_name']) ?></td>
            </tr>
        </table>

        <!-- SEAL SKOR AKHIR & PREDIKAT -->
        <div class="score-seal-box">
            <div>
                <span class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Indeks Kinerja Mutu SPMI</span>
                <div class="score-seal-predikat"><?= htmlspecialchars($perf['predikat']) ?></div>
                <small class="text-muted">Kalkulasi 4 Pilar Objektif (PPEPP 35%, Kualitas 30%, Revisi 20%, Standar 15%)</small>
            </div>
            <div class="text-end">
                <div class="score-seal-number"><?= number_format($perf['skor_total'], 1) ?></div>
                <span class="badge bg-light text-dark border">Skala 0 - 100</span>
            </div>
        </div>

        <!-- BOX PERINGATAN SIKLUS KOSONG (JIKA ADA) -->
        <?php if (!empty($perf['siklus_kosong'])): ?>
            <div class="alert-box alert-box-warning">
                <strong><i class="fas fa-triangle-exclamation me-1"></i> PERHATIAN - SIKLUS PPEPP BELUM LENGKAP:</strong><br>
                Terdapat <strong><?= count($perf['siklus_kosong']) ?> siklus</strong> yang belum memiliki dokumen mutu sama sekali, yaitu:
                <strong class="text-decoration-underline">
                    <?= implode(', ', array_map(function($c) use ($cycleLabels) { return $cycleLabels[$c] ?? ucfirst($c); }, $perf['siklus_kosong'])) ?>
                </strong>. Unit diharapkan segera memenuhi dan mengunggah dokumen pada siklus tersebut demi menjamin siklus mutu PPEPP berjalan utuh.
            </div>
        <?php else: ?>
            <div class="alert-box alert-box-success">
                <strong><i class="fas fa-circle-check me-1"></i> SIKLUS PPEPP UTUH (100%):</strong><br>
                Unit telah memiliki dokumen mutu pada seluruh 5 siklus PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan).
            </div>
        <?php endif; ?>

        <!-- BAGIAN 1: RINCIAN PEMENUHAN 5 SIKLUS PPEPP -->
        <p class="mb-1 fw-bold text-uppercase" style="font-size: 12px;">I. Pemenuhan 5 Siklus Mutu PPEPP (Bobot 35%)</p>
        <table class="report-table text-center">
            <thead>
                <tr>
                    <th style="width: 25%;">Siklus PPEPP</th>
                    <th style="width: 20%;">Jumlah Dokumen</th>
                    <th style="width: 25%;">Status Siklus</th>
                    <th style="width: 30%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cycleLabels as $key => $label): 
                    $count = $perf['cycle_counts'][$key] ?? 0;
                    $isFulfilled = ($count > 0);
                ?>
                <tr>
                    <td class="text-start ps-3 fw-bold"><?= $label ?></td>
                    <td><?= $count ?> Dokumen</td>
                    <td>
                        <?php if ($isFulfilled): ?>
                            <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Terpenuhi</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i> Belum Ada</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-start ps-2">
                        <?= $isFulfilled ? 'Aktivitas siklus terdokumentasi' : 'Segera unggah dokumen untuk siklus ini' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- BAGIAN 2: RINCIAN SKOR 4 PILAR INDEKS MUTU -->
        <p class="mb-1 fw-bold text-uppercase" style="font-size: 12px; margin-top: 14px;">II. Ringkasan Evaluasi 4 Pilar Indeks Mutu</p>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Pilar Penilaian</th>
                    <th style="width: 15%;">Bobot</th>
                    <th style="width: 15%;">Nilai Pilar</th>
                    <th style="width: 20%;">Kontribusi Skor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <strong>Kelengkapan 5 Siklus PPEPP</strong><br>
                        <small class="text-muted">Keterisian dokumen pada Penetapan, Pelaksanaan, Evaluasi, Pengendalian, Peningkatan</small>
                    </td>
                    <td class="text-center">35%</td>
                    <td class="text-center fw-bold"><?= number_format($perf['score_pilar1'], 1) ?>%</td>
                    <td class="text-center fw-bold text-primary"><?= number_format($perf['score_pilar1'] * 0.35, 1) ?></td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>
                        <strong>Kualitas & Persetujuan Dokumen</strong><br>
                        <small class="text-muted">Tingkat persetujuan LPM (<?= $perf['approval_rate'] ?>%) & Lolos tanpa revisi (<?= $perf['first_pass_rate'] ?>%)</small>
                    </td>
                    <td class="text-center">30%</td>
                    <td class="text-center fw-bold"><?= number_format($perf['score_pilar2'], 1) ?>%</td>
                    <td class="text-center fw-bold text-primary"><?= number_format($perf['score_pilar2'] * 0.30, 1) ?></td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>
                        <strong>Ketuntasan & Kecepatan Respon Revisi</strong><br>
                        <small class="text-muted">Terselesaikan: <?= $perf['sudah_diperbaiki_count'] ?>, Rata-rata durasi: <?= $perf['avg_revision_hours'] ?> jam</small>
                    </td>
                    <td class="text-center">20%</td>
                    <td class="text-center fw-bold"><?= number_format($perf['score_pilar3'], 1) ?>%</td>
                    <td class="text-center fw-bold text-primary"><?= number_format($perf['score_pilar3'] * 0.20, 1) ?></td>
                </tr>
                <tr>
                    <td class="text-center">4</td>
                    <td>
                        <strong>Cakupan Bidang Standar SPMI</strong><br>
                        <small class="text-muted">Menjangkau <?= $perf['distinct_bidang'] ?> dari 9 Bidang Standar SPMI Nasional/Universitas</small>
                    </td>
                    <td class="text-center">15%</td>
                    <td class="text-center fw-bold"><?= number_format($perf['score_pilar4'], 1) ?>%</td>
                    <td class="text-center fw-bold text-primary"><?= number_format($perf['score_pilar4'] * 0.15, 1) ?></td>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <td colspan="3" class="text-end fw-bold">TOTAL SKOR AKHIR:</td>
                    <td colspan="2" class="text-center fw-bold fs-6 text-primary">
                        <?= number_format($perf['skor_total'], 1) ?> / 100 (<?= htmlspecialchars($perf['predikat']) ?>)
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- BAGIAN 3: CATATAN & ARAHAN PEMBINAAN -->
        <p class="mb-1 fw-bold text-uppercase" style="font-size: 12px; margin-top: 14px;">III. Catatan & Arahan Pembinaan Resmi</p>
        
        <!-- Arahan GPM -->
        <div class="note-card">
            <div class="note-card-title d-flex justify-content-between">
                <span><i class="fas fa-clipboard-check me-1"></i> Arahan Gugus Penjaminan Mutu (GPM Fakultas)</span>
                <?php if (!empty($perf['eval_record']['gpm_evaluated_at'])): ?>
                    <small class="text-muted fw-normal"><?= date('d/m/Y H:i', strtotime($perf['eval_record']['gpm_evaluated_at'])) ?> oleh <?= htmlspecialchars($perf['eval_record']['gpm_user_name'] ?? 'GPM') ?></small>
                <?php endif; ?>
            </div>
            <div>
                <?php if (!empty($perf['catatan_gpm'])): ?>
                    <?= nl2br(htmlspecialchars($perf['catatan_gpm'])) ?>
                <?php else: ?>
                    <em class="text-muted">Belum ada arahan pembinaan tertulis dari GPM Fakultas.</em>
                <?php endif; ?>
            </div>
        </div>

        <!-- Masukan LPM -->
        <div class="note-card">
            <div class="note-card-title d-flex justify-content-between">
                <span><i class="fas fa-university me-1"></i> Masukan Lembaga Penjaminan Mutu (LPM Universitas)</span>
                <?php if (!empty($perf['eval_record']['lpm_evaluated_at'])): ?>
                    <small class="text-muted fw-normal"><?= date('d/m/Y H:i', strtotime($perf['eval_record']['lpm_evaluated_at'])) ?> oleh <?= htmlspecialchars($perf['eval_record']['lpm_user_name'] ?? 'LPM') ?></small>
                <?php endif; ?>
            </div>
            <div>
                <?php if (!empty($perf['catatan_lpm'])): ?>
                    <?= nl2br(htmlspecialchars($perf['catatan_lpm'])) ?>
                <?php else: ?>
                    <em class="text-muted">Belum ada catatan evaluasi khusus dari LPM Universitas.</em>
                <?php endif; ?>
            </div>
        </div>

        <!-- BAGIAN 4: PENGESAHAN / TANDA TANGAN -->
        <div class="sig-section">
            <table style="width: 100%; border: none; font-size: 12px;">
                <tr>
                    <td style="width: 33%;" class="sig-col">
                        Mengetahui,<br>
                        <strong>Kepala LPM Universitas</strong>
                        <div class="sig-space"></div>
                        <div class="sig-name">( Kepala LPM UNIKA )</div>
                        <div>NPP / NIDN. ---------------------</div>
                    </td>
                    <td style="width: 33%;" class="sig-col">
                        Disahkan Oleh,<br>
                        <strong>Ketua GPM Fakultas</strong>
                        <div class="sig-space"></div>
                        <div class="sig-name">( <?= htmlspecialchars($perf['eval_record']['forwarded_by_name'] ?? 'Ketua GPM') ?> )</div>
                        <div>NPP / NIDN. ---------------------</div>
                    </td>
                    <td style="width: 34%;" class="sig-col">
                        Semarang, <?= date('d F Y') ?><br>
                        Pimpinan Unit Kerja,<br>
                        <strong><?= ($perf['target_type'] === 'fakultas' ? 'Dekan Fakultas' : 'Ketua Program Studi') ?></strong>
                        <div class="sig-space"></div>
                        <div class="sig-name">( <?= htmlspecialchars($perf['pimpinan_name']) ?> )</div>
                        <div>NPP / NIDN. ---------------------</div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <?php if (!empty($_GET['print']) && $_GET['print'] == '1'): ?>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 600);
        });
    </script>
    <?php endif; ?>

</body>
</html>
