<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('perangkat_ajar');

        if (Schema::hasColumn('sekolahs', 'pengawas_id')) {
            Schema::table('sekolahs', function (Blueprint $table) {
                $table->dropForeign(['pengawas_id']);
                $table->dropColumn('pengawas_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('sekolahs', 'pengawas_id')) {
            Schema::table('sekolahs', function (Blueprint $table) {
                $table->foreignId('pengawas_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('perangkat_ajar')) {
            Schema::create('perangkat_ajar', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
                $table->string('jenis_perangkat');
                $table->string('mata_pelajaran');
                $table->string('kelas');
                $table->string('tahun_ajaran');
                $table->string('file_path');
                $table->string('status')->default('draft');
                $table->text('catatan_revisi')->nullable();
                $table->foreignId('verified_by_pengawas_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at_pengawas')->nullable();
                $table->foreignId('approved_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('final_approved_at')->nullable();
                $table->string('qr_code_verification')->nullable();
                $table->timestamps();
            });
        }
    }
};
