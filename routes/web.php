<?php

use App\Http\Controllers\ApprovalMasterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CetakPerkinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterAnggaranController;
use App\Http\Controllers\MasterKinerjaController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\RealisasiPerkinController;
use App\Http\Controllers\SekolahController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// dashboard pages
Route::get('/', function () {
    return Auth::check()
        ? redirect()->to(match (Auth::user()->role) {
            'admin' => route('admin.dashboard'),
            'pimpinan' => route('pimpinan.dashboard'),
            default => route('pegawai.dashboard'),
        })
        : redirect()->route('signin');
})->name('dashboard');

// MODIS PENDIS role dashboards and navigation destinations
Route::middleware('auth')->group(function () {
    Route::get('/perkin/{id}/preview', [CetakPerkinController::class, 'preview'])->name('perkin.preview');
    Route::get('/perkin/{id}/cetak-pdf', [CetakPerkinController::class, 'cetakPdf'])->name('perkin.cetak-pdf');

    Route::middleware('role:pegawai')->group(function () {
        Route::get('/pegawai/dashboard', [DashboardController::class, 'pegawaiDashboard'])->name('pegawai.dashboard');
        Route::controller(RealisasiPerkinController::class)->prefix('/pegawai/realisasi')->name('pegawai.realisasi.')->group(function () {
            Route::get('/', 'pegawaiIndex')->name('index');
            Route::get('/create', 'pegawaiCreate')->name('create');
            Route::get('/sasaran/{sasaran}/indikator', 'indikatorBySasaran')->name('indikator-by-sasaran');
            Route::post('/', 'store')->name('store');
        });
        Route::redirect('/guru/dashboard', '/pegawai/dashboard');
        Route::redirect('/guru/realisasi', '/pegawai/realisasi');
        Route::redirect('/guru/realisasi/create', '/pegawai/realisasi/create');
    });

    Route::middleware('role:pimpinan')->group(function () {
        Route::get('/pimpinan/dashboard', [DashboardController::class, 'pimpinanDashboard'])->name('pimpinan.dashboard');
        Route::get('/pimpinan/laporan-perkin', [CetakPerkinController::class, 'index'])->name('pimpinan.laporan-perkin');
        Route::get('/pimpinan/laporan-perkin/{id}/pdf', [CetakPerkinController::class, 'cetakPdf'])->name('pimpinan.laporan-perkin.pdf');
        Route::controller(RealisasiPerkinController::class)->prefix('/pimpinan/realisasi')->name('pimpinan.realisasi.')->group(function () {
            Route::get('/', 'adminIndex')->name('index');
            Route::put('/{id}/approve', 'approve')->name('approve');
            Route::put('/{id}/reject', 'reject')->name('reject');
        });
        Route::controller(ApprovalMasterController::class)->prefix('/pimpinan/approval-master')->name('pimpinan.approval-master.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{tahun}', 'show')->name('show');
            Route::put('/{tahun}/approve', 'approve')->name('approve');
            Route::put('/{tahun}/reject', 'reject')->name('reject');
        });
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/admin/monitoring-progres', [DashboardController::class, 'adminMonitoringProgres'])->name('admin.monitoring-progres');
        Route::get('/admin/master-data/tahun-anggaran', [MasterKinerjaController::class, 'indexTahun'])->name('admin.tahun-anggaran.index');
        Route::get('/admin/laporan-perkin', [CetakPerkinController::class, 'index'])->name('admin.laporan-perkin');
        Route::get('/admin/laporan-perkin/{id}/pdf', [CetakPerkinController::class, 'cetakPdf'])->name('admin.laporan-perkin.pdf');

        Route::controller(RealisasiPerkinController::class)->prefix('/admin/realisasi')->group(function () {
            Route::get('/', 'adminIndex')->name('admin.realisasi.index');
            Route::put('/{id}/approve', 'approve')->name('admin.realisasi.approve');
            Route::put('/{id}/reject', 'reject')->name('admin.realisasi.reject');
        });

        Route::controller(MasterKinerjaController::class)->prefix('/admin/master-data/kinerja')->name('admin.kinerja.')->group(function () {
            Route::get('/', 'indexTahun')->name('tahun.index');
            Route::post('/tahun', 'storeTahun')->name('tahun.store');
            Route::put('/tahun/{tahun}', 'updateTahun')->name('tahun.update');
            Route::delete('/tahun/{tahun}', 'destroyTahun')->name('tahun.destroy');
            Route::get('/tahun/{tahun}/sasaran', 'showSasaran')->name('tahun.sasaran.index');
            Route::post('/tahun/{tahun}/sasaran', 'storeSasaran')->name('tahun.sasaran.store');
            Route::put('/tahun/{tahun}/sasaran/{sasaran}', 'updateSasaran')->name('tahun.sasaran.update');
            Route::delete('/tahun/{tahun}/sasaran/{sasaran}', 'destroySasaran')->name('tahun.sasaran.destroy');
            Route::get('/sasaran/{sasaran}/indikator', 'showIndikators')->name('sasaran.indikator.index');
            Route::get('/sasaran/{sasaran}/indikator/data', 'getIndikators')->name('sasaran.indikator.data');
            Route::post('/sasaran/{sasaran}/indikator', 'storeIndikator')->name('sasaran.indikator.store');
            Route::put('/sasaran/{sasaran}/indikator/{indikator}', 'updateIndikator')->name('sasaran.indikator.update');
            Route::delete('/sasaran/{sasaran}/indikator/{indikator}', 'destroyIndikator')->name('sasaran.indikator.destroy');
        });
        Route::controller(MasterAnggaranController::class)->prefix('/admin/master-anggaran')->group(function () {
            Route::get('/', 'index')->name('admin.anggaran-program.index');
            Route::post('/tahun/{tahun_anggaran_id}/submit', 'submitToPimpinan')->name('admin.anggaran-program.submit');
            Route::post('/', 'storeProgram')->name('admin.anggaran-program.store');
            Route::put('/program/{program}', 'updateProgram')->name('admin.anggaran-program.update');
            Route::delete('/program/{program}', 'destroyProgram')->name('admin.anggaran-program.destroy');
            Route::get('/program/{program}/kegiatan', 'showProgram')->name('admin.anggaran-program.program.show');
            Route::post('/program/{program}/kegiatan', 'storeKegiatan')->name('admin.anggaran-program.kegiatan.store');
            Route::put('/program/{program}/kegiatan/{kegiatan}', 'updateKegiatan')->name('admin.anggaran-program.kegiatan.update');
            Route::delete('/program/{program}/kegiatan/{kegiatan}', 'destroyKegiatan')->name('admin.anggaran-program.kegiatan.destroy');
        });
        Route::controller(SekolahController::class)->prefix('/admin/master-data/sekolah')->group(function () {
            Route::get('/', 'index')->name('admin.schools');
            Route::post('/', 'store')->name('admin.schools.store');
            Route::put('/{sekolah}', 'update')->name('admin.schools.update');
            Route::delete('/{sekolah}', 'destroy')->name('admin.schools.destroy');
        });
        Route::controller(PimpinanController::class)->prefix('/admin/master-data/pimpinan')->group(function () {
            Route::get('/', 'index')->name('admin.pimpinan');
            Route::post('/', 'store')->name('admin.pimpinan.store');
            Route::put('/{pimpinan}', 'update')->name('admin.pimpinan.update');
            Route::post('/{pimpinan}/reset-password', 'resetPassword')->name('admin.pimpinan.reset-password');
        });
        Route::controller(PegawaiController::class)->prefix('/admin/master-data/pegawai')->name('admin.pegawai.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::post('/import', 'import')->name('import');
            Route::get('/{pegawai}/edit', 'edit')->name('edit');
            Route::get('/{pegawai}/riwayat', 'history')->name('history');
            Route::get('/{pegawai}/berkas/{document}', 'document')->name('document');
            Route::get('/{pegawai}', 'show')->name('show');
            Route::put('/{pegawai}', 'update')->name('update');
            Route::delete('/{pegawai}', 'destroy')->name('destroy');
        });
        Route::redirect('/admin/master-data/guru', '/admin/master-data/pegawai')->name('admin.teachers');
    });
});

// authentication pages
Route::get('/signin', function () {
    return Auth::check()
        ? redirect()->to(match (Auth::user()->role) {
            'admin' => route('admin.dashboard'),
            'pimpinan' => route('pimpinan.dashboard'),
            default => route('pegawai.dashboard'),
        })
        : view('pages.auth.signin', ['title' => 'MODIS PENDIS - Masuk']);
})->name('signin');

Route::middleware('guest')->group(function () {
    // Handling Login / Signin
    Route::get('/login', fn () => redirect()->route('signin'));
    Route::get('/signin', [AuthController::class, 'showLoginForm'])->name('signin');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Handling Register / Signup
    Route::get('/signup', [AuthController::class, 'showRegisterForm'])->name('signup');
    Route::get('/register', fn () => redirect()->route('signup'));
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/berkas/{document}', [AuthController::class, 'profileDocument'])->name('profile.document');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
