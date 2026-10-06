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
            'pageTitle' => 'Masuk Portal PETRA',
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
}
