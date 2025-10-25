<?php

declare(strict_types=1);

namespace Src\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_name' => ['required', 'string', 'max:255'],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.unit_price_in_cents' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Customer name is required',
            'customer_name.max' => 'Customer name cannot exceed 255 characters',
            'customer_email.required' => 'Customer email is required',
            'customer_email.email' => 'Customer email must be a valid email address',
            'customer_email.max' => 'Customer email cannot exceed 255 characters',
            'lines.required' => 'At least one invoice line is required',
            'lines.min' => 'At least one invoice line is required',
            'lines.*.product_name.required' => 'Product name is required for each line',
            'lines.*.product_name.max' => 'Product name cannot exceed 255 characters',
            'lines.*.quantity.required' => 'Quantity is required for each line',
            'lines.*.quantity.integer' => 'Quantity must be an integer',
            'lines.*.quantity.min' => 'Quantity must be positive',
            'lines.*.unit_price_in_cents.required' => 'Unit price is required for each line',
            'lines.*.unit_price_in_cents.integer' => 'Unit price must be an integer',
            'lines.*.unit_price_in_cents.min' => 'Unit price must be positive',
        ];
    }
}


