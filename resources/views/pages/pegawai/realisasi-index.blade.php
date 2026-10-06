@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-common.page-breadcrumb pageTitle="Riwayat Eviden Realisasi" />

        @if (session('success'))
            <div role="status" class="flex items-start gap-3 rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">
                <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.78-9.72a.75.75 0 0 0-1.06-1.06l-3.47 3.47-1.47-1.47a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l4-4Z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="bg-gradient-to-br from-brand-50 via-white to-white px-5 py-6 dark:from-brand-500/10 dark:via-gray-900 dark:to-gray-900 sm:px-7">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-500 text-white shadow-sm shadow-brand-500/25">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M7.5 3.75h9A2.25 2.25 0 0 1 18.75 6v13.5H5.25V6A2.25 2.25 0 0 1 7.5 3.75Z"/><path stroke-linecap="round" d="M9 3.75V2.25h6v1.5"/></svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">Riwayat Eviden Saya</h1>
                            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-600 dark:text-gray-400">Pantau status verifikasi, lihat dokumen yang sudah diunggah, dan baca catatan revisi dari verifikator.</p>
                        </div>
                    </div>
                    <a href="{{ route('pegawai.realisasi.create') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                        Input eviden baru
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 border-b border-gray-100 px-5 py-5 dark:border-gray-800 sm:grid-cols-4 sm:px-7">
                <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total eviden</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $items->count() }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-4 dark:border-amber-500/20 dark:bg-amber-500/5">
                    <p class="text-xs font-medium text-amber-800 dark:text-amber-400">Menunggu ditinjau</p>
                    <p class="mt-1 text-2xl font-bold text-amber-900 dark:text-amber-300">{{ $statusCounts['pending'] }}</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/5">
                    <p class="text-xs font-medium text-emerald-800 dark:text-emerald-400">Disetujui</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-900 dark:text-emerald-300">{{ $statusCounts['approved'] }}</p>
                </div>
                <div class="rounded-xl border border-rose-200 bg-rose-50/70 p-4 dark:border-rose-500/20 dark:bg-rose-500/5">
                    <p class="text-xs font-medium text-rose-800 dark:text-rose-400">Perlu revisi</p>
                    <p class="mt-1 text-2xl font-bold text-rose-900 dark:text-rose-300">{{ $statusCounts['rejected'] }}</p>
                </div>
            </div>

            <div class="px-5 py-5 sm:px-7">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">Daftar eviden</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cari indikator atau gunakan filter status untuk menemukan eviden dengan cepat.</p>
                    </div>
                    @if ($items->isNotEmpty())
                        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                            <label class="relative block sm:w-72">
                                <span class="sr-only">Cari indikator atau catatan</span>
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.478 9.762l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
                                <input id="history-search" type="search" class="h-11 w-full rounded-lg border border-gray-300 bg-white pl-9 pr-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Cari indikator...">
                            </label>
                            <label>
                                <span class="sr-only">Filter status</span>
                                <select id="history-status" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 sm:w-48">
                                    <option value="">Semua status</option>
                                    <option value="pending">Menunggu ditinjau</option>
                                    <option value="approved">Disetujui</option>
                                    <option value="rejected">Perlu revisi</option>
                                </select>
                            </label>
                        </div>
                    @endif
                </div>

                @if ($items->isNotEmpty())
                    <div class="space-y-3" id="evidence-history-list">
                        @foreach ($items as $item)
                            @php
                                $status = $item->status_verifikasi ?: 'pending';
                                $statusClass = match ($status) {
                                    'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                                    'rejected' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
                                    default => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
                                };
                                $statusLabel = match ($status) {
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Perlu revisi',
                                    default => 'Menunggu ditinjau',
                                };
                                $evidenPath = $item->file_eviden;
                                $evidenUrl = $evidenPath ? asset('storage/' . $evidenPath) : null;
                                $extension = $evidenPath ? strtolower(pathinfo($evidenPath, PATHINFO_EXTENSION)) : '';
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            <article data-history-item data-status="{{ $status }}" class="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-brand-200 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-brand-500/30 sm:p-5">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">{{ $item->indikator?->indikator_kinerja ?? 'Indikator tidak tersedia' }}</h3>
                                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $status === 'approved' ? 'bg-emerald-500' : ($status === 'rejected' ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span>{{ $item->created_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}</span>
                                            @if ($item->indikator?->sasaran)
                                                <span class="hidden text-gray-300 dark:text-gray-700 sm:inline">•</span>
                                                <span>Sasaran {{ $item->indikator->sasaran->no_urut }}: {{ $item->indikator->sasaran->sasaran_kegiatan }}</span>
                                            @endif
                                        </div>
                                        <div class="mt-4 flex flex-wrap items-center gap-3">
                                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 dark:bg-white/10 dark:text-gray-300">
                                                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2a.75.75 0 0 1 .75.75v6.5h6.5a.75.75 0 0 1 0 1.5h-6.5v6.5a.75.75 0 0 1-1.5 0v-6.5h-6.5a.75.75 0 0 1 0-1.5h6.5v-6.5A.75.75 0 0 1 10 2Z"/></svg>
                                                Capaian: {{ $item->realisasi_capaian ?: '-' }}
                                            </span>
                                            @if (filled($item->catatan_guru))
                                                <details class="group">
                                                    <summary class="cursor-pointer list-none text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Lihat catatan saya</summary>
                                                    <p class="mt-2 max-w-2xl rounded-lg bg-gray-50 px-3 py-2 text-xs leading-5 text-gray-600 dark:bg-white/[0.03] dark:text-gray-400">{{ $item->catatan_guru }}</p>
                                                </details>
                                            @endif
                                        </div>
                                        @if ($status === 'rejected' && filled($item->catatan_verifikator))
                                            <div class="mt-4 flex gap-3 rounded-xl border border-rose-200 bg-rose-50/70 p-3 dark:border-rose-500/20 dark:bg-rose-500/5">
                                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-rose-600 dark:text-rose-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0ZM9 6.75a1 1 0 1 1 2 0v3a1 1 0 1 1-2 0v-3Zm1 7.5a1.125 1.125 0 1 0 0-2.25 1.125 1.125 0 0 0 0 2.25Z" clip-rule="evenodd"/></svg>
                                                <div>
                                                    <p class="text-xs font-semibold text-rose-800 dark:text-rose-300">Catatan revisi dari verifikator</p>
                                                    <p class="mt-1 whitespace-pre-line text-xs leading-5 text-rose-700 dark:text-rose-400">{{ $item->catatan_verifikator }}</p>
                                                </div>
                                            </div>
                                        @elseif ($status === 'approved')
                                            <p class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.78-9.72a.75.75 0 0 0-1.06-1.06l-3.47 3.47-1.47-1.47a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l4-4Z" clip-rule="evenodd"/></svg>
                                                Eviden sudah diperiksa dan diterima.
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex shrink-0 items-center gap-3 border-t border-gray-100 pt-3 dark:border-gray-800 lg:border-0 lg:pt-0">
                                        @if ($evidenUrl)
                                            <a href="{{ $evidenUrl }}" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white p-2 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-gray-700 dark:bg-gray-900 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/5">
                                                @if ($isImage)
                                                    <img src="{{ $evidenUrl }}" alt="Pratinjau eviden" class="h-10 w-10 rounded-md object-cover">
                                                @else
                                                    <span class="flex h-10 w-10 items-center justify-center rounded-md {{ $extension === 'pdf' ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400' : 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' }}">
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 2.75A1.75 1.75 0 0 1 5.75 1h5.69c.464 0 .91.184 1.238.512l3.81 3.81c.328.328.512.774.512 1.238v10.69A1.75 1.75 0 0 1 15.25 19h-9.5A1.75 1.75 0 0 1 4 17.25V2.75Zm7.5-.19V6.5h3.94L11.5 2.56Z" clip-rule="evenodd"/></svg>
                                                    </span>
                                                @endif
                                                <span class="pr-1"><span class="block text-xs font-semibold text-gray-800 group-hover:text-brand-700 dark:text-gray-200 dark:group-hover:text-brand-400">{{ $extension ? strtoupper($extension) : 'FILE' }}</span><span class="mt-0.5 block text-xs text-brand-600 dark:text-brand-400">Buka eviden ↗</span></span>
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">Dokumen tidak tersedia</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div id="history-no-results" class="hidden rounded-xl border border-dashed border-gray-300 px-5 py-10 text-center dark:border-gray-700">
                        <p class="font-semibold text-gray-800 dark:text-gray-200">Eviden tidak ditemukan</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coba ubah kata pencarian atau pilih status lainnya.</p>
                    </div>
                    <p id="history-result-count" class="mt-3 text-xs text-gray-500 dark:text-gray-400" aria-live="polite"></p>
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 px-5 py-12 text-center dark:border-gray-700">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-white/5">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 2.75A1.75 1.75 0 0 1 5.75 1h5.69c.464 0 .91.184 1.238.512l3.81 3.81c.328.328.512.774.512 1.238v10.69A1.75 1.75 0 0 1 15.25 19h-9.5A1.75 1.75 0 0 1 4 17.25V2.75Zm7.5-.19V6.5h3.94L11.5 2.56Z" clip-rule="evenodd"/></svg>
                        </div>
                        <p class="mt-3 font-semibold text-gray-800 dark:text-gray-200">Belum ada eviden yang diunggah</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai unggah eviden realisasi untuk melihat statusnya di halaman ini.</p>
                        <a href="{{ route('pegawai.realisasi.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-600">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                            Upload eviden pertama
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </div>

    @if ($items->isNotEmpty())
        <script>
            (() => {
                const search = document.getElementById('history-search');
                const statusFilter = document.getElementById('history-status');
                const cards = Array.from(document.querySelectorAll('[data-history-item]'));
                const noResults = document.getElementById('history-no-results');
                const resultCount = document.getElementById('history-result-count');

                const filterItems = () => {
                    const query = search.value.trim().toLocaleLowerCase();
                    const status = statusFilter.value;
                    let visible = 0;

                    cards.forEach((card) => {
                        const matchesQuery = card.textContent.toLocaleLowerCase().includes(query);
                        const matchesStatus = !status || card.dataset.status === status;
                        const show = matchesQuery && matchesStatus;
                        card.hidden = !show;
                        if (show) visible += 1;
                    });

                    noResults.classList.toggle('hidden', visible > 0);
                    resultCount.textContent = `${visible} dari ${cards.length} eviden ditampilkan`;
                };

                search.addEventListener('input', filterItems);
                statusFilter.addEventListener('change', filterItems);
                filterItems();
            })();
        </script>
    @endif
@endsection
