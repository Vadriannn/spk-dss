<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;

class SesiKeputusan extends Model
{
    protected $table = 'sesi_keputusans';

    protected $fillable = [
        'nama',
        'deskripsi',
        'status',
        'users_id',
    ];

    /**
     * Return the active decision session, or create a default one if none exists.
     */
    public static function current(): self
    {
        $sesi = self::where('status', 'aktif')->orderBy('id')->first()
            ?? self::orderBy('id')->first();

        if ($sesi) {
            return $sesi;
        }

        $user = User::first() ?? User::create([
            'nama' => 'Admin Faency.id',
            'email' => 'admin@faency.id',
            'password' => Hash::make('password'),
        ]);

        return self::create([
            'nama' => 'Evaluasi Supplier Pakaian Thrift Faency.id',
            'deskripsi' => 'Sesi penilaian alternatif supplier terhadap kriteria (Tabel A).',
            'status' => 'aktif',
            'users_id' => $user->id,
        ]);
    }

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
