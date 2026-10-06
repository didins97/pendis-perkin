<?php

namespace App\Http\Controllers;

use App\Models\RealisasiPerkin;
use App\Models\Sekolah;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // public function __invoke(Request $request): View
    // {
    //     $user = Auth::user();

    //     return match ($user->role) {
    //         'guru' => $this->guruDashboard($user),
    //         'pengawas' => $this->pengawasDashboard($user),
    //         'admin', 'kemenag' => $this->adminDashboard(),
    //         default => abort(403, 'Role pengguna tidak terdefinisi.'),
    //     };
    // }

    public function guruDashboard(): View
    {
        $guru = Auth::user();
        $tahunPerkin = \App\Models\TahunAnggaran::approved()->latest('tahun')->first();

        return view('pages.dashboard.guru-self-service', [
            'guru' => $guru,
            'tahunPerkin' => $tahunPerkin,
            'indikatorCount' => 4,
            'anggaranDikelola' => 124500000,
            'progresFisik' => 65,
            'evidenSummary' => [
                'approved' => 3,
                'pending' => 1,
                'revision' => 1,
            ],
            'targets' => [
                [
                    'indicator' => 'Peningkatan hasil asesmen keagamaan peserta didik',
                    'target' => '85% peserta didik mencapai nilai tuntas',
                    'progress' => 72,
                    'evidence' => 'approved',
                    'updated' => '12 Sep 2026',
                ],
                [
                    'indicator' => 'Penyusunan perangkat pembelajaran berkualitas',
                    'target' => '4 perangkat pembelajaran',
                    'progress' => 100,
                    'evidence' => 'approved',
                    'updated' => '10 Sep 2026',
                ],
                [
                    'indicator' => 'Pelaksanaan pembinaan karakter siswa',
                    'target' => '12 kegiatan pembinaan',
                    'progress' => 58,
                    'evidence' => 'pending',
                    'updated' => '08 Sep 2026',
                ],
                [
                    'indicator' => 'Kolaborasi pengembangan kompetensi guru',
                    'target' => '6 forum berbagi praktik baik',
                    'progress' => 44,
                    'evidence' => 'revision',
                    'updated' => '05 Sep 2026',
                ],
            ],
            'leaderNote' => [
                'author' => 'Drs. Ahmad Fauzi, M.Pd.',
                'role' => 'Pimpinan / Kasi Pendidikan',
                'date' => '13 September 2026',
                'message' => 'Mohon lengkapi eviden kegiatan pembinaan karakter dengan daftar hadir dan dokumentasi kegiatan. Setelah diperbarui, kirim kembali untuk verifikasi.',
            ],
        ]);
    }

    public function pimpinanDashboard(): View
    {
        $evidenceStats = RealisasiPerkin::query()
            ->selectRaw('status_verifikasi, COUNT(*) as total')
            ->groupBy('status_verifikasi')
            ->pluck('total', 'status_verifikasi');

        return view('pages.dashboard.pimpinan', [
            'totalEvidence' => $evidenceStats->sum(),
            'pendingEvidence' => $evidenceStats->get('pending', 0),
            'approvedEvidence' => $evidenceStats->get('approved', 0),
            'revisionEvidence' => $evidenceStats->get('rejected', 0),
            'recentEvidence' => RealisasiPerkin::query()
                ->with(['user.sekolah', 'indikator.sasaran'])
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function adminDashboard(Request $request): View
    {
        $filters = $request->validate([
            'tahun_anggaran_id' => ['nullable', 'integer', 'exists:tahun_anggarans,id'],
            'sekolah_id' => ['nullable', 'integer', 'exists:sekolahs,id'],
        ]);

        $tahuns = TahunAnggaran::query()->orderByDesc('tahun')->get();
        $tahun = isset($filters['tahun_anggaran_id'])
            ? $tahuns->firstWhere('id', $filters['tahun_anggaran_id'])
            : $tahuns->first();
        $sekolahs = Sekolah::query()->orderBy('nama_sekolah')->get();
        $sekolahId = $filters['sekolah_id'] ?? null;

        $programs = $tahun
            ? $tahun->masterPrograms()
                ->withSum('kegiatans', 'anggaran')
                ->withCount('kegiatans')
                ->orderBy('nama_program')
                ->get()
                ->map(fn ($program) => [
                    'name' => $program->nama_program,
                    'anggaran' => (float) ($program->kegiatans_sum_anggaran ?? 0),
                ])
            : collect();

        $guruQuery = User::query()->where('role', 'guru')->when(
            $sekolahId,
            fn ($query) => $query->where('sekolah_id', $sekolahId)
        );
        $guruCount = (clone $guruQuery)->count();

        $realisasiQuery = RealisasiPerkin::query()
            ->when(
                $tahun,
                fn ($query) => $query->whereHas(
                    'indikator.sasaran',
                    fn ($sasaranQuery) => $sasaranQuery->where('tahun_anggaran_id', $tahun->id)
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->whereHas('user', fn ($query) => $query->where('role', 'guru')->when(
                $sekolahId,
                fn ($userQuery) => $userQuery->where('sekolah_id', $sekolahId)
            ));

        $submittedGuruCount = (clone $realisasiQuery)->distinct()->count('user_id');
        $evidenceCount = (clone $realisasiQuery)->count();
        $approvedEvidenceCount = (clone $realisasiQuery)->where('status_verifikasi', 'approved')->count();
        $revisionEvidenceCount = (clone $realisasiQuery)->where('status_verifikasi', 'rejected')->count();
        $pendingEvidenceCount = (clone $realisasiQuery)->where('status_verifikasi', 'pending')->count();
        $totalBudget = $programs->sum('anggaran');
        $activityCount = $tahun ? $tahun->masterPrograms()->withCount('kegiatans')->get()->sum('kegiatans_count') : 0;
        $budgetLabel = $totalBudget >= 1_000_000_000
            ? 'Rp ' . number_format($totalBudget / 1_000_000_000, 2, ',', '.') . ' M'
            : 'Rp ' . number_format($totalBudget / 1_000_000, 2, ',', '.') . ' jt';

        $schoolCompliance = Sekolah::query()
            ->when($sekolahId, fn ($query) => $query->whereKey($sekolahId))
            ->withCount('guru')
            ->withCount(['guru as submitted_guru_count' => fn ($query) => $query->when(
                $tahun,
                fn ($guruQuery) => $guruQuery->whereHas(
                    'realisasins.indikator.sasaran',
                    fn ($sasaranQuery) => $sasaranQuery->where('tahun_anggaran_id', $tahun->id)
                ),
                fn ($guruQuery) => $guruQuery->whereRaw('1 = 0')
            )])
            ->orderBy('nama_sekolah')
            ->get()
            ->map(fn ($sekolah) => [
                'school' => $sekolah->nama_sekolah,
                'value' => $sekolah->guru_count > 0
                    ? round($sekolah->submitted_guru_count / $sekolah->guru_count * 100)
                    : 0,
            ]);

        $metrics = [
            [
                'label' => 'Total Pagu Anggaran',
                'value' => $budgetLabel,
                'sub' => $programs->count() . ' Program · ' . $activityCount . ' Kegiatan',
                'icon' => 'wallet',
                'tone' => 'brand',
            ],
            [
                'label' => 'Progres Submit Perkin Guru',
                'value' => $submittedGuruCount . ' / ' . $guruCount,
                'sub' => ($guruCount > 0 ? round($submittedGuruCount / $guruCount * 100) : 0) . '% sudah submit',
                'icon' => 'users',
                'tone' => 'success',
            ],
            [
                'label' => 'Status Approval Master Perkin',
                'value' => $tahun ? ucfirst($tahun->status_approval ?? 'draft') : 'Belum ada data',
                'sub' => $tahun ? 'Tahun ' . $tahun->tahun : 'Tahun anggaran belum tersedia',
                'icon' => 'shield',
                'tone' => 'warning',
            ],
            [
                'label' => 'Kepatuhan Eviden',
                'value' => $approvedEvidenceCount . ' / ' . $evidenceCount,
                'sub' => $revisionEvidenceCount . ' Butuh Revisi · ' . $pendingEvidenceCount . ' Menunggu',
                'icon' => 'check',
                'tone' => 'purple',
            ],
        ];

        return view('pages.dashboard.perencanaan-admin', compact(
            'tahuns',
            'tahun',
            'sekolahs',
            'sekolahId',
            'programs',
            'schoolCompliance',
            'metrics',
        ));
    }
}
