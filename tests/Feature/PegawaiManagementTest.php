<?php

use App\Models\ProfilPegawai;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin can view and edit a pegawai account and professional profile', function () {
    Storage::fake('local');

    $admin = User::factory()->create(['role' => 'admin']);
    $school = Sekolah::create([
        'npsn' => '12345678',
        'nama_sekolah' => 'Sekolah Pegawai Test',
    ]);
    $pegawai = User::factory()->create([
        'role' => 'pegawai',
        'sekolah_id' => $school->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.pegawai.index'))
        ->assertOk()
        ->assertSee($pegawai->name);

    $this->get(route('admin.schools'))
        ->assertOk()
        ->assertSee('Sekolah Pegawai Test')
        ->assertSee('Jumlah Pegawai')
        ->assertSee('1');

    $this->actingAs($admin)
        ->get(route('admin.pegawai.show', $pegawai))
        ->assertOk()
        ->assertSee('Detail Pegawai')
        ->assertSee($pegawai->name);

    $this->get(route('admin.pegawai.edit', $pegawai))
        ->assertOk()
        ->assertSee('Status Kepegawaian');

    $this->put(route('admin.pegawai.update', $pegawai), [
        'name' => 'Pegawai Diperbarui',
        'nip' => '198001012005011001',
        'email' => $pegawai->email,
        'nomor_wa' => '081234567890',
        'sekolah_id' => $school->id,
        'status_aktif' => '0',
        'nuptk' => '1234567890123456',
        'nrg' => '998877',
        'pangkat_golongan' => 'III/a',
        'status_kepegawaian' => 'PPPK',
        'jabatan' => 'Guru Kelas',
        'tugas_tambahan' => 'Wali Kelas',
        'berkas_sk_pangkat' => UploadedFile::fake()->create('sk-pangkat.pdf', 20, 'application/pdf'),
    ])->assertRedirect(route('admin.pegawai.show', $pegawai));

    $pegawai->refresh();
    $profile = ProfilPegawai::query()->where('user_id', $pegawai->id)->firstOrFail();

    expect($pegawai->name)->toBe('Pegawai Diperbarui')
        ->and($pegawai->nomor_wa)->toBe('081234567890')
        ->and($pegawai->status_aktif)->toBeFalse()
        ->and($profile->status_kepegawaian)->toBe('PPPK')
        ->and($profile->pangkat_golongan)->toBe('III/a');

    Storage::disk('local')->assertExists($profile->berkas_sk_pangkat);

    $this->get(route('admin.pegawai.document', [$pegawai, 'sk-pangkat']))
        ->assertOk();
});
