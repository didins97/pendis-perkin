<?php

use App\Models\MasterAnggaran;
use App\Models\TahunAnggaran;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

test('master anggaran can reference tahun anggaran through foreign key relation', function () {
    Schema::create('tahun_anggarans', function (Blueprint $table) {
        $table->id();
        $table->string('tahun', 4)->unique();
        $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
        $table->timestamps();
    });

    Schema::create('master_anggarans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tahun_anggaran_id')->nullable()->constrained('tahun_anggarans')->cascadeOnDelete();
        $table->string('kode_program');
        $table->string('nama_program');
        $table->string('kode_kegiatan')->nullable();
        $table->string('nama_kegiatan')->nullable();
        $table->decimal('anggaran', 15, 2);
        $table->foreignId('created_by')->nullable();
        $table->timestamps();
    });

    $tahun = TahunAnggaran::create([
        'tahun' => '2026',
        'status' => 'aktif',
    ]);

    $anggaran = MasterAnggaran::create([
        'tahun_anggaran_id' => $tahun->id,
        'kode_program' => '025.01.WA',
        'nama_program' => 'Program Dukungan Manajemen',
        'kode_kegiatan' => '2100',
        'nama_kegiatan' => 'Pembinaan Administrasi Keuangan dan BMN',
        'anggaran' => 2102438000.00,
        'created_by' => 1,
    ]);

    expect($anggaran->refresh()->tahunAnggaran->id)->toBe($tahun->id)
        ->and($anggaran->tahunAnggaran->tahun)->toBe('2026');
});
