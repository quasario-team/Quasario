<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function validAccount(array $changes = []): array
{
    return [...[
        'email' => 'ali@example.com',
        'username' => 'ali_99',
        'phone' => '+963933123456',
        'password' => 'secret123',
    ], ...$changes];
}

test('valid data returns 204 and saves nothing', function () {
    $this->postJson('/api/v1/auth/register/check', validAccount())->assertNoContent();

    expect(User::count())->toBe(0);
});

test('missing fields return requiredField', function () {
    $this->postJson('/api/v1/auth/register/check', [])
        ->assertStatus(422)
        ->assertJsonPath('errors.email', ['requiredField'])
        ->assertJsonPath('errors.username', ['requiredField'])
        ->assertJsonPath('errors.phone', ['requiredField'])
        ->assertJsonPath('errors.password', ['requiredField']);
});

test('used email returns alreadyUsed even with capital letters', function () {
    User::factory()->create(['email' => 'ali@example.com']);

    $this->postJson('/api/v1/auth/register/check', validAccount(['email' => 'ALI@Example.com']))
        ->assertStatus(422)
        ->assertJsonPath('errors.email', ['alreadyUsed']);
});

test('used username returns alreadyUsed', function () {
    User::factory()->create(['username' => 'ali_99']);

    $this->postJson('/api/v1/auth/register/check', validAccount())
        ->assertStatus(422)
        ->assertJsonPath('errors.username', ['alreadyUsed']);
});

test('username with @ returns invalidField', function () {
    $this->postJson('/api/v1/auth/register/check', validAccount(['username' => 'ali@99']))
        ->assertStatus(422)
        ->assertJsonPath('errors.username', ['invalidField']);
});

test('bad phone returns invalidField', function () {
    $this->postJson('/api/v1/auth/register/check', validAccount(['phone' => '12ab']))
        ->assertStatus(422)
        ->assertJsonPath('errors.phone', ['invalidField']);
});

test('weak password returns weakPassword', function () {
    $this->postJson('/api/v1/auth/register/check', validAccount(['password' => '12345678']))
        ->assertStatus(422)
        ->assertJsonPath('errors.password', ['weakPassword']);
});
