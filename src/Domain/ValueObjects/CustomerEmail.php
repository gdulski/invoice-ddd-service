<?php

declare(strict_types=1);

namespace Src\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class CustomerEmail
{
    public function __construct(
        private string $value
    ) {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Customer email cannot be empty');
        }
        
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        
        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Customer email cannot exceed 255 characters');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CustomerEmail $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

