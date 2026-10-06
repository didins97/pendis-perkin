<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPerkin extends Model
{
    use HasFactory;

    protected $fillable = [
        'sasaran_kegiatan',
        'indikator_kinerja',
        'target_default',
        'satuan',
        'status_approval',
    ];
}
