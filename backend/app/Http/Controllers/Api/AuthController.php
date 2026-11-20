<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    // POST /api/v1/auth/login
    public function login(LoginRequest $request, AuthService $auth): JsonResponse
    {
        $result = $auth->login($request->validated('login'), $request->validated('password'));

        return response()->json([
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
            'active_profile' => $result['active_profile'] ? new ProfileResource($result['active_profile']) : null,
        ]);
    }

    // POST /api/v1/auth/logout : removes the token of this device only.
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    // GET /api/v1/me
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $activeProfile = $user->activeProfile;

        return response()->json([
            'user' => new UserResource($user),
            'active_profile' => $activeProfile ? new ProfileResource($activeProfile) : null,
            'profiles_count' => $user->profiles()->count(),
        ]);
    }
}
