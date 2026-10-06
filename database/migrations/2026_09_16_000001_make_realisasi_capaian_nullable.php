<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('realisasi_perkins', function (Blueprint $table) {
            $table->string('realisasi_capaian')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('realisasi_perkins', function (Blueprint $table) {
            $table->string('realisasi_capaian')->nullable(false)->change();
        });
    }
};
