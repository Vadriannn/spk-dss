<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPerhitungan extends Model
{
    protected $table = 'detail_perhitungans';

    protected $fillable = [
        'nilai_awal',
        'nilai_normalisasi',
        'nilai_terbobot',
        'riwayat_perhitungans_id',
        'sesi_kriterias_id',
        'sesi_alternatif_id',
    ];

    protected function casts(): array
    {
        return [
            'nilai_awal' => 'float',
            'nilai_normalisasi' => 'float',
            'nilai_terbobot' => 'float',
        ];
    }

    public function riwayatPerhitungan(): BelongsTo
    {
        return $this->belongsTo(RiwayatPerhitungan::class, 'riwayat_perhitungans_id');
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

