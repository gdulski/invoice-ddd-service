<?php

declare(strict_types=1);

namespace Src\Application\Handlers;

use Src\Application\DTOs\InvoiceResponseDto;
use Src\Application\DTOs\UpdateInvoiceStatusCommand;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Domain\Services\InvoiceStatusTransition;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Domain\ValueObjects\InvoiceStatus;
use InvalidArgumentException;

final class UpdateInvoiceStatusHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private InvoiceStatusTransition $statusTransition
    ) {}

    public function handle(UpdateInvoiceStatusCommand $command): InvoiceResponseDto
    {
        $invoiceId = new InvoiceId($command->invoiceId);
        $invoice = $this->invoiceRepository->findById($invoiceId);
        
        if (!$invoice) {
            throw new \DomainException('Invoice not found');
        }
        
        // Map status string to InvoiceStatus enum
        $targetStatus = InvoiceStatus::tryFrom($command->status);
        if (!$targetStatus) {
            throw new InvalidArgumentException('Invalid status value');
        }
        
        // Delegate status transition to domain service
        $this->statusTransition->transitionTo($invoice, $targetStatus);
        
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
