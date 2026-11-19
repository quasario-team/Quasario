<?php

namespace App\Services;

use App\Exceptions\DomainError;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetService
{
    // Sends the link if the email exists. The caller always answers the same way.
    public function sendLink(string $email): void
    {
        Password::sendResetLink(['email' => $email]);
    }

    public function reset(string $email, string $token, string $newPassword): void
    {
        $status = Password::reset(
            ['email' => $email, 'token' => $token, 'password' => $newPassword],
            function (User $user, string $password) {
                $user->password = $password;
                $user->setRememberToken(Str::random(60));
                $user->save();

                // Log out from every device.
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new DomainError('invalidToken', 422, 'The reset link is invalid or expired.');
        }
    }
}
