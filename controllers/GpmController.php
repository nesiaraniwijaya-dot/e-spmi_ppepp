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

        // Statistik per status review
        $reviewStats = [
            'perlu_perbaikan'  => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review='perlu_perbaikan' AND deleted_at IS NULL")->fetchColumn(),
            'sudah_diperbaiki' => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review='sudah_diperbaiki' AND deleted_at IS NULL")->fetchColumn(),
            'belum_direview'   => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review='belum_direview' AND deleted_at IS NULL")->fetchColumn(),
            'sesuai'           => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review='sesuai' AND deleted_at IS NULL")->fetchColumn(),
            'draft'            => (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review='draft' AND deleted_at IS NULL")->fetchColumn(),
        ];

        // Ringkasan Dokumen Tingkat Fakultas
        $fakultasDocCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = {$this->fakultasId} AND level = 'fakultas' AND deleted_at IS NULL")->fetchColumn();
        $fakultasPerbaikanCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = {$this->fakultasId} AND level = 'fakultas' AND status_review = 'perlu_perbaikan' AND deleted_at IS NULL")->fetchColumn();

        // Ringkasan per Program Studi
        $stmtProdis = $this->db->prepare("
            SELECT p.*, 
                   COUNT(DISTINCT CASE WHEN d.deleted_at IS NULL THEN d.id END) as total_doc,
                   COUNT(DISTINCT CASE WHEN d.status_review = 'perlu_perbaikan' AND d.deleted_at IS NULL THEN d.id END) as perbaikan_doc,
                   COUNT(DISTINCT CASE WHEN d.status_review = 'draft' AND d.deleted_at IS NULL THEN d.id END) as draft_doc
            FROM prodis p 
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id 
            WHERE p.fakultas_id = ? 
            GROUP BY p.id 
            ORDER BY p.jenjang ASC, p.nama_prodi ASC
        ");
        $stmtProdis->execute([$this->fakultasId]);
        $prodiSummary = $stmtProdis->fetchAll();

        // Dokumen yang perlu perbaikan dari LPM
        $stmtRevisi = $this->db->prepare("
            SELECT d.*, b.nama_bidang, p.nama_prodi, p.jenjang, u.name as reviewer_name 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN users u ON d.reviewed_by = u.id
            WHERE {$scopeWhereAlias} AND d.status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL 
            ORDER BY FIELD(d.status_review, 'perlu_perbaikan', 'sudah_diperbaiki'), d.updated_at DESC
            LIMIT 10
        ");
        $stmtRevisi->execute();
        $revisiDocs = $stmtRevisi->fetchAll();

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
                   SUM(CASE WHEN d.status_review IN ('belum_direview', 'sudah_diperbaiki') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as perlu_review_count,
                   SUM(CASE WHEN d.status_review = 'belum_direview' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as belum_direview_count,
                   SUM(CASE WHEN d.status_review = 'sudah_diperbaiki' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sudah_diperbaiki_count,
                   SUM(CASE WHEN d.status_review = 'perlu_perbaikan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as revisi_count,
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
            'sudah_diperbaiki_count' => 0,
            'revisi_count' => 0,
            'sesuai_count' => 0
        ];

        // 2. Rekapitulasi Program Studi di bawah naungan Fakultas
        $stmtProdi = $this->db->prepare("
            SELECT p.id, p.kode_prodi, p.nama_prodi, p.jenjang, p.fakultas_id, f.nama_fakultas, f.kode_fakultas, p.nama_kaprodi,
                   COUNT(d.id) as total_dokumen,
                   SUM(CASE WHEN d.status_review IN ('belum_direview', 'sudah_diperbaiki') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as perlu_review_count,
                   SUM(CASE WHEN d.status_review = 'belum_direview' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as belum_direview_count,
                   SUM(CASE WHEN d.status_review = 'sudah_diperbaiki' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sudah_diperbaiki_count,
                   SUM(CASE WHEN d.status_review = 'perlu_perbaikan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as revisi_count,
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
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, p.nama_prodi, p.jenjang, f.nama_fakultas, u.name as reviewer_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON d.fakultas_id = f.id
            LEFT JOIN users u ON d.reviewed_by = u.id
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

        if ($statusFilter && in_array($statusFilter, ['draft', 'belum_direview', 'perlu_perbaikan', 'sudah_diperbaiki', 'sesuai'])) {
            $sql .= " AND d.status_review = ?";
            $params[] = $statusFilter;
        }

        if ($search) {
            $sql .= " AND (d.nama_dokumen LIKE ? OR d.nomor_dokumen LIKE ? OR b.nama_bidang LIKE ? OR p.nama_prodi LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY d.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $documents = $stmt->fetchAll();
        $this->attachFilesToDocs($documents);

        // Ambil daftar bidang untuk dropdown filter
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        // Hitung dokumen notifikasi aksi dalam lingkup GPM
        $scopeWhereSimple = "( (fakultas_id = {$this->fakultasId} AND level = 'fakultas') OR prodi_id IN ({$prodiIdPlaceholder}) )";
        $gpmPerluCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'perlu_perbaikan' AND deleted_at IS NULL")->fetchColumn();
        $gpmSudahCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'sudah_diperbaiki' AND deleted_at IS NULL")->fetchColumn();
        $gpmDraftCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhereSimple} AND status_review = 'draft' AND deleted_at IS NULL")->fetchColumn();

        $this->render('gpm/documents', [
            'pageTitle'     => 'Daftar Dokumen Mutu PPEPP | GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'      => $this->fakultas,
            'prodisList'    => $this->prodisList,
            'documents'     => $documents,
            'bidangList'    => $bidangList,
            'unitFilter'    => $unitFilter,
            'siklusFilter'  => $siklusFilter,
            'bidangFilter'  => $bidangFilter,
            'statusFilter'  => $statusFilter,
            'search'        => $search,
            'gpmPerluCount' => $gpmPerluCount,
            'gpmSudahCount' => $gpmSudahCount,
            'gpmDraftCount' => $gpmDraftCount
        ]);
    }

    /**
     * Halaman Unggah Dokumen Baru
     */
    public function createDocument(): void {
        $bidangStandar = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
        $subBidangStandar = $this->db->query("SELECT * FROM sub_bidang_standar WHERE is_active = 1 ORDER BY bidang_id ASC, id ASC")->fetchAll();

        $academicYears = [
            '2026/2027', '2025/2026', '2024/2025', '2023/2024', '2022/2023', '2021/2022'
        ];

        $this->render('gpm/document_form', [
            'pageTitle'        => 'Unggah Dokumen Mutu Baru | GPM ' . $this->fakultas['nama_fakultas'],
            'isEdit'           => false,
            'fakultas'         => $this->fakultas,
            'prodisList'       => $this->prodisList,
            'bidangStandar'    => $bidangStandar,
            'subBidangStandar' => $subBidangStandar,
            'academicYears'    => $academicYears,
            'doc'              => []
        ]);
    }

    /**
     * Halaman Edit Dokumen
     */
    public function editDocument(string $id): void {
        $docId = (int)$id;
        $doc = $this->findDocWithScope($docId);

        if (!$doc) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan atau berada di luar lingkup fakultas Anda.');
            redirect('gpm/dokumen');
            return;
        }

        // Ambil lampiran berkas dokumen
        $stmtFiles = $this->db->prepare("SELECT * FROM ppepp_document_files WHERE document_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtFiles->execute([$docId]);
        $doc['files'] = $stmtFiles->fetchAll();

        if (empty($doc['files']) && !empty($doc['file_path'])) {
            $doc['files'] = [
                [
                    'id' => 0,
                    'document_id' => $doc['id'],
                    'file_name' => basename($doc['file_path']),
                    'file_path' => $doc['file_path'],
                    'file_size' => $doc['file_size'] ?? '',
                    'file_extension' => $doc['file_extension'] ?? 'pdf',
                    'narasi' => ''
                ]
            ];
        }

        $bidangStandar = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
        $subBidangStandar = $this->db->query("SELECT * FROM sub_bidang_standar WHERE is_active = 1 ORDER BY bidang_id ASC, id ASC")->fetchAll();

        $academicYears = [
            '2026/2027', '2025/2026', '2024/2025', '2023/2024', '2022/2023', '2021/2022'
        ];

        $this->render('gpm/document_form', [
            'pageTitle'        => 'Edit Dokumen: ' . htmlspecialchars($doc['nama_dokumen']),
            'isEdit'           => true,
            'doc'              => $doc,
            'fakultas'         => $this->fakultas,
            'prodisList'       => $this->prodisList,
            'bidangStandar'    => $bidangStandar,
            'subBidangStandar' => $subBidangStandar,
            'academicYears'    => $academicYears
        ]);
    }

    /**
     * Simpan Dokumen (Batch Create 1-10 Kartu ATAU Single Edit)
     */
    public function saveDocument(): void {
        $isEdit = !empty($_POST['id']);
        $isDraft = (isset($_POST['action_submit']) && $_POST['action_submit'] === 'draft');
        $allowedExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $validProdiIds = array_column($this->prodisList, 'id');

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
     * Halaman Perbaikan Dokumen (Revisi dari LPM)
     */
    public function perbaikan(): void {
        $unitFilter = trim($_GET['unit'] ?? 'all');
        $prodiIds = array_column($this->prodisList, 'id');
        $prodiIdPlaceholder = !empty($prodiIds) ? implode(',', array_map('intval', $prodiIds)) : '0';

        $sql = "
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, p.nama_prodi, p.jenjang, f.nama_fakultas, u.name as reviewer_name 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id 
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON d.fakultas_id = f.id
            LEFT JOIN users u ON d.reviewed_by = u.id
            WHERE d.status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL
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

        $sql .= " ORDER BY FIELD(d.status_review, 'perlu_perbaikan', 'sudah_diperbaiki'), d.updated_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $revisiDocs = $stmt->fetchAll();
        $this->attachFilesToDocs($revisiDocs);

        // Hitung statistik ringkas perbaikan
        $scopeWhere = "( (fakultas_id = {$this->fakultasId} AND level = 'fakultas') OR prodi_id IN ({$prodiIdPlaceholder}) )";
        $totalPerlu = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhere} AND status_review = 'perlu_perbaikan' AND deleted_at IS NULL")->fetchColumn();
        $totalSudah = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE {$scopeWhere} AND status_review = 'sudah_diperbaiki' AND deleted_at IS NULL")->fetchColumn();
        $fakultasPerlu = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = {$this->fakultasId} AND level = 'fakultas' AND status_review = 'perlu_perbaikan' AND deleted_at IS NULL")->fetchColumn();
        $prodiPerlu = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id IN ({$prodiIdPlaceholder}) AND level = 'prodi' AND status_review = 'perlu_perbaikan' AND deleted_at IS NULL")->fetchColumn();

        $this->render('gpm/perbaikan', [
            'pageTitle'     => 'Pusat Perbaikan Dokumen Mutu | GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'      => $this->fakultas,
            'prodisList'    => $this->prodisList,
            'revisiDocs'    => $revisiDocs,
            'unitFilter'    => $unitFilter,
            'totalPerlu'    => $totalPerlu,
            'totalSudah'    => $totalSudah,
            'fakultasPerlu' => $fakultasPerlu,
            'prodiPerlu'    => $prodiPerlu
        ]);
    }

    /**
     * Halaman Draf Dokumen
     */
    public function draft(): void {
        $unitFilter = trim($_GET['unit'] ?? 'all');
        $prodiIds = array_column($this->prodisList, 'id');
        $prodiIdPlaceholder = !empty($prodiIds) ? implode(',', array_map('intval', $prodiIds)) : '0';

        $sql = "
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, p.nama_prodi, p.jenjang, f.nama_fakultas
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON d.fakultas_id = f.id
            WHERE d.status_review = 'draft' AND d.deleted_at IS NULL
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

        $sql .= " ORDER BY d.updated_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $draftDocs = $stmt->fetchAll();
        $this->attachFilesToDocs($draftDocs);

        $this->render('gpm/draft', [
            'pageTitle'  => 'Draf Dokumen Mutu | GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'   => $this->fakultas,
            'prodisList' => $this->prodisList,
            'draftDocs'  => $draftDocs,
            'unitFilter' => $unitFilter
        ]);
    }

    /**
     * Ajukan Dokumen dari Draf ke LPM
     */
    public function ajukanDraft(string $id): void {
        $docId = (int)$id;
        $doc = $this->findDocWithScope($docId);

        if (!$doc) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan.');
            redirect('gpm/draft');
            return;
        }

        // Cek kelengkapan berkas/link
        $fileCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_document_files WHERE document_id = {$docId}")->fetchColumn();
        $hasLink = !empty($doc['external_link']);

        if ($doc['jenis_upload'] === 'file' && $fileCount === 0) {
            Auth::setFlash('danger', 'Dokumen draf belum memiliki berkas terunggah. Silakan edit dokumen untuk melampirkan berkas sebelum mengajukan.');
            redirect('gpm/draft');
            return;
        }

        if ($doc['jenis_upload'] === 'link' && !$hasLink) {
            Auth::setFlash('danger', 'Dokumen draf belum memiliki tautan Google Drive. Silakan edit dokumen sebelum mengajukan.');
            redirect('gpm/draft');
            return;
        }

        $stmt = $this->db->prepare("UPDATE ppepp_documents SET status_review = 'belum_direview', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$docId]);

        AuditLogger::log(
            aksi: 'UPDATE',
            modul: 'Dokumen PPEPP GPM',
            targetId: (string)$docId,
            targetName: $doc['nama_dokumen'],
            oldValues: ['status' => 'draft'],
            newValues: ['status' => 'belum_direview']
        );

        Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' berhasil diajukan ke LPM untuk ditinjau.");
        redirect('gpm/draft');
    }

    /**
     * Halaman Arsip / Riwayat Terhapus
     */
    public function arsip(): void {
        $prodiIds = array_column($this->prodisList, 'id');
        $prodiIdPlaceholder = !empty($prodiIds) ? implode(',', array_map('intval', $prodiIds)) : '0';

        $stmt = $this->db->prepare("
            SELECT d.*, b.nama_bidang, p.nama_prodi, p.jenjang, f.nama_fakultas 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON d.fakultas_id = f.id
            WHERE ( (d.fakultas_id = ? AND d.level = 'fakultas') OR d.prodi_id IN ({$prodiIdPlaceholder}) )
              AND d.deleted_at IS NOT NULL 
            ORDER BY d.deleted_at DESC
        ");
        $stmt->execute([$this->fakultasId]);
        $arsipDocs = $stmt->fetchAll();
        $this->attachFilesToDocs($arsipDocs);

        $this->render('gpm/arsip', [
            'pageTitle'  => 'Arsip & Riwayat Hapus Dokumen | GPM ' . $this->fakultas['nama_fakultas'],
            'fakultas'   => $this->fakultas,
            'arsipDocs'  => $arsipDocs
        ]);
    }

    /**
     * Soft Delete Dokumen
     */
    public function softDeleteDocument(string $id): void {
        $docId = (int)$id;
        $doc = $this->findDocWithScope($docId);

        if (!$doc) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan.');
            redirect('gpm/dokumen');
            return;
        }

        $stmt = $this->db->prepare("UPDATE ppepp_documents SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$docId]);

        AuditLogger::log('DELETE', 'Dokumen PPEPP GPM', (string)$docId, $doc['nama_dokumen'], ['deleted_at' => null], ['deleted_at' => date('Y-m-d H:i:s')]);
        Auth::setFlash('warning', "Dokumen '{$doc['nama_dokumen']}' telah dipindahkan ke Arsip.");

        $referer = $_SERVER['HTTP_REFERER'] ?? 'gpm/dokumen';
        redirect(str_contains($referer, 'draft') ? 'gpm/draft' : 'gpm/dokumen');
    }

    /**
     * Restore Dokumen
     */
    public function restoreDocument(string $id): void {
        $docId = (int)$id;
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND deleted_at IS NOT NULL");
        $stmt->execute([$docId]);
        $doc = $stmt->fetch();

        if (!$doc || !$this->isDocInScope($doc)) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan dalam arsip fakultas Anda.');
            redirect('gpm/dokumen/arsip');
            return;
        }

        $stmtUpd = $this->db->prepare("UPDATE ppepp_documents SET deleted_at = NULL WHERE id = ?");
        $stmtUpd->execute([$docId]);

        AuditLogger::log('RESTORE', 'Dokumen PPEPP GPM', (string)$docId, $doc['nama_dokumen'], ['deleted_at' => $doc['deleted_at']], ['deleted_at' => null]);
        Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' berhasil dipulihkan ke daftar aktif.");
        redirect('gpm/dokumen/arsip');
    }

    /**
     * Hapus Dokumen Secara Permanen dari Arsip (Hard Delete)
     */
    public function forceDeleteDocument(string $id): void {
        $docId = (int)$id;
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND deleted_at IS NOT NULL");
        $stmt->execute([$docId]);
        $doc = $stmt->fetch();

        if (!$doc || !$this->isDocInScope($doc)) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan dalam arsip fakultas Anda atau Anda tidak memiliki akses.');
            redirect('gpm/dokumen/arsip');
            return;
        }

        // 1. Ambil seluruh file lampiran di ppepp_document_files
        $stmtFiles = $this->db->prepare("SELECT file_path FROM ppepp_document_files WHERE document_id = ?");
        $stmtFiles->execute([$docId]);
        $files = $stmtFiles->fetchAll();

        foreach ($files as $f) {
            if (!empty($f['file_path'])) {
                $realPath = ROOT_PATH . '/' . ltrim($f['file_path'], '/\\');
                if (file_exists($realPath) && is_file($realPath)) {
                    @unlink($realPath);
                }
            }
        }

        // 2. Berkas lama jika ada
        if (!empty($doc['file_path'])) {
            $legacyPath = ROOT_PATH . '/' . ltrim($doc['file_path'], '/\\');
            if (file_exists($legacyPath) && is_file($legacyPath)) {
                @unlink($legacyPath);
            }
        }

        // 3. Hapus record file
        $this->db->prepare("DELETE FROM ppepp_document_files WHERE document_id = ?")->execute([$docId]);

        // 4. Hapus data dokumen utama
        $this->db->prepare("DELETE FROM ppepp_documents WHERE id = ?")->execute([$docId]);

        // 5. Catat audit trail
        AuditLogger::log('DELETE', 'Dokumen PPEPP GPM (Permanen)', (string)$docId, $doc['nama_dokumen'], $doc, null);

        Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' dan seluruh berkas lampirannya telah berhasil dihapus secara permanen.");
        redirect('gpm/dokumen/arsip');
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
