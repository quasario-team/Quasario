<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'display_name' => $this->display_name,
            'job_title' => [
                'id' => $this->jobTitle->id,
                'name' => $this->jobTitle->name,
                'job_category' => [
                    'id' => $this->jobTitle->jobCategory->id,
                    'name' => $this->jobTitle->jobCategory->name,
                ],
            ],
            'city' => ['id' => $this->city->id, 'name' => $this->city->name],
            // The database keeps only the file path (profiles/7/abc.png);
            // the browser needs the address: /storage/profiles/7/abc.png
            'profile_image' => $this->profile_image ? '/storage/' . $this->profile_image : null,
            'cover_image' => $this->cover_image ? '/storage/' . $this->cover_image : null,
            'bio' => $this->bio,
            'study' => $this->study,
            'experience' => $this->experience,
            'is_active' => $this->is_active,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->verified_at,
        ];
    }
}
