<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AggregateAnalytics extends Command
{
    protected $signature = 'analytics:aggregate';

    protected $description = 'Aggregate business analytics';

    public function handle(): int
    {
        $this->info('Analytics aggregation is ready.');

        return self::SUCCESS;
    }
}