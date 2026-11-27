<?php
/**
 * Helper Notifikasi Email SPMI PPEPP
 * Universitas Katolik Soegijapranata (UNIKA Soegijapranata / SCU)
 */

class MailHelper {
    /**
     * Mengirim notifikasi email permohonan buka akses unggah dokumen ke TU LPM
     *
     * @param array $data [
     *   'pemohon_name'   => string,
     *   'pemohon_role'   => string,
     *   'pemohon_email'  => string,
     *   'unit_name'      => string,
     *   'tahun_akademik' => string,
     *   'alasan'         => string,
     *   'level'          => 'prodi'|'fakultas',
     *   'created_at'     => string (optional)
     * ]
     * @return bool
     */
    public static function sendUploadAccessRequestNotification(array $data): bool {
        $toEmail = 'tu.lpm@unika.ac.id';
        
        $pemohonName   = $data['pemohon_name'] ?? 'Pengguna SPMI';
        $pemohonRole   = $data['pemohon_role'] ?? 'Unit Kerja';
        $pemohonEmail  = $data['pemohon_email'] ?? 'no-reply@unika.ac.id';
        $unitName      = $data['unit_name'] ?? 'Program Studi / Fakultas';
        $tahunAkademik = $data['tahun_akademik'] ?? '-';
        $alasan        = $data['alasan'] ?? '-';
        $levelStr      = ($data['level'] ?? 'prodi') === 'fakultas' ? 'Tingkat Fakultas' : 'Program Studi';
        $waktu         = !empty($data['created_at']) ? date('d M Y, H:i', strtotime($data['created_at'])) . ' WIB' : date('d M Y, H:i') . ' WIB';
        
        $actionUrl = base_url('admin/upload-access');

        $subject = "[SPMI PPEPP] Permohonan Izin Buka Akses Upload: {$unitName} ({$tahunAkademik})";

        // HTML Email Template dengan gaya resmi UNIKA SCU
        $html = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #1E293B; margin: 0; padding: 0; background-color: #F8FAFC; }
.wrapper { max-width: 600px; margin: 20px auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
.header { background: linear-gradient(135deg, #1E3E62 0%, #0B192C 100%); color: #FFFFFF; padding: 26px 30px; text-align: left; }
.header h2 { margin: 0 0 4px 0; font-size: 19px; font-weight: 700; color: #FFFFFF; letter-spacing: -0.3px; }
.header p { margin: 0; font-size: 12.5px; color: #94A3B8; }
.content { padding: 30px; }
.badge { display: inline-block; padding: 4px 10px; font-size: 11px; font-weight: 700; border-radius: 20px; background: #FEF3C7; color: #92400E; text-transform: uppercase; margin-bottom: 14px; }
.info-table { width: 100%; border-collapse: collapse; margin: 18px 0; font-size: 13.5px; }
.info-table th { text-align: left; padding: 9px 12px; background: #F1F5F9; color: #475569; width: 34%; border: 1px solid #E2E8F0; font-weight: 600; }
.info-table td { padding: 9px 12px; border: 1px solid #E2E8F0; color: #0F172A; }
.alasan-box { background: #F8FAFC; border-left: 4px solid #3B82F6; padding: 12px 16px; border-radius: 4px; margin: 14px 0; font-size: 13.5px; color: #334155; line-height: 1.55; }
.cta-button { display: inline-block; padding: 12px 24px; background: #1E3E62; color: #FFFFFF !important; text-decoration: none; font-weight: 600; font-size: 13.5px; border-radius: 8px; margin: 18px 0 8px 0; }
.footer { background: #F1F5F9; padding: 18px 30px; font-size: 11.5px; color: #64748B; border-top: 1px solid #E2E8F0; text-align: center; line-height: 1.5; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h2>SISTA 2 &bull; SPMI PPEPP UNIKA</h2>
        <p>Lembaga Penjaminan Mutu &bull; Universitas Katolik Soegijapranata</p>
    </div>
    <div class="content">
        <span class="badge">&bull; Permohonan Buka Akses Dokumen</span>
        <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #0F172A;">Pemberitahuan Pengajuan Izin Akses Unggah Dokumen Mutu</h3>
        <p style="margin: 0 0 16px 0; font-size: 13.5px; line-height: 1.5; color: #475569;">
            Yth. <strong>TU Lembaga Penjaminan Mutu (Admin LPM)</strong>,<br>
            Terdapat permohonan baru untuk pembukaan akses unggah dokumen mutu pada periode Tahun Akademik yang telah terkunci/tutup dari unit kerja berikut:
        </p>

        <table class="info-table">
            <tr><th>Unit Kerja</th><td><strong>' . htmlspecialchars($unitName) . '</strong> (' . htmlspecialchars($levelStr) . ')</td></tr>
            <tr><th>Pemohon</th><td>' . htmlspecialchars($pemohonName) . ' &lt;' . htmlspecialchars($pemohonEmail) . '&gt; (' . htmlspecialchars($pemohonRole) . ')</td></tr>
            <tr><th>Tahun Akademik</th><td><strong style="color: #2563EB;">' . htmlspecialchars($tahunAkademik) . '</strong></td></tr>
            <tr><th>Waktu Pengajuan</th><td>' . htmlspecialchars($waktu) . '</td></tr>
        </table>

        <div style="font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 4px;">Alasan Permohonan:</div>
        <div class="alasan-box">
            ' . nl2br(htmlspecialchars($alasan)) . '
        </div>

        <div style="text-align: center; margin: 24px 0 12px 0;">
            <a href="' . $actionUrl . '" class="cta-button">Tinjau &amp; Buka Akses di Sistem &rarr;</a>
        </div>
        <p style="font-size: 11.5px; color: #64748B; text-align: center; margin: 0;">
            Tautan persetujuan: <a href="' . $actionUrl . '" style="color: #2563EB;">' . $actionUrl . '</a>
        </p>
    </div>
    <div class="footer">
        Email ini dikirim secara otomatis oleh Sistem Penjaminan Mutu Internal (SISTA 2 SPMI PPEPP) UNIKA Soegijapranata.<br>
        Tindakan persetujuan dapat diproses melalui menu Akses &amp; Tenggat Upload pada panel admin.
    </div>
</div>
</body>
</html>';

        return self::sendMail($toEmail, $subject, $html, $pemohonEmail, $pemohonName);
    }

    /**
     * Method inti pengiriman email dengan fail-safe
     */
    public static function sendMail(string $to, string $subject, string $htmlBody, string $replyTo = '', string $replyToName = ''): bool {
        // Logging pengiriman email untuk audit trail
        self::logEmail($to, $subject, $htmlBody);

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: SPMI PPEPP UNIKA <no-reply@unika.ac.id>\r\n";
        if (!empty($replyTo)) {
            $headers .= "Reply-To: " . (!empty($replyToName) ? "{$replyToName} <{$replyTo}>" : $replyTo) . "\r\n";
        }
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        // Coba kirim via mail() native PHP
        try {
            $sent = @mail($to, $subject, $htmlBody, $headers);
            return (bool)$sent;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Pencatatan log email keluar (aman di localhost maupun hosting)
     */
    private static function logEmail(string $to, string $subject, string $body): void {
        try {
            $logDir = __DIR__ . '/../storage/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }
            $logFile = $logDir . '/mail.log';
            $logEntry = sprintf(
                "[%s] TO: %s | SUBJECT: %s | LENGTH: %d bytes\n",
                date('Y-m-d H:i:s'),
                $to,
                $subject,
                strlen($body)
            );
            @file_put_contents($logFile, $logEntry, FILE_APPEND);
        } catch (\Throwable $e) {
            // fail-safe ignore
        }
    }
}

