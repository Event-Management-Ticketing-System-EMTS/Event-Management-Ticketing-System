<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

// Models & Observer
use App\Models\Ticket;
use App\Observers\TicketObserver;

// Repositories
use App\Repositories\EventRepository;
use App\Repositories\UserRepository;

// Services
use App\Services\SortingService;
use App\Services\SimpleTicketService;
use App\Services\SimpleNotificationService;
use App\Services\SimpleBookingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* ------------------------------
         * Repository bindings
         * ------------------------------ */
        $this->app->bind(EventRepository::class, function ($app) {
            return new EventRepository($app->make(\App\Models\Event::class));
        });

        $this->app->bind(UserRepository::class, function ($app) {
            return new UserRepository($app->make(\App\Models\User::class));
        });

        /* ------------------------------
         * Service singletons
         * ------------------------------ */
        $this->app->singleton(SortingService::class);
        $this->app->singleton(SimpleTicketService::class);
        $this->app->singleton(SimpleNotificationService::class);

        // Needed by TicketObserver (to clear cached booking stats/lists)
        $this->app->singleton(SimpleBookingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /* ------------------------------
         * Observers
         * ------------------------------ */
        Ticket::observe(TicketObserver::class);

        /* ------------------------------
         * Optional: shorten "remember me" cookie lifetime to 10 minutes
         * ------------------------------ */
        // This line only checks if the current auth session is via "remember me"
        // (it doesn’t change cookie lifetime by itself, the queue below does).
        Auth::viaRemember();

        $recallerName = Auth::getRecallerName(); // default: remember_web_xxxxx

        if (Cookie::has($recallerName)) {
            $value = Cookie::get($recallerName);

            // Re-queue the cookie with a 10-minute expiration
            // Note: minutes are integer minutes, not seconds.
            Cookie::queue($recallerName, $value, 10);
        }
    }
}
