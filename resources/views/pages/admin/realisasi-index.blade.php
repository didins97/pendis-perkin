@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Verifikasi Eviden Realisasi" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700">
            {{ session('success') }}
        </div>
    @endif

    <x-common.component-card title="Antrean Eviden" desc="Daftar realisasi PERKIN yang menunggu verifikasi admin.">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Pegawai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Sekolah</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Indikator</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Capaian</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Eviden</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($items as $item)
                    <tr class="border-b">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-white">{{ $item->user?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item->user?->sekolah?->nama_sekolah ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item->indikator?->indikator_kinerja ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-white">{{ $item->realisasi_capaian }}</td>
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
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item->status_verifikasi === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($item->status_verifikasi === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ ucfirst($item->status_verifikasi) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($item->status_verifikasi !== 'approved')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('admin.realisasi.approve', $item->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.realisasi.reject', $item->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="catatan_verifikator" required placeholder="Catatan revisi" class="h-9 rounded-lg border border-gray-300 px-3 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                        <button type="submit" class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-bold text-white hover:bg-rose-700">Reject</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs font-semibold text-emerald-600">Terverifikasi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-5 text-center text-sm text-gray-500">Belum ada eviden realisasi.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-common.component-card>
@endsection
