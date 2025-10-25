<?php

declare(strict_types=1);

namespace Src\Application\Handlers;

use Src\Application\DTOs\InvoiceResponseDto;
use Src\Application\DTOs\SendInvoiceCommand;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Domain\ValueObjects\InvoiceId;
use Illuminate\Contracts\Events\Dispatcher;

final class SendInvoiceHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private Dispatcher $eventDispatcher
    ) {}

    public function handle(SendInvoiceCommand $command): InvoiceResponseDto
    {
        $invoiceId = new InvoiceId($command->invoiceId);
        $invoice = $this->invoiceRepository->findById($invoiceId);
        
        if (!$invoice) {
            throw new \DomainException('Invoice not found');
        }
        
        $invoice->send();
        
        $this->invoiceRepository->save($invoice);
        
        // Dispatch domain events
        foreach ($invoice->domainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
        
        $invoice->clearDomainEvents();
        
        return $this->mapToResponseDto($invoice);
    }
    
    private function mapToResponseDto(\Src\Domain\Entities\Invoice $invoice): InvoiceResponseDto
    {
        $lineDtos = [];
        foreach ($invoice->lines() as $line) {
            $lineDtos[] = new \Src\Application\DTOs\InvoiceLineResponseDto(
                $line->productName()->value(),
                $line->quantity()->value(),
                $line->unitPrice()->amountInCents(),
                $line->totalPrice()->amountInCents()
            );
        }
        
        return new InvoiceResponseDto(
            $invoice->id()->value(),
            $invoice->status()->value,
            $invoice->customerName()->value(),
            $invoice->customerEmail()->value(),
            $lineDtos,
            $invoice->totalPrice()->amountInCents(),
            $invoice->createdAt()->format('Y-m-d H:i:s')
        );
    }
}

