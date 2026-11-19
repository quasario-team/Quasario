<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake(); // catch emails instead of sending them
    $this->user = User::factory()->create(['email' => 'ali@example.com']);
});

// Asks for a link and returns the token that was put in the email.
function requestResetToken($test, User $user): string
{
    $test->postJson('/api/v1/auth/forgot-password', ['email' => $user->email])->assertOk();

    $token = null;
    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $mail) use (&$token) {
        $token = $mail->token;

        return true;
    });

    return $token;
}

test('forgot password sends a link that opens the frontend page', function () {
    $this->postJson('/api/v1/auth/forgot-password', ['email' => 'ALI@example.com'])->assertOk();

    Notification::assertSentTo($this->user, ResetPassword::class, function (ResetPassword $mail) {
        $url = $mail->toMail($this->user)->actionUrl;

        return str_starts_with($url, config('app.frontend_url').'/reset-password?token=');
    });
});

test('unknown email gets the same answer and no email', function () {
    $known = $this->postJson('/api/v1/auth/forgot-password', ['email' => 'ali@example.com'])->json();
    $unknown = $this->postJson('/api/v1/auth/forgot-password', ['email' => 'nobody@example.com'])->json();

    expect($unknown)->toBe($known);
    Notification::assertSentTimes(ResetPassword::class, 1);
});

test('reset with a valid token changes the password and logs out every device', function () {
    $this->user->createToken('phone');
    $token = requestResetToken($this, $this->user);

    $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'ali@example.com',
        'token' => $token,
        'password' => 'newSecret9',
    ])->assertNoContent();

    $this->user->refresh();
    expect(Hash::check('newSecret9', $this->user->password))->toBeTrue()
        ->and($this->user->tokens()->count())->toBe(0);
});

test('wrong token returns invalidToken', function () {
    requestResetToken($this, $this->user);

    $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'ali@example.com',
        'token' => 'wrong-token',
        'password' => 'newSecret9',
    ])
        ->assertStatus(422)
        ->assertJsonPath('code', 'invalidToken');
});

test('weak new password returns weakPassword', function () {
    $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'ali@example.com',
        'token' => 'anything',
        'password' => 'short',
    ])
        ->assertStatus(422)
        ->assertJsonPath('errors.password', ['weakPassword']);
});
