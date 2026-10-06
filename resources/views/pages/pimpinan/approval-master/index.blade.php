@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-common.page-breadcrumb pageTitle="Approval Master Perkin & Pagu" />

        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Verifikasi Master Tahun Anggaran</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Periksa susunan sasaran, indikator, dan pagu sebelum digunakan oleh Pegawai.</p>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">{{ session('success') }}</div>
        @endif

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($tahuns as $tahun)
                @php
                    $totalPagu = $tahun->masterPrograms->flatMap(fn ($program) => $program->kegiatans)->sum('anggaran');
                    $statusClasses = [
                        'submitted' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                        'approved' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                        'rejected' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
                    ];
                    $statusLabels = ['submitted' => 'Menunggu Review', 'approved' => 'Disetujui', 'rejected' => 'Perlu Revisi'];
                @endphp
                <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tahun Anggaran</p>
                            <h2 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $tahun->tahun }}</h2>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$tahun->status_approval] ?? 'bg-gray-100 text-gray-700' }}">{{ $statusLabels[$tahun->status_approval] ?? ucfirst($tahun->status_approval) }}</span>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <div><p class="text-xs text-gray-500 dark:text-gray-400">Total Pagu</p><p class="mt-1 text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalPagu, 0, ',', '.') }}</p></div>
                        <div><p class="text-xs text-gray-500 dark:text-gray-400">Sasaran</p><p class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $tahun->sasarans_count }}</p></div>
                    </div>
                    @if ($tahun->catatan_approval)
                        <p class="mt-4 rounded-lg bg-error-50 px-3 py-2 text-xs text-error-700 dark:bg-error-500/10 dark:text-error-400">{{ $tahun->catatan_approval }}</p>
                    @endif
                    <a href="{{ route('pimpinan.approval-master.show', $tahun) }}" class="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">Buka Verifikasi</a>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 p-12 text-center text-sm text-gray-500 md:col-span-2 xl:col-span-3">Belum ada master tahun anggaran yang perlu ditinjau.</div>
            @endforelse
        </div>
    </div>
@endsection
