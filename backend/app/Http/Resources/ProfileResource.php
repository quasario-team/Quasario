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
            'profile_image' => $this->profile_image,
            'cover_image' => $this->cover_image,
            'bio' => $this->bio,
            'study' => $this->study,
            'experience' => $this->experience,
            'is_active' => $this->is_active,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->verified_at,
        ];
    }
}
