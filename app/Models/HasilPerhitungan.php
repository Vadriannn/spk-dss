<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilPerhitungan extends Model
{
    protected $table = 'hasil_perhitungans';

    protected $fillable = [
        'jarak_positif',
        'jarak_negatif',
        'nilai_preferensi',
        'ranking',
        'riwayat_perhitungans_id',
        'sesi_alternatif_id',
    ];

    protected function casts(): array
    {
        return [
            'jarak_positif' => 'float',
            'jarak_negatif' => 'float',
            'nilai_preferensi' => 'float',
            'ranking' => 'integer',
        ];
    }

    public function riwayatPerhitungan(): BelongsTo
    {
        return $this->belongsTo(RiwayatPerhitungan::class, 'riwayat_perhitungans_id');
    }

    public function sesiAlternatif(): BelongsTo
    {
        return $this->belongsTo(SesiAlternatif::class, 'sesi_alternatif_id');
    }
}

