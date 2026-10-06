@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Riwayat Berkas Pegawai" />
    <div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Riwayat Berkas {{ $pegawai->name }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $pegawai->nip }} ·
                {{ $pegawai->sekolah?->nama_sekolah ?? 'Sekolah belum ditentukan' }}</p>
        </div><a href="{{ route('admin.pegawai.index') }}"
            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Kembali</a>
    </div>
    <x-common.component-card title="Riwayat Eviden Perkin" desc="Daftar eviden kinerja yang pernah dikirim pegawai.">
        <div class="w-full overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
            <table class="w-full min-w-[700px] text-left">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                    <tr>
                        <th class="px-5 py-4 text-xs font-medium uppercase text-gray-500">Indikator</th>
                        <th class="px-5 py-4 text-xs font-medium uppercase text-gray-500">Capaian</th>
                        <th class="px-5 py-4 text-xs font-medium uppercase text-gray-500">Catatan</th>
                        <th class="px-5 py-4 text-xs font-medium uppercase text-gray-500">Status</th>
                        <th class="px-5 py-4 text-xs font-medium uppercase text-gray-500">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                {{ $item->indikator?->indikator_kinerja ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item->realisasi_capaian ?: '-' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item->catatan_guru ?: '-' }}</td>
                            <td class="px-5 py-4"><span
                                    class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">{{ ucfirst($item->status_verifikasi) }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->created_at?->format('d M Y') }}</td>
                    </tr>@empty<tr>
                            <td colspan="5" class="px-6 py-14 text-center text-sm text-gray-500">Belum ada riwayat eviden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-common.component-card>
@endsection
