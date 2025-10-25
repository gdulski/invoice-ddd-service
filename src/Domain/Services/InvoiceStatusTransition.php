<?php

declare(strict_types=1);

namespace Src\Domain\Services;

use Src\Domain\Entities\Invoice;
use Src\Domain\ValueObjects\InvoiceStatus;
use InvalidArgumentException;

/**
 * Invoice Status Transition Service
 * Manages valid status transitions for invoices following State Machine pattern
 */
final class InvoiceStatusTransition
{
    /**
     * Transition invoice to target status
     * 
     * @param Invoice $invoice
     * @param InvoiceStatus $targetStatus
     * @return void
     * @throws InvalidArgumentException if transition is not allowed
     */
    public function transitionTo(Invoice $invoice, InvoiceStatus $targetStatus): void
    {
        $currentStatus = $invoice->status();
        
        // Validate transition
        if (!$this->isValidTransition($currentStatus, $targetStatus)) {
            throw new InvalidArgumentException(
                "Cannot transition from {$currentStatus->value} to {$targetStatus->value}"
            );
        }
        
        // Execute transition
        match ($targetStatus) {
            InvoiceStatus::SENDING => $invoice->send(),
            InvoiceStatus::SENT_TO_CLIENT => $invoice->markAsSentToClient(),
            default => throw new InvalidArgumentException("Cannot transition to {$targetStatus->value}")
        };
    }
    
    /**
     * Check if transition from current to target status is valid
     * 
     * @param InvoiceStatus $from Current status
     * @param InvoiceStatus $to Target status
     * @return bool
     */
    public function isValidTransition(InvoiceStatus $from, InvoiceStatus $to): bool
    {
        if ($from === $to) {
            return false; // No self-transitions
        }
        
        return match ([$from, $to]) {
            [InvoiceStatus::DRAFT, InvoiceStatus::SENDING] => true,
            [InvoiceStatus::SENDING, InvoiceStatus::SENT_TO_CLIENT] => true,
            default => false
        };
    }
    
    /**
     * Get all possible transitions for a given status
     * 
     * @param InvoiceStatus $status
     * @return array List of possible target statuses
     */
    public function getPossibleTransitions(InvoiceStatus $status): array
    {
        return match ($status) {
            InvoiceStatus::DRAFT => [InvoiceStatus::SENDING],
            InvoiceStatus::SENDING => [InvoiceStatus::SENT_TO_CLIENT],
            InvoiceStatus::SENT_TO_CLIENT => []
        };
    }
    
    /**
     * Check if status can be changed (not in final state)
     * 
     * @param InvoiceStatus $status
     * @return bool
     */
    public function canBeChanged(InvoiceStatus $status): bool
    {
        return !empty($this->getPossibleTransitions($status));
    }
}
