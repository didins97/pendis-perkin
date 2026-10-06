<?php

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guest is sent to the sign in page from the root route', function () {
    $this->get('/')->assertRedirect(route('signin'));
});

test('user can log in with email and is sent to the role dashboard', function () {
    User::factory()->create([
        'email' => 'admin@example.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    $this->post(route('login'), [
        'email' => 'admin@example.test',
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    expect(auth()->check())->toBeTrue();
});

test('user can log in with NIP', function () {
    User::factory()->create([
        'email' => 'guru@example.test',
        'nip' => '199001012020011001',
        'password' => Hash::make('password'),
        'role' => 'pegawai',
    ]);

    $this->post(route('login'), [
        'email' => '199001012020011001',
        'password' => 'password',
    ])->assertRedirect(route('pegawai.dashboard'));
});

test('new accounts are stored with the pegawai role', function () {
    $sekolah = Sekolah::create([
        'npsn' => '98765432',
        'nama_sekolah' => 'Sekolah Pegawai Test',
    ]);

    $this->post(route('register'), [
        'name' => 'Pegawai Test',
        'email' => 'pegawai@example.test',
        'sekolah_id' => $sekolah->id,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'terms' => 'on',
    ])->assertRedirect(route('pegawai.dashboard'));

    expect(User::query()->where('email', 'pegawai@example.test')->value('role'))->toBe('pegawai');
});

test('authenticated user cannot open the sign in page', function () {
    $user = User::factory()->create(['role' => 'pimpinan']);

    $this->actingAs($user)->get(route('signin'))->assertRedirect(route('pimpinan.dashboard'));
});

test('authenticated user can log out', function () {
    $user = User::factory()->create(['role' => 'pegawai']);

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('signin'));

    expect(auth()->check())->toBeFalse();
});

test('authenticated user can view and update their profile', function () {
    Storage::fake('local');

    $user = User::factory()->create(['role' => 'pegawai']);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk();

    $this->put(route('profile.update'), [
        'name' => 'Nama Baru',
        'email' => $user->email,
        'nip' => '198001012005011001',
        'nomor_wa' => '081234567890',
        'nuptk' => '1234567890123456',
        'nrg' => '998877',
        'pangkat_golongan' => 'III/a',
        'status_kepegawaian' => 'PPPK',
        'jabatan' => 'Pegawai',
        'tugas_tambahan' => 'Wali Kelas',
        'berkas_sk_mengajar' => UploadedFile::fake()->create('sk-mengajar.pdf', 20, 'application/pdf'),
    ])->assertRedirect(route('profile'));

    expect($user->fresh()->name)->toBe('Nama Baru')
        ->and($user->fresh()->nip)->toBe('198001012005011001')
        ->and($user->fresh()->nomor_wa)->toBe('081234567890')
        ->and($user->fresh()->profilPegawai->status_kepegawaian)->toBe('PPPK')
        ->and($user->fresh()->profilPegawai->pangkat_golongan)->toBe('III/a');

    Storage::disk('local')->assertExists($user->fresh()->profilPegawai->berkas_sk_mengajar);

    $this->get(route('profile.document', 'sk-mengajar'))->assertOk();
});
