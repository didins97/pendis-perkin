@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail Pegawai" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700">
            {{ session('success') }}
        </div>
    @endif

    @php($profil = $pegawai->profilPegawai)

    <section class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $pegawai->name }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Detail data akun dan profil kepegawaian.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.pegawai.index') }}"
                class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">Kembali</a>
            <a href="{{ route('admin.pegawai.edit', $pegawai) }}"
                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Edit Pegawai</a>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-common.component-card title="Informasi Akun" desc="Data akun dan unit kerja pegawai.">
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    'Nama' => $pegawai->name,
                    'NIP' => $pegawai->nip,
                    'Email' => $pegawai->email,
                    'Nomor WhatsApp' => $pegawai->nomor_wa,
                    'Sekolah / Unit Kerja' => $pegawai->sekolah?->nama_sekolah,
                    'Status Akun' => $pegawai->status_aktif ? 'Aktif' : 'Nonaktif',
                ] as $label => $value)
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $label }}</dt>
                        <dd class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-common.component-card>

        <x-common.component-card title="Profil Kepegawaian" desc="Informasi kepangkatan dan penugasan.">
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    'NUPTK' => $profil?->nuptk,
                    'NRG' => $profil?->nrg,
                    'Pangkat / Golongan' => $profil?->pangkat_golongan,
                    'Status Kepegawaian' => $profil?->status_kepegawaian,
                    'Jabatan' => $profil?->jabatan,
                    'Tugas Tambahan' => $profil?->tugas_tambahan,
                ] as $label => $value)
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $label }}</dt>
                        <dd class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-common.component-card>

        <x-common.component-card title="Berkas Pegawai" desc="Dokumen pendukung yang tersimpan pada profil pegawai.">
            <ul class="space-y-4">
                @foreach ([
                    'SK Pangkat' => ['sk-pangkat', $profil?->berkas_sk_pangkat],
                    'SK Mengajar' => ['sk-mengajar', $profil?->berkas_sk_mengajar],
                    'Sertifikat Pendidik' => ['serdik', $profil?->berkas_serdik],
                ] as $label => $document)
                    @php([$documentKey, $path] = $document)
                    <li class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        @if ($path)
                            <a href="{{ route('admin.pegawai.document', [$pegawai, $documentKey]) }}"
                                class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">Unduh berkas</a>
                        @else
                            <span class="text-sm text-gray-400">Belum diunggah</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-common.component-card>

        <x-common.component-card title="Ringkasan Eviden" desc="Jumlah eviden Perkin yang telah dikirim.">
            <p class="text-3xl font-semibold text-gray-800 dark:text-white/90">{{ $pegawai->realisasins->count() }}</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Eviden terdaftar</p>
            <a href="{{ route('admin.pegawai.history', $pegawai) }}" class="mt-4 inline-flex text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">Lihat riwayat eviden</a>
        </x-common.component-card>
    </div>
@endsection
