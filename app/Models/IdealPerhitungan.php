<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdealPerhitungan extends Model
{
    protected $table = 'ideal_perhitungans';

    protected $fillable = [
        'ideal_positif',
        'ideal_negatif',
        'sesi_kriterias_id',
        'riwayat_perhitungans_id',
    ];

    protected function casts(): array
    {
        return [
            'ideal_positif' => 'float',
            'ideal_negatif' => 'float',
        ];
    }

    public function sesiKriteria(): BelongsTo
    {
        return $this->belongsTo(SesiKriteria::class, 'sesi_kriterias_id');
    }

    public function riwayatPerhitungan(): BelongsTo
    {
        return $this->belongsTo(RiwayatPerhitungan::class, 'riwayat_perhitungans_id');
    }
}

