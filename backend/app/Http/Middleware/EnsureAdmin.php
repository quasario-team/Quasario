<?php

namespace App\Http\Middleware;

use App\Exceptions\DomainError;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Admin-only routes.
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            throw new DomainError('notAdmin', 403, 'Only an admin can do this.');
        }

        return $next($request);
    }
}
