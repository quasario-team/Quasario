<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function update(User $user, array $changes): User
    {
        // If the email changes, the FK on password_reset_tokens updates/removes the old token by itself.
        $user->fill($changes)->save();

        return $user;
    }

    // Keeps this device logged in, logs out every other device.
    public function changePassword(User $user, string $newPassword, int $currentTokenId): void
    {
        $user->password = $newPassword;
        $user->save();

        $user->tokens()->where('id', '!=', $currentTokenId)->delete();
    }

    // ERD: deleting the users row cascades to profiles and everything under them.
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Tokens are not linked by a foreign key, so remove them ourselves.
            $user->tokens()->delete();
            $user->delete();
        });
    }
}
