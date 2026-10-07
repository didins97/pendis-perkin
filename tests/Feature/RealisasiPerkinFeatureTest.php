<?php

use App\Models\IndikatorKinerja;
use App\Models\MasterKegiatan;
use App\Models\MasterProgram;
use App\Models\RealisasiPerkin;
use App\Models\SasaranKinerja;
use App\Models\Sekolah;
use App\Models\TahunAnggaran;
use App\Models\User;
use Database\Seeders\MasterSasaranIndikatorSeeder;
use Database\Seeders\RealisasiPerkinSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

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

test('realisasi seeder creates repeatable demo uploads for active pegawai', function () {
    Storage::fake('public');

    $pimpinan = User::factory()->create(['role' => 'pimpinan']);
    $pegawai = User::factory()->count(12)->create([
        'role' => 'pegawai',
        'status_aktif' => true,
    ]);
    (new MasterSasaranIndikatorSeeder)->run();
    expect(TahunAnggaran::approved()->where('tahun', '2026')->exists())->toBeTrue();

    $seeder = new RealisasiPerkinSeeder;
    $seeder->run();
    $seeder->run();

    $seededItems = RealisasiPerkin::query()
        ->where('catatan_guru', 'like', '[SEED REALISASI PERKIN]%')
        ->get();

    expect($seededItems)->toHaveCount(100)
        ->and($seededItems->pluck('user_id')->unique())->toHaveCount($pegawai->count())
        ->and($seededItems->pluck('status_verifikasi')->unique()->all())->toContain('pending', 'approved', 'rejected')
        ->and($seededItems->every(fn ($item) => $pegawai->contains('id', $item->user_id)))->toBeTrue();

    Storage::disk('public')->assertExists('realisasi-perkins/demo-eviden-seeder.pdf');
});

test('admin planning dashboard displays database-backed metrics', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $school = Sekolah::create([
        'npsn' => '12345678',
        'nama_sekolah' => 'Sekolah Dashboard Test',
    ]);
    $pegawai = User::factory()->create([
        'role' => 'pegawai',
        'status_aktif' => true,
        'sekolah_id' => $school->id,
    ]);
    $pegawaiZonaMerah = User::factory()->create([
        'role' => 'pegawai',
        'status_aktif' => true,
        'sekolah_id' => $school->id,
        'nomor_wa' => '081234567890',
    ]);
    $otherSchool = Sekolah::create([
        'npsn' => '87654321',
        'nama_sekolah' => 'Sekolah Lain Test',
    ]);
    $pegawaiSatkerLain = User::factory()->create([
        'role' => 'pegawai',
        'status_aktif' => true,
        'sekolah_id' => $otherSchool->id,
    ]);
    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
        'status_approval' => 'approved',
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
    IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'indikator_kinerja' => 'Indikator Kedua Dashboard Test',
        'target_default' => '1 kegiatan',
    ]);
    RealisasiPerkin::create([
        'user_id' => $pegawai->id,
        'master_indikator_id' => $indikator->id,
        'realisasi_capaian' => '100%',
        'file_eviden' => 'evidence/approved.pdf',
        'status_verifikasi' => 'approved',
    ]);
    RealisasiPerkin::create([
        'user_id' => $pegawai->id,
        'master_indikator_id' => $indikator->id,
        'realisasi_capaian' => '50%',
        'file_eviden' => 'evidence/revision.pdf',
        'status_verifikasi' => 'rejected',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Admin')
        ->assertSee('Total Pegawai Aktif')
        ->assertSee('Kuota eviden: 6 unggahan')
        ->assertSee('Capaian Tuntas')
        ->assertSee('/ 6')
        ->assertSee('17%')
        ->assertDontSee('name="sekolah_id"', false)
        ->assertDontSee('name="tahun_anggaran_id"', false)
        ->assertSee('Satker / Madrasah')
        ->assertSee('2')
        ->assertSee('Tindakan Cepat Zona Merah')
        ->assertSee($pegawaiZonaMerah->name)
        ->assertSee('0% · Belum upload')
        ->assertSee('https://wa.me/6281234567890')
        ->assertSee(route('admin.pegawai.show', $pegawaiZonaMerah))
        ->assertDontSee('Detail Kepatuhan per Indikator')
        ->assertDontSee('Indikator Kedua Dashboard Test')
        ->assertSee('Sekolah Lain Test')
        ->assertDontSee('Program Dashboard Test');

    $this->actingAs($admin)
        ->get(route('admin.dashboard', ['sekolah_id' => $school->id]))
        ->assertOk()
        ->assertSee('Kuota eviden: 6 unggahan')
        ->assertSee('/ 6')
        ->assertSee('17%')
        ->assertDontSee('Detail Kepatuhan per Indikator')
        ->assertSee($pegawaiSatkerLain->name)
        ->assertSee('Sekolah Dashboard Test')
        ->assertSee($pegawaiZonaMerah->name);
});
