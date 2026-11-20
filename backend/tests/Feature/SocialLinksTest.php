<?php

use App\Models\City;
use App\Models\JobTitle;
use App\Models\SocialLink;
use App\Models\User;
use App\Services\ProfileService;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('this-device')->plainTextToken;
    $this->profile = linksProfile($this->user, 'Main');
    $this->url = "/api/v1/me/profiles/{$this->profile->id}/links";
});

// Helper for these tests: an active profile for an account.
function linksProfile(User $user, string $name)
{
    return app(ProfileService::class)->create($user, [
        'display_name' => $name,
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ], active: true);
}

test('save links and read them back', function () {
    $links = [
        ['platform' => 'whatsapp', 'url' => 'https://wa.me/963900000000'],
        ['platform' => 'github', 'url' => 'https://github.com/someone'],
    ];

    $this->withToken($this->token)->putJson($this->url, ['links' => $links])
        ->assertOk()
        ->assertExactJson(['data' => $links]);

    $this->withToken($this->token)->getJson($this->url)
        ->assertOk()
        ->assertExactJson(['data' => $links]);
});

test('saving again replaces the old list', function () {
    $this->withToken($this->token)->putJson($this->url, ['links' => [
        ['platform' => 'facebook', 'url' => 'https://facebook.com/old'],
    ]])->assertOk();

    $this->withToken($this->token)->putJson($this->url, ['links' => [
        ['platform' => 'x', 'url' => 'https://x.com/new'],
    ]])->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.platform', 'x');

    expect(SocialLink::count())->toBe(1);
});

test('an empty list removes all links', function () {
    $this->withToken($this->token)->putJson($this->url, ['links' => [
        ['platform' => 'website', 'url' => 'https://example.com'],
    ]])->assertOk();

    $this->withToken($this->token)->putJson($this->url, ['links' => []])
        ->assertOk()
        ->assertExactJson(['data' => []]);
});

test('a wrong platform or a wrong address returns invalidField', function () {
    $this->withToken($this->token)->putJson($this->url, ['links' => [
        ['platform' => 'myspace', 'url' => 'https://myspace.com/a'],
        ['platform' => 'youtube', 'url' => 'not an address'],
    ]])
        ->assertStatus(422)
        ->assertJsonPath('errors', [
            'links.0.platform' => ['invalidField'],
            'links.1.url' => ['invalidField'],
        ]);

    expect(SocialLink::count())->toBe(0);
});

test('the same platform twice is rejected', function () {
    $this->withToken($this->token)->putJson($this->url, ['links' => [
        ['platform' => 'instagram', 'url' => 'https://instagram.com/a'],
        ['platform' => 'instagram', 'url' => 'https://instagram.com/b'],
    ]])->assertStatus(422);

    expect(SocialLink::count())->toBe(0);
});

test('links of a profile of another account return notFound', function () {
    $notMine = linksProfile(User::factory()->create(), 'Not mine');

    $this->withToken($this->token)->getJson("/api/v1/me/profiles/{$notMine->id}/links")
        ->assertNotFound();
    $this->withToken($this->token)->putJson("/api/v1/me/profiles/{$notMine->id}/links", ['links' => []])
        ->assertNotFound()
        ->assertJsonPath('code', 'notFound');
});

test('deleting a profile deletes its links', function () {
    $second = app(ProfileService::class)->create($this->user, [
        'display_name' => 'Second',
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ], active: false);
    $second->socialLinks()->create(['platform' => 'telegram', 'url' => 'https://t.me/someone']);

    $this->withToken($this->token)->deleteJson("/api/v1/me/profiles/{$second->id}")->assertNoContent();

    expect(SocialLink::count())->toBe(0);
});

test('a link of another site returns wrongPlatformUrl', function () {
    $this->withToken($this->token)->putJson($this->url, ['links' => [
        ['platform' => 'facebook', 'url' => 'https://instagram.com/someone'],
        ['platform' => 'github', 'url' => 'https://github.com.evil.com/someone'],
    ]])
        ->assertStatus(422)
        ->assertJsonPath('errors', [
            'links.0.url' => ['wrongPlatformUrl'],
            'links.1.url' => ['wrongPlatformUrl'],
        ]);

    expect(SocialLink::count())->toBe(0);
});

test('sub-domains and any personal website are accepted', function () {
    $this->withToken($this->token)->putJson($this->url, ['links' => [
        ['platform' => 'facebook', 'url' => 'https://m.facebook.com/someone'],
        ['platform' => 'youtube', 'url' => 'https://youtu.be/abc123'],
        ['platform' => 'website', 'url' => 'https://my-own-site.net'],
    ]])
        ->assertOk()
        ->assertJsonCount(3, 'data');
});
