<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function __construct(private ProfileService $profiles) {}

    // Account + first active profile in ONE transaction.
    // No login token: the user signs in afterwards (Narrative U04, Activity F6).
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = new User([
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
            ]);
            $user->role_id = Role::where('name', Role::USER)->value('id');
            $user->save();

            $profile = $this->profiles->create($user, [
                'display_name' => $user->username,
                'job_title_id' => $data['job_title_id'],
                'city_id' => $data['city_id'],
                'experience' => $data['experience'] ?? null,
            ], active: true);

            return [
                'user' => $user,
                'active_profile' => $profile,
            ];
        });
    }
}
