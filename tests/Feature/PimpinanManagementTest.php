<?php

use App\Models\User;
use App\Models\IndikatorKinerja;
use App\Models\RealisasiPerkin;
use App\Models\SasaranKinerja;
use App\Models\Sekolah;
use App\Models\TahunAnggaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pimpinan users are sent to their dashboard from the root route', function () {
    $pimpinan = User::factory()->create(['role' => 'pimpinan']);

    $this->actingAs($pimpinan)
        ->get('/')
        ->assertRedirect(route('pimpinan.dashboard'));

    $this->get(route('pimpinan.dashboard'))
        ->assertOk()
        ->assertSee('Ringkasan Eviden Perkin')
        ->assertSee('Belum ada eviden Perkin.');
});

test('admin can manage pimpinan accounts without school assignments', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->from(route('admin.pimpinan'))
        ->post(route('admin.pimpinan.store'), [
            'name' => 'Pimpinan Test',
            'nip' => '197001012000011001',
            'email' => 'pimpinan@example.test',
            'password' => 'password123',
        ])
        ->assertRedirect(route('admin.pimpinan'));

    $pimpinan = User::query()->where('email', 'pimpinan@example.test')->firstOrFail();

    expect($pimpinan->role)->toBe('pimpinan');

    $this->actingAs($admin)
        ->get(route('admin.pimpinan'))
        ->assertOk()
        ->assertSee('Manajemen Data Pimpinan')
        ->assertSee('Pimpinan Test')
        ->assertDontSee('Sekolah Binaan')
        ->assertDontSee('Sekolah Terbina');
});

test('pimpinan dashboard shows real perkin evidence counts', function () {
    $pimpinan = User::factory()->create(['role' => 'pimpinan']);
    $school = Sekolah::create([
        'npsn' => '12345678',
        'nama_sekolah' => 'Sekolah Eviden Test',
    ]);
    $guru = User::factory()->create([
        'role' => 'guru',
        'sekolah_id' => $school->id,
    ]);
    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
    ]);
    $sasaran = SasaranKinerja::create([
        'tahun_anggaran_id' => $tahun->id,
        'no_urut' => 1,
        'sasaran_kegiatan' => 'Sasaran Eviden Test',
    ]);
    $indikator = IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'indikator_kinerja' => 'Indikator Eviden Test',
        'target_default' => '2 kegiatan',
    ]);

    foreach (['pending', 'approved'] as $status) {
        RealisasiPerkin::create([
            'user_id' => $guru->id,
            'master_indikator_id' => $indikator->id,
            'realisasi_capaian' => '1 kegiatan',
            'file_eviden' => "evidence/{$status}.pdf",
            'status_verifikasi' => $status,
        ]);
    }

    $this->actingAs($pimpinan)
        ->get(route('pimpinan.dashboard'))
        ->assertOk()
        ->assertSee('Total Eviden')
        ->assertSee('Sekolah Eviden Test')
        ->assertSee('Indikator Eviden Test');
});
