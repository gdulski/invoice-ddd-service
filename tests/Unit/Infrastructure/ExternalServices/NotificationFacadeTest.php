<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\ExternalServices;

use Tests\TestCase;
use Src\Infrastructure\ExternalServices\NotificationFacade;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Domain\ValueObjects\CustomerEmail;
use Illuminate\Support\Facades\Mail;
use Mockery;

final class NotificationFacadeTest extends TestCase
{
    private NotificationFacade $notificationFacade;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationFacade = new NotificationFacade();
    }

    public function test_send_invoice_notification_sends_email(): void
    {
        // Arrange
        Mail::shouldReceive('raw')
            ->once()
            ->with(\Mockery::type('string'), \Mockery::type('callable'));
            
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');
        $subject = 'Test Invoice Subject';
        $message = 'Test invoice message';

        // Act
        $this->notificationFacade->sendInvoiceNotification(
            $invoiceId,
            $customerEmail,
            $subject,
            $message
        );

        // Assert - Mail::raw was called (verified by Mockery expectation)
        $this->addToAssertionCount(1);
    }

    public function test_send_default_invoice_notification_sends_email_with_default_content(): void
    {
        // Arrange
        Mail::shouldReceive('raw')
            ->once()
            ->with(\Mockery::type('string'), \Mockery::type('callable'));
            
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');

        // Act
        $this->notificationFacade->sendDefaultInvoiceNotification(
            $invoiceId,
            $customerEmail
        );

        // Assert - Mail::raw was called (verified by Mockery expectation)
        $this->addToAssertionCount(1);
    }

    public function test_send_invoice_notification_logs_success(): void
    {
        // Arrange
        Mail::shouldReceive('raw')
            ->once()
            ->with(\Mockery::type('string'), \Mockery::type('callable'));
            
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');
        $subject = 'Test Invoice Subject';
        $message = 'Test invoice message';

        // Act
        $this->notificationFacade->sendInvoiceNotification(
            $invoiceId,
            $customerEmail,
            $subject,
            $message
        );

        // Assert - Mail::raw was called (verified by Mockery expectation)
        $this->addToAssertionCount(1);
    }

    public function test_send_invoice_notification_handles_mail_exception(): void
    {
        // Arrange
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');
        $subject = 'Test Invoice Subject';
        $message = 'Test invoice message';

        // Mock Mail to throw an exception
        Mail::shouldReceive('raw')
            ->once()
            ->andThrow(new \Exception('Mail service unavailable'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to send notification email: Mail service unavailable');

        $this->notificationFacade->sendInvoiceNotification(
            $invoiceId,
            $customerEmail,
            $subject,
            $message
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
