<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SocialLinkController;
use Illuminate\Support\Facades\Route;

// Every route in this file starts with /api/v1 (see bootstrap/app.php).

// ---- public
Route::get('/lookups/registration', [LookupController::class, 'registration']);
Route::post('/auth/register/check', [RegisterController::class, 'check']);
Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/auth/forgot-password', [PasswordResetController::class, 'forgot'])->middleware('throttle:3,1');
Route::post('/auth/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1');

// ---- logged in, not banned
Route::middleware(['auth:sanctum', 'notBanned'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::patch('/me/account', [AccountController::class, 'update']);
    Route::patch('/me/password', [AccountController::class, 'changePassword']);
    Route::delete('/me', [AccountController::class, 'destroy'])->middleware('notAdmin');

    // ---- profiles (phase 2). An admin has no profiles.
    Route::middleware('notAdmin')->group(function () {
        Route::get('/me/profiles', [ProfileController::class, 'index']);
        Route::post('/me/profiles', [ProfileController::class, 'store']);
        Route::patch('/me/profiles/{profile}', [ProfileController::class, 'update'])->whereNumber('profile');
        Route::delete('/me/profiles/{profile}', [ProfileController::class, 'destroy'])->whereNumber('profile');
        Route::post('/me/profiles/{profile}/activate', [ProfileController::class, 'activate'])->whereNumber('profile');
        Route::post('/me/profiles/{profile}/images', [ProfileController::class, 'images'])->whereNumber('profile');
        Route::get('/me/profiles/{profile}/links', [SocialLinkController::class, 'index'])->whereNumber('profile');
        Route::put('/me/profiles/{profile}/links', [SocialLinkController::class, 'update'])->whereNumber('profile');
        Route::get('/me/profiles/{profile}/gallery', [GalleryController::class, 'index'])->whereNumber('profile');
        Route::post('/me/profiles/{profile}/gallery', [GalleryController::class, 'store'])->whereNumber('profile');
        Route::delete('/me/profiles/{profile}/gallery/{image}', [GalleryController::class, 'destroy'])
            ->whereNumber(['profile', 'image']);
        Route::get('/me/profiles/{profile}/location', [LocationController::class, 'show'])->whereNumber('profile');
        Route::post('/me/profiles/{profile}/location', [LocationController::class, 'save'])->whereNumber('profile');
        Route::delete('/me/profiles/{profile}/location', [LocationController::class, 'destroy'])->whereNumber('profile');
    });
});
