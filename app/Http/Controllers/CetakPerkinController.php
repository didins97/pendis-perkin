<?php

namespace App\Http\Controllers;

use App\Models\TahunAnggaran;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CetakPerkinController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.laporan-perkin', [
            'tahuns' => TahunAnggaran::query()
                ->withCount(['sasarans', 'masterPrograms'])
                ->with('approver')
                ->latest('tahun')
                ->get(),
        ]);
    }

    public function preview(int $id): View
    {
        return view('pages.perkin.cetak', $this->documentData($id));
    }

    public function cetakPdf(int $id): Response
    {
        ini_set('memory_limit', '256M');
        $data = $this->documentData($id);

        return Pdf::loadView('pages.perkin.cetak', $data)
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ])
            ->stream('PK-Tahun-' . $data['tahun']->tahun . '.pdf');
    }

    private function documentData(int $id): array
    {
        $tahun = TahunAnggaran::query()
            ->with([
                'sasarans' => fn ($query) => $query
                    ->with(['indikators' => fn ($indikator) => $indikator->orderBy('kode_sub')->orderBy('id')])
                    ->orderBy('no_urut'),
                'masterPrograms' => fn ($query) => $query
                    ->with(['kegiatans' => fn ($kegiatan) => $kegiatan->orderBy('kode_kegiatan')->orderBy('id')])
                    ->orderBy('kode_program'),
                'approver',
            ])
            ->findOrFail($id);

        abort_unless($tahun->status_approval === 'approved', 422, 'Dokumen PK hanya dapat dicetak setelah Tahun Anggaran disetujui.');

        $pihakPertama = Auth::user();
        abort_unless($pihakPertama, 403);

        $pihakKedua = $tahun->approver
            ?? User::query()->where('role', 'pimpinan')->orderBy('name')->first();

        $logoPath = public_path('images/logo/logocetak.png');
        $logoDataUri = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return [
            'tahun' => $tahun,
            'pihakPertama' => $pihakPertama,
            'pihakKedua' => $pihakKedua,
            'logoDataUri' => $logoDataUri,
            'kota' => config('app.kota', 'Daruba'),
            'tanggalCetak' => now()->translatedFormat('d F Y'),
        ];
    }
}
