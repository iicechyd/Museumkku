<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {
        if ($request->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
        Paginator::useBootstrapFour();
        // Register Login Event Listener
        Event::listen(Login::class, [LogSuccessfulLogin::class, 'handle']);

        // Register Logout Event Listener
        Event::listen(Logout::class, [LogSuccessfulLogout::class, 'handle']);
    }
}
