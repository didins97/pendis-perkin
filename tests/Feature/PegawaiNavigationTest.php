<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pegawai sidebar groups performance and document navigation', function () {
    $pegawai = User::factory()->create(['role' => 'pegawai']);

    $this->actingAs($pegawai)
        ->get(route('pegawai.dashboard'))
        ->assertOk()
        ->assertSee('Menu Pegawai')
        ->assertSee('Kinerja &amp; Eviden', false)
        ->assertSee('Dokumen')
        ->assertSee('Dashboard Saya')
        ->assertSee('Pengisian Eviden Kinerja')
        ->assertSee('Riwayat &amp; Status Verifikasi', false)
        ->assertSee('Cetak &amp; Dokumen Perkin', false)
        ->assertSee('href="/pegawai/dashboard"', false)
        ->assertSee('href="/pegawai/realisasi/create"', false)
        ->assertSee('href="/pegawai/realisasi"', false)
        ->assertSee('M12 16V4')
        ->assertSee('M12 7v5l3 2');
});
