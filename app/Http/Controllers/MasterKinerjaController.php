<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\SasaranKinerja;
use App\Models\TahunAnggaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MasterKinerjaController extends Controller
{
    public function indexTahun(): View
    {
        return view('pages.admin.master-data.kinerja-tahun', [
            'title' => 'Master Data Kinerja',
            'tahuns' => TahunAnggaran::withCount('sasarans')->latest('tahun')->get(),
        ]);
    }

    public function storeTahun(Request $request): RedirectResponse
    {
        TahunAnggaran::create($request->validate([
            'tahun' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/', 'unique:tahun_anggarans,tahun'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]));

        return back()->with('success', 'Tahun anggaran berhasil ditambahkan.');
    }

    public function updateTahun(Request $request, TahunAnggaran $tahun): RedirectResponse
    {
        $tahun->update($request->validate([
            'tahun' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/', Rule::unique('tahun_anggarans', 'tahun')->ignore($tahun)],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]));

        return back()->with('success', 'Tahun anggaran berhasil diperbarui.');
    }

    public function destroyTahun(TahunAnggaran $tahun): RedirectResponse
    {
        $tahun->delete();

        return back()->with('success', 'Tahun anggaran berhasil dihapus.');
    }

    public function showSasaran(TahunAnggaran $tahun): View
    {
        $tahun->load(['sasarans' => fn ($query) => $query->withCount('indikators')->orderBy('no_urut')]);

        return view('pages.admin.master-data.kinerja-sasaran', [
            'title' => "Sasaran Kinerja {$tahun->tahun}",
            'tahun' => $tahun,
            'nextNoUrut' => $tahun->sasarans->max('no_urut') ? $tahun->sasarans->max('no_urut') + 1 : 1,
        ]);
    }

    public function storeSasaran(Request $request, TahunAnggaran $tahun): RedirectResponse
    {
        $data = $request->validate($this->sasaranRules($tahun));
        $data['tahun_anggaran_id'] = $tahun->id;
        $data['created_by'] = Auth::id();

        SasaranKinerja::create($data);

        return back()->with('success', 'Sasaran kinerja berhasil ditambahkan.');
    }

    public function updateSasaran(Request $request, TahunAnggaran $tahun, SasaranKinerja $sasaran): RedirectResponse
    {
        abort_unless($sasaran->tahun_anggaran_id === $tahun->id, 404);
        $sasaran->update($request->validate($this->sasaranRules($tahun, $sasaran)));

        return back()->with('success', 'Sasaran kinerja berhasil diperbarui.');
    }

    public function destroySasaran(TahunAnggaran $tahun, SasaranKinerja $sasaran): RedirectResponse
    {
        abort_unless($sasaran->tahun_anggaran_id === $tahun->id, 404);
        $sasaran->delete();

        return back()->with('success', 'Sasaran kinerja berhasil dihapus.');
    }

    public function showIndikators(SasaranKinerja $sasaran): View
    {
        $sasaran->load(['tahunAnggaran', 'indikators' => fn ($query) => $query->orderBy('kode_sub')->orderBy('id')]);

        return view('pages.admin.master-data.kinerja-indikator', [
            'title' => 'Indikator Kinerja',
            'sasaran' => $sasaran,
            'usedCodes' => $sasaran->indikators->pluck('kode_sub')->filter()->unique()->values()->all(),
            'nextKodeSub' => $this->nextKodeSub($sasaran),
        ]);
    }

    public function getIndikators(SasaranKinerja $sasaran): JsonResponse
    {
        return response()->json($sasaran->indikators()->orderBy('kode_sub')->orderBy('id')->get());
    }

    public function storeIndikator(Request $request, SasaranKinerja $sasaran): JsonResponse|RedirectResponse
    {
        $data = $request->validate($this->indikatorRules($sasaran));
        $data['kode_sub'] = $this->nextKodeSub($sasaran, $data['kode_sub'] ?? null);

        $indikator = $sasaran->indikators()->create($data);

        return $request->expectsJson()
            ? response()->json($indikator, 201)
            : back()->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function updateIndikator(Request $request, SasaranKinerja $sasaran, IndikatorKinerja $indikator): JsonResponse|RedirectResponse
    {
        abort_unless($indikator->sasaran_id === $sasaran->id, 404);
        $data = $request->validate($this->indikatorRules($sasaran, $indikator));
        $indikator->update($data);

        return $request->expectsJson()
            ? response()->json($indikator)
            : back()->with('success', 'Indikator berhasil diperbarui.');
    }

    public function destroyIndikator(Request $request, SasaranKinerja $sasaran, IndikatorKinerja $indikator): JsonResponse|RedirectResponse
    {
        abort_unless($indikator->sasaran_id === $sasaran->id, 404);
        $indikator->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Indikator berhasil dihapus.'])
            : back()->with('success', 'Indikator berhasil dihapus.');
    }

    private function sasaranRules(TahunAnggaran $tahun, ?SasaranKinerja $sasaran = null): array
    {
        return [
            'no_urut' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('master_sasarans', 'no_urut')
                    ->where('tahun_anggaran_id', $tahun->id)
                    ->ignore($sasaran?->id),
            ],
            'sasaran_kegiatan' => ['required', 'string'],
        ];
    }

    private function indikatorRules(SasaranKinerja $sasaran, ?IndikatorKinerja $indikator = null): array
    {
        return [
            'kode_sub' => [
                'required',
                'string',
                'max:1',
                'regex:/^[a-z]$/',
                Rule::unique('master_indikators', 'kode_sub')
                    ->where('sasaran_id', $sasaran->id)
                    ->ignore($indikator?->id),
            ],
            'indikator_kinerja' => ['required', 'string'],
            'target_default' => ['required', 'string', 'max:255'],
            'satuan' => ['nullable', 'string', 'max:50'],
        ];
    }

    private function nextKodeSub(SasaranKinerja $sasaran, ?string $requestedKodeSub = null): string
    {
        if ($requestedKodeSub) {
            return strtolower($requestedKodeSub);
        }

        $used = $sasaran->indikators()->pluck('kode_sub')->filter()->unique()->values()->all();
        foreach (range('a', 'z') as $letter) {
            if (! in_array($letter, $used, true)) {
                return $letter;
            }
        }

        return 'a';
    }
}
