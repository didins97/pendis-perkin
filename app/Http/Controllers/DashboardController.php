<?php

namespace App\Http\Controllers;

use App\Models\MasterIndikator;
use App\Models\RealisasiPerkin;
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
        $tahunPerkin = TahunAnggaran::approved()->latest('tahun')->first();
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

    public function adminDashboard(): View
    {
        $tahun = TahunAnggaran::approved()->latest('tahun')->first();

        $activePegawais = User::query()
            ->where('role', 'pegawai')
            ->where('status_aktif', true)
            ->with('sekolah:id,nama_sekolah')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'nip', 'nomor_wa', 'sekolah_id']);

        $indikators = $tahun && $tahun->status_approval === 'approved'
            ? MasterIndikator::query()
                ->with('sasaran:id,no_urut,sasaran_kegiatan')
                ->whereHas('sasaran', fn ($query) => $query
                    ->where('tahun_anggaran_id', $tahun->id)
                    ->where('status_approval', 'approved'))
                ->orderBy('sasaran_id')
                ->orderBy('kode_sub')
                ->orderBy('id')
                ->get()
            : collect();

        $indicatorIds = $indikators->pluck('id')->all();
        $pegawaiIds = $activePegawais->modelKeys();
        $indicatorUploads = $indicatorIds && $pegawaiIds
            ? RealisasiPerkin::query()
                ->select('master_indikator_id')
                ->selectRaw('COUNT(DISTINCT user_id) as submitted_count')
                ->whereIn('master_indikator_id', $indicatorIds)
                ->whereIn('user_id', $pegawaiIds)
                ->groupBy('master_indikator_id')
                ->pluck('submitted_count', 'master_indikator_id')
            : collect();
        $pegawaiUploads = $indicatorIds && $pegawaiIds
            ? RealisasiPerkin::query()
                ->select('user_id')
                ->selectRaw('COUNT(DISTINCT master_indikator_id) as submitted_count')
                ->whereIn('master_indikator_id', $indicatorIds)
                ->whereIn('user_id', $pegawaiIds)
                ->groupBy('user_id')
                ->pluck('submitted_count', 'user_id')
            : collect();

        $indicatorCount = $indikators->count();
        $pegawaiCount = $activePegawais->count();
        $totalQuota = $pegawaiCount * $indicatorCount;
        $completedSlots = (int) $indicatorUploads->sum();
        $completionPercentage = $totalQuota > 0
            ? ($completedSlots === $totalQuota
                ? 100
                : min(99, (int) round($completedSlots / $totalQuota * 100)))
            : 0;

        $redZonePegawais = $indicatorCount > 0
            ? $activePegawais
                ->filter(fn (User $pegawai) => (int) $pegawaiUploads->get($pegawai->id, 0) === 0)
                ->values()
            : collect();
        $connectedSchoolCount = Sekolah::query()
            ->whereHas('pegawai', fn ($query) => $query->where('status_aktif', true))
            ->count();

        return view('pages.dashboard.perencanaan-admin', compact(
            'tahun',
            'pegawaiCount',
            'totalQuota',
            'completedSlots',
            'completionPercentage',
            'connectedSchoolCount',
            'redZonePegawais',
            'indicatorCount',
        ));
    }

    public function adminMonitoringProgres(Request $request): View
    {
        $filters = $request->validate([
            'sekolah_id' => ['nullable', 'integer', 'exists:sekolahs,id'],
            'status' => ['nullable', 'in:all,complete,progress,critical'],
        ]);

        $sekolahId = $filters['sekolah_id'] ?? null;
        $statusFilter = $filters['status'] ?? 'all';
        $tahun = TahunAnggaran::approved()->latest('tahun')->first();
        $sekolahs = Sekolah::query()->orderBy('nama_sekolah')->get();
        $pegawai = User::query()
            ->where('role', 'pegawai')
            ->where('status_aktif', true)
            ->when($sekolahId, fn ($query) => $query->where('sekolah_id', $sekolahId))
            ->with('sekolah:id,nama_sekolah')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'nip', 'sekolah_id']);
        $indikators = $tahun
            ? MasterIndikator::query()
                ->with('sasaran:id,no_urut,sasaran_kegiatan')
                ->whereHas('sasaran', fn ($query) => $query
                    ->where('tahun_anggaran_id', $tahun->id)
                    ->where('status_approval', 'approved'))
                ->orderBy('sasaran_id')
                ->orderBy('kode_sub')
                ->orderBy('id')
                ->get()
            : collect();

        $indicatorIds = $indikators->pluck('id')->all();
        $pegawaiIds = $pegawai->modelKeys();
        $uploadsByIndicator = $indicatorIds && $pegawaiIds
            ? RealisasiPerkin::query()
                ->whereIn('master_indikator_id', $indicatorIds)
                ->whereIn('user_id', $pegawaiIds)
                ->get(['master_indikator_id', 'user_id'])
                ->groupBy('master_indikator_id')
                ->map(fn ($uploads) => $uploads->pluck('user_id')->unique()->map(fn ($id) => (int) $id)->values())
            : collect();

        $indicatorGroups = $indikators
            ->map(function (MasterIndikator $indikator) use ($pegawai, $uploadsByIndicator) {
                $uploadedIds = $uploadsByIndicator->get($indikator->id, collect());
                $uploadedPegawai = $pegawai->whereIn('id', $uploadedIds)->values();
                $missingPegawai = $pegawai->whereNotIn('id', $uploadedIds)->values();
                $percentage = $pegawai->isNotEmpty()
                    ? ($uploadedPegawai->count() === $pegawai->count()
                        ? 100
                        : min(99, (int) round($uploadedPegawai->count() / $pegawai->count() * 100)))
                    : 0;

                return [
                    'id' => $indikator->id,
                    'sasaran_id' => $indikator->sasaran_id,
                    'no_urut' => $indikator->sasaran->no_urut,
                    'sasaran' => $indikator->sasaran->sasaran_kegiatan,
                    'name' => $indikator->indikator_kinerja,
                    'percentage' => $percentage,
                    'uploaded_count' => $uploadedPegawai->count(),
                    'pegawai_count' => $pegawai->count(),
                    'uploaded_pegawai' => $uploadedPegawai,
                    'missing_pegawai' => $missingPegawai,
                ];
            })
            ->when($statusFilter !== 'all', fn ($items) => $items->filter(fn ($item) => match ($statusFilter) {
                'complete' => $item['percentage'] === 100,
                'progress' => $item['percentage'] > 0 && $item['percentage'] < 100,
                'critical' => $pegawai->isNotEmpty() && $item['percentage'] === 0,
            }))
            ->groupBy('sasaran_id')
            ->map(fn ($items) => [
                'no_urut' => $items->first()['no_urut'],
                'sasaran' => $items->first()['sasaran'],
                'indikators' => $items->values(),
            ])
            ->values();

        return view('pages.admin.monitoring-progres-indikator', [
            'tahun' => $tahun,
            'sekolahs' => $sekolahs,
            'sekolahId' => $sekolahId,
            'statusFilter' => $statusFilter,
            'pegawaiCount' => $pegawai->count(),
            'indicatorGroups' => $indicatorGroups,
            'indicatorCount' => $indikators->count(),
        ]);
    }
}
