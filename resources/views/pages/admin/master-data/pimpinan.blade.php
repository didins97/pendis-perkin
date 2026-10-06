@extends('layouts.app')

@section('content')
    <div x-data="pimpinanData()" x-cloak>
        <x-common.page-breadcrumb pageTitle="Data Pimpinan" />

        <section class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Manajemen Data Pimpinan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola akun pimpinan.</p>
            </div>
            <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white hover:bg-brand-600">
                <span class="text-lg leading-none">+</span> Tambah Pimpinan
            </button>
        </section>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">{{ session('success') }}</div>
        @endif
        @if (session('temporary_password'))
            <div class="mb-6 rounded-lg border border-warning-200 bg-warning-50 px-4 py-3 text-sm text-warning-700 dark:border-warning-500/20 dark:bg-warning-500/10 dark:text-warning-400">Password sementara: <strong>{{ session('temporary_password') }}</strong>. Sampaikan kepada pimpinan secara aman.</div>
        @endif

        <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pimpinan</p>
                <p class="mt-4 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totalPimpinans) }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pimpinan Aktif</p>
                <p class="mt-4 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($activePimpinans) }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum Terverifikasi</p>
                <p class="mt-4 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($unverifiedPimpinans) }}</p>
            </div>
        </section>

        <x-common.component-card title="Daftar Pimpinan" desc="Kelola informasi akun pimpinan.">
            <form method="GET" action="{{ route('admin.pimpinan') }}" class="mb-5 flex flex-col gap-3 sm:flex-row">
                <label class="relative flex-1">
                    <span class="sr-only">Cari nama, NIP, atau email</span>
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">⌕</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIP, atau email" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-10 pr-4 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white/90">
                </label>
                <button type="submit" class="h-11 rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Cari</button>
            </form>

            <div class="w-full overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                <table class="w-full min-w-[720px] text-left">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                        <tr>
                            <th class="px-5 py-4 text-xs font-medium uppercase text-gray-500">Nama &amp; NIP</th>
                            <th class="px-5 py-4 text-xs font-medium uppercase text-gray-500">Email</th>
                            <th class="px-5 py-4 text-xs font-medium uppercase text-gray-500">Status Akun</th>
                            <th class="px-5 py-4 text-right text-xs font-medium uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($pimpinans as $pimpinan)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">{{ strtoupper(substr($pimpinan->name, 0, 1)) }}</span>
                                        <span>
                                            <span class="block text-sm font-medium text-gray-800 dark:text-white/90">{{ $pimpinan->name }}</span>
                                            <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">NIP {{ $pimpinan->nip ?: 'Belum diisi' }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $pimpinan->email }}</td>
                                <td class="px-5 py-4">
                                    @if ($pimpinan->email_verified_at)
                                        <span class="inline-flex rounded-full bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 dark:bg-green-500/10 dark:text-green-400">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600 dark:bg-white/5 dark:text-gray-400">Belum Terverifikasi</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" title="Edit pimpinan" aria-label="Edit pimpinan" @click='openEdit(@js(['id' => $pimpinan->id, 'name' => $pimpinan->name, 'nip' => $pimpinan->nip, 'email' => $pimpinan->email]))' class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-yellow-200 text-yellow-600 hover:bg-yellow-50 dark:border-yellow-500/30 dark:text-yellow-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 14l-4 1 1-4 7.5-7.5z" /></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.pimpinan.reset-password', $pimpinan) }}" onsubmit="return confirm('Reset password pimpinan ini?')">
                                            @csrf
                                            <button type="submit" title="Reset password" aria-label="Reset password" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50 dark:border-red-500/30 dark:text-red-400">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h10a4 4 0 110 8H9m0 0l-3-3m3 3l3 3M4 7l3-3m-3 3l3 3" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center">
                                    <div class="mx-auto max-w-sm">
                                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-white/5">∅</span>
                                        <h3 class="mt-4 text-base font-semibold text-gray-800 dark:text-white/90">Belum ada pimpinan</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan akun pimpinan untuk mulai mengelola data.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pimpinans->hasPages())
                <div class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 pt-5 sm:flex-row dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Menampilkan {{ $pimpinans->firstItem() }}-{{ $pimpinans->lastItem() }} dari {{ $pimpinans->total() }} pimpinan</p>
                    {{ $pimpinans->links() }}
                </div>
            @endif
        </x-common.component-card>

        <div x-show="open" @keydown.escape.window="close()" class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto bg-gray-900/50 p-4" x-transition>
            <div @click.outside="close()" class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 dark:bg-gray-900">
                <div class="mb-6 flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="editing ? 'Edit Pimpinan' : 'Tambah Pimpinan'"></h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lengkapi informasi akun pimpinan.</p>
                    </div>
                    <button type="button" @click="close()" class="text-2xl text-gray-400" aria-label="Tutup">&times;</button>
                </div>
                <form method="POST" :action="formAction" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_method" :value="editing ? 'PUT' : 'POST'">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                            <input id="name" name="name" x-model="form.name" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="nip" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">NIP</label>
                            <input id="nip" name="nip" type="text" x-model="form.nip" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            @error('nip')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input id="email" name="email" type="email" x-model="form.email" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Password <span class="font-normal text-gray-400" x-show="editing">(opsional saat edit)</span></label>
                            <input id="password" name="password" type="password" :required="!editing" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5 dark:border-gray-800">
                        <button type="button" @click="close()" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300">Batal</button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600" x-text="editing ? 'Simpan Perubahan' : 'Simpan Pimpinan'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function pimpinanData() {
            return {
                open: @js($errors->any()),
                editing: false,
                formAction: @js(route('admin.pimpinan.store')),
                form: { id: null, name: '', nip: '', email: '' },
                openCreate() {
                    this.editing = false;
                    this.formAction = @js(route('admin.pimpinan.store'));
                    this.form = { id: null, name: '', nip: '', email: '' };
                    this.open = true;
                },
                openEdit(pimpinan) {
                    this.editing = true;
                    this.formAction = '{{ url('/admin/master-data/pimpinan') }}/' + pimpinan.id;
                    this.form = { ...pimpinan };
                    this.open = true;
                },
                close() { this.open = false; }
            };
        }
    </script>
@endpush
