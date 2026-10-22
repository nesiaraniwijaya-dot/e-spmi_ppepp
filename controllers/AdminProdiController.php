<?php
/**
 * Admin Program Studi Controller
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/AuditLogger.php';

class AdminProdiController extends Controller {

    private int $prodiId;
    private array $prodi;

    public function __construct() {
        parent::__construct();
        Auth::requireRole(['kaprodi', 'sekprodi']);

        $this->prodiId = (int)(Auth::prodiId() ?? 0);
        if (!$this->prodiId) {
            Auth::setFlash('danger', 'Akun Anda belum dipetakan ke Program Studi mana pun. Hubungi Admin LPM.');
            redirect('logout');
        }

        $stmt = $this->db->prepare("
            SELECT p.*, f.nama_fakultas, f.kode_fakultas 
            FROM prodis p 
            JOIN fakultas f ON p.fakultas_id = f.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$this->prodiId]);
        $this->prodi = $stmt->fetch() ?: [];

        if (empty($this->prodi)) {
            Auth::setFlash('danger', 'Data Program Studi tidak valid.');
            redirect('logout');
        }
    }

    /**
     * Dashboard Admin Prodi
     */
    public function dashboard(): void {
        // Statistics for current prodi
        $cycleStats = [
            'penetapan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id={$this->prodiId} AND siklus='penetapan' AND deleted_at IS NULL")->fetchColumn(),
            'pelaksanaan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id={$this->prodiId} AND siklus='pelaksanaan' AND deleted_at IS NULL")->fetchColumn(),
            'evaluasi' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id={$this->prodiId} AND siklus='evaluasi' AND deleted_at IS NULL")->fetchColumn(),
            'pengendalian' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id={$this->prodiId} AND siklus='pengendalian' AND deleted_at IS NULL")->fetchColumn(),
            'peningkatan' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id={$this->prodiId} AND siklus='peningkatan' AND deleted_at IS NULL")->fetchColumn(),
            'total' => 0,
            'arsip' => $this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id={$this->prodiId} AND deleted_at IS NOT NULL")->fetchColumn()
        ];
        $cycleStats['total'] = $cycleStats['penetapan'] + $cycleStats['pelaksanaan'] + $cycleStats['evaluasi'] + $cycleStats['pengendalian'] + $cycleStats['peningkatan'];

        // Documents in review/revision cycle with GPM/LPM (revisi_gpm, revisi_lpm, perlu_perbaikan & sudah_diperbaiki)
        $stmtRevisi = $this->db->prepare("
            SELECT d.*, b.nama_bidang, u.name as reviewer_name 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN users u ON d.reviewed_by = u.id
            WHERE d.prodi_id = ? AND d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL 
            ORDER BY 
                GREATEST(
                    COALESCE(d.lpm_reviewed_at, '1970-01-01 00:00:00'),
                    COALESCE(d.gpm_reviewed_at, '1970-01-01 00:00:00'),
                    COALESCE(d.reviewed_at, '1970-01-01 00:00:00'),
                    COALESCE(d.updated_at, '1970-01-01 00:00:00')
                ) DESC, d.id DESC
        ");
        $stmtRevisi->execute([$this->prodiId]);
        $revisiDocs = $stmtRevisi->fetchAll();

        // Dokumen yang diunggah HARI INI untuk prodi ini
        $stmtRecent = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang 
            FROM ppepp_documents d 
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id 
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            WHERE d.prodi_id = ? AND d.deleted_at IS NULL AND DATE(d.created_at) = CURDATE()
            ORDER BY d.created_at DESC
        ");
        $stmtRecent->execute([$this->prodiId]);
        $recentDocs = $stmtRecent->fetchAll();
        $this->attachFilesToDocs($recentDocs);

        $this->render('admin_prodi/dashboard', [
            'pageTitle' => 'Dashboard ' . $this->prodi['nama_prodi'],
            'prodi' => $this->prodi,
            'cycleStats' => $cycleStats,
            'recentDocs' => $recentDocs,
            'revisiDocs' => $revisiDocs
        ]);
    }

    /**
     * Helper: Attach files and sub-bidang to documents array
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
     * Daftar Dokumen PPEPP Prodi (Active & Non-Draft)
     */
    public function documents(): void {
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        $stmt = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as uploader_name,
                   rev.name as reviewer_name, rev.role as reviewer_role
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            LEFT JOIN users rev ON d.reviewed_by = rev.id
            WHERE d.prodi_id = ? AND d.deleted_at IS NULL AND d.status_review != 'draft'
            ORDER BY d.created_at DESC
        ");
        $stmt->execute([$this->prodiId]);
        $documents = $stmt->fetchAll();

        $this->attachFilesToDocs($documents);

        $perluPerbaikanDocs = array_values(array_filter($documents, fn($d) => in_array($d['status_review'] ?? '', ['revisi_gpm', 'revisi_lpm', 'perlu_perbaikan'])));
        $sudahDiperbaikiDocs = array_values(array_filter($documents, fn($d) => in_array($d['status_review'] ?? '', ['sudah_diperbaiki', 'menunggu_review_lpm', 'menunggu_review_gpm'])));

        $draftCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_documents WHERE prodi_id = {$this->prodiId} AND status_review = 'draft' AND deleted_at IS NULL")->fetchColumn();

        $this->render('admin_prodi/documents', [
            'pageTitle' => 'Dokumen PPEPP ' . $this->prodi['nama_prodi'],
            'prodi' => $this->prodi,
            'bidangList' => $bidangList,
            'documents' => $documents,
            'perluPerbaikanDocs' => $perluPerbaikanDocs,
            'sudahDiperbaikiDocs' => $sudahDiperbaikiDocs,
            'perluCount' => count($perluPerbaikanDocs),
            'draftCount' => $draftCount
        ]);
    }

    /**
     * Halaman Khusus Perbaikan Dokumen Mutu (Inbox Revisi dari LPM)
     */
    public function perbaikan(): void {
        $stmt = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as uploader_name,
                   rev.name as reviewer_name, rev.role as reviewer_role
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            LEFT JOIN users rev ON d.reviewed_by = rev.id
            WHERE d.prodi_id = ? AND d.status_review IN ('revisi_gpm', 'revisi_lpm', 'perlu_perbaikan', 'sudah_diperbaiki') AND d.deleted_at IS NULL
            ORDER BY 
                GREATEST(
                    COALESCE(d.lpm_reviewed_at, '1970-01-01 00:00:00'),
                    COALESCE(d.gpm_reviewed_at, '1970-01-01 00:00:00'),
                    COALESCE(d.reviewed_at, '1970-01-01 00:00:00'),
                    COALESCE(d.updated_at, '1970-01-01 00:00:00')
                ) DESC, d.id DESC
        ");
        $stmt->execute([$this->prodiId]);
        $documents = $stmt->fetchAll();
        $this->attachFilesToDocs($documents);

        $perluDocs = array_values(array_filter($documents, fn($d) => in_array($d['status_review'] ?? '', ['revisi_gpm', 'revisi_lpm', 'perlu_perbaikan'])));
        $sudahDocs = array_values(array_filter($documents, fn($d) => in_array($d['status_review'] ?? '', ['sudah_diperbaiki', 'menunggu_review_lpm', 'menunggu_review_gpm'])));

        $this->render('admin_prodi/perbaikan', [
            'pageTitle' => 'Perbaikan Dokumen Mutu - ' . $this->prodi['nama_prodi'],
            'prodi' => $this->prodi,
            'documents' => $documents,
            'perluDocs' => $perluDocs,
            'sudahDocs' => $sudahDocs,
            'perluCount' => count($perluDocs),
            'sudahCount' => count($sudahDocs)
        ]);
    }

    /**
     * Halaman Draf Dokumen Mutu (Belum Diajukan ke LPM)
     */
    public function draft(): void {
        $stmt = $this->db->prepare("
            SELECT d.*, b.nama_bidang, sb.nama_sub_bidang, sb.kode_sub_bidang, u.name as uploader_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON d.sub_bidang_id = sb.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.prodi_id = ? AND d.status_review = 'draft' AND d.deleted_at IS NULL
            ORDER BY d.updated_at DESC
        ");
        $stmt->execute([$this->prodiId]);
        $documents = $stmt->fetchAll();
        $this->attachFilesToDocs($documents);

        $this->render('admin_prodi/draft', [
            'pageTitle' => 'Draf Dokumen Mutu - ' . $this->prodi['nama_prodi'],
            'prodi' => $this->prodi,
            'documents' => $documents,
            'totalDraft' => count($documents)
        ]);
    }

    /**
     * Ajukan Dokumen Draf ke LPM untuk Diverifikasi
     */
    public function ajukanDraft(string $id): void {
        $docId = (int)$id;
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND prodi_id = ? AND status_review = 'draft' AND deleted_at IS NULL");
        $stmt->execute([$docId, $this->prodiId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            Auth::setFlash('danger', 'Draf dokumen tidak ditemukan atau sudah diajukan.');
            redirect('prodi/draft');
            return;
        }

        // Check if doc has at least 1 file or link
        $filesCount = (int)$this->db->query("SELECT COUNT(*) FROM ppepp_document_files WHERE document_id = {$docId}")->fetchColumn();
        $hasLink = !empty($doc['external_link']);
        $hasLegacyFile = !empty($doc['file_path']);

        if ($filesCount === 0 && !$hasLink && !$hasLegacyFile) {
            Auth::setFlash('warning', "Dokumen '{$doc['nama_dokumen']}' belum memiliki berkas terlampir atau tautan Google Drive. Harap lengkapi berkas terlebih dahulu sebelum mengajukan ke LPM.");
            redirect("prodi/dokumen/edit/{$docId}");
            return;
        }

        $stmtUpd = $this->db->prepare("UPDATE ppepp_documents SET status_review = 'menunggu_review_gpm', tahap_review = 'gpm', updated_at = NOW() WHERE id = ?");
        $stmtUpd->execute([$docId]);

        AuditLogger::log('UPDATE', 'Dokumen PPEPP', (string)$docId, $doc['nama_dokumen'], [
            'status_review' => 'draft'
        ], [
            'status_review' => 'menunggu_review_gpm',
            'tahap_review' => 'gpm',
            'action' => 'publish_draft'
        ], $this->prodiId, $this->prodi['nama_prodi']);

        Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' berhasil diajukan ke Gugus Penjaminan Mutu (GPM) Fakultas untuk direview.");
        redirect('prodi/dokumen');
    }

    /**
     * Helper: Ambil daftar siklus PPEPP beserta dokumen per tahap untuk prodi ini
     */
    private function getExistingCyclesWithStages(): array {
        $stmtCycles = $this->db->prepare("
            SELECT c.*, b.nama_bidang, sb.nama_sub_bidang
            FROM ppepp_cycles c
            LEFT JOIN bidang_standar b ON c.bidang_id = b.id
            LEFT JOIN sub_bidang_standar sb ON c.sub_bidang_id = sb.id
            WHERE c.prodi_id = ? AND c.deleted_at IS NULL
            ORDER BY c.updated_at DESC, c.id DESC
        ");
        $stmtCycles->execute([$this->prodiId]);
        $cycles = $stmtCycles->fetchAll() ?: [];

        $stmtDocs = $this->db->prepare("
            SELECT d.id, d.cycle_id, d.nama_dokumen, d.nomor_dokumen, d.siklus, d.status_review, d.tahap_review, d.file_path, d.external_link, d.updated_at
            FROM ppepp_documents d
            WHERE d.prodi_id = ? AND d.cycle_id IS NOT NULL AND d.deleted_at IS NULL
            ORDER BY d.id ASC
        ");
        $stmtDocs->execute([$this->prodiId]);
        $cycleDocs = $stmtDocs->fetchAll() ?: [];

        $docsByCycle = [];
        foreach ($cycleDocs as $cd) {
            $cid = (int)$cd['cycle_id'];
            $docsByCycle[$cid][$cd['siklus']][] = $cd;
        }

        $stagesList = ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'];
        $existingCyclesWithStages = [];

        foreach ($cycles as $c) {
            $cId = (int)$c['id'];
            $stages = [];
            $completedCount = 0;

            foreach ($stagesList as $stageKey) {
                $hasDoc = !empty($docsByCycle[$cId][$stageKey]);
                $docsInStage = $hasDoc ? $docsByCycle[$cId][$stageKey] : [];
                if ($hasDoc) $completedCount++;

                $stages[$stageKey] = [
                    'has_doc' => $hasDoc,
                    'docs' => $docsInStage,
                    'primary_doc' => $hasDoc ? $docsInStage[0] : null
                ];
            }

            $existingCyclesWithStages[] = [
                'id' => $cId,
                'judul_siklus' => $c['judul_siklus'],
                'bidang_id' => $c['bidang_id'],
                'nama_bidang' => $c['nama_bidang'] ?? 'Umum',
                'sub_bidang_id' => $c['sub_bidang_id'],
                'nama_sub_bidang' => $c['nama_sub_bidang'] ?? '',
                'tahun_akademik' => $c['tahun_akademik'],
                'deskripsi' => $c['deskripsi'] ?? '',
                'stages' => $stages,
                'completed_count' => $completedCount,
                'progress_percent' => round(($completedCount / 5) * 100),
                'updated_at' => $c['updated_at'] ?? $c['created_at']
            ];
        }

        return $existingCyclesWithStages;
    }

    /**
     * Halaman Khusus: Judul yang Sudah Ada & Kelengkapan Tahapan PPEPP
     */
    public function cycles(): void {
        $existingCyclesWithStages = $this->getExistingCyclesWithStages();

        $this->render('admin_prodi/cycles', [
            'pageTitle' => 'Judul yang Sudah Ada - Tahapan Siklus PPEPP',
            'prodi' => $this->prodi,
            'existingCycles' => $existingCyclesWithStages
        ]);
    }

    /**
     * Form Tambah Dokumen
     */
    public function createDocument(): void {
        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
        $subBidangList = $this->db->query("SELECT * FROM sub_bidang_standar WHERE is_active = 1 ORDER BY bidang_id ASC, id ASC")->fetchAll();

        $existingCyclesWithStages = $this->getExistingCyclesWithStages();

        // Cek parameter pre-fill jika datang dari link lanjutan
        $targetCycleId = isset($_GET['cycle_id']) ? (int)$_GET['cycle_id'] : null;
        $targetSiklus = isset($_GET['siklus']) ? trim($_GET['siklus']) : null;
        $presetCycle = null;
        if ($targetCycleId && !empty($existingCyclesWithStages)) {
            foreach ($existingCyclesWithStages as $ecs) {
                if ($ecs['id'] === $targetCycleId) {
                    $presetCycle = $ecs;
                    break;
                }
            }
        }

        $this->render('admin_prodi/document_form', [
            'pageTitle' => 'Unggah Dokumen Mutu Baru',
            'prodi' => $this->prodi,
            'bidangList' => $bidangList,
            'subBidangList' => $subBidangList,
            'doc' => null,
            'docFiles' => [],
            'existingCycles' => $existingCyclesWithStages,
            'presetCycle' => $presetCycle,
            'presetSiklus' => $targetSiklus
        ]);
    }

    /**
     * Form Edit Dokumen
     */
    public function editDocument(string $id): void {
        $stmt = $this->db->prepare("
            SELECT d.*, u.name as reviewer_name, u.role as reviewer_role 
            FROM ppepp_documents d 
            LEFT JOIN users u ON d.reviewed_by = u.id 
            WHERE d.id = ? AND d.prodi_id = ? AND d.deleted_at IS NULL
        ");
        $stmt->execute([$id, $this->prodiId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            Auth::setFlash('danger', 'Dokumen tidak ditemukan atau bukan milik program studi Anda.');
            redirect('prodi/dokumen');
        }

        $stmtFiles = $this->db->prepare("SELECT * FROM ppepp_document_files WHERE document_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtFiles->execute([$id]);
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

        $bidangList = $this->db->query("SELECT * FROM bidang_standar WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
        $subBidangList = $this->db->query("SELECT * FROM sub_bidang_standar WHERE is_active = 1 ORDER BY bidang_id ASC, id ASC")->fetchAll();

        $this->render('admin_prodi/document_form', [
            'pageTitle' => 'Ubah Dokumen: ' . $doc['nama_dokumen'],
            'prodi' => $this->prodi,
            'bidangList' => $bidangList,
            'subBidangList' => $subBidangList,
            'doc' => $doc,
            'docFiles' => $docFiles
        ]);
    }

    /**
     * Simpan Dokumen (Create / Update Multi-File Dokumen Mutu & Batch Multi-Upload hingga 10 Dokumen)
     */
    public function saveDocument(): void {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $isEdit = !empty($id);
        $isDraft = (isset($_POST['action_submit']) && $_POST['action_submit'] === 'draft');
        $allowedExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

        // ==========================================
        // 1. BATCH MULTI-UPLOAD (CREATE UP TO 10 DOCS)
        // ==========================================
        if (!$isEdit && !empty($_POST['docs']) && is_array($_POST['docs'])) {
            $docsData = $_POST['docs'];
            $successCount = 0;
            $createdDocNames = [];

            if (count($docsData) > 10) {
                Auth::setFlash('danger', 'Maksimal dokumen yang dapat diunggah sekaligus adalah 10 dokumen.');
                redirect('prodi/dokumen/create');
                return;
            }

            $this->db->beginTransaction();
            try {
                foreach ($docsData as $cardIdx => $d) {
                    $docNumber = $cardIdx + 1;
                    $namaDokumen = trim($d['nama_dokumen'] ?? '');
                    $nomorDokumen = trim($d['nomor_dokumen'] ?? '');
                    $bidangId = !empty($d['bidang_id']) ? (int)$d['bidang_id'] : null;
                    $subBidangId = !empty($d['sub_bidang_id']) ? (int)$d['sub_bidang_id'] : null;
                    $siklus = trim($d['siklus'] ?? '');
                    $tahunAkademik = normalize_tahun_akademik($d['tahun_akademik'] ?? '');
                    $startDate = null;
                    $endDate = null;
                    $jenisUpload = trim($d['jenis_upload'] ?? 'file');
                    
                    // Parse multi-links GDrive
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
                                $errLink = validate_document_external_url($tLink);
                                if ($errLink !== null) {
                                    throw new \Exception("Dokumen #{$docNumber} (Tautan #" . ($lIdx + 1) . "): {$errLink}");
                                }
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
                            $errLink = validate_document_external_url($tLink);
                            if ($errLink !== null) {
                                throw new \Exception("Dokumen #{$docNumber}: {$errLink}");
                            }
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
                    $deskripsi = null;

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

                    // Process files for this card (files_{$cardIdx})
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
                                    $fileName = 'DOC_' . $this->prodiId . '_' . time() . '_' . uniqid() . '.' . $ext;
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

                    // Dukungan berkas Base64 (mengatasi blokir 403 OpenResty WAF pada multipart/form-data)
                    if (empty($uploadedFilesList) && !empty($d['files_base64']) && is_array($d['files_base64'])) {
                        foreach ($d['files_base64'] as $fIdx => $b64Item) {
                            $origName = trim($b64Item['name'] ?? '');
                            $rawB64 = $b64Item['data'] ?? '';
                            if (empty($origName) || empty($rawB64)) continue;
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
                                
                                if (preg_match('/^data:.*?;base64,/', $rawB64, $m)) {
                                    $rawB64 = substr($rawB64, strlen($m[0]));
                                }
                                $bin = base64_decode($rawB64);
                                if ($bin !== false) {
                                    $size = strlen($bin);
                                    $fileName = 'DOC_' . $this->prodiId . '_' . time() . '_' . uniqid() . '.' . $ext;
                                    $targetFile = DOC_UPLOAD_PATH . '/' . $fileName;
                                    if (file_put_contents($targetFile, $bin) !== false) {
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

                    // Validation per document
                    if (empty($namaDokumen)) {
                        throw new \Exception("Nama dokumen pada Dokumen #{$docNumber} wajib diisi.");
                    }
                    if (empty($tahunAkademik)) {
                        throw new \Exception("Tahun akademik pada Dokumen #{$docNumber} wajib diisi.");
                    }
                    if (empty($siklus)) {
                        throw new \Exception("Kategori Siklus PPEPP pada Dokumen #{$docNumber} wajib dipilih.");
                    }
                    if (empty($bidangId)) {
                        throw new \Exception("Bidang standar mutu pada Dokumen #{$docNumber} wajib dipilih.");
                    }

                    $filePath = null;
                    $fileSize = null;
                    $fileExt = null;

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

                    if (!$isDraft) {
                        if (!$hasFiles && !$hasLinks) {
                            throw new \Exception("Dokumen #{$docNumber} ({$namaDokumen}) wajib memiliki minimal 1 berkas file dokumen atau tautan Google Drive untuk diajukan ke LPM.");
                        }
                    }

                    if ($hasFiles) {
                        $primary = $uploadedFilesList[0];
                        $filePath = $primary['file_path'];
                        $fileSize = $primary['file_size'];
                        $fileExt = $primary['file_extension'];
                    } else {
                        $filePath = null;
                        $fileSize = null;
                        $fileExt = null;
                    }

                    if ($hasLinks) {
                        $externalLink = json_encode($rawLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    } else {
                        $externalLink = null;
                    }

                    $statusReview = $isDraft ? 'draft' : 'menunggu_review_gpm';
                    $tahapReview  = $isDraft ? null : 'gpm';

                    // Pastikan terhubung ke ppepp_cycles (Siklus Berkelanjutan)
                    $cycleId = !empty($d['cycle_id']) ? (int)$d['cycle_id'] : null;
                    if (!$cycleId) {
                        try {
                            $stmtCheckCyc = $this->db->prepare("SELECT id FROM ppepp_cycles WHERE judul_siklus = ? AND prodi_id = ? AND deleted_at IS NULL LIMIT 1");
                            $stmtCheckCyc->execute([$namaDokumen, $this->prodiId]);
                            $foundCyc = $stmtCheckCyc->fetchColumn();
                            if ($foundCyc) {
                                $cycleId = (int)$foundCyc;
                            } else {
                                $stmtInsCyc = $this->db->prepare("
                                    INSERT INTO ppepp_cycles (judul_siklus, level, prodi_id, fakultas_id, bidang_id, sub_bidang_id, tahun_akademik, deskripsi, created_by, created_at)
                                    VALUES (?, 'prodi', ?, ?, ?, ?, ?, ?, ?, NOW())
                                ");
                                $stmtInsCyc->execute([
                                    $namaDokumen, $this->prodiId, (int)($this->prodi['fakultas_id'] ?? 0),
                                    $bidangId, $subBidangId, $tahunAkademik, $deskripsi, Auth::id()
                                ]);
                                $cycleId = (int)$this->db->lastInsertId();
                            }
                        } catch (\PDOException $e) {
                            $cycleId = null;
                        }
                    } else {
                        try {
                            $stmtUpdCyc = $this->db->prepare("UPDATE ppepp_cycles SET updated_at = NOW() WHERE id = ?");
                            $stmtUpdCyc->execute([$cycleId]);
                        } catch (\PDOException $e) {}
                    }

                    // Insert into ppepp_documents
                    $stmt = $this->db->prepare("
                        INSERT INTO ppepp_documents 
                        (cycle_id, prodi_id, fakultas_id, level, user_id, bidang_id, sub_bidang_id, nama_dokumen, nomor_dokumen, siklus, tahun_akademik, 
                         tanggal_berlaku_mulai, tanggal_berlaku_selesai, jenis_upload, file_path, file_size, 
                         file_extension, external_link, public_page_limit, can_download_public, status_review, tahap_review, deskripsi)
                        VALUES (?, ?, ?, 'prodi', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $cycleId, $this->prodiId, (int)($this->prodi['fakultas_id'] ?? 0), Auth::id(), $bidangId, $subBidangId, $namaDokumen, $nomorDokumen,
                        $siklus, $tahunAkademik, $startDate, $endDate, $jenisUpload,
                        $filePath, $fileSize, $fileExt, $externalLink,
                        $publicPageLimit, $canDownloadPublic, $statusReview, $tahapReview, $deskripsi
                    ]);
                    $newDocId = (int)$this->db->lastInsertId();

                    // Sinkronisasi sub bidang dokumen induk
                    sync_document_sub_bidang($this->db, $newDocId, $allDocSubIds);

                    // Insert all files into ppepp_document_files with narasi
                    if (!empty($uploadedFilesList)) {
                        $stmtInsF = $this->db->prepare("
                            INSERT INTO ppepp_document_files (document_id, file_name, file_path, file_size, file_extension, narasi, sub_bidang_ids, sort_order, is_page_limited, public_page_limit, can_download_public)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        $sortOrder = 1;
                        $batchFileIsLim = $d['file_is_limited'] ?? [];
                        $batchFilePLim = $d['file_page_limit'] ?? [];
                        $batchFileCanDl = $d['file_can_download'] ?? [];

                        foreach ($uploadedFilesList as $uIdx => $uFile) {
                            $fIsLim = isset($batchFileIsLim[$uIdx]) ? (!empty($batchFileIsLim[$uIdx]) ? 1 : 0) : ($isPageLimited ? 1 : 0);
                            $fPLim = isset($batchFilePLim[$uIdx]) ? max(1, (int)$batchFilePLim[$uIdx]) : ($isPageLimited ? $publicPageLimit : 1);
                            $fCanDl = isset($batchFileCanDl[$uIdx]) ? (!empty($batchFileCanDl[$uIdx]) ? 1 : 0) : $canDownloadPublic;
                            $fSubsJson = json_encode($uFile['sub_bidang_ids']);

                            $stmtInsF->execute([
                                $newDocId, $uFile['file_name'], $uFile['file_path'], $uFile['file_size'], $uFile['file_extension'], $uFile['narasi'], $fSubsJson, $sortOrder++,
                                $fIsLim, $fPLim, $fCanDl
                            ]);
                            $newFileId = (int)$this->db->lastInsertId();
                            sync_file_sub_bidang($this->db, $newFileId, $uFile['sub_bidang_ids']);
                        }
                    }

                    AuditLogger::log('CREATE', 'Dokumen PPEPP', (string)$newDocId, $namaDokumen, null, [
                        'nama' => $namaDokumen,
                        'siklus' => $siklus,
                        'bidang_id' => $bidangId,
                        'sub_bidang_id' => $subBidangId,
                        'jenis' => $jenisUpload,
                        'status_review' => $statusReview,
                        'files_count' => count($uploadedFilesList),
                        'batch_index' => $cardIdx
                    ], $this->prodiId, $this->prodi['nama_prodi']);

                    $successCount++;
                    $createdDocNames[] = $namaDokumen;
                }

                $this->db->commit();

                if ($isDraft) {
                    $msg = ($successCount > 1)
                        ? "Berhasil menyimpan {$successCount} dokumen sebagai draf. Anda dapat melengkapi berkasnya dan mengajukannya kapan saja."
                        : "Dokumen '{$createdDocNames[0]}' berhasil disimpan sebagai draf.";
                    Auth::setFlash('success', $msg);
                    redirect('prodi/draft');
                    return;
                } else {
                    $msg = ($successCount > 1) 
                        ? "Berhasil mengunggah {$successCount} dokumen mutu sekaligus dan diajukan ke LPM untuk diverifikasi."
                        : "Dokumen mutu '{$createdDocNames[0]}' berhasil diunggah dan diajukan ke LPM.";
                    Auth::setFlash('success', $msg);
                    redirect('prodi/dokumen');
                    return;
                }

            } catch (\Throwable $e) {
                $this->db->rollBack();
                Auth::setFlash('danger', 'Gagal mengunggah dokumen batch: ' . $e->getMessage());
                redirect('prodi/dokumen/create');
                return;
            }
        }

        // ==========================================
        // 2. SINGLE DOCUMENT PROCESSING (EDIT OR FALLBACK SINGLE CREATE)
        // ==========================================
        $namaDokumen = trim($_POST['nama_dokumen'] ?? '');
        $nomorDokumen = trim($_POST['nomor_dokumen'] ?? '');
        $bidangId = !empty($_POST['bidang_id']) ? (int)$_POST['bidang_id'] : null;
        $subBidangId = !empty($_POST['sub_bidang_id']) ? (int)$_POST['sub_bidang_id'] : null;
        $siklus = trim($_POST['siklus'] ?? '');
        $tahunAkademik = normalize_tahun_akademik($_POST['tahun_akademik'] ?? '');
        $startDate = null;
        $endDate = null;
        $jenisUpload = trim($_POST['jenis_upload'] ?? 'file');
        
        $redirectUrl = $isEdit ? "prodi/dokumen/edit/{$id}" : 'prodi/dokumen/create';

        // Process Google Drive Links
        $rawSingleLinks = [];
        $linkSubBidangs = $_POST['external_link_sub_bidang'] ?? [];
        if (!empty($_POST['external_links']) && is_array($_POST['external_links'])) {
            $linkCanDls = !empty($_POST['external_link_can_download']) && is_array($_POST['external_link_can_download']) ? array_values($_POST['external_link_can_download']) : [];
            $linkIsLims = !empty($_POST['external_link_is_limited']) && is_array($_POST['external_link_is_limited']) ? array_values($_POST['external_link_is_limited']) : [];
            $linkPLims = !empty($_POST['external_link_page_limit']) && is_array($_POST['external_link_page_limit']) ? array_values($_POST['external_link_page_limit']) : [];
            foreach ($_POST['external_links'] as $lIdx => $linkItem) {
                $tLink = trim($linkItem);
                $tNarasi = trim($_POST['external_link_narasi'][$lIdx] ?? '');
                if (!empty($tLink)) {
                    $errLink = validate_document_external_url($tLink);
                    if ($errLink !== null) {
                        Auth::setFlash('danger', "Tautan Berkas #" . ($lIdx + 1) . ": {$errLink}");
                        redirect($redirectUrl);
                    }
                    if (empty($tNarasi)) {
                        Auth::setFlash('danger', 'Narasi untuk setiap tautan Google Drive wajib diisi.');
                        redirect($redirectUrl);
                    }
                    $lSubs = !empty($linkSubBidangs[$lIdx]) && is_array($linkSubBidangs[$lIdx])
                        ? array_values(array_filter(array_map('intval', $linkSubBidangs[$lIdx])))
                        : ($subBidangId ? [(int)$subBidangId] : []);
                    $isLim = isset($linkIsLims[$lIdx]) ? (!empty($linkIsLims[$lIdx]) ? 1 : 0) : 1;
                    $pLim = isset($linkPLims[$lIdx]) ? max(1, (int)$linkPLims[$lIdx]) : 1;
                    $canDl = !empty($linkCanDls[$lIdx]) ? 1 : 0;
                    $rawSingleLinks[] = [
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
                $errLink = validate_document_external_url($tLink);
                if ($errLink !== null) {
                    Auth::setFlash('danger', $errLink);
                    redirect($redirectUrl);
                }
                $rawSingleLinks[] = [
                    'url' => $tLink,
                    'narasi' => '',
                    'is_page_limited' => 1,
                    'public_page_limit' => 1,
                    'can_download_public' => 0,
                    'sub_bidang_ids' => $subBidangId ? [(int)$subBidangId] : []
                ];
            }
        }
        $externalLink = !empty($rawSingleLinks) ? json_encode($rawSingleLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
        $deskripsi = null;

        // Parent document access settings: inherit from item #1 (file #1 or link #1)
        if ($jenisUpload === 'file') {
            if ($isEdit) {
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
            } else {
                $isLim0 = !empty($_POST['new_file_is_limited'][0]) ? 1 : 0;
                $publicPageLimit = $isLim0 ? max(1, (int)($_POST['new_file_page_limit'][0] ?? 1)) : 0;
                $canDownloadPublic = !empty($_POST['new_file_can_download'][0]) ? 1 : 0;
            }
        } elseif (!empty($rawSingleLinks)) {
            $publicPageLimit = !empty($rawSingleLinks[0]['is_page_limited']) ? max(1, (int)($rawSingleLinks[0]['public_page_limit'] ?? 1)) : 0;
            $canDownloadPublic = !empty($rawSingleLinks[0]['can_download_public']) ? 1 : 0;
        } else {
            $publicPageLimit = 1;
            $canDownloadPublic = 0;
        }
        $isPageLimited = $publicPageLimit > 0 ? 1 : 0;

        // 1. Validasi Field Wajib
        if (empty($namaDokumen)) {
            Auth::setFlash('danger', 'Nama dokumen mutu wajib diisi.');
            redirect($redirectUrl);
        }

        if (empty($tahunAkademik)) {
            Auth::setFlash('danger', 'Tahun akademik wajib diisi.');
            redirect($redirectUrl);
        }

        if (empty($siklus)) {
            Auth::setFlash('danger', 'Kategori Siklus PPEPP wajib dipilih.');
            redirect($redirectUrl);
        }

        if (empty($bidangId)) {
            Auth::setFlash('danger', 'Bidang standar mutu wajib dipilih.');
            redirect($redirectUrl);
        }

        // Himpun seluruh sub standar dari link, berkas eksisting, dan berkas baru
        $allDocSubIds = [];
        if ($subBidangId) $allDocSubIds[] = (int)$subBidangId;
        foreach ($rawSingleLinks as $rl) {
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
            if ($subBidangId && !in_array((int)$subBidangId, $allDocSubIds, true)) {
                $allDocSubIds[] = (int)$subBidangId;
            }
        }

        if (empty($bidangId) && !empty($subBidangId)) {
            $stmtB = $this->db->prepare("SELECT bidang_id FROM sub_bidang_standar WHERE id = ?");
            $stmtB->execute([$subBidangId]);
            $bidangId = (int)$stmtB->fetchColumn();
        }

        if (empty($subBidangId)) {
            Auth::setFlash('danger', 'Bidang yang dipilih belum memiliki daftar Standar Mutu di sistem.');
            redirect($redirectUrl);
            return;
        }

        // Process Multi-File Uploads from $_FILES
        $uploadedFilesList = [];
        
        // Handle files[] (multiple upload array)
        if (!empty($_FILES['files']['name'])) {
            if (is_array($_FILES['files']['name'])) {
                foreach ($_FILES['files']['name'] as $idx => $origName) {
                    if (!empty($origName) && $_FILES['files']['error'][$idx] === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                        if (in_array($ext, $allowedExts)) {
                            $fNarasi = trim($_POST['new_file_narasi'][$idx] ?? ($_POST['file_narasi'][$idx] ?? ''));
                            if (empty($fNarasi)) {
                                $fNarasi = pathinfo($origName, PATHINFO_FILENAME);
                            }
                            $tmpName = $_FILES['files']['tmp_name'][$idx];
                            $size = $_FILES['files']['size'][$idx];
                            $fileName = 'DOC_' . $this->prodiId . '_' . time() . '_' . uniqid() . '.' . $ext;
                            $targetFile = DOC_UPLOAD_PATH . '/' . $fileName;
                            if (move_uploaded_file($tmpName, $targetFile)) {
                                $uploadedFilesList[] = [
                                    'file_name' => $origName,
                                    'file_path' => 'uploads/documents/' . $fileName,
                                    'file_size' => round($size / (1024 * 1024), 2) . ' MB',
                                    'file_extension' => $ext,
                                    'narasi' => $fNarasi
                                ];
                            }
                        }
                    }
                }
            }
        }

        // Handle single legacy file_dokumen
        if (!empty($_FILES['file_dokumen']['name']) && $_FILES['file_dokumen']['error'] === UPLOAD_ERR_OK) {
            $origName = $_FILES['file_dokumen']['name'];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExts)) {
                $fNarasi = trim($_POST['narasi'] ?? ($_POST['file_narasi'][0] ?? ''));
                if (empty($fNarasi)) {
                    $fNarasi = pathinfo($origName, PATHINFO_FILENAME);
                }
                $tmpName = $_FILES['file_dokumen']['tmp_name'];
                $size = $_FILES['file_dokumen']['size'];
                $fileName = 'DOC_' . $this->prodiId . '_' . time() . '_' . uniqid() . '.' . $ext;
                $targetFile = DOC_UPLOAD_PATH . '/' . $fileName;
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $uploadedFilesList[] = [
                        'file_name' => $origName,
                        'file_path' => 'uploads/documents/' . $fileName,
                        'file_size' => round($size / (1024 * 1024), 2) . ' MB',
                        'file_extension' => $ext,
                        'narasi' => $fNarasi
                    ];
                }
            }
        }

        // ==========================================
        // 4. MODE EDIT (UPDATE)
        // ==========================================
        if ($isEdit) {
            $stmtOld = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND prodi_id = ? AND deleted_at IS NULL");
            $stmtOld->execute([$id, $this->prodiId]);
            $oldDoc = $stmtOld->fetch();

            if (!$oldDoc) {
                Auth::setFlash('danger', 'Dokumen tidak ditemukan atau bukan milik program studi Anda.');
                redirect('prodi/dokumen');
                return;
            }

            // Handle deleted files
            $deletedLegacy = false;
            if (!empty($_POST['delete_file_ids']) && is_array($_POST['delete_file_ids'])) {
                foreach ($_POST['delete_file_ids'] as $delFileId) {
                    $delFileIdInt = (int)$delFileId;
                    if ($delFileIdInt === 0 || $delFileId === 'legacy') {
                        $deletedLegacy = true;
                        if (!empty($oldDoc['file_path']) && file_exists(ROOT_PATH . '/' . $oldDoc['file_path'])) {
                            @unlink(ROOT_PATH . '/' . $oldDoc['file_path']);
                        }
                        $oldDoc['file_path'] = null;
                        $oldDoc['file_size'] = null;
                        $oldDoc['file_extension'] = null;
                    } else {
                        $stmtF = $this->db->prepare("SELECT file_path FROM ppepp_document_files WHERE id = ? AND document_id = ?");
                        $stmtF->execute([$delFileIdInt, $id]);
                        $filePathToDel = $stmtF->fetchColumn();
                        if ($filePathToDel && file_exists(ROOT_PATH . '/' . $filePathToDel)) {
                            @unlink(ROOT_PATH . '/' . $filePathToDel);
                        }
                        $stmtDelF = $this->db->prepare("DELETE FROM ppepp_document_files WHERE id = ? AND document_id = ?");
                        $stmtDelF->execute([$delFileIdInt, $id]);
                    }
                }
            }

            // Pastikan sub standar induk terbarui
            if ($subBidangId && !in_array((int)$subBidangId, $allDocSubIds, true)) {
                $allDocSubIds[] = (int)$subBidangId;
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

            // Update narasi & sub-standar for existing files
            if (!empty($_POST['existing_file_narasi']) && is_array($_POST['existing_file_narasi'])) {
                $deletedIds = array_map('intval', $_POST['delete_file_ids'] ?? []);
                $stmtUpdF = $this->db->prepare("
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
                    $fNarasiTrimmed = trim($fNarasi);
                    $fIsLimVal = !empty($exIsLim[$fId]) ? 1 : 0;
                    $fPLimVal = $fIsLimVal ? max(1, (int)($exPLim[$fId] ?? 1)) : 1;
                    $fCanDlVal = !empty($exCanDl[$fId]) ? 1 : 0;

                    $stmtUpdF->execute([$fNarasiTrimmed, $fIsLimVal, $fPLimVal, $fCanDlVal, $fId, $id]);

                    $fSubArray = !empty($exSubs[$fId]) && is_array($exSubs[$fId])
                        ? array_values(array_filter(array_map('intval', $exSubs[$fId])))
                        : ($subBidangId ? [(int)$subBidangId] : []);
                    sync_file_sub_bidang($this->db, $fId, $fSubArray);
                }
            }

            // Insert newly uploaded files
            if (!empty($uploadedFilesList)) {
                $stmtInsF = $this->db->prepare("
                    INSERT INTO ppepp_document_files (document_id, file_name, file_path, file_size, file_extension, narasi, sub_bidang_ids, sort_order, is_page_limited, public_page_limit, can_download_public, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $newIsLim = $_POST['new_file_is_limited'] ?? [];
                $newPLim = $_POST['new_file_page_limit'] ?? [];
                $newCanDl = $_POST['new_file_can_download'] ?? [];
                $newSubs = $_POST['new_file_sub_bidang'] ?? [];
                $sortOrder = 1;

                foreach ($uploadedFilesList as $uIdx => $uFile) {
                    $fIsLimVal = isset($newIsLim[$uIdx]) ? (!empty($newIsLim[$uIdx]) ? 1 : 0) : ($isPageLimited ? 1 : 0);
                    $fPLimVal = isset($newPLim[$uIdx]) ? max(1, (int)$newPLim[$uIdx]) : ($isPageLimited ? $publicPageLimit : 1);
                    $fCanDlVal = isset($newCanDl[$uIdx]) ? (!empty($newCanDl[$uIdx]) ? 1 : 0) : $canDownloadPublic;
                    $fSubArray = !empty($newSubs[$uIdx]) && is_array($newSubs[$uIdx])
                        ? array_values(array_filter(array_map('intval', $newSubs[$uIdx])))
                        : ($subBidangId ? [(int)$subBidangId] : []);
                    $fSubsJson = json_encode($fSubArray);

                    $stmtInsF->execute([
                        $id, $uFile['file_name'], $uFile['file_path'], $uFile['file_size'], $uFile['file_extension'], $uFile['narasi'], $fSubsJson, $sortOrder++,
                        $fIsLimVal, $fPLimVal, $fCanDlVal
                    ]);
                    $newFileId = (int)$this->db->lastInsertId();
                    sync_file_sub_bidang($this->db, $newFileId, $fSubArray);
                    if (!empty($fSubArray)) {
                        $allDocSubIds = array_merge($allDocSubIds, $fSubArray);
                    }
                }
            }

            // Dukungan upload berkas baru via Base64 untuk Single Edit (mengatasi blokir 403 OpenResty WAF)
            if (!empty($_POST['new_files_base64']) && is_array($_POST['new_files_base64'])) {
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

                foreach ($_POST['new_files_base64'] as $idx => $b64Item) {
                    $origName = trim($b64Item['name'] ?? '');
                    $rawB64 = $b64Item['data'] ?? '';
                    if (empty($origName) || empty($rawB64)) continue;
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

                        if (preg_match('/^data:.*?;base64,/', $rawB64, $m)) {
                            $rawB64 = substr($rawB64, strlen($m[0]));
                        }
                        $bin = base64_decode($rawB64);
                        if ($bin !== false) {
                            $size = strlen($bin);
                            $fileName = 'DOC_' . $this->prodiId . '_' . time() . '_' . uniqid() . '.' . $ext;
                            $targetFile = DOC_UPLOAD_PATH . '/' . $fileName;
                            if (file_put_contents($targetFile, $bin) !== false) {
                                $stmtNewFile->execute([
                                    $id, $origName, 'uploads/documents/' . $fileName,
                                    round($size / (1024 * 1024), 2) . ' MB', $ext, $fNarasi, $fSubsJson, $nextSort++,
                                    $fIsLimVal, $fPLimVal, $fCanDlVal
                                ]);
                                $newFid = (int)$this->db->lastInsertId();
                                sync_file_sub_bidang($this->db, $newFid, $fSubArray);
                                if (!empty($fSubArray)) {
                                    $allDocSubIds = array_merge($allDocSubIds, $fSubArray);
                                }
                            }
                        }
                    }
                }
            }

            // Get primary file from ppepp_document_files
            $stmtPrimary = $this->db->prepare("SELECT * FROM ppepp_document_files WHERE document_id = ? ORDER BY sort_order ASC, id ASC LIMIT 1");
            $stmtPrimary->execute([$id]);
            $primaryFile = $stmtPrimary->fetch();

            if ($primaryFile) {
                $filePath = $primaryFile['file_path'];
                $fileSize = $primaryFile['file_size'];
                $fileExt = $primaryFile['file_extension'];
            } else {
                if (!$deletedLegacy && !empty($oldDoc['file_path'])) {
                    $filePath = $oldDoc['file_path'];
                    $fileSize = $oldDoc['file_size'];
                    $fileExt = $oldDoc['file_extension'];
                } else {
                    $filePath = null;
                    $fileSize = null;
                    $fileExt = null;
                }
            }

            $hasFiles = !empty($filePath);
            $hasLinks = !empty($rawSingleLinks);

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
                    Auth::setFlash('danger', 'Dokumen aktif wajib memiliki minimal 1 berkas file dokumen atau tautan Google Drive untuk diajukan ke LPM.');
                    redirect($redirectUrl);
                    return;
                }
            }

            $newStatusReview = $oldDoc['status_review'] ?? 'menunggu_review_gpm';
            $newTahapReview  = $oldDoc['tahap_review'] ?? 'gpm';
            if ($isDraft) {
                $newStatusReview = 'draft';
                $newTahapReview  = null;
            } else {
                if (in_array($newStatusReview, ['revisi_gpm', 'revisi_lpm', 'perlu_perbaikan', 'sudah_diperbaiki'])) {
                    $newStatusReview = 'sudah_diperbaiki';
                    $newTahapReview  = 'gpm';
                } elseif (in_array($newStatusReview, ['draft', 'belum_direview'])) {
                    $newStatusReview = 'menunggu_review_gpm';
                    $newTahapReview  = 'gpm';
                }
            }

            $stmt = $this->db->prepare("
                UPDATE ppepp_documents 
                SET bidang_id = ?, sub_bidang_id = ?, nama_dokumen = ?, nomor_dokumen = ?, siklus = ?, 
                    tahun_akademik = ?, tanggal_berlaku_mulai = ?, tanggal_berlaku_selesai = ?, 
                    jenis_upload = ?, file_path = ?, file_size = ?, file_extension = ?, 
                    external_link = ?, public_page_limit = ?, can_download_public = ?, 
                    status_review = ?, tahap_review = ?, deskripsi = ?, updated_at = NOW()
                WHERE id = ? AND prodi_id = ?
            ");
            $stmt->execute([
                $bidangId, $subBidangId, $namaDokumen, $nomorDokumen, $siklus, $tahunAkademik,
                $startDate, $endDate, $jenisUpload, $filePath, $fileSize, $fileExt,
                $externalLink, $publicPageLimit, $canDownloadPublic, 
                $newStatusReview, $newTahapReview, $deskripsi, $id, $this->prodiId
            ]);

            // Sinkronisasi seluruh sub bidang ke dokumen induk
            $allDocSubIds = array_values(array_unique(array_filter(array_map('intval', $allDocSubIds))));
            if (empty($allDocSubIds) && !empty($subBidangId)) {
                $allDocSubIds = [(int)$subBidangId];
            }
            sync_document_sub_bidang($this->db, (int)$id, $allDocSubIds);

            // Jika statusnya adalah perbaikan atas revisi yang diminta, catat ke riwayat revisi dan reviews
            if ($newStatusReview === 'sudah_diperbaiki') {
                $curUser = Auth::user();
                $uploaderName = ($curUser['name'] ?? 'Admin Prodi') . ' (' . ($this->prodi['nama_prodi'] ?? 'Prodi') . ')';
                $stmtRevUp = $this->db->prepare("
                    UPDATE ppepp_document_revisions 
                    SET uploader_id = ?, uploader_name = ?, uploader_role = ?, status_review = 'sudah_diperbaiki', revised_at = NOW() 
                    WHERE document_id = ? AND revised_at IS NULL 
                    ORDER BY id DESC LIMIT 1
                ");
                $stmtRevUp->execute([
                    Auth::id(),
                    $uploaderName,
                    Auth::role(),
                    $id
                ]);

                // Catat log pengajuan ulang revisi ke ppepp_document_reviews agar tercatat di Riwayat Review
                $stmtRevLog = $this->db->prepare("
                    INSERT INTO ppepp_document_reviews 
                    (document_id, reviewer_id, reviewer_role, reviewer_name, tahap, status_sebelumnya, status_hasil, catatan, created_at)
                    VALUES (?, ?, 'admin_prodi', ?, 'resubmission', ?, 'sudah_diperbaiki', 'Dokumen hasil revisi telah diunggah oleh Program Studi dan diajukan kembali ke GPM Fakultas untuk ditelaah.', NOW())
                ");
                $stmtRevLog->execute([
                    $id,
                    Auth::id(),
                    $uploaderName,
                    $oldDoc['status_review'] ?? 'perlu_perbaikan'
                ]);
            }

            AuditLogger::log('UPDATE', 'Dokumen PPEPP', (string)$id, $namaDokumen, $oldDoc, [
                'nama' => $namaDokumen,
                'siklus' => $siklus,
                'bidang_id' => $bidangId,
                'sub_bidang_id' => $subBidangId,
                'status_review' => $newStatusReview,
                'tahap_review' => $newTahapReview,
                'jenis' => $jenisUpload,
                'public_page_limit' => $publicPageLimit,
                'can_download_public' => $canDownloadPublic
            ], $this->prodiId, $this->prodi['nama_prodi']);

            if ($isDraft) {
                Auth::setFlash('success', "Perubahan draf dokumen '{$namaDokumen}' berhasil disimpan.");
                redirect('prodi/draft');
                return;
            }

            $pesanSukses = in_array($oldDoc['status_review'], ['revisi_gpm', 'revisi_lpm', 'perlu_perbaikan'])
                ? "Dokumen '{$namaDokumen}' berhasil diperbaiki dan statusnya kini 'Sudah Diperbaiki' untuk ditinjau kembali oleh Gugus Penjaminan Mutu (GPM) Fakultas sebelum diteruskan ke LPM."
                : (($oldDoc['status_review'] === 'draft')
                    ? "Dokumen '{$namaDokumen}' berhasil diajukan ke GPM Fakultas untuk direview."
                    : "Dokumen '{$namaDokumen}' berhasil diperbarui.");

            Auth::setFlash('success', $pesanSukses);
            redirect('prodi/dokumen');
            return;
        }

        // ==========================================
        // 5. MODE CREATE (SINGLE INSERT FALLBACK)
        // ==========================================
        $hasFiles = !empty($uploadedFilesList);
        $hasLinks = !empty($rawSingleLinks);

        if ($hasFiles && $hasLinks) {
            $jenisUpload = 'kombinasi';
        } elseif ($hasFiles) {
            $jenisUpload = 'file';
        } elseif ($hasLinks) {
            $jenisUpload = 'link';
        } else {
            $jenisUpload = trim($_POST['jenis_upload'] ?? 'file');
        }

        if (!$isDraft) {
            if (!$hasFiles && !$hasLinks) {
                Auth::setFlash('danger', 'Harap unggah minimal 1 berkas file dokumen mutu atau masukkan tautan Google Drive.');
                redirect($redirectUrl);
                return;
            }
        }

        if ($hasFiles) {
            $primary = $uploadedFilesList[0];
            $filePath = $primary['file_path'];
            $fileSize = $primary['file_size'];
            $fileExt = $primary['file_extension'];
        } else {
            $filePath = null;
            $fileSize = null;
            $fileExt = null;
        }

        if ($hasLinks) {
            $externalLink = json_encode($rawSingleLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $externalLink = null;
        }

        $statusReview = $isDraft ? 'draft' : 'menunggu_review_gpm';
        $tahapReview  = $isDraft ? null : 'gpm';

        // Hubungkan ke ppepp_cycles untuk single mode
        $cycleId = !empty($_POST['cycle_id']) ? (int)$_POST['cycle_id'] : null;
        if (!$cycleId) {
            try {
                $stmtCheckCyc = $this->db->prepare("SELECT id FROM ppepp_cycles WHERE judul_siklus = ? AND prodi_id = ? AND deleted_at IS NULL LIMIT 1");
                $stmtCheckCyc->execute([$namaDokumen, $this->prodiId]);
                $foundCyc = $stmtCheckCyc->fetchColumn();
                if ($foundCyc) {
                    $cycleId = (int)$foundCyc;
                } else {
                    $stmtInsCyc = $this->db->prepare("
                        INSERT INTO ppepp_cycles (judul_siklus, level, prodi_id, fakultas_id, bidang_id, sub_bidang_id, tahun_akademik, deskripsi, created_by, created_at)
                        VALUES (?, 'prodi', ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmtInsCyc->execute([
                        $namaDokumen, $this->prodiId, (int)($this->prodi['fakultas_id'] ?? 0),
                        $bidangId, $subBidangId, $tahunAkademik, $deskripsi, Auth::id()
                    ]);
                    $cycleId = (int)$this->db->lastInsertId();
                }
            } catch (\PDOException $e) {
                $cycleId = null;
            }
        } else {
            try {
                $this->db->prepare("UPDATE ppepp_cycles SET updated_at = NOW() WHERE id = ?")->execute([$cycleId]);
            } catch (\PDOException $e) {}
        }

        $stmt = $this->db->prepare("
            INSERT INTO ppepp_documents 
            (cycle_id, prodi_id, fakultas_id, level, user_id, bidang_id, sub_bidang_id, nama_dokumen, nomor_dokumen, siklus, tahun_akademik, 
             tanggal_berlaku_mulai, tanggal_berlaku_selesai, jenis_upload, file_path, file_size, 
             file_extension, external_link, public_page_limit, can_download_public, status_review, tahap_review, deskripsi)
            VALUES (?, ?, ?, 'prodi', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $cycleId, $this->prodiId, (int)($this->prodi['fakultas_id'] ?? 0), Auth::id(), $bidangId, $subBidangId, $namaDokumen, $nomorDokumen,
            $siklus, $tahunAkademik, $startDate, $endDate, $jenisUpload,
            $filePath, $fileSize, $fileExt, $externalLink,
            $publicPageLimit, $canDownloadPublic, $statusReview, $tahapReview, $deskripsi
        ]);
        $newId = (int)$this->db->lastInsertId();

        // Save all uploaded files to ppepp_document_files with sub_bidang_ids and pivot sync
        if (!empty($uploadedFilesList)) {
            $stmtInsF = $this->db->prepare("
                INSERT INTO ppepp_document_files (document_id, file_name, file_path, file_size, file_extension, narasi, sub_bidang_ids, sort_order, is_page_limited, public_page_limit, can_download_public, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $newIsLim = $_POST['new_file_is_limited'] ?? [];
            $newPLim = $_POST['new_file_page_limit'] ?? [];
            $newCanDl = $_POST['new_file_can_download'] ?? [];
            $newSubs = $_POST['new_file_sub_bidang'] ?? [];
            $sortOrder = 1;

            foreach ($uploadedFilesList as $uIdx => $uFile) {
                $fIsLimVal = isset($newIsLim[$uIdx]) ? (!empty($newIsLim[$uIdx]) ? 1 : 0) : ($isPageLimited ? 1 : 0);
                $fPLimVal = isset($newPLim[$uIdx]) ? max(1, (int)$newPLim[$uIdx]) : ($isPageLimited ? $publicPageLimit : 1);
                $fCanDlVal = isset($newCanDl[$uIdx]) ? (!empty($newCanDl[$uIdx]) ? 1 : 0) : $canDownloadPublic;
                $fSubArray = !empty($newSubs[$uIdx]) && is_array($newSubs[$uIdx])
                    ? array_values(array_filter(array_map('intval', $newSubs[$uIdx])))
                    : ($subBidangId ? [(int)$subBidangId] : []);
                $fSubsJson = json_encode($fSubArray);

                $stmtInsF->execute([
                    $newId, $uFile['file_name'], $uFile['file_path'], $uFile['file_size'], $uFile['file_extension'],
                    $uFile['narasi'] ?? null, $fSubsJson, $sortOrder++,
                    $fIsLimVal, $fPLimVal, $fCanDlVal
                ]);
                $newFileId = (int)$this->db->lastInsertId();
                sync_file_sub_bidang($this->db, $newFileId, $fSubArray);
                if (!empty($fSubArray)) {
                    $allDocSubIds = array_merge($allDocSubIds, $fSubArray);
                }
            }
        }

        $allDocSubIds = array_values(array_unique(array_filter(array_map('intval', $allDocSubIds))));
        if (empty($allDocSubIds) && !empty($subBidangId)) {
            $allDocSubIds = [(int)$subBidangId];
        }

        // Sinkronisasi seluruh sub bidang ke dokumen induk
        sync_document_sub_bidang($this->db, $newId, $allDocSubIds);

        AuditLogger::log('CREATE', 'Dokumen PPEPP', (string)$newId, $namaDokumen, null, [
            'nama' => $namaDokumen,
            'siklus' => $siklus,
            'bidang_id' => $bidangId,
            'sub_bidang_id' => $subBidangId,
            'jenis' => $jenisUpload,
            'status_review' => $statusReview,
            'files_count' => count($uploadedFilesList),
            'public_page_limit' => $publicPageLimit,
            'can_download_public' => $canDownloadPublic
        ], $this->prodiId, $this->prodi['nama_prodi']);

        if ($isDraft) {
            Auth::setFlash('success', "Dokumen '{$namaDokumen}' berhasil disimpan sebagai draf.");
            redirect('prodi/draft');
        } else {
            Auth::setFlash('success', "Dokumen mutu '{$namaDokumen}' berhasil diunggah dan diajukan ke GPM Fakultas (" . ($jenisUpload === 'file' ? count($uploadedFilesList) . ' berkas file' : 'Link Google Drive') . ").");
            redirect('prodi/dokumen');
        }
    }

    /**
     * Soft Delete Dokumen (Sesuai instruksi khusus: Jangan langsung dihapus permanen, masuk ke riwayat/arsip)
     */
    public function softDeleteDocument(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND prodi_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id, $this->prodiId]);
        $doc = $stmt->fetch();

        if ($doc) {
            $stmtDel = $this->db->prepare("UPDATE ppepp_documents SET deleted_at = NOW() WHERE id = ?");
            $stmtDel->execute([$id]);

            AuditLogger::log('DELETE', 'Dokumen PPEPP', (string)$id, $doc['nama_dokumen'], $doc, ['status' => 'soft_deleted'], $this->prodiId, $this->prodi['nama_prodi']);
            Auth::setFlash('warning', "Dokumen '{$doc['nama_dokumen']}' telah dipindahkan ke riwayat arsip (Soft Delete).");
        }

        redirect('prodi/dokumen');
    }

    /**
     * Restore Dokumen Terhapus dari Arsip
     */
    public function restoreDocument(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND prodi_id = ? AND deleted_at IS NOT NULL");
        $stmt->execute([$id, $this->prodiId]);
        $doc = $stmt->fetch();

        if ($doc) {
            $stmtRest = $this->db->prepare("UPDATE ppepp_documents SET deleted_at = NULL WHERE id = ?");
            $stmtRest->execute([$id]);

            AuditLogger::log('RESTORE', 'Dokumen PPEPP', (string)$id, $doc['nama_dokumen'], null, ['status' => 'restored'], $this->prodiId, $this->prodi['nama_prodi']);
            Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' berhasil dipulihkan dari arsip.");
        }

        redirect('prodi/dokumen/arsip');
    }

    /**
     * Hapus Dokumen Secara Permanen dari Arsip (Hard Delete)
     */
    public function forceDeleteDocument(string $id): void {
        $stmt = $this->db->prepare("SELECT * FROM ppepp_documents WHERE id = ? AND prodi_id = ? AND deleted_at IS NOT NULL");
        $stmt->execute([$id, $this->prodiId]);
        $doc = $stmt->fetch();

        if ($doc) {
            // 1. Ambil seluruh file lampiran di ppepp_document_files
            $stmtFiles = $this->db->prepare("SELECT file_path FROM ppepp_document_files WHERE document_id = ?");
            $stmtFiles->execute([$id]);
            $files = $stmtFiles->fetchAll();

            foreach ($files as $f) {
                if (!empty($f['file_path'])) {
                    $realPath = ROOT_PATH . '/' . ltrim($f['file_path'], '/\\');
                    if (file_exists($realPath) && is_file($realPath)) {
                        @unlink($realPath);
                    }
                }
            }

            // 2. Berkas lama di ppepp_documents.file_path jika ada
            if (!empty($doc['file_path'])) {
                $legacyPath = ROOT_PATH . '/' . ltrim($doc['file_path'], '/\\');
                if (file_exists($legacyPath) && is_file($legacyPath)) {
                    @unlink($legacyPath);
                }
            }

            // 3. Hapus record file
            $this->db->prepare("DELETE FROM ppepp_document_files WHERE document_id = ?")->execute([$id]);

            // 4. Hapus data dokumen utama
            $this->db->prepare("DELETE FROM ppepp_documents WHERE id = ?")->execute([$id]);

            // 5. Catat ke audit trail
            AuditLogger::log(
                'DELETE',
                'Dokumen PPEPP (Permanen)',
                (string)$id,
                $doc['nama_dokumen'],
                $doc,
                null,
                $this->prodiId,
                $this->prodi['nama_prodi'] ?? null
            );

            Auth::setFlash('success', "Dokumen '{$doc['nama_dokumen']}' dan seluruh berkas lampirannya telah berhasil dihapus secara permanen.");
        } else {
            Auth::setFlash('danger', "Dokumen tidak ditemukan dalam arsip atau Anda tidak memiliki akses.");
        }

        redirect('prodi/dokumen/arsip');
    }

    /**
     * Tab Arsip Dokumen Terhapus (Soft Deleted Archive)
     */
    public function arsip(): void {
        $stmt = $this->db->prepare("
            SELECT d.*, b.nama_bidang, u.name as uploader_name
            FROM ppepp_documents d
            LEFT JOIN bidang_standar b ON d.bidang_id = b.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.prodi_id = ? AND d.deleted_at IS NOT NULL
            ORDER BY d.deleted_at DESC
        ");
        $stmt->execute([$this->prodiId]);
        $archivedDocs = $stmt->fetchAll();
        $this->attachFilesToDocs($archivedDocs);

        $this->render('admin_prodi/arsip', [
            'pageTitle' => 'Arsip Dokumen Terhapus',
            'prodi' => $this->prodi,
            'archivedDocs' => $archivedDocs
        ]);
    }

    /**
     * Kelola Profil Ketua Program Studi
     */
    public function kaprodi(): void {
        $this->render('admin_prodi/kaprodi', [
            'pageTitle' => 'Kelola Informasi Ketua Program Studi',
            'prodi' => $this->prodi
        ]);
    }

    public function saveKaprodi(): void {
        $namaKaprodi = trim($_POST['nama_kaprodi'] ?? '');
        $nidnKaprodi = trim($_POST['nidn_kaprodi'] ?? '');
        $namaSekprodi = trim($_POST['nama_sekprodi'] ?? '');
        $nidnSekprodi = trim($_POST['nidn_sekprodi'] ?? '');

        if (empty($namaKaprodi)) {
            Auth::setFlash('danger', 'Nama Ketua Program Studi wajib diisi.');
            redirect('prodi/kaprodi');
        }

        $stmtOld = $this->db->prepare("SELECT nama_kaprodi, nidn_kaprodi, nama_sekprodi, nidn_sekprodi FROM prodis WHERE id = ?");
        $stmtOld->execute([$this->prodiId]);
        $oldData = $stmtOld->fetch();

        $stmt = $this->db->prepare("UPDATE prodis SET nama_kaprodi = ?, nidn_kaprodi = ?, nama_sekprodi = ?, nidn_sekprodi = ? WHERE id = ?");
        $stmt->execute([$namaKaprodi, $nidnKaprodi, $namaSekprodi, $nidnSekprodi, $this->prodiId]);

        AuditLogger::log('UPDATE', 'Profil Kaprodi & Sekprodi', (string)$this->prodiId, $this->prodi['nama_prodi'], $oldData, [
            'nama_kaprodi' => $namaKaprodi,
            'nidn_kaprodi' => $nidnKaprodi,
            'nama_sekprodi' => $namaSekprodi,
            'nidn_sekprodi' => $nidnSekprodi
        ], $this->prodiId, $this->prodi['nama_prodi']);

        Auth::setFlash('success', 'Informasi Ketua dan Sekretaris Program Studi berhasil diperbarui.');
        redirect('prodi/kaprodi');
    }
}
