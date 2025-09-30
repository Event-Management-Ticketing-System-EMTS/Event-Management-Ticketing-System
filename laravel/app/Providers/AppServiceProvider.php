<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository bindings
        $this->app->bind(
            \App\Repositories\EventRepository::class,
            function ($app) {
                return new \App\Repositories\EventRepository($app->make(\App\Models\Event::class));
            }
        );

        $this->app->bind(
            \App\Repositories\UserRepository::class,
            function ($app) {
                return new \App\Repositories\UserRepository($app->make(\App\Models\User::class));
            }
        );

        // Register Service bindings
        $this->app->singleton(\App\Services\SortingService::class);
        $this->app->singleton(\App\Services\TicketAvailabilityService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Observer Pattern for automatic ticket updates
        \App\Models\Ticket::observe(\App\Observers\TicketObserver::class);

        // Force "remember me" cookies to expire in 10 minutes (instead of default ~5 years)
        Auth::viaRemember();

        $recallerName = Auth::getRecallerName(); // default: remember_web_xxxxx

        // If Laravel sets a remember cookie, reset its lifetime to 10 minutes
        if (Cookie::has($recallerName)) {
            $value = Cookie::get($recallerName);

            // Re-queue it with 10 minutes expiration
            Cookie::queue($recallerName, $value, 10);
        }
    }
}
