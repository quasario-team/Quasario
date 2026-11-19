<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Requests\Account\DeleteAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Resources\UserResource;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    // PATCH /api/v1/me/account
    public function update(UpdateAccountRequest $request, AccountService $accounts): JsonResponse
    {
        $user = $accounts->update($request->user(), $request->validated());

        return response()->json(['user' => new UserResource($user)]);
    }

    // PATCH /api/v1/me/password
    public function changePassword(ChangePasswordRequest $request, AccountService $accounts): Response
    {
        $user = $request->user();
        $accounts->changePassword($user, $request->validated('password'), $user->currentAccessToken()->id);

        return response()->noContent();
    }

    // DELETE /api/v1/me
    public function destroy(DeleteAccountRequest $request, AccountService $accounts): Response
    {
        $accounts->delete($request->user());

        return response()->noContent();
    }
}
