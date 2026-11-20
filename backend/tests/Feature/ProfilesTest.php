<?php

use App\Models\City;
use App\Models\JobCategory;
use App\Models\JobTitle;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProfileService;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('this-device')->plainTextToken;
});

// Helper for these tests: give an account one profile (active or not).
function makeProfile(User $user, string $name, bool $active = false): Profile
{
    return app(ProfileService::class)->create($user, [
        'display_name' => $name,
        'job_title_id' => JobTitle::first()->id,
        'city_id' => City::first()->id,
    ], active: $active);
}

test('list shows only my profiles, the active one first', function () {
    makeProfile($this->user, 'Second');
    makeProfile($this->user, 'Main', active: true);
    makeProfile(User::factory()->create(), 'Not mine', active: true);

    $this->withToken($this->token)->getJson('/api/v1/me/profiles')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.display_name', 'Main')
        ->assertJsonPath('data.0.is_active', true)
        ->assertJsonPath('data.1.display_name', 'Second')
        ->assertJsonPath('data.1.is_active', false);
});

test('admin cannot list profiles', function () {
    $admin = User::factory()->admin()->create();

    $this->withToken($admin->createToken('admin-device')->plainTextToken)
        ->getJson('/api/v1/me/profiles')
        ->assertForbidden()
        ->assertJsonPath('code', 'adminNotAllowed');
});

test('guest cannot list profiles', function () {
    $this->getJson('/api/v1/me/profiles')
        ->assertUnauthorized()
        ->assertJsonPath('code', 'unauthenticated');
});

// ---------- P2: add a profile ----------

// Valid data for a new profile: a category and one of its titles.
function newProfileData(array $changes = []): array
{
    $category = JobCategory::first();

    return [
        'display_name' => 'My shop',
        'job_category_id' => $category->id,
        'job_title_id' => JobTitle::where('job_category_id', $category->id)->first()->id,
        'city_id' => City::first()->id,
        'bio' => 'We fix phones.',
        ...$changes,
    ];
}

test('add profile creates an inactive profile', function () {
    makeProfile($this->user, 'Main', active: true);

    $this->withToken($this->token)->postJson('/api/v1/me/profiles', newProfileData())
        ->assertCreated()
        ->assertJsonPath('data.display_name', 'My shop')
        ->assertJsonPath('data.bio', 'We fix phones.')
        ->assertJsonPath('data.is_active', false);

    expect($this->user->profiles()->count())->toBe(2)
        ->and($this->user->activeProfile->display_name)->toBe('Main');
});

test('add profile with missing fields returns requiredField', function () {
    $this->withToken($this->token)->postJson('/api/v1/me/profiles', [])
        ->assertStatus(422)
        ->assertJsonPath('errors.display_name', ['requiredField'])
        ->assertJsonPath('errors.job_category_id', ['requiredField'])
        ->assertJsonPath('errors.job_title_id', ['requiredField'])
        ->assertJsonPath('errors.city_id', ['requiredField']);
});

test('add profile with a title from another category returns invalidField', function () {
    $otherTitle = JobTitle::where('job_category_id', '!=', JobCategory::first()->id)->first();

    $this->withToken($this->token)->postJson('/api/v1/me/profiles', newProfileData(['job_title_id' => $otherTitle->id]))
        ->assertStatus(422)
        ->assertJsonPath('errors.job_title_id', ['invalidField']);
});

test('a sixth profile returns profileLimitReached', function () {
    foreach (range(1, 5) as $number) {
        makeProfile($this->user, "Profile $number", active: $number === 1);
    }

    $this->withToken($this->token)->postJson('/api/v1/me/profiles', newProfileData())
        ->assertStatus(409)
        ->assertJsonPath('code', 'profileLimitReached');

    expect($this->user->profiles()->count())->toBe(5);
});

// ---------- P3: edit a profile ----------

test('edit changes only the sent fields', function () {
    $profile = makeProfile($this->user, 'Old name', active: true);

    $this->withToken($this->token)->patchJson("/api/v1/me/profiles/{$profile->id}", [
        'display_name' => 'New name',
        'bio' => 'Hello',
    ])
        ->assertOk()
        ->assertJsonPath('data.display_name', 'New name')
        ->assertJsonPath('data.bio', 'Hello')
        ->assertJsonPath('data.city.id', $profile->city_id)
        ->assertJsonPath('data.is_active', true);
});

test('edit can empty an optional field', function () {
    $profile = makeProfile($this->user, 'Main', active: true);
    $profile->update(['bio' => 'Old bio']);

    $this->withToken($this->token)->patchJson("/api/v1/me/profiles/{$profile->id}", ['bio' => null])
        ->assertOk()
        ->assertJsonPath('data.bio', null);
});

test('edit job needs the category with the title', function () {
    $profile = makeProfile($this->user, 'Main', active: true);

    $this->withToken($this->token)->patchJson("/api/v1/me/profiles/{$profile->id}", [
        'job_title_id' => JobTitle::first()->id,
    ])
        ->assertStatus(422)
        ->assertJsonPath('errors.job_category_id', ['requiredField']);
});

test('edit job with a title from another category returns invalidField', function () {
    $profile = makeProfile($this->user, 'Main', active: true);
    $category = JobCategory::first();
    $otherTitle = JobTitle::where('job_category_id', '!=', $category->id)->first();

    $this->withToken($this->token)->patchJson("/api/v1/me/profiles/{$profile->id}", [
        'job_category_id' => $category->id,
        'job_title_id' => $otherTitle->id,
    ])
        ->assertStatus(422)
        ->assertJsonPath('errors.job_title_id', ['invalidField']);
});

test('editing a profile of another account returns notFound', function () {
    $notMine = makeProfile(User::factory()->create(), 'Not mine', active: true);

    $this->withToken($this->token)->patchJson("/api/v1/me/profiles/{$notMine->id}", ['display_name' => 'Hacked'])
        ->assertNotFound()
        ->assertJsonPath('code', 'notFound');

    expect($notMine->fresh()->display_name)->toBe('Not mine');
});
// ---------- P4: delete a profile ----------

test('delete removes an inactive profile and keeps the active one', function () {
    $main = makeProfile($this->user, 'Main', active: true);
    $extra = makeProfile($this->user, 'Extra');

    $this->withToken($this->token)->deleteJson("/api/v1/me/profiles/{$extra->id}")->assertNoContent();

    expect(Profile::find($extra->id))->toBeNull()
        ->and($main->fresh()->is_active)->toBeTrue();
});

test('deleting the active profile makes the oldest other profile active', function () {
    $main = makeProfile($this->user, 'Main', active: true);
    $second = makeProfile($this->user, 'Second');
    makeProfile($this->user, 'Third');

    $this->withToken($this->token)->deleteJson("/api/v1/me/profiles/{$main->id}")->assertNoContent();

    expect($this->user->activeProfile()->first()->id)->toBe($second->id)
        ->and($this->user->profiles()->count())->toBe(2);
});

test('the last profile cannot be deleted', function () {
    $main = makeProfile($this->user, 'Main', active: true);

    $this->withToken($this->token)->deleteJson("/api/v1/me/profiles/{$main->id}")
        ->assertStatus(409)
        ->assertJsonPath('code', 'lastProfile');

    expect(Profile::find($main->id))->not->toBeNull();
});

test('deleting a profile of another account returns notFound', function () {
    makeProfile($this->user, 'Main', active: true);
    $notMine = makeProfile(User::factory()->create(), 'Not mine', active: true);

    $this->withToken($this->token)->deleteJson("/api/v1/me/profiles/{$notMine->id}")
        ->assertNotFound()
        ->assertJsonPath('code', 'notFound');

    expect(Profile::find($notMine->id))->not->toBeNull();
});

// ---- P7: activate (switch) a profile

test('activate makes the chosen profile active and the old one inactive', function () {
    $main = makeProfile($this->user, 'Main', active: true);
    $second = makeProfile($this->user, 'Second');

    $this->withToken($this->token)->postJson("/api/v1/me/profiles/{$second->id}/activate")
        ->assertOk()
        ->assertJsonPath('data.display_name', 'Second')
        ->assertJsonPath('data.is_active', true);

    expect($second->fresh()->is_active)->toBeTrue()
        ->and($main->fresh()->is_active)->toBeFalse()
        ->and($this->user->profiles()->where('is_active', true)->count())->toBe(1);
});

test('activating the profile that is already active changes nothing', function () {
    $main = makeProfile($this->user, 'Main', active: true);
    makeProfile($this->user, 'Second');

    $this->withToken($this->token)->postJson("/api/v1/me/profiles/{$main->id}/activate")
        ->assertOk()
        ->assertJsonPath('data.is_active', true);

    expect($this->user->activeProfile()->first()->id)->toBe($main->id);
});

test('activating a profile of another account returns notFound', function () {
    $main = makeProfile($this->user, 'Main', active: true);
    $notMine = makeProfile(User::factory()->create(), 'Not mine');

    $this->withToken($this->token)->postJson("/api/v1/me/profiles/{$notMine->id}/activate")
        ->assertNotFound()
        ->assertJsonPath('code', 'notFound');

    expect($main->fresh()->is_active)->toBeTrue()
        ->and($notMine->fresh()->is_active)->toBeFalse();
});
