<?php

namespace Database\Seeders;

use App\Models\IndikatorKinerja;
use App\Models\RealisasiPerkin;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class RealisasiPerkinSeeder extends Seeder
{
    private const TOTAL_RECORDS = 100;

    private const SAMPLE_FILE = 'realisasi-perkins/demo-eviden-seeder.pdf';

    public function run(): void
    {
        $pegawai = User::query()
            ->where('role', 'pegawai')
            ->where('status_aktif', true)
            ->orderBy('id')
            ->get();

        $indikators = IndikatorKinerja::query()
            ->whereHas('sasaran', fn ($query) => $query
                ->where('status_approval', 'approved')
                ->whereHas('tahunAnggaran', fn ($tahun) => $tahun->where('status_approval', 'approved')))
            ->orderBy('sasaran_id')
            ->orderBy('kode_sub')
            ->get();

        if ($pegawai->isEmpty()) {
            throw new RuntimeException('Tidak ada pegawai aktif untuk data realisasi. Jalankan SekolahAndUserSeeder terlebih dahulu.');
        }

        if ($indikators->isEmpty()) {
            throw new RuntimeException('Tidak ada indikator PERKIN yang disetujui. Jalankan MasterSasaranIndikatorSeeder terlebih dahulu.');
        }

        $this->ensureSampleFileExists();

        $verifierId = User::query()
            ->whereIn('role', ['pimpinan', 'admin'])
            ->orderByRaw("CASE role WHEN 'pimpinan' THEN 0 ELSE 1 END")
            ->value('id');

        for ($slot = 1; $slot <= self::TOTAL_RECORDS; $slot++) {
            $pegawaiIndex = ($slot - 1) % $pegawai->count();
            $indicatorIndex = intdiv($slot - 1, $pegawai->count()) % $indikators->count();
            $status = match ($slot % 10) {
                0, 1 => 'rejected',
                2, 3, 4, 5 => 'approved',
                default => 'pending',
            };
            $uploadedAt = now()
                ->subDays(($slot * 3) % 90)
                ->setTime(9 + ($slot % 8), ($slot * 7) % 60);
            $marker = sprintf('[SEED REALISASI PERKIN] Data demonstrasi #%03d', $slot);
            $indikator = $indikators[$indicatorIndex];

            RealisasiPerkin::firstOrCreate(
                [
                    'user_id' => $pegawai[$pegawaiIndex]->id,
                    'master_indikator_id' => $indikator->id,
                    'catatan_guru' => $marker,
                ],
                [
                    'realisasi_capaian' => $this->sampleAchievement($indikator),
                    'file_eviden' => self::SAMPLE_FILE,
                    'status_verifikasi' => $status,
                    'catatan_verifikator' => $status === 'rejected'
                        ? 'Data demonstrasi: mohon lengkapi atau perjelas dokumen eviden.'
                        : null,
                    'verified_by' => $status === 'pending' ? null : $verifierId,
                    'verified_at' => $status === 'pending' ? null : $uploadedAt->copy()->addDay(),
                    'created_at' => $uploadedAt,
                    'updated_at' => $uploadedAt,
                ]
            );
        }
    }

    private function ensureSampleFileExists(): void
    {
        $disk = Storage::disk('public');

        if (! $disk->exists(self::SAMPLE_FILE)) {
            $pdf = Pdf::loadHTML(
                '<html><body style="font-family: sans-serif; text-align: center; padding-top: 120px;">'
                .'<h1>Dokumen Eviden Demonstrasi</h1>'
                .'<p>File ini dibuat otomatis oleh RealisasiPerkinSeeder untuk data contoh.</p>'
                .'<p>Dokumen ini bukan eviden resmi.</p>'
                .'</body></html>'
            )->output();

            $disk->put(self::SAMPLE_FILE, $pdf);
        }
    }

    private function sampleAchievement(IndikatorKinerja $indikator): string
    {
        $value = 60 + ($indikator->id % 41);
        $unit = trim((string) $indikator->satuan);

        return $unit === '%' ? "{$value}%" : "{$value} {$unit}";
    }
}
