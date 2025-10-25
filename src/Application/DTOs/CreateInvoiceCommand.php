<?php

declare(strict_types=1);

namespace Src\Application\DTOs;

final readonly class CreateInvoiceCommand
{
    /**
     * @param InvoiceLineDto[] $lines
     */
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public array $lines
    ) {}
}


