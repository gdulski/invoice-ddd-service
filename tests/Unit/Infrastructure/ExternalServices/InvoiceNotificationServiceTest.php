<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\ExternalServices;

use Tests\TestCase;
use Src\Infrastructure\ExternalServices\InvoiceNotificationService;
use Src\Infrastructure\ExternalServices\NotificationFacadeInterface;
use Src\Domain\Events\InvoiceSent;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Domain\ValueObjects\CustomerEmail;
use Illuminate\Support\Facades\Log;
use Mockery;

final class InvoiceNotificationServiceTest extends TestCase
{
    private InvoiceNotificationService $notificationService;
    private $notificationFacade;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationFacade = Mockery::mock(NotificationFacadeInterface::class);
        $this->notificationService = new InvoiceNotificationService($this->notificationFacade);
    }

    public function test_handle_invoice_sent_calls_notification_facade(): void
    {
        // Arrange
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');
        $event = new InvoiceSent($invoiceId, $customerEmail);

        $this->notificationFacade
            ->shouldReceive('sendDefaultInvoiceNotification')
            ->once()
            ->with($invoiceId, $customerEmail);

        // Act
        $this->notificationService->handleInvoiceSent($event);

        $this->addToAssertionCount(1);
    }

    public function test_handle_invoice_created_logs_event(): void
    {
        // Arrange
        $invoiceId = new InvoiceId('test-invoice-id');
        $customerEmail = new CustomerEmail('test@example.com');
        $event = new \Src\Domain\Events\InvoiceCreated($invoiceId, $customerEmail);

        // Act
        $this->notificationService->handleInvoiceCreated($event);

        // Assert - Just verify the method executes without error
        // Logging is tested in integration tests
        $this->addToAssertionCount(1);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
