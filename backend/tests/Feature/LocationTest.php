<?php

use App\Models\City;
use App\Models\JobTitle;
use App\Models\ProfileLocation;
use App\Models\User;
use App\Services\ProfileService;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->seed(LookupSeeder::class);
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('this-device')->plainTextToken;
    $this->profile = locationProfile($this->user, 'Main', active: true);
    $this->url = "/api/v1/me/profiles/{$this->profile->id}/location";
});

// Helpers for these tests (the names must differ from the helpers of other test files).
function locationProfile(User $user, string $name, bool $active = false)
{
    return app(ProfileService::class)->create($user, [
        'display_name' => $name,
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ], active: $active);
}

function locationPng(): UploadedFile
{
    // A real 1 x 1 PNG picture (no GD extension needed).
    return UploadedFile::fake()->createWithContent('door.png', base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
    ));
}

test('a profile without a location returns data null', function () {
    $this->withToken($this->token)->getJson($this->url)
        ->assertOk()
        ->assertJsonPath('data', null);
});

test('save a location with a picture and directions', function () {
    $this->withToken($this->token)->postJson($this->url, [
        'latitude' => 33.5138,
        'longitude' => 36.2765,
        'directions' => 'Next to the bakery',
        'place_image' => locationPng(),
    ])
        ->assertOk()
        ->assertJsonPath('data.latitude', 33.5138)
        ->assertJsonPath('data.longitude', 36.2765)
        ->assertJsonPath('data.directions', 'Next to the bakery');

    $location = ProfileLocation::first();
    expect($location->profile_id)->toBe($this->profile->id);
    expect($location->place_image)->toStartWith("profiles/{$this->profile->id}/location/");
    Storage::disk('public')->assertExists($location->place_image);

    $this->withToken($this->token)->getJson($this->url)
        ->assertOk()
        ->assertJsonPath('data.place_image', '/storage/' . $location->place_image);
});

test('saving again changes the same row and replaces the old picture', function () {
    $this->withToken($this->token)->postJson($this->url, [
        'latitude' => 33.5,
        'longitude' => 36.2,
        'place_image' => locationPng(),
    ])->assertOk();
    $oldImage = ProfileLocation::first()->place_image;

    $this->withToken($this->token)->postJson($this->url, [
        'latitude' => 34.1,
        'longitude' => 35.9,
        'place_image' => locationPng(),
    ])->assertOk()->assertJsonPath('data.latitude', 34.1);

    // Still ONE row for this profile, with a new picture; the old file is gone.
    expect(ProfileLocation::count())->toBe(1);
    $newImage = ProfileLocation::first()->place_image;
    expect($newImage)->not->toBe($oldImage);
    Storage::disk('public')->assertMissing($oldImage);
    Storage::disk('public')->assertExists($newImage);
});

test('saving without a picture keeps the old one, remove_place_image removes it', function () {
    $this->withToken($this->token)->postJson($this->url, [
        'latitude' => 33.5,
        'longitude' => 36.2,
        'place_image' => locationPng(),
    ])->assertOk();
    $image = ProfileLocation::first()->place_image;

    $this->withToken($this->token)->postJson($this->url, ['latitude' => 33.6, 'longitude' => 36.3])->assertOk();
    expect(ProfileLocation::first()->place_image)->toBe($image);

    $this->withToken($this->token)->postJson($this->url, [
        'latitude' => 33.6,
        'longitude' => 36.3,
        'remove_place_image' => true,
    ])->assertOk()->assertJsonPath('data.place_image', null);
    Storage::disk('public')->assertMissing($image);
});

test('missing or wrong numbers are rejected', function () {
    $this->withToken($this->token)->postJson($this->url, [])
        ->assertStatus(422)
        ->assertJsonPath('errors.latitude.0', 'requiredField')
        ->assertJsonPath('errors.longitude.0', 'requiredField');

    $this->withToken($this->token)->postJson($this->url, ['latitude' => 91, 'longitude' => 'east'])
        ->assertStatus(422)
        ->assertJsonPath('errors.latitude.0', 'invalidField')
        ->assertJsonPath('errors.longitude.0', 'invalidField');

    expect(ProfileLocation::count())->toBe(0);
});

test('delete removes the location and its picture', function () {
    $this->withToken($this->token)->postJson($this->url, [
        'latitude' => 33.5,
        'longitude' => 36.2,
        'place_image' => locationPng(),
    ])->assertOk();
    $image = ProfileLocation::first()->place_image;

    $this->withToken($this->token)->deleteJson($this->url)->assertNoContent();

    expect(ProfileLocation::count())->toBe(0);
    Storage::disk('public')->assertMissing($image);
});

test('the location of another account returns notFound', function () {
    $notMine = locationProfile(User::factory()->create(), 'Not mine', active: true);
    $url = "/api/v1/me/profiles/{$notMine->id}/location";

    $this->withToken($this->token)->getJson($url)->assertNotFound();
    $this->withToken($this->token)->postJson($url, ['latitude' => 33.5, 'longitude' => 36.2])
        ->assertNotFound()
        ->assertJsonPath('code', 'notFound');
    $this->withToken($this->token)->deleteJson($url)->assertNotFound();

    expect(ProfileLocation::count())->toBe(0);
});

test('deleting a profile deletes its location', function () {
    $second = locationProfile($this->user, 'Second');
    $this->withToken($this->token)
        ->postJson("/api/v1/me/profiles/{$second->id}/location", ['latitude' => 33.5, 'longitude' => 36.2])
        ->assertOk();

    $this->withToken($this->token)->deleteJson("/api/v1/me/profiles/{$second->id}")->assertNoContent();

    expect(ProfileLocation::count())->toBe(0);
});
