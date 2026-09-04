<?php

namespace Tests\Feature\Marketing;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use App\Services\Marketing\CampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CampaignManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_campaign_and_populate_recipients()
    {
        $company = Company::create(['name' => 'Marketing Co']);
        $user = User::factory()->create(['company_id' => $company->id]);

        Customer::create(['company_id' => $company->id, 'name' => 'Customer A', 'email' => 'a@client.com']);
        Customer::create(['company_id' => $company->id, 'name' => 'Customer B', 'email' => 'b@client.com']);

        $service = app(CampaignService::class);
        $campaign = $service->createCampaign([
            'name' => 'Q4 Product Launch',
            'subject' => 'Discover our new digital platform features',
            'content' => 'Hello {{name}}, explore our latest updates!',
            'target_audience' => ['type' => 'all_customers'],
        ], $company->id, $user);

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'name' => 'Q4 Product Launch',
            'total_recipients' => 2,
        ]);
        $this->assertDatabaseHas('campaign_recipients', [
            'campaign_id' => $campaign->id,
            'recipient_email' => 'a@client.com',
        ]);
    }

    public function test_campaign_dispatching_queues_jobs()
    {
        Queue::fake();

        $company = Company::create(['name' => 'Marketing Co']);
        $user = User::factory()->create(['company_id' => $company->id]);
        Customer::create(['company_id' => $company->id, 'name' => 'Target User', 'email' => 'target@client.com']);

        $service = app(CampaignService::class);
        $campaign = $service->createCampaign([
            'name' => 'Promo Blast',
            'subject' => 'Special Promo',
            'content' => 'Discounts for {{name}}!',
        ], $company->id, $user);

        $service->dispatchCampaign($campaign);

        $this->assertEquals('sent', $campaign->fresh()->status);
        Queue::assertPushed(\App\Jobs\SendCampaignEmail::class);
    }
}
