<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessWorkflows extends Command
{
    protected $signature = 'workflows:process';

    protected $description = 'Process pending workflows';

    public function handle(): int
    {
        $this->info('Workflow processing is ready.');

        return self::SUCCESS;
    }
}