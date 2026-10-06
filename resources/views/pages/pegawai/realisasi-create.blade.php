@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Input Eviden Realisasi" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mx-auto max-w-4xl" x-data="realisasiEvidenData()">
        <x-common.component-card title="Unggah Eviden" desc="Pilih Tahun Anggaran yang sudah disetujui, lalu sasaran dan indikator yang sesuai.">
            <form method="POST" action="{{ route('pegawai.realisasi.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="tahun_anggaran_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Anggaran</label>
                        <select id="tahun_anggaran_id" x-model="tahunId" @change="sasaranId = ''; resetIndikators()" required class="h-11 w-full rounded-lg border border-gray-300 px-4 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Pilih tahun approved</option>
                            @foreach ($tahuns as $tahun)
                                <option value="{{ $tahun->id }}">{{ $tahun->tahun }} - Disetujui</option>
                            @endforeach
                        </select>
                        @if ($tahuns->isEmpty())
                            <p class="mt-2 text-xs text-warning-600 dark:text-warning-400">Belum ada Tahun Anggaran yang disetujui Pimpinan.</p>
                        @endif
                    </div>
                    <div>
                        <label for="sasaran_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Sasaran Kinerja</label>
                        <select id="sasaran_id" x-model="sasaranId" @change="loadIndikators" required :disabled="!tahunId" class="h-11 w-full rounded-lg border border-gray-300 px-4 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-white/5">
                            <option value="" x-text="tahunId ? 'Pilih sasaran' : 'Pilih tahun terlebih dahulu'"></option>
                            @foreach ($sasarans as $sasaran)
                                <option value="{{ $sasaran->id }}" data-tahun-id="{{ $sasaran->tahun_anggaran_id }}" x-show="String(tahunId) === '{{ $sasaran->tahun_anggaran_id }}'">{{ $sasaran->no_urut }}. {{ $sasaran->sasaran_kegiatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="master_indikator_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Indikator Kinerja</label>
                        <select id="master_indikator_id" name="master_indikator_id" required :disabled="!sasaranId" class="h-11 w-full rounded-lg border border-gray-300 px-4 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-white/5">
                            <option value="">Pilih indikator sesuai sasaran</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="catatan_guru" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Pegawai</label>
                    <textarea id="catatan_guru" name="catatan_guru" rows="4" class="w-full rounded-lg border border-gray-300 px-4 py-3 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('catatan_guru') }}</textarea>
                </div>

                <div>
                    <label for="file_eviden" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">File Eviden</label>
                    <input id="file_eviden" type="file" name="file_eviden" required accept=".pdf,.jpg,.jpeg,.png,.zip,.doc,.docx" class="block w-full rounded-lg border border-gray-300 px-4 py-3 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('pegawai.realisasi.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Batal</a>
                    <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">Simpan Eviden</button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection

@push('scripts')
<script>
    function realisasiEvidenData() {
        return {
            tahunId: '',
            sasaranId: '',
            resetIndikators() {
                document.getElementById('master_indikator_id').innerHTML = '<option value="">Pilih indikator sesuai sasaran</option>';
            },
            loadIndikators() {
                const select = document.getElementById('master_indikator_id');
                select.innerHTML = '<option value="">Memuat indikator...</option>';

                if (!this.sasaranId) {
                    select.innerHTML = '<option value="">Pilih indikator sesuai sasaran</option>';
                    return;
                }

                fetch(`{{ url('/pegawai/realisasi/sasaran') }}/${this.sasaranId}/indikator`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    select.innerHTML = '<option value="">Pilih indikator</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.label || item.indikator_kinerja;
                        select.appendChild(option);
                    });
                })
                .catch(() => {
                    select.innerHTML = '<option value="">Indikator tidak tersedia</option>';
                });
            }
        }
    }
</script>
@endpush
