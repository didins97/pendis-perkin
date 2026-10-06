<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('pages.auth.signin');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'NIP atau email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::query()
            ->where('email', $credentials['email'])
            ->orWhere('nip', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'NIP/email atau password tidak sesuai.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->to($this->dashboardRoute($user));
    }

    public function showRegisterForm()
    {
        $sekolahs = Sekolah::orderBy('nama_sekolah', 'asc')->get();

        return view('pages.auth.signup', compact('sekolahs'));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'sekolah_id' => ['required', 'exists:sekolahs,id'], // Validasi sekolah
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'NIP atau email wajib diisi.',
            'sekolah_id.required' => 'Pilih sekolah tempat Anda bertugas.',
            'sekolah_id.exists' => 'Sekolah yang dipilih tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui ketentuan penggunaan.',
        ]);

        $identity = $data['email'];
        $isEmail = filter_var($identity, FILTER_VALIDATE_EMAIL) !== false;
        $nip = $isEmail ? null : $identity;
        $email = $isEmail ? $identity : $this->internalEmailForNip($identity);

        if (User::where('email', $email)->exists() || ($nip && User::where('nip', $nip)->exists())) {
            throw ValidationException::withMessages([
                'email' => 'NIP atau email tersebut sudah terdaftar.',
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'nip' => $nip,
            'sekolah_id' => $data['sekolah_id'], // Simpan sekolah_id
            'role' => 'pegawai',
            'password' => $data['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->to($this->dashboardRoute($user))->with('status', 'Akun MODIS PENDIS berhasil dibuat.');
    }

    public function profile(): View
    {
        $user = Auth::user();

        abort_unless($user, 403);

        return view('pages.profile', [
            'title' => 'Profile',
            'user' => $user->load(['sekolah', 'profilPegawai']),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'nomor_wa' => ['nullable', 'string', 'max:20'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'nrg' => ['nullable', 'string', 'max:50'],
            'pangkat_golongan' => ['nullable', 'string', 'max:100'],
            'status_kepegawaian' => ['nullable', Rule::in(['PNS', 'PPPK', 'Non-ASN'])],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'tugas_tambahan' => ['nullable', 'string'],
            'berkas_sk_pangkat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'berkas_sk_mengajar' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'berkas_serdik' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $user, $validated) {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'nip' => $validated['nip'] ?? $user->nip,
                'nomor_wa' => $validated['nomor_wa'] ?? null,
            ]);

            $profileData = collect([
                'nuptk',
                'nrg',
                'pangkat_golongan',
                'status_kepegawaian',
                'jabatan',
                'tugas_tambahan',
            ])->mapWithKeys(fn ($field) => [$field => $validated[$field] ?? null])->all();

            foreach (['berkas_sk_pangkat', 'berkas_sk_mengajar', 'berkas_serdik'] as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('profil-pegawai', 'local');
                    if (! $path) {
                        throw new \RuntimeException('Berkas profil pegawai gagal disimpan.');
                    }

                    $profileData[$field] = $path;
                }
            }

            $user->profilPegawai()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        });

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function profileDocument(string $document): StreamedResponse
    {
        $documentFields = [
            'sk-pangkat' => 'berkas_sk_pangkat',
            'sk-mengajar' => 'berkas_sk_mengajar',
            'serdik' => 'berkas_serdik',
        ];
        abort_unless(isset($documentFields[$document]), 404);

        $user = Auth::user();
        $user?->load('profilPegawai');
        $path = $user?->profilPegawai?->{$documentFields[$document]};
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin')->with('status', 'Anda berhasil keluar dari MODIS PENDIS.');
    }

    private function dashboardRoute(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'pimpinan' => route('pimpinan.dashboard'),
            default => route('pegawai.dashboard'),
        };
    }

    private function internalEmailForNip(string $nip): string
    {
        return $nip.'@modispendis.local';
    }
}
