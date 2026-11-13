<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RiwayatPerhitungan extends Model
{
    protected $table = 'riwayat_perhitungans';

    protected $fillable = [
        'waktu',
        'users_id',
        'sesi_keputusans_id',
    ];

    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function sesiKeputusan(): BelongsTo
    {
        return $this->belongsTo(SesiKeputusan::class, 'sesi_keputusans_id');
    }

    public function idealPerhitungans(): HasMany
    {
        return $this->hasMany(IdealPerhitungan::class, 'riwayat_perhitungans_id');
    }

    public function detailPerhitungans(): HasMany
    {
        return $this->hasMany(DetailPerhitungan::class, 'riwayat_perhitungans_id');
    }

    public function hasilPerhitungans(): HasMany
    {
        return $this->hasMany(HasilPerhitungan::class, 'riwayat_perhitungans_id');
    }
}

