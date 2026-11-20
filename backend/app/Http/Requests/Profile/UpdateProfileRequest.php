<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

// "Edit a profile" (PATCH /me/profiles/{id}). Every field is optional:
// only the sent fields change. To change the job, send the category AND the title.
class UpdateProfileRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'display_name' => ['sometimes', 'required', 'string', 'max:100'],
            'job_category_id' => ['required_with:job_title_id', 'integer', 'exists:job_categories,id'],
            'job_title_id' => [
                'required_with:job_category_id',
                'integer',
                Rule::exists('job_titles', 'id')->where('job_category_id', $this->input('job_category_id')),
            ],
            'city_id' => ['sometimes', 'required', 'integer', 'exists:cities,id'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'study' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'experience' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
