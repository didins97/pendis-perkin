<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID sekolah pertama sebagai contoh relasi untuk Guru
        // (Pastikan SekolahSeeder sudah dijalankan lebih dahulu)
        $sekolah = Sekolah::first();
        $sekolahId = $sekolah ? $sekolah->id : null;

        // Password default untuk semua akun testing: "password"
        $defaultPassword = Hash::make('password');

        // 2. Akun Admin Kemenag
        User::create([
            'name'              => 'Seksi Pendis Morotai',
            'email'             => 'pendis@app.com',
            'email_verified_at' => now(),
            'password'          => $defaultPassword,
            'role'              => 'admin',
            'nip'               => '198501012010011001',
            'sekolah_id'        => null, // Admin tidak terikat sekolah
            'remember_token'    => Str::random(10),
        ]);

        // 3. Akun Pimpinan Pembina
        User::create([
            'name'              => 'Arifin Bone',
            'email'             => 'arifinbone@app.com',
            'email_verified_at' => now(),
            'password'          => $defaultPassword,
            'role'              => 'pimpinan',
            'nip'               => '196806041997031003',
            'sekolah_id'        => null, // Pimpinan membina via tabel sekolahs
            'remember_token'    => Str::random(10),
        ]);

        // 4. Akun Guru (Terhubung ke Sekolah)
        User::create([
            'name'              => 'User Guru, S.Pd',
            'email'             => 'guru@sekolah.sch.id',
            'email_verified_at' => now(),
            'password'          => $defaultPassword,
            'role'              => 'guru',
            'nip'               => '199008202019031005',
            'sekolah_id'        => $sekolahId,
            'remember_token'    => Str::random(10),
        ]);
    }
}
