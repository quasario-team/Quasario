<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    Route::middleware(['api', 'auth:sanctum', 'notBanned'])->get('/api/v1/test-any', fn() => 'ok');
    Route::middleware(['api', 'auth:sanctum', 'notBanned', 'admin'])->get('/api/v1/test-admin', fn() => 'ok');
    Route::middleware(['api', 'auth:sanctum', 'notBanned', 'notAdmin'])->get('/api/v1/test-user', fn() => 'ok');
});

test('banned account is blocked and its token is deleted', function () {
    $user = User::factory()->banned()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withToken($token)->getJson('/api/v1/test-any')
        ->assertStatus(403)
        ->assertJsonPath('code', 'accountBanned');

    expect($user->tokens()->count())->toBe(0);
});

test('active account passes notBanned', function () {
    $token = User::factory()->create()->createToken('test')->plainTextToken;

    $this->withToken($token)->getJson('/api/v1/test-any')->assertOk();
});

test('normal user cannot open admin route', function () {
    $token = User::factory()->create()->createToken('test')->plainTextToken;

    $this->withToken($token)->getJson('/api/v1/test-admin')
        ->assertStatus(403)
        ->assertJsonPath('code', 'notAdmin');
});

test('admin can open admin route', function () {
    $token = User::factory()->admin()->create()->createToken('test')->plainTextToken;

    $this->withToken($token)->getJson('/api/v1/test-admin')->assertOk();
});

test('admin cannot open user-only route', function () {
    $token = User::factory()->admin()->create()->createToken('test')->plainTextToken;

    $this->withToken($token)->getJson('/api/v1/test-user')
        ->assertStatus(403)
        ->assertJsonPath('code', 'adminNotAllowed');
});
