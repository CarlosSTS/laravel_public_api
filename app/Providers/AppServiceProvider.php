<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        // rate limiters are now defined in the application's configuration
        RateLimiter::for('api', function ($request) {
            $RATE_LIMIT = config('app.rate_limit', 0); // default to 60 requests per minute if not set
            if ($RATE_LIMIT > 0) {
                return Limit::perMinute($RATE_LIMIT)->by($request->ip());
            } else {
                return Limit::none();
            }
        });
    }
}
