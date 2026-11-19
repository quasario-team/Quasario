<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // The reset link opens the React page, not a Laravel page.
        ResetPassword::createUrlUsing(fn($user, string $token) => config('app.frontend_url')
            . '/reset-password?token=' . $token
            . '&email=' . urlencode($user->email));
    }
}
