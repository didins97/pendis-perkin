<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SekolahController extends Controller
{
    public function index(Request $request): View
    {
        $schools = Sekolah::query()
            ->withCount('guru')
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('npsn', 'like', "%{$search}%")
                        ->orWhere('nama_sekolah', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.master-data.sekolah', [
            'title' => 'Data Sekolah',
            'schools' => $schools,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Sekolah::create($request->validate($this->validationRules()));

        return back()->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    public function update(Request $request, Sekolah $sekolah): RedirectResponse
    {
        $sekolah->update($request->validate($this->validationRules($sekolah)));

        return back()->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroy(Sekolah $sekolah): RedirectResponse
    {
        $sekolah->delete();

        return back()->with('success', 'Data sekolah berhasil dihapus.');
    }

    private function validationRules(?Sekolah $sekolah = null): array
    {
        return [
            'npsn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('sekolahs', 'npsn')->ignore($sekolah?->id),
            ],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
        ];
    }
}
