<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Services\HealthService;
use Src\Domain\Services\InvoiceStatusTransition;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Infrastructure\Persistence\Repositories\EloquentInvoiceRepository;
use Src\Infrastructure\ExternalServices\NotificationFacade;
use Src\Infrastructure\ExternalServices\NotificationFacadeInterface;
use Src\Application\Handlers\CreateInvoiceHandler;
use Src\Application\Handlers\ViewInvoiceHandler;
use Src\Application\Handlers\SendInvoiceHandler;
use Src\Application\Handlers\UpdateInvoiceStatusHandler;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(HealthService::class);
        $this->app->singleton(InvoiceStatusTransition::class);
        $this->app->singleton(NotificationFacadeInterface::class, NotificationFacade::class);
        
        // Repository bindings
        $this->app->bind(InvoiceRepositoryInterface::class, EloquentInvoiceRepository::class);
        
        // Handler bindings
        $this->app->bind(CreateInvoiceHandler::class);
        $this->app->bind(ViewInvoiceHandler::class);
        $this->app->bind(SendInvoiceHandler::class);
        $this->app->bind(UpdateInvoiceStatusHandler::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
