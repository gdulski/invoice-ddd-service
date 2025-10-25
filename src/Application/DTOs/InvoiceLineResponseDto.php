<?php

declare(strict_types=1);

namespace Src\Application\DTOs;

final readonly class InvoiceLineResponseDto
{
    public function __construct(
        public string $productName,
        public int $quantity,
        public int $unitPriceInCents,
        public int $totalUnitPriceInCents
    ) {}
}


