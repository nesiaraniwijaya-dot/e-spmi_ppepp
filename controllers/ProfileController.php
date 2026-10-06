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
    }

    /**
     * Tampilkan Halaman Edit Profil
     */
    public function index(): void {
        $userId = Auth::id();
        $stmt = $this->db->prepare("
            SELECT u.*, p.nama_prodi, p.kode_prodi, 
                   COALESCE(f_direct.nama_fakultas, f_prodi.nama_fakultas) as nama_fakultas 
            FROM users u 
            LEFT JOIN prodis p ON u.prodi_id = p.id 
            LEFT JOIN fakultas f_prodi ON p.fakultas_id = f_prodi.id 
            LEFT JOIN fakultas f_direct ON u.fakultas_id = f_direct.id 
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            Auth::setFlash('danger', 'Pengguna tidak ditemukan.');
            redirect(Auth::getDashboardRoute());
        }

        $this->render('profile/index', [
            'pageTitle' => 'Profil Saya & Pengaturan Akun',
            'user' => $user
        ]);
    }

    /**
     * Simpan Perubahan Profil (Nama, Avatar, Password)
     */
    public function update(): void {
        $userId = Auth::id();
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $oldUser = $stmt->fetch();

        if (!$oldUser) {
            Auth::setFlash('danger', 'Pengguna tidak ditemukan.');
            redirect('login');
        }

        $name = trim($_POST['name'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $role = Auth::role();
        $redirectUrl = match($role) {
            'super_admin', 'kepala_lpm' => 'admin/profile',
            'admin_fakultas' => 'fakultas/profile',
            'gpm' => 'gpm/profile',
            default => 'prodi/profile'
        };

        if (empty($name)) {
            Auth::setFlash('danger', 'Nama lengkap tidak boleh kosong.');
            redirect($redirectUrl);
        }

        $avatarPath = $oldUser['avatar'] ?? null;

        // Handle Avatar Upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExts)) {
                Auth::setFlash('danger', 'Format avatar tidak valid. Gunakan format JPG, PNG, atau WEBP.');
                redirect($redirectUrl);
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                Auth::setFlash('danger', 'Ukuran foto avatar maksimal 5 MB.');
                redirect($redirectUrl);
            }

            $avatarFileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
            $targetPath = AVATAR_UPLOAD_PATH . '/' . $avatarFileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Remove old avatar if exists
                if (!empty($oldUser['avatar']) && file_exists(ROOT_PATH . '/' . $oldUser['avatar'])) {
                    @unlink(ROOT_PATH . '/' . $oldUser['avatar']);
                }
                $avatarPath = 'uploads/avatars/' . $avatarFileName;
            }
        }

        // Handle Password Change (Only allowed for Super Admin / Admin LPM)
        $passwordHash = $oldUser['password'];
        $isAdminRole = in_array($role, ['super_admin', 'kepala_lpm']);

        if ($isAdminRole && !empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                Auth::setFlash('danger', 'Kata sandi baru minimal 6 karakter.');
                redirect($redirectUrl);
            }

            if ($newPassword !== $confirmPassword) {
                Auth::setFlash('danger', 'Konfirmasi kata sandi baru tidak cocok.');
                redirect($redirectUrl);
            }

            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Update database
        $stmtUpdate = $this->db->prepare("UPDATE users SET name = ?, avatar = ?, password = ? WHERE id = ?");
        $stmtUpdate->execute([$name, $avatarPath, $passwordHash, $userId]);

        // Update active session
        Auth::updateProfileSession($name, $avatarPath);

        AuditLogger::log('UPDATE', 'Profil Pengguna', (string)$userId, $name, $oldUser, [
            'name' => $name,
            'avatar_updated' => ($avatarPath !== $oldUser['avatar']),
            'password_changed' => !empty($newPassword)
        ]);

        Auth::setFlash('success', 'Profil dan informasi akun Anda berhasil diperbarui.');
        redirect($redirectUrl);
    }
}
