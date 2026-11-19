<?php

namespace App\Http\Requests;

use App\Rules\StrongPassword;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

// Every API request class extends this one, so all 422 errors have the same shape:
// {"message": "...", "code": "validationFailed", "errors": {"field": ["requiredField"]}}
abstract class ApiFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = [];

        foreach ($validator->failed() as $field => $failedRules) {
            $errors[$field] = [$this->errorCodeFor(array_key_first($failedRules))];
        }

        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'code' => 'validationFailed',
            'errors' => $errors,
        ], 422));
    }

    private function errorCodeFor(string $rule): string
    {
        return match ($rule) {
            'Required' => 'requiredField',
            'Unique' => 'alreadyUsed',
            StrongPassword::class => 'weakPassword',
            default => 'invalidField',
        };
    }
}
