<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiPerkin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'master_indikator_id',
        'realisasi_capaian',
        'file_eviden',
        'catatan_guru',
        'status_verifikasi',
        'catatan_verifikator',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // Relasi ke pegawai yang mengunggah eviden.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Indikator yang dipilih
    public function indikator(): BelongsTo
    {
        return $this->belongsTo(MasterIndikator::class, 'master_indikator_id');
    }

    // Relasi ke Verifikator / Atasan
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
