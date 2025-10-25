<?php

declare(strict_types=1);

namespace Src\Application\Handlers;

use Src\Application\DTOs\CreateInvoiceCommand;
use Src\Application\DTOs\InvoiceResponseDto;
use Src\Domain\Entities\Invoice;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\CustomerName;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Domain\ValueObjects\Money;
use Src\Domain\ValueObjects\ProductName;
use Src\Domain\ValueObjects\Quantity;
use Illuminate\Contracts\Events\Dispatcher;

final class CreateInvoiceHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private Dispatcher $eventDispatcher
    ) {}

    public function handle(CreateInvoiceCommand $command): InvoiceResponseDto
    {
        $customerName = new CustomerName($command->customerName);
        $customerEmail = new CustomerEmail($command->customerEmail);
        
        $lines = [];
        foreach ($command->lines as $lineDto) {
            $lines[] = [
                'productName' => new ProductName($lineDto->productName),
                'quantity' => new Quantity($lineDto->quantity),
                'unitPrice' => Money::fromCents($lineDto->unitPriceInCents)
            ];
        }
        
        $invoice = Invoice::create($customerName, $customerEmail, $lines);
        
        $this->invoiceRepository->save($invoice);
        
        // Dispatch domain events
        foreach ($invoice->domainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
        
        $invoice->clearDomainEvents();
        
        return $this->mapToResponseDto($invoice);
    }
    
    private function mapToResponseDto(Invoice $invoice): InvoiceResponseDto
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

