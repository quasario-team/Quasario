<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    // Same hash for every fake user, so tests stay fast. Plain password: "password1".
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->regexify('[a-z0-9_]{10}'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+9639########'),
            'password' => static::$password ??= Hash::make('password1'),
            'role_id' => fn() => Role::where('name', Role::USER)->value('id'),
            'status' => User::STATUS_ACTIVE,
            'remember_token' => Str::random(10),
        ];
    }

    // User::factory()->admin()->create()
    public function admin(): static
    {
        return $this->state(fn() => [
            'role_id' => Role::where('name', Role::ADMIN)->value('id'),
        ]);
    }

    // User::factory()->banned()->create()
    public function banned(): static
    {
        return $this->state(fn() => [
            'status' => User::STATUS_BANNED,
        ]);
    }
}
