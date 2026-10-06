<?php

namespace App\Http\Controllers;

use App\Models\RealisasiPerkin;
use App\Models\MasterIndikator;
use App\Models\Sekolah;
use App\Models\TahunAnggaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function pegawaiDashboard(): View
    {
        $pegawai = Auth::user();
        $tahunPerkin = \App\Models\TahunAnggaran::approved()->latest('tahun')->first();
        $pegawai->loadMissing(['sekolah', 'profilPegawai']);

        $profileFields = [
            $pegawai->name,
            $pegawai->email,
            $pegawai->nip,
            $pegawai->nomor_wa,
            $pegawai->sekolah_id,
            $pegawai->profilPegawai?->nuptk,
            $pegawai->profilPegawai?->nrg,
            $pegawai->profilPegawai?->pangkat_golongan,
            $pegawai->profilPegawai?->status_kepegawaian,
            $pegawai->profilPegawai?->jabatan,
            $pegawai->profilPegawai?->tugas_tambahan,
            $pegawai->profilPegawai?->berkas_sk_pangkat,
            $pegawai->profilPegawai?->berkas_sk_mengajar,
            $pegawai->profilPegawai?->berkas_serdik,
        ];
        $profileFieldsCompleted = count(array_filter($profileFields, fn ($value) => filled($value)));
        $profileCompletion = (int) round($profileFieldsCompleted / count($profileFields) * 100);

        return view('pages.dashboard.pegawai-self-service', [
            'pegawai' => $pegawai,
            'recentEvidence' => $pegawai->realisasins()
                ->with('indikator.sasaran')
                ->latest()
                ->limit(5)
                ->get(),
            'profileCompletion' => $profileCompletion,
            'profileFieldsCompleted' => $profileFieldsCompleted,
            'profileFieldsTotal' => count($profileFields),
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
                    'indicator' => 'Kolaborasi pengembangan kompetensi pegawai',
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

    public function pimpinanDashboard(Request $request): View
    {
        $filters = $request->validate([
            'sekolah_ids' => ['nullable', 'array'],
            'sekolah_ids.*' => ['integer', 'distinct', 'exists:sekolahs,id'],
        ]);

        $selectedSchoolIds = collect($filters['sekolah_ids'] ?? [])->map(fn ($id) => (int) $id)->all();
        $tahun = TahunAnggaran::approved()->latest('tahun')->first();
        $indicatorCount = $tahun
            ? MasterIndikator::query()
                ->whereHas('sasaran', fn ($query) => $query
                    ->where('tahun_anggaran_id', $tahun->id)
                    ->where('status_approval', 'approved'))
                ->count()
            : 0;

        $schools = Sekolah::query()
            ->withCount(['pegawai as active_pegawai_count' => fn ($query) => $query->where('status_aktif', true)])
            ->orderBy('nama_sekolah')
            ->get();
        $activePegawais = User::query()
            ->where('role', 'pegawai')
            ->where('status_aktif', true)
            ->with('sekolah:id,nama_sekolah')
            ->get(['id', 'name', 'email', 'nip', 'sekolah_id']);
        $uploadedIndicators = $tahun && $activePegawais->isNotEmpty()
            ? RealisasiPerkin::query()
                ->select('user_id')
                ->selectRaw('COUNT(DISTINCT master_indikator_id) as submitted_count')
                ->whereIn('user_id', $activePegawais->modelKeys())
                ->whereHas('indikator.sasaran', fn ($query) => $query
                    ->where('tahun_anggaran_id', $tahun->id)
                    ->where('status_approval', 'approved'))
                ->groupBy('user_id')
                ->pluck('submitted_count', 'user_id')
            : collect();

        $completionByPegawai = $activePegawais->mapWithKeys(function (User $pegawai) use ($uploadedIndicators, $indicatorCount) {
            $submittedCount = min((int) $uploadedIndicators->get($pegawai->id, 0), $indicatorCount);
            $percentage = $indicatorCount > 0 ? $submittedCount / $indicatorCount * 100 : 0;

            return [$pegawai->id => [
                'sekolah_id' => $pegawai->sekolah_id,
                'submitted_count' => $submittedCount,
                'percentage' => $percentage,
            ]];
        });

        $employeeCount = $activePegawais->count();
        $submittedSlots = $completionByPegawai->sum('submitted_count');
        $possibleSlots = $employeeCount * $indicatorCount;
        $overallCompletion = $possibleSlots > 0 ? (int) round($submittedSlots / $possibleSlots * 100) : 0;

        $complianceDistribution = [
            'complete' => $completionByPegawai->where('percentage', 100)->count(),
            'progress' => $completionByPegawai->filter(fn ($pegawai) => $pegawai['percentage'] >= 30 && $pegawai['percentage'] <= 70)->count(),
            'not_started' => $indicatorCount > 0 ? $completionByPegawai->where('submitted_count', 0)->count() : 0,
            'other_progress' => $completionByPegawai->filter(fn ($pegawai) => $pegawai['percentage'] > 0
                && $pegawai['percentage'] < 100
                && ($pegawai['percentage'] < 30 || $pegawai['percentage'] > 70))->count(),
        ];
        $redZonePegawais = $indicatorCount > 0
            ? $activePegawais
                ->filter(fn (User $pegawai) => $completionByPegawai->get($pegawai->id)['submitted_count'] === 0)
                ->sortBy('name')
                ->values()
            : collect();

        $schoolCompliance = $schools
            ->filter(fn ($school) => empty($selectedSchoolIds) || in_array($school->id, $selectedSchoolIds, true))
            ->map(function (Sekolah $school) use ($completionByPegawai, $indicatorCount) {
                $schoolPegawais = $completionByPegawai->filter(fn ($pegawai) => $pegawai['sekolah_id'] === $school->id);
                $schoolPossibleSlots = $schoolPegawais->count() * $indicatorCount;
                $schoolSubmittedSlots = $schoolPegawais->sum('submitted_count');

                return [
                    'id' => $school->id,
                    'school' => $school->nama_sekolah,
                    'value' => $schoolPossibleSlots > 0
                        ? round($schoolSubmittedSlots / $schoolPossibleSlots * 100, 1)
                        : 0,
                    'employees' => $schoolPegawais->count(),
                ];
            })
            ->values();

        return view('pages.dashboard.pimpinan', [
            'activePegawaiCount' => $employeeCount,
            'overallCompletion' => $overallCompletion,
            'schoolCount' => $schools->count(),
            'redZoneCount' => $redZonePegawais->count(),
            'redZonePegawais' => $redZonePegawais,
            'complianceDistribution' => $complianceDistribution,
            'schoolCompliance' => $schoolCompliance,
            'schools' => $schools,
            'selectedSchoolIds' => $selectedSchoolIds,
            'tahun' => $tahun,
            'indicatorCount' => $indicatorCount,
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
            : TahunAnggaran::approved()->latest('tahun')->first();
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

        $pegawaiQuery = User::query()->where('role', 'pegawai')->when(
            $sekolahId,
            fn ($query) => $query->where('sekolah_id', $sekolahId)
        );
        $pegawaiCount = (clone $pegawaiQuery)->count();

        $realisasiQuery = RealisasiPerkin::query()
            ->when(
                $tahun,
                fn ($query) => $query->whereHas(
                    'indikator.sasaran',
                    fn ($sasaranQuery) => $sasaranQuery->where('tahun_anggaran_id', $tahun->id)
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->whereHas('user', fn ($query) => $query->where('role', 'pegawai')->when(
                $sekolahId,
                fn ($userQuery) => $userQuery->where('sekolah_id', $sekolahId)
            ));

        $submittedPegawaiCount = (clone $realisasiQuery)->distinct()->count('user_id');
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
            ->withCount('pegawai')
            ->withCount(['pegawai as submitted_pegawai_count' => fn ($query) => $query->when(
                $tahun,
                fn ($pegawaiQuery) => $pegawaiQuery->whereHas(
                    'realisasins.indikator.sasaran',
                    fn ($sasaranQuery) => $sasaranQuery->where('tahun_anggaran_id', $tahun->id)
                ),
                fn ($pegawaiQuery) => $pegawaiQuery->whereRaw('1 = 0')
            )])
            ->orderBy('nama_sekolah')
            ->get()
            ->map(fn ($sekolah) => [
                'school' => $sekolah->nama_sekolah,
                'value' => $sekolah->pegawai_count > 0
                    ? round($sekolah->submitted_pegawai_count / $sekolah->pegawai_count * 100)
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
                'label' => 'Progres Submit Perkin Pegawai',
                'value' => $submittedPegawaiCount . ' / ' . $pegawaiCount,
                'sub' => ($pegawaiCount > 0 ? round($submittedPegawaiCount / $pegawaiCount * 100) : 0) . '% sudah submit',
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
