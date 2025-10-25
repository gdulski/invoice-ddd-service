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
            'lines' => ['nullable', 'array'],
            'lines.*.product_name' => ['required_with:lines', 'string', 'max:255'],
            'lines.*.quantity' => ['required_with:lines', 'integer', 'min:1'],
            'lines.*.unit_price_in_cents' => ['required_with:lines', 'integer', 'min:1'],
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
            'lines.array' => 'Lines must be an array',
            'lines.*.product_name.required_with' => 'Product name is required when lines are provided',
            'lines.*.product_name.max' => 'Product name cannot exceed 255 characters',
            'lines.*.quantity.required_with' => 'Quantity is required when lines are provided',
            'lines.*.quantity.integer' => 'Quantity must be an integer',
            'lines.*.quantity.min' => 'Quantity must be positive',
            'lines.*.unit_price_in_cents.required_with' => 'Unit price is required when lines are provided',
            'lines.*.unit_price_in_cents.integer' => 'Unit price must be an integer',
            'lines.*.unit_price_in_cents.min' => 'Unit price must be positive',
        ];
    }
}


