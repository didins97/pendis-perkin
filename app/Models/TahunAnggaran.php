<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TahunAnggaran extends Model
{
    use HasFactory;

    protected $fillable = ['tahun', 'status', 'status_approval', 'approved_by', 'approved_at', 'catatan_approval'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status_approval', 'approved');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sasarans(): HasMany
    {
        return $this->hasMany(SasaranKinerja::class, 'tahun_anggaran_id');
    }

    public function masterPrograms(): HasMany
    {
        return $this->hasMany(MasterProgram::class, 'tahun_anggaran_id');
    }
}
