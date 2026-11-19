<?php

use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration lookups return cities and categories with titles', function () {
    $this->seed(LookupSeeder::class);

    $this->getJson('/api/v1/lookups/registration')
        ->assertOk()
        ->assertJsonCount(5, 'cities')
        ->assertJsonCount(3, 'job_categories')
        ->assertJsonCount(3, 'job_categories.0.job_titles')
        ->assertJsonStructure([
            'cities' => [['id', 'name']],
            'job_categories' => [['id', 'name', 'job_titles' => [['id', 'name']]]],
        ]);
});

test('registration lookups are public', function () {
    $this->getJson('/api/v1/lookups/registration')->assertOk();
});
