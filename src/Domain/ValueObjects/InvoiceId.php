<?php

declare(strict_types=1);

namespace Src\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class InvoiceId
{
    public function __construct(
        private string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('Invoice ID cannot be empty');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(InvoiceId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function generate(): self
    {
        return new self(uniqid('inv_', true));
    }
}


