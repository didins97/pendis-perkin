@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section>
            <p class="text-sm font-medium text-brand-600 dark:text-brand-400">Pimpinan</p>
            <h1 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">Ringkasan Eviden Perkin</h1>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Eviden</p>
                <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalEvidence) }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-white p-5 dark:border-amber-500/30 dark:bg-gray-900">
                <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Verifikasi</p>
                <p class="mt-3 text-2xl font-semibold text-amber-600 dark:text-amber-400">{{ number_format($pendingEvidence) }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-white p-5 dark:border-emerald-500/30 dark:bg-gray-900">
                <p class="text-sm text-gray-500 dark:text-gray-400">Disetujui</p>
                <p class="mt-3 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($approvedEvidence) }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-white p-5 dark:border-rose-500/30 dark:bg-gray-900">
                <p class="text-sm text-gray-500 dark:text-gray-400">Perlu Revisi</p>
                <p class="mt-3 text-2xl font-semibold text-rose-600 dark:text-rose-400">{{ number_format($revisionEvidence) }}</p>
            </div>
        </section>

        <x-common.component-card title="Eviden Terbaru" desc="Realisasi kinerja yang dikirim guru.">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-gray-500">Guru / Sekolah</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-gray-500">Indikator</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-gray-500">Capaian</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-gray-500">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($recentEvidence as $item)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->user?->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->user?->sekolah?->nama_sekolah ?? 'Sekolah belum diatur' }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->indikator?->indikator_kinerja ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $item->realisasi_capaian ?: '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ match ($item->status_verifikasi) { 'approved' => 'Disetujui', 'rejected' => 'Perlu Revisi', default => 'Menunggu' } }}</td>
                                <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $item->created_at?->format('d M Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada eviden Perkin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
    </div>
@endsection
