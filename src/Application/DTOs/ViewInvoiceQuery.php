<?php

declare(strict_types=1);

namespace Src\Application\DTOs;

final readonly class ViewInvoiceQuery
{
    public function __construct(
        public string $invoiceId
    ) {}
}


