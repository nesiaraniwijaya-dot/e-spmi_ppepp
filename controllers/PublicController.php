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
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
            GROUP BY f.id
            ORDER BY f.nama_fakultas ASC
        ");
        $fakultasList = $stmt->fetchAll();

        // Overall statistics
        $totalFakultas = count($fakultasList);
        $totalProdi = $this->db->query("SELECT COUNT(*) FROM prodis")->fetchColumn();
        $totalDokumen = $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE deleted_at IS NULL AND status_review != 'draft'")->fetchColumn();

        // Count per cycle across university (excluding draft)
        $cycleCounts = [
            'penetapan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='penetapan' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
            'pelaksanaan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='pelaksanaan' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
            'evaluasi' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='evaluasi' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
            'pengendalian' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='pengendalian' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
            'peningkatan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='peningkatan' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
        ];

        $this->render('public/home', [
            'pageTitle' => 'Portal MITRA',
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
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
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
            WHERE fakultas_id = ? AND level = 'fakultas' AND deleted_at IS NULL AND status_review != 'draft'
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

        // Fetch All Active Bidang Standar for Filtering
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        // Fetch All Active Documents for this prodi (Excluding soft deleted)
        $stmtDocs = $this->db->prepare("
            SELECT d.*, b.nama_bidang, b.kode_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as uploader_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.prodi_id = ? AND d.deleted_at IS NULL AND d.status_review != 'draft'
            ORDER BY d.created_at DESC
        ");
        $stmtDocs->execute([$prodiId]);
        $allDocuments = $stmtDocs->fetchAll();

        $this->attachFilesToDocs($allDocuments);

        // Calculate accurate statistics per cycle
        $cycleStats = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0,
            'total' => 0
        ];

        // Group documents by cycle for tab rendering
        $docsByCycle = [
            'penetapan' => [],
            'pelaksanaan' => [],
            'evaluasi' => [],
            'pengendalian' => [],
            'peningkatan' => []
        ];

        // Calculate statistics per bidang
        $bidangStats = [];

        foreach ($allDocuments as $doc) {
            $siklus = $doc['siklus'];
            if (isset($cycleStats[$siklus])) {
                $cycleStats[$siklus]++;
                $cycleStats['total']++;
                $docsByCycle[$siklus][] = $doc;
            }

            $bidangName = $doc['nama_bidang'] ?? 'Lainnya';
            $bidangStats[$bidangName] = ($bidangStats[$bidangName] ?? 0) + 1;
        }

        // Fetch fakultas details
        $fakultasId = (int)$prodi['fakultas_id'];
        $stmtFak = $this->db->prepare("SELECT * FROM fakultas WHERE id = ?");
        $stmtFak->execute([$fakultasId]);
        $fakultas = $stmtFak->fetch();

        // Calculate total faculty documents count
        $stmtFakCount = $this->db->prepare("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = ? AND level = 'fakultas' AND deleted_at IS NULL AND status_review != 'draft'");
        $stmtFakCount->execute([$fakultasId]);
        $fakultas['total_dokumen'] = (int)$stmtFakCount->fetchColumn();

        // Fetch all prodis under the same fakultas with document counts for switcher
        $stmtProdis = $this->db->prepare("
            SELECT p.id, p.nama_prodi, p.jenjang, p.kode_prodi,
                   COUNT(d.id) as total_dokumen
            FROM prodis p
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
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
            'docsByCycle' => $docsByCycle,
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

        // Calculate total faculty documents count
        $stmtFakCount = $this->db->prepare("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = ? AND level = 'fakultas' AND deleted_at IS NULL AND status_review != 'draft'");
        $stmtFakCount->execute([$fId]);
        $fakultas['total_dokumen'] = (int)$stmtFakCount->fetchColumn();

        // Fetch All Active Bidang Standar for Filtering
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        // Fetch All Active Faculty Documents (level = 'fakultas')
        $stmtDocs = $this->db->prepare("
            SELECT d.*, b.nama_bidang, b.kode_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as uploader_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.deleted_at IS NULL AND d.status_review != 'draft'
            ORDER BY d.created_at DESC
        ");
        $stmtDocs->execute([$fId]);
        $allDocuments = $stmtDocs->fetchAll();

        $this->attachFilesToDocs($allDocuments);

        // Accurate statistics per cycle
        $cycleStats = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0,
            'total' => 0
        ];

        $docsByCycle = [
            'penetapan' => [],
            'pelaksanaan' => [],
            'evaluasi' => [],
            'pengendalian' => [],
            'peningkatan' => []
        ];

        // Calculate statistics per bidang
        $bidangStats = [];

        foreach ($allDocuments as $doc) {
            $siklus = $doc['siklus'];
            if (isset($cycleStats[$siklus])) {
                $cycleStats[$siklus]++;
                $cycleStats['total']++;
                $docsByCycle[$siklus][] = $doc;
            }

            $bidangName = $doc['nama_bidang'] ?? 'Lainnya';
            $bidangStats[$bidangName] = ($bidangStats[$bidangName] ?? 0) + 1;
        }

        // Fetch prodi list under this fakultas with document count for switcher
        $stmtProdis = $this->db->prepare("
            SELECT p.id, p.nama_prodi, p.jenjang, p.kode_prodi,
                   COUNT(d.id) as total_dokumen
            FROM prodis p
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
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
            'docsByCycle' => $docsByCycle,
            'cycleStats' => $cycleStats,
            'bidangStats' => $bidangStats,
            'prodiList' => $prodiList
        ]);
    }

    /**
     * 4. Halaman Repositori Dokumen Mutu Keseluruhan
     */
    public function allDocuments(): void {
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
        $fakultasList = $this->db->query("SELECT id, nama_fakultas, kode_fakultas FROM fakultas ORDER BY nama_fakultas ASC")->fetchAll();
        $prodiList = $this->db->query("SELECT id, nama_prodi, jenjang, fakultas_id FROM prodis ORDER BY nama_prodi ASC")->fetchAll();

        $stmtDocs = $this->db->query("
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
            WHERE d.deleted_at IS NULL AND d.status_review != 'draft'
            ORDER BY d.created_at DESC
        ");
        $allDocuments = $stmtDocs->fetchAll();

        $this->attachFilesToDocs($allDocuments);

        // Calculate statistics per cycle for Capaian Mutu Universitas card
        $cycleCounts = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0
        ];
        foreach ($allDocuments as $doc) {
            $s = strtolower(trim($doc['siklus']));
            if (isset($cycleCounts[$s])) {
                $cycleCounts[$s]++;
            }
        }
        $totalDokumen = count($allDocuments);
        $maxDocs = max(1, ...array_values($cycleCounts));

        $this->render('public/all_documents', [
            'pageTitle' => 'Portal Repositori Dokumen Mutu',
            'activeNav' => 'dokumen',
            'bidangList' => $bidangList,
            'fakultasList' => $fakultasList,
            'prodiList' => $prodiList,
            'allDocuments' => $allDocuments,
            'cycleCounts' => $cycleCounts,
            'totalDokumen' => $totalDokumen,
            'maxDocs' => $maxDocs
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
}
