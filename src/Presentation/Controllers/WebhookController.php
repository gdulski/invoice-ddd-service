<?php

declare(strict_types=1);

namespace Src\Presentation\Controllers;

use Src\Domain\Events\NotificationDelivered;
use Src\Domain\ValueObjects\InvoiceId;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

final class WebhookController
{
    public function __construct(
        private Dispatcher $eventDispatcher
    ) {}

    public function notificationDelivered(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_id' => 'required|string',
            'provider' => 'required|string',
        ]);

        Log::info('Webhook received: notification delivered', [
            'invoice_id' => $validated['invoice_id'],
            'provider' => $validated['provider'],
        ]);

        try {
            $invoiceId = new InvoiceId($validated['invoice_id']);
            
            $event = new NotificationDelivered(
                $invoiceId,
                $validated['provider'],
                new \DateTimeImmutable()
            );

            $this->eventDispatcher->dispatch($event);

            return response()->json([
                'message' => 'Delivery notification processed successfully',
                'invoice_id' => $validated['invoice_id'],
            ], Response::HTTP_OK);

        } catch (InvalidArgumentException $e) {
            Log::error('Invalid invoice_id in webhook', [
                'invoice_id' => $validated['invoice_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Invalid invoice_id',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            Log::error('Error processing notification webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Failed to process notification delivery',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

