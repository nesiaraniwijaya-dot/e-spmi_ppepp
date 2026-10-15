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
        Auth::requireRole(['super_admin', 'admin_lpm', 'kepala_pusat_mutu', 'gpm']);

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
        $scopeWhereSimple = "( (fakultas_id = {$this->fakultasId} AND level = 'fakultas') OR prodi_id IN ({$prodiIdPlaceholder}) )";
        $scopeWhereAlias  = "( (d.fakultas_id = {$this->fakultasId} AND d.level = 'fakultas') OR d.prodi_id IN ({$prodiIdPlaceholder}) )";

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
        $menungguGpmCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review IN ('menunggu_review_gpm', 'belum_direview') AND deleted_at IS NULL")->fetchColumn();
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
        $fakultasDocCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = {$this->fakultasId} AND level = 'fakultas' AND deleted_at IS NULL")->fetchColumn();
        $fakultasPerbaikanCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = {$this->fakultasId} AND level = 'fakultas' AND status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();

        // Ringkasan per Program Studi
        $stmtProdis = $this->db->prepare("
            SELECT p.*, 
                   COUNT(DISTINCT CASE WHEN d.deleted_at IS NULL THEN d.id END) as total_doc,
                   COUNT(DISTINCT CASE WHEN d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND d.deleted_at IS NULL THEN d.id END) as perbaikan_doc,
                   COUNT(DISTINCT CASE WHEN d.status_review IN ('menunggu_review_gpm', 'belum_direview') AND d.deleted_at IS NULL THEN d.id END) as pending_review_doc,
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
            WHERE {$scopeWhereAlias} AND d.status_review IN ('menunggu_review_gpm', 'belum_direview') AND d.deleted_at IS NULL 
            ORDER BY d.created_at ASC 
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
            LEFT JOIN users u ON d.reviewed_by = u.id
            WHERE {$scopeWhereAlias} AND d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL 
            ORDER BY FIELD(d.status_review, 'revisi_gpm', 'revisi_lpm', 'perlu_perbaikan', 'sudah_diperbaiki'), d.updated_at DESC
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

        $rekapData = $this->getRekapitulasiData();

        $this->render('gpm/dashboard', [
            'pageTitle'              => 'Dashboard GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'               => $this->fakultas,
            'prodisList'             => $this->prodisList,
            'cycleStats'             => $cycleStats,
            'reviewStats'            => $reviewStats,
            'fakultasDocCount'       => $fakultasDocCount,
            'fakultasPerbaikanCount' => $fakultasPerbaikanCount,
            'prodiSummary'           => $prodiSummary,
            'fakultasSummary'        => $rekapData['fakultasSummary'],
            'prodiSummaries'         => $rekapData['prodiSummaries'],
            'pendingReviewDocs'      => $pendingReviewDocs,
            'revisiDocs'             => $revisiDocs,
            'recentDocs'             => $recentDocs
        ]);
    }

    /**
     * Halaman Khusus Rekapitulasi Status Dokumen per Unit (Dekanat & Program Studi)
     */
    public function rekapitulasi(): void {
        $rekapData = $this->getRekapitulasiData();

        $this->render('gpm/rekapitulasi', [
            'pageTitle'       => 'Rekapitulasi Status Dokumen per Unit - GPM ' . $this->fakultas['nama_fakultas'],
            'activeNav'       => 'gpm_rekapitulasi',
            'fakultas'        => $this->fakultas,
            'prodisList'      => $this->prodisList,
            'fakultasSummary' => $rekapData['fakultasSummary'],
            'prodiSummaries'  => $rekapData['prodiSummaries']
        ]);
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
        $search       = trim($_GET['search'] ?? '');

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
            LEFT JOIN users u ON d.reviewed_by = u.id
            LEFT JOIN users up ON d.user_id = up.id
            WHERE d.deleted_at IS NULL
        ";
        $params = [];

        // Filter Kepemilikan Dokumen (Unit Scope)
        if ($unitFilter === 'fakultas') {
            $sql .= " AND d.fakultas_id = ? AND d.level = 'fakultas'";
            $params[] = $this->fakultasId;
        } elseif (is_numeric($unitFilter) && in_array((int)$unitFilter, $prodiIds)) {
            $sql .= " AND d.prodi_id = ? AND d.level = 'prodi'";
            $params[] = (int)$unitFilter;
        } else {
            // All: Tingkat Fakultas ATAU Prodi-prodi di bawah fakultas ini
            $sql .= " AND ( (d.fakultas_id = ? AND d.level = 'fakultas') OR d.prodi_id IN ({$prodiIdPlaceholder}) )";
            $params[] = $this->fakultasId;
        }

        if ($siklusFilter && in_array($siklusFilter, ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
            $sql .= " AND d.siklus = ?";
            $params[] = $siklusFilter;
        }

        if ($bidangFilter) {
            $sql .= " AND d.bidang_id = ?";
            $params[] = $bidangFilter;
        }

        if ($statusFilter) {
            if ($statusFilter === 'menunggu_review_gpm') {
                $sql .= " AND d.status_review IN ('menunggu_review_gpm', 'belum_direview')";
            } elseif ($statusFilter === 'revisi_gpm') {
                $sql .= " AND d.status_review = 'revisi_gpm'";
            } elseif ($statusFilter === 'menunggu_review_lpm') {
                $sql .= " AND d.status_review = 'menunggu_review_lpm'";
            } elseif ($statusFilter === 'revisi_lpm') {
                $sql .= " AND d.status_review IN ('revisi_lpm', 'perlu_perbaikan')";
            } elseif ($statusFilter === 'perlu_perbaikan') {
                $sql .= " AND d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan')";
            } elseif (in_array($statusFilter, ['draft', 'sesuai', 'sudah_diperbaiki'])) {
                $sql .= " AND d.status_review = ?";
                $params[] = $statusFilter;
            }
        }

        if ($search) {
            $sql .= " AND (d.nama_dokumen LIKE ? OR d.nomor_dokumen LIKE ? OR b.nama_bidang LIKE ? OR p.nama_prodi LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        // Urutan: Prioritaskan dokumen yang Menunggu Review GPM terlebih dahulu, lalu revisi, lalu menunggu LPM, lalu lainnya
        $sql .= " ORDER BY 
            CASE 
                WHEN d.status_review IN ('menunggu_review_gpm', 'belum_direview') THEN 0
                WHEN d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') THEN 1
                WHEN d.status_review = 'menunggu_review_lpm' THEN 2
                ELSE 3
            END, d.updated_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $documents = $stmt->fetchAll();
        $this->attachFilesToDocs($documents);

        // Ambil daftar bidang untuk dropdown filter
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        // Hitung dokumen notifikasi aksi dalam lingkup GPM
        $scopeWhereSimple = "( (fakultas_id = {$this->fakultasId} AND level = 'fakultas') OR prodi_id IN ({$prodiIdPlaceholder}) )";
        $gpmPendingReviewCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review IN ('menunggu_review_gpm', 'belum_direview') AND deleted_at IS NULL")->fetchColumn();
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
            'bidangList'             => $bidangList,
            'unitFilter'             => $unitFilter,
            'siklusFilter'           => $siklusFilter,
            'bidangFilter'           => $bidangFilter,
            'statusFilter'           => $statusFilter,
            'search'                 => $search,
            'gpmPendingReviewCount'  => $gpmPendingReviewCount,
            'gpmWaitingLpmCount'     => $gpmWaitingLpmCount,
            'gpmRevisiGpmCount'      => $gpmRevisiGpmCount,
            'gpmRevisiLpmCount'      => $gpmRevisiLpmCount,
            'gpmPerluCount'          => $gpmPerluCount,
            'gpmSesuaiCount'         => $gpmSesuaiCount,
            'gpmDraftCount'          => $gpmDraftCount
        ]);
    }

    /**
     * Submit Review Dokumen oleh GPM
     * Alur: Dokumen Prodi/Fakultas -> Direview GPM -> Jika Sesuai, Diteruskan ke LPM -> Jika Belum, Minta Revisi ke Pengunggah
     */
    public function submitReview(): void {
        $docId         = !empty($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
        $statusReview  = trim($_POST['status_review'] ?? '');
        $catatanReview = trim($_POST['catatan_review'] ?? '');
        $redirectUrl   = trim($_POST['redirect_to'] ?? 'gpm/dokumen');

        // Validasi Status Keputusan Review GPM:
        // 1. 'menunggu_review_lpm' : Rekomendasikan / Teruskan ke Review LPM
        // 2. 'revisi_gpm'          : Kembalikan ke Prodi / Fakultas untuk direvisi
        $allowedStatuses = ['menunggu_review_lpm', 'revisi_gpm'];
        if (!$docId || !in_array($statusReview, $allowedStatuses)) {
            Auth::setFlash('danger', 'Keputusan review dokumen tidak valid.');
            redirect($redirectUrl);
            return;
        }

        // Pastikan dokumen ada dan dalam ruang lingkup fakultas / prodi binaan GPM
        $doc = $this->findDocWithScope($docId);
        if (!$doc) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan atau di luar wewenang review GPM fakultas Anda.');
            redirect($redirectUrl);
            return;
        }

        if ($statusReview === 'revisi_gpm' && empty($catatanReview)) {
            Auth::setFlash('danger', 'Catatan / rekomendasi perbaikan wajib diisi jika meminta revisi dokumen.');
            redirect($redirectUrl);
            return;
        }

        $reviewerId           = Auth::id();
        $officialReviewerName = 'GPM ' . ($this->fakultas['nama_fakultas'] ?? 'Fakultas');
        $oldStatus            = $doc['status_review'];
        $finalStatus          = $statusReview;
        $tahapReview          = ($statusReview === 'menunggu_review_lpm') ? 'lpm' : 'gpm';
        $finalCatatan         = ($statusReview === 'menunggu_review_lpm')
            ? ($catatanReview ?: 'Disetujui oleh GPM tingkat Fakultas dan diteruskan ke LPM untuk review akhir.')
            : $catatanReview;

        // 1. Update Dokumen PPEPP
        $stmtUpdate = $this->db->prepare("
            UPDATE ppepp_documents 
            SET status_review = ?,
                tahap_review = ?,
                catatan_review = ?,
                gpm_reviewed_by = ?,
                gpm_reviewed_at = NOW(),
                gpm_catatan = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmtUpdate->execute([
            $finalStatus,
            $tahapReview,
            $finalCatatan,
            $reviewerId,
            $finalCatatan,
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

        if ($finalStatus === 'menunggu_review_lpm') {
            Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' telah berhasil disetujui oleh GPM dan diteruskan ke antrean review LPM.");
        } else {
            Auth::setFlash('warning', "Dokumen '{$doc['nama_dokumen']}' dikembalikan dengan catatan perbaikan untuk ditindaklanjuti oleh unit pengunggah.");
        }

        redirect($redirectUrl);
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

    private function _legacySaveDocument(): void {

        // =======================================================
        // 1. BATCH MULTI-UPLOAD GPM (CREATE 1-10 DOKUMEN)
        // =======================================================
        if (!$isEdit && !empty($_POST['docs']) && is_array($_POST['docs'])) {
            $docsData = $_POST['docs'];
            $successCount = 0;
            $createdDocNames = [];

            if (count($docsData) > 10) {
                Auth::setFlash('danger', 'Maksimal dokumen yang dapat diunggah sekaligus adalah 10 dokumen.');
                redirect('gpm/dokumen/create');
                return;
            }

            $this->db->beginTransaction();
            try {
                foreach ($docsData as $cardIdx => $d) {
                    $docNumber = is_numeric($cardIdx) ? ($cardIdx + 1) : 1;
                    $targetScope = trim($d['target_scope'] ?? 'fakultas'); // 'fakultas' atau 'prodi'
                    $selectedProdiId = !empty($d['prodi_id']) ? (int)$d['prodi_id'] : null;

                    // Resolve Level & IDs
                    if ($targetScope === 'prodi') {
                        if (empty($selectedProdiId) || !in_array($selectedProdiId, $validProdiIds)) {
                            throw new \Exception("Program Studi pada Dokumen #{$docNumber} wajib dipilih dengan benar.");
                        }
                        $docLevel = 'prodi';
                        $docProdiId = $selectedProdiId;
                        $docFakultasId = $this->fakultasId;
                    } else {
                        $docLevel = 'fakultas';
                        $docProdiId = null;
                        $docFakultasId = $this->fakultasId;
                    }

                    $namaDokumen = trim($d['nama_dokumen'] ?? '');
                    $nomorDokumen = trim($d['nomor_dokumen'] ?? '');
                    $bidangId = !empty($d['bidang_id']) ? (int)$d['bidang_id'] : null;
                    $subBidangId = !empty($d['sub_bidang_id']) ? (int)$d['sub_bidang_id'] : null;
                    $siklus = trim($d['siklus'] ?? '');
                    $tahunAkademik = trim($d['tahun_akademik'] ?? '');
                    $startDate = null;
                    $endDate = null;
                    $jenisUpload = trim($d['jenis_upload'] ?? 'file');

                    // Validasi tautan Google Drive
                    $rawLinks = [];
                    $linkSubs = !empty($d['external_link_sub_bidang']) && is_array($d['external_link_sub_bidang']) ? array_values($d['external_link_sub_bidang']) : [];
                    if (!empty($d['external_links']) && is_array($d['external_links'])) {
                        $linkNarasis = !empty($d['external_link_narasi']) && is_array($d['external_link_narasi']) ? array_values($d['external_link_narasi']) : [];
                        $linkCanDls = !empty($d['external_link_can_download']) && is_array($d['external_link_can_download']) ? array_values($d['external_link_can_download']) : [];
                        $linkIsLims = !empty($d['external_link_is_limited']) && is_array($d['external_link_is_limited']) ? array_values($d['external_link_is_limited']) : [];
                        $linkPLims = !empty($d['external_link_page_limit']) && is_array($d['external_link_page_limit']) ? array_values($d['external_link_page_limit']) : [];
                        foreach (array_values($d['external_links']) as $lIdx => $linkItem) {
                            $tLink = trim($linkItem);
                            $tNarasi = trim($linkNarasis[$lIdx] ?? ($d['external_link_narasi'][$lIdx] ?? ''));
                            if (!empty($tLink)) {
                                if (!$isDraft && empty($tNarasi)) {
                                    throw new \Exception("Narasi untuk tautan Google Drive pada Dokumen #{$docNumber} wajib diisi.");
                                }
                                $lSubArray = !empty($linkSubs[$lIdx]) && is_array($linkSubs[$lIdx])
                                    ? array_values(array_filter(array_map('intval', $linkSubs[$lIdx])))
                                    : (!empty($d['sub_bidang_id']) ? [(int)$d['sub_bidang_id']] : []);
                                $isLim = isset($linkIsLims[$lIdx]) ? (!empty($linkIsLims[$lIdx]) ? 1 : 0) : 1;
                                $pLim = isset($linkPLims[$lIdx]) ? max(1, (int)$linkPLims[$lIdx]) : 1;
                                $canDl = !empty($linkCanDls[$lIdx]) ? 1 : 0;
                                $rawLinks[] = [
                                    'url' => $tLink,
                                    'narasi' => $tNarasi,
                                    'is_page_limited' => $isLim,
                                    'public_page_limit' => $pLim,
                                    'can_download_public' => $canDl,
                                    'sub_bidang_ids' => $lSubArray
                                ];
                            }
                        }
                    } elseif (!empty($d['external_link'])) {
                        $tLink = trim($d['external_link']);
                        if (!empty($tLink)) {
                            $rawLinks[] = [
                                'url' => $tLink,
                                'narasi' => '',
                                'is_page_limited' => 1,
                                'public_page_limit' => 1,
                                'can_download_public' => 0,
                                'sub_bidang_ids' => !empty($d['sub_bidang_id']) ? [(int)$d['sub_bidang_id']] : []
                            ];
                        }
                    }
                    $externalLink = !empty($rawLinks) ? json_encode($rawLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;

                    // Fallback parent doc public settings inherited from item #1
                    if ($jenisUpload === 'file') {
                        $fIsLim0 = isset($d['file_is_limited'][0]) ? (!empty($d['file_is_limited'][0]) ? 1 : 0) : 1;
                        $publicPageLimit = $fIsLim0 ? max(1, (int)($d['file_page_limit'][0] ?? 1)) : 0;
                        $canDownloadPublic = !empty($d['file_can_download'][0]) ? 1 : 0;
                    } elseif (!empty($rawLinks)) {
                        $publicPageLimit = !empty($rawLinks[0]['is_page_limited']) ? max(1, (int)($rawLinks[0]['public_page_limit'] ?? 1)) : 0;
                        $canDownloadPublic = !empty($rawLinks[0]['can_download_public']) ? 1 : 0;
                    } else {
                        $publicPageLimit = 1;
                        $canDownloadPublic = 0;
                    }
                    $isPageLimited = $publicPageLimit > 0 ? 1 : 0;

                    // Proses berkas yang diunggah
                    $uploadedFilesList = [];
                    $fileKey = "files_{$cardIdx}";
                    $batchFileSubs = $d['file_sub_bidang'] ?? [];
                    if (!empty($_FILES[$fileKey]['name']) && is_array($_FILES[$fileKey]['name'])) {
                        foreach ($_FILES[$fileKey]['name'] as $fIdx => $origName) {
                            if (!empty($origName) && $_FILES[$fileKey]['error'][$fIdx] === UPLOAD_ERR_OK) {
                                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                                if (in_array($ext, $allowedExts)) {
                                    $fileNarasis = !empty($d['file_narasi']) && is_array($d['file_narasi']) ? array_values($d['file_narasi']) : [];
                                    $fNarasi = trim($fileNarasis[$fIdx] ?? ($d['file_narasi'][$fIdx] ?? ''));
                                    if (!$isDraft && empty($fNarasi)) {
                                        throw new \Exception("Narasi untuk berkas '{$origName}' pada Dokumen #{$docNumber} wajib diisi.");
                                    }
                                    $fSubs = !empty($batchFileSubs[$fIdx]) && is_array($batchFileSubs[$fIdx])
                                        ? array_values(array_filter(array_map('intval', $batchFileSubs[$fIdx])))
                                        : ($subBidangId ? [(int)$subBidangId] : []);
                                    $tmpName = $_FILES[$fileKey]['tmp_name'][$fIdx];
                                    $size = $_FILES[$fileKey]['size'][$fIdx];
                                    $fileName = 'GPM_' . $this->fakultasId . '_' . time() . '_' . uniqid() . '.' . $ext;
                                    $targetFile = DOC_UPLOAD_PATH . '/' . $fileName;
                                    if (move_uploaded_file($tmpName, $targetFile)) {
                                        $uploadedFilesList[] = [
                                            'name' => $origName,
                                            'path' => 'uploads/documents/' . $fileName,
                                            'size' => round($size / (1024 * 1024), 2) . ' MB',
                                            'ext'  => $ext,
                                            'narasi' => $fNarasi,
                                            'sub_bidang_ids' => $fSubs
                                        ];
                                    }
                                }
                            }
                        }
                    }

                    // Kumpulkan seluruh sub standar dari berkas dan link
                    $allDocSubIds = [];
                    if ($subBidangId) $allDocSubIds[] = (int)$subBidangId;
                    foreach ($uploadedFilesList as $uf) {
                        if (!empty($uf['sub_bidang_ids'])) {
                            $allDocSubIds = array_merge($allDocSubIds, $uf['sub_bidang_ids']);
                        }
                    }
                    foreach ($rawLinks as $rl) {
                        if (!empty($rl['sub_bidang_ids'])) {
                            $allDocSubIds = array_merge($allDocSubIds, $rl['sub_bidang_ids']);
                        }
                    }
                    $allDocSubIds = array_values(array_unique(array_filter($allDocSubIds)));
                    if (empty($subBidangId) && !empty($allDocSubIds)) {
                        $subBidangId = $allDocSubIds[0];
                    }
                    if (empty($subBidangId) && !empty($bidangId)) {
                        $stmtSbFirst = $this->db->prepare("SELECT id FROM sub_bidang_standar WHERE bidang_id = ? AND is_active = 1 ORDER BY id ASC LIMIT 1");
                        $stmtSbFirst->execute([$bidangId]);
                        $subBidangId = $stmtSbFirst->fetchColumn() ?: null;
                    }
                    if (empty($bidangId) && !empty($subBidangId)) {
                        $stmtB = $this->db->prepare("SELECT bidang_id FROM sub_bidang_standar WHERE id = ?");
                        $stmtB->execute([$subBidangId]);
                        $bidangId = (int)$stmtB->fetchColumn();
                    }

                    // Validasi isian wajib
                    if (empty($namaDokumen)) throw new \Exception("Nama dokumen pada Dokumen #{$docNumber} wajib diisi.");
                    if (empty($tahunAkademik)) throw new \Exception("Tahun akademik pada Dokumen #{$docNumber} wajib diisi.");
                    if (empty($siklus)) throw new \Exception("Kategori Siklus PPEPP pada Dokumen #{$docNumber} wajib dipilih.");
                    if (empty($bidangId)) throw new \Exception("Bidang standar mutu pada Dokumen #{$docNumber} wajib dipilih.");

                    $hasFiles = !empty($uploadedFilesList);
                    $hasLinks = !empty($rawLinks);

                    if ($hasFiles && $hasLinks) {
                        $jenisUpload = 'kombinasi';
                    } elseif ($hasFiles) {
                        $jenisUpload = 'file';
                    } elseif ($hasLinks) {
                        $jenisUpload = 'link';
                    } else {
                        $jenisUpload = trim($d['jenis_upload'] ?? 'file');
                    }

                    // Validasi lampiran jika bukan draf
                    if (!$isDraft) {
                        if (!$hasFiles && !$hasLinks) {
                            throw new \Exception("Setidaknya 1 berkas dokumen atau tautan Google Drive pada Dokumen #{$docNumber} ({$namaDokumen}) wajib diunggah.");
                        }
                    }

                    $primaryFilePath = $hasFiles ? $uploadedFilesList[0]['path'] : null;
                    $primaryFileSize = $hasFiles ? $uploadedFilesList[0]['size'] : null;
                    $primaryFileExt = $hasFiles ? $uploadedFilesList[0]['ext'] : null;
                    $externalLink = $hasLinks ? json_encode($rawLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
                    $statusReview = $isDraft ? 'draft' : 'belum_direview';

                    // Insert ke tabel ppepp_documents
                    $stmtIns = $this->db->prepare("
                        INSERT INTO ppepp_documents (
                            prodi_id, fakultas_id, level, user_id, nama_dokumen, nomor_dokumen,
                            bidang_id, sub_bidang_id, siklus, tahun_akademik,
                            tanggal_berlaku_mulai, tanggal_berlaku_selesai,
                            jenis_upload, file_path, file_size, file_extension, external_link,
                            public_page_limit, can_download_public,
                            status_review, created_at, updated_at
                        ) VALUES (
                            ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, ?,
                            ?, ?,
                            ?, ?, ?, ?, ?,
                            ?, ?,
                            ?, NOW(), NOW()
                        )
                    ");
                    $stmtIns->execute([
                        $docProdiId, $docFakultasId, $docLevel, Auth::id(), $namaDokumen, $nomorDokumen,
                        $bidangId, $subBidangId, $siklus, $tahunAkademik,
                        $startDate, $endDate,
                        $jenisUpload, $primaryFilePath, $primaryFileSize, $primaryFileExt, $externalLink,
                        $publicPageLimit, $canDownloadPublic,
                        $statusReview
                    ]);
                    $docId = (int)$this->db->lastInsertId();

                    // Sinkronisasi sub bidang dokumen induk
                    sync_document_sub_bidang($this->db, $docId, $allDocSubIds);

                    // Simpan berkas ke tabel ppepp_document_files
                    if (!empty($uploadedFilesList)) {
                        $stmtFile = $this->db->prepare("
                            INSERT INTO ppepp_document_files (document_id, file_name, file_path, file_size, file_extension, narasi, sub_bidang_ids, sort_order, is_page_limited, public_page_limit, can_download_public, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                        ");
                        $gSort = 1;
                        $batchFileIsLim = $d['file_is_limited'] ?? [];
                        $batchFilePLim = $d['file_page_limit'] ?? [];
                        $batchFileCanDl = $d['file_can_download'] ?? [];

                        foreach ($uploadedFilesList as $uIdx => $uf) {
                            $fIsLim = isset($batchFileIsLim[$uIdx]) ? (!empty($batchFileIsLim[$uIdx]) ? 1 : 0) : ($isPageLimited ? 1 : 0);
                            $fPLim = isset($batchFilePLim[$uIdx]) ? max(1, (int)$batchFilePLim[$uIdx]) : ($isPageLimited ? $publicPageLimit : 1);
                            $fCanDl = isset($batchFileCanDl[$uIdx]) ? (!empty($batchFileCanDl[$uIdx]) ? 1 : 0) : $canDownloadPublic;
                            $fSubsJson = !empty($uf['sub_bidang_ids']) ? json_encode($uf['sub_bidang_ids']) : null;

                            $stmtFile->execute([
                                $docId, $uf['name'], $uf['path'], $uf['size'], $uf['ext'], $uf['narasi'], $fSubsJson, $gSort++,
                                $fIsLim, $fPLim, $fCanDl
                            ]);
                            $newFileId = (int)$this->db->lastInsertId();
                            if (!empty($uf['sub_bidang_ids'])) {
                                sync_file_sub_bidang($this->db, $newFileId, $uf['sub_bidang_ids']);
                            }
                        }
                    }

                    AuditLogger::log(
                        aksi: 'CREATE',
                        modul: 'Dokumen PPEPP GPM',
                        targetId: (string)$docId,
                        targetName: $namaDokumen,
                        newValues: ['siklus' => $siklus, 'status' => $statusReview, 'level' => $docLevel, 'prodi_id' => $docProdiId]
                    );

                    $successCount++;
                    $createdDocNames[] = $namaDokumen;
                }

                $this->db->commit();

                $redirectTarget = $isDraft ? 'gpm/draft' : 'gpm/dokumen';
                $msg = $isDraft
                    ? "Berhasil menyimpan {$successCount} dokumen sebagai draf."
                    : "Berhasil mengunggah {$successCount} dokumen mutu ke sistem dan diteruskan ke LPM.";
                Auth::setFlash('success', $msg);
                redirect($redirectTarget);
                return;

            } catch (\Throwable $e) {
                $this->db->rollBack();
                Auth::setFlash('danger', 'Gagal memproses batch dokumen: ' . $e->getMessage());
                redirect('gpm/dokumen/create');
                return;
            }
        }

        // =======================================================
        // 2. SINGLE EDIT DOKUMEN OLEH GPM
        // =======================================================
        if ($isEdit) {
            $id = (int)$_POST['id'];
            $existing = $this->findDocWithScope($id);

            if (!$existing) {
                Auth::setFlash('danger', 'Dokumen tidak ditemukan atau bukan milik unit dalam fakultas Anda.');
                redirect('gpm/dokumen');
                return;
            }

            $targetScope = trim($_POST['target_scope'] ?? $existing['level']);
            $selectedProdiId = !empty($_POST['prodi_id']) ? (int)$_POST['prodi_id'] : null;

            if ($targetScope === 'prodi') {
                if (empty($selectedProdiId) || !in_array($selectedProdiId, $validProdiIds)) {
                    Auth::setFlash('danger', 'Pilih Program Studi yang valid.');
                    redirect('gpm/dokumen/edit/' . $id);
                    return;
                }
                $docLevel = 'prodi';
                $docProdiId = $selectedProdiId;
                $docFakultasId = $this->fakultasId;
            } else {
                $docLevel = 'fakultas';
                $docProdiId = null;
                $docFakultasId = $this->fakultasId;
            }

            $namaDokumen = trim($_POST['nama_dokumen'] ?? '');
            $nomorDokumen = trim($_POST['nomor_dokumen'] ?? '');
            $bidangId = !empty($_POST['bidang_id']) ? (int)$_POST['bidang_id'] : null;
            $subBidangId = !empty($_POST['sub_bidang_id']) ? (int)$_POST['sub_bidang_id'] : null;
            $siklus = trim($_POST['siklus'] ?? '');
            $tahunAkademik = trim($_POST['tahun_akademik'] ?? '');
            $startDate = null;
            $endDate = null;
            $jenisUpload = trim($_POST['jenis_upload'] ?? 'file');

            // Parse multi-links GDrive
            $rawLinks = [];
            $linkSubs = !empty($_POST['external_link_sub_bidang']) && is_array($_POST['external_link_sub_bidang']) ? array_values($_POST['external_link_sub_bidang']) : [];
            if (!empty($_POST['external_links']) && is_array($_POST['external_links'])) {
                $linkNarasis = !empty($_POST['external_link_narasi']) && is_array($_POST['external_link_narasi']) ? array_values($_POST['external_link_narasi']) : [];
                $linkCanDls = !empty($_POST['external_link_can_download']) && is_array($_POST['external_link_can_download']) ? array_values($_POST['external_link_can_download']) : [];
                $linkIsLims = !empty($_POST['external_link_is_limited']) && is_array($_POST['external_link_is_limited']) ? array_values($_POST['external_link_is_limited']) : [];
                $linkPLims = !empty($_POST['external_link_page_limit']) && is_array($_POST['external_link_page_limit']) ? array_values($_POST['external_link_page_limit']) : [];
                foreach (array_values($_POST['external_links']) as $lIdx => $linkItem) {
                    $tLink = trim($linkItem);
                    $tNarasi = trim($linkNarasis[$lIdx] ?? ($_POST['external_link_narasi'][$lIdx] ?? ''));
                    if (!empty($tLink)) {
                        if (!$isDraft && empty($tNarasi)) {
                            Auth::setFlash('danger', 'Narasi untuk setiap tautan Google Drive wajib diisi.');
                            redirect('gpm/dokumen/edit/' . $id);
                            return;
                        }
                        $lSubArray = !empty($linkSubs[$lIdx]) && is_array($linkSubs[$lIdx])
                            ? array_values(array_filter(array_map('intval', $linkSubs[$lIdx])))
                            : ($subBidangId ? [(int)$subBidangId] : []);
                        $isLim = isset($linkIsLims[$lIdx]) ? (!empty($linkIsLims[$lIdx]) ? 1 : 0) : 1;
                        $pLim = isset($linkPLims[$lIdx]) ? max(1, (int)$linkPLims[$lIdx]) : 1;
                        $canDl = !empty($linkCanDls[$lIdx]) ? 1 : 0;
                        $rawLinks[] = [
                            'url' => $tLink,
                            'narasi' => $tNarasi,
                            'is_page_limited' => $isLim,
                            'public_page_limit' => $pLim,
                            'can_download_public' => $canDl,
                            'sub_bidang_ids' => $lSubArray
                        ];
                    }
                }
            } elseif (!empty($_POST['external_link'])) {
                $tLink = trim($_POST['external_link']);
                if (!empty($tLink)) {
                    $rawLinks[] = [
                        'url' => $tLink,
                        'narasi' => '',
                        'is_page_limited' => 1,
                        'public_page_limit' => 1,
                        'can_download_public' => 0,
                        'sub_bidang_ids' => $subBidangId ? [(int)$subBidangId] : []
                    ];
                }
            }
            $externalLink = !empty($rawLinks) ? json_encode($rawLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;

            // Parent document access settings: inherit from item #1 (file #1 or link #1)
            if ($jenisUpload === 'file') {
                $firstExId = null;
                if (!empty($_POST['existing_file_narasi'])) {
                    foreach (array_keys($_POST['existing_file_narasi']) as $fid) {
                        if (!in_array((int)$fid, array_map('intval', $_POST['delete_file_ids'] ?? []), true)) {
                            $firstExId = (int)$fid;
                            break;
                        }
                    }
                }
                if ($firstExId !== null) {
                    $isLim0 = !empty($_POST['existing_file_is_limited'][$firstExId]) ? 1 : 0;
                    $publicPageLimit = $isLim0 ? max(1, (int)($_POST['existing_file_page_limit'][$firstExId] ?? 1)) : 0;
                    $canDownloadPublic = !empty($_POST['existing_file_can_download'][$firstExId]) ? 1 : 0;
                } elseif (!empty($_POST['new_file_is_limited'])) {
                    $isLim0 = !empty($_POST['new_file_is_limited'][0]) ? 1 : 0;
                    $publicPageLimit = $isLim0 ? max(1, (int)($_POST['new_file_page_limit'][0] ?? 1)) : 0;
                    $canDownloadPublic = !empty($_POST['new_file_can_download'][0]) ? 1 : 0;
                } else {
                    $publicPageLimit = 1;
                    $canDownloadPublic = 0;
                }
            } elseif (!empty($rawLinks)) {
                $publicPageLimit = !empty($rawLinks[0]['is_page_limited']) ? max(1, (int)($rawLinks[0]['public_page_limit'] ?? 1)) : 0;
                $canDownloadPublic = !empty($rawLinks[0]['can_download_public']) ? 1 : 0;
            } else {
                $publicPageLimit = 1;
                $canDownloadPublic = 0;
            }
            $isPageLimited = $publicPageLimit > 0 ? 1 : 0;

            // Kumpulkan seluruh sub standar dari link dan berkas
            $allDocSubIds = [];
            if ($subBidangId) $allDocSubIds[] = (int)$subBidangId;
            foreach ($rawLinks as $rl) {
                if (!empty($rl['sub_bidang_ids'])) $allDocSubIds = array_merge($allDocSubIds, $rl['sub_bidang_ids']);
            }
            if (!empty($_POST['existing_file_sub_bidang']) && is_array($_POST['existing_file_sub_bidang'])) {
                foreach ($_POST['existing_file_sub_bidang'] as $fSubs) {
                    if (is_array($fSubs)) $allDocSubIds = array_merge($allDocSubIds, $fSubs);
                }
            }
            if (!empty($_POST['new_file_sub_bidang']) && is_array($_POST['new_file_sub_bidang'])) {
                foreach ($_POST['new_file_sub_bidang'] as $fSubs) {
                    if (is_array($fSubs)) $allDocSubIds = array_merge($allDocSubIds, $fSubs);
                }
            }
            $allDocSubIds = array_values(array_unique(array_filter(array_map('intval', $allDocSubIds))));

            if (empty($subBidangId) && !empty($allDocSubIds)) {
                $subBidangId = $allDocSubIds[0];
            }
            if (empty($subBidangId) && !empty($bidangId)) {
                $stmtSbFirst = $this->db->prepare("SELECT id FROM sub_bidang_standar WHERE bidang_id = ? AND is_active = 1 ORDER BY id ASC LIMIT 1");
                $stmtSbFirst->execute([$bidangId]);
                $subBidangId = $stmtSbFirst->fetchColumn() ?: null;
            }
            if (empty($bidangId) && !empty($subBidangId)) {
                $stmtB = $this->db->prepare("SELECT bidang_id FROM sub_bidang_standar WHERE id = ?");
                $stmtB->execute([$subBidangId]);
                $bidangId = (int)$stmtB->fetchColumn();
            }

            if (empty($namaDokumen) || empty($tahunAkademik) || empty($siklus) || empty($bidangId)) {
                Auth::setFlash('danger', 'Harap lengkapi semua kolom bertanda bintang (*).');
                redirect('gpm/dokumen/edit/' . $id);
                return;
            }

            // Hapus berkas terpilih jika diminta
            $deletedLegacy = false;
            if (!empty($_POST['delete_file_ids']) && is_array($_POST['delete_file_ids'])) {
                foreach ($_POST['delete_file_ids'] as $delFid) {
                    $delFidInt = (int)$delFid;
                    if ($delFidInt === 0 || $delFid === 'legacy') {
                        $deletedLegacy = true;
                        if (!empty($existing['file_path']) && file_exists(ROOT_PATH . '/' . $existing['file_path'])) {
                            @unlink(ROOT_PATH . '/' . $existing['file_path']);
                        }
                        $existing['file_path'] = null;
                        $existing['file_size'] = null;
                        $existing['file_extension'] = null;
                    } else {
                        $stmtF = $this->db->prepare("SELECT file_path FROM ppepp_document_files WHERE id = ? AND document_id = ?");
                        $stmtF->execute([$delFidInt, $id]);
                        $filePathToDel = $stmtF->fetchColumn();
                        if ($filePathToDel && file_exists(ROOT_PATH . '/' . $filePathToDel)) {
                            @unlink(ROOT_PATH . '/' . $filePathToDel);
                        }
                        $stmtDelFile = $this->db->prepare("DELETE FROM ppepp_document_files WHERE id = ? AND document_id = ?");
                        $stmtDelFile->execute([$delFidInt, $id]);
                    }
                }
            }

            // Update narasi & sub-standar berkas yang sudah ada
            if (!empty($_POST['existing_file_narasi']) && is_array($_POST['existing_file_narasi'])) {
                $deletedIds = array_map('intval', $_POST['delete_file_ids'] ?? []);
                $stmtUpdNarasi = $this->db->prepare("
                    UPDATE ppepp_document_files 
                    SET narasi = ?, is_page_limited = ?, public_page_limit = ?, can_download_public = ? 
                    WHERE id = ? AND document_id = ?
                ");
                $exIsLim = $_POST['existing_file_is_limited'] ?? [];
                $exPLim = $_POST['existing_file_page_limit'] ?? [];
                $exCanDl = $_POST['existing_file_can_download'] ?? [];
                $exSubs = $_POST['existing_file_sub_bidang'] ?? [];

                foreach ($_POST['existing_file_narasi'] as $fId => $fNarasi) {
                    $fId = (int)$fId;
                    if ($fId <= 0 || in_array($fId, $deletedIds, true)) continue;
                    $fIsLimVal = !empty($exIsLim[$fId]) ? 1 : 0;
                    $fPLimVal = $fIsLimVal ? max(1, (int)($exPLim[$fId] ?? 1)) : 1;
                    $fCanDlVal = !empty($exCanDl[$fId]) ? 1 : 0;

                    $stmtUpdNarasi->execute([trim($fNarasi), $fIsLimVal, $fPLimVal, $fCanDlVal, $fId, $id]);

                    $fSubArray = !empty($exSubs[$fId]) && is_array($exSubs[$fId])
                        ? array_values(array_filter(array_map('intval', $exSubs[$fId])))
                        : ($subBidangId ? [(int)$subBidangId] : []);
                    sync_file_sub_bidang($this->db, $fId, $fSubArray);
                }
            }

            // Upload berkas baru jika ada
            if (!empty($_FILES['files']['name']) && is_array($_FILES['files']['name'])) {
                $stmtNewFile = $this->db->prepare("
                    INSERT INTO ppepp_document_files (document_id, file_name, file_path, file_size, file_extension, narasi, sub_bidang_ids, sort_order, is_page_limited, public_page_limit, can_download_public, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $newNarasis = !empty($_POST['new_file_narasi']) && is_array($_POST['new_file_narasi']) ? array_values($_POST['new_file_narasi']) : [];
                $newIsLim = $_POST['new_file_is_limited'] ?? [];
                $newPLim = $_POST['new_file_page_limit'] ?? [];
                $newCanDl = $_POST['new_file_can_download'] ?? [];
                $newSubs = $_POST['new_file_sub_bidang'] ?? [];
                $nextSort = 1;

                foreach ($_FILES['files']['name'] as $idx => $origName) {
                    if (!empty($origName) && $_FILES['files']['error'][$idx] === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                        if (in_array($ext, $allowedExts)) {
                            $fNarasi = trim($newNarasis[$idx] ?? ($_POST['file_narasi'][$idx] ?? ''));
                            if (empty($fNarasi)) {
                                $fNarasi = pathinfo($origName, PATHINFO_FILENAME);
                            }
                            $fIsLimVal = isset($newIsLim[$idx]) ? (!empty($newIsLim[$idx]) ? 1 : 0) : ($isPageLimited ? 1 : 0);
                            $fPLimVal = isset($newPLim[$idx]) ? max(1, (int)$newPLim[$idx]) : ($isPageLimited ? $publicPageLimit : 1);
                            $fCanDlVal = isset($newCanDl[$idx]) ? (!empty($newCanDl[$idx]) ? 1 : 0) : $canDownloadPublic;
                            $fSubArray = !empty($newSubs[$idx]) && is_array($newSubs[$idx])
                                ? array_values(array_filter(array_map('intval', $newSubs[$idx])))
                                : ($subBidangId ? [(int)$subBidangId] : []);
                            $fSubsJson = json_encode($fSubArray);

                            $tmpName = $_FILES['files']['tmp_name'][$idx];
                            $size = $_FILES['files']['size'][$idx];
                            $fileName = 'GPM_' . $this->fakultasId . '_' . time() . '_' . uniqid() . '.' . $ext;
                            $targetFile = DOC_UPLOAD_PATH . '/' . $fileName;
                            if (move_uploaded_file($tmpName, $targetFile)) {
                                $stmtNewFile->execute([
                                    $id, $origName, 'uploads/documents/' . $fileName,
                                    round($size / (1024 * 1024), 2) . ' MB', $ext, $fNarasi, $fSubsJson, $nextSort++,
                                    $fIsLimVal, $fPLimVal, $fCanDlVal
                                ]);
                                $newFileId = (int)$this->db->lastInsertId();
                                sync_file_sub_bidang($this->db, $newFileId, $fSubArray);
                            }
                        }
                    }
                }
            }

            // Dapatkan file utama terbaru
            $stmtPrimary = $this->db->prepare("SELECT * FROM ppepp_document_files WHERE document_id = ? ORDER BY sort_order ASC, id ASC LIMIT 1");
            $stmtPrimary->execute([$id]);
            $primaryFile = $stmtPrimary->fetch();

            if ($primaryFile) {
                $filePath = $primaryFile['file_path'];
                $fileSize = $primaryFile['file_size'];
                $fileExt = $primaryFile['file_extension'];
            } else {
                if (!$deletedLegacy && !empty($existing['file_path'])) {
                    $filePath = $existing['file_path'];
                    $fileSize = $existing['file_size'] ?? null;
                    $fileExt = $existing['file_extension'] ?? null;
                } else {
                    $filePath = null;
                    $fileSize = null;
                    $fileExt = null;
                }
            }

            $hasFiles = !empty($filePath);
            $hasLinks = !empty($rawLinks);

            if ($hasFiles && $hasLinks) {
                $jenisUpload = 'kombinasi';
            } elseif ($hasFiles) {
                $jenisUpload = 'file';
                $externalLink = null;
            } elseif ($hasLinks) {
                $jenisUpload = 'link';
                $filePath = null;
                $fileSize = null;
                $fileExt = null;
            } else {
                $jenisUpload = trim($_POST['jenis_upload'] ?? 'file');
            }

            if (!$isDraft) {
                if (!$hasFiles && !$hasLinks) {
                    Auth::setFlash('danger', 'Dokumen aktif wajib memiliki minimal 1 berkas fisik yang diunggah atau tautan Google Drive.');
                    redirect('gpm/dokumen/edit/' . $id);
                    return;
                }
            }

            // Status review transition
            $newStatusReview = $existing['status_review'];
            if ($isDraft) {
                $newStatusReview = 'draft';
            } elseif ($existing['status_review'] === 'perlu_perbaikan') {
                $newStatusReview = 'sudah_diperbaiki';
            } elseif ($existing['status_review'] === 'draft') {
                $newStatusReview = 'belum_direview';
            }

            // Update dokumen
            $stmtUpd = $this->db->prepare("
                UPDATE ppepp_documents SET
                    prodi_id = ?, fakultas_id = ?, level = ?,
                    nama_dokumen = ?, nomor_dokumen = ?,
                    bidang_id = ?, sub_bidang_id = ?, siklus = ?, tahun_akademik = ?,
                    tanggal_berlaku_mulai = ?, tanggal_berlaku_selesai = ?,
                    jenis_upload = ?, file_path = ?, file_size = ?, file_extension = ?, external_link = ?,
                    public_page_limit = ?, can_download_public = ?,
                    status_review = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmtUpd->execute([
                $docProdiId, $docFakultasId, $docLevel,
                $namaDokumen, $nomorDokumen,
                $bidangId, $subBidangId, $siklus, $tahunAkademik,
                $startDate, $endDate,
                $jenisUpload, $filePath, $fileSize, $fileExt, $externalLink,
                $publicPageLimit, $canDownloadPublic,
                $newStatusReview, $id
            ]);

            // Sinkronisasi sub bidang ke dokumen induk
            sync_document_sub_bidang($this->db, (int)$id, $allDocSubIds);

            AuditLogger::log(
                aksi: 'UPDATE',
                modul: 'Dokumen PPEPP GPM',
                targetId: (string)$id,
                targetName: $namaDokumen,
                oldValues: ['nama' => $existing['nama_dokumen'], 'status' => $existing['status_review']],
                newValues: ['nama' => $namaDokumen, 'status' => $newStatusReview, 'level' => $docLevel, 'prodi_id' => $docProdiId]
            );

            if ($existing['status_review'] === 'perlu_perbaikan') {
                Auth::setFlash('success', 'Perbaikan dokumen berhasil disimpan dan status diperbarui menjadi "Sudah Diperbaiki" untuk ditinjau kembali oleh LPM.');
                redirect('gpm/perbaikan');
            } else {
                Auth::setFlash('success', 'Dokumen mutu berhasil diperbarui.');
                redirect('gpm/dokumen');
            }
            return;
        }

        redirect('gpm/dokumen');
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
            LEFT JOIN users u ON d.reviewed_by = u.id
            LEFT JOIN users up ON d.user_id = up.id
            WHERE d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL
        ";
        $params = [];

        if ($unitFilter === 'fakultas') {
            $sql .= " AND d.fakultas_id = ? AND d.level = 'fakultas'";
            $params[] = $this->fakultasId;
        } elseif (is_numeric($unitFilter) && in_array((int)$unitFilter, $prodiIds)) {
            $sql .= " AND d.prodi_id = ? AND d.level = 'prodi'";
            $params[] = (int)$unitFilter;
        } else {
            $sql .= " AND ( (d.fakultas_id = ? AND d.level = 'fakultas') OR d.prodi_id IN ({$prodiIdPlaceholder}) )";
            $params[] = $this->fakultasId;
        }

        $sql .= " ORDER BY FIELD(d.status_review, 'revisi_gpm', 'revisi_lpm', 'perlu_perbaikan', 'sudah_diperbaiki'), d.updated_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $revisiDocs = $stmt->fetchAll();
        $this->attachFilesToDocs($revisiDocs);

        // Hitung statistik ringkas perbaikan
        $scopeWhere = "( (fakultas_id = {$this->fakultasId} AND level = 'fakultas') OR prodi_id IN ({$prodiIdPlaceholder}) )";
        $totalRevisiGpm = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhere} AND status_review = 'revisi_gpm' AND deleted_at IS NULL")->fetchColumn();
        $totalRevisiLpm = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhere} AND status_review IN ('revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();
        $totalSudah     = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhere} AND status_review = 'sudah_diperbaiki' AND deleted_at IS NULL")->fetchColumn();
        $totalPerlu     = $totalRevisiGpm + $totalRevisiLpm;

        $fakultasPerlu = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = {$this->fakultasId} AND level = 'fakultas' AND status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();
        $prodiPerlu    = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id IN ({$prodiIdPlaceholder}) AND level = 'prodi' AND status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan') AND deleted_at IS NULL")->fetchColumn();

        $this->render('gpm/perbaikan', [
            'pageTitle'      => 'Monitoring Dokumen Revisi | GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'       => $this->fakultas,
            'prodisList'     => $this->prodisList,
            'revisiDocs'     => $revisiDocs,
            'unitFilter'     => $unitFilter,
            'totalPerlu'     => $totalPerlu,
            'totalRevisiGpm' => $totalRevisiGpm,
            'totalRevisiLpm' => $totalRevisiLpm,
            'totalSudah'     => $totalSudah,
            'fakultasPerlu'  => $fakultasPerlu,
            'prodiPerlu'     => $prodiPerlu
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
        if ($doc['level'] === 'fakultas' && (int)$doc['fakultas_id'] === $this->fakultasId) {
            return true;
        }
        $prodiIds = array_column($this->prodisList, 'id');
        if ($doc['level'] === 'prodi' && in_array((int)$doc['prodi_id'], $prodiIds)) {
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
        $stmt = $this->db->prepare("SELECT * FROM ppepp_document_files WHERE document_id IN ({$inClause}) ORDER BY id ASC");
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
        }
        unset($d);
    }
}
