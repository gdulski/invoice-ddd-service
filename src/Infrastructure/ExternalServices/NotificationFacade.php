<?php

declare(strict_types=1);

namespace Src\Infrastructure\ExternalServices;

use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Domain\ValueObjects\NotificationProvider as NotificationProviderEnum;
use Illuminate\Support\Facades\Log;

/**
 * Notification Facade
 * TRUE Facade Pattern - orkiestruje wiele notification providers
 * Agreguje operacje z wielu komponentów (email, SMS, Slack, etc.)
 */
final class NotificationFacade implements NotificationFacadeInterface
{
    /** @var NotificationProviderInterface[] Map of providers by NotificationProvider enum key */
    private array $providers;
    
    private ?NotificationProviderInterface $defaultProvider;
    
    private NotificationProviderEnum $defaultProviderEnum;

    /**
     * @param array<string, NotificationProviderInterface> $providers Map of provider_name => provider
     * @param NotificationProviderEnum|null $defaultProviderEnum
     */
    public function __construct(array $providers, ?NotificationProviderEnum $defaultProviderEnum = null)
    {
        $this->providers = $providers;
        $this->defaultProviderEnum = $defaultProviderEnum ?? NotificationProviderEnum::DUMMY;
        $this->defaultProvider = $providers[$this->defaultProviderEnum->value] ?? null;
        
        if (!$this->defaultProvider) {
            throw new \InvalidArgumentException("Default provider '{$this->defaultProviderEnum->value}' not found in providers list");
        }
    }

    /**
     * Send invoice notification via default provider
     */
    public function sendInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail,
        string $subject,
        string $message
    ): void {
        if (!$this->defaultProvider) {
            throw new \RuntimeException('No notification provider configured');
        }

        $this->sendViaProviderInstance($this->defaultProvider, $invoiceId, $customerEmail, $subject, $message);
    }

    /**
     * Send via default provider with default template
     */
    public function sendDefaultInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail
    ): void {
        $subject = 'Your Invoice is Ready';
        $message = "Dear Customer,\n\nYour invoice #{$invoiceId->value()} has been prepared and is ready for review.\n\nThank you for your business!\n\nBest regards,\nInvoice Management System";
        
        $this->sendInvoiceNotification($invoiceId, $customerEmail, $subject, $message);
    }

    /**
     * Send via specific provider by enum
     */
    public function sendViaProvider(
        NotificationProviderEnum $provider,
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail,
        string $subject,
        string $message
    ): void {
        $providerInstance = $this->providers[$provider->value] ?? null;
        
        if (!$providerInstance) {
            throw new \InvalidArgumentException("Provider '{$provider->value}' not found");
        }

        $this->sendViaProviderInstance($providerInstance, $invoiceId, $customerEmail, $subject, $message);
    }

    /**
     * Send via ALL providers (true multi-channel notification)
     */
    public function sendViaAllProviders(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail,
        string $subject,
        string $message
    ): void {
        $errors = [];
        
        foreach ($this->providers as $name => $provider) {
            try {
                $provider->sendInvoiceNotification($invoiceId, $customerEmail, $subject, $message);
                Log::info('Notification sent via provider', [
                    'invoice_id' => $invoiceId->value(),
                    'provider' => $name,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send via provider', [
                    'invoice_id' => $invoiceId->value(),
                    'provider' => $name,
                    'error' => $e->getMessage(),
                ]);
                $errors[$name] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            throw new \RuntimeException('Some providers failed: ' . json_encode($errors));
        }
    }

    /**
     * Send via specific provider instance
     */
    private function sendViaProviderInstance(
        NotificationProviderInterface $provider,
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail,
        string $subject,
        string $message
    ): void {
        try {
            $provider->sendInvoiceNotification($invoiceId, $customerEmail, $subject, $message);
            
            Log::info('Invoice notification sent via provider', [
                'invoice_id' => $invoiceId->value(),
                'customer_email' => $customerEmail->value(),
                'subject' => $subject,
                'provider' => $provider->getName()->value,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send invoice notification', [
                'invoice_id' => $invoiceId->value(),
                'customer_email' => $customerEmail->value(),
                'provider' => $provider->getName()->value,
                'error' => $e->getMessage(),
            ]);
            
            throw new \RuntimeException('Failed to send notification: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get all available providers
     * @return NotificationProviderEnum[]
     */
    public function getAvailableProviders(): array
    {
        $providers = [];
        foreach (array_keys($this->providers) as $name) {
            try {
                $providers[] = NotificationProviderEnum::fromString($name);
            } catch (\ValueError $e) {
                // Ignore invalid enum values
            }
        }
        return $providers;
    }
}
