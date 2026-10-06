<?php

use App\Models\ProfilPegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can have one profile pegawai and active status defaults to true', function () {
    $user = User::factory()->create();
    $profile = $user->profilPegawai()->create([
        'nuptk' => '1234567890123456',
        'status_kepegawaian' => 'PPPK',
    ]);

    expect($user->fresh()->status_aktif)->toBeTrue()
        ->and($user->fresh()->profilPegawai)->toBeInstanceOf(ProfilPegawai::class)
        ->and($profile->user->is($user))->toBeTrue();
});
