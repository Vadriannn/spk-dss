<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    public $timestamps = false;

    protected $table = 'penilaians';

    protected $fillable = [
        'nilai',
        'sesi_kriterias_id',
        'sesi_alternatif_id',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'float',
        ];
    }

    public function sesiKriteria(): BelongsTo
    {
        return $this->belongsTo(SesiKriteria::class, 'sesi_kriterias_id');
    }

    public function sesiAlternatif(): BelongsTo
    {
        return $this->belongsTo(SesiAlternatif::class, 'sesi_alternatif_id');
    }
}
