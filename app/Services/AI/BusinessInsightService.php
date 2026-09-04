<?php

namespace App\Services\AI;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessInsightService
{
    public function generateExecutiveSummary(?int $companyId = null): array
    {
        $quotesQuery = Quote::query();
        $invoicesQuery = Invoice::query();
        $leadsQuery = Lead::query();
        $contractsQuery = Contract::query();
        $subscriptionsQuery = Subscription::query()->where('status', 'active');

        if ($companyId) {
            $quotesQuery->where('company_id', $companyId);
            $invoicesQuery->where('company_id', $companyId);
            $leadsQuery->where('company_id', $companyId);
            $contractsQuery->where('company_id', $companyId);
            $subscriptionsQuery->where('company_id', $companyId);
        }

        $totalLeads = $leadsQuery->count();
        $totalQuotes = $quotesQuery->count();
        $acceptedQuotes = (clone $quotesQuery)->where('status', 'accepted')->count();
        $conversionRate = $totalQuotes > 0 ? round(($acceptedQuotes / $totalQuotes) * 100, 1) : 0;

        $totalRevenue = (clone $invoicesQuery)->where('status', 'paid')->sum('total') ?: 0;
        $activeContracts = (clone $contractsQuery)->where('status', 'signed')->count();
        $activeSubscriptions = $subscriptionsQuery->count();

        // Financial Forecast (Next 30 Days)
        $pipelineValue = (clone $quotesQuery)->whereIn('status', ['pending', 'sent'])->sum('total') ?: 0;
        $forecastedRevenue = $totalRevenue + ($pipelineValue * 0.45);

        return [
            'total_leads' => $totalLeads,
            'total_quotes' => $totalQuotes,
            'accepted_quotes' => $acceptedQuotes,
            'conversion_rate_percentage' => $conversionRate,
            'total_revenue' => round($totalRevenue, 2),
            'pipeline_value' => round($pipelineValue, 2),
            'forecasted_30d_revenue' => round($forecastedRevenue, 2),
            'active_contracts' => $activeContracts,
            'active_subscriptions' => $activeSubscriptions,
            'insights' => [
                "Pipeline velocity is at {$conversionRate}% quote-to-deal conversion.",
                "Estimated pipeline closure yields approximately \$" . number_format($pipelineValue * 0.45, 2) . " next month.",
                $activeContracts > 0 ? "Contract health is strong with {$activeContracts} active signed legal agreements." : "Recommendation: Convert accepted quotes into binding contracts.",
            ],
        ];
    }
}