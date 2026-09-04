<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Console\Command;

class AggregateAnalytics extends Command
{
    protected $signature = 'analytics:aggregate {--date= : Specific date YYYY-MM-DD}';

    protected $description = 'Aggregate daily business metrics for all companies';

    public function handle(AnalyticsService $analyticsService): int
    {
        $date = $this->option('date') ?? now()->toDateString();
        $companies = Company::all();

        $this->info("Aggregating metrics for date: {$date}");

        // Aggregate global
        $analyticsService->aggregateDaily(null, $date);

        // Aggregate per company
        foreach ($companies as $company) {
            $analyticsService->aggregateDaily($company->id, $date);
        }

        $this->info('Daily analytics aggregation completed successfully.');

        return self::SUCCESS;
    }
}