<?php

use App\Models\City;
use App\Models\GalleryImage;
use App\Models\JobTitle;
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
    $this->profile = galleryProfile($this->user, 'Main', active: true);
    $this->url = "/api/v1/me/profiles/{$this->profile->id}/gallery";
});

// Helpers for these tests.
function galleryProfile(User $user, string $name, bool $active = false)
{
    return app(ProfileService::class)->create($user, [
        'display_name' => $name,
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ], active: $active);
}

function galleryPng(): UploadedFile
{
    // A real 1 x 1 PNG picture (no GD extension needed).
    return UploadedFile::fake()->createWithContent('work.png', base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
    ));
}

test('add a picture with a description', function () {
    $response = $this->withToken($this->token)->postJson($this->url, [
        'image' => galleryPng(),
        'description' => 'A kitchen I built',
    ]);

    $picture = GalleryImage::first();

    $response->assertCreated()
        ->assertJsonPath('data.id', $picture->id)
        ->assertJsonPath('data.image', '/storage/' . $picture->image_path)
        ->assertJsonPath('data.description', 'A kitchen I built');
    expect($picture->image_path)->toStartWith("profiles/{$this->profile->id}/gallery/");
    Storage::disk('public')->assertExists($picture->image_path);
});

test('the list shows my pictures, the newest first', function () {
    $this->withToken($this->token)->postJson($this->url, ['image' => galleryPng(), 'description' => 'Old'])->assertCreated();
    $this->withToken($this->token)->postJson($this->url, ['image' => galleryPng()])->assertCreated();

    $this->withToken($this->token)->getJson($this->url)
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.description', null)
        ->assertJsonPath('data.1.description', 'Old');
});

test('no picture or a file that is not a picture is rejected', function () {
    $this->withToken($this->token)->postJson($this->url, [])
        ->assertStatus(422)
        ->assertJsonPath('errors.image.0', 'requiredField');

    $this->withToken($this->token)->postJson($this->url, [
        'image' => UploadedFile::fake()->createWithContent('notes.txt', 'hello'),
    ])
        ->assertStatus(422)
        ->assertJsonPath('errors.image.0', 'invalidField');

    expect(GalleryImage::count())->toBe(0);
});

test('a full gallery returns galleryFull', function () {
    for ($i = 0; $i < GalleryImage::MAX_PER_PROFILE; $i++) {
        $this->profile->galleryImages()->create(['image_path' => "profiles/x/{$i}.png"]);
    }

    $this->withToken($this->token)->postJson($this->url, ['image' => galleryPng()])
        ->assertStatus(409)
        ->assertJsonPath('code', 'galleryFull');

    expect(GalleryImage::count())->toBe(GalleryImage::MAX_PER_PROFILE);
});

test('delete removes the picture and its file', function () {
    $this->withToken($this->token)->postJson($this->url, ['image' => galleryPng()])->assertCreated();
    $picture = GalleryImage::first();

    $this->withToken($this->token)->deleteJson("{$this->url}/{$picture->id}")->assertNoContent();

    expect(GalleryImage::count())->toBe(0);
    Storage::disk('public')->assertMissing($picture->image_path);
});

test('a picture of another profile cannot be deleted from this profile', function () {
    $second = galleryProfile($this->user, 'Second');
    $picture = $second->galleryImages()->create(['image_path' => 'profiles/x/a.png']);

    // The picture belongs to "Second", but the address says "Main".
    $this->withToken($this->token)->deleteJson("{$this->url}/{$picture->id}")
        ->assertNotFound();

    expect(GalleryImage::count())->toBe(1);
});

test('the gallery of another account returns notFound', function () {
    $notMine = galleryProfile(User::factory()->create(), 'Not mine', active: true);
    $url = "/api/v1/me/profiles/{$notMine->id}/gallery";

    $this->withToken($this->token)->getJson($url)->assertNotFound();
    $this->withToken($this->token)->postJson($url, ['image' => galleryPng()])
        ->assertNotFound()
        ->assertJsonPath('code', 'notFound');

    expect(GalleryImage::count())->toBe(0);
});

test('deleting a profile deletes its gallery and its folder', function () {
    $second = galleryProfile($this->user, 'Second');
    $this->withToken($this->token)
        ->postJson("/api/v1/me/profiles/{$second->id}/gallery", ['image' => galleryPng()])
        ->assertCreated();
    $path = GalleryImage::first()->image_path;

    $this->withToken($this->token)->deleteJson("/api/v1/me/profiles/{$second->id}")->assertNoContent();

    expect(GalleryImage::count())->toBe(0);
    Storage::disk('public')->assertMissing($path);
});
