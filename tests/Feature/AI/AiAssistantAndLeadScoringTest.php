<?php

namespace Tests\Feature\AI;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Quote;
use App\Services\AI\AIService;
use App\Services\AI\BusinessInsightService;
use App\Services\AI\LeadScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiAssistantAndLeadScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_assistant_intent_detection()
    {
        $service = app(AIService::class);
        $res = $service->generateAssistantResponse('How much would a new mobile app quote cost?');

        $this->assertEquals('pricing_inquiry', $res['intent']);
        $this->assertNotEmpty($res['reply']);
        $this->assertGreaterThan(0.8, $res['confidence']);
    }

    public function test_lead_scoring_algorithm()
    {
        $company = Company::create(['name' => 'AI Corp']);
        $lead = Lead::create([
            'company_id' => $company->id,
            'name' => 'CTO John',
            'email' => 'john@enterprise-tech.io',
            'phone' => '+15550199',
            'notes' => 'We require an enterprise digital transformation system with high scalability, cloud microservices and automated workflows for 500 team members.',
        ]);

        $scoring = app(LeadScoringService::class);
        $result = $scoring->calculateScore($lead);

        $this->assertGreaterThanOrEqual(70, $result['score']);
        $this->assertStringContainsString('Hot Lead', $result['grade']);

        $scoring->scoreAndUpdateLead($lead);
        $this->assertEquals($result['score'], $lead->fresh()->score);
    }

    public function test_business_intelligence_summary_generation()
    {
        $company = Company::create(['name' => 'Insight Corp']);
        $lead = Lead::create(['company_id' => $company->id, 'name' => 'L1', 'email' => 'l1@test.com']);
        $quote = Quote::create([
            'company_id' => $company->id,
            'quote_number' => 'Q-100',
            'status' => 'accepted',
            'total' => 15000,
        ]);

        $biService = app(BusinessInsightService::class);
        $summary = $biService->generateExecutiveSummary($company->id);

        $this->assertEquals(1, $summary['total_leads']);
        $this->assertEquals(1, $summary['total_quotes']);
        $this->assertEquals(100, $summary['conversion_rate_percentage']);
    }
}
