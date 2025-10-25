<?php

declare(strict_types=1);

namespace Src\Application\Handlers;

use Src\Application\DTOs\InvoiceResponseDto;
use Src\Application\DTOs\MarkInvoiceAsSentCommand;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Domain\ValueObjects\InvoiceId;

final class MarkInvoiceAsSentHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function handle(MarkInvoiceAsSentCommand $command): InvoiceResponseDto
    {
        $invoiceId = new InvoiceId($command->invoiceId);
        $invoice = $this->invoiceRepository->findById($invoiceId);
        
        if (!$invoice) {
            throw new \DomainException('Invoice not found');
        }
        
        $invoice->markAsSentToClient();
        
        $this->invoiceRepository->save($invoice);
        
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
