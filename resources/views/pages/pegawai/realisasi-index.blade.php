@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Riwayat Eviden Realisasi" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex justify-end">
        <a href="{{ route('pegawai.realisasi.create') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">
            + Input Eviden Baru
        </a>
    </div>

    <x-common.component-card title="Daftar Eviden Saya" desc="Realisasi PERKIN yang telah Anda unggah dan status verifikasinya.">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Indikator</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Eviden</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($items as $item)
                    <tr class="border-b align-top">
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ $item->indikator?->indikator_kinerja ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            @php
                                $evidenPath = $item->file_eviden ?? null;
                                $evidenUrl = $evidenPath ? asset('storage/' . $evidenPath) : null;
                                $extension = $evidenPath ? strtolower(pathinfo($evidenPath, PATHINFO_EXTENSION)) : '';
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $isPdf = $extension === 'pdf';
                            @endphp

                            @if ($evidenUrl)
                                @if ($isImage)
                                    <a href="{{ $evidenUrl }}" target="_blank" rel="noopener" class="inline-block">
                                        <img src="{{ $evidenUrl }}" alt="Preview eviden" class="h-16 w-16 rounded-md object-cover shadow-sm ring-1 ring-gray-200 transition hover:opacity-90" />
                                    </a>
                                @elseif ($isPdf)
                                    <a href="{{ $evidenUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                        <span>PDF</span>
                                        <span>Preview</span>
                                    </a>
                                @else
                                    <a href="{{ $evidenUrl }}" target="_blank" rel="noopener" class="inline-flex rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-xs text-gray-600 hover:bg-gray-100">
                                        Lihat file
                                    </a>
                                @endif
                            @else
                                <span class="inline-flex rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-xs text-gray-500">Dokumen</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php($status = $item->status_verifikasi)
                            <div class="space-y-2">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($status) }}
                                </span>

                                @if ($status === 'rejected' && filled($item->catatan_verifikator))
                                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">
                                        <p class="mb-1 font-semibold">Catatan revisi</p>
                                        <p class="leading-relaxed">{{ $item->catatan_verifikator }}</p>
                                    </div>
                                @elseif ($status === 'approved')
                                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                                        <p class="font-semibold">Eviden sudah diterima</p>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-5 text-center text-sm text-gray-500">Belum ada eviden realisasi yang diunggah.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-common.component-card>
@endsection
