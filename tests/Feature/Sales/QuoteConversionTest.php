<?php

namespace Tests\Feature\Sales;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepted_quote_can_convert_lead_into_customer(): void
    {
        $user = User::factory()->create();

        $lead = Lead::create([
            'name' => 'Stanley Hudson',
            'email' => 'stanley@dundermifflin.com',
            'phone' => '+1 555-0144',
            'source' => 'Website Inquiry',
            'status' => 'qualified',
        ]);

        $quote = Quote::create([
            'lead_id' => $lead->id,
            'number' => 'QT-CONV-CUST-01',
            'title' => 'Custom Crossword System',
            'status' => 'accepted',
            'subtotal' => 2000,
            'total' => 2000,
        ]);

        $this->assertNull($quote->customer_id);

        $response = $this->actingAs($user)->post("/admin/sales/quotes/{$quote->id}/convert-to-customer");
        $response->assertRedirect(route('admin.quotes.show', $quote));

        $quote->refresh();
        $lead->refresh();

        $this->assertNotNull($quote->customer_id);
        $this->assertEquals($quote->customer_id, $lead->customer_id);

        $customer = Customer::find($quote->customer_id);
        $this->assertNotNull($customer);
        $this->assertEquals('Stanley Hudson', $customer->name);
        $this->assertEquals('stanley@dundermifflin.com', $customer->email);
        $this->assertEquals('converted', $lead->status);
    }

    public function test_accepted_quote_can_convert_into_project(): void
    {
        $user = User::factory()->create();

        $customer = Customer::create([
            'name' => 'Phyllis Vance',
            'email' => 'phyllis@vancerefrigeration.com',
            'status' => 'active',
        ]);

        $quote = Quote::create([
            'customer_id' => $customer->id,
            'number' => 'QT-CONV-PROJ-01',
            'title' => 'Refrigeration IoT Dashboard',
            'status' => 'accepted',
            'subtotal' => 8500.00,
            'total' => 8500.00,
            'notes' => 'Real-time telemetry and alerts dashboard.',
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'IoT Backend and UI Portal',
            'quantity' => 1,
            'unit_price' => 8500.00,
            'total' => 8500.00,
        ]);

        $this->assertNull($quote->project_id);

        $response = $this->actingAs($user)->post("/admin/sales/quotes/{$quote->id}/convert-to-project");
        $response->assertRedirect(route('admin.quotes.show', $quote));

        $quote->refresh();
        $this->assertNotNull($quote->project_id);

        $project = Project::find($quote->project_id);
        $this->assertNotNull($project);
        $this->assertEquals('Refrigeration IoT Dashboard', $project->name);
        $this->assertEquals($customer->id, $project->customer_id);
        $this->assertEquals(8500.00, (float) $project->budget);
        $this->assertEquals('planned', $project->status);
        $this->assertStringContainsString('QT-CONV-PROJ-01', $project->description);
    }
}

