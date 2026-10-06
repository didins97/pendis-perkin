@extends('layouts.app')

@section('content')
    <div x-data="tahunKinerjaModal()" x-cloak>
        <x-common.page-breadcrumb pageTitle="Master Data Kinerja" />
        <section class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Master Data Kinerja</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola tahun anggaran dan susunan Perjanjian Kinerja
                    secara berjenjang.</p>
            </div>
            <button type="button" @click="openCreate()"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white hover:bg-brand-600"><span
                    class="text-lg leading-none">+</span> Tambah Tahun</button>
        </section>
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700">
                {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}</div>
        @endif
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($tahuns as $tahun)
                <article
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tahun Anggaran</p>
                            <h2 class="mt-1 text-3xl font-bold text-gray-800 dark:text-white/90">{{ $tahun->tahun }}</h2>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $tahun->status === 'aktif' ? 'bg-success-50 text-success-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($tahun->status) }}</span>
                            @php
                                $approvalClasses = [
                                    'draft' => 'bg-gray-100 text-gray-700',
                                    'submitted' => 'bg-warning-50 text-warning-700',
                                    'approved' => 'bg-success-50 text-success-700',
                                    'rejected' => 'bg-error-50 text-error-700',
                                ];
                                $approvalLabels = [
                                    'draft' => 'Draft',
                                    'submitted' => 'Menunggu Approval',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Perlu Revisi',
                                ];
                            @endphp
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $approvalClasses[$tahun->status_approval] ?? $approvalClasses['draft'] }}">Master: {{ $approvalLabels[$tahun->status_approval] ?? 'Draft' }}</span>
                        </div>
                    </div>
                    <div class="mt-6 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $tahun->sasarans_count }} sasaran kinerja</p>
                        @if ($tahun->catatan_approval)
                            <p class="mt-3 rounded-lg bg-error-50 px-3 py-2 text-xs text-error-700 dark:bg-error-500/10 dark:text-error-400">Catatan: {{ $tahun->catatan_approval }}</p>
                        @endif
                        <div class="mt-4 flex flex-col gap-2">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.kinerja.tahun.sasaran.index', $tahun) }}" class="rounded-lg bg-brand-500 px-3 py-2 text-sm font-medium text-white hover:bg-brand-600">Lihat Sasaran</a>
                                <a href="{{ route('admin.anggaran-program.index', ['tahun_anggaran_id' => $tahun->id]) }}" class="rounded-lg border border-blue-200 px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50">Lihat Anggaran</a>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if (in_array($tahun->status_approval, ['draft', 'rejected'], true))
                                    <form method="POST" action="{{ route('admin.anggaran-program.submit', $tahun->id) }}" onsubmit="return confirm('Kirim master tahun anggaran ini ke Pimpinan?')">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-warning-500 px-3 py-2 text-sm font-semibold text-white hover:bg-warning-600">Kirim ke Pimpinan</button>
                                    </form>
                                @endif
                                <button type="button"
                                    @click="openEdit({{ Js::from(['id' => $tahun->id, 'tahun' => $tahun->tahun, 'status' => $tahun->status]) }})"
                                    class="rounded-lg border border-blue-200 px-3 py-2 text-sm text-blue-600">Edit</button>
                                <form method="POST" action="{{ route('admin.kinerja.tahun.destroy', $tahun) }}"
                                    onsubmit="return confirm('Hapus tahun dan seluruh sasaran di dalamnya?')">@csrf
                                    @method('DELETE')<button type="submit"
                                        class="rounded-lg border border-red-200 px-3 py-2 text-sm text-red-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-gray-300 p-12 text-center text-sm text-gray-500 md:col-span-2 xl:col-span-3">
                    Belum ada tahun anggaran.</div>
            @endforelse
        </div>
        <div x-show="open" @keydown.escape.window="close()"
            class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4" x-transition>
            <div @click.outside="close()" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <div class="mb-6 flex items-start justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"
                        x-text="editing ? 'Edit Tahun Anggaran' : 'Tambah Tahun Anggaran'"></h2><button type="button"
                        @click="close()" class="text-2xl text-gray-400">&times;</button>
                </div>
                <form method="POST" :action="formAction" class="space-y-5">@csrf<input type="hidden" name="_method"
                        :value="editing ? 'PUT' : 'POST'">
                    <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun
                            *</label><input name="tahun" type="text" maxlength="4" pattern="[0-9]{4}"
                            x-model="form.tahun" required
                            class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    </div>
                    <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Status
                            *</label><select name="status" x-model="form.status" required
                            class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select></div>
                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5 dark:border-gray-800"><button
                            type="button" @click="close()"
                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm">Batal</button><button
                            type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white"
                            x-text="editing ? 'Simpan Perubahan' : 'Simpan Tahun'"></button></div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function tahunKinerjaModal() {
            return {
                open: false,
                editing: false,
                formAction: @js(route('admin.kinerja.tahun.store')),
                form: {
                    tahun: '',
                    status: 'aktif'
                },
                openCreate() {
                    this.editing = false;
                    this.formAction = @js(route('admin.kinerja.tahun.store'));
                    this.form = {
                        tahun: '',
                        status: 'aktif'
                    };
                    this.open = true
                },
                openEdit(tahun) {
                    this.editing = true;
                    this.formAction = @js(route('admin.kinerja.tahun.update', ['tahun' => '__TAHUN__']))
                        .replace('__TAHUN__', tahun.id);
                    this.form = {
                        tahun: String(tahun.tahun || ''),
                        status: tahun.status || 'aktif'
                    };
                    this.open = true;
                },
                close() {
                    this.open = false
                }
            }
        }
    </script>
@endpush
