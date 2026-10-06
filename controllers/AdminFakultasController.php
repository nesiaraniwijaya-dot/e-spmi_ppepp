<?php
/**
 * Admin Fakultas Controller
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/AuditLogger.php';

class AdminFakultasController extends Controller {

    private int $fakultasId;
    private array $fakultas;

    public function __construct() {
        parent::__construct();
        Auth::requireRole(['dekan', 'wadek']);

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
    }

    /**
     * Dashboard Admin Fakultas
     */
    public function dashboard(): void {
        // Statistik Dokumen PPEPP Tingkat Fakultas
        $cycleStats = [
            'penetapan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND siklus='penetapan' AND deleted_at IS NULL")->fetchColumn(),
            'pelaksanaan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND siklus='pelaksanaan' AND deleted_at IS NULL")->fetchColumn(),
            'evaluasi' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND siklus='evaluasi' AND deleted_at IS NULL")->fetchColumn(),
            'pengendalian' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND siklus='pengendalian' AND deleted_at IS NULL")->fetchColumn(),
            'peningkatan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND siklus='peningkatan' AND deleted_at IS NULL")->fetchColumn(),
            'total' => 0,
            'arsip' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND deleted_at IS NOT NULL")->fetchColumn()
        ];
        $cycleStats['total'] = $cycleStats['penetapan'] + $cycleStats['pelaksanaan'] + $cycleStats['evaluasi'] + $cycleStats['pengendalian'] + $cycleStats['peningkatan'];

        // Dokumen dalam proses review/revisi dengan LPM
        $stmtRevisi = $this->db->prepare("
            SELECT d.*, b.nama_bidang, u.name as reviewer_name 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN users u ON d.reviewed_by = u.id
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL 
            ORDER BY FIELD(d.status_review, 'perlu_perbaikan', 'sudah_diperbaiki'), d.updated_at DESC
        ");
        $stmtRevisi->execute([$this->fakultasId]);
        $revisiDocs = $stmtRevisi->fetchAll();

        // Dokumen terbaru fakultas (hingga 8 dokumen)
        $stmtRecent = $this->db->prepare("
            SELECT d.*, b.nama_bidang 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.deleted_at IS NULL 
            ORDER BY d.created_at DESC LIMIT 8
        ");
        $stmtRecent->execute([$this->fakultasId]);
        $recentDocs = $stmtRecent->fetchAll();
        $this->attachFilesToDocs($recentDocs);

        // Ringkasan program studi di bawah fakultas ini
        $stmtProdis = $this->db->prepare("
            SELECT p.*, COUNT(DISTINCT d.id) as total_doc 
            FROM prodis p 
            LEFT JOIN ppepp_documents d ON p.id = d.prodi_id AND d.deleted_at IS NULL AND d.status_review != 'draft' 
            WHERE p.fakultas_id = ? 
            GROUP BY p.id 
            ORDER BY p.nama_prodi ASC
        ");
        $stmtProdis->execute([$this->fakultasId]);
        $prodiSummary = $stmtProdis->fetchAll();

        $this->render('admin_fakultas/dashboard', [
            'pageTitle' => 'Dashboard ' . $this->fakultas['nama_fakultas'],
            'fakultas' => $this->fakultas,
            'cycleStats' => $cycleStats,
            'recentDocs' => $recentDocs,
            'revisiDocs' => $revisiDocs,
            'prodiSummary' => $prodiSummary
        ]);
    }

    /**
     * Daftar Dokumen PPEPP Tingkat Fakultas
     */
    public function documents(): void {
        $siklus = trim($_GET['siklus'] ?? '');
        $bidangId = !empty($_GET['bidang_id']) ? (int)$_GET['bidang_id'] : null;
        $search = trim($_GET['q'] ?? '');

        $sql = "
            SELECT d.*, b.nama_bidang, b.kode_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang,
                   u.name as creator_name, rev.name as reviewer_name
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id 
            LEFT JOIN users rev ON d.reviewed_by = rev.id
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.deleted_at IS NULL AND d.status_review != 'draft'
        ";
        $params = [$this->fakultasId];

        if (!empty($siklus) && in_array($siklus, ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
            $sql .= " AND d.siklus = ?";
            $params[] = $siklus;
        }

        if (!empty($bidangId)) {
            $sql .= " AND d.bidang_id = ?";
            $params[] = $bidangId;
        }

        if (!empty($search)) {
            $sql .= " AND (d.nama_dokumen LIKE ? OR d.nomor_dokumen LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY d.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $documents = $stmt->fetchAll();
        $this->attachFilesToDocs($documents);

        // Fetch all bidang for filter
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active=1 ORDER BY id ASC")->fetchAll();

        // Per-cycle counts (excluding draft)
        $cycleCounts = [
            'semua' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND status_review != 'draft' AND deleted_at IS NULL")->fetchColumn(),
            'penetapan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND status_review != 'draft' AND siklus='penetapan' AND deleted_at IS NULL")->fetchColumn(),
            'pelaksanaan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND status_review != 'draft' AND siklus='pelaksanaan' AND deleted_at IS NULL")->fetchColumn(),
            'evaluasi' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND status_review != 'draft' AND siklus='evaluasi' AND deleted_at IS NULL")->fetchColumn(),
            'pengendalian' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND status_review != 'draft' AND siklus='pengendalian' AND deleted_at IS NULL")->fetchColumn(),
            'peningkatan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id={$this->fakultasId} AND level='fakultas' AND status_review != 'draft' AND siklus='peningkatan' AND deleted_at IS NULL")->fetchColumn(),
        ];

        // Total dokumen perlu perbaikan & daftar dokumen perlu perbaikan
        $stmtPerlu = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang,
                   rev.name as reviewer_name, rev.role as reviewer_role
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users rev ON d.reviewed_by = rev.id
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.status_review = 'perlu_perbaikan' AND d.deleted_at IS NULL
            ORDER BY d.reviewed_at DESC, d.updated_at DESC
        ");
        $stmtPerlu->execute([$this->fakultasId]);
        $perluPerbaikanDocs = $stmtPerlu->fetchAll();
        $this->attachFilesToDocs($perluPerbaikanDocs);
        $perluPerbaikanCount = count($perluPerbaikanDocs);

        $sudahDiperbaikiCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = {$this->fakultasId} AND level = 'fakultas' AND status_review = 'sudah_diperbaiki' AND deleted_at IS NULL")->fetchColumn();

        $draftCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE fakultas_id = {$this->fakultasId} AND level = 'fakultas' AND status_review = 'draft' AND deleted_at IS NULL")->fetchColumn();

        $this->render('admin_fakultas/documents', [
            'pageTitle' => 'Dokumen PPEPP ' . $this->fakultas['nama_fakultas'],
            'fakultas' => $this->fakultas,
            'documents' => $documents,
            'bidangList' => $bidangList,
            'cycleCounts' => $cycleCounts,
            'perluPerbaikanDocs' => $perluPerbaikanDocs,
            'perluPerbaikanCount' => $perluPerbaikanCount,
            'sudahDiperbaikiCount' => $sudahDiperbaikiCount,
            'draftCount' => $draftCount,
            'activeSiklus' => $siklus,
            'activeBidang' => $bidangId,
            'search' => $search
        ]);
    }

    /**
     * Halaman Khusus Perbaikan Dokumen Mutu Fakultas (Inbox Revisi dari LPM)
     */
    public function perbaikan(): void {
        $stmt = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang,
                   rev.name as reviewer_name, rev.role as reviewer_role
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users rev ON d.reviewed_by = rev.id
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.status_review IN ('perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL
            ORDER BY FIELD(d.status_review, 'perlu_perbaikan', 'sudah_diperbaiki'), d.updated_at DESC
        ");
        $stmt->execute([$this->fakultasId]);
        $documents = $stmt->fetchAll();
        $this->attachFilesToDocs($documents);

        $perluDocs = array_values(array_filter($documents, fn($d) => ($d['status_review'] ?? '') === 'perlu_perbaikan'));
        $sudahDocs = array_values(array_filter($documents, fn($d) => ($d['status_review'] ?? '') === 'sudah_diperbaiki'));

        $this->render('admin_fakultas/perbaikan', [
            'pageTitle' => 'Perbaikan Dokumen Mutu - ' . $this->fakultas['nama_fakultas'],
            'fakultas' => $this->fakultas,
            'documents' => $documents,
            'perluDocs' => $perluDocs,
            'sudahDocs' => $sudahDocs,
            'perluCount' => count($perluDocs),
            'sudahCount' => count($sudahDocs)
        ]);
    }

    /**
     * Halaman Draf Dokumen Mutu Fakultas (Belum Diajukan ke LPM)
     */
    public function draft(): void {
        $stmt = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as creator_name
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.status_review = 'draft' AND d.deleted_at IS NULL
            ORDER BY d.updated_at DESC
        ");
        $stmt->execute([$this->fakultasId]);
        $documents = $stmt->fetchAll();
        $this->attachFilesToDocs($documents);

        $this->render('admin_fakultas/draft', [
            'pageTitle' => 'Draf Dokumen Mutu - ' . $this->fakultas['nama_fakultas'],
            'fakultas' => $this->fakultas,
            'documents' => $documents,
            'totalDraft' => count($documents)
        ]);
    }

    /**
     * Ajukan Dokumen Draf Fakultas ke LPM untuk Diverifikasi
     */
    public function ajukanDraft(string $id): void {
        $docId = (int)$id;
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND fakultas_id = ? AND level = 'fakultas' AND status_review = 'draft' AND deleted_at IS NULL");
        $stmt->execute([$docId, $this->fakultasId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            Auth::setFlash('danger', 'Draf dokumen tidak ditemukan atau sudah diajukan.');
            redirect('fakultas/draft');
            return;
        }

        $filesCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_document_files WHERE document_id = {$docId}")->fetchColumn();
        $hasLink = !empty($doc['external_link']);
        $hasLegacyFile = !empty($doc['file_path']);

        if ($filesCount === 0 && !$hasLink && !$hasLegacyFile) {
            Auth::setFlash('warning', "Dokumen '{$doc['nama_dokumen']}' belum memiliki berkas terlampir atau tautan Google Drive. Harap lengkapi berkas terlebih dahulu sebelum mengajukan ke LPM.");
            redirect("fakultas/dokumen/edit/{$docId}");
            return;
        }

        $stmtUpd = $this->db->prepare("UPDATE ppepp_documents SET status_review = 'belum_direview', updated_at = NOW() WHERE id = ?");
        $stmtUpd->execute([$docId]);

        AuditLogger::log('UPDATE', 'Dokumen PPEPP Fakultas', (string)$docId, $doc['nama_dokumen'], [
            'status_review' => 'draft'
        ], [
            'status_review' => 'belum_direview',
            'action' => 'publish_draft'
        ]);

        Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' berhasil diajukan ke Lembaga Penjaminan Mutu (LPM) untuk diverifikasi.");
        redirect('fakultas/dokumen');
    }

    /**
     * Form Unggah Dokumen Mutu Baru (Batch Mode up to 10)
     */
    public function createDocument(): void {
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active=1 ORDER BY id ASC")->fetchAll();
        $subBidangList = $this->db->query("SELECT * FROM sub_bidang_standar WHERE is_active=1 ORDER BY bidang_id ASC, id ASC")->fetchAll();

        $this->render('admin_fakultas/document_form', [
            'pageTitle' => 'Unggah Dokumen Mutu Fakultas',
            'fakultas' => $this->fakultas,
            'bidangList' => $bidangList,
            'subBidangList' => $subBidangList,
            'doc' => null,
            'docFiles' => [],
            'isEdit' => false
        ]);
    }

    /**
     * Form Ubah Dokumen Mutu Tingkat Fakultas
     */
    public function editDocument(string $id): void {
        $stmt = $this->db->prepare("
            SELECT d.*, u.name as reviewer_name, u.role as reviewer_role 
            FROM ppepp_documents d 
            LEFT JOIN users u ON d.reviewed_by = u.id 
            WHERE d.id = ? AND d.fakultas_id = ? AND d.level = 'fakultas' AND d.deleted_at IS NULL
        ");
        $stmt->execute([(int)$id, $this->fakultasId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan atau bukan milik fakultas Anda.');
            redirect('fakultas/dokumen');
            return;
        }

        $stmtFiles = $this->db->prepare("SELECT * FROM ppepp_document_files WHERE document_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtFiles->execute([$doc['id']]);
        $docFiles = $stmtFiles->fetchAll();

        if (empty($docFiles) && !empty($doc['file_path'])) {
            $docFiles = [
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

        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active=1 ORDER BY id ASC")->fetchAll();
        $subBidangList = $this->db->query("SELECT * FROM sub_bidang_standar WHERE is_active=1 ORDER BY bidang_id ASC, id ASC")->fetchAll();

        $this->render('admin_fakultas/document_form', [
            'pageTitle' => 'Ubah Dokumen Mutu: ' . $doc['nama_dokumen'],
            'fakultas' => $this->fakultas,
            'bidangList' => $bidangList,
            'subBidangList' => $subBidangList,
            'doc' => $doc,
            'docFiles' => $docFiles,
            'isEdit' => true
        ]);
    }

    /**
     * Simpan Dokumen (Batch Create atau Single Edit)
     */
    public function saveDocument(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('fakultas/dokumen');
            return;
        }

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $isEdit = !empty($id);
        $isDraft = (isset($_POST['action_submit']) && $_POST['action_submit'] === 'draft');
        $allowedExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

        // =======================================================
        // 1. BATCH MULTI-UPLOAD TINGKAT FAKULTAS (CREATE UP TO 10)
        // =======================================================
        if (!$isEdit && !empty($_POST['docs']) && is_array($_POST['docs'])) {
            $docsData = $_POST['docs'];
            $successCount = 0;
            $createdDocNames = [];

            if (count($docsData) > 10) {
                Auth::setFlash('danger', 'Maksimal dokumen yang dapat diunggah sekaligus adalah 10 dokumen.');
                redirect('fakultas/dokumen/create');
                return;
            }

            $this->db->beginTransaction();
            try {
                foreach ($docsData as $cardIdx => $d) {
                    $docNumber = is_numeric($cardIdx) ? ($cardIdx + 1) : 1;
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
                    $batchLinkSubs = $d['external_link_sub_bidang'] ?? [];
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
                                $lSubs = !empty($batchLinkSubs[$lIdx]) && is_array($batchLinkSubs[$lIdx])
                                    ? array_values(array_filter(array_map('intval', $batchLinkSubs[$lIdx])))
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
                                    'sub_bidang_ids' => $lSubs
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
                                'sub_bidang_ids' => $subBidangId ? [(int)$subBidangId] : []
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
                                    $fileName = 'FAK_' . $this->fakultasId . '_' . time() . '_' . uniqid() . '.' . $ext;
                                    $targetFile = DOC_UPLOAD_PATH . '/' . $fileName;
                                    if (move_uploaded_file($tmpName, $targetFile)) {
                                        $uploadedFilesList[] = [
                                            'file_name' => $origName,
                                            'file_path' => 'uploads/documents/' . $fileName,
                                            'file_size' => round($size / (1024 * 1024), 2) . ' MB',
                                            'file_extension' => $ext,
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
                    if (empty($bidangId) && !empty($subBidangId)) {
                        $stmtB = $this->db->prepare("SELECT bidang_id FROM sub_bidang_standar WHERE id = ?");
                        $stmtB->execute([$subBidangId]);
                        $bidangId = (int)$stmtB->fetchColumn();
                    }

                    // Validasi isian wajib (judul, tahun, siklus tetap wajib)
                    if (empty($namaDokumen)) throw new \Exception("Nama dokumen pada Dokumen #{$docNumber} wajib diisi.");
                    if (empty($tahunAkademik)) throw new \Exception("Tahun akademik pada Dokumen #{$docNumber} wajib diisi.");
                    if (empty($siklus)) throw new \Exception("Kategori Siklus PPEPP pada Dokumen #{$docNumber} wajib dipilih.");
                    if (empty($bidangId)) throw new \Exception("Bidang standar mutu pada Dokumen #{$docNumber} wajib dipilih.");

                    $filePath = null;
                    $fileSize = null;
                    $fileExt = null;

                    if (!$isDraft) {
                        if ($jenisUpload === 'file') {
                            if (empty($uploadedFilesList)) {
                                throw new \Exception("Dokumen #{$docNumber} ({$namaDokumen}) wajib memiliki minimal 1 berkas file dokumen untuk diajukan ke LPM.");
                            }
                            $filePath = $uploadedFilesList[0]['file_path'];
                            $fileSize = $uploadedFilesList[0]['file_size'];
                            $fileExt = $uploadedFilesList[0]['file_extension'];
                        } else {
                            if (empty($rawLinks)) {
                                throw new \Exception("Dokumen #{$docNumber} ({$namaDokumen}) berjenis Link GDrive, minimal 1 URL Google Drive wajib diisi untuk diajukan ke LPM.");
                            }
                        }
                    } else {
                        if ($jenisUpload === 'file' && !empty($uploadedFilesList)) {
                            $filePath = $uploadedFilesList[0]['file_path'];
                            $fileSize = $uploadedFilesList[0]['file_size'];
                            $fileExt = $uploadedFilesList[0]['file_extension'];
                        }
                    }

                    $statusReview = $isDraft ? 'draft' : 'belum_direview';

                    // Insert ke tabel `ppepp_documents` dengan level = 'fakultas'
                    $stmt = $this->db->prepare("
                        INSERT INTO ppepp_documents (
                            fakultas_id, prodi_id, user_id, bidang_id, sub_bidang_id,
                            nama_dokumen, nomor_dokumen, siklus, tahun_akademik,
                            tanggal_berlaku_mulai, tanggal_berlaku_selesai,
                            jenis_upload, file_path, file_size, file_extension, external_link,
                            public_page_limit, can_download_public, status_review, level, created_at
                        ) VALUES (
                            ?, NULL, ?, ?, ?,
                            ?, ?, ?, ?,
                            ?, ?,
                            ?, ?, ?, ?, ?,
                            ?, ?, ?, 'fakultas', NOW()
                        )
                    ");
                    $stmt->execute([
                        $this->fakultasId, Auth::id(), $bidangId, $subBidangId,
                        $namaDokumen, $nomorDokumen, $siklus, $tahunAkademik,
                        $startDate, $endDate,
                        $jenisUpload, $filePath, $fileSize, $fileExt, $externalLink,
                        $publicPageLimit, $canDownloadPublic, $statusReview
                    ]);
                    $docId = (int)$this->db->lastInsertId();

                    // Sinkronisasi sub bidang dokumen induk
                    sync_document_sub_bidang($this->db, $docId, $allDocSubIds);

                    // Simpan lampiran berkas ke tabel `ppepp_document_files`
                    if (!empty($uploadedFilesList)) {
                        $stmtFileInsert = $this->db->prepare("
                            INSERT INTO ppepp_document_files (document_id, file_name, file_path, file_size, file_extension, narasi, sub_bidang_ids, sort_order, is_page_limited, public_page_limit, can_download_public, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                        ");
                        $fSort = 1;
                        $batchFileIsLim = $d['file_is_limited'] ?? [];
                        $batchFilePLim = $d['file_page_limit'] ?? [];
                        $batchFileCanDl = $d['file_can_download'] ?? [];

                        foreach ($uploadedFilesList as $uIdx => $fItem) {
                            $fIsLim = isset($batchFileIsLim[$uIdx]) ? (!empty($batchFileIsLim[$uIdx]) ? 1 : 0) : ($isPageLimited ? 1 : 0);
                            $fPLim = isset($batchFilePLim[$uIdx]) ? max(1, (int)$batchFilePLim[$uIdx]) : ($isPageLimited ? $publicPageLimit : 1);
                            $fCanDl = isset($batchFileCanDl[$uIdx]) ? (!empty($batchFileCanDl[$uIdx]) ? 1 : 0) : $canDownloadPublic;
                            $fSubsJson = json_encode($fItem['sub_bidang_ids']);

                            $stmtFileInsert->execute([
                                $docId, $fItem['file_name'], $fItem['file_path'], $fItem['file_size'], $fItem['file_extension'], $fItem['narasi'], $fSubsJson, $fSort++,
                                $fIsLim, $fPLim, $fCanDl
                            ]);
                            $newFileId = (int)$this->db->lastInsertId();
                            sync_file_sub_bidang($this->db, $newFileId, $fItem['sub_bidang_ids']);
                        }
                    }

                    // Catat Audit Log
                    AuditLogger::log(
                        aksi: 'CREATE',
                        modul: 'Dokumen PPEPP Fakultas',
                        targetId: (string)$docId,
                        targetName: $namaDokumen,
                        newValues: [
                            'fakultas_id' => $this->fakultasId,
                            'level' => 'fakultas',
                            'siklus' => $siklus,
                            'tahun' => $tahunAkademik,
                            'status_review' => $statusReview,
                            'jenis' => $jenisUpload
                        ]
                    );

                    $successCount++;
                    $createdDocNames[] = $namaDokumen;
                }

                $this->db->commit();

                if ($isDraft) {
                    $msg = ($successCount > 1)
                        ? "Berhasil menyimpan {$successCount} dokumen fakultas sebagai draf."
                        : "Dokumen fakultas '{$createdDocNames[0]}' berhasil disimpan sebagai draf.";
                    Auth::setFlash('success', $msg);
                    redirect('fakultas/draft');
                    return;
                } else {
                    Auth::setFlash('success', "Berhasil mengunggah {$successCount} dokumen mutu tingkat fakultas sekaligus dan diajukan ke LPM!");
                    redirect('fakultas/dokumen');
                    return;
                }

            } catch (\Exception $e) {
                $this->db->rollBack();
                Auth::setFlash('danger', 'Gagal mengunggah dokumen: ' . $e->getMessage());
                redirect('fakultas/dokumen/create');
                return;
            }
        }

        // =======================================================
        // 2. SINGLE EDIT MODE TINGKAT FAKULTAS
        // =======================================================
        if ($isEdit) {
            $stmtCheck = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND fakultas_id = ? AND level = 'fakultas' AND deleted_at IS NULL");
            $stmtCheck->execute([$id, $this->fakultasId]);
            $existing = $stmtCheck->fetch();

            if (!$existing) {
                Auth::setFlash('danger', 'Dokumen tidak ditemukan atau bukan milik fakultas Anda.');
                redirect('fakultas/dokumen');
                return;
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
            $linkSubBidangs = $_POST['external_link_sub_bidang'] ?? [];
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
                            redirect('fakultas/dokumen/edit/' . $id);
                            return;
                        }
                        $lSubs = !empty($linkSubBidangs[$lIdx]) && is_array($linkSubBidangs[$lIdx])
                            ? array_values(array_filter(array_map('intval', $linkSubBidangs[$lIdx])))
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
                            'sub_bidang_ids' => $lSubs
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

            // Himpun seluruh sub standar dari link, berkas eksisting, dan berkas baru
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
                redirect('fakultas/dokumen/edit/' . $id);
                return;
            }

            // Hapus file terpilih jika ada instruksi
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

            // Update narasi & sub-standar file yang sudah ada
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
                            $fileName = 'FAK_' . $this->fakultasId . '_' . time() . '_' . uniqid() . '.' . $ext;
                            $targetFile = DOC_UPLOAD_PATH . '/' . $fileName;
                            if (move_uploaded_file($tmpName, $targetFile)) {
                                $stmtNewFile->execute([
                                    $id, $origName, 'uploads/documents/' . $fileName,
                                    round($size / (1024 * 1024), 2) . ' MB', $ext, $fNarasi, $fSubsJson, $nextSort++,
                                    $fIsLimVal, $fPLimVal, $fCanDlVal
                                ]);
                                $newFid = (int)$this->db->lastInsertId();
                                sync_file_sub_bidang($this->db, $newFid, $fSubArray);
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
                    $fileSize = $existing['file_size'];
                    $fileExt = $existing['file_extension'];
                } else {
                    $filePath = null;
                    $fileSize = null;
                    $fileExt = null;
                }
            }

            if (!$isDraft) {
                if ($jenisUpload === 'file') {
                    if (empty($filePath)) {
                        Auth::setFlash('danger', 'Dokumen aktif wajib memiliki minimal 1 berkas fisik yang diunggah.');
                        redirect('fakultas/dokumen/edit/' . $id);
                        return;
                    }
                    $externalLink = null;
                } else {
                    if (empty($externalLink)) {
                        Auth::setFlash('danger', 'Minimal 1 tautan Google Drive wajib diisi jika memilih metode Link.');
                        redirect('fakultas/dokumen/edit/' . $id);
                        return;
                    }
                }
            } else {
                if ($jenisUpload === 'file') {
                    $externalLink = null;
                }
            }

            // Status review handling
            $newStatusReview = $existing['status_review'] ?? 'belum_direview';
            if ($isDraft) {
                $newStatusReview = 'draft';
            } else {
                if ($newStatusReview === 'perlu_perbaikan') {
                    $newStatusReview = 'sudah_diperbaiki';
                } elseif ($newStatusReview === 'draft') {
                    $newStatusReview = 'belum_direview';
                }
            }

            $stmtUpdate = $this->db->prepare("
                UPDATE ppepp_documents SET 
                    nama_dokumen = ?, nomor_dokumen = ?, bidang_id = ?, sub_bidang_id = ?,
                    siklus = ?, tahun_akademik = ?, tanggal_berlaku_mulai = ?, tanggal_berlaku_selesai = ?,
                    jenis_upload = ?, file_path = ?, file_size = ?, file_extension = ?, external_link = ?,
                    public_page_limit = ?, can_download_public = ?, status_review = ?, updated_at = NOW()
                WHERE id = ? AND fakultas_id = ? AND level = 'fakultas'
            ");
            $stmtUpdate->execute([
                $namaDokumen, $nomorDokumen, $bidangId, $subBidangId,
                $siklus, $tahunAkademik, $startDate, $endDate,
                $jenisUpload, $filePath, $fileSize, $fileExt, $externalLink,
                $publicPageLimit, $canDownloadPublic, $newStatusReview,
                $id, $this->fakultasId
            ]);

            // Sinkronisasi seluruh sub bidang ke dokumen induk
            sync_document_sub_bidang($this->db, (int)$id, $allDocSubIds);

            AuditLogger::log(
                aksi: 'UPDATE',
                modul: 'Dokumen PPEPP Fakultas',
                targetId: (string)$id,
                targetName: $namaDokumen,
                oldValues: ['nama' => $existing['nama_dokumen'], 'siklus' => $existing['siklus']],
                newValues: ['nama' => $namaDokumen, 'siklus' => $siklus, 'status_review' => $newStatusReview]
            );

            if ($isDraft) {
                Auth::setFlash('success', "Perubahan draf dokumen fakultas '{$namaDokumen}' berhasil disimpan.");
                redirect('fakultas/draft');
                return;
            }

            $msg = ($newStatusReview === 'sudah_diperbaiki')
                ? "Dokumen '{$namaDokumen}' berhasil diperbarui dan statusnya kini 'Menunggu Review Ulang' oleh Admin LPM."
                : (($existing['status_review'] === 'draft')
                    ? "Dokumen '{$namaDokumen}' berhasil diajukan ke LPM untuk diverifikasi."
                    : "Dokumen mutu fakultas berhasil diperbarui.");

            Auth::setFlash('success', $msg);
            redirect('fakultas/dokumen');
        }
    }

    /**
     * Soft Delete Dokumen ke Arsip
     */
    public function softDeleteDocument(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND fakultas_id = ? AND level = 'fakultas'");
        $stmt->execute([(int)$id, $this->fakultasId]);
        $doc = $stmt->fetch();

        if ($doc) {
            $this->db->prepare("UPDATE ppepp_documents SET deleted_at = NOW() WHERE id = ?")->execute([(int)$id]);
            AuditLogger::log(
                aksi: 'DELETE',
                modul: 'Dokumen PPEPP Fakultas',
                targetId: (string)$id,
                targetName: $doc['nama_dokumen']
            );
            Auth::setFlash('info', "Dokumen '{$doc['nama_dokumen']}' telah dipindahkan ke Arsip Riwayat Hapus.");
        }
        redirect('fakultas/dokumen');
    }

    /**
     * Restore Dokumen dari Arsip
     */
    public function restoreDocument(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND fakultas_id = ? AND level = 'fakultas' AND deleted_at IS NOT NULL");
        $stmt->execute([(int)$id, $this->fakultasId]);
        $doc = $stmt->fetch();

        if ($doc) {
            $this->db->prepare("UPDATE ppepp_documents SET deleted_at = NULL WHERE id = ?")->execute([(int)$id]);
            AuditLogger::log(
                aksi: 'RESTORE',
                modul: 'Dokumen PPEPP Fakultas',
                targetId: (string)$id,
                targetName: $doc['nama_dokumen']
            );
            Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' berhasil dipulihkan.");
        }
        redirect('fakultas/dokumen/arsip');
    }

    /**
     * Hapus Dokumen Secara Permanen dari Arsip (Hard Delete)
     */
    public function forceDeleteDocument(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND fakultas_id = ? AND level = 'fakultas' AND deleted_at IS NOT NULL");
        $stmt->execute([(int)$id, $this->fakultasId]);
        $doc = $stmt->fetch();

        if ($doc) {
            // 1. Ambil seluruh file lampiran di ppepp_document_files
            $stmtFiles = $this->db->prepare("SELECT file_path FROM ppepp_document_files WHERE document_id = ?");
            $stmtFiles->execute([(int)$id]);
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
            $this->db->prepare("DELETE FROM ppepp_document_files WHERE document_id = ?")->execute([(int)$id]);

            // 4. Hapus data dokumen utama
            $this->db->prepare("DELETE FROM ppepp_documents WHERE id = ?")->execute([(int)$id]);

            // 5. Catat audit trail
            AuditLogger::log(
                aksi: 'DELETE',
                modul: 'Dokumen PPEPP Fakultas (Permanen)',
                targetId: (string)$id,
                targetName: $doc['nama_dokumen'],
                oldValues: $doc
            );

            Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' dan seluruh berkas lampirannya telah berhasil dihapus secara permanen.");
        } else {
            Auth::setFlash('danger', "Dokumen tidak ditemukan dalam arsip atau Anda tidak memiliki akses.");
        }

        redirect('fakultas/dokumen/arsip');
    }

    /**
     * Arsip / Riwayat Terhapus
     */
    public function arsip(): void {
        $stmt = $this->db->prepare("
            SELECT d.*, b.nama_bidang 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            WHERE d.fakultas_id = ? AND d.level = 'fakultas' AND d.deleted_at IS NOT NULL 
            ORDER BY d.deleted_at DESC
        ");
        $stmt->execute([$this->fakultasId]);
        $archivedDocs = $stmt->fetchAll();
        $this->attachFilesToDocs($archivedDocs);

        $this->render('admin_fakultas/arsip', [
            'pageTitle' => 'Arsip Dokumen Terhapus ' . $this->fakultas['nama_fakultas'],
            'fakultas' => $this->fakultas,
            'archivedDocs' => $archivedDocs
        ]);
    }

    /**
     * Pengaturan Profil Dekanat
     */
    public function dekanat(): void {
        $stmt = $this->db->prepare("SELECT * FROM fakultas WHERE id = ?");
        $stmt->execute([$this->fakultasId]);
        $this->fakultas = $stmt->fetch() ?: $this->fakultas;

        $this->render('admin_fakultas/dekanat', [
            'pageTitle' => 'Pengaturan Profil Dekanat ' . $this->fakultas['nama_fakultas'],
            'fakultas' => $this->fakultas
        ]);
    }

    /**
     * Simpan Perubahan Profil Dekanat
     */
    public function saveDekanat(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('fakultas/dekanat');
            return;
        }

        $namaDekan = trim($_POST['nama_dekan'] ?? '');
        $nidnDekan = trim($_POST['nidn_dekan'] ?? '');
        $namaWadek = trim($_POST['nama_wadek'] ?? '');
        $nidnWadek = trim($_POST['nidn_wadek'] ?? '');
        $periodeJabatan = trim($_POST['periode_jabatan'] ?? '2022 - 2026');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if (empty($namaDekan)) {
            Auth::setFlash('danger', 'Nama lengkap Dekan wajib diisi.');
            redirect('fakultas/dekanat');
            return;
        }

        $stmt = $this->db->prepare("
            UPDATE fakultas SET 
                nama_dekan = ?, nidn_dekan = ?,
                nama_wadek = ?, nidn_wadek = ?,
                periode_jabatan = ?, deskripsi = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$namaDekan, $nidnDekan, $namaWadek, $nidnWadek, $periodeJabatan, $deskripsi, $this->fakultasId]);

        AuditLogger::log(
            aksi: 'UPDATE',
            modul: 'Profil Dekanat',
            targetId: (string)$this->fakultasId,
            targetName: $this->fakultas['nama_fakultas'],
            newValues: ['dekan' => $namaDekan, 'wadek' => $namaWadek, 'periode' => $periodeJabatan]
        );

        Auth::setFlash('success', 'Profil Dekanat berhasil diperbarui dan akan ditampilkan pada portal publik.');
        redirect('fakultas/dekanat');
    }

    /**
     * Helper: Attach files to documents array
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
