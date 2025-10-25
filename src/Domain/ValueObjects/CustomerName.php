<?php

declare(strict_types=1);

namespace Src\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class CustomerName
{
    public function __construct(
        private string $value
    ) {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Customer name cannot be empty');
        }
        
        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Customer name cannot exceed 255 characters');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CustomerName $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}


