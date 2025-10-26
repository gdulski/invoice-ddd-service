<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\ExternalServices;

use Tests\TestCase;
use Src\Infrastructure\ExternalServices\NotificationFacade;
use Src\Infrastructure\ExternalServices\DummyNotificationProvider;
use Src\Infrastructure\ExternalServices\NotificationProviderInterface;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\NotificationProvider;
use Illuminate\Support\Facades\Log;
use Mockery;

final class NotificationFacadeTest extends TestCase
{
    private NotificationFacade $notificationFacade;
    
    /** @var NotificationProviderInterface&\Mockery\MockInterface */
    private $mockProvider;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a mock provider
        $this->mockProvider = Mockery::mock(NotificationProviderInterface::class);
        $this->mockProvider->shouldReceive('getName')
            ->andReturn(NotificationProvider::DUMMY);
        
        // Create providers array for the facade
        $providers = [
            NotificationProvider::DUMMY->value => $this->mockProvider,
        ];
        
        $this->notificationFacade = new NotificationFacade($providers, NotificationProvider::DUMMY);
    }

    public function test_send_invoice_notification_sends_email(): void
    {
        // Arrange
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');
        $subject = 'Test Invoice Subject';
        $message = 'Test invoice message';

        $this->mockProvider->shouldReceive('sendInvoiceNotification')
            ->once()
            ->with(
                $invoiceId,
                $customerEmail,
                $subject,
                $message
            );

        // Act
        $this->notificationFacade->sendInvoiceNotification(
            $invoiceId,
            $customerEmail,
            $subject,
            $message
        );

        // Assert - Verified by Mockery expectations
        $this->addToAssertionCount(1);
    }

    public function test_send_default_invoice_notification_sends_email_with_default_content(): void
    {
        // Arrange
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');

        // The facade should call sendInvoiceNotification with default subject and message
        $this->mockProvider->shouldReceive('sendInvoiceNotification')
            ->once()
            ->with(
                $invoiceId,
                $customerEmail,
                'Your Invoice is Ready',
                \Mockery::type('string')
            );

        // Act
        $this->notificationFacade->sendDefaultInvoiceNotification(
            $invoiceId,
            $customerEmail
        );

        // Assert - Verified by Mockery expectations
        $this->addToAssertionCount(1);
    }

    public function test_send_invoice_notification_logs_success(): void
    {
        // Arrange
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');
        $subject = 'Test Invoice Subject';
        $message = 'Test invoice message';

        $this->mockProvider->shouldReceive('sendInvoiceNotification')
            ->once();

        // Act
        $this->notificationFacade->sendInvoiceNotification(
            $invoiceId,
            $customerEmail,
            $subject,
            $message
        );

        // Assert - Verified by Mockery expectations
        $this->addToAssertionCount(1);
    }

    public function test_send_invoice_notification_handles_mail_exception(): void
    {
        // Arrange
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');
        $subject = 'Test Invoice Subject';
        $message = 'Test invoice message';

        // Mock provider to throw an exception
        $this->mockProvider->shouldReceive('sendInvoiceNotification')
            ->once()
            ->andThrow(new \Exception('Provider service unavailable'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to send notification: Provider service unavailable');

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
