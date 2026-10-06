<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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
        'role' => 'guru',
    ]);

    $this->post(route('login'), [
        'email' => '199001012020011001',
        'password' => 'password',
    ])->assertRedirect(route('guru.dashboard'));
});

test('authenticated user cannot open the sign in page', function () {
    $user = User::factory()->create(['role' => 'pimpinan']);

    $this->actingAs($user)->get(route('signin'))->assertRedirect(route('pimpinan.dashboard'));
});

test('authenticated user can log out', function () {
    $user = User::factory()->create(['role' => 'guru']);

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('signin'));

    expect(auth()->check())->toBeFalse();
});
