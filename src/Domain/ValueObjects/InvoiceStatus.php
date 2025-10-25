<?php

declare(strict_types=1);

namespace Src\Domain\ValueObjects;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case SENDING = 'sending';
    case SENT_TO_CLIENT = 'sent-to-client';

    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }

    public function isSending(): bool
    {
        return $this === self::SENDING;
    }

    public function isSentToClient(): bool
    {
        return $this === self::SENT_TO_CLIENT;
    }

    public function canBeSent(): bool
    {
        return $this->isDraft();
    }

    public function equals(InvoiceStatus $other): bool
    {
        return $this === $other;
    }

    public function toString(): string
    {
        return $this->value;
    }
}

