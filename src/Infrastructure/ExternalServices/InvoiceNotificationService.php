<?php

declare(strict_types=1);

namespace Src\Infrastructure\ExternalServices;

use Src\Domain\Events\InvoiceCreated;
use Src\Domain\Events\InvoiceSent;
use Illuminate\Support\Facades\Log;

final class InvoiceNotificationService
{
    public function __construct(
        private NotificationFacadeInterface $notificationFacade
    ) {}
    
    public function handleInvoiceCreated(InvoiceCreated $event): void
    {
        Log::info('Invoice created', [
            'invoice_id' => $event->invoiceId->value(),
            'customer_email' => $event->customerEmail->value(),
        ]);
        
        // Here you would integrate with your notification system
        // For example: send welcome email, create notification record, etc.
    }
    
    public function handleInvoiceSent(InvoiceSent $event): void
    {
        Log::info('Invoice sent', [
            'invoice_id' => $event->invoiceId->value(),
            'customer_email' => $event->customerEmail->value(),
        ]);
        
        // Send email notification to customer using NotificationFacade
        $this->notificationFacade->sendDefaultInvoiceNotification(
            $event->invoiceId,
            $event->customerEmail
        );
    }
}


