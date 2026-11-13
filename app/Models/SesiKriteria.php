<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiKriteria extends Model
{
    public $timestamps = false;

    protected $table = 'sesi_kriterias';

    protected $fillable = [
        'bobot',
        'tipe',
        'sesi_keputusans_id',
        'kriterias_id',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'float',
        ];
    }

    public function sesiKeputusan(): BelongsTo
    {
        return $this->belongsTo(SesiKeputusan::class, 'sesi_keputusans_id');
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriterias_id');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'sesi_kriterias_id');
    }

    public function idealPerhitungans(): HasMany
    {
        return $this->hasMany(IdealPerhitungan::class, 'sesi_kriterias_id');
    }

    public function detailPerhitungans(): HasMany
    {
        return $this->hasMany(DetailPerhitungan::class, 'sesi_kriterias_id');
    }
}
