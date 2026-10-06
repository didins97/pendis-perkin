<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterAnggaranController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CetakPerkinController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ApprovalMasterController;
use App\Http\Controllers\MasterKinerjaController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\RealisasiPerkinController;

// dashboard pages
Route::get('/', function () {
    return Auth::check()
        ? redirect()->to(match (Auth::user()->role) {
            'admin' => route('admin.dashboard'),
            'pimpinan' => route('pimpinan.dashboard'),
            default => route('guru.dashboard'),
        })
        : redirect()->route('signin');
})->name('dashboard');

// MODIS PENDIS role dashboards and navigation destinations
Route::middleware('auth')->group(function () {
    Route::get('/perkin/{id}/preview', [CetakPerkinController::class, 'preview'])->name('perkin.preview');
    Route::get('/perkin/{id}/cetak-pdf', [CetakPerkinController::class, 'cetakPdf'])->name('perkin.cetak-pdf');

    Route::middleware('role:guru')->group(function () {
        // Route::view('/guru/dashboard', 'pages.dashboard.ecommerce', ['title' => 'Dashboard Guru'])->name('guru.dashboard');
        Route::get('/guru/dashboard', [DashboardController::class, 'guruDashboard'])->name('guru.dashboard');
        Route::controller(RealisasiPerkinController::class)->prefix('/guru/realisasi')->group(function () {
            Route::get('/', 'guruIndex')->name('guru.realisasi.index');
            Route::get('/create', 'guruCreate')->name('guru.realisasi.create');
            Route::get('/sasaran/{sasaran}/indikator', 'indikatorBySasaran')->name('guru.realisasi.indikator-by-sasaran');
            Route::post('/', 'store')->name('guru.realisasi.store');
        });
    });

    Route::middleware('role:pimpinan')->group(function () {
        Route::get('/pimpinan/dashboard', [DashboardController::class, 'pimpinanDashboard'])->name('pimpinan.dashboard');
        Route::controller(ApprovalMasterController::class)->prefix('/pimpinan/approval-master')->name('pimpinan.approval-master.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{tahun}', 'show')->name('show');
            Route::put('/{tahun}/approve', 'approve')->name('approve');
            Route::put('/{tahun}/reject', 'reject')->name('reject');
        });
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
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
        Route::controller(GuruController::class)->prefix('/admin/master-data/guru')->group(function () {
            Route::get('/', 'index')->name('admin.teachers');
            Route::post('/', 'store')->name('admin.teachers.store');
            Route::post('/import', 'import')->name('admin.teachers.import');
            Route::put('/{guru}', 'update')->name('admin.teachers.update');
            Route::get('/{guru}/riwayat', 'history')->name('admin.teachers.history');
            Route::delete('/{guru}', 'destroy')->name('admin.teachers.destroy');
        });
    });
});

// calender pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');

// profile pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages

Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
Route::get('/signin', function () {
    return Auth::check()
        ? redirect()->to(match (Auth::user()->role) {
            'admin' => route('admin.dashboard'),
            'pimpinan' => route('pimpinan.dashboard'),
            default => route('guru.dashboard'),
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

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
