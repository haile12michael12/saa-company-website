<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Services\Communication\EmailService;
use Illuminate\Console\Command;

class ProcessFollowUps extends Command
{
    protected $signature = 'leads:process-followups';

    protected $description = 'Send automatic follow-ups to unresponsive leads';

    public function handle(EmailService $emailService): int
    {
        $leads = Lead::where('status', 'new')
            ->where('created_at', '<=', now()->subHours(24))
            ->take(50)
            ->get();

        $this->info("Processing follow-ups for " . $leads->count() . " lead(s)...");

        foreach ($leads as $lead) {
            $emailService->queueLeadFollowUp($lead, 'unresponsive_followup', 1);
        }

        return self::SUCCESS;
    }
}