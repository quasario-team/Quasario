<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

// At least 8 characters, with at least one letter and one digit. Error code: weakPassword.
class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isStrong = is_string($value)
            && strlen($value) >= 8
            && preg_match('/[A-Za-z]/', $value)
            && preg_match('/[0-9]/', $value);

        if (! $isStrong) {
            $fail('weakPassword');
        }
    }
}
