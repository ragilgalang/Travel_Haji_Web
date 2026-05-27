<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\FirebaseUserProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') !== 'local' && !app()->runningInConsole() && !in_array(request()->getHost(), ['127.0.0.1', 'localhost'])) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Auth::provider('firebase', function ($app, array $config) {
            return new FirebaseUserProvider($app->make(\Kreait\Firebase\Contract\Auth::class));
        });

        // Matikan pembatasan 429 yang kaku (naikkan limit ke 1000 per menit)
        \Illuminate\Support\Facades\RateLimiter::for('global', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(1000)->by($request->ip());
        });

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            static $siteData = null;
            if ($siteData === null) {
                // Cache data for better performance
                $siteData = \Illuminate\Support\Facades\Cache::remember('site_global_data', 60*24, function() {
                    $firebase = app(\App\Services\FirebaseService::class);
                    return [
                        'settings' => $firebase->getValue('settings') ?? [],
                        'packages' => collect($firebase->getValue('packages') ?? [])
                    ];
                });
            }
            $view->with('settings', $siteData['settings']);
            $view->with('packages', $siteData['packages']);
        });
    }
}
