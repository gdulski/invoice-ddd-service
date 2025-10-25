<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

use Src\Domain\ValueObjects\Money;
use Src\Domain\ValueObjects\ProductName;
use Src\Domain\ValueObjects\Quantity;

final readonly class InvoiceLine
{
    public function __construct(
        private ProductName $productName,
        private Quantity $quantity,
        private Money $unitPrice
    ) {}

    public function productName(): ProductName
    {
        return $this->productName;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function totalPrice(): Money
    {
        return $this->unitPrice->multiply($this->quantity->value());
    }
}


