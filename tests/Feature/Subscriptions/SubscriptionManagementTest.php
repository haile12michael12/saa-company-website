<?php

namespace Tests\Feature\Subscriptions;

use App\Models\Company;
use App\Models\Subscription\Subscription;
use App\Models\Subscription\SubscriptionPlan;
use App\Models\User;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_subscribe_company_to_plan_and_check_feature_access()
    {
        $company = Company::create(['name' => 'SaaS Client']);
        $plan = SubscriptionPlan::create([
            'name' => 'Pro Tier',
            'slug' => 'pro-tier',
            'price' => 99.00,
            'features' => ['ai_assistant', 'webhooks', 'unlimited_quotes'],
        ]);

        $service = app(SubscriptionService::class);
        $subscription = $service->subscribe($company, $plan);

        $this->assertDatabaseHas('subscriptions', [
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
        $this->assertTrue($service->checkFeature($company, 'ai_assistant'));
        $this->assertTrue($service->checkFeature($company, 'webhooks'));
        $this->assertFalse($service->checkFeature($company, 'custom_sso'));
    }
}
