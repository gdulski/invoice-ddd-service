<?php

declare(strict_types=1);

namespace Src\Domain\Events;

use Src\Domain\ValueObjects\InvoiceId;

final readonly class NotificationDelivered
{
    public function __construct(
        public InvoiceId $invoiceId,
        public string $provider,
        public \DateTimeImmutable $deliveredAt
    ) {}
}

