<?php

namespace App\Jobs;

use App\Services\AI\BusinessInsightService;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateBusinessReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $companyId = null) {}

    public function handle(BusinessInsightService $biService, AnalyticsService $analyticsService): void
    {
        $summary = $biService->generateExecutiveSummary($this->companyId);
        $analyticsService->aggregateDaily($this->companyId);
    }
}