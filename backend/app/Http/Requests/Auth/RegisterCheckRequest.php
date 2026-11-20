<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use App\Rules\StrongPassword;

// Step 1 of sign-up: account fields only. Nothing is saved.
class RegisterCheckRequest extends ApiFormRequest
{
    // Shared with RegisterRequest (step 2), so both steps check the same rules.
    public static function accountRules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            // No @ allowed: at login, a value with @ is treated as an email.
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9_.]+$/', 'unique:users,username'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{7,15}$/'],
            'password' => ['required', 'string', new StrongPassword],
        ];
    }

    public function rules(): array
    {
        return self::accountRules();
    }

    // Runs before the rules: store emails in lowercase and remove spaces around values.
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->email) ? strtolower(trim($this->email)) : $this->email,
            'username' => is_string($this->username) ? trim($this->username) : $this->username,
            'phone' => is_string($this->phone) ? trim($this->phone) : $this->phone,
        ]);
    }
}
