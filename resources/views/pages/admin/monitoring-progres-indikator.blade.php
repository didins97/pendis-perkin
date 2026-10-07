@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                    <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                    Monitoring kinerja
                </div>
                <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Indikator Kinerja</h1>
                {{-- <p class="mt-1 max-w-3xl text-sm leading-6 text-gray-500 dark:text-gray-400">Pemantauan detail persentase pengisian eviden per Sasaran dan Indikator untuk seluruh Satker/Madrasah.</p> --}}
                <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">Tahun anggaran {{ $tahun?->tahun ?? 'belum tersedia' }} · {{ $pegawaiCount }} pegawai aktif</p>
            </div>

            <form method="GET" action="{{ route('admin.monitoring-progres') }}" class="grid w-full grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:grid-cols-2 lg:w-auto">
                <label class="block sm:min-w-60">
                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Satker / Madrasah</span>
                    <select name="sekolah_id" onchange="this.form.submit()" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
                        <option value="">Semua Satker / Madrasah</option>
                        @foreach ($sekolahs as $sekolah)
                            <option value="{{ $sekolah->id }}" @selected((string) $sekolahId === (string) $sekolah->id)>{{ $sekolah->nama_sekolah }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                </label>
                <label class="block sm:min-w-48">
                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status progres</span>
                    <select name="status" onchange="this.form.submit()" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
                        <option value="all" @selected($statusFilter === 'all')>Semua status</option>
                        <option value="complete" @selected($statusFilter === 'complete')>Tuntas 100%</option>
                        <option value="progress" @selected($statusFilter === 'progress')>Progres 1–99%</option>
                        <option value="critical" @selected($statusFilter === 'critical')>Zona Merah 0%</option>
                    </select>
                    <input type="hidden" name="sekolah_id" value="{{ $sekolahId }}">
                </label>
            </form>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-4 border-b border-gray-100 px-5 py-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Progres per Sasaran Strategis</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih bar indikator untuk melihat daftar pegawai yang sudah dan belum mengunggah eviden.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs font-medium">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Tuntas 100%</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400"><span class="h-2 w-2 rounded-full bg-amber-400"></span>Progres 30–99%</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400"><span class="h-2 w-2 rounded-full bg-rose-500"></span>Kritis &lt;30%</span>
                </div>
            </div>

            <div class="space-y-7 p-5 sm:p-6">
                @forelse ($indicatorGroups as $group)
                    <section aria-labelledby="sasaran-{{ $loop->index }}">
                        <div class="mb-3 inline-flex max-w-full items-start gap-2 rounded-lg border border-brand-100 bg-brand-50/70 px-3 py-2 dark:border-brand-500/20 dark:bg-brand-500/5">
                            <span class="shrink-0 rounded-md bg-brand-500 px-2 py-1 text-[11px] font-bold text-white">Sasaran {{ $group['no_urut'] }}</span>
                            <h3 id="sasaran-{{ $loop->index }}" class="text-sm font-semibold leading-5 text-brand-900 dark:text-brand-200">{{ $group['sasaran'] }}</h3>
                        </div>

                        <div class="space-y-2">
                            @foreach ($group['indikators'] as $indikator)
                                @php
                                    $progressColor = $indikator['percentage'] === 100
                                        ? 'bg-emerald-500'
                                        : ($indikator['percentage'] >= 30 ? 'bg-amber-400' : 'bg-rose-500');
                                    $percentageColor = $indikator['percentage'] === 100
                                        ? 'text-emerald-700 dark:text-emerald-400'
                                        : ($indikator['percentage'] >= 30 ? 'text-amber-700 dark:text-amber-400' : 'text-rose-700 dark:text-rose-400');
                                @endphp
                                <details class="group rounded-xl border border-gray-200 bg-white transition open:border-brand-200 open:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:open:border-brand-500/30">
                                    <summary class="cursor-pointer list-none rounded-xl px-4 py-4 outline-none transition hover:bg-gray-50/70 focus-visible:ring-2 focus-visible:ring-brand-500 dark:hover:bg-white/[0.02] sm:px-5">
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                            <span class="pr-2 text-sm font-semibold leading-5 text-gray-800 dark:text-gray-200">{{ $indikator['name'] }}</span>
                                            <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400">{{ $indikator['uploaded_count'] }} dari {{ $indikator['pegawai_count'] }} pegawai</span>
                                        </div>
                                        <div class="mt-3 flex items-center gap-3">
                                            <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800" role="progressbar" aria-label="Progres {{ $indikator['name'] }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $indikator['percentage'] }}">
                                                <div class="h-full rounded-full {{ $progressColor }} transition-all duration-500" style="width: {{ $indikator['percentage'] }}%"></div>
                                            </div>
                                            <span class="w-12 text-right text-sm font-bold tabular-nums {{ $percentageColor }}">{{ $indikator['percentage'] }}%</span>
                                            <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                                        </div>
                                    </summary>

                                    <div class="border-t border-gray-100 px-4 py-4 dark:border-gray-800 sm:px-5">
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/5">
                                                <div class="flex items-center justify-between gap-2">
                                                    <h4 class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">Sudah mengunggah</h4>
                                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">{{ $indikator['uploaded_pegawai']->count() }}</span>
                                                </div>
                                                @if ($indikator['uploaded_pegawai']->isNotEmpty())
                                                    <ul class="mt-3 max-h-56 space-y-2 overflow-y-auto">
                                                        @foreach ($indikator['uploaded_pegawai'] as $pegawai)
                                                            <li class="flex items-start justify-between gap-3 text-xs">
                                                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $pegawai->name }}</span>
                                                                <span class="text-right text-gray-500 dark:text-gray-400">{{ $pegawai->sekolah?->nama_sekolah ?? 'Satker belum diatur' }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Belum ada pegawai yang mengunggah.</p>
                                                @endif
                                            </div>

                                            <div class="rounded-xl border border-rose-200 bg-rose-50/50 p-4 dark:border-rose-500/20 dark:bg-rose-500/5">
                                                <div class="flex items-center justify-between gap-2">
                                                    <h4 class="text-sm font-semibold text-rose-800 dark:text-rose-300">Belum mengunggah</h4>
                                                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-bold text-rose-700 dark:bg-rose-500/15 dark:text-rose-300">{{ $indikator['missing_pegawai']->count() }}</span>
                                                </div>
                                                @if ($indikator['missing_pegawai']->isNotEmpty())
                                                    <ul class="mt-3 max-h-56 space-y-2 overflow-y-auto">
                                                        @foreach ($indikator['missing_pegawai'] as $pegawai)
                                                            <li class="flex items-start justify-between gap-3 text-xs">
                                                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $pegawai->name }}</span>
                                                                <span class="text-right text-gray-500 dark:text-gray-400">{{ $pegawai->sekolah?->nama_sekolah ?? 'Satker belum diatur' }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Semua pegawai sudah mengunggah eviden.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 px-5 py-12 text-center dark:border-gray-700">
                        <p class="font-semibold text-gray-800 dark:text-gray-200">Tidak ada indikator yang sesuai</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if (! $tahun)
                                Belum ada Tahun Anggaran yang disetujui untuk dimonitor.
                            @elseif ($indicatorCount === 0)
                                Master indikator untuk tahun {{ $tahun->tahun }} belum disetujui.
                            @else
                                Tidak ada indikator yang cocok dengan filter status dan satker yang dipilih.
                            @endif
                        </p>
                        @if ($statusFilter !== 'all' || $sekolahId)
                            <a href="{{ route('admin.monitoring-progres') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Hapus filter</a>
                        @endif
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
