<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

// "Add a profile" (POST /me/profiles). Same job rules as sign-up:
// the title must belong to the chosen category.
class StoreProfileRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:100'],
            'job_category_id' => ['required', 'integer', 'exists:job_categories,id'],
            'job_title_id' => [
                'required',
                'integer',
                Rule::exists('job_titles', 'id')->where('job_category_id', $this->input('job_category_id')),
            ],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'study' => ['nullable', 'string', 'max:2000'],
            'experience' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
