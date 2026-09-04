<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Services\Marketing\CampaignService;
use Illuminate\Console\Command;

class ProcessCampaigns extends Command
{
    protected $signature = 'campaigns:process';

    protected $description = 'Dispatch scheduled marketing campaigns';

    public function handle(CampaignService $campaignService): int
    {
        $campaigns = Campaign::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        $this->info("Found " . $campaigns->count() . " scheduled campaign(s) to dispatch.");

        foreach ($campaigns as $campaign) {
            $this->info("Dispatching campaign: {$campaign->name}");
            $campaignService->dispatchCampaign($campaign);
        }

        return self::SUCCESS;
    }
}