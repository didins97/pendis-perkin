@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Pegawai" />

    <section class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Edit Data Pegawai</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perbarui akun, profil kepegawaian, dan dokumen {{ $pegawai->name }}.</p>
        </div>
        <a href="{{ route('admin.pegawai.show', $pegawai) }}"
            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Kembali ke Detail</a>
    </section>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            Periksa kembali data yang dimasukkan.
        </div>
    @endif

    @php($profil = $pegawai->profilPegawai)

    <form method="POST" action="{{ route('admin.pegawai.update', $pegawai) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <x-common.component-card title="Informasi Akun" desc="Informasi login dan unit kerja pegawai.">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pegawai</label>
                    <input id="name" name="name" value="{{ old('name', $pegawai->name) }}" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="nip" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">NIP</label>
                    <input id="nip" name="nip" value="{{ old('nip', $pegawai->nip) }}" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    @error('nip')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $pegawai->email) }}" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="nomor_wa" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor WhatsApp</label>
                    <input id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa', $pegawai->nomor_wa) }}" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    @error('nomor_wa')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="sekolah_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Sekolah / Unit Kerja</label>
                    <select id="sekolah_id" name="sekolah_id" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                        <option value="">Pilih sekolah</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}" @selected((string) old('sekolah_id', $pegawai->sekolah_id) === (string) $school->id)>{{ $school->nama_sekolah }} - {{ $school->npsn }}</option>
                        @endforeach
                    </select>
                    @error('sekolah_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="status_aktif" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Akun</label>
                    <select id="status_aktif" name="status_aktif" required class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                        <option value="1" @selected((string) old('status_aktif', (int) $pegawai->status_aktif) === '1')>Aktif</option>
                        <option value="0" @selected((string) old('status_aktif', (int) $pegawai->status_aktif) === '0')>Nonaktif</option>
                    </select>
                    @error('status_aktif')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru <span class="font-normal text-gray-400">(kosongkan jika tidak diubah)</span></label>
                    <input id="password" type="password" name="password" autocomplete="new-password" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </x-common.component-card>

        <x-common.component-card title="Profil Kepegawaian" desc="Data pangkat, status, jabatan, dan penugasan.">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="nuptk" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">NUPTK</label>
                    <input id="nuptk" name="nuptk" value="{{ old('nuptk', $profil?->nuptk) }}" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    @error('nuptk')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="nrg" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">NRG</label>
                    <input id="nrg" name="nrg" value="{{ old('nrg', $profil?->nrg) }}" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    @error('nrg')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="pangkat_golongan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Pangkat / Golongan</label>
                    <input id="pangkat_golongan" name="pangkat_golongan" value="{{ old('pangkat_golongan', $profil?->pangkat_golongan) }}" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    @error('pangkat_golongan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="status_kepegawaian" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Kepegawaian</label>
                    <select id="status_kepegawaian" name="status_kepegawaian" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                        <option value="">Pilih status</option>
                        @foreach (['PNS', 'PPPK', 'Non-ASN'] as $status)
                            <option value="{{ $status }}" @selected(old('status_kepegawaian', $profil?->status_kepegawaian) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status_kepegawaian')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="jabatan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</label>
                    <input id="jabatan" name="jabatan" value="{{ old('jabatan', $profil?->jabatan) }}" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    @error('jabatan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="tugas_tambahan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tugas Tambahan</label>
                    <textarea id="tugas_tambahan" name="tugas_tambahan" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">{{ old('tugas_tambahan', $profil?->tugas_tambahan) }}</textarea>
                    @error('tugas_tambahan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </x-common.component-card>

        <x-common.component-card title="Berkas Pendukung" desc="Format PDF, JPG, JPEG, atau PNG, maksimal 5 MB per berkas.">
            <div class="grid gap-5 md:grid-cols-3">
                @foreach ([
                    'berkas_sk_pangkat' => 'SK Pangkat',
                    'berkas_sk_mengajar' => 'SK Mengajar',
                    'berkas_serdik' => 'Sertifikat Pendidik',
                ] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
                        <input id="{{ $field }}" type="file" name="{{ $field }}" accept=".pdf,.jpg,.jpeg,.png" class="block w-full rounded-lg border border-gray-300 p-3 text-sm dark:border-gray-700 dark:text-gray-300">
                        @if ($profil?->$field)
                            @php($documentKey = ['berkas_sk_pangkat' => 'sk-pangkat', 'berkas_sk_mengajar' => 'sk-mengajar', 'berkas_serdik' => 'serdik'][$field])
                            <a href="{{ route('admin.pegawai.document', [$pegawai, $documentKey]) }}" class="mt-2 inline-block text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">Unduh berkas saat ini</a>
                        @endif
                        @error($field)<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </x-common.component-card>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.pegawai.show', $pegawai) }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">Batal</a>
            <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Simpan Perubahan</button>
        </div>
    </form>
@endsection
