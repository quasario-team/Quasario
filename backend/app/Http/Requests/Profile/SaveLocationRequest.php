<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiFormRequest;

// POST /me/profiles/{profile}/location : add or change the place of work.
class SaveLocationRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            // A point on the map. Latitude goes from -90 to 90, longitude from -180 to 180.
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'directions' => ['nullable', 'string', 'max:1000'],
            // A picture of the door or the street (optional).
            'place_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            // Send 1 to remove the old picture without choosing a new one.
            'remove_place_image' => ['sometimes', 'boolean'],
        ];
    }
}
