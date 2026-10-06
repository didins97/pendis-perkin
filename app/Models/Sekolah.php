<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    protected $fillable = ['npsn', 'nama_sekolah', 'alamat'];

    public function guru(): HasMany
    {
        return $this->hasMany(User::class, 'sekolah_id')->where('role', 'guru');
    }
}
