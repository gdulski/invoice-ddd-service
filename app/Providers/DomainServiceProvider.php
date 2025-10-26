<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Services\HealthService;
use Src\Domain\Services\InvoiceStatusTransition;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Domain\ValueObjects\NotificationProvider;
use Src\Infrastructure\Persistence\Repositories\EloquentInvoiceRepository;
use Src\Infrastructure\ExternalServices\NotificationFacade;
use Src\Infrastructure\ExternalServices\NotificationFacadeInterface;
use Src\Infrastructure\ExternalServices\NotificationProviderInterface;
use Src\Infrastructure\ExternalServices\DummyNotificationProvider;
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
        
        // Notification provider binding - register all providers
        $this->app->singleton('notification.providers', function ($app) {
            $webhookUrl = config('app.url') . '/api/webhooks/notification-delivered';
            
            return [
                NotificationProvider::DUMMY->value => new DummyNotificationProvider($webhookUrl),
                // NotificationProvider::SMTP->value => new SmtpNotificationProvider(...),
                // NotificationProvider::SENDGRID->value => new SendgridNotificationProvider(...),
            ];
        });
        
        // Notification facade binding - orkiestruje wiele providerów
        $this->app->singleton(NotificationFacadeInterface::class, function ($app) {
            $providers = $app->make('notification.providers');
            
            // Wczytaj domyślny provider z .env lub użyj DUMMY
            $defaultProviderName = env('NOTIFICATION_DEFAULT_PROVIDER', NotificationProvider::DUMMY->value);
            $defaultProvider = NotificationProvider::fromString($defaultProviderName);
            
            return new NotificationFacade($providers, $defaultProvider);
        });
        
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
