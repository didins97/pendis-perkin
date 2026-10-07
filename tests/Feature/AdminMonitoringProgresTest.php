<?php

use App\Models\IndikatorKinerja;
use App\Models\RealisasiPerkin;
use App\Models\SasaranKinerja;
use App\Models\Sekolah;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can monitor indicator progress and drill down to uploaded and missing pegawai', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pimpinan = User::factory()->create(['role' => 'pimpinan']);
    $school = Sekolah::create([
        'npsn' => '12345678',
        'nama_sekolah' => 'Madrasah Monitoring',
    ]);
    $otherSchool = Sekolah::create([
        'npsn' => '87654321',
        'nama_sekolah' => 'Madrasah Di Luar Filter',
    ]);
    $pegawai = collect([
        User::factory()->create(['role' => 'pegawai', 'status_aktif' => true, 'sekolah_id' => $school->id, 'name' => 'Pegawai Upload Satu']),
        User::factory()->create(['role' => 'pegawai', 'status_aktif' => true, 'sekolah_id' => $school->id, 'name' => 'Pegawai Upload Dua']),
        User::factory()->create(['role' => 'pegawai', 'status_aktif' => true, 'sekolah_id' => $school->id, 'name' => 'Pegawai Belum Upload']),
        User::factory()->create(['role' => 'pegawai', 'status_aktif' => true, 'sekolah_id' => $school->id, 'name' => 'Pegawai Zona Merah']),
    ]);
    $pegawaiLuar = User::factory()->create([
        'role' => 'pegawai',
        'status_aktif' => true,
        'sekolah_id' => $otherSchool->id,
        'name' => 'Pegawai Satker Lain',
    ]);
    $inactivePegawai = User::factory()->create([
        'role' => 'pegawai',
        'status_aktif' => false,
        'sekolah_id' => $school->id,
        'name' => 'Pegawai Nonaktif',
    ]);
    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
        'status_approval' => 'approved',
        'approved_by' => $pimpinan->id,
    ]);
    $sasaran = SasaranKinerja::create([
        'tahun_anggaran_id' => $tahun->id,
        'no_urut' => 1,
        'sasaran_kegiatan' => 'Peningkatan Kualitas Pendidikan',
        'status_approval' => 'approved',
    ]);
    $complete = IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'kode_sub' => 'a',
        'indikator_kinerja' => 'Indikator Tuntas',
        'target_default' => '100%',
    ]);
    $progress = IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'kode_sub' => 'b',
        'indikator_kinerja' => 'Indikator Progres',
        'target_default' => '100%',
    ]);
    $critical = IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'kode_sub' => 'c',
        'indikator_kinerja' => 'Indikator Zona Merah',
        'target_default' => '100%',
    ]);

    foreach ($pegawai as $user) {
        RealisasiPerkin::create([
            'user_id' => $user->id,
            'master_indikator_id' => $complete->id,
            'realisasi_capaian' => '100%',
            'file_eviden' => 'evidence/complete.pdf',
            'status_verifikasi' => 'pending',
        ]);
    }
    foreach ($pegawai->take(2) as $user) {
        RealisasiPerkin::create([
            'user_id' => $user->id,
            'master_indikator_id' => $progress->id,
            'realisasi_capaian' => '50%',
            'file_eviden' => 'evidence/progress.pdf',
            'status_verifikasi' => 'pending',
        ]);
    }
    RealisasiPerkin::create([
        'user_id' => $pegawaiLuar->id,
        'master_indikator_id' => $critical->id,
        'realisasi_capaian' => '100%',
        'file_eviden' => 'evidence/outside-filter.pdf',
        'status_verifikasi' => 'pending',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.monitoring-progres'))
        ->assertOk()
        ->assertSee('Indikator Kinerja')
        ->assertSee('Madrasah Monitoring')
        ->assertSee('Sasaran 1')
        ->assertSee('Peningkatan Kualitas Pendidikan')
        ->assertSee('Indikator Tuntas')
        ->assertSee('Indikator Progres')
        ->assertSee('Indikator Zona Merah')
        ->assertSee('aria-valuenow="80"', false)
        ->assertSee('aria-valuenow="40"', false)
        ->assertSee('aria-valuenow="20"', false)
        ->assertSee('Pegawai Upload Satu')
        ->assertSee('Pegawai Belum Upload')
        ->assertSee('Pegawai Satker Lain')
        ->assertDontSee('Pegawai Nonaktif');

    $this->get(route('admin.monitoring-progres', [
        'sekolah_id' => $school->id,
        'status' => 'critical',
    ]))
        ->assertOk()
        ->assertSee('Indikator Zona Merah')
        ->assertDontSee('Indikator Tuntas')
        ->assertDontSee('Indikator Progres');

    $this->get(route('admin.monitoring-progres', [
        'sekolah_id' => $school->id,
        'status' => 'complete',
    ]))
        ->assertOk()
        ->assertSee('Indikator Tuntas')
        ->assertSee('aria-valuenow="100"', false)
        ->assertDontSee('Indikator Progres')
        ->assertDontSee('Indikator Zona Merah');
});

test('monitoring progress page is restricted to admins', function () {
    $pimpinan = User::factory()->create(['role' => 'pimpinan']);

    $this->actingAs($pimpinan)
        ->get(route('admin.monitoring-progres'))
        ->assertForbidden();
});

test('admin sidebar groups menus and links to year management', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Menu Admin / Perencana')
        ->assertSee('Utama &amp; Operasional', false)
        ->assertSee('Pengaturan Kinerja')
        ->assertSee('Kelola Data')
        ->assertSee('Monitoring Progres')
        ->assertSee('Verifikasi Eviden Pegawai')
        ->assertSee('Laporan &amp; Cetak Perkin', false)
        ->assertSee('Master Perkin &amp; Indikator', false)
        ->assertSee('Data Sekolah / Satker')
        ->assertSee('Tahun Anggaran')
        ->assertSee('/admin/master-data/tahun-anggaran');

    $this->get(route('admin.tahun-anggaran.index'))->assertOk();
});
