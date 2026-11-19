<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

// Every field is optional: send only what changes. Same rules as sign-up.
class UpdateAccountRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'username' => ['sometimes', 'required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9_.]+$/', Rule::unique('users', 'username')->ignore($userId)],
            'phone' => ['sometimes', 'required', 'string', 'regex:/^\+?[0-9]{7,15}$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->email)) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }
        if (is_string($this->username)) {
            $this->merge(['username' => trim($this->username)]);
        }
    }
}
