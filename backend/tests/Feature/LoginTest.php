<?php

use App\Models\City;
use App\Models\JobTitle;
use App\Models\User;
use App\Services\ProfileService;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
    $this->user = User::factory()->create(['username' => 'ali_99', 'email' => 'ali@example.com']);
    app(ProfileService::class)->create($this->user, [
        'display_name' => 'ali_99',
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ], active: true);
});

test('login with email returns token, user and active profile', function () {
    $this->postJson('/api/v1/auth/login', ['login' => 'ali@example.com', 'password' => 'password1'])
        ->assertOk()
        ->assertJsonStructure(['token', 'user', 'active_profile'])
        ->assertJsonPath('active_profile.display_name', 'ali_99');
});

test('login with username works', function () {
    $this->postJson('/api/v1/auth/login', ['login' => 'ali_99', 'password' => 'password1'])->assertOk();
});

test('login with email in capital letters works', function () {
    $this->postJson('/api/v1/auth/login', ['login' => 'ALI@Example.com', 'password' => 'password1'])->assertOk();
});

test('wrong password returns invalidCredentials', function () {
    $this->postJson('/api/v1/auth/login', ['login' => 'ali_99', 'password' => 'wrong123'])
        ->assertStatus(401)
        ->assertJsonPath('code', 'invalidCredentials');
});

test('unknown account returns the same invalidCredentials', function () {
    $this->postJson('/api/v1/auth/login', ['login' => 'nobody', 'password' => 'password1'])
        ->assertStatus(401)
        ->assertJsonPath('code', 'invalidCredentials');
});

test('banned account with correct password returns accountBanned', function () {
    User::factory()->banned()->create(['username' => 'bad_user']);

    $this->postJson('/api/v1/auth/login', ['login' => 'bad_user', 'password' => 'password1'])
        ->assertStatus(403)
        ->assertJsonPath('code', 'accountBanned');
});

test('banned account with wrong password does not reveal the ban', function () {
    User::factory()->banned()->create(['username' => 'bad_user']);

    $this->postJson('/api/v1/auth/login', ['login' => 'bad_user', 'password' => 'wrong123'])
        ->assertStatus(401)
        ->assertJsonPath('code', 'invalidCredentials');
});

test('admin logs in with role admin and no active profile', function () {
    User::factory()->admin()->create(['username' => 'boss']);

    $this->postJson('/api/v1/auth/login', ['login' => 'boss', 'password' => 'password1'])
        ->assertOk()
        ->assertJsonPath('user.role', 'admin')
        ->assertJsonPath('active_profile', null);
});

test('sixth login attempt in a minute returns tooManyRequests', function () {
    foreach (range(1, 5) as $i) {
        $this->postJson('/api/v1/auth/login', ['login' => 'ali_99', 'password' => 'wrong123']);
    }

    $this->postJson('/api/v1/auth/login', ['login' => 'ali_99', 'password' => 'wrong123'])
        ->assertStatus(429)
        ->assertJsonPath('code', 'tooManyRequests');
});

test('me returns account, active profile and profiles count', function () {
    $token = $this->user->createToken('test')->plainTextToken;

    $this->withToken($token)->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonPath('user.username', 'ali_99')
        ->assertJsonPath('active_profile.is_active', true)
        ->assertJsonPath('profiles_count', 1);
});

test('me without token returns unauthenticated', function () {
    $this->getJson('/api/v1/me')->assertStatus(401)->assertJsonPath('code', 'unauthenticated');
});

test('logout deletes the token so it stops working', function () {
    $token = $this->user->createToken('test')->plainTextToken;

    $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();
    expect($this->user->tokens()->count())->toBe(0);

    // Forget the logged-in user cached during this test, then try the old token again.
    $this->app['auth']->forgetGuards();
    $this->withToken($token)->getJson('/api/v1/me')->assertStatus(401);
});
