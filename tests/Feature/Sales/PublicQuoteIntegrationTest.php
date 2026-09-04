<?php

namespace Tests\Feature\Sales;

use App\Models\Lead;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicQuoteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\GeneralSetting::create([
            'logo' => 'logo.png',
            'favicon' => 'favicon.png',
        ]);
    }

    public function test_public_quote_request_creates_lead_and_itemized_quote(): void
    {
        $payload = [
            'name' => 'Jim Halpert',
            'email' => 'jim@athlead.com',
            'phone' => '+1 555-0188',
            'company' => 'Athlead Sports Marketing',
            'service_id' => 'Custom Web Application Development',
            'project_type' => 'New Product / MVP Build',
            'budget_range' => '$5,000 – $10,000',
            'timeline' => '1 – 2 Months',
            'description' => 'We need an interactive athlete tracking portal with real-time analytics.',
        ];

        $response = $this->postJson('/quote-request', $payload);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $lead = Lead::where('email', 'jim@athlead.com')->first();
        $this->assertNotNull($lead);
        $this->assertEquals('Jim Halpert', $lead->name);
        $this->assertEquals('Quote Request', $lead->source);

        $quote = Quote::where('lead_id', $lead->id)->first();
        $this->assertNotNull($quote);
        $this->assertEquals('draft', $quote->status);
        $this->assertNotNull($quote->token);
        $this->assertGreaterThan(0, $quote->items()->count());

        $item = $quote->items()->first();
        $this->assertStringContainsString('New Product / MVP Build', $item->description);
        $this->assertEquals(5000.00, (float) $item->unit_price);
        $this->assertEquals(5000.00, (float) $quote->total);
    }

    public function test_client_can_view_quote_portal_using_token(): void
    {
        $quote = Quote::create([
            'number' => 'QT-PORTAL-01',
            'title' => 'Athlete Portal MVP',
            'status' => 'sent',
            'subtotal' => 5000.00,
            'total' => 5000.00,
            'token' => 'client-secret-token-xyz-123',
            'valid_until' => Carbon::today()->addDays(30),
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'Athlete Performance Tracking System',
            'quantity' => 1,
            'unit_price' => 5000.00,
            'total' => 5000.00,
        ]);

        $response = $this->get('/quote/view/client-secret-token-xyz-123');
        $response->assertStatus(200);
        $response->assertSee('QT-PORTAL-01');
        $response->assertSee('Athlete Performance Tracking System');
        $response->assertSee('Accept Quotation');
    }

    public function test_client_can_accept_quote_online(): void
    {
        $quote = Quote::create([
            'number' => 'QT-ACCEPT-ONLINE',
            'status' => 'sent',
            'subtotal' => 7500.00,
            'total' => 7500.00,
            'token' => 'accept-token-abc',
            'valid_until' => Carbon::today()->addDays(15),
        ]);

        $response = $this->postJson('/quote/view/accept-token-abc/accept', [
            'signer_name' => 'Jim Halpert',
            'agreement' => '1',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $quote->refresh();
        $this->assertEquals('accepted', $quote->status);
        $this->assertNotNull($quote->accepted_at);
        $this->assertStringContainsString('Jim Halpert', $quote->notes);
    }

    public function test_client_cannot_accept_expired_quote(): void
    {
        $expiredQuote = Quote::create([
            'number' => 'QT-EXPIRED-ONLINE',
            'status' => 'sent',
            'subtotal' => 3000.00,
            'total' => 3000.00,
            'token' => 'expired-token-123',
            'valid_until' => Carbon::today()->subDays(2),
        ]);

        $response = $this->postJson('/quote/view/expired-token-123/accept', [
            'signer_name' => 'Late Client',
            'agreement' => '1',
        ]);

        $response->assertStatus(422);

        $expiredQuote->refresh();
        $this->assertNotEquals('accepted', $expiredQuote->status);
    }

    public function test_client_can_reject_quote_online_with_feedback(): void
    {
        $quote = Quote::create([
            'number' => 'QT-REJECT-ONLINE',
            'status' => 'sent',
            'subtotal' => 9000.00,
            'total' => 9000.00,
            'token' => 'reject-token-xyz',
            'valid_until' => Carbon::today()->addDays(10),
        ]);

        $response = $this->postJson('/quote/view/reject-token-xyz/reject', [
            'reason' => 'Looking for smaller scope MVP at this time.',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $quote->refresh();
        $this->assertEquals('rejected', $quote->status);
        $this->assertNotNull($quote->rejected_at);
        $this->assertEquals('Looking for smaller scope MVP at this time.', $quote->rejection_reason);
    }

    public function test_client_can_download_pdf_copy_via_token(): void
    {
        $quote = Quote::create([
            'number' => 'QT-PDF-TOKEN',
            'status' => 'sent',
            'subtotal' => 2000.00,
            'total' => 2000.00,
            'token' => 'pdf-token-777',
        ]);

        $response = $this->get('/quote/view/pdf-token-777/pdf');
        $response->assertStatus(200);
    }
}
