<?php

namespace App\Http\Controllers;

use App\Models\MasterKegiatan;
use App\Models\MasterProgram;
use App\Models\TahunAnggaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApprovalMasterController extends Controller
{
    public function index(): View
    {
        return view('pages.pimpinan.approval-master.index', [
            'tahuns' => TahunAnggaran::query()
                ->withCount('sasarans')
                ->with('masterPrograms.kegiatans')
                ->whereIn('status_approval', ['submitted', 'rejected', 'approved'])
                ->latest('tahun')
                ->get(),
        ]);
    }

    public function show(TahunAnggaran $tahun): View
    {
        $tahun->load([
            'sasarans.indikators',
            'masterPrograms.kegiatans',
            'approver',
        ]);

        return view('pages.pimpinan.approval-master.show', [
            'tahun' => $tahun,
            'totalPagu' => MasterKegiatan::query()
                ->whereHas('program', fn ($query) => $query->where('tahun_anggaran_id', $tahun->id))
                ->sum('anggaran'),
            'programCount' => MasterProgram::query()->where('tahun_anggaran_id', $tahun->id)->count(),
        ]);
    }

    public function approve(int $tahun_anggaran_id): RedirectResponse
    {
        $tahun = TahunAnggaran::findOrFail($tahun_anggaran_id);
        abort_unless($tahun->status_approval === 'submitted', 422, 'Hanya pengajuan yang sudah dikirim yang dapat disetujui.');

        $tahun->update([
            'status_approval' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_approval' => null,
        ]);

        return redirect()->route('pimpinan.approval-master.show', $tahun)
            ->with('success', "Master Tahun Anggaran {$tahun->tahun} disetujui.");
    }

    public function reject(Request $request, int $tahun_anggaran_id): RedirectResponse
    {
        $data = $request->validate([
            'catatan_approval' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $tahun = TahunAnggaran::findOrFail($tahun_anggaran_id);
        abort_unless($tahun->status_approval === 'submitted', 422, 'Hanya pengajuan yang sudah dikirim yang dapat dikembalikan.');

        $tahun->update([
            'status_approval' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_approval' => $data['catatan_approval'],
        ]);

        return redirect()->route('pimpinan.approval-master.show', $tahun)
            ->with('success', "Master Tahun Anggaran {$tahun->tahun} dikembalikan untuk revisi.");
    }
}
