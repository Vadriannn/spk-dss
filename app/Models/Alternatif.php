<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alternatif extends Model
{
    protected $table = 'alternatifs';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
    ];

    public function sesiAlternatifs(): HasMany
    {
        return $this->hasMany(SesiAlternatif::class, 'alternatifs_id');
    }
}

