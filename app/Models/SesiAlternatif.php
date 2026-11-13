<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiAlternatif extends Model
{
    public $timestamps = false;

    protected $table = 'sesi_alternatifs';

    protected $fillable = [
        'sesi_keputusans_id',
        'alternatifs_id',
    ];

    public function sesiKeputusan(): BelongsTo
    {
        return $this->belongsTo(SesiKeputusan::class, 'sesi_keputusans_id');
    }

    public function alternatif(): BelongsTo
    {
        return $this->belongsTo(Alternatif::class, 'alternatifs_id');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'sesi_alternatif_id');
    }

    public function detailPerhitungans(): HasMany
    {
        return $this->hasMany(DetailPerhitungan::class, 'sesi_alternatif_id');
    }

    public function hasilPerhitungans(): HasMany
    {
        return $this->hasMany(HasilPerhitungan::class, 'sesi_alternatif_id');
    }
}
