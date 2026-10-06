<?php

use App\Models\IndikatorKinerja;
use App\Models\RealisasiPerkin;
use App\Models\SasaranKinerja;
use App\Models\Sekolah;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pimpinan users are sent to their dashboard from the root route', function () {
    $pimpinan = User::factory()->create(['role' => 'pimpinan']);

    $this->actingAs($pimpinan)
        ->get('/')
        ->assertRedirect(route('pimpinan.dashboard'));

    $this->get(route('pimpinan.dashboard'))
        ->assertOk()
        ->assertSee('Kepatuhan Eviden Pegawai')
        ->assertSee('Belum ada eviden Perkin.');
});

test('pimpinan can access the same perkin report and print options as admin', function () {
    $pimpinan = User::factory()->create(['role' => 'pimpinan']);
    $admin = User::factory()->create(['role' => 'admin']);
    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
        'status_approval' => 'approved',
    ]);

    $this->actingAs($pimpinan)
        ->get(route('pimpinan.laporan-perkin'))
        ->assertOk()
        ->assertSee('Laporan &amp; Cetak Perkin', false)
        ->assertSee('2026')
        ->assertSee(route('perkin.preview', $tahun))
        ->assertSee(route('pimpinan.laporan-perkin.pdf', $tahun->id));

    $this->get(route('pimpinan.laporan-perkin.pdf', $tahun->id))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    $this->actingAs($admin)
        ->get(route('admin.laporan-perkin'))
        ->assertOk()
        ->assertSee(route('admin.laporan-perkin.pdf', $tahun->id));
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
        'role' => 'pegawai',
        'sekolah_id' => $school->id,
    ]);
    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
        'status_approval' => 'approved',
    ]);
    $sasaran = SasaranKinerja::create([
        'tahun_anggaran_id' => $tahun->id,
        'no_urut' => 1,
        'sasaran_kegiatan' => 'Sasaran Eviden Test',
        'status_approval' => 'approved',
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
        ->assertSee('Total Pegawai Aktif')
        ->assertSee('Capaian Eviden Keseluruhan')
        ->assertSee('Unggah 100%')
        ->assertSee('Cari nama satker')
        ->assertSee('Pilih semua')
        ->assertSee('Kosongkan')
        ->assertSee('Sekolah Eviden Test')
        ->assertDontSee('Indikator Eviden Test')
        ->assertSee('Pegawai Zona Merah')
        ->assertSee('Tidak ada pegawai di zona merah.');
});

test('pimpinan dashboard calculates employee compliance and filters madrasah comparison', function () {
    $pimpinan = User::factory()->create(['role' => 'pimpinan']);
    $schoolNames = ['MTsN 1', 'MTsN 2', 'MIN 1', 'MTsN 3', 'MIN 2', 'Satker tanpa pegawai'];
    $schools = collect($schoolNames)->map(function ($name, $index) {
        return Sekolah::create([
            'npsn' => '9000000'.$index,
            'nama_sekolah' => $name,
        ]);
    });
    $pegawai = collect(range(0, 4))->map(fn ($index) => User::factory()->create([
        'role' => 'pegawai',
        'status_aktif' => true,
        'sekolah_id' => $schools[$index]->id,
    ]));
    User::factory()->create([
        'role' => 'pegawai',
        'status_aktif' => false,
        'sekolah_id' => $schools[0]->id,
    ]);

    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
        'status_approval' => 'approved',
    ]);
    $sasaran = SasaranKinerja::create([
        'tahun_anggaran_id' => $tahun->id,
        'no_urut' => 1,
        'sasaran_kegiatan' => 'Sasaran Kepatuhan',
        'status_approval' => 'approved',
    ]);
    $indikators = collect(range(1, 10))->map(fn ($index) => IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'indikator_kinerja' => "Indikator {$index}",
        'target_default' => '1 kegiatan',
    ]));

    foreach ([10, 5, 0, 2, 8] as $pegawaiIndex => $submittedCount) {
        foreach ($indikators->take($submittedCount) as $indicatorIndex => $indikator) {
            RealisasiPerkin::create([
                'user_id' => $pegawai[$pegawaiIndex]->id,
                'master_indikator_id' => $indikator->id,
                'realisasi_capaian' => '100%',
                'file_eviden' => "evidence/pegawai-{$pegawaiIndex}-{$indicatorIndex}.pdf",
                'status_verifikasi' => 'pending',
            ]);
        }
    }

    $this->actingAs($pimpinan)
        ->get(route('pimpinan.dashboard'))
        ->assertOk()
        ->assertSee('5')
        ->assertSee('50%')
        ->assertSee('6')
        ->assertSee('Zona Merah')
        ->assertSee('Progres 30-70%')
        ->assertSee('Progres lainnya')
        ->assertSee($pegawai[2]->name)
        ->assertSee('Belum upload')
        ->assertDontSee($pegawai[0]->name);

    $response = $this->get(route('pimpinan.dashboard', [
        'sekolah_ids' => [$schools[0]->id, $schools[1]->id],
    ]))->assertOk();

    expect($response->getContent())
        ->toContain('const schoolNames = ["MTsN 1","MTsN 2"]')
        ->not->toContain('const schoolNames = ["MTsN 1","MTsN 2","MIN 1"');
});

test('pimpinan can review and verify employee evidence from the same queue as admin', function () {
    $pimpinan = User::factory()->create(['role' => 'pimpinan']);
    $pegawai = User::factory()->create(['role' => 'pegawai']);
    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
        'status_approval' => 'approved',
    ]);
    $sasaran = SasaranKinerja::create([
        'tahun_anggaran_id' => $tahun->id,
        'no_urut' => 1,
        'sasaran_kegiatan' => 'Sasaran Verifikasi',
        'status_approval' => 'approved',
    ]);
    $indikator = IndikatorKinerja::create([
        'sasaran_id' => $sasaran->id,
        'indikator_kinerja' => 'Indikator Verifikasi',
        'target_default' => '1 kegiatan',
    ]);
    $evidence = RealisasiPerkin::create([
        'user_id' => $pegawai->id,
        'master_indikator_id' => $indikator->id,
        'realisasi_capaian' => '100%',
        'file_eviden' => 'evidence/verifikasi.pdf',
        'status_verifikasi' => 'pending',
    ]);

    $this->actingAs($pimpinan)
        ->get(route('pimpinan.realisasi.index'))
        ->assertOk()
        ->assertSee('Daftar eviden pegawai')
        ->assertSee('Menunggu ditinjau')
        ->assertSee('Indikator Verifikasi')
        ->assertSee(route('pimpinan.realisasi.approve', $evidence->id));

    $this->put(route('pimpinan.realisasi.approve', $evidence->id))
        ->assertRedirect(route('pimpinan.realisasi.index'));

    expect($evidence->fresh()->status_verifikasi)->toBe('approved')
        ->and($evidence->fresh()->verified_by)->toBe($pimpinan->id);
});
