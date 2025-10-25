<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Services\HealthService;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Infrastructure\Persistence\Repositories\EloquentInvoiceRepository;
use Src\Application\Handlers\CreateInvoiceHandler;
use Src\Application\Handlers\ViewInvoiceHandler;
use Src\Application\Handlers\SendInvoiceHandler;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(HealthService::class);
        
        // Repository bindings
        $this->app->bind(InvoiceRepositoryInterface::class, EloquentInvoiceRepository::class);
        
        // Handler bindings
        $this->app->bind(CreateInvoiceHandler::class);
        $this->app->bind(ViewInvoiceHandler::class);
        $this->app->bind(SendInvoiceHandler::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
