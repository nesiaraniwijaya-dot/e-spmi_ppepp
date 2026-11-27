<?php
/**
 * UnitPerformanceCalculator
 * Layanan Penghitung Skor Kinerja Mutu SPMI PPEPP untuk Dekanat & Program Studi
 * UNIKA Soegijapranata (SCU)
 */

class UnitPerformanceCalculator {

    public static function getAcademicYears($db): array {
        $years = [];
        try {
            $stmt = $db->query("
                SELECT DISTINCT tahun_akademik FROM ppepp_documents 
                WHERE tahun_akademik IS NOT NULL AND tahun_akademik != '' AND deleted_at IS NULL
                ORDER BY tahun_akademik DESC
            ");
            $years = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (Exception $e) {}

        // Fallback default years if empty
        if (empty($years)) {
            $currentY = (int)date('Y');
            $years = [
                ($currentY - 1) . '/' . $currentY,
                $currentY . '/' . ($currentY + 1),
                ($currentY - 2) . '/' . ($currentY - 1)
            ];
        }
        return array_values(array_unique(array_filter($years)));
    }

    /**
     * Hitung Skor & Indikator Kinerja untuk Unit (Fakultas atau Prodi)
     *
     * @param PDO $db
     * @param string $targetType 'fakultas' | 'prodi'
     * @param int $targetId ID Fakultas atau ID Prodi
     * @param string|null $tahunAkademik Tahun akademik spesifik (misal '2025/2026') atau null/Semua
     * @return array
     */
    public static function calculateForUnit($db, string $targetType, int $targetId, ?string $tahunAkademik = null): array {
        $isFakultas = ($targetType === 'fakultas');
        $taFilter = ($tahunAkademik && $tahunAkademik !== 'Semua' && $tahunAkademik !== 'all') ? trim($tahunAkademik) : null;

        // Base where clause
        $whereParams = [];
        if ($isFakultas) {
            $baseWhere = "d.fakultas_id = ? AND d.level = 'fakultas' AND d.deleted_at IS NULL AND d.status_review != 'draft'";
            $whereParams[] = $targetId;
        } else {
            $baseWhere = "d.prodi_id = ? AND d.deleted_at IS NULL AND d.status_review != 'draft'";
            $whereParams[] = $targetId;
        }

        if ($taFilter) {
            $baseWhere .= " AND d.tahun_akademik = ?";
            $whereParams[] = $taFilter;
        }

        // Ambil info nama unit
        $unitName = '-';
        $fakultasName = '-';
        $kaprodiOrDekan = '-';
        $fakultasId = $isFakultas ? $targetId : 0;
        $prodiId = $isFakultas ? null : $targetId;

        if ($isFakultas) {
            $stmtF = $db->prepare("SELECT * FROM fakultas WHERE id = ?");
            $stmtF->execute([$targetId]);
            $fak = $stmtF->fetch();
            if ($fak) {
                $unitName = 'Fakultas ' . $fak['nama_fakultas'];
                $fakultasName = $fak['nama_fakultas'];
                $kaprodiOrDekan = $fak['nama_dekan'] ?? '-';
            }
        } else {
            $stmtP = $db->prepare("
                SELECT p.*, f.nama_fakultas, f.id as fak_id 
                FROM prodis p 
                LEFT JOIN fakultas f ON p.fakultas_id = f.id 
                WHERE p.id = ?
            ");
            $stmtP->execute([$targetId]);
            $prd = $stmtP->fetch();
            if ($prd) {
                $unitName = ($prd['jenjang'] ?? 'S1') . ' ' . $prd['nama_prodi'];
                $fakultasName = $prd['nama_fakultas'] ?? '-';
                $fakultasId = (int)($prd['fak_id'] ?? 0);
                $kaprodiOrDekan = $prd['nama_kaprodi'] ?? '-';
            }
        }

        // 1. Agregasi total dokumen & breakdown siklus
        $stmtDocs = $db->prepare("
            SELECT d.id, d.siklus, d.bidang_id, d.status_review, d.created_at, d.updated_at,
                   d.gpm_reviewed_at, d.lpm_reviewed_at
            FROM ppepp_documents d
            WHERE {$baseWhere}
        ");
        $stmtDocs->execute($whereParams);
        $docs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $totalDocs = count($docs);

        // --- PILAR 1: Kelengkapan 5 Siklus PPEPP (Bobot 35%) ---
        $allCycles = ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'];
        $cycleCounts = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0
        ];
        foreach ($docs as $d) {
            $c = strtolower(trim($d['siklus'] ?? ''));
            if (isset($cycleCounts[$c])) {
                $cycleCounts[$c]++;
            }
        }

        $siklusTerpenuhi = [];
        $siklusKosong = [];
        foreach ($allCycles as $c) {
            if ($cycleCounts[$c] > 0) {
                $siklusTerpenuhi[] = $c;
            } else {
                $siklusKosong[] = $c;
            }
        }

        $scorePilar1 = round((count($siklusTerpenuhi) / 5) * 100, 1);

        // --- PILAR 2: Kualitas & Persetujuan Dokumen (Bobot 30%) ---
        $sesuaiCount = 0;
        $docIds = array_column($docs, 'id');
        $firstPassCount = 0;

        if (!empty($docIds)) {
            // Cek dokumen yang pernah dikembalikan/revisi
            $placeholders = implode(',', array_fill(0, count($docIds), '?'));
            $stmtEverRevised = $db->prepare("
                SELECT DISTINCT document_id FROM ppepp_document_reviews 
                WHERE document_id IN ({$placeholders}) 
                  AND status_hasil IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan')
            ");
            $stmtEverRevised->execute($docIds);
            $revisedDocIds = $stmtEverRevised->fetchAll(PDO::FETCH_COLUMN) ?: [];
            $revisedMap = array_flip($revisedDocIds);

            foreach ($docs as $d) {
                if ($d['status_review'] === 'sesuai') {
                    $sesuaiCount++;
                    if (!isset($revisedMap[$d['id']])) {
                        $firstPassCount++;
                    }
                }
            }
        }

        $approvalRate = $totalDocs > 0 ? round(($sesuaiCount / $totalDocs) * 100, 1) : 0.0;
        $firstPassRate = $sesuaiCount > 0 ? round(($firstPassCount / $sesuaiCount) * 100, 1) : ($totalDocs > 0 ? 0.0 : 0.0);
        $scorePilar2 = $totalDocs > 0 ? round(($approvalRate * 0.70) + ($firstPassRate * 0.30), 1) : 0.0;

        // --- PILAR 3: Ketuntasan & Kecepatan Tindak Lanjut Revisi (Bobot 20%) ---
        $revisiMacetCount = 0;
        $sudahDiperbaikiCount = 0;
        $revisionDurations = [];

        foreach ($docs as $d) {
            if (in_array($d['status_review'], ['revisi_gpm', 'revisi_lpm', 'perlu_perbaikan'])) {
                $revisiMacetCount++;
            } elseif (in_array($d['status_review'], ['sudah_diperbaiki', 'menunggu_review_lpm', 'sesuai'])) {
                if (!empty($revisedMap) && isset($revisedMap[$d['id']])) {
                    $sudahDiperbaikiCount++;
                    // Durasi perbaikan jika ada
                    $revTime = $d['gpm_reviewed_at'] ?? $d['lpm_reviewed_at'] ?? null;
                    if ($revTime && !empty($d['updated_at']) && strtotime($d['updated_at']) >= strtotime($revTime)) {
                        $hours = (strtotime($d['updated_at']) - strtotime($revTime)) / 3600;
                        if ($hours > 0 && $hours < 720) { // filter anomali > 30 hari
                            $revisionDurations[] = $hours;
                        }
                    }
                }
            }
        }

        $totalRevisiEvents = $revisiMacetCount + $sudahDiperbaikiCount;
        if ($totalRevisiEvents === 0) {
            // Unit tidak memiliki catatan revisi sama sekali (kepatuhan bersih)
            $scorePilar3 = 100.0;
            $avgRevisionHours = null;
        } else {
            $resolutionRate = round(($sudahDiperbaikiCount / $totalRevisiEvents) * 100, 1);
            $avgRevisionHours = !empty($revisionDurations) ? round(array_sum($revisionDurations) / count($revisionDurations), 1) : null;

            $speedScore = 75.0;
            if ($avgRevisionHours !== null) {
                if ($avgRevisionHours <= 48) $speedScore = 100.0;
                elseif ($avgRevisionHours <= 96) $speedScore = 85.0;
                elseif ($avgRevisionHours <= 168) $speedScore = 70.0;
                else $speedScore = 55.0;
            }
            $scorePilar3 = round(($resolutionRate * 0.70) + ($speedScore * 0.30), 1);
        }

        // --- PILAR 4: Cakupan Bidang Standar SPMI (Bobot 15%) ---
        $distinctBidang = count(array_unique(array_filter(array_column($docs, 'bidang_id'))));
        $scorePilar4 = min(100.0, round(($distinctBidang / 9) * 100, 1));

        // --- SKOR AKHIR & PREDIKAT ---
        if ($totalDocs === 0) {
            $finalScore = 0.0;
            $predikat = 'Belum Ada Dokumen';
            $predikatColor = 'secondary';
            $predikatBadge = '<span class="badge bg-secondary text-white">Belum Ada Dokumen</span>';
        } else {
            $finalScore = round(
                ($scorePilar1 * 0.35) +
                ($scorePilar2 * 0.30) +
                ($scorePilar3 * 0.20) +
                ($scorePilar4 * 0.15),
                1
            );

            if ($finalScore >= 85) {
                $predikat = 'Sangat Baik';
                $predikatColor = 'success';
                $predikatBadge = '<span class="badge bg-success text-white"><i class="fas fa-award me-1"></i> Sangat Baik</span>';
            } elseif ($finalScore >= 70) {
                $predikat = 'Baik';
                $predikatColor = 'primary';
                $predikatBadge = '<span class="badge bg-primary text-white"><i class="fas fa-circle-check me-1"></i> Baik</span>';
            } elseif ($finalScore >= 55) {
                $predikat = 'Cukup';
                $predikatColor = 'warning';
                $predikatBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-triangle-exclamation me-1"></i> Cukup</span>';
            } else {
                $predikat = 'Perlu Pembinaan';
                $predikatColor = 'danger';
                $predikatBadge = '<span class="badge bg-danger text-white"><i class="fas fa-circle-exclamation me-1"></i> Perlu Pembinaan</span>';
            }
        }

        // Cek data evaluasi tersimpan di DB
        $evalRecord = self::getEvaluationRecord($db, $targetType, $targetId, $taFilter ?: 'Semua');

        return [
            'target_type'         => $targetType,
            'target_id'           => $targetId,
            'fakultas_id'         => $fakultasId,
            'prodi_id'            => $prodiId,
            'unit_name'           => $unitName,
            'fakultas_name'       => $fakultasName,
            'pimpinan_name'       => $kaprodiOrDekan,
            'tahun_akademik'      => $taFilter ?: 'Semua',

            // Skor & Predikat
            'skor_total'          => $finalScore,
            'predikat'            => $predikat,
            'predikat_color'      => $predikatColor,
            'predikat_badge'      => $predikatBadge,

            // Pilar 1: PPEPP
            'score_pilar1'        => $scorePilar1,
            'cycle_counts'        => $cycleCounts,
            'siklus_terpenuhi'    => $siklusTerpenuhi,
            'siklus_kosong'       => $siklusKosong,

            // Pilar 2: Dokumen & Approval
            'score_pilar2'        => $scorePilar2,
            'total_docs'          => $totalDocs,
            'sesuai_count'        => $sesuaiCount,
            'first_pass_count'    => $firstPassCount,
            'approval_rate'       => $approvalRate,
            'first_pass_rate'     => $firstPassRate,

            // Pilar 3: Revisi
            'score_pilar3'        => $scorePilar3,
            'revisi_macet_count'  => $revisiMacetCount,
            'sudah_diperbaiki_count' => $sudahDiperbaikiCount,
            'avg_revision_hours'  => $avgRevisionHours,

            // Pilar 4: Standar
            'score_pilar4'        => $scorePilar4,
            'distinct_bidang'     => $distinctBidang,

            // Data Evaluasi & Penerusan
            'eval_record'         => $evalRecord,
            'catatan_gpm'         => $evalRecord['catatan_gpm'] ?? null,
            'catatan_lpm'         => $evalRecord['catatan_lpm'] ?? null,
            'is_forwarded'        => !empty($evalRecord['is_forwarded']),
            'forwarded_at'        => $evalRecord['forwarded_at'] ?? null,
            'forwarded_by_name'   => $evalRecord['forwarded_by_name'] ?? null
        ];
    }

    /**
     * Ambil record evaluasi tersimpan
     */
    public static function getEvaluationRecord($db, string $targetType, int $targetId, string $tahunAkademik): ?array {
        try {
            if ($targetType === 'fakultas') {
                $stmt = $db->prepare("
                    SELECT * FROM unit_kinerja_evaluations 
                    WHERE target_type = 'fakultas' AND fakultas_id = ? AND tahun_akademik = ? 
                    LIMIT 1
                ");
                $stmt->execute([$targetId, $tahunAkademik]);
            } else {
                $stmt = $db->prepare("
                    SELECT * FROM unit_kinerja_evaluations 
                    WHERE target_type = 'prodi' AND prodi_id = ? AND tahun_akademik = ? 
                    LIMIT 1
                ");
                $stmt->execute([$targetId, $tahunAkademik]);
            }
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Simpan Catatan / Arahan & Status Penerusan Rapor
     */
    public static function saveEvaluation($db, array $data): bool {
        $targetType = $data['target_type'];
        $fakultasId = (int)$data['fakultas_id'];
        $prodiId    = !empty($data['prodi_id']) ? (int)$data['prodi_id'] : null;
        $ta         = !empty($data['tahun_akademik']) ? trim($data['tahun_akademik']) : 'Semua';

        $stmtCheck = $db->prepare("
            SELECT id FROM unit_kinerja_evaluations 
            WHERE target_type = ? AND fakultas_id = ? AND " . ($prodiId ? "prodi_id = ?" : "prodi_id IS NULL") . " AND tahun_akademik = ?
            LIMIT 1
        ");
        $paramsCheck = [$targetType, $fakultasId];
        if ($prodiId) $paramsCheck[] = $prodiId;
        $paramsCheck[] = $ta;
        $stmtCheck->execute($paramsCheck);
        $existingId = $stmtCheck->fetchColumn();

        if ($existingId) {
            $setParts = [];
            $updateParams = [];

            if (isset($data['skor_otomatis'])) {
                $setParts[] = "skor_otomatis = ?";
                $updateParams[] = $data['skor_otomatis'];
            }
            if (isset($data['predikat'])) {
                $setParts[] = "predikat = ?";
                $updateParams[] = $data['predikat'];
            }
            if (isset($data['siklus_terpenuhi'])) {
                $setParts[] = "siklus_terpenuhi = ?";
                $updateParams[] = is_array($data['siklus_terpenuhi']) ? implode(',', $data['siklus_terpenuhi']) : $data['siklus_terpenuhi'];
            }
            if (isset($data['siklus_kosong'])) {
                $setParts[] = "siklus_kosong = ?";
                $updateParams[] = is_array($data['siklus_kosong']) ? implode(',', $data['siklus_kosong']) : $data['siklus_kosong'];
            }
            if (array_key_exists('catatan_gpm', $data)) {
                $setParts[] = "catatan_gpm = ?";
                $updateParams[] = $data['catatan_gpm'];
                $setParts[] = "gpm_user_id = ?";
                $updateParams[] = $data['gpm_user_id'] ?? null;
                $setParts[] = "gpm_user_name = ?";
                $updateParams[] = $data['gpm_user_name'] ?? null;
                $setParts[] = "gpm_evaluated_at = NOW()";
            }
            if (array_key_exists('catatan_lpm', $data)) {
                $setParts[] = "catatan_lpm = ?";
                $updateParams[] = $data['catatan_lpm'];
                $setParts[] = "lpm_user_id = ?";
                $updateParams[] = $data['lpm_user_id'] ?? null;
                $setParts[] = "lpm_user_name = ?";
                $updateParams[] = $data['lpm_user_name'] ?? null;
                $setParts[] = "lpm_evaluated_at = NOW()";
            }
            if (isset($data['is_forwarded'])) {
                $setParts[] = "is_forwarded = ?";
                $updateParams[] = (int)$data['is_forwarded'];
                if (!empty($data['is_forwarded'])) {
                    $setParts[] = "forwarded_at = NOW()";
                    $setParts[] = "forwarded_by_user_id = ?";
                    $updateParams[] = $data['forwarded_by_user_id'] ?? null;
                    $setParts[] = "forwarded_by_name = ?";
                    $updateParams[] = $data['forwarded_by_name'] ?? null;
                }
            }

            if (!empty($setParts)) {
                $setSql = implode(', ', $setParts);
                $updateParams[] = $existingId;
                $stmtUp = $db->prepare("UPDATE unit_kinerja_evaluations SET {$setSql}, updated_at = NOW() WHERE id = ?");
                return $stmtUp->execute($updateParams);
            }
            return true;
        } else {
            $stmtIns = $db->prepare("
                INSERT INTO unit_kinerja_evaluations 
                (target_type, fakultas_id, prodi_id, tahun_akademik, skor_otomatis, predikat,
                 siklus_terpenuhi, siklus_kosong, catatan_gpm, gpm_user_id, gpm_user_name, gpm_evaluated_at,
                 catatan_lpm, lpm_user_id, lpm_user_name, lpm_evaluated_at, is_forwarded, forwarded_at,
                 forwarded_by_user_id, forwarded_by_name)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $siklusT = isset($data['siklus_terpenuhi']) ? (is_array($data['siklus_terpenuhi']) ? implode(',', $data['siklus_terpenuhi']) : $data['siklus_terpenuhi']) : null;
            $siklusK = isset($data['siklus_kosong']) ? (is_array($data['siklus_kosong']) ? implode(',', $data['siklus_kosong']) : $data['siklus_kosong']) : null;
            $isFwd = !empty($data['is_forwarded']) ? 1 : 0;
            $fwdAt = $isFwd ? date('Y-m-d H:i:s') : null;
            $gpmEvalAt = !empty($data['catatan_gpm']) ? date('Y-m-d H:i:s') : null;
            $lpmEvalAt = !empty($data['catatan_lpm']) ? date('Y-m-d H:i:s') : null;

            return $stmtIns->execute([
                $targetType, $fakultasId, $prodiId, $ta,
                $data['skor_otomatis'] ?? null,
                $data['predikat'] ?? null,
                $siklusT, $siklusK,
                $data['catatan_gpm'] ?? null,
                $data['gpm_user_id'] ?? null,
                $data['gpm_user_name'] ?? null,
                $gpmEvalAt,
                $data['catatan_lpm'] ?? null,
                $data['lpm_user_id'] ?? null,
                $data['lpm_user_name'] ?? null,
                $lpmEvalAt,
                $isFwd, $fwdAt,
                $data['forwarded_by_user_id'] ?? null,
                $data['forwarded_by_name'] ?? null
            ]);
        }
    }
}
