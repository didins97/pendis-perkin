<?php

use App\Models\IndikatorKinerja;
use App\Models\MasterKegiatan;
use App\Models\MasterProgram;
use App\Models\RealisasiPerkin;
use App\Models\SasaranKinerja;
use App\Models\Sekolah;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pegawai and admin evidence realisasi routes exist and render pages', function () {
    $pegawai = User::factory()->create([
        'role' => 'pegawai',
        'email' => 'guru.test@example.com',
        'sekolah_id' => null,
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
        'email' => 'admin.test@example.com',
        'sekolah_id' => null,
    ]);

    $this->actingAs($pegawai)
        ->get(route('pegawai.realisasi.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('admin.realisasi.index'))
        ->assertOk();
});

test('pegawai evidence history shows status summaries, revision notes and only own submissions', function () {
    $pegawai = User::factory()->create(['role' => 'pegawai']);
    $pegawaiLain = User::factory()->create(['role' => 'pegawai']);
    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
        'status_approval' => 'approved',
    ]);
    $sasaran = SasaranKinerja::create([
        'tahun_anggaran_id' => $tahun->id,
        'no_urut' => 1,
        'sasaran_kegiatan' => 'Sasaran Riwayat',
        'status_approval' => 'approved',
    ]);
    $indikator = IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'indikator_kinerja' => 'Indikator Riwayat Pegawai',
        'target_default' => '1 kegiatan',
    ]);

    foreach (['pending', 'approved', 'rejected'] as $status) {
        RealisasiPerkin::create([
            'user_id' => $pegawai->id,
            'master_indikator_id' => $indikator->id,
            'realisasi_capaian' => '100%',
            'file_eviden' => "evidence/{$status}.pdf",
            'status_verifikasi' => $status,
            'catatan_verifikator' => $status === 'rejected' ? 'Mohon unggah dokumen yang lebih jelas.' : null,
        ]);
    }

    RealisasiPerkin::create([
        'user_id' => $pegawaiLain->id,
        'master_indikator_id' => $indikator->id,
        'realisasi_capaian' => '50%',
        'file_eviden' => 'evidence/pegawai-lain.pdf',
        'status_verifikasi' => 'pending',
    ]);

    $this->actingAs($pegawai)
        ->get(route('pegawai.realisasi.index'))
        ->assertOk()
        ->assertSee('Riwayat Eviden Saya')
        ->assertSee('Menunggu ditinjau')
        ->assertSee('Disetujui')
        ->assertSee('Perlu revisi')
        ->assertSee('Indikator Riwayat Pegawai')
        ->assertSee('Mohon unggah dokumen yang lebih jelas.')
        ->assertSee('Okt 2026')
        ->assertSee('history-search')
        ->assertSee('history-status')
        ->assertDontSee('pegawai-lain.pdf');
});

test('admin planning dashboard displays database-backed metrics', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $school = Sekolah::create([
        'npsn' => '12345678',
        'nama_sekolah' => 'Sekolah Dashboard Test',
    ]);
    $guru = User::factory()->create([
        'role' => 'pegawai',
        'sekolah_id' => $school->id,
    ]);
    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
        'status_approval' => 'submitted',
    ]);
    $program = MasterProgram::create([
        'tahun_anggaran_id' => $tahun->id,
        'kode_program' => 'TEST',
        'nama_program' => 'Program Dashboard Test',
    ]);
    MasterKegiatan::create([
        'program_id' => $program->id,
        'kode_kegiatan' => 'TEST-01',
        'nama_kegiatan' => 'Kegiatan Dashboard Test',
        'anggaran' => 1250000000,
    ]);
    $sasaran = SasaranKinerja::create([
        'tahun_anggaran_id' => $tahun->id,
        'no_urut' => 1,
        'sasaran_kegiatan' => 'Sasaran Dashboard Test',
        'status_approval' => 'approved',
    ]);
    $indikator = IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'indikator_kinerja' => 'Indikator Dashboard Test',
        'target_default' => '1 kegiatan',
    ]);
    RealisasiPerkin::create([
        'user_id' => $guru->id,
        'master_indikator_id' => $indikator->id,
        'realisasi_capaian' => '100%',
        'file_eviden' => 'evidence/approved.pdf',
        'status_verifikasi' => 'approved',
    ]);
    RealisasiPerkin::create([
        'user_id' => $guru->id,
        'master_indikator_id' => $indikator->id,
        'realisasi_capaian' => '50%',
        'file_eviden' => 'evidence/revision.pdf',
        'status_verifikasi' => 'rejected',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Program Dashboard Test')
        ->assertSee('Rp 1,25 M')
        ->assertSee('1 / 1')
        ->assertSee('1 / 2')
        ->assertSee('Sekolah Dashboard Test')
        ->assertSee('100');
});
