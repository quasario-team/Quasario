<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\RoutesNotifications;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, RoutesNotifications;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_BANNED = 'banned';

    // role_id and status are NOT fillable: nobody can make himself admin or unban himself from a form.
    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // An account has 1..5 profiles (admin has none). Profile model comes in a later step.
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function activeProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('is_active', true);
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === Role::ADMIN;
    }

    public function isBanned(): bool
    {
        return $this->status === self::STATUS_BANNED;
    }
}
