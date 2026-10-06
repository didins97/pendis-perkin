@extends('layouts.app')

@section('content')
    <div x-data="sasaranKinerjaPage(@js($nextNoUrut ?? 1))" x-cloak>
        <x-common.page-breadcrumb pageTitle="Sasaran Kinerja" />
        <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div><a href="{{ route('admin.kinerja.tahun.index') }}" class="text-sm font-medium text-brand-600">&larr; Kembali
                    ke Tahun Anggaran</a>
                <h1 class="mt-3 text-2xl font-semibold text-gray-800 dark:text-white/90">Sasaran Kinerja {{ $tahun->tahun }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola sasaran dan indikator dalam tahun anggaran
                    terpilih.</p>
            </div><button type="button" @click="openCreate()"
                class="rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white">+ Tambah Sasaran</button>
        </div>
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700">
                {{ session('success') }}</div>
        @endif
        <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50 dark:border-gray-800 dark:bg-white/[0.02]">
                        <th
                            class="w-16 px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            No.</th>
                        <th
                            class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Sasaran Kinerja</th>
                        <th
                            class="w-44 px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Indikator</th>
                        <th
                            class="w-72 px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                    @forelse($tahun->sasarans as $sasaran)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition">
                            <td class="px-5 py-4 text-center text-sm font-semibold text-gray-500 dark:text-gray-400">
                                {{ $sasaran->no_urut ?: '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white leading-relaxed">
                                {{ $sasaran->sasaran_kegiatan }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                                    {{ $sasaran->indikators_count }} Indikator
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.kinerja.sasaran.indikator.index', $sasaran) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-2.5 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        Kelola
                                    </a>

                                    <button type="button"
                                        @click="openEdit({{ Js::from(['id' => $sasaran->id, 'no_urut' => $sasaran->no_urut, 'sasaran_kegiatan' => $sasaran->sasaran_kegiatan]) }})"
                                        class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>

                                    <form method="POST"
                                        action="{{ route('admin.kinerja.tahun.sasaran.destroy', [$tahun, $sasaran]) }}"
                                        onsubmit="return confirm('Hapus sasaran dan seluruh indikatornya?')"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:border-rose-900/40 dark:text-rose-400 dark:hover:bg-rose-500/10 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                                Belum ada sasaran untuk tahun ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div x-show="open" @keydown.escape.window="close()"
            class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4">
            <div @click.outside="close()" class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <div class="mb-6 flex justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"
                        x-text="editing?'Edit Sasaran':'Tambah Sasaran'"></h2><button type="button" @click="close()"
                        class="text-2xl text-gray-400">&times;</button>
                </div>
                <form method="POST" :action="formAction" class="space-y-5">@csrf<input type="hidden" name="_method"
                        :value="editing ? 'PUT' : 'POST'">
                    <div><label class="mb-1.5 block text-sm">Nomor Urut *</label><input name="no_urut" type="number"
                            min="1" x-model="form.no_urut" required
                            class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    </div>
                    <div><label class="mb-1.5 block text-sm">Sasaran Kegiatan *</label>
                        <textarea name="sasaran_kegiatan" x-model="form.sasaran_kegiatan" required rows="4"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5"><button type="button"
                            @click="close()"
                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm">Batal</button><button
                            type="submit"
                            class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white">Simpan</button></div>
                </form>
            </div>
        </div>
        <div x-show="indicatorOpen" @keydown.escape.window="closeIndicator()"
            class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4">
            <div @click.outside="closeIndicator()"
                class="w-full max-w-3xl rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <div class="mb-5 flex justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Indikator Kinerja</h2>
                        <p class="mt-1 text-sm text-gray-500" x-text="selectedSasaran.sasaran_kegiatan"></p>
                    </div><button type="button" @click="closeIndicator()"
                        class="text-2xl text-gray-400">&times;</button>
                </div>
                <div x-show="indicatorError" x-text="indicatorError" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                <div x-show="indicatorLoading" class="mb-5 py-5 text-center text-sm text-gray-500">Memuat indikator...</div>
                <div x-show="!indicatorLoading" class="mb-5 space-y-2"><template x-for="item in indicators" :key="item.id">
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700"><template
                                x-if="editingIndicator !== item.id">
                                <div class="flex items-center gap-3"><span class="w-12 text-sm font-semibold"
                                        x-text="item.kode_sub || '-'"></span><span class="flex-1 text-sm"
                                        x-text="item.indikator_kinerja"></span><span class="text-sm"
                                        x-text="item.target_default + ' ' + (item.satuan || '')"></span><button
                                        type="button" @click="startIndicatorEdit(item)"
                                        class="text-sm text-blue-600">Edit</button><button type="button"
                                        @click="removeIndicator(item.id)" class="text-sm text-red-600">Hapus</button>
                                </div>
                            </template><template x-if="editingIndicator === item.id">
                                <div class="grid gap-2 sm:grid-cols-[80px_1fr_120px_100px_auto]"><input
                                        x-model="editForm.kode_sub" class="rounded border px-2 py-2 text-sm"><input
                                        x-model="editForm.indikator_kinerja"
                                        class="rounded border px-2 py-2 text-sm"><input x-model="editForm.target_default"
                                        class="rounded border px-2 py-2 text-sm"><input x-model="editForm.satuan"
                                        class="rounded border px-2 py-2 text-sm"><button type="button"
                                        @click="saveIndicator()" class="text-sm text-brand-600">Simpan</button><button type="button"
                                        @click="editingIndicator = null" class="text-sm text-gray-500">Batal</button></div>
                            </template></div>
                    </template>
                    <p x-show="!indicators.length" class="py-5 text-center text-sm text-gray-500">Belum ada indikator.</p>
                </div>
                <form @submit.prevent="saveIndicator()"
                    class="grid gap-2 border-t border-gray-100 pt-5 sm:grid-cols-[80px_1fr_120px_100px_auto]"><input
                        x-model="newIndicator.kode_sub" placeholder="Kode"
                        class="rounded border px-2 py-2 text-sm"><input x-model="newIndicator.indikator_kinerja" required
                        placeholder="Indikator kinerja" class="rounded border px-2 py-2 text-sm"><input
                        x-model="newIndicator.target_default" required placeholder="Target"
                        class="rounded border px-2 py-2 text-sm"><input x-model="newIndicator.satuan"
                        placeholder="Satuan" class="rounded border px-2 py-2 text-sm"><button type="submit"
                        class="rounded-lg bg-brand-500 px-3 py-2 text-sm text-white">Tambah</button></form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function sasaranKinerjaPage(nextNoUrut = 1) {
            return {
                open: false,
                editing: false,
                indicatorOpen: false,
                editingIndicator: null,
                indicatorLoading: false,
                indicatorSaving: false,
                indicatorError: '',
                formAction: '',
                selectedSasaran: {},
                indicators: [],
                newIndicator: {
                    kode_sub: '',
                    indikator_kinerja: '',
                    target_default: '',
                    satuan: ''
                },
                editForm: {},
                nextNoUrut,
                openCreate() {
                    this.editing = false;
                    this.formAction = @js(route('admin.kinerja.tahun.sasaran.store', ['tahun' => $tahun]));
                    this.form = {
                        no_urut: this.nextNoUrut,
                        sasaran_kegiatan: ''
                    };
                    this.open = true
                },
                openEdit(item) {
                    this.editing = true;
                    this.formAction = @js(route('admin.kinerja.tahun.sasaran.update', ['tahun' => $tahun, 'sasaran' => '__SASARAN__']))
                        .replace('__SASARAN__', item.id);
                    this.form = {
                        no_urut: item.no_urut || '',
                        sasaran_kegiatan: item.sasaran_kegiatan || ''
                    };
                    this.open = true;
                },
                close() {
                    this.open = false
                },
                openIndicator(item) {
                    this.selectedSasaran = item;
                    this.indicators = [];
                    this.indicatorError = '';
                    this.indicatorOpen = true;
                    this.loadIndicators()
                },
                closeIndicator() {
                    this.indicatorOpen = false
                },
                async loadIndicators() {
                    this.indicatorLoading = true;
                    this.indicatorError = '';
                    try {
                        const response = await fetch('{{ url('/admin/master-data/kinerja/sasaran') }}/' + this
                            .selectedSasaran.id + '/indikator/data', {
                                headers: { 'Accept': 'application/json' }
                            });
                        if (!response.ok) throw new Error('Indikator gagal dimuat.');
                        this.indicators = await response.json();
                    } catch (error) {
                        this.indicatorError = error.message || 'Terjadi kesalahan saat memuat indikator.';
                    } finally {
                        this.indicatorLoading = false;
                    }
                },
                startIndicatorEdit(item) {
                    this.editingIndicator = item.id;
                    this.editForm = {
                        ...item
                    }
                },
                async saveIndicator() {
                    if (this.indicatorSaving) return;
                    const editing = this.editingIndicator;
                    const payload = editing ? this.editForm : this.newIndicator;
                    if (!payload.indikator_kinerja || !payload.target_default) {
                        this.indicatorError = 'Indikator dan target wajib diisi.';
                        return;
                    }
                    this.indicatorSaving = true;
                    this.indicatorError = '';
                    const url = '{{ url('/admin/master-data/kinerja/sasaran') }}/' + this.selectedSasaran.id +
                        '/indikator' + (editing ? '/' + editing : '');
                    try {
                        const response = await fetch(url, {
                            method: editing ? 'PUT' : 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(payload)
                        });
                        if (!response.ok) {
                            const data = await response.json().catch(() => ({}));
                            throw new Error(data.message || 'Indikator gagal disimpan.');
                        }
                        this.editingIndicator = null;
                        this.newIndicator = {
                            kode_sub: '',
                            indikator_kinerja: '',
                            target_default: '',
                            satuan: ''
                        };
                        await this.loadIndicators();
                    } catch (error) {
                        this.indicatorError = error.message;
                    } finally {
                        this.indicatorSaving = false;
                    }
                },
                async removeIndicator(id) {
                    if (!confirm('Hapus indikator ini?')) return;
                    this.indicatorError = '';
                    try {
                        const response = await fetch('{{ url('/admin/master-data/kinerja/sasaran') }}/' + this.selectedSasaran.id +
                            '/indikator/' + id, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                }
                            });
                        if (!response.ok) throw new Error('Indikator gagal dihapus.');
                        await this.loadIndicators();
                    } catch (error) {
                        this.indicatorError = error.message;
                    }
                }
            }
        }
    </script>
@endpush
