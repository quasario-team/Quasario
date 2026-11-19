<?php

namespace App\Http\Middleware;

use App\Exceptions\DomainError;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

// A banned account is blocked everywhere and its current token is removed.
class EnsureNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->isBanned()) {
            $token = $user->currentAccessToken();
            if ($token instanceof PersonalAccessToken) {
                $token->delete();
            }

            throw new DomainError('accountBanned', 403, 'This account is banned.');
        }

        return $next($request);
    }
}
