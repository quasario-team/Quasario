<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    // Runs before EVERY test.
    protected function setUp(): void
    {
        parent::setUp();

        // Safety: no test may touch the real pictures in storage/app/public.
        // (Deleting a profile deletes its folder, and a test profile can have
        // the same id as one of your real profiles.)
        Storage::fake('public');
    }
}
