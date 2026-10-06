<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_wa', 20)->nullable();
            $table->boolean('status_aktif')->default(true);
        });

        Schema::create('profil_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nuptk')->nullable();
            $table->string('nrg')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'Non-ASN'])->nullable();
            $table->string('jabatan')->nullable();
            $table->text('tugas_tambahan')->nullable();
            $table->string('berkas_sk_pangkat')->nullable();
            $table->string('berkas_sk_mengajar')->nullable();
            $table->string('berkas_serdik')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_pegawai');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nomor_wa', 'status_aktif']);
        });
    }
};
