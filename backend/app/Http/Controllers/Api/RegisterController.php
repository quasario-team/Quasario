<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterCheckRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Services\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    // POST /api/v1/auth/register/check : validation only, saves nothing.
    public function check(RegisterCheckRequest $request): Response
    {
        return response()->noContent();
    }

    // POST /api/v1/auth/register : account + first profile. The user is NOT signed in;
    // the frontend shows a success message and the Sign in page (Narrative U04 line 9).
    public function register(RegisterRequest $request, RegistrationService $registration): JsonResponse
    {
        $result = $registration->register($request->validated());

        return response()->json([
            'user' => new UserResource($result['user']),
            'active_profile' => new ProfileResource($result['active_profile']),
        ], 201);
    }
}
