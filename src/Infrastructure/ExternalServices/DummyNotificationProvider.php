<?php

declare(strict_types=1);

namespace Src\Infrastructure\ExternalServices;

use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Domain\ValueObjects\NotificationProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

final class DummyNotificationProvider implements NotificationProviderInterface
{

    public function __construct(
        private string $webhookUrl
    ) {}

    public function sendInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail,
        string $subject,
        string $message
    ): void {
        Log::info('Dummy notification provider: simulating notification send', [
            'invoice_id' => $invoiceId->value(),
            'customer_email' => $customerEmail->value(),
            'subject' => $subject,
        ]);

        // Simulate notification sending (in real scenario, would actually send email)
        // For Dummy provider, we just simulate success immediately
        
        // Trigger webhook to simulate delivery after short delay
        $this->simulateDelivery($invoiceId);
    }

    public function sendDefaultInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail
    ): void {
        $subject = 'Your Invoice is Ready';
        $message = "Dear Customer,\n\nYour invoice #{$invoiceId->value()} has been prepared and is ready for review.\n\nThank you for your business!\n\nBest regards,\nInvoice Management System";
        
        $this->sendInvoiceNotification($invoiceId, $customerEmail, $subject, $message);
    }

    private function simulateDelivery(InvoiceId $invoiceId): void
    {
        // In a real implementation, this would be async (queue job)
        // For now, we'll trigger the webhook synchronously after simulating a delay
        
        // Dispatch to background job would be better, but for simplicity:
        try {
            Log::info('Dummy notification provider: simulating successful delivery', [
                'invoice_id' => $invoiceId->value(),
            ]);

            // Trigger the webhook to notify about successful delivery
            Http::timeout(5)->post($this->webhookUrl, [
                'invoice_id' => $invoiceId->value(),
                'provider' => NotificationProvider::DUMMY->value,
            ]);

            Log::info('Dummy notification provider: delivery webhook triggered successfully', [
                'invoice_id' => $invoiceId->value(),
            ]);

        } catch (\Exception $e) {
            Log::error('Dummy notification provider: failed to trigger delivery webhook', [
                'invoice_id' => $invoiceId->value(),
                'error' => $e->getMessage(),
            ]);
            
            // For dummy provider, we still consider it delivered even if webhook fails
            // In production, you might want to retry or handle this differently
        }
    }

    public function getName(): NotificationProvider
    {
        return NotificationProvider::DUMMY;
    }
}

