<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SekolahAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // 1. SEED DATA SEKOLAH
        // ==========================================
        $sekolahsData = [
            // Madrasah Ibtidaiyah (MI)
            ['npsn' => '60000001', 'nama_sekolah' => 'MIN 1 Morotai', 'alamat' => 'Kabupaten Pulau Morotai'],
            ['npsn' => '60000002', 'nama_sekolah' => 'MIN 2 Morotai', 'alamat' => 'Kabupaten Pulau Morotai'],
            ['npsn' => '60000003', 'nama_sekolah' => 'MIN Daruba', 'alamat' => 'Daruba, Kabupaten Pulau Morotai'],
            ['npsn' => '60000004', 'nama_sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai', 'alamat' => 'Kabupaten Pulau Morotai'],

            // Madrasah Tsanawiyah (MTs)
            ['npsn' => '60000005', 'nama_sekolah' => 'MTsN 1 Morotai', 'alamat' => 'Kabupaten Pulau Morotai'],
            ['npsn' => '60000006', 'nama_sekolah' => 'MTsN 2 Morotai', 'alamat' => 'Kabupaten Pulau Morotai'],
            ['npsn' => '60000007', 'nama_sekolah' => 'MTsS Kemenag Kabupaten Pulau Morotai', 'alamat' => 'Kabupaten Pulau Morotai'],

            // Madrasah Aliyah (MA)
            ['npsn' => '60000008', 'nama_sekolah' => 'MA Nurul Yakin Sangowo', 'alamat' => 'Sangowo, Kabupaten Pulau Morotai'],
            ['npsn' => '60000009', 'nama_sekolah' => 'MAS Gotalamo', 'alamat' => 'Gotalamo, Kabupaten Pulau Morotai'],
            ['npsn' => '60000010', 'nama_sekolah' => 'MAS Kemenag Kabupaten Pulau Morotai', 'alamat' => 'Kabupaten Pulau Morotai'],
        ];

        $sekolahMap = [];
        foreach ($sekolahsData as $sData) {
            $sekolah = Sekolah::updateOrCreate(
                ['npsn' => $sData['npsn']],
                $sData
            );
            $sekolahMap[$sekolah->nama_sekolah] = $sekolah->id;
        }

        // ==========================================
        // 2. SEED DATA USER (ADMIN & PIMPINAN)
        // ==========================================
        $defaultPassword = Hash::make('password');

        // Admin Kemenag
        User::updateOrCreate(
            ['email' => 'pendis@app.com'],
            [
                'name'              => 'Seksi Pendis Morotai',
                'email_verified_at' => now(),
                'password'          => $defaultPassword,
                'role'              => 'admin',
                'nip'               => '198501012010011001',
                'sekolah_id'        => null,
                'remember_token'    => Str::random(10),
            ]
        );

        // Pimpinan Pembina
        User::updateOrCreate(
            ['email' => 'kasipendis@app.com'],
            [
                'name'              => 'Kepala Seksi Pendis',
                'email_verified_at' => now(),
                'password'          => $defaultPassword,
                'role'              => 'pimpinan',
                'nip'               => '196806041997031003',
                'sekolah_id'        => null,
                'remember_token'    => Str::random(10),
            ]
        );

        // ==========================================
        // 3. SEED DATA USER (107 GURU DENGAN NIP AKTUAI)
        // ==========================================
        $gurus = [
            ['nama' => 'Abjan Sibua S.Pd', 'nip' => '196812311998031024', 'sekolah' => 'MA Nurul Yakin Sangowo'],
            ['nama' => 'Haiba Usman S.Ag', 'nip' => '196901011999032007', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Isra Rajak S.Pd.I', 'nip' => '197103131999032002', 'sekolah' => 'MTsS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Juniarti Husen S.Ag', 'nip' => '197106091999032001', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Safia Sinen S.Pd', 'nip' => '197203111999032002', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Hans Tundruang SE', 'nip' => '197204121999031003', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Wahidah M.Pd, S.Ag', 'nip' => '197207101999032001', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Nursina Fete S.Pd.I', 'nip' => '197210151999032003', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Drs. Jamin Muhammad Nur M.Pd', 'nip' => '196811212000031001', 'sekolah' => 'MAS Gotalamo'],
            ['nama' => 'Rohmat S.Pd', 'nip' => '197410072000031001', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Petikai Srino Wasolo S.Pd.I', 'nip' => '197505052000031002', 'sekolah' => 'MAS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Risal Ali, S.Pd.I', 'nip' => '197508152000031002', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Marhama Tarate S.Pd', 'nip' => '197603122000032003', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Mu\'min Hamsar S.Ag', 'nip' => '197607212000031001', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Farida Hadi', 'nip' => '196805162002122002', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Imran H. Abdullah', 'nip' => '196808172002121006', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Nurwaheng', 'nip' => '196903022002122002', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Abdul Haris Sibua S.Pd', 'nip' => '197305042002121004', 'sekolah' => 'MTsS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Nurhayat Bayan S.Ag', 'nip' => '197306062002122002', 'sekolah' => 'MAS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Asrul Djalil S.Pd', 'nip' => '197312302002121002', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Nurdja Naim', 'nip' => '197611032002122002', 'sekolah' => 'MIN Daruba'],
            ['nama' => 'Nurmila Sibua M.Pd., S.Pd.I', 'nip' => '197801042002122001', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Kamariah S.Pd.I', 'nip' => '197805172002122003', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Misnawati Lanony S.Pd', 'nip' => '197806282002122004', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Salfa Umar S.Pd.I', 'nip' => '197808082002122006', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Marhama Djaguna', 'nip' => '198103132002122002', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Hafid Bone S.Pd', 'nip' => '198106122002121003', 'sekolah' => 'MAS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Ismail IM S.Ag', 'nip' => '197103252003121002', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Maryana Bidullah', 'nip' => '196803022005012007', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Jun Naki', 'nip' => '197206122005011008', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Sahlan Lombo', 'nip' => '197305042005011009', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Nurbaya Sibua', 'nip' => '197410072005012006', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Nuraena Mide S.Pd', 'nip' => '197905052005012015', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Asmiyana Hi Usman S.Pd.', 'nip' => '197906152005012010', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Sajad Muhammad Yasin', 'nip' => '198006172005011005', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Nurul Magfira Hadi', 'nip' => '198009222005012005', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Eny Minarti Djufri S.Pd', 'nip' => '198102022005012007', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Sitinur Khairun', 'nip' => '198112022005012008', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Widyawati Umar S.Pd', 'nip' => '198207122005012009', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Heny Tanimbar', 'nip' => '198209212005012006', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Ruhul Ahmar Sibua S.Hum', 'nip' => '198211022005011003', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Siti Patmawati', 'nip' => '198212132005012003', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Ike Rohmawati S.Pd.I', 'nip' => '198305042005012008', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Rosita Paturo', 'nip' => '198308052005012009', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Hikmawati', 'nip' => '198309192005012002', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Musadik Sibua', 'nip' => '198311132005011004', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Aljufri Yunus', 'nip' => '198501062005011001', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Waode Salmatia Alimu', 'nip' => '198008272007102001', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Muzakir M. Usman S.Pd.I', 'nip' => '197805102009011018', 'sekolah' => 'MAS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Yuningsih Wowa', 'nip' => '198206252009012009', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Mulyati Dano S.Pd.I', 'nip' => '198211242009012005', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Arbi Turkie S.Pd', 'nip' => '198402012009011005', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Muhammad Riski Sibua S.Pd', 'nip' => '198502282009011002', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Lisa Taib S.Ag', 'nip' => '197607262009122001', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Sitti Siruang S.Pd', 'nip' => '198508282009122003', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Mirna J. Ngongira S.Pd', 'nip' => '198305082010012028', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Nur Qamaria Soamole S.Pd', 'nip' => '198711102011012019', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Sabenna Dahang S.Pd', 'nip' => '198511202014112001', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Mislia Dara S.Pd', 'nip' => '198606042014112002', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Rahima Taba S.S', 'nip' => '198205162014122001', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Susmita Jainal S.Pd', 'nip' => '198503112014122001', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Fazrul M. Yasin S.Pd.', 'nip' => '198507202014121001', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Nuraini Jobubu S.Pd.B', 'nip' => '198304032019032008', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Festty Silvia S.Pd', 'nip' => '198502202019032010', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Ibrahim Musapao S.Pd.B', 'nip' => '198508222019031006', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Gamar Bone S.Pd.I', 'nip' => '198510252019032013', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Juwita Abd Latif', 'nip' => '198512112019032012', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Muhlis Lastori S.Pd.I', 'nip' => '198601052019031008', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Nasidaria Taher S.Pd', 'nip' => '198601142019032010', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Harsandi Samsudin S.Sy', 'nip' => '198607212019031006', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Hariyanti Wisnu', 'nip' => '198701012019032014', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Malka Muhammad S.Pd.', 'nip' => '198702162019032008', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Fihir Tanimbar S.Pd', 'nip' => '198703112019031008', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Firja S. Panggola', 'nip' => '198704252019032009', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Rudi Darsono Samiun', 'nip' => '198705292019031008', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Asma Baba S.Pd.', 'nip' => '198709012019032008', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Mirna Salam S.Si', 'nip' => '198801262019032009', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Julkifli Abdullah S.Pd', 'nip' => '198805292019031007', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Murni Upara', 'nip' => '198810052019032010', 'sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai'],
            ['nama' => 'Turisah S.Pd', 'nip' => '198904092019032013', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Marniati Baco S.Pd', 'nip' => '198906052019032016', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Siti Jayanti Turkie S.Pd', 'nip' => '198907082019032013', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Siti Masita Sibua SH', 'nip' => '198908182019032014', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Muhammad Ardi Sumtaki S.IP', 'nip' => '198908292019031008', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Sri Murti Karim S.Pd.', 'nip' => '198909192019032015', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Syukur M. Kuylo S.Pd', 'nip' => '198911072019031011', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Baharuddin Umar S.Pd.I', 'nip' => '198912232019031010', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Selayanti Daeng Mangaseng', 'nip' => '199003202019032016', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Rahayu M. Rio S.Pd', 'nip' => '199004072019032018', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Darmawati Pake S.Pd', 'nip' => '199004122019032018', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Siti Julaiha Sangaji S.Pd', 'nip' => '199007012019032016', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Irham Sadek S.Pd', 'nip' => '199008202019031005', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Aditia R.S. Budian S.Pd', 'nip' => '199009132019031007', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Susi Susanti Posu S.Pd', 'nip' => '199009192019032015', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Sutni Faidah Pina S.Pd', 'nip' => '199010062019032015', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Fajri Azis S.Pd', 'nip' => '199102262019031006', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Samsuriati S.Pd', 'nip' => '199105152019032016', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Widayanti Soleman S.Pd.', 'nip' => '199108162019032009', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Halimah Baba S.Pd', 'nip' => '199109152019032011', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Safria Balaha S.Pd', 'nip' => '199110202019032012', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Rifai Ali S.Sos', 'nip' => '199201082019031008', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Irfandi Luange S.Pd', 'nip' => '199205162019031003', 'sekolah' => 'MTsN 2 Morotai'],
            ['nama' => 'Nuru Mado S.Pd', 'nip' => '199208032019032014', 'sekolah' => 'MIN 1 Morotai'],
            ['nama' => 'Sri Susiana Dj Hi. Djabid S.Pd', 'nip' => '199208222019032014', 'sekolah' => 'MIN 2 Morotai'],
            ['nama' => 'Ridwan Sibua S.Pd.I', 'nip' => '199209142019031005', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Muhammad Fajri S.Pd.I', 'nip' => '199302112019031006', 'sekolah' => 'MTsN 1 Morotai'],
            ['nama' => 'Abdul Haji Siauta S.Pd', 'nip' => '199308072019031008', 'sekolah' => 'MIN 2 Morotai'],
        ];

        foreach ($gurus as $guru) {
            // Format email otomatis dari nama depan dan NIP agar unik
            $cleanName = Str::slug(explode(' ', $guru['nama'])[0], '');
            $email = $cleanName . '.' . substr($guru['nip'], -4) . '@guru.sch.id';

            User::updateOrCreate(
                ['nip' => $guru['nip']], // Unique identifier menggunakan NIP
                [
                    'name'              => $guru['nama'],
                    'email'             => $email,
                    'email_verified_at' => now(),
                    'password'          => $defaultPassword,
                    'role'              => 'pegawai',
                    'nip'               => $guru['nip'],
                    'sekolah_id'        => $sekolahMap[$guru['sekolah']] ?? null,
                    'remember_token'    => Str::random(10),
                ]
            );
        }
    }
}