<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        $teachers = User::query()
            ->where('role', 'guru')
            ->with('sekolah')
            // Menghitung total pengunggahan eviden realisasi perkin
            ->withCount('realisasins')
            // Menghitung eviden yang sudah diverifikasi (approved)
            ->withCount(['realisasins as approved_evidens_count' => fn ($query) => $query->where('status_verifikasi', 'approved')])
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('sekolah_id'), fn ($query) => $query->where('sekolah_id', $request->input('sekolah_id')))
            // Filter guru yang sudah mengunggah eviden vs belum
            ->when($request->input('status') === 'active', fn ($query) => $query->whereHas('realisasins'))
            ->when($request->input('status') === 'incomplete', fn ($query) => $query->whereDoesntHave('realisasins'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.master-data.guru', [
            'title' => 'Data Guru & Pegawai',
            'teachers' => $teachers,
            'schools' => Sekolah::query()->orderBy('nama_sekolah')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $data['role'] = 'guru';
        $data['password'] = Hash::make($data['password'] ?? str()->random(16));

        User::create($data);

        return back()->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function update(Request $request, User $guru): RedirectResponse
    {
        abort_unless($guru->role === 'guru', 404);

        $data = $request->validate($this->rules($guru));

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $guru->update($data);

        return back()->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(User $guru): RedirectResponse
    {
        abort_unless($guru->role === 'guru', 404);
        $guru->delete();

        return back()->with('success', 'Data guru berhasil dihapus.');
    }

    public function history(User $guru): View
    {
        abort_unless($guru->role === 'guru', 404);

        // Memuat riwayat eviden perkin yang diunggah guru beserta detail indikator dan sasarannya
        $guru->load(['sekolah', 'realisasins.indikator.sasaran' => fn ($query) => $query->latest()]);

        return view('pages.admin.master-data.guru-history', [
            'title' => 'Riwayat Eviden Perkin Guru',
            'teacher' => $guru,
            'items' => $guru->realisasins,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = array_map('strtolower', array_map('trim', fgetcsv($handle) ?: []));
        $required = ['name', 'nip', 'email', 'sekolah_id'];

        abort_unless($header === $required, 422, 'Header CSV harus: name,nip,email,sekolah_id.');

        DB::transaction(function () use ($handle, $header) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                $data = array_combine($header, $row);
                validator($data, $this->rules())->validate();

                User::updateOrCreate(
                    ['nip' => $data['nip']],
                    [
                        ...$data,
                        'role' => 'guru',
                        'password' => Hash::make($data['password'] ?? str()->random(16))
                    ]
                );
            }
        });

        fclose($handle);

        return back()->with('success', 'Data guru berhasil diimport.');
    }

    private function rules(?User $guru = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50', Rule::unique('users', 'nip')->ignore($guru?->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guru?->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
        ];
    }
}
