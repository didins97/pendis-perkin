<?php

use App\Models\Sekolah;
use App\Models\IndikatorKinerja;
use App\Models\RealisasiPerkin;
use App\Models\SasaranKinerja;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pegawai dashboard shows profile completion percentage from saved account and profile data', function () {
    $school = Sekolah::create([
        'npsn' => '87654321',
        'nama_sekolah' => 'Sekolah Profil Lengkap',
    ]);
    $pegawai = User::factory()->create([
        'role' => 'pegawai',
        'nip' => '198001012005011001',
        'nomor_wa' => '081234567890',
        'sekolah_id' => $school->id,
    ]);
    $pegawai->profilPegawai()->create([
        'nuptk' => '1234567890123456',
        'nrg' => '998877',
        'pangkat_golongan' => 'III/a',
        'status_kepegawaian' => 'PPPK',
        'jabatan' => 'Pegawai',
        'tugas_tambahan' => 'Wali Kelas',
        'berkas_sk_pangkat' => 'profil-pegawai/sk-pangkat.pdf',
        'berkas_sk_mengajar' => 'profil-pegawai/sk-mengajar.pdf',
        'berkas_serdik' => 'profil-pegawai/serdik.pdf',
    ]);
    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
        'status_approval' => 'approved',
    ]);
    $sasaran = SasaranKinerja::create([
        'tahun_anggaran_id' => $tahun->id,
        'no_urut' => 1,
        'sasaran_kegiatan' => 'Sasaran Test',
    ]);
    $indikator = IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'indikator_kinerja' => 'Indikator Eviden Terbaru',
        'target_default' => '1 kegiatan',
    ]);
    RealisasiPerkin::create([
        'user_id' => $pegawai->id,
        'master_indikator_id' => $indikator->id,
        'realisasi_capaian' => '100%',
        'file_eviden' => 'evidence/terbaru.pdf',
        'status_verifikasi' => 'approved',
    ]);
    RealisasiPerkin::create([
        'user_id' => User::factory()->create(['role' => 'pegawai'])->id,
        'master_indikator_id' => $indikator->id,
        'realisasi_capaian' => '50%',
        'file_eviden' => 'evidence/pegawai-lain.pdf',
        'status_verifikasi' => 'pending',
    ]);

    $this->actingAs($pegawai)
        ->get(route('pegawai.dashboard'))
        ->assertOk()
        ->assertSee('Kelengkapan Profil')
        ->assertSee('14 dari 14 data profil sudah dilengkapi.')
        ->assertSee('100%', false)
        ->assertSee('aria-valuenow="100"', false)
        ->assertSee('Riwayat Eviden Terbaru')
        ->assertSee('Indikator Eviden Terbaru')
        ->assertSee('Disetujui')
        ->assertDontSee('50%')
        ->assertSee(route('profile'));
});
