<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\ApiFormRequest;
use App\Rules\StrongPassword;

class ChangePasswordRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password:sanctum'],
            'password' => ['required', 'string', new StrongPassword],
        ];
    }
}
