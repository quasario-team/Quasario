<?php

namespace App\Http\Requests\Auth;

use App\Rules\StrongPassword;

// Same email cleanup as ForgotPasswordRequest, plus token and the new password.
class ResetPasswordRequest extends ForgotPasswordRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'token' => ['required', 'string'],
            'password' => ['required', 'string', new StrongPassword],
        ];
    }
}
