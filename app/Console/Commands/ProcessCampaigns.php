<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessCampaigns extends Command
{
    protected $signature = 'campaigns:process';

    protected $description = 'Process pending campaigns';

    public function handle(): int
    {
        $this->info('Campaign processing is ready.');

        return self::SUCCESS;
    }
}