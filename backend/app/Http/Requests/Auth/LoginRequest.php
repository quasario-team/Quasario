<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;

class LoginRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            // Email or username.
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }
}
