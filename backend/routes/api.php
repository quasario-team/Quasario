<?php

use App\Http\Controllers\Api\LookupController;
use Illuminate\Support\Facades\Route;

// Every route in this file starts with /api/v1 (see bootstrap/app.php).

// ---- public
Route::get('/lookups/registration', [LookupController::class, 'registration']);
