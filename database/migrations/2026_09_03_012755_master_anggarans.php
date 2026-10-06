<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_anggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_anggaran_id')->nullable()->constrained('tahun_anggarans')->cascadeOnDelete();
            $table->string('kode_program');
            $table->string('nama_program');
            $table->string('kode_kegiatan')->nullable();
            $table->string('nama_kegiatan')->nullable();
            $table->decimal('anggaran', 15, 2);

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_anggarans');
    }
};
