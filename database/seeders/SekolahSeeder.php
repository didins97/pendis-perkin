<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sekolahs = [
            // Madrasah Ibtidaiyah (MI)
            [
                'npsn'         => '60000001',
                'nama_sekolah' => 'MIN 1 Morotai',
                'alamat'       => 'Kabupaten Pulau Morotai',
            ],
            [
                'npsn'         => '60000002',
                'nama_sekolah' => 'MIN 2 Morotai',
                'alamat'       => 'Kabupaten Pulau Morotai',
            ],
            [
                'npsn'         => '60000003',
                'nama_sekolah' => 'MIN Daruba',
                'alamat'       => 'Daruba, Kabupaten Pulau Morotai',
            ],
            [
                'npsn'         => '60000004',
                'nama_sekolah' => 'MIS Kemenag Kabupaten Pulau Morotai',
                'alamat'       => 'Kabupaten Pulau Morotai',
            ],

            // Madrasah Tsanawiyah (MTs)
            [
                'npsn'         => '60000005',
                'nama_sekolah' => 'MTsN 1 Morotai',
                'alamat'       => 'Kabupaten Pulau Morotai',
            ],
            [
                'npsn'         => '60000006',
                'nama_sekolah' => 'MTsN 2 Morotai',
                'alamat'       => 'Kabupaten Pulau Morotai',
            ],
            [
                'npsn'         => '60000007',
                'nama_sekolah' => 'MTsS Kemenag Kabupaten Pulau Morotai',
                'alamat'       => 'Kabupaten Pulau Morotai',
            ],

            // Madrasah Aliyah (MA)
            [
                'npsn'         => '60000008',
                'nama_sekolah' => 'MA Nurul Yakin Sangowo',
                'alamat'       => 'Sangowo, Kabupaten Pulau Morotai',
            ],
            [
                'npsn'         => '60000009',
                'nama_sekolah' => 'MAS Gotalamo',
                'alamat'       => 'Gotalamo, Kabupaten Pulau Morotai',
            ],
            [
                'npsn'         => '60000010',
                'nama_sekolah' => 'MAS Kemenag Kabupaten Pulau Morotai',
                'alamat'       => 'Kabupaten Pulau Morotai',
            ],
        ];

        foreach ($sekolahs as $sekolah) {
            Sekolah::updateOrCreate(
                ['npsn' => $sekolah['npsn']],
                $sekolah
            );
        }
    }
}
