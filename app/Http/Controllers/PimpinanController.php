<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PimpinanController extends Controller
{
    public function index(Request $request): View
    {
        $pimpinanQuery = User::query()->where('role', 'pimpinan');
        $pimpinans = (clone $pimpinanQuery)
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.master-data.pimpinan', [
            'title' => 'Data Pimpinan',
            'pimpinans' => $pimpinans,
            'totalPimpinans' => (clone $pimpinanQuery)->count(),
            'activePimpinans' => (clone $pimpinanQuery)->whereNotNull('email_verified_at')->count(),
            'unverifiedPimpinans' => (clone $pimpinanQuery)->whereNull('email_verified_at')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $data['role'] = 'pimpinan';
        $data['password'] = $data['password'] ?: Str::random(16);

        User::create($data);

        return back()->with('success', 'Akun pimpinan berhasil ditambahkan.');
    }

    public function update(Request $request, User $pimpinan): RedirectResponse
    {
        abort_unless($pimpinan->role === 'pimpinan', 404);

        $data = $request->validate($this->rules($pimpinan));
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $pimpinan->update($data);

        return back()->with('success', 'Data pimpinan berhasil diperbarui.');
    }

    public function resetPassword(User $pimpinan): RedirectResponse
    {
        abort_unless($pimpinan->role === 'pimpinan', 404);

        $temporaryPassword = Str::random(12);
        $pimpinan->update(['password' => $temporaryPassword]);

        return back()->with('temporary_password', $temporaryPassword);
    }

    private function rules(?User $pimpinan = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50', Rule::unique('users', 'nip')->ignore($pimpinan?->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($pimpinan?->id)],
            'password' => [$pimpinan ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }
}
