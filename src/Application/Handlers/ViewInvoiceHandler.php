<?php

declare(strict_types=1);

namespace Src\Application\Handlers;

use Src\Application\DTOs\InvoiceResponseDto;
use Src\Application\DTOs\ViewInvoiceQuery;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Domain\ValueObjects\InvoiceId;

final class ViewInvoiceHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function handle(ViewInvoiceQuery $query): InvoiceResponseDto
    {
        $invoiceId = new InvoiceId($query->invoiceId);
        $invoice = $this->invoiceRepository->findById($invoiceId);
        
        if (!$invoice) {
            throw new \DomainException('Invoice not found');
        }
        
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

