@extends('layouts.app')

@section('content')
    <div x-data="approvalMasterModal()" x-cloak class="space-y-6">
        <x-common.page-breadcrumb pageTitle="Review Master Tahun Anggaran" />

        @if (session('success'))
            <div class="rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">{{ session('success') }}</div>
        @endif

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <a href="{{ route('pimpinan.approval-master.index') }}" class="text-sm font-medium text-brand-600">&larr; Kembali ke daftar approval</a>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tahun Anggaran {{ $tahun->tahun }}</h1>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $tahun->status_approval === 'submitted' ? 'bg-warning-50 text-warning-700' : ($tahun->status_approval === 'approved' ? 'bg-success-50 text-success-700' : 'bg-error-50 text-error-700') }}">{{ $tahun->status_approval === 'submitted' ? 'Menunggu Review' : ucfirst($tahun->status_approval) }}</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Review struktur Master Perkin dan Pagu sebelum Guru dapat memilih indikator.</p>
                </div>
                @if ($tahun->status_approval === 'submitted')
                    <div class="flex flex-wrap gap-3">
                        <button type="button" @click="approveOpen = true" class="rounded-lg bg-success-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-success-700">ACC / Disetujui</button>
                        <button type="button" @click="rejectOpen = true" class="rounded-lg border border-error-200 px-4 py-2.5 text-sm font-semibold text-error-700 hover:bg-error-50">Kembalikan / Revisi</button>
                    </div>
                @endif
            </div>
            @if ($tahun->catatan_approval)
                <div class="mt-5 rounded-xl border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-500/20 dark:bg-error-500/10 dark:text-error-400"><strong>Catatan approval:</strong> {{ $tahun->catatan_approval }}</div>
            @endif
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900"><p class="text-sm text-gray-500 dark:text-gray-400">Total Pagu</p><p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalPagu, 0, ',', '.') }}</p></div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900"><p class="text-sm text-gray-500 dark:text-gray-400">Program / Kegiatan</p><p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $programCount }} <span class="text-base font-medium text-gray-400">program</span></p></div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900"><p class="text-sm text-gray-500 dark:text-gray-400">Sasaran / Indikator</p><p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $tahun->sasarans->count() }} <span class="text-base font-medium text-gray-400">/ {{ $tahun->sasarans->sum(fn ($sasaran) => $sasaran->indikators->count()) }}</span></p></div>
        </section>

        <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Sasaran &amp; Indikator</h2>
                <div class="mt-5 space-y-4">
                    @forelse ($tahun->sasarans as $sasaran)
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $sasaran->no_urut }}. {{ $sasaran->sasaran_kegiatan }}</p>
                            <ul class="mt-3 space-y-2">
                                @forelse ($sasaran->indikators as $indikator)
                                    <li class="flex gap-2 text-sm text-gray-600 dark:text-gray-300"><span class="font-semibold text-brand-600">{{ $indikator->kode_sub }}.</span><span>{{ $indikator->indikator_kinerja }} <span class="text-xs text-gray-400">({{ $indikator->target_default }})</span></span></li>
                                @empty
                                    <li class="text-xs text-gray-400">Belum ada indikator.</li>
                                @endforelse
                            </ul>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada sasaran kinerja.</p>
                    @endforelse
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan Pagu Program</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($tahun->masterPrograms as $program)
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-semibold text-brand-600">{{ $program->kode_program }}</p><p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $program->nama_program }}</p></div><span class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($program->kegiatans->sum('anggaran'), 0, ',', '.') }}</span></div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $program->kegiatans->count() }} kegiatan</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada program anggaran.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <div x-show="approveOpen" @keydown.escape.window="approveOpen = false" class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4" x-transition>
            <div @click.outside="approveOpen = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Setujui Master Perkin?</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Guru akan dapat memilih sasaran dan indikator dari Tahun Anggaran {{ $tahun->tahun }}.</p>
                <form method="POST" action="{{ route('pimpinan.approval-master.approve', $tahun) }}" class="mt-6 flex justify-end gap-3">@csrf @method('PUT')<button type="button" @click="approveOpen = false" class="rounded-lg border px-4 py-2 text-sm">Batal</button><button type="submit" class="rounded-lg bg-success-600 px-4 py-2 text-sm font-semibold text-white">Ya, Setujui</button></form>
            </div>
        </div>

        <div x-show="rejectOpen" @keydown.escape.window="rejectOpen = false" class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4" x-transition>
            <div @click.outside="rejectOpen = false" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Kembalikan untuk Revisi</h2>
                <form method="POST" action="{{ route('pimpinan.approval-master.reject', $tahun) }}" class="mt-5 space-y-4">@csrf @method('PUT')<div><label for="catatan_approval" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan revisi *</label><textarea id="catatan_approval" name="catatan_approval" required minlength="5" rows="4" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="Jelaskan bagian yang perlu diperbaiki..."></textarea></div><div class="flex justify-end gap-3"><button type="button" @click="rejectOpen = false" class="rounded-lg border px-4 py-2 text-sm">Batal</button><button type="submit" class="rounded-lg bg-error-600 px-4 py-2 text-sm font-semibold text-white">Kembalikan</button></div></form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function approvalMasterModal() {
            return { approveOpen: false, rejectOpen: false };
        }
    </script>
@endpush
