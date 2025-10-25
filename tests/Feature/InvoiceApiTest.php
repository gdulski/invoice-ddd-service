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
            'lines' => [] // Valid: empty lines are allowed
        ];

        $response = $this->postJson('/api/invoices', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'customer_name',
                'customer_email'
            ]);
    }
    
    public function test_can_create_invoice_without_lines(): void
    {
        $payload = [
            'customer_name' => 'Empty Invoice',
            'customer_email' => 'empty@example.com'
            // No lines provided
        ];

        $response = $this->postJson('/api/invoices', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'status',
                'customer_name',
                'customer_email',
                'lines',
                'total_price_in_cents',
                'created_at'
            ]);

        $this->assertEquals('draft', $response->json('status'));
        $this->assertEquals('Empty Invoice', $response->json('customer_name'));
        $this->assertEquals(0, $response->json('total_price_in_cents'));
        $this->assertEmpty($response->json('lines'));
    }
    
    public function test_cannot_send_invoice_without_lines(): void
    {
        // Create an invoice without lines
        $createPayload = [
            'customer_name' => 'No Lines',
            'customer_email' => 'nolines@example.com'
        ];

        $createResponse = $this->postJson('/api/invoices', $createPayload);
        $invoiceId = $createResponse->json('id');

        // Try to send it
        $response = $this->postJson("/api/invoices/{$invoiceId}/send");

        $response->assertStatus(500); // DomainException will be thrown
    }

    public function test_cannot_send_non_existent_invoice(): void
    {
        $response = $this->postJson('/api/invoices/non-existent-id/send');

        $response->assertStatus(500); // DomainException will be thrown
    }

    public function test_can_update_status_to_sending(): void
    {
        // Create a draft invoice
        $createPayload = [
            'customer_name' => 'Alice Brown',
            'customer_email' => 'alice.brown@example.com',
            'lines' => [
                [
                    'product_name' => 'Training',
                    'quantity' => 1,
                    'unit_price_in_cents' => 25000
                ]
            ]
        ];

        $createResponse = $this->postJson('/api/invoices', $createPayload);
        $invoiceId = $createResponse->json('id');

        // Update status to sending
        $response = $this->postJson("/api/invoices/{$invoiceId}/status", [
            'status' => 'sending'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $invoiceId,
                'status' => 'sending',
                'customer_name' => 'Alice Brown',
                'customer_email' => 'alice.brown@example.com',
                'total_price_in_cents' => 25000
            ]);
    }

    public function test_can_update_status_to_sent_to_client(): void
    {
        // Create and send an invoice
        $createPayload = [
            'customer_name' => 'Bob Johnson',
            'customer_email' => 'bob.johnson@example.com',
            'lines' => [
                [
                    'product_name' => 'Service',
                    'quantity' => 2,
                    'unit_price_in_cents' => 15000
                ]
            ]
        ];

        $createResponse = $this->postJson('/api/invoices', $createPayload);
        $invoiceId = $createResponse->json('id');

        // First send the invoice
        $this->postJson("/api/invoices/{$invoiceId}/send");

        // Then update to sent-to-client
        $response = $this->postJson("/api/invoices/{$invoiceId}/status", [
            'status' => 'sent-to-client'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $invoiceId,
                'status' => 'sent-to-client'
            ]);
    }

    public function test_cannot_update_status_from_draft_to_sent_to_client(): void
    {
        // Create a draft invoice
        $createPayload = [
            'customer_name' => 'Charlie Davis',
            'customer_email' => 'charlie.davis@example.com',
            'lines' => [
                [
                    'product_name' => 'Workshop',
                    'quantity' => 2,
                    'unit_price_in_cents' => 5000
                ]
            ]
        ];

        $createResponse = $this->postJson('/api/invoices', $createPayload);
        $invoiceId = $createResponse->json('id');

        // Try to update status to sent-to-client directly from draft (should fail)
        $response = $this->postJson("/api/invoices/{$invoiceId}/status", [
            'status' => 'sent-to-client'
        ]);

        $response->assertStatus(500); // InvalidArgumentException will be thrown
    }

    public function test_cannot_update_status_from_sending_to_draft(): void
    {
        // Create and send an invoice
        $createPayload = [
            'customer_name' => 'David Miller',
            'customer_email' => 'david.miller@example.com',
            'lines' => [
                [
                    'product_name' => 'Product',
                    'quantity' => 1,
                    'unit_price_in_cents' => 30000
                ]
            ]
        ];

        $createResponse = $this->postJson('/api/invoices', $createPayload);
        $invoiceId = $createResponse->json('id');

        // Send the invoice
        $this->postJson("/api/invoices/{$invoiceId}/send");

        // Try to go back to draft (should fail)
        $response = $this->postJson("/api/invoices/{$invoiceId}/status", [
            'status' => 'draft'
        ]);

        $response->assertStatus(500); // InvalidArgumentException will be thrown
    }

    public function test_status_update_validates_status_value(): void
    {
        $createPayload = [
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'lines' => [
                [
                    'product_name' => 'Item',
                    'quantity' => 1,
                    'unit_price_in_cents' => 10000
                ]
            ]
        ];

        $createResponse = $this->postJson('/api/invoices', $createPayload);
        $invoiceId = $createResponse->json('id');

        // Try with invalid status
        $response = $this->postJson("/api/invoices/{$invoiceId}/status", [
            'status' => 'invalid-status'
        ]);

        $response->assertStatus(422);
    }

    public function test_complete_status_flow_with_status_endpoint(): void
    {
        // Create invoice
        $createPayload = [
            'customer_name' => 'Status Flow Test',
            'customer_email' => 'statusflow@example.com',
            'lines' => [
                [
                    'product_name' => 'Service',
                    'quantity' => 1,
                    'unit_price_in_cents' => 10000
                ]
            ]
        ];

        $createResponse = $this->postJson('/api/invoices', $createPayload);
        $invoiceId = $createResponse->json('id');
        
        // Verify initial status is draft
        $this->assertEquals('draft', $createResponse->json('status'));

        // Update to sending
        $sendResponse = $this->postJson("/api/invoices/{$invoiceId}/status", [
            'status' => 'sending'
        ]);
        $this->assertEquals('sending', $sendResponse->json('status'));

        // Update to sent-to-client
        $sentResponse = $this->postJson("/api/invoices/{$invoiceId}/status", [
            'status' => 'sent-to-client'
        ]);
        $this->assertEquals('sent-to-client', $sentResponse->json('status'));

        // Verify final state by viewing
        $viewResponse = $this->getJson("/api/invoices/{$invoiceId}");
        $this->assertEquals('sent-to-client', $viewResponse->json('status'));
    }
}


