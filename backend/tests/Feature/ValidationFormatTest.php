<?php

use App\Http\Requests\ApiFormRequest;
use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

// A small request used only by these tests.
class TestSignUpRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', new StrongPassword],
        ];
    }
}

beforeEach(function () {
    Route::middleware('api')->post('/api/v1/test-validation', fn(TestSignUpRequest $request) => response()->noContent());
});

test('missing fields return requiredField', function () {
    $this->postJson('/api/v1/test-validation', [])
        ->assertStatus(422)
        ->assertExactJson([
            'message' => 'The given data was invalid.',
            'code' => 'validationFailed',
            'errors' => ['email' => ['requiredField'], 'password' => ['requiredField']],
        ]);
});

test('weak password returns weakPassword', function () {
    $this->postJson('/api/v1/test-validation', ['email' => 'a@b.com', 'password' => 'abcdefgh'])
        ->assertStatus(422)
        ->assertJsonPath('errors.password', ['weakPassword']);
});

test('used email returns alreadyUsed', function () {
    User::factory()->create(['email' => 'taken@b.com']);

    $this->postJson('/api/v1/test-validation', ['email' => 'taken@b.com', 'password' => 'abc12345'])
        ->assertStatus(422)
        ->assertJsonPath('errors.email', ['alreadyUsed']);
});

test('bad email format returns invalidField', function () {
    $this->postJson('/api/v1/test-validation', ['email' => 'not-an-email', 'password' => 'abc12345'])
        ->assertStatus(422)
        ->assertJsonPath('errors.email', ['invalidField']);
});

test('valid data passes', function () {
    $this->postJson('/api/v1/test-validation', ['email' => 'new@b.com', 'password' => 'abc12345'])
        ->assertNoContent();
});
