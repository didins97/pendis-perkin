@extends('layouts.app')

@section('content')
    <div x-data="schoolDataModal()" x-cloak>
        <x-common.page-breadcrumb pageTitle="Data Sekolah" />

        <section class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Manajemen Data Sekolah</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola daftar madrasah dan sekolah.</p>
            </div>
            <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white hover:bg-brand-600">
                <span class="text-lg leading-none">+</span> Tambah Sekolah
            </button>
        </section>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">{{ session('success') }}</div>
        @endif

        <x-common.component-card title="Daftar Sekolah" desc="Gunakan pencarian dan filter untuk menemukan sekolah dengan cepat.">
            <form method="GET" action="{{ route('admin.schools') }}" class="flex flex-col gap-3 lg:flex-row">
                <label class="relative flex-1">
                    <span class="sr-only">Cari berdasarkan NPSN atau Nama Sekolah</span>
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">⌕</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan NPSN atau Nama Sekolah" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-10 pr-4 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white/90" />
                </label>
                <button type="submit" class="h-11 rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">Terapkan</button>
            </form>

            <div class="w-full overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                <table class="w-full min-w-[640px] text-left">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                        <tr>
                            <th class="whitespace-nowrap px-5 py-4 text-xs font-medium uppercase text-gray-500">NPSN</th>
                            <th class="min-w-56 px-5 py-4 text-xs font-medium uppercase text-gray-500">Nama Sekolah</th>
                            <th class="min-w-64 px-5 py-4 text-xs font-medium uppercase text-gray-500">Alamat</th>
                            <th class="whitespace-nowrap px-5 py-4 text-xs font-medium uppercase text-gray-500">Jumlah Pegawai</th>
                            <th class="whitespace-nowrap px-5 py-4 text-right text-xs font-medium uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($schools as $school)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $school->npsn }}</td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $school->nama_sekolah }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $school->alamat ?: 'Belum diisi' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $school->pegawai_count }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" title="Edit sekolah" aria-label="Edit sekolah" @click='openEdit(@js(['id' => $school->id, 'npsn' => $school->npsn, 'nama_sekolah' => $school->nama_sekolah, 'alamat' => $school->alamat]))' class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 dark:border-blue-500/30 dark:text-blue-400 dark:hover:bg-blue-500/10">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 14l-4 1 1-4 7.5-7.5z" /></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.schools.destroy', $school) }}" onsubmit="return confirm('Hapus data sekolah ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus sekolah" aria-label="Hapus sekolah" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50 dark:border-red-500/30 dark:text-red-400 dark:hover:bg-red-500/10">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12m-9 0v10m6-10v10M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-9 0l1 13h10l1-13" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-white/5"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4M9 9h.01M15 9h.01M9 13h.01M15 13h.01" /></svg></span>
                                        <h3 class="mt-4 text-base font-semibold text-gray-800 dark:text-white/90">Belum ada data sekolah</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan sekolah pertama untuk mulai mengelola data.</p>
                                        <button type="button" @click="openCreate()" class="mt-5 text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">+ Tambah Sekolah</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($schools->hasPages())
                <div class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 pt-5 sm:flex-row dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Menampilkan {{ $schools->firstItem() }}-{{ $schools->lastItem() }} dari {{ $schools->total() }} sekolah</p>
                    <div class="flex items-center gap-1">
                        @if ($schools->onFirstPage())
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-300 dark:border-gray-800">&lsaquo;</span>
                        @else
                            <a href="{{ $schools->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">&lsaquo;</a>
                        @endif
                        @foreach ($schools->getUrlRange(max(1, $schools->currentPage() - 2), min($schools->lastPage(), $schools->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm {{ $page === $schools->currentPage() ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300' }}">{{ $page }}</a>
                        @endforeach
                        @if ($schools->hasMorePages())
                            <a href="{{ $schools->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">&rsaquo;</a>
                        @else
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-300 dark:border-gray-800">&rsaquo;</span>
                        @endif
                    </div>
                </div>
            @endif
        </x-common.component-card>

        <div x-show="open" @keydown.escape.window="close()" class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto bg-gray-900/50 p-4" x-transition>
            <div @click.outside="close()" class="relative max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <div class="mb-6 flex items-start justify-between">
                    <div><h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="editing ? 'Edit Data Sekolah' : 'Tambah Sekolah'"></h2><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lengkapi informasi sekolah.</p></div>
                    <button type="button" @click="close()" class="text-2xl text-gray-400 hover:text-gray-700" aria-label="Tutup modal">&times;</button>
                </div>
                <form method="POST" :action="formAction" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_method" :value="editing ? 'PUT' : 'POST'">
                    <div>
                        <label for="npsn" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">NPSN <span class="text-red-500">*</span></label>
                        <input id="npsn" name="npsn" type="text" x-model="form.npsn" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                        @error('npsn')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nama_sekolah" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Sekolah <span class="text-red-500">*</span></label>
                        <input id="nama_sekolah" name="nama_sekolah" type="text" x-model="form.nama_sekolah" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                        @error('nama_sekolah')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="alamat" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                        <textarea id="alamat" name="alamat" x-model="form.alamat" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90"></textarea>
                        @error('alamat')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5 dark:border-gray-800"><button type="button" @click="close()" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300">Batal</button><button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600" x-text="editing ? 'Simpan Perubahan' : 'Simpan Sekolah'"></button></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function schoolDataModal() {
        return {
            open: false,
            editing: false,
            formAction: @js(route('admin.schools.store')),
            form: { id: null, npsn: '', nama_sekolah: '', alamat: '' },
            openCreate() {
                this.editing = false;
                this.formAction = @js(route('admin.schools.store'));
                this.form = { id: null, npsn: '', nama_sekolah: '', alamat: '' };
                this.open = true;
            },
            openEdit(school) {
                this.editing = true;
                this.formAction = '{{ url('/admin/master-data/sekolah') }}/' + school.id;
                this.form = { ...school };
                this.open = true;
            },
            close() { this.open = false; }
        };
    }
</script>
@endpush
