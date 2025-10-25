<?php

declare(strict_types=1);

namespace Src\Presentation\Resources;

use Src\Application\DTOs\InvoiceLineResponseDto;
use Src\Application\DTOs\InvoiceResponseDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class InvoiceResource extends JsonResource
{
    public function __construct(private InvoiceResponseDto $invoice)
    {
        parent::__construct($invoice);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->invoice->id,
            'status' => $this->invoice->status,
            'customer_name' => $this->invoice->customerName,
            'customer_email' => $this->invoice->customerEmail,
            'lines' => array_map(
                fn(InvoiceLineResponseDto $line) => [
                    'product_name' => $line->productName,
                    'quantity' => $line->quantity,
                    'unit_price_in_cents' => $line->unitPriceInCents,
                    'total_unit_price_in_cents' => $line->totalUnitPriceInCents,
                ],
                $this->invoice->lines
            ),
            'total_price_in_cents' => $this->invoice->totalPriceInCents,
            'created_at' => $this->invoice->createdAt,
        ];
    }
}


