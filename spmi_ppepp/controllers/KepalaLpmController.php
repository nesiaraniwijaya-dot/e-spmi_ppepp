<?php
/**
 * Kepala LPM Controller (Monitoring & Audit Trail)
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';

class KepalaLpmController extends Controller {

    public function __construct() {
        parent::__construct();
        // Allowed for kepala_lpm and super_admin
        Auth::requireRole(['kepala_lpm', 'super_admin']);
    }

    /**
     * Dashboard Monitoring Eksekutif Kepala LPM
     */
    public function dashboard(): void {
        $totalFakultas = $this->db->query("SELECT COUNT(*) FROM fakultas")->fetchColumn();
        $totalProdi = $this->db->query("SELECT COUNT(*) FROM prodis")->fetchColumn();
        $totalDokumen = $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE deleted_at IS NULL")->fetchColumn();
        $totalAudit = $this->db->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();

        // 5 Siklus Stats
        $cycleStats = [
            'penetapan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='penetapan' AND deleted_at IS NULL")->fetchColumn(),
            'pelaksanaan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='pelaksanaan' AND deleted_at IS NULL")->fetchColumn(),
            'evaluasi' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='evaluasi' AND deleted_at IS NULL")->fetchColumn(),
            'pengendalian' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='pengendalian' AND deleted_at IS NULL")->fetchColumn(),
            'peningkatan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='peningkatan' AND deleted_at IS NULL")->fetchColumn(),
        ];

        // Sebaran Fakultas
        $stmtFakStats = $this->db->query("
            SELECT f.nama_fakultas, f.kode_fakultas,
                   COUNT(DISTINCT p.id) as total_prodi,
                   COUNT(DISTINCT d.id) as total_dokumen
            FROM fakultas f
            LEFT JOIN prodis p ON f.id = p.fakultas_id
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL
            GROUP BY f.id
            ORDER BY total_dokumen DESC
        ");
        $fakultasStats = $stmtFakStats->fetchAll();

        // Recent Audit logs
        $stmtLogs = $this->db->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 8");
        $recentLogs = $stmtLogs->fetchAll();

        $this->render('kepala_lpm/dashboard', [
            'pageTitle' => 'Monitoring Eksekutif Penjaminan Mutu',
            'totalFakultas' => $totalFakultas,
            'totalProdi' => $totalProdi,
            'totalDokumen' => $totalDokumen,
            'totalAudit' => $totalAudit,
            'cycleStats' => $cycleStats,
            'fakultasStats' => $fakultasStats,
            'recentLogs' => $recentLogs
        ]);
    }

    /**
     * Rekapitulasi Dokumen Lintas Fakultas & Program Studi
     */
    public function rekapitulasi(): void {
        $stmt = $this->db->query("
            SELECT p.id, p.kode_prodi, p.nama_prodi, p.jenjang, p.nama_kaprodi, f.nama_fakultas,
                   COUNT(d.id) as total_dokumen,
                   SUM(CASE WHEN d.siklus = 'penetapan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as p1_count,
                   SUM(CASE WHEN d.siklus = 'pelaksanaan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as p2_count,
                   SUM(CASE WHEN d.siklus = 'evaluasi' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as e_count,
                   SUM(CASE WHEN d.siklus = 'pengendalian' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as p3_count,
                   SUM(CASE WHEN d.siklus = 'peningkatan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as p4_count
            FROM prodis p
            JOIN fakultas f ON p.fakultas_id = f.id
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL
            GROUP BY p.id
            ORDER BY f.nama_fakultas ASC, p.nama_prodi ASC
        ");
        $rekapList = $stmt->fetchAll();

        $this->render('kepala_lpm/rekapitulasi', [
            'pageTitle' => 'Rekapitulasi Dokumen PPEPP Universitas',
            'rekapList' => $rekapList
        ]);
    }

    /**
     * Audit Trail Viewer (Pencatatan Riwayat Aktivitas & Perubahan Data Terperinci)
     */
    public function audit(): void {
        $prodiFilter = $_GET['prodi_id'] ?? '';
        $aksiFilter = $_GET['aksi'] ?? '';
        $userFilter = $_GET['user_id'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';

        $sql = "SELECT * FROM audit_logs WHERE 1=1";
        $params = [];

        if (!empty($prodiFilter)) {
            $sql .= " AND prodi_id = ?";
            $params[] = $prodiFilter;
        }
        if (!empty($aksiFilter)) {
            $sql .= " AND aksi = ?";
            $params[] = $aksiFilter;
        }
        if (!empty($userFilter)) {
            $sql .= " AND user_id = ?";
            $params[] = $userFilter;
        }
        if (!empty($startDate)) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $startDate;
        }
        if (!empty($endDate)) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $endDate;
        }

        $sql .= " ORDER BY created_at DESC LIMIT 200";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $auditLogs = $stmt->fetchAll();

        $prodiList = $this->db->query("SELECT id, nama_prodi, jenjang FROM prodis ORDER BY nama_prodi ASC")->fetchAll();
        $userList = $this->db->query("SELECT id, name, role FROM users ORDER BY name ASC")->fetchAll();

        $this->render('kepala_lpm/audit_trail', [
            'pageTitle' => 'Audit Trail & Rekam Jejak Sistem',
            'auditLogs' => $auditLogs,
            'prodiList' => $prodiList,
            'userList' => $userList,
            'filters' => [
                'prodi_id' => $prodiFilter,
                'aksi' => $aksiFilter,
                'user_id' => $userFilter,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }
}
