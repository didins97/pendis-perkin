@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        @php
            $verificationRoutePrefix = auth()->user()->role === 'pimpinan'
                ? 'pimpinan.realisasi'
                : 'admin.realisasi';
        @endphp
        <x-common.page-breadcrumb pageTitle="Verifikasi Eviden Realisasi" />

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
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M7.5 3.75h9a2.25 2.25 0 0 1 2.25 2.25v13.5H5.25V6A2.25 2.25 0 0 1 7.5 3.75Z"/><path stroke-linecap="round" d="M9 3.75V2.25h6v1.5"/></svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">Verifikasi Eviden Realisasi</h1>
                            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-600 dark:text-gray-400">Tinjau capaian dan dokumen pendukung pegawai. Setujui eviden yang sesuai atau berikan catatan agar pegawai dapat melakukan revisi.</p>
                        </div>
                    </div>
                    <div class="shrink-0 rounded-xl border border-brand-100 bg-white/80 px-4 py-3 text-sm shadow-sm dark:border-brand-500/20 dark:bg-gray-900/70">
                        <span class="text-gray-500 dark:text-gray-400">Total eviden</span>
                        <p class="mt-0.5 text-2xl font-bold leading-none text-gray-900 dark:text-white">{{ $items->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 border-b border-gray-100 px-5 py-5 dark:border-gray-800 sm:grid-cols-3 sm:px-7">
                <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50/70 p-4 dark:border-amber-500/20 dark:bg-amber-500/5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm.75 4a.75.75 0 0 0-1.5 0v4c0 .414.336.75.75.75h2.5a.75.75 0 0 0 0-1.5h-1.75V6Z" clip-rule="evenodd"/></svg></span>
                    <div><p class="text-xs font-medium text-amber-800 dark:text-amber-400">Menunggu ditinjau</p><p class="mt-0.5 text-xl font-bold text-amber-900 dark:text-amber-300">{{ $statusCounts['pending'] }}</p></div>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.78-9.72a.75.75 0 0 0-1.06-1.06l-3.47 3.47-1.47-1.47a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l4-4Z" clip-rule="evenodd"/></svg></span>
                    <div><p class="text-xs font-medium text-emerald-800 dark:text-emerald-400">Disetujui</p><p class="mt-0.5 text-xl font-bold text-emerald-900 dark:text-emerald-300">{{ $statusCounts['approved'] }}</p></div>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50/70 p-4 dark:border-rose-500/20 dark:bg-rose-500/5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm2.53-10.53a.75.75 0 0 0-1.06 0L10 8.94 8.53 7.47a.75.75 0 1 0-1.06 1.06L8.94 10l-1.47 1.47a.75.75 0 1 0 1.06 1.06L10 11.06l1.47 1.47a.75.75 0 1 0 1.06-1.06L11.06 10l1.47-1.47a.75.75 0 0 0 0-1.06Z" clip-rule="evenodd"/></svg></span>
                    <div><p class="text-xs font-medium text-rose-800 dark:text-rose-400">Perlu revisi</p><p class="mt-0.5 text-xl font-bold text-rose-900 dark:text-rose-300">{{ $statusCounts['rejected'] }}</p></div>
                </div>
            </div>

            <div class="px-5 py-5 sm:px-7">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">Daftar eviden pegawai</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cari nama, sekolah, atau indikator; gunakan filter untuk fokus pada status tertentu.</p>
                    </div>
                    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                        <label class="relative block sm:w-72">
                            <span class="sr-only">Cari eviden</span>
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.478 9.762l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
                            <input id="evidence-search" type="search" class="h-11 w-full rounded-lg border border-gray-300 bg-white pl-9 pr-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Cari pegawai atau indikator...">
                        </label>
                        <label>
                            <span class="sr-only">Filter status</span>
                            <select id="evidence-status" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 sm:w-48">
                                <option value="">Semua status</option>
                                <option value="pending">Menunggu ditinjau</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Perlu revisi</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="min-w-[1050px] w-full text-left">
                        <thead class="bg-gray-50 dark:bg-white/[0.03]">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Pegawai / Sekolah</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Indikator / Capaian</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Dokumen eviden</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="evidence-list" class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($items as $item)
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
                                <tr data-evidence-row data-status="{{ $status }}" class="align-top transition hover:bg-gray-50/70 dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $item->user?->name ?? 'Pegawai tidak ditemukan' }}</p>
                                        <p class="mt-1 max-w-xs text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $item->user?->sekolah?->nama_sekolah ?? 'Sekolah belum diatur' }}</p>
                                        <p class="mt-1 text-xs text-gray-400">{{ $item->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</p>
                                    </td>
                                    <td class="max-w-sm px-4 py-4">
                                        <p class="text-sm leading-5 text-gray-800 dark:text-gray-200">{{ $item->indikator?->indikator_kinerja ?? 'Indikator tidak ditemukan' }}</p>
                                        <p class="mt-2 inline-flex rounded-md bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700 dark:bg-white/10 dark:text-gray-300">Capaian: {{ $item->realisasi_capaian ?: '-' }}</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($evidenUrl)
                                            <a href="{{ $evidenUrl }}" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-2 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-gray-700 dark:bg-gray-900 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/5">
                                                @if ($isImage)
                                                    <img src="{{ $evidenUrl }}" alt="Pratinjau eviden" class="h-11 w-11 rounded-md object-cover">
                                                @else
                                                    <span class="flex h-11 w-11 items-center justify-center rounded-md {{ $extension === 'pdf' ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400' : 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' }}">
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 2.75A1.75 1.75 0 0 1 5.75 1h5.69c.464 0 .91.184 1.238.512l3.81 3.81c.328.328.512.774.512 1.238v10.69A1.75 1.75 0 0 1 15.25 19h-9.5A1.75 1.75 0 0 1 4 17.25V2.75Zm7.5-.19V6.5h3.94L11.5 2.56Z" clip-rule="evenodd"/></svg>
                                                    </span>
                                                @endif
                                                <span><span class="block text-xs font-semibold text-gray-800 group-hover:text-brand-700 dark:text-gray-200 dark:group-hover:text-brand-400">{{ $extension ? strtoupper($extension) : 'FILE' }}</span><span class="mt-0.5 block text-xs text-brand-600 dark:text-brand-400">Buka dokumen ↗</span></span>
                                            </a>
                                        @else
                                            <span class="text-sm text-gray-400">Dokumen tidak tersedia</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $status === 'approved' ? 'bg-emerald-500' : ($status === 'rejected' ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                                            {{ $statusLabel }}
                                        </span>
                                        @if ($status === 'rejected' && $item->catatan_verifikator)
                                            <p class="mt-2 max-w-48 text-xs leading-5 text-rose-600 dark:text-rose-400">{{ $item->catatan_verifikator }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        @if ($status !== 'approved')
                                            <div class="inline-flex flex-col items-stretch gap-2 text-left">
                                                <form method="POST" action="{{ route($verificationRoutePrefix . '.approve', $item->id) }}" onsubmit="return confirm('Setujui eviden ini?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7 7.06a1 1 0 0 1-1.42 0l-3-3.03a1 1 0 0 1 1.42-1.408L9 11.627l6.29-6.33a1 1 0 0 1 1.414-.007Z" clip-rule="evenodd"/></svg>
                                                        Setujui eviden
                                                    </button>
                                                </form>
                                                <details class="group">
                                                    <summary class="flex cursor-pointer list-none items-center justify-center rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:border-rose-500/30 dark:text-rose-400 dark:hover:bg-rose-500/10">
                                                        Minta revisi
                                                        <svg class="ml-1 h-3.5 w-3.5 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                                                    </summary>
                                                    <form method="POST" action="{{ route($verificationRoutePrefix . '.reject', $item->id) }}" class="mt-2 w-64 rounded-lg border border-gray-200 bg-white p-3 text-left shadow-lg dark:border-gray-700 dark:bg-gray-900">
                                                        @csrf
                                                        @method('PUT')
                                                        <label for="catatan-{{ $item->id }}" class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Alasan revisi <span class="text-rose-500">*</span></label>
                                                        <textarea id="catatan-{{ $item->id }}" name="catatan_verifikator" required minlength="5" rows="3" maxlength="2000" placeholder="Jelaskan bagian eviden yang perlu diperbaiki..." class="mt-1.5 w-full resize-y rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-800 outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/15 dark:border-gray-700 dark:bg-gray-950 dark:text-white"></textarea>
                                                        <p class="mt-1 text-[11px] text-gray-400">Minimal 5 karakter.</p>
                                                        <button type="submit" class="mt-2 w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">Kirim catatan revisi</button>
                                                    </form>
                                                </details>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.78-9.72a.75.75 0 0 0-1.06-1.06l-3.47 3.47-1.47-1.47a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l4-4Z" clip-rule="evenodd"/></svg>
                                                Selesai diverifikasi
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr data-no-evidence>
                                    <td colspan="5" class="px-5 py-14 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-white/5">
                                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 2.75A1.75 1.75 0 0 1 5.75 1h5.69c.464 0 .91.184 1.238.512l3.81 3.81c.328.328.512.774.512 1.238v10.69A1.75 1.75 0 0 1 15.25 19h-9.5A1.75 1.75 0 0 1 4 17.25V2.75Zm7.5-.19V6.5h3.94L11.5 2.56Z" clip-rule="evenodd"/></svg>
                                        </div>
                                        <p class="mt-3 font-semibold text-gray-800 dark:text-gray-200">Belum ada eviden masuk</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Eviden realisasi pegawai akan muncul di sini setelah diunggah.</p>
                                    </td>
                                </tr>
                            @endforelse
                            @if ($items->isNotEmpty())
                                <tr id="evidence-no-results" class="hidden">
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada eviden yang cocok dengan pencarian atau filter ini.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <p id="evidence-result-count" class="mt-3 text-xs text-gray-500 dark:text-gray-400" aria-live="polite"></p>
            </div>
        </section>
    </div>

    @if ($items->isNotEmpty())
        <script>
            (() => {
                const search = document.getElementById('evidence-search');
                const statusFilter = document.getElementById('evidence-status');
                const rows = Array.from(document.querySelectorAll('[data-evidence-row]'));
                const noResults = document.getElementById('evidence-no-results');
                const resultCount = document.getElementById('evidence-result-count');

                const filterRows = () => {
                    const query = search.value.trim().toLocaleLowerCase();
                    const status = statusFilter.value;
                    let visible = 0;

                    rows.forEach((row) => {
                        const matchesQuery = row.textContent.toLocaleLowerCase().includes(query);
                        const matchesStatus = !status || row.dataset.status === status;
                        const show = matchesQuery && matchesStatus;
                        row.hidden = !show;
                        if (show) visible += 1;
                    });

                    noResults.classList.toggle('hidden', visible > 0);
                    resultCount.textContent = `${visible} dari ${rows.length} eviden ditampilkan`;
                };

                search.addEventListener('input', filterRows);
                statusFilter.addEventListener('change', filterRows);
                filterRows();
            })();
        </script>
    @endif
@endsection
