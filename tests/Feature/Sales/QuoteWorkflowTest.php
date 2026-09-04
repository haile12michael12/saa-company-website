<?php

namespace Tests\Feature\Sales;

use App\Mail\QuoteMail;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QuoteWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_approve_quote(): void
    {
        $user = User::factory()->create();

        $quote = Quote::create([
            'number' => 'QT-WF-01',
            'status' => 'pending_approval',
            'subtotal' => 1500,
            'total' => 1500,
        ]);

        $response = $this->actingAs($user)->post("/admin/sales/quotes/{$quote->id}/approve");

        $quote->refresh();
        $this->assertEquals('approved', $quote->status);
        $this->assertNotNull($quote->approved_at);
        $this->assertEquals($user->id, $quote->approved_by);
    }

    public function test_user_can_send_quote_email_with_pdf(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $customer = Customer::create([
            'name' => 'Michael Scott',
            'email' => 'michael@dunder.com',
            'status' => 'active',
        ]);

        $quote = Quote::create([
            'number' => 'QT-MAIL-01',
            'customer_id' => $customer->id,
            'status' => 'approved',
            'subtotal' => 2500,
            'total' => 2500,
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'SaaS Engineering Sprint',
            'quantity' => 1,
            'unit_price' => 2500,
            'total' => 2500,
        ]);

        $response = $this->actingAs($user)->post("/admin/sales/quotes/{$quote->id}/send", [
            'email_message' => 'Please find your approved quotation attached.',
        ]);

        Mail::assertSent(QuoteMail::class, function ($mail) use ($quote) {
            return $mail->quote->id === $quote->id && $mail->hasTo('michael@dunder.com');
        });

        $quote->refresh();
        $this->assertEquals('sent', $quote->status);
        $this->assertNotNull($quote->sent_at);
    }

    public function test_user_can_mark_quote_as_accepted(): void
    {
        $user = User::factory()->create();

        $quote = Quote::create([
            'number' => 'QT-ACC-01',
            'status' => 'sent',
            'subtotal' => 4000,
            'total' => 4000,
        ]);

        $response = $this->actingAs($user)->post("/admin/sales/quotes/{$quote->id}/accept");

        $quote->refresh();
        $this->assertEquals('accepted', $quote->status);
        $this->assertNotNull($quote->accepted_at);
    }

    public function test_user_can_mark_quote_as_rejected_with_reason(): void
    {
        $user = User::factory()->create();

        $quote = Quote::create([
            'number' => 'QT-REJ-01',
            'status' => 'sent',
            'subtotal' => 3000,
            'total' => 3000,
        ]);

        $response = $this->actingAs($user)->post("/admin/sales/quotes/{$quote->id}/reject", [
            'reason' => 'Client postponed budget until next quarter.',
        ]);

        $quote->refresh();
        $this->assertEquals('rejected', $quote->status);
        $this->assertNotNull($quote->rejected_at);
        $this->assertEquals('Client postponed budget until next quarter.', $quote->rejection_reason);
    }

    public function test_quote_expiration_is_accurately_evaluated(): void
    {
        $activeQuote = Quote::create([
            'number' => 'QT-ACTIVE-01',
            'status' => 'sent',
            'valid_until' => Carbon::today()->addDays(10),
            'subtotal' => 1000,
            'total' => 1000,
        ]);

        $this->assertFalse($activeQuote->isExpired());
        $this->assertEquals('sent', $activeQuote->effective_status);

        $expiredQuote = Quote::create([
            'number' => 'QT-EXPIRED-01',
            'status' => 'sent',
            'valid_until' => Carbon::today()->subDays(5),
            'subtotal' => 1000,
            'total' => 1000,
        ]);

        $this->assertTrue($expiredQuote->isExpired());
        $this->assertEquals('expired', $expiredQuote->effective_status);

        // Accepted quotes do not become expired
        $acceptedQuote = Quote::create([
            'number' => 'QT-ACCEPTED-PAST',
            'status' => 'accepted',
            'valid_until' => Carbon::today()->subDays(5),
            'subtotal' => 1000,
            'total' => 1000,
        ]);

        $this->assertFalse($acceptedQuote->isExpired());
        $this->assertEquals('accepted', $acceptedQuote->effective_status);
    }

    public function test_user_can_download_pdf_for_quote(): void
    {
        $user = User::factory()->create();

        $quote = Quote::create([
            'number' => 'QT-PDF-01',
            'title' => 'Mobile App Architecture',
            'status' => 'approved',
            'subtotal' => 4500,
            'total' => 4500,
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'Cross-Platform React Native App',
            'quantity' => 1,
            'unit_price' => 4500,
            'total' => 4500,
        ]);

        $response = $this->actingAs($user)->get("/admin/sales/quotes/{$quote->id}/pdf");

        $response->assertStatus(200);
        // Response is either PDF or formatted HTML preview
        $this->assertTrue(
            $response->headers->get('content-type') === 'application/pdf' ||
            str_contains($response->headers->get('content-type'), 'text/html')
        );
    }
}

