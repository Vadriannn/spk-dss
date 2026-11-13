<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    protected $table = 'kriterias';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'enum',
        'bobot_default',
    ];

    protected function casts(): array
    {
        return [
            'bobot_default' => 'float',
        ];
    }

    public function sesiKriterias(): HasMany
    {
        return $this->hasMany(SesiKriteria::class, 'kriterias_id');
    }
}

