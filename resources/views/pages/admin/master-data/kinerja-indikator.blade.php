@extends('layouts.app')

@section('content')
    <div x-data="indikatorPage(@js($usedCodes ?? []), @js($nextKodeSub ?? 'a'))" x-cloak>
        <!-- Breadcrumb -->
        <x-common.page-breadcrumb pageTitle="Indikator Kinerja" />

        <!-- Header Section -->
        <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <a href="{{ $sasaran->tahunAnggaran ? route('admin.kinerja.tahun.sasaran.index', $sasaran->tahunAnggaran) : route('admin.kinerja.tahun.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Sasaran
                </a>
                <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                    Sasaran {{ $sasaran->no_urut ?: '-' }}
                </p>
                <h1 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">
                    {{ $sasaran->sasaran_kegiatan }}
                </h1>
            </div>

            <button type="button" @click="openCreate()"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Indikator
            </button>
        </div>

        <!-- Alert Notification -->
        @if (session('success'))
            <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 dark:border-emerald-800/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 dark:border-rose-800/30 dark:bg-rose-500/10 dark:text-rose-400">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Table Container -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-xs">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/50 dark:border-gray-800 dark:bg-white/[0.02]">
                            <th class="w-24 px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kode</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Indikator Kinerja</th>
                            <th class="w-48 px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Target</th>
                            <th class="w-56 px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                        @forelse ($sasaran->indikators as $indikator)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition">
                                <td class="px-5 py-4 text-center text-sm">
                                    <span class="inline-block rounded-md bg-gray-100 px-2.5 py-1 font-mono text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        {{ $indikator->kode_sub ?: '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white leading-relaxed">
                                    {{ $indikator->indikator_kinerja }}
                                </td>
                                <td class="px-5 py-4 text-center text-sm font-medium text-gray-600 dark:text-gray-300">
                                    <span class="inline-flex items-center gap-1">
                                        {{ $indikator->target_default }}
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $indikator->satuan }}</span>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <button type="button"
                                            @click="openEdit({{ Js::from(['id' => $indikator->id, 'kode_sub' => $indikator->kode_sub, 'indikator_kinerja' => $indikator->indikator_kinerja, 'target_default' => $indikator->target_default, 'satuan' => $indikator->satuan]) }})"
                                                title="Edit Indikator"
                                                class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>

                                        <form method="POST"
                                              action="{{ route('admin.kinerja.sasaran.indikator.destroy', [$sasaran, $indikator]) }}"
                                              onsubmit="return confirm('Hapus indikator ini?')"
                                              class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus Indikator"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:border-rose-900/40 dark:text-rose-400 dark:hover:bg-rose-500/10 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                    Belum ada indikator untuk sasaran ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL SIMPAN & EDIT INDIKATOR -->
        <div x-show="openModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="closeModal()"
             class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">

            <div @click.outside="closeModal()"
                 class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800">

                <div class="mb-5 flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white"
                        x-text="isEdit ? 'Edit Indikator Kinerja' : 'Tambah Indikator Kinerja'"></h2>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">&times;</button>
                </div>

                <form method="POST" :action="formAction" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Kode Sub</label>
                            <input name="kode_sub" type="text" x-model="form.kode_sub" @input="form.kode_sub = form.kode_sub.toLowerCase().replace(/[^a-z]/g, '').slice(0, 1)" placeholder="Contoh: a"
                                   class="h-10 w-full rounded-xl border border-gray-200 px-3.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Target *</label>
                            <input name="target_default" type="text" required x-model="form.target_default" placeholder="Contoh: 100"
                                   class="h-10 w-full rounded-xl border border-gray-200 px-3.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Indikator Kinerja *</label>
                        <textarea name="indikator_kinerja" x-model="form.indikator_kinerja" required rows="3" placeholder="Tuliskan deskripsi indikator..."
                                  class="w-full rounded-xl border border-gray-200 p-3.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white"></textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                        <input name="satuan" type="text" x-model="form.satuan" placeholder="Contoh: %, Dokumen, Point"
                               class="h-10 w-full rounded-xl border border-gray-200 px-3.5 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white">
                    </div>

                    <div class="flex justify-end gap-2.5 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <button type="button" @click="closeModal()"
                                class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                            Batal
                        </button>
                        <button type="submit"
                                class="rounded-xl bg-brand-500 px-4 py-2 text-xs font-medium text-white hover:bg-brand-600 transition">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function indikatorPage(usedCodes = [], nextKodeSub = 'a') {
            return {
                openModal: false,
                isEdit: false,
                formAction: '',
                usedCodes,
                nextKodeSub,
                form: {
                    kode_sub: '',
                    indikator_kinerja: '',
                    target_default: '',
                    satuan: ''
                },

                openCreate() {
                    this.isEdit = false;
                    this.formAction = @js(route('admin.kinerja.sasaran.indikator.store', ['sasaran' => $sasaran]));
                    this.form = {
                        kode_sub: this.nextKodeSub,
                        indikator_kinerja: '',
                        target_default: '',
                        satuan: ''
                    };
                    this.openModal = true;
                },

                openEdit(item) {
                    this.isEdit = true;
                    this.formAction = @js(route('admin.kinerja.sasaran.indikator.update', ['sasaran' => $sasaran, 'indikator' => '__INDIKATOR__']))
                        .replace('__INDIKATOR__', item.id);
                    this.form = {
                        kode_sub: item.kode_sub || this.nextKodeSub,
                        indikator_kinerja: item.indikator_kinerja || '',
                        target_default: item.target_default || '',
                        satuan: item.satuan || ''
                    };
                    this.openModal = true;
                },

                closeModal() {
                    this.openModal = false;
                }
            }
        }
    </script>
@endpush
