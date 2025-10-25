<?php

declare(strict_types=1);

namespace Src\Application\DTOs;

final readonly class InvoiceLineDto
{
    public function __construct(
        public string $productName,
        public int $quantity,
        public int $unitPriceInCents
    ) {}
}


