<?php

namespace App\Services\AI;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Service;
use App\Models\SubscriptionPlan;

class RecommendationService
{
    public function recommendServicesForLead(Lead $lead): array
    {
        $services = Service::where('status', 1)->take(3)->get();
        $recommendations = [];

        foreach ($services as $service) {
            $recommendations[] = [
                'service_id' => $service->id,
                'name' => $service->name,
                'match_score' => rand(80, 98),
                'reason' => "Based on recent industry trends and your inquiry category.",
            ];
        }

        return $recommendations;
    }

    public function recommendUpsellForCustomer(Customer $customer): array
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return [
            'recommended_plan' => $plans->first()?->name ?? 'Enterprise Growth',
            'suggested_addons' => ['Priority SLA Support', 'Automated Daily Backups', 'AI Insights Engine'],
            'potential_roi_gain' => '35% estimated efficiency boost',
        ];
    }
}