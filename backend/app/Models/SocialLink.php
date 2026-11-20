<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialLink extends Model
{
    // The 9 platforms of the ERD. The migration, the validation and the
    // tests all read this one list, so the names are the same everywhere.
    public const PLATFORMS = [
        'whatsapp',
        'facebook',
        'linkedin',
        'youtube',
        'x',
        'instagram',
        'telegram',
        'github',
        'website',
    ];

    // The web addresses each platform accepts (the rule PlatformUrl reads this list).
    // A sub-domain is accepted too: m.facebook.com, www.instagram.com ...
    // null = any address (a personal website).
    public const HOSTS = [
        'whatsapp' => ['wa.me', 'whatsapp.com'],
        'facebook' => ['facebook.com', 'fb.com', 'fb.me'],
        'linkedin' => ['linkedin.com'],
        'youtube' => ['youtube.com', 'youtu.be'],
        'x' => ['x.com', 'twitter.com'],
        'instagram' => ['instagram.com'],
        'telegram' => ['t.me', 'telegram.me'],
        'github' => ['github.com'],
        'website' => null,
    ];

    protected $fillable = ['platform', 'url'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
