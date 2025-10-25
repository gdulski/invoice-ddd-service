<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Repositories;

use Src\Domain\Entities\Invoice;
use Src\Domain\Repositories\InvoiceRepositoryInterface;
use Src\Domain\ValueObjects\InvoiceId;
use Src\Infrastructure\Persistence\Models\InvoiceModel;
use Src\Infrastructure\Persistence\Models\InvoiceLineModel;
use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\CustomerName;
use Src\Domain\ValueObjects\InvoiceStatus;
use Src\Domain\ValueObjects\Money;
use Src\Domain\ValueObjects\ProductName;
use Src\Domain\ValueObjects\Quantity;

final class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void
    {
        $invoiceModel = InvoiceModel::find($invoice->id()->value());
        
        if (!$invoiceModel) {
            $invoiceModel = new InvoiceModel();
            $invoiceModel->id = $invoice->id()->value();
        }
        
        $invoiceModel->status = $invoice->status()->value;
        $invoiceModel->customer_name = $invoice->customerName()->value();
        $invoiceModel->customer_email = $invoice->customerEmail()->value();
        $invoiceModel->created_at = $invoice->createdAt();
        
        $invoiceModel->save();
        
        // Handle invoice lines
        $invoiceModel->lines()->delete(); // Remove existing lines
        
        foreach ($invoice->lines() as $line) {
            $lineModel = new InvoiceLineModel();
            $lineModel->invoice_id = $invoice->id()->value();
            $lineModel->product_name = $line->productName()->value();
            $lineModel->quantity = $line->quantity()->value();
            $lineModel->unit_price_in_cents = $line->unitPrice()->amountInCents();
            $lineModel->save();
        }
    }
    
    public function findById(InvoiceId $id): ?Invoice
    {
        $invoiceModel = InvoiceModel::with('lines')->find($id->value());
        
        if (!$invoiceModel) {
            return null;
        }
        
        return $this->mapToDomainEntity($invoiceModel);
    }
    
    public function delete(InvoiceId $id): void
    {
        InvoiceModel::destroy($id->value());
    }
    
    private function mapToDomainEntity(InvoiceModel $invoiceModel): Invoice
    {
        // Use reconstitute() factory method - allows any status when loading from persistence
        $invoice = Invoice::reconstitute(
            new InvoiceId($invoiceModel->id),
            InvoiceStatus::from($invoiceModel->status),
            new CustomerName($invoiceModel->customer_name),
            new CustomerEmail($invoiceModel->customer_email),
            $invoiceModel->created_at->toDateTimeImmutable()
        );
        
        // Clear domain events to avoid duplication
        $invoice->clearDomainEvents();
        
        // Add invoice lines
        foreach ($invoiceModel->lines as $lineModel) {
            $invoice->addLine(
                new ProductName($lineModel->product_name),
                new Quantity($lineModel->quantity),
                Money::fromCents($lineModel->unit_price_in_cents)
            );
        }
        
        return $invoice;
    }
}

