<?php
/**
 * Profile Controller
 * Handles user profile editing (Name, Avatar, Password)
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/AuditLogger.php';

class ProfileController extends Controller {

    public function __construct() {
        parent::__construct();
        Auth::requireLogin();

        if (!empty($_SESSION['is_unika_civitas']) || Auth::role() === 'civitas_unika') {
            Auth::setFlash('info', 'Akun Civitas UNIKA dikelola melalui Google SSO dan tidak memiliki halaman profil internal terpisah.');
            redirect('dokumen');
        }

        $this->enforceRouteRole();
    }

    private function enforceRouteRole(): void {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }
        $uri = strtolower(trim($uri, '/'));

        if (str_starts_with($uri, 'admin/profile')) {
            Auth::requireRole(Auth::LPM_ROLES);
        } elseif (str_starts_with($uri, 'fakultas/profile')) {
            Auth::requireRole(['dekan', 'wadek']);
        } elseif (str_starts_with($uri, 'prodi/profile')) {
            Auth::requireRole(['kaprodi', 'sekprodi']);
        } elseif (str_starts_with($uri, 'gpm/profile')) {
            Auth::requireRole(['gpm']);
        } elseif (str_starts_with($uri, 'profile')) {
            Auth::requireRole(['pengguna']);
        }
    }

    /**
     * Halaman Edit Profil Dinonaktifkan
     * Nama dan foto profil disinkronkan langsung dari akun email Google yang digunakan login.
     */
    public function index(): void {
        Auth::setFlash('info', 'Nama dan foto profil akun Anda disinkronkan secara otomatis dari email Google institusi yang digunakan untuk masuk.');
        redirect(Auth::getDashboardRoute());
    }

    /**
     * Simpan Perubahan Profil (Dinonaktifkan)
     */
    public function update(): void {
        Auth::setFlash('info', 'Nama dan foto profil akun Anda disinkronkan secara otomatis dari email Google institusi yang digunakan untuk masuk.');
        redirect(Auth::getDashboardRoute());
    }
}
