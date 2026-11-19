<?php

use App\Models\City;
use App\Models\JobTitle;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProfileService;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['username' => 'ali_99', 'email' => 'ali@example.com']);
    $this->token = $this->user->createToken('this-device')->plainTextToken;
});

test('update changes only the sent fields', function () {
    $this->withToken($this->token)->patchJson('/api/v1/me/account', ['email' => 'NEW@Example.com'])
        ->assertOk()
        ->assertJsonPath('user.email', 'new@example.com')
        ->assertJsonPath('user.username', 'ali_99');
});

test('update with own current email is allowed', function () {
    $this->withToken($this->token)->patchJson('/api/v1/me/account', ['email' => 'ali@example.com'])->assertOk();
});

test('update with an email of another account returns alreadyUsed', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->withToken($this->token)->patchJson('/api/v1/me/account', ['email' => 'taken@example.com'])
        ->assertStatus(422)
        ->assertJsonPath('errors.email', ['alreadyUsed']);
});

test('change password with wrong current password returns wrongPassword', function () {
    $this->withToken($this->token)->patchJson('/api/v1/me/password', [
        'current_password' => 'wrong999',
        'password' => 'newSecret9',
    ])
        ->assertStatus(422)
        ->assertJsonPath('errors.current_password', ['wrongPassword']);
});

test('change password keeps this device and logs out the others', function () {
    $this->user->createToken('other-device');

    $this->withToken($this->token)->patchJson('/api/v1/me/password', [
        'current_password' => 'password1',
        'password' => 'newSecret9',
    ])->assertNoContent();

    $this->user->refresh();
    expect(Hash::check('newSecret9', $this->user->password))->toBeTrue()
        ->and($this->user->tokens()->count())->toBe(1);
});

test('delete account with wrong password returns wrongPassword', function () {
    $this->withToken($this->token)->deleteJson('/api/v1/me', ['password' => 'wrong999'])
        ->assertStatus(422)
        ->assertJsonPath('errors.password', ['wrongPassword']);

    expect(User::find($this->user->id))->not->toBeNull();
});

test('delete account removes the account, its profiles and tokens', function () {
    $this->seed(LookupSeeder::class);
    app(ProfileService::class)->create($this->user, [
        'display_name' => 'ali_99',
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ], active: true);

    $this->withToken($this->token)->deleteJson('/api/v1/me', ['password' => 'password1'])->assertNoContent();

    expect(User::find($this->user->id))->toBeNull()
        ->and(Profile::count())->toBe(0)
        ->and($this->user->tokens()->count())->toBe(0);
});

test('admin cannot delete his account from here', function () {
    $token = User::factory()->admin()->create()->createToken('test')->plainTextToken;

    $this->withToken($token)->deleteJson('/api/v1/me', ['password' => 'password1'])
        ->assertStatus(403)
        ->assertJsonPath('code', 'adminNotAllowed');
});
