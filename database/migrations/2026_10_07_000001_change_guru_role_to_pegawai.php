<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pimpinan', 'guru', 'pegawai'])->default('pegawai')->change();
        });

        DB::table('users')->where('role', 'guru')->update(['role' => 'pegawai']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pimpinan', 'pegawai'])->default('pegawai')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pimpinan', 'guru', 'pegawai'])->default('guru')->change();
        });

        DB::table('users')->where('role', 'pegawai')->update(['role' => 'guru']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pimpinan', 'guru'])->default('guru')->change();
        });
    }
};
