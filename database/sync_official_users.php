<?php
/**
 * Sinkronisasi Data Akun Pengguna Resmi SPMI PPEPP UNIKA Soegijapranata
 * - 6 Akun Tim LPM
 * - 10 Akun Koordinator GPM Fakultas
 * - 37 Akun Official Auditee (10 Dekan Fakultas + 27 Kaprodi Program Studi)
 *
 * Password default: password123
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "========================================================\n";
    echo "  SINKRONISASI 53 PENGGUNA RESMI SPMI PPEPP UNIKA\n";
    echo "========================================================\n\n";

    $defaultPassword = 'password123';
    $passwordHash = password_hash($defaultPassword, PASSWORD_BCRYPT);

    $usersData = [
        // ==========================================
        // 1. DATA EMAIL TIM LPM (6 AKUN)
        // ==========================================
        [
            'name' => 'Kepala Lembaga Penjaminan Mutu',
            'email' => 'lpm@unika.ac.id',
            'role' => 'kepala_lpm',
            'fakultas_id' => null,
            'prodi_id' => null
        ],
        [
            'name' => 'Sekretaris Lembaga Penjaminan Mutu',
            'email' => 'lpm.sekretaris@unika.ac.id',
            'role' => 'sekretaris_lpm',
            'fakultas_id' => null,
            'prodi_id' => null
        ],
        [
            'name' => 'Ketua Pusat Pengembangan Sistem Penjaminan Mutu',
            'email' => 'lpm.spm@unika.ac.id',
            'role' => 'ketua_pusat_spm',
            'fakultas_id' => null,
            'prodi_id' => null
        ],
        [
            'name' => 'Ketua Pusat Audit Mutu Internal',
            'email' => 'lpm.ami@unika.ac.id',
            'role' => 'ketua_pusat_ami',
            'fakultas_id' => null,
            'prodi_id' => null
        ],
        [
            'name' => 'Ketua Pusat Pemeringkatan',
            'email' => 'lpm.pemeringkatan@unika.ac.id',
            'role' => 'ketua_pusat_pemeringkatan',
            'fakultas_id' => null,
            'prodi_id' => null
        ],
        [
            'name' => 'Admin (TU LPM)',
            'email' => 'tu.lpm@unika.ac.id',
            'role' => 'super_admin',
            'fakultas_id' => null,
            'prodi_id' => null
        ],

        // ==========================================
        // 2. DATA EMAIL GPM FAKULTAS (10 AKUN)
        // ==========================================
        [
            'name' => 'Dr. Dra. Alberta Rika Pratiwi, M.Si',
            'email' => 'pratiwi@unika.ac.id',
            'role' => 'gpm',
            'fakultas_id' => 8, // FTP
            'prodi_id' => null
        ],
        [
            'name' => 'Ir. Budi Santosa, MT',
            'email' => 'budi@unika.ac.id',
            'role' => 'gpm',
            'fakultas_id' => 5, // FT
            'prodi_id' => null
        ],
        [
            'name' => 'Erdhi Widyarto Nugroho, ST., MT',
            'email' => 'erdhi@unika.ac.id',
            'role' => 'gpm',
            'fakultas_id' => 2, // FIKOM
            'prodi_id' => null
        ],
        [
            'name' => 'Ch. Tri Hardjanti, SE., M.Si',
            'email' => 'hardjanti@unika.ac.id', // domain resmi kampus
            'role' => 'gpm',
            'fakultas_id' => 3, // FEB
            'prodi_id' => null
        ],
        [
            'name' => 'Rafael Bonaventura Josemaria Escriva Theo Adi Negoro, SH., MH',
            'email' => 'negorotheo@unika.ac.id',
            'role' => 'gpm',
            'fakultas_id' => 7, // FHK
            'prodi_id' => null
        ],
        [
            'name' => 'Dr. Dra. Margaretha Sih Setija Utami, M.Kes.',
            'email' => 'cicih@unika.ac.id',
            'role' => 'gpm',
            'fakultas_id' => 6, // FPSI
            'prodi_id' => null
        ],
        [
            'name' => 'Dr. Ir. Antonius Ardiyanto, MT',
            'email' => 'ardiyanto@unika.ac.id',
            'role' => 'gpm',
            'fakultas_id' => 1, // FAD
            'prodi_id' => null
        ],
        [
            'name' => 'dr. To Lidwina Prillya Indra Christyana, M.Biomed',
            'email' => 'lidwina@unika.ac.id',
            'role' => 'gpm',
            'fakultas_id' => 9, // FK
            'prodi_id' => null
        ],
        [
            'name' => 'Dr. Dra. Ekawati Marhaenny Dukut, M.Hum',
            'email' => 'ekawati@unika.ac.id',
            'role' => 'gpm',
            'fakultas_id' => 4, // FBS
            'prodi_id' => null
        ],
        [
            'name' => 'Dhiyan Krishna Wardani, ST., M.U.E',
            'email' => 'dhiyan.krishna@unika.ac.id',
            'role' => 'gpm',
            'fakultas_id' => 10, // FITL
            'prodi_id' => null
        ],

        // ==========================================
        // 3. DATA EMAIL AUDITEE: DEKAN (10 AKUN)
        // ==========================================
        [
            'name' => 'Dr. Kristiana Haryanti, M.Si',
            'email' => 'psikologi@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 6, // FPSI
            'prodi_id' => null
        ],
        [
            'name' => 'Inneke Hartono, S.TP., M.Sc',
            'email' => 'ftp@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 8, // FTP
            'prodi_id' => null
        ],
        [
            'name' => 'dr. Edward Hartono, M.ARS',
            'email' => 'dekanfk@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 9, // FK
            'prodi_id' => null
        ],
        [
            'name' => 'Dr. B. Tyas Susanti, MA',
            'email' => 'fad@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 1, // FAD
            'prodi_id' => null
        ],
        [
            'name' => 'Dr. B. Resti Nurhayati, S.H. M.Hum.',
            'email' => 'fhk@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 7, // FHK
            'prodi_id' => null
        ],
        [
            'name' => 'Dr. Monica Palupi M. S.E., M.M.',
            'email' => 'feb@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 3, // FEB
            'prodi_id' => null
        ],
        [
            'name' => 'Prof. Dr. F. Ridwan Sanjaya, S.E., S.Kom., MS.IEC.',
            'email' => 'fikom@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 2, // FIKOM
            'prodi_id' => null
        ],
        [
            'name' => 'G.M. Adhyanggono, S.S., M.A., Ph.D',
            'email' => 'dekanfbs@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 4, // FBS
            'prodi_id' => null
        ],
        [
            'name' => 'Prof. Dr. Ir. Slamet Riyadi, M.T.',
            'email' => 'sipil3@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 5, // FT
            'prodi_id' => null
        ],
        [
            'name' => 'B. Danang Setianto, S.H., LL.M., Ph.D',
            'email' => 'fitl@unika.ac.id',
            'role' => 'dekan',
            'fakultas_id' => 10, // FITL
            'prodi_id' => null
        ],

        // ==========================================
        // 4. DATA EMAIL AUDITEE: KAPRODI (27 AKUN)
        // ==========================================
        // --- FPSI (Fakultas 6) ---
        [
            'name' => 'Eugenius Tintus Reinaldi, S.Psi., M.Psi.',
            'email' => 'kaprodipsikologi@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 6,
            'prodi_id' => 11 // S1 Psikologi
        ],
        [
            'name' => 'Dr. Cicilia Tanti Utami, S.Psi., M.A.',
            'email' => 'mspsi@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 6,
            'prodi_id' => 20 // S2 Magister Psikologi
        ],
        [
            'name' => 'Dr. Suparmi, M.Si',
            'email' => 'kaprodiprofpsi@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 6,
            'prodi_id' => 30 // Profesi Pendidikan Profesi Psikolog
        ],

        // --- FTP (Fakultas 8) ---
        [
            'name' => 'Ir. Gelbert Jethro Sanyoto, ST, MT',
            'email' => 'tp@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 8,
            'prodi_id' => 14 // S1 Teknologi Pangan
        ],
        [
            'name' => 'Dr. Dra. Laksmi Hartajanie, MP',
            'email' => 'pmtp@unika.ac.id', // email utama prodi Magister TP
            'role' => 'kaprodi',
            'fakultas_id' => 8,
            'prodi_id' => 32 // S2 Magister Teknologi Pangan
        ],

        // --- FK (Fakultas 9) ---
        [
            'name' => 'Dr. dr. Fransisca Pramesshinta Hardimarta, M.Si.Med',
            'email' => 'prodi.pddok@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 9,
            'prodi_id' => 16 // S1 Kedokteran
        ],
        [
            'name' => 'dr. Eviana Budiartanti Sutanto, M.Biomed',
            'email' => 'profesi.fk@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 9,
            'prodi_id' => 17 // Profesi Pendidikan Profesi Dokter
        ],

        // --- FAD (Fakultas 1) ---
        [
            'name' => 'Dr. Ir. V.G. Sri Rejeki, M.T.',
            'email' => 'arsitektur@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 1,
            'prodi_id' => 7 // S1 Arsitektur
        ],
        [
            'name' => 'Alfons Christian Hardjana, S.Ds., M.A.',
            'email' => 'dkv@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 1,
            'prodi_id' => 8 // S1 DKV
        ],
        [
            'name' => 'Dr.,Ir. Robert Riyanto W., M.T.',
            'email' => 'pmta@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 1,
            'prodi_id' => 23 // S2 Magister Arsitektur
        ],
        [
            'name' => 'Prof., Dr.-Ing., L.M.F. Purwanto',
            'email' => 'psda@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 1,
            'prodi_id' => 24 // S3 Doktor Arsitektur
        ],

        // --- FHK (Fakultas 7) ---
        [
            'name' => 'Prof. Rika Saraswati, S.H., CN., M.Hum. Ph.D',
            'email' => 'hukum@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 7,
            'prodi_id' => 12 // S1 Ilmu Hukum
        ],
        [
            'name' => 'Adrianus Bintang Hanto Nugroho, S.E., M.A.',
            'email' => 'komunikasi@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 7,
            'prodi_id' => 13 // S1 Ilmu Komunikasi
        ],
        [
            'name' => 'Dr. Marcella Elwina S., SH., M.Hum',
            'email' => 'mh.kes@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 7,
            'prodi_id' => 26 // S2 Magister Hukum
        ],

        // --- FEB (Fakultas 3) ---
        [
            'name' => 'G. Freddy Koeswoyo, SE., M.Si',
            'email' => 'akuntansi@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 3,
            'prodi_id' => 4 // S1 Akuntansi
        ],
        [
            'name' => 'Bayu Prestianto, SE., MM',
            'email' => 'manajemen@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 3,
            'prodi_id' => 5 // S1 Manajemen
        ],
        [
            'name' => 'Dr. Elizabeth Lucky Maretha S., M.S',
            'email' => 'kaprog.maksi@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 3,
            'prodi_id' => 25 // S2 Magister Akuntansi
        ],
        [
            'name' => 'Prof. MY. Dwi Hayu Agustini, MBA, Ph.D',
            'email' => 'tu.mm@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 3,
            'prodi_id' => 19 // S2 Magister Manajemen
        ],

        // --- FIKOM (Fakultas 2) ---
        [
            'name' => 'Dr. Albertus Dwiyoga Widiantoro, S.Kom., M.Kom.',
            'email' => 'ti@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 2,
            'prodi_id' => 1 // S1 Teknik Informatika
        ],
        [
            'name' => 'Yonathan Purbo Santosa, S.Kom., M.Sc.',
            'email' => 'si@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 2,
            'prodi_id' => 2 // S1 Sistem Informasi
        ],

        // --- FBS (Fakultas 4) ---
        [
            'name' => 'Dra. Cecilia Titiek Murniati, M.A., Ph.D',
            'email' => 'sastrainggris@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 4,
            'prodi_id' => 18 // S1 Sastra Inggris
        ],

        // --- FT (Fakultas 5) ---
        [
            'name' => 'Ir. Gabriel J.P. Ghewa, ST., MT',
            'email' => 'sipil1@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 5,
            'prodi_id' => 9 // S1 Teknik Sipil
        ],
        [
            'name' => 'Dr.Ir. Leonardus Heru Pratomo, S.T., M.T.',
            'email' => 'elektro.unika@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 5,
            'prodi_id' => 10 // S1 Teknik Elektro
        ],
        [
            'name' => 'Dr.Ir T. Brenda Chandrawati, S.T., M.T',
            'email' => 'gpm.ppi@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 5,
            'prodi_id' => 31 // Profesi Insinyur
        ],

        // --- FITL (Fakultas 10) ---
        [
            'name' => 'B.Y. Arya Wastunimpuna, ST., M.Ars.',
            'email' => 'ril@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 10,
            'prodi_id' => 27 // S1 Rekayasa Infrastruktur dan Lingkungan
        ],
        [
            'name' => 'Hotmauli Sidabalok, S.H., CN., M.H., Ph.D',
            'email' => 'pmlp@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 10,
            'prodi_id' => 28 // S2 Magister Lingkungan dan Perkotaan
        ],
        [
            'name' => 'Prof. Dr. Theresia Dwi Hastuti S.E., M.Si., AK., CA., CPA., CGAA.',
            'email' => 'kaprodipdil@unika.ac.id',
            'role' => 'kaprodi',
            'fakultas_id' => 10,
            'prodi_id' => 29 // S3 Doktor Ilmu Lingkungan
        ],
    ];

    $insertSql = "INSERT INTO users (name, email, password, password_plain, role, fakultas_id, prodi_id, is_active, created_at, updated_at)
                  VALUES (:name, :email, :password, :password_plain, :role, :fakultas_id, :prodi_id, 1, NOW(), NOW())
                  ON DUPLICATE KEY UPDATE
                    name = VALUES(name),
                    password = VALUES(password),
                    password_plain = VALUES(password_plain),
                    role = VALUES(role),
                    fakultas_id = VALUES(fakultas_id),
                    prodi_id = VALUES(prodi_id),
                    is_active = 1,
                    updated_at = NOW()";

    $stmt = $db->prepare($insertSql);

    $inserted = 0;
    $updated = 0;

    foreach ($usersData as $idx => $user) {
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $checkStmt->execute([':email' => $user['email']]);
        $exists = $checkStmt->fetchColumn();

        $stmt->execute([
            ':name' => $user['name'],
            ':email' => $user['email'],
            ':password' => $passwordHash,
            ':password_plain' => $defaultPassword,
            ':role' => $user['role'],
            ':fakultas_id' => $user['fakultas_id'],
            ':prodi_id' => $user['prodi_id'],
        ]);

        $num = $idx + 1;
        if ($exists) {
            $updated++;
            echo "[$num/53] UPDATED: {$user['name']} ({$user['email']}) -> Role: {$user['role']}\n";
        } else {
            $inserted++;
            echo "[$num/53] INSERTED: {$user['name']} ({$user['email']}) -> Role: {$user['role']}\n";
        }
    }

    echo "\n--------------------------------------------------------\n";
    echo "Ringkasan Hasil:\n";
    echo "  - Total Akun Diproses : " . count($usersData) . "\n";
    echo "  - Pengguna Baru Masuk : $inserted\n";
    echo "  - Pengguna Diperbarui : $updated\n";
    echo "  - Password Default    : $defaultPassword\n";
    echo "========================================================\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

