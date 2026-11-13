<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SesiKeputusan extends Model
{
    protected $table = 'sesi_keputusans';

    protected $fillable = [
        'nama',
        'deskripsi',
        'status',
        'users_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function sesiKriterias(): HasMany
    {
        return $this->hasMany(SesiKriteria::class, 'sesi_keputusans_id');
    }

    public function sesiAlternatifs(): HasMany
    {
        return $this->hasMany(SesiAlternatif::class, 'sesi_keputusans_id');
    }

    public function riwayatPerhitungans(): HasMany
    {
        return $this->hasMany(RiwayatPerhitungan::class, 'sesi_keputusans_id');
    }

    public function latestRiwayat(): HasOne
    {
        return $this->hasOne(RiwayatPerhitungan::class, 'sesi_keputusans_id')->latestOfMany('id');
    }
}

