<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterKegiatan extends Model
{
    use HasFactory;

    protected $table = 'master_kegiatans';

    protected $fillable = [
        'program_id',
        'kode_kegiatan',
        'nama_kegiatan',
        'anggaran',
    ];

    protected $casts = [
        'anggaran' => 'decimal:2',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(MasterProgram::class, 'program_id');
    }

    public function getAnggaranRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->anggaran, 2, ',', '.');
    }
}
