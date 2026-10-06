@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-common.page-breadcrumb pageTitle="Master Anggaran & Program" />

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Master Anggaran & Program</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data program, kegiatan, dan nominal anggaran per tahun.</p>
                </div>
                <button type="button" onclick="const modal = document.getElementById('createProgramModal'); modal.classList.remove('hidden'); modal.classList.add('flex');" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">
                    Tambah Anggaran
                </button>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tahun</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Kode Program</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Program</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Jumlah Kegiatan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Total Anggaran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($programs as $program)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $program->tahunAnggaran?->tahun ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $program->kode_program }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $program->nama_program }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $program->kegiatans->count() }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ number_format((float) $program->total_anggaran, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            data-program-id="{{ $program->id }}"
                                            data-kode-program="{{ $program->kode_program }}"
                                            data-nama-program="{{ $program->nama_program }}"
                                            data-tahun-anggaran-id="{{ $program->tahun_anggaran_id }}"
                                            onclick="openEditProgramModal(this)"
                                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                            Edit
                                        </button>
                                        <a href="{{ route('admin.anggaran-program.program.show', $program) }}" class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-50">
                                            Kelola
                                        </a>
                                        <form method="POST" action="{{ route('admin.anggaran-program.destroy', $program) }}" onsubmit="return confirm('Hapus program ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data program.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $programs->links() }}
            </div>

            <div class="mt-4 rounded-xl border border-gray-100 bg-gray-50 p-3 text-sm">
                <span class="font-semibold">Grand Total:</span>
                {{ number_format((float) $grandTotal, 2, ',', '.') }}
            </div>
        </section>
    </div>

    <div id="createProgramModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Tambah Program</h2>
                <button type="button" onclick="const modal = document.getElementById('createProgramModal'); modal.classList.add('hidden'); modal.classList.remove('flex');" class="text-2xl text-gray-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.anggaran-program.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Tahun Anggaran</label>
                    <select name="tahun_anggaran_id" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="">Pilih Tahun</option>
                        @foreach ($tahuns as $tahun)
                            <option value="{{ $tahun->id }}">{{ $tahun->tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kode Program</label>
                    <input type="text" name="kode_program" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Program</label>
                    <input type="text" name="nama_program" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="const modal = document.getElementById('createProgramModal'); modal.classList.add('hidden'); modal.classList.remove('flex');" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">Batal</button>
                    <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editProgramModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Edit Program</h2>
                <button type="button" onclick="const modal = document.getElementById('editProgramModal'); modal.classList.add('hidden'); modal.classList.remove('flex');" class="text-2xl text-gray-400">&times;</button>
            </div>
            <form id="editProgramForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Tahun Anggaran</label>
                    <select name="tahun_anggaran_id" id="editProgramTahun" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        @foreach ($tahuns as $tahun)
                            <option value="{{ $tahun->id }}">{{ $tahun->tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kode Program</label>
                    <input type="text" name="kode_program" id="editProgramKode" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Program</label>
                    <input type="text" name="nama_program" id="editProgramNama" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="const modal = document.getElementById('editProgramModal'); modal.classList.add('hidden'); modal.classList.remove('flex');" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">Batal</button>
                    <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditProgramModal(button) {
            const modal = document.getElementById('editProgramModal');
            const form = document.getElementById('editProgramForm');
            const programId = button.dataset.programId;
            const tahunAnggaranId = button.dataset.tahunAnggaranId;
            const kodeProgram = button.dataset.kodeProgram;
            const namaProgram = button.dataset.namaProgram;

            form.action = '{{ url('/admin/master-anggaran/program') }}/' + programId;
            document.getElementById('editProgramTahun').value = tahunAnggaranId;
            document.getElementById('editProgramKode').value = kodeProgram;
            document.getElementById('editProgramNama').value = namaProgram;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    </script>
@endsection
