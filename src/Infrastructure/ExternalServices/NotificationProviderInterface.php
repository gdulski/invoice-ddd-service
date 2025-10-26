<?php

declare(strict_types=1);

namespace Src\Infrastructure\ExternalServices;

use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Domain\ValueObjects\NotificationProvider as NotificationProviderEnum;

interface NotificationProviderInterface
{
    public function sendInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail,
        string $subject,
        string $message
    ): void;
    
    public function sendDefaultInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail
    ): void;

    public function getName(): NotificationProviderEnum;
}

