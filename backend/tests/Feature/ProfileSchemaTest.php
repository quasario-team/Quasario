
<?php

use App\Models\City;
use App\Models\JobTitle;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
    $this->profileData = [
        'display_name' => 'Test profile',
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ];
});

test('roles user and admin exist after migration', function () {
    expect(Role::pluck('name')->sort()->values()->all())->toBe(['admin', 'user']);
});

test('an account cannot have two active profiles', function () {
    $user = User::factory()->create();
    $user->profiles()->forceCreate([...$this->profileData, 'is_active' => true]);

    expect(fn () => $user->profiles()->forceCreate([...$this->profileData, 'is_active' => true]))
        ->toThrow(QueryException::class);
});

test('an account can have many inactive profiles next to one active', function () {
    $user = User::factory()->create();
    $user->profiles()->forceCreate([...$this->profileData, 'is_active' => true]);
    $user->profiles()->forceCreate([...$this->profileData, 'is_active' => false]);
    $user->profiles()->forceCreate([...$this->profileData, 'is_active' => false]);

    expect($user->profiles()->count())->toBe(3)
        ->and($user->activeProfile)->not->toBeNull();
});

test('two different accounts can each have one active profile', function () {
    User::factory()->create()->profiles()->forceCreate([...$this->profileData, 'is_active' => true]);
    User::factory()->create()->profiles()->forceCreate([...$this->profileData, 'is_active' => true]);

    expect(App\Models\Profile::where('is_active', true)->count())->toBe(2);
});

test('deleting an account deletes its profiles', function () {
    $user = User::factory()->create();
    $user->profiles()->forceCreate([...$this->profileData, 'is_active' => true]);

    $user->delete();

    expect(App\Models\Profile::count())->toBe(0);
});

test('a city used by a profile cannot be deleted', function () {
    User::factory()->create()->profiles()->forceCreate([...$this->profileData, 'is_active' => true]);

    expect(fn () => City::first()->delete())->toThrow(QueryException::class);
});
