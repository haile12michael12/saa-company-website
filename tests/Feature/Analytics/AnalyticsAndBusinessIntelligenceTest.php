<?php

namespace Tests\Feature\Analytics;

use App\Models\AnalyticsMetric;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\User;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsAndBusinessIntelligenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_and_aggregate_metrics()
    {
        $company = Company::create(['name' => 'Analytics Org']);
        $service = app(AnalyticsService::class);

        $service->recordMetric('page_views', 150, $company->id);
        $service->recordMetric('page_views', 50, $company->id);

        $this->assertDatabaseHas('analytics_metrics', [
            'company_id' => $company->id,
            'metric_key' => 'page_views',
            'metric_value' => 200,
        ]);
    }

    public function test_admin_analytics_endpoint()
    {
        $company = Company::create(['name' => 'Analytics Org']);
        $user = User::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user)->getJson(route('admin.analytics.index'));
        $response->assertStatus(200)
            ->assertJsonStructure(['metrics', 'business_intelligence']);
    }
}
