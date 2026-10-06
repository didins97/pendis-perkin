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
                        <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">Guru</span>
                        <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">Pimpinan</span>
                        <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">Admin Kemenag</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative flex min-h-screen w-full items-center px-6 py-10 sm:px-10 lg:w-1/2 lg:px-16 xl:px-24">
            <div class="mx-auto w-full max-w-md">

                <div class="mb-8">
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
                    <h1 class="text-title-sm sm:text-title-md font-semibold text-gray-800 dark:text-white/90">Masuk ke
                        MODIS PENDIS</h1>
                    <p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">Portal Verifikasi &amp; Pengesahan
                        Perangkat Belajar Guru</p>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-800 dark:bg-success-950/40 dark:text-success-300"
                        role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300"
                        role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300"
                        role="alert">
                        <p class="font-semibold">Login belum berhasil.</p>
                        <p class="mt-1">Periksa kembali NIP/email dan password Anda.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">NIP
                            / Email<span class="text-error-500">*</span></label>
                        <input type="text" id="email" name="email" value="{{ old('email') }}"
                            autocomplete="username" required autofocus placeholder="Masukkan NIP atau email"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border {{ $errors->has('email') ? 'border-error-500' : 'border-gray-300' }} bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('email')
                            <p class="text-theme-xs mt-1.5 text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Password<span
                                    class="text-error-500">*</span></label>
                            <a href="{{ url('/reset-password') }}"
                                class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Lupa
                                password?</a>
                        </div>
                        <div x-data="{ showPassword: false }" class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                                autocomplete="current-password" required placeholder="Masukkan password"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border {{ $errors->has('password') ? 'border-error-500' : 'border-gray-300' }} bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute top-1/2 right-3.5 -translate-y-1/2 text-gray-500 dark:text-gray-400"
                                :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                                <svg x-show="!showPassword" class="size-5 fill-current" viewBox="0 0 20 20"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 4.04c-3.52 0-6.51 2.27-7.58 5.42a1.5 1.5 0 0 0 0 1.08c1.07 3.15 4.06 5.42 7.58 5.42s6.51-2.27 7.58-5.42a1.5 1.5 0 0 0 0-1.08C16.51 6.31 13.52 4.04 10 4.04Zm0 8.52a2.56 2.56 0 1 1 0-5.12 2.56 2.56 0 0 1 0 5.12Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="size-5 fill-current" viewBox="0 0 20 20"
                                    aria-hidden="true">
                                    <path
                                        d="m3.58 3.58 12.84 12.84-1.06 1.06-2.1-2.1A9.35 9.35 0 0 1 10 16c-3.52 0-6.51-2.27-7.58-5.42a1.5 1.5 0 0 1 0-1.08 9.3 9.3 0 0 1 3.1-4.1L2.52 4.64l1.06-1.06ZM10 6.44c-.45 0-.89.08-1.28.21l1.45 1.45a2.56 2.56 0 0 1 1.7 1.7l1.45 1.45c.13-.4.21-.83.21-1.28A3.53 3.53 0 0 0 10 6.44ZM6.63 7.69a3.53 3.53 0 0 0 5.68 5.68L6.63 7.69Z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-theme-xs mt-1.5 text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex cursor-pointer items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <input type="checkbox" name="remember"
                            class="size-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900" />
                        Ingat saya di perangkat ini
                    </label>

                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-semibold text-white transition focus:ring-4 focus:ring-brand-500/20 focus:outline-hidden">
                        Masuk ke MODIS PENDIS
                    </button>
                </form>

                <p class="mt-7 text-center text-sm text-gray-600 dark:text-gray-400">
                    Belum memiliki akun Guru?
                    <a href="{{ route('signup') }}"
                        class="font-semibold text-brand-500 hover:text-brand-600 dark:text-brand-400">
                        Daftar akun baru
                    </a>
                </p>

                <p class="mt-6 text-center text-xs leading-5 text-gray-500 dark:text-gray-500">
                    Akses terbatas untuk pengguna terdaftar pada lingkungan Kementerian Agama.
                </p>
            </div>
        </div>
    </div>
@endsection
