<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndikatorKinerja extends Model
{
    use HasFactory;

    protected $table = 'master_indikators';

    protected $fillable = ['sasaran_id', 'kode_sub', 'indikator_kinerja', 'target_default', 'satuan'];

    public function sasaran(): BelongsTo
    {
        return $this->belongsTo(SasaranKinerja::class, 'sasaran_id', 'id');
    }
}
