@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section>
            <div>
                <div class="flex items-center gap-2 text-sm font-medium text-brand-600 dark:text-brand-400">
                    <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                    <span>Monitoring Operasional</span>
                </div>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Dashboard Admin</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pemantauan eviden pegawai untuk tahun anggaran {{ $tahun?->tahun ?? 'belum tersedia' }}.
                </p>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan operasional">
            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75M14 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg>
                    </span>
                    <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">Pegawai aktif</span>
                </div>
                <p class="mt-5 text-sm font-medium text-gray-500 dark:text-gray-400">Total Pegawai Aktif</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($pegawaiCount) }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kuota eviden: {{ number_format($totalQuota) }} unggahan</p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/><circle cx="12" cy="12" r="9"/></svg>
                    </span>
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">{{ $completionPercentage }}%</span>
                </div>
                <p class="mt-5 text-sm font-medium text-gray-500 dark:text-gray-400">Capaian Tuntas</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($completedSlots) }} <span class="text-base font-medium text-gray-400">/ {{ number_format($totalQuota) }}</span></p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Indikator-pegawai yang sudah diunggah</p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01"/></svg>
                    </span>
                    <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700 dark:bg-violet-500/10 dark:text-violet-400">Terhubung</span>
                </div>
                <p class="mt-5 text-sm font-medium text-gray-500 dark:text-gray-400">Satker / Madrasah</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($connectedSchoolCount) }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Memiliki pegawai aktif</p>
            </article>

            <article class="rounded-2xl border border-rose-300 bg-gradient-to-br from-rose-50 to-white p-5 shadow-sm shadow-rose-100 dark:border-rose-500/30 dark:from-rose-500/10 dark:to-gray-900 dark:shadow-none">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-600 text-white shadow-md shadow-rose-600/25">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.86 1.82 18.5A2 2 0 0 0 3.55 21h16.9a2 2 0 0 0 1.73-2.5L13.7 3.86a2 2 0 0 0-3.4 0Z"/></svg>
                    </span>
                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">Perlu tindakan</span>
                </div>
                <p class="mt-5 text-sm font-semibold text-rose-700 dark:text-rose-300">Zona Merah</p>
                <p class="mt-1 text-3xl font-bold text-rose-700 dark:text-rose-300">{{ number_format($redZonePegawais->count()) }}</p>
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">Pegawai belum mengunggah eviden</p>
            </article>
        </section>

        <section class="overflow-hidden rounded-2xl border border-rose-200 bg-white shadow-sm dark:border-rose-500/20 dark:bg-gray-900">
            <div class="flex flex-col gap-3 border-b border-rose-100 bg-rose-50/70 px-5 py-4 dark:border-rose-500/10 dark:bg-rose-500/5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-600 text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.86 1.82 18.5A2 2 0 0 0 3.55 21h16.9a2 2 0 0 0 1.73-2.5L13.7 3.86a2 2 0 0 0-3.4 0Z"/></svg>
                        </span>
                        <h2 class="font-bold text-rose-900 dark:text-rose-200">Tindakan Cepat Zona Merah</h2>
                    </div>
                    <p class="mt-1 text-sm text-rose-700 dark:text-rose-300">Pegawai aktif yang belum mengunggah eviden untuk tahun anggaran terpilih.</p>
                </div>
                <span class="inline-flex w-fit rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">{{ $redZonePegawais->count() }} pegawai</span>
            </div>

            @if ($indicatorCount === 0)
                <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Zona merah belum dapat dihitung karena belum ada indikator yang disetujui.</div>
            @elseif ($redZonePegawais->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead class="bg-gray-50 dark:bg-white/[0.02]">
                            <tr>
                                <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Pegawai</th>
                                <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Satker / Madrasah</th>
                                <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Kepatuhan</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($redZonePegawais as $pegawai)
                                @php
                                    $waNumber = preg_replace('/\D+/', '', (string) $pegawai->nomor_wa);
                                    if (str_starts_with($waNumber, '0')) {
                                        $waNumber = '62'.substr($waNumber, 1);
                                    }
                                @endphp
                                <tr class="transition hover:bg-rose-50/30 dark:hover:bg-rose-500/[0.03]">
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $pegawai->name }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $pegawai->nip ?: $pegawai->email }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $pegawai->sekolah?->nama_sekolah ?? 'Satker belum diatur' }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-700 dark:bg-rose-500/15 dark:text-rose-300"><span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>0% · Belum upload</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            @if (strlen($waNumber) >= 9)
                                                @php
                                                    $reminderMessage = 'Yth. '.$pegawai->name.', mohon segera melengkapi eviden PERKIN tahun '.$tahun->tahun.'. Silakan masuk ke aplikasi untuk mengunggah eviden. Terima kasih.';
                                                @endphp
                                                <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode($reminderMessage) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" aria-label="Ingatkan {{ $pegawai->name }} melalui WhatsApp">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.52 3.48A11.82 11.82 0 0 0 12.1 0C5.53 0 .18 5.34.18 11.92c0 2.1.55 4.14 1.6 5.94L.08 24l6.3-1.65a11.9 11.9 0 0 0 5.71 1.46h.01c6.57 0 11.92-5.35 11.92-11.92a11.84 11.84 0 0 0-3.5-8.41ZM12.1 21.8h-.01a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.74.98 1-3.65-.23-.37a9.9 9.9 0 0 1-1.52-5.25c0-5.47 4.45-9.92 9.92-9.92a9.86 9.86 0 0 1 7.02 2.91 9.86 9.86 0 0 1 2.9 7.03c0 5.47-4.45 9.92-9.92 9.92Zm5.45-7.43c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.47-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.6.14-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.49 0 1.47 1.07 2.89 1.22 3.09.15.2 2.1 3.2 5.08 4.49.71.31 1.27.49 1.7.63.71.23 1.36.2 1.87.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35Z"/></svg>
                                                    Ingatkan
                                                </a>
                                            @else
                                                <span class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-xs font-medium text-gray-400 dark:border-gray-700" title="Nomor WhatsApp belum tersedia">WA belum tersedia</span>
                                            @endif
                                            <a href="{{ route('admin.pegawai.show', $pegawai) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700 dark:border-gray-700 dark:text-gray-300 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/5 dark:hover:text-brand-400">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center">
                    <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.78-9.72a.75.75 0 0 0-1.06-1.06l-3.47 3.47-1.47-1.47a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l4-4Z" clip-rule="evenodd"/></svg>
                    </span>
                    <p class="mt-3 font-semibold text-gray-800 dark:text-gray-200">Tidak ada pegawai di zona merah</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Semua pegawai sudah mengunggah setidaknya satu eviden.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
