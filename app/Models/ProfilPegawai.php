<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilPegawai extends Model
{
    protected $table = 'profil_pegawai';

    protected $fillable = [
        'user_id',
        'nuptk',
        'nrg',
        'pangkat_golongan',
        'status_kepegawaian',
        'jabatan',
        'tugas_tambahan',
        'berkas_sk_pangkat',
        'berkas_sk_mengajar',
        'berkas_serdik',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
