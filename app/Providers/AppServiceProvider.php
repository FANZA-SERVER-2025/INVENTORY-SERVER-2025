<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS scheme for all URLs
        URL::forceScheme('https');
        
        // Force root URL to use https
        if (str_starts_with(config('app.url'), 'https://')) {
            $this->app['url']->forceRootUrl(config('app.url'));
        }
        
        // Force asset URLs to use HTTPS
        URL::forceRootUrl(config('app.url'));
        
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });
    }
}
