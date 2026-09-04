<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\AI\LeadScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAILeadScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function handle(LeadScoringService $scoringService): void
    {
        $scoringService->scoreAndUpdateLead($this->lead);
    }
}