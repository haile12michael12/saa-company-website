<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsMetric;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Quote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function recordMetric(string $key, float $value, ?int $companyId = null, ?string $date = null, array $dimension = []): AnalyticsMetric
    {
        $metricDate = Carbon::parse($date ?? now())->format('Y-m-d');

        $existing = AnalyticsMetric::where('company_id', $companyId)
            ->where('metric_key', $key)
            ->whereDate('metric_date', $metricDate)
            ->first();

        if ($existing) {
            $existing->increment('metric_value', $value);
            if (!empty($dimension)) {
                $existing->update(['dimension' => $dimension]);
            }
            return $existing->fresh();
        }

        return AnalyticsMetric::create([
            'company_id' => $companyId,
            'metric_key' => $key,
            'metric_date' => $metricDate,
            'metric_value' => $value,
            'dimension' => $dimension,
        ]);
    }

    public function getMetricsSummary(?int $companyId = null, int $days = 30): array
    {
        $startDate = now()->subDays($days)->toDateString();

        $metricsQuery = AnalyticsMetric::where('metric_date', '>=', $startDate);
        if ($companyId) {
            $metricsQuery->where('company_id', $companyId);
        }

        $dailyBreakdown = $metricsQuery->orderBy('metric_date')
            ->get()
            ->groupBy('metric_key');

        $leadsCount = Lead::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->where('created_at', '>=', $startDate)->count();

        $quotesCount = Quote::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->where('created_at', '>=', $startDate)->count();

        $revenue = Invoice::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->where('status', 'paid')
            ->where('created_at', '>=', $startDate)->sum('total') ?: 0;

        return [
            'period_days' => $days,
            'leads_count' => $leadsCount,
            'quotes_count' => $quotesCount,
            'revenue_total' => round($revenue, 2),
            'metrics_breakdown' => $dailyBreakdown,
        ];
    }

    public function aggregateDaily(?int $companyId = null, ?string $date = null): array
    {
        $targetDate = $date ?? now()->toDateString();

        $leads = Lead::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereDate('created_at', $targetDate)->count();

        $quotes = Quote::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereDate('created_at', $targetDate)->count();

        $revenue = Invoice::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->where('status', 'paid')
            ->whereDate('updated_at', $targetDate)->sum('total') ?: 0;

        AnalyticsMetric::updateOrCreate(
            ['company_id' => $companyId, 'metric_key' => 'leads_generated', 'metric_date' => $targetDate],
            ['metric_value' => $leads]
        );

        AnalyticsMetric::updateOrCreate(
            ['company_id' => $companyId, 'metric_key' => 'quotes_created', 'metric_date' => $targetDate],
            ['metric_value' => $quotes]
        );

        AnalyticsMetric::updateOrCreate(
            ['company_id' => $companyId, 'metric_key' => 'revenue_collected', 'metric_date' => $targetDate],
            ['metric_value' => $revenue]
        );

        return [
            'date' => $targetDate,
            'leads' => $leads,
            'quotes' => $quotes,
            'revenue' => $revenue,
        ];
    }
}