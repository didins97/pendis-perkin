<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisasi_perkins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Guru memilih Indikator Spesifik yang sudah disetujui (Approved)
            $table->foreignId('master_indikator_id')->constrained('master_indikators')->onDelete('cascade');

            $table->string('realisasi_capaian'); // Contoh: "88%"
            $table->string('file_eviden');       // Path dokumen PDF/ZIP/JPG
            $table->text('catatan_guru')->nullable();

            $table->enum('status_verifikasi', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_verifikator')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_perkins');
    }
};
