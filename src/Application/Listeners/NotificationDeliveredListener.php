<?php

declare(strict_types=1);

namespace Src\Application\Listeners;

use Src\Domain\Events\NotificationDelivered;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Domain\ValueObjects\InvoiceId;
use Illuminate\Support\Facades\Log;

final class NotificationDeliveredListener
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository
    ) {}
    
    public function handle(NotificationDelivered $event): void
    {
        Log::info('Notification delivered', [
            'invoice_id' => $event->invoiceId->value(),
            'provider' => $event->provider,
            'delivered_at' => $event->deliveredAt->format('Y-m-d H:i:s'),
        ]);
        
        $invoice = $this->invoiceRepository->findById($event->invoiceId);
        
        if (!$invoice) {
            Log::warning('Invoice not found when processing notification delivery', [
                'invoice_id' => $event->invoiceId->value(),
            ]);
            return;
        }
        
        // Update invoice status from sending to sent-to-client
        $invoice->markAsSentToClient();
        
        // Save the updated invoice
        $this->invoiceRepository->save($invoice);
        
        Log::info('Invoice status updated to sent-to-client', [
            'invoice_id' => $event->invoiceId->value(),
        ]);
    }
}

