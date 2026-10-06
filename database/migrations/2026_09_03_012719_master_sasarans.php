<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_sasarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_anggaran_id')->nullable()->constrained('tahun_anggarans')->cascadeOnDelete();
            $table->integer('no_urut')->nullable();
            $table->text('sasaran_kegiatan');

            $table->enum('status_approval', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->text('catatan_pimpinan')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_sasarans');
    }
};
