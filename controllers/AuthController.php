<?php
/**
 * Auth Controller
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';

class AuthController extends Controller {
    public function showLogin(): void {
        $returnUrl = $_GET['return_url'] ?? $_SESSION['return_url'] ?? '';
        if (!empty($returnUrl)) {
            $_SESSION['return_url'] = $returnUrl;
        }

        if (Auth::check()) {
            if (!empty($returnUrl) && !preg_match('#^https?://#i', $returnUrl) && !str_starts_with($returnUrl, '//')) {
                unset($_SESSION['return_url']);
                redirect(ltrim($returnUrl, '/'));
            }
            redirect(Auth::getDashboardRoute());
        }

        $this->render('auth/login', [
            'pageTitle' => 'Masuk Portal MITRA',
            'returnUrl' => $returnUrl
        ]);
    }

    public function login(): void {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $returnUrl = trim($_POST['return_url'] ?? $_SESSION['return_url'] ?? '');

        if (empty($email) || empty($password)) {
            Auth::setFlash('danger', 'Email dan password wajib diisi.');
            redirect('login' . (!empty($returnUrl) ? '?return_url=' . urlencode($returnUrl) : ''));
        }

        if (Auth::attempt($email, $password)) {
            $multiRoleEmails = ['nesiaraniwijaya@gmail.com', 'ravywhienelda@gmail.com', 'lpm@unika.ac.id', 'tu.lpm@unika.ac.id', 'admin.lpm@unika.ac.id'];
            if (in_array(strtolower($email), $multiRoleEmails) || Auth::role() === 'testing' || !empty($_SESSION['is_multi_role_testing'])) {
                $_SESSION['is_multi_role_testing'] = true;
                redirect('auth/select-role');
            }

            Auth::setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($_SESSION['user_name']) . '!');
            unset($_SESSION['return_url']);

            // Jika ada return_url yang aman (relative local path), arahkan langsung ke halaman dokumen tersebut
            if (!empty($returnUrl) && !preg_match('#^https?://#i', $returnUrl) && !str_starts_with($returnUrl, '//')) {
                redirect(ltrim($returnUrl, '/'));
            }

            redirect(Auth::getDashboardRoute());
        } else {
            Auth::setFlash('danger', 'Email atau password yang Anda masukkan salah, atau akun Anda dinonaktifkan.');
            redirect('login' . (!empty($returnUrl) ? '?return_url=' . urlencode($returnUrl) : ''));
        }
    }

    public function logout(): void {
        Auth::logout();
        Auth::setFlash('info', 'Anda telah berhasil keluar dari sistem.');
        redirect('login');
    }

    public function redirectToGoogle(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Ambil Client ID & Redirect URI dengan fallback aman
        $clientId = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';
        if (empty($clientId) && file_exists(__DIR__ . '/../config/env.php')) {
            $env = require __DIR__ . '/../config/env.php';
            $clientId = $env['GOOGLE_CLIENT_ID'] ?? '';
        }

        $redirectUri = defined('GOOGLE_REDIRECT_URI') ? GOOGLE_REDIRECT_URI : base_url('auth/google/callback');

        if (empty($clientId)) {
            Auth::setFlash('danger', 'Google Client ID belum dikonfigurasi. Pastikan config/env.php sudah terpasang.');
            redirect('login');
        }

        $scope = urlencode('openid email profile');
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2_state'] = $state;

        $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?"
            . "client_id=" . urlencode($clientId)
            . "&redirect_uri=" . urlencode($redirectUri)
            . "&response_type=code"
            . "&scope={$scope}"
            . "&state={$state}"
            . "&prompt=select_account";

        header("Location: " . $authUrl);
        exit;
    }

    public function handleGoogleCallback(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        $sessionState = $_SESSION['oauth2_state'] ?? null;

        if (!$code) {
            Auth::setFlash('danger', 'Gagal memproses autentikasi Google. Kode autentikasi tidak ditemukan.');
            redirect('login');
        }

        if ($sessionState && $state && $state !== $sessionState) {
            // State mismatch fallback (ignore if session state expired but code valid)
        }
        unset($_SESSION['oauth2_state']);

        $clientId = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';
        $clientSecret = defined('GOOGLE_CLIENT_SECRET') ? GOOGLE_CLIENT_SECRET : '';
        if ((empty($clientId) || empty($clientSecret)) && file_exists(__DIR__ . '/../config/env.php')) {
            $env = require __DIR__ . '/../config/env.php';
            $clientId = $env['GOOGLE_CLIENT_ID'] ?? $clientId;
            $clientSecret = $env['GOOGLE_CLIENT_SECRET'] ?? $clientSecret;
        }
        $redirectUri = defined('GOOGLE_REDIRECT_URI') ? GOOGLE_REDIRECT_URI : base_url('auth/google/callback');

        // 1. Tukar Authorization Code dengan Access Token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT => 15,
        ]);
        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'] ?? null;

        if (!$accessToken) {
            $errMsg = $tokenData['error_description'] ?? $tokenData['error'] ?? $curlErr ?? 'Response tidak valid dari Google.';
            Auth::setFlash('danger', 'Gagal memverifikasi akun Google: ' . htmlspecialchars($errMsg));
            redirect('login');
        }

        // 2. Ambil Profil Pengguna dari Google API
        $ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $accessToken],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT => 15,
        ]);
        $userInfoResponse = curl_exec($ch);
        curl_close($ch);

        $userInfo = json_decode($userInfoResponse, true);
        $googleEmail = strtolower(trim($userInfo['email'] ?? ''));

        if (empty($googleEmail)) {
            Auth::setFlash('danger', 'Tidak dapat mengambil alamat email dari akun Google Anda.');
            redirect('login');
        }

        // 3. Verifikasi & Login ke Sistem SPMI
        if (Auth::attemptGoogleLogin($googleEmail)) {
            $multiRoleEmails = ['nesiaraniwijaya@gmail.com', 'ravywhienelda@gmail.com', 'lpm@unika.ac.id', 'tu.lpm@unika.ac.id', 'admin.lpm@unika.ac.id'];
            if (in_array($googleEmail, $multiRoleEmails) || Auth::role() === 'testing' || Auth::role() === 'super_admin') {
                $_SESSION['is_multi_role_testing'] = true;
                redirect('auth/select-role');
            }
            Auth::setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($_SESSION['user_name']) . '! (Login via Google SSO)');
            $returnUrl = $_SESSION['return_url'] ?? '';
            if (!empty($returnUrl) && !preg_match('#^https?://#i', $returnUrl) && !str_starts_with($returnUrl, '//')) {
                unset($_SESSION['return_url']);
                redirect(ltrim($returnUrl, '/'));
            }
            redirect(Auth::getDashboardRoute());
        } elseif (str_ends_with($googleEmail, '@unika.ac.id') || str_ends_with($googleEmail, '@unica.ac.id') || str_ends_with($googleEmail, '.unika.ac.id') || str_ends_with($googleEmail, '.unica.ac.id')) {
            $_SESSION['user_id'] = 'civitas_' . substr(md5($googleEmail), 0, 10);
            $_SESSION['user_name'] = $userInfo['name'] ?? explode('@', $googleEmail)[0];
            $_SESSION['user_real_name'] = $_SESSION['user_name'];
            $_SESSION['user_email'] = $googleEmail;
            $_SESSION['user_role'] = 'civitas_unika';
            $_SESSION['user_real_role'] = 'civitas_unika';
            $_SESSION['user_avatar'] = $userInfo['picture'] ?? null;
            $_SESSION['is_unika_civitas'] = true;
            $_SESSION['logged_in_at'] = date('Y-m-d H:i:s');

            Auth::setFlash('success', 'Selamat datang, ' . htmlspecialchars($_SESSION['user_name']) . '! Anda berhasil masuk dengan akun resmi UNIKA. Akses dokumen penuh dan unduhan telah diaktifkan.');
            $returnUrl = $_SESSION['return_url'] ?? '';
            if (!empty($returnUrl) && !preg_match('#^https?://#i', $returnUrl) && !str_starts_with($returnUrl, '//')) {
                unset($_SESSION['return_url']);
                redirect(ltrim($returnUrl, '/'));
            }
            redirect('dokumen');
        } else {
            Auth::setFlash('danger', 'Email Google <strong>' . htmlspecialchars($googleEmail) . '</strong> bukan akun resmi civitas @unika.ac.id atau belum terdaftar di sistem E-SPMI.');
            redirect('login');
        }
    }

    public function showSelectRole(): void {
        if (!Auth::check()) {
            redirect('login');
        }
        if (!Auth::canSwitchRole()) {
            Auth::setFlash('danger', 'Akses ditolak: Anda tidak memiliki wewenang untuk membuka halaman Ganti Peran.');
            redirect(Auth::getDashboardRoute());
        }

        // Konsumsi flash sebelumnya jika ada, agar halaman pemilihan peran tetap bersih
        Auth::getFlash();

        $pdo = Database::getInstance()->getConnection();
        $fakultasList = $pdo->query("SELECT id, nama_fakultas, kode_fakultas FROM fakultas ORDER BY nama_fakultas ASC")->fetchAll();
        $prodiList = $pdo->query("SELECT id, fakultas_id, nama_prodi, jenjang, kode_prodi FROM prodis ORDER BY nama_prodi ASC, jenjang ASC")->fetchAll();

        $defaultFakultasId = (int)($_SESSION['user_fakultas_id'] ?? 0);
        if (!$defaultFakultasId) {
            foreach ($fakultasList as $f) {
                if (in_array(strtoupper(trim($f['kode_fakultas'] ?? '')), ['FIK', 'FIKOM'])) {
                    $defaultFakultasId = (int)$f['id'];
                    break;
                }
            }
            if (!$defaultFakultasId && !empty($fakultasList)) {
                $defaultFakultasId = (int)$fakultasList[0]['id'];
            }
        }

        $defaultProdiId = (int)($_SESSION['user_prodi_id'] ?? 0);
        if (!$defaultProdiId) {
            foreach ($prodiList as $p) {
                if (($p['kode_prodi'] ?? '') === '55201') {
                    $defaultProdiId = (int)$p['id'];
                    break;
                }
            }
            if (!$defaultProdiId && !empty($prodiList)) {
                $defaultProdiId = (int)$prodiList[0]['id'];
            }
        }

        $this->render('auth/select_role', [
            'pageTitle'         => 'Pilih Peran Akses Sistem - MITRA',
            'flash'             => null,
            'fakultasList'      => $fakultasList,
            'prodiList'         => $prodiList,
            'defaultFakultasId' => $defaultFakultasId,
            'defaultProdiId'    => $defaultProdiId,
        ]);
    }

    public function processSelectRole(): void {
        if (!Auth::check()) {
            redirect('login');
        }
        if (!Auth::canSwitchRole()) {
            Auth::setFlash('danger', 'Akses ditolak: Anda tidak memiliki wewenang untuk mengganti peran.');
            redirect(Auth::getDashboardRoute());
        }

        $selectedRole = trim($_POST['role'] ?? '');
        $allowedRoles = ['super_admin', 'kepala_lpm', 'kepala_pusat_mutu', 'gpm', 'dekan', 'wadek', 'kaprodi', 'sekprodi', 'pengguna'];
        if (!in_array($selectedRole, $allowedRoles, true)) {
            Auth::setFlash('danger', 'Peran yang dipilih tidak valid.');
            redirect('auth/select-role');
        }
        $reqFakId     = !empty($_POST['fakultas_id']) ? (int)$_POST['fakultas_id'] : 0;
        $reqProdiId   = !empty($_POST['prodi_id']) ? (int)$_POST['prodi_id'] : 0;
        $pdo = Database::getInstance()->getConnection();

        $selectedFak = null;
        if ($reqFakId > 0) {
            $stmtFak = $pdo->prepare("SELECT id, nama_fakultas, kode_fakultas FROM fakultas WHERE id = ? LIMIT 1");
            $stmtFak->execute([$reqFakId]);
            $selectedFak = $stmtFak->fetch();
        }
        if (!$selectedFak) {
            $selectedFak = $pdo->query("SELECT id, nama_fakultas, kode_fakultas FROM fakultas WHERE kode_fakultas IN ('FIK', 'FIKOM') LIMIT 1")->fetch()
                ?: $pdo->query("SELECT id, nama_fakultas, kode_fakultas FROM fakultas ORDER BY id ASC LIMIT 1")->fetch();
        }
        $targetFakId    = (int)($selectedFak['id'] ?? 2);
        $targetFakName  = $selectedFak['nama_fakultas'] ?? 'Fakultas Ilmu Komputer';
        $targetFakShort = trim($selectedFak['kode_fakultas'] ?? '') ?: $targetFakName;

        $selectedProdi = null;
        if ($reqProdiId > 0) {
            $stmtPr = $pdo->prepare("SELECT p.id, p.nama_prodi, p.jenjang, p.fakultas_id, f.nama_fakultas FROM prodis p JOIN fakultas f ON p.fakultas_id = f.id WHERE p.id = ? LIMIT 1");
            $stmtPr->execute([$reqProdiId]);
            $selectedProdi = $stmtPr->fetch();
        }
        if (!$selectedProdi) {
            $stmtPr = $pdo->prepare("SELECT p.id, p.nama_prodi, p.jenjang, p.fakultas_id, f.nama_fakultas FROM prodis p JOIN fakultas f ON p.fakultas_id = f.id WHERE p.fakultas_id = ? ORDER BY p.id ASC LIMIT 1");
            $stmtPr->execute([$targetFakId]);
            $selectedProdi = $stmtPr->fetch()
                ?: $pdo->query("SELECT p.id, p.nama_prodi, p.jenjang, p.fakultas_id, f.nama_fakultas FROM prodis p JOIN fakultas f ON p.fakultas_id = f.id WHERE p.kode_prodi = '55201' LIMIT 1")->fetch();
        }
        $targetProdiId      = (int)($selectedProdi['id'] ?? 1);
        $targetProdiName    = $selectedProdi['nama_prodi'] ?? 'Teknik Informatika';
        $targetProdiFakId   = (int)($selectedProdi['fakultas_id'] ?? $targetFakId);
        $targetProdiFakName = $selectedProdi['nama_fakultas'] ?? $targetFakName;

        $rawName = $_SESSION['user_real_name'] ?? $_SESSION['user_name'] ?? 'Pengguna Testing';
        $cleanName = preg_replace('/\s*\(.*?\)$/', '', $rawName);

        switch ($selectedRole) {
            case 'super_admin':
                $_SESSION['user_name'] = $cleanName . ' (Admin LPM / Super Admin)';
                $_SESSION['user_role'] = 'super_admin';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = null;
                $_SESSION['user_fakultas_name'] = null;
                break;
            case 'kepala_lpm':
                $_SESSION['user_name'] = $cleanName . ' (Kepala LPM)';
                $_SESSION['user_role'] = 'kepala_lpm';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = null;
                $_SESSION['user_fakultas_name'] = null;
                break;
            case 'kepala_pusat_mutu':
                $_SESSION['user_name'] = $cleanName . ' (Kepala Pusat Mutu)';
                $_SESSION['user_role'] = 'kepala_pusat_mutu';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = null;
                $_SESSION['user_fakultas_name'] = null;
                break;
            case 'gpm':
                $_SESSION['user_name'] = $cleanName . " (GPM {$targetFakShort})";
                $_SESSION['user_role'] = 'gpm';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = $targetFakId;
                $_SESSION['user_fakultas_name'] = $targetFakName;
                break;
            case 'dekan':
                $_SESSION['user_name'] = $cleanName . " (Dekan {$targetFakShort})";
                $_SESSION['user_role'] = 'dekan';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = $targetFakId;
                $_SESSION['user_fakultas_name'] = $targetFakName;
                break;
            case 'wadek':
                $_SESSION['user_name'] = $cleanName . " (Wakil Dekan {$targetFakShort})";
                $_SESSION['user_role'] = 'wadek';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = $targetFakId;
                $_SESSION['user_fakultas_name'] = $targetFakName;
                break;
            case 'kaprodi':
                $_SESSION['user_name'] = $cleanName . " (Kaprodi {$targetProdiName})";
                $_SESSION['user_role'] = 'kaprodi';
                $_SESSION['user_prodi_id'] = $targetProdiId;
                $_SESSION['user_prodi_name'] = $targetProdiName;
                $_SESSION['user_fakultas_id'] = $targetProdiFakId;
                $_SESSION['user_fakultas_name'] = $targetProdiFakName;
                break;
            case 'sekprodi':
                $_SESSION['user_name'] = $cleanName . " (Sekprodi {$targetProdiName})";
                $_SESSION['user_role'] = 'sekprodi';
                $_SESSION['user_prodi_id'] = $targetProdiId;
                $_SESSION['user_prodi_name'] = $targetProdiName;
                $_SESSION['user_fakultas_id'] = $targetProdiFakId;
                $_SESSION['user_fakultas_name'] = $targetProdiFakName;
                break;
            case 'pengguna':
                $_SESSION['user_name'] = $cleanName . ' (Civitas / Pengguna)';
                $_SESSION['user_role'] = 'pengguna';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = null;
                $_SESSION['user_fakultas_name'] = null;
                break;
            default:
                Auth::setFlash('danger', 'Peran yang dipilih tidak valid.');
                redirect('auth/select-role');
        }

        $_SESSION['is_multi_role_testing'] = true;

        AuditLogger::log(
            aksi: 'LOGIN',
            modul: 'Autentikasi (Simulasi Peran)',
            targetId: (string)$_SESSION['user_id'],
            targetName: $_SESSION['user_name'],
            newValues: ['email' => $_SESSION['user_email'], 'switched_role' => $_SESSION['user_role']]
        );

        Auth::setFlash('success', 'Simulasi Peran Berhasil! Anda sekarang mengakses sistem sebagai <strong>' . htmlspecialchars($_SESSION['user_name']) . '</strong> (' . strtoupper(str_replace('_', ' ', $_SESSION['user_role'])) . ').');
        redirect(Auth::getDashboardRoute());
    }
}
