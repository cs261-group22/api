<?php

namespace App\Providers;

use App\Contracts\AnalyticsService;
use App\Services\MockAnalyticsService;
use App\Services\ProductionAnalyticsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            AnalyticsService::class,
            fn ($app) => config('cs261.analytics.mock')
                ? new MockAnalyticsService()
                : new ProductionAnalyticsService()
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
