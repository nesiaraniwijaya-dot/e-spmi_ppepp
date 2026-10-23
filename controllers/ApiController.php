<?php
/**
 * API Controller untuk endpoint AJAX terintegrasi
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';

class ApiController extends Controller {

    public function __construct() {
        parent::__construct();
        // Memerlukan sesi login sah dari pengguna terautentikasi (semua role)
        Auth::requireLogin();
    }

    /**
     * API JSON: Mengambil Riwayat Catatan Revisi Dokumen dari tabel ppepp_document_reviews
     */
    public function reviewHistoryJson(string $id): void {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $docId = (int)$id;
            $stmtDoc = $this->db->prepare("
                SELECT id, nama_dokumen, nomor_dokumen, status_review, tahap_review, level, prodi_id, fakultas_id,
                       gpm_catatan, gpm_reviewed_at, lpm_catatan, lpm_reviewed_at
                FROM ppepp_documents 
                WHERE id = ? AND deleted_at IS NULL
            ");
            $stmtDoc->execute([$docId]);
            $doc = $stmtDoc->fetch();

            if (!$doc) {
                echo json_encode(['success' => false, 'message' => 'Dokumen tidak ditemukan.']);
                exit;
            }

            $stmtHist = $this->db->prepare("
                SELECT r.*,
                       DATE_FORMAT(r.created_at, '%d %b %Y, %H:%i') as created_at_formatted,
                       u.name as reviewer_user_name, u.role as reviewer_user_role
                FROM ppepp_document_reviews r
                LEFT JOIN users u ON r.reviewer_id = u.id
                WHERE r.document_id = ?
                ORDER BY r.created_at ASC, r.id ASC
            ");
            $stmtHist->execute([$docId]);
            $history = $stmtHist->fetchAll();

            // Ambil dari ppepp_document_revisions untuk melengkapi catatan perbaikan uploader
            $stmtRevs = $this->db->prepare("
                SELECT r.*,
                       DATE_FORMAT(r.reviewed_at, '%d %b %Y, %H:%i') as reviewed_at_formatted,
                       DATE_FORMAT(r.revised_at, '%d %b %Y, %H:%i') as revised_at_formatted
                FROM ppepp_document_revisions r
                WHERE r.document_id = ?
                ORDER BY r.revisi_ke ASC, r.id ASC
            ");
            $stmtRevs->execute([$docId]);
            $revisions = $stmtRevs->fetchAll();

            // Integrasikan data tanggapan uploader / revisi jika ada
            if (!empty($history) && !empty($revisions)) {
                foreach ($history as &$hItem) {
                    foreach ($revisions as $rev) {
                        if (!empty($rev['catatan_perbaikan']) && (
                            $hItem['catatan'] === $rev['catatan_review'] ||
                            (!empty($hItem['created_at']) && !empty($rev['reviewed_at']) && substr($hItem['created_at'], 0, 16) === substr($rev['reviewed_at'], 0, 16))
                        )) {
                            $hItem['catatan_perbaikan'] = $rev['catatan_perbaikan'];
                            $hItem['uploader_name'] = $rev['uploader_name'];
                            $hItem['revised_at'] = $rev['revised_at_formatted'] ?? $rev['revised_at'];
                            break;
                        }
                    }
                }
                unset($hItem);

                // Tambahkan entri revisi yang belum terdaftar di ppepp_document_reviews
                foreach ($revisions as $rev) {
                    $matched = false;
                    foreach ($history as $h) {
                        if ($h['catatan'] === $rev['catatan_review'] || 
                            (!empty($h['created_at']) && !empty($rev['reviewed_at']) && substr($h['created_at'], 0, 16) === substr($rev['reviewed_at'], 0, 16))) {
                            $matched = true;
                            break;
                        }
                    }
                    if (!$matched) {
                        $history[] = [
                            'id' => 0,
                            'document_id' => $doc['id'],
                            'reviewer_id' => $rev['reviewer_id'],
                            'reviewer_role' => $rev['reviewer_role'] ?? 'lpm',
                            'reviewer_name' => $rev['reviewer_name'] ?? 'LPM Reviewer',
                            'tahap' => 'lpm',
                            'status_sebelumnya' => null,
                            'status_hasil' => $rev['status_review'],
                            'catatan' => $rev['catatan_review'],
                            'catatan_perbaikan' => $rev['catatan_perbaikan'],
                            'uploader_name' => $rev['uploader_name'],
                            'revised_at' => $rev['revised_at_formatted'] ?? $rev['revised_at'],
                            'created_at' => $rev['reviewed_at'],
                            'reviewer_user_name' => $rev['reviewer_name'] ?? 'LPM Reviewer',
                            'reviewer_user_role' => $rev['reviewer_role'] ?? 'admin_lpm'
                        ];
                    }
                }
                usort($history, function($a, $b) {
                    return strcmp($a['created_at'] ?? '', $b['created_at'] ?? '');
                });
            } elseif (empty($history) && !empty($revisions)) {
                foreach ($revisions as $rev) {
                    $history[] = [
                        'id' => 0,
                        'document_id' => $doc['id'],
                        'reviewer_id' => $rev['reviewer_id'],
                        'reviewer_role' => $rev['reviewer_role'] ?? 'lpm',
                        'reviewer_name' => $rev['reviewer_name'] ?? 'LPM Reviewer',
                        'tahap' => 'lpm',
                        'status_sebelumnya' => null,
                        'status_hasil' => $rev['status_review'],
                        'catatan' => $rev['catatan_review'],
                        'catatan_perbaikan' => $rev['catatan_perbaikan'],
                        'uploader_name' => $rev['uploader_name'],
                        'revised_at' => $rev['revised_at_formatted'] ?? $rev['revised_at'],
                        'created_at' => $rev['reviewed_at'],
                        'reviewer_user_name' => $rev['reviewer_name'] ?? 'LPM Reviewer',
                        'reviewer_user_role' => $rev['reviewer_role'] ?? 'admin_lpm'
                    ];
                }
            }

            // Jika belum ada record di kedua tabel tapi ada catatan lama di ppepp_documents
            if (empty($history)) {
                $syntheticHistory = [];
                if (!empty($doc['gpm_catatan'])) {
                    $gpmDate = $doc['gpm_reviewed_at'] ?? date('Y-m-d H:i:s');
                    $syntheticHistory[] = [
                        'id' => 0,
                        'document_id' => $doc['id'],
                        'reviewer_id' => null,
                        'reviewer_role' => 'gpm',
                        'reviewer_name' => 'Gugus Penjaminan Mutu (GPM)',
                        'tahap' => 'gpm',
                        'status_hasil' => ($doc['status_review'] === 'revisi_gpm') ? 'revisi_gpm' : 'menunggu_review_lpm',
                        'catatan' => $doc['gpm_catatan'],
                        'reviewer_user_name' => 'Gugus Penjaminan Mutu (GPM)',
                        'reviewer_user_role' => 'gpm',
                        'created_at' => $gpmDate,
                        'created_at_formatted' => date('d M Y, H:i', strtotime($gpmDate)) . ' WIB'
                    ];
                }
                if (!empty($doc['lpm_catatan']) || (!empty($doc['catatan_review']) && in_array($doc['status_review'], ['perlu_perbaikan', 'revisi_lpm', 'sesuai']))) {
                    $lpmDate = $doc['lpm_reviewed_at'] ?? date('Y-m-d H:i:s');
                    $syntheticHistory[] = [
                        'id' => 0,
                        'document_id' => $doc['id'],
                        'reviewer_id' => null,
                        'reviewer_role' => 'lpm',
                        'reviewer_name' => 'Pusat Penjaminan Mutu LPM',
                        'tahap' => 'lpm',
                        'status_hasil' => $doc['status_review'],
                        'catatan' => $doc['lpm_catatan'] ?? $doc['catatan_review'] ?? '',
                        'reviewer_user_name' => 'Pusat Penjaminan Mutu LPM',
                        'reviewer_user_role' => 'admin_lpm',
                        'created_at' => $lpmDate,
                        'created_at_formatted' => date('d M Y, H:i', strtotime($lpmDate)) . ' WIB'
                    ];
                }
                if (!empty($syntheticHistory)) {
                    $history = $syntheticHistory;
                }
            }

            // Standardize created_at_formatted for all items
            foreach ($history as &$hItem) {
                if (empty($hItem['created_at_formatted']) && !empty($hItem['created_at'])) {
                    $hItem['created_at_formatted'] = date('d M Y, H:i', strtotime($hItem['created_at'])) . ' WIB';
                } elseif (!empty($hItem['created_at_formatted']) && !str_ends_with($hItem['created_at_formatted'], 'WIB')) {
                    $hItem['created_at_formatted'] .= ' WIB';
                }
            }
            unset($hItem);

            echo json_encode([
                'success' => true,
                'document' => [
                    'id' => $doc['id'],
                    'nama_dokumen' => $doc['nama_dokumen'],
                    'nomor_dokumen' => $doc['nomor_dokumen'],
                    'status_review' => $doc['status_review'],
                    'tahap_review' => $doc['tahap_review'] ?? null,
                    'status_badge' => render_review_status_badge($doc['status_review'], $doc['tahap_review'] ?? null),
                    'status_label' => get_review_status_label($doc['status_review'])
                ],
                'history' => $history
            ]);
            exit;
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * API JSON: Periksa Status Keterbukaan / Kunci Unggah Tahun Akademik Berdasarkan Tanggal Dokumen
     */
    public function checkUploadAccess(): void {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $user = Auth::user();
            $level = trim($_GET['level'] ?? ($_POST['level'] ?? ($user['role'] ?? 'prodi')));
            $prodiId = !empty($_GET['prodi_id']) ? (int)$_GET['prodi_id'] : (!empty($_POST['prodi_id']) ? (int)$_POST['prodi_id'] : ($user['prodi_id'] ?? null));
            $fakultasId = !empty($_GET['fakultas_id']) ? (int)$_GET['fakultas_id'] : (!empty($_POST['fakultas_id']) ? (int)$_POST['fakultas_id'] : ($user['fakultas_id'] ?? null));

            $date = trim($_GET['date'] ?? ($_POST['date'] ?? ''));
            $taInput = trim($_GET['tahun_akademik'] ?? ($_POST['tahun_akademik'] ?? ''));

            if (!empty($date)) {
                $tahunAkademik = calculate_tahun_akademik_from_date($date);
            } elseif (!empty($taInput)) {
                $tahunAkademik = normalize_tahun_akademik($taInput);
            } else {
                $activeAy = get_active_tahun_akademik($this->db);
                $tahunAkademik = $activeAy['tahun_akademik'] ?? date('Y') . '/' . (date('Y') + 1);
            }

            $activeAy = get_active_tahun_akademik($this->db);
            $activeTaStr = $activeAy['tahun_akademik'] ?? '';

            $accessResult = check_academic_year_upload_access($tahunAkademik, $level, $prodiId, $fakultasId, $this->db);

            // Cek apakah unit sudah memiliki permohonan pending untuk TA ini
            $hasPendingRequest = false;
            try {
                $sqlPend = "SELECT id, created_at FROM upload_access_requests WHERE status = 'pending' AND tahun_akademik = ? AND level = ?";
                $pendParams = [$tahunAkademik, $level];
                if ($level === 'prodi') {
                    $sqlPend .= " AND prodi_id = ?";
                    $pendParams[] = (int)$prodiId;
                } else {
                    $sqlPend .= " AND fakultas_id = ?";
                    $pendParams[] = (int)$fakultasId;
                }
                $sqlPend .= " ORDER BY id DESC LIMIT 1";
                $stmtPend = $this->db->prepare($sqlPend);
                $stmtPend->execute($pendParams);
                $pendingReq = $stmtPend->fetch();
                if ($pendingReq) {
                    $hasPendingRequest = true;
                }
            } catch (\Throwable $e) {}

            echo json_encode([
                'success' => true,
                'tanggal_dokumen' => $date,
                'tahun_akademik' => $tahunAkademik,
                'active_tahun_akademik' => $activeTaStr,
                'is_active_year' => ($tahunAkademik === $activeTaStr),
                'is_locked' => $accessResult['is_locked'],
                'reason' => $accessResult['reason'],
                'has_waiver' => $accessResult['has_waiver'],
                'waiver' => $accessResult['waiver'],
                'is_past_year' => $accessResult['is_past_year'],
                'is_past_deadline' => $accessResult['is_past_deadline'],
                'has_pending_request' => $hasPendingRequest
            ]);
            exit;
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'is_locked' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}
