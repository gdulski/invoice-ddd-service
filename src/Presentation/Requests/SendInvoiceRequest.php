<?php

declare(strict_types=1);

namespace Src\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SendInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // No additional validation needed for sending invoice
            // The invoice ID is validated in the route parameter
        ];
    }
}


