@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative min-h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
        <div class="absolute inset-y-0 right-0 hidden w-1/2 overflow-hidden bg-brand-950 lg:block">
            <!-- GAMBAR BACKGROUND (Diberi animasi slow-zoom seolah video) -->
            <img src="{{ asset('images/assets/kantor.webp') }}" alt="Background MODIS PENDIS"
                class="absolute inset-0 h-full w-full object-cover scale-105 animate-[kenburns_20s_ease-in-out_infinite_alternate]">

            <!-- OVERLAY SINEMATIK (Gradasi warna & blur halus) -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/70 to-brand-950/50 backdrop-blur-[1px]">
            </div>

            <div class="relative flex h-full items-center justify-center px-16">
                <div class="max-w-lg text-center text-white">
                    <div
                        class="mx-auto mb-8 flex size-28 items-center justify-center rounded-3xl border border-white/50 bg-white p-2 shadow-2xl">
                        <img src="{{ asset('images/logo/logokemenag.webp') }}" alt="Logo MODIS PENDIS"
                            class="h-full w-full object-contain">
                    </div>
                    <p class="mb-3 text-sm font-semibold uppercase tracking-[0.2em] text-white/80">Kementerian Agama Kab.
                        Pulau Morotai</p>
                    <h2 class="text-4xl font-bold tracking-tight">MODIS PENDIS</h2>
                    <p class="mt-4 text-lg leading-8 text-white/75">Sistem Informasi Perangkat Belajar untuk proses
                        verifikasi dan pengesahan yang terintegrasi.</p>
                    <div class="mt-10 flex flex-wrap justify-center gap-3 text-sm text-white/80">
                        <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">Pegawai</span>
                        <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">Pimpinan</span>
                        <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">Admin Kemenag</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative flex min-h-screen w-full items-center px-6 py-10 sm:px-10 lg:w-1/2 lg:px-16 xl:px-24">
            <div class="mx-auto w-full max-w-md">

                <div class="mb-7">
                    <div class="mb-5 flex items-center gap-3 lg:hidden">
                        <div
                            class="flex size-11 items-center justify-center rounded-xl bg-brand-500 text-white shadow-lg shadow-brand-900/15">
                            <svg class="size-6" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true">
                                <path d="M24 5L39 11V22C39 31.5 32.6 39.3 24 43C15.4 39.3 9 31.5 9 22V11L24 5Z"
                                    stroke="currentColor" stroke-width="3" />
                                <path d="M16 23.5L21.5 29L32.5 18" stroke="currentColor" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-gray-800 dark:text-white">MODIS PENDIS</span>
                    </div>
                    <p class="mb-2 text-sm font-semibold uppercase tracking-[0.16em] text-brand-500 dark:text-brand-400">
                        Portal resmi</p>
                    <h1 class="text-title-sm sm:text-title-md font-semibold text-gray-800 dark:text-white/90">Buat akun
                        MODIS PENDIS</h1>
                    <p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">Daftarkan identitas Anda untuk
                        mengakses layanan perangkat belajar pegawai.</p>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-950/40 dark:text-success-300"
                        role="status">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300"
                        role="alert">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300"
                        role="alert">
                        <p class="font-semibold">Pendaftaran belum berhasil.</p>
                        <p class="mt-1">Periksa kembali data yang Anda masukkan.</p>
                    </div>
                @endif

                <form method="POST" action="{{ url('/register') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama
                            lengkap<span class="text-error-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name"
                            required autofocus placeholder="Masukkan nama lengkap"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border {{ $errors->has('name') ? 'border-error-500' : 'border-gray-300' }} bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('name')
                            <p class="text-theme-xs mt-1.5 text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">NIP
                            / Email<span class="text-error-500">*</span></label>
                        <input type="text" id="email" name="email" value="{{ old('email') }}"
                            autocomplete="username" required placeholder="Masukkan NIP atau email"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border {{ $errors->has('email') ? 'border-error-500' : 'border-gray-300' }} bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('email')
                            <p class="text-theme-xs mt-1.5 text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Sekolah (Select Option) -->
                    <div>
                        <label for="sekolah_id"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Asal Sekolah /
                            Madrasah<span class="text-error-500">*</span></label>
                        <select id="sekolah_id" name="sekolah_id" required
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border {{ $errors->has('sekolah_id') ? 'border-error-500' : 'border-gray-300' }} bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="" disabled {{ old('sekolah_id') ? '' : 'selected' }}>-- Pilih Sekolah --
                            </option>
                            @foreach ($sekolahs as $sekolah)
                                <option value="{{ $sekolah->id }}"
                                    {{ old('sekolah_id') == $sekolah->id ? 'selected' : '' }}>
                                    {{ $sekolah->nama_sekolah }}
                                </option>
                            @endforeach
                        </select>
                        @error('sekolah_id')
                            <p class="text-theme-xs mt-1.5 text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password<span
                                    class="text-error-500">*</span></label>
                            <input type="password" id="password" name="password" autocomplete="new-password" required
                                placeholder="Minimal 8 karakter"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border {{ $errors->has('password') ? 'border-error-500' : 'border-gray-300' }} bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                            @error('password')
                                <p class="text-theme-xs mt-1.5 text-error-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Konfirmasi
                                password<span class="text-error-500">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                autocomplete="new-password" required placeholder="Ulangi password"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        </div>
                    </div>
                    @error('password_confirmation')
                        <p class="text-theme-xs -mt-2 text-error-500">{{ $message }}</p>
                    @enderror

                    <label
                        class="flex cursor-pointer items-start gap-3 text-sm leading-5 text-gray-600 dark:text-gray-400">
                        <input type="checkbox" name="terms" required
                            class="mt-0.5 size-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900" />
                        <span>Saya menyatakan data yang diisi benar dan menyetujui ketentuan penggunaan MODIS PENDIS.</span>
                    </label>
                    @error('terms')
                        <p class="text-theme-xs text-error-500">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-semibold text-white transition focus:ring-4 focus:ring-brand-500/20 focus:outline-hidden">Daftar
                        akun MODIS PENDIS</button>
                </form>

                <p class="mt-7 text-center text-sm text-gray-600 dark:text-gray-400">Sudah memiliki akun? <a
                        href="{{ url('/signin') }}"
                        class="font-semibold text-brand-500 hover:text-brand-600 dark:text-brand-400">Masuk di sini</a></p>
            </div>
        </div>
    </div>
@endsection
