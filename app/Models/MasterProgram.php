<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterProgram extends Model
{
    use HasFactory;

    protected $table = 'master_programs';

    protected $fillable = [
        'tahun_anggaran_id',
        'kode_program',
        'nama_program',
        'created_by',
    ];

    protected $appends = ['total_anggaran'];

    public function tahunAnggaran(): BelongsTo
    {
        return $this->belongsTo(TahunAnggaran::class, 'tahun_anggaran_id');
    }

    public function kegiatans(): HasMany
    {
        return $this->hasMany(MasterKegiatan::class, 'program_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalAnggaranAttribute(): float
    {
        return (float) $this->kegiatans()->sum('anggaran');
    }
}
