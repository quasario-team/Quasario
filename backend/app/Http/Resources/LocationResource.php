<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            // The browser address of the file, or null (same idea as ProfileResource).
            'place_image' => $this->place_image ? '/storage/' . $this->place_image : null,
            'directions' => $this->directions,
            'updated_at' => $this->updated_at,
        ];
    }
}
