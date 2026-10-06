<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SasaranKinerja extends Model
{
    use HasFactory;

    protected $table = 'master_sasarans';

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
        'no_urut' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function tahunAnggaran(): BelongsTo
    {
        return $this->belongsTo(TahunAnggaran::class, 'tahun_anggaran_id');
    }

    public function indikators(): HasMany
    {
        return $this->hasMany(IndikatorKinerja::class, 'sasaran_id', 'id');
    }
}
