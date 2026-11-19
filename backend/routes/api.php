<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\RegisterController;
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
});
