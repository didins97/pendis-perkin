<?php

namespace App\Http\Controllers;

use App\Models\MasterKegiatan;
use App\Models\MasterProgram;
use App\Models\TahunAnggaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MasterAnggaranController extends Controller
{
    public function index(Request $request): View
    {
        $tahunId = $request->input('tahun_anggaran_id');

        $programs = MasterProgram::query()
            ->with('kegiatans')
            ->when($tahunId, fn ($query) => $query->where('tahun_anggaran_id', $tahunId))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('kode_program', 'like', "%{$search}%")
                        ->orWhere('nama_program', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $grandTotal = MasterKegiatan::query()
            ->join('master_programs', 'master_kegiatans.program_id', '=', 'master_programs.id')
            ->when($tahunId, fn ($query) => $query->where('master_programs.tahun_anggaran_id', $tahunId))
            ->sum('master_kegiatans.anggaran');

        $tahuns = TahunAnggaran::orderBy('tahun', 'desc')->get();

        return view('pages.admin.master-perkin.master-anggaran', [
            'title' => 'Master Anggaran & Program',
            'programs' => $programs,
            'grandTotal' => $grandTotal,
            'tahuns' => $tahuns,
            'selectedTahun' => $tahunId,
        ]);
    }

    public function showProgram(MasterProgram $program): View
    {
        $program->load('kegiatans');

        return view('pages.admin.master-perkin.program-kegiatan', [
            'title' => 'Kelola Kegiatan Program',
            'program' => $program,
            'kegiatans' => $program->kegiatans,
        ]);
    }

    public function storeProgram(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun_anggaran_id' => ['required', 'exists:tahun_anggarans,id'],
            'kode_program' => ['required', 'string', 'max:255'],
            'nama_program' => ['required', 'string', 'max:255'],
        ]);

        $data['created_by'] = Auth::id();

        MasterProgram::create($data);

        return back()->with('success', 'Program berhasil ditambahkan.');
    }

    public function updateProgram(Request $request, MasterProgram $program): RedirectResponse
    {
        $program->update($request->validate([
            'tahun_anggaran_id' => ['required', 'exists:tahun_anggarans,id'],
            'kode_program' => ['required', 'string', 'max:255'],
            'nama_program' => ['required', 'string', 'max:255'],
        ]));

        return back()->with('success', 'Program berhasil diperbarui.');
    }

    public function destroyProgram(MasterProgram $program): RedirectResponse
    {
        $program->delete();

        return back()->with('success', 'Program berhasil dihapus.');
    }

    public function storeKegiatan(Request $request, MasterProgram $program): RedirectResponse
    {
        $data = $request->validate([
            'kode_kegiatan' => ['required', 'string', 'max:255'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'anggaran' => ['required', 'numeric', 'min:0'],
        ]);

        $program->kegiatans()->create($data);

        return back()->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function updateKegiatan(Request $request, MasterProgram $program, MasterKegiatan $kegiatan): RedirectResponse
    {
        abort_unless($kegiatan->program_id === $program->id, 404);

        $kegiatan->update($request->validate([
            'kode_kegiatan' => ['required', 'string', 'max:255'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'anggaran' => ['required', 'numeric', 'min:0'],
        ]));

        return back()->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroyKegiatan(MasterProgram $program, MasterKegiatan $kegiatan): RedirectResponse
    {
        abort_unless($kegiatan->program_id === $program->id, 404);

        $kegiatan->delete();

        return back()->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function submitToPimpinan(int $tahun_anggaran_id): RedirectResponse
    {
        $tahun = TahunAnggaran::findOrFail($tahun_anggaran_id);

        abort_unless(in_array($tahun->status_approval, ['draft', 'rejected'], true), 422, 'Tahun anggaran tidak dapat dikirim pada status ini.');

        $tahun->update([
            'status_approval' => 'submitted',
            'approved_by' => null,
            'approved_at' => null,
            'catatan_approval' => null,
        ]);

        return back()->with('success', "Tahun anggaran {$tahun->tahun} berhasil dikirim ke Pimpinan.");
    }
}
