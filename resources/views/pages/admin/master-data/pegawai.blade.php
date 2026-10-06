@extends('layouts.app')

@section('content')
    <div x-data="pegawaiData()" x-cloak>
        <x-common.page-breadcrumb pageTitle="Data Pegawai" />

        <section class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Manajemen Data Pegawai</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftar seluruh pegawai terdaftar beserta unit kerja/sekolah tempat bertugas.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="button" @click="importOpen = true"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0L8 8m4-4l4 4M5 20h14" />
                    </svg>Import Data (CSV)
                </button>
                <button type="button" @click="openCreate()"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white hover:bg-brand-600">
                    <span class="text-lg leading-none">+</span> Tambah Pegawai
                </button>
            </div>
        </section>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400">
                Periksa kembali data yang dimasukkan.
            </div>
        @endif

        <x-common.component-card title="Daftar Pegawai" desc="Cari dan filter pegawai berdasarkan sekolah atau keterisian eviden Perkin.">
            <form method="GET" action="{{ route('admin.pegawai.index') }}"
                class="mb-5 grid grid-cols-1 gap-3 lg:grid-cols-[1fr_220px_240px_auto]">
                <label class="relative">
                    <span class="sr-only">Cari Nama atau NIP Pegawai</span>
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">⌕</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIP Pegawai"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-10 pr-4 text-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white/90">
                </label>
                <select name="sekolah_id"
                    class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-700 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">Semua Sekolah</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" @selected((string) request('sekolah_id') === (string) $school->id)>
                            {{ $school->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
                <select name="status"
                    class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-700 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">Semua Status Eviden</option>
                    <option value="active" @selected(request('status') === 'active')>Sudah Unggah Eviden</option>
                    <option value="incomplete" @selected(request('status') === 'incomplete')>Belum Unggah Eviden</option>
                </select>
                <button type="submit"
                    class="h-11 rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Terapkan</button>
            </form>

            <div class="w-full overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50/75 dark:border-gray-800 dark:bg-white/[0.02]">
                        <tr>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Profil Pegawai</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sekolah / Unit Kerja</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status &amp; Eviden Perkin</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($pegawais as $pegawai)
                            <tr class="transition hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                                {{-- Profil Pegawai --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                            {{ strtoupper(substr($pegawai->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.pegawai.show', $pegawai) }}" class="block truncate font-medium text-gray-800 hover:text-brand-600 dark:text-white/90">{{ $pegawai->name }}</a>
                                            <div class="mt-0.5">
                                                @if ($pegawai->nip)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">NIP: {{ $pegawai->nip }}</span>
                                                @else
                                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                                                        Non-PNS / Honorer
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Sekolah Asal --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($pegawai->sekolah)
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                            {{ $pegawai->sekolah->nama_sekolah }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum ditentukan</span>
                                    @endif
                                </td>

                                {{-- Email --}}
                                <td class="px-5 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                    {{ $pegawai->email }}
                                </td>

                                {{-- Status & Eviden --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $pegawai->status_aktif ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                            <span class="text-xs font-medium {{ $pegawai->status_aktif ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}">
                                                {{ $pegawai->status_aktif ? 'Akun Aktif' : 'Akun Nonaktif' }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $pegawai->realisasins_count ? 'Sudah unggah eviden' : 'Belum unggah eviden' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Edit -->
                                        <a href="{{ route('admin.pegawai.edit', $pegawai) }}" title="Edit pegawai" aria-label="Edit pegawai"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-200 text-amber-600 hover:bg-amber-50 dark:border-amber-500/20 dark:text-amber-400 dark:hover:bg-amber-500/10 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 14l-4 1 1-4 7.5-7.5z" />
                                            </svg>
                                        </a>

                                        <!-- Riwayat Eviden -->
                                        <a href="{{ route('admin.pegawai.history', $pegawai) }}"
                                            title="Lihat riwayat eviden" aria-label="Lihat riwayat eviden"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 dark:border-blue-500/20 dark:text-blue-400 dark:hover:bg-blue-500/10 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>

                                        <!-- Hapus -->
                                        <form method="POST" action="{{ route('admin.pegawai.destroy', $pegawai) }}"
                                            onsubmit="return confirm('Hapus data pegawai ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus pegawai" aria-label="Hapus pegawai"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 dark:border-rose-500/20 dark:text-rose-400 dark:hover:bg-rose-500/10 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12m-9 0v10m6-10v10M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-9 0l1 13h10l1-13" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-white/5">∅</span>
                                    <h3 class="mt-3 text-sm font-semibold text-gray-800 dark:text-white/90">Belum ada data pegawai</h3>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tambahkan pegawai atau import data untuk mulai mengelola eviden.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($pegawais->hasPages())
                <div class="mt-4 flex flex-col items-center justify-between gap-3 px-1 sm:flex-row">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Menampilkan <span class="font-medium text-gray-700 dark:text-gray-300">{{ $pegawais->firstItem() }}-{{ $pegawais->lastItem() }}</span>
                        dari <span class="font-medium text-gray-700 dark:text-gray-300">{{ $pegawais->total() }}</span> pegawai
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($pegawais->onFirstPage())
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-xs text-gray-300 dark:border-gray-800 dark:text-gray-600">&lsaquo;</span>
                        @else
                            <a href="{{ $pegawais->previousPageUrl() }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">&lsaquo;</a>
                        @endif

                        @foreach ($pegawais->getUrlRange(max(1, $pegawais->currentPage() - 2), min($pegawais->lastPage(), $pegawais->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-xs font-medium transition {{ $page === $pegawais->currentPage() ? 'bg-brand-500 text-white shadow-xs' : 'border border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                {{ $page }}
                            </a>
                        @endforeach

                        @if ($pegawais->hasMorePages())
                            <a href="{{ $pegawais->nextPageUrl() }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">&rsaquo;</a>
                        @else
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-xs text-gray-300 dark:border-gray-800 dark:text-gray-600">&rsaquo;</span>
                        @endif
                    </div>
                </div>
            @endif
        </x-common.component-card>

        {{-- Modal Tambah Pegawai --}}
        <div x-show="open" class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto bg-gray-900/50 p-4" x-transition>
            <div @click.outside="close()" class="w-full max-w-xl rounded-2xl bg-white p-6 dark:bg-gray-900">
                <div class="mb-6 flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Tambah Pegawai</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lengkapi identitas dan sekolah asal pegawai.</p>
                    </div>
                    <button type="button" @click="close()" class="text-2xl text-gray-400">&times;</button>
                </div>
                <form method="POST" :action="formAction" class="space-y-5">
                    @csrf
                    <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pegawai</label>
                        <input name="name" x-model="form.name" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">NIP</label>
                            <input name="nip" x-model="form.nip" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            @error('nip')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input name="email" type="email" x-model="form.email" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Sekolah / Unit Kerja</label>
                        <select name="sekolah_id" x-model="form.sekolah_id" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            <option value="">Pilih sekolah</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->nama_sekolah }} - {{ $school->npsn }}</option>
                            @endforeach
                        </select>
                        @error('sekolah_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Password <span class="font-normal text-gray-400">(opsional saat edit)</span></label>
                        <input name="password" type="password" :required="!editing" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5 dark:border-gray-800">
                        <button type="button" @click="close()" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">Batal</button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white">Simpan Pegawai</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Import CSV --}}
        <div x-show="importOpen" class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4" x-transition>
            <div @click.outside="importOpen = false" class="w-full max-w-lg rounded-2xl bg-white p-6 dark:bg-gray-900">
                <div class="mb-5 flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Import Data Pegawai</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gunakan CSV dengan header: name,nip,email,sekolah_id.</p>
                    </div>
                    <button type="button" @click="importOpen = false" class="text-2xl text-gray-400">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.pegawai.import') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="file" name="file" accept=".csv,.txt" required class="block w-full rounded-lg border border-gray-300 p-3 text-sm dark:border-gray-700 dark:text-gray-300">
                    @error('file')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="importOpen = false" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">Batal</button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white">Import Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function pegawaiData() {
            return {
                open: @js($errors->any() && !old('file')),
                importOpen: false,
                formAction: @js(route('admin.pegawai.store')),
                form: {
                    id: null,
                    name: '',
                    nip: '',
                    email: '',
                    sekolah_id: ''
                },
                openCreate() {
                    this.formAction = @js(route('admin.pegawai.store'));
                    this.form = {
                        id: null,
                        name: '',
                        nip: '',
                        email: '',
                        sekolah_id: ''
                    };
                    this.open = true;
                },
                close() {
                    this.open = false;
                }
            };
        }
    </script>
@endpush
