<?php

namespace Database\Seeders;

use App\Models\MasterKegiatan;
use App\Models\MasterProgram;
use App\Models\TahunAnggaran;
use Illuminate\Database\Seeder;

class MasterAnggaranSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = TahunAnggaran::updateOrCreate(
            ['tahun' => '2026'],
            ['status' => 'aktif']
        );

        $program = MasterProgram::updateOrCreate(
            ['tahun_anggaran_id' => $tahun->id, 'kode_program' => '025.01.WA'],
            [
                'nama_program' => 'Program Dukungan Manajemen',
                'created_by' => 1,
            ]
        );

        $program->kegiatans()->delete();
        $program->kegiatans()->createMany([
            ['kode_kegiatan' => '2100', 'nama_kegiatan' => 'Pembinaan Administrasi Keuangan dan BMN', 'anggaran' => 2102438000.00],
            ['kode_kegiatan' => '2103', 'nama_kegiatan' => 'Pembinaan Administrasi Umum', 'anggaran' => 539632000.00],
            ['kode_kegiatan' => '2125', 'nama_kegiatan' => 'Dukungan Manajemen dan Pelaksanaan Tugas Teknis Lainnya Bimas Islam', 'anggaran' => 166642000.00],
            ['kode_kegiatan' => '2135', 'nama_kegiatan' => 'Dukungan Manajemen Pendidikan dan Pelayanan Tugas Teknis Lainnya Pendidikan Islam', 'anggaran' => 526441000.00],
            ['kode_kegiatan' => '5100', 'nama_kegiatan' => 'Penyelenggaraan Administrasi Perkantoran Pendidikan Bimas Kristen', 'anggaran' => 50198000.00],
            ['kode_kegiatan' => '6708', 'nama_kegiatan' => 'Dukungan Manajemen Pendidikan', 'anggaran' => 12517926000.00],
        ]);

        $program2 = MasterProgram::updateOrCreate(
            ['tahun_anggaran_id' => $tahun->id, 'kode_program' => '025.01.DC'],
            [
                'nama_program' => 'Program Kerukunan Umat dan Layanan',
                'created_by' => 1,
            ]
        );

        $program2->kegiatans()->delete();
        $program2->kegiatans()->create([
            'kode_kegiatan' => '1001',
            'nama_kegiatan' => 'Kegiatan Kerukunan Umat dan Layanan',
            'anggaran' => 151748000.00,
        ]);
    }
}
