@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section>
            <p class="text-sm font-medium text-brand-600 dark:text-brand-400">Pimpinan</p>
            <h1 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">Kepatuhan Eviden Pegawai</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tahun Perkin {{ $tahun?->tahun ?? 'belum tersedia' }}
                · {{ number_format($indicatorCount) }} indikator disetujui
            </p>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                [
                    'label' => 'Total Pegawai Aktif',
                    'value' => number_format($activePegawaiCount),
                    'description' => 'Guru dan tenaga kependidikan',
                    'badge' => 'Pegawai',
                    'tone' => 'brand',
                    'icon' => 'users',
                ],
                [
                    'label' => 'Capaian Eviden Keseluruhan',
                    'value' => $overallCompletion . '%',
                    'description' => 'Rata-rata indikator terkumpul',
                    'badge' => 'Capaian',
                    'tone' => 'success',
                    'icon' => 'chart',
                ],
                [
                    'label' => 'Satker / Madrasah Terhubung',
                    'value' => number_format($schoolCount),
                    'description' => 'Unit kerja terdaftar',
                    'badge' => 'Satker',
                    'tone' => 'warning',
                    'icon' => 'building',
                ],
                [
                    'label' => 'Zona Merah',
                    'value' => number_format($redZoneCount),
                    'description' => 'Pegawai belum mengunggah eviden',
                    'badge' => 'Perlu Tindak Lanjut',
                    'tone' => 'danger',
                    'icon' => 'alert',
                ],
            ] as $metric)
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $metric['tone'] === 'brand' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : ($metric['tone'] === 'success' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : ($metric['tone'] === 'warning' ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400')) }}">
                            @switch($metric['icon'])
                                @case('users')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10 10v-2a4 4 0 0 0-3-3.87m-1-11.96a4 4 0 0 1 0 7.75"/></svg>
                                    @break
                                @case('chart')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19V5m0 14h17M8 15l4-4 3 2 5-6"/></svg>
                                    @break
                                @case('building')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01m8-4v.01m0 3v.01"/></svg>
                                    @break
                                @default
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0Z"/></svg>
                            @endswitch
                        </span>
                        <span class="rounded-full px-3 py-1 text-[11px] font-semibold {{ $metric['tone'] === 'brand' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : ($metric['tone'] === 'success' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : ($metric['tone'] === 'warning' ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400')) }}">
                            {{ $metric['badge'] }}
                        </span>
                    </div>
                    <div class="mt-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $metric['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $metric['value'] }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $metric['description'] }}</p>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-5 py-5 dark:border-gray-800">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Status Kepatuhan Pegawai</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Distribusi progres pengumpulan eviden.</p>
                    </div>
                    <span class="shrink-0 rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ number_format($activePegawaiCount) }} pegawai</span>
                </div>
                @if ($indicatorCount === 0)
                    <div class="flex min-h-[320px] items-center justify-center px-6 text-center text-sm text-gray-500 dark:text-gray-400">
                        Belum ada indikator Perkin yang disetujui sebagai dasar pengukuran.
                    </div>
                @else
                    <div class="p-3 sm:p-5">
                        <div id="pegawai-compliance-donut" class="min-h-[320px]" role="img" aria-label="Grafik status kepatuhan pegawai"></div>
                    </div>
                @endif
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-100 px-5 py-5 dark:border-gray-800">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Perbandingan Kepatuhan Madrasah</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Rata-rata pengumpulan eviden per pegawai aktif.</p>
                        </div>
                        <span class="shrink-0 rounded-lg bg-violet-50 px-3 py-1.5 text-xs font-semibold text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">Perbandingan</span>
                    </div>
                </div>
                <div class="p-5">
                    <form method="GET" action="{{ route('pimpinan.dashboard') }}" class="mb-4 space-y-3"
                        x-data="{
                            open: false,
                            search: '',
                            selectedSchools: @js(array_map('strval', $selectedSchoolIds)),
                            allSchools: @js($schools->pluck('id')->map(fn ($id) => (string) $id)->values()),
                            get selectionLabel() {
                                if (this.selectedSchools.length === 0) return 'Semua satker / madrasah';
                                return `${this.selectedSchools.length} satker dipilih`;
                            },
                            selectAll() {
                                this.selectedSchools = [...this.allSchools];
                            },
                            clearSelection() {
                                this.selectedSchools = [];
                            }
                        }">
                        <div class="relative" @click.outside="open = false">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Bandingkan satker / madrasah
                            </span>
                            <button type="button" @click="open = !open" @keydown.escape="open = false"
                                :aria-expanded="open.toString()" aria-haspopup="listbox"
                                class="flex min-h-12 w-full items-center justify-between gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-left text-sm text-gray-800 shadow-sm transition hover:border-brand-300 hover:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-800">
                                <span class="flex min-w-0 items-center gap-3">
                                    <svg class="h-5 w-5 shrink-0 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M7 12h10m-7 6h4"/>
                                    </svg>
                                    <span class="truncate" x-text="selectionLabel"></span>
                                </span>
                                <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                                </svg>
                            </button>

                            <div x-cloak x-show="open" x-transition.origin.top
                                class="absolute left-0 right-0 z-30 mt-2 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900">
                                <div class="border-b border-gray-100 p-3 dark:border-gray-800">
                                    <label for="school-comparison-search" class="sr-only">Cari satker atau madrasah</label>
                                    <div class="relative">
                                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="11" cy="11" r="7" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/>
                                        </svg>
                                        <input id="school-comparison-search" type="search" x-model="search" placeholder="Cari nama satker..."
                                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-sm text-gray-800 outline-none placeholder:text-gray-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="`${selectedSchools.length} dipilih`"></span>
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="selectAll()" class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">Pilih semua</button>
                                            <button type="button" @click="clearSelection()" class="text-xs font-medium text-gray-500 hover:underline dark:text-gray-400">Kosongkan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="max-h-56 overflow-y-auto p-2" role="group" aria-label="Pilihan satker dan madrasah">
                                    @foreach ($schools as $school)
                                        <label x-show="{{ \Illuminate\Support\Js::from(mb_strtolower($school->nama_sekolah)) }}.includes(search.trim().toLowerCase())"
                                            class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-gray-50 dark:hover:bg-white/[0.04]">
                                            <input type="checkbox" name="sekolah_ids[]" value="{{ $school->id }}" x-model="selectedSchools"
                                                class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800">
                                            <span class="min-w-0 flex-1 truncate text-sm text-gray-700 dark:text-gray-200">{{ $school->nama_sekolah }}</span>
                                        </label>
                                    @endforeach
                                    <p x-show="search.trim() !== '' && !Array.from($el.parentElement.querySelectorAll('label')).some((label) => label.textContent.toLowerCase().includes(search.trim().toLowerCase()))"
                                        class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Satker tidak ditemukan.
                                    </p>
                                    @if ($schools->isEmpty())
                                        <p class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada satker/madrasah terdaftar.</p>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between gap-3 border-t border-gray-100 bg-gray-50 px-3 py-3 dark:border-gray-800 dark:bg-gray-800/60">
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Kosongkan pilihan untuk membandingkan semua satker.</p>
                                    <button type="button" @click="open = false"
                                        class="shrink-0 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
                                        Selesai
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $schoolCompliance->count() }} satker ditampilkan</span>
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                                Terapkan perbandingan
                            </button>
                        </div>
                    </form>
                    @if ($schoolCompliance->isEmpty())
                        <div class="flex min-h-[220px] items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400">
                            Belum ada satker/madrasah untuk ditampilkan.
                        </div>
                    @else
                        <div id="madrasah-compliance-bar" class="min-h-[260px]" role="img" aria-label="Grafik perbandingan kepatuhan antar madrasah"></div>
                    @endif
                </div>
            </div>
        </section>

        <x-common.component-card title="Pegawai Zona Merah" desc="Pegawai aktif yang belum mengunggah eviden untuk indikator Perkin yang disetujui.">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Pegawai</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">NIP / Email</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Satker / Madrasah</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($redZonePegawais as $pegawai)
                            <tr class="transition hover:bg-rose-50/50 dark:hover:bg-rose-500/[0.03]">
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $pegawai->name }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $pegawai->nip ?: $pegawai->email }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $pegawai->sekolah?->nama_sekolah ?? 'Satker belum diatur' }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        Belum upload
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                        {{ $indicatorCount > 0 ? 'Tidak ada pegawai di zona merah.' : 'Belum ada indikator Perkin disetujui untuk menentukan zona merah.' }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $indicatorCount > 0 ? 'Semua pegawai aktif sudah mengunggah minimal satu eviden.' : 'Setelah indikator disetujui, daftar pegawai yang belum mengunggah akan muncul di sini.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.ApexCharts === 'undefined') {
                return;
            }

            const donutElement = document.querySelector('#pegawai-compliance-donut');
            if (donutElement) {
                new window.ApexCharts(donutElement, {
                    chart: {
                        type: 'donut',
                        height: 320,
                        fontFamily: 'Inter, sans-serif',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 700,
                            animateGradually: { enabled: true, delay: 120 },
                        },
                        toolbar: { show: false },
                    },
                    series: @json(array_values($complianceDistribution)),
                    labels: ['Unggah 100%', 'Progres 30-70%', 'Belum Upload 0%', 'Progres lainnya'],
                    colors: ['#10b981', '#f59e0b', '#f43f5e', '#6366f1'],
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '13px',
                        itemMargin: { horizontal: 12, vertical: 8 },
                        labels: { colors: '#6b7280' },
                        markers: { width: 9, height: 9, radius: 9 },
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (value) {
                            return `${Math.round(value)}%`;
                        },
                        style: { fontSize: '12px', fontWeight: 600 },
                        dropShadow: { enabled: false },
                    },
                    stroke: { width: 4, colors: ['#ffffff'] },
                    states: {
                        hover: { filter: { type: 'lighten', value: 0.04 } },
                        active: { filter: { type: 'none' } },
                    },
                    tooltip: {
                        fillSeriesColor: false,
                        y: {
                            formatter: function (value) {
                                return `${value} pegawai`;
                            },
                        },
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '72%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        offsetY: 20,
                                        color: '#6b7280',
                                        fontSize: '13px',
                                    },
                                    value: {
                                        show: true,
                                        offsetY: -18,
                                        color: '#111827',
                                        fontSize: '28px',
                                        fontWeight: 700,
                                        formatter: function (value) {
                                            return value;
                                        },
                                    },
                                    total: {
                                        show: true,
                                        label: 'Pegawai aktif',
                                        color: '#6b7280',
                                        formatter: function () {
                                            return '{{ $activePegawaiCount }}';
                                        },
                                    },
                                },
                            },
                        },
                    },
                }).render();
            }

            const barElement = document.querySelector('#madrasah-compliance-bar');
            if (barElement) {
                const schoolNames = @json($schoolCompliance->pluck('school')->values());
                const complianceValues = @json($schoolCompliance->pluck('value')->values());
                const employeeCounts = @json($schoolCompliance->pluck('employees')->values());

                new window.ApexCharts(barElement, {
                    chart: {
                        type: 'bar',
                        height: Math.max(280, schoolNames.length * 46),
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif',
                        animations: { enabled: true, easing: 'easeinout', speed: 700 },
                        parentHeightOffset: 0,
                    },
                    series: [{ name: 'Kepatuhan', data: complianceValues }],
                    colors: ['#6366f1', '#0ea5e9', '#8b5cf6', '#14b8a6', '#f59e0b'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            distributed: true,
                            borderRadius: 7,
                            borderRadiusApplication: 'end',
                            barHeight: '52%',
                            dataLabels: { position: 'top' },
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (value) {
                            return `${Number(value).toFixed(1).replace(/\.0$/, '')}%`;
                        },
                        offsetX: 12,
                        style: { colors: ['#4b5563'], fontSize: '12px', fontWeight: 600 },
                        background: { enabled: false },
                    },
                    xaxis: {
                        categories: schoolNames,
                        min: 0,
                        max: 100,
                        tickAmount: 4,
                        labels: {
                            formatter: function (value) {
                                return `${value}%`;
                            },
                            style: { colors: '#9ca3af', fontSize: '11px' },
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: {
                        labels: {
                            maxWidth: 190,
                            style: { colors: '#4b5563', fontSize: '12px', fontWeight: 500 },
                        },
                    },
                    grid: {
                        borderColor: '#e5e7eb',
                        strokeDashArray: 4,
                        xaxis: { lines: { show: true } },
                        yaxis: { lines: { show: false } },
                        padding: { left: 8, right: 28 },
                    },
                    legend: { show: false },
                    tooltip: {
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                        y: {
                            formatter: function (value, options) {
                                const count = employeeCounts[options.dataPointIndex] ?? 0;
                                return `${value}% dari ${count} pegawai aktif`;
                            },
                        },
                    },
                    noData: {
                        text: 'Belum ada data kepatuhan',
                        style: { color: '#6b7280', fontSize: '14px' },
                    },
                }).render();
            }
        });
    </script>
@endpush
