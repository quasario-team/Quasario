<?php

use App\Exceptions\DomainError;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureNotAdmin;
use App\Http\Middleware\EnsureNotBanned;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Short names used in routes/api.php, e.g. ->middleware(['auth:sanctum', 'notBanned']).
        $middleware->alias([
            'notBanned' => EnsureNotBanned::class,
            'admin' => EnsureAdmin::class,
            'notAdmin' => EnsureNotAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Every response under /api is JSON, never an HTML error page.
        $exceptions->shouldRenderJsonWhen(fn(Request $request) => $request->is('api/*'));

        $exceptions->render(fn(DomainError $e) => response()->json([
            'message' => $e->getMessage(),
            'code' => $e->errorCode,
        ], $e->status));

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.', 'code' => 'unauthenticated'], 401);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Forbidden.', 'code' => 'forbidden'], 403);
            }
        });

        // Also covers ModelNotFoundException (Laravel converts it to NotFoundHttpException first).
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Not found.', 'code' => 'notFound'], 404);
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Too many requests.', 'code' => 'tooManyRequests'], 429);
            }
        });
    })->create();
