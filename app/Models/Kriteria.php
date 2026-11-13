<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    protected $table = 'kriterias';

    protected $fillable = [
        'kode',
        'deskripsi',
        'enum',
    ];

    /**
     * Expose `nama` accessor backed by `deskripsi` to match the ERD schema.
     *
     * @return Attribute<string, never>
     */
    protected function nama(): Attribute
    {
        return Attribute::make(
            get: fn (): string => (string) $this->deskripsi,
        );
    }

    public function sesiKriterias(): HasMany
    {
        return $this->hasMany(SesiKriteria::class, 'kriterias_id');
    }
}
