@extends('layouts.app')

@section('content')
    @php
        $displayName = $pegawai?->name ?? 'Pegawai';
        $identityNumber = $pegawai?->nip ?? $pegawai?->email ?? 'NIP belum diatur';
        $schoolName = $pegawai?->sekolah?->nama_sekolah ?? 'Satker belum diatur';
        $evidenceLabels = [
            'approved' => ['label' => 'Disetujui', 'class' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400'],
            'pending' => ['label' => 'Menunggu', 'class' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400'],
            'revision' => ['label' => 'Perlu Revisi', 'class' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400'],
        ];
    @endphp

    <div class="space-y-6">
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

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Kelengkapan Profil</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $profileFieldsCompleted }} dari {{ $profileFieldsTotal }} data profil sudah dilengkapi.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $profileCompletion }}%</span>
                    <a href="{{ route('profile') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Lengkapi Profil
                    </a>
                </div>
            </div>
            <div
                class="mt-4 h-3 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800"
                role="progressbar"
                aria-label="Kelengkapan profil pegawai"
                aria-valuemin="0"
                aria-valuemax="100"
                aria-valuenow="{{ $profileCompletion }}"
            >
                <div class="h-full rounded-full bg-brand-500 transition-all duration-500" style="width: {{ $profileCompletion }}%"></div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat Eviden Terbaru</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lima eviden terakhir yang Anda kirim.</p>
                </div>
                <a href="{{ route('pegawai.realisasi.index') }}" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">
                    Lihat semua riwayat
                </a>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Indikator</th>
                            <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Capaian</th>
                            <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Tanggal Kirim</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($recentEvidence as $evidence)
                            @php
                                $status = match ($evidence->status_verifikasi) {
                                    'approved' => ['Disetujui', 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400'],
                                    'rejected' => ['Perlu Revisi', 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400'],
                                    default => ['Menunggu', 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400'],
                                };
                            @endphp
                            <tr>
                                <td class="px-4 py-3.5 text-gray-800 dark:text-white/90">
                                    {{ $evidence->indikator?->indikator_kinerja ?? 'Indikator tidak tersedia' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-gray-600 dark:text-gray-300">
                                    {{ $evidence->realisasi_capaian ?: '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-gray-500 dark:text-gray-400">
                                    {{ $evidence->created_at?->format('d M Y') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Belum ada eviden yang dikirim.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
@endsection
