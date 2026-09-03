<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessFollowUps extends Command
{
    protected $signature = 'follow-ups:process';

    protected $description = 'Process pending lead follow-ups';

    public function handle(): int
    {
        $this->info('Follow-up processing is ready.');

        return self::SUCCESS;
    }
}