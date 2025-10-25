<?php

declare(strict_types=1);

namespace Src\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Quantity
{
    public function __construct(
        private int $value
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException('Quantity must be positive');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function add(Quantity $other): self
    {
        return new self($this->value + $other->value);
    }

    public function multiply(int $multiplier): self
    {
        if ($multiplier <= 0) {
            throw new InvalidArgumentException('Multiplier must be positive');
        }
        
        return new self($this->value * $multiplier);
    }

    public function equals(Quantity $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }
}

