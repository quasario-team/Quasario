<?php

use App\Exceptions\DomainError;
use Illuminate\Support\Facades\Route;

test('unknown api route returns notFound json', function () {
    $this->getJson('/api/v1/does-not-exist')
        ->assertStatus(404)
        ->assertExactJson(['message' => 'Not found.', 'code' => 'notFound']);
});

test('protected route without token returns unauthenticated json', function () {
    Route::middleware(['api', 'auth:sanctum'])->get('/api/v1/test-auth', fn() => 'ok');

    $this->getJson('/api/v1/test-auth')
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthenticated.', 'code' => 'unauthenticated']);
});

test('domain error returns its code and status', function () {
    Route::middleware('api')->get('/api/v1/test-domain', function () {
        throw new DomainError('profileLimitReached', 409, 'An account can have at most 5 profiles.');
    });

    $this->getJson('/api/v1/test-domain')
        ->assertStatus(409)
        ->assertExactJson(['message' => 'An account can have at most 5 profiles.', 'code' => 'profileLimitReached']);
});
