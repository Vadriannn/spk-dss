<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nama', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Map `name` transparently to `nama` for compatibility with Laravel helpers.
     *
     * @return Attribute<string|null, string>
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes): ?string => $attributes['nama'] ?? null,
            set: fn (string $value): array => ['nama' => $value],
        );
    }

    public function sesiKeputusans(): HasMany
    {
        return $this->hasMany(SesiKeputusan::class, 'users_id');
    }

    public function riwayatPerhitungans(): HasMany
    {
        return $this->hasMany(RiwayatPerhitungan::class, 'users_id');
    }
}
