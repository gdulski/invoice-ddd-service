<?php

declare(strict_types=1);

namespace Src\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        private int $amountInCents
    ) {
        if ($amountInCents < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative');
        }
    }

    public function amountInCents(): int
    {
        return $this->amountInCents;
    }

    public function amount(): float
    {
        return $this->amountInCents / 100;
    }

    public function add(Money $other): self
    {
        return new self($this->amountInCents + $other->amountInCents);
    }

    public function multiply(int $multiplier): self
    {
        if ($multiplier < 0) {
            throw new InvalidArgumentException('Multiplier cannot be negative');
        }
        
        return new self($this->amountInCents * $multiplier);
    }

    public function equals(Money $other): bool
    {
        return $this->amountInCents === $other->amountInCents;
    }

    public function isZero(): bool
    {
        return $this->amountInCents === 0;
    }

    public function __toString(): string
    {
        return number_format($this->amount(), 2);
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public static function fromAmount(float $amount): self
    {
        return new self((int) round($amount * 100));
    }

    public static function zero(): self
    {
        return new self(0);
    }
}


