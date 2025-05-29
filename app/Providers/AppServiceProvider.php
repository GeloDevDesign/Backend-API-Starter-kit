<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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

        // OR DO IT GLOBALLY 
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        //YOU MAY DO THIS FOR SPECIFIC ROUTE 
        // RateLimiter::for('note', function (Request $request) {
        //     return Limit::perMinute(1)->by($request->user()?->id ?: $request->ip());
        // });
        
    }
}
