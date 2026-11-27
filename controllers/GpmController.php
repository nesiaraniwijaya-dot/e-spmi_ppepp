<?php
/**
 * Gugus Penjaminan Mutu (GPM) Controller
 * SPMI PPEPP UNIKA Soegijapranata
 * Bertanggung jawab mengelola dokumen PPEPP tingkat Fakultas dan Program Studi di bawahnya
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/AuditLogger.php';

class GpmController extends Controller {

    private int $fakultasId;
    private array $fakultas;
    private array $prodisList;

    public function __construct() {
        parent::__construct();
        Auth::requireRole(['gpm']);

        $this->fakultasId = (int)(Auth::fakultasId() ?? 0);
        if (!$this->fakultasId) {
            Auth::setFlash('danger', 'Akun Anda belum dipetakan ke Fakultas mana pun. Silakan hubungi Admin LPM.');
            redirect('logout');
        }

        $stmt = $this->db->prepare("SELECT * FROM fakultas WHERE id = ?");
        $stmt->execute([$this->fakultasId]);
        $this->fakultas = $stmt->fetch() ?: [];

        if (empty($this->fakultas)) {
            Auth::setFlash('danger', 'Data Fakultas tidak valid atau tidak ditemukan.');
            redirect('logout');
        }

        // Ambil daftar program studi yang berada di bawah naungan fakultas ini
        $stmtP = $this->db->prepare("SELECT * FROM prodis WHERE fakultas_id = ? ORDER BY jenjang ASC, nama_prodi ASC");
        $stmtP->execute([$this->fakultasId]);
        $this->prodisList = $stmtP->fetchAll() ?: [];
    }

    /**
     * Dashboard Gugus Penjaminan Mutu (GPM)
     */
    public function dashboard(): void {
        $prodiIds = array_column($this->prodisList, 'id');
        $prodiIdPlaceholder = !empty($prodiIds) ? implode(',', array_map('intval', $prodiIds)) : '0';

        // Query Statistik Dokumen PPEPP (Gabungan Fakultas & Prodi di bawahnya)
        $scopeWhereSimple = "( fakultas_id = {$this->fakultasId} OR prodi_id IN ({$prodiIdPlaceholder}) )";
        $scopeWhereAlias  = "( d.fakultas_id = {$this->fakultasId} OR d.prodi_id IN ({$prodiIdPlaceholder}) )";

        $cycleStats = [
            'penetapan'    => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND siklus='penetapan' AND deleted_at IS NULL")->fetchColumn(),
            'pelaksanaan'  => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND siklus='pelaksanaan' AND deleted_at IS NULL")->fetchColumn(),
            'evaluasi'     => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND siklus='evaluasi' AND deleted_at IS NULL")->fetchColumn(),
            'pengendalian' => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND siklus='pengendalian' AND deleted_at IS NULL")->fetchColumn(),
            'peningkatan'  => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND siklus='peningkatan' AND deleted_at IS NULL")->fetchColumn(),
            'total'        => 0,
            'arsip'        => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND deleted_at IS NOT NULL")->fetchColumn()
        ];
        $cycleStats['total'] = $cycleStats['penetapan'] + $cycleStats['pelaksanaan'] + $cycleStats['evaluasi'] + $cycleStats['pengendalian'] + $cycleStats['peningkatan'];

        // Statistik per status review (Mendukung alur bertingkat GPM -> LPM)
        $menungguGpmCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review IN ('menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview') AND deleted_at IS NULL")->fetchColumn();
        $menungguLpmCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'menunggu_review_lpm' AND deleted_at IS NULL")->fetchColumn();
        $revisiGpmCount   = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'revisi_gpm' AND deleted_at IS NULL")->fetchColumn();
        $revisiLpmCount   = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review IN ('revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();
        $sesuaiCount      = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'sesuai' AND deleted_at IS NULL")->fetchColumn();
        $draftCount       = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'draft' AND deleted_at IS NULL")->fetchColumn();

        $reviewStats = [
            'menunggu_review_gpm' => $menungguGpmCount,
            'menunggu_review_lpm' => $menungguLpmCount,
            'revisi_gpm'          => $revisiGpmCount,
            'revisi_lpm'          => $revisiLpmCount,
            'perlu_perbaikan'     => $revisiGpmCount + $revisiLpmCount,
            'sudah_diperbaiki'    => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'sudah_diperbaiki' AND deleted_at IS NULL")->fetchColumn(),
            'belum_direview'      => $menungguGpmCount,
            'sesuai'              => $sesuaiCount,
            'draft'               => $draftCount,
        ];

        // Ringkasan Dokumen Tingkat Fakultas
        $fakultasDocCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE (fakultas_id = {$this->fakultasId} AND (level = 'fakultas' OR prodi_id IS NULL)) AND deleted_at IS NULL")->fetchColumn();
        $fakultasPendingCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE (fakultas_id = {$this->fakultasId} AND (level = 'fakultas' OR prodi_id IS NULL)) AND status_review IN ('menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview') AND deleted_at IS NULL")->fetchColumn();
        $fakultasPerbaikanCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE (fakultas_id = {$this->fakultasId} AND (level = 'fakultas' OR prodi_id IS NULL)) AND status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();

        // Ringkasan per Program Studi
        $stmtProdis = $this->db->prepare("
            SELECT p.*, 
                   COUNT(DISTINCT CASE WHEN d.deleted_at IS NULL THEN d.id END) as total_doc,
                   COUNT(DISTINCT CASE WHEN d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND d.deleted_at IS NULL THEN d.id END) as perbaikan_doc,
                   COUNT(DISTINCT CASE WHEN d.status_review IN ('menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview') AND d.deleted_at IS NULL THEN d.id END) as pending_review_doc,
                   COUNT(DISTINCT CASE WHEN d.status_review = 'draft' AND d.deleted_at IS NULL THEN d.id END) as draft_doc
            FROM prodis p 
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id 
            WHERE p.fakultas_id = ? 
            GROUP BY p.id 
            ORDER BY p.jenjang ASC, p.nama_prodi ASC
        ");
        $stmtProdis->execute([$this->fakultasId]);
        $prodiSummary = $stmtProdis->fetchAll();

        // Antrean Dokumen yang Menunggu Review GPM (Prioritas Utama GPM)
        $stmtPending = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, p.nama_prodi, p.jenjang, f.nama_fakultas, u.name as uploader_name
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON d.fakultas_id = f.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE {$scopeWhereAlias} AND d.status_review IN ('menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview') AND d.deleted_at IS NULL 
            ORDER BY FIELD(d.status_review, 'sudah_diperbaiki', 'menunggu_review_gpm', 'belum_direview'), d.updated_at DESC 
            LIMIT 10
        ");
        $stmtPending->execute();
        $pendingReviewDocs = $stmtPending->fetchAll();
        $this->attachFilesToDocs($pendingReviewDocs);

        // Dokumen yang dalam status revisi / perbaikan
        $stmtRevisi = $this->db->prepare("
            SELECT d.*, b.nama_bidang, p.nama_prodi, p.jenjang, f.nama_fakultas, u.name as reviewer_name 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON d.fakultas_id = f.id
            LEFT JOIN users u ON COALESCE(d.reviewed_by, d.gpm_reviewed_by, d.lpm_reviewed_by) = u.id
            WHERE {$scopeWhereAlias} AND d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL 
            ORDER BY 
                GREATEST(
                    COALESCE(d.lpm_reviewed_at, '1970-01-01 00:00:00'),
                    COALESCE(d.gpm_reviewed_at, '1970-01-01 00:00:00'),
                    COALESCE(d.reviewed_at, '1970-01-01 00:00:00'),
                    COALESCE(d.updated_at, '1970-01-01 00:00:00')
                ) DESC, d.id DESC
            LIMIT 10
        ");
        $stmtRevisi->execute();
        $revisiDocs = $stmtRevisi->fetchAll();
        $this->attachFilesToDocs($revisiDocs);

        // Dokumen yang diunggah HARI INI
        $stmtRecent = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, p.nama_prodi, p.jenjang 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN prodis p ON d.prodi_id = p.id
            WHERE {$scopeWhereAlias} AND d.deleted_at IS NULL AND DATE(d.created_at) = CURDATE()
            ORDER BY d.created_at DESC
        ");
        $stmtRecent->execute();
        $recentDocs = $stmtRecent->fetchAll();
        $this->attachFilesToDocs($recentDocs);

        $this->render('gpm/dashboard', [
            'pageTitle'              => 'Dashboard GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'               => $this->fakultas,
            'prodisList'             => $this->prodisList,
            'cycleStats'             => $cycleStats,
            'reviewStats'            => $reviewStats,
            'fakultasDocCount'       => $fakultasDocCount,
            'fakultasPendingCount'   => $fakultasPendingCount,
            'fakultasPerbaikanCount' => $fakultasPerbaikanCount,
            'prodiSummary'           => $prodiSummary,
            'pendingReviewDocs'      => $pendingReviewDocs,
            'revisiDocs'             => $revisiDocs,
            'recentDocs'             => $recentDocs
        ]);
    }

    /**
     * Halaman Khusus Rekapitulasi Status Dokumen per Unit (Dialihkan ke Review Dokumen Mutu)
     */
    public function rekapitulasi(): void {
        redirect('gpm/dokumen');
    }

    /**
     * Helper: Menghitung rekapitulasi status dokumen non-draf untuk Dekanat & seluruh Prodi binaan
     */
    private function getRekapitulasiData(): array {
        // 1. Rekapitulasi Tingkat Fakultas (Dekanat)
        $stmtFak = $this->db->prepare("
            SELECT f.id, f.kode_fakultas, f.nama_fakultas, f.nama_dekan,
                   COUNT(d.id) as total_dokumen_fakultas,
                   SUM(CASE WHEN d.status_review IN ('menunggu_review_gpm', 'belum_direview', 'sudah_diperbaiki') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as perlu_review_count,
                   SUM(CASE WHEN d.status_review IN ('menunggu_review_gpm', 'belum_direview') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as belum_direview_count,
                   SUM(CASE WHEN d.status_review = 'menunggu_review_lpm' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as menunggu_lpm_count,
                   SUM(CASE WHEN d.status_review = 'sudah_diperbaiki' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sudah_diperbaiki_count,
                   SUM(CASE WHEN d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as revisi_count,
                   SUM(CASE WHEN d.status_review = 'sesuai' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sesuai_count
            FROM fakultas f
            LEFT JOIN ppepp_documents d ON f.id = d.fakultas_id AND d.level = 'fakultas' AND d.deleted_at IS NULL AND d.status_review != 'draft'
            WHERE f.id = ?
            GROUP BY f.id
        ");
        $stmtFak->execute([$this->fakultasId]);
        $fakultasSummary = $stmtFak->fetch() ?: [
            'id' => $this->fakultasId,
            'kode_fakultas' => $this->fakultas['kode_fakultas'] ?? '',
            'nama_fakultas' => $this->fakultas['nama_fakultas'] ?? '',
            'nama_dekan' => $this->fakultas['nama_dekan'] ?? '-',
            'total_dokumen_fakultas' => 0,
            'perlu_review_count' => 0,
            'belum_direview_count' => 0,
            'menunggu_lpm_count' => 0,
            'sudah_diperbaiki_count' => 0,
            'revisi_count' => 0,
            'sesuai_count' => 0
        ];

        // 2. Rekapitulasi Program Studi di bawah naungan Fakultas
        $stmtProdi = $this->db->prepare("
            SELECT p.id, p.kode_prodi, p.nama_prodi, p.jenjang, p.fakultas_id, f.nama_fakultas, f.kode_fakultas, p.nama_kaprodi,
                   COUNT(d.id) as total_dokumen,
                   SUM(CASE WHEN d.status_review IN ('menunggu_review_gpm', 'belum_direview', 'sudah_diperbaiki') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as perlu_review_count,
                   SUM(CASE WHEN d.status_review IN ('menunggu_review_gpm', 'belum_direview') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as belum_direview_count,
                   SUM(CASE WHEN d.status_review = 'menunggu_review_lpm' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as menunggu_lpm_count,
                   SUM(CASE WHEN d.status_review = 'sudah_diperbaiki' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sudah_diperbaiki_count,
                   SUM(CASE WHEN d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as revisi_count,
                   SUM(CASE WHEN d.status_review = 'sesuai' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sesuai_count
            FROM prodis p
            JOIN fakultas f ON p.fakultas_id = f.id
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
            WHERE p.fakultas_id = ?
            GROUP BY p.id
            ORDER BY p.jenjang ASC, p.nama_prodi ASC
        ");
        $stmtProdi->execute([$this->fakultasId]);
        $prodiSummaries = $stmtProdi->fetchAll() ?: [];

        return [
            'fakultasSummary' => $fakultasSummary,
            'prodiSummaries'  => $prodiSummaries
        ];
    }

    /**
     * Daftar Dokumen Mutu PPEPP (Fakultas & Prodi)
     */
    public function documents(): void {
        $unitFilter   = trim($_GET['unit'] ?? 'all');
        $siklusFilter = trim($_GET['siklus'] ?? '');
        $bidangFilter = !empty($_GET['bidang_id']) ? (int)$_GET['bidang_id'] : 0;
        $statusFilter = trim($_GET['status_review'] ?? '');
        $tahunFilter  = trim($_GET['tahun_akademik'] ?? '');
        $search       = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $perPage      = !empty($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 50;
        }
        $page = max(1, (int)($_GET['page'] ?? 1));

        $prodiIds = array_column($this->prodisList, 'id');
        $prodiIdPlaceholder = !empty($prodiIds) ? implode(',', array_map('intval', $prodiIds)) : '0';

        // Base WHERE dasar (tanpa filter siklus untuk tab counter real-time)
        $baseWhere = ["d.deleted_at IS NULL"];
        $baseParams = [];

        // Filter Kepemilikan Dokumen (Unit Scope)
        if ($unitFilter === 'fakultas') {
            $baseWhere[] = "(d.fakultas_id = ? AND (d.level = 'fakultas' OR d.prodi_id IS NULL))";
            $baseParams[] = $this->fakultasId;
        } elseif (is_numeric($unitFilter) && in_array((int)$unitFilter, $prodiIds)) {
            $baseWhere[] = "d.prodi_id = ?";
            $baseParams[] = (int)$unitFilter;
        } else {
            // All: Tingkat Fakultas ATAU Seluruh Prodi di bawah fakultas ini
            $baseWhere[] = "(d.fakultas_id = ? OR d.prodi_id IN ({$prodiIdPlaceholder}))";
            $baseParams[] = $this->fakultasId;
        }

        if ($bidangFilter) {
            $baseWhere[] = "d.bidang_id = ?";
            $baseParams[] = $bidangFilter;
        }

        if ($statusFilter) {
            if ($statusFilter === 'menunggu_review_gpm' || $statusFilter === 'belum_direview') {
                $baseWhere[] = "d.status_review IN ('menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview')";
            } elseif ($statusFilter === 'revisi_gpm') {
                $baseWhere[] = "d.status_review = 'revisi_gpm'";
            } elseif ($statusFilter === 'menunggu_review_lpm') {
                $baseWhere[] = "d.status_review = 'menunggu_review_lpm'";
            } elseif ($statusFilter === 'revisi_lpm') {
                $baseWhere[] = "d.status_review IN ('revisi_lpm', 'perlu_perbaikan')";
            } elseif ($statusFilter === 'perlu_perbaikan') {
                $baseWhere[] = "d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan')";
            } elseif (in_array($statusFilter, ['draft', 'sesuai', 'sudah_diperbaiki'])) {
                $baseWhere[] = "d.status_review = ?";
                $baseParams[] = $statusFilter;
            }
        }

        if ($tahunFilter) {
            $baseWhere[] = "d.tahun_akademik = ?";
            $baseParams[] = $tahunFilter;
        }

        if ($search) {
            $baseWhere[] = "(d.nama_dokumen LIKE ? OR d.nomor_dokumen LIKE ? OR d.deskripsi LIKE ? OR b.nama_bidang LIKE ? OR p.nama_prodi LIKE ?)";
            $baseParams[] = "%{$search}%";
            $baseParams[] = "%{$search}%";
            $baseParams[] = "%{$search}%";
            $baseParams[] = "%{$search}%";
            $baseParams[] = "%{$search}%";
        }

        $baseWhereClause = implode(" AND ", $baseWhere);
        $cycleCounts = $this->getSiklusCountsByWhere(
            "FROM ppepp_documents d LEFT JOIN bidang_standar b ON d.bidang_id = b.id LEFT JOIN prodis p ON d.prodi_id = p.id",
            $baseWhereClause,
            $baseParams
        );
        $totalAllCycles = array_sum($cycleCounts);
        $siklusCounts = array_merge(['all' => $totalAllCycles], $cycleCounts);

        $where = $baseWhere;
        $params = $baseParams;
        if ($siklusFilter && in_array($siklusFilter, ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
            $where[] = "LOWER(TRIM(d.siklus)) = ?";
            $params[] = strtolower($siklusFilter);
        }

        $whereClause = implode(" AND ", $where);

        // Count total
        $stmtCount = $this->db->prepare("
            SELECT COUNT(*)
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN prodis p ON d.prodi_id = p.id
            WHERE {$whereClause}
        ");
        $stmtCount->execute($params);
        $totalFiltered = (int)$stmtCount->fetchColumn();

        $totalPages = max(1, (int)ceil($totalFiltered / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        // Ambil data per halaman
        $sql = "
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, p.nama_prodi, p.jenjang, f.nama_fakultas, u.name as reviewer_name,
                   up.name as uploader_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON d.fakultas_id = f.id
            LEFT JOIN users u ON COALESCE(d.reviewed_by, d.gpm_reviewed_by, d.lpm_reviewed_by) = u.id
            LEFT JOIN users up ON d.user_id = up.id
            WHERE {$whereClause}
            ORDER BY 
                CASE 
                    WHEN d.status_review IN ('sudah_diperbaiki', 'menunggu_review_gpm', 'belum_direview') THEN 0
                    WHEN d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') THEN 1
                    WHEN d.status_review = 'menunggu_review_lpm' THEN 2
                    ELSE 3
                END, d.updated_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $documents = $stmt->fetchAll();
        $this->attachFilesToDocs($documents);

        $startNum = $totalFiltered > 0 ? $offset + 1 : 0;
        $endNum = min($offset + $perPage, $totalFiltered);

        if (!empty($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            ob_start();
            $no = $startNum;
            require ROOT_PATH . '/views/gpm/documents_rows.php';
            $rowsHtml = ob_get_clean();

            echo json_encode([
                'success'          => true,
                'html'             => $rowsHtml,
                'page'             => $page,
                'per_page'         => $perPage,
                'total'            => $totalFiltered,
                'total_pages'      => $totalPages,
                'start_num'        => $startNum,
                'end_num'          => $endNum,
                'cycle_counts'     => $cycleCounts,
                'total_all_cycles' => $totalAllCycles
            ]);
            exit;
        }

        // Ambil daftar bidang untuk dropdown filter
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        // Hitung dokumen notifikasi aksi dalam lingkup GPM
        $scopeWhereSimple = "( fakultas_id = {$this->fakultasId} OR prodi_id IN ({$prodiIdPlaceholder}) )";
        $gpmPendingReviewCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review IN ('menunggu_review_gpm', 'sudah_diperbaiki', 'belum_direview') AND deleted_at IS NULL")->fetchColumn();
        $gpmWaitingLpmCount    = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'menunggu_review_lpm' AND deleted_at IS NULL")->fetchColumn();
        $gpmRevisiGpmCount     = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'revisi_gpm' AND deleted_at IS NULL")->fetchColumn();
        $gpmRevisiLpmCount     = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review IN ('revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();
        $gpmPerluCount         = $gpmRevisiGpmCount + $gpmRevisiLpmCount;
        $gpmSesuaiCount        = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'sesuai' AND deleted_at IS NULL")->fetchColumn();
        $gpmDraftCount         = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'draft' AND deleted_at IS NULL")->fetchColumn();

        $this->render('gpm/documents', [
            'pageTitle'              => 'Review Dokumen Mutu PPEPP | GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'               => $this->fakultas,
            'prodisList'             => $this->prodisList,
            'documents'              => $documents,
            'siklusCounts'           => $siklusCounts,
            'bidangList'             => $bidangList,
            'unitFilter'             => $unitFilter,
            'siklusFilter'           => $siklusFilter,
            'bidangFilter'           => $bidangFilter,
            'statusFilter'           => $statusFilter,
            'tahunFilter'            => $tahunFilter,
            'search'                 => $search,
            'gpmPendingReviewCount'  => $gpmPendingReviewCount,
            'gpmWaitingLpmCount'     => $gpmWaitingLpmCount,
            'gpmRevisiGpmCount'      => $gpmRevisiGpmCount,
            'gpmRevisiLpmCount'      => $gpmRevisiLpmCount,
            'gpmPerluCount'          => $gpmPerluCount,
            'gpmSesuaiCount'         => $gpmSesuaiCount,
            'gpmDraftCount'          => $gpmDraftCount,
            'page'                   => $page,
            'perPage'                => $perPage,
            'totalFiltered'          => $totalFiltered,
            'totalPages'             => $totalPages,
            'startNum'               => $startNum,
            'endNum'                 => $endNum
        ]);
    }

    /**
     * Submit Review Dokumen oleh GPM
     * Alur: Dokumen Prodi/Fakultas -> Direview GPM -> Jika Sesuai, Diteruskan ke LPM -> Jika Belum, Minta Revisi ke Pengunggah
     */
    public function submitReview(): void {
        $docId         = !empty($_POST['document_id']) ? (int)$_POST['document_id'] : (!empty($_POST['id']) ? (int)$_POST['id'] : 0);
        $statusReview  = trim($_POST['status_review'] ?? '');
        $catatanReview = trim($_POST['catatan_review'] ?? '');
        $redirectUrl   = trim($_POST['redirect_to'] ?? 'gpm/dokumen');
        $isAjax        = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        // Validasi Status Keputusan Review GPM:
        // 1. 'menunggu_review_lpm' : Rekomendasikan / Teruskan ke Review LPM
        // 2. 'revisi_gpm'          : Kembalikan ke Prodi / Fakultas untuk direvisi
        $allowedStatuses = ['menunggu_review_lpm', 'revisi_gpm'];
        if (!$docId || !in_array($statusReview, $allowedStatuses)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Keputusan review dokumen tidak valid.']);
                exit;
            }
            Auth::setFlash('danger', 'Keputusan review dokumen tidak valid.');
            redirect($redirectUrl);
            return;
        }

        // Pastikan dokumen ada dan dalam ruang lingkup fakultas / prodi binaan GPM
        $doc = $this->findDocWithScope($docId);
        if (!$doc) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Dokumen tidak ditemukan atau di luar wewenang review GPM fakultas Anda.']);
                exit;
            }
            Auth::setFlash('danger', 'Dokumen tidak ditemukan atau di luar wewenang review GPM fakultas Anda.');
            redirect($redirectUrl);
            return;
        }

        // Dokumen yang berstatus sedang diperbaiki unit tidak dapat di-review ulang sebelum unit mengunggah revisi
        if ($doc['status_review'] === 'revisi_gpm') {
            $msg = 'Dokumen ini sedang dalam perbaikan oleh unit sasaran. Menunggu unit mengunggah revisi terbaru sebelum dapat di-review kembali.';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            Auth::setFlash('warning', $msg);
            redirect($redirectUrl);
            return;
        }

        if ($statusReview === 'revisi_gpm' && empty($catatanReview)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Catatan / rekomendasi perbaikan wajib diisi jika meminta revisi dokumen.']);
                exit;
            }
            Auth::setFlash('danger', 'Catatan / rekomendasi perbaikan wajib diisi jika meminta revisi dokumen.');
            redirect($redirectUrl);
            return;
        }

        $reviewerId           = Auth::id() ?: 0;
        $officialReviewerName = 'GPM ' . ($this->fakultas['nama_fakultas'] ?? 'Fakultas');
        $oldStatus            = $doc['status_review'];
        $finalStatus          = $statusReview;
        $tahapReview          = ($statusReview === 'menunggu_review_lpm') ? 'lpm' : 'gpm';
        $finalCatatan         = ($statusReview === 'menunggu_review_lpm')
            ? ($catatanReview ?: 'Disetujui oleh GPM tingkat Fakultas dan diteruskan ke LPM untuk review akhir.')
            : $catatanReview;

        try {
            $this->db->beginTransaction();

            // 1. Update Dokumen PPEPP
            $stmtUpdate = $this->db->prepare("
                UPDATE ppepp_documents 
                SET status_review = ?,
                    tahap_review = ?,
                    catatan_review = ?,
                    gpm_reviewed_by = ?,
                    gpm_reviewed_at = NOW(),
                    gpm_catatan = ?,
                    reviewed_by = ?,
                    reviewed_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmtUpdate->execute([
                $finalStatus,
                $tahapReview,
                $finalCatatan,
                $reviewerId,
                $finalCatatan,
                $reviewerId,
                $docId
            ]);

            // 2. Catat Riwayat Review ke ppepp_document_reviews
            $stmtLog = $this->db->prepare("
                INSERT INTO ppepp_document_reviews 
                (document_id, reviewer_id, reviewer_role, reviewer_name, tahap, status_sebelumnya, status_hasil, catatan, created_at)
                VALUES (?, ?, 'gpm', ?, 'gpm', ?, ?, ?, NOW())
            ");
            $stmtLog->execute([
                $docId,
                $reviewerId,
                $officialReviewerName,
                $oldStatus,
                $finalStatus,
                $finalCatatan
            ]);

            // 2b. Jika status 'revisi_gpm', catat juga tracking revisi untuk unit pengunggah
            if ($finalStatus === 'revisi_gpm') {
                $this->recordRevisionTracking($doc, $reviewerId, $officialReviewerName, $finalCatatan);
            }

            // 3. Catat ke Audit Logger
            $unitName = ($doc['level'] === 'fakultas')
                ? ($this->fakultas['nama_fakultas'] ?? 'Dekanat Fakultas')
                : 'Program Studi';
            AuditLogger::log('UPDATE', 'Review Dokumen GPM', (string)$docId, $doc['nama_dokumen'], $doc, [
                'status_review'  => $finalStatus,
                'tahap_review'   => $tahapReview,
                'catatan_review' => $finalCatatan,
                'reviewer_role'  => 'gpm',
                'reviewer'       => $officialReviewerName,
                'verifikator'    => Auth::user()['name'] ?? 'Petugas GPM'
            ], (int)($doc['prodi_id'] ?? 0), $unitName);

            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[GpmController::submitReview] Error: ' . $e->getMessage());
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menyimpan review: ' . $e->getMessage()]);
                exit;
            }
            Auth::setFlash('danger', 'Terjadi kesalahan sistem saat menyimpan keputusan review.');
            redirect($redirectUrl);
            return;
        }

        $msgSuccess = ($finalStatus === 'menunggu_review_lpm')
            ? "Dokumen '{$doc['nama_dokumen']}' telah berhasil disetujui oleh GPM dan diteruskan ke antrean review LPM."
            : "Dokumen '{$doc['nama_dokumen']}' dikembalikan dengan catatan perbaikan untuk ditindaklanjuti oleh unit pengunggah.";

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $msgSuccess,
                'status'  => $finalStatus
            ]);
            exit;
        }

        if ($finalStatus === 'menunggu_review_lpm') {
            Auth::setFlash('success', $msgSuccess);
        } else {
            Auth::setFlash('warning', $msgSuccess);
        }

        redirect($redirectUrl);
    }

    /**
     * Meneruskan Catatan Evaluasi dari LPM ke Unit Pengunggah (Prodi / Dekanat).
     * Melakukan masking: catatan dan reviewer diposisikan murni atas nama GPM Fakultas,
     * menyembunyikan deliberasi internal LPM dari akses langsung Prodi/Dekanat.
     */
    public function forwardLpmRevision(): void {
        $docId         = !empty($_POST['document_id']) ? (int)$_POST['document_id'] : (!empty($_POST['id']) ? (int)$_POST['id'] : 0);
        $catatanGpm    = trim($_POST['catatan_gpm'] ?? '');
        $redirectUrl   = trim($_POST['redirect_to'] ?? 'gpm/perbaikan');

        if (!$docId) {
            Auth::setFlash('danger', 'ID Dokumen tidak valid.');
            redirect($redirectUrl);
            return;
        }

        $doc = $this->findDocWithScope($docId);
        if (!$doc) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan atau di luar wewenang review GPM fakultas Anda.');
            redirect($redirectUrl);
            return;
        }

        if (empty($catatanGpm)) {
            Auth::setFlash('danger', 'Arahan perbaikan resmi GPM wajib diisi sebelum diteruskan ke unit sasaran.');
            redirect($redirectUrl ?: 'gpm/perbaikan');
            return;
        }

        // Guard idempotensi: cegah duplikasi record jika tombol Teruskan terklik dua kali dalam rentang singkat
        $gpmTs = !empty($doc['gpm_reviewed_at']) ? strtotime($doc['gpm_reviewed_at']) : 0;
        if (($doc['status_review'] ?? '') === 'revisi_gpm'
            && trim((string)($doc['gpm_catatan'] ?? '')) === $catatanGpm
            && $gpmTs > 0
            && (time() - $gpmTs) <= 15
        ) {
            Auth::setFlash('success', "Arahan perbaikan dokumen '{$doc['nama_dokumen']}' telah berhasil diteruskan ke unit sasaran atas nama GPM Fakultas.");
            redirect($redirectUrl ?: 'gpm/perbaikan');
            return;
        }

        $reviewerId           = Auth::id();
        $officialReviewerName = 'GPM ' . ($this->fakultas['nama_fakultas'] ?? 'Fakultas');
        $prevStatus           = $doc['status_review'] ?? 'revisi_lpm';

        // 1. Update Dokumen PPEPP: Status menjadi 'revisi_gpm', catatan resmi diupdate dengan arahan GPM
        $stmtUpdate = $this->db->prepare("
            UPDATE ppepp_documents 
            SET status_review = 'revisi_gpm',
                tahap_review = 'gpm',
                catatan_review = ?,
                gpm_catatan = ?,
                gpm_reviewed_by = ?,
                gpm_reviewed_at = NOW(),
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmtUpdate->execute([
            $catatanGpm,
            $catatanGpm,
            $reviewerId,
            $docId
        ]);

        // 2. Catat Riwayat Review ke ppepp_document_reviews atas nama GPM Fakultas
        $stmtLog = $this->db->prepare("
            INSERT INTO ppepp_document_reviews 
            (document_id, reviewer_id, reviewer_role, reviewer_name, tahap, status_sebelumnya, status_hasil, catatan, created_at)
            VALUES (?, ?, 'gpm', ?, 'gpm', ?, 'revisi_gpm', ?, NOW())
        ");
        $stmtLog->execute([
            $docId,
            $reviewerId,
            $officialReviewerName,
            $prevStatus,
            $catatanGpm
        ]);

        // 3. Catat ke ppepp_document_revisions atas nama GPM untuk tracking unit pengunggah
        $this->recordRevisionTracking($doc, $reviewerId, $officialReviewerName, $catatanGpm);

        // 4. Audit Log
        $unitName = ($doc['level'] === 'fakultas')
            ? ($this->fakultas['nama_fakultas'] ?? 'Dekanat Fakultas')
            : 'Program Studi';
        AuditLogger::log('UPDATE', 'Teruskan Revisi GPM ke Unit', (string)$docId, $doc['nama_dokumen'], $doc, [
            'status_review'  => 'revisi_gpm',
            'tahap_review'   => 'gpm',
            'catatan_review' => $catatanGpm,
            'reviewer_role'  => 'gpm',
            'reviewer'       => $officialReviewerName,
            'verifikator'    => Auth::user()['name'] ?? 'Petugas GPM'
        ], (int)($doc['prodi_id'] ?? 0), $unitName);

        Auth::setFlash('success', "Arahan perbaikan dokumen '{$doc['nama_dokumen']}' telah berhasil diteruskan ke unit sasaran atas nama GPM Fakultas.");
        redirect($redirectUrl ?: 'gpm/perbaikan');
    }

    /**
     * Catat riwayat tracking revisi pada ppepp_document_revisions untuk unit pengunggah.
     */
    private function recordRevisionTracking(array $doc, int $reviewerId, string $reviewerName, string $catatan): void {
        try {
            $docId = (int)$doc['id'];
            $revNumberStmt = $this->db->prepare("SELECT COUNT(*) FROM ppepp_document_revisions WHERE document_id = ?");
            $revNumberStmt->execute([$docId]);
            $nextRevNumber = (int)$revNumberStmt->fetchColumn() + 1;

            $stmtInsRev = $this->db->prepare("
                INSERT INTO ppepp_document_revisions 
                (document_id, cycle_id, revisi_ke, reviewer_id, reviewer_name, reviewer_role, status_review, catatan_review, reviewed_at, created_at)
                VALUES (?, ?, ?, ?, ?, 'gpm', 'revisi_gpm', ?, NOW(), NOW())
            ");
            $stmtInsRev->execute([
                $docId,
                $doc['cycle_id'] ?? null,
                $nextRevNumber,
                $reviewerId,
                $reviewerName,
                $catatan
            ]);
        } catch (\Throwable $e) {
            // Abaikan jika tabel revisi belum tersedia
        }
    }

    /**
     * GPM tidak lagi mengelola pengunggahan dokumen mandiri.
     * Pengunggahan dilakukan oleh Dekanat Fakultas atau Program Studi binaan.
     */
    public function createDocument(): void {
        Auth::setFlash('info', 'GPM kini bertugas mereview dokumen mutu tingkat Fakultas dan Program Studi. Pengunggahan dokumen dilakukan langsung oleh Dekanat Fakultas atau Program Studi terkait.');
        redirect('gpm/dokumen');
    }

    public function editDocument(string $id): void {
        Auth::setFlash('info', 'GPM tidak memiliki izin mengedit dokumen. Untuk memberi arahan atau perbaikan, silakan gunakan tombol Review Dokumen.');
        redirect('gpm/dokumen');
    }

    /**
     * Simpan Dokumen (Dinonaktifkan untuk GPM)
     */
    public function saveDocument(): void {
        Auth::setFlash('info', 'GPM bertugas sebagai penelaah mutu dan tidak memiliki wewenang untuk mengunggah atau mengubah berkas dokumen.');
        redirect('gpm/dokumen');
        return;
    }


    /**
     * Halaman Monitoring Dokumen Revisi dalam Lingkup Fakultas & Prodi
     */
    public function perbaikan(): void {
        $unitFilter = trim($_GET['unit'] ?? 'all');
        $prodiIds = array_column($this->prodisList, 'id');
        $prodiIdPlaceholder = !empty($prodiIds) ? implode(',', array_map('intval', $prodiIds)) : '0';

        $sql = "
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, p.nama_prodi, p.jenjang, f.nama_fakultas, u.name as reviewer_name,
                   up.name as uploader_name
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id 
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON d.fakultas_id = f.id
            LEFT JOIN users u ON COALESCE(d.reviewed_by, d.gpm_reviewed_by, d.lpm_reviewed_by) = u.id
            LEFT JOIN users up ON d.user_id = up.id
            WHERE d.status_review IN ('menunggu_review_gpm', 'belum_direview', 'revisi_gpm', 'revisi_lpm', 'perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL
        ";
        $params = [];

        if ($unitFilter === 'fakultas') {
            $sql .= " AND d.fakultas_id = ? AND (d.level = 'fakultas' OR d.prodi_id IS NULL)";
            $params[] = $this->fakultasId;
        } elseif (is_numeric($unitFilter) && in_array((int)$unitFilter, $prodiIds)) {
            $sql .= " AND d.prodi_id = ?";
            $params[] = (int)$unitFilter;
        } else {
            $sql .= " AND ( (d.fakultas_id = ? AND (d.level = 'fakultas' OR d.prodi_id IS NULL)) OR d.prodi_id IN ({$prodiIdPlaceholder}) )";
            $params[] = $this->fakultasId;
        }

        $sql .= " ORDER BY 
            GREATEST(
                COALESCE(d.lpm_reviewed_at, '1970-01-01 00:00:00'),
                COALESCE(d.gpm_reviewed_at, '1970-01-01 00:00:00'),
                COALESCE(d.reviewed_at, '1970-01-01 00:00:00'),
                COALESCE(d.updated_at, '1970-01-01 00:00:00'),
                COALESCE(d.created_at, '1970-01-01 00:00:00')
            ) DESC, d.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $revisiDocs = $stmt->fetchAll();
        $this->attachFilesToDocs($revisiDocs);

        // Segmentasi dokumen berjenjang:
        // 1. Dokumen yang menunggu review awal GPM
        $menungguGpmDocs = array_values(array_filter($revisiDocs, fn($d) => in_array($d['status_review'] ?? '', ['menunggu_review_gpm', 'belum_direview'])));
        // 2. Dokumen yang baru dikembalikan LPM dan menunggu GPM meneruskannya ke unit
        $lpmRevisiDocs = array_values(array_filter($revisiDocs, fn($d) => in_array($d['status_review'] ?? '', ['revisi_lpm', 'perlu_perbaikan'])));
        // 3. Dokumen yang sudah diteruskan GPM dan sedang dalam pengerjaan oleh unit sasaran
        $unitRevisiDocs = array_values(array_filter($revisiDocs, fn($d) => ($d['status_review'] ?? '') === 'revisi_gpm'));
        // 4. Dokumen hasil revisi dari unit yang siap ditelaah ulang oleh GPM
        $sudahDocs = array_values(array_filter($revisiDocs, fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));
        // 5. Seluruh dokumen dalam cakupan perlu tindakan
        $perluDocs = array_values(array_filter($revisiDocs, fn($d) => in_array($d['status_review'] ?? '', ['menunggu_review_gpm', 'belum_direview', 'revisi_lpm', 'revisi_gpm', 'perlu_perbaikan'])));

        // Hitung statistik ringkas perbaikan
        $scopeWhere = "( (fakultas_id = {$this->fakultasId} AND level = 'fakultas') OR prodi_id IN ({$prodiIdPlaceholder}) )";
        $totalMenungguGpm = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhere} AND status_review IN ('menunggu_review_gpm', 'belum_direview') AND deleted_at IS NULL")->fetchColumn();
        $totalRevisiGpm = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhere} AND status_review = 'revisi_gpm' AND deleted_at IS NULL")->fetchColumn();
        $totalRevisiLpm = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhere} AND status_review IN ('revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();
        $totalSudah     = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhere} AND status_review = 'sudah_diperbaiki' AND deleted_at IS NULL")->fetchColumn();
        $totalPerlu     = $totalRevisiGpm + $totalRevisiLpm;

        $fakultasPerlu = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = {$this->fakultasId} AND level = 'fakultas' AND status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();
        $prodiPerlu    = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id IN ({$prodiIdPlaceholder}) AND level = 'prodi' AND status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();

        $this->render('gpm/perbaikan', [
            'pageTitle'         => 'Monitoring Dokumen Revisi | GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'          => $this->fakultas,
            'prodisList'        => $this->prodisList,
            'revisiDocs'        => $revisiDocs,
            'menungguGpmDocs'   => $menungguGpmDocs,
            'lpmRevisiDocs'     => $lpmRevisiDocs,
            'unitRevisiDocs'    => $unitRevisiDocs,
            'sudahDocs'         => $sudahDocs,
            'perluDocs'         => $perluDocs,
            'unitFilter'        => $unitFilter,
            'totalPerlu'        => $totalPerlu,
            'totalMenungguGpm'  => $totalMenungguGpm,
            'totalRevisiGpm'    => $totalRevisiGpm,
            'totalRevisiLpm'    => $totalRevisiLpm,
            'totalSudah'        => $totalSudah,
            'fakultasPerlu'     => $fakultasPerlu,
            'prodiPerlu'        => $prodiPerlu
        ]);
    }

    /**
     * Draf dokumen dikelola langsung oleh unit pengunggah (Dekanat & Prodi).
     */
    public function draft(): void {
        Auth::setFlash('info', 'Menu draf dokumen dikelola langsung oleh Dekanat dan Program Studi.');
        redirect('gpm/dokumen');
    }

    public function ajukanDraft(string $id): void {
        Auth::setFlash('info', 'Pengajuan draf dokumen dilakukan oleh pengunggah (Dekanat / Prodi).');
        redirect('gpm/dokumen');
    }

    public function arsip(): void {
        Auth::setFlash('info', 'Arsip dan riwayat dokumen terhapus dikelola oleh Dekanat dan Program Studi.');
        redirect('gpm/dokumen');
    }

    public function softDeleteDocument(string $id): void {
        Auth::setFlash('danger', 'GPM tidak memiliki wewenang untuk menghapus dokumen mutu.');
        redirect('gpm/dokumen');
    }

    public function restoreDocument(string $id): void {
        Auth::setFlash('danger', 'GPM tidak memiliki wewenang untuk memulihkan dokumen terhapus.');
        redirect('gpm/dokumen');
    }

    public function forceDeleteDocument(string $id): void {
        Auth::setFlash('danger', 'GPM tidak memiliki wewenang untuk menghapus dokumen secara permanen.');
        redirect('gpm/dokumen');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Cek apakah dokumen berada dalam ruang lingkup fakultas GPM ini
     */
    private function findDocWithScope(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        $doc = $stmt->fetch();
        if (!$doc) return null;
        return $this->isDocInScope($doc) ? $doc : null;
    }

    private function isDocInScope(array $doc): bool {
        // 1. Dokumen yang terasosiasi dengan fakultas GPM ini (baik tingkat fakultas maupun prodi di bawahnya)
        if ((int)($doc['fakultas_id'] ?? 0) === $this->fakultasId) {
            return true;
        }
        // 2. Dokumen tingkat Program Studi yang berada di bawah fakultas GPM ini
        $prodiIds = array_column($this->prodisList, 'id');
        if (!empty($doc['prodi_id']) && in_array((int)$doc['prodi_id'], $prodiIds)) {
            return true;
        }
        return false;
    }

    /**
     * Lampirkan berkas ke array dokumen
     */
    private function attachFilesToDocs(array &$documents): void {
        if (empty($documents)) return;
        $docIds = array_column($documents, 'id');
        if (empty($docIds)) return;

        $inClause = implode(',', array_fill(0, count($docIds), '?'));
        $stmt = $this->db->prepare("SELECT * FROM ppepp_document_files WHERE document_id IN ({$inClause}) ORDER BY sort_order ASC, id ASC");
        $stmt->execute($docIds);
        $allFiles = $stmt->fetchAll();

        $groupedFiles = [];
        foreach ($allFiles as $f) {
            $groupedFiles[$f['document_id']][] = $f;
        }

        foreach ($documents as &$d) {
            $d['files'] = $groupedFiles[$d['id']] ?? [];
            if (empty($d['files']) && !empty($d['file_path'])) {
                $d['files'][] = [
                    'id' => 0,
                    'document_id' => $d['id'],
                    'file_name' => $d['nama_dokumen'] . '.' . ($d['file_extension'] ?: 'pdf'),
                    'file_path' => $d['file_path'],
                    'file_size' => $d['file_size'] ?? '',
                    'file_extension' => $d['file_extension'] ?? 'pdf',
                    'sub_bidang_ids' => !empty($d['sub_bidang_id']) ? [$d['sub_bidang_id']] : []
                ];
            }
            $d['files_count'] = count($d['files']);
        }
        unset($d);
    }

    /**
     * Mengajukan permohonan izin dispensasi buka akses upload ke LPM (GPM)
     */
    public function requestUploadAccess(): void {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $tahunAkademik = normalize_tahun_akademik($input['tahun_akademik'] ?? '');
            $alasan = trim($input['alasan'] ?? '');
            $level = in_array($input['level'] ?? '', ['prodi', 'fakultas']) ? $input['level'] : 'fakultas';
            $prodiId = !empty($input['prodi_id']) ? (int)$input['prodi_id'] : null;

            if (empty($tahunAkademik)) {
                echo json_encode(['success' => false, 'message' => 'Tahun akademik yang diajukan wajib ditentukan.']);
                exit;
            }
            if (empty($alasan)) {
                echo json_encode(['success' => false, 'message' => 'Alasan permohonan izin buka akses wajib diisi.']);
                exit;
            }

            // Cek apakah permohonan pending sudah ada
            if ($level === 'prodi' && $prodiId) {
                $stmtPending = $this->db->prepare("
                    SELECT id FROM upload_access_requests 
                    WHERE level = 'prodi' AND prodi_id = ? AND tahun_akademik = ? AND status = 'pending'
                ");
                $stmtPending->execute([$prodiId, $tahunAkademik]);
            } else {
                $stmtPending = $this->db->prepare("
                    SELECT id FROM upload_access_requests 
                    WHERE level = 'fakultas' AND fakultas_id = ? AND tahun_akademik = ? AND status = 'pending'
                ");
                $stmtPending->execute([$this->fakultasId, $tahunAkademik]);
            }

            if ($stmtPending->fetch()) {
                echo json_encode(['success' => true, 'message' => "Permohonan izin buka akses untuk Tahun Akademik {$tahunAkademik} sudah ada dalam antrean dan sedang menunggu tinjauan LPM."]);
                exit;
            }

            $stmt = $this->db->prepare("
                INSERT INTO upload_access_requests 
                (user_id, level, prodi_id, fakultas_id, tahun_akademik, alasan, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->execute([
                Auth::id(),
                $level,
                $level === 'prodi' ? $prodiId : null,
                $this->fakultasId,
                $tahunAkademik,
                $alasan
            ]);

            // Kirim notifikasi email ke TU LPM
            if (class_exists('MailHelper')) {
                $unitName = 'Fakultas';
                if ($level === 'prodi' && $prodiId) {
                    $pStmt = $this->db->prepare("SELECT nama_prodi FROM prodis WHERE id = ?");
                    $pStmt->execute([$prodiId]);
                    $unitName = $pStmt->fetchColumn() ?: 'Program Studi';
                } else {
                    $fStmt = $this->db->prepare("SELECT nama_fakultas FROM fakultas WHERE id = ?");
                    $fStmt->execute([$this->fakultasId]);
                    $unitName = $fStmt->fetchColumn() ?: 'Fakultas';
                }

                $pemohonName = method_exists('Auth', 'name') ? Auth::name() : ($_SESSION['user_name'] ?? (Auth::user()['name'] ?? 'GPM Fakultas'));
                $pemohonRole = method_exists('Auth', 'roleName') ? Auth::roleName() : ($_SESSION['user_role'] ?? 'GPM Fakultas');
                MailHelper::sendUploadAccessRequestNotification([
                    'pemohon_name'   => $pemohonName,
                    'pemohon_role'   => $pemohonRole,
                    'pemohon_email'  => Auth::user()['email'] ?? '',
                    'unit_name'      => $unitName,
                    'tahun_akademik' => $tahunAkademik,
                    'alasan'         => $alasan,
                    'level'          => $level,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
            }

            echo json_encode([
                'success' => true, 
                'message' => "Permohonan izin buka akses untuk Tahun Akademik {$tahunAkademik} berhasil diajukan melalui sistem dan notifikasi telah dikirimkan ke email TU LPM (tu.lpm@unika.ac.id)."
            ]);
            exit;
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengajukan permohonan: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Master Standar Mutu & Bidang (Khusus Fakultas & Prodi Binaan GPM)
     */
    public function masterStandar(): void {
        $prodiIds = array_column($this->prodisList, 'id');
        $prodiIdPlaceholder = !empty($prodiIds) ? implode(',', array_map('intval', $prodiIds)) : '0';

        // 1. Ambil semua Bidang yang berlaku untuk Fakultas & Prodi ini (Universitas + Fakultas + Prodi)
        $bidangList = $this->db->query("
            SELECT b.*, 
                   f.nama_fakultas, p.nama_prodi, p.jenjang,
                   COUNT(DISTINCT d.id) as total_dokumen
            FROM bidang_standar b
            LEFT JOIN fakultas f ON b.fakultas_id = f.id
            LEFT JOIN prodis p ON b.prodi_id = p.id
            LEFT JOIN ppepp_documents d ON b.id = d.bidang_id AND d.deleted_at IS NULL AND d.status_review != 'draft' 
                 AND (d.fakultas_id = {$this->fakultasId} OR d.prodi_id IN ({$prodiIdPlaceholder}))
            WHERE (
                b.scope = 'universitas'
                OR (b.scope = 'fakultas' AND b.fakultas_id = {$this->fakultasId})
                OR (b.scope = 'prodi' AND b.prodi_id IN ({$prodiIdPlaceholder}))
            )
            GROUP BY b.id
            ORDER BY 
                CASE 
                    WHEN b.scope = 'fakultas' THEN 1
                    WHEN b.scope = 'prodi' THEN 2
                    ELSE 3
                END,
                b.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 2. Ambil semua Sub Bidang Standar (Universitas + Fakultas + Prodi)
        $subBidangList = $this->db->query("
            SELECT sb.*, b.nama_bidang, b.kode_bidang,
                   f.nama_fakultas, p.nama_prodi, p.jenjang,
                   COUNT(DISTINCT d.id) as total_dokumen
            FROM sub_bidang_standar sb
            JOIN bidang_standar b ON sb.bidang_id = b.id
            LEFT JOIN fakultas f ON sb.fakultas_id = f.id
            LEFT JOIN prodis p ON sb.prodi_id = p.id
            LEFT JOIN ppepp_documents d ON sb.id = d.sub_bidang_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
                 AND (d.fakultas_id = {$this->fakultasId} OR d.prodi_id IN ({$prodiIdPlaceholder}))
            WHERE (
                sb.scope = 'universitas'
                OR (sb.scope = 'fakultas' AND sb.fakultas_id = {$this->fakultasId})
                OR (sb.scope = 'prodi' AND sb.prodi_id IN ({$prodiIdPlaceholder}))
            )
            GROUP BY sb.id
            ORDER BY 
                CASE 
                    WHEN sb.scope = 'fakultas' THEN 1
                    WHEN sb.scope = 'prodi' THEN 2
                    ELSE 3
                END,
                sb.bidang_id ASC, sb.kode_sub_bidang ASC, sb.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Hitung statistik & group sub bidang ke masing-masing bidang induk
        $subByBidang = [];
        $statBidangFakultas = 0;
        $statStandarFakultas = 0;
        $statStandarProdi = 0;

        foreach ($subBidangList as $sb) {
            $subByBidang[$sb['bidang_id']][] = $sb;
            if (($sb['scope'] ?? '') === 'fakultas') $statStandarFakultas++;
            elseif (($sb['scope'] ?? '') === 'prodi') $statStandarProdi++;
        }

        foreach ($bidangList as &$b) {
            $b['sub_bidang'] = $subByBidang[$b['id']] ?? [];
            $b['total_sub'] = count($b['sub_bidang']);
            if (($b['scope'] ?? '') === 'fakultas') $statBidangFakultas++;
        }
        unset($b);

        $this->render('gpm/master_standar', [
            'pageTitle'           => 'Master Bidang & Standar Mutu | GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'            => $this->fakultas,
            'prodisList'          => $this->prodisList,
            'bidangList'          => $bidangList,
            'subBidangList'       => $subBidangList,
            'statBidangFakultas'  => $statBidangFakultas,
            'statStandarFakultas' => $statStandarFakultas,
            'statStandarProdi'    => $statStandarProdi
        ]);
    }

    /**
     * Simpan Tambah / Edit Bidang Standar Khusus oleh GPM
     */
    public function saveBidang(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $namaBidang = trim($_POST['nama_bidang'] ?? '');
        $kodeBidang = strtoupper(trim($_POST['kode_bidang'] ?? ''));
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $scope = trim($_POST['scope'] ?? 'fakultas');
        $prodiId = !empty($_POST['prodi_id']) ? (int)$_POST['prodi_id'] : null;
        $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

        if ($namaBidang === '') {
            Auth::setFlash('danger', 'Nama Bidang Standar wajib diisi.');
            redirect('gpm/master-standar');
            return;
        }

        if (!in_array($scope, ['fakultas', 'prodi'])) {
            $scope = 'fakultas';
        }

        $prodiIds = array_column($this->prodisList, 'id');
        if ($scope === 'prodi' && (!in_array($prodiId, $prodiIds))) {
            Auth::setFlash('danger', 'Program Studi yang dipilih tidak valid atau berada di luar naungan fakultas Anda.');
            redirect('gpm/master-standar');
            return;
        }

        if ($scope === 'fakultas') {
            $prodiId = null;
        }

        if ($kodeBidang === '') {
            $prefix = $scope === 'fakultas' ? 'BF-' : 'BP-';
            $kodeBidang = $prefix . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $namaBidang), 0, 6)) . '-' . rand(10, 99);
        }

        if ($id) {
            $stmtCheck = $this->db->prepare("SELECT * FROM bidang_standar WHERE id = ?");
            $stmtCheck->execute([$id]);
            $existing = $stmtCheck->fetch();

            if (!$existing || $existing['scope'] === 'universitas' || (int)$existing['fakultas_id'] !== $this->fakultasId) {
                Auth::setFlash('danger', 'Akses ditolak: Anda tidak memiliki izin mengubah Bidang Standar tingkat Universitas atau Fakultas lain.');
                redirect('gpm/master-standar');
                return;
            }

            $stmt = $this->db->prepare("
                UPDATE bidang_standar 
                SET nama_bidang = ?, kode_bidang = ?, deskripsi = ?, scope = ?, prodi_id = ?, is_active = ?, updated_at = NOW()
                WHERE id = ? AND fakultas_id = ?
            ");
            $stmt->execute([$namaBidang, $kodeBidang, $deskripsi, $scope, $prodiId, $isActive, $id, $this->fakultasId]);
            Auth::setFlash('success', "Bidang Standar \"{$namaBidang}\" berhasil diperbarui.");
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO bidang_standar (kode_bidang, nama_bidang, deskripsi, scope, fakultas_id, prodi_id, created_by_role, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 'gpm', ?, NOW(), NOW())
            ");
            $stmt->execute([$kodeBidang, $namaBidang, $deskripsi, $scope, $this->fakultasId, $prodiId, $isActive]);
            Auth::setFlash('success', "Bidang Standar baru \"{$namaBidang}\" berhasil ditambahkan.");
        }

        redirect('gpm/master-standar');
    }

    /**
     * Hapus Bidang Khusus oleh GPM
     */
    public function deleteBidang(string $id): void {
        $bidangId = (int)$id;
        $stmtCheck = $this->db->prepare("SELECT * FROM bidang_standar WHERE id = ?");
        $stmtCheck->execute([$bidangId]);
        $existing = $stmtCheck->fetch();

        if (!$existing || $existing['scope'] === 'universitas' || (int)$existing['fakultas_id'] !== $this->fakultasId) {
            Auth::setFlash('danger', 'Akses ditolak: Anda tidak dapat menghapus Bidang Standar Universitas atau milik Fakultas lain.');
            redirect('gpm/master-standar');
            return;
        }

        $countDocs = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE bidang_id = {$bidangId} AND deleted_at IS NULL")->fetchColumn();

        if ($countDocs > 0) {
            $this->db->prepare("UPDATE bidang_standar SET is_active = 0 WHERE id = ?")->execute([$bidangId]);
            Auth::setFlash('warning', "Bidang Standar \"{$existing['nama_bidang']}\" dinonaktifkan karena sudah terkait dengan {$countDocs} dokumen mutu aktif.");
        } else {
            $this->db->prepare("DELETE FROM sub_bidang_standar WHERE bidang_id = ? AND scope != 'universitas'")->execute([$bidangId]);
            $this->db->prepare("DELETE FROM bidang_standar WHERE id = ?")->execute([$bidangId]);
            Auth::setFlash('success', "Bidang Standar \"{$existing['nama_bidang']}\" berhasil dihapus.");
        }

        redirect('gpm/master-standar');
    }

    /**
     * Simpan Tambah / Edit Standar Mutu (Sub-Bidang) Khusus oleh GPM
     */
    public function saveSubBidang(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $bidangId = (int)($_POST['bidang_id'] ?? 0);
        $namaSubBidang = trim($_POST['nama_sub_bidang'] ?? '');
        $kodeSubBidang = strtoupper(trim($_POST['kode_sub_bidang'] ?? ''));
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $scope = trim($_POST['scope'] ?? 'fakultas');
        $prodiId = !empty($_POST['prodi_id']) ? (int)$_POST['prodi_id'] : null;
        $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

        if ($namaSubBidang === '' || $bidangId <= 0) {
            Auth::setFlash('danger', 'Bidang Mutu dan Nama Standar Mutu wajib diisi.');
            redirect('gpm/master-standar');
            return;
        }

        if (!in_array($scope, ['fakultas', 'prodi'])) {
            $scope = 'fakultas';
        }

        $prodiIds = array_column($this->prodisList, 'id');
        if ($scope === 'prodi' && (!in_array($prodiId, $prodiIds))) {
            Auth::setFlash('danger', 'Program Studi yang dipilih tidak valid.');
            redirect('gpm/master-standar');
            return;
        }

        if ($scope === 'fakultas') {
            $prodiId = null;
        }

        $prodiIdPlaceholder = !empty($prodiIds) ? implode(',', array_map('intval', $prodiIds)) : '0';
        $bCheck = $this->db->query("
            SELECT * FROM bidang_standar 
            WHERE id = {$bidangId} 
              AND (scope = 'universitas' OR (scope = 'fakultas' AND fakultas_id = {$this->fakultasId}) OR (scope = 'prodi' AND prodi_id IN ({$prodiIdPlaceholder})))
        ")->fetch();

        if (!$bCheck) {
            Auth::setFlash('danger', 'Bidang Mutu yang dipilih tidak valid atau berada di luar wewenang Anda.');
            redirect('gpm/master-standar');
            return;
        }

        if ($kodeSubBidang === '') {
            $prefix = $scope === 'fakultas' ? 'SF-' : 'SP-';
            $kodeSubBidang = $prefix . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $namaSubBidang), 0, 6)) . '-' . rand(10, 99);
        }

        if ($id) {
            $stmtCheck = $this->db->prepare("SELECT * FROM sub_bidang_standar WHERE id = ?");
            $stmtCheck->execute([$id]);
            $existing = $stmtCheck->fetch();

            if (!$existing || $existing['scope'] === 'universitas' || (int)$existing['fakultas_id'] !== $this->fakultasId) {
                Auth::setFlash('danger', 'Akses ditolak: Anda tidak dapat mengubah Standar Mutu Universitas atau milik Fakultas lain.');
                redirect('gpm/master-standar');
                return;
            }

            $stmt = $this->db->prepare("
                UPDATE sub_bidang_standar 
                SET bidang_id = ?, nama_sub_bidang = ?, kode_sub_bidang = ?, deskripsi = ?, scope = ?, prodi_id = ?, is_active = ?, updated_at = NOW()
                WHERE id = ? AND fakultas_id = ?
            ");
            $stmt->execute([$bidangId, $namaSubBidang, $kodeSubBidang, $deskripsi, $scope, $prodiId, $isActive, $id, $this->fakultasId]);
            Auth::setFlash('success', "Standar Mutu \"{$namaSubBidang}\" berhasil diperbarui.");
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO sub_bidang_standar (bidang_id, kode_sub_bidang, nama_sub_bidang, deskripsi, scope, fakultas_id, prodi_id, created_by_role, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'gpm', ?, NOW(), NOW())
            ");
            $stmt->execute([$bidangId, $kodeSubBidang, $namaSubBidang, $deskripsi, $scope, $this->fakultasId, $prodiId, $isActive]);
            Auth::setFlash('success', "Standar Mutu baru \"{$namaSubBidang}\" berhasil ditambahkan.");
        }

        redirect('gpm/master-standar');
    }

    /**
     * Hapus Standar Mutu (Sub-Bidang) Khusus oleh GPM
     */
    public function deleteSubBidang(string $id): void {
        $subId = (int)$id;
        $stmtCheck = $this->db->prepare("SELECT * FROM sub_bidang_standar WHERE id = ?");
        $stmtCheck->execute([$subId]);
        $existing = $stmtCheck->fetch();

        if (!$existing || $existing['scope'] === 'universitas' || (int)$existing['fakultas_id'] !== $this->fakultasId) {
            Auth::setFlash('danger', 'Akses ditolak: Anda tidak dapat menghapus Standar Mutu Universitas atau milik Fakultas lain.');
            redirect('gpm/master-standar');
            return;
        }

        $countDocs = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE sub_bidang_id = {$subId} AND deleted_at IS NULL")->fetchColumn();
        $countFiles = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_document_files WHERE sub_bidang_ids LIKE '%\"{$subId}\"%' OR sub_bidang_ids LIKE '%{$subId}%'")->fetchColumn();

        if ($countDocs > 0 || $countFiles > 0) {
            $this->db->prepare("UPDATE sub_bidang_standar SET is_active = 0 WHERE id = ?")->execute([$subId]);
            Auth::setFlash('warning', "Standar Mutu \"{$existing['nama_sub_bidang']}\" dinonaktifkan karena telah tertaut dengan dokumen mutu aktif.");
        } else {
            $this->db->prepare("DELETE FROM sub_bidang_standar WHERE id = ?")->execute([$subId]);
            Auth::setFlash('success', "Standar Mutu \"{$existing['nama_sub_bidang']}\" berhasil dihapus.");
        }

        redirect('gpm/master-standar');
    }

    /**
     * Halaman Monitoring Kinerja Unit (Dekanat & Prodi di Bawah Fakultas Ini)
     */
    public function monitoringKinerja(): void {
        $tahunAkademik = trim($_GET['tahun_akademik'] ?? 'Semua');
        $academicYears = UnitPerformanceCalculator::getAcademicYears($this->db);

        // 1. Hitung Kinerja Dekanat (Tingkat Fakultas)
        $fakultasPerf = UnitPerformanceCalculator::calculateForUnit($this->db, 'fakultas', $this->fakultasId, $tahunAkademik);

        // 2. Hitung Kinerja Tiap Program Studi di bawah Fakultas ini
        $prodiPerfs = [];
        foreach ($this->prodisList as $prd) {
            $prodiPerfs[] = UnitPerformanceCalculator::calculateForUnit($this->db, 'prodi', (int)$prd['id'], $tahunAkademik);
        }

        // 3. Ringkasan KPI Kinerja Fakultas & Prodi
        $allUnits = array_merge([$fakultasPerf], $prodiPerfs);
        $totalUnits = count($allUnits);
        $validScores = array_filter(array_column($allUnits, 'skor_total'), fn($s) => $s > 0);
        $avgScore = !empty($validScores) ? round(array_sum($validScores) / count($validScores), 1) : 0.0;

        $predikatCounts = [
            'Sangat Baik' => count(array_filter($allUnits, fn($u) => $u['predikat'] === 'Sangat Baik')),
            'Baik'        => count(array_filter($allUnits, fn($u) => $u['predikat'] === 'Baik')),
            'Cukup'       => count(array_filter($allUnits, fn($u) => $u['predikat'] === 'Cukup')),
            'Perlu Pembinaan' => count(array_filter($allUnits, fn($u) => $u['predikat'] === 'Perlu Pembinaan')),
            'Belum Ada Dokumen' => count(array_filter($allUnits, fn($u) => $u['predikat'] === 'Belum Ada Dokumen'))
        ];

        $unitsWithEmptyCycles = count(array_filter($allUnits, fn($u) => !empty($u['siklus_kosong'])));
        $totalForwarded = count(array_filter($allUnits, fn($u) => !empty($u['is_forwarded'])));

        $this->render('gpm/monitoring_kinerja', [
            'pageTitle'            => 'Monitoring Kinerja Prodi & Dekanat',
            'fakultas'             => $this->fakultas,
            'tahunAkademik'        => $tahunAkademik,
            'academicYears'        => $academicYears,
            'fakultasPerf'         => $fakultasPerf,
            'prodiPerfs'           => $prodiPerfs,
            'totalUnits'           => $totalUnits,
            'avgScore'             => $avgScore,
            'predikatCounts'       => $predikatCounts,
            'unitsWithEmptyCycles' => $unitsWithEmptyCycles,
            'totalForwarded'       => $totalForwarded
        ]);
    }

    /**
     * Simpan Catatan Pembinaan GPM & Teruskan Rapor Kinerja ke Unit
     */
    public function saveKinerjaEvaluation(): void {
        $targetType = trim($_POST['target_type'] ?? '');
        $targetId   = (int)($_POST['target_id'] ?? 0);
        $ta         = trim($_POST['tahun_akademik'] ?? 'Semua');
        $catatanGpm = trim($_POST['catatan_gpm'] ?? '');
        $actionType = trim($_POST['action_type'] ?? 'save'); // 'save' | 'forward'

        if (!in_array($targetType, ['fakultas', 'prodi']) || $targetId <= 0) {
            Auth::setFlash('danger', 'Parameter unit sasaran tidak valid.');
            redirect('gpm/monitoring-kinerja');
            return;
        }

        // Validasi kepemilikan unit terhadap fakultas ini
        if ($targetType === 'fakultas' && $targetId !== $this->fakultasId) {
            Auth::setFlash('danger', 'Akses ditolak: Anda hanya dapat mengevaluasi Dekanat fakultas Anda.');
            redirect('gpm/monitoring-kinerja');
            return;
        }
        if ($targetType === 'prodi') {
            $prodiIds = array_column($this->prodisList, 'id');
            if (!in_array($targetId, $prodiIds)) {
                Auth::setFlash('danger', 'Akses ditolak: Program studi tidak berada di bawah fakultas Anda.');
                redirect('gpm/monitoring-kinerja');
                return;
            }
        }

        $perf = UnitPerformanceCalculator::calculateForUnit($this->db, $targetType, $targetId, $ta);
        $isForward = ($actionType === 'forward');

        $saveData = [
            'target_type'         => $targetType,
            'fakultas_id'         => $this->fakultasId,
            'prodi_id'            => ($targetType === 'prodi') ? $targetId : null,
            'tahun_akademik'      => $ta,
            'skor_otomatis'       => $perf['skor_total'],
            'predikat'            => $perf['predikat'],
            'siklus_terpenuhi'    => $perf['siklus_terpenuhi'],
            'siklus_kosong'       => $perf['siklus_kosong'],
            'catatan_gpm'         => $catatanGpm,
            'gpm_user_id'         => Auth::id(),
            'gpm_user_name'       => Auth::name(),
            'is_forwarded'        => $isForward ? 1 : ($perf['is_forwarded'] ? 1 : 0),
            'forwarded_by_user_id'=> $isForward ? Auth::id() : ($perf['eval_record']['forwarded_by_user_id'] ?? null),
            'forwarded_by_name'   => $isForward ? Auth::name() : ($perf['eval_record']['forwarded_by_name'] ?? null)
        ];

        UnitPerformanceCalculator::saveEvaluation($this->db, $saveData);

        if ($isForward) {
            Auth::setFlash('success', "Rapor Kinerja & Arahan Pembinaan berhasil resmi diteruskan ke {$perf['unit_name']}. Unit kini dapat melihat dan mengunduh softfile rapornya.");
        } else {
            Auth::setFlash('success', "Catatan Arahan Pembinaan untuk {$perf['unit_name']} berhasil disimpan.");
        }

        redirect('gpm/monitoring-kinerja?tahun_akademik=' . urlencode($ta));
    }

    /**
     * Cetak / Pratinjau Softfile Rapor Kinerja Unit
     */
    public function cetakRaporKinerja(string $type, string $id): void {
        $targetType = ($type === 'fakultas') ? 'fakultas' : 'prodi';
        $targetId   = (int)$id;
        $ta         = trim($_GET['tahun_akademik'] ?? 'Semua');

        // Validasi hak akses GPM
        if ($targetType === 'fakultas' && $targetId !== $this->fakultasId) {
            die('Akses ditolak.');
        }
        if ($targetType === 'prodi') {
            $prodiIds = array_column($this->prodisList, 'id');
            if (!in_array($targetId, $prodiIds)) {
                die('Akses ditolak.');
            }
        }

        $perf = UnitPerformanceCalculator::calculateForUnit($this->db, $targetType, $targetId, $ta);

        $this->render('shared/rapor_kinerja_cetak', [
            'pageTitle' => 'Rapor Kinerja Mutu SPMI - ' . $perf['unit_name'],
            'perf'      => $perf,
            'isPrint'   => true
        ]);
    }
}
