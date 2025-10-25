<?php

declare(strict_types=1);

namespace Src\Infrastructure\ExternalServices;

use Src\Domain\ValueObjects\CustomerEmail;
use Src\Domain\ValueObjects\InvoiceId;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Notification Facade
 * Handles email notifications for invoice-related events
 * This is a technical implementation detail in the Infrastructure layer
 */
final class NotificationFacade implements NotificationFacadeInterface
{
    /**
     * Send invoice notification email to customer
     * 
     * @param InvoiceId $invoiceId
     * @param CustomerEmail $customerEmail
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function sendInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail,
        string $subject,
        string $message
    ): void {
        try {
            // For now, we'll use Laravel's Mail facade to send emails
            // In a real application, you might want to use a queue for better performance
            Mail::raw($message, function ($mail) use ($customerEmail, $subject, $invoiceId) {
                $mail->to($customerEmail->value())
                     ->subject($subject);
            });
            
            Log::info('Invoice notification email sent successfully', [
                'invoice_id' => $invoiceId->value(),
                'customer_email' => $customerEmail->value(),
                'subject' => $subject,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send invoice notification email', [
                'invoice_id' => $invoiceId->value(),
                'customer_email' => $customerEmail->value(),
                'error' => $e->getMessage(),
            ]);
            
            // Re-throw the exception to let the caller handle it
            throw new \RuntimeException('Failed to send notification email: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Send a default invoice notification with hardcoded subject and message
     * 
     * @param InvoiceId $invoiceId
     * @param CustomerEmail $customerEmail
     * @return void
     */
    public function sendDefaultInvoiceNotification(
        InvoiceId $invoiceId,
        CustomerEmail $customerEmail
    ): void {
        $subject = 'Your Invoice is Ready';
        $message = "Dear Customer,\n\nYour invoice #{$invoiceId->value()} has been prepared and is ready for review.\n\nThank you for your business!\n\nBest regards,\nInvoice Management System";
        
        $this->sendInvoiceNotification($invoiceId, $customerEmail, $subject, $message);
    }
}
