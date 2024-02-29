<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DhlService;

class DhlServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(DhlService::class, function () {
            return new DhlService(config('services.dhl'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
