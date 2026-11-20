<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{
    // At most 30 pictures in one gallery (keeps the disk and the page small).
    public const MAX_PER_PROFILE = 30;

    protected $fillable = ['image_path', 'description'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
