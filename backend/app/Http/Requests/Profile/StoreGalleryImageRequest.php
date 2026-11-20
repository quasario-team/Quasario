<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiFormRequest;

// POST /me/profiles/{profile}/gallery : one picture of my work, with an optional description.
class StoreGalleryImageRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
