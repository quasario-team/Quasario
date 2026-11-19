<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PasswordResetController extends Controller
{
    // POST /api/v1/auth/forgot-password : same answer whether the email exists or not.
    public function forgot(ForgotPasswordRequest $request, PasswordResetService $service): JsonResponse
    {
        $service->sendLink($request->validated('email'));

        return response()->json(['message' => 'If this email is registered, a reset link has been sent.']);
    }

    // POST /api/v1/auth/reset-password
    public function reset(ResetPasswordRequest $request, PasswordResetService $service): Response
    {
        $service->reset(
            $request->validated('email'),
            $request->validated('token'),
            $request->validated('password'),
        );

        return response()->noContent();
    }
}
