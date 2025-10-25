<?php

declare(strict_types=1);

namespace Src\Domain\Events;

use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\InvoiceId;

final readonly class InvoiceCreated
{
    public function __construct(
        public InvoiceId $invoiceId,
        public CustomerEmail $customerEmail
    ) {}
}


