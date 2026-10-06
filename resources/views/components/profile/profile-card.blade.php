@props(['user'])

<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="mb-6 flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex w-full flex-col items-center gap-6 xl:flex-row">
            <div class="h-20 w-20 overflow-hidden rounded-full border border-gray-200 bg-gray-100 dark:border-gray-800">
                <img src="/images/user/owner.png" alt="{{ $user->name }}" class="h-full w-full object-cover" />
            </div>

            <div class="order-3 xl:order-2">
                <h4 class="mb-2 text-center text-lg font-semibold text-gray-800 xl:text-left dark:text-white/90">
                    {{ $user->name }}
                </h4>
                <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->role === 'pegawai' ? 'Pegawai' : ucfirst($user->role) }}
                    </p>
                    <div class="hidden h-3.5 w-px bg-gray-300 xl:block dark:bg-gray-700"></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->sekolah?->nama_sekolah ?? 'Sekolah belum diatur' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php($profil = $user->profilPegawai)

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <div class="col-span-2 lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Nama Lengkap
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    required />
            </div>

            <div class="col-span-2 lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    NIP
                </label>
                <input type="text" name="nip" value="{{ old('nip', $user->nip ?? '') }}"
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            </div>

            <div class="col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    required />
            </div>

            <div class="col-span-2 lg:col-span-1">
                <label for="nomor_wa" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor WhatsApp</label>
                <input id="nomor_wa" type="tel" name="nomor_wa" value="{{ old('nomor_wa', $user->nomor_wa) }}"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
        </div>

        <section class="border-t border-gray-200 pt-6 dark:border-gray-800">
            <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Profil Kepegawaian</h4>
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                <div>
                    <label for="nuptk" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">NUPTK</label>
                    <input id="nuptk" name="nuptk" value="{{ old('nuptk', $profil?->nuptk) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                    <label for="nrg" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">NRG</label>
                    <input id="nrg" name="nrg" value="{{ old('nrg', $profil?->nrg) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                    <label for="pangkat_golongan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pangkat / Golongan</label>
                    <input id="pangkat_golongan" name="pangkat_golongan" value="{{ old('pangkat_golongan', $profil?->pangkat_golongan) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                    <label for="status_kepegawaian" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status Kepegawaian</label>
                    <select id="status_kepegawaian" name="status_kepegawaian" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Pilih status</option>
                        @foreach (['PNS', 'PPPK', 'Non-ASN'] as $status)
                            <option value="{{ $status }}" @selected(old('status_kepegawaian', $profil?->status_kepegawaian) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="jabatan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jabatan</label>
                    <input id="jabatan" name="jabatan" value="{{ old('jabatan', $profil?->jabatan) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                    <label for="tugas_tambahan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tugas Tambahan</label>
                    <textarea id="tugas_tambahan" name="tugas_tambahan" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('tugas_tambahan', $profil?->tugas_tambahan) }}</textarea>
                </div>
            </div>
        </section>

        <section class="border-t border-gray-200 pt-6 dark:border-gray-800">
            <h4 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">Berkas Kepegawaian</h4>
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">Format PDF, JPG, JPEG, atau PNG; maksimal 5 MB per berkas.</p>
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                @foreach ([
                    'berkas_sk_pangkat' => ['SK Pangkat', 'sk-pangkat'],
                    'berkas_sk_mengajar' => ['SK Mengajar', 'sk-mengajar'],
                    'berkas_serdik' => ['Sertifikat Pendidik', 'serdik'],
                ] as $field => [$label, $documentKey])
                    <div>
                        <label for="{{ $field }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</label>
                        <input id="{{ $field }}" type="file" name="{{ $field }}" accept=".pdf,.jpg,.jpeg,.png" class="block w-full rounded-lg border border-gray-300 p-3 text-sm dark:border-gray-700 dark:text-gray-300" />
                        @if ($profil?->$field)
                            <a href="{{ route('profile.document', $documentKey) }}" class="mt-2 inline-block text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">Unduh berkas saat ini</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit"
                class="flex w-full justify-center rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
