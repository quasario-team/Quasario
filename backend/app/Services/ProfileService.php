<?php

namespace App\Services;

use App\Exceptions\DomainError;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public const MAX_PROFILES_PER_ACCOUNT = 5;

    // Creates a profile for the account. Used by registration (active) and later by "add profile" (inactive).
    public function create(User $user, array $data, bool $active = false): Profile
    {
        return DB::transaction(function () use ($user, $data, $active) {
            // Lock the account row: two requests at the same time cannot both pass the limit check.
            User::whereKey($user->id)->lockForUpdate()->first();

            if ($user->profiles()->count() >= self::MAX_PROFILES_PER_ACCOUNT) {
                throw new DomainError('profileLimitReached', 409, 'An account can have at most 5 profiles.');
            }

            $profile = new Profile($data);
            $profile->user_id = $user->id;
            $profile->is_active = $active;
            $profile->save();

            return $profile;
        });
    }

    // Deletes one profile of the account.
    // - The last profile cannot be deleted (an account always has 1..5 profiles).
    // - If the active profile is deleted, the oldest remaining profile becomes active.
    // - Its files (photo, cover, gallery) are deleted from the disk at the end.
    public function delete(User $user, Profile $profile): void
    {
        DB::transaction(function () use ($user, $profile) {
            // Same lock as create(): two deletes at the same time cannot remove the last two profiles.
            User::whereKey($user->id)->lockForUpdate()->first();

            if ($user->profiles()->count() <= 1) {
                throw new DomainError('lastProfile', 409, 'An account must keep at least one profile.');
            }

            $wasActive = $profile->is_active;
            $profile->delete();

            if ($wasActive) {
                $next = $user->profiles()->orderBy('id')->first();
                $next->is_active = true;
                $next->save();
            }
        });

        // After the database work succeeded: remove the folder storage/app/public/profiles/{id}.
        Storage::disk('public')->deleteDirectory("profiles/{$profile->id}");
    }

    // Makes one profile the active one (the "switch profile" button).
    // First every other profile is turned off, then this one is turned on,
    // because the database allows only ONE active profile per account (UNIQUE active_user_id).
    public function activate(User $user, Profile $profile): Profile
    {
        return DB::transaction(function () use ($user, $profile) {
            // Same lock as create() and delete(): two switches at the same time wait for each other.
            User::whereKey($user->id)->lockForUpdate()->first();

            $user->profiles()->whereKeyNot($profile->id)->update(['is_active' => false]);

            $profile->is_active = true;
            $profile->save();

            return $profile;
        });
    }
}
