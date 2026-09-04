<?php

namespace Tests\Feature\Sales;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_quotes_index_and_filters(): void
    {
        $user = User::factory()->create();

        Quote::create([
            'number' => 'QT-DRAFT-01',
            'title' => 'Draft Project',
            'status' => 'draft',
            'subtotal' => 1000,
            'total' => 1000,
        ]);

        Quote::create([
            'number' => 'QT-ACCEPTED-01',
            'title' => 'Accepted Project',
            'status' => 'accepted',
            'subtotal' => 2000,
            'total' => 2000,
        ]);

        $response = $this->actingAs($user)->get('/admin/sales/quotes');
        $response->assertStatus(200);
        $response->assertSee('QT-DRAFT-01');
        $response->assertSee('QT-ACCEPTED-01');

        // Test filter by status
        $filterResponse = $this->actingAs($user)->get('/admin/sales/quotes?status=accepted');
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('QT-ACCEPTED-01');
        $filterResponse->assertDontSee('QT-DRAFT-01');
    }

    public function test_user_can_view_quote_create_page_with_lead_prefill(): void
    {
        $user = User::factory()->create();

        $lead = Lead::create([
            'name' => 'Dwight Schrute',
            'email' => 'dwight@dundermifflin.com',
            'phone' => '+1 555-0199',
            'source' => 'Quote Request',
            'status' => 'new',
            'notes' => "Company: Schrute Farms\nProject Type: E-commerce Store",
        ]);

        $response = $this->actingAs($user)->get("/admin/sales/quotes/create?lead_id={$lead->id}");
        $response->assertStatus(200);
        $response->assertSee('Dwight Schrute');
        $response->assertSee('dwight@dundermifflin.com');
    }

    public function test_user_can_create_quote_with_items_discount_and_tax(): void
    {
        $user = User::factory()->create();

        $lead = Lead::create([
            'name' => 'Andy Bernard',
            'email' => 'andy@cornell.edu',
            'status' => 'new',
        ]);

        $payload = [
            'number' => 'QT-2026-TESTCREATE',
            'title' => 'Enterprise Web Portal',
            'lead_id' => $lead->id,
            'currency' => 'USD',
            'valid_until' => Carbon::today()->addDays(30)->format('Y-m-d'),
            'discount_type' => 'percentage',
            'discount_rate' => 10, // 10%
            'tax_rate' => 5, // 5%
            'notes' => 'Custom notes for client.',
            'terms' => 'Standard net 30 days.',
            'submit_action' => 'submit_approval',
            'items' => [
                [
                    'description' => 'Frontend UI/UX Design System',
                    'quantity' => 2,
                    'unit_price' => 1000.00, // 2000
                ],
                [
                    'description' => 'Backend API & Cloud Architecture',
                    'quantity' => 1,
                    'unit_price' => 3000.00, // 3000
                ],
            ],
        ];

        // Total calculation:
        // Subtotal = 2000 + 3000 = 5000
        // Discount 10% = 500
        // Taxable = 4500
        // Tax 5% = 225
        // Total = 4725

        $response = $this->actingAs($user)->post('/admin/sales/quotes', $payload);

        $quote = Quote::where('number', 'QT-2026-TESTCREATE')->first();
        $this->assertNotNull($quote);
        $this->assertEquals('pending_approval', $quote->status);
        $this->assertEquals(5000.00, (float) $quote->subtotal);
        $this->assertEquals(500.00, (float) $quote->discount_amount);
        $this->assertEquals(225.00, (float) $quote->tax);
        $this->assertEquals(4725.00, (float) $quote->total);
        $this->assertCount(2, $quote->items);

        $response->assertRedirect(route('admin.quotes.show', $quote));
    }

    public function test_user_can_view_quote_show_page(): void
    {
        $user = User::factory()->create();

        $quote = Quote::create([
            'number' => 'QT-SHOW-01',
            'title' => 'Platform Redesign',
            'status' => 'draft',
            'subtotal' => 3000,
            'total' => 3000,
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'Design & Prototyping',
            'quantity' => 1,
            'unit_price' => 3000,
            'total' => 3000,
        ]);

        $response = $this->actingAs($user)->get("/admin/sales/quotes/{$quote->id}");
        $response->assertStatus(200);
        $response->assertSee('QT-SHOW-01');
        $response->assertSee('Design &amp; Prototyping', false);
    }

    public function test_user_can_update_quote_and_sync_items(): void
    {
        $user = User::factory()->create();

        $quote = Quote::create([
            'number' => 'QT-UPDATE-01',
            'title' => 'Initial Title',
            'status' => 'draft',
            'subtotal' => 1000,
            'total' => 1000,
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'Original Item',
            'quantity' => 1,
            'unit_price' => 1000,
            'total' => 1000,
        ]);

        $updatePayload = [
            'number' => 'QT-UPDATE-01',
            'title' => 'Updated Project Title',
            'currency' => 'USD',
            'discount_type' => 'fixed',
            'discount_rate' => 200,
            'tax_rate' => 0,
            'items' => [
                [
                    'description' => 'New Revised Item 1',
                    'quantity' => 2,
                    'unit_price' => 1500, // 3000 - 200 = 2800
                ],
            ],
        ];

        $response = $this->actingAs($user)->put("/admin/sales/quotes/{$quote->id}", $updatePayload);
        $response->assertRedirect(route('admin.quotes.show', $quote));

        $quote->refresh();
        $this->assertEquals('Updated Project Title', $quote->title);
        $this->assertEquals(3000, (float) $quote->subtotal);
        $this->assertEquals(200, (float) $quote->discount_amount);
        $this->assertEquals(2800, (float) $quote->total);
        $this->assertCount(1, $quote->items);
        $this->assertEquals('New Revised Item 1', $quote->items->first()->description);
    }

    public function test_user_can_delete_quote(): void
    {
        $user = User::factory()->create();

        $quote = Quote::create([
            'number' => 'QT-DEL-01',
            'status' => 'draft',
            'subtotal' => 500,
            'total' => 500,
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'Temporary Item',
            'quantity' => 1,
            'unit_price' => 500,
            'total' => 500,
        ]);

        $response = $this->actingAs($user)->delete("/admin/sales/quotes/{$quote->id}");
        $response->assertRedirect(route('admin.quotes.index'));

        $this->assertDatabaseMissing('quotes', ['id' => $quote->id]);
        $this->assertDatabaseMissing('quote_items', ['quote_id' => $quote->id]);
    }
}

