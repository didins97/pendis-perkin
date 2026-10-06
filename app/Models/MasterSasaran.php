<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterSasaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_anggaran_id',
        'tahun_anggaran',
        'no_urut',
        'sasaran_kegiatan',
        'status_approval',
        'catatan_pimpinan',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tahun_anggaran' => 'integer',
        'no_urut' => 'integer',
        'approved_at' => 'datetime',
    ];

    // Relasi One-to-Many ke Indikator (1 Sasaran punya banyak Indikator)
    public function indikators(): HasMany
    {
        return $this->hasMany(IndikatorKinerja::class, 'sasaran_id');
    }

    // Relasi ke Admin pembuat
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke Pimpinan penyetuju
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope query untuk memfilter perkin yang sudah approved
    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved');
    }
}
