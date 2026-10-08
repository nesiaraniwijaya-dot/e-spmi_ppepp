<?php
/**
 * Application Constants & Configuration
 * SPMI PPEPP UNIKA Soegijapranata (SCU)
 */

if (!defined('APP_NAME')) {
    define('APP_NAME', 'MITRA');
    define('APP_LONG_NAME', 'Sistem Informasi Penjaminan Mutu Internal (PPEPP)');
    define('INSTITUTION_NAME', 'Universitas Katolik Soegijapranata');
    define('INSTITUTION_SHORT', 'UNIKA Soegijapranata / SCU');
    define('LPM_NAME', 'Lembaga Penjaminan Mutu (LPM)');
    define('APP_VERSION', '1.0.0');

    // Base URL Detection (Supports direct SSL, Reverse Proxy, & Cloudflare)
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https'));
    $protocol = $isHttps ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $baseUrl = rtrim($protocol . $host . $scriptName, '/');
    define('BASE_URL', $baseUrl);

    // Paths
    define('ROOT_PATH', dirname(__DIR__));
    define('UPLOAD_PATH', ROOT_PATH . '/uploads');
    define('DOC_UPLOAD_PATH', UPLOAD_PATH . '/documents');
    define('LOGO_UPLOAD_PATH', UPLOAD_PATH . '/logos');
    define('AVATAR_UPLOAD_PATH', UPLOAD_PATH . '/avatars');

    // Ensure upload directories exist
    if (!is_dir(UPLOAD_PATH)) @mkdir(UPLOAD_PATH, 0777, true);
    if (!is_dir(DOC_UPLOAD_PATH)) @mkdir(DOC_UPLOAD_PATH, 0777, true);
    if (!is_dir(LOGO_UPLOAD_PATH)) @mkdir(LOGO_UPLOAD_PATH, 0777, true);
    if (!is_dir(AVATAR_UPLOAD_PATH)) @mkdir(AVATAR_UPLOAD_PATH, 0777, true);

    // Load env variables if available
    $envConfig = file_exists(__DIR__ . '/env.php') ? (require __DIR__ . '/env.php') : [];

    // Google SSO (OAuth 2.0) Configuration
    define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: ($envConfig['GOOGLE_CLIENT_ID'] ?? ''));
    define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: ($envConfig['GOOGLE_CLIENT_SECRET'] ?? ''));
    define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth/google/callback');
}

// Global Helper Functions
function base_url(string $path = ''): string {
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

function asset(string $path): string {
    $fullPath = ROOT_PATH . '/assets/' . ltrim($path, '/');
    $version = file_exists($fullPath) ? filemtime($fullPath) : '1.0';
    return BASE_URL . '/assets/' . ltrim($path, '/') . '?v=' . $version;
}

function redirect(string $path): void {
    header("Location: " . base_url($path));
    exit;
}

function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function format_date(?string $date, string $format = 'd M Y'): string {
    if (!$date) return '-';
    return date($format, strtotime($date));
}

function format_file_size(mixed $bytes): string {
    if (empty($bytes)) return 'PDF';
    if (!is_numeric($bytes)) return (string)$bytes;
    $bytes = (float)$bytes;
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
}

function siklus_badge(string $siklus): string {
    $colors = [
        'penetapan' => 'badge-penetapan',
        'pelaksanaan' => 'badge-pelaksanaan',
        'evaluasi' => 'badge-evaluasi',
        'pengendalian' => 'badge-pengendalian',
        'peningkatan' => 'badge-peningkatan',
    ];
    $labels = [
        'penetapan' => 'Penetapan (P1)',
        'pelaksanaan' => 'Pelaksanaan (P2)',
        'evaluasi' => 'Evaluasi (E)',
        'pengendalian' => 'Pengendalian (P3)',
        'peningkatan' => 'Peningkatan (P4)',
    ];
    $cls = $colors[$siklus] ?? 'badge-secondary';
    $lbl = $labels[$siklus] ?? ucfirst($siklus);
    return "<span class=\"badge {$cls}\">{$lbl}</span>";
}

function review_status_badge(string $status): string {
    return match($status) {
        'perlu_perbaikan' => '<span class="badge rounded-pill badge-status-perbaikan px-2.5 py-1.5" style="background:#FEE2E2 !important; color:#991B1B !important; border:1px solid #FCA5A5 !important; font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:0.35rem;"><i class="fas fa-triangle-exclamation" style="color:#DC2626 !important;"></i> Perlu Perbaikan</span>',
        'sudah_diperbaiki' => '<span class="badge rounded-pill badge-status-review-ulang px-2.5 py-1.5" style="background:#FEF3C7 !important; color:#92400E !important; border:1px solid #FCD34D !important; font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:0.35rem;"><i class="fas fa-rotate" style="color:#D97706 !important;"></i> Perlu Review Ulang</span>',
        'sesuai' => '<span class="badge rounded-pill badge-status-sesuai px-2.5 py-1.5" style="background:#DCFCE7 !important; color:#166534 !important; border:1px solid #86EFAC !important; font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:0.35rem;"><i class="fas fa-circle-check" style="color:#15803D !important;"></i> Sesuai Standar</span>',
        'draft' => '<span class="badge rounded-pill badge-status-draft px-2.5 py-1.5" style="background:#F1F5F9 !important; color:#334155 !important; border:1px solid #CBD5E1 !important; font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:0.35rem;"><i class="fas fa-pencil" style="color:#64748B !important;"></i> Draf Dokumen</span>',
        default => '<span class="badge rounded-pill badge-status-belum px-2.5 py-1.5" style="background:#FFFBEB !important; color:#92400E !important; border:1px solid #FDE68A !important; font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:0.35rem;"><i class="fas fa-clock" style="color:#D97706 !important;"></i> Belum Direview</span>'
    };
}

function parse_external_links(?string $raw): array {
    if (!$raw) return [];
    $raw = trim($raw);
    if ($raw === '') return [];

    if (str_starts_with($raw, '[') || str_starts_with($raw, '{')) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $result = [];
            foreach ($decoded as $item) {
                if (is_array($item) && !empty($item['url'])) {
                    $result[] = [
                        'url' => trim($item['url']),
                        'narasi' => trim($item['narasi'] ?? ''),
                        'can_download_public' => (int)($item['can_download_public'] ?? 0),
                        'sub_bidang_ids' => !empty($item['sub_bidang_ids']) && is_array($item['sub_bidang_ids']) 
                            ? array_values(array_filter(array_map('intval', $item['sub_bidang_ids']))) 
                            : []
                    ];
                } elseif (is_string($item) && trim($item) !== '') {
                    $result[] = [
                        'url' => trim($item),
                        'narasi' => '',
                        'can_download_public' => 0,
                        'sub_bidang_ids' => []
                    ];
                }
            }
            if (!empty($result)) return $result;
        }
    }

    $lines = preg_split('/[\r\n]+/', $raw);
    $result = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $result[] = [
                'url' => $line,
                'narasi' => '',
                'can_download_public' => 0,
                'sub_bidang_ids' => []
            ];
        }
    }
    return $result;
}

function is_user_logged_in(): bool {
    return class_exists('Auth') && Auth::check();
}

/**
 * Mendapatkan seluruh konfigurasi beranda publik (cache per request)
 */
function get_landing_settings(): array {
    static $settingsCache = null;
    if ($settingsCache !== null) {
        return $settingsCache;
    }
    $settingsCache = [];
    try {
        if (class_exists('Database')) {
            $db = Database::getInstance()->getConnection();
            $rows = $db->query("SELECT setting_key, setting_value FROM landing_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
            if (is_array($rows)) {
                $settingsCache = $rows;
            }
        }
    } catch (\Throwable $e) {
        $settingsCache = [];
    }
    return $settingsCache;
}

/**
 * Mendapatkan nilai pengaturan beranda dengan fallback default aman
 */
function get_landing_setting(string $key, string $default = ''): string {
    $settings = get_landing_settings();
    if (isset($settings[$key]) && trim($settings[$key]) !== '') {
        return $settings[$key];
    }
    return $default;
}

/**
 * Mendapatkan peta seluruh sub bidang standar terindeks ID
 */
function get_all_sub_bidang_map(): array {
    static $mapCache = null;
    if ($mapCache !== null) return $mapCache;
    $mapCache = [];
    try {
        if (class_exists('Database')) {
            $db = Database::getInstance()->getConnection();
            $rows = $db->query("
                SELECT sb.id, sb.bidang_id, sb.kode_sub_bidang, sb.nama_sub_bidang, b.nama_bidang, b.kode_bidang
                FROM sub_bidang_standar sb
                JOIN bidang_standar b ON sb.bidang_id = b.id
                WHERE sb.is_active = 1
                ORDER BY b.id ASC, sb.id ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $mapCache[(int)$r['id']] = $r;
            }
        }
    } catch (\Throwable $e) {
        $mapCache = [];
    }
    return $mapCache;
}

/**
 * Render label badge standar mutu (Nama standar langsung tanpa kode)
 */
function render_sub_standar_badges($subIds = null, array $customMap = null): string {
    if (empty($subIds)) return '';
    if (is_string($subIds)) {
        $decoded = json_decode($subIds, true);
        if (is_array($decoded)) {
            $subIds = $decoded;
        } elseif (is_numeric($subIds) || !empty(trim($subIds))) {
            $subIds = [(int)$subIds];
        } else {
            return '';
        }
    } elseif (is_numeric($subIds)) {
        $subIds = [(int)$subIds];
    }
    if (!is_array($subIds) || empty($subIds)) return '';

    $map = $customMap ?? get_all_sub_bidang_map();
    $html = '';
    foreach ($subIds as $sId) {
        $sId = (int)$sId;
        if (!isset($map[$sId])) continue;
        $item = $map[$sId];
        $name = htmlspecialchars($item['nama_sub_bidang'] ?? '');
        $html .= '<span class="badge rounded-pill px-2.5 py-1 d-inline-flex align-items-center border shadow-2xs me-1.5 mb-1" style="background:#EFF6FF; color:#1D4ED8; border-color:#BFDBFE !important; font-size:0.75rem; font-weight:600; line-height:1.4;" title="' . $name . '"><i class="fas fa-bookmark text-primary opacity-75 me-1.5 flex-shrink-0" style="font-size:0.7rem;"></i><span>' . $name . '</span></span>';
    }
    return $html;
}

/**
 * Analisis adaptif standar SPMI dan bidang untuk sebuah dokumen
 * Mengumpulkan seluruh sub_bidang_ids dari file lampiran, link eksternal, atau dokumen induk
 * dan mengembalikan HTML adaptif yang rapi agar tidak menumpuk di baris tabel.
 */
function get_doc_adaptive_standards_and_bidang(array $doc, array $subBidangMap = null): array {
    $map = $subBidangMap ?? get_all_sub_bidang_map();
    $files = $doc['files'] ?? [];
    $gdriveLinks = parse_external_links($doc['external_link'] ?? '');
    
    $fileBreakdown = [];
    $allSubIds = [];
    $bidangIds = [];

    if (!empty($doc['bidang_id'])) {
        $bidangIds[] = (int)$doc['bidang_id'];
    }

    if (!empty($files)) {
        foreach ($files as $idx => $f) {
            $fName = $f['file_name'] ?? ('Berkas ' . ($idx + 1));
            $rawSubs = $f['sub_bidang_ids'] ?? null;
            $subIds = [];
            if (!empty($rawSubs)) {
                $subIds = is_array($rawSubs) ? $rawSubs : json_decode($rawSubs, true);
                if (!is_array($subIds)) $subIds = is_numeric($rawSubs) ? [(int)$rawSubs] : [];
            }
            $subIds = array_values(array_filter(array_map('intval', $subIds)));
            if (empty($subIds) && !empty($doc['sub_bidang_id'])) {
                $subIds = [(int)$doc['sub_bidang_id']];
            }
            $fileBreakdown[] = [
                'type' => 'file',
                'title' => $fName,
                'sub_ids' => $subIds
            ];
            foreach ($subIds as $sid) {
                $allSubIds[] = $sid;
            }
        }
    }

    if (!empty($gdriveLinks)) {
        foreach ($gdriveLinks as $idx => $l) {
            $lTitle = !empty($l['narasi']) ? $l['narasi'] : ('Tautan GDrive #' . ($idx + 1));
            $subIds = $l['sub_bidang_ids'] ?? [];
            if (!is_array($subIds)) $subIds = [];
            $subIds = array_values(array_filter(array_map('intval', $subIds)));
            if (empty($subIds) && !empty($doc['sub_bidang_id'])) {
                $subIds = [(int)$doc['sub_bidang_id']];
            }
            $fileBreakdown[] = [
                'type' => 'link',
                'title' => $lTitle,
                'sub_ids' => $subIds
            ];
            foreach ($subIds as $sid) {
                $allSubIds[] = $sid;
            }
        }
    }

    if (empty($fileBreakdown) && !empty($doc['sub_bidang_id'])) {
        $sid = (int)$doc['sub_bidang_id'];
        $allSubIds[] = $sid;
        $fileBreakdown[] = [
            'type' => 'doc',
            'title' => $doc['nama_dokumen'] ?? 'Dokumen',
            'sub_ids' => [$sid]
        ];
    }

    $allSubIds = array_values(array_unique(array_filter($allSubIds)));

    $uniqueStandards = [];
    $uniqueBidangs = [];
    foreach ($allSubIds as $sId) {
        if (isset($map[$sId])) {
            $sName = $map[$sId]['nama_sub_bidang'] ?? '';
            $bName = $map[$sId]['nama_bidang'] ?? '';
            $bId = (int)($map[$sId]['bidang_id'] ?? 0);
            if ($sName) $uniqueStandards[$sId] = $sName;
            if ($bName && !in_array($bName, $uniqueBidangs, true)) {
                $uniqueBidangs[] = $bName;
            }
            if ($bId > 0 && !in_array($bId, $bidangIds, true)) {
                $bidangIds[] = $bId;
            }
        }
    }

    if (empty($uniqueBidangs) && !empty($doc['nama_bidang'])) {
        $uniqueBidangs[] = $doc['nama_bidang'];
    }

    // 1. Render Standards HTML (Adaptive Pill & Dropdown)
    $standardsHtml = '';
    $stdCount = count($uniqueStandards);
    if ($stdCount === 1) {
        $firstId = array_key_first($uniqueStandards);
        $firstName = htmlspecialchars($uniqueStandards[$firstId]);
        $standardsHtml = '<span class="badge rounded-pill px-2.5 py-1 d-inline-flex align-items-center border shadow-2xs me-1 mb-1" style="background:#EFF6FF; color:#1D4ED8; border-color:#BFDBFE !important; font-size:0.74rem; font-weight:600; line-height:1.4;" title="' . $firstName . '"><i class="fas fa-bookmark text-primary opacity-75 me-1.5 flex-shrink-0" style="font-size:0.7rem;"></i><span class="text-truncate" style="max-width:240px;">' . $firstName . '</span></span>';
    } elseif ($stdCount > 1) {
        $firstId = array_key_first($uniqueStandards);
        $firstName = htmlspecialchars($uniqueStandards[$firstId]);
        $remainingCount = $stdCount - 1;

        $dropdownItemsHtml = '';
        foreach ($fileBreakdown as $fb) {
            $fTitle = htmlspecialchars($fb['title']);
            $fIcon = $fb['type'] === 'file' ? 'fa-file-pdf text-danger' : 'fa-link text-primary';
            $fStdBadges = '';
            if (!empty($fb['sub_ids'])) {
                foreach ($fb['sub_ids'] as $sId) {
                    if (isset($map[$sId])) {
                        $fStdBadges .= '<span class="badge bg-light text-primary border me-1 mb-1" style="font-size:0.7rem;"><i class="fas fa-bookmark me-1 opacity-75"></i>' . htmlspecialchars($map[$sId]['nama_sub_bidang']) . '</span>';
                    }
                }
            } else {
                $fStdBadges = '<span class="badge bg-light text-muted border me-1 mb-1" style="font-size:0.7rem;">Standar Umum</span>';
            }

            $dropdownItemsHtml .= '<li class="mb-2 pb-2 border-bottom border-light">';
            $dropdownItemsHtml .= '<div class="fw-semibold text-dark text-truncate mb-1" style="font-size:0.75rem;" title="' . $fTitle . '"><i class="fas ' . $fIcon . ' me-1"></i>' . $fTitle . '</div>';
            $dropdownItemsHtml .= '<div class="d-flex flex-wrap align-items-center">' . $fStdBadges . '</div>';
            $dropdownItemsHtml .= '</li>';
        }

        $standardsHtml = '<div class="d-inline-flex flex-wrap align-items-center gap-1 mt-1">';
        $standardsHtml .= '<span class="badge rounded-pill px-2.5 py-1 d-inline-flex align-items-center border shadow-2xs me-0.5" style="background:#EFF6FF; color:#1D4ED8; border-color:#BFDBFE !important; font-size:0.74rem; font-weight:600; line-height:1.4;" title="' . $firstName . '"><i class="fas fa-bookmark text-primary opacity-75 me-1.5 flex-shrink-0" style="font-size:0.7rem;"></i><span class="text-truncate" style="max-width:200px;">' . $firstName . '</span></span>';
        $standardsHtml .= '<div class="dropdown d-inline-block">';
        $standardsHtml .= '<button type="button" class="badge rounded-pill px-2.5 py-1 d-inline-flex align-items-center border shadow-2xs dropdown-toggle btn-link text-decoration-none" style="background:#F0FDF4; color:#15803D; border-color:#BBF7D0 !important; font-size:0.74rem; font-weight:600; cursor:pointer;" data-bs-toggle="dropdown" aria-expanded="false" title="Klik untuk melihat rincian standar per berkas">';
        $standardsHtml .= '<i class="fas fa-layer-group me-1.5 text-success" style="font-size:0.7rem;"></i><span>+' . $remainingCount . ' Standar Berkas</span>';
        $standardsHtml .= '</button>';
        $standardsHtml .= '<ul class="dropdown-menu dropdown-menu-start shadow-lg border-0 p-2.5 rounded-3" style="min-width: 290px; max-width: min(92vw, 380px); font-size: 0.78rem;">';
        $standardsHtml .= '<li class="dropdown-header text-muted fw-bold px-1 py-1" style="font-size: 0.68rem; letter-spacing: 0.4px;">RINCIAN STANDAR PER BERKAS:</li>';
        $standardsHtml .= $dropdownItemsHtml;
        $standardsHtml .= '</ul>';
        $standardsHtml .= '</div>';
        $standardsHtml .= '</div>';
    }

    // 2. Render Bidang HTML (Single vs Multi-Bidang Dropdown)
    $bidangHtml = '';
    $bCount = count($uniqueBidangs);
    if ($bCount <= 1) {
        $singleBidang = !empty($uniqueBidangs) ? $uniqueBidangs[0] : ($doc['nama_bidang'] ?: 'Umum / Lainnya');
        $bidangHtml = '<div class="fw-semibold text-dark mb-0.5" style="font-size:0.85rem;">' . htmlspecialchars($singleBidang) . '</div>';
    } else {
        $bidangItemsHtml = '';
        foreach ($uniqueBidangs as $bName) {
            $bidangItemsHtml .= '<li class="px-2 py-1 text-dark fw-semibold d-flex align-items-center gap-2" style="font-size:0.76rem;"><i class="fas fa-check-circle text-success" style="font-size:0.7rem;"></i><span>' . htmlspecialchars($bName) . '</span></li>';
        }

        $bidangHtml = '<div class="d-flex flex-column align-items-start gap-1">';
        $bidangHtml .= '<div class="dropdown d-inline-block">';
        $bidangHtml .= '<button type="button" class="badge rounded-pill px-2.5 py-1 d-inline-flex align-items-center border shadow-2xs dropdown-toggle btn-link text-decoration-none" style="background:#F5F3FF; color:#6D28D9; border-color:#DDD6FE !important; font-size:0.74rem; font-weight:600; cursor:pointer;" data-bs-toggle="dropdown" aria-expanded="false" title="Klik untuk rincian bidang yang tercakup">';
        $bidangHtml .= '<i class="fas fa-cubes me-1.5 text-purple" style="font-size:0.7rem;"></i><span>Multi-Bidang (' . $bCount . ')</span>';
        $bidangHtml .= '</button>';
        $bidangHtml .= '<ul class="dropdown-menu dropdown-menu-start shadow-lg border-0 p-2.5 rounded-3" style="min-width: 250px; font-size: 0.78rem;">';
        $bidangHtml .= '<li class="dropdown-header text-muted fw-bold px-1 py-1" style="font-size: 0.68rem;">BIDANG TERCAKUP:</li>';
        $bidangHtml .= $bidangItemsHtml;
        $bidangHtml .= '</ul>';
        $bidangHtml .= '</div>';
        $bidangHtml .= '<div class="text-muted text-truncate" style="font-size: 0.72rem; max-width: 175px;" title="' . htmlspecialchars(implode(', ', $uniqueBidangs)) . '">' . htmlspecialchars(implode(', ', $uniqueBidangs)) . '</div>';
        $bidangHtml .= '</div>';
    }

    return [
        'standards_count' => $stdCount,
        'bidang_count' => $bCount,
        'standards_html' => $standardsHtml,
        'bidang_html' => $bidangHtml,
        'bidang_ids' => array_values(array_unique(array_filter($bidangIds))),
        'standards_text' => implode(' ', $uniqueStandards),
        'unique_standards' => $uniqueStandards,
        'unique_bidangs' => $uniqueBidangs
    ];
}

/**
 * Sinkronisasi sub bidang untuk berkas ppepp_document_files
 */
function sync_file_sub_bidang(PDO $pdo, int $fileId, array $subBidangIds): void {
    $subBidangIds = array_values(array_unique(array_filter(array_map('intval', $subBidangIds))));
    $pdo->prepare("UPDATE ppepp_document_files SET sub_bidang_ids = ? WHERE id = ?")
        ->execute([json_encode($subBidangIds), $fileId]);

    $pdo->prepare("DELETE FROM ppepp_document_file_sub_bidang WHERE document_file_id = ?")->execute([$fileId]);
    if (!empty($subBidangIds)) {
        $stmtIns = $pdo->prepare("INSERT INTO ppepp_document_file_sub_bidang (document_file_id, sub_bidang_id) VALUES (?, ?)");
        foreach ($subBidangIds as $sId) {
            $stmtIns->execute([$fileId, $sId]);
        }
    }
}

/**
 * Sinkronisasi seluruh sub bidang untuk dokumen induk ppepp_documents
 */
function sync_document_sub_bidang(PDO $pdo, int $documentId, array $subBidangIds): void {
    $subBidangIds = array_values(array_unique(array_filter(array_map('intval', $subBidangIds))));
    $pdo->prepare("DELETE FROM ppepp_document_sub_bidang WHERE document_id = ?")->execute([$documentId]);
    if (!empty($subBidangIds)) {
        $stmtIns = $pdo->prepare("INSERT INTO ppepp_document_sub_bidang (document_id, sub_bidang_id) VALUES (?, ?)");
        foreach ($subBidangIds as $sId) {
            $stmtIns->execute([$documentId, $sId]);
        }
    }
}

/**
 * Render HTML picker pemilih standar mutu untuk file / link (tanpa kode, responsif, terfilter per bidang)
 */
function render_sub_standar_picker_html(string $inputName, array $selectedIds = [], string $uniqueKey = '', array $bidangList = [], array $subBidangList = [], ?int $currentBidangId = null): string {
    if (empty($uniqueKey)) $uniqueKey = 'picker_' . bin2hex(random_bytes(4));
    $selectedIds = array_values(array_filter(array_map('intval', $selectedIds)));

    if (empty($bidangList) || empty($subBidangList)) {
        $allSubs = get_all_sub_bidang_map();
        if (!empty($allSubs)) {
            $subBidangList = array_values($allSubs);
            $bidangSeen = [];
            foreach ($subBidangList as $sb) {
                if (!isset($bidangSeen[$sb['bidang_id']])) {
                    $bidangSeen[$sb['bidang_id']] = [
                        'id' => $sb['bidang_id'],
                        'kode_bidang' => $sb['kode_bidang'] ?? '',
                        'nama_bidang' => $sb['nama_bidang'] ?? ''
                    ];
                }
            }
            $bidangList = array_values($bidangSeen);
        }
    }

    // Filter per bidang jika bidang dipilih
    if (!empty($currentBidangId)) {
        $cBidangId = (int)$currentBidangId;
        $bidangList = array_values(array_filter($bidangList, fn($b) => (int)$b['id'] === $cBidangId));
        $subBidangList = array_values(array_filter($subBidangList, fn($s) => (int)$s['bidang_id'] === $cBidangId));
        $validSubIds = array_map(fn($s) => (int)$s['id'], $subBidangList);
        $selectedIds = array_values(array_intersect($selectedIds, $validSubIds));
    }

    $subMap = [];
    foreach ($subBidangList as $sb) {
        $subMap[(int)$sb['id']] = $sb;
    }

    // Build badges HTML: langsung nama standar
    $badgesHtml = '';
    foreach ($selectedIds as $sId) {
        if (!isset($subMap[$sId])) continue;
        $sb = $subMap[$sId];
        $name = htmlspecialchars($sb['nama_sub_bidang']);
        $badgesHtml .= '
            <span class="badge rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5 border shadow-2xs me-1 mb-1 text-wrap text-start lh-sm" 
                  style="background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE !important; font-size: 0.78rem; font-weight: 600; max-width: 100%; word-break: break-word;" 
                  id="badge_' . $uniqueKey . '_' . $sId . '" title="' . $name . '">
                <i class="fas fa-bookmark text-primary opacity-75 flex-shrink-0" style="font-size: 0.7rem;"></i>
                <span>' . $name . '</span>
                <i class="fas fa-xmark cursor-pointer text-muted hover-danger ms-1 flex-shrink-0" 
                   onclick="removeSubStandarTag(\'' . $uniqueKey . '\', ' . $sId . ')" title="Hapus standar ini"></i>
            </span>
        ';
    }

    // Build dropdown list
    $optionsHtml = '';
    if (empty($currentBidangId) && empty($bidangList)) {
        $optionsHtml = '
            <div class="px-3 py-3 text-center text-muted small fst-italic no-bidang-notice">
                <i class="fas fa-arrow-left text-primary me-1"></i> Silakan pilih Bidang pada formulir dokumen terlebih dahulu
            </div>
        ';
    } else {
        foreach ($bidangList as $b) {
            $bId = (int)$b['id'];
            $subsInBidang = array_filter($subBidangList, fn($s) => (int)$s['bidang_id'] === $bId);
            if (!empty($subsInBidang)) {
                $optionsHtml .= '
                    <div class="px-2.5 py-1.5 mt-1.5 mb-1 fw-bold text-dark-blue rounded bg-light border-bottom d-flex align-items-center justify-content-between" style="font-size: 0.76rem; letter-spacing: 0.2px;">
                        <span class="text-truncate"><i class="fas fa-layer-group text-primary me-1"></i> ' . htmlspecialchars($b['nama_bidang']) . '</span>
                        <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill" style="font-size: 0.68rem;">' . count($subsInBidang) . '</span>
                    </div>
                ';
                foreach ($subsInBidang as $sb) {
                    $sbId = (int)$sb['id'];
                    $isChecked = in_array($sbId, $selectedIds, true) ? 'checked' : '';
                    $searchStr = strtolower($sb['nama_sub_bidang'] . ' ' . $b['nama_bidang']);
                    $optionsHtml .= '
                        <label class="dropdown-item d-flex align-items-start gap-2 py-2 px-2.5 rounded cursor-pointer sub-option-item" 
                               style="font-size: 0.8rem; white-space: normal;" 
                               data-search-text="' . htmlspecialchars($searchStr) . '">
                            <input type="checkbox" class="form-check-input flex-shrink-0 mt-0.5 sub-check-input" 
                                   name="' . htmlspecialchars($inputName) . '" value="' . $sbId . '" ' . $isChecked . ' 
                                   onchange="handleSubStandarCheckChange(\'' . $uniqueKey . '\', this, ' . $sbId . ', \'\', \'' . htmlspecialchars($sb['nama_sub_bidang'], ENT_QUOTES) . '\')">
                            <span class="lh-sm text-dark">' . htmlspecialchars($sb['nama_sub_bidang']) . '</span>
                        </label>
                    ';
                }
            } else {
                $optionsHtml .= '
                    <div class="px-3 py-3 text-center text-muted small fst-italic">
                        Belum ada standar mutu yang terdaftar untuk bidang ini.
                    </div>
                ';
            }
        }
    }

    $noSubPlaceholder = '<span class="text-muted fst-italic no-sub-placeholder" style="font-size: 0.78rem;">Belum ada standar dipilih (Bisa pilih 1 atau lebih)</span>';
    $buttonLabel = !empty($currentBidangId)
        ? '<i class="fas fa-plus-circle text-primary me-1.5"></i> Pilih / Tambah Standar...'
        : '<i class="fas fa-info-circle text-muted me-1.5"></i> Pilih Bidang terlebih dahulu...';

    return '
        <div class="sub-standar-picker-wrap p-3 rounded-3 bg-white border shadow-2xs mt-2" 
             id="picker_wrap_' . $uniqueKey . '" 
             data-picker-key="' . $uniqueKey . '" 
             data-input-name="' . htmlspecialchars($inputName) . '" 
             data-current-bidang="' . (!empty($currentBidangId) ? (int)$currentBidangId : '') . '">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-label fw-bold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                    <i class="fas fa-tags text-primary"></i>
                    <span>Standar Mutu Berkas Ini: <span class="text-danger">*</span></span>
                </label>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bisa > 1 Standar</span>
            </div>

            <!-- Selected Tags List -->
            <div class="selected-sub-badges d-flex flex-wrap gap-1 mb-2" id="badges_container_' . $uniqueKey . '">
                ' . ($badgesHtml ?: $noSubPlaceholder) . '
            </div>

            <!-- Dropdown Selector Button -->
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-start d-flex justify-content-between align-items-center py-2 px-3 rounded-3 dropdown-toggle-custom" 
                        id="btn_toggle_' . $uniqueKey . '"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="font-size: 0.82rem; border-style: dashed; border-width: 1.5px;">
                    <span class="text-truncate text-secondary dropdown-label-text">
                        ' . $buttonLabel . '
                    </span>
                    <i class="fas fa-chevron-down text-muted ms-1" style="font-size: 0.75rem;"></i>
                </button>
                <div class="dropdown-menu p-2.5 shadow-lg border rounded-3 w-100" style="max-height: 300px; overflow-y: auto; font-size: 0.8rem; z-index: 1060;">
                    <div class="px-1 pb-2 mb-1.5 border-bottom">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted px-2.5"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control py-1 px-2.5" placeholder="Cari nama standar..." onkeyup="filterSubStandarList(\'' . $uniqueKey . '\', this.value)">
                        </div>
                    </div>
                    <div class="sub-standar-options-list" id="options_list_' . $uniqueKey . '">
                        ' . $optionsHtml . '
                    </div>
                </div>
            </div>
        </div>
    ';
}



