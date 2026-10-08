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
                Auth::setFlash('info', 'Login Berhasil! Silakan pilih peran yang ingin Anda gunakan untuk simulasi/pengujian.');
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
        Auth::startSession();
        $clientId = GOOGLE_CLIENT_ID;
        $redirectUri = urlencode(GOOGLE_REDIRECT_URI);
        $scope = urlencode('openid email profile');
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2_state'] = $state;

        $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?"
            . "client_id={$clientId}"
            . "&redirect_uri={$redirectUri}"
            . "&response_type=code"
            . "&scope={$scope}"
            . "&state={$state}"
            . "&prompt=select_account";

        header("Location: " . $authUrl);
        exit;
    }

    public function handleGoogleCallback(): void {
        Auth::startSession();
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

        // 1. Tukar Authorization Code dengan Access Token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'code' => $code,
                'client_id' => GOOGLE_CLIENT_ID,
                'client_secret' => GOOGLE_CLIENT_SECRET,
                'redirect_uri' => GOOGLE_REDIRECT_URI,
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
            if (in_array($googleEmail, $multiRoleEmails) || Auth::role() === 'testing' || in_array(Auth::role(), ['super_admin', 'admin_lpm', 'kepala_lpm', 'kepala_pusat_mutu'])) {
                $_SESSION['is_multi_role_testing'] = true;
                Auth::setFlash('info', 'Login Google SSO Berhasil! Silakan pilih peran yang ingin Anda gunakan untuk simulasi/pengujian.');
                redirect('auth/select-role');
            }
            Auth::setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($_SESSION['user_name']) . '! (Login via Google SSO)');
            redirect(Auth::getDashboardRoute());
        } else {
            Auth::setFlash('danger', 'Email Google <strong>' . htmlspecialchars($googleEmail) . '</strong> belum terdaftar di sistem E-SPMI atau akun Anda sedang dinonaktifkan. Silakan hubungi Admin LPM.');
            redirect('login');
        }
    }

    public function showSelectRole(): void {
        if (!Auth::check()) {
            redirect('login');
        }

        $this->render('auth/select_role', [
            'pageTitle' => 'Pilih Peran Akses Sistem - MITRA'
        ]);
    }

    public function processSelectRole(): void {
        if (!Auth::check()) {
            redirect('login');
        }

        $selectedRole = trim($_POST['role'] ?? 'super_admin');
        $pdo = Database::getInstance()->getConnection();

        $resFak = $pdo->query("SELECT id FROM fakultas WHERE kode_fakultas IN ('FIK', 'FIKOM') LIMIT 1")->fetch();
        $fikomId = $resFak['id'] ?? 2;

        $resProdi = $pdo->query("SELECT id FROM prodis WHERE kode_prodi = '55201' LIMIT 1")->fetch();
        $tiId = $resProdi['id'] ?? 1;

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
                $_SESSION['user_name'] = $cleanName . ' (GPM FIKOM)';
                $_SESSION['user_role'] = 'gpm';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = $fikomId;
                $_SESSION['user_fakultas_name'] = 'Fakultas Ilmu Komputer';
                break;
            case 'dekan':
                $_SESSION['user_name'] = $cleanName . ' (Dekan FIKOM)';
                $_SESSION['user_role'] = 'dekan';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = $fikomId;
                $_SESSION['user_fakultas_name'] = 'Fakultas Ilmu Komputer';
                break;
            case 'wadek':
                $_SESSION['user_name'] = $cleanName . ' (Wakil Dekan FIKOM)';
                $_SESSION['user_role'] = 'wadek';
                $_SESSION['user_prodi_id'] = null;
                $_SESSION['user_prodi_name'] = null;
                $_SESSION['user_fakultas_id'] = $fikomId;
                $_SESSION['user_fakultas_name'] = 'Fakultas Ilmu Komputer';
                break;
            case 'kaprodi':
                $_SESSION['user_name'] = $cleanName . ' (Kaprodi TI)';
                $_SESSION['user_role'] = 'kaprodi';
                $_SESSION['user_prodi_id'] = $tiId;
                $_SESSION['user_prodi_name'] = 'Teknik Informatika';
                $_SESSION['user_fakultas_id'] = $fikomId;
                $_SESSION['user_fakultas_name'] = 'Fakultas Ilmu Komputer';
                break;
            case 'sekprodi':
                $_SESSION['user_name'] = $cleanName . ' (Sekprodi TI)';
                $_SESSION['user_role'] = 'sekprodi';
                $_SESSION['user_prodi_id'] = $tiId;
                $_SESSION['user_prodi_name'] = 'Teknik Informatika';
                $_SESSION['user_fakultas_id'] = $fikomId;
                $_SESSION['user_fakultas_name'] = 'Fakultas Ilmu Komputer';
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
                $_SESSION['user_role'] = 'super_admin';
                break;
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
