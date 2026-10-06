@extends('layouts.app')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')
<div class="space-y-6">
    <section class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-brand-600 dark:text-brand-400">
                <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                <span>Perencanaan & Kinerja</span>
            </div>
            <h1 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">Dashboard Admin / Perencanaan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Monitoring tahun anggaran {{ $tahun?->tahun ?? 'belum tersedia' }} · Master Pagu & Perkin</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label for="tahun_anggaran_id" class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Tahun</label>
                    <select id="tahun_anggaran_id" name="tahun_anggaran_id" onchange="this.form.submit()" class="h-11 rounded-xl border border-gray-200 bg-white px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        @forelse($tahuns as $optionTahun)
                            <option value="{{ $optionTahun->id }}" @selected($tahun?->id === $optionTahun->id)>{{ $optionTahun->tahun }}</option>
                        @empty
                            <option value="">Belum ada tahun anggaran</option>
                        @endforelse
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="sekolah_id" class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Sekolah</label>
                    <select id="sekolah_id" name="sekolah_id" onchange="this.form.submit()" class="h-11 rounded-xl border border-gray-200 bg-white px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Semua Satker</option>
                        @foreach($sekolahs as $sekolah)
                            <option value="{{ $sekolah->id }}" @selected((string) $sekolahId === (string) $sekolah->id)>{{ $sekolah->nama_sekolah }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="flex gap-2">
                <button class="rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">+ Tambah Pagu</button>
                <button class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-100">+ Input Master Perkin</button>
                <button class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-100">Export Rekap</button>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach($metrics as $metric)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        @switch($metric['icon'])
                            @case('wallet')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18v10H3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 14h4"/></svg>
                                @break
                            @case('users')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20a5 5 0 0 0-10 0M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                                @break
                            @case('shield')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l8 4v5c0 5-3 8-8 10-5-2-8-5-8-10V7z"/></svg>
                                @break
                            @default
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 0"/></svg>
                        @endswitch
                    </span>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $metric['tone'] == 'brand' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : ($metric['tone'] == 'success' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : ($metric['tone'] == 'warning' ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400')) }}">
                        @if($metric['label'] == 'Kepatuhan Eviden')
                            Verifikasi
                        @elseif($metric['label'] == 'Status Approval Master Perkin')
                            Master
                        @else
                            Live
                        @endif
                    </span>
                </div>
                <div class="mt-6">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $metric['label'] }}</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $metric['value'] }}</div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $metric['sub'] }}</div>
                </div>
            </div>
        @endforeach
    </section>

    <section class="grid grid-cols-12 gap-4">
        <div class="col-span-12 xl:col-span-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Alokasi Pagu Program</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Komposisi anggaran per program utama</p>
                    </div>
                    <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ $tahun?->tahun ?? '-' }}</span>
                </div>
                <div id="budget-donut" class="min-h-[260px]"></div>
            </div>
        </div>

        <div class="col-span-12 xl:col-span-7">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kepatuhan Pengisian Perkin</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Per sekolah/satker</p>
                    </div>
                    <button class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-100">Detail</button>
                </div>
                <div id="compliance-bar" class="min-h-[260px]"></div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const donut = document.querySelector('#budget-donut');
        const bar = document.querySelector('#compliance-bar');

        if (donut && typeof ApexCharts !== 'undefined') {
            const donutSeries = @json($programs->map(fn($program) => round($program['anggaran'] / 1000000, 2))->values());
            new ApexCharts(donut, {
                chart: { type: 'donut', height: 260, fontFamily: 'Inter, sans-serif' },
                series: donutSeries,
                labels: @json($programs->pluck('name')->values()),
                colors: ['#5B8DEF', '#12B886', '#F59F00', '#845EF7'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '75%' } } },
                dataLabels: { enabled: true },
                tooltip: { y: { formatter: function(val) { return val + ' M'; } } },
            }).render();
        }

        if (bar && typeof ApexCharts !== 'undefined') {
            const complianceSeries = @json($schoolCompliance->pluck('value')->values());
            const labels = @json($schoolCompliance->pluck('school')->values());
            new ApexCharts(bar, {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: [{ name: 'Kepatuhan', data: complianceSeries }],
                xaxis: { categories: labels },
                yaxis: { max: 100 },
                colors: ['#5B8DEF'],
                plotOptions: { bar: { horizontal: false, columnWidth: '50%' } },
                grid: { borderColor: '#E5E7EB' },
                dataLabels: { enabled: true },
                tooltip: { theme: 'light' },
            }).render();
        }
    });
</script>
@endpush
