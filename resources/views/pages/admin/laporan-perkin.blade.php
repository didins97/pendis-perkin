@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-common.page-breadcrumb pageTitle="Laporan & Cetak Perkin" />

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Laporan Perjanjian Kinerja</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih Tahun Anggaran untuk melihat preview atau mengunduh dokumen PK resmi.</p>
                </div>
                <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">{{ $tahuns->where('status_approval', 'approved')->count() }} tahun siap cetak</span>
            </div>

            @if (session('success'))
                <div class="mt-5 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">{{ session('success') }}</div>
            @endif

            <div class="mt-6 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                <table class="min-w-full text-left">
                    <thead class="bg-gray-50 dark:bg-white/[0.03]">
                        <tr>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Tahun</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Status Master</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Sasaran / Program</th>
                            <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Disetujui Oleh</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($tahuns as $tahun)
                            @php
                                $statusClass = match ($tahun->status_approval) {
                                    'approved' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                                    'submitted' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                                    'rejected' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300',
                                };
                                $statusLabel = match ($tahun->status_approval) {
                                    'approved' => 'Disetujui',
                                    'submitted' => 'Menunggu Approval',
                                    'rejected' => 'Perlu Revisi',
                                    default => 'Draft',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02]">
                                <td class="whitespace-nowrap px-5 py-4"><p class="text-lg font-bold text-gray-900 dark:text-white">{{ $tahun->tahun }}</p><p class="text-xs text-gray-500 dark:text-gray-400">Status: {{ ucfirst($tahun->status) }}</p></td>
                                <td class="whitespace-nowrap px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $tahun->sasarans_count }} sasaran <span class="text-gray-400">·</span> {{ $tahun->master_programs_count }} program</td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $tahun->approver?->name ?? '-' }}<p class="text-xs text-gray-400">{{ $tahun->approved_at?->translatedFormat('d M Y') ?? '-' }}</p></td>
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    @if ($tahun->status_approval === 'approved')
                                        <div class="inline-flex gap-2"><a href="{{ route('perkin.preview', $tahun) }}" target="_blank" rel="noopener" class="rounded-lg border border-brand-200 px-3 py-2 text-xs font-semibold text-brand-700 hover:bg-brand-50 dark:border-brand-500/30 dark:text-brand-400">Preview</a><a href="{{ route($pdfRouteName, $tahun->id) }}" class="rounded-lg bg-brand-500 px-3 py-2 text-xs font-semibold text-white hover:bg-brand-600">Download PDF</a></div>
                                    @else
                                        <span class="text-xs text-gray-400">Belum dapat dicetak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-500">Belum ada Tahun Anggaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
