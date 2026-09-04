<?php

namespace Tests\Feature\Sales;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_sales_dashboard(): void
    {
        $response = $this->get('/admin/sales');
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_sales_dashboard(): void
    {
        $user = User::factory()->create();

        $quote = Quote::create([
            'number' => 'QT-2026-TEST01',
            'title' => 'Web App Development',
            'status' => 'accepted',
            'subtotal' => 5000.00,
            'discount_type' => 'fixed',
            'discount_rate' => 500.00,
            'discount_amount' => 500.00,
            'tax_rate' => 10.0,
            'tax' => 450.00,
            'total' => 4950.00,
            'currency' => 'USD',
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'Full-stack development',
            'quantity' => 1,
            'unit_price' => 5000.00,
            'total' => 5000.00,
        ]);

        $response = $this->actingAs($user)->get('/admin/sales');

        $response->assertStatus(200);
        $response->assertSee('Sales Overview');
        $response->assertSee('Pipeline Performance');
        $response->assertSee('QT-2026-TEST01');
        $response->assertSee('4,950.00');
    }
}
