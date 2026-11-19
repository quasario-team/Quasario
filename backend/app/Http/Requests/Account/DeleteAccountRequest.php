<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\ApiFormRequest;

// Deleting the account needs the password, so a stolen open session cannot do it alone.
class DeleteAccountRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'current_password:sanctum'],
        ];
    }
}
