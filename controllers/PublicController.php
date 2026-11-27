<?php
/**
 * Public Controller (Guest / Read-Only Portal)
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../core/Controller.php';

class PublicController extends Controller {

    /**
     * 1. Halaman Utama: Daftar Fakultas UNIKA Soegijapranata
     */
    public function index(): void {
        // Fetch all fakultas with prodi count, doc count, and prodi list
        $stmt = $this->db->query("
            SELECT f.*, 
                   COUNT(DISTINCT p.id) as total_prodi,
                   COUNT(DISTINCT d.id) as total_dokumen,
                   GROUP_CONCAT(DISTINCT p.nama_prodi ORDER BY p.nama_prodi SEPARATOR '||') as prodi_names
            FROM fakultas f
            LEFT JOIN prodis p ON f.id = p.fakultas_id
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review = 'sesuai'
            GROUP BY f.id
            ORDER BY f.nama_fakultas ASC
        ");
        $fakultasList = $stmt->fetchAll();

        // Overall statistics
        $totalFakultas = count($fakultasList);
        $totalProdi = $this->db->query("SELECT COUNT(*) FROM prodis")->fetchColumn();
        $totalDokumen = $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE deleted_at IS NULL AND status_review = 'sesuai'")->fetchColumn();

        // Count per cycle across university (excluding draft and non-approved)
        $cycleCounts = [
            'penetapan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='penetapan' AND deleted_at IS NULL AND status_review = 'sesuai'")->fetchColumn(),
            'pelaksanaan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='pelaksanaan' AND deleted_at IS NULL AND status_review = 'sesuai'")->fetchColumn(),
            'evaluasi' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='evaluasi' AND deleted_at IS NULL AND status_review = 'sesuai'")->fetchColumn(),
            'pengendalian' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='pengendalian' AND deleted_at IS NULL AND status_review = 'sesuai'")->fetchColumn(),
            'peningkatan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='peningkatan' AND deleted_at IS NULL AND status_review = 'sesuai'")->fetchColumn(),
        ];

        $this->render('public/home', [
            'pageTitle' => 'Portal SISTA 2',
            'activeNav' => 'home',
            'fakultasList' => $fakultasList,
            'totalFakultas' => $totalFakultas,
            'totalProdi' => $totalProdi,
            'totalDokumen' => $totalDokumen,
            'cycleCounts' => $cycleCounts
        ]);
    }

    /**
     * 2. Halaman Program Studi di Bawah Fakultas Terpilih
     */
    public function prodis(string $fakultasId): void {
        $stmtFak = $this->db->prepare("SELECT * FROM fakultas WHERE id = ?");
        $stmtFak->execute([$fakultasId]);
        $fakultas = $stmtFak->fetch();

        if (!$fakultas) {
            Auth::setFlash('danger', 'Fakultas tidak ditemukan.');
            redirect('');
        }

        // Fetch all prodis under this fakultas with document statistics
        $stmtProdi = $this->db->prepare("
            SELECT p.*, 
                   COUNT(DISTINCT d.id) as total_dokumen,
                   SUM(CASE WHEN d.siklus = 'penetapan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as count_p1,
                   SUM(CASE WHEN d.siklus = 'pelaksanaan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as count_p2,
                   SUM(CASE WHEN d.siklus = 'evaluasi' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as count_e,
                   SUM(CASE WHEN d.siklus = 'pengendalian' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as count_p3,
                   SUM(CASE WHEN d.siklus = 'peningkatan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as count_p4
            FROM prodis p
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review = 'sesuai'
            WHERE p.fakultas_id = ?
            GROUP BY p.id
            ORDER BY p.jenjang ASC, p.nama_prodi ASC
        ");
        $stmtProdi->execute([$fakultasId]);
        $prodiList = $stmtProdi->fetchAll();

        // Fetch fakultas-level PPEPP documents statistics
        $stmtFakDocs = $this->db->prepare("
            SELECT 
                COUNT(id) as total_dokumen,
                SUM(CASE WHEN siklus = 'penetapan' THEN 1 ELSE 0 END) as count_p1,
                SUM(CASE WHEN siklus = 'pelaksanaan' THEN 1 ELSE 0 END) as count_p2,
                SUM(CASE WHEN siklus = 'evaluasi' THEN 1 ELSE 0 END) as count_e,
                SUM(CASE WHEN siklus = 'pengendalian' THEN 1 ELSE 0 END) as count_p3,
                SUM(CASE WHEN siklus = 'peningkatan' THEN 1 ELSE 0 END) as count_p4
            FROM ppepp_documents
            WHERE fakultas_id = ? AND level = 'fakultas' AND deleted_at IS NULL AND status_review = 'sesuai'
        ");
        $stmtFakDocs->execute([$fakultasId]);
        $fakultasDocStats = $stmtFakDocs->fetch() ?: [
            'total_dokumen' => 0, 'count_p1' => 0, 'count_p2' => 0, 'count_e' => 0, 'count_p3' => 0, 'count_p4' => 0
        ];

        $this->render('public/prodi_list', [
            'pageTitle' => 'Program Studi - ' . $fakultas['nama_fakultas'],
            'activeNav' => 'home',
            'fakultas' => $fakultas,
            'fakultasDocStats' => $fakultasDocStats,
            'prodiList' => $prodiList
        ]);
    }

    /**
     * Helper: Attach files to documents array
     */
    private function attachFilesToDocs(array &$documents): void {
        if (empty($documents)) return;
        $docIds = array_column($documents, 'id');
        if (empty($docIds)) return;

        $placeholders = implode(',', array_fill(0, count($docIds), '?'));
        $stmt = $this->db->prepare("SELECT * FROM ppepp_document_files WHERE document_id IN ($placeholders) ORDER BY sort_order ASC, id ASC");
        $stmt->execute($docIds);
        $files = $stmt->fetchAll();

        $filesByDoc = [];
        foreach ($files as $f) {
            $filesByDoc[$f['document_id']][] = $f;
        }

        foreach ($documents as &$d) {
            $d['files'] = $filesByDoc[$d['id']] ?? [];
            if (empty($d['files']) && !empty($d['file_path'])) {
                $d['files'][] = [
                    'id' => 0,
                    'document_id' => $d['id'],
                    'file_name' => $d['nama_dokumen'] . '.' . ($d['file_extension'] ?: 'pdf'),
                    'file_path' => $d['file_path'],
                    'file_size' => $d['file_size'] ?? '',
                    'file_extension' => $d['file_extension'] ?? 'pdf'
                ];
            }
            $d['files_count'] = count($d['files']);
        }
    }

    /**
     * 3. Halaman Dashboard PPEPP Publik Program Studi
     */
    public function ppepp(string $prodiId): void {
        // Fetch Prodi & Fakultas Details
        $stmt = $this->db->prepare("
            SELECT p.*, f.nama_fakultas, f.kode_fakultas, f.id as fak_id
            FROM prodis p
            JOIN fakultas f ON p.fakultas_id = f.id
            WHERE p.id = ?
        ");
        $stmt->execute([$prodiId]);
        $prodi = $stmt->fetch();

        if (!$prodi) {
            Auth::setFlash('danger', 'Program Studi tidak ditemukan.');
            redirect('');
        }

        // Fetch All Active Bidang Standar for Filtering (Scoped to University + This Faculty/Prodi)
        $fakId = (int)($prodi['fakultas_id'] ?? 0);
        $bidangList = $this->db->query("
            SELECT * FROM bidang_standar 
            WHERE is_active = 1 
              AND (scope = 'universitas' OR (scope = 'fakultas' AND fakultas_id = {$fakId}) OR (scope = 'prodi' AND prodi_id = {$prodiId}))
            ORDER BY id ASC
        ")->fetchAll();

        // Fetch All Active Documents for this prodi (Excluding soft deleted, only approved by LPM)
        $stmtDocs = $this->db->prepare("
            SELECT d.*, b.nama_bidang, b.kode_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as uploader_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.prodi_id = ? AND d.deleted_at IS NULL AND d.status_review = 'sesuai'
            ORDER BY d.created_at DESC
        ");
        $stmtDocs->execute([$prodiId]);
        $allDocuments = $stmtDocs->fetchAll();

        $this->attachFilesToDocs($allDocuments);

        // Accurate statistics per cycle & bidang (single pass, zero duplication)
        $cycleStats = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0,
            'total' => 0
        ];
        $bidangStats = [];

        foreach ($allDocuments as $doc) {
            $siklus = $doc['siklus'];
            if (isset($cycleStats[$siklus])) {
                $cycleStats[$siklus]++;
                $cycleStats['total']++;
            }

            $bidangName = $doc['nama_bidang'] ?? 'Lainnya';
            $bidangStats[$bidangName] = ($bidangStats[$bidangName] ?? 0) + 1;
        }

        // Fetch fakultas details
        $fakultasId = (int)$prodi['fakultas_id'];
        $stmtFak = $this->db->prepare("SELECT * FROM fakultas WHERE id = ?");
        $stmtFak->execute([$fakultasId]);
        $fakultas = $stmtFak->fetch();

        // Calculate total faculty documents count (only approved by LPM)
        $stmtFakCount = $this->db->prepare("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = ? AND level = 'fakultas' AND deleted_at IS NULL AND status_review = 'sesuai'");
        $stmtFakCount->execute([$fakultasId]);
        $fakultas['total_dokumen'] = (int)$stmtFakCount->fetchColumn();

        // Fetch all prodis under the same fakultas with document counts for switcher
        $stmtProdis = $this->db->prepare("
            SELECT p.id, p.nama_prodi, p.jenjang, p.kode_prodi,
                   COUNT(d.id) as total_dokumen
            FROM prodis p
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review = 'sesuai'
            WHERE p.fakultas_id = ?
            GROUP BY p.id
            ORDER BY p.jenjang ASC, p.nama_prodi ASC
        ");
        $stmtProdis->execute([$fakultasId]);
        $prodiList = $stmtProdis->fetchAll();

        $this->render('public/ppepp_dashboard', [
            'pageTitle' => 'Siklus PPEPP ' . $prodi['nama_prodi'],
            'activeNav' => 'home',
            'prodi' => $prodi,
            'fakultas' => $fakultas,
            'prodiList' => $prodiList,
            'bidangList' => $bidangList,
            'allDocuments' => $allDocuments,
            'cycleStats' => $cycleStats,
            'bidangStats' => $bidangStats
        ]);
    }

    /**
     * 3b. Halaman Dashboard PPEPP Publik Tingkat Fakultas (Dekanat)
     */
    public function fakultasPpepp(string $fakultasId): void {
        $stmtFak = $this->db->prepare("SELECT * FROM fakultas WHERE id = ?");
        $stmtFak->execute([$fakultasId]);
        $fakultas = $stmtFak->fetch();

        if (!$fakultas) {
            Auth::setFlash('danger', 'Fakultas tidak ditemukan.');
            redirect('');
        }

        $fId = (int)$fakultas['id'];

        // Calculate total faculty documents count (only approved by LPM)
        $stmtFakCount = $this->db->prepare("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = ? AND level = 'fakultas' AND deleted_at IS NULL AND status_review = 'sesuai'");
        $stmtFakCount->execute([$fId]);
        $fakultas['total_dokumen'] = (int)$stmtFakCount->fetchColumn();

        // Fetch All Active Bidang Standar for Filtering (Scoped to University + This Faculty)
        $bidangList = $this->db->query("
            SELECT * FROM bidang_standar 
            WHERE is_active = 1 
              AND (scope = 'universitas' OR (scope = 'fakultas' AND fakultas_id = {$fId}))
            ORDER BY id ASC
        ")->fetchAll();

        // Fetch All Active Faculty Documents (level = 'fakultas', only approved by LPM)
        $stmtDocs = $this->db->prepare("
            SELECT d.*, b.nama_bidang, b.kode_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as uploader_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.deleted_at IS NULL AND d.status_review = 'sesuai'
            ORDER BY d.created_at DESC
        ");
        $stmtDocs->execute([$fId]);
        $allDocuments = $stmtDocs->fetchAll();

        $this->attachFilesToDocs($allDocuments);

        // Accurate statistics per cycle & bidang (single pass, zero duplication)
        $cycleStats = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0,
            'total' => 0
        ];
        $bidangStats = [];

        foreach ($allDocuments as $doc) {
            $siklus = $doc['siklus'];
            if (isset($cycleStats[$siklus])) {
                $cycleStats[$siklus]++;
                $cycleStats['total']++;
            }

            $bidangName = $doc['nama_bidang'] ?? 'Lainnya';
            $bidangStats[$bidangName] = ($bidangStats[$bidangName] ?? 0) + 1;
        }

        // Fetch prodi list under this fakultas with document count for switcher
        $stmtProdis = $this->db->prepare("
            SELECT p.id, p.nama_prodi, p.jenjang, p.kode_prodi,
                   COUNT(d.id) as total_dokumen
            FROM prodis p
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review = 'sesuai'
            WHERE p.fakultas_id = ?
            GROUP BY p.id
            ORDER BY p.jenjang ASC, p.nama_prodi ASC
        ");
        $stmtProdis->execute([$fId]);
        $prodiList = $stmtProdis->fetchAll();

        $this->render('public/fakultas_ppepp', [
            'pageTitle' => 'Dokumen Mutu Dekanat - ' . $fakultas['nama_fakultas'],
            'activeNav' => 'home',
            'fakultas' => $fakultas,
            'bidangList' => $bidangList,
            'allDocuments' => $allDocuments,
            'cycleStats' => $cycleStats,
            'bidangStats' => $bidangStats,
            'prodiList' => $prodiList
        ]);
    }

    /**
     * 4. Halaman Repositori Dokumen Mutu Keseluruhan (Paginated per 50 dokumen)
     */
    public function allDocuments(): void {
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
        $fakultasList = $this->db->query("SELECT id, nama_fakultas, kode_fakultas FROM fakultas ORDER BY nama_fakultas ASC")->fetchAll();
        $prodiList = $this->db->query("SELECT id, nama_prodi, jenjang, fakultas_id FROM prodis ORDER BY nama_prodi ASC")->fetchAll();

        // Filter parameters
        $initialSiklus = isset($_GET['siklus']) ? strtolower(trim($_GET['siklus'])) : '';
        if (!in_array($initialSiklus, ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
            $initialSiklus = '';
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $fakultas = isset($_GET['fakultas']) ? trim($_GET['fakultas']) : '';
        $prodi = isset($_GET['prodi']) ? (int)$_GET['prodi'] : 0;
        $bidang = isset($_GET['bidang']) ? (int)$_GET['bidang'] : 0;
        $tahun = isset($_GET['tahun']) ? trim($_GET['tahun']) : '';

        // Default pagination: 50 dokumen per page (sesuai request user)
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 50;
        }
        $page = max(1, (int)($_GET['page'] ?? 1));

        // Base Query Builder (semua filter KECUALI siklus) agar jumlah tab per siklus real-time sesuai periode/filter aktif
        // HANYA dokumen yang telah disetujui LPM (status_review = 'sesuai') yang ditampilkan di portal publik
        $baseWhere = ["d.deleted_at IS NULL", "d.status_review = 'sesuai'"];
        $baseParams = [];

        // Pencarian teks pada nama dokumen, nomor dokumen, nama prodi, nama bidang
        if (!empty($search)) {
            $baseWhere[] = "(d.nama_dokumen LIKE :search1 OR d.nomor_dokumen LIKE :search2 OR p.nama_prodi LIKE :search3 OR b.nama_bidang LIKE :search4)";
            $searchVal = '%' . $search . '%';
            $baseParams[':search1'] = $searchVal;
            $baseParams[':search2'] = $searchVal;
            $baseParams[':search3'] = $searchVal;
            $baseParams[':search4'] = $searchVal;
        }

        // Filter Fakultas
        if ($fakultas === 'tingkat_fakultas') {
            $baseWhere[] = "(d.level = 'fakultas' OR d.prodi_id IS NULL OR d.prodi_id = 0)";
        } elseif (!empty($fakultas) && is_numeric($fakultas)) {
            $baseWhere[] = "(p.fakultas_id = :fakultas1 OR (d.prodi_id IS NULL AND d.fakultas_id = :fakultas2))";
            $baseParams[':fakultas1'] = (int)$fakultas;
            $baseParams[':fakultas2'] = (int)$fakultas;
        }

        // Filter Prodi
        if (!empty($prodi)) {
            $baseWhere[] = "d.prodi_id = :prodi";
            $baseParams[':prodi'] = $prodi;
        }

        // Filter Bidang
        if (!empty($bidang)) {
            $baseWhere[] = "d.bidang_id = :bidang";
            $baseParams[':bidang'] = $bidang;
        }

        // Filter Tahun Akademik (Periode)
        if (!empty($tahun)) {
            $baseWhere[] = "d.tahun_akademik = :tahun";
            $baseParams[':tahun'] = $tahun;
        }

        $baseWhereClause = implode(" AND ", $baseWhere);

        // Hitung statistik per siklus real-time sesuai periode & filter aktif
        $cycleCounts = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0
        ];
        $stmtCycles = $this->db->prepare("
            SELECT LOWER(TRIM(d.siklus)) as s, COUNT(*) as cnt
            FROM ppepp_documents d
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            WHERE $baseWhereClause
            GROUP BY LOWER(TRIM(d.siklus))
        ");
        $stmtCycles->execute($baseParams);
        while ($row = $stmtCycles->fetch()) {
            $s = strtolower(trim($row['s'] ?? ''));
            if (isset($cycleCounts[$s])) {
                $cycleCounts[$s] = (int)$row['cnt'];
            }
        }
        $totalDokumen = array_sum($cycleCounts);
        $maxDocs = max(1, ...array_values($cycleCounts));

        // Terapkan filter siklus untuk daftar tabel utama
        $where = $baseWhere;
        $params = $baseParams;

        // STRICT SIKLUS PROTECTION: Ketika memilih siklus tertentu (contoh: penetapan),
        // query hanya mengizinkan dokumen siklus tersebut sehingga siklus lain tidak bocor
        if (!empty($initialSiklus)) {
            $where[] = "LOWER(TRIM(d.siklus)) = :siklus";
            $params[':siklus'] = $initialSiklus;
        }

        $whereClause = implode(" AND ", $where);

        // Hitung total data yang cocok dengan filter
        $countSql = "
            SELECT COUNT(*) 
            FROM ppepp_documents d
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            WHERE $whereClause
        ";
        $stmtCount = $this->db->prepare($countSql);
        $stmtCount->execute($params);
        $filteredTotal = (int)$stmtCount->fetchColumn();

        $totalPages = max(1, (int)ceil($filteredTotal / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        // Ambil data hanya sejumlah $perPage (50 data)
        $sql = "
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, 
                   COALESCE(p.nama_prodi, 'Dekanat / Tingkat Fakultas') as nama_prodi, 
                   p.jenjang, 
                   COALESCE(f_prodi.nama_fakultas, f_doc.nama_fakultas) as nama_fakultas,
                   COALESCE(p.fakultas_id, d.fakultas_id) as computed_fakultas_id
            FROM ppepp_documents d
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f_prodi ON p.fakultas_id = f_prodi.id
            LEFT JOIN fakultas f_doc ON d.fakultas_id = f_doc.id
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            WHERE $whereClause
            ORDER BY d.created_at DESC
            LIMIT $perPage OFFSET $offset
        ";
        $stmtDocs = $this->db->prepare($sql);
        $stmtDocs->execute($params);
        $documents = $stmtDocs->fetchAll();

        // Lampirkan berkas HANYA untuk 50 dokumen pada halaman ini (super cepat & hemat memori)
        $this->attachFilesToDocs($documents);

        $startNum = $filteredTotal > 0 ? $offset + 1 : 0;
        $endNum = min($offset + $perPage, $filteredTotal);

        // Jika request melalui AJAX, kembalikan respons JSON langsung
        if (!empty($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            ob_start();
            require ROOT_PATH . '/views/public/all_documents_rows.php';
            $rowsHtml = ob_get_clean();

            echo json_encode([
                'success' => true,
                'html' => $rowsHtml,
                'page' => $page,
                'per_page' => $perPage,
                'total' => $filteredTotal,
                'total_pages' => $totalPages,
                'start_num' => $startNum,
                'end_num' => $endNum,
                'siklus' => $initialSiklus,
                'cycle_counts' => $cycleCounts,
                'total_all_cycles' => $totalDokumen
            ]);
            exit;
        }

        $this->render('public/all_documents', [
            'pageTitle' => 'Portal Repositori Dokumen Mutu',
            'activeNav' => 'dokumen',
            'bidangList' => $bidangList,
            'fakultasList' => $fakultasList,
            'prodiList' => $prodiList,
            'documents' => $documents,
            'cycleCounts' => $cycleCounts,
            'totalDokumen' => $totalDokumen,
            'maxDocs' => $maxDocs,
            'filteredTotal' => $filteredTotal,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'offset' => $offset,
            'startNum' => $startNum,
            'endNum' => $endNum,
            'initialSiklus' => $initialSiklus,
            'search' => $search,
            'fakultas' => $fakultas,
            'prodi' => $prodi,
            'bidang' => $bidang,
            'tahun' => $tahun
        ]);
    }
    /**
     * 5. Halaman Tentang PPEPP
     */
    public function about(): void {
        $this->render('public/about', [
            'pageTitle' => 'Tentang Siklus PPEPP SPMI',
            'activeNav' => 'tentang'
        ]);
    }

    /**
     * 6. API Publik: Ambil metadata satu dokumen mutu untuk auto-preview deep-linking
     */
    public function getDocumentJson(string $id): void {
        header('Content-Type: application/json; charset=utf-8');
        $docId = (int)$id;
        if ($docId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID dokumen tidak valid']);
            exit;
        }

        $stmt = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang,
                   COALESCE(p.nama_prodi, 'Dekanat / Tingkat Fakultas') as nama_prodi,
                   COALESCE(f_prodi.nama_fakultas, f_doc.nama_fakultas) as nama_fakultas
            FROM ppepp_documents d
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f_prodi ON p.fakultas_id = f_prodi.id
            LEFT JOIN fakultas f_doc ON d.fakultas_id = f_doc.id
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            WHERE d.id = ? AND d.deleted_at IS NULL AND d.status_review = 'sesuai'
            LIMIT 1
        ");
        $stmt->execute([$docId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            echo json_encode(['success' => false, 'message' => 'Dokumen tidak ditemukan atau belum disetujui.']);
            exit;
        }

        $docsList = [$doc];
        $this->attachFilesToDocs($docsList);
        $doc = $docsList[0];

        $isUnikaUser = is_unika_user_logged_in();
        $files = $doc['files'] ?? [];
        $firstFile = !empty($files) ? $files[0] : ['file_path' => $doc['file_path'], 'file_name' => $doc['nama_dokumen'], 'narasi' => ''];
        $meta = get_portal_file_meta($firstFile['file_path'] ?? '', $firstFile['file_extension'] ?? '');

        echo json_encode([
            'success' => true,
            'doc' => [
                'id' => $doc['id'],
                'nama_dokumen' => $doc['nama_dokumen'],
                'tahun_akademik' => $doc['tahun_akademik'] ?? '',
                'file_path' => base_url($firstFile['file_path']),
                'file_name' => $firstFile['file_name'],
                'narasi' => $firstFile['narasi'] ?? '',
                'standar_html' => render_sub_standar_badges($firstFile['sub_bidang_ids'] ?? null),
                'public_limit' => $isUnikaUser ? 0 : 1,
                'can_download' => $isUnikaUser ? 1 : 0,
                'file_type' => $meta['type'],
                'files' => $files,
            ]
        ]);
        exit;
    }
}
