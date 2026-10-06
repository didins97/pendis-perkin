<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterIndikator extends Model
{
    use HasFactory;

    protected $fillable = [
        'sasaran_id',
        'kode_sub',
        'indikator_kinerja',
        'target_default',
        'satuan',
    ];

    // Relasi ke Parent Sasaran Kegiatan
    public function sasaran(): BelongsTo
    {
        return $this->belongsTo(SasaranKinerja::class, 'sasaran_id');
    }

    // Relasi ke eviden realisasi yang diunggah pegawai.
    public function realisasis(): HasMany
    {
        return $this->hasMany(RealisasiPerkin::class, 'master_indikator_id');
    }

    // Accessor / Helper untuk menampilkan label lengkap (contoh: "26.b Persentase...")
    public function getKodeLengkapAttribute(): string
    {
        $noUrut = $this->sasaran->no_urut ?? '';
        $sub = $this->kode_sub ? ".{$this->kode_sub}" : '';
        return trim("{$noUrut}{$sub}");
    }
}
