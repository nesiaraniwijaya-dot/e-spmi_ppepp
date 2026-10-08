<?php
/**
 * Super Admin / Admin LPM Controller
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/AuditLogger.php';

class SuperAdminController extends Controller {

    public function __construct() {
        parent::__construct();
        Auth::requireRole(['super_admin', 'admin_lpm', 'kepala_lpm', 'kepala_pusat_mutu']);
    }

    /**
     * Dashboard Super Admin
     */
    public function dashboard(): void {
        $totalFakultas = $this->db->query("SELECT COUNT(*) FROM fakultas")->fetchColumn();
        $totalProdi = $this->db->query("SELECT COUNT(*) FROM prodis")->fetchColumn();
        $totalUsers = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $totalDokumen = $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE deleted_at IS NULL AND status_review != 'draft'")->fetchColumn();

        // 5 Siklus Stats (hanya dokumen non-draft yang telah diajukan)
        $cycleStats = [
            'penetapan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='penetapan' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
            'pelaksanaan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='pelaksanaan' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
            'evaluasi' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='evaluasi' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
            'pengendalian' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='pengendalian' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
            'peningkatan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE siklus='peningkatan' AND deleted_at IS NULL AND status_review != 'draft'")->fetchColumn(),
        ];

        // Review Statistics for Executive Summary
        $belumDireviewCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE status_review = 'belum_direview' AND deleted_at IS NULL")->fetchColumn();
        $sudahDiperbaikiCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE status_review = 'sudah_diperbaiki' AND deleted_at IS NULL")->fetchColumn();
        $perluPerbaikanCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE status_review = 'perlu_perbaikan' AND deleted_at IS NULL")->fetchColumn();
        $sesuaiCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE status_review = 'sesuai' AND deleted_at IS NULL")->fetchColumn();

        $reviewStats = [
            'perlu_tindakan' => $belumDireviewCount + $sudahDiperbaikiCount,
            'belum_direview' => $belumDireviewCount,
            'sudah_diperbaiki' => $sudahDiperbaikiCount,
            'perlu_perbaikan' => $perluPerbaikanCount,
            'sesuai' => $sesuaiCount,
            'total' => (int)$totalDokumen
        ];

        // Antrean 6 Dokumen Paling Mendesak Membutuhkan Review
        $stmtUrgent = $this->db->query("
            SELECT d.*, 
                   b.nama_bidang, b.kode_bidang, 
                   sb.nama_sub_bidang, sb.kode_sub_bidang, 
                   p.nama_prodi, p.kode_prodi, p.jenjang,
                   f.id as calculated_fakultas_id, f.nama_fakultas, f.kode_fakultas,
                   u.name as uploader_name
            FROM ppepp_documents d
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON (f.id = COALESCE(d.fakultas_id, p.fakultas_id))
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.status_review IN ('belum_direview', 'sudah_diperbaiki') AND d.deleted_at IS NULL
            ORDER BY 
              CASE WHEN d.status_review = 'sudah_diperbaiki' THEN 1 ELSE 2 END ASC,
              d.created_at DESC
            LIMIT 6
        ");
        $urgentReviewDocs = $stmtUrgent->fetchAll();
        $this->attachFilesToDocs($urgentReviewDocs);

        // Recent Audit Logs
        $stmtLogs = $this->db->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 10");
        $recentLogs = $stmtLogs->fetchAll();

        // Fakultas list & Prodis grouped by Fakultas for hierarchical review workspace
        $stmtFakultas = $this->db->query("
            SELECT f.id, f.kode_fakultas, f.nama_fakultas, COUNT(p.id) as prodi_count
            FROM fakultas f
            LEFT JOIN prodis p ON f.id = p.fakultas_id
            GROUP BY f.id
            ORDER BY f.nama_fakultas ASC
        ");
        $fakultasGroups = $stmtFakultas->fetchAll();

        // Prodi summary with review status breakdown
        $stmtProdiSummary = $this->db->query("
            SELECT p.id, p.kode_prodi, p.nama_prodi, p.jenjang, p.fakultas_id, f.nama_fakultas, p.nama_kaprodi,
                   COUNT(d.id) as total_dokumen,
                   SUM(CASE WHEN d.status_review IN ('belum_direview', 'sudah_diperbaiki') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as perlu_review_count,
                   SUM(CASE WHEN d.status_review = 'perlu_perbaikan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as revisi_count,
                   SUM(CASE WHEN d.status_review = 'sesuai' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sesuai_count
            FROM prodis p
            JOIN fakultas f ON p.fakultas_id = f.id
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
            GROUP BY p.id
            ORDER BY f.nama_fakultas ASC, p.jenjang ASC, p.nama_prodi ASC
        ");
        $prodiSummaries = $stmtProdiSummary->fetchAll();

        $prodisByFakultas = [];
        foreach ($prodiSummaries as $ps) {
            $prodisByFakultas[$ps['fakultas_id']][] = $ps;
        }

        // Fakultas PPEPP summary (dokumen level = 'fakultas')
        $stmtFakPpepp = $this->db->query("
            SELECT f.id, f.kode_fakultas, f.nama_fakultas, f.nama_dekan,
                   COUNT(d.id) as total_dokumen_fakultas,
                   SUM(CASE WHEN d.status_review IN ('belum_direview', 'sudah_diperbaiki') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as perlu_review_count,
                   SUM(CASE WHEN d.status_review = 'perlu_perbaikan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as revisi_count,
                   SUM(CASE WHEN d.status_review = 'sesuai' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sesuai_count
            FROM fakultas f
            LEFT JOIN ppepp_documents d ON f.id = d.fakultas_id AND d.level = 'fakultas' AND d.deleted_at IS NULL AND d.status_review != 'draft'
            GROUP BY f.id
        ");
        $fakultasPpeppSummaries = [];
        foreach ($stmtFakPpepp->fetchAll() as $fps) {
            $fakultasPpeppSummaries[$fps['id']] = $fps;
        }

        $this->render('super_admin/dashboard', [
            'pageTitle' => 'Dashboard Admin LPM',
            'totalFakultas' => $totalFakultas,
            'totalProdi' => $totalProdi,
            'totalUsers' => $totalUsers,
            'totalDokumen' => $totalDokumen,
            'cycleStats' => $cycleStats,
            'reviewStats' => $reviewStats,
            'urgentReviewDocs' => $urgentReviewDocs,
            'recentLogs' => $recentLogs,
            'prodiSummaries' => $prodiSummaries,
            'fakultasGroups' => $fakultasGroups,
            'prodisByFakultas' => $prodisByFakultas,
            'fakultasPpeppSummaries' => $fakultasPpeppSummaries
        ]);
    }

    /**
     * Master Fakultas
     */
    public function fakultas(): void {
        $stmt = $this->db->query("
            SELECT f.*, COUNT(p.id) as total_prodi 
            FROM fakultas f 
            LEFT JOIN prodis p ON f.id = p.fakultas_id 
            GROUP BY f.id 
            ORDER BY f.nama_fakultas ASC
        ");
        $fakultasList = $stmt->fetchAll();

        $this->render('super_admin/fakultas', [
            'pageTitle' => 'Kelola Master Fakultas',
            'fakultasList' => $fakultasList
        ]);
    }

    public function saveFakultas(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $kode = strtoupper(trim($_POST['kode_fakultas'] ?? ''));
        $nama = trim($_POST['nama_fakultas'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if (empty($kode) || empty($nama)) {
            Auth::setFlash('danger', 'Kode dan Nama Fakultas wajib diisi.');
            redirect('admin/fakultas');
        }

        if ($id) {
            // Update
            $stmtOld = $this->db->prepare("SELECT * FROM fakultas WHERE id = ?");
            $stmtOld->execute([$id]);
            $oldData = $stmtOld->fetch();

            $stmt = $this->db->prepare("UPDATE fakultas SET kode_fakultas = ?, nama_fakultas = ?, deskripsi = ? WHERE id = ?");
            $stmt->execute([$kode, $nama, $deskripsi, $id]);

            AuditLogger::log('UPDATE', 'Master Fakultas', (string)$id, $nama, $oldData, ['kode' => $kode, 'nama' => $nama, 'deskripsi' => $deskripsi]);
            Auth::setFlash('success', "Fakultas '{$nama}' berhasil diperbarui.");
        } else {
            // Create
            $stmt = $this->db->prepare("INSERT INTO fakultas (kode_fakultas, nama_fakultas, deskripsi) VALUES (?, ?, ?)");
            $stmt->execute([$kode, $nama, $deskripsi]);
            $newId = $this->db->lastInsertId();

            AuditLogger::log('CREATE', 'Master Fakultas', (string)$newId, $nama, null, ['kode' => $kode, 'nama' => $nama, 'deskripsi' => $deskripsi]);
            Auth::setFlash('success', "Fakultas baru '{$nama}' berhasil ditambahkan.");
        }

        redirect('admin/fakultas');
    }

    public function deleteFakultas(string $id): void {
        $stmtOld = $this->db->prepare("SELECT * FROM fakultas WHERE id = ?");
        $stmtOld->execute([$id]);
        $fak = $stmtOld->fetch();

        if ($fak) {
            $stmt = $this->db->prepare("DELETE FROM fakultas WHERE id = ?");
            $stmt->execute([$id]);

            AuditLogger::log('DELETE', 'Master Fakultas', (string)$id, $fak['nama_fakultas'], $fak, null);
            Auth::setFlash('success', "Fakultas '{$fak['nama_fakultas']}' beserta data prodi terkait telah dihapus.");
        }

        redirect('admin/fakultas');
    }

    /**
     * Master Program Studi (Prodi)
     */
    public function prodi(): void {
        $fakultasList = $this->db->query("SELECT id, nama_fakultas, kode_fakultas FROM fakultas ORDER BY nama_fakultas ASC")->fetchAll();

        $stmt = $this->db->query("
            SELECT p.*, f.nama_fakultas, f.kode_fakultas as kode_fak,
                   COUNT(d.id) as total_dokumen,
                   (SELECT u.name FROM users u WHERE u.prodi_id = p.id AND u.role = 'kaprodi' LIMIT 1) as kaprodi_user_name,
                   (SELECT u.name FROM users u WHERE u.prodi_id = p.id AND u.role = 'sekprodi' LIMIT 1) as sekprodi_user_name
            FROM prodis p
            JOIN fakultas f ON p.fakultas_id = f.id
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
            GROUP BY p.id
            ORDER BY f.nama_fakultas ASC, p.nama_prodi ASC
        ");
        $prodiList = $stmt->fetchAll();

        $this->render('super_admin/prodi', [
            'pageTitle' => 'Kelola Master Program Studi',
            'fakultasList' => $fakultasList,
            'prodiList' => $prodiList
        ]);
    }

    public function saveProdi(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $fakultasId = (int)($_POST['fakultas_id'] ?? 0);
        $kodeProdi = trim($_POST['kode_prodi'] ?? '');
        $namaProdi = trim($_POST['nama_prodi'] ?? '');
        $jenjang = trim($_POST['jenjang'] ?? 'S1');
        $namaKaprodi = trim($_POST['nama_kaprodi'] ?? '');
        $nidnKaprodi = trim($_POST['nidn_kaprodi'] ?? '');
        $namaSekprodi = trim($_POST['nama_sekprodi'] ?? '');
        $nidnSekprodi = trim($_POST['nidn_sekprodi'] ?? '');

        if (empty($fakultasId) || empty($kodeProdi) || empty($namaProdi)) {
            Auth::setFlash('danger', 'Fakultas, Kode Prodi, dan Nama Prodi wajib diisi.');
            redirect('admin/prodi');
        }

        if ($id) {
            $stmtOld = $this->db->prepare("SELECT * FROM prodis WHERE id = ?");
            $stmtOld->execute([$id]);
            $oldData = $stmtOld->fetch();

            $stmt = $this->db->prepare("UPDATE prodis SET fakultas_id = ?, kode_prodi = ?, nama_prodi = ?, jenjang = ?, nama_kaprodi = ?, nidn_kaprodi = ?, nama_sekprodi = ?, nidn_sekprodi = ? WHERE id = ?");
            $stmt->execute([$fakultasId, $kodeProdi, $namaProdi, $jenjang, $namaKaprodi, $nidnKaprodi, $namaSekprodi, $nidnSekprodi, $id]);

            AuditLogger::log('UPDATE', 'Master Prodi', (string)$id, $namaProdi, $oldData, ['kode' => $kodeProdi, 'nama' => $namaProdi, 'kaprodi' => $namaKaprodi, 'sekprodi' => $namaSekprodi], $id, $namaProdi);
            Auth::setFlash('success', "Program Studi '{$namaProdi}' berhasil diperbarui.");
        } else {
            $stmt = $this->db->prepare("INSERT INTO prodis (fakultas_id, kode_prodi, nama_prodi, jenjang, nama_kaprodi, nidn_kaprodi, nama_sekprodi, nidn_sekprodi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fakultasId, $kodeProdi, $namaProdi, $jenjang, $namaKaprodi, $nidnKaprodi, $namaSekprodi, $nidnSekprodi]);
            $newId = $this->db->lastInsertId();

            AuditLogger::log('CREATE', 'Master Prodi', (string)$newId, $namaProdi, null, ['kode' => $kodeProdi, 'nama' => $namaProdi, 'jenjang' => $jenjang], (int)$newId, $namaProdi);
            Auth::setFlash('success', "Program Studi baru '{$namaProdi}' berhasil ditambahkan.");
        }

        redirect('admin/prodi');
    }

    public function deleteProdi(string $id): void {
        $stmtOld = $this->db->prepare("SELECT * FROM prodis WHERE id = ?");
        $stmtOld->execute([$id]);
        $p = $stmtOld->fetch();

        if ($p) {
            $stmt = $this->db->prepare("DELETE FROM prodis WHERE id = ?");
            $stmt->execute([$id]);

            AuditLogger::log('DELETE', 'Master Prodi', (string)$id, $p['nama_prodi'], $p, null, (int)$id, $p['nama_prodi']);
            Auth::setFlash('success', "Program Studi '{$p['nama_prodi']}' telah dihapus.");
        }

        redirect('admin/prodi');
    }

    /**
     * Master Bidang Standar Dokumen Kustom (Permintaan User: Dapat dikustomisasi Admin LPM)
     */
    public function bidang(): void {
        $stmt = $this->db->query("
            SELECT b.*, COUNT(d.id) as total_dokumen
            FROM bidang_standar b
            LEFT JOIN ppepp_documents d ON b.id = d.bidang_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
            GROUP BY b.id
            ORDER BY b.id ASC
        ");
        $bidangList = $stmt->fetchAll();

        // Fetch sub-bidang standar with total associated documents
        $stmtSub = $this->db->query("
            SELECT s.*, COUNT(d.id) as total_dokumen
            FROM sub_bidang_standar s
            LEFT JOIN ppepp_documents d ON s.id = d.sub_bidang_id AND d.deleted_at IS NULL AND d.status_review != 'draft'
            GROUP BY s.id
            ORDER BY s.bidang_id ASC, s.kode_sub_bidang ASC, s.id ASC
        ");
        $allSub = $stmtSub->fetchAll();

        $subByBidang = [];
        foreach ($allSub as $sub) {
            $subByBidang[$sub['bidang_id']][] = $sub;
        }

        foreach ($bidangList as &$b) {
            $b['sub_bidang'] = $subByBidang[$b['id']] ?? [];
            $b['total_sub'] = count($b['sub_bidang']);
        }
        unset($b);

        $this->render('super_admin/bidang', [
            'pageTitle' => 'Kelola Master Bidang & Sub Standar Mutu',
            'bidangList' => $bidangList
        ]);
    }

    public function saveBidang(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $kode = trim($_POST['kode_bidang'] ?? '');
        $nama = trim($_POST['nama_bidang'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($nama)) {
            Auth::setFlash('danger', 'Nama Bidang Standar wajib diisi.');
            redirect('admin/bidang');
        }

        if ($id) {
            $stmtOld = $this->db->prepare("SELECT * FROM bidang_standar WHERE id = ?");
            $stmtOld->execute([$id]);
            $oldData = $stmtOld->fetch();

            $stmt = $this->db->prepare("UPDATE bidang_standar SET kode_bidang = ?, nama_bidang = ?, deskripsi = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$kode, $nama, $deskripsi, $isActive, $id]);

            AuditLogger::log('UPDATE', 'Master Bidang Standar', (string)$id, $nama, $oldData, ['kode' => $kode, 'nama' => $nama, 'is_active' => $isActive]);
            Auth::setFlash('success', "Bidang Standar '{$nama}' berhasil diperbarui.");
        } else {
            $stmt = $this->db->prepare("INSERT INTO bidang_standar (kode_bidang, nama_bidang, deskripsi, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([$kode, $nama, $deskripsi, $isActive]);
            $newId = $this->db->lastInsertId();

            AuditLogger::log('CREATE', 'Master Bidang Standar', (string)$newId, $nama, null, ['kode' => $kode, 'nama' => $nama, 'is_active' => $isActive]);
            Auth::setFlash('success', "Bidang Standar baru '{$nama}' berhasil ditambahkan.");
        }

        redirect('admin/bidang');
    }

    public function deleteBidang(string $id): void {
        $stmtOld = $this->db->prepare("SELECT * FROM bidang_standar WHERE id = ?");
        $stmtOld->execute([$id]);
        $b = $stmtOld->fetch();

        if ($b) {
            // Check if any documents are tied to this bidang
            $stmtDocs = $this->db->prepare("SELECT COUNT(*) FROM ppepp_documents WHERE bidang_id = ? AND deleted_at IS NULL");
            $stmtDocs->execute([$id]);
            $docCount = (int)$stmtDocs->fetchColumn();
            if ($docCount > 0) {
                Auth::setFlash('danger', "Bidang Standar '{$b['nama_bidang']}' tidak dapat dihapus karena masih digunakan oleh {$docCount} dokumen aktif. Silakan nonaktifkan status bidang.");
                redirect('admin/bidang');
            }

            $stmt = $this->db->prepare("DELETE FROM bidang_standar WHERE id = ?");
            $stmt->execute([$id]);

            AuditLogger::log('DELETE', 'Master Bidang Standar', (string)$id, $b['nama_bidang'], $b, null);
            Auth::setFlash('success', "Bidang Standar '{$b['nama_bidang']}' telah dihapus.");
        }

        redirect('admin/bidang');
    }

    /**
     * Master Sub-Bidang Standar Mutu CRUD
     */
    public function saveSubBidang(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $bidangId = !empty($_POST['bidang_id']) ? (int)$_POST['bidang_id'] : null;
        $kode = trim($_POST['kode_sub_bidang'] ?? '');
        $nama = trim($_POST['nama_sub_bidang'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($bidangId)) {
            Auth::setFlash('danger', 'Bidang Standar Mutu induk wajib dipilih.');
            redirect('admin/bidang');
        }

        if (empty($nama)) {
            Auth::setFlash('danger', 'Nama Sub Standar Mutu wajib diisi.');
            redirect('admin/bidang#bidang-' . $bidangId);
        }

        if ($id) {
            $stmtOld = $this->db->prepare("SELECT * FROM sub_bidang_standar WHERE id = ?");
            $stmtOld->execute([$id]);
            $oldData = $stmtOld->fetch();

            $stmt = $this->db->prepare("UPDATE sub_bidang_standar SET bidang_id = ?, kode_sub_bidang = ?, nama_sub_bidang = ?, deskripsi = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$bidangId, $kode, $nama, $deskripsi, $isActive, $id]);

            AuditLogger::log('UPDATE', 'Master Sub Standar', (string)$id, $nama, $oldData, ['bidang_id' => $bidangId, 'kode' => $kode, 'nama' => $nama, 'is_active' => $isActive]);
            Auth::setFlash('success', "Sub Standar Mutu '{$nama}' berhasil diperbarui.");
        } else {
            $stmt = $this->db->prepare("INSERT INTO sub_bidang_standar (bidang_id, kode_sub_bidang, nama_sub_bidang, deskripsi, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$bidangId, $kode, $nama, $deskripsi, $isActive]);
            $newId = $this->db->lastInsertId();

            AuditLogger::log('CREATE', 'Master Sub Standar', (string)$newId, $nama, null, ['bidang_id' => $bidangId, 'kode' => $kode, 'nama' => $nama, 'is_active' => $isActive]);
            Auth::setFlash('success', "Sub Standar Mutu baru '{$nama}' berhasil ditambahkan.");
        }

        redirect('admin/bidang#bidang-' . $bidangId);
    }

    public function deleteSubBidang(string $id): void {
        $stmtOld = $this->db->prepare("SELECT s.*, b.nama_bidang FROM sub_bidang_standar s JOIN bidang_standar b ON s.bidang_id = b.id WHERE s.id = ?");
        $stmtOld->execute([$id]);
        $sub = $stmtOld->fetch();

        if ($sub) {
            $bidangId = $sub['bidang_id'];
            // Check if any documents use this sub_bidang_id
            $stmtDocs = $this->db->prepare("SELECT COUNT(*) FROM ppepp_documents WHERE sub_bidang_id = ? AND deleted_at IS NULL");
            $stmtDocs->execute([$id]);
            $usedDocsCount = (int)$stmtDocs->fetchColumn();

            if ($usedDocsCount > 0) {
                Auth::setFlash('danger', "Sub Standar '{$sub['nama_sub_bidang']}' tidak dapat dihapus karena masih digunakan oleh {$usedDocsCount} dokumen aktif. Anda dapat mengubah statusnya menjadi 'Non-Aktif' jika tidak ingin digunakan kembali.");
                redirect('admin/bidang#bidang-' . $bidangId);
            }

            $stmt = $this->db->prepare("DELETE FROM sub_bidang_standar WHERE id = ?");
            $stmt->execute([$id]);

            AuditLogger::log('DELETE', 'Master Sub Standar', (string)$id, $sub['nama_sub_bidang'], $sub, null);
            Auth::setFlash('success', "Sub Standar Mutu '{$sub['nama_sub_bidang']}' berhasil dihapus.");
            redirect('admin/bidang#bidang-' . $bidangId);
        }

        redirect('admin/bidang');
    }

    /**
     * Manajemen Pengguna (Users & Roles)
     */
    public function users(): void {
        $fakultasList = $this->db->query("SELECT id, kode_fakultas, nama_fakultas FROM fakultas ORDER BY nama_fakultas ASC")->fetchAll();
        $prodiList = $this->db->query("SELECT p.id, p.nama_prodi, p.jenjang, f.nama_fakultas FROM prodis p JOIN fakultas f ON p.fakultas_id = f.id ORDER BY p.nama_prodi ASC")->fetchAll();

        $stmt = $this->db->query("
            SELECT u.*, p.nama_prodi, p.jenjang, 
                   COALESCE(f_direct.nama_fakultas, f_prodi.nama_fakultas) as nama_fakultas,
                   COALESCE(u.fakultas_id, p.fakultas_id) as resolved_fakultas_id
            FROM users u
            LEFT JOIN prodis p ON u.prodi_id = p.id
            LEFT JOIN fakultas f_prodi ON p.fakultas_id = f_prodi.id
            LEFT JOIN fakultas f_direct ON u.fakultas_id = f_direct.id
            ORDER BY u.role ASC, u.name ASC
        ");
        $usersList = $stmt->fetchAll();

        $this->render('super_admin/users', [
            'pageTitle' => 'Manajemen Pengguna & Hak Akses',
            'fakultasList' => $fakultasList,
            'prodiList' => $prodiList,
            'usersList' => $usersList
        ]);
    }

    public function saveUser(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? 'kaprodi');
        $isProdiRole = in_array($role, ['kaprodi', 'sekprodi']);
        $isFakultasRole = in_array($role, ['dekan', 'wadek', 'gpm']);
        $prodiId = ($isProdiRole && !empty($_POST['prodi_id'])) ? (int)$_POST['prodi_id'] : null;
        $fakultasId = ($isFakultasRole && !empty($_POST['fakultas_id'])) ? (int)$_POST['fakultas_id'] : null;
        if ($isProdiRole && $prodiId) {
            $fakultasId = (int)$this->db->query("SELECT fakultas_id FROM prodis WHERE id = {$prodiId}")->fetchColumn() ?: null;
        }
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name) || empty($email) || empty($role)) {
            Auth::setFlash('danger', 'Nama, Email, dan Role wajib diisi.');
            redirect('admin/users');
        }

        if ($id) {
            $stmtOld = $this->db->prepare("SELECT id, name, email, role, prodi_id, fakultas_id, is_active FROM users WHERE id = ?");
            $stmtOld->execute([$id]);
            $oldData = $stmtOld->fetch();

            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, password = ?, password_plain = ?, role = ?, prodi_id = ?, fakultas_id = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $email, $hashed, $password, $role, $prodiId, $fakultasId, $isActive, $id]);
            } else {
                $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, role = ?, prodi_id = ?, fakultas_id = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $email, $role, $prodiId, $fakultasId, $isActive, $id]);
            }

            AuditLogger::log('UPDATE', 'Manajemen User', (string)$id, $name, $oldData, ['name' => $name, 'email' => $email, 'role' => $role, 'prodi_id' => $prodiId, 'fakultas_id' => $fakultasId, 'is_active' => $isActive]);
            Auth::setFlash('success', "Akun pengguna '{$name}' berhasil diperbarui.");
        } else {
            $plainPwd = !empty($password) ? $password : '-(Diatur via Google SSO)-';
            $pwdToHash = !empty($password) ? $password : bin2hex(random_bytes(10));
            $hashed = password_hash($pwdToHash, PASSWORD_BCRYPT);

            $stmt = $this->db->prepare("INSERT INTO users (name, email, password, password_plain, role, prodi_id, fakultas_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed, $plainPwd, $role, $prodiId, $fakultasId, $isActive]);
            $newId = $this->db->lastInsertId();

            AuditLogger::log('CREATE', 'Manajemen User', (string)$newId, $name, null, ['name' => $name, 'email' => $email, 'role' => $role, 'prodi_id' => $prodiId, 'fakultas_id' => $fakultasId]);
            Auth::setFlash('success', "Akun pengguna baru '{$name}' ({$email}) berhasil didaftarkan ke sistem.");
        }

        redirect('admin/users');
    }

    public function deleteUser(string $id): void {
        if ((int)$id === Auth::id()) {
            Auth::setFlash('danger', 'Anda tidak dapat menghapus akun Anda sendiri.');
            redirect('admin/users');
        }

        $stmtOld = $this->db->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmtOld->execute([$id]);
        $u = $stmtOld->fetch();

        if ($u) {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            AuditLogger::log('DELETE', 'Manajemen User', (string)$id, $u['name'], $u, null);
            Auth::setFlash('success', "Pengguna '{$u['name']}' telah dihapus.");
        }

        redirect('admin/users');
    }

    /**
     * Monitoring Unggah Dokumen PPEPP oleh Admin Prodi
     */
    public function monitoringDokumen(): void {
        $fakultasId = !empty($_GET['fakultas_id']) ? (int)$_GET['fakultas_id'] : '';
        $prodiId    = !empty($_GET['prodi_id']) ? (int)$_GET['prodi_id'] : '';
        $siklus     = trim($_GET['siklus'] ?? '');
        $aksi       = trim($_GET['aksi'] ?? '');
        $startDate  = trim($_GET['start_date'] ?? '');
        $endDate    = trim($_GET['end_date'] ?? '');

        // Base Query: Focus purely on Dokumen PPEPP activities
        $sql = "
            SELECT a.*, 
                   p.nama_prodi, p.kode_prodi, p.jenjang,
                   f.nama_fakultas, f.kode_fakultas,
                   d.nama_dokumen as doc_nama, d.siklus as doc_siklus,
                   d.jenis_upload, d.file_path, d.external_link, d.deleted_at as doc_deleted,
                   b.nama_bidang
            FROM audit_logs a
            LEFT JOIN prodis p ON a.prodi_id = p.id
            LEFT JOIN fakultas f ON p.fakultas_id = f.id
            LEFT JOIN ppepp_documents d ON a.target_id = d.id
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            WHERE a.modul = 'Dokumen PPEPP'
        ";
        $params = [];

        if ($fakultasId) {
            $sql .= " AND p.fakultas_id = ?";
            $params[] = $fakultasId;
        }

        if ($prodiId) {
            $sql .= " AND a.prodi_id = ?";
            $params[] = $prodiId;
        }

        if ($aksi) {
            $sql .= " AND a.aksi = ?";
            $params[] = $aksi;
        }

        if ($siklus) {
            $sql .= " AND (d.siklus = ? OR a.new_values LIKE ? OR a.old_values LIKE ?)";
            $params[] = $siklus;
            $params[] = '%"siklus":"' . $siklus . '"%';
            $params[] = '%"siklus":"' . $siklus . '"%';
        }

        if ($startDate) {
            $sql .= " AND DATE(a.created_at) >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND DATE(a.created_at) <= ?";
            $params[] = $endDate;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT 100";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $activities = $stmt->fetchAll();

        // Statistics
        $totalUploads = $this->db->query("SELECT COUNT(*) FROM audit_logs WHERE modul='Dokumen PPEPP' AND aksi='CREATE'")->fetchColumn();
        $totalUpdates = $this->db->query("SELECT COUNT(*) FROM audit_logs WHERE modul='Dokumen PPEPP' AND aksi='UPDATE'")->fetchColumn();
        $totalArchives = $this->db->query("SELECT COUNT(*) FROM audit_logs WHERE modul='Dokumen PPEPP' AND aksi='DELETE'")->fetchColumn();
        $totalThisMonth = $this->db->query("SELECT COUNT(*) FROM audit_logs WHERE modul='Dokumen PPEPP' AND aksi='CREATE' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn();

        // Master lists for filters
        $fakultasList = $this->db->query("SELECT id, nama_fakultas, kode_fakultas FROM fakultas ORDER BY nama_fakultas ASC")->fetchAll();
        $prodiList = $this->db->query("SELECT id, fakultas_id, nama_prodi, kode_prodi, jenjang FROM prodis ORDER BY nama_prodi ASC")->fetchAll();

        $this->render('super_admin/monitoring_dokumen', [
            'pageTitle' => 'Monitoring Unggah Dokumen PPEPP',
            'activities' => $activities,
            'fakultasList' => $fakultasList,
            'prodiList' => $prodiList,
            'filters' => [
                'fakultas_id' => $fakultasId,
                'prodi_id' => $prodiId,
                'siklus' => $siklus,
                'aksi' => $aksi,
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'stats' => [
                'total_uploads' => (int)$totalUploads,
                'total_updates' => (int)$totalUpdates,
                'total_archives' => (int)$totalArchives,
                'this_month' => (int)$totalThisMonth
            ]
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
                    'file_extension' => $d['file_extension'] ?? 'pdf',
                    'sub_bidang_ids' => !empty($d['sub_bidang_id']) ? [$d['sub_bidang_id']] : []
                ];
            }
            $d['files_count'] = count($d['files']);
        }
    }

    /**
     * Pusat Review Dokumen Mutu Terpusat (Admin LPM / Super Admin)
     */
    public function reviewHub(): void {
        // Ambil semua dokumen PPEPP aktif beserta relasi unit, bidang, dan uploader
        $stmtDocs = $this->db->query("
            SELECT d.*, 
                   b.nama_bidang, b.kode_bidang, 
                   sb.nama_sub_bidang, sb.kode_sub_bidang, 
                   p.nama_prodi, p.kode_prodi, p.jenjang,
                   f.id as calculated_fakultas_id, f.nama_fakultas, f.kode_fakultas,
                   u.name as uploader_name, 
                   r.name as reviewer_name
            FROM ppepp_documents d
            LEFT JOIN prodis p ON d.prodi_id = p.id
            LEFT JOIN fakultas f ON (f.id = COALESCE(d.fakultas_id, p.fakultas_id))
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            LEFT JOIN users r ON d.reviewed_by = r.id
            WHERE d.deleted_at IS NULL AND d.status_review != 'draft'
            ORDER BY 
              CASE 
                WHEN d.status_review = 'belum_direview' THEN 1
                WHEN d.status_review = 'sudah_diperbaiki' THEN 2
                WHEN d.status_review = 'perlu_perbaikan' THEN 3
                WHEN d.status_review = 'sesuai' THEN 4
                ELSE 5
              END ASC,
              d.created_at DESC
        ");
        $documents = $stmtDocs->fetchAll();
        $this->attachFilesToDocs($documents);

        // Hitung statistik review terpusat
        $reviewStats = [
            'total' => count($documents),
            'perlu_tindakan' => 0,
            'belum_direview' => 0,
            'sudah_diperbaiki' => 0,
            'perlu_perbaikan' => 0,
            'sesuai' => 0
        ];

        $cycleStats = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0
        ];

        foreach ($documents as $doc) {
            $st = $doc['status_review'] ?? 'belum_direview';
            if (isset($reviewStats[$st])) {
                $reviewStats[$st]++;
            }
            if ($st === 'belum_direview' || $st === 'sudah_diperbaiki') {
                $reviewStats['perlu_tindakan']++;
            }
            $cy = $doc['siklus'] ?? '';
            if (isset($cycleStats[$cy])) {
                $cycleStats[$cy]++;
            }
        }

        // Master lists untuk filter terpusat
        $fakultasList = $this->db->query("SELECT id, nama_fakultas, kode_fakultas FROM fakultas ORDER BY nama_fakultas ASC")->fetchAll();
        $prodiList = $this->db->query("SELECT id, fakultas_id, nama_prodi, kode_prodi, jenjang FROM prodis ORDER BY nama_prodi ASC")->fetchAll();
        $bidangList = $this->db->query("SELECT id, nama_bidang, kode_bidang FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        // Data Ringkasan Kepatuhan per Unit (Prodi & Fakultas) untuk Tab 2
        $stmtProdiSummary = $this->db->query("
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
            GROUP BY p.id
            ORDER BY f.nama_fakultas ASC, p.jenjang ASC, p.nama_prodi ASC
        ");
        $prodiSummaries = $stmtProdiSummary->fetchAll();

        // Fakultas PPEPP summary (dokumen level = 'fakultas')
        $stmtFakPpepp = $this->db->query("
            SELECT f.id, f.kode_fakultas, f.nama_fakultas, f.nama_dekan,
                   COUNT(d.id) as total_dokumen_fakultas,
                   SUM(CASE WHEN d.status_review IN ('belum_direview', 'sudah_diperbaiki') AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as perlu_review_count,
                   SUM(CASE WHEN d.status_review = 'belum_direview' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as belum_direview_count,
                   SUM(CASE WHEN d.status_review = 'sudah_diperbaiki' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sudah_diperbaiki_count,
                   SUM(CASE WHEN d.status_review = 'perlu_perbaikan' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as revisi_count,
                   SUM(CASE WHEN d.status_review = 'sesuai' AND d.deleted_at IS NULL THEN 1 ELSE 0 END) as sesuai_count
            FROM fakultas f
            LEFT JOIN ppepp_documents d ON f.id = d.fakultas_id AND d.level = 'fakultas' AND d.deleted_at IS NULL AND d.status_review != 'draft'
            GROUP BY f.id
            ORDER BY f.nama_fakultas ASC
        ");
        $fakultasSummaries = $stmtFakPpepp->fetchAll();

        $this->render('super_admin/review_hub', [
            'pageTitle' => 'Pusat Review Dokumen Mutu PPEPP',
            'documents' => $documents,
            'reviewStats' => $reviewStats,
            'cycleStats' => $cycleStats,
            'fakultasList' => $fakultasList,
            'prodiList' => $prodiList,
            'bidangList' => $bidangList,
            'prodiSummaries' => $prodiSummaries,
            'fakultasSummaries' => $fakultasSummaries
        ]);
    }

    /**
     * Halaman Review Dokumen Mutu per Program Studi (LPM)
     */
    public function reviewProdi(string $prodiId): void {
        $id = (int)$prodiId;
        $stmtProdi = $this->db->prepare("
            SELECT p.*, f.nama_fakultas, f.kode_fakultas 
            FROM prodis p 
            JOIN fakultas f ON p.fakultas_id = f.id 
            WHERE p.id = ?
        ");
        $stmtProdi->execute([$id]);
        $prodi = $stmtProdi->fetch();

        if (!$prodi) {
            Auth::setFlash('danger', 'Program Studi tidak ditemukan.');
            redirect('admin/dashboard');
        }

        // Ambil semua dokumen aktif prodi ini
        $stmtDocs = $this->db->prepare("
            SELECT d.*, b.nama_bidang, b.kode_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as uploader_name, r.name as reviewer_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            LEFT JOIN users r ON d.reviewed_by = r.id
            WHERE d.prodi_id = ? AND d.deleted_at IS NULL AND d.status_review != 'draft'
            ORDER BY d.created_at DESC
        ");
        $stmtDocs->execute([$id]);
        $documents = $stmtDocs->fetchAll();

        $this->attachFilesToDocs($documents);

        // Hitung statistik review prodi ini
        $reviewStats = [
            'total' => count($documents),
            'belum_direview' => 0,
            'perlu_perbaikan' => 0,
            'sudah_diperbaiki' => 0,
            'sesuai' => 0
        ];

        $cycleStats = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0
        ];

        foreach ($documents as $doc) {
            $st = $doc['status_review'] ?? 'belum_direview';
            if (isset($reviewStats[$st])) {
                $reviewStats[$st]++;
            }
            $cy = $doc['siklus'] ?? '';
            if (isset($cycleStats[$cy])) {
                $cycleStats[$cy]++;
            }
        }

        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        $this->render('super_admin/review_prodi', [
            'pageTitle' => 'Review Dokumen: ' . $prodi['nama_prodi'],
            'prodi' => $prodi,
            'documents' => $documents,
            'reviewStats' => $reviewStats,
            'cycleStats' => $cycleStats,
            'bidangList' => $bidangList
        ]);
    }

    /**
     * Halaman Review Dokumen Mutu per Fakultas (LPM)
     */
    public function reviewFakultas(string $fakultasId): void {
        $id = (int)$fakultasId;
        $stmtFak = $this->db->prepare("SELECT * FROM fakultas WHERE id = ?");
        $stmtFak->execute([$id]);
        $fakultas = $stmtFak->fetch();

        if (!$fakultas) {
            Auth::setFlash('danger', 'Fakultas tidak ditemukan.');
            redirect('admin/dashboard');
            return;
        }

        // Ambil semua dokumen aktif fakultas ini (level = 'fakultas')
        $stmtDocs = $this->db->prepare("
            SELECT d.*, b.nama_bidang, b.kode_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as uploader_name, r.name as reviewer_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            LEFT JOIN users r ON d.reviewed_by = r.id
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.deleted_at IS NULL AND d.status_review != 'draft'
            ORDER BY d.created_at DESC
        ");
        $stmtDocs->execute([$id]);
        $documents = $stmtDocs->fetchAll();

        $this->attachFilesToDocs($documents);

        $reviewStats = [
            'total' => count($documents),
            'belum_direview' => 0,
            'perlu_perbaikan' => 0,
            'sudah_diperbaiki' => 0,
            'sesuai' => 0
        ];

        $cycleStats = [
            'penetapan' => 0,
            'pelaksanaan' => 0,
            'evaluasi' => 0,
            'pengendalian' => 0,
            'peningkatan' => 0
        ];

        foreach ($documents as $doc) {
            $st = $doc['status_review'] ?? 'belum_direview';
            if (isset($reviewStats[$st])) {
                $reviewStats[$st]++;
            }
            $cy = $doc['siklus'] ?? '';
            if (isset($cycleStats[$cy])) {
                $cycleStats[$cy]++;
            }
        }

        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        $this->render('super_admin/review_fakultas', [
            'pageTitle' => 'Review Dokumen PPEPP ' . $fakultas['nama_fakultas'],
            'fakultas' => $fakultas,
            'documents' => $documents,
            'reviewStats' => $reviewStats,
            'cycleStats' => $cycleStats,
            'bidangList' => $bidangList
        ]);
    }

    /**
     * Submit Review Dokumen (Setujui / Minta Revisi dengan Catatan)
     */
    public function submitReview(): void {
        $docId = !empty($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
        $statusReview = trim($_POST['status_review'] ?? '');
        $catatanReview = trim($_POST['catatan_review'] ?? '');
        $customRedirect = trim($_POST['redirect_to'] ?? '');
        $allowedRedirects = ['admin/review', 'admin/dashboard', 'admin/review-dokumen'];
        $fallbackRedirect = ($customRedirect && in_array($customRedirect, $allowedRedirects)) ? $customRedirect : 'admin/dashboard';

        $allowedStatuses = ['sesuai', 'perlu_perbaikan'];
        if (!$docId || !in_array($statusReview, $allowedStatuses)) {
            Auth::setFlash('danger', 'Data verifikasi dokumen tidak valid.');
            redirect($fallbackRedirect);
            return;
        }

        // Ambil dokumen
        $stmt = $this->db->prepare("
            SELECT d.*, p.nama_prodi, f.nama_fakultas 
            FROM ppepp_documents d 
            LEFT JOIN prodis p ON d.prodi_id = p.id 
            LEFT JOIN fakultas f ON (f.id = COALESCE(d.fakultas_id, p.fakultas_id))
            WHERE d.id = ? AND d.deleted_at IS NULL
        ");
        $stmt->execute([$docId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan.');
            redirect($fallbackRedirect);
            return;
        }

        $isFakultasDoc = ($doc['level'] === 'fakultas' || empty($doc['prodi_id']));
        $redirectUrl = $isFakultasDoc ? "admin/review-fakultas/{$doc['fakultas_id']}" : "admin/review-prodi/{$doc['prodi_id']}";

        if ($customRedirect && in_array($customRedirect, $allowedRedirects)) {
            $redirectUrl = $customRedirect;
        }

        if ($statusReview === 'perlu_perbaikan' && empty($catatanReview)) {
            Auth::setFlash('danger', 'Catatan / komentar perbaikan wajib diisi jika meminta revisi dokumen.');
            redirect($redirectUrl);
            return;
        }

        // Review formal diterbitkan atas nama Pusat Penjaminan Mutu LPM
        // Verifikator dicatat sesuai akun yang sedang login (Kepala LPM atau Admin LPM)
        $reviewerId = Auth::id();
        $officialReviewerName = 'Pusat Penjaminan Mutu LPM';

        // Update status review (disahkan atas nama Pusat Penjaminan Mutu LPM)
        $stmtUpdate = $this->db->prepare("
            UPDATE ppepp_documents 
            SET status_review = ?, catatan_review = ?, reviewed_by = ?, reviewed_at = NOW()
            WHERE id = ?
        ");
        $stmtUpdate->execute([
            $statusReview,
            $statusReview === 'sesuai' ? ($catatanReview ?: 'Disetujui dan sesuai standar mutu.') : $catatanReview,
            $reviewerId,
            $docId
        ]);

        $statusLabel = ($statusReview === 'sesuai') ? 'Sesuai Standar Mutu' : 'Perlu Perbaikan';
        $unitName = $isFakultasDoc ? ($doc['nama_fakultas'] ?? 'Fakultas') : ($doc['nama_prodi'] ?? 'Program Studi');
        AuditLogger::log('UPDATE', 'Review Dokumen PPEPP', (string)$docId, $doc['nama_dokumen'], $doc, [
            'status_review' => $statusReview,
            'catatan_review' => $catatanReview,
            'pengesahan' => 'Atas Nama Pusat Penjaminan Mutu LPM',
            'reviewer' => $officialReviewerName,
            'verifikator' => Auth::user()['name'] ?? 'Admin LPM'
        ], (int)($doc['prodi_id'] ?? 0), $unitName);

        Auth::setFlash('success', "Review dokumen '{$doc['nama_dokumen']}' berhasil disimpan atas nama Pusat Penjaminan Mutu LPM (Status: {$statusLabel}).");
        redirect($redirectUrl);
    }

    /**
     * Halaman Kelola Konten Beranda Publik & Footer
     */
    public function landingSettings(): void {
        $stmt = $this->db->query("SELECT setting_key, setting_value, setting_group FROM landing_settings");
        $allRows = $stmt->fetchAll();
        
        $settings = [];
        foreach ($allRows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }

        $totalFakultas = (int)$this->db->query("SELECT COUNT(*) FROM fakultas")->fetchColumn();
        $totalProdi    = (int)$this->db->query("SELECT COUNT(*) FROM prodis")->fetchColumn();
        $totalDokumen  = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE deleted_at IS NULL AND file_path IS NOT NULL AND file_path != ''")->fetchColumn();
        $lastUpdated   = $this->db->query("SELECT MAX(updated_at) FROM landing_settings")->fetchColumn();

        $this->render('super_admin/landing_settings', [
            'pageTitle'     => 'Kelola Beranda & Footer Publik',
            'activeNav'     => 'landing_settings',
            'settings'      => $settings,
            'totalFakultas' => $totalFakultas,
            'totalProdi'    => $totalProdi,
            'totalDokumen'  => $totalDokumen,
            'lastUpdated'   => $lastUpdated
        ]);
    }

    /**
     * Simpan Pengaturan Konten Beranda Publik & Footer
     */
    public function saveLandingSettings(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/landing-settings');
            return;
        }

        $posted = $_POST['settings'] ?? [];
        if (!is_array($posted)) {
            Auth::setFlash('danger', 'Format data tidak valid.');
            redirect('admin/landing-settings');
            return;
        }

        // Ambil data lama untuk audit log
        $stmtOld = $this->db->query("SELECT setting_key, setting_value FROM landing_settings");
        $oldSettings = $stmtOld->fetchAll(PDO::FETCH_KEY_PAIR);

        $stmtUpsert = $this->db->prepare("
            INSERT INTO landing_settings (setting_key, setting_value, updated_at)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()
        ");

        $updatedKeys = [];
        foreach ($posted as $key => $val) {
            $key = trim($key);
            if ($key === '') continue;
            $val = trim($val);
            $stmtUpsert->execute([$key, $val]);
            $updatedKeys[] = $key;
        }

        AuditLogger::log(
            'UPDATE',
            'Kelola Beranda Publik',
            'landing_settings',
            'Konten Beranda & Footer Publik (' . count($updatedKeys) . ' item)',
            $oldSettings,
            $posted
        );

        Auth::setFlash('success', 'Konten beranda publik dan footer berhasil diperbarui secara langsung!');
        redirect('admin/landing-settings');
    }

    /**
     * Reset Pengaturan Beranda Publik ke Standar Pabrik
     */
    public function resetLandingSettings(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/landing-settings');
            return;
        }

        $defaultSettings = [
            'hero_badge' => ['Lembaga Penjaminan Mutu • UNIKA Soegijapranata', 'hero'],
            'hero_title' => ['Sistem Penjaminan Mutu Internal', 'hero'],
            'hero_highlight' => ['Siklus PPEPP Berkelanjutan', 'hero'],
            'hero_desc' => ['Portal terpadu pengawasan dan repositori dokumen Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan mutu tridharma di seluruh program studi Universitas Katolik Soegijapranata.', 'hero'],
            'hero_btn1_text' => ['Jelajahi Direktori Fakultas', 'hero'],
            'hero_btn1_url' => ['#direktoriFakultas', 'hero'],
            'hero_btn2_text' => ['Repositori Dokumen Mutu', 'hero'],
            'hero_btn2_url' => ['dokumen', 'hero'],

            'stats_fakultas_label' => ['Fakultas & Pascasarjana', 'stats'],
            'stats_prodi_label' => ['Program Studi Aktif', 'stats'],
            'stats_dokumen_label' => ['Dokumen Mutu Terverifikasi', 'stats'],
            'stats_siklus_label' => ['Siklus PPEPP Terintegrasi', 'stats'],

            'siklus_section_tag' => ['ALUR PENJAMINAN MUTU', 'siklus'],
            'siklus_section_title' => ['5 Siklus Penjaminan Mutu Internal (PPEPP)', 'siklus'],
            'siklus_section_desc' => ['Implementasi siklus berkelanjutan (Continuous Quality Improvement) sesuai pedoman Permendikbudristek No. 53 Tahun 2023 untuk mencapai akreditasi unggul institusi.', 'siklus'],
            'siklus_p1_title' => ['Penetapan', 'siklus'],
            'siklus_p1_desc' => ['Perumusan standar, manual mutu, kebijakan SPMI, dan Capaian Pembelajaran Lulusan (CPL).', 'siklus'],
            'siklus_p2_title' => ['Pelaksanaan', 'siklus'],
            'siklus_p2_desc' => ['Implementasi kurikulum, RPS, SOP pembelajaran, penelitian, dan pengabdian masyarakat prodi.', 'siklus'],
            'siklus_e_title' => ['Evaluasi', 'siklus'],
            'siklus_e_desc' => ['Audit Mutu Internal (AMI), monitoring berkala, monev pembelajaran, dan pengukuran kepuasan pengguna.', 'siklus'],
            'siklus_p3_title' => ['Pengendalian', 'siklus'],
            'siklus_p3_desc' => ['Tindakan koreksi terhadap deviasi standar, evaluasi akar masalah, dan Rapat Tinjauan Manajemen (RTM).', 'siklus'],
            'siklus_p4_title' => ['Peningkatan', 'siklus'],
            'siklus_p4_desc' => ['Pembaruan dan peningkatan standar mutu secara berkelanjutan (Kaizen) melampaui SN-Dikti.', 'siklus'],

            'direktori_section_tag' => ['DIREKTORI AKADEMIK', 'direktori'],
            'direktori_section_title' => ['Fakultas & Program Studi', 'direktori'],
            'direktori_section_desc' => ['Pilih fakultas di bawah untuk meninjau profil kepemimpinan, sebaran dokumen mutu 5 siklus PPEPP, dan status kepatuhan standar mutu masing-masing prodi.', 'direktori'],

            'footer_brand_title' => ['MITRA', 'footer'],
            'footer_brand_sub' => ['Monitoring dan Implementasi Tahapan PPEPP & Rencana Aksi', 'footer'],
            'footer_desc' => ['MITRA = Monitoring dan Implementasi Tahapan PPEPP & Rencana Aksi. MITRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.', 'footer'],
            'footer_address' => ['Ruang Lembaga Penjaminan Mutu, Gedung Thomas Aquinas Lantai 5, Kampus Universitas Katolik Soegijapranata, Jalan Pawiyatan Luhur IV/1 Bendan Duwur, Semarang 50234', 'footer'],
            'footer_akreditasi' => ['Terakreditasi UNGGUL • BAN-PT', 'footer'],
            'footer_siklus_heading' => ['5 Siklus PPEPP PETRA', 'footer'],
            'footer_contact_title' => ['Layanan Bantuan LPM', 'footer'],
            'footer_email' => ['lpm@unika.ac.id', 'footer'],
            'footer_phone' => ['024-8441555 Ext 1473', 'footer'],
            'footer_website_url' => ['https://www.unika.ac.id', 'footer'],
            'footer_website_text' => ['Website Utama SCU', 'footer'],
            'footer_website_lpm_url' => ['https://lpm.unika.ac.id', 'footer'],
            'footer_website_lpm_text' => ['Website Resmi LPM', 'footer'],
            'footer_website_custom_url' => ['', 'footer'],
            'footer_website_custom_text' => ['', 'footer'],
            'footer_copyright_text' => ['Universitas Katolik Soegijapranata', 'footer'],
        ];

        $stmtReset = $this->db->prepare("
            INSERT INTO landing_settings (setting_key, setting_value, setting_group, updated_at)
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_group = VALUES(setting_group), updated_at = NOW()
        ");

        foreach ($defaultSettings as $k => [$v, $grp]) {
            $stmtReset->execute([$k, $v, $grp]);
        }

        AuditLogger::log(
            'RESET',
            'Kelola Beranda Publik',
            'landing_settings',
            'Reset Seluruh Konten Beranda ke Standar Default'
        );

        Auth::setFlash('success', 'Konten beranda publik berhasil dikembalikan ke standar awal.');
        redirect('admin/landing-settings');
    }
}
