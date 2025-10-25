<?php

declare(strict_types=1);

namespace Src\Infrastructure\ExternalServices;

use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\InvoiceId;

/**
 * Notification Facade Interface
 * Defines the contract for sending notifications
 */
interface NotificationFacadeInterface
{
    /**
     * Send invoice notification email to customer
     * 
     * @param InvoiceId $invoiceId
     * @param CustomerEmail $customerEmail
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function sendInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail,
        string $subject,
        string $message
    ): void;
    
    /**
     * Send a default invoice notification with hardcoded subject and message
     * 
     * @param InvoiceId $invoiceId
     * @param CustomerEmail $customerEmail
     * @return void
     */
    public function sendDefaultInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail
    ): void;
}
