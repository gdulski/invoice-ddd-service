<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Src\Domain\ValueObjects\InvoiceStatus;

final class InvoiceStatusEnumTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertEquals('draft', InvoiceStatus::DRAFT->value);
        $this->assertEquals('sending', InvoiceStatus::SENDING->value);
        $this->assertEquals('sent-to-client', InvoiceStatus::SENT_TO_CLIENT->value);
    }

    public function test_enum_methods(): void
    {
        $draft = InvoiceStatus::DRAFT;
        $sending = InvoiceStatus::SENDING;
        $sent = InvoiceStatus::SENT_TO_CLIENT;

        $this->assertTrue($draft->isDraft());
        $this->assertFalse($draft->isSending());
        $this->assertFalse($draft->isSentToClient());
        $this->assertTrue($draft->canBeSent());

        $this->assertFalse($sending->isDraft());
        $this->assertTrue($sending->isSending());
        $this->assertFalse($sending->isSentToClient());
        $this->assertFalse($sending->canBeSent());

        $this->assertFalse($sent->isDraft());
        $this->assertFalse($sent->isSending());
        $this->assertTrue($sent->isSentToClient());
        $this->assertFalse($sent->canBeSent());
    }

    public function test_enum_equality(): void
    {
        $status1 = InvoiceStatus::DRAFT;
        $status2 = InvoiceStatus::DRAFT;
        $status3 = InvoiceStatus::SENDING;

        $this->assertTrue($status1->equals($status2));
        $this->assertFalse($status1->equals($status3));
        $this->assertTrue($status1 === $status2);
        $this->assertFalse($status1 === $status3);
    }

    public function test_enum_from_string(): void
    {
        $this->assertEquals(InvoiceStatus::DRAFT, InvoiceStatus::from('draft'));
        $this->assertEquals(InvoiceStatus::SENDING, InvoiceStatus::from('sending'));
        $this->assertEquals(InvoiceStatus::SENT_TO_CLIENT, InvoiceStatus::from('sent-to-client'));
    }

    public function test_enum_cases(): void
    {
        $cases = InvoiceStatus::cases();
        $this->assertCount(3, $cases);
        $this->assertContains(InvoiceStatus::DRAFT, $cases);
        $this->assertContains(InvoiceStatus::SENDING, $cases);
        $this->assertContains(InvoiceStatus::SENT_TO_CLIENT, $cases);
    }

    public function test_enum_to_string(): void
    {
        $this->assertEquals('draft', InvoiceStatus::DRAFT->toString());
        $this->assertEquals('sending', InvoiceStatus::SENDING->toString());
        $this->assertEquals('sent-to-client', InvoiceStatus::SENT_TO_CLIENT->toString());
    }
}
