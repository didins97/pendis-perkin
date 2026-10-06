@extends('layouts.app')

@section('content')
    @php
        $displayName = $guru?->name ?? 'Guru / Pegawai';
        $identityNumber = $guru?->nip ?? $guru?->email ?? 'NIP belum diatur';
        $schoolName = $guru?->sekolah?->nama_sekolah ?? 'Satker belum diatur';
        $evidenceLabels = [
            'approved' => ['label' => 'Disetujui', 'class' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400'],
            'pending' => ['label' => 'Menunggu', 'class' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400'],
            'revision' => ['label' => 'Perlu Revisi', 'class' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400'],
        ];
    @endphp

    <div x-data="guruDashboard({{ $progresFisik }})" x-init="initChart()" class="space-y-6">
        <section class="overflow-hidden rounded-3xl bg-brand-600 p-6 text-white shadow-sm md:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-50">
                        <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                        Self-Service Dashboard
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight md:text-3xl">Selamat Datang, {{ $displayName }}</h1>
                    <p class="mt-2 text-sm text-brand-100">NIP/NPTK: {{ $identityNumber }}</p>
                    <div class="mt-5 flex flex-wrap gap-2 text-sm text-brand-50">
                        <span class="rounded-lg bg-white/10 px-3 py-2">{{ $schoolName }}</span>
                        <span class="rounded-lg bg-white/10 px-3 py-2">Tahun Anggaran 2026</span>
                    </div>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-5 lg:min-w-[250px]">
                    <p class="text-xs font-semibold uppercase tracking-wider text-brand-100">Status Kontrak Perkin</p>
                    <div class="mt-3 flex items-end justify-between gap-4">
                        <p class="text-3xl font-bold">Aktif</p>
                        <span class="rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-semibold text-emerald-100">2026</span>
                    </div>
                    <p class="mt-2 text-xs text-brand-100">4 indikator kinerja sedang dipantau</p>
                </div>
            </div>
        </section>

    </div>
@endsection
