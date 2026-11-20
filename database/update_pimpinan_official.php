<?php
/**
 * Update Pimpinan Fakultas & Program Studi sesuai Data Excel Resmi
 * SPMI PPEPP UNIKA Soegijapranata
 */
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

echo "Memperbarui Data Pimpinan Fakultas (Dekan)...\n";

$dekanData = [
    1 => 'Dr. B. Tyas Susanti, MA', // FAD
    2 => 'Prof. Dr. F. Ridwan Sanjaya, S.E., S.Kom., MS.IEC.', // FIKOM
    3 => 'Dr. Monica Palupi M. S.E., M.M.', // FEB
    4 => 'G.M. Adhyanggono, S.S., M.A., Ph.D', // FBS
    5 => 'Prof. Dr. Ir. Slamet Riyadi, M.T.', // FT
    6 => 'Dr. Kristiana Haryanti, M.Si', // FPSI
    7 => 'Dr. B. Resti Nurhayati, S.H. M.Hum.', // FHK
    8 => 'Inneke Hartono, S.TP., M.Sc', // FTP
    9 => 'dr. Edward Hartono, M.ARS', // FK
    10 => 'B. Danang Setianto, S.H., LL.M., Ph.D', // FITL
];

$stmtDekan = $db->prepare("UPDATE fakultas SET nama_dekan = ? WHERE id = ?");
foreach ($dekanData as $fakId => $namaDekan) {
    $stmtDekan->execute([$namaDekan, $fakId]);
    echo "  [OK] Fakultas ID {$fakId}: {$namaDekan}\n";
}

echo "\nMemperbarui Data Ketua Program Studi (Kaprodi)...\n";

$kaprodiData = [
    // FAD
    7 => 'Dr. Ir. V.G. Sri Rejeki, M.T.', // S1 Arsitektur
    8 => 'Alfons Christian Hardjana, S.Ds., M.A.', // S1 DKV
    23 => 'Dr.,Ir. Robert Riyanto W., M.T.', // S2 Arsitektur
    24 => 'Prof., Dr.-Ing., L.M.F. Purwanto', // S3 Arsitektur

    // FIKOM
    1 => 'Dr. Albertus Dwiyoga Widiantoro, S.Kom., M.Kom.', // S1 Teknik Informatika (email: ti@unika.ac.id)
    2 => 'Yonathan Purbo Santosa, S.Kom., M.Sc.', // S1 Sistem Informasi (email: si@unika.ac.id)

    // FEB
    4 => 'G. Freddy Koeswoyo, SE., M.Si', // S1 Akuntansi
    5 => 'Bayu Prestianto, SE., MM', // S1 Manajemen
    19 => 'Prof. MY. Dwi Hayu Agustini, MBA, Ph.D', // S2 Manajemen
    25 => 'Dr. Elizabeth Lucky Maretha S., M.S', // S2 Akuntansi

    // FBS
    18 => 'Dra. Cecilia Titiek Murniati, M.A., Ph.D', // S1 Sastra Inggris

    // FT
    9 => 'Ir. Gabriel J.P. Ghewa, ST., MT', // S1 Teknik Sipil
    10 => 'Dr.Ir. Leonardus Heru Pratomo, S.T., M.T.', // S1 Teknik Elektro
    31 => 'Dr.Ir T. Brenda Chandrawati, S.T., M.T', // Profesi Insinyur

    // FPSI
    11 => 'Eugenius Tintus Reinaldi, S.Psi., M.Psi.', // S1 Psikologi
    20 => 'Dr. Cicilia Tanti Utami, S.Psi., M.A.', // S2 Psikologi
    30 => 'Dr. Suparmi, M.Si', // Profesi Psikolog

    // FHK
    12 => 'Prof. Rika Saraswati, S.H., CN., M.Hum. Ph.D', // S1 Ilmu Hukum
    13 => 'Adrianus Bintang Hanto Nugroho, S.E., M.A.', // S1 Ilmu Komunikasi
    26 => 'Dr. Marcella Elwina S., SH., M.Hum', // S2 Ilmu Hukum

    // FTP
    14 => 'Ir. Gelbert Jethro Sanyoto, ST, MT', // S1 Teknologi Pangan
    32 => 'Dr. Dra. Laksmi Hartajanie, MP', // S2 Teknologi Pangan

    // FK
    16 => 'Dr. dr. Fransisca Pramesshinta Hardimarta, M.Si.Med', // S1 Kedokteran
    17 => 'dr. Eviana Budiartanti Sutanto, M.Biomed', // Profesi Dokter

    // FITL
    27 => 'B.Y. Arya Wastunimpuna, ST., M.Ars.', // S1 RIL
    28 => 'Hotmauli Sidabalok, S.H., CN., M.H., Ph.D', // S2 Lingkungan dan Perkotaan
    29 => 'Prof. Dr. Theresia Dwi Hastuti S.E., M.Si., AK., CA., CPA., CGAA.', // S3 Ilmu Lingkungan
];

$stmtKaprodi = $db->prepare("UPDATE prodis SET nama_kaprodi = ? WHERE id = ?");
foreach ($kaprodiData as $prodiId => $namaKaprodi) {
    $stmtKaprodi->execute([$namaKaprodi, $prodiId]);
    echo "  [OK] Prodi ID {$prodiId}: {$namaKaprodi}\n";
}

echo "\nSelesai! Seluruh nama Dekan dan Kaprodi telah disinkronkan sesuai data Excel.\n";
