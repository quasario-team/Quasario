<?php

namespace App\Services;

use App\Exceptions\DomainError;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(string $login, string $password): array
    {
        $login = trim($login);

        // A value with @ is an email (usernames cannot contain @).
        $user = str_contains($login, '@')
            ? User::where('email', strtolower($login))->first()
            : User::where('username', $login)->first();

        // Password first: same answer for "no such account" and "wrong password".
        if ($user === null || ! Hash::check($password, $user->password)) {
            throw new DomainError('invalidCredentials', 401, 'Wrong login or password.');
        }

        // Ban second: only someone who knows the password learns the account is banned.
        if ($user->isBanned()) {
            throw new DomainError('accountBanned', 403, 'This account is banned.');
        }

        return [
            'token' => $user->createToken('auth')->plainTextToken,
            'user' => $user,
            'active_profile' => $user->activeProfile,
        ];
    }
}
