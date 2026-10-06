<?php

namespace App\Providers;

use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('use-approved-master', function (User $user, TahunAnggaran $tahun): bool {
            return $user->role === 'pegawai' && $tahun->status_approval === 'approved';
        });
    }
}
