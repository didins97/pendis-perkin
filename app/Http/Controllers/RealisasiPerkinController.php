<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\RealisasiPerkin;
use App\Models\SasaranKinerja;
use App\Models\TahunAnggaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RealisasiPerkinController extends Controller
{
    public function pegawaiIndex(): View
    {
        $items = RealisasiPerkin::query()
            ->with(['indikator.sasaran'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pages.pegawai.realisasi-index', [
            'items' => $items,
        ]);
    }

    public function pegawaiCreate(): View
    {
        $tahuns = TahunAnggaran::approved()
            ->with(['sasarans' => fn ($query) => $query->with('indikators')->orderBy('no_urut')])
            ->orderByDesc('tahun')
            ->get();

        $sasarans = SasaranKinerja::query()
            ->with(['indikators', 'tahunAnggaran'])
            ->whereHas('tahunAnggaran', fn ($query) => $query->where('status_approval', 'approved'))
            ->orderBy('no_urut')
            ->get();

        return view('pages.pegawai.realisasi-create', [
            'tahuns' => $tahuns,
            'sasarans' => $sasarans,
        ]);
    }

    public function indikatorBySasaran(SasaranKinerja $sasaran): JsonResponse
    {
        Gate::authorize('use-approved-master', $sasaran->tahunAnggaran);

        $indikators = $sasaran->indikators()
            ->select('id', 'indikator_kinerja', 'kode_sub', 'satuan')
            ->orderBy('kode_sub')
            ->get();

        return response()->json($indikators->map(function ($indikator) {
            return [
                'id' => $indikator->id,
                'indikator_kinerja' => $indikator->indikator_kinerja,
                'kode_sub' => $indikator->kode_sub,
                'satuan' => $indikator->satuan,
                'label' => $indikator->kode_sub ? 'Indikator ' . $indikator->kode_sub . ' - ' . $indikator->indikator_kinerja : $indikator->indikator_kinerja,
            ];
        }));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'master_indikator_id' => ['required', 'exists:master_indikators,id'],
            'catatan_guru' => ['nullable', 'string'],
            'file_eviden' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,zip,doc,docx', 'max:2048'],
        ]);

        $indikator = IndikatorKinerja::query()
            ->with('sasaran.tahunAnggaran')
            ->findOrFail($data['master_indikator_id']);
        Gate::authorize('use-approved-master', $indikator->sasaran->tahunAnggaran);

        $path = $request->file('file_eviden')->store('realisasi-perkins', 'public');

        RealisasiPerkin::create([
            'user_id' => Auth::id(),
            'master_indikator_id' => $data['master_indikator_id'],
            'catatan_guru' => $data['catatan_guru'] ?? null,
            'file_eviden' => $path,
            'status_verifikasi' => 'pending',
        ]);

        return redirect()->route('pegawai.realisasi.index')->with('success', 'Eviden realisasi berhasil diunggah.');
    }

    public function adminIndex(): View
    {
        $items = RealisasiPerkin::query()
            ->with(['user.sekolah', 'indikator.sasaran', 'verifier'])
            ->latest()
            ->get();

        return view('pages.admin.realisasi-index', [
            'items' => $items,
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $item = RealisasiPerkin::query()->findOrFail($id);

        $item->update([
            'status_verifikasi' => 'approved',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikator' => 'Eviden telah diterima dan diverifikasi oleh Admin Kemenag.',
        ]);

        return redirect()->route('admin.realisasi.index')->with('success', 'Eviden berhasil diverifikasi.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'catatan_verifikator' => ['required', 'string', 'min:5'],
        ]);

        $item = RealisasiPerkin::query()->findOrFail($id);

        $item->update([
            'status_verifikasi' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikator' => $data['catatan_verifikator'],
        ]);

        return redirect()->route('admin.realisasi.index')->with('success', 'Eviden dikembalikan untuk revisi.');
    }
}
