<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Services\HealthService;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(HealthService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
