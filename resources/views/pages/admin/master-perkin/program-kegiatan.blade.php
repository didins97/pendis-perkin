@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <x-common.page-breadcrumb pageTitle="Kelola Kegiatan Program" />

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.anggaran-program.index') }}" class="text-sm font-medium text-brand-600">&larr; Kembali ke Program</a>
            <h1 class="mt-3 text-2xl font-semibold text-gray-800 dark:text-white/90">
                {{ $program->kode_program }} - {{ $program->nama_program }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Total Anggaran: <span class="font-semibold text-brand-600">{{ number_format($program->kegiatans->sum('anggaran'), 2, ',', '.') }}</span>
            </p>
        </div>

        <button type="button"
            onclick="const modal = document.getElementById('createKegiatanModal'); modal.classList.remove('hidden'); modal.classList.add('flex');"
            class="rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white">
            + Tambah Kegiatan
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Kegiatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Kegiatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Anggaran</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($kegiatans as $kegiatan)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $kegiatan->kode_kegiatan }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $kegiatan->nama_kegiatan }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ number_format($kegiatan->anggaran, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex gap-2">
                                <button type="button"
                                    data-kegiatan-id="{{ $kegiatan->id }}"
                                    data-kode-kegiatan="{{ $kegiatan->kode_kegiatan }}"
                                    data-nama-kegiatan="{{ $kegiatan->nama_kegiatan }}"
                                    data-anggaran="{{ $kegiatan->anggaran }}"
                                    onclick="openEditKegiatanModal(this)"
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-700">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.anggaran-program.kegiatan.destroy', [$program, $kegiatan]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-200 px-3 py-2 text-xs text-rose-600">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada kegiatan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="createKegiatanModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 p-4">
    <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Tambah Kegiatan</h2>
            <button type="button" onclick="const modal = document.getElementById('createKegiatanModal'); modal.classList.add('hidden'); modal.classList.remove('flex');" class="text-2xl text-gray-400">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.anggaran-program.kegiatan.store', $program) }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium">Kode Kegiatan</label>
                <input type="text" name="kode_kegiatan" required class="h-11 w-full rounded-lg border px-4">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium">Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" required class="h-11 w-full rounded-lg border px-4">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium">Anggaran</label>
                <input type="number" step="0.01" min="0" name="anggaran" required class="h-11 w-full rounded-lg border px-4">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="const modal = document.getElementById('createKegiatanModal'); modal.classList.add('hidden'); modal.classList.remove('flex');" class="rounded-lg border px-4 py-2">Batal</button>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-white">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editKegiatanModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 p-4">
    <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Edit Kegiatan</h2>
            <button type="button" onclick="const modal = document.getElementById('editKegiatanModal'); modal.classList.add('hidden'); modal.classList.remove('flex');" class="text-2xl text-gray-400">&times;</button>
        </div>
        <form id="editKegiatanForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1.5 block text-sm font-medium">Kode Kegiatan</label>
                <input type="text" name="kode_kegiatan" id="editKegiatanKode" required class="h-11 w-full rounded-lg border px-4">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium">Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" id="editKegiatanNama" required class="h-11 w-full rounded-lg border px-4">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium">Anggaran</label>
                <input type="number" step="0.01" min="0" name="anggaran" id="editKegiatanAnggaran" required class="h-11 w-full rounded-lg border px-4">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="const modal = document.getElementById('editKegiatanModal'); modal.classList.add('hidden'); modal.classList.remove('flex');" class="rounded-lg border px-4 py-2">Batal</button>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditKegiatanModal(button) {
        const modal = document.getElementById('editKegiatanModal');
        const form = document.getElementById('editKegiatanForm');
        const programId = {{ $program->id }};
        const kegiatanId = button.dataset.kegiatanId;

        form.action = '{{ url('/admin/master-anggaran/program') }}/' + programId + '/kegiatan/' + kegiatanId;
        document.getElementById('editKegiatanKode').value = button.dataset.kodeKegiatan;
        document.getElementById('editKegiatanNama').value = button.dataset.namaKegiatan;
        document.getElementById('editKegiatanAnggaran').value = button.dataset.anggaran;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
</script>
@endsection
