<?php

use App\Exceptions\DomainError;
use App\Models\JobCategory;
use App\Models\City;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProfileService;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
    $category = JobCategory::orderBy('id')->first();
    $this->data = [
        'email' => 'Ali@Example.com',
        'username' => 'ali_99',
        'phone' => '+963933123456',
        'password' => 'secret123',
        'job_category_id' => $category->id,
        'job_title_id' => $category->jobTitles()->first()->id,
        'city_id' => City::first()->id,
        'experience' => '5 years',
    ];
});

test('register creates account and one active profile without signing in', function () {
    $this->postJson('/api/v1/auth/register', $this->data)
        ->assertCreated()
        ->assertJsonPath('user.email', 'ali@example.com')
        ->assertJsonPath('user.role', 'user')
        ->assertJsonPath('active_profile.display_name', 'ali_99')
        ->assertJsonPath('active_profile.is_active', true)
        ->assertJsonStructure(['user', 'active_profile' => ['job_title' => ['job_category'], 'city']])
        ->assertJsonMissingPath('token')
        ->assertJsonMissingPath('user.password');

    $user = User::firstWhere('username', 'ali_99');
    expect($user->profiles()->count())->toBe(1)
        ->and($user->tokens()->count())->toBe(0);
});

test('title from another category is rejected and nothing is created', function () {
    $otherCategory = JobCategory::orderBy('id', 'desc')->first();

    $this->postJson('/api/v1/auth/register', [
        ...$this->data,
        'job_title_id' => $otherCategory->jobTitles()->first()->id,
    ])
        ->assertStatus(422)
        ->assertJsonPath('errors.job_title_id', ['invalidField']);

    expect(User::count())->toBe(0)->and(Profile::count())->toBe(0);
});

test('missing profile fields return requiredField', function () {
    $this->postJson('/api/v1/auth/register', [...$this->data, 'city_id' => null, 'job_category_id' => null])
        ->assertStatus(422)
        ->assertJsonPath('errors.city_id', ['requiredField'])
        ->assertJsonPath('errors.job_category_id', ['requiredField']);
});

test('an account cannot get a sixth profile', function () {
    $user = User::factory()->create();
    $service = app(ProfileService::class);
    $profileData = ['display_name' => 'p', 'job_title_id' => $this->data['job_title_id'], 'city_id' => $this->data['city_id']];

    foreach (range(1, 5) as $i) {
        $service->create($user, $profileData);
    }

    expect(fn() => $service->create($user, $profileData))->toThrow(DomainError::class);
    expect($user->profiles()->count())->toBe(5);
});

test('an extra profile is inactive by default', function () {
    $user = User::factory()->create();

    $profile = app(ProfileService::class)->create($user, [
        'display_name' => 'Second',
        'job_title_id' => $this->data['job_title_id'],
        'city_id' => $this->data['city_id'],
    ]);

    expect($profile->is_active)->toBeFalse();
});
