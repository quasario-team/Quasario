<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileLocation extends Model
{
    protected $fillable = ['latitude', 'longitude', 'place_image', 'directions'];

    protected function casts(): array
    {
        // The database keeps decimals as text ("33.5138000"); we want real numbers.
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
