<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\CustomerName;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Domain\ValueObjects\InvoiceStatus;
use Src\Domain\ValueObjects\Money;
use Src\Domain\ValueObjects\ProductName;
use Src\Domain\ValueObjects\Quantity;
use Src\Domain\Events\InvoiceCreated;
use Src\Domain\Events\InvoiceSent;
use InvalidArgumentException;

final class Invoice
{
    /** @var InvoiceLine[] */
    private array $lines = [];
    private array $domainEvents = [];

    public function __construct(
        private readonly InvoiceId $id,
        private InvoiceStatus $status,
        private readonly CustomerName $customerName,
        private readonly CustomerEmail $customerEmail,
        private readonly \DateTimeImmutable $createdAt
    ) {
        $this->addDomainEvent(new InvoiceCreated($this->id, $this->customerEmail));
    }

    public function id(): InvoiceId
    {
        return $this->id;
    }

    public function status(): InvoiceStatus
    {
        return $this->status;
    }

    public function customerName(): CustomerName
    {
        return $this->customerName;
    }

    public function customerEmail(): CustomerEmail
    {
        return $this->customerEmail;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function lines(): array
    {
        return $this->lines;
    }

    public function addLine(ProductName $productName, Quantity $quantity, Money $unitPrice): void
    {
        $line = new InvoiceLine($productName, $quantity, $unitPrice);
        $this->lines[] = $line;
    }

    public function removeLine(int $index): void
    {
        if (!isset($this->lines[$index])) {
            throw new InvalidArgumentException('Invoice line not found');
        }
        
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines); // Re-index array
    }

    public function totalPrice(): Money
    {
        $total = Money::zero();
        
        foreach ($this->lines as $line) {
            $total = $total->add($line->totalPrice());
        }
        
        return $total;
    }

    public function canBeSent(): bool
    {
        return $this->status->canBeSent() && !empty($this->lines);
    }

    public function send(): void
    {
        if (!$this->canBeSent()) {
            throw new InvalidArgumentException('Invoice cannot be sent in current state');
        }
        
        $this->status = InvoiceStatus::SENDING;
        $this->addDomainEvent(new InvoiceSent($this->id, $this->customerEmail));
    }

    public function markAsSentToClient(): void
    {
        if (!$this->status->isSending()) {
            throw new InvalidArgumentException('Invoice must be in sending status to mark as sent');
        }
        
        $this->status = InvoiceStatus::SENT_TO_CLIENT;
    }

    public function domainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    private function addDomainEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public static function create(
        CustomerName $customerName,
        CustomerEmail $customerEmail,
        array $lines = []
    ): self {
        $invoice = new self(
            InvoiceId::generate(),
            InvoiceStatus::DRAFT,
            $customerName,
            $customerEmail,
            new \DateTimeImmutable()
        );

        foreach ($lines as $line) {
            $invoice->addLine($line['productName'], $line['quantity'], $line['unitPrice']);
        }

        return $invoice;
    }
}

