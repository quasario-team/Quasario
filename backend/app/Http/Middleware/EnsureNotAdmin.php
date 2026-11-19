<?php

namespace App\Http\Middleware;

use App\Exceptions\DomainError;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// User-only routes (profiles, messages...). An admin account has no profiles.
class EnsureNotAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin()) {
            throw new DomainError('adminNotAllowed', 403, 'An admin account cannot do this.');
        }

        return $next($request);
    }
}
