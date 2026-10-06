<?php

namespace App\Http\Controllers;

use App\Models\MasterAnggaran;
use App\Models\TahunAnggaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnggranProgramController extends Controller
{
    public function index(Request $request): View
    {
        $anggarans = MasterAnggaran::query()
            ->with(['creator', 'tahunAnggaran'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('kode_program', 'like', "%{$search}%")
                        ->orWhere('nama_program', 'like', "%{$search}%")
                        ->orWhere('kode_kegiatan', 'like', "%{$search}%")
                        ->orWhere('nama_kegiatan', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('tahun_anggaran_id'), fn ($query) => $query->where('tahun_anggaran_id', $request->integer('tahun_anggaran_id')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $tahuns = TahunAnggaran::orderBy('tahun', 'desc')->get();

        return view('pages.admin.master-perkin.master-anggaran', [
            'title' => 'Master Anggaran & Program',
            'anggarans' => $anggarans,
            'tahuns' => $tahuns,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->validationRules());
        $data['created_by'] = Auth::id();

        MasterAnggaran::create($data);

        return back()->with('success', 'Data anggaran berhasil ditambahkan.');
    }

    public function update(Request $request, MasterAnggaran $masterAnggaran): RedirectResponse
    {
        $masterAnggaran->update($request->validate($this->validationRules()));

        return back()->with('success', 'Data anggaran berhasil diperbarui.');
    }

    public function destroy(MasterAnggaran $masterAnggaran): RedirectResponse
    {
        $masterAnggaran->delete();

        return back()->with('success', 'Data anggaran berhasil dihapus.');
    }

    private function validationRules(): array
    {
        return [
            'tahun_anggaran_id' => ['required', 'exists:tahun_anggarans,id'],
            'kode_program' => ['required', 'string', 'max:255'],
            'nama_program' => ['required', 'string', 'max:255'],
            'kode_kegiatan' => ['nullable', 'string', 'max:255'],
            'nama_kegiatan' => ['nullable', 'string', 'max:255'],
            'anggaran' => ['required', 'numeric', 'min:0'],
        ];
    }
}
