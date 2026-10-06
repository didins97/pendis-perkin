<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PegawaiController extends Controller
{
    public function index(Request $request): View
    {
        $pegawais = User::query()
            ->where('role', 'pegawai')
            ->with(['sekolah', 'profilPegawai'])
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
            // Filter pegawai yang sudah mengunggah eviden vs belum
            ->when($request->input('status') === 'active', fn ($query) => $query->whereHas('realisasins'))
            ->when($request->input('status') === 'incomplete', fn ($query) => $query->whereDoesntHave('realisasins'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.master-data.pegawai', [
            'title' => 'Data Pegawai',
            'pegawais' => $pegawais,
            'schools' => Sekolah::query()->orderBy('nama_sekolah')->get(),
        ]);
    }

    public function show(User $pegawai): View
    {
        abort_unless($pegawai->role === 'pegawai', 404);

        $pegawai->load(['sekolah', 'profilPegawai', 'realisasins']);

        return view('pages.admin.master-data.pegawai-show', [
            'title' => 'Detail Pegawai',
            'pegawai' => $pegawai,
        ]);
    }

    public function edit(User $pegawai): View
    {
        abort_unless($pegawai->role === 'pegawai', 404);

        return view('pages.admin.master-data.pegawai-edit', [
            'title' => 'Edit Pegawai',
            'pegawai' => $pegawai->load(['sekolah', 'profilPegawai']),
            'schools' => Sekolah::query()->orderBy('nama_sekolah')->get(),
        ]);
    }

    public function document(User $pegawai, string $document): StreamedResponse
    {
        abort_unless($pegawai->role === 'pegawai', 404);

        $documentFields = [
            'sk-pangkat' => 'berkas_sk_pangkat',
            'sk-mengajar' => 'berkas_sk_mengajar',
            'serdik' => 'berkas_serdik',
        ];
        abort_unless(isset($documentFields[$document]), 404);

        $path = $pegawai->profilPegawai?->{$documentFields[$document]};
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $data['role'] = 'pegawai';
        $data['status_aktif'] = $data['status_aktif'] ?? true;
        $data['password'] = Hash::make($data['password'] ?? str()->random(16));

        User::create($data);

        return back()->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function update(Request $request, User $pegawai): RedirectResponse
    {
        abort_unless($pegawai->role === 'pegawai', 404);

        $data = $request->validate($this->rules($pegawai));

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $profileData = $request->validate($this->profileRules());

        DB::transaction(function () use ($pegawai, $data, $profileData, $request) {
            $pegawai->update($data);

            foreach (['berkas_sk_pangkat', 'berkas_sk_mengajar', 'berkas_serdik'] as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('profil-pegawai', 'local');
                    if (! $path) {
                        throw new \RuntimeException('Berkas profil pegawai gagal disimpan.');
                    }

                    $profileData[$field] = $path;
                }
            }

            $pegawai->profilPegawai()->updateOrCreate(
                ['user_id' => $pegawai->id],
                $profileData
            );
        });

        return redirect()->route('admin.pegawai.show', $pegawai)->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(User $pegawai): RedirectResponse
    {
        abort_unless($pegawai->role === 'pegawai', 404);
        $pegawai->delete();

        return back()->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function history(User $pegawai): View
    {
        abort_unless($pegawai->role === 'pegawai', 404);

        $pegawai->load(['sekolah', 'realisasins.indikator.sasaran' => fn ($query) => $query->latest()]);

        return view('pages.admin.master-data.pegawai-history', [
            'title' => 'Riwayat Eviden Pegawai',
            'pegawai' => $pegawai,
            'items' => $pegawai->realisasins,
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
                        'role' => 'pegawai',
                        'password' => Hash::make($data['password'] ?? str()->random(16)),
                    ]
                );
            }
        });

        fclose($handle);

        return back()->with('success', 'Data pegawai berhasil diimport.');
    }

    private function rules(?User $pegawai = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50', Rule::unique('users', 'nip')->ignore($pegawai?->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($pegawai?->id)],
            'nomor_wa' => ['nullable', 'string', 'max:20'],
            'status_aktif' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
            'sekolah_id' => ['required', 'exists:sekolahs,id'],
        ];
    }

    private function profileRules(): array
    {
        return [
            'nuptk' => ['nullable', 'string', 'max:50'],
            'nrg' => ['nullable', 'string', 'max:50'],
            'pangkat_golongan' => ['nullable', 'string', 'max:100'],
            'status_kepegawaian' => ['nullable', Rule::in(['PNS', 'PPPK', 'Non-ASN'])],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'tugas_tambahan' => ['nullable', 'string'],
            'berkas_sk_pangkat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'berkas_sk_mengajar' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'berkas_serdik' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
