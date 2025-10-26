<?php

declare(strict_types=1);

namespace Src\Domain\ValueObjects;

/**
 * Notification Provider Enum
 * Defines available notification providers in the system
 */
enum NotificationProvider: string
{
    case DUMMY = 'dummy';
    case SMTP = 'smtp';
    case SENDGRID = 'sendgrid';
    case MAILGUN = 'mailgun';
    case SLACK = 'slack';
    case SMS = 'sms';

    public function getName(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}

