<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rule;

// Step 2 of sign-up: account fields again + the first profile fields.
// Extends RegisterCheckRequest, so it also lowercases the email.
class RegisterRequest extends RegisterCheckRequest
{
    public function rules(): array
    {
        return [
            ...self::accountRules(),
            'job_category_id' => ['required', 'integer', 'exists:job_categories,id'],
            // The title must belong to the chosen category.
            'job_title_id' => [
                'required',
                'integer',
                Rule::exists('job_titles', 'id')->where('job_category_id', $this->input('job_category_id')),
            ],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'experience' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
