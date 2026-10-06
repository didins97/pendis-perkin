<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('master_anggarans');

        Schema::create('master_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_anggaran_id')->nullable()->constrained('tahun_anggarans')->cascadeOnDelete();
            $table->string('kode_program');
            $table->string('nama_program');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('master_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('master_programs')->cascadeOnDelete();
            $table->string('kode_kegiatan');
            $table->string('nama_kegiatan');
            $table->decimal('anggaran', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_kegiatans');
        Schema::dropIfExists('master_programs');
    }
};
