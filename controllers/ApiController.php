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
                SELECT r.*, u.name as reviewer_user_name, u.role as reviewer_user_role
                FROM ppepp_document_reviews r
                LEFT JOIN users u ON r.reviewer_id = u.id
                WHERE r.document_id = ?
                ORDER BY r.created_at ASC, r.id ASC
            ");
            $stmtHist->execute([$docId]);
            $history = $stmtHist->fetchAll();

            // Jika belum ada record di ppepp_document_reviews tapi ada catatan lama di ppepp_documents
            if (empty($history)) {
                $syntheticHistory = [];
                if (!empty($doc['gpm_catatan'])) {
                    $syntheticHistory[] = [
                        'id' => 0,
                        'document_id' => $doc['id'],
                        'reviewer_id' => null,
                        'reviewer_role' => 'gpm',
                        'tahap' => 'gpm',
                        'status_hasil' => ($doc['status_review'] === 'revisi_gpm') ? 'revisi_gpm' : 'menunggu_review_lpm',
                        'catatan' => $doc['gpm_catatan'],
                        'reviewer_user_name' => 'Gugus Penjaminan Mutu (GPM)',
                        'reviewer_user_role' => 'gpm',
                        'created_at' => $doc['gpm_reviewed_at'] ?? date('Y-m-d H:i:s')
                    ];
                }
                if (!empty($doc['lpm_catatan']) || (!empty($doc['catatan_review']) && in_array($doc['status_review'], ['perlu_perbaikan', 'revisi_lpm', 'sesuai']))) {
                    $syntheticHistory[] = [
                        'id' => 0,
                        'document_id' => $doc['id'],
                        'reviewer_id' => null,
                        'reviewer_role' => 'lpm',
                        'tahap' => 'lpm',
                        'status_hasil' => $doc['status_review'],
                        'catatan' => $doc['lpm_catatan'] ?? $doc['catatan_review'] ?? '',
                        'reviewer_user_name' => 'Pusat Penjaminan Mutu LPM',
                        'reviewer_user_role' => 'admin_lpm',
                        'created_at' => $doc['lpm_reviewed_at'] ?? date('Y-m-d H:i:s')
                    ];
                }
                if (!empty($syntheticHistory)) {
                    $history = $syntheticHistory;
                }
            }

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
}
