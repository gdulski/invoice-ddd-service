<?php

declare(strict_types=1);

namespace Src\Application\DTOs;

final readonly class InvoiceResponseDto
{
    /**
     * @param InvoiceLineResponseDto[] $lines
     */
    public function __construct(
        public string $id,
        public string $status,
        public string $customerName,
        public string $customerEmail,
        public array $lines,
        public int $totalPriceInCents,
        public string $createdAt
    ) {}
}


