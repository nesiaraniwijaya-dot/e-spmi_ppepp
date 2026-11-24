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
            Auth::setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($_SESSION['user_name']) . '!');
            unset($_SESSION['return_url']);

            // Jika akun memiliki multi-penugasan (Opsi 1), tampilkan layar pemilihan peran
            if (Auth::hasMultipleAssignments()) {
                redirect('auth/select-role');
                return;
            }

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
        $googleName = trim($userInfo['name'] ?? '');
        $googlePicture = trim($userInfo['picture'] ?? '');

        if (empty($googleEmail)) {
            Auth::setFlash('danger', 'Tidak dapat mengambil alamat email dari akun Google Anda.');
            redirect('login');
        }

        // 3. Verifikasi & Login ke Sistem SPMI (Sinkronkan nama dan foto profil dari Google)
        if (Auth::attemptGoogleLogin($googleEmail, $googleName, $googlePicture)) {
            Auth::setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($_SESSION['user_name']) . '! (Login via Google SSO)');

            // Jika akun memiliki multi-penugasan (Opsi 1), tampilkan layar pemilihan peran terlebih dahulu
            if (Auth::hasMultipleAssignments()) {
                redirect('auth/select-role');
                return;
            }

            $returnUrl = $_SESSION['return_url'] ?? '';
            if (!empty($returnUrl) && !preg_match('#^https?://#i', $returnUrl) && !str_starts_with($returnUrl, '//')) {
                unset($_SESSION['return_url']);
                redirect(ltrim($returnUrl, '/'));
            }
            redirect(Auth::getDashboardRoute());
        } elseif (str_contains($googleEmail, 'student.') || str_contains($googleEmail, '@student.')) {
            // Akun email mahasiswa (student.unika.ac.id) wajib terdaftar di Manajemen Pengguna
            Auth::setFlash('danger', 'Akun mahasiswa Google <strong>' . htmlspecialchars($googleEmail) . '</strong> belum terdaftar di Manajemen Pengguna sistem E-SPMI. Silakan hubungi Administrator LPM jika Anda memerlukan akses.');
            redirect('login');
        } elseif (str_ends_with($googleEmail, '@unika.ac.id') || str_ends_with($googleEmail, '@unica.ac.id') || str_ends_with($googleEmail, '.unika.ac.id') || str_ends_with($googleEmail, '.unica.ac.id')) {
            $_SESSION['user_id'] = 'civitas_' . substr(md5($googleEmail), 0, 10);
            $_SESSION['user_name'] = !empty($googleName) ? $googleName : explode('@', $googleEmail)[0];
            $_SESSION['user_real_name'] = $_SESSION['user_name'];
            $_SESSION['user_email'] = $googleEmail;
            $_SESSION['user_role'] = 'civitas_unika';
            $_SESSION['user_real_role'] = 'civitas_unika';
            $_SESSION['user_avatar'] = !empty($googlePicture) ? $googlePicture : null;
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
            return;
        }

        $assignments = Auth::getAssignments();
        if (empty($assignments) || count($assignments) <= 1) {
            redirect(Auth::getDashboardRoute());
            return;
        }

        $this->render('auth/select_role', [
            'pageTitle' => 'Pilih Peran Akses Sistem - MITRA',
            'assignments' => $assignments
        ]);
    }

    public function processSelectRole(): void {
        if (!Auth::check()) {
            redirect('login');
            return;
        }

        $assignmentId = (int)($_POST['assignment_id'] ?? 0);
        if ($assignmentId > 0 && Auth::switchAssignment($assignmentId)) {
            Auth::setFlash('success', 'Sesi aktif diatur sebagai <strong>' . htmlspecialchars(Auth::roleName()) . '</strong>.');
            redirect(Auth::getDashboardRoute());
            return;
        }

        Auth::setFlash('danger', 'Penugasan yang Anda pilih tidak valid atau bukan wewenang akun Anda.');
        redirect('auth/select-role');
    }

    public function switchRole(string $id): void {
        if (!Auth::check()) {
            redirect('login');
            return;
        }

        $assignmentId = (int)$id;
        if ($assignmentId > 0 && Auth::switchAssignment($assignmentId)) {
            Auth::setFlash('success', 'Berhasil beralih ke peran <strong>' . htmlspecialchars(Auth::roleName()) . '</strong>.');
            redirect(Auth::getDashboardRoute());
            return;
        }

        Auth::setFlash('danger', 'Gagal beralih peran: Penugasan tidak ditemukan atau bukan milik akun Anda.');
        redirect(Auth::getDashboardRoute());
    }
}
