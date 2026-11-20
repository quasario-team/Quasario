<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiFormRequest;

// POST /me/profiles/{profile}/images : the profile picture, the cover picture, or both.
// Each one: a real picture (jpg, png or webp), at most 2 MB (2048 KB).
class UploadProfileImagesRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $picture = ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];

        return [
            // At least one of the two must be sent.
            'profile_image' => ['required_without:cover_image', ...$picture],
            'cover_image' => ['required_without:profile_image', ...$picture],
        ];
    }
}
