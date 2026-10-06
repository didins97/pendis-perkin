<?php

namespace Database\Seeders;

use App\Models\IndikatorKinerja;
use App\Models\RealisasiPerkin;
use App\Models\User;
use Illuminate\Database\Seeder;

class RealisasiPerkinSeeder extends Seeder
{
    public function run(): void
    {
        $guru = User::query()->where('role', 'guru')->first();
        $indikator = IndikatorKinerja::query()->first();

        if (! $guru || ! $indikator) {
            return;
        }

        RealisasiPerkin::updateOrCreate(
            [
                'user_id' => $guru->id,
                'master_indikator_id' => $indikator->id,
            ],
            [
                'realisasi_capaian' => '88%',
                'file_eviden' => 'realisasi-perkins/example-eviden.pdf',
                'catatan_guru' => 'Eviden upload sample untuk pelaporan realisasi PERKIN.',
                'status_verifikasi' => 'pending',
            ]
        );
    }
}
