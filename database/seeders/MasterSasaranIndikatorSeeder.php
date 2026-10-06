<?php

namespace Database\Seeders;

use App\Models\IndikatorKinerja;
use App\Models\SasaranKinerja;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class MasterSasaranIndikatorSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = TahunAnggaran::updateOrCreate(
            ['tahun' => '2026'],
            ['status' => 'aktif']
        );

        $adminId = User::where('role', 'admin')->value('id');
        $pimpinanId = User::where('role', 'pimpinan')->value('id');

        $data = [
            [1, 'Meningkatnya kualitas perencanaan dan anggaran', [
                ['a', 'Nilai Kinerja Anggaran', '85.7', 'Point'],
            ]],
            [2, 'Meningkatnya kualitas pengelolaan tata persuratan, arsip dan layanan pengadaan barang jasa', [
                ['a', 'Persentase Digitalisasi Arsip dan mudah di akses', '46', '%'],
                ['b', 'Persentase sarana dan prasarana perkantoran yang dikembangkan berbasis roadmap', '52', '%'],
            ]],
            [3, 'Meningkatnya layanan informasi dan dokumentasi', [
                ['a', 'Tingkat kematangan penyelenggaraan PPID', '96', 'Point'],
                ['b', 'Persentase pemberitaan negatif tentang Kemenag yang discounter', '88', '%'],
                ['c', 'Persentase peningkatan jumlah konten keagamaan dan pendidikan yang dipublikasi', '45', '%'],
            ]],
        ];

        foreach ($data as [$noUrut, $sasaranKegiatan, $indikators]) {
            $sasaran = SasaranKinerja::updateOrCreate(
                ['tahun_anggaran_id' => $tahun->id, 'no_urut' => $noUrut],
                [
                    'sasaran_kegiatan' => $sasaranKegiatan,
                    'status_approval' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => $pimpinanId,
                    'created_by' => $adminId,
                ]
            );

            foreach ($indikators as [$kodeSub, $indikatorKinerja, $targetDefault, $satuan]) {
                IndikatorKinerja::updateOrCreate(
                    ['sasaran_id' => $sasaran->id, 'kode_sub' => $kodeSub],
                    [
                        'indikator_kinerja' => $indikatorKinerja,
                        'target_default' => $targetDefault,
                        'satuan' => $satuan,
                    ]
                );
            }
        }
    }
}
