<?php
/**
 * Migration: Create landing_settings table and seed initial content
 * SPMI PPEPP UNIKA Soegijapranata
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    // 1. Create table if not exists
    $db->exec("
        CREATE TABLE IF NOT EXISTS `landing_settings` (
            `setting_key` VARCHAR(100) NOT NULL PRIMARY KEY,
            `setting_value` LONGTEXT NULL,
            `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "[OK] Table landing_settings checked/created.\n";

    // 2. Default settings definitions
    $defaultSettings = [
        // HERO SECTION
        'hero_badge' => ['Lembaga Penjaminan Mutu • UNIKA Soegijapranata', 'hero'],
        'hero_title' => ['Sistem Penjaminan Mutu Internal', 'hero'],
        'hero_highlight' => ['Siklus PPEPP Berkelanjutan', 'hero'],
        'hero_desc' => ['Portal terpadu pengawasan dan repositori dokumen Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan mutu tridharma di seluruh program studi Universitas Katolik Soegijapranata.', 'hero'],
        'hero_btn1_text' => ['Jelajahi Direktori Fakultas', 'hero'],
        'hero_btn1_url' => ['#direktoriFakultas', 'hero'],
        'hero_btn2_text' => ['Repositori Dokumen Mutu', 'hero'],
        'hero_btn2_url' => ['dokumen', 'hero'],

        // STATS BAR
        'stats_fakultas_label' => ['Fakultas & Pascasarjana', 'stats'],
        'stats_prodi_label' => ['Program Studi Aktif', 'stats'],
        'stats_dokumen_label' => ['Dokumen Mutu Terverifikasi', 'stats'],
        'stats_siklus_label' => ['Siklus PPEPP Terintegrasi', 'stats'],

        // 5 SIKLUS PPEPP
        'siklus_section_tag' => ['ALUR PENJAMINAN MUTU', 'siklus'],
        'siklus_section_title' => ['5 Siklus Penjaminan Mutu Internal (PPEPP)', 'siklus'],
        'siklus_section_desc' => ['Implementasi siklus berkelanjutan (Continuous Quality Improvement) sesuai pedoman Permendikbudristek No. 53 Tahun 2023 untuk mencapai akreditasi unggul institusi.', 'siklus'],
        'siklus_p1_title' => ['Penetapan', 'siklus'],
        'siklus_p1_desc' => ['Perumusan standar, manual mutu, kebijakan SPMI, dan Capaian Pembelajaran Lulusan (CPL).', 'siklus'],
        'siklus_p2_title' => ['Pelaksanaan', 'siklus'],
        'siklus_p2_desc' => ['Implementasi kurikulum, RPS, SOP pembelajaran, penelitian, dan pengabdian masyarakat prodi.', 'siklus'],
        'siklus_e_title' => ['Evaluasi', 'siklus'],
        'siklus_e_desc' => ['Audit Mutu Internal (AMI), monitoring berkala, monev pembelajaran, dan pengukuran kepuasan pengguna.', 'siklus'],
        'siklus_p3_title' => ['Pengendalian', 'siklus'],
        'siklus_p3_desc' => ['Tindakan koreksi terhadap deviasi standar, evaluasi akar masalah, dan Rapat Tinjauan Manajemen (RTM).', 'siklus'],
        'siklus_p4_title' => ['Peningkatan', 'siklus'],
        'siklus_p4_desc' => ['Pembaruan dan peningkatan standar mutu secara berkelanjutan (Kaizen) melampaui SN-Dikti.', 'siklus'],

        // DIREKTORI AKADEMIK
        'direktori_section_tag' => ['DIREKTORI AKADEMIK', 'direktori'],
        'direktori_section_title' => ['Fakultas & Program Studi', 'direktori'],
        'direktori_section_desc' => ['Pilih fakultas di bawah untuk meninjau profil kepemimpinan, sebaran dokumen mutu 5 siklus PPEPP, dan status kepatuhan standar mutu masing-masing prodi.', 'direktori'],

        // FOOTER & KONTAK
        'footer_brand_title' => ['PETRA', 'footer'],
        'footer_brand_sub' => ['PEmantauan Tahapan PPEPP & Rencana Aksi', 'footer'],
        'footer_desc' => ['PETRA = PEmantauan Tahapan PPEPP & Rencana Aksi. PETRA adalah Pengawal Mutu dalam Mewujudkan Perbaikan Berkelanjutan.', 'footer'],
        'footer_address' => ['Kampus Bendan Dhuwur, Jl. Pawiyatan Luhur IV/1, Semarang 50234', 'footer'],
        'footer_akreditasi' => ['Terakreditasi UNGGUL • BAN-PT', 'footer'],
        'footer_email' => ['lpm@unika.ac.id', 'footer'],
        'footer_phone' => ['(024) 8441555 ext. 1402', 'footer'],
        'footer_website_url' => ['https://www.unika.ac.id', 'footer'],
        'footer_website_text' => ['Website Utama SCU', 'footer'],
    ];

    $stmtInsert = $db->prepare("
        INSERT INTO `landing_settings` (`setting_key`, `setting_value`, `setting_group`)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE `setting_group` = VALUES(`setting_group`)
    ");

    $seededCount = 0;
    foreach ($defaultSettings as $key => [$val, $group]) {
        $stmtInsert->execute([$key, $val, $group]);
        $seededCount++;
    }

    echo "[OK] Successfully seeded {$seededCount} landing settings.\n";

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
