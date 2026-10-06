<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_anggarans', function (Blueprint $table) {
            $table->enum('status_approval', ['draft', 'submitted', 'approved', 'rejected'])
                ->default('draft')
                ->after('status');
            $table->foreignId('approved_by')
                ->nullable()
                ->after('status_approval')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('catatan_approval')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('tahun_anggarans', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status_approval', 'approved_by', 'approved_at', 'catatan_approval']);
        });
    }
};
