<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_indikators', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sasaran_id')->constrained('master_sasarans')->onDelete('cascade');

            $table->string('kode_sub')->nullable();
            $table->text('indikator_kinerja');
            $table->string('target_default');
            $table->string('satuan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_indikators');
    }
};
