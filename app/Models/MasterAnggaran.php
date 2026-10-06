<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterAnggaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_anggaran_id',
        'kode_program',
        'nama_program',
        'kode_kegiatan',
        'nama_kegiatan',
        'anggaran',
        'created_by',
    ];

    protected $casts = [
        'anggaran' => 'decimal:2',
    ];

    public function tahunAnggaran(): BelongsTo
    {
        return $this->belongsTo(TahunAnggaran::class, 'tahun_anggaran_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
