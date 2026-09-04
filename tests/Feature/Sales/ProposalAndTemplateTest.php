<?php

namespace Tests\Feature\Sales;

use App\Models\Customer;
use App\Models\Proposal;
use App\Models\ProposalTemplate;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalAndTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_proposals_and_templates_page(): void
    {
        $user = User::factory()->create();

        ProposalTemplate::create([
            'name' => 'General Engineering Proposal',
            'category' => 'Web Development',
            'content' => '## Scope: {{quote_number}} for {{client_name}}',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin/sales/proposals');
        $response->assertStatus(200);
        $response->assertSee('Proposals & Templates', false);
        $response->assertSee('General Engineering Proposal');
    }

    public function test_user_can_create_proposal_template(): void
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'Mobile SaaS Architecture Template',
            'category' => 'Mobile Applications',
            'subject' => 'Mobile Solution for {{client_name}}',
            'content' => "## Architecture\n\nQuotation: {{quote_number}}\nTotal: {{total_amount}}",
        ];

        $response = $this->actingAs($user)->post('/admin/sales/proposal-templates', $payload);
        $response->assertRedirect('/admin/sales/proposals');

        $this->assertDatabaseHas('proposal_templates', [
            'name' => 'Mobile SaaS Architecture Template',
            'category' => 'Mobile Applications',
        ]);
    }

    public function test_user_can_generate_proposal_for_quote_using_template_with_placeholders(): void
    {
        $user = User::factory()->create();

        $customer = Customer::create([
            'name' => 'Acme Corporation',
            'email' => 'contact@acme.corp',
            'status' => 'active',
        ]);

        $quote = Quote::create([
            'customer_id' => $customer->id,
            'number' => 'QT-PROP-01',
            'title' => 'Cloud Migration Proposal',
            'status' => 'draft',
            'subtotal' => 6000.00,
            'total' => 6000.00,
            'currency' => 'USD',
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'AWS Kubernetes Infrastructure',
            'quantity' => 1,
            'unit_price' => 6000.00,
            'total' => 6000.00,
        ]);

        $template = ProposalTemplate::create([
            'name' => 'Cloud Migration Framework',
            'category' => 'Cloud & DevOps',
            'content' => "Proposal for {{client_name}} regarding Quote {{quote_number}}.\nTotal Investment: {{total_amount}}.\n\nDeliverables:\n{{items_table}}",
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/admin/sales/proposals', [
            'quote_id' => $quote->id,
            'title' => 'Cloud Architecture Proposal for Acme',
            'template_id' => $template->id,
        ]);

        $response->assertRedirect(route('admin.quotes.show', $quote));

        $proposal = Proposal::where('quote_id', $quote->id)->first();
        $this->assertNotNull($proposal);
        $this->assertEquals('Cloud Architecture Proposal for Acme', $proposal->title);
        $this->assertStringContainsString('Acme Corporation', $proposal->content);
        $this->assertStringContainsString('QT-PROP-01', $proposal->content);
        $this->assertStringContainsString('6,000.00', $proposal->content);
        $this->assertStringContainsString('AWS Kubernetes Infrastructure', $proposal->content);
    }
}
