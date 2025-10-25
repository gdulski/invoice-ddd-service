<?php

declare(strict_types=1);

namespace Src\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateInvoiceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:draft,sending,sent-to-client'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required',
            'status.in' => 'Status must be one of: draft, sending, sent-to-client',
        ];
    }
}
