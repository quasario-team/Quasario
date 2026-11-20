<?php

use App\Models\City;
use App\Models\JobTitle;
use App\Models\User;
use App\Services\ProfileService;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // A fake disk: the test files are never written to the real storage folder.
    Storage::fake('public');
    $this->seed(LookupSeeder::class);
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('this-device')->plainTextToken;
    $this->profile = app(ProfileService::class)->create($this->user, [
        'display_name' => 'Main',
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ], active: true);
});

// A real tiny PNG picture (1 x 1 pixel). It does not need the PHP GD extension.
function tinyPng(string $name = 'photo.png'): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
    ));
}

test('upload a profile picture saves the file and returns its address', function () {
    $response = $this->withToken($this->token)->postJson(
        "/api/v1/me/profiles/{$this->profile->id}/images",
        ['profile_image' => tinyPng()],
    );

    $path = $this->profile->fresh()->profile_image;

    $response->assertOk()->assertJsonPath('data.profile_image', '/storage/' . $path);
    expect($path)->toStartWith("profiles/{$this->profile->id}/");
    Storage::disk('public')->assertExists($path);
});

test('upload both pictures at once', function () {
    $this->withToken($this->token)->postJson(
        "/api/v1/me/profiles/{$this->profile->id}/images",
        ['profile_image' => tinyPng('face.png'), 'cover_image' => tinyPng('cover.png')],
    )->assertOk();

    $profile = $this->profile->fresh();
    Storage::disk('public')->assertExists([$profile->profile_image, $profile->cover_image]);
});

test('a new picture deletes the old file', function () {
    $url = "/api/v1/me/profiles/{$this->profile->id}/images";

    $this->withToken($this->token)->postJson($url, ['profile_image' => tinyPng()])->assertOk();
    $oldPath = $this->profile->fresh()->profile_image;

    $this->withToken($this->token)->postJson($url, ['profile_image' => tinyPng()])->assertOk();
    $newPath = $this->profile->fresh()->profile_image;

    expect($newPath)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($newPath);
});

test('sending no picture returns requiredField', function () {
    $this->withToken($this->token)->postJson("/api/v1/me/profiles/{$this->profile->id}/images", [])
        ->assertStatus(422)
        ->assertJsonPath('errors.profile_image.0', 'requiredField')
        ->assertJsonPath('errors.cover_image.0', 'requiredField');
});

test('a file that is not a picture returns invalidField', function () {
    $notes = UploadedFile::fake()->createWithContent('notes.txt', 'hello');

    $this->withToken($this->token)->postJson(
        "/api/v1/me/profiles/{$this->profile->id}/images",
        ['profile_image' => $notes],
    )
        ->assertStatus(422)
        ->assertJsonPath('errors.profile_image.0', 'invalidField');

    expect($this->profile->fresh()->profile_image)->toBeNull();
});

test('uploading to a profile of another account returns notFound', function () {
    $notMine = app(ProfileService::class)->create(User::factory()->create(), [
        'display_name' => 'Not mine',
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ], active: true);

    $this->withToken($this->token)->postJson(
        "/api/v1/me/profiles/{$notMine->id}/images",
        ['profile_image' => tinyPng()],
    )
        ->assertNotFound()
        ->assertJsonPath('code', 'notFound');

    expect($notMine->fresh()->profile_image)->toBeNull();
});
