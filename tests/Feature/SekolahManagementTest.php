<?php

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

test('school records no longer have legacy supervisor or Perangkat Ajar storage', function () {
    expect(Schema::hasColumn('sekolahs', 'pengawas_id'))->toBeFalse()
        ->and(Schema::hasTable('perangkat_ajar'))->toBeFalse();

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->from(route('admin.schools'))
        ->post(route('admin.schools.store'), [
            'npsn' => '98765432',
            'nama_sekolah' => 'Sekolah Tanpa Pengawas Test',
            'alamat' => 'Pulau Morotai',
        ])
        ->assertRedirect(route('admin.schools'));

    $this->assertDatabaseHas('sekolahs', [
        'npsn' => '98765432',
        'nama_sekolah' => 'Sekolah Tanpa Pengawas Test',
    ]);

    expect(Sekolah::query()->where('npsn', '98765432')->first()->getAttributes())
        ->not->toHaveKey('pengawas_id');
});
