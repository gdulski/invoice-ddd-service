<?php

declare(strict_types=1);

namespace Src\Application\DTOs;

final readonly class UpdateInvoiceStatusCommand
{
    public function __construct(
        public string $invoiceId,
        public string $status
    ) {}
}
