<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
        'role' => 'guru',
        'password' => $data['password'],
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->to($this->dashboardRoute($user))->with('status', 'Akun MODIS PENDIS berhasil dibuat.');
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
            default => route('guru.dashboard'),
        };
    }

    private function internalEmailForNip(string $nip): string
    {
        return $nip.'@modispendis.local';
    }
}
