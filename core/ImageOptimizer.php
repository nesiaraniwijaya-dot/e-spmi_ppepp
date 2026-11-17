<?php
/**
 * Image Optimizer & WebP Converter
 * SPMI PPEPP UNIKA Soegijapranata (SCU)
 *
 * Mengoptimalkan seluruh gambar yang diunggah ke sistem MITRA
 * dengan mengonversinya secara otomatis ke format WebP terkompresi
 * guna mempercepat loading web dan menghemat bandwidth serta ruang server.
 */

class ImageOptimizer {

    /**
     * Ekstensi berkas gambar yang didukung untuk konversi WebP
     */
    public const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

    /**
     * Kualitas kompresi default WebP (0 - 100)
     * 82 memberikan rasio kompresi tinggi (50-80% lebih hemat) dengan kualitas visual yang tajam.
     */
    public const DEFAULT_QUALITY = 82;

    /**
     * Cek apakah ekstensi merupakan gambar yang dapat dikonversi
     */
    public static function isImageExtension(string $ext): bool {
        return in_array(strtolower(trim($ext)), self::SUPPORTED_EXTENSIONS, true);
    }

    /**
     * Cek apakah file fisik merupakan gambar yang valid
     */
    public static function isImageFile(string $filePath): bool {
        if (!file_exists($filePath) || !is_readable($filePath) || filesize($filePath) === 0) {
            return false;
        }

        // Coba via exif_imagetype jika tersedia
        if (function_exists('exif_imagetype')) {
            $type = @exif_imagetype($filePath);
            return in_array($type, [
                IMAGETYPE_JPEG,
                IMAGETYPE_PNG,
                IMAGETYPE_GIF,
                IMAGETYPE_BMP,
                IMAGETYPE_WEBP
            ], true);
        }

        // Alternatif via getimagesize
        $info = @getimagesize($filePath);
        if ($info === false) {
            return false;
        }

        $validMimes = [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/x-png',
            'image/gif',
            'image/bmp',
            'image/x-ms-bmp',
            'image/webp'
        ];

        return in_array(strtolower($info['mime'] ?? ''), $validMimes, true);
    }

    /**
     * Konversi berkas gambar ke WebP menggunakan ekstensi GD
     * 
     * @param string $sourcePath Path absolut berkas gambar asli
     * @param string|null $destPath Path tujuan berkas WebP (jika null, akan mengganti ekstensi sourcePath menjadi .webp)
     * @param int $quality Kualitas WebP (0 - 100)
     * @param bool $deleteSource Hapus berkas asli jika berhasil dikonversi dan path-nya berbeda
     * @return string|false Mengembalikan path absolut file WebP jika sukses, atau false jika gagal
     */
    public static function convertToWebp(
        string $sourcePath, 
        ?string $destPath = null, 
        int $quality = self::DEFAULT_QUALITY, 
        bool $deleteSource = true
    ): string|false {
        if (!file_exists($sourcePath) || !is_readable($sourcePath)) {
            return false;
        }

        // Tentukan path tujuan jika tidak ditentukan
        if ($destPath === null) {
            $destPath = preg_replace('/\.[^.]+$/', '.webp', $sourcePath);
        }

        // Pastikan GD mendukung WebP
        if (!function_exists('imagewebp')) {
            error_log('[ImageOptimizer] GD imagewebp() tidak tersedia pada server ini.');
            return false;
        }

        // Dapatkan tipe gambar
        $imgInfo = @getimagesize($sourcePath);
        if (!$imgInfo) {
            return false;
        }

        $mime = strtolower($imgInfo['mime'] ?? '');
        $image = null;

        // Load image resource berdasarkan tipe mime
        switch ($mime) {
            case 'image/jpeg':
            case 'image/pjpeg':
                if (function_exists('imagecreatefromjpeg')) {
                    $image = @imagecreatefromjpeg($sourcePath);
                }
                break;

            case 'image/png':
            case 'image/x-png':
                if (function_exists('imagecreatefrompng')) {
                    $image = @imagecreatefrompng($sourcePath);
                }
                break;

            case 'image/gif':
                if (function_exists('imagecreatefromgif')) {
                    $image = @imagecreatefromgif($sourcePath);
                }
                break;

            case 'image/bmp':
            case 'image/x-ms-bmp':
                if (function_exists('imagecreatefrombmp')) {
                    $image = @imagecreatefrombmp($sourcePath);
                }
                break;

            case 'image/webp':
                // Jika sudah WebP dan target path sama, periksa apakah perlu re-kompresi
                if (function_exists('imagecreatefromwebp')) {
                    $image = @imagecreatefromwebp($sourcePath);
                }
                break;

            default:
                return false;
        }

        if (!$image) {
            return false;
        }

        // Tangani transparansi alpha channel (sangat penting untuk PNG dan GIF)
        if (in_array($mime, ['image/png', 'image/x-png', 'image/gif', 'image/webp'], true)) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        // Pastikan direktori tujuan tersedia
        $destDir = dirname($destPath);
        if (!is_dir($destDir)) {
            @mkdir($destDir, 0777, true);
        }

        // Simpan gambar ke format WebP
        $success = @imagewebp($image, $destPath, max(1, min(100, $quality)));
        imagedestroy($image);

        if (!$success || !file_exists($destPath) || filesize($destPath) === 0) {
            return false;
        }

        // Hapus file sumber jika path berbeda dan opsi deleteSource aktif
        if ($deleteSource && realpath($sourcePath) !== realpath($destPath)) {
            @unlink($sourcePath);
        }

        return $destPath;
    }

    /**
     * Memproses berkas yang baru saja diunggah ke server:
     * - Jika merupakan gambar (JPG/PNG/GIF/BMP/WebP), otomatis dikonversi ke WebP.
     * - Jika merupakan dokumen (PDF/DOCX/dll), dibiarkan utuh tanpa perubahan.
     *
     * @param string $targetFile Path absolut berkas yang baru disimpan di disk
     * @param string $origName Nama asli berkas dari pengguna (misal: "kegiatan.jpg")
     * @param string $relativePrefix Prefix path relatif (misal: "uploads/documents")
     * @param int $quality Kualitas WebP (default: 82)
     * @return array [
     *     'is_image' => bool,
     *     'converted' => bool,
     *     'file_name' => string (nama file di disk, misal DOC_1_xxx.webp),
     *     'file_path' => string (path relatif untuk DB, misal uploads/documents/DOC_1_xxx.webp),
     *     'orig_name' => string (nama berkas asli berakhiran .webp jika dikonversi),
     *     'file_extension' => string (ekstensi, misal 'webp' atau 'pdf'),
     *     'file_size' => string (ukuran terformat misal '0.45 MB' atau '450 KB'),
     *     'size_bytes' => int
     * ]
     */
    public static function processUploadedFile(
        string $targetFile,
        string $origName = '',
        string $relativePrefix = 'uploads/documents',
        int $quality = self::DEFAULT_QUALITY
    ): array {
        $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $baseName = pathinfo($targetFile, PATHINFO_BASENAME);
        $sizeBytes = file_exists($targetFile) ? (int)filesize($targetFile) : 0;

        $defaultResult = [
            'is_image'       => false,
            'converted'      => false,
            'target_file'    => $targetFile,
            'file_name'      => $baseName,
            'file_path'      => rtrim($relativePrefix, '/') . '/' . $baseName,
            'orig_name'      => $origName ?: $baseName,
            'file_extension' => $ext,
            'file_size'      => round($sizeBytes / (1024 * 1024), 2) . ' MB',
            'size_bytes'     => $sizeBytes,
        ];

        // Cek apakah ekstensi dan berkas merupakan gambar
        if (!self::isImageExtension($ext) && !self::isImageFile($targetFile)) {
            return $defaultResult;
        }

        // Path tujuan WebP
        $webpTargetFile = preg_replace('/\.[^.]+$/', '.webp', $targetFile);

        $convertedPath = self::convertToWebp($targetFile, $webpTargetFile, $quality, true);

        if ($convertedPath && file_exists($convertedPath)) {
            $newBaseName = pathinfo($convertedPath, PATHINFO_BASENAME);
            $newSizeBytes = (int)filesize($convertedPath);

            // Ganti ekstensi nama file asli pengguna menjadi .webp agar seragam saat diunduh
            $newOrigName = $origName ? (preg_replace('/\.[^.]+$/', '.webp', $origName)) : $newBaseName;

            return [
                'is_image'       => true,
                'converted'      => true,
                'target_file'    => $convertedPath,
                'file_name'      => $newBaseName,
                'file_path'      => rtrim($relativePrefix, '/') . '/' . $newBaseName,
                'orig_name'      => $newOrigName,
                'file_extension' => 'webp',
                'file_size'      => round($newSizeBytes / (1024 * 1024), 2) . ' MB',
                'size_bytes'     => $newSizeBytes,
            ];
        }

        // Jika konversi gagal (misal file gambar rusak), fallback gunakan file asli
        $defaultResult['is_image'] = true;
        return $defaultResult;
    }
}
