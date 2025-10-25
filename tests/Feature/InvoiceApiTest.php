<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_invoice(): void
    {
        $payload = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john.doe@example.com',
            'lines' => [
                [
                    'product_name' => 'Web Development',
                    'quantity' => 10,
                    'unit_price_in_cents' => 5000
                ],
                [
                    'product_name' => 'Consulting',
                    'quantity' => 5,
                    'unit_price_in_cents' => 10000
                ]
            ]
        ];

        $response = $this->postJson('/api/invoices', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'status',
                'customer_name',
                'customer_email',
                'lines' => [
                    '*' => [
                        'product_name',
                        'quantity',
                        'unit_price_in_cents',
                        'total_unit_price_in_cents'
                    ]
                ],
                'total_price_in_cents',
                'created_at'
            ]);

        $this->assertEquals('draft', $response->json('status'));
        $this->assertEquals('John Doe', $response->json('customer_name'));
        $this->assertEquals('john.doe@example.com', $response->json('customer_email'));
        $this->assertEquals(100000, $response->json('total_price_in_cents'));
    }

    public function test_can_view_invoice(): void
    {
        // First create an invoice
        $createPayload = [
            'customer_name' => 'Jane Smith',
            'customer_email' => 'jane.smith@example.com',
            'lines' => [
                [
                    'product_name' => 'Design',
                    'quantity' => 3,
                    'unit_price_in_cents' => 7500
                ]
            ]
        ];

        $createResponse = $this->postJson('/api/invoices', $createPayload);
        $invoiceId = $createResponse->json('id');

        // Then view it
        $response = $this->getJson("/api/invoices/{$invoiceId}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $invoiceId,
                'status' => 'draft',
                'customer_name' => 'Jane Smith',
                'customer_email' => 'jane.smith@example.com',
                'total_price_in_cents' => 22500
            ]);
    }

    public function test_can_send_invoice(): void
    {
        // First create an invoice
        $createPayload = [
            'customer_name' => 'Bob Wilson',
            'customer_email' => 'bob.wilson@example.com',
            'lines' => [
                [
                    'product_name' => 'Support',
                    'quantity' => 2,
                    'unit_price_in_cents' => 15000
                ]
            ]
        ];

        $createResponse = $this->postJson('/api/invoices', $createPayload);
        $invoiceId = $createResponse->json('id');

        // Then send it
        $response = $this->postJson("/api/invoices/{$invoiceId}/send");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $invoiceId,
                'status' => 'sending',
                'customer_name' => 'Bob Wilson',
                'customer_email' => 'bob.wilson@example.com',
                'total_price_in_cents' => 30000
            ]);
    }

    public function test_validation_errors_on_invalid_data(): void
    {
        $payload = [
            'customer_name' => '', // Invalid: empty
            'customer_email' => 'invalid-email', // Invalid: not an email
            'lines' => [] // Invalid: empty lines
        ];

        $response = $this->postJson('/api/invoices', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'customer_name',
                'customer_email',
                'lines'
            ]);
    }

    public function test_cannot_send_non_existent_invoice(): void
    {
        $response = $this->postJson('/api/invoices/non-existent-id/send');

        $response->assertStatus(500); // DomainException will be thrown
    }
}


