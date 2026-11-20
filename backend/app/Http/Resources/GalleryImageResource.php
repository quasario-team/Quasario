<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // The browser address of the file (same idea as ProfileResource).
            'image' => '/storage/' . $this->image_path,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
